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
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordEncodingError;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordException;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordRequired;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetException;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetRequestException;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenException;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenExpired;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenInvalid;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenTooMuchFastRequests;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenTooMuchStillActive;
use SerendipityHQ\Bundle\UsersBundle\Exception\RolesException;

final class SimpleExceptionsTest extends TestCase
{
    /**
     * @dataProvider provideSimpleExceptions
     */
    public function testSimpleExceptions(string $exceptionClass, string $parentClass): void
    {
        $exception = new $exceptionClass();
        $this->assertInstanceOf($exceptionClass, $exception);
        $this->assertInstanceOf($parentClass, $exception);
    }

    /**
     * @return array<array<string>>
     */
    public static function provideSimpleExceptions(): array
    {
        return [
            [PasswordException::class, \Exception::class],
            [PasswordEncodingError::class, PasswordException::class],
            [PasswordRequired::class, PasswordException::class],
            [PasswordResetException::class, PasswordException::class],
            [PasswordResetRequestException::class, PasswordResetException::class],
            [PasswordResetTokenException::class, PasswordResetException::class],
            [PasswordResetTokenExpired::class, PasswordResetTokenException::class],
            [PasswordResetTokenInvalid::class, PasswordResetTokenException::class],
            [PasswordResetTokenTooMuchFastRequests::class, PasswordResetRequestException::class],
            [PasswordResetTokenTooMuchStillActive::class, PasswordResetRequestException::class],
            [RolesException::class, \Exception::class],
        ];
    }
}
