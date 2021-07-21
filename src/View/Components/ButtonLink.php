<?php

namespace BrilliantPortal\Framework\View\Components;

use Illuminate\View\Component;

class ButtonLink extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $bg = '',
        public string $text = '',
        public string $hover = '',
        public string $active = '',
        public string $focus = '',
        public string $other = '',
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('brilliant-portal-framework::components.button-link');
    }
}
