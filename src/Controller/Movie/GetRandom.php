<?php

namespace App\Controller\Movie;

use App\Manager\AchievementManager;
use App\Repository\MovieRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GetRandom extends Controller
{
    private $movieRepository;
    private $tokenStorage;
    private $achievementManager;

    /**
     * GetRandom constructor.
     * @param MovieRepository $movieRepository
     * @param TokenStorageInterface $tokenStorage
     * @param AchievementManager $achievementManager
     */
    public function __construct(MovieRepository $movieRepository, TokenStorageInterface $tokenStorage, AchievementManager $achievementManager)
    {
        $this->movieRepository = $movieRepository;
        $this->tokenStorage = $tokenStorage;
        $this->achievementManager = $achievementManager;
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->tokenStorage->getToken()->getUser();

        if(!is_string($user)) {
            $movie = $this->movieRepository->findCustomRandom($user->getId(), $em);
        } else {
            $movie = $this->movieRepository->findRandom($em);
        }

        $this->achievementManager->movieRandomAchievement($user);

        return $movie[0];
    }
}