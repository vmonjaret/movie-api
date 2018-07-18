<?php

namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Comment;
use App\Entity\Movie;
use App\Entity\User;
use App\Manager\AchievementManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AchievementSubscriber implements EventSubscriberInterface
{
    private $achievementManager;
    private $tokenStorage;

    /**
     * AchievementSubscriber constructor.
     * @param $achievementManager
     */
    public function __construct(AchievementManager $achievementManager, TokenStorageInterface $tokenStorage)
    {
        $this->achievementManager = $achievementManager;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array(
                'affectAchievement', EventPriorities::POST_WRITE,
            )
        );
    }

    public function affectAchievement(GetResponseForControllerResultEvent $event)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $object = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof User) {
            return;
        }

        if ($object instanceof Comment && Request::METHOD_POST === $method) {
            $this->achievementManager->commentsAchievement($user, $object);
        }
    }
}