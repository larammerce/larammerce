@foreach($discount_cards as $discount_card)
    <?php $directories_title = join(", ", array_map(function ($iter_discount_card) {
        $href = route("admin.directory.edit", $iter_discount_card["id"]);
        $title = $iter_discount_card["title"];
        return "<a href='{$href}'>{$title}</a>";
    }, $discount_card->directories()->get()->toArray())); ?>
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row discount-cards @if(!$discount_card->is_active) disabled @endif">
        <div class="col-lg-1 col-md-3 col-sm-6 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$discount_card->id}}#</div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-3 col">
            <div class="label">کد</div>
            <div>{{$discount_card->code}}</div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-3 col">
            <div class="label">تعداد دفعات استفاده</div>
            <div>{{$discount_card->invoices()->count()}}</div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-3 col">
            @if($discount_card->getIsEventAttribute())
                <div class="label">رویداد</div>
                <div>{{$discount_card->code}}</div>
            @else
                <div class="label">نام کاربر</div>
                <div>@if(isset($discount_card->customer_user_id)) {{$discount_card->customer->user->full_name}} @endif</div>
            @endif
        </div>
        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-3 col">
            <div class="label">تاریخ استفاده</div>
            <div>@if($discount_card->invoices()->count() > 0) {{JDate::forge($discount_card->invoices()->first()->created_at)->format("Y/m/d H:i:s")}} @else
                    - @endif</div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-3 col">
            <div class="label">دسته بندی‌ها</div>
            <div>
                {!! $directories_title  !!}
            </div>
        </div>
        <div class="col-lg-1 col-md-3 col-sm-6 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm @if($discount_card->is_active) btn-danger @else btn-info @endif virt-form"
                   data-action="{{ route('admin.discount-card.destroy', $discount_card) }}"
                   data-method="DELETE" confirm>
                    @if($discount_card->is_active)
                        <i class="fa fa-stop"></i>
                    @else
                        <i class="fa fa-play"></i>
                    @endif
                </a>
                @if($discount_group->is_assigned and !$discount_card->is_notified)
                    <a class="btn btn-sm btn-info virt-form"
                       data-action="{{ route('admin.discount-card.notify', $discount_card) }}"
                       data-method="POST" confirm>
                        <i class="fa fa-mobile-phone"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endforeach
