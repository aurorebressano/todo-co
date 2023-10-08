<?php

namespace App\Security;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    // these strings are just invented: you can use anything+

    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    public const CREATE = 'create';

    public function __construct(private Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::CREATE, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof User) {
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
        $currentUser = $token->getUser();

        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$currentUser instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Trick object, thanks to `supports()`
        /** @var User $task */
        $user = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($user, $currentUser),
            self::EDIT => $this->canEdit($user, $currentUser),
            self::DELETE => $this->canDelete($user, $currentUser),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    public function publicVoteOnAttribute($attribute, $subject, $token)
    {
        return $this->voteOnAttribute($attribute, $subject, $token);
    }

    private function canView(User $user, User $currentUser): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($user, $currentUser)) {
            return true;
        }

        // the Trick object could have, for example, a method `isPrivate()`
        return false;
    }

    private function canEdit(User $user, User $currentUser): bool
    {
        // this assumes that the Trick object has a `getOwner()` method
        return $user === $currentUser;
    }

    private function canDelete(User $user, User $currentUser): bool
    {
        // this assumes that the Trick object has a `getOwner()` method
        return $user === $currentUser;
    }
}
