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

use Doctrine\ORM\Events;
use SerendipityHQ\Bundle\UsersBundle\Doctrine\UserEncodePasswordListener;
use SerendipityHQ\Bundle\UsersBundle\Helper\PasswordHelper;
use SerendipityHQ\Bundle\UsersBundle\Helper\PasswordResetHelper;
use SerendipityHQ\Bundle\UsersBundle\Manager\PasswordManager;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManager;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerRegistry;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Repository\PasswordResetTokenRepository;
use SerendipityHQ\Bundle\UsersBundle\Util\PasswordResetTokenGenerator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * {@inheritdoc}
 */
final class SHQUsersExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $containerBuilder): void
    {
        $securityExtConfig       = $containerBuilder->getExtensionConfig('security');
        $securityEntityProviders = $securityExtConfig[0][Configuration::SECURITY_PROVIDERS_KEY];

        $providers = [];
        foreach ($securityEntityProviders as $provider => $config) {
            $providers[$provider] = [
                Configuration::SECURITY_ENTITY_CLASS_KEY    => $config[Configuration::SECURITY_ENTITY_KEY][Configuration::SECURITY_ENTITY_CLASS_KEY],
                Configuration::SECURITY_ENTITY_PROPERTY_KEY => $config[Configuration::SECURITY_ENTITY_KEY][Configuration::SECURITY_ENTITY_PROPERTY_KEY],
            ];
        }

        $containerBuilder->prependExtensionConfig(Configuration::BUNDLE_CONFIG_NAME, [Configuration::SECURITY_PROVIDERS_KEY => $providers]);
    }

    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $managerRegistryDefinition = new Definition(UsersManagerRegistry::class);
        $containerBuilder->setDefinition(UsersManagerRegistry::class, $managerRegistryDefinition);

        $maxActiveTokens                        = $config[Configuration::BUNDLE_CONFIG_THROTTLING][Configuration::BUNDLE_CONFIG_MAX_ACTIVE_TOKENS];
        $minTimeBetweenTokens                   = $config[Configuration::BUNDLE_CONFIG_THROTTLING][Configuration::BUNDLE_CONFIG_MIN_TIME_BETWEEN_TOKENS];
        $appSecret                              = $containerBuilder->getParameter('kernel.secret');
        $dispatcherReference                    = new Reference('event_dispatcher');
        $encoderFactoryReference                = new Reference('security.encoder_factory');
        $entityManagerReference                 = new Reference('doctrine.orm.default_entity_manager');
        $formFactoryReference                   = new Reference('form.factory');
        $propertyAccessorReference              = new Reference('property_accessor');
        $routerReference                        = new Reference('router.default');
        $userPasswordEncoderReference           = new Reference('security.password_encoder');
        $passwordResetTokenRepositoryDefinition = new Definition(PasswordResetTokenRepository::class, ['App\Entity\PasswordResetToken']);
        $passwordResetTokenRepositoryDefinition->setFactory([$entityManagerReference, 'getRepository']);
        $containerBuilder->setDefinition(PasswordResetTokenRepository::class, $passwordResetTokenRepositoryDefinition);

        $passwordHelperDefinition  = new Definition(PasswordHelper::class, [$formFactoryReference, $routerReference, $userPasswordEncoderReference]);
        $containerBuilder->setDefinition(PasswordHelper::class, $passwordHelperDefinition);

        foreach ($config[Configuration::SECURITY_PROVIDERS_KEY] as $provider => $providerConfig) {
            $userClass    = $providerConfig[Configuration::SECURITY_ENTITY_CLASS_KEY];
            $userProperty = $providerConfig[Configuration::SECURITY_ENTITY_PROPERTY_KEY];

            $manager           = 'shq_users.managers.' . $provider;
            $managerDefinition = new Definition(UsersManager::class, [$provider, $userClass, $userProperty, $dispatcherReference, $entityManagerReference, $propertyAccessorReference]);
            $containerBuilder->setDefinition($manager, $managerRegistryDefinition);
            $managerRegistryDefinition->addMethodCall('addManager', [$provider, $managerDefinition]);

            $passwordResetTokenGeneratorDefinition = new Definition(PasswordResetTokenGenerator::class, [$appSecret, $userProperty, $propertyAccessorReference]);
            $containerBuilder->setDefinition(PasswordResetTokenGenerator::class, $passwordResetTokenGeneratorDefinition);

            $passwordResetHelperDefinition  = new Definition(PasswordResetHelper::class, [$passwordResetTokenRepositoryDefinition, $passwordResetTokenGeneratorDefinition]);
            $containerBuilder->setDefinition(PasswordHelper::class, $passwordResetHelperDefinition);

            $passwordManagerDefinition = new Definition(PasswordManager::class, [$maxActiveTokens, $minTimeBetweenTokens, $config[Configuration::BUNDLE_CONFIG_TOKEN_CLASS], $userClass, $userProperty, $entityManagerReference, $dispatcherReference, $passwordHelperDefinition, $passwordResetHelperDefinition, $passwordResetTokenRepositoryDefinition]);
            $containerBuilder->setDefinition(PasswordManager::class, $passwordManagerDefinition);

            if (\is_subclass_of($providerConfig[Configuration::SECURITY_ENTITY_CLASS_KEY], HasPlainPasswordInterface::class)) {
                $userEncodePasswordListenerDefinition = (new Definition(UserEncodePasswordListener::class, [$encoderFactoryReference]))
                    ->addTag(
                        'doctrine.orm.entity_listener',
                        [
                            'event'                            => Events::preFlush,
                            Configuration::SECURITY_ENTITY_KEY => $providerConfig[Configuration::SECURITY_ENTITY_CLASS_KEY],
                            'lazy'                             => true,
                        ]
                    );
                $containerBuilder->setDefinition(UserEncodePasswordListener::class, $userEncodePasswordListenerDefinition);
            }
        }
    }
}
