<?php

namespace App\Tests\Service;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistException;
use App\Model\SubscriberRequest;
use App\Repository\SubscriberRepository;
use App\Service\SubscriberService;
use App\Tests\AbstractTestCase;
use Doctrine\ORM\EntityManagerInterface;

class SubscriberServiceTest extends AbstractTestCase
{
    private SubscriberRepository $repository;

    private EntityManagerInterface $entityManager;

    public const EMAIL = 'test@test.com1';

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(SubscriberRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testSubscribeAlreadyExist(): void
    {
        $this->expectException(SubscriberAlreadyExistException::class);

        $this->repository->expects($this->once())
            ->method('existByEmail')
            ->with(self::EMAIL)
            ->willReturn(true);

        $request = new SubscriberRequest();
        $request->setEmail(self::EMAIL);

        (new SubscriberService($this->repository, $this->entityManager))->subscribe($request);
    }

    public function testSubscribe(): void
    {
        $this->repository->expects($this->once())
            ->method('existByEmail')
            ->with(self::EMAIL)
            ->willReturn(false);

        $expectedSubscriber = new Subscriber();
        $expectedSubscriber->setEmail(self::EMAIL);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($expectedSubscriber);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $request = new SubscriberRequest();
        $request->setEmail(self::EMAIL);

        (new SubscriberService($this->repository, $this->entityManager))->subscribe($request);
    }
}
