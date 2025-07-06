<?php

namespace App\Dto;

use DateTimeImmutable;

class ExchangeRateDTO
{
    public function __construct(
        public string $base,
        public array $targets,
        public DateTimeImmutable $date,
        public ?string $rate = null,
    ) {}
}
