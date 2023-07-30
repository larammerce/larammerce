<?php


namespace App\Utils\CMS\Setting\CustomerLocation;


use App\Interfaces\SettingDataInterface;
use App\Models\City;
use App\Models\State;
use JetBrains\PhpStorm\ArrayShape;

class CustomerLocationModel implements SettingDataInterface
{
    private State $state;
    private City $city;

    public function __construct(State $state, City $city)
    {
        $this->state = $state;
        $this->city = $city;
    }

    public function getState(): State
    {
        return $this->state;
    }

    public function setState(State $state): void
    {
        $this->state = $state;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): void
    {
        $this->city = $city;
    }


    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize($data): void
    {
        $tmp_data = json_decode($data, true);
        $this->state = new State();
        $this->state->setRawAttributes($tmp_data["state"]);

        $this->city = new City();
        $this->city->setRawAttributes($tmp_data["city"]);
    }

    #[ArrayShape(["state" => "array", "city" => "array"])]
    public function jsonSerialize(): array
    {
        return [
            "state" => $this->state->toArray(),
            "city" => $this->city->toArray()
        ];
    }

    public function validate(): bool
    {
        return isset($this->state) and isset($this->state->id)
            and isset($this->city) and isset($this->city->id);
    }

    public function getPrimaryKey(): string
    {
        return "";
    }

    public function equals(CustomerLocationModel $model): bool {
        return $this->city->id === $model->city->id and
            $this->state->id === $model->state->id;
    }
}
