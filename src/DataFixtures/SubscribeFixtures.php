<?php

namespace App\DataFixtures;

use App\Entity\Subscriber;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubscribeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $subscribe = new Subscriber();
        $subscribe->setEmail('test@test.com');

        $manager->persist($subscribe);
        $manager->flush();
    }
}
