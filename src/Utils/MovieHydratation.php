<?php

namespace App\Utils;

use App\Entity\Movie;
use App\Entity\Notation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MovieHydratation extends Controller
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
                    $mark = $this->em->getRepository(Notation::class)->findOneBy(['movie' => $result->getId(), 'user' => $user->getId()]);
                    if($mark !== null){
                        $result->mark = $mark->getMark();
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

                $community_note_result = $this->em->getRepository(Notation::class)->findBy(['movie' => $result->getId()]);
                $community_note = 0;

                if (sizeof($community_note_result) > 0){
                    foreach ($community_note_result as $notation) {
                        $community_note += $notation->getMark();
                    }

                    $result->community_note = $community_note / sizeof($community_note_result);
                }
            }
        }
    }
}