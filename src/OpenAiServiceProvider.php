<?php

namespace PacificDev\LaravelOpenAi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
//use PacificDev\LaravelOpenAi\Views\Components\AiChat;
use Illuminate\Support\Facades\File;

class OpenAiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // load views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'pacificdev');
        // load components
        Blade::componentNamespace('PacificDev\\LaravelOpenAi\\Views\\Components', 'pacificdev');

        /* Check if the route file exists */
        if (!File::exists(base_path('routes/ai-endpoints.php'))) {
            // Add the routes file
            File::copy(__DIR__ . '/routes/ai-endpoints.php', base_path('routes/ai-endpoints.php'));

            // Append at the end of the web.php file our ai-endpoints.php require __DIR__ . '/ai-endpoints.php'
            $web_php_file = 'routes/web.php';
            $this->append_to_file($web_php_file, "require __DIR__ . '/ai-endpoints.php';");

            // Append the OPEN_API_KEY to the .env file
            $env_file_path =  '.env';
            $this->append_to_file($env_file_path, 'OPENAI_API_KEY=your_api_key_goes_here');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                \PacificDev\LaravelOpenAi\Commands\Ai::class,
            ]);
        }


        // TODO:? publish vendor assets

        // TODO: when assets are published we should make sure the css or scss folders exists.
        /* if(is_dir(resource_path('css/'))) {
            $this->publishes([
                __DIR__ . '/assets/styles/chat.css' => resource_path('css/chat.css')
            ]);
        } */

        // TODO: if want to use scss to style components we should also add the dependency in the package.json
        /* if (is_dir(resource_path('scss/'))) {
            $this->publishes([
                //__DIR__ . '/assets/styles/chat.scss' => resource_path('scss/chat.scss')
            ]);
        }
        */
        $this->publishes([
            //__DIR__ . '/config/openai.php' => config_path('openai.php'),
            __DIR__ . '/resources/views' => resource_path('views/vendor/pacificdev'),
            //__DIR__ . '/assets/js/' => resource_path('js/'),
        ]);
    }

    private function append_to_file($file, string $contents)
    {
        $file_path = base_path($file);
        $file_contents = file_get_contents($file_path);
        $file_contents .= $contents;
        file_put_contents($file_path, $file_contents);
    }
}
