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
use SerendipityHQ\Bundle\UsersBundle\Command\Arguments\UniqueCommandArgument;
use Symfony\Component\Console\Input\InputInterface;

final class UniqueCommandArgumentTest extends TestCase
{
    public function testGetArgumentUniqueOrNullReturnsString(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('unique')->willReturn('unique_value');

        $trait = new class {
            use UniqueCommandArgument {
                getArgumentUniqueOrNull as public;
            }
        };

        self::assertSame('unique_value', $trait->getArgumentUniqueOrNull($input));
    }

    public function testGetArgumentUniqueOrNullReturnsNull(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('unique')->willReturn(null);

        $trait = new class {
            use UniqueCommandArgument {
                getArgumentUniqueOrNull as public;
            }
        };

        self::assertNull($trait->getArgumentUniqueOrNull($input));
    }

    public function testGetArgumentUniqueOrNullThrowsExceptionOnInvalidType(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('unique')->willReturn(123);

        $trait = new class {
            use UniqueCommandArgument {
                getArgumentUniqueOrNull as public;
            }
        };

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The unique must be a string. Current type: int.');
        $trait->getArgumentUniqueOrNull($input);
    }

    public function testGetArgumentUniqueReturnsString(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('unique')->willReturn('unique_value');

        $trait = new class {
            use UniqueCommandArgument {
                getArgumentUnique as public;
            }
        };

        self::assertSame('unique_value', $trait->getArgumentUnique($input));
    }

    public function testGetArgumentUniqueThrowsExceptionOnNull(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('unique')->willReturn(null);

        $trait = new class {
            use UniqueCommandArgument {
                getArgumentUnique as public;
            }
        };

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The unique is required.');
        $trait->getArgumentUnique($input);
    }
}
