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

namespace SerendipityHQ\Bundle\UsersBundle\Command\Arguments;

use SerendipityHQ\Bundle\UsersBundle\Validator\RolesValidator;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Methods are protected because, this way, when the class of the using command is extended,
 * it is possible to access the methods also from the extending command.
 */
trait RolesCommandArgument
{
    public const ARG_ROLES = 'roles';

    /**
     * @return string[]|null
     */
    protected function getArgumentRolesOrNull(InputInterface $input): ?array
    {
        $roles = $input->getArgument(self::ARG_ROLES);

        if (null === $roles) {
            return null;
        }

        $errors = RolesValidator::validate($roles);

        if ([] !== $errors) {
            throw new \InvalidArgumentException(RolesValidator::formatErrors($errors));
        }

        return $roles;
    }

    /**
     * @return string[]
     */
    protected function getArgumentRoles(InputInterface $input): array
    {
        $roles = $this->getArgumentRolesOrNull($input);

        if (null === $roles) {
            throw new \InvalidArgumentException('The roles is required.');
        }

        return $roles;
    }
}
