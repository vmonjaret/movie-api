<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"user"}},
 *          "denormalization_context"={"groups"={"user_write"}}
 *     }
 * )
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user"})
     */
    private $id;

    /**
     * @Assert\Email()
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user_write"})
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=25, unique=true)
     * @Groups({"user", "comment", "user_write"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Groups({"user_write"})
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = true;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Genre", cascade={"persist"})
     * @Groups("user")
     */
    private $favoritesGenres;

    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->comments = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->favoritesGenres = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken($confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive($active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function isEnabled()
    {
        return $this->active;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->active
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->active
            ) = unserialize($serialized, array('allowed_classes' => false));
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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
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
     * @return Collection|Genre[]
     */
    public function getFavoritesGenres(): Collection
    {
        return $this->favoritesGenres;
    }

    public function addFavoritesGenre(Genre $favoritesGenre): self
    {
        if (!$this->favoritesGenres->contains($favoritesGenre)) {
            $this->favoritesGenres[] = $favoritesGenre;
        }

        return $this;
    }

    public function removeFavoritesGenre(Genre $favoritesGenre): self
    {
        if ($this->favoritesGenres->contains($favoritesGenre)) {
            $this->favoritesGenres->removeElement($favoritesGenre);
        }

        return $this;
    }
}
