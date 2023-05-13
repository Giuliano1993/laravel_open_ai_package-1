<?php

use PacificDev\LaravelOpenAi\Commands\Presets\Blade\AssistantCommand;
use Illuminate\Support\Facades\File;

it('adds AlpineJS dependencies to package.json', function () {


    // Create a temporary package.json file
    $temp_json = tempnam(sys_get_temp_dir(), 'package.json');
    file_put_contents($temp_json, '{}');

    // Call the install method
    AssistantCommand::add_alpine_js_to_package_json($temp_json);
    //AssistantCommand::install($temp_json, $temp_js);

    // Check if Alpine js is added to package.json
    $packages_array = json_decode(file_get_contents(base_path('package.json')), true);
    expect($packages_array['dependencies']['alpinejs'])->toBe('^3.10.5');

    //unlink($temp_json);
})->group('commands');

it('updates app.js file', function () {
    // Create a temporary app.js file
    $temp_js = tempnam(sys_get_temp_dir(), 'app.js');
    file_put_contents($temp_js, '{}');

    AssistantCommand::update_app_js_file($temp_js);

    $appJsContent = file_get_contents($temp_js);
    expect($appJsContent)->toContain("import Alpine from 'alpinejs'");
    expect($appJsContent)->toContain("window.Alpine = Alpine");
    expect($appJsContent)->toContain("Alpine.start()");
    //unlink($temp_js);
})->group('commands');
