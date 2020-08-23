<?php

/*
 * This file is part of the Serendipity HQ Users Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\UsersBundle\Validator;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\String\UnicodeString;

final class RolesValidator
{
    /**
     * @todo Remove the suppression
     * @suppress PhanWriteOnlyPublicProperty
     */
    public RoleHierarchyInterface $roleHierarchy;

    /** @var array<string,array<string>> */
    private array $errors = [];

    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * @param string|string[] $roles
     *
     * @return array<string,array<string>>
     */
    public function validate($roles): array
    {
        if (\is_string($roles)) {
            $roles = [$roles];
        }

        if (false === \is_array($roles)) {
            throw new \InvalidArgumentException('The $roles argument can be only a string or an array<string>');
        }

        foreach ($roles as $role) {
            $this->validateRole($role);
        }

        return $this->errors;
    }

    /**
     * @return array<string,array<string>>
     */
    public function validateRole(string $role): array
    {
        if (1 !== \Safe\preg_match('#^[A-Z0-9_]+$#', $role)) {
            $this->errors[$role][] = 'Role name can contain only UPPERCASE LETTERS, numbers and underscores (ex.: ROLE_ADMIN).';
        }

        $string = new UnicodeString($role);
        if (false === $string->startsWith('ROLE_')) {
            $this->errors[$role][] = 'Must start with "ROLE_" (ex.: ROLE_ADMIN).';
        }

        return $this->errors;
    }
}
