<?php

namespace App\DataProvider\Movie;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use ApiPlatform\Core\Exception\RuntimeException;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MovieCollectionDataProvider implements CollectionDataProviderInterface
{
    private $managerRegistry;
    private $collectionExtensions;
    private $tokenStorage;

    public function __construct(ManagerRegistry $managerRegistry, iterable $collectionExtensions, TokenStorageInterface $tokenStorage)
    {
        $this->managerRegistry = $managerRegistry;
        $this->collectionExtensions = $collectionExtensions;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Retrieves a collection.
     *
     * @throws ResourceClassNotSupportedException
     *
     * @return array|\Traversable
     */
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $manager = $this->managerRegistry->getManagerForClass($resourceClass);

        $repository = $manager->getRepository($resourceClass);
        if (!method_exists($repository, 'createQueryBuilder')) {
            throw new RuntimeException('The repository class must have a "createQueryBuilder" method.');
        }

        $movies = null;

        /**
         * @var QueryBuilder $queryBuilder;
         */
        $queryBuilder = $repository->createQueryBuilder('m');
        if ("get_populars" === $operationName) {
            $queryBuilder->orderBy('m.popularity', 'DESC');
        } elseif ("get_recents" === $operationName) {
            $queryBuilder->orderBy('m.releasedAt', 'DESC')
                ->where('m.releasedAt <= :now')
                ->setParameter('now', new \DateTime());
        }

        $queryNameGenerator = new QueryNameGenerator();
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName);
            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName)) {
                $movies = $extension->getResult($queryBuilder, $resourceClass, $operationName);
            }
        }

        if (null !== $movies) {
            $this->hydrateWithUser($movies);
            return $movies;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function hydrateWithUser($movies)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User) {
            foreach ($movies as $movie) {
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
}