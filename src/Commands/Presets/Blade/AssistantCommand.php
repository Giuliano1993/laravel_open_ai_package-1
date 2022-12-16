<?php

namespace PacificDev\LaravelOpenAi\Commands\Presets\Blade;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class AssistantCommand
{

  public static function install()
  {
    /* Add Alpine js to the package.json */
    if (File::exists(base_path('package.json'))) {
      $packages = [
        "alpinejs" => "^3.10.5",
      ];
      $packages_array = json_decode(file_get_contents(base_path('package.json')), true);
      if (!Arr::has($packages_array, 'dependencies')) {
        $packages_array['dependencies'] = $packages;
      } else if (!Arr::has($packages_array['dependencies'], 'alpinejs')) {
        Arr::add($packages_array['dependencies'], 'alpinejs', '^3.10.5');
      }
    }

    // Update the app.js file
    if (File::exists(resource_path('js/app.js'))) {

      $data = [
        "import Alpine from 'alpinejs'", "window.Alpine = Alpine",
        "Alpine.start()"
      ];
      $data = implode(';', $data);

      File::append(resource_path('js/app.js'), $data);
    }
  }
}
