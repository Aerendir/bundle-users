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
use SerendipityHQ\Bundle\UsersBundle\Validator\RolesValidator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Safe\sprintf;

final class RoleRemCommand extends AbstractUserRolesCommand
{
    /** @var string */
    protected static $defaultName  = 'shq:user:role:rem';

    /** @var string */
    protected static $defaultDescription = 'Adds one or more roles to a user.';

    protected static string $title = 'Add role';

    public function __construct(EntityManagerInterface $entityManager, RolesValidator $rolesValidator, UsersManagerRegistry $usersManagerRegistry)
    {
        $this->entityManager = $entityManager;
        parent::__construct($entityManager, $rolesValidator, $usersManagerRegistry);
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setHelp(<<<'EOT'
The <info>%command.name%</info> command removes one or more roles from a user:

  <info>php %command.full_name% Aerendir ROLE_CUSTOM</info>
<info>php %command.full_name% Aerendir ROLE_CUSTOM1 ROLE_CUSTOM2 ROLE_CUSTOM3</info>
EOT);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $initialized = parent::execute($input, $output);
        if (0 !== $initialized) {
            return (int) $initialized;
        }

        $manager = $this->usersManagerRegistry->getManager($this->provider);
        $manager->removeRoles($this->user, $this->roles);

        $this->entityManager->flush();

        $message = sprintf('Roles removed from user %s.', $this->unique);
        $this->io->success($message);

        return (int) Command::SUCCESS;
    }
}
