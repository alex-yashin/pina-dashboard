<?php


namespace PinaDashboard;


use Exception;
use Pina\Access;
use Pina\App;
use Pina\Container\NotFoundException;
use Pina\Controls\Control;
use Pina\Controls\Nav\Nav;
use Pina\Http\Location;
use Pina\Model\LinkedItem;
use Pina\Model\LinkedItemCollection;

class Section
{
    protected $title = '';
    protected $location;
    protected $endpoints = [];

    public function __construct(string $title, Location $location)
    {
        $this->title = $title;
        $this->location = $location;
    }

    public function register(string $pattern, $class, $context = [])
    {
        App::router()->register($this->location->resource('@') . '/' . $pattern, $class, $context);
        if (strpos($pattern, '/') === false) {
            //обратные ссылки на вложенные коллекции не регистрируем
            $this->endpoints[$class] = $pattern;
        }
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
        foreach ($this->endpoints as $pattern) {
            if (strpos($pattern, '/') !== false) {
                continue;
            }

            $resource = $this->location->resource('@/' . $pattern);
            if (!Access::isPermitted($resource)) {
                continue;
            }

            try {
                $title = App::router()->run($resource, 'title');
                if ($title) {
                    $control->appendLink($title, '/' . $resource);
                }
            } catch (Exception $e) {
            }
        }
    }

}