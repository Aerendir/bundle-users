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

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\Event\UserCreatedEvent;
use SerendipityHQ\Bundle\UsersBundle\Manager\Exception\UsersManagerException;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * {@inheritdoc}
 */
final class UsersManager implements UsersManagerInterface
{
    /** @var string $provider */
    private $provider;

    /** @var string $userClass */
    private $userClass;

    /** @var string $uniqueProperty */
    private $uniqueProperty;

    /** @var EventDispatcherInterface $dispatcher */
    private $dispatcher;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var \Symfony\Component\PropertyAccess\PropertyAccessor $propertyAccessor */
    private $propertyAccessor;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $provider, string $userClass, string $uniqueProperty, EventDispatcherInterface $dispatcher, EntityManagerInterface $entityManager, PropertyAccessor $propertyAccessor)
    {
        $this->provider         = $provider;
        $this->userClass        = $userClass;
        $this->uniqueProperty   = $uniqueProperty;
        $this->dispatcher       = $dispatcher;
        $this->entityManager    = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $unique, string $pass): UserInterface
    {
        /** @var UserInterface $user */
        $user = new $this->userClass();
        $this->propertyAccessor->setValue($user, $this->uniqueProperty, $unique);

        try {
            $this->propertyAccessor->setValue($user, HasPlainPasswordInterface::FIELD_PLAIN_PASSWORD, $pass);
        } catch (\Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException $noSuchPropertyException) {
            $toThrow = $noSuchPropertyException;

            if (false !== \strpos($noSuchPropertyException->getMessage(), HasPlainPasswordInterface::FIELD_PLAIN_PASSWORD)) {
                $toThrow = UsersManagerException::userClassMustImplementHasPlainPasswordInterface($this->userClass);
            }

            throw $toThrow;
        }
        $event = new UserCreatedEvent($user, $this->provider);
        $this->dispatcher->dispatch($event);

        if (false === $event->isPropagationStopped()) {
            $this->entityManager->persist($user);
        }

        return $user;
    }
}
