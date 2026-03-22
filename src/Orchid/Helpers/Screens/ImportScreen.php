<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Screens;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use OrchidHelpers\Orchid\Helpers\Alerts\SaveAlert;
use OrchidHelpers\Orchid\Helpers\Layouts\FormLayout;
use OrchidHelpers\Orchid\Helpers\Layouts\ModalLayout;

abstract class ImportScreen extends AbstractScreen
{
    /**
     * The Eloquent model class name.
     */
    abstract protected function modelClass(): string;

    /**
     * Get the form fields for the import screen.
     */
    protected function fields(): array
    {
        return [
            \Orchid\Screen\Fields\Upload::make('file')
                ->title('Import File')
                ->required()
                ->acceptedFiles('.csv,.xlsx,.xls')
                ->help('Upload CSV or Excel file for import'),
        ];
    }

    /**
     * Get the validation rules for import.
     */
    protected function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
        ];
    }

    /**
     * Get the validation messages for import.
     */
    protected function messages(): array
    {
        return [
            'file.required' => 'Please select a file to import.',
            'file.mimes' => 'File must be CSV or Excel format.',
            'file.max' => 'File size must not exceed 10MB.',
        ];
    }

    /**
     * Process the imported file.
     */
    abstract protected function processImport(UploadedFile $file): array;

    /**
     * Get the redirect URL after successful import.
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
        
        // Convert import route to list route (e.g., platform.users.import -> platform.users.list)
        return str_replace('.import', '.list', $currentRoute);
    }

    /**
     * Handle the import process.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function import(Request $request): RedirectResponse
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass();
        
        // Authorize import
        $this->authorize('create', $model);
        
        // Validate request data
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Process the import
        $file = $request->file('file');
        $results = $this->processImport($file);
        
        // Show success alert with import results
        $this->showImportResultsAlert($results);
        
        // Redirect to appropriate page
        return redirect($this->redirectTo());
    }

    /**
     * Show import results alert.
     */
    protected function showImportResultsAlert(array $results): void
    {
        $message = sprintf(
            'Import completed: %d successful, %d failed, %d skipped.',
            $results['successful'] ?? 0,
            $results['failed'] ?? 0,
            $results['skipped'] ?? 0
        );
        
        SaveAlert::make($message);
    }

    /**
     * Get the screen data for the import screen.
     */
    public function query(): array
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass();
        
        $this->authorize('create', $model);
        
        return [];
    }

    /**
     * Get the layouts for the import screen.
     */
    public function layout(): iterable
    {
        return [
            FormLayout::make($this->fields()),
        ];
    }

    /**
     * Get the command bar for the import screen.
     */
    public function commandBar(): iterable
    {
        return [
            \OrchidHelpers\Orchid\Helpers\Buttons\SaveButton::make()
                ->method('import')
                ->label('Import'),
        ];
    }

    /**
     * Get sample import template file path.
     */
    protected function getSampleTemplatePath(): string
    {
        return resource_path('templates/import-template.csv');
    }

    /**
     * Download sample import template.
     */
    public function downloadTemplate(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $path = $this->getSampleTemplatePath();
        
        if (!file_exists($path)) {
            $this->generateSampleTemplate();
        }
        
        return response()->download($path, 'import-template.csv');
    }
    
    /**
     * Generate sample import template.
     */
    protected function generateSampleTemplate(): void
    {
        // This method should be implemented by concrete classes
        // to generate appropriate sample templates
    }
}
