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
use function Safe\sprintf;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerRegistry;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class UserDeactivateCommand extends AbstractUserActivationCommand
{
    protected static $defaultName  = 'shq:user:deactivate';
    protected static string $title = 'Deactivate user';

    public function __construct(EntityManagerInterface $entityManager, UsersManagerRegistry $usersManagerRegistry)
    {
        parent::__construct($entityManager, $usersManagerRegistry);
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setDescription('Deactivates a user.')
            ->setHelp(
<<<'EOT'
The <info>%command.name%</info> command deactivates the user:

  <info>php %command.full_name% Aerendir</info>
EOT);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $initialized = parent::execute($input, $output);
        if (0 !== $initialized) {
            return (int) $initialized;
        }

        $this->user->activate(false);
        $this->entityManager->flush();

        $message = sprintf('User %s deactivated.', $this->unique);
        $this->io->success($message);

        return 0;
    }
}
