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

use Doctrine\ORM\Mapping as ORM;

/**
 * @suppress PhanUnreferencedClass
 */
trait HasActivationTrait
{
    /** @ORM\Column(type="boolean") */
    private bool $active = false;

    public function activate(bool $activate = true): void
    {
        $this->active = $activate;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function deactivate(): void
    {
        $this->activate(false);
    }
}
