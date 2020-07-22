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

use Doctrine\ORM\Event\PreFlushEventArgs;
use Safe\Exceptions\StringsException;
use SerendipityHQ\Bundle\UsersBundle\Manager\Exception\UsersManagerException;
use SerendipityHQ\Bundle\UsersBundle\Property\HasPlainPasswordInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Encodes the password of the user if required.
 *
 * This is implemented as a lazy listener, so the overhed on the app is practically null.
 *
 * @see https://symfony.com/doc/current/doctrine/events.html
 */
class UserEncodePasswordListener
{
    /** @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory */
    private $encoderFactory;

    /**
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param UserInterface     $user
     * @param PreFlushEventArgs $event
     *
     * @throws UsersManagerException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws StringsException
     * @throws BadCredentialsException
     */
    public function preFlush(UserInterface $user): void
    {
        if ( ! $user instanceof HasPlainPasswordInterface) {
            throw UsersManagerException::userClassMustImplementHasPlainPasswordInterface($user->getUsername());
        }

        if (null === $user->getPlainPassword()) {
            return;
        }

        $encoder         = $this->encoderFactory->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());

        if (false === $encoder->isPasswordValid($encodedPassword, $user->getPlainPassword(), $user->getSalt())) {
            throw UsersManagerException::errorInEncodingThePassword($user->getUsername());
        }

        $user->setPassword($encodedPassword);
    }
}
