<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getStatsMovieByGenre(User $user)
    {
        $rawSql = "
SELECT count(*) as nb_movies, genre.name as name
FROM watched_movies
LEFT JOIN movie_genre ON movie_genre.movie_id = watched_movies.movie_id
LEFT JOIN genre ON genre.id = movie_genre.genre_id
WHERE watched_movies.user_id = :id
GROUP BY movie_genre.genre_id
ORDER BY nb_movies DESC";

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute(['id' => $user->getId()]);

        return $stmt->fetchAll();
    }
}
