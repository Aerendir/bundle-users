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

final class UsersExceptionTest extends TestCase
{
    public function testGetUserClass(): void
    {
        $exception = new class extends UsersException {
            public function callGetUserClass($user): string
            {
                return $this->getUserClass($user);
            }
        };

        $user = new \stdClass();
        $this->assertSame(\stdClass::class, $exception->callGetUserClass($user));
        $this->assertSame('SomeString', $exception->callGetUserClass('SomeString'));
        $this->assertSame('unknown', $exception->callGetUserClass(123));
    }
}
