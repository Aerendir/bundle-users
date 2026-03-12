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
trait PassCommandArgument
{
    public const ARG_PASS = 'pass';

    protected function getArgumentPassOrNull(InputInterface $input): ?string
    {
        $pass = $input->getArgument(self::ARG_PASS);

        if (is_string($pass)) {
            return $pass;
        }

        if (null === $pass) {
            return null;
        }

        throw new \InvalidArgumentException(sprintf('The pass must be a string. Current type: %s.', get_debug_type($pass)));
    }

    protected function getArgumentPass(InputInterface $input): string
    {
        $pass = $this->getArgumentPassOrNull($input);

        if (null === $pass) {
            throw new \InvalidArgumentException('The pass is required.');
        }

        return $pass;
    }

    protected function ensurePassIsString(mixed $pass): void
    {
        if (false === is_string($pass)) {
            throw new \InvalidArgumentException(sprintf('The pass "%s" must be a string.', is_scalar($pass) ? (string) $pass : get_debug_type($pass)));
        }
    }
}
