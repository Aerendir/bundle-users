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

namespace SerendipityHQ\Bundle\UsersBundle\Exception;

use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasRolesInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserClassMustImplementHasRolesInterface extends UserClassMustImplementInterface
{
    /**
     * @param HasPlainPasswordInterface|string|UserInterface $user
     */
    public function __construct($user)
    {
        parent::__construct($user, HasRolesInterface::class);
    }
}
