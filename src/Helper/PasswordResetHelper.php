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

namespace SerendipityHQ\Bundle\UsersBundle\Helper;

use SerendipityHQ\Bundle\UsersBundle\Model\PasswordResetTokenPublic;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\PasswordResetTokenInterface;
use SerendipityHQ\Bundle\UsersBundle\Repository\PasswordResetTokenRepository;
use SerendipityHQ\Bundle\UsersBundle\Util\PasswordResetTokenGenerator;
use Symfony\Component\HttpFoundation\Request;

final class PasswordResetHelper
{
    public const RESET_PASSWORD_1_CHECK_EMAIL = 'reset_password_1_check_email';

    public const RESET_PASSWORD_2_PUBLIC_TOKEN = 'reset_password_2_public_token';

    /**
     * @var int The life time of a token (in seconds)
     *
     * @todo Make configurable
     */
    public const RESET_TOKEN_LIFETIME = 3600;

    private PasswordResetTokenRepository $repository;

    private PasswordResetTokenGenerator $passwordResetTokenGenerator;

    public function __construct(PasswordResetTokenRepository $repository, PasswordResetTokenGenerator $passwordResetTokenGenerator)
    {
        $this->repository                  = $repository;
        $this->passwordResetTokenGenerator = $passwordResetTokenGenerator;
    }

    /**
     * @return PasswordResetTokenGenerator
     */
    public function getPasswordResetTokenGenerator(): PasswordResetTokenGenerator
    {
        return $this->passwordResetTokenGenerator;
    }

    public function allowAccessToPageCheckYourEmail(Request $request): void
    {
        $request->getSession()->set(self::RESET_PASSWORD_1_CHECK_EMAIL, true);
    }

    public function canAccessPageCheckYourEmail(Request $request): bool
    {
        return $request->getSession()->has(self::RESET_PASSWORD_1_CHECK_EMAIL);
    }

    public function storeTokenInSession(Request $request, string $token): void
    {
        $request->getSession()->set(self::RESET_PASSWORD_2_PUBLIC_TOKEN, $token);
    }

    public function getTokenFromSession(Request $request): string
    {
        return $request->getSession()->get(self::RESET_PASSWORD_2_PUBLIC_TOKEN);
    }

    public function cleanSessionAfterReset(Request $request): void
    {
        $request->getSession()->remove(self::RESET_PASSWORD_1_CHECK_EMAIL);
        $request->getSession()->remove(self::RESET_PASSWORD_2_PUBLIC_TOKEN);
    }

    /**
     * Some of the cryptographic strategies were taken from
     * https://paragonie.com/blog/2017/02/split-tokens-token-based-authentication-protocols-without-side-channels.
     */
    public function activateResetToken(PasswordResetTokenInterface $resetToken): PasswordResetTokenPublic
    {
        // @todo use Carbon
        $expiresAt = new \DateTimeImmutable(\Safe\sprintf('+%d seconds', self::RESET_TOKEN_LIFETIME));

        $tokenComponents = $this->passwordResetTokenGenerator->createToken($expiresAt, $resetToken->getUser());

        $resetToken->activate($expiresAt, $tokenComponents->getSelector(), $tokenComponents->getHashedToken());

        // final "public" token is the selector + non-hashed verifier token
        return new PasswordResetTokenPublic(
            $tokenComponents->getPublicToken(),
            $expiresAt,
            self::RESET_TOKEN_LIFETIME
        );
    }
}
