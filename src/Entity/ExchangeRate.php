<?php

namespace App\Entity;

use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Trait\TimeStampableTrait;

#[ORM\Entity(repositoryClass: ExchangeRateRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ExchangeRate
{
    use TimeStampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 10)]
    private string $rate;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'exchangeRates')]
    #[ORM\JoinColumn(nullable: false)]
    private ExchangePair $exchangePair;

    public function __construct(ExchangePair $exchangePair, string $rate)
    {
        $this->exchangePair = $exchangePair;
        $this->rate = $rate;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getExchangePair(): ExchangePair
    {
        return $this->exchangePair;
    }

    public function setExchangePair(ExchangePair $exchangePair): static
    {
        $this->exchangePair = $exchangePair;

        return $this;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function setRate(string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }


}
