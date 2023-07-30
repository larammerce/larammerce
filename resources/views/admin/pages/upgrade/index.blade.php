@extends('admin.form_layout.col_6')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.upgrade.index')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.upgrade.index')}}">به روز رسانی پروژه</a></li>

@endsection

@section('form_title')
    تنظیمات به روز رسانی پلتفرم
@endsection

@section('form_attributes')
    action="{{route('admin.setting.upgrade.save-config')}}" method="POST"
    enctype="multipart/form-data" form-with-hidden-checkboxes
@endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.upgrade.index";</script>
    <div class="col-md-9">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">آدرس گیت هسته</span>
            <input class="form-control input-sm" name="larammerce_repo_address" value="{{ $larammerce_repo_address }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">برنچ هسته</span>
            <input class="form-control input-sm" name="larammerce_branch_name" value="{{ $larammerce_branch_name }}">
        </div>
    </div>
    <div class="col-md-9">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">آدرس گیت تم پروژه</span>
            <input class="form-control input-sm" name="larammerce_theme_repo_address"
                   value="{{ $larammerce_theme_repo_address }}">
        </div>
    </div>
    <div class="col-md-3">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">برنچ تم</span>
            <input class="form-control input-sm" name="larammerce_theme_branch_name"
                   value="{{ $larammerce_theme_branch_name }}">
        </div>
    </div>
    @if(isset($public_key))
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">بسیار عالی!</h4>
            <p>تنظیمات با موفقیت ذخیره شدند، حالا باید کلید عمومی زیر را کپی کنید و در ریپازیتوری گیت خود قرار دهید تا
                این سرور دسترسی‌های لازم برای اتصال به سرور گیت را داشته باشد.</p>
            <hr>
            <p class="mb-0">لطفا دقت کنید که حتما این کد را کپی کنید، چون در صورت خروج از این صفحه دیگر به آن دسترسی
                ندارید.</p>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">کلید پابلیک </span>
            <textarea act="tag" class="form-control input-sm" name="fields"
                      data-field-name="title"
                      rows="15" dir="ltr"
                      data-container=".form-layout-container">{{$public_key}}</textarea>
        </div>
    @endif

    <div class="update-container">
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <button id="upgradeThemeButton" class="btn btn-primary btn-sm">به روز رسانی تم</button>
                <button id="upgradeCoreButton" class="btn btn-primary btn-sm">به روز رسانی هسته</button>
                <button id="upgradeAllButton" class="btn btn-primary btn-sm">به روز رسانی کلی</button>
            </div>
        </div>
        <div class="row mt-15" id="updating-note" style="display: none">
            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">در حال به روز رسانی</h4>
                    <hr>
                    <p>لطفا تا پایان عملیات از بستن این صفحه خودداری نمایید.</p>
                </div>
            </div>
        </div>
        <div class="row mt-15">
            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <pre id="output" dir="ltr"></pre>
            </div>
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
    <input type="submit" class="btn btn-danger btn-sm" name="create_key" value="ذخیره و ایجاد کلید دسترسی گیت">
@endsection
