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

use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerRegistry;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasRolesInterface;
use SerendipityHQ\Bundle\UsersBundle\Validator\RolesValidator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractUserRolesCommand extends AbstractUserCommand
{
    /** @var string[] */
    protected array $roles;

    /** @var UserInterface&HasRolesInterface */
    protected $user;
    protected RolesValidator $rolesValidator;

    public function __construct(RolesValidator $rolesValidator, UsersManagerRegistry $usersManagerRegistry)
    {
        $this->rolesValidator = $rolesValidator;
        parent::__construct($usersManagerRegistry);
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
            return $initialized;
        }

        $roles = $input->getArgument('roles');
        if (false === \is_array($roles)) {
            return 1;
        }
        $this->roles = $roles;

        $errors = $this->rolesValidator->validate($roles);
        if (0 < \count($errors)) {
            $this->printErrors($errors);

            return 1;
        }

        $manager = $this->usersManagerRegistry->getManager($this->provider);
        $user    = $manager->load($this->unique);

        if (null === $user) {
            $message = \Safe\sprintf('User "%s" not found.', $this->unique);
            $this->io->error($message);

            return 1;
        }

        if ( ! $user instanceof HasRolesInterface) {
            $message = \Safe\sprintf('User class "%s" must implement interface "%s".', \get_class($user), HasRolesInterface::class);
            $this->io->error($message);

            return 1;
        }

        $this->user = $user;

        return 0;
    }

    /**
     * @param array<string,array<string>> $errors
     */
    private function printErrors(array $errors): void
    {
        $this->io->error('The roles you passed have some errors');
        $this->io->writeln('Found errors');

        foreach ($errors as $role => $roleErrors) {
            $message = \Safe\sprintf('> <fg=green>%s</>', $role);
            $this->io->writeln($message);
            foreach ($roleErrors as $error) {
                $message = \Safe\sprintf('  - %s', $error);
                $this->io->writeln($message);
            }
        }
    }
}
