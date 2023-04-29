<?php

namespace App\Service;

use App\Repository\ReviewRepository;

class RatingService
{
    public function __construct(private ReviewRepository $reviewRepository)
    {
    }

    public function calcReviewRatingForBook(int $id, int $total): float|int
    {
        return $total > 0 ? $this->reviewRepository->getBookRatingTotalSum($id) / $total : 0;
    }
}