<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Client;
use App\DataFixtures\AppFixtures;
use App\Repository\ClientRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use DateTimeImmutable;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        // users
        $usersDatas = array(
            [
                'email' => 'anonyme@fr.fr', 
                'roles' => [
                    "ROLE_USER"
                ], 
                'username' => 'Anonyme'
            ],
            [
                'email' =>'lambda@fr.fr', 
                'roles' =>[
                    "ROLE_USER"
                ], 
                'username' =>'Lambda'
            ],
            [
                'email' =>'admin@admin.fr', 
                'roles' =>[
                    "ROLE_USER",
                    "ROLE_ADMIN"
                ], 
                'username' =>'Admin'
            ]
        );

        foreach($usersDatas as $data)
        {
            $user = new User();
            $user->setEmail($data['email'])
            ->setRoles($data['roles'])
            ->setUsername($data['username']);

            $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'test'));

            $manager->persist($user);
        }
    
        $manager->flush();
    }
}