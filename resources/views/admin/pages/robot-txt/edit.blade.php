@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.robot-txt.index')}}">متن فایل ربات</a></li>
    <li class="active"><a href="#">ویرایش متن فایل ربات</a></li>
@endsection
@section('main_content')
    <div class="inner-container">
        <div class="inner-container has-toolbar has-pagination">
            <div class="view-port">
                <form method="post" action="{{route('admin.robot-txt.update', get_system_user()->id)}}">
                    {{csrf_field()}}
                    {{method_field('PATCH')}}
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> تایید</button>
                        <button class="btn btn-default btn-sm" act="link" href="{{route('admin.robot-txt.index')}}">
                            انصراف
                        </button>
                    </div>
                    <div class="form-group">
                        <textarea dir="ltr" class="codemirror-textarea-edit hidden"
                                  rows="40" cols="80"
                                  name="robot">{{$file_content}}
                        </textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

