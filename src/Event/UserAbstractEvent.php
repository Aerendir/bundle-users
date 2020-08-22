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

namespace SerendipityHQ\Bundle\UsersBundle\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class UserAbstractEvent extends Event
{
    private string $provider;
    private UserInterface $user;

    public function __construct(UserInterface $user, string $provider)
    {
        $this->provider = $provider;
        $this->user     = $user;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
