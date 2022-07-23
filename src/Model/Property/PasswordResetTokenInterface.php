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

namespace SerendipityHQ\Bundle\UsersBundle\Model\Property;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 * @author Adamo Aerendir Crespi <aerendir@serendipityhq.com>
 */
interface PasswordResetTokenInterface
{
    public function __construct(UserInterface $user);

    /**
     * Get the time the reset password request was created.
     */
    public function getRequestedAt(): \DateTimeInterface;

    /**
     * Check if the reset password request is expired.
     */
    public function isExpired(): bool;

    /**
     * Get the time the reset password request expires.
     */
    public function getExpiresAt(): \DateTimeInterface;

    /**
     * Get the non-public hashed token used to verify a request password request.
     */
    public function getHashedToken(): string;

    /**
     * Get the user who requested a password reset.
     */
    public function getUser(): UserInterface;

    /**
     * Actually initialize the token.
     */
    public function activate(\DateTimeImmutable $expiresAt, string $selector, string $hashedToken);
}
