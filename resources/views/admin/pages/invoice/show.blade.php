@extends('admin.form_layout.col_12')
@section('bread_crumb')
    <li><a href="{{route('admin.invoice.index')}}">صورت حساب ها</a></li>
    <li class="active"><a href="{{route('admin.invoice.edit', $invoice)}}">ویرایش صورت حساب</a></li>

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
                                        <b>به:</b> {{$invoice->customer->user->full_name}}<br>
                                        <b>آدرس خریدار : </b>
                                        <address>
                                            @if(isset($invoice->customer_address))
                                                <p>
                                                    {{$invoice->customer_address}}
                                                </p>
                                            @endif
                                        </address>
                                        <b>تلفن : </b> {{$invoice->customer->user->username}}<br>
                                        <b>ایمیل : </b> {{$invoice->customer->user->email}}<br>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3 invoice-col" dir="rtl">
                                        <b> سفارش : </b> {{'#00'.$invoice->id}} <br>
                                        @if($invoice->hasTrackableShipment())
                                            <b>کد سفارش پستی: </b> {{$invoice->getShipmentTrackingCode()}}<br>
                                        @else
                                            <b>کد سفارش : </b> {{$invoice->tracking_code}}<br>
                                        @endif

                                        <b>تاریخ سفارش : </b> {{TimeService::getDateFrom($invoice->created_at)}}<br>
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
                                                <th class="text-center" scope="col">تخفیف</th>
                                                <th class="text-center" scope="col">قیمت واحد</th>
                                                <th class="text-center" scope="col">قیمت کل</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($invoice->rows as $key=>$product)
                                                <tr>
                                                    <td class="text-center">{{env('APP_NAME')}}{{$product->product->code}}</td>
                                                    <td class="text-center  price-data">{{$product->count}}</td>
                                                    <td class="text-center">
                                                        {{ $product->product->title }}
                                                    </td>
                                                    <td class="text-center price-data">{{$product->discount_amount * $product->count }}</td>
                                                    <td class="text-center"
                                                    >{{\App\Utils\Common\Format::number($product->shownPrice()) }}</td>
                                                    <td class="text-center">{{\App\Utils\Common\Format::number($product->shownPrice() * $product->count)}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                    </div>
                                </div>
                                <div dir="rtl" class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                                        <p class="lead">مشخصات پرداخت :</p>
                                        <div class="table-responsive">
                                            <table class="table">
                                                @foreach($invoice->payments as $payment)
                                                    @if($payment->invoice->payment_status == 1 )
                                                        <tr>
                                                            <th class="text-right" style="width:50%">شماره پرداخت</th>
                                                            <td>{{$payment->id}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right" style="width:50%">درگاه پرداخت</th>
                                                            <td>@lang('payment.drivers.'.$payment->driver)</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right" style="width:50%">شماره رسید</th>
                                                            <td>{{$payment->invoice->tracking_code}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right" style="width:50%">وضعیت</th>
                                                            <td><span class="status-{{$invoice->payment_status}}">
                                                    @lang("invoice.payment_status.".$invoice->payment_status)</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right" style="width:50%">مبلغ کل</th>
                                                            <td class="price-data">{{$payment->amount}} ریال</td>
                                                        </tr>
                                                        @break
                                                    @else
                                                        <tr>
                                                            <th class="text-right" style="width:50%">مبلغ کل</th>
                                                            <td class="price-data">{{$payment->amount}} ریال</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-right" style="width:50%">وضعیت پرداخت</th>
                                                            <td><span class="status-{{$invoice->payment_status}}">
                                                    @lang("invoice.payment_status.".$invoice->payment_status)</span>
                                                            </td>
                                                        </tr>
                                                        @break
                                                    @endif
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-8 col-xs-9">
                                        <p class="lead">اطلاعات خرید :</p>
                                        {!! $invoice->getCMIComment("<br/>")  !!}
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
    <button class="btn btn-default" onclick="printProductList(event,'invoice')"><i class="fa fa-print"></i> پرینت
    </button>
@endsection