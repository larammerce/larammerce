@extends('admin.form_layout.col_6')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.cart-notification.edit')}}">ویرایش تنظیمات اطلاع رسانی سبد</a>
    </li>
@endsection

@section('form_title')ویرایش تنظیمات اطلاع رسانی سبد@endsection

@section('form_attributes') action="{{route('admin.setting.cart-notification.update')}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.cart-notification.edit";</script>
    {{ method_field('PUT') }}
    <h4>تنظیمات پیش‌فرض</h4>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">ساعت تاخیر ارسال اطلاع رسانی سبد (۰ تا ۲۴ ساعت)</span>
                <input class="form-control input-sm" name="default_delay_hours"
                       value="{{old("default_delay_hours") ?: $cart_notification->getDefaultDelayHours()}}">
            </div>
        </div>
    </div>

    <div class="input-group group-sm col-lg-10 col-sm-12 col-md-12 col-xs-12"
         style="margin-bottom: 20px; margin-right: 20px;">
            <span class="material-switch pull-right">اطلاع رسانی با ایمیل انجام شود؟ &nbsp
                <input id="notify_with_email" name="notify_with_email"
                       type="checkbox" value="1"
                       @if($cart_notification->getNotifyWithEmail()) checked @endif/>
                <label for="notify_with_email"></label>
                <input id="notify_with_email_hidden" name="notify_with_email"
                       type="hidden" value="0"/>
            </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         style="margin-bottom: 20px; margin-right: 20px;">
            <span class="material-switch pull-right">اطلاع رسانی با پیامک انجام شود؟ &nbsp
                <input id="notify_with_sms" name="notify_with_sms"
                       type="checkbox" value="1"
                       @if($cart_notification->getNotifyWithSMS()) checked @endif/>
                <label for="notify_with_sms"></label>
                <input id="notify_with_sms_hidden" name="notify_with_sms"
                       type="hidden" value="0"/>
            </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         style="margin-bottom: 10px; margin-right: 30px;">
        <span class="material-switch pull-right">تنظیمات فعال شوند ؟ &nbsp
                    <input id="is_active" name="is_active" type="checkbox" value="1"
                           @if($cart_notification->getIsActive()) checked @endif/>
                    <label for="is_active"></label>
                    <input id="is_active_hidden" name="is_active" type="hidden" value="0"/>
                </span>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
