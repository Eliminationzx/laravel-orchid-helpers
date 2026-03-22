<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait TranslatableTrait
{
    /**
     * Get translatable fields for the model.
     *
     * @return array
     */
    public function getTranslatableFields(): array
    {
        if (property_exists($this, 'translatable')) {
            return $this->translatable;
        }

        return [];
    }

    /**
     * Get translation for a field in current locale.
     *
     * @param string $field
     * @param string|null $locale
     * @return mixed
     */
    public function getTranslation(string $field, ?string $locale = null)
    {
        $locale = $locale ?? App::getLocale();
        $translations = $this->getTranslations($field);

        return $translations[$locale] ?? $this->getAttribute($field);
    }

    /**
     * Get all translations for a field.
     *
     * @param string $field
     * @return array
     */
    public function getTranslations(string $field): array
    {
        $translations = $this->getAttribute($field . '_translations');

        if (is_string($translations)) {
            return json_decode($translations, true) ?? [];
        }

        return is_array($translations) ? $translations : [];
    }

    /**
     * Set translation for a field.
     *
     * @param string $field
     * @param string $locale
     * @param mixed $value
     * @return $this
     */
    public function setTranslation(string $field, string $locale, $value): self
    {
        $translations = $this->getTranslations($field);
        $translations[$locale] = $value;

        $this->setAttribute($field . '_translations', json_encode($translations));

        // Also set the field value for current locale
        if ($locale === App::getLocale()) {
            $this->setAttribute($field, $value);
        }

        return $this;
    }

    /**
     * Set multiple translations for a field.
     *
     * @param string $field
     * @param array $translations
     * @return $this
     */
    public function setTranslations(string $field, array $translations): self
    {
        foreach ($translations as $locale => $value) {
            $this->setTranslation($field, $locale, $value);
        }

        return $this;
    }

    /**
     * Get all translations for all translatable fields.
     *
     * @return array
     */
    public function getAllTranslations(): array
    {
        $translations = [];
        foreach ($this->getTranslatableFields() as $field) {
            $translations[$field] = $this->getTranslations($field);
        }

        return $translations;
    }

    /**
     * Check if a field has translation for locale.
     *
     * @param string $field
     * @param string|null $locale
     * @return bool
     */
    public function hasTranslation(string $field, ?string $locale = null): bool
    {
        $locale = $locale ?? App::getLocale();
        $translations = $this->getTranslations($field);

        return isset($translations[$locale]);
    }

    /**
     * Get field value for current locale with fallback.
     *
     * @param string $field
     * @param string|null $fallbackLocale
     * @return mixed
     */
    public function translate(string $field, ?string $fallbackLocale = null)
    {
        $locale = App::getLocale();
        $translations = $this->getTranslations($field);

        if (isset($translations[$locale])) {
            return $translations[$locale];
        }

        // Try fallback locale
        if ($fallbackLocale && isset($translations[$fallbackLocale])) {
            return $translations[$fallbackLocale];
        }

        // Try default locale from config
        $defaultLocale = config('app.fallback_locale', 'en');
        if (isset($translations[$defaultLocale])) {
            return $translations[$defaultLocale];
        }

        // Return first available translation
        if (!empty($translations)) {
            return reset($translations);
        }

        // Return the field value itself
        return $this->getAttribute($field);
    }

    /**
     * Scope to filter by translation.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field
     * @param string $value
     * @param string|null $locale
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereTranslation($query, string $field, string $value, ?string $locale = null)
    {
        $locale = $locale ?? App::getLocale();

        return $query->whereRaw("JSON_EXTRACT({$field}_translations, '$.{$locale}') = ?", [$value]);
    }

    /**
     * Scope to filter by translation like.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field
     * @param string $value
     * @param string|null $locale
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereTranslationLike($query, string $field, string $value, ?string $locale = null)
    {
        $locale = $locale ?? App::getLocale();

        return $query->whereRaw("JSON_EXTRACT({$field}_translations, '$.{$locale}') LIKE ?", ["%{$value}%"]);
    }

    /**
     * Get available locales for translations.
     *
     * @return array
     */
    public function getAvailableLocales(): array
    {
        $locales = [];
        foreach ($this->getTranslatableFields() as $field) {
            $translations = $this->getTranslations($field);
            $locales = array_merge($locales, array_keys($translations));
        }

        return array_unique($locales);
    }

    /**
     * Check if model has any translations.
     *
     * @return bool
     */
    public function hasTranslations(): bool
    {
        foreach ($this->getTranslatableFields() as $field) {
            if (!empty($this->getTranslations($field))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Clear translations for a field.
     *
     * @param string $field
     * @param string|null $locale
     * @return $this
     */
    public function clearTranslation(string $field, ?string $locale = null): self
    {
        if ($locale) {
            $translations = $this->getTranslations($field);
            unset($translations[$locale]);
            $this->setAttribute($field . '_translations', json_encode($translations));
        } else {
            $this->setAttribute($field . '_translations', json_encode([]));
        }

        return $this;
    }

    /**
     * Clear all translations.
     *
     * @return $this
     */
    public function clearAllTranslations(): self
    {
        foreach ($this->getTranslatableFields() as $field) {
            $this->clearTranslation($field);
        }

        return $this;
    }

    /**
     * Get translation model for relationships.
     *
     * @param string|null $locale
     * @return Model|null
     */
    public function translation(?string $locale = null): ?Model
    {
        $locale = $locale ?? App::getLocale();

        // This method assumes you have a translations relationship
        if (method_exists($this, 'translations')) {
            return $this->translations->where('locale', $locale)->first();
        }

        return null;
    }

    /**
     * Fill model with translated attributes.
     *
     * @param array $attributes
     * @param string|null $locale
     * @return $this
     */
    public function fillTranslations(array $attributes, ?string $locale = null): self
    {
        $locale = $locale ?? App::getLocale();

        foreach ($attributes as $field => $value) {
            if (in_array($field, $this->getTranslatableFields())) {
                $this->setTranslation($field, $locale, $value);
            } else {
                $this->setAttribute($field, $value);
            }
        }

        return $this;
    }

    /**
     * Convert model to array with translations.
     *
     * @param string|null $locale
     * @return array
     */
    public function toTranslatedArray(?string $locale = null): array
    {
        $locale = $locale ?? App::getLocale();
        $array = $this->toArray();

        foreach ($this->getTranslatableFields() as $field) {
            $array[$field] = $this->translate($field, $locale);
            
            // Include all translations if needed
            if (config('translatable.include_all_translations', false)) {
                $array[$field . '_translations'] = $this->getTranslations($field);
            }
        }

        return $array;
    }

    /**
     * Get translatable fields configuration for UI.
     *
     * @return array
     */
    public static function getTranslatableConfig(): array
    {
        $instance = new static();
        $fields = $instance->getTranslatableFields();
        $config = [];

        foreach ($fields as $field) {
            $config[$field] = [
                'label' => str_replace('_', ' ', ucfirst($field)),
                'type' => 'text', // Can be extended to detect field type
                'required' => in_array($field, $instance->getFillable()),
            ];
        }

        return $config;
    }

    /**
     * Boot the translatable trait.
     *
     * @return void
     */
    public static function bootTranslatableTrait(): void
    {
        // Add any boot logic here
    }
}
