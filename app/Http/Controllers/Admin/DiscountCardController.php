<?php
/**
 */

namespace App\Http\Controllers\Admin;


use App\Enums\Directory\DirectoryType;
use App\Models\Directory;
use App\Models\DiscountCard;
use App\Models\DiscountGroup;
use App\Utils\Common\History;
use App\Utils\Common\SMSService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class DiscountCardController extends BaseController
{
    public static function storeRules(?int $discount_group_id): array
    {
        if ($discount_group_id === null)
            return [];
        $discount_group = DiscountGroup::find($discount_group_id);
        if ($discount_group->is_assigned) {
            $rules = [
                'users.*.id' => 'required|exists:users,id',
                'users.*.customer_user.id' => 'required|exists:customer_users,id'
            ];
        } else if ($discount_group->is_event) {
            $rules = [];
        } else {
            $rules = [
                'count' => 'required|numeric'
            ];
        }
        return $rules;
    }

    /**
     * @role(super_user, acc_manager)
     * @rules(discount_group_id="required|exists:discount_groups,id")
     */
    public function index(): Factory|View|Application
    {
        $discount_group = DiscountGroup::find(request()->get('discount_group_id'));
        parent::setPageAttribute($discount_group->id);
        $discount_cards = $discount_group->cards()->with('invoice', 'customer', 'directories:id,title')->paginate(
            DiscountCard::getPaginationCount());
        return view('admin.pages.discount-card.index', compact('discount_cards', 'discount_group'));
    }

    /**
     * @role(super_user, acc_manager)
     * @rules(discount_group_id="required|exists:discount_groups,id")
     */
    public function create(): Factory|View|Application
    {
        $directories = Directory::from(DirectoryType::PRODUCT)->get();
        $discount_group = DiscountGroup::find(request()->get('discount_group_id'));
        return view('admin.pages.discount-card.create', compact('discount_group', 'directories'));
    }

    /**
     * @role(super_user, acc_manager)
     * @rules(discount_group_id="required|exists:discount_groups,id",
     *     dynamic_rulee=\App\Http\Controllers\Admin\DiscountCardController::storeRules(request()->discount_group_id))
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $discount_group = DiscountGroup::find(request()->get('discount_group_id'));
        if ($discount_group->is_assigned and !$discount_group->is_event) {
            $users = json_decode($request->get('users'));
            if ($users != null) {
                foreach ($users as $user) {
                    $result = DiscountCard::create([
                        'prefix' => $discount_group->prefix,
                        'postfix' => $discount_group->postfix,
                        'discount_group_id' => $discount_group->id,
                        'code' => $request->has('code') ? $request->code : null,
                        'customer_user_id' => $user->customer_user->id,
                    ]);
                    if(!is_null($result)){
                        $this->setDirectory($discount_group, $result, $request);
                    }
                }
            }
        } else {
            $count = $request->has('count') ? intval($request->get('count')) : 1;
            global $created_cards;
            $created_cards = 0;
            while ($created_cards < $count) {
                $result = DiscountCard::create([
                    'prefix' => $discount_group->prefix,
                    'postfix' => $discount_group->postfix,
                    'code' => $request->has('code') ? $request->code : null,
                    'discount_group_id' => $discount_group->id
                ]);
                if(!is_null($result)){
                    $this->setDirectory($discount_group, $result, $request);
                }
            }
        }
        return redirect()->route('admin.discount-group.show', $discount_group);

    }

    public function setDirectory(DiscountGroup $discount_group, DiscountCard $result, Request $request): void
    {
        if ($discount_group->has_directory)
            {
                $result->directories()->sync($request->get("directories"));
                global $created_cards;
                $created_cards++;
            }
    }

    /**
     * @role(super_user)
     */
    public function destroy(DiscountCard $discount_card): RedirectResponse
    {
        $discount_card->is_active = !($discount_card->is_active);
        $discount_card->save();
        return History::redirectBack();
    }

    /**
     * @role(super_user, acc_manager)
     */
    public function notify(DiscountCard $discount_card): RedirectResponse
    {
        if ($discount_card->group->is_assigned and !$discount_card->is_notified) {
            try {
                if ($discount_card->group->is_percentage)
                    $template = "sms-discount-percent";
                else
                    $template = "sms-discount-toman";
                SMSService::send($template, $discount_card->customer->main_phone,
                    [
                        "discountCardGroupValue" => $discount_card->group->value,
                        "discountCardCode" => $discount_card->code,
                    ],
                    [
                        "customerName" => $discount_card->customer->user->name,
                    ]);
                $discount_card->is_notified = true;
                $discount_card->save();
            } catch (Throwable $e) {
                Log::error("discount_card.notify_customer.{$discount_card->customer->id} : " . $e->getMessage());
            }
        }
        return History::redirectBack();
    }

    public function getModel(): ?string
    {
        return DiscountCard::class;
    }
}
