<?php

namespace App\DataFixtures;

use App\Entity\Mission;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class MissionsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($missions = 1; $missions <= 1000; $missions++){

            $user = $this->getReference('user_'. $faker->numberBetween(1, 200));
            
            $mission = new Mission();
            $mission->setAmount($faker->numberBetween(100, 1000));
            $mission->setDescription($faker->realText(20));
            $mission->setOfferId($faker->numberBetween(1, 100));
            $mission->setUser($user);
            $manager->persist($mission);

            // Enregistre la catégorie dans une référence
            $this->addReference('mission_' . $missions, $mission);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UsersFixtures::class,
        ];
    }
}
