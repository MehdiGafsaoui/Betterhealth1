<?php
namespace App\DataFixtures;

use App\Entity\DemandeDonSang;
use App\Entity\User;
use App\Entity\CentreDeDon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
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

        $manager->flush();

        echo "Fixtures successfully loaded!\n";
    }
}
