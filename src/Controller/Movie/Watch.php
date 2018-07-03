<?php
/**
 * Created by PhpStorm.
 * User: myriam
 * Date: 03/07/18
 * Time: 21:13
 */

namespace App\Controller\Movie;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

class Watch
{
    private $movieRepository;
    private $tokenStorage;
    private $entityManager;

    /**
     * Watch constructor.
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

        $movie = $this->movieRepository->find($movie->getId());
        if ($movie != null) {
            if ($user->getMoviesWatched()->contains($movie)) {
                $user->removeMovieWatched($movie);
            } else {
                $user->addMovieWatched($movie);
            }

            $this->entityManager->flush();

            return new JsonResponse(null, 200);
        }

        return new JsonResponse('Error:Movie not found', 400);
    }
}