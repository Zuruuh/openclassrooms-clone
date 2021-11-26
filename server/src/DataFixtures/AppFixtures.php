<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $em): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; ++$i) {
            $dev = $i === 0;
            $user = new User();
            $password = $this->hasher->hashPassword($user, 'password');

            $user
                ->setUsername($dev ? 'Zuruh' : $faker->name() . $i)
                ->setEmail($dev ? 'younesziadi@outlook.fr' : $i . $faker->email())
                ->setPassword($password);
            $em->persist($user);
            $em->flush();
        }
    }
}
