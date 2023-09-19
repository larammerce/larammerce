@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.representative.edit')}}">ویرایش تنظیمات معرف</a></li>

@endsection

@section('form_title')
    ویرایش تنظیمات پرسشنامه
@endsection

@section('form_attributes')
    action="{{route('admin.setting.representative.update')}}" method="POST" form-with-hidden-checkboxes
@endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.representative.edit";</script>
    {{ method_field('PUT') }}
    <h4>تنظیمات اولیه</h4>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">فعال شود؟
            <input id="is_enabled" name="is_enabled" type="checkbox" value="1"
                   @if(old("is_enabled", $representative_setting->isEnabled())) checked @endif/>
            <label for="is_enabled"></label>
            <input id="is_enabled_hidden" name="is_enabled" type="hidden" value="0"/>
        </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">فیلد اجباری باشد؟
            <input id="is_forced" name="is_forced" type="checkbox" value="1"
                   @if(old("is_forced", $representative_setting->isForced())) checked @endif/>
            <label for="is_forced"></label>
            <input id="is_forced_hidden" name="is_forced" type="hidden" value="0"/>
        </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">امکان انتخاب مشتریان فعلی به عنوان معرف فعال شود؟
            <input id="is_customer_representative_enabled" name="is_customer_representative_enabled" type="checkbox"
                   value="1"
                   @if(old("is_customer_representative_enabled", $representative_setting->isCustomerRepresentativeEnabled())) checked @endif/>
            <label for="is_customer_representative_enabled"></label>
            <input id="is_customer_representative_enabled_hidden" name="is_customer_representative_enabled"
                   type="hidden" value="0"/>
        </span>
    </div>
    <hr/>
    <h4>آیتم‌های قابل انتخاب</h4>
    <div id="options-container" data-rows="{{json_encode(old("options", $representative_setting->getOptions()))}}">

    </div>
@endsection


@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
