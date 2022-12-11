<?php

namespace PacificDev\LaravelOpenAi\Services;

use Illuminate\Support\Facades\Http;
use Exception;
class OpenAi
{

    public function get_models()
    {
        # code...
    }

    public function get_model()
    {
        # code...
    }

    /* TODO - Get all choices! now taking only one prediction from the choices array!  */
    /**
     * ### Makes requests to the Completation endpoint
     * Given a prompt, the model will return one or more predicted completions, and can also return the probabilities of alternative tokens at each position.
     * https://beta.openai.com/docs/api-reference/completions
     *
     * @param any $istructions - the initial istructions for the request
     * @param string $user_prompt - the text input provided by the user
     * @param string $model - the model id from the models list https://beta.openai.com/docs/models
     */
    public function text_complete($istructions, string $user_prompt, string $model='text-davinci-003')
    {


        if(is_null($istructions)){
            $istructions = config('openai.presets.completation');
        }
        $full_prompt = $istructions . trim($user_prompt) . "\n\nAI: ";
        //dd($istructions, $user_prompt, $full_prompt, $model);
        try {
            //code...

            $r = Http::withToken(config('openai.api_key'))
                ->post(config('openai.endpoints.completation'),
                    [
                        'prompt' => $full_prompt,
                        'model' => $model,
                        "max_tokens" => 600,
                        'echo'=> true,
                        'stop' => ["\nMe: ", "\nAI: "]
                    ]
                );

            //dd($r->body());
            return json_decode($r->body(), true)['choices'][0]['text'] . "\n\nMe: ";
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