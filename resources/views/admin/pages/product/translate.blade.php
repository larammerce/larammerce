@extends("admin.pages.model-translation.edit")

@section("extra_fields")
    @php
        $extra_properties_trans_raw = $translatable_object?->translate($lang_id)?->extra_properties;
        $extra_properties_trans_raw = ($extra_properties_trans_raw == null or strlen($extra_properties_trans_raw) == 0) ? "[]" : $extra_properties_trans_raw;
        $extra_properties_trans = json_decode($extra_properties_trans_raw, true);
    @endphp
    <div class="clearfix"></div>
    @foreach($translatable_object->getExtraProperties() as $index => $extra_property)
        <div class="row">
            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <h5>@lang("structures.attributes.key"):</h5>
                    <p>{{$extra_property->key}}</p>
                    <hr>
                </div>
            </div>
            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <h5>@lang("structures.attributes.value"):</h5>
                    <p>{{$extra_property->value}}</p>
                    <hr>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <span class="label">@lang("structures.attributes.key") (@lang('language.id.'.$lang_id))</span>
                    <input class="form-control input-sm" name="extra_properties[{{$index}}][key]"
                           value="{{($extra_properties_trans[$index] ?? null)["key"]}}">
                </div>
            </div>
            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <span class="label">@lang("structures.attributes.value") (@lang('language.id.'.$lang_id))</span>
                    <input class="form-control input-sm" name="extra_properties[{{$index}}][value]"
                           value="{{($extra_properties_trans[$index] ?? null)["value"]}}">
                </div>
            </div>
            <input type="hidden" name="extra_properties[{{$index}}][type]" value="{{$extra_property->type}}">
            <input type="hidden" name="extra_properties[{{$index}}][priority]" value="{{$extra_property->priority}}">
        </div>
    @endforeach
@endsection
