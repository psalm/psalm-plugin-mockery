<?php
namespace Psalm\MockeryPlugin\Tests\Helper;
use Codeception\Exception\Skip;
use Codeception\Exception\TestRuntimeException;
use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use Muglug\PackageVersions\Versions;
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
     * @Given /I have Psalm (newer than|older than) "([0-9.]+)" \(because of "([^"]+)"\)/
     *
     * @return void
     */
    public function havePsalmOfACertainVersionRangeBecauseOf(string $operator, string $version, string $reason)
    {
        if (!isset(self::VERSION_OPERATORS[$operator])) {
            throw new TestRuntimeException("Unknown operator: $operator");
        }
        $op = (string) self::VERSION_OPERATORS[$operator];
        $currentVersion = (string) Versions::getShortVersion('vimeo/psalm');
        $this->debug(sprintf("Current version: %s", $currentVersion));
        $parser = new VersionParser();
        $currentVersion = $parser->normalize($currentVersion);
        $version = $parser->normalize($version);
        $result = Comparator::compare($currentVersion, $op, $version);
        $this->debug("Comparing $currentVersion $op $version => $result");
        if (!$result) {
            throw new Skip("This scenario requires Psalm $op $version because of $reason");
        }
    }

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
        $currentVersion = (string) Versions::getShortVersion('mockery/mockery');
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
}
