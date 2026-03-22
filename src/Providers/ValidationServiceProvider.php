<?php

declare(strict_types=1);

namespace OrchidHelpers\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidationServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->registerCustomRules();
        $this->registerCustomMessages();
        $this->registerCustomAttributes();
    }

    private function registerCustomRules() : void
    {
        // Register custom validation rules
        // Example:
        // Validator::extend('phone_number', function ($attribute, $value, $parameters, $validator) {
        //     return preg_match('/^\+?[1-9]\d{1,14}$/', $value);
        // }, 'The :attribute must be a valid phone number.');
        
        // Validator::extend('strong_password', function ($attribute, $value, $parameters, $validator) {
        //     return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value);
        // }, 'The :attribute must contain at least 8 characters, one uppercase, one lowercase, one number and one special character.');
    }

    private function registerCustomMessages() : void
    {
        // Register custom validation messages
        // Example:
        // Validator::replacer('phone_number', function ($message, $attribute, $rule, $parameters) {
        //     return str_replace(':attribute', $attribute, 'The phone number format is invalid.');
        // });
    }

    private function registerCustomAttributes() : void
    {
        // Register custom attribute names
        // Example:
        // Validator::addAttributes([
        //     'email' => 'email address',
        //     'phone' => 'phone number',
        // ]);
    }
}