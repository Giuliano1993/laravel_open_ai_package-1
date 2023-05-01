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
                ["role" => "system", "content" => "You are a smart bilingual English and Italian assistant called Grogu AI. Reply to questions and write document proposals. Write code blocks and snippes as git flavoured markdown.Add reference links for the sources you use."],
                ["role" => "user", "content" => "what is PHP?"],
                ["role" => "assistant", "content" => "PHP is a general purpose programming language used to build: \n- Websites\n- Web Apps"],
                ["role" => "user", "content" => "show me a php function"],
                ["role" => "assistant", "content" => "<script type='text/plain' class='language-php'>function calcAge(){}</script>"],
                ["role" => "user", "content" => "write an html page for a shop"],
                ["role" => "assistant", "content" => 'Sure, here is an example of a basic html page.
                <script type="text/plain" class="language-markup">
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Sample Page</title>
                    </head>
                    <body>
                        <header>
                            <h1>Header</h1>
                        </header>
                        <main></main>
                        <footer></footer>
                    </body>
                    </html>
                </script>']
            ],
            "temperature" => 0,
            "max_tokens" => 2000
        ],
        "blog" => [
            "title" => [
                'instructions' => "Topics:\nFullstack Web Development\nREST API\nLaravel Development\nWriting tests (Pest, cypress, jest, mocha)\nPHP\nSQL\nHTML\nJS\nCSS\nSCSS\nVuejs\nAI in web development.\n\nAudience: Web Developers\n\n",
                "prompt" => "Pick a random topic from the list above and generate a title for a blog post.",
                "model" => "text-davinci-002",
                "temperature" => 0.6,
                "max_tokens" => 25
            ],
            "summary" => [
                'prompt' => "Summary generation form post title.",
                "model" => "text-davinci-002",
                "temperature" => 0.8,
                "max_tokens" => 200
            ],
            "content" => [
                'prompt' => "Given the excerpt above, generate a short tip article for fullstack web developers with two code snippets.",
                "model" => "text-davinci-002",
                "temperature" => 1,
                "max_tokens" => 2500
            ],
            "image" => [
                "prompt" => "Developer in a dark room with a purple and blue backlight, multi monitor setup with nice ui, ",
                "type" => [
                    "3d" => "3D render.",
                    "art" => "digital art.",
                    "pixel" => "pixel art."
                ]
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
