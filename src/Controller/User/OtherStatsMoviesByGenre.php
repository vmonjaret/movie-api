<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OtherStatsMoviesByGenre
{
    private $userRepository;

    /**
     * StatsMoviesByGenre constructor.
     * @param $tokenStorage
     * @param $entityManager
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function __invoke(User $user)
    {
        $stats = $this->userRepository->getStatsMovieByGenre($user);

        return $stats;
    }


}