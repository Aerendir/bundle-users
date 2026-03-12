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
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenClassMustImplementPasswordResetTokenInterfaceInterface;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenException;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\PasswordResetTokenInterface;

final class PasswordResetTokenClassMustImplementPasswordResetTokenInterfaceInterfaceTest extends TestCase
{
    public function testConstructor(): void
    {
        $class     = 'SomeClass';
        $exception = new PasswordResetTokenClassMustImplementPasswordResetTokenInterfaceInterface($class);

        $this->assertInstanceOf(PasswordResetTokenException::class, $exception);
        $this->assertSame(sprintf('The entity %s MUST implement interface %s.', $class, PasswordResetTokenInterface::class), $exception->getMessage());
    }
}
