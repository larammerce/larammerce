@extends('admin.form_layout.col_10')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.survey.edit')}}">ویرایش تنظیمات پرسشنامه</a></li>

@endsection

@section('form_title')ویرایش تنظیمات پرسشنامه@endsection

@section('form_attributes') action="{{route('admin.setting.survey.update')}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.survey.edit";</script>
    {{ method_field('PUT') }}
    <h4>تنظیمات پیش‌فرض</h4>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">ساعت تاخیر ارسال پرسشنامه (۰ تا ۲۴ ساعت)</span>
                <input class="form-control input-sm" name="default_delay_hours"
                       value="{{old("default_delay_hours") ?: $survey->getDefaultDelayHours()}}">
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">روزهای تاخیر ارسال پرسشنامه (۰ تا ۳۰ روز)</span>
                <input class="form-control input-sm" name="default_delay_days"
                       value="{{old("default_delay_days") ?: $survey->getDefaultDelayDays()}}">
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">آدرس وب‌‌پیج فرم پرسشنامه</span>
                <input class="form-control input-sm" name="default_survey_url"
                       value="{{old("default_survey_url") ?: $survey->getDefaultSurveyUrl()}}">
            </div>
        </div>
    </div>
    <hr/>
    <h4>تنظیمات به ازای استان‌ها</h4>
    <div id="custom-config-container" data-rows="{{json_encode($survey->getCustomStates())}}">

    </div>
@endsection


@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
