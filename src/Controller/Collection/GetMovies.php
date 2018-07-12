<?php

namespace App\Controller\Collection;

use App\Entity\Collection;
use App\Entity\User;
use App\Utils\MovieHydratation;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetMovies
{
    private $tokenStorage;
    private $movieHydratation;

    /**
     * ListMovies constructor.
     * @param $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage, MovieHydratation $movieHydratation)
    {
        $this->tokenStorage = $tokenStorage;
        $this->movieHydratation = $movieHydratation;
    }

    public function __invoke(Collection $collection)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if ($collection->getIsPublic() || ($user instanceof User && $user == $collection->getUser())) {
            $movies = $collection->getMovies();
            $this->movieHydratation->hydrateMovieWithUser($movies);
            return $movies;
        }

        return array();
    }

}