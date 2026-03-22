<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Screens;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Helpers\Orchid\Helpers\Alerts\SaveAlert;

abstract class UpdateScreen extends EditScreen
{
    /**
     * The Eloquent model class name.
     */
    abstract protected function modelClass(): string;

    /**
     * Get the form fields for updating a record.
     */
    abstract protected function fields(): array;

    /**
     * Get the validation rules for updating a record.
     */
    protected function rules(): array
    {
        return [];
    }

    /**
     * Get the validation messages for updating a record.
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * Get the redirect URL after successful update.
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
        
        // Convert update route to list route (e.g., platform.users.update -> platform.users.list)
        return str_replace('.update', '.list', $currentRoute);
    }

    /**
     * Handle the update of an existing record.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request): RedirectResponse
    {
        $modelClass = $this->modelClass();
        
        // Find the model instance
        $model = $modelClass::findOrFail($request->input('id'));
        
        // Authorize update
        $this->authorize('update', $model);
        
        // Validate request data
        $validated = $request->validate($this->rules(), $this->messages());
        
        // Update the model
        $model->fill($validated);
        $model->save();
        
        // Show success alert
        SaveAlert::make();
        
        // Redirect to appropriate page
        return redirect($this->redirectTo());
    }

    /**
     * Get the screen data for the update form.
     */
    public function query(Model $model): array
    {
        $this->authorize('update', $model);
        
        return $this->model($model);
    }

    /**
     * Get the layouts for the update screen.
     */
    public function layout(): iterable
    {
        return [
            \Orchid\Helpers\Orchid\Helpers\Layouts\FormLayout::make($this->fields()),
        ];
    }

    /**
     * Get the command bar for the update screen.
     */
    public function commandBar(): iterable
    {
        return [
            \Orchid\Helpers\Orchid\Helpers\Buttons\SaveButton::make()
                ->method('update'),
        ];
    }
}
