<?php


namespace App\Utils\CMS\Setting\SystemUpgrade;


use App\Utils\CMS\Setting\AbstractSettingModel;
use App\Utils\Reflection\ReflectiveNamespace;
use JetBrains\PhpStorm\ArrayShape;

class SystemUpgradeSettingModel extends AbstractSettingModel
{
    private string $larammerce_repo_address;
    private string $larammerce_theme_repo_address;

    public function __construct(string $larammerce_repo_address = "", string $larammerce_theme_repo_address = "")
    {
        $this->larammerce_repo_address = $larammerce_repo_address;
        $this->larammerce_theme_repo_address = $larammerce_theme_repo_address;
    }

    /**
     * @return string
     */
    public function getLarammerceRepoAddress(): string {
        return $this->larammerce_repo_address;
    }

    /**
     * @param string $larammerce_repo_address
     */
    public function setLarammerceRepoAddress(string $larammerce_repo_address): void {
        $this->larammerce_repo_address = $larammerce_repo_address;
    }

    /**
     * @return string
     */
    public function getLarammerceThemeRepoAddress(): string {
        return $this->larammerce_theme_repo_address;
    }

    /**
     * @param string $larammerce_theme_repo_address
     */
    public function setLarammerceThemeRepoAddress(string $larammerce_theme_repo_address): void {
        $this->larammerce_theme_repo_address = $larammerce_theme_repo_address;
    }

    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize(string $data): void
    {
        $tmp_data = json_decode($data, true);
        $this->larammerce_repo_address = $tmp_data["larammerce_repo_address"];
        $this->larammerce_theme_repo_address = $tmp_data["larammerce_theme_repo_address"];

    }

    public function validate(): bool
    {
        return true;
    }

    public function getPrimaryKey(): string
    {
        return "";
    }

    public function jsonSerialize(): array
    {
        return [
            "larammerce_repo_address" => $this->larammerce_repo_address,
            "larammerce_theme_repo_address" => $this->larammerce_theme_repo_address
        ];
    }

}
