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

use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetException;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched when the creation of the token to reset the password failed.
 */
final class PasswordResetTokenCreationFailedEvent extends Event
{
    private PasswordResetException $resetPasswordException;

    public function __construct(PasswordResetException $resetPasswordException)
    {
        $this->resetPasswordException = $resetPasswordException;
    }

    /**
     * @return PasswordResetException
     */
    public function getThrowable(): PasswordResetException
    {
        return $this->resetPasswordException;
    }
}
