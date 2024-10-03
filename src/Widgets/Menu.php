<?php

namespace PinaDashboard\Widgets;

use Pina\App;
use Pina\Controls\Control;
use Pina\Controls\Nav\Nav;
use PinaDashboard\Dashboard;

class Menu extends Nav
{

    public function __construct()
    {
        parent::__construct();

        /** @var Dashboard $dashboard */
        $dashboard = App::load(Dashboard::class);
        foreach ($dashboard as $section) {
            $section->getMenu($this);
        }
    }

}