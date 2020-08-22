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
use Symfony\Component\Security\Core\User\UserInterface;

class UsersException extends \Exception
{
    /**
     * @param HasPlainPasswordInterface|string|UserInterface $user
     */
    protected function getUserClass($user): string
    {
        $userClass = 'unknown';
        if (\is_object($user)) {
            $userClass = \get_class($user);
        }

        if (\is_string($user)) {
            $userClass = $user;
        }

        return $userClass;
    }
}
