<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 22/02/2019
 * Time: 03:45 PM
 */

namespace Nitro\Options\Contracts;

interface OptionableInterface
{
    public function getType()   : string;
    public function getId()     : int;
    public function option(string $key = null, $default = null, $reload = false);
}