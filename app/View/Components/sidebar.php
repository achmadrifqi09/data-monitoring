<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class sidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public $menus = [
        ['label' => 'Dashboard', 'path' => '/', 'routeName' => 'dashboard'],
        ['label' => 'BPL', 'path' => '/bpl', 'routeName' => 'bpl.index'],
        ['label' => 'Rekanan', 'path' => '/rekanan', 'routeName' => 'rekanan.index'],
    ];
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar');
    }
}
