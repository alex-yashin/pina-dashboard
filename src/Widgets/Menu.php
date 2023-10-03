<?php

namespace PinaDashboard\Widgets;

use Pina\App;
use Pina\Controls\Nav;
use PinaDashboard\Dashboard;

class Menu extends Nav
{

    public function __construct()
    {
        /** @var Dashboard $dashboard */
        $dashboard = App::load(Dashboard::class);
        foreach ($dashboard as $section) {
            $menu = $section->getMenu();
            foreach ($menu as $item) {
                $this->add($item);
            }
        }
    }

}