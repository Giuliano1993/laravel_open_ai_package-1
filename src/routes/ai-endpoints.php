<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chat\ConversationController;
use App\Http\Controllers\Chat\ConversationMessageController;

/* Add the message in the messages table */


Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Conversation routes
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index'); //x
    Route::get('/conversations/new', [ConversationController::class, 'new'])->name('conversations.new'); //x
    Route::patch('/conversations/{conversation}', [ConversationController::class, 'update'])->name('conversations.update'); //x
    Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy'])->name('conversations.delete'); //x
    // Rotes for messages in a conversation
    Route::get('/conversations/{conversation}/messages', [ConversationMessageController::class, 'index'])->name('conversations.show'); //x
    Route::post('/conversations/{conversation}/messages', [ConversationMessageController::class, 'store'])->name('ai.complete'); //x

    Route::post('/conversations/{conversation}/share', [ConversationController::class, 'share'])->name('conversations.share'); //x
    Route::delete('/conversations/{conversation}/unshare/{user}', [ConversationController::class, 'unshare'])->name('conversations.unshare'); //x
    Route::patch('/conversations/{conversation}/messages/{message}/star', [ConversationMessageController::class, 'star'])->name('messages.star'); //x
});
