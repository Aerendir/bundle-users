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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\UsersBundle\DependencyInjection\Configuration;
use SerendipityHQ\Bundle\UsersBundle\DependencyInjection\SHQUsersExtension;
use SerendipityHQ\Bundle\UsersBundle\Doctrine\UserEncodePasswordListener;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerRegistry;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity\User;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class SHQUsersExtensionTest extends TestCase
{
    private SHQUsersExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new SHQUsersExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.secret', 'secret');
    }

    public function testPrepend(): void
    {
        $securityConfig = [
            'providers' => [
                'main' => [
                    'entity' => [
                        'class'    => 'App\Entity\User',
                        'property' => 'email',
                    ],
                ],
            ],
        ];

        $this->container->prependExtensionConfig('security', $securityConfig);

        // Simulating the behavior of prepend method which uses getExtensionConfig
        // However, ContainerBuilder::getExtensionConfig returns configs that were prepended.
        // SHQUsersExtension::prepend calls $containerBuilder->getExtensionConfig('security')

        $this->extension->prepend($this->container);

        $shqUsersConfig = $this->container->getExtensionConfig(Configuration::BUNDLE_CONFIG_NAME);
        self::assertCount(1, $shqUsersConfig);
        self::assertArrayHasKey('providers', $shqUsersConfig[0]);
        self::assertArrayHasKey('main', $shqUsersConfig[0]['providers']);
        self::assertSame('App\Entity\User', $shqUsersConfig[0]['providers']['main']['class']);
        self::assertSame('email', $shqUsersConfig[0]['providers']['main']['property']);
    }

    public function testPrependThrowsExceptionIfProvidersNotListed(): void
    {
        $this->container->prependExtensionConfig('security', []);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Security providers are not listed.');

        $this->extension->prepend($this->container);
    }

    public function testPrependThrowsExceptionIfEntityNotConfigured(): void
    {
        $securityConfig = [
            'providers' => [
                'main' => [
                    // 'entity' is missing
                ],
            ],
        ];

        $this->container->prependExtensionConfig('security', $securityConfig);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('It seems you have not configured any Entity User Provider in the security of Symfony.');

        $this->extension->prepend($this->container);
    }

    public function testLoad(): void
    {
        $configs = [
            'shq_users' => [
                'providers' => [
                    'main' => [
                        'class'    => User::class,
                        'property' => 'email',
                    ],
                ],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertTrue($this->container->hasDefinition(UsersManagerRegistry::class));
        self::assertTrue($this->container->hasDefinition('shq_users.managers.main'));

        $managerDefinition = $this->container->getDefinition('shq_users.managers.main');
        self::assertSame(UsersManagerRegistry::class, $managerDefinition->getClass());

        // When only one provider, UsersManagerInterface should be an alias/definition to the manager
        self::assertTrue($this->container->hasDefinition(UsersManagerInterface::class));
    }

    public function testLoadWithMultipleProviders(): void
    {
        $configs = [
            'shq_users' => [
                'providers' => [
                    'main' => [
                        'class'    => User::class,
                        'property' => 'email',
                    ],
                    'other' => [
                        'class'    => User::class,
                        'property' => 'username',
                    ],
                ],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertTrue($this->container->hasDefinition('shq_users.managers.main'));
        self::assertTrue($this->container->hasDefinition('shq_users.managers.other'));

        // When multiple providers, UsersManagerInterface should NOT be defined
        self::assertFalse($this->container->hasDefinition(UsersManagerInterface::class));
    }

    public function testLoadWithHasPlainPasswordInterface(): void
    {
        $configs = [
            'shq_users' => [
                'providers' => [
                    'main' => [
                        'class'    => User::class,
                        'property' => 'email',
                    ],
                ],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertTrue($this->container->hasDefinition(UserEncodePasswordListener::class));
        $definition = $this->container->getDefinition(UserEncodePasswordListener::class);
        self::assertTrue($definition->hasTag('doctrine.orm.entity_listener'));

        $tags = $definition->getTag('doctrine.orm.entity_listener');
        self::assertCount(1, $tags);
        self::assertSame('preFlush', $tags[0]['event']);
        self::assertSame(User::class, $tags[0]['entity']);
    }

    public function testLoadWithoutHasPlainPasswordInterface(): void
    {
        // Simple anonymous class to test the behavior
        $configs = [
            'shq_users' => [
                'providers' => [
                    'main' => [
                        'class'    => \stdClass::class,
                        'property' => 'email',
                    ],
                ],
            ],
        ];

        $this->extension->load($configs, $this->container);

        self::assertFalse($this->container->hasDefinition(UserEncodePasswordListener::class));
    }
}
