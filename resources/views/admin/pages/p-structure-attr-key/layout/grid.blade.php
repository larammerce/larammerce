@foreach($p_structure_attr_keys as $p_structure_attr_key)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item roles">
        <div class="item-container">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col">
                <div class="img-container">
                    <img class="img-responsive" src="/admin_dashboard/images/No_image.jpg.png">
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col">
                <div class="label">شناسه</div>
                <div>{{$p_structure_attr_key->id}}#</div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">عنوان</div>
                <div>{{$p_structure_attr_key->title}}</div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col col-action">
                <div class="label">عملیات</div>
                <div class="actions-container">
                    <div class="col-lg-5 col-md-12 col-xs-12">
                        <a class="btn btn-sm btn-primary"
                           href="{{route('admin.p-structure-attr-key.edit', $p_structure_attr_key)}}">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a class="btn btn-sm btn-danger virt-form"
                           data-action="{{ route('admin.p-structure-attr-key.destroy', $p_structure_attr_key) }}"
                           data-method="DELETE" confirm>
                            <i class="fa fa-trash"></i>
                        </a>
                        <a class="btn btn-sm btn-success"
                           href="{{route('admin.p-structure-attr-key.show', $p_structure_attr_key)}}">
                            <i class="fa fa-eye"></i>
                        </a>
                    </div>
                    <div class="col-lg-7 col-md-6 col-xs-6">
                        <span class="material-switch">کاربر ارشد
                            <input id="has_value_{{$p_structure_attr_key->id}}" name="has_value_{{$p_structure_attr_key->id}}"
                                   type="checkbox" @if($p_structure_attr_key->has_value) checked @endif disabled/>
                            <label for="has_value_{{$p_structure_attr_key->id}}"></label>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
