<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/23/2017 AD
 * Time: 19:05
 */

namespace App\Http\Controllers\Customer;


use App\Models\Product;
use App\Utils\CMS\Cart\CartAttachCountLimitExceedException;
use App\Utils\CMS\Cart\CartAttachDuplicateProductException;
use App\Utils\CMS\Cart\CartAttachInvalidProductException;
use App\Utils\CMS\Cart\Provider as CartProvider;
use App\Utils\Common\MessageFactory;
use App\Utils\Common\RequestService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CartController extends BaseController
{
    public function show()
    {
        $customer = get_customer_user();

        if ($customer == null) {
            return redirect()->route('customer-auth.show-auth', 'mobile');
        }

        if (request()->session()->has('invoice_not_saved')) {
            request()->session()->forget('invoice_not_saved');

            return h_view('public.cart', [
                'cartRows' => $customer->cartRows()->with('product')->orderBy('id', 'DESC')->get(),
                'invoice_not_saved' => true
            ]);
        }

        return h_view('public.cart', [
            'cartRows' => $customer->cartRows()->with('product')->orderBy('id', 'DESC')->get()
        ]);
    }

    public function showLocal(): Application|Factory|View|RedirectResponse
    {
        if (get_customer_user() != false)
            return redirect()->route('customer.cart.show');

        $cartRows = get_local_cart(true);

        return h_view('public.cart', compact('cartRows'));
    }

    public function attachProduct(Product $product): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        try {
            $customer = get_customer_user('web');
            $cartRows = [];
            $new_row = CartProvider::attachProduct($product, $customer);
            if ($new_row !== false) {
                $cartRows = $customer->cartRows()->orderBy('id', 'DESC')->with("product")->get();
            }
        } catch (CartAttachDuplicateProductException $e) {
            if (RequestService::isRequestAjax()) {
                return response()->json(MessageFactory::create(
                    ['messages.product_cart.product_not_attached.due_to_duplicate'], 500
                ), 500);
            }
            return redirect()->route('customer.cart.show');
        } catch (CartAttachInvalidProductException $e) {
            if (RequestService::isRequestAjax()) {
                return response()->json(MessageFactory::create(
                    ['messages.product_cart.product_not_attached.due_to_invalid'], 500
                ), 500);
            }
            return redirect()->route('customer.cart.show');
        } catch (CartAttachCountLimitExceedException $e) {
            if (RequestService::isRequestAjax()) {
                return response()->json(MessageFactory::create(
                    ['messages.product_cart.product_not_attached.due_to_count_limit'], 500
                ), 500);
            }
            return redirect()->route('customer.cart.show');
        } catch (\Exception $e) {
            Log::error("cart.attach.product -> " . $e->getMessage());
            if (RequestService::isRequestAjax()) {
                return response()->json(MessageFactory::create(
                    ['messages.product_cart.product_not_attached.unknown'], 500
                ), 500);
            }
            return redirect()->route('customer.cart.show');
        }
        if ($product->hasCustomerMetaCategory()) {
            if (RequestService::isRequestAjax()) {
                $cmc_data = [
                    "cmc" => $product->customerMetaCategory,
                    "new_row" => $new_row
                ];
                return response()->json(MessageFactory::create(
                    ['messages.product_cart.product_attached'], 200, compact('cartRows', 'cmc_data')
                ), 200);
            }
            return redirect()->route('customer.meta-item.create', [$new_row, $product->customerMetaCategory]);
        } else {
            if (RequestService::isRequestAjax()) {
                return response()->json(MessageFactory::create(
                    ['messages.product_cart.product_attached'], 200, compact('cartRows')
                ), 200);
            }
            return redirect()->route('customer.cart.show');
        }
    }

    public function detachProduct(Product $product): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        if (CartProvider::detachProduct($product, get_customer_user('web'))) {
            if (RequestService::isRequestAjax()) {
                return response()->json(MessageFactory::create(
                    ['messages.product_cart.product_detached'], 200
                ), 200);
            }
        } else {
            if (RequestService::isRequestAjax()) {
                return response()->json(MessageFactory::create(
                    ['messages.product_cart.product_not_detached'], 500
                ), 500);
            }
        }
        return redirect()->back();
    }

    /**
     * @rules(count="required|numeric|min:1")
     */
    public function updateCount(Product $product): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        if (CartProvider::updateRowCount($product, request("count"), get_customer_user())) {
            if (RequestService::isRequestAjax()) {
                return response()->json(MessageFactory::create(
                    ['messages.product_cart.row_updated'], 200
                ), 200);
            }
        } else {
            if (RequestService::isRequestAjax()) {
                return response()->json(MessageFactory::create(
                    ['messages.product_cart.row_not_updated'], 500
                ), 500);
            }
        }

        return redirect()->route('customer.cart.show');
    }

    public function removeDeactivated(): RedirectResponse
    {
        $customer = get_customer_user('web');
        CartProvider::removeDeactivated($customer);
        return redirect()->back();
    }

    public function removeAll(): RedirectResponse
    {
        CartProvider::flush("web");
        return redirect()->back();
    }

    public function removeLocal(): RedirectResponse
    {
        CartProvider::cleanCookie();
        return redirect()->back();
    }
}
