<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
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

    public function testFindByIsDoneTrue()
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([]); // Add specific criteria if needed, e.g. ['username' => 'example']

        $tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findByIsDoneTrue($user);

        $this->assertNotEmpty($tasks);

        foreach ($tasks as $task) {
            $this->assertTrue($task->getIsDone());
            $this->assertEquals($user->getId(), $task->getUser()->getId());
        }
    }

    public function testFindByIsDoneFalse()
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([]); // Add specific criteria if needed

        $tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findByIsDoneFalse($user);

        $this->assertNotEmpty($tasks);

        foreach ($tasks as $task) {
            $this->assertFalse($task->getIsDone());
            $this->assertEquals($user->getId(), $task->getUser()->getId());
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
