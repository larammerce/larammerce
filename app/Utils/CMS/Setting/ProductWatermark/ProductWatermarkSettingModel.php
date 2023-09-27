<?php

namespace App\Utils\CMS\Setting\ProductWatermark;

use App\Interfaces\ImageOwnerInterface;
use App\Interfaces\SettingDataInterface;
use App\Utils\Common\ImageService;
use Ramsey\Uuid\Uuid;

class ProductWatermarkSettingModel implements SettingDataInterface, ImageOwnerInterface {

    private string $watermark_image;
    private string $watermark_uuid;
    private string $watermark_position;
    private int $watermark_size_percentage;
    private bool $is_active;

    public function __construct(
        string $watermark_image = "",
        string $watermark_position = "",
        int    $watermark_size_percentage = 10,
        bool   $is_active = false
    ) {
        $this->watermark_image = $watermark_image;
        $this->watermark_position = $watermark_position;
        $this->watermark_size_percentage = $watermark_size_percentage;
        $this->is_active = $is_active;
        $this->watermark_uuid = Uuid::uuid4()->toString();

    }

    /**
     * @return string
     */
    public function getWatermarkImage(): string {
        return $this->watermark_image;
    }

    /**
     * @param string $watermark_image
     */
    public function setWatermarkImage(string $watermark_image): void {
        $this->watermark_image = $watermark_image;
    }

    /**
     * @return string
     */
    public function getWatermarkUUID(): string {
        return $this->watermark_uuid;
    }

    /**
     * @param string $watermark_uuid
     */
    public function setWatermarkUUID(string $watermark_uuid): void {
        $this->watermark_uuid = $watermark_uuid;
    }

    /**
     * @return string
     */
    public function getWatermarkPosition(): string {
        return $this->watermark_position;
    }

    /**
     * @param string $watermark_position
     */
    public function setWatermarkPosition(string $watermark_position): void {
        $this->watermark_position = $watermark_position;
    }

    /**
     * @return int
     */
    public function getWatermarkSizePercentage(): int {
        return $this->watermark_size_percentage;
    }

    /**
     * @param int $watermark_size_percentage
     */
    public function setWatermarkSizePercentage(int $watermark_size_percentage): void {
        $this->watermark_size_percentage = $watermark_size_percentage;
    }

    public function isActive(): bool {
        return $this->is_active;
    }

    public function activate(): void {
        $this->is_active = true;
    }

    public function deactivate(): void {
        $this->is_active = false;
    }

    public function regenerateUUID(): void {
        $this->watermark_uuid = Uuid::uuid4()->toString();
    }

    public function serialize(): bool|string|null {
        return json_encode($this);
    }

    public function unserialize(string $data): void {
        $data = json_decode($data);
        $this->watermark_image = $data->watermark_image;
        $this->watermark_uuid = $data->watermark_uuid;
        $this->watermark_position = $data->watermark_position;
        $this->watermark_size_percentage = $data->watermark_size_percentage;
        $this->is_active = $data->is_active;
    }

    public function validate(): bool {
        return true;
    }

    public function getPrimaryKey(): string {
        return "";
    }

    public function jsonSerialize(): array {
        return [
            "watermark_image" => $this->watermark_image,
            "watermark_uuid" => $this->watermark_uuid,
            "watermark_position" => $this->watermark_position,
            "watermark_size_percentage" => $this->watermark_size_percentage,
            "is_active" => $this->is_active,
        ];
    }

    public function hasImage() {
        return strlen($this->watermark_image) !== 0;
    }

    public function getImagePath() {
        return $this->watermark_image;
    }

    public function setImagePath() {
        $tmpImage = ImageService::saveImage($this->getImageCategoryName(), "watermark_image");
        $this->watermark_image = $tmpImage->destinationPath . '/' . $tmpImage->name;
    }

    public function removeImage() {
        $this->watermark_image = "";
    }

    public function getDefaultImagePath() {
        return "/admin_dashboard/images/icons/unknown-brand.png";
    }

    public function getImageCategoryName() {
        return "product_watermark";
    }

    public function isImageLocal() {
        return true;
    }
}
