<?php

namespace App\Service;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DataFetcher implements DataFetcherInterface
{
    private string $aresUrl;

    private HttpClientInterface $client;

    private LoggerInterface $logger;

    public function __construct(
        string $aresUrl,
        HttpClientInterface $client,
        LoggerInterface $logger,
    )
    {
        $this->aresUrl = $aresUrl;
        $this->client = $client;
        $this->logger = $logger;
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

}