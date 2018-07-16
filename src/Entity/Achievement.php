<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"achievement"}},
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\AchievementRepository")
 */
class Achievement
{
    const AUTHOR = 'author';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"achievement", "profile"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"achievement", "profile"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Groups({"achievement", "profile"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="achievements")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    /**
     * @param array $achievement
     * @return Achievement
     */
    public function createFromArray(array $achievement): self
    {
        $this->name = $achievement['name'];
        $this->description = $achievement['description'];
        $this->type = $achievement['type'];

        return $this;
    }
}
