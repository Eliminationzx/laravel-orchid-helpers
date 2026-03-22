<?php

namespace Orchid\Helpers\Tests\Unit\Fields;

use Orchid\Helpers\Orchid\Helpers\Fields\BooleanCheckbox;
use Orchid\Helpers\Tests\TestCase;

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
            'validation.attributes.model.is_published' => 'Published',
        ], 'ru');
        
        // Set locale to Russian
        $this->app->setLocale('ru');
        
        $checkbox = BooleanCheckbox::make('is_published');
        
        $this->assertEquals('Published', $checkbox->get('title'));
    }
}
