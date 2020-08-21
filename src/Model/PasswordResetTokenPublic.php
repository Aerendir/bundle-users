<?php

/*
 * This file is part of the Serendipity HQ Users Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\UsersBundle\Model;

final class PasswordResetTokenPublic
{
    /** Selector + non-hashed verifier token */
    private string $publicToken;

    private int $lifetime;

    private \DateTimeInterface $expiresAt;

    public function __construct(string $publicToken, \DateTimeInterface $expiresAt, int $lifetime)
    {
        $this->publicToken = $publicToken;
        $this->expiresAt   = $expiresAt;
        $this->lifetime    = $lifetime;
    }

    /**
     * Returns the full token the user should use.
     *
     * Internally, this consists of two parts - the selector and
     * the hashed token - but that's an implementation detail
     * of how the token will later be parsed.
     */
    public function getPublicToken(): string
    {
        return $this->publicToken;
    }

    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * @return int
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }
}
