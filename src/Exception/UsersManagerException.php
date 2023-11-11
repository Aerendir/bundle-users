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

namespace SerendipityHQ\Bundle\UsersBundle\Exception;

/**
 * Exceptions related to UsersManager.
 */
final class UsersManagerException extends UsersException
{
    public static function managerNotFound(string $provider): UsersManagerException
    {
        $message = sprintf('The users manager for provider "%s" you are looking for doesn\'t exist. Please, be sure it is configured in "security.providers".', $provider);

        return new self($message);
    }

    /**
     * @param string[] $availableProviders
     */
    public static function providerMustBeSpecified(array $availableProviders): UsersManagerException
    {
        $message = sprintf('Currently there are "%s" providers configured in "security.providers". Please, specify for which one you\'d like to get the UsersManager. Available providers are: %s', \count($availableProviders), \implode(', ', $availableProviders));

        return new self($message);
    }
}
