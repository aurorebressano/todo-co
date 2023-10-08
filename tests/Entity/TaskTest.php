<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskTest extends KernelTestCase
{
    private $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    private function validateTask(Task $task): ConstraintViolationListInterface
    {
        return $this->validator->validate($task);
    }

    public function testValidTask(): void
    {
        $user = new User();
        $user->setUsername('TestUser');
        $user->setPassword('testpassword');
        $user->setEmail('test@example.com');

        $task = new Task();
        $task->setTitle('Test title');
        $task->setContent('Test content');
        $task->setCreatedAt(new \DateTime());
        $task->setIsDone(false);
        $task->setUser($user);

        $errors = $this->validateTask($task);
        $this->assertCount(0, $errors, 'Unexpected validation errors for valid task data.');
    }

    public function testEmptyTitle(): void
    {
        $task = new Task();
        $task->setTitle('');
        $task->setContent('Test content');
        $task->setCreatedAt(new \DateTime());
        $task->setIsDone(false);
        $errors = $this->validateTask($task);
        $this->assertGreaterThan(0, count($errors), 'Expected validation errors for empty title.');
    }

    public function testEmptyContent(): void
    {
        $task = new Task();
        $task->setTitle('Test title');
        $task->setContent('');
        $task->setCreatedAt(new \DateTime());
        $task->setIsDone(false);
        $errors = $this->validateTask($task);
        $this->assertGreaterThan(0, count($errors), 'Expected validation errors for empty content.');
    }

    public function testNoUserAssigned(): void
    {
        $task = new Task();
        $task->setTitle('Test title');
        $task->setContent('Test content');
        $task->setCreatedAt(new \DateTime());
        $task->setIsDone(false);
        $errors = $this->validateTask($task);
        $this->assertGreaterThan(0, count($errors), 'Expected validation errors for task without a user.');
    }

    // Vous pouvez ajouter d'autres tests si nécessaire
}
