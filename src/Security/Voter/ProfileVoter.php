<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class ProfileVoter extends Voter
{
    public const PROFILE_ACCESS = 'PROFILE_ACCESS';


    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::PROFILE_ACCESS])
            /* && $subject instanceof \App\Entity\User */;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::PROFILE_ACCESS:
                // logic to determine if the user can EDIT
                // return true or false
                return $subject->getId() === $user->getId();
                break;
        }

        return false;
    }
}
