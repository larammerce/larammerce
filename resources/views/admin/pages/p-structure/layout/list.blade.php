@foreach($p_structures as $p_structure)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row roles">
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
            <div class="img-container">
                <img class="img-responsive" src="/admin_dashboard/images/No_image.jpg.png">
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$p_structure->id}}#</div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 col">
            <div class="label">عنوان</div>
            <div>{{$p_structure->title}}</div>
        </div>
        <div class="col-lg-6 col-md-9 col-sm-7 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{route('admin.p-structure.edit', $p_structure)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.p-structure.destroy', $p_structure) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
                <a class="btn btn-sm btn-success" href="{{route('admin.p-structure.download-excel', $p_structure)}}">
                    <i class="fa fa-download"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
