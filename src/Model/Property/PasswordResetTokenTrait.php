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

namespace SerendipityHQ\Bundle\UsersBundle\Model\Property;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver   <ryan@symfonycasts.com>
 * @author Adamo Crespi <aerendir@serendipityhq.com>
 */
trait PasswordResetTokenTrait
{
    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $selector;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $hashedToken;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $requestedAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $expiresAt;

    public function activate(\DateTimeImmutable $expiresAt, string $selector, string $hashedToken): void
    {
        $this->requestedAt = new \DateTimeImmutable();
        $this->expiresAt   = $expiresAt;
        $this->selector    = $selector;
        $this->hashedToken = $hashedToken;
    }

    public function getRequestedAt(): \DateTimeImmutable
    {
        return $this->requestedAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->getTimestamp() <= \time();
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }
}
