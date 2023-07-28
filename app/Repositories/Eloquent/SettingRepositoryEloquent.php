<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\Setting\CMSRecordNotFoundException;
use App\Interfaces\Repositories\SettingRepositoryInterface;
use App\Models\Setting;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SettingRepositoryEloquent implements SettingRepositoryInterface
{
    const CACHE_TAGS = ["settings"];

    /**
     * @throws CMSRecordNotFoundException
     */
    public function getCMSRecord($key): Setting {
        $cache_key = "SettingRepositoryEloquent.getCMSRecord.{$key}";
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            try {
                $result = Setting::cmsRecords()->where("key", $key)->firstOrFail();
                Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, 1);
            } catch (Exception $e) {
                Cache::put($cache_key, null, 1);
                throw new CMSRecordNotFoundException("CMS Record with key {$key} not found !");
            }
        }
        return $result;
    }

    public function getAllCMSRecords(): array|Collection {
        $cache_key = "SettingRepositoryEloquent.getAllCMSRecords";
        if (Cache::tags(static::CACHE_TAGS)->has($cache_key)) {
            $result = Cache::tags(static::CACHE_TAGS)->get($cache_key);
        } else {
            $result = Setting::cmsRecords()->get();
            Cache::tags(static::CACHE_TAGS)->put($cache_key, $result, 1);
        }
        return $result;
    }

    public function getAllCMSRecordsPaginated(): LengthAwarePaginator {
        return Setting::cmsRecords()->paginate(Setting::getPaginationCount());
    }
}
