<?php

namespace App\DataProvider\Movie;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Utils\MovieHydratation;
use Doctrine\Common\Persistence\ManagerRegistry;

class MovieItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $itemExtensions;
    private $movieRepository;
    private $movieHydratation;

    public function __construct(MovieRepository $movieRepository, iterable $itemExtensions, MovieHydratation $movieHydratation)
    {
        $this->movieRepository = $movieRepository;
        $this->itemExtensions = $itemExtensions;
        $this->movieHydratation = $movieHydratation;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Movie::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $queryBuilder = $this->movieRepository->createQueryBuilder('o');
        $queryNameGenerator = new QueryNameGenerator();
        $identifiers = ['id' => $id];

        $movie = null;

        foreach ($this->itemExtensions as $extension) {
            $extension->applyToItem($queryBuilder, $queryNameGenerator, $resourceClass, $identifiers, $operationName, $context);
            if ($extension instanceof QueryResultItemExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                $movie = $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }
        if (null === $movie) {
            $queryBuilder->where('o.id = :id')->setParameter('id', $id);
            $movie = $queryBuilder->getQuery()->getOneOrNullResult();
        }

        $this->movieHydratation->hydrateMovieWithUser($movie);

        return $movie;

    }
}