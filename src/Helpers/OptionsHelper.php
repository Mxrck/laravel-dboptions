<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 20/02/2019
 * Time: 10:24 PM
 */
if (!function_exists('option'))
{
    /**
     * Helper to get an option, the options instance, or the options instance with a context
     * @param string|\Nitro\Options\Context|\Nitro\Options\Contracts\OptionableInterface $key The key
     * to find the option
     * @param null $default The default value to return in case the option key doesn't exists
     * @param bool $reload if you don't want to use the collection cache, you can reload the value from the database
     * @return mixed|\Nitro\Options\Facades\Options
     */
    function option($key = null, $default = null, $reload = false)
    {
        if ($key === null) return app()->make('Options');
        if ($key instanceof \Nitro\Options\Context || $key instanceof \Nitro\Options\Contracts\OptionableInterface)
        {
            return Options::context($key);
        }
        return Options::get($key, $default, $reload);
    }
}
if (!function_exists('option_update'))
{
    /**
     * Update a value in the options table
     * @param string $key The key to be updated
     * @param $value The new value for the key
     * @param array $meta Array with metadata, by default only "autoload", "public", both booleans
     * @return mixed The new or updated stored value
     */
    function option_update(string $key, $value, array $meta = [])
    {
        return Options::update($key, $value, $meta);
    }
}
if (!function_exists('option_remove'))
{
    /**
     * Remove an option from the database
     * @param string $key The option key to be removed
     * @return bool
     */
    function option_remove(string $key)
    {
        return Options::remove($key);
    }
}
if (!function_exists('option_exists'))
{
    /**
     * Check if an option exists in the database
     * @param string $key
     * @return mixed
     */
    function option_exists(string $key)
    {
        return Options::exists($key);
    }
}
if (!function_exists('option_context'))
{
    function option_context(\Nitro\Options\Contracts\OptionableInterface $optionable, bool $fallback = false)
    {
        return \Nitro\Options\Context::make($optionable, $fallback);
    }
}