<?php

namespace App\Service;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistException;
use App\Model\SubscriberRequest;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriberService
{
    public function __construct(private SubscriberRepository $subscriberRepository, private EntityManagerInterface $entityManager)
    {
    }

    public function subscribe(SubscriberRequest $subscriberRequest): void
    {
        if ($this->subscriberRepository->existByEmail($subscriberRequest->getEmail())) {
            throw new SubscriberAlreadyExistException();
        }

        $subscriber = new Subscriber();
        $subscriber->setEmail($subscriberRequest->getEmail());

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();
    }
}
