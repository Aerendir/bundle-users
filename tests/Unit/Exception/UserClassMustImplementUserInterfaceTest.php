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
use SerendipityHQ\Bundle\UsersBundle\Exception\UserClassMustImplementInterface;
use SerendipityHQ\Bundle\UsersBundle\Exception\UserClassMustImplementUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserClassMustImplementUserInterfaceTest extends TestCase
{
    public function testConstructor(): void
    {
        $user      = new \stdClass();
        $exception = new UserClassMustImplementUserInterface($user);

        $this->assertInstanceOf(UserClassMustImplementInterface::class, $exception);
        $this->assertSame(sprintf('The User class "%s" MUST implement the "%s" interface.', \stdClass::class, UserInterface::class), $exception->getMessage());
    }
}
