<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class BlogCommentVoter extends Voter
{
    public const BLOGCOMMENT_EDIT = 'BLOGCOMMENT_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::BLOGCOMMENT_EDIT])
            && $subject instanceof \App\Entity\BlogComment;
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
            case self::BLOGCOMMENT_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $subject->getUser() === $user || in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_MODERATOR', $user->getRoles());
                break;
        }

        return false;
    }
}