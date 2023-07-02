<?php

namespace PinaDashboard;

use Exception;
use Pina\Access;
use Pina\App;
use Pina\Container\NotFoundException;
use Pina\Http\Location;
use Pina\Model\LinkedItem;
use Pina\Url;

class Dashboard
{
    protected $endpoints = [];

    public function __construct()
    {
        Access::permit('admin', 'root');
    }

    public function register(string $pattern, $class, $context = [])
    {
        App::router()->register($this->getBasePattern() . '/' . $pattern, $class, $context);
        $this->endpoints[$class] = $pattern;
    }

    public function getBasePattern()
    {
        return 'admin/:lang';
    }

    public function getBaseLocation(): Location
    {
        return new Location(Url::resource($this->getBasePattern(), ['lang' => 'en']));
    }

    public function getEndpointLocation($class): Location
    {
        if (!isset($this->endpoints[$class])) {
            throw new NotFoundException();
        }

        $pattern = $this->endpoints[$class];
        return $this->getBaseLocation()->location('@/' . $pattern);
    }

    /**
     * @return LinkedItem[]
     */
    public function getMenu()
    {
        $menu = [];
        foreach ($this->endpoints as $pattern) {
            if (strpos($pattern, '/') !== false) {
                continue;
            }

            $resource = $this->getBaseLocation()->resource('@/' . $pattern);
            if (!Access::isPermitted($resource)) {
                continue;
            }

            try {
                $title = App::router()->run($resource, 'title');
                if ($title) {
                    $menu[] = new LinkedItem($title, '/' . $resource);
                }
            } catch (Exception $e) {
            }
        }
        return $menu;
    }

}