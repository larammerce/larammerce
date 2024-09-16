@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.invoice.index')}}">صورت حساب ها</a></li>
    <li class="active"><a href="{{route('admin.invoice.index')}}">لیست صورت حساب ها</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul class="has-divider-left">
                <li class="btn btn-default" href="{{route('admin.customer-user.index')}}" act="link">
                    <i class="fa fa-shopping-cart"></i>خریداران
                </li>
                <li class="btn btn-default" href="{{route('admin.customer-address.index')}}" act="link">
                    <i class="fa fa-location-arrow"></i>آدرس خریداران
                </li>
            </ul>
            <ul class="has-divider-left">
                @foreach(LayoutService::getLayoutMethods() as $layout_method)
                    <li href="{{route('admin.null')}}?layout_model=Invoice&layout_method={{$layout_method["method"]}}"
                        act="link"
                        @if($layout_method["method"] == LayoutService::getRecord("Invoice")->getMethod()) class="active" @endif>
                        <i class="fa {{$layout_method["icon"]}}"></i>
                    </li>
                @endforeach
            </ul>
            <ul>
                @foreach(SortService::getSortableFields('Invoice') as $sortable_field)
                    <li class="btn btn-default {{$sortable_field->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=Invoice&sort_field={{$sortable_field->field}}&sort_method={{$sortable_field->method}}"
                        act="link">
                        @if($sortable_field->is_active)
                            <i class="fa {{$sortable_field->method == SortMethod::ASCENDING ? "fa-long-arrow-up" : "fa-long-arrow-down"}}"></i>
                        @endif
                        {{$sortable_field->title}}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="inner-container has-toolbar has-pagination">


            <div class="view-port">
                @include('admin.pages.invoice.layout.'.LayoutService::getRecord("Invoice")->getMethod())
            </div>
            @if(isset($customerUser))
                <div class="fab-container">
                    <div class="fab green">
                        <button act="link"
                                href="{{route('admin.invoice.create')}}?customer_user_id={{$customerUser->id}}">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            @else
                <div class="fab-container">
                    @include('admin.templates.buttons.fab-buttons', ['buttons' => ['download']])
                </div>
            @endif
        </div>
        @include('admin.templates.pagination', [
            "modelName" => "Invoice",
            "lastPage" => $invoices->lastPage(),
            "total" => $invoices->total(),
            "count" => $invoices->perPage(),
            "parentId" => $scope ?? null
        ])
    </div>
@endsection

@section('side_bar')
    {{-- Invoice Filter Form--}}
    <form action="{{route('admin.invoice.index')}}" method="GET" class="pb-100">
        <div class="form-group">
            <div class="">تاریخ ایجاد فاکتور</div>
            <div>
                <label for="create-date-from">از</label>
                <input id="create-date-from" class="form-control" type="date" value="{{ old('create_date_from',now()) }}" name="create_date_from">
                <label for="create-date-to">تا</label>
                <input id="create-date-to" class="form-control" type="date" value="{{ old('create_date_to',now()) }}" name="create_date_to">
            </div>
        </div>
        <div class="form-group">
            <div class="">تاریخ پرداخت فاکتور</div>
            <div>
                <label for="payment-date-from">از</label>
                <input id="payment-date-from" type="date" class="form-control" value="{{ old('payment_date_from',now()) }}" name="payment_date_from">
                <label for="payment-date-to">تا</label>
                <input id="payment-date-to" type="date" class="form-control" value="{{ old('payment_date_to',now()) }}" name="payment_date_to">
            </div>
        </div>
        <div class="form-group">
            <div class="">بازه مبلغ فاکتور</div>
            <div>
                <label for="price-from">از</label>
                <input id="price-from" class="form-control" type="number" value="{{ old('price_from','') }}" name="price_from">
                <label for="price-to">تا</label>
                <input id="price-to" class="form-control" type="number" value="{{ old('price_to','') }}" name="price_to">
            </div>
        </div>
        <div class="form-group">
            <div class="pb-10">نام و نام خانوادگی</div>
            <div class="pb-20">
                <input id="first-name" class="form-control" type="text" placeholder="نام" value="{{ old('first_name','') }}" name="first_name">
            </div>
            <div>
                <input id="last-name" class="form-control" type="text" placeholder="نام خانوادگی" value="{{ old('last_name','') }}" name="last_name">
            </div>
        </div>
        <div class="form-group">
            <label for="payment-status">وضعیت فاکتور</label>
            <select id="payment-status" name="payment_status" class="form-control"
                    class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option selected type="number" value="">همه</option>
                @foreach(App\Enums\Invoice\PaymentStatus::values() as $value)
                    @switch($value)
                        @case('0')
                            <option value="{{$value}}">در حال انتظار</option>
                            @break
                        @case('1')
                            <option value="{{$value}}">ثبت شده</option>
                            @break
                        @case('2')
                            <option value="{{$value}}">تایید شده</option>
                            @break
                        @case('3')
                            <option value="{{$value}}">پرداخت شده</option>
                            @break
                        @case('4')
                            <option value="{{$value}}">ناموفق</option>
                            @break
                        @case('5')
                            <option value="{{$value}}">لغو شده</option>
                            @break
                        @case('6')
                            <option value="{{$value}}">هزینه برگشت داده شده</option>
                            @break
                    @endswitch
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="tracking-code">کد ملی</label>
            <input id="tracking-code" class="form-control" type="text" value="{{ old('national_code','') }}" name="national_code">
        </div>
        <div class="form-group pb-10">
            <label for="contact-number">شماره تماس</label>
            <input id="contact-number" class="form-control" type="text" value="{{ old('user_number','') }}" name="user_number">
        </div>
        <input type="submit" class="btn btn-warning">
    </form>
    {{-- Invoice Filter Form--}}
@endsection
