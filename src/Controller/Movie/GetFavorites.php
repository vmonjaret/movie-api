<?php

namespace App\Controller\Movie;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
        $user = $this->tokenStorage->getToken()->getUser();

        return $user->getMoviesLiked();
    }




}