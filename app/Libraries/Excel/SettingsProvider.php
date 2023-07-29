<?php

namespace App\Libraries\Excel;

use App\Libraries\Excel\Cache\CacheManager;
use PhpOffice\PhpSpreadsheet\Settings;

class SettingsProvider
{
    /**
     * @var CacheManager
     */
    private $cache;

    public function __construct(CacheManager $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Provide PhpSpreadsheet settings.
     */
    public function provide()
    {
        $this->configureCellCaching();
    }

    protected function configureCellCaching()
    {
        Settings::setCache(
            $this->cache->driver()
        );
    }
}
