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
use SerendipityHQ\Bundle\UsersBundle\Event\UserPasswordChangedEvent;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserPasswordChangedEventTest extends TestCase
{
    public function testUserPasswordChangedEvent(): void
    {
        $user     = $this->createMock(UserInterface::class);
        $provider = 'main_provider';
        $event    = new UserPasswordChangedEvent($user, $provider);

        $this->assertSame($user, $event->getUser());
        $this->assertSame($provider, $event->getProvider());
    }
}
