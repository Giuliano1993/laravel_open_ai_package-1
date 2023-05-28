<?php

namespace App\Http\Controllers\Git;

use App\Models\Issue;

class GitLabController extends GitRemoteProvider
{
    public function handleProviderIssuesResponse($client, $request, $issue_body)
    {
        $url = "projects/$request->repo_id/issues";

        return $client->post($url, [
            'title' => $request->issue_summary,
            'description' => $issue_body,
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
        //dd('here gl');
        // access levels link https://docs.gitlab.com/ee/api/members.html#roles
        $repoUrl = 'projects?min_access_level=20';

        return $repoUrl;
    }

    public function handleProviderFailedResponse($response, $provider)
    {
        if ($response->failed()) {
            $error = json_decode($response->body(), true);
            $message = $error['error'];
            return $message;
        }

    }

    public function handleIssueCreation($response, $message, $issueTitle){
        $issue = new Issue();
        $issue->url = $response['web_url'];
        $issue->provider = 'gitlab';
        $issue->title = $issueTitle;
        $issue->message_id = $message->id;
        $issue->save();
        return $issue;
    }
}
