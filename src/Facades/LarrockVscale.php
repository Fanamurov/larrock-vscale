<?php

namespace Larrock\ComponentVscale\Facades;

use Illuminate\Support\Facades\Facade;

class LarrockVscale extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'larrockvscale';
    }
}
