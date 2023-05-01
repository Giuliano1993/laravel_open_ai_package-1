<?php

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PacificDev\LaravelOpenAi\Services\OpenAi;

uses(RefreshDatabase::class);


it('can get all conversations for the authenticated user', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create(['user_id' => $user->id]);
    $message = Message::factory()->create(['status' => 'sent', 'body' => 'Hi what is your name', 'conversation_id' => $conversation->id, 'has_star' => false]);

    Auth::login($user);

    $response = $this->get(route('admin.conversations.index'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.conversations.index');
    $response->assertViewHas('conversations');
    $response->assertViewHas('starredMessages');
})->group('chat');

it('can start a new conversation', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $response = $this->get(route('admin.conversations.new'));

    $response->assertRedirect(route('admin.conversations.show', Conversation::first()->id));
})->group('chat');

it('can update a conversation', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create(['user_id' => $user->id]);
    Auth::login($user);

    $response = $this->patch(route('admin.conversations.update', $conversation->id), ['summary' => 'new summary']);

    $response->assertRedirect();
    $this->assertDatabaseHas('conversations', ['id' => $conversation->id, 'summary' => 'new summary']);
})->group('chat');

it('can delete a conversation', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create(['user_id' => $user->id]);
    Auth::login($user);

    $response = $this->delete(route('admin.conversations.delete', $conversation->id));

    $response->assertRedirect();
    $this->assertDatabaseMissing('conversations', ['id' => $conversation->id]);
})->group('chat');

it('can get all messages in a conversation', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create(['user_id' => $user->id]);
    $message = Message::factory()->create(['status' => 'sent', 'body' => 'Hi what is your name', 'conversation_id' => $conversation->id, 'has_star' => false]);
    Auth::login($user);

    $response = $this->get(route('admin.conversations.show', $conversation->id));

    $response->assertStatus(200);
    $response->assertViewIs('admin.conversations.show');
    $response->assertViewHas('messages');
    $response->assertViewHas('url');
    $response->assertViewHas('conversation');
    $response->assertViewHas('git_providers');
})->group('chat');

/* TODO: Rewrite avoiding beind asked the OPENAI KEY in the .env.testing file */
it('stores a new message in a conversation and gets a response from OpenAi', function ($request_prompt, $ai_response) {

    $user = User::factory()->create();
    // Create a conversation and a message
    $conversation = Conversation::factory()->create(['user_id' => $user->id]);
    Auth::login($user);

    // Mock the OpenAi class
    $ai = Mockery::mock(OpenAi::class);
    $ai->allows(['chat' => 'How can i help you', 'text_complete' => 'New Conversation summary']);

    // Make a POST request to the route
    $response = $this->post(route('admin.ai.complete', ['conversation' => $conversation->id]), [
        'prompt' => $request_prompt,
        'max_tokens' => 1000,
        'temperature' => 0.5
    ]);
    Message::create([
        'body' => $request_prompt,
        'status' => 'sent',
        'conversation_id' => $conversation->id
    ]);

    Message::create([
        'status' => 'received',
        'body' => $ai_response,
        'conversation_id' => $conversation->id
    ]);

    // Assert that the response is a redirect back
    $response->assertRedirect();

    // Assert that the message was stored in the database
    $this->assertDatabaseHas('messages', [
        'conversation_id' => $conversation->id,
        'status' => 'received',
        'body' => $ai_response
    ]);

    // Assert that the conversation summary was generated
    $conversation->refresh();
    $this->assertEquals(' Welcome to Our Conversation!', $conversation->summary);
})->group('chat')->with(['request_prompt' => 'Hi there'], ['ai_response' => 'Hi, how can i help you?'])->skip();


it('can star message in a conversation', function () {
    $user = User::factory()->create();
    $conversation = Conversation::factory()->create(['user_id' => $user->id]);
    $message = Message::factory()->create(['status' => 'sent', 'body' => 'Hi what is your name', 'conversation_id' => $conversation->id, 'has_star' => false]);
    Auth::login($user);

    $this->patch(route('admin.messages.star', [$conversation->id, $message->id]));

    $this->assertDatabaseHas('messages', ['id' => $message->id, 'has_star' => true]);
})->group('chat');
