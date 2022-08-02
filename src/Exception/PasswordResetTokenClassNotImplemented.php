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

use function Safe\sprintf;

final class PasswordResetTokenClassNotImplemented extends PasswordResetTokenException
{
    public function __construct(?string $passResetTokenClass)
    {
        $message = sprintf("The entity class %s doesn't exist.", $passResetTokenClass);
        parent::__construct($message);
    }
}
