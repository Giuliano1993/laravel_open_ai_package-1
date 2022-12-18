<?php

namespace PacificDev\LaravelOpenAi\Views\Components;

use Illuminate\View\Component;


class AiConversation extends Component
{


  public $messages;

  public function __construct($messages)
  {
    $this->messages = $messages;
  }

  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\Contracts\View\View|\Closure|string
   */
  public function render()
  {
    return view('pacificdev::ai-conversation');
  }
}
