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

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * This trait MUST be implemented by all UserInterface in the app.
 */
interface HasPlainPasswordInterface extends PasswordAuthenticatedUserInterface
{
    public const FIELD_PLAIN_PASSWORD = 'plainPassword';

    public function setPlainPassword(string $password): void;

    public function getPlainPassword(): ?string;

    public function setPassword(string $password): void;
}
