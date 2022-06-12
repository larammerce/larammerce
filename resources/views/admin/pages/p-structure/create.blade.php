@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.p-structure.index')}}">قالب محصولات</a></li>
    <li class="active"><a href="{{route('admin.p-structure.index')}}">اضافه کردن قالب محصول</a></li>

@endsection

@section('form_title')اضافه کردن قالب محصول@endsection

@section('form_attributes') action="{{route('admin.p-structure.store')}}" method="POST"  @endsection

@section('form_body')
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">عنوان قالب</span>
        <input class="form-control input-sm" name="title" value="{{old('title')}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام بلید</span>
        <?php $selected = old("blade_name") ?? "product-single"; ?>
        <select class="form-control" name="blade_name">
            @foreach(get_template_views() as $view)
                <option value="{{$view}}" @if($selected === $view) selected @endif>{{$view}}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
