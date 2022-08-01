<?php

namespace Farsh4d\Bank\Managers;


use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use Farsh4d\Bank\Contracts\Factory;
use Farsh4d\Bank\Drivers\AbstractDriver;
use Farsh4d\Bank\Models\PaymentTransaction;
use Farsh4d\Bank\Costants\PaymentStatus;

class BankManager extends Manager implements Factory
{
    public function getDefaultDriver()
    {
        return $this->app['config']['bank.defaultDriver'];
    }

    public function __call($method, $parameters)
    {
        if (str_starts_with($method, 'create') && str_ends_with($method, 'Driver')) {
            return $this->buildDriver($method);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * @param $method
     * @return AbstractDriver
     */
    protected function buildDriver($method)
    {
        $driverName = $this->getCalledDriverName($method);
        $config = $this->app['config']['bank.' . $driverName];
        $driver = '\\Farsh4d\\Bank\\Drivers\\' . ucfirst($driverName);
        
        return new $driver($this->app, $config, $driverName);
    }

    private function getCalledDriverName($method)
    {
        $method = strtolower($method);
        $driver = explode('create', $method)[1];
        
        return explode('driver', $driver)[0];
    }

    protected function createDriver($driver)
    {
        $method = 'create' . Str::studly($driver) . 'Driver';

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        try {
            return $this->$method();
        } catch (\Exception $exception) {
            throw new \InvalidArgumentException("Driver [$driver] is not supported.");
        }
    }
    
    public static function verify() {
        $transaction = PaymentTransaction::find(\request()->get('tr_id'));
        if (is_null($transaction)) {
            throw new \Exception('Payment not found!', -1);
        }

        if ($transaction->status !== PaymentStatus::PAYMENT_PENDING) {
            throw new \Exception('Payment is verified or failed already!', -2);
        }
        
        self::driver($transaction->psp)->verify($transaction);
    }
}
