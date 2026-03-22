<?php

namespace OrchidHelpers\Tests\Unit\Fields;

use OrchidHelpers\Orchid\Helpers\Fields\BooleanCheckbox;
use OrchidHelpers\Tests\TestCase;

class BooleanCheckboxTest extends TestCase
{
    public function test_it_creates_checkbox_with_correct_configuration()
    {
        $checkbox = BooleanCheckbox::make('is_active');
        
        $this->assertInstanceOf(\Orchid\Screen\Fields\CheckBox::class, $checkbox);
        $this->assertEquals('model.is_active', $checkbox->get('name'));
        $this->assertEquals('Is Active', $checkbox->get('title'));
        
        // The checkbox should be created without sendTrueOrFalse hack
        // Verify it's a properly configured checkbox field
    }
    
    public function test_it_uses_attrName_for_title_generation()
    {
        // Mock translation
        $this->app['translator']->addLines([
            'validation.attributes.model.is_published' => 'Опубликовано',
        ], 'ru');
        
        // Set locale to Russian
        $this->app->setLocale('ru');
        
        $checkbox = BooleanCheckbox::make('is_published');
        
        $this->assertEquals('Опубликовано', $checkbox->get('title'));
    }
}