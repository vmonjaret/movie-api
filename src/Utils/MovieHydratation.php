<?php

namespace App\Utils;

use App\Entity\Movie;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MovieHydratation
{
    private $tokenStorage;

    /**
     * MovieHydratation constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }


    public function hydrateMovieWithUser($result)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User) {
            if (!$result instanceof Movie) {
                foreach ($result as $movie) {
                    if ($user->getMoviesLiked()->contains($movie)) {
                        $movie->liked = true;
                    }
                    if ($user->getMoviesWatched()->contains($movie)) {
                        $movie->watched = true;
                    }
                    if ($user->getMoviesWished()->contains($movie)) {
                        $movie->wished = true;
                    }
                }
            } else {
                if ($user->getMoviesLiked()->contains($result)) {
                    $result->liked = true;
                }
                if ($user->getMoviesWatched()->contains($result)) {
                    $result->watched = true;
                }
                if ($user->getMoviesWished()->contains($result)) {
                    $result->wished = true;
                }
            }
        }
    }
}