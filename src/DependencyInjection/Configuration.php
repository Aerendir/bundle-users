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

use SerendipityHQ\Bundle\UsersBundle\Model\Property\PasswordResetTokenInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

final class Configuration implements ConfigurationInterface
{
    public const BUNDLE_CONFIG_NAME = 'shq_users';

    public const BUNDLE_CONFIG_TOKEN_CLASS = 'token_class';

    public const BUNDLE_CONFIG_THROTTLING = 'throttling';

    public const BUNDLE_CONFIG_MAX_ACTIVE_TOKENS = 'max_active_tokens';

    public const BUNDLE_CONFIG_MIN_TIME_BETWEEN_TOKENS = 'min_time_between_tokens';

    public const SECURITY_PROVIDERS_KEY = 'providers';

    public const SECURITY_ENTITY_KEY = 'entity';

    public const SECURITY_ENTITY_CLASS_KEY = 'class';

    public const SECURITY_ENTITY_PROPERTY_KEY = 'property';

    /**
     * @throws \RuntimeException
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::BUNDLE_CONFIG_NAME);
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode(self::BUNDLE_CONFIG_TOKEN_CLASS)
                    ->defaultValue('\App\Entity\PasswordResetToken')
                ->end()
                ->arrayNode(self::BUNDLE_CONFIG_THROTTLING)
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode(self::BUNDLE_CONFIG_MAX_ACTIVE_TOKENS)
                            ->info('The max number of non expired requests a user can have at the same time.')
                            ->defaultValue(3)
                        ->end()
                        ->integerNode(self::BUNDLE_CONFIG_MIN_TIME_BETWEEN_TOKENS)
                            ->info('Minimum time (in seconds) between two password reset requests.')
                            ->defaultValue(180)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode(self::SECURITY_PROVIDERS_KEY)
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode(self::SECURITY_ENTITY_CLASS_KEY)->cannotBeEmpty()->end()
                            ->scalarNode(self::SECURITY_ENTITY_PROPERTY_KEY)->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->validate()
                ->ifTrue(function ($tree) {
                    $tokenClass = $tree[self::BUNDLE_CONFIG_TOKEN_CLASS];
                    if (false === \class_exists($tokenClass)) {
                        throw new InvalidTypeException(\Safe\sprintf("The entity class %s doesn't exist.", $tokenClass));
                    }

                    if (false === \is_a($tokenClass, PasswordResetTokenInterface::class, true)) {
                        throw new InvalidTypeException(\Safe\sprintf('The entity %s MUST implement interface %s.', $tokenClass, PasswordResetTokenInterface::class));
                    }

                    return $tree;
                })
                ->then(function ($tree) {
                    return $tree;
                })
            ->end();

        return $treeBuilder;
    }
}
