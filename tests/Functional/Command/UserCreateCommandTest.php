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

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\Foundry\Attribute\ResetDatabase;

#[ResetDatabase]
final class UserCreateCommandTest extends KernelTestCase
{
    public function testUserCreate(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command       = $application->find('shq:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'unique' => 'test@example.com',
            'pass'   => 'password123',
        ]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('User test@example.com created.', $output);

        $container     = self::getContainer();
        $entityManager = $container->get('doctrine.orm.default_entity_manager');
        if (false === $entityManager instanceof EntityManagerInterface) {
            throw new \LogicException('The "doctrine.orm.default_entity_manager" service is not an instance of EntityManagerInterface.');
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertNotNull($user->getPassword());
    }

    public function testUserCreateFailsWithInvalidEmail(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command       = $application->find('shq:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'unique' => 'invalid-email',
            'pass'   => 'password123',
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('This value is not a valid email address.', $output);
        $this->assertStringContainsString('Impossible to create the user "invalid-email".', $output);
    }

    public function testUserCreateFailsWithShortPassword(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command       = $application->find('shq:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'unique' => 'test@example.com',
            'pass'   => 'short',
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('This value is too short. It should have 8 characters or more.', $output);
        $this->assertStringContainsString('Impossible to create the user "test@example.com".', $output);
    }

    public function testUserCreateFailsWithInvalidProvider(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command       = $application->find('shq:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'unique'     => 'test@example.com',
            'pass'       => 'password123',
            '--provider' => 'non-existent-provider',
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('The provider "non-existent-provider" you passed is not configured', $output);
    }
}
