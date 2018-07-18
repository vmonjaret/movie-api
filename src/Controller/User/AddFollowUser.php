<?php

namespace App\Controller\User;

use App\Entity\Feed;
use App\Entity\Follow;
use App\Utils\NotificationCenter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Response;

class AddFollowUser
{

    private $tokenStorage;
    private $em;
    private $notificationCenter;

    /**
     * AddFollowUser constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $em
     * @param NotificationCenter $notificationCenter
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em, NotificationCenter $notificationCenter)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->notificationCenter = $notificationCenter;
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

            $this->notificationCenter->sendNotification($follow, "Follow", "${follower} vous suit", "/profile/${followerId}");
        }

        $this->em->flush();

        return new Response(null, 204);
    }
}