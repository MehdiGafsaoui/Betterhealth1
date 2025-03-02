<?php

namespace App\DataFixtures;

use App\Entity\DemandeDonSang;
use App\Entity\User;
use App\Entity\CentreDeDon;
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
        // Blood donation fixtures
        $this->loadBloodDonationFixtures($manager);
        
        // Therapy fixtures
        $this->loadTherapyFixtures($manager);
        
        $manager->flush();
        echo "All fixtures successfully loaded!\n";
    }
    
    private function loadBloodDonationFixtures(ObjectManager $manager): void
    {
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $statuses = ['pending', 'refused', 'accepted'];
        
        // Fetch existing users and centers
        $users = $manager->getRepository(User::class)->findAll();
        $centres = $manager->getRepository(CentreDeDon::class)->findAll();
        
        if (empty($users) || empty($centres)) {
            throw new \Exception("You must have users and centers in the database before running fixtures.");
        }
        
        for ($i = 0; $i < 10; $i++) {
            $demande = new DemandeDonSang();
            $demande->setGroupesanguain($bloodTypes[$i % count($bloodTypes)]);
            $demande->setQuantite(round(0.2 + ($i * 0.2), 1));
            $demande->setStatus($statuses[$i % count($statuses)]);
            $demande->setCreatedAt(new \DateTime(sprintf('-%d days', $i * 10)));
            $demande->setUser($users[$i % count($users)]);
            $demande->setCentreDeDon($centres[$i % count($centres)]);
            
            $manager->persist($demande);
        }
    }
    
    private function loadTherapyFixtures(ObjectManager $manager): void
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
    }
}