<?php

namespace App\Tests\Security;

use App\Entity\Task;
use App\Entity\User;
use App\Security\TaskVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskVoterTest extends TestCase
{
    private $securityMock;
    private $voter;

    protected function setUp(): void
    {
        $this->securityMock = $this->createMock(Security::class);
        $this->voter = new TaskVoter($this->securityMock);
    }

    public function publicSupports($attribute, $subject)
    {
        return $this->voter->publicSupports($attribute, $subject);
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->publicSupports(TaskVoter::VIEW, new Task()));
        $this->assertFalse($this->publicSupports('unknown_attribute', new Task()));
        $this->assertFalse($this->publicSupports(TaskVoter::VIEW, new \stdClass()));
    }

    public function testCanView(): void
    {
        $user = new User();
        $task = new Task();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $task->setUser($user);

        $this->assertTrue($this->voter->publicVoteOnAttribute(TaskVoter::VIEW, $task, $token));
    }

    public function testCanEditAsOwner(): void
    {
        $user = new User();
        $task = new Task();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $task->setUser($user);

        $this->assertTrue($this->voter->publicVoteOnAttribute(TaskVoter::EDIT, $task, $token));
    }

    public function testCannotEditAsNonOwner(): void
    {
        $user = new User();
        $otherUser = new User();
        $task = new Task();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($otherUser);

        $task->setUser($user);

        $this->assertFalse($this->voter->publicVoteOnAttribute(TaskVoter::EDIT, $task, $token));
    }

    public function testCanDeleteAsOwner(): void
    {
        $user = new User();
        $task = new Task();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $task->setUser($user);

        $this->assertTrue($this->voter->publicVoteOnAttribute(TaskVoter::DELETE, $task, $token));
    }

    public function testCannotDeleteAsNonOwner(): void
    {
        $user = new User();
        $otherUser = new User();
        $task = new Task();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($otherUser);

        $task->setUser($user);

        $this->assertFalse($this->voter->publicVoteOnAttribute(TaskVoter::DELETE, $task, $token));
    }

    public function testAdminCanDoAnything(): void
    {
        $user = new User();
        $task = new Task();

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->securityMock->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $this->assertTrue($this->voter->publicVoteOnAttribute(TaskVoter::VIEW, $task, $token));
        $this->assertTrue($this->voter->publicVoteOnAttribute(TaskVoter::EDIT, $task, $token));
        $this->assertTrue($this->voter->publicVoteOnAttribute(TaskVoter::DELETE, $task, $token));
    }
}
