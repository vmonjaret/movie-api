<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 */
class CollectionItem
{
    /**
     * @var Collection $collection
     */
    private $collection;

    /**
     * @var Movie $movie
     */
    private $movie;

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param mixed $collection
     * @return CollectionItem
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMovie()
    {
        return $this->movie;
    }

    /**
     * @param mixed $movie
     * @return CollectionItem
     */
    public function setMovie($movie)
    {
        $this->movie = $movie;
        return $this;
    }
}
