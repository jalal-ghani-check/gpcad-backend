<?php

namespace App\Models;

class Connection
{
    protected static $__connection;

    public static function get($server, $options = [])
    {
        if (! self::$__connection) {
            self::$__connection = new \MongoDB\Driver\Manager($server, $options);
        }

        return self::$__connection;
    }
}
