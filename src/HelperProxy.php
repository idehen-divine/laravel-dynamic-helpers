<?php

namespace L0n3ly\LaravelDynamicHelpers;

class HelperProxy
{
    public static function __callStatic($name, $arguments)
    {
        return helpers()->{$name}(...$arguments);
    }
}
