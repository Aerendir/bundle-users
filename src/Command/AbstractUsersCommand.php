<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Users Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\UsersBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractUsersCommand extends Command
{
    /** @var string The title to print when starting the command */
    protected static string $title;

    protected string $unique;
    protected string $provider;
    protected SymfonyStyle $io;

    public function __construct(protected EntityManagerInterface $entityManager, protected UsersManagerRegistry $usersManagerRegistry)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('unique', InputArgument::REQUIRED, 'The value to use with the unique field.')
            ->addOption('provider', 'p', InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title(static::$title);

        $unique = $input->getArgument('unique');
        if (false === \is_string($unique)) {
            return self::FAILURE;
        }

        $this->unique = $unique;

        $provider          = $input->getOption('provider');
        $availableManagers = \array_keys($this->usersManagerRegistry->getManagers());
        if (null === $provider) {
            if (1 < \count($availableManagers)) {
                $message = sprintf('There is more than one provider configured in your "security.providers". Please, pass the option --provider to the command to use the right one. Available providers are: %s', \implode(', ', $availableManagers));
                $this->io->error($message);

                return self::FAILURE;
            }

            $provider = $availableManagers[0];
        }

        if (false === \is_string($provider)) {
            $message = 'Impossible to find a suitable user provider: please, check your security configuration.';
            $this->io->error($message);

            return self::FAILURE;
        }

        $this->provider = $provider;

        if (false === $this->usersManagerRegistry->hasProvider($this->provider)) {
            $message = sprintf('The provider "%s" you passed is not configured in your "security.providers". Available providers are: %s', $provider, \implode(', ', $availableManagers));
            $this->io->error($message);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
