<?php

namespace App\Controller\Collection;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Collection;
use App\Entity\CollectionItem;
use App\Entity\Movie;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AddMovie
{
    private $tokenStorage;
    private $em;

    /**
     * ListMovies constructor.
     * @param $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function __invoke(CollectionItem $data)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        /**
         * @var Collection $collection
         */
        $collection = $data->getCollection();
        if ($user !== $collection->getUser()) {
            return new Response(null, 403);
        }

        if ($collection->getMovies()->contains($data->getMovie())) {
            $collection->removeMovie($data->getMovie());
        } else {
            $collection->addMovie($data->getMovie());
        }

        $this->em->flush();

        return new Response(null, 204);
    }

}