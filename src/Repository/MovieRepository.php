<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function getSimilars(Movie $movie, int $maxResult = 3)
    {
        $query = $this->createQueryBuilder('m')
            ->leftJoin('m.mySuggestions', 'my_suggestions')
            ->leftJoin('m.suggestions', 'suggestions')
            ->where('suggestions.id = :movieId')
            ->orWhere('my_suggestions.id = :movieId')
            ->setMaxResults($maxResult)
            ->setParameter('movieId', $movie->getId())
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function findCustomRandom($id, $em)
    {
        $sql = " 
        SELECT *
          FROM movie
          LEFT JOIN movie_genre as moviesGenre ON moviesGenre.movie_id = movie.id
          where 
          (moviesGenre.genre_id IN (
              SELECT DISTINCT(favoriteMoviesGenre.genre_id) FROM movie
              LEFT JOIN liked_movies as likedMovies ON likedMovies.user_id = :id
              LEFT JOIN movie_genre as favoriteMoviesGenre ON favoriteMoviesGenre.movie_id = likedMovies.movie_id)
              OR moviesGenre.genre_id IN (
              SELECT DISTINCT(favoriteGenres.genre_id) FROM movie
              LEFT JOIN user_genre as favoriteGenres ON favoriteGenres.user_id = :id)
          )
          AND NOT EXISTS (
              SELECT DISTINCT(liked_movies.movie_id) FROM liked_movies
              WHERE liked_movies.user_id = :id AND liked_movies.movie_id = movie.id
          )
          AND NOT EXISTS (
              SELECT DISTINCT(watched_movies.movie_id) FROM watched_movies
              WHERE watched_movies.user_id = :id AND watched_movies.movie_id = movie.id
          )
          AND NOT EXISTS (
              SELECT DISTINCT(wished_movies.movie_id) FROM wished_movies
              WHERE wished_movies.user_id = :id AND wished_movies.movie_id = movie.id
          )
          
          GROUP BY id
          ORDER BY RAND()
        ";

        $params['id'] = $id;

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findRandom($em)
    {
        $sql = " 
        SELECT *
          FROM movie
          GROUP BY id
          ORDER BY RAND()
        ";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
