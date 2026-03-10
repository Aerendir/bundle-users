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
use SerendipityHQ\Bundle\UsersBundle\Event\PasswordResetTokenCreatedEvent;
use SerendipityHQ\Bundle\UsersBundle\Model\PasswordResetTokenPublic;
use Symfony\Component\Security\Core\User\UserInterface;

final class PasswordResetTokenCreatedEventTest extends TestCase
{
    public function testPasswordResetTokenCreatedEvent(): void
    {
        $user  = $this->createMock(UserInterface::class);
        $token = new PasswordResetTokenPublic('token_string', new \DateTime('+1 hour'), 3600);
        $event = new PasswordResetTokenCreatedEvent($user, $token);

        $this->assertSame($user, $event->getUser());
        $this->assertSame($token, $event->getToken());
    }
}
