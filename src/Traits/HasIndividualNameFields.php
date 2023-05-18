<?php

namespace BrilliantPortal\Framework\Traits;

/**
 * @property-read string $name
 */
trait HasIndividualNameFields
{
    public function toArray(): array{
        return array_merge(
            parent::toArray(),
            ['name' => $this->name]
        );
    }

    public function getNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
