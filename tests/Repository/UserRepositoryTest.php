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
    }

    public function testFindUserById()
    {
        // Assuming you have a user with ID 1 for testing purposes
        $user = $this->entityManager
            ->getRepository(User::class)
            ->find(1);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->getId());
    }

    public function testFindAllUsers()
    {
        $users = $this->entityManager
            ->getRepository(User::class)
            ->findAll();

        $this->assertIsArray($users);
        $this->assertNotEmpty($users);

        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    public function testFindByUsername()
    {
        // Assuming you have a user with username "testUser" for testing purposes
        $users = $this->entityManager
            ->getRepository(User::class)
            ->findBy(['username' => 'testUser']);

        $this->assertIsArray($users);
        $this->assertNotEmpty($users);

        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertEquals('testUser', $user->getUsername());
        }
    }

    public function testFindOneByUsername()
    {
        // Assuming you have a user with username "testUser" for testing purposes
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => 'testUser']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('testUser', $user->getUsername());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
