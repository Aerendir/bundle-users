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

use function Safe\sprintf;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasActivationInterface;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasRolesInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractUserCommand extends AbstractUsersCommand
{
    /**
     * @var HasActivationInterface|HasPlainPasswordInterface|HasRolesInterface|UserInterface
     * @suppress PhanWriteOnlyProtectedProperty
     */
    protected $user;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $initialized = parent::execute($input, $output);
        if (0 !== $initialized) {
            return (int) $initialized;
        }

        $manager = $this->usersManagerRegistry->getManager($this->provider);
        $user    = $manager->load($this->unique);

        if (null === $user) {
            $message = sprintf('User "%s" not found.', $this->unique);
            $this->io->error($message);

            return 1;
        }

        $this->user = $user;

        return 0;
    }
}
