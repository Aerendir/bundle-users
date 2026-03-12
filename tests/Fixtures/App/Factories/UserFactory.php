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

use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return User::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'email'    => self::faker()->unique()->safeEmail(),
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'active'   => true,
            'roles'    => [],
        ];
    }
}
