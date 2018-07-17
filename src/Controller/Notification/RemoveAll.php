<?php

namespace App\Controller\Notification;


use Doctrine\ORM\EntityManagerInterface;
use Mgilet\NotificationBundle\Entity\NotifiableNotification;
use Mgilet\NotificationBundle\Entity\Repository\NotifiableNotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RemoveAll
{
    private $entityManager;
    private $tokenStorage;
    private $container;

    /**
     * GetAllNotification constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->container = $container;
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function __invoke()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $manager = $this->container->get('mgilet.notification');
        $notifications = $manager->getNotifications($user);
        foreach ($notifications as $notification) {
            $this->entityManager->remove($notification);
        }

        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}