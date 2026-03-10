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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Functional\Command;

use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity\User;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Factories\UserFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\Foundry\Attribute\ResetDatabase;

#[ResetDatabase]
final class UserRoleAddCommandTest extends KernelTestCase
{
    public function testUserRoleAdd(): void
    {
        self::bootKernel();

        $user = UserFactory::createOne(['email' => 'test@example.com', 'roles' => ['ROLE_USER']]);

        $application   = new Application(self::$kernel);
        $command       = $application->find('shq:user:role:add');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'unique' => 'test@example.com',
            'roles'  => ['ROLE_ADMIN', 'ROLE_MANAGER'],
        ]);

        $commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('Roles added to user test@example.com.', $commandTester->getDisplay());

        // Refresh from DB
        $container     = self::getContainer();
        $entityManager = $container->get('doctrine.orm.default_entity_manager');
        $entityManager->clear();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_MANAGER', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testUserRoleAddFailsWithInvalidRoles(): void
    {
        self::bootKernel();
        UserFactory::createOne(['email' => 'test@example.com']);

        $application   = new Application(self::$kernel);
        $command       = $application->find('shq:user:role:add');
        $commandTester = new CommandTester($command);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Role "invalid_role": Role name can contain only UPPERCASE LETTERS, numbers and underscores (ex.: ROLE_ADMIN). Must start with "ROLE_" (ex.: ROLE_ADMIN).');

        $commandTester->execute([
            'unique' => 'test@example.com',
            'roles'  => ['invalid_role'],
        ]);
    }
}
