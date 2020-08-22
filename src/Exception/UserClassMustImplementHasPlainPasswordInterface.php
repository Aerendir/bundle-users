<?php

/*
 * This file is part of the Serendipity HQ Users Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\UsersBundle\Exception;

use function Safe\sprintf;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserClassMustImplementHasPlainPasswordInterface extends UsersException
{
    /**
     * @param HasPlainPasswordInterface|string|UserInterface $user
     */
    public function __construct($user)
    {
        $message = sprintf('The UserInterface class "%s" MUST implement the "%s" interface.', $this->getUserClass($user), HasPlainPasswordInterface::class);
        parent::__construct($message);
    }
}
