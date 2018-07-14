<?php

namespace App\DataProvider\Feed;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Feed;
use App\Repository\FeedRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FeedCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $tokenStorage;
    private $feedRepository;
    private $collectionExtensions;

    /**
     * FeedCollectionDataProvider constructor.
     * @param $tokenStorage
     * @param $feedRepository
     * @param $collectionExtensions
     */
    public function __construct(TokenStorageInterface $tokenStorage, FeedRepository $feedRepository, iterable $collectionExtensions)
    {
        $this->tokenStorage = $tokenStorage;
        $this->feedRepository = $feedRepository;
        $this->collectionExtensions = $collectionExtensions;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Feed::class === $resourceClass;
    }

    /**
     * Retrieves a collection.
     *
     * @throws ResourceClassNotSupportedException
     *
     * @return array|\Traversable
     */
    public function getCollection(string $resourceClass, string $operationName = null, $context = [])
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $ids = array();
        foreach ($user->getFollows() as $follow) {
            $ids[] = $follow->getId();
        }

        $queryBuilder = $this->feedRepository->createQueryBuilder('f')
            ->where('f.user IN (:follows)')
            ->setParameter('follows', $ids)
            ->orderBy('f.createdAt', 'DESC')
        ;

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