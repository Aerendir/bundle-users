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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Safe\sprintf;

final class UserActivateCommand extends AbstractUserActivationCommand
{
    /** @var string */
    protected static $defaultName  = 'shq:user:activate';

    /** @var string */
    protected static $defaultDescription = 'Activates a user.';

    protected static string $title = 'Activate user';

    public function __construct(EntityManagerInterface $entityManager, UsersManagerRegistry $usersManagerRegistry)
    {
        parent::__construct($entityManager, $usersManagerRegistry);
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setHelp(<<<'EOT'
The <info>%command.name%</info> command activates the user:

  <info>php %command.full_name% Aerendir</info>
EOT);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $initialized = parent::execute($input, $output);
        if (0 !== $initialized) {
            return (int) $initialized;
        }

        $this->user->activate();
        $this->entityManager->flush();

        $message = sprintf('User %s activated.', $this->unique);
        $this->io->success($message);

        return 0;
    }
}
