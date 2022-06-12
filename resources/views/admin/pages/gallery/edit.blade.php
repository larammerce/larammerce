@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.gallery.index')}}">گالری ها</a></li>
    <li class="active"><a href="{{route('admin.gallery.edit', $gallery)}}">ویرایش گالری</a></li>

@endsection

@section('form_title')ویرایش گالری@endsection

@section('form_attributes') action="{{route('admin.gallery.update', $gallery)}}" method="POST" @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <input type="hidden" name="id" value="{{ $gallery->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شناساگر</span>
        <input class="form-control input-sm" name="identifier" value="{{ $gallery->identifier }}">
    </div>
    <div class="filled tag-manager input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">ویژگی ها</span>
        <textarea act="tag" class="form-control input-sm" name="fields"
                  data-field-name="title"
                  data-container=".form-layout-container" disabled></textarea>
        <ul act="tag-data">
            @foreach($gallery->getGalleryFields() as $key => $galleryField)
                <li data-id="1" data-text="{{ $galleryField->getTitle() }}"></li>
            @endforeach
        </ul>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <a class="btn btn-sm btn-primary pull-left" href="{{route('admin.gallery.show', $gallery)}}">
            مشاهده آیتم ها
        </a>
        <a class="btn btn-sm btn-success pull-left"
           href="{{route('admin.gallery-item.create')}}?gallery_id={{$gallery->id}}">
            افزودن آیتم جدید
        </a>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
