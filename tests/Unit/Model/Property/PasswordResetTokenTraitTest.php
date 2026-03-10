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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Model\Property;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\PasswordResetTokenTrait;

final class PasswordResetTokenTraitTest extends TestCase
{
    public function testPasswordResetTokenTrait(): void
    {
        $mock = new class {
            use PasswordResetTokenTrait;
        };

        $expiresAt   = new \DateTimeImmutable('+1 hour');
        $selector    = 'selector123';
        $hashedToken = 'hashed_token';

        $mock->activate($expiresAt, $selector, $hashedToken);

        $this->assertSame($expiresAt, $mock->getExpiresAt());
        $this->assertSame($hashedToken, $mock->getHashedToken());
        $this->assertInstanceOf(\DateTimeImmutable::class, $mock->getRequestedAt());
        $this->assertFalse($mock->isExpired());
    }

    public function testIsExpired(): void
    {
        $mock = new class {
            use PasswordResetTokenTrait;
        };

        $expiresAt = new \DateTimeImmutable('-1 minute');
        $mock->activate($expiresAt, 'selector', 'hashed');

        $this->assertTrue($mock->isExpired());
    }
}
