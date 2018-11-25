<?php
namespace Psalm\MockeryPlugin;

use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;

class Plugin implements PluginEntryPointInterface
{
    /** @return void */
    public function __invoke(RegistrationInterface $psalm, SimpleXMLElement $config = null)
    {
    	$psalm->addStubFile(__DIR__ . '/stubs/Mockery.php');
    	
    	require_once __DIR__ . '/hooks/MockReturnTypeUpdater.php';
        $registration->registerHooksFromClass(Hooks\MockReturnTypeUpdater::class);
    }
}
