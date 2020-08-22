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

namespace SerendipityHQ\Bundle\UsersBundle\Manager;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\Event\PasswordResetTokenCreatedEvent;
use SerendipityHQ\Bundle\UsersBundle\Event\PasswordResetTokenCreationFailedEvent;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetException;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenException;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenExpired;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenInvalid;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenTooMuchFastRequests;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenTooMuchStillActive;
use SerendipityHQ\Bundle\UsersBundle\Helper\PasswordHelper;
use SerendipityHQ\Bundle\UsersBundle\Helper\PasswordResetHelper;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\PasswordResetTokenInterface;
use SerendipityHQ\Bundle\UsersBundle\Model\ResetPasswordTokenComponents;
use SerendipityHQ\Bundle\UsersBundle\Repository\PasswordResetTokenRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

final class PasswordManager
{
    private int $passResetThrottlingMaxActiveTokens;
    private int $passResetThrottlingMinTimeBetweenTokens;
    private int $passResetLifespanAmountOf;
    private string $passResetLifespanUnit;
    private string $passResetTokenClass;
    private string $secUserClass;
    private string $secUserProperty;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;
    private PasswordHelper $passwordHelper;
    private PasswordResetHelper $passwordResetHelper;
    private PasswordResetTokenRepository $passwordResetTokenRepository;

    public function __construct(
        int $passResetThrottlingMaxActiveTokens,
        int $passResetThrottlingMinTimeBetweenTokens,
        int $passResetLifespanAmountOf,
        string $passResetLifespanUnit,
        string $passResetTokenClass,
        string $secUserClass,
        string $secUserProperty,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        PasswordHelper $passwordHelper,
        PasswordResetHelper $passwordResetHelper
    ) {
        $this->passResetThrottlingMaxActiveTokens      = $passResetThrottlingMaxActiveTokens;
        $this->passResetThrottlingMinTimeBetweenTokens = $passResetThrottlingMinTimeBetweenTokens;
        $this->passResetLifespanAmountOf               = $passResetLifespanAmountOf;
        $this->passResetLifespanUnit                   = $passResetLifespanUnit;
        $this->passResetTokenClass                     = $passResetTokenClass;
        $this->secUserClass                            = $secUserClass;
        $this->secUserProperty                         = $secUserProperty;
        $this->entityManager                           = $entityManager;
        $this->eventDispatcher                         = $eventDispatcher;
        $this->passwordHelper                          = $passwordHelper;
        $this->passwordResetHelper                     = $passwordResetHelper;

        $passwordResetTokenRepository = $entityManager->getRepository($passResetTokenClass);
        if ( ! $passwordResetTokenRepository instanceof PasswordResetTokenRepository) {
            throw new PasswordResetTokenException('Wrong PasswordResetTokenRepository.');
        }

        $this->passwordResetTokenRepository = $passwordResetTokenRepository;
    }

    /**
     * @return PasswordHelper
     */
    public function getPasswordHelper(): PasswordHelper
    {
        return $this->passwordHelper;
    }

    /**
     * @return PasswordResetHelper
     */
    public function getPasswordResetHelper(): PasswordResetHelper
    {
        return $this->passwordResetHelper;
    }

    public function handleResetRequest(Request $request, FormInterface $form): bool
    {
        $userPropertyValue = $form->get($this->secUserProperty)->getData();
        $user              = $this->entityManager->getRepository($this->secUserClass)->findOneBy([$this->secUserProperty => $userPropertyValue]);

        if ( ! $user instanceof UserInterface) {
            return false;
        }

        $this->checkThrottling($user);

        /** @var PasswordResetTokenInterface $resetToken */
        $resetToken = new $this->passResetTokenClass($user);
        try {
            $publicResetToken = $this->passwordResetHelper->activateResetToken($resetToken);
        } catch (PasswordResetException $passwordResetException) {
            $event = new PasswordResetTokenCreationFailedEvent($passwordResetException);
            $this->eventDispatcher->dispatch($event);

            return false;
        }
        $this->entityManager->persist($resetToken);
        // Marks that the current user is allowed to see the user_password_reset_check_email page.
        $this->getPasswordResetHelper()->allowAccessToPageCheckYourEmail($request);
        $event = new PasswordResetTokenCreatedEvent($resetToken->getUser(), $publicResetToken);
        $this->eventDispatcher->dispatch($event);

        return true;
    }

    public function handleReset(string $publicToken, HasPlainPasswordInterface $user, FormInterface $form, Request $request): void
    {
        // A password reset token should be used only once, remove it.
        $this->deleteUsedToken($publicToken);

        // The session is cleaned up after the password has been changed.
        $this->getPasswordResetHelper()->cleanSessionAfterReset($request);

        $plainPassword = $form->get(HasPlainPasswordInterface::FIELD_PLAIN_PASSWORD)->getData();
        $user->setPlainPassword($plainPassword);
    }

    public function findUserByPublicToken(string $publicToken): UserInterface
    {
        $token = $this->loadTokenFromPublicOne($publicToken);

        $this->validateToken($publicToken, $token);

        return $token->getUser();
    }

    public function loadTokenFromPublicOne(string $publicToken): PasswordResetTokenInterface
    {
        if (40 !== \strlen($publicToken)) {
            throw new PasswordResetTokenInvalid();
        }

        $selector = ResetPasswordTokenComponents::extractSelectorFromPublicToken($publicToken);

        $token = $this->passwordResetTokenRepository->findBySelector($selector);

        if (null === $token) {
            throw new PasswordResetTokenInvalid();
        }

        return $token;
    }

    public function deleteUsedToken(string $publicToken): void
    {
        $token = $this->loadTokenFromPublicOne($publicToken);
        $this->entityManager->remove($token);
    }

    public function validateToken(string $publicToken, PasswordResetTokenInterface $token): void
    {
        if ($token->isExpired()) {
            throw new PasswordResetTokenExpired();
        }

        $user     = $token->getUser();
        $verifier = ResetPasswordTokenComponents::extractVerifierFromPublicToken($publicToken);

        $hashedVerifierToken = $this->getPasswordResetHelper()->getPasswordResetTokenGenerator()->createToken(
            $token->getExpiresAt(), $user, $verifier
        );

        if (false === \hash_equals($token->getHashedToken(), $hashedVerifierToken->getHashedToken())) {
            throw new PasswordResetTokenInvalid();
        }
    }

    public function cleanExpiredTokens(): int
    {
        return $this->passwordResetTokenRepository->removeExpiredResetPasswordRequests($this->passResetLifespanAmountOf, $this->passResetLifespanUnit);
    }

    public function removeResetToken(PasswordResetTokenInterface $resetToken): void
    {
        $this->entityManager->remove($resetToken);
    }

    private function checkThrottling(UserInterface $user): ?\DateTimeInterface
    {
        $tokens = $this->passwordResetTokenRepository->getTokensStillValid($user);

        if (null === $tokens) {
            return null;
        }

        if (\count($tokens) >= $this->passResetThrottlingMaxActiveTokens) {
            throw new PasswordResetTokenTooMuchStillActive();
        }

        $lastToken = \end($tokens);

        if ( ! $lastToken instanceof PasswordResetTokenInterface) {
            return null;
        }

        $now  = new Carbon();
        $diff = $now->diffInSeconds($lastToken->getRequestedAt());

        if ($this->passResetThrottlingMinTimeBetweenTokens > $diff) {
            // Do not specify how much time the user has to wait before
            // the next request to avoid disclosing sensitive information.
            throw new PasswordResetTokenTooMuchFastRequests();
        }

        return null;
    }
}
