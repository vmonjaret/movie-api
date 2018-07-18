<?php

namespace App\Manager;

use App\Entity\Achievement;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\AchievementRepository;
use App\Repository\CommentRepository;
use App\Repository\NotationRepository;
use App\Utils\NotificationCenter;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class AchievementManager
{
    private $commentRepository;
    private $achievementRepository;
    private $entityManager;
    private $notificationCenter;
    private $notationRepository;

    /**
     * AchievementManager constructor.
     * @param CommentRepository $commentRepository
     * @param AchievementRepository $achievementRepository
     * @param EntityManagerInterface $entityManager
     * @param NotificationCenter $notificationCenter
     * @param NotationRepository $notationRepository
     */
    public function __construct(CommentRepository $commentRepository, AchievementRepository $achievementRepository,
                                EntityManagerInterface $entityManager, NotificationCenter $notificationCenter, NotationRepository $notationRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->achievementRepository = $achievementRepository;
        $this->entityManager = $entityManager;
        $this->notificationCenter = $notificationCenter;
        $this->notationRepository = $notationRepository;
    }

    public function commentsAchievement(User $user, Comment $comment)
    {
        $count = $this->commentRepository->count(array('user' => $user));

        // Achievement "auteur" : Write one comment
        if ($count >= 1) {
            $badge = $this->achievementRepository->getUserAchievement($user, Achievement::AUTHOR);
            if (null === $badge) {
                $badge = $this->achievementRepository->findOneBy(array('type' => Achievement::AUTHOR));
                $badgeName = $badge->getName();
                $user->addAchievement($badge);

                $this->notificationCenter->sendNotification($user, "Achievement", "Vous avez gagné le badge ${badgeName}", "/profile");

                $this->entityManager->flush();
            }
        }

        // Achievement "writer" : Write a comment more than 100 character
        if (strlen($comment->getContent()) > 100) {
            $badge = $this->achievementRepository->getUserAchievement($user, Achievement::WRITER);
            if (null === $badge) {
                $badge = $this->achievementRepository->findOneBy(array('type' => Achievement::WRITER));
                $badgeName = $badge->getName();
                $user->addAchievement($badge);

                $this->notificationCenter->sendNotification($user, "Achievement", "Vous avez gagné le badge ${badgeName}", "/profile");

                $this->entityManager->flush();
            }
        }
    }

    public function movieWatchAchievement(User $user)
    {
        $count = count($user->getMoviesWatched());

        // Achievement "noob" : Watch 1 movie
        if ($count >= 1) {
            $badge = $this->achievementRepository->getUserAchievement($user, Achievement::NOOB);
            if (null === $badge) {
                $badge = $this->achievementRepository->findOneBy(array('type' => Achievement::NOOB));
                $badgeName = $badge->getName();
                $user->addAchievement($badge);

                $this->notificationCenter->sendNotification($user, "Achievement", "Vous avez gagné le badge ${badgeName}", "/profile");

                $this->entityManager->flush();
            }
        }

        // Achievement "padawan" : Watch 50 movies
        if ($count >= 50) {
            $badge = $this->achievementRepository->getUserAchievement($user, Achievement::PADAWAN);
            if (null === $badge) {
                $badge = $this->achievementRepository->findOneBy(array('type' => Achievement::PADAWAN));
                $badgeName = $badge->getName();
                $user->addAchievement($badge);

                $this->notificationCenter->sendNotification($user, "Achievement", "Vous avez gagné le badge ${badgeName}", "/profile");

                $this->entityManager->flush();
            }
        }

        // Achievement "jedi master" : Watch 100 movies
        if ($count >= 100) {
            $badge = $this->achievementRepository->getUserAchievement($user, Achievement::JEDI_MASTER);
            if (null === $badge) {
                $badge = $this->achievementRepository->findOneBy(array('type' => Achievement::JEDI_MASTER));
                $badgeName = $badge->getName();
                $user->addAchievement($badge);


                $this->notificationCenter->sendNotification($user, "Achievement", "Vous avez gagné le badge ${badgeName}", "/profile");

                $this->entityManager->flush();
            }
        }
    }

    public function movieNotationAchievement(User $user)
    {
        $count = $this->commentRepository->count(array('user' => $user));

        // Achievement "judge" : Rate a movie
        if ($count >= 1) {
            $badge = $this->achievementRepository->getUserAchievement($user, Achievement::JUDGE);
            if (null === $badge) {
                $badge = $this->achievementRepository->findOneBy(array('type' => Achievement::JUDGE));
                $badgeName = $badge->getName();
                $user->addAchievement($badge);

                $this->notificationCenter->sendNotification($user, "Achievement", "Vous avez gagné le badge ${badgeName}", "/profile");

                $this->entityManager->flush();
            }
        }
    }

    public function movieRandomAchievement(User $user)
    {
        $badge = $this->achievementRepository->getUserAchievement($user, Achievement::UNDECIDED);
        if (null === $badge) {
            $badge = $this->achievementRepository->findOneBy(array('type' => Achievement::UNDECIDED));
            $badgeName = $badge->getName();
            $user->addAchievement($badge);

            $this->notificationCenter->sendNotification($user, "Achievement", "Vous avez gagné le badge ${badgeName}", "/profile");

            $this->entityManager->flush();
        }
    }
}