<?php

namespace App\Manager;

use App\Entity\Achievement;
use App\Entity\User;
use App\Repository\AchievementRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

class AchievementManager
{
    private $commentRepository;
    private $achievementRepository;
    private $entityManager;

    /**
     * AchievementManager constructor.
     * @param CommentRepository $commentRepository
     * @param AchievementRepository $achievementRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(CommentRepository $commentRepository, AchievementRepository $achievementRepository, EntityManagerInterface $entityManager)
    {
        $this->commentRepository = $commentRepository;
        $this->achievementRepository = $achievementRepository;
        $this->entityManager = $entityManager;
    }

    public function commentsAchievement(User $user)
    {
        $count = $this->commentRepository->count(array('user' => $user));

        // Achievement "auteur" : Write one comment
        if ($count >= 1) {
            $badge = $this->achievementRepository->getUserAchievement($user, Achievement::AUTHOR);
            if (null === $badge) {
                $badge = $this->achievementRepository->findOneBy(array('type' => Achievement::AUTHOR));
                $user->addAchievement($badge);

                $this->entityManager->flush();
            }
        }

        if ($count >= 100) {
            $badge = $this->achievementRepository->getUserAchievement($user, Achievement::WRITER);
            if (null === $badge) {
                $badge = $this->achievementRepository->findOneBy(array('type' => Achievement::WRITER));
                $user->addAchievement($badge);

                $this->entityManager->flush();
            }
        }
    }
}