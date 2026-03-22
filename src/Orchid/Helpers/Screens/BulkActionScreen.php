<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Screens;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use OrchidHelpers\\Orchid\Helpers\Alerts\SaveAlert;
use OrchidHelpers\\Orchid\Helpers\Layouts\FormLayout;
use OrchidHelpers\\Orchid\Helpers\Layouts\ModalLayout;

abstract class BulkActionScreen extends AbstractScreen
{
    /**
     * The Eloquent model class name.
     */
    abstract protected function modelClass(): string;

    /**
     * Get available bulk actions.
     */
    protected function actions(): array
    {
        return [
            'delete' => 'Delete Selected',
            'activate' => 'Activate Selected',
            'deactivate' => 'Deactivate Selected',
            'export' => 'Export Selected',
        ];
    }

    /**
     * Get the form fields for bulk actions.
     */
    protected function fields(): array
    {
        return [
            \Orchid\Screen\Fields\Select::make('action')
                ->title('Bulk Action')
                ->options($this->actions())
                ->required()
                ->help('Select the action to perform on selected records'),
            
            \Orchid\Screen\Fields\TextArea::make('ids')
                ->title('Record IDs')
                ->required()
                ->rows(5)
                ->help('Enter comma-separated IDs of records to process'),
            
            \Orchid\Screen\Fields\CheckBox::make('confirm')
                ->title('Confirm Action')
                ->value(false)
                ->help('Check to confirm you want to perform this action'),
        ];
    }

    /**
     * Get the validation rules for bulk actions.
     */
    protected function rules(): array
    {
        return [
            'action' => 'required|in:' . implode(',', array_keys($this->actions())),
            'ids' => 'required|string',
            'confirm' => 'required|accepted',
        ];
    }

    /**
     * Parse IDs from input string.
     */
    protected function parseIds(string $ids): array
    {
        return array_filter(
            array_map('trim', explode(',', $ids)),
            fn($id) => !empty($id) && is_numeric($id)
        );
    }

    /**
     * Get records by IDs.
     */
    protected function getRecords(array $ids): Collection
    {
        $modelClass = $this->modelClass();
        
        return $modelClass::whereIn('id', $ids)->get();
    }

    /**
     * Process bulk action.
     */
    protected function processAction(string $action, Collection $records): array
    {
        $results = [
            'successful' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        foreach ($records as $record) {
            try {
                if ($this->authorizeAction($action, $record)) {
                    $this->performAction($action, $record);
                    $results['successful']++;
                } else {
                    $results['skipped']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Authorize the action on a record.
     */
    protected function authorizeAction(string $action, Model $record): bool
    {
        $permission = $this->getActionPermission($action);
        
        return $this->can($permission, $record);
    }

    /**
     * Get permission name for action.
     */
    protected function getActionPermission(string $action): string
    {
        $permissions = [
            'delete' => 'delete',
            'activate' => 'update',
            'deactivate' => 'update',
            'export' => 'view',
        ];

        return $permissions[$action] ?? $action;
    }

    /**
     * Perform the action on a record.
     */
    protected function performAction(string $action, Model $record): void
    {
        switch ($action) {
            case 'delete':
                $record->delete();
                break;
            case 'activate':
                $record->update(['active' => true]);
                break;
            case 'deactivate':
                $record->update(['active' => false]);
                break;
            case 'export':
                // Export logic would be handled separately
                break;
            default:
                throw new \InvalidArgumentException("Unknown action: {$action}");
        }
    }

    /**
     * Get the redirect URL after bulk action.
     */
    protected function redirectTo(): string
    {
        return route($this->getListRouteName());
    }

    /**
     * Get the list route name.
     */
    protected function getListRouteName(): string
    {
        $currentRoute = request()->route()->getName();
        
        // Convert bulk action route to list route
        return preg_replace('/\.bulk.*$/', '.list', $currentRoute);
    }

    /**
     * Handle the bulk action process.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function process(Request $request): RedirectResponse
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass();
        
        // Authorize bulk actions
        $this->authorize('viewAny', $model);
        
        // Validate request data
        $validated = $request->validate($this->rules());
        
        // Parse IDs
        $ids = $this->parseIds($validated['ids']);
        
        if (empty($ids)) {
            return back()->withErrors(['ids' => 'No valid IDs provided.'])->withInput();
        }
        
        // Get records
        $records = $this->getRecords($ids);
        
        if ($records->isEmpty()) {
            return back()->withErrors(['ids' => 'No records found with the provided IDs.'])->withInput();
        }
        
        // Process action
        $results = $this->processAction($validated['action'], $records);
        
        // Show success alert with results
        $this->showResultsAlert($results, $validated['action']);
        
        // Redirect to appropriate page
        return redirect($this->redirectTo());
    }

    /**
     * Show results alert.
     */
    protected function showResultsAlert(array $results, string $action): void
    {
        $message = sprintf(
            'Bulk %s completed: %d successful, %d failed, %d skipped.',
            $action,
            $results['successful'] ?? 0,
            $results['failed'] ?? 0,
            $results['skipped'] ?? 0
        );
        
        SaveAlert::make($message);
    }

    /**
     * Get the screen data for the bulk action screen.
     */
    public function query(): array
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass();
        
        $this->authorize('viewAny', $model);
        
        return [];
    }

    /**
     * Get the layouts for the bulk action screen.
     */
    public function layout(): iterable
    {
        return [
            FormLayout::make($this->fields()),
        ];
    }

    /**
     * Get the command bar for the bulk action screen.
     */
    public function commandBar(): iterable
    {
        return [
            \OrchidHelpers\Orchid\Helpers\Buttons\SaveButton::make()
                ->method('process')
                ->label('Process Bulk Action'),
        ];
    }

    /**
     * Check if user can perform action.
     */
    protected function can(string $ability, Model $model): bool
    {
        try {
            $this->authorize($ability, $model);
            return true;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return false;
        }
    }
}
