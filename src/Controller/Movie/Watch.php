<?php

namespace App\Controller\Movie;

use App\Entity\Movie;
use App\Manager\AchievementManager;
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
    private $achievementManager;

    /**
     * Watch constructor.
     * @param MovieRepository $movieRepository
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $entityManager
     * @param AchievementManager $achievementManager
     */
    public function __construct(MovieRepository $movieRepository, TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager, AchievementManager $achievementManager)
    {
        $this->movieRepository = $movieRepository;
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        $this->achievementManager = $achievementManager;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @param Movie $movie
     * @return JsonResponse
     */
    public function __invoke(Movie $movie)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user->getMoviesWatched()->contains($movie)) {
            $user->removeMovieWatched($movie);
        } else {
            $user->addMovieWatched($movie);
            $this->achievementManager->movieWatchAchievement($user);
        }

        $this->entityManager->flush();

        return new JsonResponse(null, 200);
    }
}