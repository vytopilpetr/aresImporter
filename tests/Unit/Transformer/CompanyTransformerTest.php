<?php

namespace Unit\Transformer;

use App\Entity\Company;
use App\Transformer\CompanyTransformer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CompanyTransformerTest extends TestCase
{
    private CompanyTransformer $companyTransformer;

    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->companyTransformer = new CompanyTransformer($this->logger);
    }

    public function testTransformSuccess(): void
    {
        $companyId = 1111;
        $companyData = file_get_contents('tests/Data/company.xml');

        $this->logger->expects($this->never())->method('error');

        $result = $this->companyTransformer->transform($companyData, $companyId);

        $this->assertInstanceOf(Company::class, $result);
        $this->assertEquals($companyId, $result->getCompanyId());
        $this->assertEquals('Asseco Central Europe, a.s.', $result->getName());
        $this->assertEquals('Praha', $result->getBranch()->getCity());
        $this->assertEquals('Budějovická 778', $result->getBranch()->getStreet());
        $this->assertEquals(14000, $result->getBranch()->getPostalCode());
    }

    public function testTransformEmptyResponse(): void
    {
        $companyId = 456;
        $companyData = '';

        $this->logger->expects($this->once())->method('error');

        $result = $this->companyTransformer->transform($companyData, $companyId);

        $this->assertNull($result);
    }

    public function testTransformEmptyBranch(): void
    {
        $companyId = 789;
        $companyData = file_get_contents('tests/Data/companyWithoutBranch.xml');

        $this->logger->expects($this->once())->method('error');

        $result = $this->companyTransformer->transform($companyData, $companyId);

        $this->assertInstanceOf(Company::class, $result);
        $this->assertEquals($companyId, $result->getCompanyId());
        $this->assertEquals('Asseco Central Europe, a.s.', $result->getName());
        $this->assertNull($result->getBranch());
    }

}
