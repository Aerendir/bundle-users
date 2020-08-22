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

use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use function Safe\sprintf;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserClassMustImplementUserInterface extends UsersException
{
    /**
     * @param string|UserInterface|HasPlainPasswordInterface $user
     */
    public function __construct($user)
    {
        $message = sprintf('The User class "%s" MUST implement the "%s" interface.', $this->getUserClass($user), UserInterface::class);
        parent::__construct($message);
    }
}
