<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\UpdateConversationRequest;

use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /**
     * Display all conversations for the authenticated user.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(): \Illuminate\Contracts\View\View
    {
        /**
         * @var $user App/Models/User
         */
        $user = Auth::user(); // Get the authenticated user

        // Get all messages that are starred
        $starredMessages = Message::where('has_star', true)
            ->orderByDesc('updated_at')
            ->whereIn('conversation_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('conversations')
                    ->where('user_id', $user->id); // Get all conversations that belong to the user
            })
            ->take(6)->get();
        // dd($starredMessages);
        $conversations = $user->conversations()->orderByDesc('id')->paginate(12);

        $sharedConversations = $user->sharedConversations()->orderByDesc('id')->paginate(12);


        $conversationsSharedWithOthers = Conversation::where('user_id',$user->id)
        ->whereIn('id', function($query) use ($user){
            $query->select('conversation_id')->from('conversations_users')->whereIn('conversation_id', function($query) use ($user){
                $query->select('id')->from('conversations')->where('user_id', $user->id);
            });
        })->get();


        return view('admin.conversations.index', compact('conversations', 'starredMessages', 'sharedConversations','conversationsSharedWithOthers'));

    }

    /**
     * Creates a new empty conversation and store it in th db
     *
     * @return \Illuminate\Http\Response
     */
    public function new(): RedirectResponse
    {
        $conversation = Conversation::create(['summary' => 'new conversation', 'user_id' => Auth::id()]);
        return to_route('admin.conversations.show', $conversation->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateConversationRequest  $request
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateConversationRequest $request, Conversation $conversation)
    {


        $conversation->update($request->validated());
        return redirect()->back()->with('message', 'Conversation Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conversation $conversation): RedirectResponse
    {
        $conversation->delete();
        return redirect()->back()->with('message', 'Conversation Deleted.');
    }


    public function share(Conversation $conversation, Request $request): RedirectResponse
    {
        $mail = $request->mail;

        $writeAccess = $request->writeAccess;

        $user = User::firstWhere('email', $mail);


        if (!$user) {
            return redirect()->back()->with('message', "Impossible to share: the user does not exists.");
        }

        //TODO: add check if conversation is already shared with the user
        $conversation->sharedWithUsers()->attach($user,['write_access'=>$writeAccess]);

        return redirect()->back()->with('message', "Shared with $mail.");
    }

    public function unshare(Conversation $conversation, User $user): RedirectResponse
    {
        $conversation->sharedWithUsers()->detach($user);
        return redirect()->back()->with('message', "User $user->email can no longer see this conversation.");
    }
}
