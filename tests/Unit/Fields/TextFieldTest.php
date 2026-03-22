<?php

namespace OrchidHelpers\Tests\Unit\Fields;

use OrchidHelpers\Orchid\Helpers\Fields\TextField;
use OrchidHelpers\Tests\TestCase;

class TextFieldTest extends TestCase
{
    public function test_it_creates_text_field_with_correct_configuration()
    {
        $field = TextField::make('title');
        
        $this->assertInstanceOf(\Orchid\Screen\Fields\Input::class, $field);
        $this->assertEquals('model.title', $field->get('name'));
        $this->assertEquals('text', $field->get('type'));
        $this->assertEquals('Title', $field->get('title'));
    }
    
    public function test_it_uses_attrName_for_title_generation()
    {
        // Mock translation
        $this->app['translator']->addLines([
            'validation.attributes.model.description' => 'Description',
        ], 'ru');
        
        // Set locale to Russian
        $this->app->setLocale('ru');
        
        $field = TextField::make('description');
        
        $this->assertEquals('Description', $field->get('title'));
    }
}