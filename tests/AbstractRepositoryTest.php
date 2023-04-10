<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractRepositoryTest extends KernelTestCase
{
    protected ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function getRepositoryForEntity(string $entityClass): mixed
    {
        return $this->entityManager->getRepository($entityClass);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}