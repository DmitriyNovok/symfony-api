<?php

namespace App\Mapper;

use App\Entity\Book;
use App\Model\BookDetails;
use App\Model\BookListItem;

class BookMapper
{
    public static function map(Book $book, BookDetails|BookListItem $model): BookDetails|BookListItem
    {
        return $model
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setAuthors($book->getAuthors())
            ->setImage($book->getImage())
            ->setPublicationDate($book->getPublicationDate()->getTimestamp())
            ->setMeap($book->isMeap());
    }
}
