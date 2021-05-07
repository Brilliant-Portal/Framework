<?php

namespace BrilliantPortal\Framework\Http\Controllers\Api;

use App\Http\Controllers\Controller as AppController;
use App\Models\Team;
use Vyuldashev\LaravelOpenApi\Annotations as OpenApi;

/**
 * Team controller.
 *
 * @since 0.0.1
 *
 * @OpenApi\PathItem()
 */
class Controller extends AppController
{
    /**
     * Set authorization for all methods.
     *
     * @since 0.0.1
     */
    public function __construct()
    {
        $this->authorizeResource($this->model, $this->ability);
    }
}
