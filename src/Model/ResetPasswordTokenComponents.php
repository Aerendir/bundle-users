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

namespace SerendipityHQ\Bundle\UsersBundle\Model;

use function Safe\substr;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 * @author Adamo Crespi <aerendir@serendipityhq.com>
 *
 * @internal
 */
final class ResetPasswordTokenComponents
{
    /** @var int */
    public const TOKEN_SELECTOR_LENGTH = 20;

    /** @var int */
    public const TOKEN_VERIFIER_LENGTH = 20;

    public function __construct(
        /** @var string Non-hashed random string used to fetch request objects from persistence */
        private readonly string $selector,
        private readonly string $verifier,
        /** @var string The hashed non-public token used to validate reset password requests */
        private readonly string $hashedToken
    ) {
    }

    public static function extractSelectorFromPublicToken(string $publicToken): string
    {
        return substr($publicToken, 0, self::TOKEN_SELECTOR_LENGTH);
    }

    public static function extractVerifierFromPublicToken(string $publicToken): string
    {
        return substr($publicToken, self::TOKEN_SELECTOR_LENGTH, self::TOKEN_VERIFIER_LENGTH);
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }

    /**
     * The public token consists of a concatenated random non-hashed
     * selector string and random non-hashed verifier string.
     */
    public function getPublicToken(): string
    {
        return $this->selector . $this->verifier;
    }
}
