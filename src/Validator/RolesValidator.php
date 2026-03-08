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

namespace SerendipityHQ\Bundle\UsersBundle\Validator;

use Symfony\Component\String\UnicodeString;

use function Safe\preg_match;

final class RolesValidator
{
    /**
     * @param string|string[] $roles
     *
     * @return array<string,array<string>>
     */
    public static function validate(array|string $roles): array
    {
        if (\is_string($roles)) {
            $roles = [$roles];
        }

        $errors = [];
        foreach ($roles as $role) {
            if (false === \is_string($role)) {
                throw new \InvalidArgumentException(sprintf('Each role must be a string. Current type: %s.', get_debug_type($role)));
            }

            $errors = self::validateRole($role, $errors);
        }

        return $errors;
    }

    /**
     * @param array<string,array<string>> $errors
     *
     * @return array<string,array<string>>
     */
    public static function validateRole(string $role, array $errors = []): array
    {
        if (1 !== preg_match('#^[A-Z0-9_]+$#', $role)) {
            $errors[$role][] = 'Role name can contain only UPPERCASE LETTERS, numbers and underscores (ex.: ROLE_ADMIN).';
        }

        $string = new UnicodeString($role);
        if (false === $string->startsWith('ROLE_')) {
            $errors[$role][] = 'Must start with "ROLE_" (ex.: ROLE_ADMIN).';
        }

        return $errors;
    }

    /**
     * @param array<string,array<string>> $errors
     */
    public static function formatErrors(array $errors): string
    {
        $message = '';
        foreach ($errors as $role => $roleErrors) {
            $message .= sprintf('Role "%s": %s ', (string) $role, implode(' ', $roleErrors));
        }

        return trim($message);
    }
}
