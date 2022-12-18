<?php

namespace PacificDev\LaravelOpenAi\Views\Components;

use Illuminate\View\Component;


class AiPrompt extends Component
{

  public $url;

  /**
   * Create a new component instance.
   *
   * @return void
   */
  public function __construct($url)
  {

    $this->url = $url;
  }

  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\Contracts\View\View|\Closure|string
   */
  public function render()
  {
    return view('pacificdev::ai-prompt');
  }
}
