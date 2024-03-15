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

namespace SerendipityHQ\Bundle\UsersBundle\Manager;

use SerendipityHQ\Bundle\UsersBundle\Exception\UsersManagerException;

/**
 * Stores all created UsersManager.
 */
final class UsersManagerRegistry
{
    /** @var UsersManagerInterface[] $managers */
    private array $managers = [];

    /**
     * @param string $provider The provider as set in security.providers.[provider]
     */
    public function addManager(string $provider, UsersManagerInterface $manager): void
    {
        $this->managers[$provider] = $manager;
    }

    public function getManager(?string $provider = null): UsersManagerInterface
    {
        $availableManagers = \array_keys($this->getManagers());
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
