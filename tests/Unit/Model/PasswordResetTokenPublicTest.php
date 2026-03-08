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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Model\PasswordResetTokenPublic;

final class PasswordResetTokenPublicTest extends TestCase
{
    public function testPasswordResetTokenPublic(): void
    {
        $publicToken = 'selectorverifier';
        $expiresAt   = new \DateTimeImmutable('+1 hour');
        $lifetime    = 3600;

        $tokenPublic = new PasswordResetTokenPublic($publicToken, $expiresAt, $lifetime);

        $this->assertSame($publicToken, $tokenPublic->getPublicToken());
        $this->assertSame($expiresAt, $tokenPublic->getExpiresAt());
        $this->assertSame($lifetime, $tokenPublic->getLifetime());
    }
}
