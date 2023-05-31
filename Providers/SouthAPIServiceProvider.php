<?php

namespace Modules\SouthAPI\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;
use Modules\SouthAPI\Auth\Auth;
use Modules\SouthAPI\Http\ClientHandler\HandlerStack;
use Modules\SouthAPI\Http\ClientHandler\HandlerStackable;
use Modules\SouthAPI\ISouthAPI;
use Modules\SouthAPI\SouthAPI;

class SouthAPIServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'SouthAPI';

    /**
     * @var string
     */
    protected $moduleNameLower = 'southapi';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HandlerStackable::class, HandlerStack::class);

        $this->app->bind(ClientInterface::class, function () {
            return new Client(['handler' => $this->app->make(HandlerStackable::class)->getStack()]);
        });
        $this->app->bind(ISouthAPI::class, SouthAPI::class);
        $this->app->bind(Auth::class, function ($app) {
            return $app->make(config('southapi.auth_interface'));
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower.'.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
