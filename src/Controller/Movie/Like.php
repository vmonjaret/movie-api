<?php

namespace App\Controller\Movie;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

class Like
{
    private $movieRepository;
    private $tokenStorage;
    private $entityManager;

    /**
     * Like constructor.
     * @param MovieRepository $movieRepository
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(MovieRepository $movieRepository, TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->movieRepository = $movieRepository;
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @param Movie $movie
     * @return JsonResponse
     */
    public function __invoke(Movie $movie)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user->getMoviesLiked()->contains($movie)) {
            $user->removeMovieLiked($movie);
        } else {
            $user->addMovieLiked($movie);
        }

        $this->entityManager->flush();

        return new JsonResponse(null, 200);
    }
}