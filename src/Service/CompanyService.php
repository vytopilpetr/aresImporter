<?php

namespace App\Service;

use App\Entity\Company;
use App\Transformer\CompanyTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

class CompanyService
{
    private CompanyTransformer $companyTransformer;

    private EntityManagerInterface $em;

    private LoggerInterface $logger;

    private DataFetcherInterface $dataFetcher;

    public function __construct(
        CompanyTransformer $companyTransformer,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        DataFetcherInterface $dataFetcher
    )
    {
        $this->companyTransformer = $companyTransformer;
        $this->em = $em;
        $this->logger = $logger;
        $this->dataFetcher = $dataFetcher;
    }

    public function getCompany(int $companyId): ?array
    {
        $companyRepo = $this->em->getRepository(Company::class);

        return $companyRepo->getCompany($companyId);
    }

    public function importCompany(int $companyId): bool
    {
        $companyData = $this->dataFetcher->fetchCompanyData($companyId);

        if (!empty($companyData)) {
            $transformedData = $this->companyTransformer->transform($companyData, $companyId);
            $companyRepo = $this->em->getRepository(Company::class);
            $company = $companyRepo->findOneBy(['companyId' => $companyId]);

            return $this->createOrUpdate($company, $transformedData);
        }

        $this->logger->error('No data fetched for companyId ' . $companyId);

        return false;
    }

    private function createOrUpdate(?Company $company, ?Company $companyData): bool
    {
        if ($companyData) {
            if ($company) {
                $company->setCompanyId($companyData->getCompanyId());
                $company->setName($companyData->getName());
                $companyBranch = $company->getBranch();
                $companyBranch->setCity($companyData->getBranch()->getCity());
                $companyBranch->setStreet($companyData->getBranch()->getStreet());
                $companyBranch->setPostalCode($companyData->getBranch()->getPostalCode());
                $company->setBranch($companyBranch);
            }

            return $this->saveCompany($company ?? $companyData);
        }

        return false;
    }

    private function saveCompany(Company $company): bool
    {
        try {
            $this->em->persist($company);
            $this->em->flush();
            return true;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return false;
    }

}