<?php

namespace App\Http\Controllers\Admin;

use App\Utils\CMS\Setting\Logistic\LogisticService;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class LogisticController extends BaseController
{
    /**
     * @role(super_user, acc_manager, cms_manager, stock_manager)
     */
    public function edit(): Factory|View|Application
    {
        $record = LogisticService::getRecord();
        $delivery_days = $record->getDeliveryDays();
        $delivery_hours = $record->getDeliveryHours();
        $delivery_table_cells = $record->getDeliveryTableCells();
        $max_items_count = $record->getMaxItemsCount();
        $max_total_price = $record->getMaxTotalPrice();
        $rows_offset = $record->getRowsOffset();
        $rows_available = $record->getRowsAvailable();
        $columns_count = count($delivery_hours);

        return view("admin.pages.logistic.edit")->with([
            //"logistic" => $record,
            "days" => $delivery_days,
            "hours" => $delivery_hours,
            "cells" => $delivery_table_cells,
            "columns_count" => $columns_count,
            "max_items_count" => $max_items_count,
            "max_total_price" => $max_total_price,
            "rows_offset" => $rows_offset,
            "rows_available" => $rows_available
        ]);
    }


    /**
     * @role(super_user, acc_manager)
     * @rules(max_items_count="required|integer|min:0",
     *        max_total_price="required|integer|min:0",
     *        rows_offset="required|integer|min:0|max:100",
     *        rows_available="required|integer|min:1|max:100",
     *        )
     */
    public function update(Request $request): RedirectResponse
    {
        $max_items_count = $request->get('max_items_count');
        $max_total_price = $request->get('max_total_price');
        $rows_offset = $request->get('rows_offset');
        $rows_available = $request->get('rows_available');
        $cells = json_decode($request->get('cells'), true);
        $hours = json_decode($request->get('hours'), true);
        LogisticService::update($cells, $hours, $max_items_count, $max_total_price, $rows_offset, $rows_available);
        return History::redirectBack();
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function ajaxUpdateCells(Request $request): ?JsonResponse
    {
        if ($request->ajax()) {
            $fresh_record = LogisticService::getRecord();
            $fresh_delivery_table_cells = $fresh_record->getDeliveryTableCells();
            return response()->json(array('fresh_cells_array' => $fresh_delivery_table_cells));
        }
        return null;
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function getInvoices(): Factory|View|Application
    {
        return view("admin.pages.logistic.get-invoices");
    }

    public function getModel(): ?string
    {
        return null;
    }
}
