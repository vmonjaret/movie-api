<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class StatsMoviesByGenre
{
    private $tokenStorage;
    private $userRepository;

    /**
     * StatsMoviesByGenre constructor.
     * @param $tokenStorage
     * @param $entityManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, UserRepository $userRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->userRepository = $userRepository;
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

        $stats = $this->userRepository->getStatsMovieByGenre($user);

        return $stats;
    }


}