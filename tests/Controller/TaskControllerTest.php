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

    protected function setUp(): void
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'your_test_username',
            'PHP_AUTH_PW' => 'your_test_password',
        ]);
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

    public function testCreateNewTask(): void
    {
        $this->client->loginUser($this->createUser());
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Test Task',
            'task[content]' => 'Test content',
        ]);

        $this->client->submit($form);

        // $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Check if flash message is displayed
        // $this->assertSelectorTextContains('.flash-success', 'La tâche a été bien été ajoutée.');

        // Optionally, you can also check if the task has been added in the database
        $task = $this->validator = self::getContainer()->get('doctrine')->getRepository(Task::class)->findOneBy(['title' => 'Test Task']);
        $this->assertNotNull($task);
    }
}
