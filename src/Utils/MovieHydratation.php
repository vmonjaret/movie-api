<?php

namespace App\Utils;

use App\Entity\Movie;
use App\Entity\Notation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MovieHydratation
{
    private $tokenStorage;
    private $em;

    /**
     * MovieHydratation constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $entityManager;
    }


    public function hydrateMovieWithUser($result)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User) {
            if (!$result instanceof Movie) {
                foreach ($result as $movie) {
                    if ($user->getMoviesLiked()->contains($movie)) {
                        $movie->liked = true;
                    }
                    if ($user->getMoviesWatched()->contains($movie)) {
                        $movie->watched = true;
                    }
                    if ($user->getMoviesWished()->contains($movie)) {
                        $movie->wished = true;
                    }
                }
            } else {
                if ($user->getMoviesLiked()->contains($result)) {
                    $result->liked = true;
                }
                if ($user->getMoviesWatched()->contains($result)) {
                    $result->watched = true;
                }
                if ($user->getMoviesWished()->contains($result)) {
                    $result->wished = true;
                }

                $mark = $this->em->getRepository(Notation::class)->findOneBy(['movie' => $result->getId(), 'user' => $user->getId()]);
                if ($mark !== null) {
                    $result->mark = $mark->getMark();
                }

                $communityNoteResult = $result->getNotations();
                $communityNote = 0;

                if (sizeof($communityNoteResult) > 0){
                    foreach ($communityNoteResult as $notation) {
                        $communityNote += $notation->getMark();
                    }

                    $result->communityNote = $communityNote / sizeof($communityNoteResult);
                }
            }
        }
    }
}