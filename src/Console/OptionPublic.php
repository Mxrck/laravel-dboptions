<?php

namespace Nitro\Options\Console;

use Illuminate\Console\Command;

class OptionPublic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'option:public';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all public options';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->table(['key', 'value', 'autoload', 'public'], option()->public());
    }
}
