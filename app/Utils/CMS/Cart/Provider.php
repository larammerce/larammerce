<?php


namespace App\Utils\CMS\Cart;

use App\Models\CustomerUser;
use App\Services\CRMService\CRMService;
use Exception;

class Provider {
    /**
     * @throws CartAttachInvalidProductException
     * @throws CartAttachDuplicateProductException
     * @throws CartAttachCountLimitExceedException
     */
    public static function attachProduct($product, $customer, $count = 1) {
        $customer->update(["is_cart_checked" => false]);
        $rows = $customer->cartRows();
        $limit = (int)env("SITE_LOCAL_CART_COUNT_LIMIT", 60);

        if ($rows->count() <= $limit) {
            if ($product->is_active or config("cms.general.site.show_deactivated_products")) {
                if ($rows->where('product_id', '=', $product->id)->count() === 0) {
                    try {

                        /** @var CRMService $crm_service */
                        $crm_service = app(CRMService::class);
                        $crm_service->customerCartUpdated($customer);
                        return $customer->cartRows()->create(["product_id" => $product->id, "count" => $count]);
                    } catch (Exception $e) {
                        return false;
                    }

                } else {
                    throw new CartAttachDuplicateProductException("The product with id `{$product->id} " .
                        "exists in customer cart.`");
                }
            } else {
                $rows->where('product_id', '=', $product->id)->delete();
                throw new CartAttachInvalidProductException("The product with id `{$product->id}` is not active.");
            }
        } else {
            throw new CartAttachCountLimitExceedException("Can't attach product with id `{$product->id}`, local cart count limit exceeded.");
        }
    }

    public static function detachProduct($product, $customer): bool {
        /** @var CRMService $crm_service */
        $crm_service = app(CRMService::class);
        $crm_service->customerCartUpdated($customer);
        $customer->update(["is_cart_checked" => false]);
        $productId = gettype($product) == "integer" ? $product :
            (gettype($product) == "string" ? intval($product) : $product->id);
        $cartRow = $customer->cartRows()->where('product_id', '=', $productId)->first();
        if ($cartRow != null) {
            try {
                $cartRow->delete();
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }

    public static function removeDeactivated(CustomerUser $customer_user): bool {
        try {
            foreach ($customer_user->cartRows as $cart_row) {
                if (!$cart_row->product->is_active) {
                    $cart_row->delete();
                }
            }
            /** @var CRMService $crm_service */
            $crm_service = app(CRMService::class);
            $crm_service->customerCartUpdated($customer_user);
            static::cleanCookie();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateRowCount($product, $count, $customer): bool {
        $customer->update(["is_cart_checked" => false]);
        $row = $customer->cartRows()->where("product_id", "=", $product->id)->first();
        /** @var CRMService $crm_service */
        $crm_service = app(CRMService::class);
        $crm_service->customerCartUpdated($customer);
        if ($row != null) {
            try {
                $row->update([
                    "count" => $count
                ]);
            } catch (Exception $e) {
                return false;
            }
        }
        return true;

    }

    public static function cleanCookie() {
        $cart_cookie = env("SITE_LOCAL_CART_COOKIE_NAME");
        if (key_exists($cart_cookie, $_COOKIE)) {
            setcookie($cart_cookie, '{}', time() + (86400 * 30), '/');
        }
    }

    public static function flush($guard = null) {
        static::cleanCookie();
        get_customer_user($guard)->cartRows()->delete();
    }
}
