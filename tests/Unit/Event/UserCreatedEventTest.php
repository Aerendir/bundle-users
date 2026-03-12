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
use SerendipityHQ\Bundle\UsersBundle\Event\UserCreatedEvent;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserCreatedEventTest extends TestCase
{
    public function testUserCreatedEvent(): void
    {
        $user     = $this->createMock(UserInterface::class);
        $provider = 'main_provider';
        $event    = new UserCreatedEvent($user, $provider);

        $this->assertSame($user, $event->getUser());
        $this->assertSame($provider, $event->getProvider());
    }

    public function testUserCreatedEventWithNullProvider(): void
    {
        $user  = $this->createMock(UserInterface::class);
        $event = new UserCreatedEvent($user);

        $this->assertSame($user, $event->getUser());
        $this->assertNull($event->getProvider());
    }
}
