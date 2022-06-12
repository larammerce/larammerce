@php
    $related_model = request()->related_model?:$related_model;
    $entity_name = get_model_entity_name($related_model);
    $dashed_string = str_to_dashed($entity_name);
@endphp
<div class="btn-group dropup fab green">
    @if(count($buttons) > 1)
    <button act="link" type="button" class=" dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-cogs"></i>
    </button>
    <ul class="dropdown-menu">
        @if(in_array('create', $buttons))
        <li>
            <button act="link" class="dropdown-item" href="{{route('admin.'.$dashed_string.'.create')}}">
                <i class="fa fa-plus"></i>
            </button>
        </li>
        @endif
        @if(in_array('download', $buttons))
        <li>
            <button act="link" class="dropdown-item" href="" data-toggle="modal" data-target="#excel-export-modal">
                <i class="fa fa-download"></i>
            </button>
        </li>
        @endif
        @if(in_array('upload', $buttons))
            <li>
                <button act="link" href="{{route('admin.excel.view-import', $related_model)}}">
                    <i class="fa fa-upload"></i>
                </button>
            </li>
        @endif
    </ul>
    @else
        @if(in_array('create', $buttons))
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" class="dropdown-item" href="{{route('admin.'.$dashed_string.'.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        @elseif(in_array('download', $buttons))
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" class="dropdown-item" href="" data-toggle="modal" data-target="#excel-export-modal">
                        <i class="fa fa-download"></i>
                    </button>
                </div>
            </div>
        @elseif(in_array('upload', $buttons))
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.excel.view-import', $related_model)}}">
                        <i class="fa fa-upload"></i>
                    </button>
                </div>
            </div>
        @endif
    @endif

</div>
