@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.web-form-message.index')}}">متن فایل ربات</a></li>
@endsection

@section('main_content')
    <div class="inner-container">
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                <div class="form-group">
                    @if(file_exists(public_path()."/robots.txt"))
                        <button class="btn btn-default btn-sm" act="link"
                                href="{{route("admin.robot-txt.edit", get_system_user()->id)}}">
                            <i class="fa fa-edit blue"> </i> ویرایش
                        </button>
                        <button class="btn btn-danger btn-sm" act="link"
                                href="{{route("admin.robot-txt.remove", get_system_user()->id)}}">
                            <i class="fa fa-remove red"> </i> حذف
                        </button>
                    @else
                        <button class="btn btn-primary btn-sm" act="link"
                                href="{{route("admin.robot-txt.create", ["user-id" => get_system_user()->id])}}">
                            <i class="fa fa-plus-circle green"></i> ایجاد
                        </button>
                    @endif
                </div>
                <div class="form-group">
                    <textarea dir="ltr" class="codemirror-textarea hidden"
                              class="form-control col-xs-12"
                              rows="40" cols="80"
                              name="robot" disabled>{{$file_content}}
                    </textarea>
                </div>
            </div>
        </div>
    </div>
@endsection
