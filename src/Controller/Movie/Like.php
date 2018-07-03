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
     */
    public function __construct(MovieRepository $movieRepository, TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->movieRepository = $movieRepository;
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    /**
     * @IsGranted("ROLE_USER")
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

        return new JsonResponse(null, 201);
    }
}