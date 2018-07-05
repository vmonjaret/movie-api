<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource(
 *     attributes={
            "normalization_context"={"groups" = {"movie"}}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 */
class Movie
{
    const MAX_ITEMS = 9;
    const MAX_SIMILAR = 3;
    const MAX_CASTING = 10;


    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @Groups({"movie", "comment"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"movie", "comment"})
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("movie")
     */
    private $overview;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"movie", "comment"})
     */
    private $cover;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("movie")
     */
    private $releasedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("movie")
     */
    private $runtime;

    /**
     * @ORM\Column(type="integer")
     * @Groups("movie")
     */
    private $popularity;

    /**
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="movies")
     * @ApiSubresource(maxDepth=1)
     * @Groups("movie")
     */
    private $genres;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="movie", orphanRemoval=true)
     * @ApiSubresource()
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Movie", mappedBy="mySuggestions", cascade={"persist"})
     */
    private $suggestions;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Movie", inversedBy="suggestions", cascade={"persist"})
     */
    private $mySuggestions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Casting", mappedBy="movie", orphanRemoval=true)
     */
    private $castings;

    /**
     * Movie constructor.
     */
    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->suggestions = new ArrayCollection();
        $this->suggestions = new ArrayCollection();
        $this->mySuggestions = new ArrayCollection();
        $this->castings = new ArrayCollection();
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

    public function setOverview(?string $overview): self
    {
        $this->overview = $overview;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): self
    {
        if ($cover !== null) {
            $this->cover = "https://image.tmdb.org/t/p/w500" . $cover;
        }

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

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        if ($this->genres->contains($genre)) {
            $this->genres->removeElement($genre);
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setMovie($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getMovie() === $this) {
                $comment->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Movie[]
     */
    public function getSuggestions(): Collection
    {
        return $this->suggestions;
    }

    public function addSuggestion(Movie $suggestion): self
    {
        if (!$this->suggestions->contains($suggestion)) {
            $this->suggestions[] = $suggestion;
            $suggestion->addMySuggestion($this);
        }

        return $this;
    }

    public function removeSuggestion(Movie $suggestion): self
    {
        if ($this->suggestions->contains($suggestion)) {
            $this->suggestions->removeElement($suggestion);
            $suggestion->removeMySuggestion($this);
        }

        return $this;
    }

    /**
     * @return Collection|Movie[]
     */
    public function getMySuggestions(): Collection
    {
        return $this->mySuggestions;
    }

    public function addMySuggestion(Movie $mySuggestion): self
    {
        if (!$this->mySuggestions->contains($mySuggestion)) {
            $this->mySuggestions[] = $mySuggestion;
        }

        return $this;
    }

    public function removeMySuggestion(Movie $mySuggestion): self
    {
        if ($this->mySuggestions->contains($mySuggestion)) {
            $this->mySuggestions->removeElement($mySuggestion);
        }

        return $this;
    }

    /**
     * @return Collection|Casting[]
     */
    public function getCastings(): Collection
    {
        return $this->castings;
    }

    public function addCasting(Casting $casting): self
    {
        if (!$this->castings->contains($casting)) {
            $this->castings[] = $casting;
            $casting->setMovie($this);
        }

        return $this;
    }

    public function removeCasting(Casting $casting): self
    {
        if ($this->castings->contains($casting)) {
            $this->castings->removeElement($casting);
            // set the owning side to null (unless already changed)
            if ($casting->getMovie() === $this) {
                $casting->setMovie(null);
            }
        }

        return $this;
    }
}
