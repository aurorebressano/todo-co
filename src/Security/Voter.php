<?php

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

abstract class Voter implements VoterInterface
{
    abstract protected function supports(string $attribute, mixed $subject): bool;

    abstract protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool;
}
