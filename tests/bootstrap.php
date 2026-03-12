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

require_once __DIR__ . '/../vendor/autoload.php';

if (false === file_exists(__DIR__ . '/../vendor-bin/phpunit/vendor/autoload.php')) {
    throw new LogicException('PHPUnit is required to run the tests. Please install it via composer bin phpunit install');
}

require_once __DIR__ . '/../vendor-bin/phpunit/vendor/autoload.php';
