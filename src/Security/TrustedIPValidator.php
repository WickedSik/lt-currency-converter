<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TrustedIPValidator implements UserCheckerInterface
{
    public function __construct(
        private readonly RequestStack $request
    )
    {
    }


    public function checkPreAuth(UserInterface $user): void
    {
        if($user instanceof User) {
            $trustedIps = $user->getTrustedIPs() ?: [];
            $ip = $this->request->getCurrentRequest()->getClientIp();

            if(!in_array($ip, $trustedIps)) {
                throw new CustomUserMessageAuthenticationException("This IP ( {$ip} ) is not trusted");
            }
        }

        return;
    }

    public function checkPostAuth(UserInterface $user): void
    {
        return;
    }
}