<?php

namespace App\Controller\Comment;


use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostComment
{
    /**
     * Like constructor.
     * @param NotationRepository $notationRepository
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $entityManager
     * @param AchievementManager $achievementManager
     */
    public function __construct(TokenStorageInterface $tokenStorage,
                                EntityManagerInterface $entityManager,
                                ValidatorInterface $validator)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @param Comment $data
     * @return JsonResponse
     */
    public function __invoke(Comment $data)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $data->setUser($user);

        $errors = $this->validator->validate($data);

        return $data;
    }
}