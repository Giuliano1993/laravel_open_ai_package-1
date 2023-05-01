<?php

namespace App\Http\Controllers\Git;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\GitProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Constraint\ArrayHasKey;
use Symfony\Component\Finder\Gitignore;

class GitController extends Controller
{

    private $user_path;
    /* TODO: adapt this code to the codebase */
    public function auth(Request $request)
    {

        //dd($request->all());
        /* Validate the request */
        $validatedData = $this->validation($request);

        //dd($validatedData, 'the user selected a provider and inserted a token');
        // Perform the request
        try {
            $response = $this->baseRequest($validatedData)->get($this->user_path);
            //dd($response);
        } catch (\Throwable $th) {
            $message = 'Server Error!' . $th->getMessage();
            dd('thereis an error');
            //TODO: change this! don't show server errors to the user
            // this will change when the codebase will mature to a clear implementation
            return response()->json(
                [
                    'success' => false,
                    'error' => "Server Ops!🙄 " . ' Failed to connect to ' . ucfirst($validatedData['provider']) . '.'
                ]
            );
        }
        // Handling the a failed client response
        if ($response->failed()) {
            //dd(json_decode($response->body(), true));
            // TODO: Refactor this code
            $error = json_decode($response->body(), true);
            switch ($request->provider) {
                case 'github':
                    $message = $error['message'];
                    break;
                case 'bitbucket':
                    $message = $error['error']['message'];
                    # code...
                    break;
                case 'gitlab':
                    # code...
                    //dd($error);
                    $message = $error['error'];
                    break;
            }

            //dd($message);
            return response()->json(

                [
                    'success' => false,
                    'error' => "Error, $message" . ' Failed to connect to ' . ucfirst($validatedData['provider']) . '.'
                ]
            );
        }

        /*
        At this point the request was successful and there is a provider and a token waithing to be stored

            - if the credentials are NOT in the db
            - create a new record for the selected provider
            - add the credentials to the git_providers session

        */
        //dd(GitProvider::where('name', $request->provider)->count());
        if (GitProvider::where('name', $request->provider)->where('user_id', Auth::user()->id)->count() === 0) {
            //dd('first time');
            GitProvider::create([
                'name' => $request->provider,
                'token' => $request->token,
                'refresh_token' => $request->refresh_token,
                'user_id' => Auth::id()
            ]);
        }

        /*
            - if the credentials are already in the db for the requested provider
            - update the records in the db
            - update the session
            */
        if (GitProvider::where('name', $request->provider)->where('user_id', Auth::user()->id)->count() > 0) {
            //dd('already saved');
            GitProvider::where('name', $request->provider)->where('user_id', Auth::user()->id)->update([
                'token' => $request->token,
                'refresh_token' => $request->refresh_token,
                'user_id' => Auth::id()
            ]);
        }



        //dd($request->session()->get('git_providers'));
        // check if the current provider is in the session
        // if yes update it
        // otherwise push it into the section

        // get all git providers from the session and convert them in a collection
        $session_providers = collect($request->session()->get('git_providers'));
        // check if the collection has a key matching the request provider
        if ($session_providers->hasAny($request->provider)) {
            // update the provider
            //dd($request->session()->get('git_providers'), 'update the provider');
            // select the provider from the collection
            $request->session()->get('git_providers')[$request->provider]['token'] = $request->token;
        } else {
            // push the new provider

            //dd($request->session()->get('git_providers'), 'add the provider');
            $request->session()->put(['git_providers' => [
                $request->provider => [
                    'token' => $request->token,
                    'name' => $request->provider
                ]
            ]]);
        }



        return response()->json(
            [
                'success' => true,
                'message' => 'Connected to ' . ucfirst($validatedData['provider']) . ' successfully.'
            ]
        );
    }

    /**
     * This call returns the bitbucket token and the refresh token needed in order to renew the bitbucket token that, in fact expires after 2 hours
     *
     * @param Request $request
     * @return Json
     */
    public function bitbucketCode(Request $request)
    {
        $code = $request->query('code');
        $key = env('BITBUCKET_KEY');
        $secret = env('BITBUCKET_SECRET');
        exec("curl -X POST -u $key:$secret https://bitbucket.org/site/oauth2/access_token -d grant_type=authorization_code -d code=$code", $output);
        return response()->json($output);
    }

    /**
     * This function checks if bitbucket token is still valid or not. If not asks for a new token and save it, then, in both cases, returns  the provider
     *
     * @param GitProvider $provider
     * @return GitProvider
     */
    private function bitbucketTokenOrRefreshToken(GitProvider $provider)
    {
        $now = new \Datetime();
        $interval = $now->diff($provider->updated_at);
        if ($interval->h >= 2) {
            $key = env('BITBUCKET_KEY');
            $secret = env('BITBUCKET_SECRET');
            exec("curl -X POST -u $key:$secret https://bitbucket.org/site/oauth2/access_token -d grant_type=refresh_token -d refresh_token=$provider->refresh_token", $output);
            $response = json_decode($output[0]);
            $newToken = $response->access_token;
            $refreshToken = $response->refresh_token;
            $provider->update([
                'token' => $newToken,
                'refresh_token' => $refreshToken
            ]);
        }
        return $provider;
    }

    /**
     * Get a list of available workspaces. We will need to select one to accesss the repositories inside it
     *
     * @return Json
     */
    public function workspaces()
    {
        $provider = GitProvider::where('name', 'bitbucket')->where('user_id', Auth::user()->id)->first();
        $provider = $this->bitbucketTokenOrRefreshToken($provider);
        $token = $provider->token;
        $response = Http::withToken($token)->accept('application/json')->baseUrl('https://api.bitbucket.org/2.0/')->get('user/permissions/workspaces');
        //$response = $this->basicAuth()->get('user/permissions/workspaces');
        return json_decode($response->getBody(), true);
    }

    public function repositories(Request $request)
    {
        //dd($request->all());
        //dd(session()->get('git_providers'));
        /* TODO:
        Update the $data below, to reflect the new implementation of the git_providers
        providers are now stored in the db first then in the session.
        Now we are getting only the first provider in the list and from it taking its repos
        */
        $providerName = $request->query('provider');
        $data = [
            'token' => GitProvider::where('name', $providerName)->where('user_id', Auth::user()->id)->first()->token,
            'provider' => $providerName,
        ];
        //dd($data);
        switch ($providerName) {
            case 'github':
                $repoUrl = 'user/repos?sort=updated&per_page=50';
                break;
            case 'gitlab':
                // access levels link https://docs.gitlab.com/ee/api/members.html#roles
                $repoUrl = 'projects?min_access_level=20';
                break;
            case 'bitbucket':
                $repoUrl = 'repositories/' . $request->query('ws');
                break;
            default:
                $repoUrl = 'repositories';
                break;
        }
        $response = $this->baseRequest($data)->get($repoUrl);
        //dd($response);
        $repositories = collect(json_decode($response->getBody()));
        //dd($repositories);

        if ($providerName == 'bitbucket') $repositories = collect($repositories->get('values'));
        $repos =  $repositories->map(function ($repository) {
            //dd($repository);
            return [
                'id' => isset($repository->id) ? $repository->id : null,
                'name' => $repository?->name,
                'owner' => isset($repository?->owner) ? $repository?->owner : null
            ];
        });
        // return the response
        return $repos;
    }

    public function issues(Request $request)
    {
        //dd($request->all());
        // TODO: need to validate the input
        $provider_name = $request->git_provider;
        $issue_body = Message::findOrFail($request->issue_body)->body;
        //dd($provider, $issue_body);



        // #IF the session is empty
        // the user either logged out and logged back in or never connected a git provider
        $request->session()->put(['git_providers' => Auth::user()->gitProviders]);
        //dd(session('git_providers'));
        if (!session('git_providers')) {

            // check if there are git providers credentials in the db for the auth user
            if (!Auth::user()->gitProviders) {
                return to_route('admin.git.connect')->with('message', 'you need to connect a git provider account');
            }

            // retrive the credentials from the db and store them into the session
            //$request->session()->put(['git_providers' => Auth::user()->gitProviders]);
            // find the user selected provider records
            $provider = Auth::user()->gitProviders()->where('name', $provider_name)->first();
            // store them in the session for future reference
            $provider_token = $provider->token;
            //dd(session('git_providers'), 'SESSION WAS EMPTY');
        } else {
            // otherwise take them from the session
            //dd(session('git_providers'), 'IN THE SESSION');
            // get the provider token
            foreach (session()->get('git_providers') as $pro) {
                if ($pro['name'] == $provider_name) {
                    $provider_token = $pro['token'];
                }
            }
        }
        //dd($provider_name, $provider_token);
        // perform a base request with provider credentials
        $data = [
            'token' => $provider_token,
            'provider' => $provider_name
        ];
        // sumbint a post request passing the payload
        $client = $this->baseRequest($data);
        if ($provider_name == 'github') {
            $url = "repos/$request->owner/$request->repo_id/issues";
            //dd($url);
            $response =  $client->withHeaders([
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ])->post($url, [
                'owner' => $request->owner,
                'repo' => $request->repo_id,
                'title' => $request->issue_summary,
                'body' => $issue_body
            ]);
        } elseif ($provider_name == 'bitbucket') {
            $url = "repositories/$request->workspace/$request->repo_id/issues";
            $response =  $client->post($url, [
                'state' => 'open',
                'title' => $request->issue_summary,
                'priority' => 'minor',
                'content' => [
                    'raw' => $issue_body,
                    'markup' => $issue_body,
                    'html' => $issue_body
                ]
            ]);
        } elseif ($provider_name == 'gitlab') {
            $url = "projects/$request->repo_id/issues";
            $response =  $client->post($url, [
                'title' => $request->issue_summary,
                'description' => $issue_body
            ]);
        }

        //dump the response
        //dd($response);
        return response()->json([
            'success' => true,
            'header' => $response->headers(),
            'body' => json_decode($response->getBody())
        ]);
        // redirect back

    }



    private function baseRequest($data)
    {
        // Set the base url and user auth path based on the provider
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
        // return the response for the user request
        return Http::withToken($token)->baseUrl($base_url);
    }

    private function validation($data)
    {
        return $data->validate([
            'provider' => 'required|in:github,gitlab,bitbucket',
            'token' => 'required|string',
        ]);
    }
}
