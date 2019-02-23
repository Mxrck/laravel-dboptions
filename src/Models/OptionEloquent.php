<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 20/02/2019
 * Time: 04:18 PM
 */

namespace Nitro\Options\Models;


use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Query\Builder;
use Nitro\Options\Contracts\OptionableInterface;

/**
 * Class OptionEloquent
 * @package Nitro\Options\Models
 * @property string $key
 * @property $value
 * @property boolean $autoload
 * @property boolean $public
 * @property OptionableInterface|null $optionable
 * @method static Builder onlyPublic()
 * @method static Builder onlyAutoloaded()
 */
class OptionEloquent extends Eloquent
{
    protected $table        = 'options';
    protected $primaryKey   = 'key';
    protected $fillable     = ['key', 'value', 'autoload', 'public', 'optionable_id', 'optionable_type'];
    protected $hidden       = ['autoload', 'public'];
    protected $casts        = [
        'autoload'      => 'boolean',
        'public'        => 'boolean',
        'value'         => 'array',
        'optionable_id' => 'integer',
    ];
    public $timestamps      = false;
    public $incrementing    = false;

    public function optionable()
    {
        return $this->morphTo();
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopeOnlyPublic($query)
    {
        return $query->where('public', true);
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopeOnlyAutoloaded($query)
    {
        return $query->where('autoload', true);
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = \json_encode($value);
    }
}