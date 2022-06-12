@extends('admin.form_layout.col_8')

@section('bread_crumb')
    <li><a href="{{route('admin.modal.index')}}">پاپ آپ ها</a></li>
    <li class="active"><a href="{{route('admin.modal.create')}}">اضافه کردن پاپ آپ</a></li>
@endsection

@section('form_title')اضافه کردن پاپ آپ@endsection

@section('form_attributes') action="{{route('admin.modal.store')}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes @endsection

@section('form_body')

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">عنوان</span>
                <input class="form-control input-sm" name="title">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">متن</span>
                <textarea class="form-control input-sm" name="text"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">اندازه</span>
                <select class="form-control input-sm" name="size_class">
                    <option value="modal-sm">کوچک</option>
                    <option value="-" selected>متوسط</option>
                    <option value="modal-lg">بزرگ</option>
                </select>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">تعداد دفعات تکرار</span>
                <input class="form-control input-sm" name="repeat_count" type="number">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <label>تصویر</label>
                (حداقل کیفیت: {{ get_image_min_height('modal') }}*{{ get_image_min_width('modal') }}
                و نسبت: {{ get_image_ratio('modal') }})
                <input class="form-control input-sm" name="image" type="file" multiple="true">
            </div>
        </div>
    </div>
    <hr>
    <!--div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <label>کلید ها</label>
            <a class="btn btn-success" href="" style="border-radius: 50%" data-toggle="modal" data-target="#add-button">
                <i class="fa fa-plus"></i>
            </a>
        </div>

    </div-->



@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection

@section('extra_javascript')

@endsection
