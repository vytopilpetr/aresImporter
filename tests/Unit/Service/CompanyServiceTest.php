<?php

namespace Unit\Service;

use App\Entity\Branch;
use App\Entity\Company;
use App\Service\DataFetcherInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Service\CompanyService;
use App\Transformer\CompanyTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Repository\CompanyRepository;

class CompanyServiceTest extends TestCase
{
    private CompanyRepository|MockObject $companyRepoMock;
    private EntityManagerInterface|MockObject $entityManagerMock;
    private LoggerInterface|MockObject $loggerMock;
    private DataFetcherInterface|MockObject $dataFetcherMock;
    private CompanyTransformer|MockObject $transformerMock;

    protected function setUp(): void
    {
        $this->companyRepoMock = $this->createMock(CompanyRepository::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->dataFetcherMock = $this->createMock(DataFetcherInterface::class);
        $this->transformerMock = $this->createMock(CompanyTransformer::class);

        $this->companyService = new CompanyService(
            $this->transformerMock,
            $this->entityManagerMock,
            $this->loggerMock,
            $this->dataFetcherMock
        );
    }

    public function testImportCompanyWithNoData(): void
    {
        $companyId = 1;
        $this->dataFetcherMock->method('fetchCompanyData')->willReturn('');

        $result = $this->companyService->importCompany($companyId);

        $this->assertFalse($result);
    }

    public function testImportCompanyWithNoTransformedData(): void
    {
        $companyId = 1;
        $this->dataFetcherMock->method('fetchCompanyData')->willReturn('');
        $this->transformerMock->method('transform')->willReturn(null);

        $result = $this->companyService->importCompany($companyId);

        $this->assertFalse($result);
    }

    public function testImportCompanyWithExistingCompany(): void
    {
        $companyId = 1;
        $companyData = 'mocked_company_data_response';
        $this->dataFetcherMock->method('fetchCompanyData')->willReturn($companyData);
        $transformedCompany = new Company();
        $transformedCompany->setCompanyId($companyId);
        $transformedCompany->setName('nameUpdated');
        $transformedBranch = new Branch();
        $transformedBranch->setCity('city');
        $transformedBranch->setStreet('streetUpdated');
        $transformedBranch->setPostalCode(22233);
        $transformedCompany->setBranch($transformedBranch);
        $this->transformerMock->method('transform')->willReturn($transformedCompany);
        $this->entityManagerMock->method('getRepository')->willReturn($this->companyRepoMock);
        $existingCompany = new Company();
        $existingCompany->setCompanyId($companyId);
        $existingCompany->setName('name');
        $existingBranch = new Branch();
        $existingBranch->setCity('city');
        $existingBranch->setStreet('street');
        $existingBranch->setPostalCode(22233);
        $existingCompany->setBranch($existingBranch);
        $this->companyRepoMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['companyId' => $companyId])
            ->willReturn($existingCompany);

        $result = $this->companyService->importCompany($companyId);

        $this->assertTrue($result);
    }


    public function testImportCompanyWithNewCompany(): void
    {
        $companyId = 1;
        $companyData = 'mocked_company_data_response';
        $this->dataFetcherMock->method('fetchCompanyData')->willReturn($companyData);
        $transformedCompany = new Company();
        $transformedCompany->setCompanyId($companyId);
        $transformedCompany->setName('nameUpdated');
        $transformedBranch = new Branch();
        $transformedBranch->setCity('city');
        $transformedBranch->setStreet('streetUpdated');
        $transformedBranch->setPostalCode(22233);
        $transformedCompany->setBranch($transformedBranch);
        $this->transformerMock->method('transform')->willReturn($transformedCompany);
        $this->entityManagerMock->method('getRepository')->willReturn($this->companyRepoMock);
        $this->companyRepoMock->method('findOneBy')->willReturn(null);

        $result = $this->companyService->importCompany($companyId);

        $this->assertTrue($result);
    }

}

