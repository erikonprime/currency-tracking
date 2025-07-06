<?php

namespace App\Dto;

use DateTimeImmutable;

class RateHistoricalDTO
{
    public function __construct(
        public string $base,
        public string $target,
        public ?string $rate = null,
        public ?DateTimeImmutable $date = null,
    ) {}
}
