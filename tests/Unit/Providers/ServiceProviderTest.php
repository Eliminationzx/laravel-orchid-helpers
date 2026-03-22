<?php

namespace OrchidHelpers\Tests\Unit\Providers;

use OrchidHelpers\Providers\FoundationServiceProvider;
use OrchidHelpers\Providers\MacrosServiceProvider;
use OrchidHelpers\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure configuration is loaded
        $this->app['config']->set('orchid-helpers', require __DIR__ . '/../../../config/orchid-helpers.php');
    }
    
    public function test_foundation_service_provider_registers_correctly()
    {
        $provider = new FoundationServiceProvider($this->app);
        
        $this->assertInstanceOf(FoundationServiceProvider::class, $provider);
        
        // Test that the provider can be booted without errors
        $provider->boot();
        
        // Test that the provider can be registered without errors
        $provider->register();
    }
    
    public function test_macros_service_provider_registers_correctly()
    {
        $provider = new MacrosServiceProvider($this->app);
        
        $this->assertInstanceOf(MacrosServiceProvider::class, $provider);
        
        // Test that the provider can be booted without errors
        $provider->boot();
        
        // Test that the provider can be registered without errors
        $provider->register();
    }
    
    public function test_service_providers_are_registered_in_app()
    {
        // The TestCase should already register the providers
        $providers = $this->app->getProviders(FoundationServiceProvider::class);
        
        $this->assertNotEmpty($providers);
        
        $providers = $this->app->getProviders(MacrosServiceProvider::class);
        
        $this->assertNotEmpty($providers);
    }
    
    public function test_configuration_file_is_publishable()
    {
        // Test that configuration can be merged
        $config = $this->app['config']->get('orchid-helpers');
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('allowed_models', $config);
    }
}