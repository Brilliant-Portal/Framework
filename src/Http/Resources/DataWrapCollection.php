<?php

namespace BrilliantPortal\Framework\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DataWrapCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @since 0.1.0
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'count' => $this->collection->count(),
            ],
        ];
    }
}
