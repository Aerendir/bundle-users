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
use SerendipityHQ\Bundle\UsersBundle\Helper\PasswordResetHelper;
use SerendipityHQ\Bundle\UsersBundle\Model\PasswordResetTokenPublic;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\PasswordResetTokenInterface;
use SerendipityHQ\Bundle\UsersBundle\Model\ResetPasswordTokenComponents;
use SerendipityHQ\Bundle\UsersBundle\Util\PasswordResetTokenGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class PasswordResetHelperTest extends TestCase
{
    private MockObject $generator;
    private MockObject $session;
    private MockObject $requestStack;
    private PasswordResetHelper $helper;

    protected function setUp(): void
    {
        $this->generator    = $this->createMock(PasswordResetTokenGeneratorInterface::class);
        $this->session      = $this->createMock(SessionInterface::class);
        $this->requestStack = $this->getMockBuilder(RequestStack::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestStack->method('getSession')->willReturn($this->session);

        $this->helper = new PasswordResetHelper($this->generator, $this->requestStack);
    }

    public function testGetPasswordResetTokenGenerator(): void
    {
        $this->assertSame($this->generator, $this->helper->getPasswordResetTokenGenerator());
    }

    public function testAllowAccessToPageCheckYourEmail(): void
    {
        $this->session->expects($this->once())
            ->method('set')
            ->with(PasswordResetHelper::RESET_PASSWORD_1_CHECK_EMAIL, true);

        $this->helper->allowAccessToPageCheckYourEmail();
    }

    public function testCanAccessPageCheckYourEmail(): void
    {
        $this->session->expects($this->once())
            ->method('has')
            ->with(PasswordResetHelper::RESET_PASSWORD_1_CHECK_EMAIL)
            ->willReturn(true);

        $this->assertTrue($this->helper->canAccessPageCheckYourEmail());
    }

    public function testStoreTokenInSession(): void
    {
        $this->session->expects($this->once())
            ->method('set')
            ->with(PasswordResetHelper::RESET_PASSWORD_2_PUBLIC_TOKEN, 'some-token');

        $this->helper->storeTokenInSession('some-token');
    }

    public function testGetTokenFromSession(): void
    {
        $this->session->expects($this->once())
            ->method('get')
            ->with(PasswordResetHelper::RESET_PASSWORD_2_PUBLIC_TOKEN)
            ->willReturn('some-token');

        $this->assertSame('some-token', $this->helper->getTokenFromSession());
    }

    public function testCleanSessionAfterReset(): void
    {
        $this->session->expects($this->exactly(2))
            ->method('remove')
            ->with($this->callback(function ($argument): bool {
                static $count = 0;
                $expected     = [
                    PasswordResetHelper::RESET_PASSWORD_1_CHECK_EMAIL,
                    PasswordResetHelper::RESET_PASSWORD_2_PUBLIC_TOKEN,
                ];

                return $argument === $expected[$count++];
            }));

        $this->helper->cleanSessionAfterReset();
    }

    public function testActivateResetToken(): void
    {
        $user       = $this->createMock(UserInterface::class);
        $resetToken = $this->createMock(PasswordResetTokenInterface::class);
        $resetToken->method('getUser')->willReturn($user);

        $tokenComponents = new ResetPasswordTokenComponents('selector', 'verifier', 'hashed-token');

        $this->generator->expects($this->once())
            ->method('createToken')
            ->with($this->isInstanceOf(\DateTimeImmutable::class), $user)
            ->willReturn($tokenComponents);

        $resetToken->expects($this->once())
            ->method('activate')
            ->with(
                $this->isInstanceOf(\DateTimeImmutable::class),
                'selector',
                'hashed-token'
            );

        $result = $this->helper->activateResetToken($resetToken);

        $this->assertInstanceOf(PasswordResetTokenPublic::class, $result);
        $this->assertSame('selectorverifier', $result->getPublicToken());
        $this->assertSame(PasswordResetHelper::RESET_TOKEN_LIFETIME, $result->getLifetime());
    }
}
