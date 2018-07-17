<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 * @ApiResource(
 *     normalizationContext={"groups"={"comment"}},
 *     denormalizationContext={"groups"={"comment_write"}},
 *     order={"createdAt": "DESC"}
 * )
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"comment", "profile", "feed"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(
     *     message="Le champs contenu est obligatoire"
     * )
     * @Groups({"comment", "comment_write", "profile", "feed"})
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     */
    private $signaled = false;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"comment", "profile"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"comment", "comment_write", "profile"})
     */
    private $movie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"comment"})
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Feed", mappedBy="comment", orphanRemoval=true)
     */
    private $feed;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = trim($content);

        return $this;
    }

    public function getSignaled()
    {
        return $this->signaled;
    }

    public function setSignaled($signaled)
    {
        $this->signaled = $signaled;

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

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

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
