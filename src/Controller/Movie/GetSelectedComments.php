<?php

namespace App\Controller\Movie;

use App\Entity\Movie;
use App\Repository\CommentRepository;
use App\Repository\MovieRepository;
use App\Utils\MovieHydratation;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetSelectedComments
{
    private $commentRepository;

    /**
     * GetSelectedComments constructor.
     * @param CommentRepository $commentRepository
     */
    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function __invoke(Movie $movie)
    {
        $comments = $this->commentRepository->getMovieSelectedComments($movie);

        return $comments;
    }


}