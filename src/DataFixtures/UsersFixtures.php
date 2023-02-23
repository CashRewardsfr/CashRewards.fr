<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class UsersFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for($nbUsers = 1; $nbUsers <= 200; $nbUsers++){
            //$parrain = $this->getReference('user_'. $faker->numberBetween(1, 200));
            $user = new User();
            $user->setEmail($faker->email);
            if($nbUsers === 1)
                //$user->setEmail('admin@gmail.com');
                $user->setRoles(['ROLE_ADMIN']);
            else
                $user->setRoles(['ROLE_USER']);
            $user->setPaypal('https://www.paypal.com');
            $user->setIsVerified($faker->numberBetween(0, 1));
            $user->setPoints($faker->numberBetween(10000, 50000));
            $user->setPassword($this->encoder->hashPassword($user, 'azerty'));
            $user->setPseudo($faker->lastName);
            //$user->setParrain($parrain);
            $manager->persist($user);

            // Enregistre l'utilisateur dans une référence
            $this->addReference('user_'. $nbUsers, $user);
        }

        $manager->flush();
    }
}
