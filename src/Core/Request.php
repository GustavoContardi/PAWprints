<?php

namespace Core;

class Request
{
    public string $method;
    public string $path;
    public array $params = [];

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path   = strtok($_SERVER['REQUEST_URI'], '?');
    }

    public function method()
    {
        return $this->method;
    }

    public function path()
    {
        return $this->path;
    }

    public function params()
    {
        return $this->params;
    }

    public function setParam(string $key, $value)
    {
        $this->params[$key] = $value;
    }      
}