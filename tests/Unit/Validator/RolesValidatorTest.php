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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Validator;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Validator\RolesValidator;

final class RolesValidatorTest extends TestCase
{
    public function testValidateRoleSucceedsWithValidRole(): void
    {
        $errors = RolesValidator::validateRole('ROLE_ADMIN');
        self::assertEmpty($errors);
    }

    public function testValidateRoleFailsWithLowercaseCharacters(): void
    {
        $role   = 'role';
        $errors = RolesValidator::validateRole($role);
        self::assertArrayHasKey($role, $errors);
        self::assertContains('Role name can contain only UPPERCASE LETTERS, numbers and underscores (ex.: ROLE_ADMIN).', $errors[$role]);
    }

    public function testValidateRoleFailsWithoutRolePrefix(): void
    {
        $role   = 'ADMIN';
        $errors = RolesValidator::validateRole($role);
        self::assertArrayHasKey($role, $errors);
        self::assertContains('Must start with "ROLE_" (ex.: ROLE_ADMIN).', $errors[$role]);
    }

    public function testValidateWithValidRolesArray(): void
    {
        $errors = RolesValidator::validate(['ROLE_USER', 'ROLE_ADMIN']);
        self::assertEmpty($errors);
    }

    public function testValidateWithInvalidRolesArray(): void
    {
        $role   = 'invalid_role';
        $errors = RolesValidator::validate(['ROLE_USER', $role]);
        self::assertArrayHasKey($role, $errors);
    }

    public function testValidateFailsWithNonStringInArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Each role must be a string. Current type: int.');
        RolesValidator::validate(['ROLE_USER', 123]);
    }

    public function testFormatErrors(): void
    {
        $errors = [
            'ROLE_1' => ['Error 1', 'Error 2'],
            'ROLE_2' => ['Error 3'],
        ];

        $message = RolesValidator::formatErrors($errors);
        self::assertSame('Role "ROLE_1": Error 1 Error 2 Role "ROLE_2": Error 3', $message);
    }

    public function testValidateWithSingleRoleString(): void
    {
        $errors = RolesValidator::validate('ROLE_USER');
        self::assertEmpty($errors);
    }
}
