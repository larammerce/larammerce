@foreach($p_structure_attr_values as $p_structure_attr_value)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row roles">
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
            <div class="img-container">
                <img class="img-responsive" src="/admin_dashboard/images/No_image.jpg.png">
            </div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$p_structure_attr_value->id}}#</div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 col">
            <div class="label">نام کلید مربوطه</div>
            <div>{{$p_structure_attr_value->key->title}}</div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 col">
            <div class="label">مقدار</div>
            <div>{{$p_structure_attr_value->name}}</div>
        </div>
        <div class="col-lg-4 col-md-9 col-sm-7 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary"
                   href="{{route('admin.p-structure-attr-value.edit', $p_structure_attr_value)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.p-structure-attr-value.destroy', $p_structure_attr_value) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
                <a class="btn btn-sm btn-success"
                   href="{{route('admin.p-structure-attr-value.show', $p_structure_attr_value)}}">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
