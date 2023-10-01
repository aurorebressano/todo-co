<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTest extends KernelTestCase
{
    private $validator;
    private $userPasswordHasherInterface;

    protected function setUp(): void
    {
        // parent::setUp();

        self::bootKernel();
        //$this->validator = static::getContainer()->get('validator');
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
        $this->userPasswordHasherInterface = static::getContainer()->get('security.user_password_hasher');
    }

    // protected function tearDown(): void
    // {
    //     $this->validator = null;
    //     parent::tearDown();
    // }

    private function validateUser(User $user): ConstraintViolationListInterface
    {
        return $this->validator->validate($user);
    }

    public function testValidEmail(): void
    {
        $user = new User();
        $user->setUsername('TesteurPHP1');
        $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'test'));
        $user->setEmail('test@example.com');
        $errors = $this->validator->validate($user);
        foreach ($errors as $error) {
            echo $error->getMessage() . "\n";
            // echo $user->getEmail()  . "\n";
        }

        $this->assertCount(0, $errors, "Aucune erreur attendue pour un email valide");
    }

    public function testInvalidEmail(): void
    {
        $user = new User();
        $user->setUsername('TesteurPHP2');
        $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'test'));
        $user->setEmail('invalid-email');
        $errors = $this->validateUser($user);

        foreach ($errors as $error) {
            echo $error->getMessage() . "\n";
            echo $user->getEmail()  . "\n";
        }

        $this->assertCount(1, $errors, "Erreurs de validation attendues pour un email invalide.");
    }

    public function testEmptyEmail(): void
    {
        $user = new User();
        $user->setUsername('TesteurPHP3');
        $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'test'));
        $user->setEmail('');
        $errors = $this->validateUser($user);

        $this->assertCount(1, $errors, "Erreur de validation attendue pour un email vide.");
    }

    public function testEmptyUsername(): void
    {
        $user = new User();
        $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'test'));
        $user->setUsername('');
        $user->setEmail('test@example.com');
        $errors = $this->validateUser($user);

        foreach ($errors as $error) {
            echo $error->getMessage() . "\n";
            echo $user->getUsername()  . "\n";
        }

        $this->assertCount(1, $errors, "Erreur de validation attendue pour un username vide.");
    }
}
   