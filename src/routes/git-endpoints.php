<?php

use App\Http\Controllers\Git\BitBucketController;
use App\Http\Controllers\Git\GitHubController;
use App\Http\Controllers\Git\GitLabController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // auth
    Route::post('git/auth', function (Request $request) {
        switch($request->provider) {
            case 'bitbucket':
                return app(BitBucketController::class)->auth($request);
                break;
            case 'github':
                return app(GitHubController::class)->auth($request);
                break;
            case 'gitlab':
                return app(GitLabController::class)->auth($request);
                break;
            default:
                abort(404);
        }
    })->name('git.auth');
    // repos

    Route::get('git/repositories', function (Request $request) {
        switch($request->query('provider')) {
            case 'bitbucket':
                return app(BitBucketController::class)->repositories($request);
                break;
            case 'github':
                return app(GitHubController::class)->repositories($request);
                break;
            case 'gitlab':
                return app(GitLabController::class)->repositories($request);
                break;
            default:
                abort(404);
        }
    })->name('git.repositories');

    // issues

    Route::post('git/issues', function (Request $request) {
        switch($request->query('provider')) {
            case 'bitbucket':
                return app(BitBucketController::class)->issues($request);
                break;
            case 'github':
                return app(GitHubController::class)->issues($request);
                break;
            case 'gitlab':
                return app(GitLabController::class)->issues($request);
                break;
            default:
                abort(404);
        }
    })->name('git.issues');

    Route::get('git/workspaces', [BitBucketController::class, 'workspaces'])->name('git.workspaces')->where('provider', 'bitbucket');
    Route::post('git/bitbucket-auth', [BitBucketController::class, 'bitbucketCode'])->name('bitbucket.code')->where('provider', 'bitbucket');
});
