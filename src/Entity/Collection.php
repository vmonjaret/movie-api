<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"collection"}},
 *     denormalizationContext={"groups"={"collection_write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CollectionRepository")
 */
class Collection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Le champs 'nom' est obligatoire"
     * )
     * @Groups({"collection", "collection_write", "feed"})
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"collection", "collection_write"})
     */
    private $isPublic;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"collection"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Movie", inversedBy="collections")
     * @Groups({"collection", "feed"})
     */
    private $movies;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="collections")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"collection"})
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Feed", mappedBy="collection", orphanRemoval=true)
     */
    private $feed;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->isPublic = true;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = trim($name);

        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return ArrayCollection|Movie[]
     */
    public function getMovies(): PersistentCollection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
        }

        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        if ($this->movies->contains($movie)) {
            $this->movies->removeElement($movie);
        }

        return $this;
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
}
