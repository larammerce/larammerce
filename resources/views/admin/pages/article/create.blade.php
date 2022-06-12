@extends('admin.form_layout.col_12')

@section('bread_crumb')
    <li><a href="{{route('admin.article.index')}}">وبلاگ</a></li>
    <li class="active"><a href="{{route('admin.article.create')}}">اضافه کردن وبلاگ</a></li>

@endsection

@section('form_title')اضافه کردن وبلاگ@endsection

@section('form_attributes') action="{{route('admin.article.store')}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes  @endsection

@section('form_body')

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <input type="hidden" name="directory_id" @if(isset($directory)) value="{{ $directory->id }}" @endif>
        <input type="hidden" name="content_type" @if(isset($directory)) value="{{ $directory->data_type }}" @endif>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نام پوشه مربوطه</span>
            <input class="form-control input-sm" name="directory" @if(isset($directory)) value="{{ $directory->title }}"
                   @endif disabled>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نوع محتوا</span>
            <input class="form-control input-sm" name="content" disabled
                   @if(isset($directory)) value="{{ get_article_type($directory->data_type)['trans'] }}" @endif>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">عنوان وبلاگ</span>
            <input class="form-control input-sm" name="title" value="{{ old('title') }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">منبع</span>
            <input class="form-control input-sm" name="source" value="{{ old('source') }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="material-switch pull-right">  مطلب پیشنهادی &nbsp;
                    <input id="is_suggested" name="is_suggested" type="checkbox" value="1"
                           @if(old('is_suggested')) checked @endif/>
                    <label for="is_suggested"></label>
                </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <label>تصویر</label>
            (حداقل کیفیت: {{ get_image_min_height('blog') }}*{{ get_image_min_width('blog') }}
            و نسبت: {{ get_image_ratio('blog') }})
            <input class="form-control input-sm" name="image" type="file" multiple="true">
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">متن کوتاه</span>
            <textarea class="form-control input-sm" name="short_content" rows="3">{{ old('short_content') }}</textarea>
        </div>
        <div class="text-editor col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <textarea class="tinymce" name="full_text">@if($errors->count() > 0){{ old('full_text') }}@else متن
                اصلی @endif</textarea>
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
