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

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenExpired;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenInvalid;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenTooMuchFastRequests;
use SerendipityHQ\Bundle\UsersBundle\Exception\PasswordResetTokenTooMuchStillActive;
use SerendipityHQ\Bundle\UsersBundle\Helper\PasswordHelper;
use SerendipityHQ\Bundle\UsersBundle\Helper\PasswordResetHelper;
use SerendipityHQ\Bundle\UsersBundle\Manager\PasswordManager;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity\PasswordResetToken;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity\User;
use SerendipityHQ\Bundle\UsersBundle\Util\PasswordResetTokenGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

final class PasswordManagerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private PasswordHelper $passwordHelper;
    private PasswordResetHelper $passwordResetHelper;

    protected function setUp(): void
    {
        self::bootKernel();
        $container           = self::getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();
        // Purge tables to isolate tests
        $conn = $this->entityManager->getConnection();

        try {
            $conn->executeStatement('DELETE FROM password_reset_tokens');
        } catch (\Throwable $throwable) {
            // ignore if table does not exist yet
        }

        try {
            $conn->executeStatement('DELETE FROM users');
        } catch (\Throwable $throwable) {
            // ignore if table does not exist yet
        }

        // Build helpers manually as they are private services in tests
        $secUserProperty       = 'email';
        $passwordHasherFactory = $container->get('security.password_hasher_factory');
        $formFactory           = $container->get('form.factory');
        $router                = $container->get('router.default');
        $propertyAccessor      = $container->get('property_accessor');
        $requestStack          = $container->get('request_stack');
        $request               = new Request();
        $session               = new Session(new MockFileSessionStorage(sys_get_temp_dir()));
        $request->setSession($session);
        $requestStack->push($request);
        $appSecret = $container->getParameter('kernel.secret');

        $this->passwordHelper = new PasswordHelper($secUserProperty, $passwordHasherFactory, $formFactory, $router);

        $tokenGenerator = new PasswordResetTokenGenerator(
            (string) $appSecret,
            $secUserProperty,
            $propertyAccessor
        );
        $this->passwordResetHelper = new PasswordResetHelper($tokenGenerator, $requestStack);
    }

    public function testHandleResetRequestSuccess(): void
    {
        $manager = $this->createManager();

        $user = (new User())->setEmail('john@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $formFactory = self::getContainer()->get('form.factory');
        $form        = $formFactory->createBuilder()
            ->add('email')
            ->getForm();
        $form->submit(['email' => 'john@example.com']);

        self::assertTrue($manager->handleResetRequest($form));

        // After success, the helper should allow access to the "check your email" page
        self::assertTrue($this->passwordResetHelper->canAccessPageCheckYourEmail());
    }

    public function testHandleResetRequestUserNotFoundReturnsFalse(): void
    {
        $manager = $this->createManager();

        $formFactory = self::getContainer()->get('form.factory');
        $form        = $formFactory->createBuilder()
            ->add('email')
            ->getForm();
        $form->submit(['email' => 'missing@example.com']);

        self::assertFalse($manager->handleResetRequest($form));
    }

    public function testThrottlingTooManyActive(): void
    {
        $manager = $this->createManager(maxActive: 1);

        $user = (new User())->setEmail('alice@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Create an already active token
        $token  = new PasswordResetToken($user);
        $public = $this->passwordResetHelper->activateResetToken($token);
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        $formFactory = self::getContainer()->get('form.factory');
        $form        = $formFactory->createBuilder()->add('email')->getForm();
        $form->submit(['email' => 'alice@example.com']);

        $this->expectException(PasswordResetTokenTooMuchStillActive::class);
        $manager->handleResetRequest($form);
    }

    public function testThrottlingTooFast(): void
    {
        Carbon::setTestNow(new Carbon());
        $manager = $this->createManager(maxActive: 3, minSeconds: 180);

        $user = (new User())->setEmail('bob@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Create a very recent token
        $token = new PasswordResetToken($user);
        $this->passwordResetHelper->activateResetToken($token);
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        $formFactory = self::getContainer()->get('form.factory');
        $form        = $formFactory->createBuilder()->add('email')->getForm();
        $form->submit(['email' => 'bob@example.com']);

        $this->expectException(PasswordResetTokenTooMuchFastRequests::class);

        try {
            $manager->handleResetRequest($form);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function testLoadTokenAndValidateAndFindUser(): void
    {
        $manager = $this->createManager();

        $user = (new User())->setEmail('chris@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Manually build a token with known selector/verifier
        $token     = new PasswordResetToken($user);
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $selector  = str_repeat('a', 20);
        $verifier  = str_repeat('b', 20);
        $hashed    = $this->passwordResetHelper->getPasswordResetTokenGenerator()->createToken($expiresAt, $user, $verifier)->getHashedToken();
        $token->activate($expiresAt, $selector, $hashed);
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        // Build a public token string: selector (20) + verifier (20)
        $publicToken = $selector . $verifier;
        self::assertSame(40, strlen($publicToken));

        $loaded = $manager->loadTokenFromPublicOne($publicToken);
        self::assertSame($token->getSelector(), $loaded->getSelector());

        // validate should not throw
        $manager->validateToken($publicToken, $loaded);

        // find user by token
        $foundUser = $manager->findUserByPublicToken($publicToken);
        self::assertSame($user->getUserIdentifier(), $foundUser->getUserIdentifier());
    }

    public function testLoadTokenFromPublicOneInvalidLength(): void
    {
        $manager = $this->createManager();
        $this->expectException(PasswordResetTokenInvalid::class);
        $manager->loadTokenFromPublicOne('short');
    }

    public function testValidateTokenExpiredThrows(): void
    {
        $manager = $this->createManager();

        $user = (new User())->setEmail('dave@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $token = new PasswordResetToken($user);
        // Activate and then manually expire
        $this->passwordResetHelper->activateResetToken($token);
        $ref = new \ReflectionProperty(PasswordResetToken::class, 'expiresAt');
        $ref->setAccessible(true);
        $ref->setValue($token, new \DateTimeImmutable('-1 hour'));

        $selector    = 'aaaaaaaaaaaaaaaaaaaa';
        $verifier    = 'bbbbbbbbbbbbbbbbbbbb';
        $publicToken = $selector . $verifier; // 40 chars

        $this->expectException(PasswordResetTokenExpired::class);
        $manager->validateToken($publicToken, $token);
    }

    public function testHandleResetSetsPlainPassword(): void
    {
        $manager = $this->createManager();

        $user = (new User())->setEmail('eve@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Create a valid token we can reference by public token
        $token     = new PasswordResetToken($user);
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $selector  = str_repeat('c', 20);
        $verifier  = str_repeat('d', 20);
        $hashed    = $this->passwordResetHelper->getPasswordResetTokenGenerator()->createToken($expiresAt, $user, $verifier)->getHashedToken();
        $token->activate($expiresAt, $selector, $hashed);
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        $publicToken = $selector . $verifier;

        $formFactory = self::getContainer()->get('form.factory');
        $form        = $formFactory->createBuilder()
            ->add(HasPlainPasswordInterface::FIELD_PLAIN_PASSWORD)
            ->getForm();
        $form->submit([HasPlainPasswordInterface::FIELD_PLAIN_PASSWORD => 'new-plain-pass']);

        $manager->handleReset($publicToken, $user, $form);
        self::assertSame('new-plain-pass', $user->getPlainPassword());

        // handleReset calls deleteUsedToken, so token should be removed from EM (it is marked for removal)
        // But since we didn't flush in handleReset, it might still be in DB unless we flush.
        // In PasswordManager: $this->entityManager->remove($token);
        $this->entityManager->flush();
        $this->entityManager->clear();
        $deletedToken = $this->entityManager->getRepository(PasswordResetToken::class)->findOneBy(['selector' => $selector]);
        self::assertNull($deletedToken);
    }

    public function testCleanExpiredTokens(): void
    {
        $manager = $this->createManager(lifespanAmount: 1, lifespanUnit: 'hour');

        $user = (new User())->setEmail('clean@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);

        // Valid token
        $token1 = new PasswordResetToken($user);
        $token1->activate(new \DateTimeImmutable('+1 hour'), 'sel11111111111111111', 'hash1');

        // Expired token
        $token2 = new PasswordResetToken($user);
        $token2->activate(new \DateTimeImmutable('-1 hour'), 'sel22222222222222222', 'hash2');

        $this->entityManager->persist($token1);
        $this->entityManager->persist($token2);
        $this->entityManager->flush();

        $removedCount = $manager->cleanExpiredTokens();

        // It should remove token2
        self::assertSame(1, $removedCount);

        $this->entityManager->clear();
        self::assertNotNull($this->entityManager->getRepository(PasswordResetToken::class)->findOneBy(['selector' => 'sel11111111111111111']));
        self::assertNull($this->entityManager->getRepository(PasswordResetToken::class)->findOneBy(['selector' => 'sel22222222222222222']));
    }

    public function testRemoveResetToken(): void
    {
        $manager = $this->createManager();
        $user    = (new User())->setEmail('remove@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);

        $token = new PasswordResetToken($user);
        $token->activate(new \DateTimeImmutable('+1 hour'), 'sel33333333333333333', 'hash3');
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        $manager->removeResetToken($token);
        $this->entityManager->flush();
        $this->entityManager->clear();

        self::assertNull($this->entityManager->getRepository(PasswordResetToken::class)->findOneBy(['selector' => 'sel33333333333333333']));
    }

    public function testValidateTokenThrowsOnInvalidHash(): void
    {
        $manager = $this->createManager();

        $user = (new User())->setEmail('invalidhash@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $token     = new PasswordResetToken($user);
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $selector  = str_repeat('e', 20);
        $verifier  = str_repeat('f', 20);
        // Set a wrong hash
        $token->activate($expiresAt, $selector, 'wrong-hash');
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        $publicToken = $selector . $verifier;

        $this->expectException(PasswordResetTokenInvalid::class);
        $manager->validateToken($publicToken, $token);
    }

    public function testThrottlingNoTokens(): void
    {
        $manager = $this->createManager();
        $user    = (new User())->setEmail('notokens@example.com');
        $user->setPassword('secret');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Accessing private method via reflection
        $reflection = new \ReflectionClass(PasswordManager::class);
        $method     = $reflection->getMethod('checkThrottling');
        $method->setAccessible(true);

        self::assertNull($method->invoke($manager, $user));
    }

    private function createManager(int $maxActive = 3, int $minSeconds = 180, int $lifespanAmount = 3, string $lifespanUnit = 'week'): PasswordManager
    {
        return new PasswordManager(
            $maxActive,
            $minSeconds,
            $lifespanAmount,
            $lifespanUnit,
            User::class,
            'email',
            $this->entityManager,
            self::getContainer()->get('event_dispatcher'),
            $this->passwordHelper,
            $this->passwordResetHelper,
            PasswordResetToken::class
        );
    }
}
