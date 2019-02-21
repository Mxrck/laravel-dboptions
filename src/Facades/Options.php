<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 20/02/2019
 * Time: 04:26 PM
 */

namespace Nitro\Options\Facades;


use Illuminate\Support\Collection;
use Nitro\Options\Events\OptionUpdatedEvent;
use Nitro\Options\Models\OptionEloquent as Option;

class Options
{
    /**
     * @var Collection $cached
     */
    private $cached;
    private $model;
    private $modelClassName;

    public function __construct()
    {
        $this->cached = new Collection();
        $this->modelClassName = config('nitro.options.model', Option::class);
        $this->model = new $this->modelClassName();
        $this->load();
    }

    private function load() : bool
    {
        if (!empty($this->cached)) return false;
        $this->cached = $this->model->onlyAutoloaded()->get();
        return true;
    }

    public function get(string $key, $default = null, bool $reload = false)
    {
        if ($this->exists($key))
        {
            if ($this->existsInCache($key) && !$reload)
            {
                $option = $this->cached->firstWhere('key', $key);
                return $option->value;
            }
            else
            {
                $this->removeFromCache($key);
                $option = $this->model->find($key);
                $this->cached->push($option);
                return $option->value;
            }
        }
        return $default;
    }

    public function update(string $key, $value, array $meta = [])
    {
        $data = array_merge([
            'key' => $key,
            'value' => $value
        ], $meta);
        $option = $this->getOption($key);
        if ($option === null)
        {
            $option = $this->model->create($data);
            if ($option !== null)
            {
                $this->cached->push($option);
            }
        }
        else
        {
            $option->update($data);
            $option->fresh();
            $this->removeFromCache($key);
            $this->cached->push($option);
        }
        new OptionUpdatedEvent($option);
        return $option->value;
    }

    public function remove(string $key) : bool
    {
        if ($this->exists($key))
        {
            $option = $this->getOption($key);
            $this->removeFromCache($key);
            return $option !== null ? $option->delete() : false;
        }
        return true;
    }

    public function all() : array
    {
        return $this->model->get()->keyBy('key')->toArray();
    }

    public function public() : array
    {
        return $this->model->onlyPublic()->get()->keyBy('key')->toArray();
    }

    public function javascript()
    {
        $varname = config('nitro.options.javascript_varname', 'options');
        $options = [];
        foreach($this->public() as $key => $option)
        {
            $options[$key] = $option['value'];
        }
        unset($key);
        $json = \json_encode($options);
        return "<script type=\"text/javascript\">var {$varname} = {$json}</script>";
    }

    public function exists(string $key) : bool
    {
        if ($this->existsInCache($key)) return true;
        return $this->model->where('key', $key)->exists();
    }

    private function removeFromCache(string $key) : bool
    {
        if ($this->existsInCache($key))
        {
            $this->cached = $this->cached->filter(function ($value) use ($key) {
                return $value->key !== $key;
            });
            return true;
        }
        return false;
    }

    private function existsInCache(string $key) : bool
    {
        return $this->cached->contains('key',$key);
    }

    private function getOption(string $key) :? Option
    {
        if ($this->exists($key))
        {
            if ($this->existsInCache($key))
            {
                return $this->cached->firstWhere('key', $key);
            }
            return $this->model->find($key);
        }
        return null;
    }
}