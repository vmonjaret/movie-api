<?php

namespace App\Controller\Genre;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetFavorites
{
    private $tokenStorage;
    private $entityManager;

    /**
     * FavoritesGenres constructor.
     * @param $tokenStorage
     * @param $entityManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function __invoke()
    {
        /**
         * @var User $user
         */
        $user = $this->tokenStorage->getToken()->getUser();

        return $user->getFavoritesGenres();
    }




}