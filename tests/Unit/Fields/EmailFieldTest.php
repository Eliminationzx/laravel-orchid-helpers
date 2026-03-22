<?php

namespace OrchidHelpers\Tests\Unit\Fields;

use OrchidHelpers\Orchid\Helpers\Fields\EmailField;
use OrchidHelpers\Tests\TestCase;

class EmailFieldTest extends TestCase
{
    public function test_it_creates_email_field_with_correct_configuration()
    {
        $field = EmailField::make('email');
        
        $this->assertInstanceOf(\Orchid\Screen\Fields\Input::class, $field);
        $this->assertEquals('model.email', $field->get('name'));
        $this->assertEquals('email', $field->get('type'));
        $this->assertEquals('Email', $field->get('title'));
    }
    
    public function test_it_uses_attrName_for_title_generation()
    {
        // Mock translation
        $this->app['translator']->addLines([
            'validation.attributes.model.user_email' => 'Email address',
        ], 'ru');
        
        // Set locale to Russian
        $this->app->setLocale('ru');
        
        $field = EmailField::make('user_email');
        
        $this->assertEquals('Email address', $field->get('title'));
    }
}