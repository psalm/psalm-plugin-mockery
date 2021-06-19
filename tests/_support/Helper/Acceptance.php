<?php

namespace Psalm\MockeryPlugin\Tests\Helper;

use Codeception\Exception\TestRuntimeException;
use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use Muglug\PackageVersions\Versions as LegacyVersions;
use PackageVersions\Versions as Versions;
use RuntimeException;
use PHPUnit\Framework\SkippedTestError;

// here you can define custom actions
// all public methods declared in helper class will be available in $I
class Acceptance extends \Codeception\Module
{
}
