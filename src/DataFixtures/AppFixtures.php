<?php

namespace App\DataFixtures;


use App\Entity\Therapie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Cocur\Slugify\Slugify;
use DateTimeImmutable;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 150; $i++) {
            $therapie = new Therapie();
            $name = $faker->unique()->words(2, true);
            $therapie->setNom($name);
            $therapie->setDescription('Lorem ipsum ' . $i);
            $therapie->setObjectif('Objectif ' . $i);
            $therapie->setDuree($faker->numberBetween(30, 120));
            $therapie->setType($faker->randomElement(['Physique', 'Mentale', 'Émotionnelle']));
            $therapie->setImage('sanstabac-67b0c1530f1a9.jpg');


            $manager->persist($therapie);
        }

        $manager->flush();
    }
}
