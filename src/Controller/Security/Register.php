<?php

namespace App\Controller\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Register
{
    private $passwordEncoder;
    private $em;
    private $jwtManager;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em,
                                JWTTokenManagerInterface $jwtManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(User $data)
    {
        $password = $this->passwordEncoder->encodePassword($data, $data->getPlainPassword());
        $data->setPassword($password);

        $this->em->persist($data);
        $this->em->flush();

        $token = $this->jwtManager->create($data);

        return array('token' => $token);
    }
}