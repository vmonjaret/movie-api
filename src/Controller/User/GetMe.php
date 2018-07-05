<?php

namespace App\Controller\User;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetMe
{
    private $tokenStorage;

    /**
     * GetMe constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return $user;
    }

}