<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 20/02/2019
 * Time: 10:24 PM
 */
if (!function_exists('option'))
{
    function option(string $key = null, $default = null, $reload = false)
    {
        if ($key === null) return app()->make('Options');
        return Options::get($key, $default, $reload);
    }
}
if (!function_exists('option_update'))
{
    function option_update(string $key, $value, array $meta = [])
    {
        return Options::update($key, $value, $meta);
    }
}
if (!function_exists('option_remove'))
{
    function option_remove(string $key)
    {
        return Options::remove($key);
    }
}
if (!function_exists('option_exists'))
{
    function option_exists(string $key)
    {
        return Options::exists($key);
    }
}