<?php

namespace App\Controller\Security;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Register
{
    private $passwordEncoder;
    private $validator;
    private $em;
    private $jwtManager;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em,
                                JWTTokenManagerInterface $jwtManager, ValidatorInterface $validator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->jwtManager = $jwtManager;
        $this->validator = $validator;
    }

    public function __invoke(User $data)
    {
        $password = $this->passwordEncoder->encodePassword($data, $data->getPlainPassword());
        $data->setPassword($password);

        $errors = $this->validator->validate($data);

        $this->em->persist($data);
        $this->em->flush();

        $token = $this->jwtManager->create($data);

        return array('token' => $token);
    }
}