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

namespace SerendipityHQ\Bundle\UsersBundle\Command\Options;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Methods are protected because, this way, when the class of the using command is extended,
 * it is possible to access the methods also from the extending command.
 */
trait ProviderCommandOption
{
    public const OPT_PROVIDER = 'provider';

    protected function getOptionProviderOrNull(InputInterface $input): ?string
    {
        $provider = $input->getOption(self::OPT_PROVIDER);

        if (is_string($provider)) {
            return $provider;
        }

        if (null === $provider) {
            return null;
        }

        throw new \InvalidArgumentException(sprintf('The provider must be a string. Current type: %s.', get_debug_type($provider)));
    }

    protected function getOptionProvider(InputInterface $input): string
    {
        $provider = $this->getOptionProviderOrNull($input);

        if (null === $provider) {
            throw new \InvalidArgumentException('The provider is required.');
        }

        return $provider;
    }
}
