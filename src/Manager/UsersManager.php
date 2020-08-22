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
use SerendipityHQ\Bundle\UsersBundle\Event\UserPasswordChangedEvent;
use SerendipityHQ\Bundle\UsersBundle\Event\UserUpdatedEvent;
use SerendipityHQ\Bundle\UsersBundle\Exception\UserClassMustImplementHasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;

final class UsersManager implements UsersManagerInterface
{
    private string $provider;
    private string $secUserClass;
    private string $secUserProperty;
    private EventDispatcherInterface $dispatcher;
    private EntityManagerInterface $entityManager;
    private PropertyAccessor $propertyAccessor;

    public function __construct(string $provider, string $secUserClass, string $secUserProperty, EventDispatcherInterface $dispatcher, EntityManagerInterface $entityManager, PropertyAccessor $propertyAccessor)
    {
        $this->provider         = $provider;
        $this->secUserClass     = $secUserClass;
        $this->secUserProperty  = $secUserProperty;
        $this->dispatcher       = $dispatcher;
        $this->entityManager    = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function create(string $unique, string $pass): UserInterface
    {
        /** @var UserInterface $user */
        $user = new $this->secUserClass();
        $this->propertyAccessor->setValue($user, $this->secUserProperty, $unique);

        try {
            $this->propertyAccessor->setValue($user, HasPlainPasswordInterface::FIELD_PLAIN_PASSWORD, $pass);
        } catch (NoSuchPropertyException $noSuchPropertyException) {
            $toThrow = $noSuchPropertyException;

            if (false !== \strpos($noSuchPropertyException->getMessage(), HasPlainPasswordInterface::FIELD_PLAIN_PASSWORD)) {
                $toThrow = new UserClassMustImplementHasPlainPasswordInterface($this->secUserClass);
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

    public function load(string $primaryProperty): ?UserInterface
    {
        return $this->entityManager->getRepository($this->secUserClass)->findOneBy([$this->secUserProperty => $primaryProperty]);
    }

    public function updated(UserInterface $user): void
    {
        $event = new UserUpdatedEvent($user, $this->provider);
        $this->dispatcher->dispatch($event);
    }

    public function passwordChanged(UserInterface $user): void
    {
        $event = new UserPasswordChangedEvent($user, $this->provider);
        $this->dispatcher->dispatch($event);
    }
}
