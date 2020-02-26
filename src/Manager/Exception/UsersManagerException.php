<?php

declare(strict_types=1);

/*
 * This file is part of SHQUsersBundle.
 *
 * (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\UsersBundle\Manager\Exception;

use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use SerendipityHQ\Bundle\UsersBundle\Property\HasPlainPasswordInterface;

/**
 * Exceptions related to UsersManager.
 */
class UsersManagerException extends \RuntimeException
{
    /**
     * @param string $userClass
     *
     * @throws StringsException
     *
     * @return UsersManagerException
     */
    public static function userClassMustImplementHasPlainPasswordInterface(string $userClass): UsersManagerException
    {
        $message = sprintf('The UserInterface class "%s" MUST implement the "%s" interface.', $userClass, HasPlainPasswordInterface::class);

        return new self($message);
    }

    /**
     * @param string $provider
     *
     * @throws StringsException
     *
     * @return UsersManagerException
     */
    public static function managerNotFound(string $provider): UsersManagerException
    {
        $message = sprintf('The users manager for provider "%s" you are looking for doesn\'t exist. Please, be sure it is configured in "security.providers".', $provider);

        return new self($message);
    }

    /**
     * @param array $availableProviders
     *
     * @throws StringsException
     *
     * @return UsersManagerException
     */
    public static function providerMustBeSpecified(array $availableProviders): UsersManagerException
    {
        $message = sprintf('Currently there are "%s" providers configured in "security.providers". Please, specify for which one you\'d like to get the UsersManager. Available providers are: %s', count($availableProviders), implode(', ', $availableProviders));

        return new self($message);
    }

    public static function errorInEncodingThePassword(string $unique): UsersManagerException
    {
        $message = sprintf('An unknown error occurred encoding the password for the user %s.', $unique);

        return new self($message);
    }
}
