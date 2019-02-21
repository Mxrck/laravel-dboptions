<?php

namespace Nitro\Options\Console;

use Illuminate\Console\Command;

class OptionGet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'option:get {key : Option key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get an option value';

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
        $key = $this->argument('key');
        $this->info(option()->get($key));
    }
}
