<?php

namespace App\Manager;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Tmdb\ApiToken;
use Tmdb\Client;
use Tmdb\Model\Genre as TmdbGenre;

class GenreManager
{
    private $genreRepository;
    private $entityManager;
    private $apiGenreRepo;

    /**
     * GenreManager constructor.
     * @param $genreRepository
     * @param $entityManager
     */
    public function __construct(GenreRepository $genreRepository, EntityManagerInterface $entityManager)
    {
        $this->genreRepository = $genreRepository;
        $this->entityManager = $entityManager;
        $token = new ApiToken(getenv('TMDBD_API_KEY'));
        $client = new Client($token);
        $this->apiGenreRepo = new \Tmdb\Repository\GenreRepository($client);
    }

    public function getGenre(TmdbGenre $import): Genre
    {
        $genre = $this->genreRepository->find($import->getId());
        if (null === $genre) {
            $genre = $this->apiGenreRepo->load($import->getId(), array('language' => 'fr'));
            $genre = $this->createFromTmdbModel($genre);
        }

        return $genre;
    }

    public function createFromTmdbModel(\Tmdb\Model\Genre $import)
    {
        $genre = (new Genre())
            ->setId($import->getId())
            ->setName($import->getName());

        $this->entityManager->persist($genre);
        $this->entityManager->flush();

        return $genre;
    }
}