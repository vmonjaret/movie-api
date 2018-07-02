<?php

namespace App\Manager;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Tmdb\ApiToken;
use Tmdb\Client;
use Tmdb\Model\Movie as TmdbMovie;
use Tmdb\Repository\MovieRepository;

class MovieManager
{
    private $genreManager;
    private $entityManager;
    private $movieRepostiory;

    /**
     * MovieManager constructor.
     * @param $genreManager
     */
    public function __construct(GenreManager $genreManager, EntityManagerInterface $entityManager)
    {
        $this->genreManager = $genreManager;
        $this->entityManager = $entityManager;

        $token = new ApiToken(getenv('TMDBD_API_KEY'));
        $client = new Client($token);
        $this->movieRepostiory = new MovieRepository($client);
    }

    public function getMovie(TmdbMovie $import, bool $isRecommandation = false): Movie
    {
        $movie = $this->entityManager->getRepository(Movie::class)->find($import->getId());
        if (null === $movie) {
            $movie = $this->createMovieFromModel($import, $isRecommandation);
        }

        return $movie;
    }

    public function createMovieFromModel(TmdbMovie $import, bool $isRecommandation = false): Movie
    {
        $movie = new Movie();
        $movie->setId($import->getId())
            ->setTitle($import->getTitle())
            ->setOverview($import->getOverview())
            ->setReleasedAt($import->getReleaseDate())
            ->setRuntime($import->getRuntime())
            ->setCover($import->getPosterPath())
            ->setPopularity($import->getPopularity());

        foreach ($import->getGenres() as $genre) {
            $movie->addGenre($this->genreManager->getGenre($genre));
        }

        if (!$isRecommandation) {
            $recommandations = $this->movieRepostiory->getSimilar($import->getId(), array('language' => 'fr'));
            $i = 0;
            foreach ($recommandations as $recommandation) {
                $movie->addSuggestion($this->getMovie($recommandation, true));
                $i++;
                if($i >= 3) {
                    break;
                }
            }
        }

        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        return $movie;
    }

    public function updateMovie(Movie $movie, TmdbMovie $import)
    {
        $movie->setId($import->getId())
            ->setTitle($import->getTitle())
            ->setOverview($import->getOverview())
            ->setReleasedAt($import->getReleaseDate())
            ->setRuntime($import->getRuntime())
            ->setCover($import->getPosterPath())
            ->setPopularity($import->getPopularity());

        return $movie;
    }
}