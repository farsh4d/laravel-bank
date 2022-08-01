<?php

namespace Farsh4d\Bank\Contracts;


interface Driver
{
    public function callback($callback);
    public function price($price);
    public function orderId($orderId);
    public function refId();
    public function ready();
    public function redirect();
    public function verify($transaction);
}
