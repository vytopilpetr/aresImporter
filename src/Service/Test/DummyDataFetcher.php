<?php

namespace App\Service\Test;

use App\Service\DataFetcherInterface;

class DummyDataFetcher implements DataFetcherInterface
{
    public function fetchCompanyData(int $companyId): string
    {
        if ($companyId === 1111) {
            return file_get_contents('tests/Data/company.xml');
        } elseif ($companyId === 2222) {
            return file_get_contents('tests/Data/companyWithoutBranch.xml');
        }

        return '';
    }
}