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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Factories;

use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity\PasswordResetToken;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<PasswordResetToken>
 */
final class PasswordResetTokenFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return PasswordResetToken::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'user'        => UserFactory::new(),
            'selector'    => self::faker()->lexify('????????????????????'),
            'hashedToken' => self::faker()->sha256(),
            'requestedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'expiresAt'   => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('+1 hour', '+2 hours')),
        ];
    }
}
