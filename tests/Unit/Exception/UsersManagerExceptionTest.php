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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Exception\UsersException;
use SerendipityHQ\Bundle\UsersBundle\Exception\UsersManagerException;

final class UsersManagerExceptionTest extends TestCase
{
    public function testManagerNotFound(): void
    {
        $provider  = 'some_provider';
        $exception = UsersManagerException::managerNotFound($provider);

        $this->assertInstanceOf(UsersManagerException::class, $exception);
        $this->assertInstanceOf(UsersException::class, $exception);
        $this->assertStringContainsString($provider, $exception->getMessage());
    }

    public function testProviderMustBeSpecified(): void
    {
        $providers = ['provider1', 'provider2'];
        $exception = UsersManagerException::providerMustBeSpecified($providers);

        $this->assertInstanceOf(UsersManagerException::class, $exception);
        $this->assertStringContainsString('2', $exception->getMessage());
        $this->assertStringContainsString('provider1, provider2', $exception->getMessage());
    }
}
