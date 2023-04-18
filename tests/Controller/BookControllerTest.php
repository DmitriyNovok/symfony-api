<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Tests\AbstractControllerTest;
use Doctrine\Common\Collections\ArrayCollection;

class BookControllerTest extends AbstractControllerTest
{
    public function testBooksByCategories(): void
    {
        $categoryId = $this->createCategory();

        $this->client->request('GET', "/api/v1/category/$categoryId/books");
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationDate'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'image' => ['type' => 'string'],
                            'meap' => ['type' => 'boolean'],
                            'publicationDate' => ['type' => 'integer'],
                            'id' => ['type' => 'integer'],
                            'authors' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'string',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function createCategory(): int
    {
        $bookCategory = (new BookCategory())->setTitle('Devices')->setSlug('devices');
        $this->entityManager->persist($bookCategory);

        $book = (new Book())
            ->setTitle('Test book')
            ->setSlug('test-book')
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setImage('/storage/fsfsd87sdfn')
            ->setMeap(true)
            ->setAuthors(['Test authors'])
            ->setPublicationDate(new \DateTimeImmutable());

        $this->entityManager->persist($book);

        $this->entityManager->flush();

        return $bookCategory->getId();
    }
}
