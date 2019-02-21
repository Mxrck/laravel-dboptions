<?php

namespace Nitro\Options\Console;

use Illuminate\Console\Command;

class OptionUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'option:update {key : Option key} {value : Option value} {autoload? : Autoload option}, {public? : Is public?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update an option';

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
        $key        = $this->argument('key');
        $value      = $this->argument('value');
        $autoload   = (bool) $this->argument('autoload') ?? false;
        $public     = (bool) $this->argument('public') ?? false;
        $option     = option()->update($key, $value, [
            'autoload'  => $autoload,
            'public'    => $public
        ]);
        if ($option !== null)
        {
            $this->info("The option {$key} was created");
        }
        else
        {
            $this->error("The option {$key} can't be created");
        }
    }
}
