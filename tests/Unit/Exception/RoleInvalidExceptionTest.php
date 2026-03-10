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
use SerendipityHQ\Bundle\UsersBundle\Exception\RoleInvalidException;
use SerendipityHQ\Bundle\UsersBundle\Exception\RolesException;

final class RoleInvalidExceptionTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $role      = 'ROLE_INVALID';
        $errors    = ['field' => ['error']];
        $exception = new RoleInvalidException($role, $errors);

        $this->assertInstanceOf(RolesException::class, $exception);
        $this->assertSame($role, $exception->getRole());
        $this->assertSame($errors, $exception->getErrors());
    }
}
