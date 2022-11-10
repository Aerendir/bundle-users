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
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasRolesInterface;
use SerendipityHQ\Bundle\UsersBundle\Validator\RolesValidator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function Safe\sprintf;

abstract class AbstractUserRolesCommand extends AbstractUserCommand
{
    /** @var HasRolesInterface|UserInterface */
    protected $user;

    /** @var string[] */
    protected array $roles;

    protected RolesValidator $rolesValidator;

    public function __construct(EntityManagerInterface $entityManager, RolesValidator $rolesValidator, UsersManagerRegistry $usersManagerRegistry)
    {
        $this->rolesValidator = $rolesValidator;
        parent::__construct($entityManager, $usersManagerRegistry);
    }

    protected function configure(): void
    {
        parent::configure();
        $this->addArgument('roles', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The role(s) to apply to or remove from the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $initialized = parent::execute($input, $output);
        if (0 !== $initialized) {
            return (int) $initialized;
        }

        if ( ! $this->user instanceof HasRolesInterface) {
            $message = sprintf('User class "%s" must implement interface "%s".', \get_class($this->user), HasRolesInterface::class);
            $this->io->error($message);

            return (int) Command::FAILURE;
        }

        $roles = $input->getArgument('roles');
        if (false === \is_array($roles)) {
            return (int) Command::FAILURE;
        }

        $this->roles = $roles;

        $errors = $this->rolesValidator->validate($roles);
        if ([] !== $errors) {
            $this->printErrors($errors);

            return (int) Command::FAILURE;
        }

        return (int) Command::SUCCESS;
    }

    /**
     * @param array<string,array<string>> $errors
     */
    private function printErrors(array $errors): void
    {
        $this->io->error('The roles you passed have some errors');
        $this->io->writeln('Found errors');

        foreach ($errors as $role => $roleErrors) {
            $message = sprintf('> <fg=green>%s</>', $role);
            $this->io->writeln($message);
            foreach ($roleErrors as $error) {
                $message = sprintf('  - %s', $error);
                $this->io->writeln($message);
            }
        }
    }
}
