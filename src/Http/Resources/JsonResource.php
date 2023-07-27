<?php

namespace BrilliantPortal\Framework\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource as LaravelJsonResource;

class JsonResource extends LaravelJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @since 0.1.0
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request)
    {
        return parent::toArray($request);
    }
}
