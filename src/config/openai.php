<?php

return [
  "api_key" => env('OPENAI_API_KEY'),
  "endpoints" => [
    'completation' => 'https://api.openai.com/v1/completions',
    'edits' => 'https://api.openai.com/v1/edits',
    'images' => [
      'create' => 'https://api.openai.com/v1/images/generations',
      'edit' => '',
      'variation' => 'https://api.openai.com/v1/images/variations'
    ]

  ],

  "presets" => [
    "completation" => "Respond to the following questions. Place any code inside the pre and code tags. Show a list as an HTML list with `ul` or `ol` tags and put each item in a `li` tag.\n\nMe: How do you split a string in JavaStript?\nAI: To split a string by its caracters you can use a method called `.split()"
  ],

];
