<?php


namespace App\Utils\CMS\Setting\ShipmentCost;


use App\Interfaces\SettingDataInterface;
use App\Models\State;
use JetBrains\PhpStorm\ArrayShape;
use stdClass;

class ShipmentCostModel implements SettingDataInterface
{
    private int $shipment_cost;
    private int $minimum_purchase_free_shipment;
    private array $custom_states;

    public function __construct()
    {
        $this->shipment_cost = 0;
        $this->minimum_purchase_free_shipment = 0;
        $this->custom_states = [];
    }

    public function getShipmentCost(): int
    {
        return $this->shipment_cost;
    }

    public function setShipmentCost(int $shipment_cost): void
    {
        $this->shipment_cost = $shipment_cost;
    }

    public function getMinimumPurchaseFreeShipment(): int
    {
        return $this->minimum_purchase_free_shipment;
    }

    public function setMinimumPurchaseFreeShipment(int $minimum_purchase_free_shipment): void
    {
        $this->minimum_purchase_free_shipment = $minimum_purchase_free_shipment;
    }

    public function getCustomStates(): array
    {
        return $this->custom_states;
    }

    public function putCustomState(int $state_id, int $shipment_cost): array
    {
        $new_state = new stdClass();
        $new_state->shipment_cost = $shipment_cost;
        $new_state->state = State::find($state_id);

        $this->custom_states[$state_id] = $new_state;
        return $this->custom_states;
    }

    public function serialize(): bool|string|null
    {
        return json_encode($this);
    }

    public function unserialize(string $data): void
    {
        $tmp_data = json_decode($data, true);
        $this->shipment_cost = $tmp_data["shipment_cost"];
        $this->custom_states = $tmp_data["custom_states"];
        $this->minimum_purchase_free_shipment = $tmp_data["minimum_purchase_free_shipment"];
    }

    public function validate(): bool
    {
        foreach ($this->custom_states as $state_id => $custom_state) {
            if (!$this->isValidStateId($state_id) or
                !$this->isValidShipmentCost($custom_state->shipment_cost))
                return false;
        }

        return $this->isValidShipmentCost($this->shipment_cost);
    }

    private function isValidShipmentCost($shipment_cost): bool
    {
        return is_integer($shipment_cost) and $shipment_cost >= 0;
    }

    private function isValidStateId($state_id): bool
    {
        return State::where("id", $state_id)->count() > 0;
    }

    public function getPrimaryKey(): string
    {
        return "";
    }

    #[ArrayShape(["shipment_cost" => "int", "minimum_purchase_free_shipment" => "int", "custom_states" => "array"])]
    public function jsonSerialize(): array
    {
        return [
            "shipment_cost" => $this->shipment_cost,
            "minimum_purchase_free_shipment" => $this->minimum_purchase_free_shipment,
            "custom_states" => $this->custom_states
        ];
    }
}
