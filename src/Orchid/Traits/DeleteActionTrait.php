<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use OrchidHelpers\Orchid\Helpers\Alerts\DestroyAlert;

/**
 * @mixin \Orchid\Screen\Screen
 */
trait DeleteActionTrait
{
    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request) : RedirectResponse
    {
        // Validate required parameters
        if (! $request->filled('morph') || ! $request->filled('id')) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403);
        }

        $modelClass = $request->input('morph');

        // Validate model class exists and is a string
        if (!is_string($modelClass) || !class_exists($modelClass)) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'The specified class does not exist.');
        }

        // Validate model class is allowed
        if (!$this->isModelAllowed($modelClass)) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'The specified model is not allowed for this operation.');
        }

        // Instantiate and validate model type
        $model = app($modelClass);
        if (!$model instanceof Model) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'The specified class is not a valid Eloquent model.');
        }

        // Find the specific model instance
        /* @var Model $model */
        $model = $model->query()->findOrFail($request->input('id'));

        // Authorize the delete action
        $this->authorize('delete', $model);
        
        // Perform deletion
        $model->delete();

        // Show success alert
        DestroyAlert::make();

        // Handle redirect
        if($request->filled('redirect')) {
            return redirect($request->input('redirect'));
        }

        $parameters = Arr::except(Route::current()->parameters, 'method');

        return to_route(str_replace('show', 'list', Route::currentRouteName()), $parameters);
    }

    /**
     * Check if a model class is allowed for instantiation.
     *
     * This method provides basic security by ensuring the class is a concrete
     * Eloquent model. Developers can override this method in their screen class
     * to implement custom validation logic.
     *
     * @param string $modelClass
     * @return bool
     */
    protected function isModelAllowed(string $modelClass): bool
    {
        $reflection = new \ReflectionClass($modelClass);
        
        // Reject abstract classes and interfaces
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            return false;
        }
        
        // Only allow classes that extend Illuminate\Database\Eloquent\Model
        return $reflection->isSubclassOf(\Illuminate\Database\Eloquent\Model::class);
    }
}
