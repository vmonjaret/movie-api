<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotationRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"comment"}}
 *     }
 * )
 */
class Notation
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notations")
     */
    private $user;
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie", inversedBy="notations")
     */
    private $movie;
    /**
     * @var int
     *
     * @ORM\Column(name="mark", type="integer")
     */
    private $mark;

    public function __construct(User $user, Movie $movie, $mark = null)
    {
        $this->movie = $movie;
        $this->user = $user;
        $this->mark = $mark;
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
