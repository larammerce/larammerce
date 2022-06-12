@extends('admin.form_layout.col_4')

@section('extra_style')
    <!-- fontIconPicker core CSS -->
    <link rel="stylesheet" type="text/css"
          href="/admin_dashboard/vendor/fontIconPicker/dist/css/base/jquery.fonticonpicker.min.css"/>

    <!-- required default theme -->
    <link rel="stylesheet" type="text/css"
          href="/admin_dashboard/vendor/fontIconPicker/dist/css/themes/grey-theme/jquery.fonticonpicker.grey.min.css"/>

    <!-- optional themes -->
    <link rel="stylesheet" type="text/css"
          href="/admin_dashboard/vendor/fontIconPicker/dist/css/themes/dark-grey-theme/jquery.fonticonpicker.darkgrey.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="/admin_dashboard/vendor/fontIconPicker/dist/css/themes/bootstrap-theme/jquery.fonticonpicker.bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="/admin_dashboard/vendor/fontIconPicker/dist/css/themes/inverted-theme/jquery.fonticonpicker.inverted.min.css"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css">

    <link rel="stylesheet" type="text/css" href="/admin_dashboard/vendor/spectrum/spectrum.css">


@endsection

@section('bread_crumb')
    <li><a href="{{route('admin.badge.index')}}">نشان ها</a></li>
    <li class="active"><a href="{{route('admin.badge.create')}}">ویرایش نشان</a></li>
@endsection

@section('form_title')ویرایش نشان@endsection

@section('form_attributes') action="{{route('admin.badge.update', $badge)}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <script>window.PAGE_ID = "admin.pages.badge"</script>

    <div class="row" style="margin-bottom: 20px">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">عنوان</span>
                <input class="form-control input-sm" name="title" value="{{ $badge->title }}">
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <span class="material-switch pull-right">آیا عنوان نمایش داده شود؟ &nbsp
                        <input id="show-title" name="show_title" type="checkbox" value="1"
                               @if($badge->show_title) checked @endif/>
                        <label for="show-title"></label>
                        <input id="show-title_hidden" name="show_title" type="hidden" value="0"/>
                    </span>
            </div>
        </div>

    </div>


    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <label>تصویر</label>
        @if(!$badge->hasImage())
            (حداقل کیفیت: {{ get_image_min_height('badge') }}*{{ get_image_min_width('badge') }}
            و نسبت: {{ get_image_ratio('badge') }})
            <input class="form-control input-sm" name="image" type="file" multiple="true">
        @else
            <div class="photo-container">
                <a href="{{ route('admin.badge.remove-image', $badge)  }}"
                   class="btn btn-sm btn-danger btn-remove">x</a>
                <img src="{{ $badge->getImagePath() }}" style="height: 200px;">
            </div>
        @endif
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <div id="select-icon"
             style="display: inline-block;max-width: 50%;margin-right: 5%;margin-left: 5%;margin-top: 5%;margin-bottom: 5%">
            <label>آیکون</label>
            <input type="text" id="icon-picker" value="{{ $badge->icon }}" name="icon"/>
        </div>

        <div id="select-color"
             style="display: inline-block;max-width: 50%;margin-left: 5%;margin-right: 10%;margin-top: 5%;margin-bottom: 5%">
            <label>رنگ</label>
            <input type='text' id="color-picker" value="{{ $badge->color }}" name="color"/>
        </div>

    </div>

@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
