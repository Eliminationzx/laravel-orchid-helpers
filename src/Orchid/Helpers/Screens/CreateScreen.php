<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Screens;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use OrchidHelpers\\Orchid\Helpers\Alerts\SaveAlert;

abstract class CreateScreen extends EditScreen
{
    /**
     * The Eloquent model class name.
     */
    abstract protected function modelClass(): string;

    /**
     * Get the form fields for creating a new record.
     */
    abstract protected function fields(): array;

    /**
     * Get the validation rules for creating a new record.
     */
    protected function rules(): array
    {
        return [];
    }

    /**
     * Get the validation messages for creating a new record.
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * Get the redirect URL after successful creation.
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
        
        // Convert create route to list route (e.g., platform.users.create -> platform.users.list)
        return str_replace('.create', '.list', $currentRoute);
    }

    /**
     * Handle the creation of a new record.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request): RedirectResponse
    {
        $modelClass = $this->modelClass();
        
        // Create new model instance
        $model = new $modelClass();
        
        // Authorize creation
        $this->authorize('create', $model);
        
        // Validate request data
        $validated = $request->validate($this->rules(), $this->messages());
        
        // Fill and save the model
        $model->fill($validated);
        $model->save();
        
        // Show success alert
        SaveAlert::make();
        
        // Redirect to appropriate page
        return redirect($this->redirectTo());
    }

    /**
     * Get the screen data for the create form.
     */
    public function query(): array
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass();
        
        $this->authorize('create', $model);
        
        return $this->model($model);
    }

    /**
     * Get the layouts for the create screen.
     */
    public function layout(): iterable
    {
        return [
            \OrchidHelpers\Orchid\Helpers\Layouts\FormLayout::make($this->fields()),
        ];
    }

    /**
     * Get the command bar for the create screen.
     */
    public function commandBar(): iterable
    {
        return [
            \OrchidHelpers\Orchid\Helpers\Buttons\SaveButton::make()
                ->method('create'),
        ];
    }
}
