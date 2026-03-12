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

namespace SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App;

use Composer\InstalledVersions;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\UsersBundle\SHQUsersBundle;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Controller\TestController;
use SerendipityHQ\Bundle\UsersBundle\Tests\Fixtures\App\Entity\User;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Zenstruck\Foundry\Configuration;
use Zenstruck\Foundry\ZenstruckFoundryBundle;

final class SHQBundleUsersTestKernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        if (false === class_exists(ZenstruckFoundryBundle::class)) {
            throw new \LogicException('ZenstruckFoundryBundle is required to run the tests. Please install it via composer require --dev zenstruck/foundry');
        }

        yield new FrameworkBundle();
        yield new SecurityBundle();
        yield new TwigBundle();
        yield new DoctrineBundle();
        yield new SHQUsersBundle();
        yield new ZenstruckFoundryBundle();
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->loadFromExtension('framework', [
            'secret'               => 'test_secret',
            'test'                 => true,
            'router'               => ['utf8' => true],
            'session'              => ['storage_factory_id' => 'session.storage.factory.mock_file'],
            'translator'           => ['enabled' => true],
            'validation'           => ['enabled' => true],
            'property_access'      => ['enabled' => true],
            'csrf_protection'      => ['enabled' => false],
        ]);

        $container->loadFromExtension('twig', [
            'default_path'     => '%kernel.project_dir%/App/templates',
            'strict_variables' => true,
        ]);

        $container->loadFromExtension('security', [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => 'auto',
            ],
            'providers' => [
                'users' => [
                    'entity' => [
                        'class'    => User::class,
                        'property' => 'email',
                    ],
                ],
            ],
            'firewalls' => [
                'main' => [
                    'provider' => 'users',
                ],
            ],
        ]);

        $ormConfig = [
            'mappings'                    => [
                'App' => [
                    'is_bundle' => false,
                    'type'      => 'attribute',
                    'dir'       => '%kernel.project_dir%/App/Entity',
                    'prefix'    => (new \ReflectionClass(User::class))->getNamespaceName(),
                    'alias'     => 'App',
                ],
            ],
        ];

        if (interface_exists(EntityManagerInterface::class)) {
            $isOrm3 = false;
            if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled('doctrine/orm')) {
                $version = InstalledVersions::getVersion('doctrine/orm');
                if (null !== $version && str_starts_with($version, '3.')) {
                    $isOrm3 = true;
                }
            }

            if (false === $isOrm3) {
                $ormConfig['auto_generate_proxy_classes'] = true;
            }
        }

        $container->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite:///%kernel.project_dir%/var/data.db'],
            'orm'  => $ormConfig,
        ]);

        $zenstruckFoundryConfig = [];
        if (PHP_VERSION_ID >= 80400 && class_exists(InstalledVersions::class) && InstalledVersions::isInstalled('zenstruck/foundry')) {
            $version = InstalledVersions::getVersion('zenstruck/foundry');
            if (null !== $version && version_compare($version, '2.4.0', '>=') && class_exists(Configuration::class) && method_exists(Configuration::class, 'enableAutoRefreshWithLazyObjects')) {
                $zenstruckFoundryConfig['enable_auto_refresh_with_lazy_objects'] = true;
            }
        }

        $container->loadFromExtension('zenstruck_foundry', $zenstruckFoundryConfig);

        if (file_exists(__DIR__ . '/config/services.yaml')) {
            $loader->load(__DIR__ . '/config/services.yaml');
        }

        $container->register(TestController::class)
            ->setPublic(true)
            ->setAutoconfigured(true)
            ->setAutowired(true)
            ->addTag('container.service_subscriber')
            ->addTag('controller.service_arguments')
            ->setArguments([
                new Reference('security.token_storage'),
                new Reference('form.factory'),
                new Reference('twig'),
            ])
            ->addMethodCall('setContainer', [new Reference('service_container')]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->add('password_change', '/password-change')
            ->controller([TestController::class, 'passwordChange']);

        $routes->add('confirmed_password', '/confirmed-password')
            ->controller([TestController::class, 'confirmedPassword']);

        $routes->add('password_reset_request', '/password-reset-request')
            ->controller([TestController::class, 'passwordResetRequest']);

        $routes->add('password_reset', '/password-reset')
            ->controller([TestController::class, 'passwordReset']);
    }
}
