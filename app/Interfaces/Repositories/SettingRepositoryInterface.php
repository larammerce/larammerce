<?php

namespace App\Interfaces\Repositories;

use App\Exceptions\Setting\CMSRecordNotFoundException;
use App\Interfaces\SettingDataInterface;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SettingRepositoryInterface {
    /**
     * @throws CMSRecordNotFoundException
     */
    public function getCMSRecord(string $key): Setting;

    /**
     * @return Setting[]|Collection
     */
    public function getAllCMSRecords(): array|Collection;

    public function getAllCMSRecordsPaginated(): LengthAwarePaginator;

    public function create(string $key, string $value, ?User $user = null, bool $is_system_setting = false): Setting;

    public function createWithSettingDataInterface(string $key, SettingDataInterface $data, ?User $user = null, bool $is_system_setting = false): Setting;

    public function update(Setting $setting, string $key, string $value, ?User $user = null, bool $is_system_setting = false): Setting;

    public function updateWithSettingDataInterface(Setting $setting, string $key, SettingDataInterface $data, ?User $user = null, bool $is_system_setting = false): Setting;

    public function delete(Setting $setting): bool;

    public function find(int $id): ?Setting;

    public function findByKey(string $key): ?Setting;

    public function findGlobalSystemSetting(string $key): ?Setting;

    public function updateGlobalSystemSetting(Setting $setting, setting $key, setting $value): Setting;
}
