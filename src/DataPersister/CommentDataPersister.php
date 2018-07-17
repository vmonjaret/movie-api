<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentDataPersister implements DataPersisterInterface
{
    private $tokenStorage;

    /**
     * CollectionDataPersister constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface$tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }


    /**
     * Is the data supported by the persister?
     *
     * @param mixed $data
     *
     * @return bool
     */
    public function supports($data): bool
    {
        return $data instanceof Comment;
    }

    /**
     * Persists the data.
     *
     * @param Comment $data
     *
     * @return object|void Void will not be supported in API Platform 3, an object should always be returned
     */
    public function persist($data)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $data->setUser($user);

        return $data;
    }

    /**
     * Removes the data.
     *
     * @param mixed $data
     */
    public function remove($data)
    {
        // TODO: Implement remove() method.
    }
}