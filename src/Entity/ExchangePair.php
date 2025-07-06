<?php

namespace App\Entity;

use App\Repository\ExchangePairRepository;
use App\Trait\TimeStampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ExchangePairRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['baseCurrency', 'targetCurrency'], message: 'This currency pair already exists.')]
class ExchangePair
{
    use TimeStampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 3)]
    private string $baseCurrency;

    #[ORM\Column(length: 3)]
    private string $targetCurrency;

    /**
     * @var Collection<int, ExchangeRate>
     */
    #[ORM\OneToMany(targetEntity: ExchangeRate::class, mappedBy: 'exchangePair', orphanRemoval: true)]
    private Collection $exchangeRates;

    public function __construct(string $baseCurrency, string $targetCurrency)
    {
        $this->exchangeRates = new ArrayCollection();
        $this->baseCurrency = $baseCurrency;
        $this->targetCurrency = $targetCurrency;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency(string $baseCurrency): static
    {
        $this->baseCurrency = $baseCurrency;

        return $this;
    }

    public function getTargetCurrency(): string
    {
        return $this->targetCurrency;
    }

    public function setTargetCurrency(string $targetCurrency): static
    {
        $this->targetCurrency = $targetCurrency;

        return $this;
    }

    /**
     * @return Collection<int, ExchangeRate>
     */
    public function getExchangeRates(): Collection
    {
        return $this->exchangeRates;
    }

    public function addExchangeRate(ExchangeRate $exchangeRate): static
    {
        if (!$this->exchangeRates->contains($exchangeRate)) {
            $this->exchangeRates->add($exchangeRate);
            $exchangeRate->setExchangePair($this);
        }

        return $this;
    }

    public function removeExchangeRate(ExchangeRate $exchangeRate): static
    {
        if ($this->exchangeRates->removeElement($exchangeRate)) {
            // set the owning side to null (unless already changed)
            if ($exchangeRate->getExchangePair() === $this) {
                $exchangeRate->setExchangePair(null);
            }
        }

        return $this;
    }
}
