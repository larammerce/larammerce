<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\Setting\CMSRecordNotFoundException;
use App\Helpers\CacheHelper;
use App\Interfaces\Repositories\SettingRepositoryInterface;
use App\Interfaces\SettingDataInterface;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SettingRepositoryEloquent implements SettingRepositoryInterface {
    const CACHE_TAGS = ["settings"];
    const CACHE_TTL = 60 * 24;

    private function clearCache(): bool {
        return Cache::tags(static::CACHE_TAGS)->flush();
    }

    /**
     * @throws CMSRecordNotFoundException
     */
    public function getCMSRecord($key): Setting {
        $cache_key = CacheHelper::getCacheKey([static::class, __FUNCTION__], func_get_args());
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            try {
                $result = Setting::cmsRecords()->where("key", $key)->firstOrFail();
                Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, static::CACHE_TTL);
            } catch (Exception $e) {
                throw new CMSRecordNotFoundException("CMS Record with key {$key} not found !");
            }
        }
        return $result;
    }

    /**
     * @return Setting[]|Collection
     */
    public function getAllCMSRecords(): array|Collection {
        $cache_key = CacheHelper::getCacheKey([static::class, __FUNCTION__], func_get_args());
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            $result = Setting::cmsRecords()->get();
            Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, static::CACHE_TTL);
        }
        return $result;
    }

    public function getAllCMSRecordsPaginated(): LengthAwarePaginator {
        return Setting::cmsRecords()->paginate(Setting::getPaginationCount());
    }

    public function create(string $key, string $value, Authenticatable|User|null $user = null, bool $is_system_setting = false): Setting {
        $this->clearCache();
        $setting = new Setting();
        $setting->key = $key;
        $setting->value = $value;
        $setting->user_id = $user?->id;
        $setting->is_system_setting = $is_system_setting;
        $setting->save();

        return $setting;
    }

    public function createWithSettingDataInterface(string $key, SettingDataInterface $data, Authenticatable|User|null $user = null, bool $is_system_setting = false): Setting {
        $this->clearCache();
        $setting = new Setting();
        $setting->key = $key;
        $setting->data = $data;
        $setting->user_id = $user?->id;
        $setting->is_system_setting = $is_system_setting;
        $setting->save();

        return $setting;
    }

    public function update(Setting $setting, string $key, string $value, Authenticatable|User|null $user = null, bool $is_system_setting = false): Setting {
        $this->clearCache();
        $setting->key = $key;
        $setting->value = $value;
        $setting->user_id = $user?->id;
        $setting->is_system_setting = $is_system_setting;
        $setting->save();

        return $setting;
    }

    public function updateWithSettingDataInterface(Setting $setting, string $key, SettingDataInterface $data, Authenticatable|User|null $user = null, bool $is_system_setting = false): Setting {
        $this->clearCache();
        $setting->key = $key;
        $setting->data = $data;
        $setting->user_id = $user?->id;
        $setting->is_system_setting = $is_system_setting;
        $setting->save();

        return $setting;
    }

    public function delete(Setting $setting): bool {
        $this->clearCache();
        return $setting->delete();
    }

    public function find(int $id): ?Setting {
        $cache_key = CacheHelper::getCacheKey([static::class, __FUNCTION__], func_get_args());
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            $result = Setting::find($id);
            Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, static::CACHE_TTL);
        }
        return $result;
    }

    public function findByKey(string $key): ?Setting {
        $cache_key = CacheHelper::getCacheKey([static::class, __FUNCTION__], func_get_args());
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            /** @var Setting $result */
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            /** @var Setting $result */
            $result = Setting::where("key", $key)->first();
            Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, static::CACHE_TTL);
        }
        return $result;
    }

    public function findGlobalSystemSetting(string $key): ?Setting {
        $cache_key = CacheHelper::getCacheKey([static::class, __FUNCTION__], func_get_args());
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            /** @var Setting $result */
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            /** @var Setting $result */
            $result = Setting::globalItems()->systemSettings()->where("key", $key)->first();
            Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, static::CACHE_TTL);
        }
        return $result;
    }

    public function updateGlobalSystemSetting(Setting $setting, string $key, string $value): Setting {
        return $this->update($setting, $key, $value, null, true);
    }

    public function updateGlobalSystemSettingWithDataInterface(Setting $setting, string $key, SettingDataInterface $data): Setting {
        return $this->updateWithSettingDataInterface($setting, $key, $data, null, true);
    }

    public function createGlobalSystemSettingWithDataInterface(string $key, SettingDataInterface $data): Setting {
        return $this->createWithSettingDataInterface($key, $data, null, true);
    }

    public function findPersonalSystemSetting(string $key, Authenticatable|User $user): ?Setting {
        $cache_key = CacheHelper::getCacheKey([static::class, __FUNCTION__], func_get_args());
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            /** @var Setting $result */
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            /** @var Setting $result */
            $result = Setting::personalItems($user)->systemSettings()->where("key", $key)->first();
            Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, static::CACHE_TTL);
        }
        return $result;
    }

    public function updatePersonalSystemSetting(Setting $setting, string $key, string $value, Authenticatable|User $user): Setting {
        return $this->update($setting, $key, $value, $user, true);
    }

    public function updatePersonalSystemSettingWithDataInterface(Setting $setting, string $key, SettingDataInterface $data, Authenticatable|User $user): Setting {
        return $this->updateWithSettingDataInterface($setting, $key, $data, $user, true);
    }

    public function createPersonalSystemSettingWithDataInterface(string $key, SettingDataInterface $data, Authenticatable|User $user): Setting {
        return $this->createWithSettingDataInterface($key, $data, $user, true);
    }
}
