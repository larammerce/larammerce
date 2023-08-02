<?php

namespace App\Utils\CMS\Setting\SystemUpgrade;


use App\Interfaces\SettingDataInterface;


class SystemUpgradeSettingModel implements SettingDataInterface
{
    private string $larammerce_repo_address;

    private string $larammerce_branch_name;
    private string $larammerce_theme_repo_address;

    private string $larammerce_theme_branch_name;

    public function __construct(
        string $larammerce_repo_address = "",
        string $larammerce_branch_name = "",
        string $larammerce_theme_repo_address = "",
        string $larammerce_theme_branch_name = ""
    ) {
        $this->larammerce_repo_address = $larammerce_repo_address;
        $this->larammerce_branch_name = $larammerce_branch_name;
        $this->larammerce_theme_repo_address = $larammerce_theme_repo_address;
        $this->larammerce_theme_branch_name = $larammerce_theme_branch_name;
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

    /**
     * @return string
     */
    public function getLarammerceBranchName(): string {
        return $this->larammerce_branch_name;
    }

    /**
     * @param string $larammerce_branch_name
     */
    public function setLarammerceBranchName(string $larammerce_branch_name): void {
        $this->larammerce_branch_name = $larammerce_branch_name;
    }

    /**
     * @return string
     */
    public function getLarammerceThemeBranchName(): string {
        return $this->larammerce_theme_branch_name;
    }

    /**
     * @param string $larammerce_theme_branch_name
     */
    public function setLarammerceThemeBranchName(string $larammerce_theme_branch_name): void {
        $this->larammerce_theme_branch_name = $larammerce_theme_branch_name;
    }

    public function serialize(): bool|string|null {
        return json_encode($this);
    }

    public function unserialize(string $data): void {
        $tmp_data = json_decode($data, true);
        $this->larammerce_repo_address = $tmp_data["larammerce_repo_address"] ?? "";
        $this->larammerce_branch_name = $tmp_data["larammerce_branch_name"] ?? "";
        $this->larammerce_theme_repo_address = $tmp_data["larammerce_theme_repo_address"] ?? "";
        $this->larammerce_theme_branch_name = $tmp_data["larammerce_theme_branch_name"] ?? "";
    }

    public function validate(): bool {
        return true;
    }

    public function getPrimaryKey(): string {
        return "";
    }

    public function jsonSerialize(): array {
        return [
            "larammerce_repo_address" => $this->larammerce_repo_address,
            "larammerce_branch_name" => $this->larammerce_branch_name,
            "larammerce_theme_repo_address" => $this->larammerce_theme_repo_address,
            "larammerce_theme_branch_name" => $this->larammerce_theme_branch_name
        ];
    }

}
