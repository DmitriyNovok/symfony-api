<?php

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Repository\BookRepository;
use App\Tests\AbstractRepositoryTest;
use Doctrine\Common\Collections\ArrayCollection;

class BookRepositoryTest extends AbstractRepositoryTest
{
    private BookRepository $bookRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->getRepositoryForEntity(Book::class);
    }

    public function testFindBooksByCategoryId()
    {
        $devicesCategory = (new BookCategory())->setTitle('Devices')->setSlug('devices');
        $this->entityManager->persist($devicesCategory);

        for ($i = 0; $i < 5; ++$i) {
            $book = $this->createBook('device-'.$i, $devicesCategory);
            $this->entityManager->persist($book);
        }

        $this->entityManager->flush();

        $this->assertCount(
            5,
            $this->bookRepository->findBooksByCategoryId($devicesCategory->getId())
        );
    }

    private function createBook(string $title, BookCategory $bookCategory): Book
    {
        return (new Book())
            ->setPublicationDate(new \DateTime())
            ->setSlug($title)
            ->setTitle($title)
            ->setAuthors(['authors'])
            ->setMeap(false)
            ->setImage('/storage/'.$title)
            ->setCategories(new ArrayCollection([$bookCategory]));
    }
}
