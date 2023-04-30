<?php

namespace App\Tests\Mapper;

use App\Entity\Book;
use App\Mapper\BookMapper;
use App\Model\BookDetails;
use App\Tests\AbstractTestCase;

class BookMapperTest extends AbstractTestCase
{
    public function testMap()
    {
        $book = (new Book())
            ->setTitle('Test')
            ->setSlug('slug')
            ->setAuthors(['Tester'])
            ->setPublicationDate(new \DateTimeImmutable('2020-10-10'))
            ->setImage('/storage/2138hvvs121f441')
            ->setMeap(true);

        $this->setEntityId($book, 1);

        $expected = (new BookDetails())
            ->setId(1)
            ->setTitle('Test')
            ->setSlug('slug')
            ->setAuthors(['Tester'])
            ->setPublicationDate(1602288000)
            ->setImage('/storage/2138hvvs121f441')
            ->setMeap(true);

        $this->assertEquals($expected, BookMapper::map($book, new BookDetails()));
    }
}
