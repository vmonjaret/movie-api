<?php

namespace App\Controller\Movie;

use App\Entity\FavoritesMovies;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddFavorite
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
     * @param FavoritesMovies $data
     * @return JsonResponse
     */
    public function __invoke(FavoritesMovies $data)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        foreach ($data->getMovies() as $movie) {
            if ($user->getMoviesLiked()->contains($movie)) {
                $user->removeMovieLiked($movie);
            } else {
                $user->addMovieLiked($movie);
            }
        }

        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }




}