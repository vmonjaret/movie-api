<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotationRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"notation"}}
 *     }
 * )
 */
class Notation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie", inversedBy="notations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $movie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notations", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @ApiSubresource(maxDepth=1)
     * @Groups({"comment"})
     */
    private $user;

    /**
     * @var int
     * @ORM\Column(name="mark", type="integer")
     * @Groups({"notation"})
     */
    private $mark;

    public function getId()
    {
        return $this->id;
    }

    public function setMark($mark)
    {
        $this->mark = $mark;
        return $this;
    }

    public function getMark()
    {
        return $this->mark;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setMovie(Movie $movie)
    {
        $this->movie = $movie;
        return $this;
    }

    public function getMovie()
    {
        return $this->movie;
    }
}
