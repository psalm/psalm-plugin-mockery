<?php

namespace Psalm\MockeryPlugin;

use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;

class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, SimpleXMLElement $config = null): void
    {
        $registration->addStubFile(__DIR__ . '/stubs/Mockery.php');

        $this->loadPhpUnitIntegration($registration);

        require_once __DIR__ . '/Hooks/MockReturnTypeUpdater.php';
        $registration->registerHooksFromClass(Hooks\MockReturnTypeUpdater::class);
    }

    private function loadPhpUnitIntegration(RegistrationInterface $registration): void
    {
        // Mockery doesn't do any funny stuff with trait aliases since v1.4
        if (VersionUtils::packageVersionIs('mockery/mockery', '>=', '1.4')) {
            return;
        }

        if (
            class_exists('PHPUnit_Framework_TestCase')
            || (class_exists('\PHPUnit\Runner\Version')
                && version_compare(\PHPUnit\Runner\Version::id(), '8.0.0', '<')
            )
        ) {
            $registration->addStubFile(__DIR__ . '/stubs/Mockery/AssertPostConditionsV7.php');
        } elseif (
            class_exists('\PHPUnit\Runner\Version')
            && version_compare(\PHPUnit\Runner\Version::id(), '8.0.0', '>=')
        ) {
            $registration->addStubFile(__DIR__ . '/stubs/Mockery/AssertPostConditionsV8.php');
        }
    }
}
