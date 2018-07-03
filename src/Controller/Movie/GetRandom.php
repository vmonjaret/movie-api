<?php

namespace App\Controller\Movie;

use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Request;

class GetRandom
{
    private $movieRepository;
    private $request;

    /**
     * GetRandom constructor.
     * @param $movieRepository
     */
    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function __invoke(Request $request)
    {
        $movie = $this->movieRepository->findRandom();

        return $movie;
    }
}