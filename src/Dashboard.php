<?php

namespace PinaDashboard;

use Pina\Access;
use Pina\Container\NotFoundException;
use Pina\Http\Location;
use Iterator;
use Countable;

class Dashboard implements Iterator, Countable
{
    protected $endpoints = [];

    /** @var Section[] */
    protected $sections = [];

    /** @var Location */
    protected $location;

    protected $cursor = 0;

    public function __construct()
    {
        Access::permit('admin', 'root');
        $this->location = new Location('admin/en');
    }

    public function location($pattern, $params = []): Location
    {
        return $this->location->location($pattern, $params);
    }

    public function section(string $title)
    {
        $section = new Section($title, $this->location);
        $this->sections[] = $section;
        return $section;
    }

    public function getEndpointLocation($class): Location
    {
        foreach ($this->sections as $section) {
            if ($section->has($class)) {
                return $section->getEndpointLocation($class);
            }
        }
        throw new NotFoundException();
    }

    /**
     *
     * @return Section
     */
    public function current()
    {
        return $this->sections[$this->cursor];
    }

    public function key()
    {
        return $this->cursor;
    }

    public function next()
    {
        $this->cursor++;
    }

    public function rewind()
    {
        $this->cursor = 0;
    }

    public function valid()
    {
        return isset($this->sections[$this->cursor]);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->sections);
    }

}