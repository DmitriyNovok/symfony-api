<?php

namespace App\Service;

use App\Entity\Book;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;

class BookService
{
    public function __construct(
        private BookRepository $bookRepository,
        private BookCategoryRepository $bookCategoryRepository
    ) {
    }

    public function getBookByCategory(int $category_id): BookListResponse
    {
        if (!$this->bookCategoryRepository->existById($category_id)) {
            throw new BookCategoryNotFoundException();
        }

        return new BookListResponse(
            array_map(
                [$this, 'map'],
                $this->bookRepository->findBooksByCategoryId($category_id)
            )
        );
    }

    public function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setAuthors($book->getAuthors())
            ->setImage($book->getImage())
            ->setPublicationDate($book->getPublicationDate()->getTimestamp())
            ->setMeap($book->isMeap());
    }
}
