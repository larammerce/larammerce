<?php

namespace App\Interfaces\Repositories;

use App\Exceptions\Setting\CMSRecordNotFoundException;
use App\Exceptions\Setting\SettingNotFoundException;
use App\Interfaces\SettingDataInterface;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SettingRepositoryInterface
{
    /**
     * @throws CMSRecordNotFoundException
     */
    public function getCMSRecord(string $key): Setting;

    /**
     * @return Setting[]|Collection
     */
    public function getAllCMSRecords(): array|Collection;

    public function getAllCMSRecordsPaginated(): LengthAwarePaginator;

    /**
     * @throws SettingNotFoundException
     */
    public function findById(int $id): Setting;

    /**
     * @throws SettingNotFoundException
     */
    public function findByKey(string $key): Setting;

    public function create(string $key, string $value, ?User $user = null, bool $is_system_setting = false): Setting;

    public function createWithSettingDataInterface(string $key, SettingDataInterface $data, ?User $user = null, bool $is_system_setting = false): Setting;

    public function update(Setting $setting, string $key, string $value, ?User $user = null, bool $is_system_setting = false): Setting;

    public function delete(Setting $setting): bool;

    /**
     * @return Setting[]|Collection
     */
    public function getAll(): array|Collection;

    public function getAllPaginated(): LengthAwarePaginator;

    /**
     * @return Setting[]|Collection
     */
    public function getAllPersonal(): array|Collection;

    public function getAllPersonalPaginated(): LengthAwarePaginator;

    /**
     * @return Setting[]|Collection
     */
    public function getAllGlobal(): array|Collection;

    public function getAllGlobalPaginated(): LengthAwarePaginator;

    /**
     * @return Setting[]|Collection
     */
    public function getAllSystem(): array|Collection;

    public function getAllSystemPaginated(): LengthAwarePaginator;
}
