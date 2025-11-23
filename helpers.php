<?php

use L0n3ly\LaravelDynamicHelpers\Helper;

if (! function_exists('helpers')) {
    function helpers(): Helper
    {
        return Helper::getInstance();
    }
}
