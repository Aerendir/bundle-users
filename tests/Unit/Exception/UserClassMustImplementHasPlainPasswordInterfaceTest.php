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
use SerendipityHQ\Bundle\UsersBundle\Exception\UserClassMustImplementHasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Exception\UserClassMustImplementInterface;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;

final class UserClassMustImplementHasPlainPasswordInterfaceTest extends TestCase
{
    public function testConstructor(): void
    {
        $user      = new \stdClass();
        $exception = new UserClassMustImplementHasPlainPasswordInterface($user);

        $this->assertInstanceOf(UserClassMustImplementInterface::class, $exception);
        $this->assertSame(sprintf('The User class "%s" MUST implement the "%s" interface.', \stdClass::class, HasPlainPasswordInterface::class), $exception->getMessage());
    }
}
