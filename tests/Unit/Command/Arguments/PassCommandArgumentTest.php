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
use SerendipityHQ\Bundle\UsersBundle\Command\Arguments\PassCommandArgument;
use Symfony\Component\Console\Input\InputInterface;

final class PassCommandArgumentTest extends TestCase
{
    private object $traitUser;

    protected function setUp(): void
    {
        $this->traitUser = new class {
            use PassCommandArgument {
                getArgumentPassOrNull as public;
                getArgumentPass as public;
                ensurePassIsString as public;
            }
        };
    }

    public function testGetArgumentPassOrNullWithValidString(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('pass')->willReturn('my-password');

        self::assertSame('my-password', $this->traitUser->getArgumentPassOrNull($input));
    }

    public function testGetArgumentPassOrNullWithNull(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('pass')->willReturn(null);

        self::assertNull($this->traitUser->getArgumentPassOrNull($input));
    }

    public function testGetArgumentPassOrNullWithInvalidType(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('pass')->willReturn(123);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The pass must be a string. Current type: int.');

        $this->traitUser->getArgumentPassOrNull($input);
    }

    public function testGetArgumentPassWithValidString(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('pass')->willReturn('my-password');

        self::assertSame('my-password', $this->traitUser->getArgumentPass($input));
    }

    public function testGetArgumentPassWithNullThrowsException(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('pass')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The pass is required.');

        $this->traitUser->getArgumentPass($input);
    }

    public function testEnsurePassIsStringWithValidString(): void
    {
        $this->traitUser->ensurePassIsString('my-password');
        $this->assertTrue(true); // Should not throw exception
    }

    public function testEnsurePassIsStringWithInvalidTypeScalar(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The pass "123" must be a string.');

        $this->traitUser->ensurePassIsString(123);
    }

    public function testEnsurePassIsStringWithInvalidTypeNonScalar(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The pass "stdClass" must be a string.');

        $this->traitUser->ensurePassIsString(new \stdClass());
    }
}
