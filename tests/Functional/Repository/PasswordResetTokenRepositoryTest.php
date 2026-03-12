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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Functional\Repository;

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\Repository\PasswordResetTokenRepository;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity\PasswordResetToken;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Factories\PasswordResetTokenFactory;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Factories\UserFactory;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\SHQBundleUsersTestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class PasswordResetTokenRepositoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    private EntityManagerInterface $entityManager;
    private PasswordResetTokenRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->repository    = $this->entityManager->getRepository(PasswordResetToken::class);
    }

    public function testGetTokensStillValid(): void
    {
        $user = UserFactory::createOne();

        // A valid token
        PasswordResetTokenFactory::createOne([
            'user'        => $user,
            'expiresAt'   => new \DateTimeImmutable('+1 hour'),
            'requestedAt' => new \DateTimeImmutable('-10 minutes'),
        ]);

        // Another more recent valid token
        PasswordResetTokenFactory::createOne([
            'user'        => $user,
            'expiresAt'   => new \DateTimeImmutable('+2 hours'),
            'requestedAt' => new \DateTimeImmutable('-5 minutes'),
        ]);

        // A token for another user
        PasswordResetTokenFactory::createOne();

        // An expired token for the user (must not be returned)
        PasswordResetTokenFactory::createOne([
            'user'      => $user,
            'expiresAt' => new \DateTimeImmutable('-1 hour'),
        ]);

        $tokens = $this->repository->getTokensStillValid($user);

        self::assertCount(2, $tokens);
        // Verify ordering (DESC by requestedAt)
        self::assertTrue($tokens[0]->getRequestedAt() > $tokens[1]->getRequestedAt());
    }

    public function testFindBySelector(): void
    {
        $selector = 'unique_selector_12345';
        PasswordResetTokenFactory::createOne(['selector' => $selector]);

        $token = $this->repository->findBySelector($selector);

        self::assertInstanceOf(PasswordResetToken::class, $token);
        self::assertSame($selector, $token->getSelector());
    }

    public function testRemoveExpiredResetPasswordRequests(): void
    {
        // An expired token (more than 24 hours ago)
        PasswordResetTokenFactory::createOne([
            'expiresAt' => new \DateTimeImmutable('-25 hours'),
        ]);

        // A token not yet expired (expires in 1 hour)
        PasswordResetTokenFactory::createOne([
            'expiresAt' => new \DateTimeImmutable('+1 hour'),
        ]);

        // A token that expired exactly now (or almost)
        PasswordResetTokenFactory::createOne([
            'expiresAt' => new \DateTimeImmutable('-1 minute'),
        ]);

        // Remove tokens expired more than 24 hours ago
        $removedCount = $this->repository->removeExpiredResetPasswordRequests(24, 'hours');

        self::assertSame(1, $removedCount);
        self::assertCount(2, $this->repository->findAll());
    }

    protected static function getKernelClass(): string
    {
        return SHQBundleUsersTestKernel::class;
    }
}
