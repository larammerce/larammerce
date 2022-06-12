<ul id="attr_key_0" class="collapse in searchable-list" aria-expanded="true" style="padding: 0" data-searchable-id="0"
    data-searchable-title="لیست کلید ها">
    @foreach($attribute_keys as $attribute_key)
        <li style="margin-bottom: 8px; margin-top: 8px" data-searchable-title="{{$attribute_key->title}}">
            <button type="button" class="btn btn-dark" data-toggle="collapse"
                    data-target="#attr_key_{{$attribute_key->id}}">
                {{$attribute_key->title}}
            </button>
            <ul id="attr_key_{{$attribute_key->id}}" class="collapse searchable-list"
                data-searchable-id="{{$attribute_key->id}}" data-searchable-title="{{$attribute_key->title}}">
                @foreach($attribute_key->values()->orderBy("name", "ASC")->get() as $attribute_value)
                    <li style="margin-bottom: 8px; margin-top: 8px" data-searchable-title="{{$attribute_value->name}}">
                        <input type="checkbox" name="data[ps_values][{{$attribute_key->id}}][]"
                               value="{{$attribute_value->id}}"
                               class="filter-select"
                               data-filter-select-title="{{$attribute_key->title}}:{{$attribute_value->name}}"
                               id="ps_value_input_{{$attribute_value->id}}"
                               @if(in_array($attribute_value->id, isset(old("data")["ps_values"][$attribute_key->id]) ?
                                   old("data")["ps_values"][$attribute_key->id] : []) or
                                   in_array($attribute_value->id, $product_filter->getPSValueIdsPlain())) checked @endif/>
                        {{$attribute_value->name}}
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
</ul>
