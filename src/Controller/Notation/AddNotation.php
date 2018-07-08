<?php

namespace App\Controller\Notation;

use App\Entity\Notation;
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

    /**
     * Like constructor.
     * @param NotationRepository $notationRepository
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(NotationRepository $notationRepository, TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->notationRepository = $notationRepository;
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
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

        if ($notation) {
            $notation->setMark($data->getMark());
        } else {
            $this->entityManager->persist($data);
        }

        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}