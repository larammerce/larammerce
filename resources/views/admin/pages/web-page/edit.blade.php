@extends('admin.form_layout.col_12')

@section('bread_crumb')
    <li><a href="{{route('admin.web-page.index')}}">صفحات وب</a></li>
    <li class="active"><a href="{{route('admin.web-page.edit', $webPage)}}">ویرایش صفحه وب</a></li>

@endsection

@section('form_title')ویرایش صفحه وب@endsection

@section('form_attributes') action="{{route('admin.web-page.update', $webPage)}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('PUT')}}
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نام پوشه مربوطه</span>
            <input class="form-control input-sm" name="directory" value="{{ $webPage->directory->title }}" disabled>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نام بلید</span>
            <input class="form-control input-sm" value="{{ $webPage->blade_name }}" disabled>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <label>تصویر</label>
            @if(!$webPage->hasImage())
                (حداقل کیفیت: {{ get_image_min_height('web_page') }}*{{ get_image_min_width('web_page') }}
                و نسبت: {{ get_image_ratio('web_page') }})
                <input class="form-control input-sm" name="image" type="file" multiple="true">
            @else
                <div class="photo-container">
                    <a href="{{ route('admin.web-page.remove-image', $webPage)  }}"
                       class="btn btn-sm btn-danger btn-remove">x</a>
                    <img src="{{ $webPage->getImagePath() }}" style="height: 200px;">
                </div>
            @endif
        </div>
        <div class="filled tag-manager input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">تگ ها</span>
            <textarea act="tag" class="form-control input-sm attachable" name="tags"
                      data-save="{{ route('admin.tag.store') }}"
                      data-query="{{ route('admin.tag.query') }}"
                      data-attach="{{ route('admin.web-page.attach-tag', $webPage) }}"
                      data-detach="{{ route('admin.web-page.detach-tag', $webPage) }}"
                      data-field-name="name"
                      data-open-tag="{{ route('admin.tag.edit', -1) }}"
                      data-container=".form-layout-container"></textarea>
            <ul act="tag-data">
                @foreach($webPage->tags as $tag)
                    <li data-id="{{$tag->id}}" data-text="{{$tag->name}}"></li>
                @endforeach
            </ul>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">تگ‌های سئو</span>
            <input class="form-control input-sm" name="seo_keywords" value="{{ $webPage->seo_keywords }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">عنوان سئو</span>
            <input class="form-control input-sm" name="seo_title" value="{{ $webPage->seo_title }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">توضیحات سئو</span>
            <textarea class="form-control input-sm" name="seo_description">{{ $webPage->seo_description }}</textarea>
        </div>
        @foreach(TemplateService::getGalleries($webPage->blade_name, $webPage->directory->id) as $gallery)
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <h5>{{$gallery->title}}</h5>
                <a href="{{route('admin.gallery.edit', $gallery->model)}}" class="btn btn-sm btn-primary">
                    ویرایش گالری
                </a>
            </div>
        @endforeach
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        @include('admin.pages.web-page.dynamic_fields')
    </div>

@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
