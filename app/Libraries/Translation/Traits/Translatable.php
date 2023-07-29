<?php

namespace App\Libraries\Translation\Traits;

use App\Features\Language\LanguageConfig;
use App\Libraries\Translation\Locales;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

/**
 * @property-read null|Model $translation
 * @property-read Collection|Model[] $translations
 * @property-read string $translationModel
 * @property-read string $translationForeignKey
 * @property-read string $localeKey
 * @property-read bool $useTranslationFallback
 *
 * @mixin Model
 */
trait Translatable
{
    use Scopes, Relationship;

    protected static ?bool $AUTOLOAD_TRANSLATIONS = null;
    protected static ?bool $DELETE_TRANSLATIONS_CASCADE = false;
    protected $default_locale;

    public static function getTranslatableFields(bool $with_input_type = false, bool $with_column_type = false): array
    {
        if ($with_column_type and $with_input_type)
            return static::$TRANSLATABLE_FIELDS;
        if ($with_input_type) {
            $result = [];
            foreach (static::$TRANSLATABLE_FIELDS as $field => $data) {
                $result[$field] = $data[1];
            }
            return $result;
        }

        if ($with_column_type) {
            $result = [];
            foreach (static::$TRANSLATABLE_FIELDS as $field => $data) {
                $result[$field] = $data[0];
            }
            return $result;
        }

        return array_keys(static::$TRANSLATABLE_FIELDS);
    }

    public static function getTranslationEditForm(): string
    {
        return static::$TRANSLATION_EDIT_FORM ?? 'admin.pages.model-translation.edit';
    }

    public static function bootTranslatable(): void
    {
        static::saved(function (Model $model) {
            /* @var Translatable $model */
            return $model->saveTranslations();
        });

        static::deleting(function (Model $model) {
            /* @var Translatable $model */
            if (self::$DELETE_TRANSLATIONS_CASCADE === true) {
                return $model->deleteTranslations();
            }
        });
    }

    public static function defaultAutoloadTranslations(): void
    {
        self::$AUTOLOAD_TRANSLATIONS = null;
    }

    public static function disableAutoloadTranslations(): void
    {
        self::$AUTOLOAD_TRANSLATIONS = false;
    }

    public static function enableAutoloadTranslations(): void
    {
        self::$AUTOLOAD_TRANSLATIONS = true;
    }

    public static function disableDeleteTranslationsCascade(): void
    {
        self::$DELETE_TRANSLATIONS_CASCADE = false;
    }

    public static function enableDeleteTranslationsCascade(): void
    {
        self::$DELETE_TRANSLATIONS_CASCADE = true;
    }

    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        if (count(config("translation.locales")) === 1
            or app()->getLocale() == config("translation.fallback_locale")
            or (!$this->relationLoaded('translations') && !$this->toArrayAlwaysLoadsTranslations() && is_null(self::$AUTOLOAD_TRANSLATIONS))
            or self::$AUTOLOAD_TRANSLATIONS === false
        ) {
            return $attributes;
        }

        $hiddenAttributes = $this->getHidden();

        foreach (static::getTranslatableFields() as $field) {
            if (in_array($field, $hiddenAttributes)) {
                continue;
            }

            $attributes[$field] = $this->getAttributeOrFallback(null, $field);
        }

        return $attributes;
    }

    /**
     * @param string|array|null $locales The locales to be deleted
     */
    public function deleteTranslations($locales = null): void
    {
        if ($locales === null) {
            $translations = $this->translations()->get();
        } else {
            $locales = (array)$locales;
            $translations = $this->translations()->whereIn($this->getLocaleKey(), $locales)->get();
        }

        $translations->each->delete();

        // we need to manually "reload" the collection built from the relationship
        // otherwise $this->translations()->get() would NOT be the same as $this->translations
        $this->load('translations');
    }

    public function getAttribute($key)
    {
        [$attribute, $locale] = $this->getAttributeAndLocale($key);
        if (LanguageConfig::isMultiLangSystem() and
            $locale !== config("translation.fallback_locale") and
            $this->isTranslationAttribute($attribute)) {
            if ($this->getTranslation($locale) === null) {
                return $this->getAttributeValue($attribute);
            }

            if ($this->hasGetMutator($attribute)) {
                $this->attributes[$attribute] = $this->getAttributeOrFallback($locale, $attribute);

                return $this->getAttributeValue($attribute);
            }

            return $this->getAttributeOrFallback($locale, $attribute);
        }

        return parent::getAttribute($key);
    }

    public function getDefaultLocale(): ?string
    {
        return $this->default_locale;
    }

    /**
     * @internal will change to protected
     */
    public function getLocaleKey(): string
    {
        return $this->localeKey ?: config('translation.locale_key', 'locale');
    }

    public function getNewTranslation(string $locale): Model
    {
        $modelName = $this->getTranslationModelName();

        /** @var Model $translation */
        $translation = new $modelName();
        $translation->setAttribute($this->getLocaleKey(), $locale);
        $this->translations->add($translation);

        return $translation;
    }

    public function getTranslation(?string $locale = null, bool $withFallback = null): ?Model
    {
        $configFallbackLocale = $this->getFallbackLocale();
        $locale = $locale ?: $this->locale();
        $withFallback = $withFallback === null ? $this->useFallback() : $withFallback;
        $fallbackLocale = $this->getFallbackLocale($locale);

        if ($translation = $this->getTranslationByLocaleKey($locale)) {
            return $translation;
        }

        if ($withFallback && $fallbackLocale) {
            if ($translation = $this->getTranslationByLocaleKey($fallbackLocale)) {
                return $translation;
            }
            if (
                is_string($configFallbackLocale)
                && $fallbackLocale !== $configFallbackLocale
                && $translation = $this->getTranslationByLocaleKey($configFallbackLocale)
            ) {
                return $translation;
            }
        }

        if ($withFallback && $configFallbackLocale === null) {
            $configuredLocales = $this->getLocalesHelper()->all();
            foreach ($configuredLocales as $configuredLocale) {
                if (
                    $locale !== $configuredLocale
                    && $fallbackLocale !== $configuredLocale
                    && $translation = $this->getTranslationByLocaleKey($configuredLocale)
                ) {
                    return $translation;
                }
            }
        }
        return null;
    }

    public function getTranslationOrNew(?string $locale = null): Model
    {
        $locale = $locale ?: $this->locale();
        if (($translation = $this->getTranslation($locale, false)) === null) {
            $translation = $this->getNewTranslation($locale);
        }
        return $translation;
    }

    public function getTranslationOrFail(string $locale): Model
    {
        if (($translation = $this->getTranslation($locale, false)) === null) {
            throw (new ModelNotFoundException)->setModel($this->getTranslationModelName(), $locale);
        }
        return $translation;
    }

    public function getTranslationsArray(): array
    {
        $translations = [];
        foreach ($this->translations as $translation) {
            foreach (static::getTranslatableFields() as $attr) {
                $translations[$translation->{$this->getLocaleKey()}][$attr] = $translation->{$attr};
            }
        }
        return $translations;
    }

    public function hasTranslation(?string $locale = null): bool
    {
        $locale = $locale ?: $this->locale();
        foreach ($this->translations as $translation) {
            if ($translation->getAttribute($this->getLocaleKey()) == $locale) {
                return true;
            }
        }
        return false;
    }

    public function isTranslationAttribute(string $key): bool
    {
        return in_array($key, static::getTranslatableFields());
    }

    public function replicateWithTranslations(array $except = null): Model
    {
        $newInstance = $this->replicate($except);
        unset($newInstance->translations);
        foreach ($this->translations as $translation) {
            $newTranslation = $translation->replicate();
            $newInstance->translations->add($newTranslation);
        }
        return $newInstance;
    }

    public function setDefaultLocale(?string $locale)
    {
        $this->default_locale = $locale;
        return $this;
    }

    public function translate(?string $locale = null, bool $withFallback = false): ?Model
    {
        return $this->getTranslation($locale, $withFallback);
    }

    public function translateOrDefault(?string $locale = null): ?Model
    {
        return $this->getTranslation($locale, true);
    }

    public function translateOrNew(?string $locale = null): Model
    {
        return $this->getTranslationOrNew($locale);
    }

    public function translateOrFail(string $locale): Model
    {
        return $this->getTranslationOrFail($locale);
    }

    protected function getLocalesHelper(): Locales
    {
        return app(Locales::class);
    }

    protected function isEmptyTranslatableAttribute(string $key, $value): bool
    {
        return empty($value);
    }

    protected function isTranslationDirty(Model $translation): bool
    {
        $dirtyAttributes = $translation->getDirty();
        unset($dirtyAttributes[$this->getLocaleKey()]);
        return count($dirtyAttributes) > 0;
    }

    protected function locale(): string
    {
        $default_locale = $this->getDefaultLocale();
        if ($default_locale != null) {
            return $default_locale;
        } else {
            $default_locale = $this->getLocalesHelper()->current();
            if ($default_locale != null)
                return $default_locale;
            else
                $this->setDefaultLocale($this->getFallbackLocale());
        }
        return $this->getDefaultLocale();
    }

    protected function saveTranslations(): bool
    {
        $saved = true;
        if (!$this->relationLoaded('translations')) {
            return $saved;
        }
        foreach ($this->translations as $translation) {
            if ($saved && $this->isTranslationDirty($translation)) {
                if (!empty($connectionName = $this->getConnectionName())) {
                    $translation->setConnection($connectionName);
                }
                $translation->setAttribute($this->getTranslationRelationKey(), $this->getKey());
                $saved = $translation->save();
            }
        }
        return $saved;
    }

    protected function getAttributeAndLocale(string $key): array
    {
        if (Str::contains($key, ':')) {
            return explode(':', $key);
        }
        return [$key, $this->locale()];
    }

    protected function getAttributeOrFallback(?string $locale, string $attribute)
    {
        $translation = $this->getTranslation($locale);
        if (
            (
                !$translation instanceof Model
                || $this->isEmptyTranslatableAttribute($attribute, $translation->$attribute)
            )
            && $this->usePropertyFallback()
        ) {
            $translation = $this->getTranslation($this->getFallbackLocale(), false);
        }
        if ($translation instanceof Model) {
            return $translation->$attribute;
        }
        return null;
    }

    protected function getFallbackLocale(?string $locale = null): ?string
    {
        if ($locale && $this->getLocalesHelper()->isLocaleCountryBased($locale)) {
            if ($fallback = $this->getLocalesHelper()->getLanguageFromCountryBasedLocale($locale)) {
                return $fallback;
            }
        }
        return config('translation.fallback_locale');
    }

    protected function getTranslationByLocaleKey(string $key): ?Model
    {
        if (
            $this->relationLoaded('translation')
            && $this->translation
            && $this->translation->getAttribute($this->getLocaleKey()) == $key
        ) {
            return $this->translation;
        }
        return $this->translations->firstWhere($this->getLocaleKey(), $key);
    }

    protected function toArrayAlwaysLoadsTranslations(): bool
    {
        return config('translation.to_array_always_loads_translations', true);
    }

    protected function useFallback(): bool
    {
        if (isset($this->useTranslationFallback) && is_bool($this->useTranslationFallback)) {
            return $this->useTranslationFallback;
        }
        return (bool)config('translation.use_fallback');
    }

    protected function usePropertyFallback(): bool
    {
        return $this->useFallback() && config('translation.use_property_fallback', false);
    }

    public function __isset($key)
    {
        return $this->isTranslationAttribute($key) || parent::__isset($key);
    }
}
