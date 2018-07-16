<?php

namespace App\Controller\User;

use App\Entity\Feed;
use App\Entity\Follow;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Mgilet\NotificationBundle\Manager\NotificationManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;

class AddFollowUser
{

    private $tokenStorage;
    private $em;
    private $container;

    /**
     * AddFollowUser constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $em
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function __invoke(Follow $data)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $follow = $data->getFollow();

        if ($user->getFollows()->contains($follow)) {
            $user->removeFollow($follow);
        } else {
            $user->addFollow($follow);
            $follower = $user->getUsername();
            $followerId = $user->getId();
            $notificationManager = $this->container->get('mgilet.notification');
            $notif = $notificationManager->createNotification('Follow');
            $notif->setMessage("${follower} vous suit")
                ->setLink("/profile/${followerId}");

            $notificationManager->addNotification(array($follow), $notif, true);

            $feed = $this->em->getRepository(Feed::class)->findOneBy(array(
                'user' => $user->getId(),
                'friend' => $follow->getId(),
                'type' => Feed::FOLLOW
            ));
            if (null === $feed) {
                $feed = new Feed();
                $feed->setType(Feed::FOLLOW)
                    ->setUser($user)
                    ->setFriend($follow);

                $this->em->persist($feed);
            }
        }

        $this->em->flush();

        return new Response(null, 204);
    }
}