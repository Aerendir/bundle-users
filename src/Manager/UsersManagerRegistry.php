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

namespace SerendipityHQ\Bundle\UsersBundle\Manager;

use Safe\Exceptions\StringsException;
use SerendipityHQ\Bundle\UsersBundle\Manager\Exception\UsersManagerException;

/**
 * Stores all created UsersManager.
 */
class UsersManagerRegistry
{
    /** @var UsersManagerInterface[] $managers */
    private $managers = [];

    /**
     * @param string                $provider The provider as set in security.providers.[provider]
     * @param UsersManagerInterface $manager
     */
    public function addManager(string $provider, UsersManagerInterface $manager): void
    {
        $this->managers[$provider] = $manager;
    }

    /**
     * @param string|null $provider
     *
     * @throws UsersManagerException
     * @throws StringsException
     *
     * @return UsersManagerInterface
     */
    public function getManager(?string $provider = null): UsersManagerInterface
    {
        $availableManagers = array_keys($this->getManagers());
        if (null === $provider) {
            if (1 < \count($availableManagers)) {
                throw UsersManagerException::providerMustBeSpecified($availableManagers);
            }

            $provider = $availableManagers[0];
        }

        if (false === $this->hasProvider($provider)) {
            throw UsersManagerException::managerNotFound($provider);
        }

        return $this->managers[$provider];
    }

    /**
     * @param string $provider
     *
     * @return bool
     */
    public function hasProvider(string $provider): bool
    {
        return isset($this->managers[$provider]);
    }

    /**
     * @return UsersManagerInterface[]
     */
    public function getManagers(): array
    {
        return $this->managers;
    }
}
