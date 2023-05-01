<?php

namespace App\Http\Controllers\Chat;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use PacificDev\LaravelOpenAi\Services\OpenAi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ConversationMessageController extends Controller
{


    public function star(Request $request, Conversation $conversation, Message $message)
    {
        //dd($request->all());
        $message = $conversation->messages()->where('id', $message->id)->first();
        //dd($message);
        $message->has_star = !$message->has_star;
        //dd($message);
        $message->save();
        return response()->json(['success' => true]);

        //return redirect()->back();
    }
    /**
     * Display a listing of all messages in a conversation
     *
     * @return \Illuminate\Contracts\View\View | HttpException
     */
    public function index(Conversation $conversation): \Illuminate\Contracts\View\View | HttpException
    {
        if ($conversation->user_id === Auth::id() || $conversation->sharedWithUsers->contains(Auth::user())) {
            $messages = $conversation->messages;
            //dd($messages);

            /* TODO: the component should have a default route where send posts requests */
            $url = "/admin/conversations/$conversation->id/messages";
            //dd($messages);
            $git_providers = Auth::user()->gitProviders;
            //dd($git_providers);
            $isShared = $conversation->user_id !== Auth::id();
            return view('admin.conversations.show', compact('messages', 'url', 'conversation', 'git_providers', 'isShared'));
        } else {
            abort('403', 'You can access only your conversations!');
        }
    }


    /**
     * Store a newly created message resource in storage.
     *
     * @param  \App\Http\Requests\StoreMessageRequest $request
     * @param  \PacificDev\LaravelOpenAi\Services\OpenAi $ai
     * @return \Illuminate\Http\RedirectResponse | string
     */
    public function store(Conversation $conversation, StoreMessageRequest $request, OpenAi $ai): \Illuminate\Http\RedirectResponse | string
    {

        Message::create([
            'body' => $request['prompt'],
            'status' => 'sent',
            'conversation_id' => $conversation->id
        ]);

        try {

            if ($request->has('temperature')) {
                $temperature = floatval($request->temperature);
            }
            if ($request->has('max_tokens')) {
                $max_tokens = intval($request->max_tokens);
            }

            //dd($request['prompt'], $temperature ??= 0, $model ??= 'gpt-3.5-turbo', $max_tokens ??= 1500);
            $text = $ai->chat($request['prompt'], $temperature ??= 0, $model ??= 'gpt-3.5-turbo', $max_tokens ??= 1500);
            Message::create([
                'status' => 'received',
                'body' => $text,
                'conversation_id' => $conversation->id
            ]);

            if ($conversation->messages->count() <= 2) {
                //$conversation_messages = $conversation->messages->pluck('body')->join('');
                $title = $ai->text_complete('Generate a title for the new conversation: ', $conversation);
                $conversation->update(['summary' => $title]);
            }

            return redirect()->back()->with('chat', $text);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
