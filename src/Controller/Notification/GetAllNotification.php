<?php

namespace App\Controller\Notification;


use Doctrine\ORM\EntityManagerInterface;
use Mgilet\NotificationBundle\Entity\NotifiableNotification;
use Mgilet\NotificationBundle\Entity\Repository\NotifiableNotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetAllNotification
{
    private $entityManager;
    private $tokenStorage;

    /**
     * GetAllNotification constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function __invoke()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return $this->entityManager->getRepository(NotifiableNotification::class)->findAllForNotifiableId($user);
    }
}