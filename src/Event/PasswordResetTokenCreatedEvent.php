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

use SerendipityHQ\Bundle\UsersBundle\Model\PasswordResetTokenPublic;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched when the creation of the token to reset the password failed.
 */
final class PasswordResetTokenCreatedEvent extends Event
{
    private PasswordResetTokenPublic $token;

    private UserInterface $user;

    public function __construct(UserInterface $user, PasswordResetTokenPublic $token)
    {
        $this->token = $token;
        $this->user  = $user;
    }

    public function getToken(): PasswordResetTokenPublic
    {
        return $this->token;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
