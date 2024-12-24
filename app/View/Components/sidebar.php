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
        ['label' => 'Dashboard', 'path' => '/', 'routeName' => 'dashboard', 'icon' => 'solar-spedometer-max-bold'],
        ['label' => 'BPL', 'path' => '/bpl', 'routeName' => 'bpl', 'icon' => 'solar-bill-list-bold'],
        ['label' => 'Rekanan', 'path' => '/rekanan', 'routeName' => 'partner', 'icon' => 'solar-users-group-two-rounded-bold'],
        ['label' => 'Order', 'path' => '/order', 'routeName' => 'order', 'icon' => 'solar-cart-large-4-bold'],
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
