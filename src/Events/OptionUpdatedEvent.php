<?php

namespace Nitro\Options\Events;

class OptionUpdatedEvent
{
    public $option = null;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($option = null)
    {
        $this->option = $option;
    }
}
