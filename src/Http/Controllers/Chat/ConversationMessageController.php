<?php

namespace App\Http\Controllers\Chat;


use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMessageRequest;
use PacificDev\LaravelOpenAi\Services\OpenAi;
use Symfony\Component\HttpKernel\Exception\HttpException;


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
            //$messages = $conversation->messages;
            $messages = $conversation->messages()->with(
                [
                    'issues',
                    'conversation' => [
                        'user:id,name'
                    ]
                ]
            )->get();
            //dd($messages);

            /* TODO: the component should have a default route where send posts requests */
            $url = "/admin/conversations/$conversation->id/messages";
            //dd($messages);
            $git_providers = Auth::user()->gitProviders;
            //dd($git_providers);
            $isShared = $conversation->user_id !== Auth::id();

            $hasWriteAccess = false;
            if ($isShared) {
                $sharedRow = $conversation->sharedWithUsers()->where('user_id', Auth::id())->first()->pivot;
                $hasWriteAccess = $sharedRow->write_access;
            }
            return view('admin.conversations.show', compact('messages', 'url', 'conversation', 'git_providers', 'isShared', 'hasWriteAccess'));
        } else {
            abort('403', 'You can access only your conversations!');
        }
    }


    /**
     * Store a newly created message resource in storage.
     *
     * @param  \App\Http\Requests\StoreMessageRequest $request
     * @param  \PacificDev\LaravelOpenAi\Services\OpenAi $ai
     *
     */
    public function store(Conversation $conversation, StoreMessageRequest $request, OpenAi $ai)
    {

        // Check if the auth user can add messages to a shaded conversation
        //dd($request->all());
        $isSharedWithUser = $conversation->sharedWithUsers()->where('user_id', Auth::id())->first()?->pivot;

        if (($conversation->user_id != Auth::id() && !$isSharedWithUser) || ($conversation->user_id != Auth::id() && !$isSharedWithUser->write_access)) {
            return redirect()->back()->with('message', 'You can\'t send message in this conversation');
        }
        // Store the sent message in the db
        $this->storeSentMessage($conversation->id, $request['prompt']);
        // Check if the request has a temperature key and if so store it in the temperature variable.
        if ($request->has('temperature')) {
            $temperature = floatval($request->temperature);
        }
        // Check if the request has a max_tokens key and if so sets it in the max_tokens variable.
        if ($request->has('max_tokens')) {
            $max_tokens = intval($request->max_tokens);
        }


        try {
            // Get the chat model response or catch the exception
            $content = $request['prompt'];
            if (is_null($content)) {
                $messages = config('openai.presets.chat.assistant');
            } else {
                $preset = config('openai.presets.chat.assistant');
                $role = 'user';
                $messages = [...$preset, compact('role', 'content')];
            }
            //dd($messages);
            $payload = [
                'messages' => $messages,
                'temperature' => $temperature ??= 0,
                'model' => $model ??= 'gpt-3.5-turbo',
                'max_tokens' => $max_tokens ??= 1500
            ];
            //dd($payload);
            $response = $ai->chat($payload);
        } catch (Exception $error) {
            //dd($error->getMessage());
            //return redirect()->back()->with('msssage', $error->getMessage());
            return $error->getMessage();
        }
        //dd($answer);
        //dd($ai->getAnswer($response));
        // check if the response doesn't contain an aswer and return a json error response


        if (!$ai->getAnswer($response)) {
            //dd('here we are');
            return [
                'success' => false,
                'error' => $ai->getFailureMessage($response)
            ];
        }
        // otherwise get the answer
        $answer = $ai->getAnswer($response);
        //dd($answer);
        //dd($conversation->id);
        // store the message response
        $aiMessage = $this->storeAiReplyMessage($conversation->id, $answer);

        //Generates a conversation summary based on the sent message
        if ($conversation->messages->count() <= 2) {
            $conversation_messages = $conversation->messages->pluck('body')->join('');
            $title = $ai->text_complete("Generate a title for the new conversation below:\n", $conversation_messages);
            $conversation->update(['summary' => $title]);
        }
        // redirect back
        return response()->json([
            'success' => true,
            'message' => Message::where('id', $aiMessage->id)->with(
                [
                    'issues',
                    'conversation' => [
                        'user:id,name'
                    ]
                ]
            )->first(),
        ]);
    }



    private function storeSentMessage($conversationId, $body)
    {
        //dd($conversationId);
        Message::create([
            'body' => $body,
            'status' => 'sent',
            'conversation_id' => $conversationId
        ]);
    }

    private function storeAiReplyMessage($conversationId, $body)
    {
        //dd($conversationId);

        //dd($conversationId, $body);
        $message = Message::create([
            'body' => $body,
            'status' => 'received',
            'conversation_id' => $conversationId
        ]);
        //dd($message);
        return $message;
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
