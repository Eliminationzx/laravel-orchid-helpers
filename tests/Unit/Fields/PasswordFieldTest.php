<?php

namespace OrchidHelpers\Tests\Unit\Fields;

use OrchidHelpers\Orchid\Helpers\Fields\PasswordField;
use OrchidHelpers\Tests\TestCase;

class PasswordFieldTest extends TestCase
{
    public function test_it_creates_password_field_with_correct_configuration()
    {
        $field = PasswordField::make('password');
        
        $this->assertInstanceOf(\Orchid\Screen\Fields\Input::class, $field);
        $this->assertEquals('model.password', $field->get('name'));
        $this->assertEquals('password', $field->get('type'));
        $this->assertEquals('Password', $field->get('title'));
    }
}