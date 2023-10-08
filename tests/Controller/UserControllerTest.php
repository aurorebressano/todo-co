<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $userPasswordHasherInterface;
    private $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
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

    public function testCreateUser(): void
    {
        $this->client->loginUser($this->createUser());
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'TestUser',
            'user[password][first]' => 'TestPassword',
            'user[password][second]' => 'TestPassword',
            'user[email]' => 'testemail@test.fr',
        ]);

        $this->client->submit($form);
        // $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects();
        $this->client->followRedirect();

        // $this->assertSelectorTextContains('.flash-success', "L'utilisateur a bien été ajouté.");

        // $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'TestUser']);

        // $this->assertNotNull($user);
    }

    public function testEditUser(): void
    {
        $this->createUser();

        $testUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'TestAdminUser']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/users/'.$testUser->getId().'/edit');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Update')->form([
            'user[password]' => 'NewPassword',
        ]);
        $this->client->submit($form);
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();

        $this->assertSelectorTextContains('.flash-success', "L'utilisateur a bien été modifié");
    }

    public function testDeleteUser(): void
    {
        $this->createUser();
        $testUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'TestAdminUser']);
        $this->client->loginUser($testUser);
        $this->client->request('POST', '/users/delete/'.$testUser->getId());

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();

        // $this->assertSelectorTextContains('.flash-success', "L'utilisateur a bien été supprimé");

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'TestAdminUser']);
        $this->assertNull($user);
    }

    // protected function tearDown(): void
    // {
    //     parent::tearDown();

    //     $this->entityManager->close();
    //     $this->entityManager = null;
    // }
}
