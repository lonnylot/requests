<?php

namespace Requests;

class Headers implements \ArrayAccess
{
    private $container = [];

    /**
     * Construct a new Headers object and set the headers on instantiation
     *
     * @api
     *
     * @param array $headers The headers to be set
     */
    public function __construct(array $headers)
    {
        $this->container = $headers;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->container);
    }

    public function offsetGet($offset)
    {
        return array_key_exists($offset, $this->container) ? $this->container[$offset] : null;
    }

    public function offsetSet($offset,$value)
    {
        return false;
    }

    public function offsetUnset($offset)
    {
        return false;
    }

}