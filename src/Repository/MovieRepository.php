<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function search(string $title)
    {
        $query = $this->createQueryBuilder('m')
            ->where('m.title LIKE :title')
            ->setParameter('title', '%'.$title.'%')
            ->getQuery();

        return $query->getResult();
    }
}
