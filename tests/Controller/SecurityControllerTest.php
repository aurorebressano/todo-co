<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $userRepository;
    private $userPasswordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userPasswordHasher = $this->client->getContainer()->get('security.user_password_hasher');
        $this->userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
    }

    private function createUser(): User
    {
        $user = new User();
        $user
            ->setEmail('user@email.fr')
            ->setPassword($this->userPasswordHasher->hashPassword($user, 'password'))
            ->setUsername('username')
            ->setRoles(['ROLE_USER']);
        $this->userRepository->save($user, true);

        return $user;
    }

    public function testLoginAccess(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('form.login-form')->count());
    }

    public function testLoginWithInvalidCredentials(): void
    {

        $crawler = $this->client->request('GET', '/login');

        // Submit the login form with invalid credentials
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'invalid_username',
            '_password' => 'invalid_password',
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertSelectorTextContains('.alert-danger', 'Invalid credentials');
    }

    public function testLoginSuccess(): void
    {
        $this->createUser();

        $this->client->request('GET', '/login');
        $this->client->submitForm('Se connecter', ['_username' => 'username', '_password' => 'password']);
        $crawler = $this->client->followRedirect();
        $currentUrl = $this->client->getRequest()->getPathInfo();

        $this->assertResponseIsSuccessful();
        $this->assertEquals('/', $currentUrl);
    }

    public function testLogout(): void
    {
        $this->client->loginUser($this->createUser());
        $this->client->request('GET', '/logout');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful('/login');
    }
}