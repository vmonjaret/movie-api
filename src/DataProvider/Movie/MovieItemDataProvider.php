<?php

namespace App\DataProvider\Movie;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MovieItemDataProvider implements ItemDataProviderInterface
{
    private $itemExtensions;
    private $managerRegistry;
    private $tokenStorage;

    public function __construct(ManagerRegistry $managerRegistry, iterable $itemExtensions, TokenStorageInterface $tokenStorage)
    {
        $this->managerRegistry = $managerRegistry;
        $this->itemExtensions = $itemExtensions;
        $this->tokenStorage = $tokenStorage;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $manager = $this->managerRegistry->getManagerForClass($resourceClass);
        $repository = $manager->getRepository($resourceClass);
        $queryBuilder = $repository->createQueryBuilder('o');
        $queryNameGenerator = new QueryNameGenerator();
        $identifiers = ['id' => $id];

        $movie = null;

        foreach ($this->itemExtensions as $extension) {
            $extension->applyToItem($queryBuilder, $queryNameGenerator, $resourceClass, $identifiers, $operationName, $context);
            if ($extension instanceof QueryResultItemExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                dump('toto');
                $movie = $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }
        if (null === $movie) {
            $queryBuilder->where('o.id = :id')->setParameter('id', $id);
            $movie = $queryBuilder->getQuery()->getOneOrNullResult();
        }

        $this->hydrateWithUser($movie);

        return $movie;

    }


    public function hydrateWithUser($movie)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User) {
            if ($user->getMoviesLiked()->contains($movie)) {
                $movie->liked = true;
            }
            if ($user->getMoviesWatched()->contains($movie)) {
                $movie->watched = true;
            }
            if ($user->getMoviesWished()->contains($movie)) {
                $movie->wished = true;
            }
        }
    }
}