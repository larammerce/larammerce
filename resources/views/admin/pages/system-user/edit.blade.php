@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.system-user.index')}}">کاربران سیستمی</a></li>
    <li class="active"><a href="{{route('admin.system-user.edit', $system_user)}}">ویرایش کاربر سیستمی</a></li>

@endsection

@section('form_title')ویرایش کاربر سیستمی@endsection

@section('form_attributes') action="{{route('admin.system-user.update', $system_user)}}" method="POST"
enctype="multipart/form-data" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('PUT')}}
    <div class="input-group filled group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نقش</span>
        <input
            type="text"
            multiple
            class="tags-multi-select attachable"
            value=""
            data-initial-value="{{ json_encode($system_user->roles) }}"
            data-user-option-allowed="false"
            data-url="/admin/system-role"
            data-load-once="true"
            placeholder="نقش مورد نظر خود را انتخاب کنید"
            data-attach="{{route('admin.system-user.attach-role', $system_user)}}"
            data-detach="{{route('admin.system-user.detach-role', $system_user)}}"
        />
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <label>تصویر</label>
        @if(!$system_user->hasImage())
            ( نسبت: {{ get_image_ratio('system_user') }})
            <input class="form-control input-sm" name="image" type="file" multiple="true">
        @else
            <div class="photo-container">
                <a href="{{ route('admin.system-user.remove-image', $system_user )  }}"
                   class="btn btn-sm btn-danger btn-remove">x</a>
                <img src="{{ $system_user->getImagePath() }}" style="width: 200px;">
            </div>
        @endif
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">کاربر ارشد
            <input id="is_super_user" name="is_super_user" type="checkbox" value="1"
                   @if($system_user->is_super_user) checked @endif/>
            <label for="is_super_user"></label>
            <input id="is_super_user_hidden" name="is_super_user" type="hidden" value="0"/>
        </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">مدیر انبار
            <input id="is_stock_manager" name="is_stock_manager" type="checkbox" value="1"
                   @if($system_user->is_stock_manager) checked @endif/>
            <label for="is_stock_manager"></label>
            <input id="is_stock_manager_hidden" name="is_stock_manager" type="hidden" value="0"/>
        </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">مدیر بهینه سازی
            <input id="is_seo_master" name="is_seo_master" type="checkbox" value="1"
                   @if($system_user->is_seo_master) checked @endif/>
            <label for="is_seo_master"></label>
            <input id="is_seo_master_hidden" name="is_seo_master" type="hidden" value="0"/>
        </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">مدیر محتوا
            <input id="is_cms_manager" name="is_cms_manager" type="checkbox" value="1"
                   @if($system_user->is_cms_manager) checked @endif/>
            <label for="is_cms_manager"></label>
            <input id="is_cms_manager_hidden" name="is_cms_manager" type="hidden" value="0"/>
        </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">مدیر حسابرسی
            <input id="is_acc_manager" name="is_acc_manager" type="checkbox" value="1"
                   @if($system_user->is_acc_manager) checked @endif/>
            <label for="is_acc_manager"></label>
            <input id="is_acc_manager_hidden" name="is_acc_manager" type="hidden" value="0"/>
        </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">کارشناس سیستم
            <input id="is_expert" name="is_expert" type="checkbox" value="1"
                   @if($system_user->is_expert) checked @endif/>
            <label for="is_expert"></label>
            <input id="is_expert_hidden" name="is_expert" type="hidden" value="0"/>
        </span>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
