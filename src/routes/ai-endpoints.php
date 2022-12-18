<?php

use App\Models\Message;
use PacificDev\LaravelOpenAi\Services\OpenAi;
use Illuminate\Http\Request;

/* Add the message in the messages table */


Route::group([
  'namespace' => 'Closure',
  'prefix' => 'pacificdev-ai',
], function () {

  Route::post('/ai-completation', function (Request $request, OpenAi $ai) {
    //dd($request->all());

    // validate the message
    $request->validate([
      'prompt' => 'required'
    ]);
    $sent_message = new Message();
    $sent_message->body = $request['prompt'];
    $sent_message->status = 'sent';
    $sent_message->status = 'sent';
    $sent_message->save();
    //dd($val_data);
    // create a new object

    /* TODO:
      The first parameter should be a string of the entire conversation taken from the db and passed as string
      $istructions = $conversation->messages->all();
    */

    // get all messages body as a string
    $messages_count = count(Message::all('body'));
    if ($messages_count > 0) {
      $istructions = Message::all('body')->map(function ($message) {
        return $message->body;
      });
      // transform them in a string
      $istructions = collect($istructions)->join('');
    } else {
      $istructions = config('openai.presets.completation');
    }

    // pass the istructions as parameter of the ai->text_complete method
    // save the ai response
    $text = $ai->text_complete($istructions, $request['prompt'], $request['model'] ?? 'text-davinci-003');
    // create a new message
    Message::create(['status' => 'received', 'body' => $text]);


    return redirect()->back()->with('chat', $text);
  })->name('ai.complete');
});
