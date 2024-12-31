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
        ['label' => 'Dashboard', 'path' => '/', 'routeName' => 'dashboard', 'icon' => 'fa-solid fa-gauge-high', 'permission' => 'view_dashboard'],
        ['label' => 'BPL', 'path' => '/bpl', 'routeName' => 'bpl', 'icon' => 'fa-solid fa-hammer', 'permission' => 'bpl_view'],
        ['label' => 'Rekanan', 'path' => '/partner', 'routeName' => 'partner', 'icon' => 'fa-solid fa-people-group', 'permission' => 'partner_view'],
        ['label' => 'Order', 'path' => '/order', 'routeName' => 'order', 'icon' => 'fa-solid fa-cart-shopping', 'permission' => 'order_view'],
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
