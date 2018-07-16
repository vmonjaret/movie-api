<?php

namespace App\Controller\Notification;

use App\Entity\Notification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MarkAsSeen
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
    public function __invoke(Request $request)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $notification = $request->get('id');

        $manager = $this->container->get('mgilet.notification');
        $manager->markAsSeen(
            $manager->getNotifiableInterface($manager->getNotifiableEntityById($user)),
            $manager->getNotification($notification),
            true
        );

        return new JsonResponse(true);
    }
}