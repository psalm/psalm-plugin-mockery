<?php

namespace Psalm\MockeryPlugin;

use Composer\InstalledVersions;
use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;

abstract class VersionUtils
{
    /** @param "!="|"<"|"<="|"<>"|"="|"=="|">"|">=" $op */
    public static function packageVersionIs(string $package, string $op, string $ref): bool
    {
        try {
            $currentVersion = InstalledVersions::getVersion($package);
        } catch (\OutOfBoundsException $exception) {
            return false;
        }

        if ($currentVersion === null) {
            return false;
        }

        $parser = new VersionParser();

        $currentVersion = $parser->normalize($currentVersion);
        $ref = $parser->normalize($ref);

        return Comparator::compare($currentVersion, $op, $ref);
    }
}
