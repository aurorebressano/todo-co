<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private $client;
    private $userPasswordHasherInterface;
    private $userRepository;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'your_test_username',
            'PHP_AUTH_PW' => 'your_test_password',
        ]);
        $this->userPasswordHasherInterface = static::getContainer()->get('security.user_password_hasher');
        $this->userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $this->taskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
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

    public function testCreateNewTask(): void
    {
        $this->client->loginUser($this->createUser());
        $this->client->request('GET', '/tasks/create');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->client->submitForm('Ajouter', ['task[title]' => 'Title', 'task[content]' => 'Content']);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());


        $this->assertSelectorExists('.alert.alert-success', 'La tâche a été bien été ajoutée.');

        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Title']);
        $this->assertNotNull($task);
    }

    public function testEditTask(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);
        $this->client->request('GET', '/tasks/create');
        $this->client->submitForm('Ajouter', ['task[title]' => 'Title', 'task[content]' => 'Content']);
        $this->client->followRedirect();
        $task = $this->taskRepository->findOneByUser($user);

        $taskId = $task->getId();
        $this->client->request('GET', '/tasks/'.$taskId.'/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->submitForm('Modifier', ['task[title]' => 'EditedTitle', 'task[content]' => 'EditedContent']);
        $crawler = $this->client->followRedirect();
        $currentUrl = $this->client->getRequest()->getPathInfo();
        $task = $this->taskRepository->findOneByUser($user);
        $taskCount = $this->taskRepository->findByTitle('Title');
        $editedTaskCount = $this->taskRepository->findByTitle('EditedTitle');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();
        $this->assertCount(0, $taskCount);
        $this->assertCount(1, $editedTaskCount);
    }

    public function testTasksDelete(): void
    {
        $user = $this->createUser();
        $this->client->loginUser($user);
        $this->client->request('GET', '/tasks/create');
        $this->client->submitForm('Ajouter', ['task[title]' => 'Title', 'task[content]' => 'Content']);
        $crawler = $this->client->followRedirect();

        $this->client->request('GET', '/tasksNotDone');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorExists('.btn-info', 'Créer une tâche');
        $this->assertSelectorExists('.btn-danger', 'Supprimer');
        $this->client->submitForm('Supprimer', []);

        $this->client->followRedirect();
        $this->client->getRequest()->getPathInfo();
        $this->taskRepository->findByTitle('Title');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();
    }
}