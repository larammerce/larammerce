@extends('admin.form_layout.col_4')

@section('extra_style')
    <style>
        .scroll-wrap {
            max-height: 50vh;
            overflow-y: auto;
        }
    </style>
@endsection

@section('bread_crumb')
    <li><a href="{{route('admin.'.str_to_dashed(get_model_entity_name($related_model)).'.index')}}">{{ trans('structures.classes.'.str_to_dashed(get_model_entity_name($related_model))) }} ها</a></li>
    <li class="active"><a href="{{route('admin.excel.view-import', $related_model)}}">بارگذاری با اکسل</a></li>
@endsection

@section('form_title')بارگذاری با اکسل@endsection

@section('form_attributes') action="{{route('admin.excel.import')}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes @endsection

@section('form_body')

    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <label>انتخاب فایل اکسل</label>
        <input class="form-control input-sm" name="file" type="file"
               accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
        <input type="hidden" name="model_name" value="{{ $related_model }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <strong>لطفا فایل را مطابق نمونه بارگذاری کنید</strong>
        <a class="btn btn-default btn-s" href="{{ route('admin.excel.get-import-sample',[$related_model])  }}" style="margin-right: 1%">دانلود نمونه</a>
    </div>


@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection

@section('outer_content')
    @if (session()->has('import_errors') && count(session('import_errors'))>0 )
    <div id="import-error-modal" class="modal show" role="dialog" style="display: none">
        <div class="modal-dialog modal-dialog-scrollable">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <div class="button-container">
                        <div class="btn btn-exit" data-dismiss="modal" onclick = "$('.modal').removeClass('show').addClass('fade');"></div>
                    </div>
                    <div class="title-container"><h1 class="title">خطا در بارگذاری</h1></div>
                </div>
                <div class="modal-body">
                    <p class="message">بارگذاری به دلیل خطاهای زیر انجام نشد:</p>
                        <div class="scroll-wrap alert alert-danger">
                            <ul>
                                @foreach (session()->get('import_errors') as $import_error)
                                    <li>{{ $import_error }}</li>
                                @endforeach
                            </ul>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal" onclick = "$('.modal').removeClass('show').addClass('fade');">انصراف
                    </button>
                </div>
            </div>

        </div>
    </div>
    @endif
@endsection
