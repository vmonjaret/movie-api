<?php

namespace App\Controller\Comment;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CreateComment {

    private $commentRepository;
    private $tokenStorage;
    private $entityManager;

    /**
     * CreateComment constructor.
     * @param \App\Repository\CommentRepository $commentRepository
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(CommentRepository $commentRepository, TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->commentRepository = $commentRepository;
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Comment $data
     * @return Comment
     */
    public function __invoke(Comment $data)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $data->setUser($user);
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}