<?php

namespace App\Controller\Movie;

use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Request;

class GetSearch
{
    private $movieRepository;
    private $request;

    /**
     * GetSearch constructor.
     * @param $movieRepository
     */
    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function __invoke(Request $request)
    {
        $title = $request->get('title');
        $movies = $this->movieRepository->search($title);

        return $movies;
    }


}