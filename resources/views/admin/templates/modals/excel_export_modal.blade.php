<script>window.HAS_CHECKBOX_INPUT = true;</script>
@php
    $related_model = request()->related_model?:$related_model;
    $cached_attributes = \App\Utils\CMS\Setting\Excel\ExcelCacheService::getAttributes($related_model);
    $cached_relations = \App\Utils\CMS\Setting\Excel\ExcelCacheService::getRelations($related_model);
@endphp
<form method="get"  action="{{route('admin.excel.export')}}">
    <!-- Modal -->
    <div id="excel-export-modal" class="modal fade" role="dialog" style="display: none">
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
                                        <input class="checkbox-input-hidden" id="{{$exportable}}_hidden" type="hidden" value="0"/>
                                    </span>
                                </div>
                                <div class="col-lg-8 col-md-4 col-sm-6 col-xs-6">
                                    <p id="entity-">{{ trans("structures.attributes.{$exportable}") }}</p>
                                </div>
                            </div>
                        @endforeach
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
                                        <input class="checkbox-input" id="{{$relation}}" type="checkbox" value="1"
                                               @if(in_array($relation,$cached_relations)) checked @endif/>
                                        <label class="checkbox-input-label" for="{{$relation}}"></label>
                                        <input class="checkbox-input-hidden" id="{{$relation}}_hidden" type="hidden" value="0"/>
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
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal" onclick="window.rejectReq()">انصراف
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>


