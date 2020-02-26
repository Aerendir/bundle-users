<?php

declare(strict_types=1);

/*
 * This file is part of SHQUsersBundle.
 *
 * (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\UsersBundle\Property;

/**
 * This trait MUST be implemented by all UserINterface in the app.
 */
trait HasPlainPasswordTrait
{
    /** @var string|null */
    private $plainPassword;

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }
}
