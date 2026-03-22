<?php

namespace Orchid\Helpers\Tests\Unit\TD;

use Orchid\Helpers\Orchid\Helpers\TD\EmailTD;
use Orchid\Helpers\Orchid\Helpers\TD\PhoneTD;
use Orchid\Helpers\Orchid\Helpers\TD\CurrencyTD;
use Orchid\Helpers\Orchid\Helpers\TD\PercentageTD;
use Orchid\Helpers\Orchid\Helpers\TD\BadgeTD;
use Orchid\Helpers\Orchid\Helpers\TD\ImageTD;
use Orchid\Helpers\Orchid\Helpers\TD\DateTD;
use Orchid\Helpers\Orchid\Helpers\TD\DateTimeTD;
use Orchid\Helpers\Orchid\Helpers\TD\TruncatedTextTD;
use Orchid\Helpers\Orchid\Helpers\TD\JsonTD;
use Orchid\Screen\Repository;
use Orchid\Helpers\Tests\TestCase;

class NewTDTests extends TestCase
{
    /** @test */
    public function test_email_td_creates_td_instance()
    {
        $td = EmailTD::make('email', 'Email Address');
        
        $this->assertInstanceOf(\Orchid\Screen\TD::class, $td);
    }
    
    /** @test */
    public function test_phone_td_creates_td_instance()
    {
        $td = PhoneTD::make('phone', 'Phone Number');
        
        $this->assertInstanceOf(\Orchid\Screen\TD::class, $td);
    }
    
    /** @test */
    public function test_currency_td_creates_td_instance()
    {
        $td = CurrencyTD::make('price', 'Price');
        
        $this->assertInstanceOf(\Orchid\Screen\TD::class, $td);
    }
    
    /** @test */
    public function test_percentage_td_creates_td_instance()
    {
        $td = PercentageTD::make('discount', 'Discount');
        
        $this->assertInstanceOf(\Orchid\Screen\TD::class, $td);
    }
    
    /** @test */
    public function test_badge_td_creates_td_instance()
    {
        $td = BadgeTD::make('status', 'Status');
        
        $this->assertInstanceOf(\Orchid\Screen\TD::class, $td);
    }
    
    /** @test */
    public function test_image_td_creates_td_instance()
    {
        $td = ImageTD::make('avatar', 'Avatar');
        
        $this->assertInstanceOf(\Orchid\Screen\TD::class, $td);
    }
    
    /** @test */
    public function test_date_td_creates_td_instance()
    {
        $td = DateTD::make('created_at', 'Created At');
        
        $this->assertInstanceOf(\Orchid\Screen\TD::class, $td);
    }
    
    /** @test */
    public function test_date_time_td_creates_td_instance()
    {
        $td = DateTimeTD::make('updated_at', 'Updated At');
        
        $this->assertInstanceOf(\Orchid\Screen\TD::class, $td);
    }
    
    /** @test */
    public function test_truncated_text_td_creates_td_instance()
    {
        $td = TruncatedTextTD::make('description', 'Description');
        
        $this->assertInstanceOf(\Orchid\Screen\TD::class, $td);
    }
    
    /** @test */
    public function test_json_td_creates_td_instance()
    {
        $td = JsonTD::make('metadata', 'Metadata');
        
        $this->assertInstanceOf(\Orchid\Screen\TD::class, $td);
    }
}
