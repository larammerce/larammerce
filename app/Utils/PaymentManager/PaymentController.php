<?php


namespace App\Utils\PaymentManager;

use App\Enums\Invoice\PaymentStatus;
use App\Enums\Invoice\ShipmentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\EmailService;
use App\Utils\FinancialManager\Factory as FinManFactory;
use App\Utils\PaymentManager\Exceptions\PaymentCallbackInvalidParametersException;
use App\Utils\PaymentManager\Exceptions\PaymentConnectionException;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidDriverException;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidParametersException;
use App\Utils\PaymentManager\Exceptions\PaymentInvoiceProblemException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|Application|RedirectResponse|View
     */
    public function paymentRedirection(Request $request): Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
    {
        $form = $request->session()->pull(Kernel::$sessionKey . ":form-object");
        if ($form !== null and $form !== false)
            return view('public.payment-redirection', ['form' => $form]);
        return redirect()->route('customer.invoice.index');
    }

    public function bankCallback(Request $request, string $driver_name): RedirectResponse
    {
        try {
            $driver = Factory::driver($driver_name);
            $payment_data = json_encode($request->all());
            $payment_id = $driver->getPaymentId($payment_data);
            $payment = Payment::findOrFail($payment_id);

            if ($payment->driver !== $driver->getId())
                return abort(400, "system_messages.payment.bad_driver_passed");

            if ($driver->isCalledBack($payment->payment_data))
                return abort(400, "system_messages.payment.not_available");

            $invoice = $payment->invoice;

            if ($invoice == null)
                return abort(400, "system_messages.payment.invalid_invoice");

            if ($invoice->shipment_status > ShipmentStatus::WAITING_TO_CONFIRM) {
                SystemMessageService::addWarningMessage("system_messages.payment.invoice_not_payable");
                throw new PaymentInvoiceProblemException("The payment in status " .
                    "{$invoice->shipment_status} can not be payed", $invoice->id);
            }

            if (!$driver->isSuccessful($payment->amount, $payment->id, $payment_data)) {
                $payment->payment_data = $payment_data;
                $payment->save();
                SystemMessageService::addWarningMessage('system_messages.payment.failed');
                throw new PaymentInvoiceProblemException("The payment is not successful.",
                    $invoice->id);
            }

            $paymentTrackingCode = $driver->getTrackingCode($payment_data);

            try {
                $payment->payment_data =
                    $driver->verifyPayment($payment->amount, $payment->id, $payment_data);
                $payment->save();

            } catch (PaymentConnectionException $e) {
                $payment->payment_data = $payment_data;
                $payment->save();

                $invoice->payment_status = PaymentStatus::FAILED;
                $invoice->payment_id = $paymentTrackingCode;
                $invoice->save();
                SystemMessageService::addErrorMessage("system_messages.payment.failed");
                throw new PaymentInvoiceProblemException($e->getMessage(), $invoice->id);
            } catch (PaymentInvalidParametersException $e) {
            }

            if ($driver->getStatus($payment->amount, $payment->id, $payment->payment_data) !==
                PaymentStatus::CONFIRMED) {
                SystemMessageService::addWarningMessage("system_messages.payment.not_verified");
                throw new PaymentInvoiceProblemException("The payment is not verified",
                    $invoice->id);
            }

            $finResult = FinManFactory::driver()
                ->submitWarehousePermission($invoice->fin_relation);

            if ($finResult !== false) {
                $invoice->fin_relation = $finResult;
                $invoice->payment_id = $paymentTrackingCode;
                $invoice->payment_status = PaymentStatus::SUBMITTED;
                $invoice->shipment_status = ShipmentStatus::PREPARING_TO_SEND;
                $invoice->save();

                //finalize payment
                $payment->payment_data =
                    $driver->finalizePayment($payment->amount, $payment->id, $payment->payment_data);
                $payment->save();

                SystemMessageService::addSuccessMessage("system_messages.payment.successful");

                if (config('mail-notifications.invoices.new_invoice_payment')) {
                    $subject = 'سفارش جدید';
                    $emailAddress = config('mail-notifications.invoices.related_mail');
                    $template = "public.mail-new-invoice-payment-notification";
                    EmailService::send([
                        "data" => Invoice::find($invoice->id),
                    ], $template, $emailAddress, $emailAddress, $subject);
                }
            } else {
                try {
                    $payment->payment_data =
                        $driver->rejectPayment($payment->amount, $payment->id,
                            $payment->payment_data);
                    $payment->save();

                } catch (Exception $e) {
                    $invoice->payment_status = PaymentStatus::FAILED;
                    $invoice->payment_id = $paymentTrackingCode;
                    $invoice->save();

                    SystemMessageService::addErrorMessage("system_messages.payment.reject_failed");
                    throw new PaymentInvoiceProblemException($e->getMessage(), $invoice->id);
                }

                if ($driver->getStatus($payment->amount, $payment->id, $payment->payment_data) !==
                    PaymentStatus::CHARGED_BACK) {
                    SystemMessageService::addWarningMessage("system_messages.payment.reject_failed");
                    throw new PaymentInvoiceProblemException("The payment is not rejected",
                        $invoice->id);
                }

                $invoice->payment_id = $paymentTrackingCode;
                $invoice->payment_status = PaymentStatus::CHARGED_BACK;
                $invoice->save();

                SystemMessageService::addWarningMessage("system_messages.payment.rejected");
            }

            return redirect()->route('customer.invoice.show-checkout', $invoice);
        } catch (PaymentInvoiceProblemException $e) {
            Log::error("payment_controller.callback.invoice_id:{$e->getCode()}." . $e->getMessage());
            return redirect()->route('customer.invoice.show-checkout', $e->getCode());
        } catch (PaymentCallbackInvalidParametersException $e) {
            Log::error("payment_controller.callback.invoice_id:{$e->getCode()}." . $e->getMessage());
            return abort(400, "system_messages.payment.invalid_callback_parameters");
        } catch (ModelNotFoundException $e) {
            Log::error("payment_controller.callback.invoice_id:{$e->getCode()}." . $e->getMessage());
            return abort(400, "system_messages.payment.invalid_payment_id");
        } catch (PaymentInvalidDriverException $e) {
            Log::error("payment_controller.callback.invoice_id:{$e->getCode()}." . $e->getMessage());
            return abort(500, "system_message.payment.bad_driver_passed");
        } catch (PaymentConnectionException $e) {
            Log::error("payment_controller.callback.invoice_id:{$e->getCode()}." . $e->getMessage());
            return abort(400, "system_messages.payment.failed");
        }
    }
}
