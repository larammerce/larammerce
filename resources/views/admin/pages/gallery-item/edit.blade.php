@extends('admin.form_layout.col_12')

@section('bread_crumb')
    <li><a href="{{route('admin.gallery.show', $gallery_item->gallery)}}">آیتم های گالری</a></li>
    <li class="active"><a href="{{route('admin.gallery-item.edit', $gallery_item)}}">ویرایش آیتم گالری</a></li>

@endsection

@section('form_title')ویرایش آیتم گالری@endsection

@section('form_attributes')
    action="{{route('admin.gallery-item.update', $gallery_item)}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes
@endsection

@section('form_body')
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        {{ method_field('PUT')}}
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">شناساگر گالری</span>
            <input class="form-control input-sm" value="{{ $gallery_item->gallery->identifier }}" disabled>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">سطح اولویت</span>
            <input class="form-control input-sm" name="priority" type="number" value="{{ $gallery_item->priority }}">
            @if(($gallery_item->priority-1) === $neededPriorityToShow)
                <p class="message message-gray" style="display: block;">
                    در حال حاضر اولویت این آیتم در بالاترین حالت قرار دارد.
                </p>
            @else
                <p class="message message-gray" style="display: block;">
                    برای نمایش این آیتم به عنوان اولین آیتم باید مقدار اولویت را روی {{$neededPriorityToShow}} تنظیم
                    کنید.
                </p>
            @endif
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <label>تصویر</label>
            @if(!$gallery_item->hasImage())
                (حداقل کیفیت: {{ get_image_min_height('gallery') }}*{{ get_image_min_width('gallery') }}
                و نسبت: {{ get_image_ratio('gallery') }})
                <input name="image" type="file" multiple="true">
            @else
                <div class="photo-container">
                    <a href="{{ route('admin.gallery-item.remove-image', $gallery_item)  }}"
                       class="btn btn-sm btn-danger btn-remove">x</a>
                    <img src="{{ $gallery_item->getImagePath() }}" style="height: 200px;">
                </div>
            @endif
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="material-switch pull-right">  آیتم نمایش داده شود &nbsp;
                <input id="is_active" name="is_active" type="checkbox" value="1"
                       @if($gallery_item->is_active) checked @endif/>
                <label for="is_active"></label>
                <input id="is_active_hidden" name="is_active" type="hidden" value="0"/>
            </span>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        @foreach($gallery_item->gallery->getGalleryFields() as $key => $field)
            @if(strpos($key, 'description') !== false)
                <textarea class="tinymce"
                          name="data__field__{{ $key }}">@if($errors->count() > 0){{ old('data__field__'.$key) }}@else{{ $gallery_item->getField($key)->getContent() }}@endif</textarea>
            @else
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <span class="label">{{ $field->getTitle() }}</span>
                    <input class="form-control input-sm" name="data__field__{{ $key }}"
                           value="{{ $gallery_item->getField($key)->getContent() }}">
                </div>
            @endif
        @endforeach
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
