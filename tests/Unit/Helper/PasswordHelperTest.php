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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Helper;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordEncodingError;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordRequired;
use SerendipityHQ\Bundle\UsersBundle\Exception\UserClassMustImplementUserInterface;
use SerendipityHQ\Bundle\UsersBundle\Form\Type\PasswordResetRequestType;
use SerendipityHQ\Bundle\UsersBundle\Form\Type\PasswordResetType;
use SerendipityHQ\Bundle\UsersBundle\Form\Type\UserPasswordChangeType;
use SerendipityHQ\Bundle\UsersBundle\Helper\PasswordHelper;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Routes;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class PasswordHelperTest extends TestCase
{
    private string $secUserProperty = 'email';
    private MockObject $hasherFactory;
    private MockObject $formFactory;
    private MockObject $router;
    private PasswordHelper $passwordHelper;

    protected function setUp(): void
    {
        $this->hasherFactory = $this->createMock(PasswordHasherFactoryInterface::class);
        $this->formFactory   = $this->createMock(FormFactoryInterface::class);
        $this->router        = $this->createMock(RouterInterface::class);

        $this->passwordHelper = new PasswordHelper(
            $this->secUserProperty,
            $this->hasherFactory,
            $this->formFactory,
            $this->router
        );
    }

    public function testCreateFormPasswordChange(): void
    {
        $user = $this->createMock(HasPlainPasswordInterface::class);
        $form = $this->createMock(FormInterface::class);

        $this->router->expects($this->once())
            ->method('generate')
            ->with(Routes::PASSWORD_CHANGE)
            ->willReturn('/password-change');

        $this->formFactory->expects($this->once())
            ->method('create')
            ->with(UserPasswordChangeType::class, $user, [
                'action' => '/password-change',
                'method' => 'POST',
            ])
            ->willReturn($form);

        $result = $this->passwordHelper->createFormPasswordChange($user);
        $this->assertSame($form, $result);
    }

    public function testCreateFormPasswordResetRequest(): void
    {
        $form = $this->createMock(FormInterface::class);

        $this->router->expects($this->once())
            ->method('generate')
            ->with(Routes::PASSWORD_RESET_REQUEST)
            ->willReturn('/password-reset-request');

        $this->formFactory->expects($this->once())
            ->method('create')
            ->with(PasswordResetRequestType::class, null, [
                'action'                                    => '/password-reset-request',
                'method'                                    => 'POST',
                'allow_extra_fields'                        => true,
                PasswordResetRequestType::SEC_USER_PROPERTY => $this->secUserProperty,
            ])
            ->willReturn($form);

        $result = $this->passwordHelper->createFormPasswordResetRequest();
        $this->assertSame($form, $result);
    }

    public function testCreateFormPasswordReset(): void
    {
        $form = $this->createMock(FormInterface::class);

        $this->router->expects($this->once())
            ->method('generate')
            ->with(Routes::PASSWORD_RESET_RESET)
            ->willReturn('/password-reset');

        $this->formFactory->expects($this->once())
            ->method('create')
            ->with(PasswordResetType::class, null, [
                'action'             => '/password-reset',
                'method'             => 'POST',
                'allow_extra_fields' => true,
            ])
            ->willReturn($form);

        $result = $this->passwordHelper->createFormPasswordReset();
        $this->assertSame($form, $result);
    }

    public function testEncodePlainPassword(): void
    {
        $user   = $this->createMock(UserWithPlainPassword::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $user->method('getPlainPassword')->willReturn('plain-password');

        $this->hasherFactory->expects($this->once())
            ->method('getPasswordHasher')
            ->with($user)
            ->willReturn($hasher);

        $hasher->expects($this->once())
            ->method('hash')
            ->with('plain-password')
            ->willReturn('hashed-password');

        $hasher->expects($this->once())
            ->method('verify')
            ->with('hashed-password', 'plain-password')
            ->willReturn(true);

        $result = $this->passwordHelper->encodePlainPassword($user);
        $this->assertSame('hashed-password', $result);
    }

    public function testEncodePlainPasswordWithExplicitPlainPassword(): void
    {
        $user   = $this->createMock(UserWithPlainPassword::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $this->hasherFactory->expects($this->once())
            ->method('getPasswordHasher')
            ->with($user)
            ->willReturn($hasher);

        $hasher->expects($this->once())
            ->method('hash')
            ->with('explicit-password')
            ->willReturn('hashed-password');

        $hasher->expects($this->once())
            ->method('verify')
            ->with('hashed-password', 'explicit-password')
            ->willReturn(true);

        $result = $this->passwordHelper->encodePlainPassword($user, 'explicit-password');
        $this->assertSame('hashed-password', $result);
    }

    public function testEncodePlainPasswordThrowsPasswordRequired(): void
    {
        $user = $this->createMock(HasPlainPasswordInterface::class);
        $user->method('getPlainPassword')->willReturn(null);

        $this->expectException(PasswordRequired::class);
        $this->passwordHelper->encodePlainPassword($user);
    }

    public function testEncodePlainPasswordThrowsUserClassMustImplementUserInterface(): void
    {
        $user = $this->createMock(HasPlainPasswordInterface::class);
        $user->method('getPlainPassword')->willReturn('plain-password');

        $this->expectException(UserClassMustImplementUserInterface::class);
        $this->passwordHelper->encodePlainPassword($user);
    }

    public function testEncodePlainPasswordThrowsPasswordEncodingError(): void
    {
        $user   = $this->createMock(UserWithPlainPassword::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $user->method('getPlainPassword')->willReturn('plain-password');

        $this->hasherFactory->method('getPasswordHasher')->willReturn($hasher);
        $hasher->method('hash')->willReturn('hashed-password');
        $hasher->method('verify')->willReturn(false);

        $this->expectException(PasswordEncodingError::class);
        $this->passwordHelper->encodePlainPassword($user);
    }

    public function testGeneratePlainPassword(): void
    {
        $password = $this->passwordHelper->generatePlainPassword();
        $this->assertIsString($password);
        $this->assertSame(12, strlen($password));
    }
}

interface UserWithPlainPassword extends UserInterface, HasPlainPasswordInterface
{
}
