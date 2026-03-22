<?php

namespace OrchidHelpers\Tests\Unit\Buttons;

use OrchidHelpers\Orchid\Helpers\Buttons\SaveButton;
use OrchidHelpers\Orchid\Helpers\Buttons\SubmitButton;
use OrchidHelpers\Orchid\Helpers\Buttons\CancelButton;
use OrchidHelpers\Orchid\Helpers\Buttons\DeleteButton;
use OrchidHelpers\Orchid\Helpers\Buttons\EditButton;
use OrchidHelpers\Orchid\Helpers\Buttons\ViewButton;
use OrchidHelpers\Orchid\Helpers\Buttons\AddButton;
use OrchidHelpers\Orchid\Helpers\Buttons\BackButton;
use OrchidHelpers\Orchid\Helpers\Buttons\NextButton;
use OrchidHelpers\Orchid\Helpers\Buttons\PreviousButton;
use OrchidHelpers\Orchid\Helpers\Buttons\DownloadButton;
use OrchidHelpers\Orchid\Helpers\Buttons\PrintButton;
use OrchidHelpers\Orchid\Helpers\Buttons\ExportButton;
use OrchidHelpers\Orchid\Helpers\Buttons\ImportButton;
use OrchidHelpers\Orchid\Helpers\Buttons\RefreshButton;
use OrchidHelpers\Orchid\Helpers\Buttons\SearchButton;
use OrchidHelpers\Orchid\Helpers\Buttons\FilterButton;
use OrchidHelpers\Orchid\Helpers\Buttons\SortButton;
use OrchidHelpers\Orchid\Helpers\Buttons\ToggleButton;
use OrchidHelpers\Orchid\Helpers\Buttons\CopyButton;
use OrchidHelpers\Orchid\Helpers\Buttons\ShareButton;
use OrchidHelpers\Tests\TestCase;

class ButtonHelpersTest extends TestCase
{
    public function test_save_button_creates_button_with_correct_properties()
    {
        $button = SaveButton::make();
        
        $this->assertEquals('Save', $button->get('name'));
        $this->assertEquals('bs.check-circle', $button->get('icon'));
        $this->assertEquals('default', $button->get('type'));
        $this->assertEquals('save', $button->get('method'));
    }

    public function test_submit_button_creates_button_with_correct_properties()
    {
        $button = SubmitButton::make();
        
        $this->assertEquals('Submit', $button->get('name'));
        $this->assertEquals('bs.check-circle', $button->get('icon'));
        $this->assertEquals('primary', $button->get('type'));
        $this->assertEquals('submit', $button->get('method'));
    }

    public function test_cancel_button_creates_button_with_correct_properties()
    {
        $button = CancelButton::make();
        
        $this->assertEquals('Cancel', $button->get('name'));
        $this->assertEquals('bs.x-circle', $button->get('icon'));
        $this->assertEquals('secondary', $button->get('type'));
        $this->assertEquals('cancel', $button->get('method'));
    }

    public function test_delete_button_creates_button_with_correct_properties()
    {
        $button = DeleteButton::make();
        
        $this->assertEquals('Delete', $button->get('name'));
        $this->assertEquals('bs.trash2', $button->get('icon'));
        $this->assertEquals('danger', $button->get('type'));
        $this->assertEquals('destroy', $button->get('method'));
        $this->assertEquals('Are you sure you want to delete this item?', $button->get('confirm'));
    }

    public function test_edit_button_creates_button_with_correct_properties()
    {
        $button = EditButton::make();
        
        $this->assertEquals('Edit', $button->get('name'));
        $this->assertEquals('bs.wrench', $button->get('icon'));
        $this->assertEquals('primary', $button->get('type'));
        $this->assertEquals('edit', $button->get('method'));
    }

    public function test_view_button_creates_button_with_correct_properties()
    {
        $button = ViewButton::make();
        
        $this->assertEquals('View', $button->get('name'));
        $this->assertEquals('bs.eye', $button->get('icon'));
        $this->assertEquals('info', $button->get('type'));
        $this->assertEquals('show', $button->get('method'));
    }

    public function test_add_button_creates_button_with_correct_properties()
    {
        $button = AddButton::make();
        
        $this->assertEquals('Add', $button->get('name'));
        $this->assertEquals('bs.plus', $button->get('icon'));
        $this->assertEquals('success', $button->get('type'));
        $this->assertEquals('create', $button->get('method'));
    }

    public function test_back_button_creates_button_with_correct_properties()
    {
        $button = BackButton::make();
        
        $this->assertEquals('Back', $button->get('name'));
        $this->assertEquals('bs.arrow-left', $button->get('icon'));
        $this->assertEquals('secondary', $button->get('type'));
        $this->assertEquals('back', $button->get('method'));
    }

    public function test_next_button_creates_button_with_correct_properties()
    {
        $button = NextButton::make();
        
        $this->assertEquals('Next', $button->get('name'));
        $this->assertEquals('bs.arrow-right', $button->get('icon'));
        $this->assertEquals('primary', $button->get('type'));
        $this->assertEquals('next', $button->get('method'));
    }

    public function test_previous_button_creates_button_with_correct_properties()
    {
        $button = PreviousButton::make();
        
        $this->assertEquals('Previous', $button->get('name'));
        $this->assertEquals('bs.arrow-left', $button->get('icon'));
        $this->assertEquals('secondary', $button->get('type'));
        $this->assertEquals('previous', $button->get('method'));
    }

    public function test_download_button_creates_button_with_correct_properties()
    {
        $button = DownloadButton::make();
        
        $this->assertEquals('Download', $button->get('name'));
        $this->assertEquals('bs.download', $button->get('icon'));
        $this->assertEquals('success', $button->get('type'));
        $this->assertEquals('download', $button->get('method'));
    }

    public function test_print_button_creates_button_with_correct_properties()
    {
        $button = PrintButton::make();
        
        $this->assertEquals('Print', $button->get('name'));
        $this->assertEquals('bs.printer', $button->get('icon'));
        $this->assertEquals('default', $button->get('type'));
        $this->assertEquals('print', $button->get('method'));
    }

    public function test_export_button_creates_button_with_correct_properties()
    {
        $button = ExportButton::make();
        
        $this->assertEquals('Export', $button->get('name'));
        $this->assertEquals('bs.download', $button->get('icon'));
        $this->assertEquals('info', $button->get('type'));
        $this->assertEquals('export', $button->get('method'));
    }

    public function test_import_button_creates_button_with_correct_properties()
    {
        $button = ImportButton::make();
        
        $this->assertEquals('Import', $button->get('name'));
        $this->assertEquals('bs.upload', $button->get('icon'));
        $this->assertEquals('warning', $button->get('type'));
        $this->assertEquals('import', $button->get('method'));
    }

    public function test_refresh_button_creates_button_with_correct_properties()
    {
        $button = RefreshButton::make();
        
        $this->assertEquals('Refresh', $button->get('name'));
        $this->assertEquals('bs.arrow-clockwise', $button->get('icon'));
        $this->assertEquals('default', $button->get('type'));
        $this->assertEquals('refresh', $button->get('method'));
    }

    public function test_search_button_creates_button_with_correct_properties()
    {
        $button = SearchButton::make();
        
        $this->assertEquals('Search', $button->get('name'));
        $this->assertEquals('bs.search', $button->get('icon'));
        $this->assertEquals('info', $button->get('type'));
        $this->assertEquals('search', $button->get('method'));
    }

    public function test_filter_button_creates_button_with_correct_properties()
    {
        $button = FilterButton::make();
        
        $this->assertEquals('Filter', $button->get('name'));
        $this->assertEquals('bs.funnel', $button->get('icon'));
        $this->assertEquals('secondary', $button->get('type'));
        $this->assertEquals('filter', $button->get('method'));
    }

    public function test_sort_button_creates_button_with_correct_properties()
    {
        $button = SortButton::make();
        
        $this->assertEquals('Sort', $button->get('name'));
        $this->assertEquals('bs.sort-down', $button->get('icon'));
        $this->assertEquals('default', $button->get('type'));
        $this->assertEquals('sort', $button->get('method'));
    }

    public function test_toggle_button_creates_button_with_correct_properties()
    {
        $button = ToggleButton::make();
        
        $this->assertEquals('Toggle', $button->get('name'));
        $this->assertEquals('bs.toggle-on', $button->get('icon'));
        $this->assertEquals('default', $button->get('type'));
        $this->assertEquals('toggle', $button->get('method'));
    }

    public function test_copy_button_creates_button_with_correct_properties()
    {
        $button = CopyButton::make();
        
        $this->assertEquals('Copy', $button->get('name'));
        $this->assertEquals('bs.clipboard', $button->get('icon'));
        $this->assertEquals('info', $button->get('type'));
        $this->assertEquals('copy', $button->get('method'));
    }

    public function test_share_button_creates_button_with_correct_properties()
    {
        $button = ShareButton::make();
        
        $this->assertEquals('Share', $button->get('name'));
        $this->assertEquals('bs.share', $button->get('icon'));
        $this->assertEquals('info', $button->get('type'));
        $this->assertEquals('share', $button->get('method'));
    }
}