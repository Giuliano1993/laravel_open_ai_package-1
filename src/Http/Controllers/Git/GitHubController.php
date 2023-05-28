<?php

namespace App\Http\Controllers\Git;

class GitHubController extends GitRemoteProvider
{
    public function handleProviderIssuesResponse($client, $request, $issue_body)
    {
        $url = "repos/$request->owner/$request->repo_id/issues";
        //dd($url);
        return $client->withHeaders([
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
        ])->post($url, [
            'owner' => $request->owner,
            'repo' => $request->repo_id,
            'title' => $request->issue_summary,
            'body' => $issue_body,
        ]);
    }

    public function handle_repositories_mapping($repositories)
    {
        return $repositories->map(function ($repository) {
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
        //dd('here github');
        $repoUrl = 'user/repos?sort=updated&per_page=50';

        return $repoUrl;
    }

    public function handleProviderFailedResponse($response, $provider)
    {
        if ($response->failed()) {
            $error = json_decode($response->body(), true);
            $message = $error['message'];
            return $message;
        }

    }
}
