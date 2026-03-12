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
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testDefaultConfig(): void
    {
        $configuration = new Configuration();
        $processor     = new Processor();

        $config = $processor->processConfiguration($configuration, []);

        $expected = [
            Configuration::BUNDLE_CONFIG_PASS => [
                Configuration::BUNDLE_CONFIG_PASS_RESET => [
                    Configuration::BUNDLE_CONFIG_PASS_RESET_TOKEN_CLASS                        => '\App\Entity\PasswordResetToken',
                    Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING                         => [
                        Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING_MAX_ACTIVE_TOKENS       => 3,
                        Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING_MIN_TIME_BETWEEN_TOKENS => 180,
                    ],
                    Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN                           => [
                        Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_AMOUNT_OF                 => 3,
                        Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_UNIT                      => 'week',
                    ],
                ],
            ],
            Configuration::SECURITY_PROVIDERS => [],
        ];

        self::assertSame($expected, $config);
    }

    public function testCustomConfig(): void
    {
        $configuration = new Configuration();
        $processor     = new Processor();

        $customConfig = [
            Configuration::BUNDLE_CONFIG_NAME => [
                Configuration::BUNDLE_CONFIG_PASS => [
                    Configuration::BUNDLE_CONFIG_PASS_RESET => [
                        Configuration::BUNDLE_CONFIG_PASS_RESET_TOKEN_CLASS => 'My\Entity\Token',
                        Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING  => [
                            Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING_MAX_ACTIVE_TOKENS       => 5,
                            Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING_MIN_TIME_BETWEEN_TOKENS => 300,
                        ],
                        Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN => [
                            Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_AMOUNT_OF => 1,
                            Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_UNIT      => 'day',
                        ],
                    ],
                ],
                Configuration::SECURITY_PROVIDERS => [
                    'main' => [
                        Configuration::SECURITY_PROVIDERS_ENTITY_CLASS    => 'App\Entity\User',
                        Configuration::SECURITY_PROVIDERS_ENTITY_PROPERTY => 'email',
                    ],
                ],
            ],
        ];

        $config = $processor->processConfiguration($configuration, $customConfig);

        $expected = [
            Configuration::BUNDLE_CONFIG_PASS => [
                Configuration::BUNDLE_CONFIG_PASS_RESET => [
                    Configuration::BUNDLE_CONFIG_PASS_RESET_TOKEN_CLASS                        => 'My\Entity\Token',
                    Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING                         => [
                        Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING_MAX_ACTIVE_TOKENS       => 5,
                        Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING_MIN_TIME_BETWEEN_TOKENS => 300,
                    ],
                    Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN                           => [
                        Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_AMOUNT_OF                 => 1,
                        Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_UNIT                      => 'day',
                    ],
                ],
            ],
            Configuration::SECURITY_PROVIDERS => [
                'main' => [
                    Configuration::SECURITY_PROVIDERS_ENTITY_CLASS    => 'App\Entity\User',
                    Configuration::SECURITY_PROVIDERS_ENTITY_PROPERTY => 'email',
                ],
            ],
        ];

        self::assertSame($expected, $config);
    }

    public function testInvalidLifespanUnit(): void
    {
        $configuration = new Configuration();
        $processor     = new Processor();

        $invalidConfig = [
            Configuration::BUNDLE_CONFIG_NAME => [
                Configuration::BUNDLE_CONFIG_PASS => [
                    Configuration::BUNDLE_CONFIG_PASS_RESET => [
                        Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN => [
                            Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_UNIT => 'invalid_unit',
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The value "invalid_unit" is not allowed for path "shq_users.password.reset_request.lifespan.unit".');

        $processor->processConfiguration($configuration, $invalidConfig);
    }

    public function testMissingProviderClass(): void
    {
        $configuration = new Configuration();
        $processor     = new Processor();

        $invalidConfig = [
            'shq_users' => [
                'providers' => [
                    'main' => [
                        'property' => 'email',
                    ],
                ],
            ],
        ];

        $config = $processor->processConfiguration($configuration, $invalidConfig);
        self::assertArrayHasKey('providers', $config);
        self::assertArrayHasKey('main', $config['providers']);
        self::assertArrayNotHasKey('class', $config['providers']['main']);
    }
}
