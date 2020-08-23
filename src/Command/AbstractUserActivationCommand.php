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
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasActivationInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractUserActivationCommand extends AbstractUserCommand
{
    /** @var HasActivationInterface&UserInterface */
    protected $user;

    public function __construct(EntityManagerInterface $entityManager, UsersManagerRegistry $usersManagerRegistry)
    {
        parent::__construct($entityManager, $usersManagerRegistry);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $initialized = parent::execute($input, $output);
        if (0 !== $initialized) {
            return $initialized;
        }

        if ( ! $this->user instanceof HasActivationInterface) {
            $message = \Safe\sprintf('User class "%s" must implement interface "%s".', \get_class($this->user), HasActivationInterface::class);
            $this->io->error($message);

            return 1;
        }

        return 0;
    }
}
