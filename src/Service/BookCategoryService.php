<?php

namespace App\Service;

use App\Entity\BookCategory;
use App\Model\BookCategoryListItem;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use Doctrine\Common\Collections\Criteria;

class BookCategoryService
{
    public function __construct(private BookCategoryRepository $bookCategoryRepository)
    {
    }

    public function getCategories(): BookCategoryListResponse
    {
        $items = array_map(
            fn (BookCategory $bookCategory) => new BookCategoryListItem(
                $bookCategory->getId(),
                $bookCategory->getTitle(),
                $bookCategory->getSlug()
            ),
            $this->bookCategoryRepository->findBy([], ['title' => Criteria::ASC])
        );

        return new BookCategoryListResponse($items);
    }
}
