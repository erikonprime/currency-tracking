<?php

namespace App\Dto;

class RateCollectionDTO
{
    /**
     * @param RatesDTO[] $items
     */
    public function __construct(
        public array $items = [],
    ) {}
}
