<?php

namespace OrchidHelpers\Tests\Unit\Links;

use OrchidHelpers\Orchid\Helpers\Links\BackLink;
use OrchidHelpers\Orchid\Helpers\Links\HomeLink;
use OrchidHelpers\Orchid\Helpers\Links\ExternalLink;
use OrchidHelpers\Orchid\Helpers\Links\DownloadLink;
use OrchidHelpers\Orchid\Helpers\Links\PrintLink;
use OrchidHelpers\Orchid\Helpers\Links\RefreshLink;
use OrchidHelpers\Orchid\Helpers\Links\CopyLink;
use OrchidHelpers\Orchid\Helpers\Links\ShareLink;
use OrchidHelpers\Orchid\Helpers\Links\BreadcrumbLink;
use OrchidHelpers\Orchid\Helpers\Links\PaginationLink;
use OrchidHelpers\Orchid\Helpers\Links\SortLink;
use OrchidHelpers\Orchid\Helpers\Links\FilterLink;
use OrchidHelpers\Orchid\Helpers\Links\ModalLink;
use OrchidHelpers\Tests\TestCase;

class LinkHelpersTest extends TestCase
{
    public function test_back_link_creates_link_with_correct_properties()
    {
        $link = BackLink::make();
        
        $this->assertEquals('Back', $link->get('name'));
        $this->assertEquals('bs.arrow-left', $link->get('icon'));
        $this->assertEquals('javascript:history.back()', $link->get('href'));
    }

    public function test_back_link_with_custom_label()
    {
        $link = BackLink::make('Go Back');
        
        $this->assertEquals('Go Back', $link->get('name'));
        $this->assertEquals('bs.arrow-left', $link->get('icon'));
    }

    public function test_home_link_creates_link_with_home_icon()
    {
        $link = HomeLink::make();
        
        $this->assertEquals('Home', $link->get('name'));
        $this->assertEquals('bs.house', $link->get('icon'));
    }

    public function test_external_link_creates_link_with_target_blank()
    {
        $link = ExternalLink::make('https://example.com', 'Example');
        
        $this->assertEquals('Example', $link->get('name'));
        $this->assertEquals('bs.box-arrow-up-right', $link->get('icon'));
        $this->assertEquals('https://example.com', $link->get('href'));
        $this->assertEquals('_blank', $link->get('target'));
        $this->assertEquals('noopener noreferrer', $link->get('rel'));
    }

    public function test_download_link_creates_link_with_download_attribute()
    {
        $link = DownloadLink::make('/files/document.pdf', 'Download PDF');
        
        $this->assertEquals('Download PDF', $link->get('name'));
        $this->assertEquals('bs.download', $link->get('icon'));
        $this->assertEquals('/files/document.pdf', $link->get('href'));
        $this->assertTrue($link->get('download'));
    }

    public function test_print_link_creates_link_with_print_functionality()
    {
        $link = PrintLink::make();
        
        $this->assertEquals('Print', $link->get('name'));
        $this->assertEquals('bs.printer', $link->get('icon'));
        $this->assertEquals('javascript:window.print()', $link->get('href'));
    }

    public function test_refresh_link_creates_link_with_reload_functionality()
    {
        $link = RefreshLink::make();
        
        $this->assertEquals('Refresh', $link->get('name'));
        $this->assertEquals('bs.arrow-clockwise', $link->get('icon'));
        $this->assertEquals('javascript:location.reload()', $link->get('href'));
    }

    public function test_copy_link_creates_link_with_clipboard_functionality()
    {
        $link = CopyLink::make('Text to copy', 'Copy Text');
        
        $this->assertEquals('Copy Text', $link->get('name'));
        $this->assertEquals('bs.clipboard', $link->get('icon'));
        $this->assertEquals('javascript:void(0)', $link->get('href'));
        $this->assertStringContainsString("navigator.clipboard.writeText('Text to copy')", $link->get('onClick'));
    }

    public function test_share_link_creates_twitter_share_link()
    {
        $link = ShareLink::twitter('https://example.com', 'Check this out!');
        
        $this->assertEquals('Share on Twitter', $link->get('name'));
        $this->assertEquals('bs.twitter', $link->get('icon'));
        $this->assertStringContainsString('twitter.com/intent/tweet', $link->get('href'));
        $this->assertEquals('_blank', $link->get('target'));
    }

    public function test_breadcrumb_link_creates_link_with_breadcrumb_class()
    {
        $link = BreadcrumbLink::make('Products', '/products');
        
        $this->assertEquals('Products', $link->get('name'));
        $this->assertStringContainsString('breadcrumb-item', $link->get('class'));
        $this->assertEquals('/products', $link->get('href'));
    }

    public function test_breadcrumb_active_link_has_active_class()
    {
        $link = BreadcrumbLink::active('Current Page');
        
        $this->assertEquals('Current Page', $link->get('name'));
        $this->assertStringContainsString('breadcrumb-item active', $link->get('class'));
    }

    public function test_pagination_link_creates_previous_link_with_icon()
    {
        $link = PaginationLink::previous('/page/1', 'Prev');
        
        $this->assertEquals('Prev', $link->get('name'));
        $this->assertEquals('bs.chevron-left', $link->get('icon'));
        $this->assertEquals('/page/1', $link->get('href'));
        $this->assertStringContainsString('page-link', $link->get('class'));
    }

    public function test_sort_link_creates_link_with_sort_icon()
    {
        $link = SortLink::make('name', 'Name', null, 'asc');
        
        $this->assertEquals('Name', $link->get('name'));
        $this->assertEquals('bs.sort', $link->get('icon'));
        $this->assertStringContainsString('sort=name', $link->get('href'));
        $this->assertStringContainsString('direction=asc', $link->get('href'));
    }

    public function test_filter_link_creates_link_with_filter_icon()
    {
        $link = FilterLink::make('Active', ['status' => 'active']);
        
        $this->assertEquals('Active', $link->get('name'));
        $this->assertEquals('bs.funnel', $link->get('icon'));
        $this->assertStringContainsString('status=active', $link->get('href'));
        $this->assertStringContainsString('filter-link', $link->get('class'));
    }

    public function test_filter_clear_link_creates_clear_filters_link()
    {
        $link = FilterLink::clear();
        
        $this->assertEquals('Clear Filters', $link->get('name'));
        $this->assertEquals('bs.funnel-x', $link->get('icon'));
        $this->assertEquals('?', $link->get('href'));
    }

    public function test_modal_link_creates_modal_toggle()
    {
        $link = ModalLink::make('confirmModal', 'Open Modal');
        
        $this->assertEquals('Open Modal', $link->get('name'));
        $this->assertEquals('bs.window', $link->get('icon'));
        $this->assertEquals('confirmModal', $link->get('modal'));
    }
}
