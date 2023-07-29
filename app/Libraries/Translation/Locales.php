<?php

namespace App\Libraries\Translation;

use App\Libraries\Translation\Exception\LocalesNotDefinedException;
use ArrayAccess;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;

class Locales implements Arrayable, ArrayAccess
{
    /**
     * @var ConfigContract
     */
    protected $config;

    /**
     * @var array
     */
    protected $locales = [];

    /**
     * @var TranslatorContract
     */
    protected $translator;

    public function __construct(ConfigContract $config, TranslatorContract $translator)
    {
        $this->config = $config;
        $this->translator = $translator;

        $this->load();
    }

    public function add(string $locale): void
    {
        $this->locales[$locale] = $locale;
    }

    public function all(): array
    {
        return array_values($this->locales);
    }

    public function current(): ?string
    {
        $locale = $this->config->get('translation.locale');
        if ($locale == null)
            return $this->translator->getLocale();
        return $locale;
    }

    public function forget(string $locale): void
    {
        unset($this->locales[$locale]);
    }

    public function get(string $locale): ?string
    {
        return $this->locales[$locale] ?? null;
    }

    public function getCountryLocale(string $locale, string $country): string
    {
        return $locale . $this->getLocaleSeparator() . $country;
    }

    public function getLanguageFromCountryBasedLocale(string $locale): string
    {
        return explode($this->getLocaleSeparator(), $locale)[0];
    }

    public function getLocaleSeparator(): string
    {
        return $this->config->get('translation.locale_separator') ?: '-';
    }

    public function has(string $locale): bool
    {
        return isset($this->locales[$locale]);
    }

    public function isLocaleCountryBased(string $locale): bool
    {
        return str_contains($locale, $this->getLocaleSeparator());
    }

    /**
     * @throws \App\Libraries\Translation\Exception\LocalesNotDefinedException
     */
    public function load(): void
    {
        $localesConfig = (array)$this->config->get('translation.locales', []);

        if (empty($localesConfig)) {
            throw LocalesNotDefinedException::make();
        }

        $this->locales = [];
        foreach ($localesConfig as $key => $locale) {
            if (is_string($key) && is_array($locale)) {
                $this->locales[$key] = $key;

                foreach ($locale as $country) {
                    $countryLocale = $this->getCountryLocale($key, $country);
                    $this->locales[$countryLocale] = $countryLocale;
                }
            } elseif (is_string($locale)) {
                $this->locales[$locale] = $locale;
            }
        }
    }

    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    public function offsetGet($key): ?string
    {
        return $this->get($key);
    }

    public function offsetSet($key, $value): void
    {
        if (is_string($key) && is_string($value)) {
            $this->add($this->getCountryLocale($key, $value));
        } elseif (is_string($value)) {
            $this->add($value);
        }
    }

    public function offsetUnset($key): void
    {
        $this->forget($key);
    }

    public function toArray(): array
    {
        return $this->all();
    }
}
