<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FilterCard extends Component
{
    public $title;
    public $filterAction;
    public $resetAction;
    /**
     * Create a new component instance.
     */
    public function __construct(
        $title = 'Filtres',
        $filterAction = 'filter',
        $resetAction = 'resetFilters'
    )
    {
        $this->title = $title;
        $this->filterAction = $filterAction;
        $this->resetAction = $resetAction;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.filter-card');
    }
}
