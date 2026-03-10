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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Util;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Model\ResetPasswordTokenComponents;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity\User;
use SerendipityHQ\Bundle\UsersBundle\Util\PasswordResetTokenGenerator;
use Symfony\Component\PropertyAccess\PropertyAccess;

use function Safe\json_encode;

final class PasswordResetTokenGeneratorTest extends TestCase
{
    private string $appSecret              = 'test_secret';
    private string $userIdentifierProperty = 'email';
    private PasswordResetTokenGenerator $generator;

    protected function setUp(): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->generator  = new PasswordResetTokenGenerator($this->appSecret, $this->userIdentifierProperty, $propertyAccessor);
    }

    public function testCreateTokenWithVerifier(): void
    {
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $user      = new User();
        $user->setEmail('test_user@example.com');

        $verifier = 'test_verifier_1234567890'; // Should be long enough for realism, but any string works here

        $components = $this->generator->createToken($expiresAt, $user, $verifier);

        self::assertInstanceOf(ResetPasswordTokenComponents::class, $components);
        self::assertSame($verifier, $this->getPrivateProperty($components, 'verifier'));
        self::assertNotEmpty($components->getSelector());
        self::assertNotEmpty($components->getHashedToken());

        // Verify hashing logic
        $expectedEncodedData = json_encode([$verifier, 'test_user@example.com', $expiresAt->getTimestamp()], JSON_THROW_ON_ERROR);
        $expectedHashed      = \hash_hmac('sha256', $expectedEncodedData, $this->appSecret, true);
        $expectedHashedToken = \base64_encode($expectedHashed);

        self::assertSame($expectedHashedToken, $components->getHashedToken());
    }

    public function testCreateTokenWithoutVerifierGeneratesRandomOne(): void
    {
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $user      = new User();
        $user->setEmail('test_user@example.com');

        $components = $this->generator->createToken($expiresAt, $user);

        self::assertInstanceOf(ResetPasswordTokenComponents::class, $components);
        $verifier = $this->getPrivateProperty($components, 'verifier');
        self::assertNotEmpty($verifier);
        self::assertSame(ResetPasswordTokenComponents::TOKEN_VERIFIER_LENGTH, strlen($verifier));

        self::assertNotEmpty($components->getSelector());
        self::assertSame(ResetPasswordTokenComponents::TOKEN_VERIFIER_LENGTH, strlen($components->getSelector()));

        // Verify hashing logic with the generated verifier
        $expectedEncodedData = json_encode([$verifier, 'test_user@example.com', $expiresAt->getTimestamp()], JSON_THROW_ON_ERROR);
        $expectedHashed      = \hash_hmac('sha256', $expectedEncodedData, $this->appSecret, true);
        $expectedHashedToken = \base64_encode($expectedHashed);

        self::assertSame($expectedHashedToken, $components->getHashedToken());
    }

    /**
     * Helper to get private properties for testing.
     */
    private function getPrivateProperty(object $object, string $propertyName): mixed
    {
        $reflection = new \ReflectionClass($object);
        $property   = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
