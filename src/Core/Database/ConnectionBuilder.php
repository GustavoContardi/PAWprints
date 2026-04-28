<?php

namespace Core\Database;

class ConnectionBuilder
{
    public static function make()
    {
        return new Connection();
    }
}