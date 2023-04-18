<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookToBookFormat;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookDetails;
use App\Model\BookFormat;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ReadableCollection;

class BookService
{
    public function __construct(
        private BookRepository $bookRepository,
        private BookCategoryRepository $bookCategoryRepository,
        private ReviewRepository $reviewRepository
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

    public function getBookById(int $id): BookDetails
    {
        $rating = 0;
        $book = $this->bookRepository->getById($id);
        $reviews = $this->reviewRepository->countByBookId($id);
        $categories = $book->getCategories()
            ->map(function (BookCategory $bookCategory) {
                return new BookCategoryModel(
                    $bookCategory->getId(),
                    $bookCategory->getTitle(),
                    $bookCategory->getSlug()
                );
            });

        if ($reviews > 0) {
            $rating = $this->reviewRepository->getBookRatingTotalSum($id) / $reviews;
        }

        return (new BookDetails())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setAuthors($book->getAuthors())
            ->setImage($book->getImage())
            ->setPublicationDate($book->getPublicationDate()->getTimestamp())
            ->setMeap($book->isMeap())
            ->setRating($rating)
            ->setReviews($reviews)
            ->setFormats((array) $this->mapFormats($book->getFormats()))
            ->setCategories($categories->toArray());
    }

    /**
     * @param Collection<BookToBookFormat> $formats
     */
    private function mapFormats(Collection $formats): ReadableCollection
    {
        return $formats->map(function (BookToBookFormat $bookToBookFormat) {
            return (new BookFormat())
                ->setId($bookToBookFormat->getFormat()->getId())
                ->setTitle($bookToBookFormat->getFormat()->getTitle())
                ->setDescription($bookToBookFormat->getFormat()->getDescription())
                ->setComment($bookToBookFormat->getFormat()->getComment())
                ->setPrice($bookToBookFormat->getPrice())
                ->setDiscountPercent($bookToBookFormat->getDiscountPercent());
        });
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
