<script>window.HAS_CHECKBOX_INPUT = true;</script>
@php
    $related_model = request()->related_model?:$related_model;
    $cached_attributes = \App\Services\FeatureConfig\FeatureConfig\Excel\ExcelCacheService::getAttributes($related_model);
    $cached_relations = \App\Services\FeatureConfig\FeatureConfig\Excel\ExcelCacheService::getRelations($related_model);
@endphp
    <!-- Modal -->
<div id="excel-export-modal" class="modal fade" role="dialog" style="display: none">
    <form method="get" action="{{route('admin.excel.export')}}">
        @if(isset($related_model_query_data))
            @foreach($related_model_query_data as $related_model_query_key => $related_model_query_value)
                <input type="hidden" name="query_data[{{$related_model_query_key}}]"
                       value="{{$related_model_query_value}}">
            @endforeach
        @endif
        @if(isset($related_model_raw_query))
            <input type="hidden" name="raw_query" value="{{$related_model_raw_query}}">
        @endif
        @if(isset($related_query_select_raw))
            <input type="hidden" name="raw_select" value="{{$related_query_select_raw}}">
        @endif
        @if(isset($related_query_extended_attributes))
            @foreach($related_query_extended_attributes as $related_model_ex_attr_key => $related_model_ex_attr_value)
                <input type="hidden" name="extended_attributes[{{$related_model_ex_attr_key}}]"
                       value="{{$related_model_ex_attr_value}}">
            @endforeach
        @endif
        @if(isset($related_query_group_by))
            @foreach($related_query_group_by as $related_model_group_key => $related_model_group_value)
                <input type="hidden" name="group_by[{{$related_model_group_key}}]"
                       value="{{$related_model_group_value}}">
            @endforeach
        @endif

        <div class="modal-dialog modal-sm">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <div class="button-container">
                        <div class="btn btn-exit" data-dismiss="modal"></div>
                    </div>
                    <div class="title-container"><h1 class="title">دریافت خروجی</h1></div>
                </div>
                <div class="modal-body">
                    <p class="message">دریافت خروجی بر اساس:</p>
                    <div class="check-box-inputs-container input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
                         data-output-id="exporting-fields">
                        <div class="row">

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <span class="material-switch pull-left">انتخاب همه &nbsp
                                    <input id="check-all" class="checkbox-input-check-all" type="checkbox" value="1"/>
                                    <label for="check-all"></label>
                                    <input id="check-all_hidden" type="hidden" value="0"/>
                                </span>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <strong class="pull-right">فیلدها</strong>
                            </div>
                        </div>
                        <br>
                        @foreach(app($related_model)->getExportableAttributes() as $exportable)
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                    <span class="material-switch pull-left ">
                                        <input class="checkbox-input" id="{{$exportable}}" type="checkbox" value="1"
                                               @if(in_array($exportable,$cached_attributes)) checked @endif/>
                                        <label class="checkbox-input-label" for="{{$exportable}}"></label>
                                        <input class="checkbox-input-hidden" id="{{$exportable}}_hidden" type="hidden"
                                               value="0"/>
                                    </span>
                                </div>
                                <div class="col-lg-8 col-md-4 col-sm-6 col-xs-6">
                                    <p id="entity-">{{ trans("structures.attributes.{$exportable}") }}</p>
                                </div>
                            </div>
                        @endforeach
                        @if(isset($related_query_extended_attributes))
                            @foreach($related_query_extended_attributes as $related_model_ex_attr_key => $related_model_ex_attr_value)
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                    <span class="material-switch pull-left ">
                                        <input class="checkbox-input" id="{{$related_model_ex_attr_value}}"
                                               type="checkbox" value="1"
                                               @if(in_array($related_model_ex_attr_value,$cached_attributes)) checked @endif/>
                                        <label class="checkbox-input-label"
                                               for="{{$related_model_ex_attr_value}}"></label>
                                        <input class="checkbox-input-hidden"
                                               id="{{$related_model_ex_attr_value}}_hidden" type="hidden"
                                               value="0"/>
                                    </span>
                                    </div>
                                    <div class="col-lg-8 col-md-4 col-sm-6 col-xs-6">
                                        <p id="entity-">{{ trans("structures.attributes.{$related_model_ex_attr_value}") }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <hr>
                    <div class="check-box-inputs-container input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
                         data-output-id="exporting-relations">
                        <div class="row">

                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <span class="material-switch pull-left">انتخاب همه &nbsp
                                    <input id="check-all" class="checkbox-input-check-all" type="checkbox" value="1"/>
                                    <label for="check-all"></label>
                                    <input id="check-all_hidden" type="hidden" value="0"/>
                                </span>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <strong class="pull-right">روابط</strong>
                            </div>
                        </div>
                        <br>
                        @foreach(app($related_model)->getExportableRelations() as $name => $relation)
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                    <span class="material-switch pull-left ">
                                        <input class="checkbox-input" id="{{$relation["name"]}}" type="checkbox"
                                               value="1"
                                               @if(in_array($relation["name"],$cached_relations)) checked @endif/>
                                        <label class="checkbox-input-label" for="{{$relation["name"]}}"></label>
                                        <input class="checkbox-input-hidden" id="{{$relation["name"]}}_hidden"
                                               type="hidden"
                                               value="0"/>
                                    </span>
                                </div>
                                <div class="col-lg-8 col-md-4 col-sm-6 col-xs-6">
                                    <p id="entity-">{{ trans("structures.classes.".get_model_entity_name($name)) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    <input type="hidden" id="exporting-fields" name="exporting_fields" value="">
                    <input type="hidden" id="exporting-relations" name="exporting_relations" value="">
                    <input type="hidden" id="exporting-object-type" name="model_name" value="{{ $related_model }}">
                </div>
                <div class="modal-footer">
                    <button id="submit-export" type="submit" class="btn btn-sm btn-default btn-success">
                        دریافت
                    </button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"
                            onclick="window.rejectReq()">انصراف
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>


