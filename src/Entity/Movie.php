<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 */
class Movie
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $overview;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cover;

    /**
     * @ORM\Column(type="datetime")
     */
    private $releasedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $runtime;

    /**
     * @ORM\Column(type="integer")
     */
    private $popularity;

    /**
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="movies")
     * @ApiSubresource(maxDepth=1)
     */
    private $genres;

    /**
     * Movie constructor.
     */
    public function __construct()
    {
        $this->genres = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getOverview(): ?string
    {
        return $this->overview;
    }

    public function setOverview(string $overview): self
    {
        $this->overview = $overview;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): self
    {
        $this->cover = "https://image.tmdb.org/t/p/w500" . $cover;

        return $this;
    }

    public function getReleasedAt(): ?\DateTimeInterface
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(\DateTimeInterface $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getRuntime(): ?int
    {
        return $this->runtime;
    }

    /**
     * @param int $runtime
     * @return Movie
     */
    public function setRuntime(?int $runtime): self
    {
        $this->runtime = $runtime;

        return $this;
    }

    /**
     * @return int
     */
    public function getPopularity(): int
    {
        return $this->popularity;
    }

    /**
     * @param mixed $popularity
     * @return Movie
     */
    public function setPopularity(int $popularity): self
    {
        $this->popularity = $popularity;

        return $this;
    }

    /**
     * @return Genre[]|ArrayCollection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @param Genre $genres
     * @return Movie
     */
    public function addGenres(Genre $genre): self
    {
        $this->genres[] = $genre;

        return $this;
    }

    /**
     * @param Genre $genres
     * @return Movie
     */
    public function removeGenres(Genre $genre): self
    {
        $this->genres->removeElement($genre);

        return $this;
    }
}
