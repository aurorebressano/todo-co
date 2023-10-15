<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
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

    public function testCreateTask()
    {
        $this->createUser();
        $taskRepository = new TaskRepository(static::getContainer()->get(ManagerRegistry::class));
        $userRepository = new UserRepository(static::getContainer()->get(ManagerRegistry::class));

        $task = new Task();
        $task
            ->setTitle('TestTitle')
            ->setContent('Content test')
            ->setUser($userRepository->findOneByUsername('TestAdminUser'))
        ;

        $taskRepository->save($task, true);
        $this->assertNotNull($taskRepository->findOneByTitle('TestTitle'));

        return $task;
    }

    public function testUpdateTask()
    {
        $this->testCreateTask();
        $taskRepository = new TaskRepository(static::getContainer()->get(ManagerRegistry::class));
        $task = $taskRepository->findOneByTitle('TestTitle');
        $task->setTitle('TestTitleUpdated');
        $task->setIsDone(false);
        $taskRepository->save($task, true);
        $this->assertNotNull($taskRepository->findOneByTitle('TestTitleUpdated'));
        $this->assertEquals(false, $taskRepository->findOneByTitle('TestTitleUpdated')->isDone());
        return $task;
    }

    public function testFindByIsDoneTrue(): void
    {
        $createTask = $this->testCreateTask();
        $user = $createTask->getUser();
        $taskRepository = new TaskRepository(static::getContainer()->get(ManagerRegistry::class));
        $task = $taskRepository->findByIsDoneTrue($user);

        $this->assertNotNull($task);
    }

    public function testFindByIsDoneFalse(): void
    {
        $createTask = $this->testUpdateTask();
        $user = $createTask->getUser();
        $taskRepository = new TaskRepository(static::getContainer()->get(ManagerRegistry::class));
        $task = $taskRepository->findByIsDoneTrue($user);
        $this->assertNotNull($task);
    }
}