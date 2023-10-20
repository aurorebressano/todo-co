<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private TaskRepository $taskRepository, private UserRepository $userRepository, private UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // tasks

        $tasksDatas = [
            [
                'title' => 'Faire les courses',
                'content' => 'Voir liste dans bloc notes',
                'isDone' => true,
                'user' => 'Anonyme',
            ],
            [
                'title' => 'Arroser les plantes',
                'content' => 'Sauf les plantes grasses !',
                'isDone' => false,
                'user' => 'Admin',
            ],
            [
                'title' => 'Contemplation méditative',
                'content' => 'Toute la journée',
                'isDone' => false,
                'user' => 'Lambda',
            ],
        ];

        foreach ($tasksDatas as $data) {
            $task = new Task();
            $task->setTitle($data['title']);
            $task->setContent($data['content']);
            $task->setUser($this->userRepository->findOneByUsername($data['user']));
            $task->setCreatedAt(new \DateTime());
            $task->setIsDone($data['isDone']);

            $manager->persist($task);
        }

        $manager->flush();
    }
}
