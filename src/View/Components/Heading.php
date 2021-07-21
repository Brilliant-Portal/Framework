<?php

namespace BrilliantPortal\Framework\View\Components;

use Illuminate\View\Component;

class Heading extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $element = 'h1',
        public string $text = '',
        public string $margin = '',
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
        return view('brilliant-portal-framework::components.heading');
    }
}
