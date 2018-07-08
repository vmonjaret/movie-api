<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource
 * @ORM\Entity(repositoryClass="App\Repository\GenreRepository")
 */
class Genre
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @Groups("user")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"movie", "user"})
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Movie", mappedBy="genres")
     * @ApiSubresource(maxDepth=1)
     */
    private $movies;

    /**
     * Genre constructor.
     */
    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Genre
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Genre
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Movie[]|ArrayCollection
     */
    public function getMovies()
    {
        return $this->movies;
    }

    /**
     * @param Movie $movie
     * @return $this
     */
    public function addMovie(Movie $movie)
    {
        $this->movies[] = $movie;

        return $this;
    }

    /**
     * @param Movie $movie
     * @return $this
     */
    public function removeMovie(Movie $movie)
    {
        $this->movies->removeElement($movie);

        return $this;
    }
}
