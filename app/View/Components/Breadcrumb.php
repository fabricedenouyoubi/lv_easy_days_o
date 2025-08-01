<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public $items;
    public $showHome;

    /**
     * Create a new component instance.
     */
    public function __construct($items = [], $showHome = true)
    {
        $this->items = $items;
        $this->showHome = $showHome;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.breadcrumb');
    }
}
