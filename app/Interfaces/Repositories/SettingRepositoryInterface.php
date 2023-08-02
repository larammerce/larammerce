<?php

namespace App\Interfaces\Repositories;

use App\Exceptions\Setting\CMSRecordNotFoundException;
use App\Interfaces\SettingDataInterface;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
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

    public function create(string $key, string $value, Authenticatable|User|null $user = null, bool $is_system_setting = false): Setting;

    public function createWithSettingDataInterface(string $key, SettingDataInterface $data, Authenticatable|User|null $user = null, bool $is_system_setting = false): Setting;

    public function update(Setting $setting, string $key, string $value, Authenticatable|User|null $user = null, bool $is_system_setting = false): Setting;

    public function updateWithSettingDataInterface(Setting $setting, string $key, SettingDataInterface $data, Authenticatable|User|null $user = null, bool $is_system_setting = false): Setting;

    public function delete(Setting $setting): bool;

    public function find(int $id): ?Setting;

    public function findByKey(string $key): ?Setting;

    public function findGlobalSystemSetting(string $key): ?Setting;

    public function updateGlobalSystemSetting(Setting $setting, string $key, string $value): Setting;

    public function updateGlobalSystemSettingWithDataInterface(Setting $setting, string $key, SettingDataInterface $data): Setting;

    public function createGlobalSystemSettingWithDataInterface(string $key, SettingDataInterface $data): Setting;

    public function findPersonalSystemSetting(string $key, Authenticatable|User $user): ?Setting;

    public function updatePersonalSystemSetting(Setting $setting, string $key, string $value, Authenticatable|User $user): Setting;

    public function updatePersonalSystemSettingWithDataInterface(Setting $setting, string $key, SettingDataInterface $data, Authenticatable|User $user): Setting;

    public function createPersonalSystemSettingWithDataInterface(string $key, SettingDataInterface $data, Authenticatable|User $user): Setting;
}
