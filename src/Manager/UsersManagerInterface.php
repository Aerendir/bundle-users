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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;

interface UsersManagerInterface
{
    public function __construct(string $provider, string $secUserClass, string $secUserProperty, EventDispatcherInterface $dispatcher, EntityManagerInterface $entityManager, PropertyAccessor $propertyAccessor);

    public function create(string $unique, string $pass): UserInterface;

    public function load(string $primaryProperty): ?UserInterface;
}
