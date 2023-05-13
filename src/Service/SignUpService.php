<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\UserAlreadyExistException;
use App\Model\IdResponse;
use App\Model\SignUpRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function signUp(SignUpRequest $signUpRequest): IdResponse
    {
        if ($this->userRepository->existByEmail($signUpRequest->getEmail())) {
            throw new UserAlreadyExistException();
        }

        $user = (new User())
            ->setEmail($signUpRequest->getEmail())
            ->setFirstName($signUpRequest->getFirstName())
            ->setLastName($signUpRequest->getLastName());

        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                $signUpRequest->getPassword()
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new IdResponse($user->getId());
    }
}
