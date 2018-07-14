<?php

namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Collection;
use App\Entity\Comment;
use App\Entity\Feed;
use App\Entity\Follow;
use App\Entity\Notation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FeedSubscriber implements EventSubscriberInterface
{
    private $entityManager;

    /**
     * FeedSubscriber constructor.
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array(
                'createFeed', EventPriorities::POST_WRITE,
                )
        );
    }

    public function createFeed(GetResponseForControllerResultEvent $event)
    {
        $object = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $operationPath = $event->getRequest()->getRequestUri();

        $feed = null;

        if ($object instanceof Comment && $method === Request::METHOD_POST) {
            $feed = new Feed();
            $feed->setUser($object->getUser())
                ->setType(Feed::TYPE_COMMENT)
                ->setComment($object)
                ->setMovie($object->getMovie())
            ;
        } elseif ($object instanceof Collection && $method === Request::METHOD_POST && $object->getIsPublic()) {
            $feed = new Feed();
            $feed->setUser($object->getUser())
                ->setType(Feed::TYPE_COLLECTION)
                ->setCollection($object)
            ;
        }

        if (null !== $feed) {
            $this->entityManager->persist($feed);
            $this->entityManager->flush();
        }
    }
}