@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.product-watermark.edit')}}">ویرایش تنظیمات واترمارک</a></li>

@endsection

@section('form_title')
    ویرایش تنظیمات واترمارک
@endsection

@section('form_attributes')
    action="{{route('admin.setting.product-watermark.update')}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes
@endsection

@section('form_body')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        {{ method_field('PUT')}}
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">شناسه واترمارک</span>
            <input class="form-control input-sm" value="{{ $watermark_setting->getWatermarkUUID() }}" disabled>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">موقعیت واترمارک</span>
            <select class="form-control input-sm" name="watermark_position">
                @foreach(\App\Enums\Product\ProductWatermarkPosition::values() as $watermark_position)
                    <option
                        value="{{ $watermark_position }}" {{ $watermark_setting->getWatermarkPosition() == $watermark_position ? 'selected' : '' }}>
                        {{ trans("general.product_watermark_position." . $watermark_position) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">اندازه عرض واترمارک (درصد)</span>
            <select class="form-control input-sm" name="watermark_size_percentage">
                @for($i=1; $i <=10; $i += 1)
                    <option value="{{ $i * 10 }}" {{ $watermark_setting->getWatermarkSizePercentage() == $i * 10 ? 'selected' : '' }}>
                        {{ $i * 10 }}
                @endfor
            </select>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <label>تصویر</label>
            @if(!$watermark_setting->hasImage())
                (حداقل کیفیت: {{ get_image_min_height('gallery') }}*{{ get_image_min_width('gallery') }}
                و نسبت: {{ get_image_ratio('gallery') }})
                <input name="watermark_image" type="file" multiple="true">
            @else
                <div class="photo-container">
                    <a href="{{ route('admin.setting.product-watermark.remove-image')  }}"
                       class="btn btn-sm btn-danger btn-remove">x</a>
                    <img src="{{ $watermark_setting->getImagePath() }}" style="height: 200px;">
                </div>
            @endif
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
