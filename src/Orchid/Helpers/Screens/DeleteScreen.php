<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Screens;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use OrchidHelpers\Orchid\Helpers\Alerts\DestroyAlert;
use OrchidHelpers\Orchid\Helpers\Layouts\ModalLayout;

abstract class DeleteScreen extends AbstractScreen
{
    /**
     * The Eloquent model class name.
     */
    abstract protected function modelClass(): string;

    /**
     * Get the confirmation message for deletion.
     */
    protected function confirmationMessage(Model $model): string
    {
        return 'Are you sure you want to delete this record?';
    }

    /**
     * Get the redirect URL after successful deletion.
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
        
        // Convert delete route to list route (e.g., platform.users.delete -> platform.users.list)
        return str_replace('.delete', '.list', $currentRoute);
    }

    /**
     * Handle the deletion of a record.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(Model $model): RedirectResponse
    {
        // Authorize deletion
        $this->authorize('delete', $model);
        
        // Delete the model
        $model->delete();
        
        // Show success alert
        DestroyAlert::make();
        
        // Redirect to appropriate page
        return redirect($this->redirectTo());
    }

    /**
     * Get the screen data for the delete confirmation.
     */
    public function query(Model $model): array
    {
        $this->authorize('delete', $model);
        
        return [
            'model' => $model,
        ];
    }

    /**
     * Get the layouts for the delete screen.
     */
    public function layout(): iterable
    {
        // We need to get the model from the query data
        // Since we can't access $this->model here, we'll use a generic message
        return [
            ModalLayout::confirm(
                title: 'Confirm Deletion',
                message: 'Are you sure you want to delete this record?',
                options: [
                    'method' => 'delete',
                ]
            ),
        ];
    }

    /**
     * Get the command bar for the delete screen.
     */
    public function commandBar(): iterable
    {
        return [];
    }
}