<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Register
{
    private $passwordEncoder;
    private $em;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
    }

    public function __invoke(User $data)
    {
        $password = $this->passwordEncoder->encodePassword($data, $data->getPlainPassword());
        $data->setPassword($password);

        dump($data);

        return $data;
//        $this->em->persist($data);
//        $this->em->flush();
    }
}