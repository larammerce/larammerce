<?php

namespace App\Helpers;

use App\Utils\CMS\Setting\SystemUpgrade\SystemUpgradeSettingService;
use Exception;

class VersionHelper {
    const REPO_OWNER = 'larammerce';
    const REPO_NAME = 'larammerce';
    const VERSION_FILE = '.version';
    const GITHUB_API_URL = 'https://api.github.com/repos/';

    private static $REQUEST_CACHE = [];

    public static function getCurrentVersion(bool $only_major_version = false): string {
        if (!key_exists('current_version', self::$REQUEST_CACHE)) {
            // Check if the .version file exists
            if (!file_exists(base_path(self::VERSION_FILE))) {
                return '0.00.000';
            }
            self::$REQUEST_CACHE['current_version'] = trim(file_get_contents(base_path(self::VERSION_FILE)));
        }

        // if the flag $only_major_version_is_set, explode the version value to 3 parts separated by '.' and return the first two parts joined by '.'
        if ($only_major_version) {
            $version_parts = explode('.', self::$REQUEST_CACHE['current_version']);

            // Check if the exploded version has at least 2 parts
            if (count($version_parts) >= 2) {
                return $version_parts[0] . '.' . $version_parts[1];
            }
        }
        return self::$REQUEST_CACHE['current_version'];
    }

    public static function getLatestPatchVersion(bool $only_major_version = false): string {
        if (!key_exists('latest_patch_version', self::$REQUEST_CACHE)) {
            $record = SystemUpgradeSettingService::getRecord();
            $branch = $record->getLarammerceBranchName();

            // Return the content of the .version file from the online repository for this branch
            try {
                self::$REQUEST_CACHE['latest_patch_version'] = static::fetchVersionFromGithub($branch);
            } catch (Exception $e) {
                self::$REQUEST_CACHE['latest_patch_version'] = static::getCurrentVersion();
            }
        }

        // if the flag $only_major_version_is_set, explode the version value to 3 parts separated by '.' and return the first two parts joined by '.'
        if ($only_major_version) {
            $version_parts = explode('.', self::$REQUEST_CACHE['latest_patch_version']);

            // Check if the exploded version has at least 2 parts
            if (count($version_parts) >= 2) {
                return $version_parts[0] . '.' . $version_parts[1];
            }
        }

        return self::$REQUEST_CACHE['latest_patch_version'];
    }

    public static function getLatestStableVersion(bool $only_major_version = false): string {
        if (!key_exists('latest_stable_version', self::$REQUEST_CACHE)) {
            // Return the content of the .version file from the production branch of the online repository
            try {
                self::$REQUEST_CACHE['latest_stable_version'] = static::fetchVersionFromGithub('production');
            } catch (Exception $e) {
                self::$REQUEST_CACHE['latest_stable_version'] = static::getCurrentVersion();
            }
        }

        // if the flag $only_major_version_is_set, explode the version value to 3 parts separated by '.' and return the first two parts joined by '.'
        if ($only_major_version) {
            $version_parts = explode('.', self::$REQUEST_CACHE['latest_stable_version']);

            // Check if the exploded version has at least 2 parts
            if (count($version_parts) >= 2) {
                return $version_parts[0] . '.' . $version_parts[1];
            }
        }

        return self::$REQUEST_CACHE['latest_stable_version'];
    }

    /**
     * @throws Exception
     */
    private static function fetchVersionFromGithub(string $branch): string {
        # Fetch the data using address https://raw.githubusercontent.com/larammerce/larammerce/production/.version
        $rawUrl = 'https://raw.githubusercontent.com/' . self::REPO_OWNER . '/' . self::REPO_NAME . '/' . $branch . '/' . self::VERSION_FILE;

        # Use file_get_contents to fetch the data
        try {
            return file_get_contents($rawUrl);
        } catch (Exception $e) {
            throw new Exception('Failed to fetch version from GitHub.');
        }

    }
}

