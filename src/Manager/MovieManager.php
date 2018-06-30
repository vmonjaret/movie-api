<?php

namespace App\Manager;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Tmdb\Model\Movie as TmdbMovie;

class MovieManager
{
    private $genreManager;
    private $entityManager;

    /**
     * MovieManager constructor.
     * @param $genreManager
     */
    public function __construct(GenreManager $genreManager, EntityManagerInterface $entityManager)
    {
        $this->genreManager = $genreManager;
        $this->entityManager = $entityManager;
    }

    public function getMovie(TmdbMovie $import): Movie
    {
        $movie = $this->entityManager->getRepository(Movie::class)->find($import->getId());
        if (null === $movie) {
            $movie = $this->createMovieFromModel($import);
        }

        return $movie;
    }

    public function createMovieFromModel(TmdbMovie $import): Movie
    {
        $movie = new Movie();
        $movie->setId($import->getId())
            ->setTitle($import->getTitle())
            ->setOverview($import->getOverview())
            ->setReleasedAt($import->getReleaseDate())
            ->setRuntime($import->getRuntime())
            ->setCover($import->getPosterPath());

        foreach ($import->getGenres() as $genre) {
            $movie->addGenres($this->genreManager->getGenre($genre));
        }

        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        return $movie;
    }
}