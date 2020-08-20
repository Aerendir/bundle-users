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
    /**
     * @var string
     */
    public const BUNDLE_CONFIG_NAME = 'shq_users';
    /**
     * @var string
     */
    public const BUNDLE_CONFIG_TOKEN_CLASS = 'token_class';
    /**
     * @var string
     */
    public const SECURITY_PROVIDERS_KEY = 'providers';
    /**
     * @var string
     */
    public const SECURITY_ENTITY_KEY = 'entity';
    /**
     * @var string
     */
    public const SECURITY_ENTITY_CLASS_KEY = 'class';
    /**
     * @var string
     */
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
