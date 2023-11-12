<?php

namespace App\Service;

interface DataFetcherInterface
{
    public function fetchCompanyData(int $companyId): string;
}