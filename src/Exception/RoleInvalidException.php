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

final class RoleInvalidException extends RolesException
{
    private string $role;

    /** @var array<string,array<string>> $errors */
    private array $errors;

    /**
     * @param array<string,array<string>> $errors
     */
    public function __construct(string $role, array $errors)
    {
        $this->role   = $role;
        $this->errors = $errors;

        parent::__construct();
    }

    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return array<string,array<string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
