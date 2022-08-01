<?php

namespace Farsh4d\Bank\Providers;

use Illuminate\Support\ServiceProvider;
use Farsh4d\Bank\Contracts\Factory;
use Farsh4d\Bank\Facades\Bank;
use Farsh4d\Bank\Managers\BankManager;

/**
 * Class BankServiceProvider
 * @package Farsh4d\Bank\Providers
 */
class BankServiceProvider extends ServiceProvider {

    public function register() {
        $configPath = __DIR__ . '/../../config/bank.php';
        $this->mergeConfigFrom($configPath, 'bank');

        $this->registerBank();
    }

    protected function registerBank() {
        app()->singleton(Factory::class, function ($app) {
            return new BankManager($app);
        });

        Bank::shouldProxyTo(Factory::class);
    }

    public function boot() {
        $configPath = __DIR__ . '/../../config/bank.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'config');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'bank');
        
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
