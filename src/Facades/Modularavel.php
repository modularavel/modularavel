<?php

namespace Modularavel\Starter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Modularavel\Starter\Modularavel
 */
class Modularavel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Modularavel\Starter\Modularavel::class;
    }
}
