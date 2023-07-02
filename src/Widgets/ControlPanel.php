<?php

namespace PinaDashboard\Widgets;

use Pina\Access;
use Pina\App;
use Pina\Controls\Control;
use Pina\Controls\ActionLink;
use Pina\Html;
use function Pina\__;

class ControlPanel extends Control
{
    /**
     * @return string
     * @throws \Exception
     */
    protected function draw()
    {
        if (!Access::isPermitted('admin')) {
            return '';
        }

        return Html::zz(
            'nav.control-panel(ul.nav bar(li.dropdown(a[href=#]%+.dropdown-menu%)+li(%)))',
            __('Управление'),
            $this->drawMenu(),
            $this->drawLogout()
        );
    }

    protected function drawMenu()
    {
        /** @var Menu $menu */
        $menu = App::make(Menu::class);
        $menu->addClass('nav');
        return $menu;
    }

    protected function drawLogout()
    {
        /** @var ActionLink $logout */
        $logout = App::make(ActionLink::class);
        $logout->setHandler('auth', 'delete');
        $logout->setTitle('Выйти');
        return $logout;
    }

}