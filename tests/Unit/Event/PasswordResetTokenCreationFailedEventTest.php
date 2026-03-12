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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Event;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Event\PasswordResetTokenCreationFailedEvent;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetException;

final class PasswordResetTokenCreationFailedEventTest extends TestCase
{
    public function testPasswordResetTokenCreationFailedEvent(): void
    {
        $exception = new PasswordResetException('Error during password reset');
        $event     = new PasswordResetTokenCreationFailedEvent($exception);

        $this->assertSame($exception, $event->getThrowable());
    }
}
