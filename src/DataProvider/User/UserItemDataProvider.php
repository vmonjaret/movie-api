<?php

namespace App\DataProvider\User;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Movie;
use App\Entity\User;
use App\Repository\MovieRepository;
use App\Repository\UserRepository;
use App\Utils\MovieHydratation;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $itemExtensions;
    private $userRepository;
    private $tokenStorage;

    public function __construct(UserRepository $userRepository, iterable $itemExtensions, TokenStorageInterface $tokenStorage)
    {
        $this->userRepository = $userRepository;
        $this->itemExtensions = $itemExtensions;
        $this->tokenStorage = $tokenStorage;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return User::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $authenticatedUser = $this->tokenStorage->getToken()->getUser();
        $queryBuilder = $this->userRepository->createQueryBuilder('o');
        $queryNameGenerator = new QueryNameGenerator();
        $identifiers = ['id' => $id];
        $user = null;

        if ($authenticatedUser instanceof User) {
            $queryBuilder->where('o.id != :me')
                ->setParameter('me', $authenticatedUser->getId());
        }

        foreach ($this->itemExtensions as $extension) {
            $extension->applyToItem($queryBuilder, $queryNameGenerator, $resourceClass, $identifiers, $operationName, $context);
            if ($extension instanceof QueryResultItemExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                $user = $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }
        if (null === $user) {
            $queryBuilder->andWhere('o.id = :id')->setParameter('id', $id);
            $user = $queryBuilder->getQuery()->getOneOrNullResult();
        }

        if ($authenticatedUser instanceof User) {
            if ($authenticatedUser->getFollows()->contains($user)) {
                $user->isFollow = true;
            }
        }

        return $user;
    }
}