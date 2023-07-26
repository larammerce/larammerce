<?php

namespace App\Http\Controllers\Admin;

use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\ShipmentCost\ShipmentCostDataInterface;
use App\Utils\CMS\Setting\ShipmentCost\ShipmentCostService;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class ShipmentCostController extends BaseController
{

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function edit(): Factory|View|Application
    {
        $shipment_cost_setting_record = ShipmentCostService::getRecord();
        return view("admin.pages.shipment-cost.edit")->with([
            "shipment_cost_model" => $shipment_cost_setting_record
        ]);
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(shipment_cost="required|integer|min:0",
     *        minimum_purchase_free_shipment="required|integer|min:0",
     *        custom_states="array",
     *        custom_states.*.state_id="exists:states,id",
     *        custom_states.*.shipment_cost="integer|min:0")
     */
    public function update(Request $request): RedirectResponse
    {
        $record = new ShipmentCostDataInterface();
        $record->setShipmentCost($request->get("shipment_cost"));
        $record->setMinimumPurchaseFreeShipment($request->get("minimum_purchase_free_shipment"));
        $items = $request->get("custom_states");
        if (is_array($items))
            foreach ($items as $item) {
                if (isset($item["state_id"]) and $item["state_id"] != null and
                    ((is_string($item["state_id"]) and strlen($item["state_id"]) > 0) or
                        (is_integer($item["state_id"]) and $item["state_id"] > 0)))
                    $record->putCustomState($item["state_id"], $item["shipment_cost"]);
            }
        try {
            ShipmentCostService::setRecord($record);
            return History::redirectBack();
        } catch (NotValidSettingRecordException $e) {
            SystemMessageService::addErrorMessage('system_messages.shipment_cost.invalid_record');
            return redirect()->back()->withInput();
        }

    }

    public function getModel(): ?string
    {
        return null;
    }
}
