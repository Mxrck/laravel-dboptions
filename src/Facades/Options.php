<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 20/02/2019
 * Time: 04:26 PM
 */

namespace Nitro\Options\Facades;


use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Collection;
use Nitro\Options\Context;
use Illuminate\Support\Arr;
use Nitro\Options\Contracts\OptionableInterface;
use Nitro\Options\Models\OptionEloquent as Option;

class Options
{
    /**
     * @var Collection $cached
     */
    private $cached;

    /**
     * @var Eloquent $model
     */
    private $model;

    /**
     * @var string $modelClassName
     */
    private $modelClassName;

    /**
     * @var Context $context
     */
    private $context;

    public function __construct()
    {
        $this->cached = new Collection();
        $this->modelClassName = config('nitro.options.model', Option::class);
        $this->model = new $this->modelClassName();
        $this->load();
    }

    /**
     * Preload options marked with autoload
     * @return bool
     */
    private function load() : bool
    {
        if (!empty($this->cached)) return false;
        $this->cached = $this->model->onlyAutoloaded()->get();
        return true;
    }

    /**
     * Set a context for the next option petition
     * @param Context|OptionableInterface $context
     * @param bool $fallback if $context is an OptionableInterface, it define the fallback for the context
     * to be used
     * @return $this
     */
    public function context($context, $fallback = false)
    {
        if ($context instanceof OptionableInterface) $context = Context::make($context, $fallback);
        $this->context = $context;
        return $this;
    }

    /**
     * Apply the current context to the options model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyContext()
    {
        if ($this->context === null)
        {
            return $this->model
                ->whereNull('optionable_id')
                ->whereNull('optionable_type');
        }
        else if (isset($this->context->optionable))
        {
            return $this->model
                ->where('optionable_id', $this->context->optionable->getId())
                ->where('optionable_type', $this->context->optionable->getType());
        }
        return $this->model->newQuery();
    }


    /**
     * Reset the current context, for the next option petition
     * @return $this
     */
    private function resetContext()
    {
        $this->context = null;
        return $this;
    }

    /**
     * Find the index of a key saved on runtime collection cache
     * @param string $key
     * @param Context|null $context
     * @return bool|Option
     */
    private function cacheFindIndex(string $key, ?Context $context = null)
    {
        if ($this->cached->count() === 0) return false;
        $index = $this->cached->search(function ($item, $_key) use ( $key, $context ){
            $optionable_id      = null;
            $optionable_type    = null;
            if ($context !== null)
            {
                $optionable_id      = $context->optionable->getId();
                $optionable_type    = $context->optionable->getType();
            }
            return $item->key === $key && $item->optionable_id === $optionable_id && $item->optionable_type === $optionable_type;
        });
        return $index;
    }

    /**
     * Find an option saved on runtime collection cache
     * @param string $key
     * @param Context|null $context
     * @return Option|null
     */
    private function cacheFind(string $key, ?Context $context) :? Option
    {
        $index = $this->cacheFindIndex($key, $context);
        return $index !== false ? $this->cached->get($index) : null;
    }

    /**
     * Return the current stored value in the options table or the default passed
     * if there is a current context, the option will be filtered with the rules of context
     * or fallback to system option if the context has fallback in true
     * @param string $key The option key to search
     * @param null $default The default value in case the option don't exists
     * @param bool $reload if reload is true, then always search in the database instead of check
     * the collection cache
     * @return mixed|null The option required or the default value (null by default)
     */
    public function get(string $key, $default = null, bool $reload = false)
    {
        if ($this->exists($key))
        {
            if ($this->existsInCache($key) && !$reload)
            {
                $option = $this->cacheFind($key, $this->context);
                $this->resetContext();
                return $option->value;
            }
            else
            {
                $this->removeFromCache($key);
                $option = $this->applyContext()->where('key', $key)->first();
                $this->cached->push($option);
                $this->context = null;
                return $option->value;
            }
        }
        if ($this->context !== null && $this->context->fallback)
        {
            $this->resetContext();
            return $this->get($key, $default, $reload);
        }
        return $default;
    }

    /**
     * Update a value stored in the database or create a new one if don't exists, if the current context
     * isn't null, then the option is updated/saved for the context item
     * @param string $key The key to update in the store
     * @param mixed $value The value to store in the database
     * @param array $meta the default metadata is "autoload", "public" both boolean, if you extend the default
     * Option model you can define your own metadata
     * @return mixed The new or updated stored value
     */
    public function update(string $key, $value, array $meta = [])
    {
        $_meta = Arr::except($meta, ['key', 'value', 'optionable_id', 'optionable_type']);
        $data = array_merge([
            'key'               => $key,
            'value'             => $value,
            'optionable_id'     => $this->context !== null ? $this->context->optionable->getId() : null,
            'optionable_type'   => $this->context !== null ? $this->context->optionable->getType() : null,
        ], $_meta);
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
        $this->resetContext();
        return $option->value;
    }

    /**
     * Remove from cache and database the option by key
     * @param string $key The option to be deleted
     * @return bool
     * @throws \Exception
     */
    public function remove(string $key) : bool
    {
        if ($this->exists($key))
        {
            $option = $this->getOption($key);
            $this->removeFromCache($key);
            $this->resetContext();
            return $option !== null ? $option->delete() : false;
        }
        $this->resetContext();
        return true;
    }

    /**
     * Get all the options for the current context as array by keys
     * @return array
     */
    public function all() : array
    {
        $all = $this->applyContext()->get()->keyBy('key')->toArray();
        $this->resetContext();
        return $all;
    }

    /**
     * Get all the public options for the current context as array by keys
     * @return array
     */
    public function public() : array
    {
        $public = $this->applyContext()->onlyPublic()->get()->keyBy('key')->toArray();
        $this->resetContext();
        return $public;
    }

    /**
     * Generate a string with a javascript block, with all the public values for the current context
     * the javascript block generate a variable (by default "options") with the json_encode options
     * @return string
     */
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

    /**
     * Check if an options already exists in the database
     * @param string $key
     * @return bool
     */
    public function exists(string $key) : bool
    {
        if ($this->existsInCache($key)) return true;
        return $this->applyContext()->where('key', $key)->exists();
    }

    private function removeFromCache(string $key) : bool
    {
        if ($this->existsInCache($key))
        {
            $index = $this->cacheFindIndex($key, $this->context);
            if ($index !== false)
            {
                $this->cached->forget($index);
            }
            return true;
        }
        return false;
    }

    /**
     * Check if an option exists in the current cache
     * @param string $key
     * @return bool
     */
    private function existsInCache(string $key) : bool
    {
        $index = $this->cacheFindIndex($key, $this->context);
        return $index !== false;
    }

    /**
     * Get an Option object by key, from the cache or database
     * @param string $key
     * @return Option|null
     */
    private function getOption(string $key) :? Option
    {
        if ($this->exists($key))
        {
            if ($this->existsInCache($key))
            {
                $cached = $this->cacheFind($key, $this->context);
                return $cached;
            }
            $option = $this->applyContext()->where('key', $key)->first();
            return $option;
        }
        return null;
    }
}