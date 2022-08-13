@extends('admin.form_layout.col_6')

@section('bread_crumb')
    <li><a href="{{route("admin.".str_to_dashed($entity_name).".index")}}">
            @lang("structures.classes.{$entity_name}")
        </a></li>
    <li class="active"><a href="{{route('admin.model-translation.edit')}}">
            ویرایش @lang('language.id.'.$lang_id)
        </a>
    </li>
@endsection

@section('form_title')
    ویرایش @lang("structures.classes.".$entity_name)
@endsection

@section('form_attributes')
    action="{{route('admin.model-translation.update')}}" method="POST"
@endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.model-translation.edit";</script>
    <input name="id" type="hidden" value="{{ $translatable_object->id }}">
    <input name="related_model" type="hidden" value="{{ $related_model }}">
    <input name="lang_id" type="hidden" value="{{ $lang_id }}">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        {{ method_field('PUT') }}
        @foreach($translatable_fields as $name => $type)
            @if(str_starts_with($type, "input"))
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <h4>@lang("structures.attributes.".$name):</h4>
                    <p>{{$translatable_object->$name}}</p>
                    <hr>
                </div>
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <span class="label">@lang("structures.attributes.".$name) (@lang('language.id.'.$lang_id))</span>
                    <input class="form-control input-sm" name="{{$name}}"
                           value="@if($translatable_object->translate($lang_id) != null){{ $translatable_object->translate($lang_id)->$name }} @endif"
                           maxlength="62">
                </div>
            @elseif($type == "textarea:normal")
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <h4>@lang("structures.attributes.".$name):</h4>
                    <p>{{$translatable_object->$name}}</p>
                    <hr>
                </div>
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <span class="label">@lang("structures.attributes.".$name) (@lang('language.id.'.$lang_id))</span>
                    <textarea class="form-control input-sm" name="{{$name}}">@if($errors->count() > 0){{ old($name) }}@elseif($translatable_object->translate($lang_id) != null){{ $translatable_object->translate($lang_id)->$name }}@endif</textarea>
                </div>
            @elseif($type == "textarea:rich")
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <h4>@lang("structures.attributes.".$name):</h4>
                    <p>{!! $translatable_object->$name !!}</p>
                    <hr>
                </div>
                <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
                    <div class="text-editor col-lg-12 col-sm-12 col-md-12 col-xs-12">
                        <label for="{{$name}}">@lang("structures.attributes.".$name) (@lang('language.id.'.$lang_id)
                            )</label>
                        <textarea class="tinymce"
                                  name="{{$name}}">@if($errors->count() > 0){!! old($name) !!}@elseif($translatable_object->translate($lang_id) != null){!! $translatable_object->translate($lang_id)->$name !!}@endif</textarea>
                    </div>
                </div>
            @endif
        @endforeach
        @yield("extra_fields")
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
@section('outer_content')
    @include('admin.templates.modals.add_caption')
@stop
