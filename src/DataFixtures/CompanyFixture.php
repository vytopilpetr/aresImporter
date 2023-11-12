<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompanyFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $branch = $this->getReference('branch');

        $company = new Company();
        $company->setCompanyId(1111);
        $company->setName('companyName');
        $company->setBranch($branch);
        $manager->persist($company);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BranchFixture::class,
        ];
    }
}