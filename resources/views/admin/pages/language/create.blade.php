@php
    $firstKey = array_key_first($available_languages);
@endphp

@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.gallery.index')}}">زبان ها</a></li>
    <li class="active"><a href="{{route('admin.gallery.create')}}">اضافه کردن زبان</a></li>

@endsection

@section('form_title')
    اضافه کردن زبان
@endsection

@section('form_attributes')
    action="{{route('admin.setting.language.item.store')}}" method="POST"
@endsection

@section('form_body')
    <div class="accordion" id="accordion">
        <div class="panel panel-default">
            <div class="panel-body">
                    @foreach($available_languages as $key => $data)
                        <div class="panel panel-default">
                            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                <label for="{{$data['short_name']}}" class="mr-10">{{$data['name']}}</label>
                                <input id="{{$data['short_name']}}" class="mr-5" name="language_id"
                                       type="radio" value="{{$data['short_name']}}"
                                       @if($firstKey === $key) checked @endif/>
                            </div>
                        </div>
                    @endforeach
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <label for="form_checkbox1" class="mr-10">فعالسازی؟</label>
                    <input id="form_checkbox1" class="mr-5" name="is_enabled"
                           type="checkbox" value="1"/>
                    <label class="mr-10" for="form_checkbox2">پیشفرض؟</label>
                    <input id="form_checkbox2" class="mr-5"
                           name="is_default" type="checkbox" value="1"/>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
