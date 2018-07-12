<?php

namespace App\DataProvider\Collection;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Collection;
use App\Entity\User;
use App\Repository\CollectionRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CollectionCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $collectionRepository;
    private $collectionExtensions;
    private $tokenStorage;

    /**
     * CollectionCollectionDataProvider constructor.
     * @param $collectionRepository
     * @param $collectionExtensions
     * @param $tokenStorage
     */
    public function __construct(CollectionRepository $collectionRepository, iterable $collectionExtensions, TokenStorageInterface $tokenStorage)
    {
        $this->collectionRepository = $collectionRepository;
        $this->collectionExtensions = $collectionExtensions;
        $this->tokenStorage = $tokenStorage;
    }


    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Collection::class === $resourceClass;
    }

    /**
     * Retrieves a collection.
     *
     * @throws ResourceClassNotSupportedException
     *
     * @return array|\Traversable
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $queryBuilder = $this->collectionRepository->createQueryBuilder('c')
            ->where('c.isPublic = true');

        if ($user instanceof User) {
            $queryBuilder->orWhere('c.user = :user AND c.isPublic = false')
            ->setParameter('user', $user->getId());
        }

        $queryNameGenerator = new QueryNameGenerator();
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

}