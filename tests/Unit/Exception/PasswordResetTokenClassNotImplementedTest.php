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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenClassNotImplemented;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenException;

final class PasswordResetTokenClassNotImplementedTest extends TestCase
{
    public function testConstructor(): void
    {
        $class     = 'NonExistentClass';
        $exception = new PasswordResetTokenClassNotImplemented($class);

        $this->assertInstanceOf(PasswordResetTokenException::class, $exception);
        $this->assertSame("The entity class NonExistentClass doesn't exist.", $exception->getMessage());
    }
}
