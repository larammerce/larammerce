@extends('admin.layout')

@section('bread_crumb')
    <li class="active"><a href="{{route('admin.modal.index')}}">پاپ آپ ها</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="toolbar">
            <ul>
                @foreach(SortService::getSortableFields('Modal') as $sortableField)
                    <li class="btn btn-default {{$sortableField->is_active ? "active" : ""}}"
                        href="{{route('admin.null')}}?sort_model=Modal&sort_field={{$sortableField->field}}&sort_method={{$sortableField->method}}"
                        act="link">
                        @if($sortableField->is_active)
                            <i class="fa {{$sortableField->method == SortMethod::ASCENDING ? "fa-long-arrow-up" : "fa-long-arrow-down"}}"></i>
                        @endif
                        {{$sortableField->title}}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                @include('admin.pages.modal.layout.list')
            </div>
            <div class="fab-container">
                <div class="fab green">
                    <button act="link" href="{{route('admin.modal.create')}}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        @include('admin.templates.pagination', [
        "modelName" => "Modal",
        "lastPage" => $modals->lastPage(),
        "total" => $modals->total(),
        "count" => $modals->perPage()
        ])
    </div>
@endsection
