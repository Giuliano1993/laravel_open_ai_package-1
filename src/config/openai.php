<?php

return [
    "api_key" => env('OPENAI_API_KEY'),
    "endpoints" => [
        'chat' => [
            'completations' => 'https://api.openai.com/v1/chat/completions',
            'edits' => 'https://api.openai.com/v1/edits'
        ],
        'completation' => 'https://api.openai.com/v1/completions',
        'edits' => 'https://api.openai.com/v1/edits',
        'images' => [
            'create' => 'https://api.openai.com/v1/images/generations',
            'edit' => '',
            'variation' => 'https://api.openai.com/v1/images/variations'
        ]

    ],

    "presets" => [
        "chat" => [
            "assistant" => [
                ["role" => "system", "content" => "You are a smart bilingual English and Italian assistant. You will reply to questions and write document proposals using the appropriate format.\nShow a list as an HTML list with `ul` or `ol` tags and put each item in a `li` tag"],
                ["role" => "user", "content" => "what is PHP?"],
                ["role" => "assistant", "content" => "PHP is a general purpose programming language used to build: \n- Websites\n- Web Apps"],
                ["role" => "user", "content" => "show me a php function"],
                ["role" => "assistant", "content" => "<script type='text/plain' class='language-markup'>function calcAge(){}</code></script>"],
                ["role" => "user", "content" => "show me a sample html page"],
                ["role" => "assistant", "content" => 'Sure, here\'s an example of a basic html page <script type="text/plain" class=\'language-html\'>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Document</title>
                </head>
                <body>
                    <header></header>
                    <main></main>
                    <footer></footer>
                </body>
                </html>
                </script>']
            ]
        ],
        "completation" => "You are a smart assistant that reply to questions asked. When you reply, you must:\n
        Write any code inside the pre and code tags\nShow a list as an HTML list with `ul` or `ol` tags and put each item in a `li` tag.\n
        Examples:\n
        \nMe: How do you split a string in JavaStript?
        \nAI: To split a string by its caracters you can use a method called `.split()\n
        \nMe:"
    ],

];
