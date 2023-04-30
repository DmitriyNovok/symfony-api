<?php

namespace App\Tests\Service;

use App\Entity\Review;
use App\Model\Review as ReviewModel;
use App\Model\ReviewPage;
use App\Repository\ReviewRepository;
use App\Service\RatingService;
use App\Service\ReviewService;
use App\Tests\AbstractTestCase;

class ReviewServiceTest extends AbstractTestCase
{
    private ReviewRepository $reviewRepository;

    private const BOOK_ID = 1;

    private const PER_PAGE = 5;

    private RatingService $ratingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewRepository = $this->createMock(ReviewRepository::class);
        $this->ratingService = $this->createMock(RatingService::class);
    }

    public function dataProvider(): array
    {
        return [
            [0, 0],
            [-1, 0],
            [-20, 0],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetReviewPageBookIdInvalidPage(int $page, int $offset): void
    {
        $this->ratingService->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(self::BOOK_ID, 0)
            ->willReturn(0.0);

        $this->reviewRepository->expects($this->once())
            ->method('getPageBookById')
            ->with(self::BOOK_ID, $offset, self::PER_PAGE)
            ->willReturn(new \ArrayIterator());

        $service = new ReviewService($this->reviewRepository, $this->ratingService);
        $expected = (new ReviewPage())
            ->setRating(0)
            ->setTotal(0)
            ->setPage($page)
            ->setItems([])
            ->setPages(0)
            ->setPerPage(self::PER_PAGE);

        $this->assertEquals($expected, $service->getReviewPageByBookId(self::BOOK_ID, $page));
    }

    public function testGetReviewPageBookId(): void
    {
        $this->ratingService->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(self::BOOK_ID, 1)
            ->willReturn(4.0);

        $entity = (new Review())
            ->setAuthor('Tester')
            ->setContent('test content')
            ->setRating(4)
            ->setCreatedAt(new \DateTimeImmutable('2020-10-10'));

        $this->setEntityId($entity, 1);

        $this->reviewRepository->expects($this->once())
            ->method('getPageBookById')
            ->with(self::BOOK_ID, 0, self::PER_PAGE)
            ->willReturn(new \ArrayIterator([$entity]));

        $service = new ReviewService($this->reviewRepository, $this->ratingService);

        $expected = (new ReviewPage())
            ->setRating(4)
            ->setTotal(1)
            ->setPage(1)
            ->setItems([
               (new ReviewModel())
                   ->setId(1)
                   ->setRating(4)
                   ->setCreatedAt(1602288000)
                   ->setContent('test content')
                   ->setAuthor('Tester'),
            ])
            ->setPages(1)
            ->setPerPage(self::PER_PAGE);

        $this->assertEquals($expected, $service->getReviewPageByBookId(self::BOOK_ID, 1));
    }
}
