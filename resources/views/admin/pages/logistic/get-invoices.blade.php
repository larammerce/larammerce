@extends('admin.form_layout.col_12')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.setting.logistic.edit')}}">دریافت فاکتورهای لوجستیک</a></li>

@endsection

@section('form_title')دریافت فاکتورهای لوجستیک@endsection

@section('form_attributes') action="{{route('admin.invoice.index')}}" method="GET" form-with-hidden-checkboxes @endsection

@section('form_body')

    {{ method_field('GET') }}
    <h4>انتخاب بازه های زمانی</h4>
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">از ساعت</span>
                <input class="form-control input-sm" type="time"
                       name="start_hour" value="00:00">
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">تا ساعت</span>
                <input class="form-control input-sm" type="time"
                       name="finish_hour" value="00:00">
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">از روز</span>
                <input class="form-control input-sm" type="date"
                       name="first_date" value="">
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">تا روز</span>
                <input class="form-control input-sm" type="date"
                       name="last_date" value="">
            </div>
        </div>
    </div>



@endsection


@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
