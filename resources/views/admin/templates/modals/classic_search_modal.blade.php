
<form method="get" action="{{route('admin.classic-search')}}">
    <!-- Modal -->
    <div id="classic-search-modal" class="modal fade" role="dialog" style="display: none;">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <div class="button-container">
                        <div class="btn btn-exit" data-dismiss="modal"></div>
                    </div>
                    <div class="title-container"><h1 class="title">جستجوی دقیق</h1></div>
                </div>
                <div class="modal-body">
                    <p class="message">جستجو بر اساس:</p>
                    <div class="row" id="search-inputs">
                        @if(isset(request()->related_model))
                            @foreach(app(request()->related_model)->getSearchableFields() as $field)
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="float: right">
                                    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                        <span class="label">{{ trans("structures.attributes.{$field}") }}</span>
                                        <input class="form-control input-sm" type="text"
                                               name="{{ $field }}">
                                    </div>
                                </div>
                            @endforeach
                        @elseif(isset($related_model))
                            @foreach(app($related_model)->getSearchableFields() as $field)
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="float: right">
                                    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                        <span class="label">{{ trans("structures.attributes.{$field}").',' }}</span>
                                        <input class="form-control input-sm" type="text"
                                               name="{{ $field }}">
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <input type="hidden" id="searched-object-type" name="related_model" value="{{ request()->related_model??$related_model }}">
                </div>
                <div class="modal-footer">
                    <button id="submit-classic-search" type="submit" class="btn btn-sm btn-default btn-success">
                        جستجو
                    </button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal" onclick="window.rejectReq()">انصراف
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>


