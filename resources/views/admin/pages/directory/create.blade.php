@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.directory.index')}}">پوشه ها</a></li>
    <li class="active"><a href="{{route('admin.directory.index')}}">اضافه کردن پوشه</a></li>

@endsection

@section('form_title')اضافه کردن پوشه@endsection

@section('form_attributes') action="{{route('admin.directory.store')}}" method="POST" enctype="multipart/form-data" @endsection

@section('form_body')

    @if($directory != null)
        <input type="hidden" name="directory_id" value="{{ $directory->id }}">
    @endif
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">عنوان</span>
            <input class="form-control input-sm" name="title" value="{{ old('title') }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">بخش url</span>
            <input class="form-control input-sm" name="url_part" value="{{ old('url_part') }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">اولویت</span>
            <input class="form-control input-sm" name="priority" type="number" value="{{ old('priority') | 0 }}">
        </div>
        @if($directory != null)
            <input type="hidden" name="content_type" value="{{ $directory->content_type }}">
            <input type="hidden" name="data_type" value="{{ $directory->data_type }}">
        @endif
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نوع محتوا</span>
            <select class="form-control input-sm" name="content_type" act="select-control"
                    @if($directory != null) disabled @endif>
                @foreach(\App\Models\Directory::getContentTypes() as $type => $content)
                    <option value="{{ $content }}"
                            @if(old('content_type') == $content or
                                ($directory != null and $directory->content_type == $content)) selected @endif
                            @if($type == 'blog') data-target-container=".article-type" @endif>
                        @lang('general.directory.type.'.$content)
                    </option>
                @endforeach
            </select>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12 article-type">
            <span class="label">نوع زیرشاخه ها</span>
            <select class="form-control input-sm" name="data_type" @if($directory != null) disabled @endif>
                <option value="0">
                    معمولی
                </option>
                <optgroup label="محصول">
                    @foreach(get_product_types() as $type => $content)
                        <option value="{{ $type }}" @if(old('data_type') == $type or
                                ($directory != null and $directory->data_type == $type)) selected @endif>
                            {{ $content['trans'] }}
                        </option>
                    @endforeach
                </optgroup>
                <optgroup label="وبلاگ">
                    @foreach(get_article_types() as $type => $content)
                        <option value="{{ $type }}" @if(old('data_type') == $type or
                            ($directory != null and $directory->data_type == $type)) selected @endif>
                            {{ $content['trans'] }}
                        </option>
                    @endforeach
                </optgroup>
            </select>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="material-switch pull-right">لینک داخلی
                <input id="is_internal_link" name="is_internal_link" type="checkbox" value="1"
                       @if(old('is_internal_link')) checked @endif/>
                <label for="is_internal_link"></label>
            </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="material-switch pull-right">دارای صفحه وب
                <input id="has_web_page" name="has_web_page" type="checkbox" value="1"
                       @if(old('has_web_page')) checked @endif/>
                <label for="has_web_page"></label>
            </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="material-switch pull-right">قابل دسترس بدون ورود به حساب
                <input id="is_anonymously_accessible" name="is_anonymously_accessible" type="checkbox" value="1"
                       checked/>
                <label for="is_anonymously_accessible"></label>
            </span>
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
