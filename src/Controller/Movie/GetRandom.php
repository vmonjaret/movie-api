<?php

namespace App\Controller\Movie;

use App\Repository\MovieRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GetRandom extends Controller
{
    private $movieRepository;
    private $tokenStorage;

    /**
     * GetRandom constructor.
     * @param MovieRepository $movieRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(MovieRepository $movieRepository, TokenStorageInterface $tokenStorage)
    {
        $this->movieRepository = $movieRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        $em = $this->getDoctrine()->getManager();

        if(!is_string($this->tokenStorage->getToken()->getUser())) $movie = $this->movieRepository->findCustomRandom($this->tokenStorage->getToken()->getUser()->getId(), $em);
        else $movie = $this->movieRepository->findRandom($em);

        return $movie[0];
    }
}