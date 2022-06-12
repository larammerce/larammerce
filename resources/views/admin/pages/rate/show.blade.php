@extends('admin.form_layout.col_12')
@section('bread_crumb')
    <li><a href="{{route('admin.rate.index')}}">نظرات کاربران</a></li>
@endsection

@section('form_body')
    <div class="inner-container">
        <div class="inner-container has-toolbar has-pagination">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-xs-12">
                            <div class="inner-container" style="overflow: scroll">
                                <div
                                    class="col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12"
                                    dir="rtl" style="margin-bottom: 30px">
                                    <div class="date-show-web-form" style="margin-bottom: 10px"> تاریخ ارسال پیام :
                                        <span class="label label-primary"
                                              style="padding: 6px 15px;">{{TimeService::getDateTimeFrom($rate->created_at)}}</span>
                                    </div>
                                    <strong dir="rtl">امتیاز : </strong>
                                    <span dir="rtl">{{$rate->value}}</span>
                                    <hr>
                                    <strong dir="rtl">پیام : </strong>
                                    <span dir="rtl">{{$rate->comment}}</span>
                                    <hr>
                                    <strong dir="rtl">کاربر : </strong>
                                    <span dir="rtl">{{$rate->customerUser->user->full_name}}</span>
                                    <hr>
                                    <strong dir="rtl">شماره موبایل : </strong>
                                    <span dir="rtl">{{$rate->customerUser->main_phone}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
