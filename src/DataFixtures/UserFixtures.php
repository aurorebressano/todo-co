<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasherInterface)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // users
        $usersDatas = [
            [
                'email' => 'anonyme@fr.fr',
                'roles' => [
                    'ROLE_USER',
                ],
                'username' => 'Anonyme',
            ],
            [
                'email' => 'lambda@fr.fr',
                'roles' => [
                    'ROLE_USER',
                ],
                'username' => 'Lambda',
            ],
            [
                'email' => 'admin@admin.fr',
                'roles' => [
                    'ROLE_USER',
                    'ROLE_ADMIN',
                ],
                'username' => 'Admin',
            ],
        ];

        foreach ($usersDatas as $data) {
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
