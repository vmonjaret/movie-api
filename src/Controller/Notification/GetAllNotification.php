<?php

namespace App\Controller\Notification;


use Doctrine\ORM\EntityManagerInterface;
use Mgilet\NotificationBundle\Entity\NotifiableNotification;
use Mgilet\NotificationBundle\Entity\Repository\NotifiableNotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetAllNotification
{
    private $container;
    private $tokenStorage;

    /**
     * MarkAsSeen constructor.
     * @param $container
     * @param $tokenStorage
     */
    public function __construct(ContainerInterface $container, TokenStorageInterface $tokenStorage)
    {
        $this->container = $container;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function __invoke()
    {

        $user = $this->tokenStorage->getToken()->getUser();

        $manager = $this->container->get('mgilet.notification');
        return $manager->getNotifications($user);
    }
}