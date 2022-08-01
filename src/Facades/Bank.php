<?php

namespace Farsh4d\Bank\Facades;


use Illuminate\Support\Facades\Facade;
use Farsh4d\Bank\Drivers\AbstractDriver;

/**
 * @see \Farsh4d\Bank\Managers\BankManager
 *
 * @method static AbstractDriver driver($psp_name) create a psp gateway implementation
 */
class Bank extends Facade
{
    public static function shouldProxyTo($class)
    {
        return app()->singleton(self::getFacadeAccessor(), $class);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'bank';
    }
}
