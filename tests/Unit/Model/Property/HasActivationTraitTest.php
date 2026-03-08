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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\Model\Property;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasActivationTrait;

final class HasActivationTraitTest extends TestCase
{
    public function testHasActivationTrait(): void
    {
        $mock = new class {
            use HasActivationTrait;
        };

        $this->assertFalse($mock->isActive());

        $mock->activate();
        $this->assertTrue($mock->isActive());

        $mock->activate(false);
        $this->assertFalse($mock->isActive());

        $mock->activate(true);
        $this->assertTrue($mock->isActive());

        $mock->deactivate();
        $this->assertFalse($mock->isActive());
    }
}
