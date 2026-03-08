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

use Rector\Config\RectorConfig;
use SerendipityHQ\Integration\Rector\SerendipityHQ;

return static function (RectorConfig $rectorConfig): void {
    $allowedRunPaths = [
        // From inside Docker
        '/project',

        // ON GitHub Actions
        '/home/runner/.composer/vendor/bin',
    ];

    $canRun = false;
    foreach ($allowedRunPaths as $allowedRunPath) {
        if (str_starts_with($_SERVER['PATH'] ?? '', $allowedRunPath)) {
            $canRun = true;
        }
    }

    if (false === $canRun) {
        $message = <<<EOF
            It seems you are running `composer fix` from outside the development container, maybe from your host machine.
            Please, run it from inside the container (`make start && make sh`).
            EOF;

        throw new RuntimeException(sprintf("%s\n\nCurrent path:\n%s\n\nAllowed paths:\n%s", $message, $_SERVER['PATH'], implode(', ', $allowedRunPaths)));
    }

    $rectorConfig->paths([__DIR__ . '/src', __DIR__ . '/tests']);

    // This causes issues with controllers
    // Until required for tests, keep it commented
    $rectorConfig->bootstrapFiles([__DIR__ . '/vendor-bin/phpunit/vendor/autoload.php']);
    $rectorConfig->import(SerendipityHQ::SHQ_SYMFONY_BUNDLE);

    // Symfony
    $rectorConfig->import(Rector\Symfony\Set\SymfonySetList::SYMFONY_50);
    $rectorConfig->import(Rector\Symfony\Set\SymfonySetList::SYMFONY_50_TYPES);
    $rectorConfig->import(Rector\Symfony\Set\SymfonySetList::SYMFONY_51);
    $rectorConfig->import(Rector\Symfony\Set\SymfonySetList::SYMFONY_52);
    $rectorConfig->import(Rector\Symfony\Set\SymfonySetList::SYMFONY_52_VALIDATOR_ATTRIBUTES);
    $rectorConfig->import(Rector\Symfony\Set\SymfonySetList::SYMFONY_53);
    $rectorConfig->import(Rector\Symfony\Set\SymfonySetList::SYMFONY_54);

    // PHP sets
    $rectorConfig->symfonyContainerXml(__DIR__ . '/tests/Fixtures/var/cache/test/SerendipityHQ_Bundle_UsersBundle_Tests_Fixtures_App_SHQBundleUsersTestKernelTestDebugContainer.xml');

    $toSkip   = SerendipityHQ::buildToSkip(SerendipityHQ::SHQ_SYMFONY_BUNDLE_SKIP);
    $toSkip[] = __DIR__ . '/tests/Fixtures/var';
    $rectorConfig->skip($toSkip);

    // Ensure file system caching is used instead of in-memory.
    $rectorConfig->cacheClass(Rector\Caching\ValueObject\Storage\FileCacheStorage::class);

    // Specify a path that works locally as well as on CI job runners.
    $rectorConfig->cacheDirectory('./var/cache/rector');
};
