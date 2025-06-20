<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ActionButton extends Component
{
    public $type;
    public $size;
    public $icon;
    public $text;
    public $wireClick;
    public $href;
    public $tooltip;
    public $disabled;
    public $loading;
    public $loadingTarget;
    public $typeButton;
    public $dataBsToogle;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $type = 'primary',
        $size = 'sm',
        $icon = null,
        $text = null,
        $wireClick = null,
        $href = null,
        $tooltip = null,
        $disabled = false,
        $loading = false,
        $loadingTarget = null,
        $typeButton = null,
        $dataBsToogle = null,
    )
    {
        $this->type = $type;
        $this->size = $size;
        $this->icon = $icon;
        $this->text = $text;
        $this->wireClick = $wireClick;
        $this->href = $href;
        $this->tooltip = $tooltip;
        $this->disabled = $disabled;
        $this->loading = $loading;
        $this->loadingTarget = $loadingTarget;
        $this->typeButton = $typeButton;
        $this->dataBsToogle = $dataBsToogle;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.action-button');
    }
}
