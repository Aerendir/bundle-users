<?php

declare(strict_types=1);

/*
 * This file is part of SHQUsersBundle.
 *
 * (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\UsersBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Manages Users.
 */
interface UsersManagerInterface
{
    /**
     * @param string                   $provider
     * @param string                   $userClass
     * @param string                   $uniqueProperty
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManagerInterface   $entityManager
     * @param PropertyAccessor         $propertyAccessor
     */
    public function __construct(string $provider, string $userClass, string $uniqueProperty, EventDispatcherInterface $dispatcher, EntityManagerInterface $entityManager, PropertyAccessor $propertyAccessor);

    /**
     * @param string $unique
     * @param string $pass
     *
     * @throws \Symfony\Component\PropertyAccess\Exception\AccessException
     * @throws \Symfony\Component\PropertyAccess\Exception\InvalidArgumentException
     * @throws \Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException
     *
     * @return UserInterface
     */
    public function create(string $unique, string $pass): UserInterface;
}
