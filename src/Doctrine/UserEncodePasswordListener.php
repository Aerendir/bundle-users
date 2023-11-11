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

namespace SerendipityHQ\Bundle\UsersBundle\Doctrine;

use SerendipityHQ\Bundle\UsersBundle\Exception\UserClassMustImplementHasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Manager\PasswordManager;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Encodes the password of the user if required.
 *
 * This is implemented as a lazy listener, so the overhead on the app is practically null.
 *
 * @see https://symfony.com/doc/current/doctrine/events.html
 */
final class UserEncodePasswordListener
{
    public function __construct(private readonly PasswordManager $passwordManager)
    {
    }

    public function preFlush(UserInterface $user): void
    {
        if ( ! $user instanceof HasPlainPasswordInterface) {
            throw new UserClassMustImplementHasPlainPasswordInterface($user);
        }

        if (null === $user->getPlainPassword()) {
            // Here we do not throw any exception: maybe other fields of the
            // UserInterface were changed, so we don't care about the password.
            return;
        }

        $encodedPassword = $this->passwordManager->getPasswordHelper()->encodePlainPassword($user);

        $user->setPassword($encodedPassword);
    }
}
