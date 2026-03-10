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

use Symfony\Component\Console\Input\InputInterface;

/**
 * Methods are protected because, this way, when the class of the using command is extended,
 * it is possible to access the methods also from the extending command.
 */
trait UniqueCommandArgument
{
    public const ARG_UNIQUE = 'unique';

    protected function getArgumentUniqueOrNull(InputInterface $input): ?string
    {
        $unique = $input->getArgument(self::ARG_UNIQUE);

        if (is_string($unique)) {
            return $unique;
        }

        if (null === $unique) {
            return null;
        }

        throw new \InvalidArgumentException(sprintf('The unique must be a string. Current type: %s.', get_debug_type($unique)));
    }

    protected function getArgumentUnique(InputInterface $input): string
    {
        $unique = $this->getArgumentUniqueOrNull($input);

        if (null === $unique) {
            throw new \InvalidArgumentException('The unique is required.');
        }

        return $unique;
    }
}
