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

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-ip-to-user',
    description: 'Add a short description for your command',
)]
class AddIpToUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'User (by username) to update')
            ->addArgument('ip', InputArgument::REQUIRED, 'Trusted IP to add to user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');
        $ip = $input->getArgument('ip');

        if(ip2long($ip) === false) {
            $io->error("{$ip} is not a valid IPv4 address");
            return Command::FAILURE;
        }

        $repository = $this->entityManager->getRepository(User::class);

        $user = $repository->findOneBy(['username' => $username]);

        $existingIPs = $user->getTrustedIPs() ?: [];
        $existingIPs[] = $ip;

        $user->setTrustedIPs($existingIPs);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success("{$ip} added to trusted IP addresses for User {$user->getUsername()}");

        return Command::SUCCESS;
    }
}
