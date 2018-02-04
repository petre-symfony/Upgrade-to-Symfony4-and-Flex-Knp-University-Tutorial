<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RandomAccessVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return $attribute === 'RANDOM_ACCESS';
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        return random_int(0, 10) > 5;
    }
}
