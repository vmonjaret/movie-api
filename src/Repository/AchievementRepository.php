<?php

namespace App\Repository;

use App\Entity\Achievement;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Achievement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Achievement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Achievement[]    findAll()
 * @method Achievement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchievementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Achievement::class);
    }

    public function getUserAchievement(User $user, string $type)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.type = :type')
            ->andWhere(':user MEMBER OF a.users')
            ->setParameters(array(
                'type' => $type,
                'user' => $user
            ))
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /*
    public function findOneBySomeField($value): ?Achievement
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
