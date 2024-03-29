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

namespace SerendipityHQ\Bundle\UsersBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Safe\DateTimeImmutable;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\PasswordResetTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method PasswordResetTokenInterface|null find($id, $lockMode = null, $lockVersion = null)
 * @method PasswordResetTokenInterface|null findOneBy(array $criteria, array $orderBy = null)
 * @method PasswordResetTokenInterface[]    findAll()
 * @method PasswordResetTokenInterface[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PasswordResetTokenRepository extends EntityRepository
{
    /**
     * Finds all still active tokens that generated for the given user.
     *
     * @todo Actually filters only still active tokens.
     */
    public function getTokensStillValid(UserInterface $user): ?array
    {
        return $this->createQueryBuilder('t')
             ->where('t.user = :user')
             ->setParameter('user', $user)
             ->orderBy('t.requestedAt', Criteria::DESC)
             ->getQuery()
             ->getResult();
    }

    public function findBySelector(string $selector): ?PasswordResetTokenInterface
    {
        return $this->findOneBy(['selector' => $selector]);
    }

    public function removeExpiredResetPasswordRequests(int $passResetLifespanAmountOf, string $passResetLifespanUnit): int
    {
        $time  = new DateTimeImmutable(sprintf('-%s %s', $passResetLifespanAmountOf, $passResetLifespanUnit));

        return $this
            ->createQueryBuilder('t')
            ->delete()
            ->where('t.expiresAt <= :time')
            ->setParameter('time', $time)
            ->getQuery()
            ->getResult();
    }
}
