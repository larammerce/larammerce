@foreach($need_lists as $need_list)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row products @if(!$need_list->product->is_active) disabled @endif">
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
            <div class="img-container">
                <img class="img-responsive" src="{{ ImageService::getImage($need_list->product, 'thumb') }}">
            </div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$need_list->product->id}}#</div>
        </div>
        <div class="col-lg-1 col-md-3 col-sm-2 col-xs-3 col">
            <div class="label">کد محصول</div>
            <div>{{$need_list->product->code}}</div>
        </div>
        <div class="col-lg-3 col-md-2 col-sm-2 col-xs-3 col">
            <div class="label">عنوان</div>
            <div>{{$need_list->product->title}}</div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 col">
            <div class="label">تاریخ درخواست</div>
            <div>{{TimeService::getDateTimeFrom($need_list->created_at)}}</div>
        </div>
        <div class="col-lg-1 col-md-3 col-sm-5 col-xs-6 col">
            <div class="label">آخرین قیمت</div>
            <div>{{\App\Utils\Common\Format::number($need_list->product->latest_price)}}</div>
        </div>
        <div class="col-lg-3 col-md-9 col-sm-7 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{route('admin.product.edit', $need_list->product)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-success" style="width: auto"
                   href="{{route('admin.need-list.show-product', $need_list->product)}}">
                    <i class="fa fa-eye"></i> مشاهده کاربران
                </a>
            </div>
        </div>
    </div>
@endforeach
