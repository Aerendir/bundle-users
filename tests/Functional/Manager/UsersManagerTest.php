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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Functional\Manager;

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\Event\UserCreatedEvent;
use SerendipityHQ\Bundle\UsersBundle\Event\UserPasswordChangedEvent;
use SerendipityHQ\Bundle\UsersBundle\Event\UserUpdatedEvent;
use SerendipityHQ\Bundle\UsersBundle\Exception\RoleInvalidException;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity\User;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Factories\UserFactory;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\SHQBundleUsersTestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UsersManagerTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    private UsersManagerInterface $manager;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $dispatcher;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->manager       = self::getContainer()->get(UsersManagerInterface::class);
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->dispatcher    = self::getContainer()->get('event_dispatcher');
    }

    public function testCreate(): void
    {
        $email    = 'test@example.com';
        $password = 'password123';

        // Set up an event listener to check if the event is dispatched
        $eventDispatched = false;
        $this->dispatcher->addListener(UserCreatedEvent::class, function (UserCreatedEvent $event) use (&$eventDispatched, $email): void {
            $eventDispatched = true;
            $this->assertSame($email, $event->getUser()->getUserIdentifier());
            $this->assertSame('users', $event->getProvider());
        });

        $user = $this->manager->create($email, $password);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($email, $user->getEmail());
        $this->assertTrue($eventDispatched);

        // The user should be persisted but not flushed yet
        $this->assertTrue($this->entityManager->contains($user));

        // Let's flush to see if it saves correctly
        $this->entityManager->flush();

        // Reload from database
        $this->entityManager->clear();
        $loadedUser = $this->manager->load($email);
        $this->assertNotNull($loadedUser);
        $this->assertSame($email, $loadedUser->getUserIdentifier());
    }

    public function testCreatePropagationStopped(): void
    {
        $email    = 'stopped@example.com';
        $password = 'password123';

        // Set up an event listener to stop propagation
        $this->dispatcher->addListener(UserCreatedEvent::class, function (UserCreatedEvent $event): void {
            $event->stopPropagation();
        }, 10); // High priority to be first

        $user = $this->manager->create($email, $password);

        $this->assertInstanceOf(User::class, $user);

        // The user should NOT be persisted because propagation was stopped
        $this->assertFalse($this->entityManager->contains($user));
    }

    public function testLoad(): void
    {
        $email = 'existing@example.com';
        UserFactory::createOne(['email' => $email]);

        $user = $this->manager->load($email);
        $this->assertNotNull($user);
        $this->assertSame($email, $user->getUserIdentifier());

        $nonExistent = $this->manager->load('nonexistent@example.com');
        $this->assertNull($nonExistent);
    }

    public function testAddSingleRole(): void
    {
        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);

        $this->manager->addRoles($user, 'ROLE_ADMIN');
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testAddMultipleRoles(): void
    {
        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);

        $this->manager->addRoles($user, ['ROLE_EDITOR', 'ROLE_MANAGER']);
        $this->assertContains('ROLE_EDITOR', $user->getRoles());
        $this->assertContains('ROLE_MANAGER', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testAddInvalidRole(): void
    {
        $user = UserFactory::createOne(['roles' => ['ROLE_USER']]);

        $this->expectException(RoleInvalidException::class);
        $this->manager->addRoles($user, 'invalid_role');
    }

    public function testRemoveSingleRole(): void
    {
        $user = UserFactory::createOne(['roles' => ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_EDITOR']]);

        $this->manager->removeRoles($user, 'ROLE_EDITOR');
        $this->assertNotContains('ROLE_EDITOR', $user->getRoles());
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testRemoveMultipleRoles(): void
    {
        $user = UserFactory::createOne(['roles' => ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_EDITOR']]);

        $this->manager->removeRoles($user, ['ROLE_ADMIN', 'ROLE_EDITOR']);
        $this->assertNotContains('ROLE_ADMIN', $user->getRoles());
        $this->assertNotContains('ROLE_EDITOR', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testUpdated(): void
    {
        $user            = UserFactory::createOne();
        $eventDispatched = false;

        $this->dispatcher->addListener(UserUpdatedEvent::class, function (UserUpdatedEvent $event) use (&$eventDispatched, $user): void {
            $eventDispatched = true;
            $this->assertSame($user, $event->getUser());
            $this->assertSame('users', $event->getProvider());
        });

        $this->manager->updated($user);
        $this->assertTrue($eventDispatched);
    }

    public function testPasswordChanged(): void
    {
        $user            = UserFactory::createOne();
        $eventDispatched = false;

        $this->dispatcher->addListener(UserPasswordChangedEvent::class, function (UserPasswordChangedEvent $event) use (&$eventDispatched, $user): void {
            $eventDispatched = true;
            $this->assertSame($user, $event->getUser());
            $this->assertSame('users', $event->getProvider());
        });

        $this->manager->passwordChanged($user);
        $this->assertTrue($eventDispatched);
    }

    protected static function getKernelClass(): string
    {
        return SHQBundleUsersTestKernel::class;
    }
}
