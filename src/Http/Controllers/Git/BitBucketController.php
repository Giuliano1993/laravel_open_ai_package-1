<?php

namespace App\Http\Controllers\Git;

use App\Models\GitProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class BitBucketController extends GitRemoteProvider
{
    public function handleProviderIssuesResponse($client, $request, $issue_body)
    {
        $url = "repositories/$request->workspace/$request->repo_id/issues";

        return $client->post($url, [
            'state' => 'open',
            'title' => $request->issue_summary,
            'priority' => 'minor',
            'content' => [
                'raw' => $issue_body,
                'markup' => $issue_body,
                'html' => $issue_body,
            ],
        ]);
    }

    public function handle_repositories_mapping($repositories)
    {
        $repositories_collection = collect($repositories->get('values'));

        return $repositories_collection->map(function ($repository) {
            //dd($repository);
            return [
                'id' => isset($repository->id) ? $repository->id : null,
                'name' => $repository?->name,
                'owner' => isset($repository?->owner) ? $repository?->owner : null,
            ];
        });
    }

    public function getProviderRepositoriesUrl($request)
    {
        //dd('here bb');
        $repoUrl = 'repositories/'.$request->query('ws');

        return $repoUrl;
    }

    public function handleProviderFailedResponse($response, $provider)
    {
        if ($response->failed()) {
            $error = json_decode($response->body(), true);
            $message = $error['error']['message'];

            return $message;
        }
    }

    /**
     * This call returns the bitbucket token and the refresh token needed in order to renew the bitbucket token that, in fact expires after 2 hours
     *
     * @param  Request  $request
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
     * @param  GitProvider  $provider
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
                'refresh_token' => $refreshToken,
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
}
