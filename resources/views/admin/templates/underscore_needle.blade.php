<script type="text/template" id="form-input">

    <input type="hidden" name="<%- inputName %>" value="<%- inputValue %>"/>

</script>

<script type="text/template" id="form-input-message">

    <p class="message message-<%- messageColor %>"><%- message %></p>

</script>

<script type="text/template" id="tags-container-template">
    <div class="tags-container">
        <ul tag-input-name="<%- name %>" class="clearfix">
        </ul>
    </div>
</script>

<script type="text/template" id="tag-element-template">
    <li class="tag-element" tag-id="<%- id %>" href="<%- href %>">
        <span class="tag-text"><%- text %></span>
        <button class="remove-tag" tag-id="<%- id %>" input-name="<%- inputName %>" type="button">
            <i class="fa fa-times"></i>
        </button>
    </li>
</script>

<script type="text/template" id="extra-property-template">
    <div class="row extra-property ui-state-default" row-id="<%- rowId %>">
        <input type="hidden" class="form-control input-sm" name="extra_properties[<%- rowId %>][priority]"
               value="">
        <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12">
            <span class="btn btn-block btn-default">
                <i class="fa fa-arrows"></i>
            </span>
        </div>
        <div class="input-group group-sm col-lg-3 col-md-2 col-sm-6 col-xs-12">
            <span class="label">عنوان</span>
            <input class="form-control input-sm" name="extra_properties[<%- rowId %>][key]" value="<%- title %>">
        </div>
        <div class="input-group group-sm col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <span class="label">مقدار</span>
            <input class="form-control input-sm" name="extra_properties[<%- rowId %>][value]" value="<%- value %>">
        </div>
        <div class="input-group group-sm col-lg-3 col-md-5 col-sm-6 col-xs-9">
            <select name="extra_properties[<%- rowId %>][type]" class="form-control input-sm">
                @foreach(\App\Models\Enums\PExtraPropertyShowType::values() as $value)
                    <% if(type === {{$value}}){ %>
                    <option selected value="{{$value}}">@lang('general.p_extra_property_show_type.'.$value)</option>
                    <% } else { %>
                    <option value="{{$value}}"> @lang('general.p_extra_property_show_type.'.$value) </option>
                    <% } %>
                @endforeach
            </select>
        </div>
        <div class="input-group group-sm col-lg-1 col-md-2 col-sm-6 col-xs-3 actions-container">
            <a href="#" row-id="<%- rowId %>" class="btn btn-sm btn-danger remove-btn">
                <i style="color:#fff" class="fa fa-times"></i>
            </a>
            <a href="#" row-id="<%- rowId %>" class="btn btn-sm btn-success add-btn">
                <i style="color: #fff" class="fa fa-plus"></i>
            </a>
        </div>
    </div>
</script>
<script type="text/template" id="virtual-form-template">
    <form class="hidden" action="<%- formAction %>" method="<%- formMethod %>">

    </form>
</script>

<script type="text/template" id="protector-layer-template">
    <div class="protector-layer fade-level-<%- fadeLevel %>"><p class="note"><%- note %></p></div>
</script>

<script type="text/template" id="search-container">
    <div class="search-container">
        <div class="inner-container">
            <button class="exit-button"><i class="fa fa-times"></i></button>
            <div class="view-port">
                <h1 class="page-title">نتایج جستجو
                    <img id="loading-animation" src="/admin_dashboard/images/search-loading.gif"/>
                </h1>
                <hr/>
                <div class="result-row">

                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="search-result-item">
    <div class="search-result-item type-<%- type %>">
        <div class="image-container">
            <div class="image-frame">
                <img src="<%- image %>"/>
            </div>
        </div>
        <div class="details-container">
            <div class="text-container">
                <a href="<%- link %>" target="_blank">
                    <p><%- title %></p>
                </a>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="template-query-scope">
    <div class="scope row" data-scope-id="<%- scope.id %>">
    </div>
</script>

<script type="text/template" id="template-query-scope-select">
    <div class="col-md-<%- size %> input-group group-sm">
        <label for="scope_<%- row_id %>_<%- select.id %>"><%- select.title %></label>
        <select name="scopes[<%- row_id %>][<%- select.id %>]"
                id="scope_<%- row_id %>_<%- select.id %>"
                class="form-control input-sm">
        </select>
    </div>
</script>

<script type="text/template" id="template-query-scope-option">
    <% if(selected) { %>
    <option value="<%- option.id %>" selected><%- option.title %></option>
    <% } else { %>
    <option value="<%- option.id %>"><%- option.title %></option>
    <% } %>
</script>

<script type="text/template" id="template-query-scope-and-btn">
    <div class="col-md-12">
        <a href="#" class="btn btn-block btn-sm btn-info">
            اضافه کردن
            <i class="fa fa-plus"></i>
        </a>
    </div>
</script>

<script type="text/template" id="template-query-scope-value">
    <div class="col-md-12 input-group group-sm">
        <label for="scope_<%- row_id %>_value">مقدار</label>
        <textarea name="scopes[<%- row_id %>][value]" placeholder="<%- comment %>"
                  id="scope_<%- row_id %>_value"
                  class="form-control input-sm"><%- value %></textarea>
    </div>
</script>

<script type="text/template" id="searchable-list-search-input">
    <li>
        <form>
            <div class="row">
                <div class="col-md-9 input-group group-sm">
                    <input name="search_<%- list_id %>"
                           style="margin-bottom: 8px; margin-top: 8px"
                           class="form-control input-sm"
                           placeholder="جستجو در بین آیتم های <%- list_title %>"/>
                </div>
                <div class="col-md-3" style="margin-top: 30px">
                    <button class="btn btn-xs btn-success submit">
                        <i class="fa fa-check"></i>
                    </button>
                    <button class="btn btn-xs btn-danger clear" style="display: none;">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        </form>
    </li>
</script>

<script type="text/template" id="cmf-row">
    <div class="row dynamic-form-container">
        <div class="input-group group-sm col-lg-2 col-sm-3 col-md-6 col-xs-12">
            <span class="label">عنوان</span>
            <input class="form-control input-sm" name="data_object[item_<%- index %>][input_title]"
                   value="<%- input_title %>">
        </div>
        <div class="input-group group-sm col-lg-2 col-sm-3 col-md-6 col-xs-12">
            <span class="label">شناسه</span>
            <input class="form-control input-sm" name="data_object[item_<%- index %>][input_identifier]"
                   value="<%- input_identifier %>">
        </div>
        <div class="input-group group-sm col-lg-1 col-sm-3 col-md-6 col-xs-12">
            <span class="label">نوع</span>
            <select class="form-control input-sm" name="data_object[item_<%- index %>][input_type]">
                <option
                <% if(input_type === "text") { %> selected <% } %> value="text" >متن</option>
                <option
                <% if(input_type === "long_text") { %> selected <% } %> value="long_text">متن طولانی</option>
                <option
                <% if(input_type === "option") { %> selected <% } %> value="option">انتخاب</option>
                <option
                <% if(input_type === "checkbox") { %> selected <% } %> value="checkbox">تیک</option>
            </select>
        </div>
        <div class="input-group group-sm col-lg-5 col-sm-12 col-md-12 col-xs-12">
            <span class="label">محتوا</span>
            <input class="form-control input-sm" name="data_object[item_<%- index %>][input_content]"
                   value="<%- input_content %>">
        </div>
        <div class="input-group group-sm col-lg-1 col-sm-3 col-md-6 col-xs-12">
            <span class="label">تکمیل توسط</span>
            <select class="form-control input-sm" name="data_object[item_<%- index %>][input_fill_by]">
                <option
                <% if(input_fill_by === "admin") { %> selected <% } %> value="admin">ادمین</option>
                <option
                <% if(input_fill_by === "customer") { %> selected <% } %> value="customer">مشتری</option>
            </select>
        </div>
        <div class="input-group group-sm col-lg-1 col-sm-3 col-md-6 col-xs-12">
            <div class="actions-container">
                <a class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i>
                </a>
                <a class="btn btn-sm btn-danger">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="survey-custom-state-row">
    <div class="row" data-row-id="<%- index %>">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 pt-10">
            <input type="text" class="form-control input-sm fast-select" name="custom_states[<%- index %>][state_id]"
                   placeholder="نام استان"
                   value="<%- state_id %>"
            <% if(state !== null) { %>
            data-initial-value='<%- JSON.stringify(state) %>'
            <% } %>
            data-url="{{route('api.v1.location.get-states')}}">
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">ساعت تاخیر ارسال پرسشنامه (۰ تا ۲۴ ساعت)</span>
                <input class="form-control input-sm" name="custom_states[<%- index %>][custom_delay_hours]"
                       value="<%- custom_delay_hours %>">
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">روزهای تاخیر ارسال پرسشنامه (۰ تا ۳۰ روز)</span>
                <input class="form-control input-sm" name="custom_states[<%- index %>][custom_delay_days]"
                       value="<%- custom_delay_days %>">
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">آدرس وب‌‌پیج فرم پرسشنامه</span>
                <input class="form-control input-sm" name="custom_states[<%- index %>][custom_survey_url]"
                       value="<%- custom_survey_url %>">
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
            <div class="actions-container">
                <a class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i>
                </a>
                <a class="btn btn-sm btn-danger">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
</script>


<script type="text/template" id="shipment-cost-custom-state-row">
    <div class="row" data-row-id="<%- index %>">
        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-6 pt-10">
            <input type="text" class="form-control input-sm fast-select" name="custom_states[<%- index %>][state_id]"
                   placeholder="نام استان" value="<%- state_id %>"
            <% if(state !== null) { %> data-initial-value='<%- JSON.stringify(state) %>' <% } %>
            data-url="{{route('api.v1.location.get-states')}}" >
        </div>
        <div class="col-lg-% col-md-5 col-sm-6 col-xs-6">
            <div class="input-group with-icon with-unit group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <i class="fa fa-dollar"></i>
                <span class="label">هزینه ارسال محصولات</span>
                <input class="form-control input-sm" name="custom_states[<%- index %>][shipment_cost]"
                       value="<%- shipment_cost %>">
                <span class="unit">ریال</span>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
            <div class="actions-container">
                <a class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i>
                </a>
                <a class="btn btn-sm btn-danger">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="product-package-row">
    <div class="row" data-row-id="<%- index %>">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <input class="form-control input-sm" placeholder="شناسه محصول"
                       name="product_items[<%- index %>][product_id]"
                       value="<%- product_id %>">
            </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-6">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <input class="form-control input-sm" placeholder="تعداد"
                       name="product_items[<%- index %>][product_count]"
                       value="<%- product_count %>">
            </div>
        </div>
        <div class="col-lg-4 col-md-3 col-sm-6 col-xs-6">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <input class="form-control input-sm" disabled placeholder="عنوان محصول"
                       name="product_items[<%- index %>][product_title]"
                       value="<%- product_title %>">
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
            <div class="actions-container">
                <a class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i>
                </a>
                <a class="btn btn-sm btn-danger">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="discount-step-row">
    <div class="row">
        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-6">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">مبلغ خرید آیتم (تومان)</span>
                <input class="form-control input-sm" name="steps_data[<%- index %>][amount]"
                       value="<%- amount %>">
            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-6">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">مقدار تخفیف اعمال شده روی آیتم (درصد یا تومان)</span>
                <input class="form-control input-sm"
                       name="steps_data[<%- index %>][value]"
                       value="<%- value %>">
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
            <div class="actions-container">
                <a class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i>
                </a>
                <a class="btn btn-sm btn-danger">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="modal-button-row">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">عنوان</span>
                <input class="form-control input-sm" name="buttons[<%- index %>][text]"
                       value="<%- text %>">
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">رنگ</span>
                <select name="buttons[<%- index %>][tag_class]" class="form-control input-sm" id="button-class"
                        required>
                    <option
                    <%- tag_class == "btn btn-primary" ? "selected" : "" %> value="btn
                    btn-primary">Primary(آبی)</option>
                    <option
                    <%- tag_class == "btn btn-secondary" ? "selected" : "" %> value="btn
                    btn-secondary">Secondary(خاکستری)</option>
                    <option
                    <%- tag_class == "btn btn-success" ? "selected" : "" %> value="btn
                    btn-success">Success(سبز)</option>
                    <option
                    <%- tag_class == "btn btn-danger" ? "selected" : "" %> value="btn btn-danger">Danger(قرمز)</option>
                    <option
                    <%- tag_class == "btn btn-warning" ? "selected" : "" %> value="btn
                    btn-warning">Warning(زرد)</option>
                    <option
                    <%- tag_class == "btn btn-info" ? "selected" : "" %> value="btn btn-info">Info(آبی روشن)</option>
                    <option
                    <%- tag_class == "btn btn-light" ? "selected" : "" %> value="btn btn-light">Light(سفید)</option>
                    <option
                    <%- tag_class == "btn btn-dark" ? "selected" : "" %> value="btn btn-dark">Dark(مشکی)</option>
                    <option
                    <%- tag_class == "btn btn-link" ? "selected" : "" %> value="btn btn-link">Link(فقط لینک)</option>
                </select>
            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-6">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">نوع</span>
                <select class="form-control input-sm" id="button-type" name="buttons[<%- index %>][type]" required>
                    <option
                    <%- type == "data-dismiss" ? "selected" : "" %> value="data-dismiss" selected>بستن پاپ آپ</option>
                    <option
                    <%- type == "link" ? "selected" : "" %> value="link">لینک</option>
                </select>
            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-6">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">لینک</span>
                <input class="form-control input-sm" id="button-link" name="buttons[<%- index %>][link]"
                       value="<%- link %>">
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
            <div class="actions-container">
                <a class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i>
                </a>
                <a class="btn btn-sm btn-danger">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="representative-option-row">
    <div class="row" data-row-id="<%- index %>">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">عنوان</span>
                <input class="form-control input-sm" name="options[<%- index %>]" value="<%- title %>">
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="actions-container">
                <a class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i>
                </a>
                <a class="btn btn-sm btn-danger">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
</script>
