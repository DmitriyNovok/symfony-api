<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use App\Service\BookService;
use App\Tests\AbstractTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class BookServiceTest extends AbstractTestCase
{
    public function testGetBookByCategoryNotFound(): void
    {
        $reviewsRepository = $this->createMock(ReviewRepository::class);
        $bookRepository = $this->createMock(BookRepository::class);
        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);

        $bookCategoryRepository->expects($this->once())
            ->method('existById')
            ->with(130)
            ->willReturn(false);

        $this->expectException(BookCategoryNotFoundException::class);

        (new BookService($bookRepository, $bookCategoryRepository, $reviewsRepository))->getBookByCategory(130);
    }

    public function testGetBookByCategory(): void
    {
        $reviewsRepository = $this->createMock(ReviewRepository::class);
        $bookRepository = $this->createMock(BookRepository::class);
        $bookRepository->expects($this->once())
            ->method('findBooksByCategoryId')
            ->with(130)
            ->willReturn([
                $this->createBookEntity(),
            ]);

        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);

        $bookCategoryRepository->expects($this->once())
            ->method('existById')
            ->with(130)
            ->willReturn(true);

        $service = new BookService($bookRepository, $bookCategoryRepository, $reviewsRepository);
        $expected = new BookListResponse([
            $this->createBookItemModel(),
        ]);

        $this->assertEquals($expected, $service->getBookByCategory(130));
    }

    private function createBookEntity()
    {
        $book = (new Book())
            ->setTitle('Test book')
            ->setSlug('test-book')
            ->setCategories(new ArrayCollection())
            ->setImage('/storage/images/324c0vdsjffs')
            ->setIsbn(3232323232)
            ->setDescription('description')
            ->setMeap(false)
            ->setPublicationDate(new \DateTimeImmutable('2010-10-10'))
            ->setAuthors(['Tester']);

        $this->setEntityId($book, 123);

        return $book;
    }

    private function createBookItemModel(): BookListItem
    {
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setImage('/storage/images/324c0vdsjffs')
            ->setPublicationDate(1286668800);
    }
}
