# AI-API

This Laravel package provides an easy API for interacting with the Open AI REST API. It provides simple Blade components that make it easy to access the many features of this powerful REST API, including natural language processing, computer vision, and more. It is secure, efficient, and will ensure a seamless experience integrating Open AI with your Laravel project.

## Istallation

add the package to your laravel project using
`composer require pacificdev/ai`

## Configuration

To use this package you need an api key that you can get by signing up on [openai.com](https://beta.openai.com/)

- add your api key to the .env file

`OPENAI_API_KEY=your_api_key_goes_here`

## Components

- ai-assistant component (blade) name x-pacificdev::ai-assistant

## Usage

When you install the package you will notice a new config/openai.php file. Use its data to get the initial istructions
to pass to the ai-assistant component.

In your route closure or inside a controller get from the config file the preset istructions for the component
and make sure to pass this variable to the view.

```php

Route::get('/', function () {
    $text = config('openai.presets.completation');
    return view('welcome', compact('text'));
});
```

Next use the component inside the view

```php

<x-pacificdev::ai-assistant text="{{$text}}" url="{{ route('ai.complete')}}" />
```

The component expects two properties, the text (from the preset) and the url of the completation endpoint as above/

## Publish package files

To get the package files run

```bash
php artisan vendor:publish
```
