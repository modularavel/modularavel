<?php

namespace Modularavel\Modularavel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Modularavel\Modularavel\Modularavel
 */
class Modularavel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Modularavel\Modularavel\Modularavel::class;
    }
}
