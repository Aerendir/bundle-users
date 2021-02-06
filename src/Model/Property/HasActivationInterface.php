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

namespace SerendipityHQ\Bundle\UsersBundle\Model\Property;

/**
 * This trait MUST be implemented by all UserInterface in the app to use activation.
 */
interface HasActivationInterface
{
    public function activate(bool $activate = true): void;

    public function isActive(): bool;
}
