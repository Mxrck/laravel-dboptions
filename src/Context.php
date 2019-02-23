<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 22/02/2019
 * Time: 04:56 PM
 */

namespace Nitro\Options;


use Nitro\Options\Contracts\OptionableInterface;

/**
 * Class Context
 * @package Nitro\Options
 * @property  OptionableInterface $optionable
 * @property  bool $fallback
 */
class Context
{
    protected $optionable;
    protected $fallback;

    public function __construct(OptionableInterface $optionable, bool $fallback = false)
    {
        $this->optionable   = $optionable;
        $this->fallback     = $fallback;
    }

    public static function make(OptionableInterface $optionable, bool $fallback = false)
    {
        return new Context($optionable, $fallback);
    }

    public function __get($name)
    {
        if ($name === 'optionable')
        {
            return $this->optionable;
        }
        if ($name === 'fallback')
        {
            return $this->fallback;
        }
        return null;
    }

    public function __isset($name)
    {
        if ($name === 'optionable' && $this->optionable !== null)
        {
            return true;
        }
        if ($name === 'fallback')
        {
            return true;
        }
        return false;
    }
}