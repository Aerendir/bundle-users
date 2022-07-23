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

use SerendipityHQ\Bundle\UsersBundle\Model\Property\PasswordResetTokenInterface;

use function Safe\sprintf;

final class PasswordResetTokenClassMustImplementPasswordResetTokenInterfaceInterface extends PasswordResetTokenException
{
    public function __construct(string $passResetTokenClass)
    {
        $message = sprintf('The entity %s MUST implement interface %s.', $passResetTokenClass, PasswordResetTokenInterface::class);
        parent::__construct($message);
    }
}
