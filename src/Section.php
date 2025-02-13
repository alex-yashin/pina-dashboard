<?php


namespace PinaDashboard;


use Pina\App;
use Pina\Container\NotFoundException;
use Pina\Controls\Nav\Nav;
use Pina\Http\Location;
use Pina\Router\Route;
use PinaDashboard\Menu\SectionMenu;

class Section
{
    protected $title = '';

    protected $location;

    /** @var SectionMenu */
    protected $menu;

    protected $groups = [];

    protected $endpoints = [];

    public function __construct(string $title, Location $location)
    {
        $this->title = $title;
        $this->location = $location;
        $this->menu = App::make(SectionMenu::class);
    }

    public function register(string $pattern, $class, $context = []): Route
    {
        $r = App::router()->register($this->location->resource('@') . '/' . $pattern, $class, $context)->addToMenu($this->menu);
        foreach ($this->groups as $group) {
            $r->permit($group);
        }
        if (strpos($pattern, '/') === false) {//обратные ссылки на вложенные коллекции не регистрируем
            $this->endpoints[$class] = $pattern;
        }
        return $r;
    }

    public function permit(string $group)
    {
        $this->groups[] = $group;
        return $this;
    }

    public function has($class): bool
    {
        return isset($this->endpoints[$class]);
    }

    public function getEndpointLocation($class): Location
    {
        if (!isset($this->endpoints[$class])) {
            throw new NotFoundException();
        }

        $pattern = $this->endpoints[$class];
        return $this->location->location('@/' . $pattern);
    }

    public function getMenu(Nav $control)
    {
        return $this->menu;
    }

}