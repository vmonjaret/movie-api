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

    public function findPopulars(int $page = 1)
    {
        $firstResult = ($page - 1) * Movie::MAX_ITEMS;

        $query = $this->createQueryBuilder('m')
            ->orderBy('m.releasedAt', 'DESC')
            ->setFirstResult($firstResult)
            ->setMaxResults(Movie::MAX_ITEMS)
            ->getQuery();

        return $query->getResult();
    }

    public function findRecents(int $page = 1)
    {
        $firstResult = ($page - 1) * Movie::MAX_ITEMS;

        $query = $this->createQueryBuilder('m')
            ->where('m.releasedAt <= :now')
            ->orderBy('m.releasedAt', 'DESC')
            ->setFirstResult($firstResult)
            ->setMaxResults(Movie::MAX_ITEMS)
            ->setParameters(array('now' => new \DateTime()))
            ->getQuery();

        return $query->getResult();
    }

    public function findRandom()
    {
        $query = $this->createQueryBuilder('m')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getResult();
    }
}
