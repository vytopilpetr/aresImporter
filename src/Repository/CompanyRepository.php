<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function getCompany(int $companyId): ?array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.branch', 'b')
            ->select(
                'c.companyId',
                'c.name',
                'c.createdAt',
                'b.street as branch_street',
                'b.city as branch_city',
                'b.postalCode as branch_postalCode'
            )
            ->where('c.companyId = :companyId')
            ->setParameter('companyId', $companyId)
            ->getQuery()
            ->getResult();
    }

}
