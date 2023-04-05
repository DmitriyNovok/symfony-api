<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookCategoryControllerTest extends WebTestCase
{
    public function testCategories(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/book/categories');

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonFile(
            __DIR__.'/responses/BookCategoryControllerTest_testCategories.json',
            $client->getResponse()->getContent()
        );
    }
}
