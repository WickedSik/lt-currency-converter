<?php

namespace App\EventSubscriber;

use App\Entity\CurrencyRate;
use App\Services\SearchIndexerService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsEntityListener(
    event: Events::postUpdate,
    method: 'postUpdate',
    entity: CurrencyRate::class
)]
readonly class CurrencyRateUpdatedListener
{
    public function __construct(
        private SearchIndexerService $indexerService,
        private LoggerInterface $logger
    )
    {
    }

    public function postUpdate(CurrencyRate $currencyRate, PostUpdateEventArgs $event): void
    {
        try {
            $this->indexerService->addDocument([
                'from' => $currencyRate->getFromCode(),
                'to' => $currencyRate->getToCode(),
                'rate' => $currencyRate->getRate(),
                'update' => $currencyRate->getDate()->format('c')
            ]);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }
    }
}