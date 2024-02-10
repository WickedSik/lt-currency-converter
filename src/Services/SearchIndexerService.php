<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class SearchIndexerService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire(param: 'app.elasticsearch.host')]
        private string $elasticHost
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function addDocument(array $document): bool
    {
        $index = "currency-rate-".date('Ymd');

        $response = $this->httpClient->request('POST', "{$this->elasticHost}/{$index}/_doc", [
            'json' => $document
        ]);

        return $response->getStatusCode() >= 200 && $response->getStatusCode() <= 300;
    }
}