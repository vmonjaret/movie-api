<?php

namespace App\Utils;

use App\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NotificationCenter
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function sendNotification(User $user, string $subject, string $content, string $link)
    {
        $notificationManager = $this->container->get('mgilet.notification');
        $notif = $notificationManager->createNotification($subject);
        $notif->setMessage($content)
            ->setLink($link);

        $notificationManager->addNotification(array($user), $notif, true);
    }
}