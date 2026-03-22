<?php

namespace OrchidHelpers\Tests\Unit\Sights;

use OrchidHelpers\Orchid\Helpers\Sights\TextSight;
use OrchidHelpers\Orchid\Helpers\Sights\EmailSight;
use OrchidHelpers\Orchid\Helpers\Sights\PhoneSight;
use OrchidHelpers\Orchid\Helpers\Sights\UrlSight;
use OrchidHelpers\Orchid\Helpers\Sights\ImageSight;
use OrchidHelpers\Orchid\Helpers\Sights\AvatarSight;
use OrchidHelpers\Orchid\Helpers\Sights\BadgeSight;
use OrchidHelpers\Orchid\Helpers\Sights\ProgressSight;
use OrchidHelpers\Orchid\Helpers\Sights\RatingSight;
use OrchidHelpers\Orchid\Helpers\Sights\CurrencySight;
use OrchidHelpers\Orchid\Helpers\Sights\PercentageSight;
use OrchidHelpers\Orchid\Helpers\Sights\DateSight;
use OrchidHelpers\Orchid\Helpers\Sights\DateTimeSight;
use OrchidHelpers\Orchid\Helpers\Sights\JsonSight;
use OrchidHelpers\Orchid\Helpers\Sights\CodeSight;
use OrchidHelpers\Orchid\Helpers\Sights\MarkdownSight;
use OrchidHelpers\Orchid\Helpers\Sights\HtmlSight;
use OrchidHelpers\Orchid\Helpers\Sights\FileSizeSight;
use OrchidHelpers\Orchid\Helpers\Sights\DurationSight;
use OrchidHelpers\Orchid\Helpers\Sights\CountSight;
use Orchid\Screen\Repository;
use OrchidHelpers\Tests\TestCase;

class NewSightTests extends TestCase
{
    /** @test */
    public function test_text_sight_creates_sight_instance()
    {
        $sight = TextSight::make('description', 'Description');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_text_sight_with_truncation_creates_sight_instance()
    {
        $sight = TextSight::make('description', 'Description', 10);
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_email_sight_creates_sight_instance()
    {
        $sight = EmailSight::make('email', 'Email Address');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_phone_sight_creates_sight_instance()
    {
        $sight = PhoneSight::make('phone', 'Phone Number');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_url_sight_creates_sight_instance()
    {
        $sight = UrlSight::make('website', 'Website');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_image_sight_creates_sight_instance()
    {
        $sight = ImageSight::make('avatar', 'Avatar');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_avatar_sight_creates_sight_instance()
    {
        $sight = AvatarSight::make('avatar', 'Avatar');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_badge_sight_creates_sight_instance()
    {
        $sight = BadgeSight::make('status', 'Status');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_progress_sight_creates_sight_instance()
    {
        $sight = ProgressSight::make('progress', 'Progress');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_rating_sight_creates_sight_instance()
    {
        $sight = RatingSight::make('rating', 'Rating');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_currency_sight_creates_sight_instance()
    {
        $sight = CurrencySight::make('price', 'Price');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_percentage_sight_creates_sight_instance()
    {
        $sight = PercentageSight::make('discount', 'Discount');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_date_sight_creates_sight_instance()
    {
        $sight = DateSight::make('created_at', 'Created At');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_date_time_sight_creates_sight_instance()
    {
        $sight = DateTimeSight::make('updated_at', 'Updated At');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_json_sight_creates_sight_instance()
    {
        $sight = JsonSight::make('data', 'Data');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_code_sight_creates_sight_instance()
    {
        $sight = CodeSight::make('code', 'Code');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_markdown_sight_creates_sight_instance()
    {
        $sight = MarkdownSight::make('content', 'Content');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_html_sight_creates_sight_instance()
    {
        $sight = HtmlSight::make('html_content', 'HTML Content');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_file_size_sight_creates_sight_instance()
    {
        $sight = FileSizeSight::make('size', 'File Size');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_duration_sight_creates_sight_instance()
    {
        $sight = DurationSight::make('duration', 'Duration');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
    
    /** @test */
    public function test_count_sight_creates_sight_instance()
    {
        $sight = CountSight::make('items_count', 'Items Count');
        
        $this->assertInstanceOf(\Orchid\Screen\Sight::class, $sight);
    }
}