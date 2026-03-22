<?php

namespace OrchidHelpers\Tests\Unit\Alerts;

use OrchidHelpers\Tests\TestCase;
use Orchid\Support\Facades\Alert;
use Mockery;

class AlertHelpersTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function test_success_alert_calls_alert_success()
    {
        Alert::shouldReceive('success')
            ->once()
            ->with('Test success message');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\SuccessAlert::make('Test success message');
        
        $this->assertTrue(true); // Add assertion to avoid risky test
    }

    /** @test */
    public function test_success_alert_uses_default_message()
    {
        Alert::shouldReceive('success')
            ->once()
            ->with('Операция выполнена успешно!');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\SuccessAlert::make();
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_error_alert_calls_alert_error()
    {
        Alert::shouldReceive('error')
            ->once()
            ->with('Test error message');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\ErrorAlert::make('Test error message');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_error_alert_uses_default_message()
    {
        Alert::shouldReceive('error')
            ->once()
            ->with('Произошла ошибка!');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\ErrorAlert::make();
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_warning_alert_calls_alert_warning()
    {
        Alert::shouldReceive('warning')
            ->once()
            ->with('Test warning message');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\WarningAlert::make('Test warning message');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_warning_alert_uses_default_message()
    {
        Alert::shouldReceive('warning')
            ->once()
            ->with('Внимание!');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\WarningAlert::make();
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_info_alert_calls_alert_info()
    {
        Alert::shouldReceive('info')
            ->once()
            ->with('Test info message');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\InfoAlert::make('Test info message');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_info_alert_uses_default_message()
    {
        Alert::shouldReceive('info')
            ->once()
            ->with('Информация');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\InfoAlert::make();
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_toast_alert_calls_alert_toast()
    {
        Alert::shouldReceive('toast')
            ->once()
            ->with('Test toast message', 'success');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\ToastAlert::make('Test toast message');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_toast_alert_with_custom_type()
    {
        Alert::shouldReceive('toast')
            ->once()
            ->with('Test toast message', 'error');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\ToastAlert::make('Test toast message', 'error');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_banner_alert_calls_alert_info_by_default()
    {
        Alert::shouldReceive('info')
            ->once()
            ->with('Test banner message');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\BannerAlert::make('Test banner message');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_banner_alert_with_success_type()
    {
        Alert::shouldReceive('success')
            ->once()
            ->with('Test banner message');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\BannerAlert::make('Test banner message', 'success');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_inline_alert_calls_alert_error()
    {
        Alert::shouldReceive('error')
            ->once()
            ->with('Поле email: Test validation error');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\InlineAlert::make('Test validation error', 'email');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_inline_alert_without_field()
    {
        Alert::shouldReceive('error')
            ->once()
            ->with('Test validation error');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\InlineAlert::make('Test validation error');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_confirmation_alert_calls_alert_warning()
    {
        Alert::shouldReceive('warning')
            ->once();
        
        \OrchidHelpers\Orchid\Helpers\Alerts\ConfirmationAlert::make('Are you sure?');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_progress_alert_calls_alert_info()
    {
        Alert::shouldReceive('info')
            ->once()
            ->with('Processing... (50%)');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\ProgressAlert::make('Processing...', 50);
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_progress_alert_complete_calls_alert_success()
    {
        Alert::shouldReceive('success')
            ->once()
            ->with('Completed!');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\ProgressAlert::complete('Completed!');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_timed_alert_calls_alert_info_by_default()
    {
        Alert::shouldReceive('info')
            ->once();
        
        \OrchidHelpers\Orchid\Helpers\Alerts\TimedAlert::make('Test timed message');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_dismissible_alert_calls_alert_info_by_default()
    {
        Alert::shouldReceive('info')
            ->once();
        
        \OrchidHelpers\Orchid\Helpers\Alerts\DismissibleAlert::make('Test dismissible message');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_action_alert_calls_alert_info_by_default()
    {
        Alert::shouldReceive('info')
            ->once();
        
        \OrchidHelpers\Orchid\Helpers\Alerts\ActionAlert::make('Test action message', 'Click me', '#');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_status_alert_calls_alert_info()
    {
        Alert::shouldReceive('info')
            ->once()
            ->with('Status changed: draft → published');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\StatusAlert::make('Status changed', 'draft', 'published');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_system_alert_calls_alert_warning_by_default()
    {
        Alert::shouldReceive('warning')
            ->once();
        
        \OrchidHelpers\Orchid\Helpers\Alerts\SystemAlert::make('System notification');
        
        $this->assertTrue(true);
    }

    /** @test */
    public function test_system_alert_with_code()
    {
        Alert::shouldReceive('warning')
            ->once()
            ->with('[Система] System error (код: ERR-001)');
        
        \OrchidHelpers\Orchid\Helpers\Alerts\SystemAlert::make('System error', 'ERR-001');
        
        $this->assertTrue(true);
    }
}