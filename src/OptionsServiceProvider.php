<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 19/02/2019
 * Time: 12:03 PM
 */

namespace Nitro\Options;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nitro\Options\Console\OptionAll;
use Nitro\Options\Console\OptionGet;
use Nitro\Options\Console\OptionPublic;
use Nitro\Options\Console\OptionUpdate;
use Nitro\Options\Facades\Options;

class OptionsServiceProvider extends ServiceProvider
{
    private const OPTIONS_PACKAGE_PATH = __DIR__.DIRECTORY_SEPARATOR;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Options', function (){
            return new Options();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(self::OPTIONS_PACKAGE_PATH.'Migrations');
        $this->publishes([
            self::OPTIONS_PACKAGE_PATH.'nitro.options.php' => config_path('nitro.options.php'),
        ]);
        $this->mergeConfigFrom(self::OPTIONS_PACKAGE_PATH.'nitro.options.php', 'nitro.options');
        Blade::directive('options', function () {
            return '<?php print option()->javascript() ?>';
        });
        if ($this->app->runningInConsole()) {
            $this->commands([
                OptionUpdate::class,
                OptionGet::class,
                OptionAll::class,
                OptionPublic::class,
            ]);
        }
    }
}