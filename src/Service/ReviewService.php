<?php

namespace App\Service;

use App\Entity\Review;
use App\Model\Review as ReviewModel;
use App\Model\ReviewPage;
use App\Repository\ReviewRepository;

class ReviewService
{
    public const PAGE_LIMIT = 5;

    public function __construct(
        private ReviewRepository $reviewRepository,
        private RatingService $ratingService
    ) {
    }

    public function getReviewPageByBookId(int $id, int $page): ReviewPage
    {
        $offset = max($page - 1, 0) * self::PAGE_LIMIT;
        $paginator = $this->reviewRepository->getPageBookById($id, $offset, self::PAGE_LIMIT);
        $total = count($paginator);

        return (new ReviewPage())
            ->setRating(
                $this->ratingService->calcReviewRatingForBook($id, $total)
            )
            ->setTotal($total)
            ->setPage($page)
            ->setPerPage(self::PAGE_LIMIT)
            ->setPages(ceil($total / self::PAGE_LIMIT))
            ->setItems(
                array_map(
                    [$this, 'map'],
                    $paginator->getIterator()->getArrayCopy()
                )
            );
    }

    public function map(Review $review): ReviewModel
    {
        return (new ReviewModel())
            ->setId($review->getId())
            ->setRating($review->getRating())
            ->setAuthor($review->getAuthor())
            ->setContent($review->getContent())
            ->setCreatedAt($review->getCreatedAt()->getTimestamp());
    }
}
