<?php

namespace BrilliantPortal\Framework;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BrilliantPortal\Framework\Framework
 */
class FrameworkFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Framework::class;
    }
}
