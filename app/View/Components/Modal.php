<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public $show;
    public $title;
    public $icon;
    public $size;
    public $closeAction;
    public $showFooter;
    /**
     * Create a new component instance.
     */
    public function __construct(
       $show = false,
        $title = '',
        $icon = null,
        $size = 'lg',
        $closeAction = 'closeModal',
        $showFooter = true
    ) {
        $this->show = $show;
        $this->title = $title;
        $this->icon = $icon;
        $this->size = $size;
        $this->closeAction = $closeAction;
        $this->showFooter = $showFooter;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal');
    }
}
