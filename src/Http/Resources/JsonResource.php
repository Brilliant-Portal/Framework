<?php

namespace BrilliantPortal\Framework\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource as LaravelJsonResource;

class JsonResource extends LaravelJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @since 0.1.0
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
