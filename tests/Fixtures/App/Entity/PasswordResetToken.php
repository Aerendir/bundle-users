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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\PasswordResetTokenInterface;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\PasswordResetTokenTrait;
use SerendipityHQ\Bundle\UsersBundle\Repository\PasswordResetTokenRepository;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: PasswordResetTokenRepository::class)]
#[ORM\Table(name: 'password_reset_tokens')]
class PasswordResetToken implements PasswordResetTokenInterface
{
    use PasswordResetTokenTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private UserInterface $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function activate(\DateTimeImmutable $expiresAt, string $selector, string $hashedToken): void
    {
        $this->requestedAt = new \DateTimeImmutable();
        $this->expiresAt   = $expiresAt;
        $this->selector    = $selector;
        $this->hashedToken = $hashedToken;
    }
}
