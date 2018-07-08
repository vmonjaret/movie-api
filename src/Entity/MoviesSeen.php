<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(attributes={
 *      "normalization_context"={"groups"={"light_movie"}}
 * })
 * Class MoviesSeen
 * @package App\Entity
 */
class MoviesSeen
{
    /**
     * @var Movie[]
     * @Groups("light_movie")
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
     * @param mixed $movies
     * @return MoviesSeen
     */
    public function setMovies($movies)
    {
        $this->movies = $movies;
        return $this;
    }
}