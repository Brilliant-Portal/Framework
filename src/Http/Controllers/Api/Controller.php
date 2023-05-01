<?php

namespace BrilliantPortal\Framework\Http\Controllers\Api;

use App\Http\Controllers\Controller as AppController;

/**
 * Team controller.
 *
 * @since 0.1.0
 *
 * @property class-string $model
 * @property string $ability
 */
class Controller extends AppController
{
    /**
     * Set authorization for all methods.
     *
     * @since 0.1.0
     */
    public function __construct()
    {
        $this->authorizeResource($this->model, $this->ability);
    }
}
