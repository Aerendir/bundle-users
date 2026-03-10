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
final class UserRoleRemCommandTest extends KernelTestCase
{
    public function testUserRoleRem(): void
    {
        self::bootKernel();

        $user = UserFactory::createOne(['email' => 'test@example.com', 'roles' => ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MANAGER']]);

        $application   = new Application(self::$kernel);
        $command       = $application->find('shq:user:role:rem');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'unique' => 'test@example.com',
            'roles'  => ['ROLE_ADMIN', 'ROLE_MANAGER'],
        ]);

        $commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('Roles removed from user test@example.com.', $commandTester->getDisplay());

        // Refresh from DB
        $container     = self::getContainer();
        $entityManager = $container->get('doctrine.orm.default_entity_manager');
        $entityManager->clear();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);

        $this->assertNotContains('ROLE_ADMIN', $user->getRoles());
        $this->assertNotContains('ROLE_MANAGER', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }
}
