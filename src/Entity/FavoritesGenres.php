<?php

namespace App\Entity;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource()
 */
class FavoritesGenres {

    /**
     * @var Genre[]
     */
    private $genres;

    /**
     * @return mixed
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @param mixed $genres
     * @return FavoritesGenres
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;
        return $this;
    }
}