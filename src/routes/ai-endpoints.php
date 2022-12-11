<?php

use PacificDev\LaravelOpenAi\Services\OpenAi;
use Illuminate\Http\Request;


Route::group([
  'namespace' => 'Closure',
  'prefix' => 'pacificdev-ai',
], function () {

  Route::post('/ai-completation', function (Request $request, OpenAi $ai) {
    $text = $ai->text_complete($request['istructions'], $request['prompt'], $request['model'] ?? 'text-davinci-003');
    return redirect()->back()->with('chat', $text);
  })->name('ai.complete');
});
