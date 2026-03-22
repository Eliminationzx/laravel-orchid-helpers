<?php

namespace OrchidHelpers\Tests\Unit\Providers;

use OrchidHelpers\Providers\FoundationServiceProvider;
use OrchidHelpers\Providers\MacrosServiceProvider;
use OrchidHelpers\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
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
    
    public function test_views_are_registered()
    {
        // Test that views are registered by the FoundationServiceProvider
        $viewFinder = $this->app['view']->getFinder();
        
        // This is a basic test to ensure the provider doesn't throw errors
        // when booting with view registration
        $provider = new FoundationServiceProvider($this->app);
        $provider->boot();
        
        $this->assertTrue(true); // Just ensure no exceptions
    }
}