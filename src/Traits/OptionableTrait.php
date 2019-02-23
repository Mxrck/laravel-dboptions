<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 22/02/2019
 * Time: 01:38 PM
 */

namespace Nitro\Options\Traits;


use Nitro\Options\Context;

trait OptionableTrait
{
    protected $option_type = self::class;

    public function getType(): string
    {
        return $this->option_type;
    }

    public function getId(): int
    {
        return $this->attributes['id'] ?? null;
    }

    public function option(string $key = null, $default = null, $reload = false)
    {
        if ($key === null) return app()->make('Options')->context(Context::make($this));
        return \Options::context(Context::make($this))->get($key, $default, $reload);
    }

    public function optionFallback(string $key = null, $default = null, $reload = false)
    {
        if ($key === null) return app()->make('Options')->context(Context::make($this, true));
        return \Options::context(Context::make($this, true))->get($key, $default, $reload);
    }

    public function options()
    {
        return $this->morphMany($this->getType(), 'optionable');
    }

}