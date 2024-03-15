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
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\ByteString;

use function Safe\json_encode;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 * @author Adamo Crespi <aerendir@serendipityhq.com>
 *
 * @internal
 */
final class PasswordResetTokenGenerator
{
    public function __construct(private readonly string $appSecret, private readonly string $userIdentifierProperty, private readonly PropertyAccessor $propertyAccessor)
    {
    }

    /**
     * Get a cryptographically secure token with it's non-hashed components.
     *
     * @param UserInterface $user     Unique user identifier
     * @param string|null   $verifier Only required for token comparison
     *
     * @todo The user was originally UserId: currently the Id is a field that may be not present.
     */
    public function createToken(\DateTimeInterface $expiresAt, UserInterface $user, ?string $verifier = null): ResetPasswordTokenComponents
    {
        if (null === $verifier) {
            $verifier = ByteString::fromRandom(ResetPasswordTokenComponents::TOKEN_VERIFIER_LENGTH);
        }

        $selector    = ByteString::fromRandom(ResetPasswordTokenComponents::TOKEN_VERIFIER_LENGTH);
        $hashedToken = $this->generateHashedToken($expiresAt, $user, $verifier);

        return new ResetPasswordTokenComponents($selector, $verifier, $hashedToken);
    }

    private function generateHashedToken(\DateTimeInterface $expiresAt, UserInterface $user, string $verifier): string
    {
        $userIdentifier = $this->propertyAccessor->getValue($user, $this->userIdentifierProperty);
        $encodedData    = json_encode([$verifier, $userIdentifier, $expiresAt->getTimestamp()], JSON_THROW_ON_ERROR);
        $hashed         = \hash_hmac('sha256', $encodedData, $this->appSecret, true);

        if (false === $hashed) {
            throw new \RuntimeException('Algo is unknown or is a non-cryptographic hash function.');
        }

        return \base64_encode($hashed);
    }
}
