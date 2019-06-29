<?php
namespace Psalm\MockeryPlugin\Tests\Helper;
use Codeception\Exception\Skip;
use Codeception\Exception\TestRuntimeException;
use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use Muglug\PackageVersions\Versions as LegacyVersions;
use PackageVersions\Versions as Versions;
use RuntimeException;
// here you can define custom actions
// all public methods declared in helper class will be available in $I
class Acceptance extends \Codeception\Module
{
    /** @var array<string,string */
    const VERSION_OPERATORS = [
        'newer than' => '>',
        'older than' => '<',
    ];

    /**
     * @Given /I have Mockery (newer than|older than) "([0-9.]+)" \(because of "([^"]+)"\)/
     *
     * @return void
     */
    public function haveMockeryOfACertainVersionRangeBecauseOf(string $operator, string $version, string $reason)
    {
        if (!isset(self::VERSION_OPERATORS[$operator])) {
            throw new TestRuntimeException("Unknown operator: $operator");
        }
        $op = (string) self::VERSION_OPERATORS[$operator];
        $currentVersion = $this->getShortVersion('mockery/mockery');
        $this->debug(sprintf("Current version: %s", $currentVersion));
        $parser = new VersionParser();
        $currentVersion = $parser->normalize($currentVersion);
        $version = $parser->normalize($version);
        $result = Comparator::compare($currentVersion, $op, $version);
        $this->debug("Comparing $currentVersion $op $version => $result");
        if (!$result) {
            throw new Skip("This scenario requires Mockery $op $version because of $reason");
        }
    }

    private function getShortVersion(string $package): string
    {
        if (class_exists(Versions::class)) {
            /** @psalm-suppress UndefinedClass psalm 3.0 ignores class_exists check */
            $version = (string) Versions::getVersion($package);
        } elseif (class_exists(LegacyVersions::class)) {
            $version = (string) LegacyVersions::getVersion($package);
        } else {
            throw new RuntimeException(
                'Neither muglug/package-versions-56 nor ocramius/package-version is available,'
                . ' cannot determine versions'
            );
        }

        if (false === strpos($version, '@')) {
            throw new RuntimeException('$version must contain @');
        }

        return explode('@', $version)[0];
    }
}
