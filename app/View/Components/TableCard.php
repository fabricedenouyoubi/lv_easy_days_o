<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableCard extends Component
{
    public $title;
    public $icon;
    public $buttonText;
    public $buttonIcon;
    public $buttonAction;
    public $canAdd;
    public $link;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $title,
        $icon = null,
        $buttonText = null,
        $buttonIcon = 'fas fa-plus',
        $buttonAction = null,
        $canAdd = true,
        $link = null
    )
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->buttonText = $buttonText;
        $this->buttonIcon = $buttonIcon;
        $this->buttonAction = $buttonAction;
        $this->canAdd = $canAdd;
        $this->link = $link;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.table-card');
    }
}
