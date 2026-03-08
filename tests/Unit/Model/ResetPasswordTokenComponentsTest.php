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
use SerendipityHQ\Bundle\UsersBundle\Model\ResetPasswordTokenComponents;

final class ResetPasswordTokenComponentsTest extends TestCase
{
    public function testResetPasswordTokenComponents(): void
    {
        $selector    = 'selector1234567890123'; // 20 chars
        $verifier    = 'verifier1234567890123'; // 20 chars
        $hashedToken = 'hashed_token';

        $components = new ResetPasswordTokenComponents($selector, $verifier, $hashedToken);

        $this->assertSame($selector, $components->getSelector());
        $this->assertSame($hashedToken, $components->getHashedToken());
        $this->assertSame($selector . $verifier, $components->getPublicToken());
    }

    public function testStaticExtractors(): void
    {
        $selector    = '12345678901234567890';
        $verifier    = 'abcdefghijklmnopqrst';
        $publicToken = $selector . $verifier;

        $this->assertSame($selector, ResetPasswordTokenComponents::extractSelectorFromPublicToken($publicToken));
        $this->assertSame($verifier, ResetPasswordTokenComponents::extractVerifierFromPublicToken($publicToken));
    }
}
