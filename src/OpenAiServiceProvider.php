<?php

namespace PacificDev\LaravelOpenAi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
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
    public function boot(): void
    {
        // load views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'pacificdev');
        // load components
        $this->loadComponents();
        // Copy Routes
        $this->loadRoutes();
        // Copy Models
        $this->loadModels();
        // Copy Controllers
        $this->loadControllers();
        // Copy form requests
        $this->loadFormRequests();
        // Copy Tests
        $this->loadTests();

        // load migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');


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


    // Helpers
    private function loadComponents()
    {
        Blade::componentNamespace('PacificDev\\LaravelOpenAi\\Views\\Components', 'pacificdev');
    }
    private function loadRoutes()
    {
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

        if (!File::exists(base_path('routes/git-endpoints.php'))) {
            // Add the routes file
            File::copy(__DIR__ . '/routes/git-endpoints.php', base_path('routes/git-endpoints.php'));

            // Append at the end of the web.php file our git-endpoints.php require __DIR__ . '/git-endpoints.php'
            $web_php_file = 'routes/web.php';
            $this->append_to_file($web_php_file, "require __DIR__ . '/git-endpoints.php';");

            // Append the OPEN_API_KEY to the .env file
            $env_file_path =  '.env';
            $this->append_to_file($env_file_path, 'BITBUCKET_KEY=BIT_BUCKET_KEY_HERE');
            $this->append_to_file($env_file_path, 'BITBUCKET_SECRET=SECRET_HERE');
        }
    }

    private function loadModels()
    {
        File::copy(__DIR__ . '/Models/Conversation.php', base_path('app/Models/Conversation.php'));
        File::copy(__DIR__ . '/Models/GitProvider.php', base_path('app/Models/GitProvider.php'));
        File::copy(__DIR__ . '/Models/Message.php', base_path('app/Models/Message.php'));
        File::copy(__DIR__ . '/Models/User.php', base_path('app/Models/User.php'));
    }

    private function loadControllers()
    {

        // Copy controllers
        File::copy(__DIR__ . '/Http/Controllers/Chat/ConversationController.php', base_path('app/Http/Controllers/Chat/ConversationController.php'));
        File::copy(__DIR__ . '/Http/Controllers/Chat/ConversationMessageController.php', base_path('app/Http/Controllers/Chat/ConversationMessageController.php'));
        File::copy(__DIR__ . '/Http/Controllers/Git/GitController.php', base_path('app/Http/Controllers/Git/GitController.php'));
    }

    private function loadFormRequests()
    {
        File::copy(__DIR__ . '/Http/Requests/StoreMessageRequest.php', base_path('app/Http/Requests/StoreMessageRequest.php'));
        File::copy(__DIR__ . '/Http/Requests/UpdateConversationRequest.php', base_path('app/Http/Requests/UpdateConversationRequest.php'));
    }

    private function loadTests()
    {
        File::copy(__DIR__ . '/tests/Feature/Chat/ConversationsTest.php', base_path('tests/Feature/Chat/ConversationsTest.php'));
    }

    private function append_to_file($file, string $contents)
    {
        $file_path = base_path($file);
        $file_contents = file_get_contents($file_path);
        $file_contents .= $contents;
        file_put_contents($file_path, $file_contents);
    }
}
