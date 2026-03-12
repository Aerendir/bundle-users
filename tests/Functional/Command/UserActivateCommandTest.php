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
final class UserActivateCommandTest extends KernelTestCase
{
    public function testUserActivate(): void
    {
        self::bootKernel();

        $user = UserFactory::createOne(['email' => 'test@example.com', 'active' => false]);
        $this->assertFalse($user->isActive());

        $application   = new Application(self::$kernel);
        $command       = $application->find('shq:user:activate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['unique' => 'test@example.com']);

        $commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('User test@example.com activated.', $commandTester->getDisplay());

        // Refresh from DB
        $container     = self::getContainer();
        $entityManager = $container->get('doctrine.orm.default_entity_manager');
        $entityManager->clear();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);

        $this->assertTrue($user->isActive());
    }

    public function testUserActivateFailsWithNonExistentUser(): void
    {
        self::bootKernel();

        $application   = new Application(self::$kernel);
        $command       = $application->find('shq:user:activate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['unique' => 'non-existent@example.com']);

        $this->assertSame(1, $commandTester->getStatusCode());
        $this->assertStringContainsString('User "non-existent@example.com" not found.', $commandTester->getDisplay());
    }
}
