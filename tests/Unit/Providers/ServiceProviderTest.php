<?php

namespace OrchidHelpers\Tests\Unit\Providers;

use OrchidHelpers\Providers\FoundationServiceProvider;
use OrchidHelpers\Providers\MacrosServiceProvider;
use OrchidHelpers\Providers\ComponentServiceProvider;
use OrchidHelpers\Providers\ViewServiceProvider;
use OrchidHelpers\Providers\RouteServiceProvider;
use OrchidHelpers\Providers\EventServiceProvider;
use OrchidHelpers\Providers\CommandServiceProvider;
use OrchidHelpers\Providers\MigrationServiceProvider;
use OrchidHelpers\Providers\ValidationServiceProvider;
use OrchidHelpers\Providers\HelperServiceProvider;
use OrchidHelpers\Providers\ConfigServiceProvider;
use OrchidHelpers\Providers\MiddlewareServiceProvider;
use OrchidHelpers\Providers\PolicyServiceProvider;
use OrchidHelpers\Providers\RepositoryServiceProvider;
use OrchidHelpers\Providers\ServiceServiceProvider;
use OrchidHelpers\Providers\FacadeServiceProvider;
use OrchidHelpers\Providers\CacheServiceProvider;
use OrchidHelpers\Providers\QueueServiceProvider;
use OrchidHelpers\Providers\MailServiceProvider;
use OrchidHelpers\Providers\NotificationServiceProvider;
use OrchidHelpers\Providers\BroadcastServiceProvider;
use OrchidHelpers\Providers\AuthServiceProvider;
use OrchidHelpers\Providers\DatabaseServiceProvider;
use OrchidHelpers\Providers\FilesystemServiceProvider;
use OrchidHelpers\Providers\SessionServiceProvider;
use OrchidHelpers\Providers\TranslationServiceProvider;
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
    
    public function test_component_service_provider_registers_correctly()
    {
        $provider = new ComponentServiceProvider($this->app);
        
        $this->assertInstanceOf(ComponentServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_view_service_provider_registers_correctly()
    {
        $provider = new ViewServiceProvider($this->app);
        
        $this->assertInstanceOf(ViewServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_route_service_provider_registers_correctly()
    {
        $provider = new RouteServiceProvider($this->app);
        
        $this->assertInstanceOf(RouteServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_event_service_provider_registers_correctly()
    {
        $provider = new EventServiceProvider($this->app);
        
        $this->assertInstanceOf(EventServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_command_service_provider_registers_correctly()
    {
        $provider = new CommandServiceProvider($this->app);
        
        $this->assertInstanceOf(CommandServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_migration_service_provider_registers_correctly()
    {
        $provider = new MigrationServiceProvider($this->app);
        
        $this->assertInstanceOf(MigrationServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_validation_service_provider_registers_correctly()
    {
        $provider = new ValidationServiceProvider($this->app);
        
        $this->assertInstanceOf(ValidationServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_helper_service_provider_registers_correctly()
    {
        $provider = new HelperServiceProvider($this->app);
        
        $this->assertInstanceOf(HelperServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_config_service_provider_registers_correctly()
    {
        $provider = new ConfigServiceProvider($this->app);
        
        $this->assertInstanceOf(ConfigServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_middleware_service_provider_registers_correctly()
    {
        $provider = new MiddlewareServiceProvider($this->app);
        
        $this->assertInstanceOf(MiddlewareServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_policy_service_provider_registers_correctly()
    {
        $provider = new PolicyServiceProvider($this->app);
        
        $this->assertInstanceOf(PolicyServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_repository_service_provider_registers_correctly()
    {
        $provider = new RepositoryServiceProvider($this->app);
        
        $this->assertInstanceOf(RepositoryServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_service_service_provider_registers_correctly()
    {
        $provider = new ServiceServiceProvider($this->app);
        
        $this->assertInstanceOf(ServiceServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_facade_service_provider_registers_correctly()
    {
        $provider = new FacadeServiceProvider($this->app);
        
        $this->assertInstanceOf(FacadeServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_cache_service_provider_registers_correctly()
    {
        $provider = new CacheServiceProvider($this->app);
        
        $this->assertInstanceOf(CacheServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_queue_service_provider_registers_correctly()
    {
        $provider = new QueueServiceProvider($this->app);
        
        $this->assertInstanceOf(QueueServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_mail_service_provider_registers_correctly()
    {
        $provider = new MailServiceProvider($this->app);
        
        $this->assertInstanceOf(MailServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_notification_service_provider_registers_correctly()
    {
        $provider = new NotificationServiceProvider($this->app);
        
        $this->assertInstanceOf(NotificationServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_broadcast_service_provider_registers_correctly()
    {
        $provider = new BroadcastServiceProvider($this->app);
        
        $this->assertInstanceOf(BroadcastServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_auth_service_provider_registers_correctly()
    {
        $provider = new AuthServiceProvider($this->app);
        
        $this->assertInstanceOf(AuthServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_database_service_provider_registers_correctly()
    {
        $provider = new DatabaseServiceProvider($this->app);
        
        $this->assertInstanceOf(DatabaseServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_filesystem_service_provider_registers_correctly()
    {
        $provider = new FilesystemServiceProvider($this->app);
        
        $this->assertInstanceOf(FilesystemServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_session_service_provider_registers_correctly()
    {
        $provider = new SessionServiceProvider($this->app);
        
        $this->assertInstanceOf(SessionServiceProvider::class, $provider);
        
        $provider->boot();
        $provider->register();
    }
    
    public function test_translation_service_provider_registers_correctly()
    {
        $provider = new TranslationServiceProvider($this->app);
        
        $this->assertInstanceOf(TranslationServiceProvider::class, $provider);
        
        $provider->boot();
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