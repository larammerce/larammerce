<?php

namespace App\Interfaces\Repositories;

use App\Exceptions\Setting\CMSRecordNotFoundException;
use App\Models\Setting;

interface SettingRepositoryInterface
{
    /**
     * @throws CMSRecordNotFoundException
     */
    public function getCMSRecord(string $key): Setting;
}
