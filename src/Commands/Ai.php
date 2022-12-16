<?php

namespace PacificDev\LaravelOpenAi\Commands;


use Illuminate\Console\Command;
use InvalidArgumentException;
use Illuminate\Support\Arr;

class Ai extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'ai:assistant
    { type : The preset type ( blade | vue ) }
    ';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generates an ai assistant blade | vue component';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    //var_dump($this->argument('type'));
    if ($this->argument('type') == 'blade') {
      $this->blade();
    }
    if ($this->argument('type') == 'vue') {
      # Install the auth preset - run preset:auth
      $this->vue();
    }
    if (Arr::has(['blade', 'vue'], $this->argument('type'))) {
      # Invalid preset
      throw new InvalidArgumentException('Invalid Command Syntax. Add a valid preset. Presets available: blade or vue');
    }
  }

  public function blade()
  {
    Presets\Blade\AssistantCommand::install();
    $this->info('Ai Assistant component installed!');
    $this->warn('Now you can run: [npm i && npm run dev] to compile all assets');
  }
  public function vue()
  {
    Presets\Vue\AssistantAuthCommand::install();
    $this->info('Bootstrap, Sass, and Vite Authentication scaffolding setup successful');
    $this->warn('Now you can run: [npm i && npm run dev] to compile all assets');
  }
}
