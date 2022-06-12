@foreach($cart_owners as $cart_owner)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row roles @if(!$cart_owner->is_cart_checked) disabled @endif">
        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$cart_owner->id}}#</div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-3 col">
            <div class="label">نام</div>
            <div>{{$cart_owner->user->full_name}}</div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-3 col">
            <div class="label">تاریخ آخرین تغییرات</div>
            <div><span class="label label-primary"
                       style="padding: 6px;">{{TimeService::getDateFrom($cart_owner->updated_At)}}</span>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 col">
            <div class="label">وضعیت</div>
            @if($cart_owner->is_cart_checked)
                <div>خوانده شده</div>
            @else
                <div>خوانده نشده</div>
            @endif
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-success" href="{{route('admin.cart.show', $cart_owner)}}">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
