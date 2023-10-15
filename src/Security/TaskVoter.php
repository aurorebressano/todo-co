<?php

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    // these strings are just invented: you can use anything+

    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    public function __construct(private Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        // only vote on `Task` objects
        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    public function publicSupports($attribute, $subject)
    {
        return $this->supports($attribute, $subject);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Task object, thanks to `supports()`
        /** @var Task $task */
        $task = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($task, $user),
            self::EDIT => $this->canEdit($task, $user),
            self::DELETE => $this->canDelete($task, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    public function publicVoteOnAttribute($attribute, $subject, $token)
    {
        return $this->voteOnAttribute($attribute, $subject, $token);
    }

    private function canView(Task $task, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($task, $user)) {
            return true;
        }

        // the Task object could have, for example, a method `isPrivate()`
        return !$task->isPrivate();
    }

    private function canEdit(Task $task, User $user): bool
    {
        // this assumes that the Task object has a `getOwner()` method
        return $user == $task->getUser();
    }

    private function canDelete(Task $task, User $user): bool
    {
        // this assumes that the Task object has a `getOwner()` method
        return $user == $task->getUser();
    }
}
