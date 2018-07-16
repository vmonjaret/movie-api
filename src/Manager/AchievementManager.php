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
    private $achievementRepostory;
    private $entityManager;

    /**
     * AchievementManager constructor.
     * @param $commentRepository
     */
    public function __construct(CommentRepository $commentRepository, AchievementRepository $achievementRepository, EntityManagerInterface $entityManager)
    {
        $this->commentRepository = $commentRepository;
        $this->achievementRepostory = $achievementRepository;
        $this->entityManager = $entityManager;
    }

    public function commentsAchievement(User $user)
    {
        $count = $this->commentRepository->count(array('user' => $user));

        // Achievemnt "auteur" : Write one comment
        if ($count >= 1) {
            $badge = $this->achievementRepostory->getUserAchievement($user, Achievement::AUTHOR);
            if (null === $badge) {
                $badge = $this->achievementRepostory->findOneBy(array('type' => Achievement::AUTHOR));
                $user->addAchievement($badge);

                $this->entityManager->flush();
            }
        }
    }
}