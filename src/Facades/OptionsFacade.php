<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 20/02/2019
 * Time: 10:15 PM
 */

namespace Nitro\Options\Facades;


use Illuminate\Support\Facades\Facade;

class OptionsFacade extends Facade
{
    public static function getFacadeAccessor(){return 'Options';}
}