<?php

namespace App\Controller\Feed;

use App\Repository\FeedRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CountFeed
{
    private $feedRepository;
    private $tokenStorage;

    /**
     * Feed constructor.
     * @param $feedRepository
     * @param $tokenStorage
     */
    public function __construct(FeedRepository $feedRepository, TokenStorageInterface $tokenStorage)
    {
        $this->feedRepository = $feedRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function __invoke()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $count = 0;
        foreach ($user->getFollows() as $follow) {
            dump($follow);
            $count += $this->feedRepository->count(array('user' => $follow->getId()));
        }

        return $count;
    }
}