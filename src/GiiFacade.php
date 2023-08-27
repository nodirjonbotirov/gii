<?php

namespace Botiroff\Gii;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Botiroff\Gii\Skeleton\SkeletonClass
 */
class GiiFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'gii';
    }
}
