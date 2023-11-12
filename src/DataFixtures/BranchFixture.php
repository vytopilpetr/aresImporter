<?php

namespace App\DataFixtures;

use App\Entity\Branch;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BranchFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $branch = new Branch();
        $branch->setId(1);
        $branch->setCity('testCity');
        $branch->setStreet('testAddress 1');
        $branch->setPostalCode(666);
        $manager->persist($branch);

        $this->addReference('branch', $branch);

        $manager->flush();
    }

}