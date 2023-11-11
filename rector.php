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
use Rector\Core\ValueObject\PhpVersion;
use SerendipityHQ\Integration\Rector\SerendipityHQ;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->phpVersion(PhpVersion::PHP_81);
    $rectorConfig->paths([__DIR__ . '/src', __DIR__ . '/tests']);

    // This causes issues with controllers
    // Until required for tests, keep it commented
    // $rectorConfig->bootstrapFiles([__DIR__ . '/vendor-bin/phpunit/vendor/autoload.php']);
    $rectorConfig->import(SerendipityHQ::SHQ_SYMFONY_BUNDLE);

    $toSkip = SerendipityHQ::buildToSkip(SerendipityHQ::SHQ_SYMFONY_BUNDLE_SKIP);
    $rectorConfig->skip($toSkip);
};
