<?php

namespace PacificDev\LaravelOpenAi\Commands\Presets\Blade;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;


class AssistantCommand
{

    public static function install()
    {
        self::add_alpine_js_to_package_json(base_path('package.json'));
        // Update the app.js file
        self::update_app_js_file(resource_path('js/app.js'));
    }

    public static function add_alpine_js_to_package_json($packages_path)
    {
        /* Add Alpine js to the package.json */
        if (File::exists($packages_path)) {
            $packages = [
                "alpinejs" => "^3.10.5",
            ];
            $packages_array = json_decode(file_get_contents($packages_path), true);
            if (!Arr::has($packages_array, 'dependencies')) {
                $packages_array['dependencies'] = $packages;
            } else if (!Arr::has($packages_array['dependencies'], 'alpinejs')) {
                Arr::add($packages_array['dependencies'], 'alpinejs', '^3.10.5');
            }
        }
    }

    public static function update_app_js_file($js_path)
    {
        if (File::exists($js_path)) {

            $data = [
                "import Alpine from 'alpinejs'", "window.Alpine = Alpine",
                "Alpine.start()"
            ];
            $data = implode(';', $data);

            File::append($js_path, $data);
        }
    }
}
