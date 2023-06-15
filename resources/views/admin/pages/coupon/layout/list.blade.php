@foreach($coupons as $coupon)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row roles">
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 col">
            <div class="img-container" style="background-coupon: {{$coupon->hex_code}}"></div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-2 col-xs-6 col">
            <div class="label">شناسه</div>
            <div>{{$coupon->id}}#</div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 col">
            <div class="label">عنوان</div>
            <div>{{$coupon->title}}</div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 col">
            <div class="label">مبلغ</div>
            <div>{{$coupon->amount}}</div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-6 col">
            <div class="label">تاریخ استفاده</div>
            <div>{{jDate::forge($coupon->used_at)->format("%Y/%m/%d %H:%i")}}</div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-6 col">
            <div class="label">تاریخ اتقضا</div>
            <div>{{jDate::forge($coupon->expire_at)->format("%Y/%m/%d %H:%i")}}</div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{route('admin.coupon.edit', $coupon)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.coupon.destroy', $coupon) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
                <a class="btn btn-sm btn-success" href="{{route('admin.coupon.show', $coupon)}}">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
