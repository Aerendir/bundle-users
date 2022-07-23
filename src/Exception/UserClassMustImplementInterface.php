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

use function Safe\sprintf;

class UserClassMustImplementInterface extends UsersException
{
    /**
     * @param HasPlainPasswordInterface|HasRolesInterface|string|UserInterface $user
     */
    public function __construct($user, string $interfaceToImplement)
    {
        $message = sprintf('The User class "%s" MUST implement the "%s" interface.', $this->getUserClass($user), $interfaceToImplement);
        parent::__construct($message);
    }
}
