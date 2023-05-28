<?php

namespace PacificDev\LaravelOpenAi\Services;

use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OpenAi
{
    private $timeout = 300;
    public function get_models()
    {
        #TODO: code...

    }

    public function get_model()
    {
        # TODO: code...
    }

    public function chat($content, $temperature = 0, $model = "gpt-3.5-turbo", $max_tokens = 2000)
    {
        //dd($content);
        //dd(config("openai.presets.chat.assistant"));
        if (is_null($content)) {
            $messages = config("openai.presets.chat.assistant");
        } else {
            $preset = config("openai.presets.chat.assistant");
            $role = 'user';
            $messages = [...$preset, compact('role', 'content')];
        }
        //dd($messages);
        $response = Http::withToken(config('openai.api_key'))->timeout($this->timeout)->post(
            config('openai.endpoints.chat.completations'),
            [
                "model" => $model,
                "messages" => $messages,
                "max_tokens" => $max_tokens,
                "temperature" => $temperature,
            ]
        );

        if ($response->successful()) {
            $answerText = json_decode($response->body(), true)['choices'][0]['message']['content'];
            // return the response
            return $answerText;
        }

        if ($response->failed()) {
            if ($response->clientError()) {
                return json_decode($response->body(), true)['error']['message'];
            }
            if ($response->serverError()) {
                return json_decode($response->body(), true);
            }
        }
    }

    /**
     * ### Makes requests to the Completation endpoint
     * Given a prompt, the model will return one or more predicted completions, and can also return the probabilities of alternative tokens at each position.
     * https://beta.openai.com/docs/api-reference/completions
     *
     * @param any $istructions - the initial istructions for the request
     * @param string $user_prompt - the text input provided by the user
     * @param string $model - the model id from the models list https://beta.openai.com/docs/models
     */
    public function text_complete(string $istructions, $user_prompt = "List five php advanced topics", string $model = 'text-davinci-003', $temperature = 0, $max_tokens = 2500)
    {

        if (is_null($istructions)) {
            $istructions = config('openai.presets.completation');
        }
        $full_prompt = $istructions . trim($user_prompt) . "\n\nAI: ";
        //dd($istructions, $user_prompt, $full_prompt, $model);
        try {

            $r = Http::withToken(config('openai.api_key'))->timeout($this->timeout)
                ->post(
                    config('openai.endpoints.completation'),
                    [
                        'prompt' => $full_prompt,
                        'model' => $model,
                        "max_tokens" => $max_tokens,
                        "temperature" => $temperature,
                        "echo" => false,
                        'stop' => ["\nMe: ", "\nAI: "]
                    ]
                );

            /* TODO: Need to manage the error better. When inserting an incorrect api key the core returns the stack trace referring to the choices key being null. */
            if ($r->successful()) {
                //dd(json_decode($r->body(), true));
                // retunr the response
                return json_decode($r->body(), true)['choices'][0]['text'];
            }

            $r->onError(function ($error) {
                $error_array = json_decode($error->body(), true);
                //var_dump($error_array['error']['message']);
                die($error_array['error']['message']);
            });
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * ### Makes requests to the edits endpoint
     * Given a prompt and an instruction, the model will return an edited version of the prompt.
     * https://beta.openai.com/docs/api-reference/edits
     */
    public function text_edit()
    {
        # code...
    }

    /** ### Makes requests to the endpoint IMAGES CREATE
     * Given a prompt and/or an input image, the model will generate a new image.
     *
     * API reference: https://beta.openai.com/docs/api-reference/images/create
     * Guide: https://beta.openai.com/docs/guides/images
     *
     */

    public function generateImages($prompt = 'Generate an image of a black cat')
    {
        // call the api endpoint

        // handle the response and return the generated image
        try {

            $r = Http::withToken(config('openai.api_key'))->timeout($this->timeout)
                ->post(
                    config('openai.endpoints.images.create'),
                    [
                        'prompt' => $prompt,
                        "n" => 1,
                        "size" => "512x512",
                        "response_format" => 'b64_json'
                    ]
                );

            /* TODO: Need to manage the error better. When inserting an incorrect api key the core returns the stack trace referring to the choices key being null. */
            if ($r->successful()) {
                //dd(json_decode($r->body(), true)['data']);
                $image_b64  = json_decode($r->body(), true)['data'][0]['b64_json'];

                // retunr the response
                $image = base64_decode($image_b64);
                return $image;
            }

            $r->onError(function ($error) {
                $error_array = json_decode($error->body(), true);
                //var_dump($error_array['error']['message']);
                die($error_array['error']['message']);
            });
        } catch (Exception $e) {
            return $e;
        }
    }

    /** ### Makes requests to the endpoint IMAGES EDIT
     * Given a prompt and/or an input image, the model will generate a new image.
     *
     * API reference: https://beta.openai.com/docs/api-reference/images/create-edit
     * Guide: https://beta.openai.com/docs/guides/images
     *
     */


    /** ### Makes requests to the endpoint IMAGES VARIATION
     * Given a prompt and/or an input image, the model will generate a new image.
     *
     * API reference: https://beta.openai.com/docs/api-reference/images/create-variation
     * Guide: https://beta.openai.com/docs/guides/images
     *
     */
}
