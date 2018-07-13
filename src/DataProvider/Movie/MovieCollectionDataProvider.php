<?php

namespace App\DataProvider\Movie;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Utils\MovieHydratation;

class MovieCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $movieRepository;
    private $collectionExtensions;
    private $movieHydration;

    public function __construct(MovieRepository $movieRepository, iterable $collectionExtensions, MovieHydratation $movieHydratation)
    {
        $this->movieRepository = $movieRepository;
        $this->collectionExtensions = $collectionExtensions;
        $this->movieHydration = $movieHydratation;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Movie::class === $resourceClass;
    }

    /**
     * Retrieves a collection.
     *
     * @return array|\Traversable
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $movies = null;

        $queryBuilder = $this->movieRepository->createQueryBuilder('m');
        if ("get_populars" === $operationName) {
            $queryBuilder->orderBy('m.popularity', 'DESC');
        } elseif ("get_recents" === $operationName) {
            $queryBuilder->orderBy('m.releasedAt', 'DESC')
                ->where('m.releasedAt <= :now')
                ->setParameter('now', new \DateTime());
        }

        $queryNameGenerator = new QueryNameGenerator();
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                $movies = $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        if(null === $movies) {
            $movies = $queryBuilder->getQuery()->getResult();
        }

        $this->movieHydration->hydrateMovieWithUser($movies);

        return $movies;
    }
}