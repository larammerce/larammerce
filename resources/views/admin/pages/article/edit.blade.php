@extends('admin.form_layout.col_12')

@section('bread_crumb')
    <li><a href="{{route('admin.article.index')}}">وبلاگ ها</a></li>
    <li class="active"><a href="{{route('admin.article.edit', $article)}}">ویرایش وبلاگ</a></li>

@endsection

@section('form_title')ویرایش وبلاگ@endsection

@section('form_attributes') action="{{route('admin.article.update', $article)}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes  @endsection

@section('form_body')
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        {{ method_field('PUT') }}
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نام پوشه مربوطه</span>
            <input class="form-control input-sm" name="directory" value="{{ $article->directory->title }}" disabled>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نوع محتوا</span>
            <input class="form-control input-sm" name="content" disabled
                   value="{{ get_article_type($article->directory->article_type)['trans'] ?? "" }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($article, 'title')>
            <span class="label">عنوان وبلاگ</span>
            <input class="form-control input-sm" name="title" value="{{ $article->title }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($article, 'source')>
            <span class="label">منبع</span>
            <input class="form-control input-sm" name="source" value="{{ $article->source }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="material-switch pull-right">  مطلب پیشنهادی&nbsp;
                <input id="is_suggested" name="is_suggested" type="checkbox" value="1"
                       @if($article->is_suggested) checked @endif/>
                <label for="is_suggested"></label>
                <input id="is_suggested_hidden" name="is_suggested" type="hidden" value="0"/>
            </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($article, 'main_image')>
            <label>تصویر</label>
            @if(!$article->hasImage())
                (حداقل کیفیت: {{ get_image_min_height('blog') }}*{{ get_image_min_width('blog') }}
                و نسبت: {{ get_image_ratio('blog') }})
                <input class="form-control input-sm" name="image" type="file" multiple="true">
            @else
                <div class="photo-container">
                    <a href="{{ route('admin.article.remove-image', $article)  }}"
                       class="btn btn-sm btn-danger btn-remove">x</a>
                    <img src="{{ $article->getImagePath() }}" style="height: 200px;">
                </div>
            @endif
        </div>
        <div class="filled tag-manager input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
            @roleinput($article, 'tags')>
            <span class="label">تگ ها</span>
            <textarea act="tag" class="form-control input-sm attachable" name="tags"
                      data-save="{{ route('admin.tag.store') }}"
                      data-query="{{ route('admin.tag.query') }}"
                      data-attach="{{ route('admin.article.attach-tag', $article) }}"
                      data-detach="{{ route('admin.article.detach-tag', $article) }}"
                      data-field-name="name"
                      data-open-tag="{{ route('admin.tag.edit', -1) }}"
                      data-container=".form-layout-container"></textarea>
            <ul act="tag-data">
                @foreach($article->tags as $tag)
                    <li data-id="{{$tag->id}}" data-text="{{$tag->name}}"></li>
                @endforeach
            </ul>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($article, 'seo_keywords')>
            <span class="label">تگ‌های سئو</span>
            <input class="form-control input-sm" name="seo_keywords" value="{{ $article->seo_keywords }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($article, 'seo_title')>
            <span class="label">عنوان سئو</span>
             <input class="form-control input-sm" name="seo_title" value="{{ $article->seo_title }}">
          </div>
        <div
            class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($article, 'seo_description')>
            <span class="label">توضیحات سئو</span>
            <textarea class="form-control input-sm" name="seo_description">{{ $article->seo_description }}</textarea>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($article, 'short_content')>
            <span class="label">متن کوتاه</span>
            <textarea class="form-control input-sm" name="short_content"
                      rows="3">{{ $article->short_content }}</textarea>
        </div>
        <div class="text-editor col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($article, 'full_text')>
            <textarea class="tinymce"
                      name="full_text">@if($errors->count() > 0){{ old('full_text') }}@else{{ $article->full_text }}@endif</textarea>
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection

@section('outer_content')
    @include('admin.templates.modals.add_caption')
@stop
