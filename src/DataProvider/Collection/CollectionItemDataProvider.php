<?php

namespace App\DataProvider\Collection;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Collection;
use App\Entity\User;
use App\Repository\CollectionRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CollectionItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $collectionRepository;
    private $itemExtensions;
    private $tokenStorage;

    /**
     * CollectionCollectionDataProvider constructor.
     * @param $collectionRepository
     * @param $itemExtensions
     * @param $tokenStorage
     */
    public function __construct(CollectionRepository $collectionRepository, iterable $itemExtensions, TokenStorageInterface $tokenStorage)
    {
        $this->collectionRepository = $collectionRepository;
        $this->itemExtensions = $itemExtensions;
        $this->tokenStorage = $tokenStorage;
    }


    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Collection::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $queryBuilder = $this->collectionRepository->createQueryBuilder('c');

        if ($user instanceof User) {
            $queryBuilder->andWhere('c.isPublic = true OR c.user = :user')
            ->setParameter('user', $user->getId());
        } else {
            $queryBuilder->andWhere('c.isPublic = true');

        }

        $identifiers = ['id' => $id];
        $queryNameGenerator = new QueryNameGenerator();
        foreach ($this->itemExtensions as $extension) {
            $extension->applyToItem($queryBuilder, $queryNameGenerator, $resourceClass, $identifiers, $operationName, $context);
            if ($extension instanceof QueryResultItemExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        $queryBuilder->andWhere('c.id = :id')
            ->setParameter('id', $id);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}