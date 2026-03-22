<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * @mixin \Orchid\Screen\Screen
 */
trait AuthorizableTrait
{
    use AuthorizesRequests;

    /**
     * Authorize a model action.
     *
     * @param string $ability
     * @param Model|string $model
     * @param array $arguments
     * @return void
     * @throws AuthorizationException
     */
    public function authorizeModel(string $ability, $model, array $arguments = []): void
    {
        if (is_string($model)) {
            $model = app($model);
        }

        $this->authorize($ability, $model, ...$arguments);
    }

    /**
     * Authorize multiple abilities for a model.
     *
     * @param array $abilities
     * @param Model|string $model
     * @param array $arguments
     * @return bool
     */
    public function authorizeMultiple(array $abilities, $model, array $arguments = []): bool
    {
        foreach ($abilities as $ability) {
            try {
                $this->authorizeModel($ability, $model, $arguments);
            } catch (AuthorizationException $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the current user has a specific ability for a model.
     *
     * @param string $ability
     * @param Model|string $model
     * @param array $arguments
     * @return bool
     */
    public function canForModel(string $ability, $model, array $arguments = []): bool
    {
        try {
            $this->authorizeModel($ability, $model, $arguments);
            return true;
        } catch (AuthorizationException $e) {
            return false;
        }
    }

    /**
     * Check if the current user lacks a specific ability for a model.
     *
     * @param string $ability
     * @param Model|string $model
     * @param array $arguments
     * @return bool
     */
    public function cannotForModel(string $ability, $model, array $arguments = []): bool
    {
        return !$this->canForModel($ability, $model, $arguments);
    }

    /**
     * Authorize based on a policy method.
     *
     * @param string $method
     * @param Model|string $model
     * @param mixed ...$arguments
     * @return mixed
     */
    public function authorizePolicy(string $method, $model, ...$arguments)
    {
        if (is_string($model)) {
            $model = app($model);
        }

        $policy = $this->getPolicyFor($model);

        if (!$policy) {
            throw new AuthorizationException('Policy not found for model.');
        }

        if (!method_exists($policy, $method)) {
            throw new AuthorizationException("Policy method {$method} does not exist.");
        }

        $user = Auth::user();
        $result = $policy->{$method}($user, $model, ...$arguments);

        if ($result === false) {
            throw new AuthorizationException();
        }

        return $result;
    }

    /**
     * Get the policy instance for a model.
     *
     * @param Model $model
     * @return mixed
     */
    protected function getPolicyFor(Model $model)
    {
        $policy = Gate::getPolicyFor(get_class($model));

        if ($policy && method_exists($policy, 'resolve')) {
            return app()->make($policy);
        }

        return $policy;
    }

    /**
     * Authorize a resource controller action.
     *
     * @param string $modelClass
     * @param string $action
     * @param Model|null $modelInstance
     * @return void
     * @throws AuthorizationException
     */
    public function authorizeResource(string $modelClass, string $action, ?Model $modelInstance = null): void
    {
        $model = $modelInstance ?? app($modelClass);

        $this->authorize($action, $model);
    }

    /**
     * Authorize based on a gate ability.
     *
     * @param string $ability
     * @param array $arguments
     * @return bool
     */
    public function authorizeGate(string $ability, array $arguments = []): bool
    {
        try {
            $this->authorize($ability, $arguments);
            return true;
        } catch (AuthorizationException $e) {
            return false;
        }
    }

    /**
     * Check if user has any of the given abilities.
     *
     * @param array $abilities
     * @param Model|string $model
     * @param array $arguments
     * @return bool
     */
    public function canAny(array $abilities, $model, array $arguments = []): bool
    {
        foreach ($abilities as $ability) {
            if ($this->canForModel($ability, $model, $arguments)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given abilities.
     *
     * @param array $abilities
     * @param Model|string $model
     * @param array $arguments
     * @return bool
     */
    public function canAll(array $abilities, $model, array $arguments = []): bool
    {
        foreach ($abilities as $ability) {
            if (!$this->canForModel($ability, $model, $arguments)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get authorization rules for a model.
     *
     * @param Model|string $model
     * @return array
     */
    public function getAuthorizationRules($model): array
    {
        if (is_string($model)) {
            $model = app($model);
        }

        $policy = $this->getPolicyFor($model);

        if (!$policy) {
            return [];
        }

        $rules = [];
        $methods = get_class_methods($policy);

        foreach ($methods as $method) {
            if (in_array($method, ['__construct', 'before', 'after'])) {
                continue;
            }

            $rules[$method] = $this->canForModel($method, $model);
        }

        return $rules;
    }

    /**
     * Authorize based on a custom callback.
     *
     * @param callable $callback
     * @param string $message
     * @return void
     * @throws AuthorizationException
     */
    public function authorizeCallback(callable $callback, string $message = 'This action is unauthorized.'): void
    {
        $user = Auth::user();
        if (!call_user_func($callback, $user)) {
            throw new AuthorizationException($message);
        }
    }

    /**
     * Authorize based on a role or permission (for Spatie Laravel Permission).
     *
     * @param string|array $rolesOrPermissions
     * @param string $guard
     * @return bool
     */
    public function authorizeRoleOrPermission($rolesOrPermissions, string $guard = 'web'): bool
    {
        $user = Auth::guard($guard)->user();

        if (!$user) {
            return false;
        }

        if (is_string($rolesOrPermissions)) {
            $rolesOrPermissions = [$rolesOrPermissions];
        }

        // Check if Spatie Laravel Permission package is installed
        if (method_exists($user, 'hasRole') && method_exists($user, 'hasPermissionTo')) {
            foreach ($rolesOrPermissions as $roleOrPermission) {
                if ($user->hasRole($roleOrPermission) || $user->hasPermissionTo($roleOrPermission)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get authorized models for a specific ability.
     *
     * @param string $ability
     * @param string $modelClass
     * @param array $arguments
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAuthorizedModels(string $ability, string $modelClass, array $arguments = [])
    {
        $query = app($modelClass)->query();

        // Apply policy scope if available
        $policy = $this->getPolicyFor(app($modelClass));
        $user = Auth::user();
        
        if ($policy && method_exists($policy, 'scope' . ucfirst($ability))) {
            $policy->{'scope' . ucfirst($ability)}($user, $query, ...$arguments);
        }

        return $query->get()->filter(function ($model) use ($ability, $arguments) {
            return $this->canForModel($ability, $model, $arguments);
        });
    }
}
