<?php

namespace App\DataProvider\User;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserCollectionDataProvider implements RestrictedDataProviderInterface, CollectionDataProviderInterface
{
    private $userRepository;
    private $tokenStorage;
    private $collectionExtensions;

    /**
     * UserCollectionDataProvider constructor.
     * @param $userRepository
     * @param $tokenStorage
     * @param $collectionExtensions
     */
    public function __construct(UserRepository $userRepository, TokenStorageInterface $tokenStorage, iterable $collectionExtensions)
    {
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
        $this->collectionExtensions = $collectionExtensions;
    }


    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return User::class === $resourceClass;
    }

    /**
     * Retrieves a collection.
     *
     * @throws ResourceClassNotSupportedException
     *
     * @return array|\Traversable
     */
    public function getCollection(string $resourceClass, string $operationName = null, $context =[])
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $queryBuilder = $this->userRepository->createQueryBuilder('u');

        /*if ($user instanceof User) {
            $queryBuilder->where('u.id != :id')
                ->setParameter('id', $user->getId());
        }*/

        $queryNameGenerator = new QueryNameGenerator();
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                $users = $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        if ($user instanceof User) {
            foreach ($users as $current) {
                if ($user->getFollows()->contains($current)) {
                    $current->isFollow = true;
                }
            }

            return $users;
        }

        return $queryBuilder->getQuery()->getResult();
    }
}