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
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\Manager\UsersManagerRegistry;
use SerendipityHQ\Bundle\UsersBundle\Model\Property\HasPlainPasswordInterface;
use SerendipityHQ\Bundle\UsersBundle\Util\PasswordResetTokenGenerator;
use SerendipityHQ\Bundle\UsersBundle\Validator\RolesValidator;
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

        if (false === is_array($securityEntityProviders)) {
            throw new \InvalidArgumentException('Security providers are not listed.');
        }

        $providers = [];
        foreach ($securityEntityProviders as $provider => $config) {
            if (false === isset($config[Configuration::SECURITY_PROVIDERS_ENTITY])) {
                throw new \RuntimeException('It seems you have not configured any Entity User Provider in the security of Symfony. Configure one to use SHQUsersBundle.');
            }

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

        /** @var string $passResetTokenClass */
        $passResetTokenClass = $config[Configuration::BUNDLE_CONFIG_PASS][Configuration::BUNDLE_CONFIG_PASS_RESET][Configuration::BUNDLE_CONFIG_PASS_RESET_TOKEN_CLASS];

        /** @var int $passResetLifespanAmountOf */
        $passResetLifespanAmountOf = $config[Configuration::BUNDLE_CONFIG_PASS][Configuration::BUNDLE_CONFIG_PASS_RESET][Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN][Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_AMOUNT_OF];

        /** @var string $passResetLifespanUnit */
        $passResetLifespanUnit = $config[Configuration::BUNDLE_CONFIG_PASS][Configuration::BUNDLE_CONFIG_PASS_RESET][Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN][Configuration::BUNDLE_CONFIG_PASS_RESET_LIFESPAN_UNIT];

        /** @var int $passResetThrottlingMaxActiveTokens */
        $passResetThrottlingMaxActiveTokens = $config[Configuration::BUNDLE_CONFIG_PASS][Configuration::BUNDLE_CONFIG_PASS_RESET][Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING][Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING_MAX_ACTIVE_TOKENS];

        /** @var int $passResetThrottlingMinTimeBetweenTokens */
        $passResetThrottlingMinTimeBetweenTokens = $config[Configuration::BUNDLE_CONFIG_PASS][Configuration::BUNDLE_CONFIG_PASS_RESET][Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING][Configuration::BUNDLE_CONFIG_PASS_RESET_THROTTLING_MIN_TIME_BETWEEN_TOKENS];

        /** @var string $appSecret */
        $appSecret                           = $containerBuilder->getParameter('kernel.secret');
        $dispatcherReference                 = new Reference('event_dispatcher');
        $entityManagerReference              = new Reference('doctrine.orm.default_entity_manager');
        $formFactoryReference                = new Reference('form.factory');
        $propertyAccessorReference           = new Reference('property_accessor');
        $routerReference                     = new Reference('router.default');
        $userPasswordEncoderFactoryReference = new Reference('security.encoder_factory');
        $sessionReference                    = new Reference('session');

        $managerRegistryDefinition = new Definition(UsersManagerRegistry::class);
        $containerBuilder->setDefinition(UsersManagerRegistry::class, $managerRegistryDefinition);

        $rolesValidatorDefinition = new Definition(RolesValidator::class);
        $containerBuilder->setDefinition(RolesValidator::class, $rolesValidatorDefinition);

        foreach ($config[Configuration::SECURITY_PROVIDERS] as $provider => $providerConfig) {
            /** @var string $secUserClass */
            $secUserClass = $providerConfig[Configuration::SECURITY_PROVIDERS_ENTITY_CLASS];

            /** @var string $secUserProperty */
            $secUserProperty = $providerConfig[Configuration::SECURITY_PROVIDERS_ENTITY_PROPERTY];

            $manager           = 'shq_users.managers.' . $provider;
            $managerDefinition = new Definition(UsersManager::class, [$provider, $secUserClass, $secUserProperty, $dispatcherReference, $entityManagerReference, $propertyAccessorReference, $rolesValidatorDefinition]);
            $containerBuilder->setDefinition($manager, $managerRegistryDefinition);
            $managerRegistryDefinition->addMethodCall('addManager', [$provider, $managerDefinition]);

            $passwordHelperDefinition  = new Definition(PasswordHelper::class, [$secUserProperty, $userPasswordEncoderFactoryReference, $formFactoryReference, $routerReference]);
            $containerBuilder->setDefinition(PasswordHelper::class, $passwordHelperDefinition);

            $passwordResetTokenGeneratorDefinition = new Definition(PasswordResetTokenGenerator::class, [$appSecret, $secUserProperty, $propertyAccessorReference]);
            $containerBuilder->setDefinition(PasswordResetTokenGenerator::class, $passwordResetTokenGeneratorDefinition);

            $passwordResetHelperDefinition  = new Definition(PasswordResetHelper::class, [$passwordResetTokenGeneratorDefinition, $sessionReference]);
            $containerBuilder->setDefinition(PasswordResetHelper::class, $passwordResetHelperDefinition);

            $passwordManagerDefinition = new Definition(PasswordManager::class, [$passResetThrottlingMaxActiveTokens, $passResetThrottlingMinTimeBetweenTokens, $passResetLifespanAmountOf, $passResetLifespanUnit, $secUserClass, $secUserProperty, $entityManagerReference, $dispatcherReference, $passwordHelperDefinition, $passwordResetHelperDefinition, $passResetTokenClass]);
            $containerBuilder->setDefinition(PasswordManager::class, $passwordManagerDefinition);

            if (\is_subclass_of($secUserClass, HasPlainPasswordInterface::class)) {
                $userEncodePasswordListenerDefinition = (new Definition(UserEncodePasswordListener::class, [$passwordManagerDefinition]))
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

        if (isset($manager) && \is_string($manager) && isset($managerDefinition) && $managerDefinition instanceof Definition && 1 === (\is_countable($config[Configuration::SECURITY_PROVIDERS]) ? \count($config[Configuration::SECURITY_PROVIDERS]) : 0)) {
            $containerBuilder->setAlias('shq_users.managers.default_manager', $manager);
            $containerBuilder->setDefinition(UsersManagerInterface::class, $managerDefinition);
        }
    }
}
