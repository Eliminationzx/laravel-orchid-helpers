<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\ValidationException;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ValidatableTrait
{
    /**
     * Boot the validatable trait.
     *
     * @return void
     */
    public static function bootValidatableTrait(): void
    {
        static::saving(function (Model $model) {
            $model->validate();
        });
    }

    /**
     * Validate the model attributes.
     *
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return bool
     * @throws ValidationException
     */
    public function validate(array $rules = [], array $messages = [], array $customAttributes = []): bool
    {
        $rules = empty($rules) ? $this->getValidationRules() : $rules;
        $messages = empty($messages) ? $this->getValidationMessages() : $messages;
        $customAttributes = empty($customAttributes) ? $this->getValidationCustomAttributes() : $customAttributes;

        $validator = ValidatorFacade::make($this->getAttributes(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Get validation rules for the model.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        if (property_exists($this, 'rules')) {
            return $this->rules;
        }

        if (method_exists($this, 'rules')) {
            return $this->rules();
        }

        return [];
    }

    /**
     * Get validation messages for the model.
     *
     * @return array
     */
    public function getValidationMessages(): array
    {
        if (property_exists($this, 'validationMessages')) {
            return $this->validationMessages;
        }

        return [];
    }

    /**
     * Get validation custom attributes for the model.
     *
     * @return array
     */
    public function getValidationCustomAttributes(): array
    {
        if (property_exists($this, 'validationAttributes')) {
            return $this->validationAttributes;
        }

        // Generate human-readable attribute names
        $attributes = [];
        foreach ($this->getFillable() as $field) {
            $attributes[$field] = str_replace('_', ' ', ucfirst($field));
        }

        return $attributes;
    }

    /**
     * Validate specific fields only.
     *
     * @param array|string $fields
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return bool
     * @throws ValidationException
     */
    public function validateOnly($fields, array $rules = [], array $messages = [], array $customAttributes = []): bool
    {
        $fields = is_array($fields) ? $fields : [$fields];
        $allRules = $this->getValidationRules();
        
        // Filter rules for specified fields
        $filteredRules = [];
        foreach ($fields as $field) {
            if (isset($allRules[$field])) {
                $filteredRules[$field] = $allRules[$field];
            } elseif (isset($rules[$field])) {
                $filteredRules[$field] = $rules[$field];
            }
        }

        // Filter attributes for specified fields
        $filteredAttributes = array_intersect_key($this->getAttributes(), array_flip($fields));

        $validator = ValidatorFacade::make($filteredAttributes, $filteredRules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Validate against request data.
     *
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return array
     * @throws ValidationException
     */
    public static function validateRequest(Request $request, array $rules = [], array $messages = [], array $customAttributes = []): array
    {
        $instance = new static();
        $rules = empty($rules) ? $instance->getValidationRules() : $rules;

        $validated = $request->validate($rules, $messages, $customAttributes);

        return $validated;
    }

    /**
     * Get validator instance without throwing exception.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return Validator
     */
    public static function validator(array $data, array $rules = [], array $messages = [], array $customAttributes = []): Validator
    {
        $instance = new static();
        $rules = empty($rules) ? $instance->getValidationRules() : $rules;

        return ValidatorFacade::make($data, $rules, $messages, $customAttributes);
    }

    /**
     * Check if data is valid without throwing exception.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return bool
     */
    public static function isValid(array $data, array $rules = [], array $messages = [], array $customAttributes = []): bool
    {
        $validator = static::validator($data, $rules, $messages, $customAttributes);

        return !$validator->fails();
    }

    /**
     * Get validation errors for data.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return array
     */
    public static function getValidationErrors(array $data, array $rules = [], array $messages = [], array $customAttributes = []): array
    {
        $validator = static::validator($data, $rules, $messages, $customAttributes);

        return $validator->errors()->toArray();
    }

    /**
     * Get scenario-based validation rules.
     *
     * @param string $scenario
     * @return array
     */
    public function getScenarioRules(string $scenario): array
    {
        $allRules = $this->getValidationRules();
        
        if (property_exists($this, 'scenarios')) {
            $scenarios = $this->scenarios;
            
            if (isset($scenarios[$scenario])) {
                $scenarioFields = $scenarios[$scenario];
                return array_intersect_key($allRules, array_flip($scenarioFields));
            }
        }

        return $allRules;
    }

    /**
     * Validate for a specific scenario.
     *
     * @param string $scenario
     * @param array $data
     * @param array $messages
     * @param array $customAttributes
     * @return bool
     * @throws ValidationException
     */
    public function validateScenario(string $scenario, array $data = [], array $messages = [], array $customAttributes = []): bool
    {
        $rules = $this->getScenarioRules($scenario);
        $data = empty($data) ? $this->getAttributes() : $data;

        $validator = ValidatorFacade::make($data, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Get validation rule for a specific field.
     *
     * @param string $field
     * @return string|array|null
     */
    public function getFieldRule(string $field): string|array|null
    {
        $rules = $this->getValidationRules();

        return $rules[$field] ?? null;
    }

    /**
     * Check if field is required.
     *
     * @param string $field
     * @return bool
     */
    public function isFieldRequired(string $field): bool
    {
        $rule = $this->getFieldRule($field);

        if (!$rule) {
            return false;
        }

        $rules = is_array($rule) ? $rule : explode('|', $rule);

        return in_array('required', $rules);
    }

    /**
     * Get field validation type.
     *
     * @param string $field
     * @return string|null
     */
    public function getFieldValidationType(string $field): ?string
    {
        $rule = $this->getFieldRule($field);

        if (!$rule) {
            return null;
        }

        $rules = is_array($rule) ? $rule : explode('|', $rule);

        foreach ($rules as $r) {
            if (str_starts_with($r, 'string')) {
                return 'string';
            }
            if (str_starts_with($r, 'integer') || str_starts_with($r, 'numeric')) {
                return 'number';
            }
            if (str_starts_with($r, 'boolean')) {
                return 'boolean';
            }
            if (str_starts_with($r, 'date')) {
                return 'date';
            }
            if (str_starts_with($r, 'email')) {
                return 'email';
            }
            if (str_starts_with($r, 'url')) {
                return 'url';
            }
        }

        return 'string';
    }

    /**
     * Get validation summary for UI.
     *
     * @return array
     */
    public static function getValidationSummary(): array
    {
        $instance = new static();
        $rules = $instance->getValidationRules();
        $summary = [];

        foreach ($rules as $field => $rule) {
            $summary[$field] = [
                'label' => str_replace('_', ' ', ucfirst($field)),
                'required' => $instance->isFieldRequired($field),
                'type' => $instance->getFieldValidationType($field),
                'rules' => is_array($rule) ? $rule : explode('|', $rule),
            ];
        }

        return $summary;
    }

    /**
     * Disable automatic validation.
     *
     * @return void
     */
    public function disableValidation(): void
    {
        static::flushEventListeners();
        static::boot();
    }

    /**
     * Enable automatic validation.
     *
     * @return void
     */
    public function enableValidation(): void
    {
        static::bootValidatableTrait();
    }
}
