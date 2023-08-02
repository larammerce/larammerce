<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/28/2017 AD
 * Time: 12:44
 */

namespace App\Http\Controllers\Customer;

use App\Enums\Discount\DiscountCardStatus;
use App\Enums\Invoice\NewInvoiceType;
use App\Enums\Invoice\PaymentStatus;
use App\Enums\Invoice\PaymentType;
use App\Enums\Invoice\ShipmentMethod;
use App\Enums\Invoice\ShipmentStatus;
use App\Exceptions\Discount\InvalidDiscountCodeException;
use App\Models\CustomerAddress;
use App\Models\DiscountCard;
use App\Models\Invoice;
use App\Models\InvoiceRow;
use App\Services\Customer\CustomerAddressService;
use App\Services\Invoice\NewInvoiceService;
use App\Utils\CMS\Enums\ExportType;
use App\Utils\CMS\ProductService;
use App\Utils\CMS\Setting\Logistic\LogisticService;
use App\Utils\CMS\Setting\Survey\SurveyService;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\EmailService;
use App\Utils\Common\MessageFactory;
use App\Utils\Common\SMSService;
use App\Utils\PaymentManager\ConfigProvider;
use App\Utils\PaymentManager\Exceptions\PaymentConnectionException;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidDriverException;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidParametersException;
use App\Utils\PaymentManager\Provider;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Throwable;

class InvoiceController extends BaseController {
    private NewInvoiceService $new_invoice_service;
    private CustomerAddressService $customer_address_service;

    public function __construct(NewInvoiceService $new_invoice_service, CustomerAddressService $customer_address_service) {
        parent::__construct();
        $this->new_invoice_service = $new_invoice_service;
        $this->customer_address_service = $customer_address_service;
    }

    public function index() {
        return h_view("public.orders");
    }

    public function submitCart(): RedirectResponse {

        $customer = get_customer_user();

        if ($customer->national_code == null) {
            SystemMessageService::addWarningMessage("messages.customer_user.no_national_code");
            return redirect()->route("customer.profile.show-edit-profile");
        }

        $fault_flag = false;
        $invoice = new Invoice();
        $invoice->status = NewInvoiceType::CART_SUBMISSION;
        $invoice->customer()->associate($customer);
        $invoice->status = NewInvoiceType::SHIPMENT;
        $invoice->has_paper = false;
        $invoice->is_legal = false;
        $invoice->shipment_method = ShipmentMethod::NONE;
        $invoice->updateAddress($customer->main_address);

        foreach (get_cart() as $row) {
            if (!$row->product->can_deliver) {
                SystemMessageService::addWarningMessage(
                    "system_messages.cart.cant_deliver",
                    [
                        "product_title" => $row->product->title
                    ]);
                $fault_flag = true;
                continue;
            }

            if ($row->product->hasCustomerMetaCategory() and
                $row->customerMetaItem === null) {
                SystemMessageService::addWarningMessage(
                    "system_messages.cart.fill_meta",
                    [
                        "product_title" => $row->product->title
                    ]);
                $fault_flag = true;
                continue;
            }

            if ($row->product === null) {
                $fault_flag = true;
                SystemMessageService::addWarningMessage(
                    "system_messages.cart.product_not_found",
                    ["product_id" => $row->product_id]);
                continue;
            }

            if (!$row->product->is_active) {
                $fault_flag = true;
                SystemMessageService::addWarningMessage(
                    "system_messages.cart.product_not_active",
                    [
                        "product_title" => $row->product->title
                    ]);
                continue;
            }

            if ($row->product->getMaximumAllowedPurchaseCount() < $row->count) {
                $fault_flag = true;
                SystemMessageService::addWarningMessage(
                    "system_messages.cart.product_max_count_not_allowed",
                    [
                        "product_title" => $row->product->title,
                        "product_count" => $row->product->getMaximumAllowedPurchaseCount()
                    ]);
                continue;
            }

            if ($row->product->getMinimumAllowedPurchaseCount() > $row->count) {
                $fault_flag = true;
                SystemMessageService::addWarningMessage(
                    "system_messages.cart.product_min_count_not_allowed",
                    [
                        "product_title" => $row->product->title,
                        "product_count" => $row->product->getMinimumAllowedPurchaseCount()
                    ]);
                continue;
            }

            $invoice_row = new InvoiceRow();
            $invoice_row->product()->associate($row->product);
            $invoice_row->count = $row->count;
            $invoice_row->cmi_id = $row->cmi_id;
            $invoice->rows->add($invoice_row);
        }

        $invoice->updateRows();
        $this->new_invoice_service->setTheNew($invoice);

        if ($invoice->sum < $this->new_invoice_service->getMinimumPurchase()) {
            SystemMessageService::addWarningMessage("system_messages.cart.minimum_purchase_error",
                ["minimum_purchase" => $this->new_invoice_service->getMinimumPurchase()]);
            $fault_flag = true;
        }

        if ($fault_flag)
            return redirect()->route("customer.cart.show");

        if ($invoice->is_shippable)
            SystemMessageService::addInfoMessage("system_messages.invoice.cart_submitted");
        return redirect()->route("customer.invoice.show-shipment");
    }

    public function showShipment(): Application|Factory|View|RedirectResponse {
        if (!$this->new_invoice_service->hasNew(NewInvoiceType::CART_SUBMISSION)) {
            SystemMessageService::addWarningMessage("system_messages.invoice.no_invoice");
            return redirect()->route("customer.cart.show");
        }

        $invoice = $this->new_invoice_service->getTheNew();

        if ($invoice->is_shippable)
            return h_view("public.invoice-shipment", [
                "invoice" => $invoice
            ]);

        return redirect()->route("customer.invoice.show-payment");
    }

    /**
     * @rules(customer_address_id="required|exists:customer_addresses,id", is_legal="required|boolean",
     *     shipment_method="required|in:".\App\Enums\Invoice\ShipmentMethod::stringValues(),
     *     delivery_period=config("cms.logistics.enabled") ? "required_if:logistics_enabled,1|delivery_period" : "delivery_period" )
     * @param Request $request
     * @description(comment="this method is for adding customer address and shipment method and paper need selection for each invoice")
     * @return RedirectResponse
     */
    public function saveShipment(Request $request): RedirectResponse {
        if (!$this->new_invoice_service->hasNew(NewInvoiceType::CART_SUBMISSION)) {
            SystemMessageService::addWarningMessage("system_messages.invoice.no_invoice");
            return redirect()->route("customer.cart.show");
        }

        $invoice = $this->new_invoice_service->getTheNew();
        $invoice->status = NewInvoiceType::SHIPMENT;
        $invoice->has_paper = true;
        $invoice->is_legal = $request->get("is_legal");
        $invoice->shipment_method = $request->get("shipment_method");

        if (LogisticService::isEnabled() and $request->has("logistics_enabled") and $request->get("logistics_enabled") == 1) {
            if (!LogisticService::selectDeliveryTableCell($request->get("delivery_period"), $invoice))
                return redirect()->back();
        }

        $this->new_invoice_service->setTheNew($invoice);

        $customer_address = CustomerAddress::find($request->get("customer_address_id"));
        $this->customer_address_service->setAddressAsMain($customer_address);

        $fault_flag = false;
        foreach ($invoice->rows as $row) {
            if (!$row->product->can_deliver) {
                SystemMessageService::addWarningMessage(
                    "system_messages.cart.cant_deliver",
                    [
                        "product_title" => $row->product->title
                    ]);
                $fault_flag = true;
            }
        }

        if ($fault_flag) {
            return redirect()->back();
        }

        return redirect()->route("customer.invoice.show-payment");
    }

    public function showPayment(): Application|Factory|View|RedirectResponse {
        if (!$this->new_invoice_service->hasNew(NewInvoiceType::SHIPMENT)) {
            SystemMessageService::addWarningMessage("system_messages.invoice.no_invoice");
            return redirect()->route("customer.cart.show");
        }

        $invoice = $this->new_invoice_service->getTheNew();

        return h_view("public.invoice-payment", [
            "invoice" => $invoice
        ]);
    }

    /**
     * @rules(payment_type="required|in:".\App\Enums\Invoice\PaymentType::stringValues())
     */
    public function savePayment(Request $request): Factory|Application|Redirector|View|\Illuminate\Contracts\Foundation\Application|RedirectResponse {

        $customer_user = get_customer_user();

        if (!$this->new_invoice_service->hasNew(NewInvoiceType::SHIPMENT)) {
            SystemMessageService::addWarningMessage("system_messages.invoice.no_invoice");
            return redirect()->route("customer.cart.show");
        }

        $invoice = $this->new_invoice_service->getTheNew();
        $invoice->payment_status = PaymentStatus::PENDING;
        $invoice->payment_type = $request->get("payment_type");
        $invoice->shipment_status = ShipmentStatus::WAITING_TO_CONFIRM;
        $invoice->is_active = false;
        $invoice->is_warned = false;
        $invoice->tracking_code = $this->new_invoice_service->createTrackingCode();
        $invoice->updateRows();
        $invoice->customPush();

        $this->new_invoice_service->forgetTheNew();
        $this->new_invoice_service->flush();


        if ($invoice->createFinManRelation()) {

            SMSService::send("sms-invoice-submitted", $customer_user->main_phone,
                [
                    "trackingCode" => $invoice->tracking_code
                ],
                [
                    "customerName" => $customer_user->user->name
                ]);

            if (config('mail-notifications.invoices.new_invoice')) {
                $subject = 'New Order';
                $emailAddress = config('mail-notifications.invoices.related_mail');
                // set blade template
                $template = "public.mail-new-invoice-notification";
                EmailService::send([
                    "data" => Invoice::find($invoice->id),
                ], $template, $emailAddress, $emailAddress, $subject);
            }

            if ($invoice->payment_type == PaymentType::ONLINE) {
                try {
                    if ($invoice->sum >= ConfigProvider::getMaxTransactionAmount()) {
                        SystemMessageService::addWarningMessage("system_messages.invoice.maximum_order_ceiling");
                        return redirect(route('customer.invoice.index'));
                    }
                    Provider::initiatePayment($invoice);
                    return redirect()->to(Provider::getPaymentRedirectionUrl());
                } catch (PaymentConnectionException|PaymentInvalidParametersException $e) {
                    Log::error("InvoiceController:savePayment:{$e->getMessage()}");
                    SystemMessageService::addErrorMessage("system_messages.invoice.payment_initiation_failed");
                } catch (PaymentInvalidDriverException $e) {
                    Log::error("InvoiceController:savePayment:{$e->getMessage()}");
                    SystemMessageService::addErrorMessage("system_messages.payment.bad_driver_passed");
                }
            }
        } else {
            SystemMessageService::addWarningMessage("system_messages.invoice.save_fin_man_error");
        }
        return redirect()->route("customer.invoice.show-checkout", $invoice);
    }

    public function showCheckout(?Invoice $invoice): Application|Factory|View|RedirectResponse {
        if ($invoice->customer_user_id != get_customer_user()->id) {
            SystemMessageService::addErrorMessage("system_messages.invoice.not_owner");
            return redirect()->route("customer.invoice.index");
        }

        if ($invoice == null) {
            SystemMessageService::addWarningMessage("system_messages.invoice.no_invoice");
            return redirect()->route("customer.cart.show");
        }

        if ($invoice->payment_status == PaymentStatus::SUBMITTED or
            $invoice->payment_status == PaymentStatus::CONFIRMED) {
            $invoice->status = NewInvoiceType::PAYMENT_DONE;
        } else {
            $invoice->status = NewInvoiceType::PAYMENT_PENDING;
        }

        if (request()->has("export")) {
            if (request()->get("export") === ExportType::WEB) {
                return h_view("public.invoice-checkout-pdf", compact("invoice"));
            } else
                if (request()->get("export") === ExportType::PDF) {
                    try {
                        $pdf = PDF::loadView("public.invoice-checkout-pdf", compact("invoice"));
                        return $pdf->download("invoice{$invoice->id}.pdf");
                    } catch (Throwable $exception) {
                        SystemMessageService::addErrorMessage("system_messages.invoice.pdf_export_failed");
                    }
                }
        }

        return h_view("public.invoice-checkout", [
            "invoice" => $invoice
        ]);
    }

    /**
     * @rules(payment_driver="required|in:".\App\Utils\PaymentManager\Provider::getEnabledDrivers(false, true))
     */
    public function payOnline(Invoice $invoice): RedirectResponse {
        if ($invoice->customer_user_id != get_customer_user()->id or (!$invoice->is_active))
            return redirect()->back();
        if ($invoice->payment_type == PaymentType::ONLINE) {
            if ($invoice->payment_status == PaymentStatus::SUBMITTED or
                $invoice->payment_status == PaymentStatus::CONFIRMED) {
                SystemMessageService::addWarningMessage("system_messages.invoice.is_payed");
                return redirect()->route("customer.invoice.show-checkout", $invoice);
            }
        } else if ($invoice->payment_type == PaymentType::CASH) {
            if ($invoice->shipment_status >= ShipmentStatus::PREPARING_TO_SEND) {
                SystemMessageService::addWarningMessage("system_messages.invoice.must_pay_cash");
                return redirect()->route("customer.invoice.show-checkout", $invoice);
            }
        }
        try {
            Provider::initiatePayment($invoice, request()->get("payment_driver"));
            return redirect()->to(Provider::getPaymentRedirectionUrl());
        } catch (PaymentConnectionException|PaymentInvalidParametersException $e) {
            Log::error("InvoiceController:payOnline:{$e->getMessage()}");
            SystemMessageService::addErrorMessage("system_messages.invoice.payment_initiation_failed");
        } catch (PaymentInvalidDriverException $e) {
            Log::error("InvoiceController:payOnline:{$e->getMessage()}");
            SystemMessageService::addErrorMessage("system_messages.payment.bad_driver_passed");
        }
        return redirect()->route("customer.invoice.show-checkout", $invoice);
    }

    /**
     * @rules(discount_code="required|exists:discount_cards,code")
     */
    public function checkDiscountCode(): JsonResponse {
        $discount_code = strtoupper(request()->get("discount_code"));
        try {
            $discount_card = DiscountCard::checkCode($discount_code);
        } catch (InvalidDiscountCodeException $e) {
            return response()->json(
                MessageFactory::create(["system_messages.invoice.discount_card_status." . $e->getCode()], 400, [])
                , 400);
        }

        $invoice = $this->new_invoice_service->getTheNew();
        $invoice->discountCard()->associate($discount_card);
        $discountable_amount = $invoice->getDiscountableAmount();
        $discount_value = $discount_card->group->calculate($discountable_amount);

        if ($discount_card->group->is_percentage) {
            $discount_amount = intval($discountable_amount * $discount_value / 100);
        } else {
            $discount_amount = $discount_value;
        }
        $discount_amount /= $this->new_invoice_service->getProductPriceRatio();

        if ($discount_amount === 0) {
            return response()->json(
                MessageFactory::create(["system_messages.invoice.discount_card_status." . DiscountCardStatus::NOT_MATCH],
                    400, []), 400);
        }

        $this->new_invoice_service->setTheNew($invoice);

        return response()->json(
            MessageFactory::create(
                ["system_messages.invoice.discount_card_status.0"],
                200, [
                "discount_amount" => $discount_amount,
                "invoice_sum" => ($invoice->sum - $discount_amount)
            ]), 200);
    }

    /**
     * @param Invoice $invoice
     * @return RedirectResponse
     */
    public function enable(Invoice $invoice) {
        if (get_customer_user()->id == $invoice->customer_user_id) {
            $invoice->updateRows();
            $invoice->customPush();
            if ($invoice->rows()->count() > 0) {
                if ($invoice->createFinManRelation())
                    SystemMessageService::addSuccessMessage("system_messages.invoice.enabled");
                else
                    SystemMessageService::addWarningMessage("system_messages.invoice.not_enabled");
            } else
                SystemMessageService::addWarningMessage("system_messages.invoice.is_empty");
        }

        return redirect()->back();
    }

    public function showSurvey(Invoice $invoice): RedirectResponse {
        $survey_config = SurveyService::getRecord();
        if (!is_string($survey_config->getDefaultSurveyUrl()) or !strlen($survey_config->getDefaultSurveyUrl()) > 0) {
            abort(404);
        }
        $invoice->update([
                "survey_viewed_at" => Carbon::now()
            ]
        );

        $survey_url = $survey_config->getDefaultSurveyUrl();
        if ($survey_config->hasCustomState($invoice->state_id)) {
            $custom_state = $survey_config->getCustomState($invoice->state_id);
            $survey_url = $custom_state->custom_survey_url;
        }

        return redirect()->to($survey_url);
    }
}
