<?php

namespace App\Dto;

class RatesDTO
{
    public function __construct(
        public string $base,
        public string $targets,
        public string $rate,
        public string $date,
    ) {}
}
