<?php

namespace App\Service;

use App\Entity\Company;
use App\Transformer\CompanyTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CompanyService
{
    private string $aresUrl;
    private HttpClientInterface $client;

    private CompanyTransformer $companyTransformer;

    private EntityManagerInterface $em;

    private LoggerInterface $logger;

    public function __construct(
        string $aresUrl,
        HttpClientInterface $client,
        CompanyTransformer $companyTransformer,
        EntityManagerInterface $em,
        LoggerInterface $logger,
    )
    {
        $this->aresUrl = $aresUrl;
        $this->client = $client;
        $this->companyTransformer = $companyTransformer;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function getCompany(int $companyId): ?array
    {
        $companyRepo = $this->em->getRepository(Company::class);

        return $companyRepo->getCompany($companyId);
    }

    private function findCompany(int $companyId): ?Company
    {
        $companyRepo = $this->em->getRepository(Company::class);

        return $companyRepo->findOneBy(['companyId' => $companyId]);
    }


    public function importCompany(int $companyId): bool
    {
        $companyData = $this->fetchCompanyData($companyId);

        if (!empty($companyData)) {
            $transformedData = $this->companyTransformer->transform($companyData, $companyId);
            $company = $this->findCompany($companyId);

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

    public function fetchCompanyData(int $companyId): string
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->aresUrl,
                ['query' => [
                    'ico' => $companyId,
                ],
                ]
            );

            return $response->getContent();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return '';
        }
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