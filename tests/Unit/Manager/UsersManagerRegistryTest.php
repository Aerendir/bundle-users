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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Manager;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Exception\UsersManagerException;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerRegistry;

final class UsersManagerRegistryTest extends TestCase
{
    public function testAddAndGetManagers(): void
    {
        $registry = new UsersManagerRegistry();
        $manager1 = $this->createMock(UsersManagerInterface::class);
        $manager2 = $this->createMock(UsersManagerInterface::class);

        $registry->addManager('provider1', $manager1);
        $registry->addManager('provider2', $manager2);

        $managers = $registry->getManagers();
        self::assertCount(2, $managers);
        self::assertSame($manager1, $managers['provider1']);
        self::assertSame($manager2, $managers['provider2']);
    }

    public function testHasProvider(): void
    {
        $registry = new UsersManagerRegistry();
        $manager  = $this->createMock(UsersManagerInterface::class);

        $registry->addManager('provider1', $manager);

        self::assertTrue($registry->hasProvider('provider1'));
        self::assertFalse($registry->hasProvider('non_existent'));
    }

    public function testGetManagerWithoutSpecifyingProvider(): void
    {
        $registry = new UsersManagerRegistry();
        $manager  = $this->createMock(UsersManagerInterface::class);

        $registry->addManager('provider1', $manager);

        self::assertSame($manager, $registry->getManager());
        self::assertSame($manager, $registry->getManager('provider1'));
    }

    public function testGetManagerThrowsExceptionIfProviderMustBeSpecified(): void
    {
        $registry = new UsersManagerRegistry();
        $manager1 = $this->createMock(UsersManagerInterface::class);
        $manager2 = $this->createMock(UsersManagerInterface::class);

        $registry->addManager('provider1', $manager1);
        $registry->addManager('provider2', $manager2);

        $this->expectException(UsersManagerException::class);
        // The message should contain information about available providers
        $this->expectExceptionMessage('Currently there are "2" providers configured');

        $registry->getManager();
    }

    public function testGetManagerThrowsExceptionIfManagerNotFound(): void
    {
        $registry = new UsersManagerRegistry();
        $manager  = $this->createMock(UsersManagerInterface::class);

        $registry->addManager('provider1', $manager);

        $this->expectException(UsersManagerException::class);
        $this->expectExceptionMessage('The users manager for provider "non_existent" you are looking for doesn\'t exist');

        $registry->getManager('non_existent');
    }
}
