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

final class SHQUsersExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $containerBuilder): void
    {
        $securityExtConfig       = $containerBuilder->getExtensionConfig('security');
        $securityEntityProviders = $securityExtConfig[0][Configuration::SECURITY_PROVIDERS];

        $providers = [];
        foreach ($securityEntityProviders as $provider => $config) {
            $providers[$provider] = [
                Configuration::SECURITY_PROVIDERS_ENTITY_CLASS    => $config[Configuration::SECURITY_PROVIDERS_ENTITY][Configuration::SECURITY_PROVIDERS_ENTITY_CLASS],
                Configuration::SECURITY_PROVIDERS_ENTITY_PROPERTY => $config[Configuration::SECURITY_PROVIDERS_ENTITY][Configuration::SECURITY_PROVIDERS_ENTITY_PROPERTY],
            ];
        }

        $containerBuilder->prependExtensionConfig(Configuration::BUNDLE_CONFIG_NAME, [Configuration::SECURITY_PROVIDERS => $providers]);
    }

    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $managerRegistryDefinition = new Definition(UsersManagerRegistry::class);
        $containerBuilder->setDefinition(UsersManagerRegistry::class, $managerRegistryDefinition);

        /** @var int $passResetLifespanAmountOf */
        $passResetLifespanAmountOf = $config[Configuration::BUNDLE_CONFIG_PASS][Configuration::BUNDLE_CONFIG_PASS_RESET][Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN][Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_AMOUNT_OF];

        /** @var string $passResetLifespanUnit */
        $passResetLifespanUnit = $config[Configuration::BUNDLE_CONFIG_PASS][Configuration::BUNDLE_CONFIG_PASS_RESET][Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN][Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_UNIT];

        /** @var int $passResetThrottlingMaxActiveTokens */
        $passResetThrottlingMaxActiveTokens = $config[Configuration::BUNDLE_CONFIG_PASS][Configuration::BUNDLE_CONFIG_PASS_RESET][Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING][Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING_MAX_ACTIVE_TOKENS];

        /** @var int $passResetThrottlingMinTimeBetweenTokens */
        $passResetThrottlingMinTimeBetweenTokens = $config[Configuration::BUNDLE_CONFIG_PASS][Configuration::BUNDLE_CONFIG_PASS_RESET][Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING][Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING_MIN_TIME_BETWEEN_TOKENS];

        /** @var string $appSecret */
        $appSecret = $containerBuilder->getParameter('kernel.secret');

        $dispatcherReference                     = new Reference('event_dispatcher');
        $encoderFactoryReference                 = new Reference('security.encoder_factory');
        $entityManagerReference                  = new Reference('doctrine.orm.default_entity_manager');
        $formFactoryReference                    = new Reference('form.factory');
        $propertyAccessorReference               = new Reference('property_accessor');
        $routerReference                         = new Reference('router.default');
        $userPasswordEncoderReference            = new Reference('security.password_encoder');
        $passwordResetTokenRepositoryDefinition  = new Definition(PasswordResetTokenRepository::class, ['App\Entity\PasswordResetToken']);
        $passwordResetTokenRepositoryDefinition->setFactory([$entityManagerReference, 'getRepository']);
        $containerBuilder->setDefinition(PasswordResetTokenRepository::class, $passwordResetTokenRepositoryDefinition);

        foreach ($config[Configuration::SECURITY_PROVIDERS] as $provider => $providerConfig) {
            /** @var string $secUserClass */
            $secUserClass    = $providerConfig[Configuration::SECURITY_PROVIDERS_ENTITY_CLASS];

            /** @var string $secUserProperty */
            $secUserProperty = $providerConfig[Configuration::SECURITY_PROVIDERS_ENTITY_PROPERTY];

            $manager           = 'shq_users.managers.' . $provider;
            $managerDefinition = new Definition(UsersManager::class, [$provider, $secUserClass, $secUserProperty, $dispatcherReference, $entityManagerReference, $propertyAccessorReference]);
            $containerBuilder->setDefinition($manager, $managerRegistryDefinition);
            $managerRegistryDefinition->addMethodCall('addManager', [$provider, $managerDefinition]);

            $passwordHelperDefinition  = new Definition(PasswordHelper::class, [$secUserProperty, $formFactoryReference, $routerReference, $userPasswordEncoderReference]);
            $containerBuilder->setDefinition(PasswordHelper::class, $passwordHelperDefinition);

            $passwordResetTokenGeneratorDefinition = new Definition(PasswordResetTokenGenerator::class, [$appSecret, $secUserProperty, $propertyAccessorReference]);
            $containerBuilder->setDefinition(PasswordResetTokenGenerator::class, $passwordResetTokenGeneratorDefinition);

            $passwordResetHelperDefinition  = new Definition(PasswordResetHelper::class, [$passwordResetTokenRepositoryDefinition, $passwordResetTokenGeneratorDefinition]);
            $containerBuilder->setDefinition(PasswordHelper::class, $passwordResetHelperDefinition);

            $passwordManagerDefinition = new Definition(PasswordManager::class, [$passResetThrottlingMaxActiveTokens, $passResetThrottlingMinTimeBetweenTokens, $passResetLifespanAmountOf, $passResetLifespanUnit, $config[Configuration::BUNDLE_CONFIG_PASS][Configuration::BUNDLE_CONFIG_PASS_RESET][Configuration::BUNDLE_CONFIG_PASS_RESET_TOKEN_CLASS], $secUserClass, $secUserProperty, $entityManagerReference, $dispatcherReference, $passwordHelperDefinition, $passwordResetHelperDefinition, $passwordResetTokenRepositoryDefinition]);
            $containerBuilder->setDefinition(PasswordManager::class, $passwordManagerDefinition);

            if (\is_subclass_of($secUserClass, HasPlainPasswordInterface::class)) {
                $userEncodePasswordListenerDefinition = (new Definition(UserEncodePasswordListener::class, [$encoderFactoryReference]))
                    ->addTag(
                        'doctrine.orm.entity_listener',
                        [
                            'event'                                  => Events::preFlush,
                            Configuration::SECURITY_PROVIDERS_ENTITY => $secUserClass,
                            'lazy'                                   => true,
                        ]
                    );
                $containerBuilder->setDefinition(UserEncodePasswordListener::class, $userEncodePasswordListenerDefinition);
            }
        }
    }
}
