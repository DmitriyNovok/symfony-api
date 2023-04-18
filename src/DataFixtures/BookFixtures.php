<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $androidCategory = $this->getReference(BookCategoryFixtures::ANDROID_CATEGORY);
        $deviceCategory = $this->getReference(BookCategoryFixtures::DEVICES_CATEGORY);

        $book = (new Book())
            ->setTitle('RX Java for Android Developers')
            ->setPublicationDate(new \DateTimeImmutable('2019-04-01'))
            ->setIsbn(323232332323)
            ->setDescription('description')
            ->setCategories(new ArrayCollection([$androidCategory, $deviceCategory]))
            ->setMeap(false)
            ->setAuthors(['Timo Mianen'])
            ->setSlug('rx-java-for-android-developers')
            ->setImage('/storage/images/dsf6sdfhfwe73');

        $manager->persist($book);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [BookCategoryFixtures::class];
    }
}
