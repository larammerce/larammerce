@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.gallery.index')}}">گالری ها</a></li>
    <li class="active"><a href="{{route('admin.gallery.create')}}">اضافه کردن گالری</a></li>

@endsection

@section('form_title')اضافه کردن گالری@endsection

@section('form_attributes') action="{{route('admin.gallery.store')}}" method="POST"  @endsection

@section('form_body')
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">شناساگر</span>
        <input class="form-control input-sm" name="identifier" value="{{old('identifier')}}">
    </div>
    <div class="filled tag-manager input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">ویژگی ها</span>
        <textarea act="tag" class="form-control input-sm" name="fields"
                  data-field-name="title"
                  data-container=".form-layout-container"></textarea>
        <ul act="tag-data">
            @if(old('fields') != null)
                @foreach(json_decode(old('fields')) as $field)
                    <li data-id="1" data-text="{{$field->text}}"></li>
                @endforeach
            @endif
        </ul>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
