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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Command\Options;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Command\Options\ProviderCommandOption;
use Symfony\Component\Console\Input\InputInterface;

final class ProviderCommandOptionTest extends TestCase
{
    public function testGetOptionProviderOrNullReturnsString(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')->with('provider')->willReturn('provider_value');

        $trait = new class {
            use ProviderCommandOption {
                getOptionProviderOrNull as public;
            }
        };

        self::assertSame('provider_value', $trait->getOptionProviderOrNull($input));
    }

    public function testGetOptionProviderOrNullReturnsNull(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')->with('provider')->willReturn(null);

        $trait = new class {
            use ProviderCommandOption {
                getOptionProviderOrNull as public;
            }
        };

        self::assertNull($trait->getOptionProviderOrNull($input));
    }

    public function testGetOptionProviderOrNullThrowsExceptionOnInvalidType(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')->with('provider')->willReturn(123);

        $trait = new class {
            use ProviderCommandOption {
                getOptionProviderOrNull as public;
            }
        };

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The provider must be a string. Current type: int.');
        $trait->getOptionProviderOrNull($input);
    }

    public function testGetOptionProviderReturnsString(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')->with('provider')->willReturn('provider_value');

        $trait = new class {
            use ProviderCommandOption {
                getOptionProvider as public;
            }
        };

        self::assertSame('provider_value', $trait->getOptionProvider($input));
    }

    public function testGetOptionProviderThrowsExceptionOnNull(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getOption')->with('provider')->willReturn(null);

        $trait = new class {
            use ProviderCommandOption {
                getOptionProvider as public;
            }
        };

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The provider is required.');
        $trait->getOptionProvider($input);
    }
}
