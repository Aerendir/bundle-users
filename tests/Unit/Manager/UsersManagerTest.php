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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Manager;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Exception\UserClassMustImplementHasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Exception\UserClassMustImplementHasRolesInterface;
use SerendipityHQ\Bundle\UsersBundle\Exception\UserClassMustImplementUserInterface;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManager;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasRolesInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\User\UserInterface;

final class UsersManagerTest extends TestCase
{
    private UsersManager $usersManager;
    private MockObject $mockDispatcher;
    private MockObject $mockEntityManager;
    private MockObject $mockPropertyAccessor;

    protected function setUp(): void
    {
        $this->mockDispatcher       = $this->createMock(EventDispatcherInterface::class);
        $this->mockEntityManager    = $this->createMock(EntityManagerInterface::class);
        $this->mockPropertyAccessor = $this->createMock(PropertyAccessor::class);

        $this->usersManager = new UsersManager(
            'provider',
            'UserClass',
            'email',
            $this->mockDispatcher,
            $this->mockEntityManager,
            $this->mockPropertyAccessor
        );
    }

    public function testCreateThrowsUserInterfaceException(): void
    {
        // We need a UsersManager with a class that does NOT implement UserInterface
        $usersManager = new UsersManager(
            'provider',
            \stdClass::class,
            'email',
            $this->mockDispatcher,
            $this->mockEntityManager,
            $this->mockPropertyAccessor
        );

        $this->expectException(UserClassMustImplementUserInterface::class);
        $usersManager->create('test@example.com', 'password');
    }

    public function testCreateThrowsHasPlainPasswordInterfaceException(): void
    {
        $userClass = get_class($this->createValidUser());

        $usersManager = new UsersManager(
            'provider',
            $userClass,
            'email',
            $this->mockDispatcher,
            $this->mockEntityManager,
            $this->mockPropertyAccessor
        );

        $this->mockPropertyAccessor->method('setValue')
            ->willReturnCallback(function ($object, $property, $value): void {
                if (HasPlainPasswordInterface::FIELD_PLAIN_PASSWORD === $property) {
                    throw new NoSuchPropertyException('Can not write property "plainPassword"');
                }
            });

        $this->expectException(UserClassMustImplementHasPlainPasswordInterface::class);
        $usersManager->create('test@example.com', 'password');
    }

    public function testCreateSuccess(): void
    {
        $userClass = get_class($this->createValidUser());

        $usersManager = new UsersManager(
            'provider',
            $userClass,
            'email',
            $this->mockDispatcher,
            $this->mockEntityManager,
            $this->mockPropertyAccessor
        );

        $this->mockDispatcher->expects($this->once())->method('dispatch');
        $this->mockEntityManager->expects($this->once())->method('persist');

        $user = $usersManager->create('test@example.com', 'password');

        $this->assertInstanceOf(UserInterface::class, $user);
    }

    public function testEnsureUserImplementsHasRolesInterfacesThrowsUserInterfaceException(): void
    {
        $invalidUser = new \stdClass();

        $this->expectException(UserClassMustImplementUserInterface::class);

        $reflection = new \ReflectionClass(UsersManager::class);
        $method     = $reflection->getMethod('ensureUserImplementsHasRolesInterfaces');
        $method->setAccessible(true);
        $method->invoke($this->usersManager, $invalidUser);
    }

    public function testEnsureUserImplementsHasRolesInterfacesThrowsHasRolesInterfaceException(): void
    {
        $userWithoutHasRoles = $this->createMock(UserInterface::class);

        $this->expectException(UserClassMustImplementHasRolesInterface::class);

        $reflection = new \ReflectionClass(UsersManager::class);
        $method     = $reflection->getMethod('ensureUserImplementsHasRolesInterfaces');
        $method->setAccessible(true);
        $method->invoke($this->usersManager, $userWithoutHasRoles);
    }

    public function testEnsureUserImplementsHasRolesInterfacesWithValidUser(): void
    {
        $validUser = $this->createValidUser();

        $reflection = new \ReflectionClass(UsersManager::class);
        $method     = $reflection->getMethod('ensureUserImplementsHasRolesInterfaces');
        $method->setAccessible(true);

        $method->invoke($this->usersManager, $validUser);

        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    private function createValidUser(): UserInterface
    {
        return new class implements UserInterface, HasRolesInterface {
            public function getRoles(): array
            {
                return [];
            }

            public function setRoles(array $roles): void
            {
            }

            public function getPassword(): ?string
            {
                return null;
            }

            public function getSalt(): ?string
            {
                return null;
            }

            public function eraseCredentials(): void
            {
            }

            public function getUserIdentifier(): string
            {
                return 'test';
            }

            public function getUsername(): string
            {
                return 'test';
            }
        };
    }
}
