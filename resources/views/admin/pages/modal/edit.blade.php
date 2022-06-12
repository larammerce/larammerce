@extends('admin.form_layout.col_8')

@section('bread_crumb')
    <li><a href="{{route('admin.modal.index')}}">پاپ آپ ها</a></li>
    <li class="active"><a href="{{route('admin.modal.create')}}">ویرایش پاپ آپ</a></li>
@endsection

@section('form_title')ویرایش پاپ آپ@endsection

@section('form_attributes') action="{{route('admin.modal.update', $modal)}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <script>window.PAGE_ID = "admin.pages.modal"</script>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">عنوان</span>
                <input class="form-control input-sm" name="title" value="{{ $modal->title }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">متن</span>
                <textarea class="form-control input-sm" name="text">{{ $modal->text }}</textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">اندازه</span>
                <select class="form-control input-sm" name="size_class">
                    @if($modal->size_class === 'modal-sm')
                        <option value="modal-sm" selected>کوچک</option>
                        <option value="-">متوسط</option>
                        <option value="modal-lg">بزرگ</option>
                    @elseif($modal->size_class === 'modal-lg')
                        <option value="modal-sm">کوچک</option>
                        <option value="-">متوسط</option>
                        <option value="modal-lg" selected>بزرگ</option>
                    @else
                        <option value="modal-sm">کوچک</option>
                        <option value="-" selected>متوسط</option>
                        <option value="modal-lg">بزرگ</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">تعداد دفعات تکرار</span>
                <input class="form-control input-sm" name="repeat_count" type="number"
                       value="{{ $modal->repeat_count }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <label>تصویر</label>
                @if(!$modal->hasImage())
                    (حداقل کیفیت: {{ get_image_min_height('modal') }}*{{ get_image_min_width('modal') }}
                    و نسبت: {{ get_image_ratio('modal') }})
                    <input class="form-control input-sm" name="image" type="file" multiple="true">
                @else
                    <div class="photo-container">
                        <a href="{{ route('admin.modal.remove-image', $modal)  }}"
                           class="btn btn-sm btn-danger btn-remove">x</a>
                        <img src="{{ $modal->getImagePath() }}" style="height: 200px;">
                    </div>
                @endif
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <label>کلید ها</label>
        </div>
    </div>
    <br>

    <div id="buttons-row-container" data-rows="{{$modal->buttons}}"></div>



    <hr>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <label>مسیرهای فعال</label>
            <a class="btn btn-success" href="" style="border-radius: 50%" data-toggle="modal"
               data-target="#add-route-modal">
                <i class="fa fa-plus"></i>
            </a>
        </div>
    </div>
    <br>

    <table class="table" id="routes-table">
        <thead>
        <tr>
            <th scope="col" style="text-align: right;border:none">مسیرها</th>
            <th scope="col" style="text-align: right;border:none">مسیرهای ادامه حساب شود</th>
            <th scope="col" style="text-align: right;border:none">خود مسیر حساب شود</th>
            <th scope="col" style="text-align: right;border:none">عملیات</th>
        </tr>
        </thead>
        <tbody>
        @if($modal->routes != null)
            @foreach($modal->routes as $route)
                <tr>
                    <td style="text-align: right;border:none">{{ $route->route }}</td>
                    @if($route->children_included)
                        <td style="text-align: right;border:none">بله</td>
                    @else
                        <td style="text-align: right;border:none">خیر</td>
                    @endif
                    @if($route->self_included)
                        <td style="text-align: right;border:none">بله</td>
                    @else
                        <td style="text-align: right;border:none">خیر</td>
                    @endif
                    <td style="text-align: right;border:none">
                        <a class="btn btn-sm btn-primary edit-route"
                           type="button"
                           data-route="{{ json_encode($route) }}"
                           href="javascript:void(0);">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a class="btn btn-sm btn-danger virt-form"
                           data-action="{{ route('admin.modal-route.destroy', $route) }}"
                           data-method="POST" confirm>
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection

@section('outer_content')
    @include('admin.pages.modal.add_route_modal')
    @include('admin.pages.modal.edit_route_modal')
@endsection
