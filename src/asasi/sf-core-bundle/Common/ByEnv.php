<?php

namespace Dbh\SfCoreBundle\Common;

class ByEnv
{
    public static function get($key)
    {
        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        } else if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }
        return "";
    }
}
