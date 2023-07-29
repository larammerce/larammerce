@extends('admin.form_layout.col_12')

@section('bread_crumb')
    <li><a href="{{route('admin.cart.index')}}">سبدهای خرید</a></li>
    <li class="active"><a href="{{route('admin.cart.index')}}">لیست سبدها</a></li>
@endsection

@section('form_body')
    <div class="inner-container">
        <div class="inner-container has-toolbar has-pagination">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-xs-12">
                            <div class="alert alert-info">
                                <h5><i class="fa fa-info"></i> نکته :</h5>
                                این صفحه مناسب برای پرینت طراحی شده است برای تست روی دکمه پرینت کلیک کنید
                            </div>
                            <div class="invoice p-3 mb-3" id="invoice">
                                <div class="row" dir="rtl">
                                    <div class="col-lg-12 col-md-12">
                                        <h4 dir="rtl" style="font-size: 16px">
                                            <i class="fa fa-globe"></i> وبسایت فروشگاهی {{env('APP_NAME')}}
                                        </h4>
                                        <hr>
                                    </div>
                                </div>
                                <div dir="rtl" class="row invoice-info">
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 invoice-col" dir="rtl">
                                        <b>از : </b> وبسایت فروشگاهی {{env('APP_NAME')}}<br>
                                        <address>
                                            <strong></strong><br>
                                        </address>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 invoice-col" dir="rtl">
                                        <b>تلفن : </b> {{$customer_user->main_phone}}<br>
                                        <b>ایمیل : </b> {{$customer_user->user->email}}<br>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3 invoice-col" dir="rtl">
                                        <b>تاریخ آخرین تغییرات
                                            : </b> {{TimeService::getDateFrom($customer_user->updated_at)}}<br>
                                    </div>
                                </div>
                                <div dir="rtl" class="row">
                                    <div class="col-lg-12 col-md-12 col-xs-12 table-responsive">
                                        <hr>
                                        <table class="table table-striped" dir="rtl" width="100%">
                                            <thead>
                                            <tr>
                                                <th class="text-center" scope="col">شناسه</th>
                                                <th class="text-center" scope="col">تعداد</th>
                                                <th class="text-center" scope="col">محصول</th>
                                                <th class="text-center" scope="col">قیمت واحد</th>
                                                <th class="text-center" scope="col">قیمت کل</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($customer_user->cartRows as $cart_row)
                                                <tr>
                                                    <td class="text-center">{{$cart_row->product->code}}</td>
                                                    <td class="text-center  price-data">{{$cart_row->count}}</td>
                                                    <td class="text-center">
                                                        {{ $cart_row->product->title }}
                                                    </td>
                                                    <td class="text-center"
                                                    >{{format_price($cart_row->product->latest_price) }}</td>
                                                    <td class="text-center">{{format_price($cart_row->product->latest_price * $cart_row->count)}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@section('form_footer')
    <button class="btn btn-default virt-form" data-action="{{route('admin.cart.set-checked', $customer_user)}}"
            data-method="PUT">
        <i class="fa fa-check"></i>
        بررسی شد
    </button>
@endsection