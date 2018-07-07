<?php

namespace App\Entity;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource()
 */
class FavoritesMovies {

    /**
     * @var Movie[]
     */
    private $movies;

    /**
     * @return mixed
     */
    public function getMovies()
    {
        return $this->movies;
    }

    /**
     * @param $movies
     * @return FavoritesMovies
     */
    public function setMovies($movies)
    {
        $this->movies = $movies;
        return $this;
    }
}