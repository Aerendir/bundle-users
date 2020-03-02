<?php

declare(strict_types=1);

/*
 * This file is part of SHQUsersBundle.
 *
 * (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\UsersBundle\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched when a user is created but not still persisted (nor flushed).
 */
class UserCreatedEvent extends Event
{
    /** @var \Symfony\Component\Security\Core\User\UserInterface $user */
    private $user;

    /** @var string $provider */
    private $provider;

    /**
     * @param UserInterface $user
     * @param string|null   $provider
     */
    public function __construct(UserInterface $user, string $provider)
    {
        $this->user     = $user;
        $this->provider = $provider;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }
}
