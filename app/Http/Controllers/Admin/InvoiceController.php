<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Invoice\ShipmentStatus;
use App\Models\CustomerUser;
use App\Models\Invoice;
use App\Utils\Common\History;
use App\Utils\Common\RequestService;
use App\Utils\Common\SMSService;
use App\Utils\ShipmentService\Factory as ShipmentFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class InvoiceController extends BaseController
{
    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(customer_user_id="exists:customer_users,id",
     *     start_hour="date_format:H:i",
     *     finish_hour="date_format:H:i",
     *     first_date="date",
     *     last_date="date")
     */
    public function index(Request $request): Factory|View|Application
    {
        $delivery_start_time = $request->get('start_hour');
        $delivery_finish_time = $request->get('finish_hour');
        $first_delivery_date = $request->get('first_date');
        $last_delivery_date = $request->get('last_date');

        $customerUser = null;
        if (request()->has('customer_user_id')) {
            parent::setPageAttribute(request()->get("customer_user_id"));
            $customerUser = CustomerUser::find(request()->get('customer_user_id'));
            $invoices = Invoice::where("customer_user_id", request()->get("customer_user_id"))
                ->with(['customer', 'products'])->paginate(Invoice::getPaginationCount());
        } else {
            parent::setPageAttribute();
            if ($request->has('start_hour')) {
                if ($last_delivery_date === '') {
                    $invoices = Invoice::whereBetween('delivery_finish_time', [$delivery_start_time, $delivery_finish_time])
                        ->with('customer', 'products')->paginate(Invoice::getPaginationCount());
                } else {
                    $invoices = Invoice::whereBetween('delivery_finish_time', [$delivery_start_time, $delivery_finish_time])
                        ->whereBetween('delivery_date', [$first_delivery_date, $last_delivery_date])
                        ->with('customer', 'products')->paginate(Invoice::getPaginationCount());
                }

            } else {
                $invoices = Invoice::with(['customer', 'products'])->paginate(Invoice::getPaginationCount());
            }
        }
        return view('admin.pages.invoice.index', compact('invoices', 'customerUser'));
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(customer_user_id="exists:customer_users,id")
     */
    public function create(): Factory|View|Application
    {
        $customerUser = CustomerUser::find(request()->get('customer_user_id'));
        return view('admin.pages.invoice.create', compact('customerUser'));
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(payment_type="required", customer_user_id="required|exists:customer_users,id",
     *     customer_address_id="required|exists:customer_addresses,id", payment_status="required")
     */
    public function store(Request $request): RedirectResponse
    {
        $invoice = Invoice::create($request->all());
        return redirect()->route('admin.invoice.edit', $invoice);
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     */
    public function show(Invoice $invoice): Factory|View|Application
    {
        return view('admin.pages.invoice.show', compact('invoice'));
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     */
    public function edit(Invoice $invoice): Factory|View|Application
    {
        $invoice->load('products', 'customer');
        return view('admin.pages.invoice.edit')->with(['invoice' => $invoice]);
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(customer_address_id="required|exists:customer_addresses,id")
     */
    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->update($request->all());
        return History::redirectBack();
    }

    /**
     * @role(super_user)
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();
        return back();
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(products="array", products.*="exists:products,id")
     */
    public function attachProducts(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->products()->detach();
        $invoice->products()->attach($request->get('products'));
        return redirect()->route('admin.pages.invoice.index');
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     */
    public function showShipmentSending(Request $request, Invoice $invoice): View|Factory|Application|RedirectResponse
    {
        if ($invoice->shipment_status == ShipmentStatus::WAREHOUSE_EXIT_TAB)
            return view('admin.pages.invoice.shipment-sending')->with(['invoice' => $invoice]);
        return History::redirectBack();
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     * @rules(shipment_driver="required", shipment_data_tracking_code="required_if:shipment_driver,post",
     *     shipment_data_delivery_date="required", base_rules="call method getBaseRules of current selected driver.")
     */
    public function setShipmentSending(Request $request, Invoice $invoice): RedirectResponse
    {
        RequestService::setAttr('shipment_status', ShipmentStatus::SENDING);
        RequestService::setAttr('shipment_data',
            ShipmentFactory::driver($request->get('shipment_driver'))->parseData($request->all())
        );

        $invoice->update($request->all());
        if ($request->has('notify_customer'))
            SMSService::send(
                'sms-invoice-sending',
                $invoice->customer->main_phone,
                [
                    'invoiceTrackingCode' => $invoice->tracking_code,
                ],
                [
                    'customerName' => $invoice->customer->user->name,
                    'shipmentDeliveryDate' => $invoice->getShipmentDeliveryDate(),
                ]);
        return redirect()->route('admin.invoice.edit', $invoice);
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     */
    public function showShipmentDelivered(Request $request, Invoice $invoice): View|Factory|Application|RedirectResponse
    {
        if ($invoice->shipment_status == ShipmentStatus::SENDING)
            return view('admin.pages.invoice.shipment-delivered')->with(['invoice' => $invoice]);
        return History::redirectBack();
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     */
    public function setShipmentDelivered(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->update(['shipment_status' => ShipmentStatus::DELIVERED]);
        if ($request->has('notify_customer'))
            SMSService::send(
                'sms-invoice-delivered',
                $invoice->customer->main_phone,
                [
                    'invoiceTrackingCode' => $invoice->tracking_code,
                ],
                [
                    'customerName' => $invoice->customer->user->name,
                ]);
        return redirect()->route('admin.invoice.edit', $invoice);
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     */
    public function showShipmentExitTab(Request $request, Invoice $invoice): View|Factory|Application|RedirectResponse
    {
        if ($invoice->shipment_status == ShipmentStatus::PREPARING_TO_SEND)
            return view('admin.pages.invoice.shipment-exit-tab')->with(['invoice' => $invoice]);
        return History::redirectBack();
    }

    /**
     * @role(super_user, stock_manager, acc_manager)
     */
    public function setShipmentExitTab(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->update(['shipment_status' => ShipmentStatus::WAREHOUSE_EXIT_TAB]);
        if ($request->has('notify_customer'))
            SMSService::send(
                'sms-invoice-exit-tab',
                $invoice->customer->main_phone,
                [
                    'invoiceTrackingCode' => $invoice->tracking_code,
                ],
                [
                    'customerName' => $invoice->customer->user->name,
                ]);
        return redirect()->route('admin.invoice.edit', $invoice);
    }

    public function getModel(): ?string
    {
        return Invoice::class;
    }
}
