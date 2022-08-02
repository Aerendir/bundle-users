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

namespace SerendipityHQ\Bundle\UsersBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const BUNDLE_CONFIG_NAME                                          = 'shq_users';
    public const BUNDLE_CONFIG_PASS                                          = 'password';
    public const BUNDLE_CONFIG_PASS_RESET                                    = 'reset_request';
    public const BUNDLE_CONFIG_PASS_RESET_TOKEN_CLASS                        = 'token_class';
    public const BUNDLE_CONFIG_PASS_RESET_THROTTLING                         = 'throttling';
    public const BUNDLE_CONFIG_PASS_RESET_THROTTLING_MAX_ACTIVE_TOKENS       = 'max_active_tokens';
    public const BUNDLE_CONFIG_PASS_RESET_THROTTLING_MIN_TIME_BETWEEN_TOKENS = 'min_time_between_tokens';
    public const BUNDLE_CONFIG_PASS_RESET_LIFESPAN                           = 'lifespan';
    public const BUNDLE_CONFIG_PASS_RESET_LIFESPAN_AMOUNT_OF                 = 'amount_of';
    public const BUNDLE_CONFIG_PASS_RESET_LIFESPAN_UNIT                      = 'unit';
    public const SECURITY_PROVIDERS                                          = 'providers';
    public const SECURITY_PROVIDERS_ENTITY                                   = 'entity';
    public const SECURITY_PROVIDERS_ENTITY_CLASS                             = 'class';
    public const SECURITY_PROVIDERS_ENTITY_PROPERTY                          = 'property';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::BUNDLE_CONFIG_NAME);
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode(self::BUNDLE_CONFIG_PASS)
                ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode(self::BUNDLE_CONFIG_PASS_RESET)
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode(self::BUNDLE_CONFIG_PASS_RESET_TOKEN_CLASS)
                                    ->info('The FQN of the PasswordResetToken class.')
                                    ->defaultValue('\App\Entity\PasswordResetToken')
                                ->end()
                                ->arrayNode(self::BUNDLE_CONFIG_PASS_RESET_THROTTLING)
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->integerNode(self::BUNDLE_CONFIG_PASS_RESET_THROTTLING_MAX_ACTIVE_TOKENS)
                                            ->info('The max number of non expired requests a user can have at the same time.')
                                            ->defaultValue(3)
                                        ->end()
                                        ->integerNode(self::BUNDLE_CONFIG_PASS_RESET_THROTTLING_MIN_TIME_BETWEEN_TOKENS)
                                            ->info('Minimum time (in seconds) between two password reset requests.')
                                            ->defaultValue(180)
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode(self::BUNDLE_CONFIG_PASS_RESET_LIFESPAN)
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->integerNode(self::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_AMOUNT_OF)
                                            ->info('The amount of units for which the token is valid.')
                                            ->defaultValue(3)
                                        ->end()
                                        ->enumNode(self::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_UNIT)
                                            ->info('The unit in which the amount has to be counted on.')
                                            // See https://www.php.net/manual/en/datetime.formats.relative.php
                                            ->values(['sec', 'second', 'seconds', 'min', 'minute', 'minutes', 'hour', 'hours', 'day', 'days', 'month', 'months', 'year', 'years', 'week', 'weeks'])
                                            ->defaultValue('week')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode(self::SECURITY_PROVIDERS)
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode(self::SECURITY_PROVIDERS_ENTITY_CLASS)->cannotBeEmpty()->end()
                            ->scalarNode(self::SECURITY_PROVIDERS_ENTITY_PROPERTY)->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
