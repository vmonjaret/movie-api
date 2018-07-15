<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"feed"}},
 *     order={"createdAt": "DESC"}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\FeedRepository")
 */
class Feed
{
    const TYPE_COMMENT = "comment";
    const TYPE_COLLECTION = "collection";
    const TYPE_NOTATION = "notation";
    const FOLLOW = "follow";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("feed")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("feed")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="feeds", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"feed", "user"})
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("feed")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Groups({"feed", "profile"})
     */
    private $friend;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie")
     * @Groups({"feed", "light_movie"})
     */
    private $movie;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Comment", inversedBy="feed")
     * @Groups({"feed", "comment"})
     */
    private $comment;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Notation", inversedBy="feed")
     * @Groups({"feed", "notation"})
     */
    private $notation;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Collection", inversedBy="feed")
     * @Groups({"feed", "collection"})
     */
    private $collection;

    /**
     * Feed constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFriend(): ?User
    {
        return $this->friend;
    }

    public function setFriend(?User $friend): self
    {
        $this->friend = $friend;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getNotation(): ?Notation
    {
        return $this->notation;
    }

    public function setNotation(?Notation $notation): self
    {
        $this->notation = $notation;

        return $this;
    }

    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    public function setCollection(?Collection $collection): self
    {
        $this->collection = $collection;

        return $this;
    }
}
