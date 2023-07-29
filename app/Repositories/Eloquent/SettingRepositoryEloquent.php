<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\Setting\CMSRecordNotFoundException;
use App\Exceptions\Setting\SettingNotFoundException;
use App\Helpers\CacheHelper;
use App\Interfaces\Repositories\SettingRepositoryInterface;
use App\Interfaces\SettingDataInterface;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SettingRepositoryEloquent implements SettingRepositoryInterface
{
    const CACHE_TAGS = ["settings"];
    const CACHE_TTL = 60 * 60 * 24;

    private CacheHelper $cache_service;

    public function __construct(CacheHelper $cache_service) {
        $this->cache_service = $cache_service;
    }

    public function clearCache(): bool {
        return Cache::tags(static::CACHE_TAGS)->flush();
    }

    /**
     * @throws CMSRecordNotFoundException
     */
    public function getCMSRecord(string $key): Setting {
        $cache_key = $this->cache_service->getCacheKey([static::class, __FUNCTION__], func_get_args());
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

    public function setCMSRecord(string $key, string $value): Model|Builder {
        $this->clearCache();

        try {
            $record = $this->getCMSRecord($key);
            $record = $this->update($record, $key, $value, null, true);
        } catch (CMSRecordNotFoundException $e) {
            $record = $this->create($key, $value, null, true);
        }

        return $record;
    }

    /**
     * @return Setting[]|Collection
     */
    public function getAllCMSRecords(): array|Collection {
        $cache_key = $this->cache_service->getCacheKey([static::class, __FUNCTION__], func_get_args());
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

    /**
     * @throws SettingNotFoundException
     */
    public function findById(int $id): Setting {
        $cache_key = $this->cache_service->getCacheKey([static::class, __FUNCTION__], func_get_args());
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            try {
                $result = Setting::findOrFail($id);
                Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, static::CACHE_TTL);
            } catch (Exception $e) {
                throw new SettingNotFoundException("FeatureConfig with id {$id} not found !");
            }
        }
        return $result;
    }

    /**
     * @throws SettingNotFoundException
     */
    public function findByKey(string $key): Setting {
        $cache_key = $this->cache_service->getCacheKey([static::class, __FUNCTION__], func_get_args());
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            try {
                $result = Setting::where("key", $key)->firstOrFail();
                Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, static::CACHE_TTL);
            } catch (Exception $e) {
                throw new SettingNotFoundException("FeatureConfig with key {$key} not found !");
            }
        }
        return $result;
    }

    public function create(string $key, string $value, ?User $user = null, bool $is_system_setting = false): Setting {
        return Setting::create([
            "key" => $key,
            "value" => $value,
            "user_id" => $user?->id,
            "is_system_setting" => $is_system_setting
        ]);
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
        $this->clearCache();

        $setting->key = $key;
        $setting->value = $value;
        $setting->user_id = $user?->id;
        $setting->is_system_setting = $is_system_setting;
        $setting->save();

        return $setting;
    }

    public function updateWithSettingDataInterface(Setting $setting, string $key, SettingDataInterface $data, ?User $user = null, bool $is_system_setting = false): Setting {
        $this->clearCache();

        $setting->key = $key;
        $setting->data = $data;
        $setting->user_id = $user?->id;
        $setting->is_system_setting = $is_system_setting;
        $setting->save();

        return $setting;
    }

    public function delete(Setting $setting): bool {
        return $setting->delete();
    }

    /**
     * @return Setting[]|Collection
     */
    public function getAll(): array|Collection {
        $cache_key = $this->cache_service->getCacheKey([static::class, __FUNCTION__], func_get_args());
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            $result = Setting::all();
            Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, static::CACHE_TTL);
        }
        return $result;
    }

    public function getAllPaginated(): LengthAwarePaginator {
        return Setting::query()->paginate(Setting::getPaginationCount());
    }

    /**
     * @return Setting[]|Collection
     */
    public function getAllPersonal(): array|Collection {
        $cache_key = $this->cache_service->getCacheKey([static::class, __FUNCTION__], func_get_args());
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            $result = Setting::personalItems()->get();
            Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, static::CACHE_TTL);
        }
        return $result;
    }

    public function getAllPersonalPaginated(): LengthAwarePaginator {
        return Setting::personalItems()->paginate(Setting::getPaginationCount());
    }

    /**
     * @return Setting[]|Collection
     */
    public function getAllGlobal(): array|Collection {
        $cache_key = $this->cache_service->getCacheKey([static::class, __FUNCTION__], func_get_args());
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            $result = Setting::globalItems()->get();
            Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, static::CACHE_TTL);
        }
        return $result;
    }

    public function getAllGlobalPaginated(): LengthAwarePaginator {
        return Setting::globalItems()->paginate(Setting::getPaginationCount());
    }

    /**
     * @return Setting[]|Collection
     */
    public function getAllSystem(): array|Collection {
        $cache_key = $this->cache_service->getCacheKey([static::class, __FUNCTION__], func_get_args());
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            $result = Setting::systemSettings()->get();
            Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, static::CACHE_TTL);
        }
        return $result;
    }

    public function getAllSystemPaginated(): LengthAwarePaginator {
        return Setting::systemSettings()->paginate(Setting::getPaginationCount());
    }
}
