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
use App\Service\RatingService;
use App\Tests\AbstractTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class BookServiceTest extends AbstractTestCase
{
    private BookRepository $bookRepository;

    private ReviewRepository $reviewsRepository;

    private BookCategoryRepository $bookCategoryRepository;

    private RatingService $ratingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewsRepository = $this->createMock(ReviewRepository::class);
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $this->ratingService = $this->createMock(RatingService::class);
    }

    public function testGetBookByCategoryNotFound(): void
    {
        $this->bookCategoryRepository->expects($this->once())
            ->method('existById')
            ->with(130)
            ->willReturn(false);

        $this->expectException(BookCategoryNotFoundException::class);

        $this->createBookService()->getBookByCategory(130);
    }

    public function testGetBookByCategory(): void
    {
        $this->bookRepository->expects($this->once())
            ->method('findBooksByCategoryId')
            ->with(130)
            ->willReturn([
                $this->createBookEntity(),
            ]);

        $this->bookCategoryRepository->expects($this->once())
            ->method('existById')
            ->with(130)
            ->willReturn(true);

        $expected = new BookListResponse([
            $this->createBookItemModel(),
        ]);

        $this->assertEquals(
            $expected,
            $this->createBookService()
                ->getBookByCategory(130)
        );
    }

    private function createBookService(): BookService
    {
        return new BookService(
            $this->bookRepository,
            $this->bookCategoryRepository,
            $this->reviewsRepository,
            $this->ratingService
        );
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
