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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Manager;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Manager\PasswordManager;

final class PasswordManagerTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(PasswordManager::class));
    }
}
