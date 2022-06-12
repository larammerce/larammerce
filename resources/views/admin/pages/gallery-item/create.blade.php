@extends('admin.form_layout.col_8')

@section('bread_crumb')
    <li><a href="{{route('admin.gallery.show', $gallery)}}">آیتم های گالری</a></li>
    <li class="active"><a href="{{route('admin.gallery-item.create')}}?gallery_id={{$gallery->id}}">اضافه کردن آیتم
            گالری</a></li>

@endsection

@section('form_title')اضافه کردن آیتم گالری@endsection

@section('form_attributes')
    action="{{route('admin.gallery-item.store')}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes
@endsection

@section('form_body')
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <input name="gallery_id" type="hidden" value="{{ $gallery->id }}">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">شناساگر گالری</span>
            <input class="form-control input-sm" value="{{ $gallery->identifier }}" disabled>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <label>تصویر</label>
            (حداقل کیفیت: {{ get_image_min_height('gallery') }}*{{ get_image_min_width('gallery') }}
            و نسبت: {{ get_image_ratio('gallery') }})
            <input name="image" type="file" multiple="true">
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        @foreach($gallery->getGalleryFields() as $key => $field)
            @if(strpos($key, 'description') !== false)
                <textarea class="tinymce" name="data__field__{{ $key }}">{{ old('data__field__'.$key) }}</textarea>
            @else
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <span class="label">{{ $field->getTitle() }}</span>
                    <input class="form-control input-sm" name="data__field__{{ $key }}"
                           value="{{ old('data__field__'.$key) }}">
                </div>
            @endif
        @endforeach
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">  آیتم نمایش داده شود؟ &nbsp;
            <input id="is_active" name="is_active" type="checkbox" value="1"
                   @if(old('is_active') === null or old('is_active')) checked @endif/>
            <label for="is_active"></label>
            <input id="is_active_hidden" name="is_active" type="hidden" value="0"/>
        </span>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
