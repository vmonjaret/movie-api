<?php

namespace App\Controller\Movie;

use App\Entity\User;
use App\Utils\MovieHydratation;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetOtherUserMovies
{
    /**
     * @IsGranted("ROLE_USER")
     */
    public function __invoke(User $user)
    {
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