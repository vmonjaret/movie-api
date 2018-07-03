<?php

namespace App\Controller\Movie;

use App\Entity\Movie;
use App\Repository\CastingRepository;

class GetCasting
{
    private $castingRepository;

    /**
     * GetCasting constructor.
     * @param $castingRepository
     */
    public function __construct(CastingRepository $castingRepository)
    {
        $this->castingRepository = $castingRepository;
    }

    public function __invoke(Movie $movie)
    {
        $casting = $this->castingRepository->getMovieCasting($movie);

        return $casting;
    }


}