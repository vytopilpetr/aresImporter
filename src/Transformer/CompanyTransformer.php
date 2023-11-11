<?php

namespace App\Transformer;

use App\Entity\Branch;
use App\Entity\Company;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

class CompanyTransformer
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private const RECORD_SELECTOR = 'are|Odpoved are|Zaznam';

    private const ADDRESS_ARES_SELECTOR = self::RECORD_SELECTOR . ' are|Identifikace are|Adresa_ARES';

    public function transform(string $companyData, int $companyId): ?Company
    {
        $crawler = new Crawler($companyData);
        $recordExist = $crawler->filter('are|Odpoved are|Pocet_zaznamu')->getNode(0)?->nodeValue;

        if ($recordExist) {
            $company = new Company();
            $company->setCompanyId($companyId);

            $name = $crawler->filter(self::RECORD_SELECTOR . ' are|Obchodni_firma')->getNode(0)?->nodeValue;
            if (!empty($name)) {
                $company->setName($name);
            }

            $branch = $this->transformBranch($crawler);
            !empty($branch) ? $company->setBranch($branch) : $this->logger->error('Data for ' . self::ADDRESS_ARES_SELECTOR . ' is empty');

            return $company;
        }

        $this->logger->error('No record in data for companyId: ' . $companyId);
        return null;
    }

    private function transformBranch(Crawler $crawler): ?Branch
    {
        $addressExist = $crawler->filter(self::ADDRESS_ARES_SELECTOR)->getNode(0)?->nodeValue;

        if ($addressExist) {
            $branch = new Branch();

            $city = $crawler->filter(self::ADDRESS_ARES_SELECTOR . ' dtt|Nazev_obce')->getNode(0)?->nodeValue;
            if (!empty($city)) {
                $branch->setCity($city);
            }

            $street = $crawler->filter(self::ADDRESS_ARES_SELECTOR . ' dtt|Nazev_ulice')->getNode(0)?->nodeValue;
            $number = $crawler->filter(self::ADDRESS_ARES_SELECTOR . ' dtt|Cislo_domovni')->getNode(0)?->nodeValue;
            if (!empty($street)) {
                if (!empty($number)) {
                    $branch->setStreet($street . ' ' . $number);
                } else {
                    $branch->setStreet($street);
                }
            }

            $postalCode = $crawler->filter(self::ADDRESS_ARES_SELECTOR . ' dtt|PSC')->getNode(0)?->nodeValue;
            if (!empty($city)) {
                $branch->setPostalCode($postalCode);
            }

            return $branch;
        }

        return null;
    }

}