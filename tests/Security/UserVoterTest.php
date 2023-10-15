<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\UserVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoterTest extends TestCase
{
    private $securityMock;
    private $voter;

    protected function setUp(): void
    {
        $this->securityMock = $this->createMock(Security::class);
        $this->voter = new UserVoter($this->securityMock);
    }

    public function publicSupports($attribute, $subject)
    {
        return $this->voter->publicSupports($attribute, $subject);
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->publicSupports(UserVoter::VIEW, new User()));
        $this->assertFalse($this->publicSupports('unknown_attribute', new User()));
        $this->assertFalse($this->publicSupports(UserVoter::VIEW, new \stdClass()));
    }

    public function testCanViewAsSelf(): void
    {
        $user = new User();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->assertTrue($this->voter->publicVoteOnAttribute(UserVoter::VIEW, $user, $token));
    }

    public function testCannotViewAsOthers(): void
    {
        $user = new User();
        $otherUser = new User();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($otherUser);

        $this->assertFalse($this->voter->publicVoteOnAttribute(UserVoter::VIEW, $user, $token));
    }

    public function testCanEditAsSelf(): void
    {
        $user = new User();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->assertTrue($this->voter->publicVoteOnAttribute(UserVoter::EDIT, $user, $token));
    }

    public function testCannotEditAsOthers(): void
    {
        $user = new User();
        $otherUser = new User();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($otherUser);

        $this->assertFalse($this->voter->publicVoteOnAttribute(UserVoter::EDIT, $user, $token));
    }

    public function testCanDeleteAsSelf(): void
    {
        $user = new User();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->assertTrue($this->voter->publicVoteOnAttribute(UserVoter::DELETE, $user, $token));
    }

    public function testCannotDeleteAsOthers(): void
    {
        $user = new User();
        $otherUser = new User();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($otherUser);

        $this->assertFalse($this->voter->publicVoteOnAttribute(UserVoter::DELETE, $user, $token));
    }

    public function testAdminCanDoAnything(): void
    {
        $user = new User();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->securityMock->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $this->assertTrue($this->voter->publicVoteOnAttribute(UserVoter::VIEW, $user, $token));
        $this->assertTrue($this->voter->publicVoteOnAttribute(UserVoter::EDIT, $user, $token));
        $this->assertTrue($this->voter->publicVoteOnAttribute(UserVoter::DELETE, $user, $token));
    }
}
