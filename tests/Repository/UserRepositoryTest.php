<?php

namespace App\Tests\Repository;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->userPasswordHasherInterface = static::getContainer()->get('security.user_password_hasher');
        $this->userRepository = static::getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
    }

    public function createUser()
    {
        $user = new User();
        $user->setUsername('TestAdminUser');
        $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'test'));
        $user->setEmail('admin@email.fr');
        $user->setRoles(['ROLE_ADMIN']);

        $this->userRepository->save($user, true);

        return $user;
    }

    public function testFindUserById()
    {
        $this->createUser();
        // Assuming you have a user with ID 1 for testing purposes
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => 'TestAdminUser']);

        $this->assertInstanceOf(User::class, $user);
    }

    public function testFindAllUsers()
    {
        $this->createUser();
        $users = $this->entityManager
            ->getRepository(User::class)
            ->findAll();

        $this->assertIsArray($users);
        $this->assertNotEmpty($users);

        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    public function testFindOneByUsername()
    {
        $this->createUser();
        // Assuming you have a user with ID 1 for testing purposes
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => 'TestAdminUser']);

        $this->assertInstanceOf(User::class, $user);
    }

}