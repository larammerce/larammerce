<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\Setting\CMSRecordNotFoundException;
use App\Helpers\CacheHelper;
use App\Interfaces\Repositories\SettingRepositoryInterface;
use App\Interfaces\SettingDataInterface;
use App\Models\Setting;
use App\Models\User;
use Exception;
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

    public function create(string $key, string $value, ?User $user = null, bool $is_system_setting = false): Setting {
        $setting = new Setting();
        $setting->key = $key;
        $setting->value = $value;
        $setting->user_id = $user?->id;
        $setting->is_system_setting = $is_system_setting;
        $setting->save();

        return $setting;
    }

    public function createWithSettingDataInterface(string $key, SettingDataInterface $data, ?User $user = null, bool $is_system_setting = false): Setting {
        $setting = new Setting();
        $setting->key = $key;
        $setting->data = $data;
        $setting->user_id = $user?->id;
        $setting->is_system_setting = $is_system_setting;
        $setting->save();

        return $setting;
    }

    public function update(Setting $setting, string $key, string $value, ?User $user = null, bool $is_system_setting = false): Setting {
        $setting->key = $key;
        $setting->value = $value;
        $setting->user_id = $user->id;
        $setting->is_system_setting = $is_system_setting;
        $setting->save();

        return $setting;
    }

    public function updateWithSettingDataInterface(Setting $setting, string $key, SettingDataInterface $data, ?User $user = null, bool $is_system_setting = false): Setting {
        $setting->key = $key;
        $setting->data = $data;
        $setting->user_id = $user->id;
        $setting->is_system_setting = $is_system_setting;
        $setting->save();

        return $setting;
    }

    public function delete(Setting $setting): bool {
        return $setting->delete();
    }

    public function find(int $id): ?Setting {
        return Setting::find($id);
    }

    public function findByKey(string $key): ?Setting {
        /** @var Setting $setting */
        $setting = Setting::where("key", $key)->first();

        return $setting;
    }

    public function findGlobalSystemSetting(string $key): ?Setting {
        /** @var Setting $setting */
        $setting = Setting::globalItems()->systemSettings()->where("key", $key)->first();

        return $setting;
    }

    public function updateGlobalSystemSetting(Setting $setting, Setting $key, Setting $value): Setting {
        $setting->key = $key;
        $setting->value = $value;
        $setting->user_id = null;
        $setting->is_system_setting = true;
        $setting->save();

        return $setting;
    }
}
