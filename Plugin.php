<?php
namespace Psalm\MockeryPlugin;

use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;

class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, SimpleXMLElement $config = null) : void
    {
        $registration->addStubFile(__DIR__ . '/stubs/Mockery.php');

        if (class_exists('PHPUnit_Framework_TestCase')
            || (class_exists('\PHPUnit\Runner\Version')
                && version_compare(\PHPUnit\Runner\Version::id(), '8.0.0', '<')
            )
        ) {
            $registration->addStubFile(__DIR__ . '/stubs/Mockery/AssertPostConditionsV7.php');
        } elseif (class_exists('\PHPUnit\Runner\Version')
            && version_compare(\PHPUnit\Runner\Version::id(), '8.0.0', '>=')
        ) {
            $registration->addStubFile(__DIR__ . '/stubs/Mockery/AssertPostConditionsV8.php');
        }

        require_once __DIR__ . '/hooks/MockReturnTypeUpdater.php';
        $registration->registerHooksFromClass(Hooks\MockReturnTypeUpdater::class);
    }
}
