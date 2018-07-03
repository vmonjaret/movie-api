<?php

namespace App\Manager;

use App\Entity\Actor;
use Doctrine\ORM\EntityManagerInterface;
use Tmdb\ApiToken;
use Tmdb\Client;
use Tmdb\Model\Person;
use Tmdb\Repository\PeopleRepository;

class ActorManager
{
    private $entityManager;
    private $actorRepository;

    /**
     * ActorManager constructor.
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $token = new ApiToken(getenv('TMDBD_API_KEY'));
        $client = new Client($token);
        $this->actorRepository = new PeopleRepository($client);
    }

    public function createFromModel(Person\CastMember $person): Actor
    {
        $actor = new Actor();

        $actor->setId($person->getId())
            ->setName($person->getName())
            ->setProfile($person->getProfilePath());

        $this->entityManager->persist($actor);
        $this->entityManager->flush();

        return $actor;
    }

    public function getActor(Person\CastMember $person): Actor
    {
        $actor = $this->entityManager->getRepository(Actor::class)->find($person->getId());

        if (null === $actor) {
            $actor = $this->createFromModel($person);
        }

        return $actor;
    }

}