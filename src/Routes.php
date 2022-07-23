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

namespace SerendipityHQ\Bundle\UsersBundle;

final class Routes
{
    public const PASSWORD_CHANGE        = 'user_password_change';
    public const PASSWORD_RESET_REQUEST = 'user_password_reset_request';
    public const PASSWORD_RESET_RESET   = 'user_password_reset_reset_password';
}
