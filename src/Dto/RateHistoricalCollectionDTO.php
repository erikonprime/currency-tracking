<?php

namespace App\Dto;

class RateHistoricalCollectionDTO
{
    /**
     * @param RateHistoricalDTO[] $items
     */
    public function __construct(
        public array $items = [],
    ) {}
}
