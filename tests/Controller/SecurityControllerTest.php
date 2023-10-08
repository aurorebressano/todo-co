<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // Submit the login form with invalid credentials
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'invalid_username',
            '_password' => 'invalid_password',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('.alert-danger', 'Invalid credentials');
    }
}
