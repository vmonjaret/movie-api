<?php

namespace App\Controller\Movie;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Utils\MovieHydratation;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetSimilars
{
    private $movieRepository;
    private $movieHydratation;

    /**
     * GetSimilars constructor.
     */
    public function __construct(MovieRepository $movieRepository, MovieHydratation $movieHydratation)
    {
        $this->movieRepository = $movieRepository;
        $this->movieHydratation = $movieHydratation;
    }

    public function __invoke(Movie $movie)
    {
        $movies = $this->movieRepository->getSimilars($movie);
        $this->movieHydratation->hydrateMovieWithUser($movies);

        return $movies;
    }


}