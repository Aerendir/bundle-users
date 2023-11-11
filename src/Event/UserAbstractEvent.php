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
    public function __construct(
        private readonly UserInterface $user,
        /** @var string|null May be null when extended by custom events in implementing apps: they may not need to specify the provider. */
        private readonly ?string $provider = null
    ) {
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
