@extends('admin.form_layout.col_12')
@section('bread_crumb')
    <li><a href="{{route('admin.action-log.index')}}">لاگ ها</a></li>
@endsection

@section('form_body')
    <div class="inner-container">
        <div class="inner-container has-toolbar has-pagination">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-xs-12">
                            <div class="p-3 mb-3">
                                <div class="row" dir="rtl">
                                    <div class="col-lg-12 col-md-12">
                                        <h4 dir="rtl" style="font-size: 16px">
                                            <i class="fa fa-globe"></i> پنل فروشگاهی {{env('APP_NAME')}}
                                        </h4>
                                        <hr>
                                    </div>
                                </div>
                                <div dir="rtl" class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" dir="rtl">
                                        <b> شناسه لاگ : </b> {{'#'.$action_log->_id}} <br>
                                        <b>نام کاربر انجام دهنده عملیات:</b> {{$action_log->user->full_name}}<br>
                                        <b>نام کاربری : </b> {{$action_log->user->username}}<br>
                                        <b>ایمیل : </b> {{$action_log->user->email}}<br>
                                        <b>دسترسی : </b> @if($action_log->is_allowed) مجاز@else غیرمجاز@endif<br>
                                        <b>تاریخ : </b> {{TimeService::getDateTimeFrom($action_log->created_at)}}<br>
                                    </div>
                                    {{--TODO: Add request_data and url_parameters--}}
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3" dir="rtl">
                                        <b>گزارش : </b> {{$action_log->message}}<br>
                                        <b> عملیات : </b> {{$action_log->action}} <br>
                                        <b> مدل مرتیط
                                            : </b> {{trans("structures.classes.".$action_log->related_model_type)}} <br>
                                        <b> شناسه مدل مرتیط : </b> {{$action_log->related_model_id}} <br>
                                        <b>آی پی : </b> {{$action_log->user_agent_ip}}<br>
                                        <b>مرورگر : </b> {{$action_log->user_agent_title}}<br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
