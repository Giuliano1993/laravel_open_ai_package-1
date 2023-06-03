<?php

namespace App\Http\Controllers\Git;

use App\Models\Issue;
use App\Models\Message;
use App\Models\GitProvider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

abstract class GitRemoteProvider
{
    /**
     * The user path for the remote provider.
     *
     * @var string
     */
    protected $user_path;

    /**
     * Authenticate the user with the remote provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function auth(Request $request)
    {
        // Validate the request
        $validatedData = $this->validation($request);
        // Perform the request
        try {
            $response = $this->baseRequest($validatedData)->get($this->user_path);
        } catch (\Throwable $th) {
            $message = 'Server Error!' . $th->getMessage();

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Server Ops!🙄 ' . $message . ' Failed to connect to ' . ucfirst($validatedData['provider']) . '.',
                ]
            );
        }

        // Handle the provider response
        $errorMessage = $this->handleProviderFailedResponse($response, $request->provider);

        if ($errorMessage) {
            return response()->json(
                [
                    'success' => false,
                    'error' => "Error, $errorMessage" . ' Failed to connect to ' . ucfirst($validatedData['provider']) . '.',
                ]
            );
        }

        // Save the credentials to the database
        $this->saveCredentials($request->provider, $request->token, $request->refresh_token);

        // Update the session
        $this->updateSession($request->provider, $request->token);

        return response()->json(
            [
                'success' => true,
                'message' => 'Connected to ' . ucfirst($validatedData['provider']) . ' successfully.',
            ]
        );
    }

    /**
     * Get repositories from a Git provider.
     *
     * @param  Request  $request
     * @return array
     *
     * @throws Exception
     */
    public function repositories(Request $request)
    {
        // Get the Git provider name from the query string
        $providerName = $request->query('provider');
        //dd($providerName);
        // Get the Git provider token for the authenticated user
        $token = GitProvider::where('name', $providerName)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail()
            ->token;

        // Set the data to be sent in the request
        $data = [
            'token' => $token,
            'provider' => $providerName,
        ];

        // Get the URL for the provider's repositories
        $repoUrl = $this->getProviderRepositoriesUrl($request);

        if (!$repoUrl) {
            throw new \Exception('Error Processing Request, missing Repositories URL', 1);
        }
        //dd($repoUrl);
        // Send the request to the provider's API
        $response = $this->baseRequest($data)->get($repoUrl);

        // Map the repositories to a collection
        $repositories = collect(json_decode($response->getBody()));
        //dd($repositories);
        // Return the mapped repositories
        return $this->handle_repositories_mapping($repositories);
    }

    /**
     * Adds an issue to a Git provider.
     *
     * @param  Request  $request The HTTP request object.
     * @return JsonResponse The JSON response object.
     */
    public function issues(Request $request): JsonResponse
    {
        // TODO: need to validate the input
        $provider_name = $request->input('git_provider');
        $message = Message::findOrFail($request->input('issue_body'));
        $issue_body = $message->body;

        $provider_token = $this->getProviderToken($request, $provider_name);

        // perform a base request with provider credentials
        $data = [
            'token' => $provider_token,
            'provider' => $provider_name,
        ];
        $client = $this->baseRequest($data);

        // submit a post request passing the payload
        $response = $this->handleProviderIssuesResponse($client, $request, $issue_body);
        /* TODO: We should find out the issue url (by git provide) at this point (insead of in alpine) and return that only as part of the response
        if there is an error then the response json should return an error instead of the whole response*/
        $response_body = json_decode($response->getBody(), true);
        //dd($response_body);
        $issue = new Issue();
        $issue->title = $request->issue_summary;
        $issue->message_id = $message->id;
        $this->handleCreatedIssueUrl($response, $issue);
        $issue->save();

        return response()->json([
            'success' => true,
            'header' => $response->headers(),
            'body' => json_decode($response->getBody()),
        ]);
    }

    /* Abstract Methods */

    /**
     * Handle the Git Provider Issue response
     */
    abstract protected function handleProviderIssuesResponse($client, $request, $issue_body);

    /**
     * Handle the Git Provider failed http response
     */
    abstract protected function handleProviderFailedResponse($response, $provider);

    /**
     * Get the URL for the Git provider's repositories.
     *
     * @param  Request  $request
     * @return string|null
     */
    abstract protected function getProviderRepositoriesUrl($request);

    /**
     * Map the repositories to a custom format.
     *
     * @param  Collection  $repositories
     * @return array
     */
    abstract protected function handle_repositories_mapping($repositories);


    /**
     * Handle the different url behaviour for the created issue
     * @param Response $response
     * @param Issue $issue
     * @return Issue
     */
    abstract protected function handleCreatedIssueUrl($response, &$issue);

    // Protected Methods

    /**
     * Save user's Git provider credentials
     *
     * @param  string  $provider The name of the Git provider
     * @param  string  $token The access token for the Git provider
     * @param  string  $refreshToken The refresh token for the Git provider
     * @return void
     */
    protected function saveCredentials(string $provider, string $token, string|null $refreshToken): void
    {
        $user = Auth::user();
        $providerCount = GitProvider::where('name', $provider)->where('user_id', $user->id)->count();

        if ($providerCount === 0) {
            GitProvider::create([
                'name' => $provider,
                'token' => $token,
                'refresh_token' => $refreshToken,
                'user_id' => $user->id,
            ]);
        } else {
            GitProvider::where('name', $provider)->where('user_id', $user->id)->update([
                'token' => $token,
                'refresh_token' => $refreshToken,
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Update the session with the given provider and token.
     *
     * @param  string  $provider The name of the provider.
     * @param  string  $token The token to update.
     * @return void
     */
    protected function updateSession(string $provider, string $token): void
    {
        $session_providers = collect(session()->get('git_providers'));

        // If the provider already exists in the session, update the token.
        if ($session_providers->has($provider)) {
            $session_providers->put($provider, [
                'token' => $token,
                'name' => $provider,
            ]);
        } else {
            // Otherwise, add the provider and token to the session.
            $session_providers->put($provider, [
                'token' => $token,
                'name' => $provider,
            ]);
            session()->put('git_providers', $session_providers->toArray());
        }
    }

    /**
     * Validate the data for the remote provider.
     *
     * @param  \Illuminate\Http\Request  $data
     * @return mixed
     */
    protected function validation($data)
    {
        return $data->validate([
            'provider' => 'required|in:github,gitlab,bitbucket',
            'token' => 'required|string',
        ]);
    }

    /**
     * Get the base request for the remote provider.
     *
     * @param  array  $data
     * @return mixed
     */
    protected function baseRequest($data)
    {
        switch ($data['provider']) {
            case 'github':
                $base_url = 'https://api.github.com';
                $this->user_path = '/user';
                break;
            case 'gitlab':
                $base_url = 'https://gitlab.com/api/v4';
                $this->user_path = '/user';
                break;
            case 'bitbucket':
                $base_url = 'https://api.bitbucket.org/2.0';
                $this->user_path = '/user';
                break;
            default:
                $base_url = '';
                $this->user_path = '';
                break;
        }
        $token = $data['token'];

        return Http::withToken($token)->baseUrl($base_url);
    }

    /**
     * Get the provider token for the given provider name.
     *
     * @param  Request  $request
     * @param  string  $provider_name
     * @return string|\Illuminate\Http\RedirectResponse
     */
    protected function getProviderToken(Request $request, $provider_name)
    {
        $request->session()->put(['git_providers' => Auth::user()->gitProviders]);
        // If the session is empty, the user either logged out and logged back in or never connected a git provider.
        if (!session('git_providers')) {
            // Check if there are git provider credentials in the db for the auth user.
            if (!Auth::user()->gitProviders) {
                return redirect()->route('admin.git.connect')->with('message', 'You need to connect a git provider account.');
            }

            // Retrieve the credentials from the db and store them into the session.
            //$request->session()->put(['git_providers' => Auth::user()->gitProviders]);

            // Find the user selected provider records.
            $provider = Auth::user()->gitProviders()->where('name', $provider_name)->first();

            // Store them in the session for future reference.
            $provider_token = $provider->token;
        } else {
            // Otherwise take them from the session.
            foreach (session()->get('git_providers') as $pro) {
                if ($pro['name'] == $provider_name) {
                    $provider_token = $pro['token'];
                }
            }
        }

        return $provider_token;
    }
}
