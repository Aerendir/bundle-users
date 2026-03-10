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

namespace SerendipityHQ\Bundle\UsersBundle\Util;

use SerendipityHQ\Bundle\UsersBundle\Model\ResetPasswordTokenComponents;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 * @author Adamo Crespi <aerendir@serendipityhq.com>
 *
 * @internal
 */
interface PasswordResetTokenGeneratorInterface
{
    /**
     * Get a cryptographically secure token with it's non-hashed components.
     *
     * @param UserInterface $user     Unique user identifier
     * @param string|null   $verifier Only required for token comparison
     */
    public function createToken(\DateTimeInterface $expiresAt, UserInterface $user, ?string $verifier = null): ResetPasswordTokenComponents;
}
