<ul id="attr_key_0" class="collapse in searchable-list" aria-expanded="true" style="padding: 0" data-searchable-id="0"
    data-searchable-title="لیست کلید ها">
    @foreach($p_structure_attr_keys as $p_structure_attr_key)
        <li style="margin-bottom: 8px; margin-top: 8px" data-searchable-title="{{$p_structure_attr_key->title}}">
            <button type="button" class="btn btn-dark" data-toggle="collapse"
                    data-target="#attr_key_{{$p_structure_attr_key->id}}">
                {{$p_structure_attr_key->title}}
            </button>
            <ul id="attr_key_{{$p_structure_attr_key->id}}" class="collapse searchable-list"
                data-searchable-id="{{$p_structure_attr_key->id}}" data-searchable-title="{{$p_structure_attr_key->title}}">
                @foreach($p_structure_attr_key->values()->orderBy("name", "ASC")->get() as $attribute_value)
                    <li style="margin-bottom: 8px; margin-top: 8px" data-searchable-title="{{$attribute_value->name}}">
                        <input type="checkbox" name="data[ps_values][{{$p_structure_attr_key->id}}][]"
                               value="{{$attribute_value->id}}"
                               class="filter-select"
                               data-filter-select-title="{{$p_structure_attr_key->title}}:{{$attribute_value->name}}"
                               id="ps_value_input_{{$attribute_value->id}}"
                               @if(in_array($attribute_value->id, isset(old("data")["ps_values"][$p_structure_attr_key->id]) ?
                                   old("data")["ps_values"][$p_structure_attr_key->id] : []) or
                                   in_array($attribute_value->id, $product_filter->getPSValueIdsPlain())) checked @endif/>
                        {{$attribute_value->name}}
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
</ul>
