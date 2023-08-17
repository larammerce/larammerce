@extends('admin.form_layout.col_12')

@section('bread_crumb')
    <li><a href="{{route('admin.invoice.index')}}">فاکتورها</a></li>
    <li class="active"><a href="{{route('admin.invoice.filter')}}">فیلتر کردن فاکتورها</a></li>
@endsection

@section('form_title')انتخاب نوع فیلتر@endsection

@section('form_attributes') action="{{route('admin.invoice.index')}}?filtered=true" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('GET') }}
    <script>window.PAGE_ID = "admin.pages.invoice.filter";</script>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">شناسه خریدار</span>
            <input class="form-control input-sm" name="customer_user_id" value="">
        </div>
    </div>
    {{-- <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <h4>تنظیمات عمومی</h4>
        <hr/>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">عنوان پلن تخفیف</span>
            <input class="form-control input-sm" name="title" value="{{$discount_group->title}}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">سقف مبلغ تخفیف (تومان)</span>
            <input class="form-control input-sm" name="max_amount_supported"
                   value="{{$discount_group->max_amount_supported}}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">حداقل مبلغ شامل تخفیف (تومان)</span>
            <input class="form-control input-sm" name="min_amount_supported"
                   value="{{$discount_group->min_amount_supported}}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">پلن شامل تاریخ انقضا میشود ؟ &nbsp
            <input id="has_expiration" name="has_expiration" type="checkbox" value="1"
                   @if($discount_group->has_expiration) checked @endif/>
            <label for="has_expiration"></label>
            <input id="has_expiration_hidden" name="has_expiration" type="hidden" value="0"/>
        </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">تاریخ انقضا</span>
            <input class="form-control input-sm" name="expiration_datepicker"
                   value="{{$discount_group->expiration_date}}">
            <input type="hidden" name="expiration_date">
        </div>
        <br/><br/>
        <h4>تنظیمات مختص صدور کد تخفیف</h4>
        <hr/>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">کد پیشین</span>
            <input class="form-control input-sm" name="prefix" value="{{$discount_group->prefix}}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">کد پسین</span>
            <input class="form-control input-sm" name="postfix" value="{{$discount_group->postfix}}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">کد های تخفیف مختص شخص خاصی صادر شود؟ &nbsp
            <input id="is_assigned" name="is_assigned" type="checkbox" value="1"
                   @if($discount_group->is_assigned) checked @endif/>
            <label for="is_assigned"></label>
            <input id="is_assigned_hidden" name="is_assigned" type="hidden" value="0"/>
        </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">کد های تخفیف برای دسته بندی خاصی صادر شود؟ &nbsp
            <input id="has_directory" name="has_directory" type="checkbox" value="1"
                   @if($discount_group->has_directory) checked @endif/>
            <label for="has_directory"></label>
            <input id="has_directory_hidden" name="has_directory" type="hidden" value="0"/>
        </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">کد برای رویداد ایجاد می‌شود ؟ &nbsp
            <input id="is_event" name="is_event" type="checkbox" value="1"
                   @if($discount_group->is_event) checked @endif/>
            <label for="is_event"></label>
            <input id="is_event_hidden" name="is_event" type="hidden" value="0"/>
        </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">کد به صورت چند بار مصرف صادر شود ؟ &nbsp
            <input id="is_multi" name="is_multi" type="checkbox" value="1"
                   @if($discount_group->is_multi) checked @endif/>
            <label for="is_multi"></label>
            <input id="is_multi_hidden" name="is_multi" type="hidden" value="0"/>
        </span>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">آیا مقدار تخفیف به درصد است ؟ &nbsp
            <input id="is_percentage" name="is_percentage" type="checkbox" value="1"
                   @if($discount_group->is_percentage) checked @endif/>
            <label for="is_percentage"></label>
            <input id="is_percentage_hidden" name="is_percentage" type="hidden" value="0"/>
        </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">مقدار تخفیف (درصد یا تومان)</span>
            <input class="form-control input-sm" name="value" value="{{$discount_group->value}}" act="price">
        </div>
        <br/><br/>
        <h4>تنظیمات پلکان تخفیف</h4>
        <hr/>
        <div id="discount-steps-container" data-rows="{{$discount_group->steps_data}}">

        </div>
    </div> --}}
@endsection

@section('form_footer')
    {{-- <button type="submit" class="btn btn-default btn-sm">ذخیره</button> --}}
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="اعمال فیلتر">
@endsection
