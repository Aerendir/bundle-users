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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Command\Arguments;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Command\Arguments\RolesCommandArgument;
use Symfony\Component\Console\Input\InputInterface;

final class RolesCommandArgumentTest extends TestCase
{
    public function testGetArgumentRolesOrNullReturnsArray(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('roles')->willReturn(['ROLE_USER']);

        $trait = new class {
            use RolesCommandArgument {
                getArgumentRolesOrNull as public;
            }
        };

        self::assertSame(['ROLE_USER'], $trait->getArgumentRolesOrNull($input));
    }

    public function testGetArgumentRolesOrNullReturnsNull(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('roles')->willReturn(null);

        $trait = new class {
            use RolesCommandArgument {
                getArgumentRolesOrNull as public;
            }
        };

        self::assertNull($trait->getArgumentRolesOrNull($input));
    }

    public function testGetArgumentRolesReturnsArray(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('roles')->willReturn(['ROLE_USER']);

        $trait = new class {
            use RolesCommandArgument {
                getArgumentRoles as public;
            }
        };

        self::assertSame(['ROLE_USER'], $trait->getArgumentRoles($input));
    }

    public function testGetArgumentRolesThrowsExceptionOnNull(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('roles')->willReturn(null);

        $trait = new class {
            use RolesCommandArgument {
                getArgumentRoles as public;
            }
        };

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The roles is required.');
        $trait->getArgumentRoles($input);
    }

    public function testGetArgumentRolesOrNullThrowsExceptionOnInvalidElement(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('roles')->willReturn(['ROLE_USER', 123]);

        $trait = new class {
            use RolesCommandArgument {
                getArgumentRolesOrNull as public;
            }
        };

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Each role must be a string. Current type: int.');
        $trait->getArgumentRolesOrNull($input);
    }

    public function testGetArgumentRolesOrNullThrowsExceptionOnInvalidRole(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('roles')->willReturn(['invalid_role']);

        $trait = new class {
            use RolesCommandArgument {
                getArgumentRolesOrNull as public;
            }
        };

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Role "invalid_role": Role name can contain only UPPERCASE LETTERS, numbers and underscores (ex.: ROLE_ADMIN). Must start with "ROLE_" (ex.: ROLE_ADMIN).');
        $trait->getArgumentRolesOrNull($input);
    }
}
