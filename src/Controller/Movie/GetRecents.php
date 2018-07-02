<?php

namespace App\Controller\Movie;

use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Request;

class GetRecents
{
    private $movieRepository;
    private $request;

    /**
     * GetPopulars constructor.
     * @param $movieRepository
     */
    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function __invoke(Request $request)
    {
        $page = $request->get('page') ?? 1;
        $movies = $this->movieRepository->findRecents($page);

        return $movies;
    }


}