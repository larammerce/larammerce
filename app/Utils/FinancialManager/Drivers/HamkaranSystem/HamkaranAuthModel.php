<?php


namespace App\Utils\FinancialManager\Drivers\HamkaranSystem;


use App\Interfaces\SettingDataInterface;
use Carbon\Carbon;

class HamkaranAuthModel implements SettingDataInterface
{
    private string $id;
    private string $m;
    private string $e;
    private array $c;
    private Carbon $created_at;

    public function __construct($id, $m, $e, $c = [])
    {
        $this->created_at = Carbon::now();
        $this->id = $id;
        $this->m = $m;
        $this->e = $e;
        $this->c = $c;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getModulus(): string
    {
        return $this->m;
    }

    public function setModulus(string $m): void
    {
        $this->m = $m;
    }

    public function getExponent(): string
    {
        return $this->e;
    }

    public function setExponent(string $e): void
    {
        $this->e = $e;
    }

    public function getCookies(): array
    {
        return $this->c;
    }

    public function setCookies(array $c): void
    {
        $this->c = $c;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize(string $data): void
    {
        $tmp_data = json_decode($data);
        $this->id = $tmp_data->id;
        $this->e = $tmp_data->e;
        $this->m = $tmp_data->m;
        $this->c = $tmp_data->c;
        $this->created_at = unserialize($tmp_data->created_at);
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "m" => $this->m,
            "e" => $this->e,
            "c" => $this->c,
            "created_at" => serialize($this->created_at)
        ];
    }

    public function validate(): bool
    {
        return isset($this->id) and isset($this->m) and isset($this->e);
    }

    public function getPrimaryKey(): string
    {
        return "";
    }
}
