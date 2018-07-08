<?php

namespace App\Controller\Movie;

use App\Entity\Movie;
use App\Repository\MovieRepository;

class GetSimilars
{
    private $movieRepository;

    /**
     * GetSimilars constructor.
     */
    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function __invoke(Movie $movie)
    {
        $movies = $this->movieRepository->getSimilars($movie);

        return $movies;
    }


}