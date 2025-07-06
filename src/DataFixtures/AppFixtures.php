<?php

namespace App\DataFixtures;

use App\Entity\ExchangePair;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $pairs = [
            ['USD', 'EUR'],
            ['USD', 'JPY'],
            ['EUR', 'GBP'],
            ['EUR', 'CAD'],
            ['EUR', 'RUB'],
            ['EUR', 'CNY'],
        ];

        foreach ($pairs as [$base, $target]) {
            $pair = new ExchangePair($base, $target);
            $manager->persist($pair);
        }

        $manager->flush();

        $manager->flush();
    }
}
