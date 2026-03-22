<?php

namespace OrchidHelpers\Tests\Unit\Screens;

use OrchidHelpers\Orchid\Helpers\Screens\AbstractScreen;
use OrchidHelpers\Orchid\Helpers\Screens\ListScreen;
use OrchidHelpers\Orchid\Helpers\Screens\CreateScreen;
use OrchidHelpers\Orchid\Helpers\Screens\UpdateScreen;
use OrchidHelpers\Orchid\Helpers\Screens\DeleteScreen;
use OrchidHelpers\Orchid\Helpers\Screens\ImportScreen;
use OrchidHelpers\Orchid\Helpers\Screens\ExportScreen;
use OrchidHelpers\Orchid\Helpers\Screens\BulkActionScreen;
use OrchidHelpers\Orchid\Helpers\Screens\SearchScreen;
use OrchidHelpers\Orchid\Helpers\Screens\ReportScreen;
use OrchidHelpers\Orchid\Helpers\Screens\DashboardScreen;
use OrchidHelpers\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Mockery;

class ScreenHelpersTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function test_abstract_screen_can_be_instantiated()
    {
        $screen = new class extends AbstractScreen {
            public function query(): array { return []; }
            public function layout(): iterable { return []; }
        };

        $this->assertInstanceOf(AbstractScreen::class, $screen);
    }

    /** @test */
    public function test_list_screen_abstract_methods_are_defined()
    {
        $screen = new class extends ListScreen {
            protected function model(): string { return TestModel::class; }
            protected function columns(): array { return []; }
        };

        $this->assertInstanceOf(ListScreen::class, $screen);
        $this->assertIsString($screen->model());
        $this->assertIsArray($screen->columns());
    }

    /** @test */
    public function test_create_screen_abstract_methods_are_defined()
    {
        $screen = new class extends CreateScreen {
            protected function modelClass(): string { return TestModel::class; }
            protected function fields(): array { return []; }
        };

        $this->assertInstanceOf(CreateScreen::class, $screen);
        $this->assertIsString($screen->modelClass());
        $this->assertIsArray($screen->fields());
    }

    /** @test */
    public function test_update_screen_abstract_methods_are_defined()
    {
        $screen = new class extends UpdateScreen {
            protected function modelClass(): string { return TestModel::class; }
            protected function fields(): array { return []; }
        };

        $this->assertInstanceOf(UpdateScreen::class, $screen);
        $this->assertIsString($screen->modelClass());
        $this->assertIsArray($screen->fields());
    }

    /** @test */
    public function test_delete_screen_abstract_methods_are_defined()
    {
        $screen = new class extends DeleteScreen {
            protected function modelClass(): string { return TestModel::class; }
        };

        $this->assertInstanceOf(DeleteScreen::class, $screen);
        $this->assertIsString($screen->modelClass());
    }

    /** @test */
    public function test_import_screen_abstract_methods_are_defined()
    {
        $screen = new class extends ImportScreen {
            protected function modelClass(): string { return TestModel::class; }
            protected function processImport($file): array { return []; }
        };

        $this->assertInstanceOf(ImportScreen::class, $screen);
        $this->assertIsString($screen->modelClass());
    }

    /** @test */
    public function test_export_screen_abstract_methods_are_defined()
    {
        $screen = new class extends ExportScreen {
            protected function modelClass(): string { return TestModel::class; }
            protected function processExport($data, $columns, $format) { return null; }
        };

        $this->assertInstanceOf(ExportScreen::class, $screen);
        $this->assertIsString($screen->modelClass());
    }

    /** @test */
    public function test_bulk_action_screen_abstract_methods_are_defined()
    {
        $screen = new class extends BulkActionScreen {
            protected function modelClass(): string { return TestModel::class; }
        };

        $this->assertInstanceOf(BulkActionScreen::class, $screen);
        $this->assertIsString($screen->modelClass());
        $this->assertIsArray($screen->actions());
    }

    /** @test */
    public function test_search_screen_abstract_methods_are_defined()
    {
        $screen = new class extends SearchScreen {
            protected function modelClass(): string { return TestModel::class; }
            protected function searchFields(): array { return []; }
            protected function resultColumns(): array { return []; }
        };

        $this->assertInstanceOf(SearchScreen::class, $screen);
        $this->assertIsString($screen->modelClass());
        $this->assertIsArray($screen->searchFields());
        $this->assertIsArray($screen->resultColumns());
    }

    /** @test */
    public function test_report_screen_abstract_methods_are_defined()
    {
        $screen = new class extends ReportScreen {
            protected function modelClass(): string { return TestModel::class; }
            protected function metrics(): array { return []; }
            protected function charts(): array { return []; }
            protected function tables(): array { return []; }
        };

        $this->assertInstanceOf(ReportScreen::class, $screen);
        $this->assertIsString($screen->modelClass());
        $this->assertIsArray($screen->metrics());
        $this->assertIsArray($screen->charts());
        $this->assertIsArray($screen->tables());
    }

    /** @test */
    public function test_dashboard_screen_abstract_methods_are_defined()
    {
        $screen = new class extends DashboardScreen {
            protected function metrics(): array { return []; }
            protected function charts(): array { return []; }
            protected function tables(): array { return []; }
        };

        $this->assertInstanceOf(DashboardScreen::class, $screen);
        $this->assertIsArray($screen->metrics());
        $this->assertIsArray($screen->charts());
        $this->assertIsArray($screen->tables());
    }

    /** @test */
    public function test_list_screen_default_methods_return_expected_types()
    {
        $screen = new class extends ListScreen {
            protected function model(): string { return TestModel::class; }
            protected function columns(): array { return ['id' => 'ID']; }
            protected function filters(): array { return ['filter1']; }
            protected function actions(): array { return ['action1']; }
        };

        $this->assertIsArray($screen->filters());
        $this->assertIsArray($screen->actions());
        $this->assertIsInt($screen->perPage());
    }

    /** @test */
    public function test_create_screen_default_methods_return_expected_types()
    {
        $screen = new class extends CreateScreen {
            protected function modelClass(): string { return TestModel::class; }
            protected function fields(): array { return []; }
        };

        $this->assertIsArray($screen->rules());
        $this->assertIsArray($screen->messages());
    }

    /** @test */
    public function test_delete_screen_default_methods_return_expected_types()
    {
        $screen = new class extends DeleteScreen {
            protected function modelClass(): string { return TestModel::class; }
        };

        $this->assertIsString($screen->confirmationMessage(new TestModel()));
    }

    /** @test */
    public function test_import_screen_default_methods_return_expected_types()
    {
        $screen = new class extends ImportScreen {
            protected function modelClass(): string { return TestModel::class; }
            protected function processImport($file): array { return []; }
        };

        $this->assertIsArray($screen->fields());
        $this->assertIsArray($screen->rules());
        $this->assertIsArray($screen->messages());
    }

    /** @test */
    public function test_export_screen_default_methods_return_expected_types()
    {
        $screen = new class extends ExportScreen {
            protected function modelClass(): string { return TestModel::class; }
            protected function processExport($data, $columns, $format) { return null; }
        };

        $this->assertIsArray($screen->fields());
        $this->assertIsArray($screen->rules());
        $this->assertIsArray($screen->getExportableColumns());
    }

    /** @test */
    public function test_bulk_action_screen_default_methods_return_expected_types()
    {
        $screen = new class extends BulkActionScreen {
            protected function modelClass(): string { return TestModel::class; }
        };

        $this->assertIsArray($screen->fields());
        $this->assertIsArray($screen->rules());
        $this->assertIsArray($screen->actions());
    }

    /** @test */
    public function test_search_screen_default_methods_return_expected_types()
    {
        $screen = new class extends SearchScreen {
            protected function modelClass(): string { return TestModel::class; }
            protected function searchFields(): array { return []; }
            protected function resultColumns(): array { return []; }
        };

        $this->assertIsArray($screen->getSearchTips());
    }

    /** @test */
    public function test_report_screen_default_methods_return_expected_types()
    {
        $screen = new class extends ReportScreen {
            protected function modelClass(): string { return TestModel::class; }
            protected function metrics(): array { return []; }
            protected function charts(): array { return []; }
            protected function tables(): array { return []; }
        };

        $this->assertIsArray($screen->filters());
    }

    /** @test */
    public function test_dashboard_screen_default_methods_return_expected_types()
    {
        $screen = new class extends DashboardScreen {
            protected function metrics(): array { return []; }
            protected function charts(): array { return []; }
            protected function tables(): array { return []; }
        };

        $this->assertIsArray($screen->cards());
        $this->assertIsArray($screen->alerts());
        $this->assertIsArray($screen->quickActions());
        $this->assertIsArray($screen->recentActivity());
        $this->assertIsString($screen->name());
        $this->assertIsString($screen->description());
        $this->assertIsString($screen->getWelcomeMessage());
    }
}

// Test model class for testing
class TestModel extends Model
{
    protected $table = 'test_models';
    protected $fillable = ['name', 'email'];
    public $timestamps = true;
}