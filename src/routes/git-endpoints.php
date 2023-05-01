<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Git\GitController;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('git/auth', [GitController::class, 'auth'])->name('git.auth');
    Route::get('git/repositories', [GitController::class, 'repositories'])->name('git.repositories');
    Route::post('git/issues', [GitController::class, 'issues'])->name('git.issues');
    Route::get('git/workspaces', [GitController::class, 'workspaces'])->name('git.workspaces');
    Route::post('git/bitbucket-auth', [GitController::class, 'bitbucketCode'])->name('bitbucket.code');
});
