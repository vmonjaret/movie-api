<?php

namespace App\Controller\Notation;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Feed;
use App\Entity\Notation;
use App\Manager\AchievementManager;
use App\Repository\NotationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

class AddNotation extends Controller
{
    private $notationRepository;
    private $tokenStorage;
    private $entityManager;
    private $achievementManager;
    private $validator;

    /**
     * Like constructor.
     * @param NotationRepository $notationRepository
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $entityManager
     * @param AchievementManager $achievementManager
     */
    public function __construct(NotationRepository $notationRepository, TokenStorageInterface $tokenStorage,
                                EntityManagerInterface $entityManager, AchievementManager $achievementManager,
                                ValidatorInterface $validator)
    {
        $this->notationRepository = $notationRepository;
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        $this->achievementManager = $achievementManager;
        $this->validator = $validator;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @param Notation $data
     * @return JsonResponse
     */
    public function __invoke(Notation $data)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $data->setUser($user);

        $em = $this->getDoctrine()->getManager();
        $notation = $em->getRepository(Notation::class)->findOneBy(['movie' => $data->getMovie()->getId(), 'user' => $user->getId()]);

        $errors = $this->validator->validate($data);

        if ($notation) {
            $notation->setMark($data->getMark());
        } else {
            $this->entityManager->persist($data);

            $feed = new Feed();
            $feed->setUser($user)
                ->setType(Feed::TYPE_NOTATION)
                ->setNotation($data)
                ->setMovie($data->getMovie())
            ;
            $this->entityManager->persist($feed);
        }

        $this->achievementManager->movieNotationAchievement($user);

        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}