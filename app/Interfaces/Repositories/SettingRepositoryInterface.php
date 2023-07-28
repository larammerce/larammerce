<?php

namespace App\Interfaces\Repositories;

use App\Exceptions\Setting\CMSRecordNotFoundException;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SettingRepositoryInterface
{
    /**
     * @throws CMSRecordNotFoundException
     */
    public function getCMSRecord(string $key): Setting;

    public function getAllCMSRecords(): array|Collection;

    public function getAllCMSRecordsPaginated(): LengthAwarePaginator;
}
