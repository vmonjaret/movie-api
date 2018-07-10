<?php

namespace App\Controller\Movie;

use App\Entity\User;
use App\Utils\MovieHydratation;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetUserMovies
{
    private $tokenStorage;
    private $movieHydratation;

    /**
     * GetMe constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorage, MovieHydratation $movieHydratation)
    {
        $this->tokenStorage = $tokenStorage;
        $this->movieHydratation = $movieHydratation;
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

        $movies = new ArrayCollection();
        $moviesWished = $user->getMoviesWished();
        $this->addToArray($movies, $moviesWished, 'wished');
        $moviesWatched = $user->getMoviesWatched();
        $this->addToArray($movies, $moviesWatched, 'watched');
        $moviesLiked = $user->getMoviesLiked();
        $this->addToArray($movies, $moviesLiked, 'liked');

        return $movies;
    }

    public function addToArray(ArrayCollection $first, $second, $type)
    {
        foreach ($second as $movie) {
            $position = $first->indexOf($movie);
            if (false === $position) {
                $movie->$type = true;
                $first[] = $movie;
            } else {
                $first->get($position)->$type = true;
            }
        }
    }
}