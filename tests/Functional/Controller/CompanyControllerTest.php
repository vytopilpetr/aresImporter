<?php

namespace Functional\Controller;

use App\DataFixtures\CompanyFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CompanyControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->loadFixtures();
    }

    private function loadFixtures(): void
    {
        $container = self::getContainer();
        $doctrine = $container->get('doctrine');
        $entityManager = $doctrine->getManager();

        $loader = new ContainerAwareLoader($container);
        $loader->addFixture(new CompanyFixture());

        $executor = new ORMExecutor($entityManager, new ORMPurger($entityManager));
        $executor->execute($loader->getFixtures());
    }

    public function testProvideCompanyActionCompanyNotFound(): void
    {
        $companyId = 1;

        $this->client->request('GET', '/rest/api/' . $companyId);
        $response = $this->client->getResponse();

        $this->assertSame(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());

    }

    public function testProvideCompanyActionEmptyCompanyId(): void
    {
        $this->client->request('GET', '/rest/api/');
        $response = $this->client->getResponse();

        $this->assertSame(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testProvideCompanyActionInvalidCompanyId(): void
    {
        $invalidCompanyId = 'abc';

        $this->client->request('GET', '/rest/api/' . $invalidCompanyId);
        $response = $this->client->getResponse();

        $this->assertSame(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testProvideCompanyActionSuccess(): void
    {
        $companyId = 1111;

        $this->client->request('GET', '/rest/api/' . $companyId);
        $response = $this->client->getResponse();

        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent())[0];
        $this->assertSame($content->companyId, $companyId);
        $this->assertSame($content->name, 'companyName');
        $this->assertNotEmpty($content->createdAt);
        $this->assertSame($content->branch_street, 'testAddress 1');
        $this->assertSame($content->branch_city, 'testCity');
        $this->assertSame($content->branch_postalCode, 666);
    }

    public function testCompanyImportActionFormDisplay(): void
    {
        $this->client->request('GET', '/importCompany');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorExists('form[name="company_search"]');
    }

    public function testCompanyImportActionFormSubmissionSuccess(): void
    {
        $formData = ['companyId' => 1111];

        $this->client->request('POST', '/importCompany', ['company_search' => $formData]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.ft-success', 'Import successful for entered ICO!');
    }

    public function testCompanyImportActionFormSubmissionEmptyDataForCompanyId(): void
    {
        $formData = ['companyId' => 0];

        $this->client->request('POST', '/importCompany', ['company_search' => $formData]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.ft-fail', 'Import failed! Please check the entered Company ICO.');
    }

}
