@foreach($todos as $todo)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row roles">
        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$todo->id}}#</div>
        </div>
        <div class="col-lg-6 col-md-3 col-sm-4 col-xs-6 col">
            <div class="label">موضوع</div>
            <div>{{$todo->subject}}</div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 col">
            <div class="label">وضعیت</div>
            <div>{{trans("general.todo.status.".$todo->status)}}</div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{route('admin.todo.edit', $todo)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.todo.destroy', $todo) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
