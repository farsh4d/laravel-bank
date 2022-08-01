<?php

namespace Farsh4d\Bank\Drivers;

use Illuminate\Container\Container;
use Farsh4d\Bank\Contracts\Driver;
use Farsh4d\Bank\Models\PaymentTransaction;
use Farsh4d\Bank\Costants\PaymentStatus;

/**
 * Class AbstractDriver
 * @package Farsh4d\Bank\Drivers
 */
abstract class AbstractDriver implements Driver {
    protected $app;
    protected $config;
    protected $driver;
    protected $callback = null;
    protected $price = null;
    protected $orderId = null;
    protected $refId = null;
    protected $transaction = null;

    /**
     * Create a new driver instance.
     *
     * @param Container $app
     * @param array $config
     */
    public function __construct(Container $app, $config, $driver) {
        $this->app = $app;
        $this->config = $config;
        $this->driver = $driver;
    }

    /**
     * @param $callback
     * @return AbstractDriver
     */
    public function callback($callback) {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @param $price
     * @return AbstractDriver
     */
    public function price($price) {
        $this->price = $price;

        return $this;
    }

    public function orderId($orderId) {
        $this->orderId = $orderId;

        return $this;
    }

    public function refId() {
        return $this->refId;
    }

    public function transaction() {
        return $this->transaction;
    }
    
    protected function intitReady() {
        $this->createTransaction();
        $this->callback = $this->callback . '?tr_id=' . $this->transaction->id;
    }

    protected function createTransaction() {
        $transaction = PaymentTransaction::create([
            'psp' => $this->driver,
            'price' => $this->price,
            'order_id' => $this->orderId,
        ]);

        $this->transaction = $transaction;
    }

    protected function updateTransactionRefId() {
        $this->transaction->ref_id = $this->refId;
        $this->transaction->save();
    }

    protected function transactionSucceeded($paymentResault = null) {
        $this->transaction->status = PaymentStatus::SUCCESS;
        $this->transaction->pay_res = serialize($paymentResault);

        $this->transaction->save();
    }

    protected function transactionfailed($paymentResault = null) {
        $this->transaction->status = PaymentStatus::FAILED;
        $this->transaction->pay_res = serialize($paymentResault);

        $this->transaction->save();
    }

}
