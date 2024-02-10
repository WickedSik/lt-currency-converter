<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use _PHPStan_39fe102d2\Nette\Utils\DateTime;
use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import-rates',
    description: 'Import currency rates from floatrates.com',
)]
class ImportRatesCommand extends Command
{
    private EntityRepository $repository;

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct();

        $this->repository = $this->entityManager->getRepository(CurrencyRate::class);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('code', InputArgument::REQUIRED, 'The country code to pull')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $code = $input->getArgument('code');

        try {
            $response = $this->client->request('GET', "https://www.floatrates.com/daily/{$code}.json");
            if ($response->getStatusCode() !== 200) {
                $io->error("Unable to fetch data: {$response->getContent()}");

                return Command::FAILURE;
            } else {
                $io->info("Fetched data");

                $data = json_decode($response->getContent(), true);
                foreach ($data as $conversion) {
                    $this->updateCurrencyRate($code, $conversion);
                    // $io->info("=> {$code}:{$conversion['code']} conversion added/updated");
                }

                $io->info("Flushing data");
                $this->entityManager->flush();

                $io->success('Currency rates imported');

                return Command::SUCCESS;
            }
        } catch (ExceptionInterface $e) {
            $io->error("Something went wrong: {$e->getMessage()}");

            return Command::FAILURE;
        }
    }

    private function updateCurrencyRate(string $code, array $data): void {
        $rate = $this->repository->findOneBy([
            'fromCode' => strtoupper($code),
            'toCode' => $data['code']
        ]);
        if(!$rate) {
            $rate = new CurrencyRate();
            $rate
                ->setFromCode(strtoupper($code))
                ->setToCode($data['code'])
                ->setToName($data['name'])
            ;
        }

        $rate
            ->setRate($data['rate'])
            ->setInverseRate($data['inverseRate'])
            ->setDate(\DateTimeImmutable::createFromFormat(DateTimeInterface::RFC7231, $data['date']))
        ;

        $this->entityManager->persist($rate);
    }
}
