<?php

namespace App\Tests\Repository;

use App\Entity\BookCategory;
use App\Repository\BookCategoryRepository;
use App\Tests\AbstractRepositoryTest;

class BookCategoryRepositoryTest extends AbstractRepositoryTest
{
    private BookCategoryRepository $bookCategoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookCategoryRepository = $this->getRepositoryForEntity(BookCategory::class);
    }

    public function testFindAllSortedByTitle()
    {
        $android = (new BookCategory())->setTitle('Android')->setSlug('android');
        $computer = (new BookCategory())->setTitle('Computer')->setSlug('computer');
        $devices = (new BookCategory())->setTitle('Devices')->setSlug('devices');

        foreach ([$android, $computer, $devices] as $item) {
            $this->entityManager->persist($item);
        }

        $this->entityManager->flush();

        $titles = array_map(
            fn (BookCategory $bookCategory) => $bookCategory->getTitle(),
            $this->bookCategoryRepository->findAllSortedByTitle()
        );

        $this->assertEquals(['Android', 'Computer', 'Devices'], $titles);
    }
}
