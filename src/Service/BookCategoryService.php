<?php

namespace App\Service;

use App\Entity\BookCategory;
use App\Model\BookCategoryListItem;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;

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
            $this->bookCategoryRepository->findAllSortedByTitle()
        );

        return new BookCategoryListResponse($items);
    }
}
