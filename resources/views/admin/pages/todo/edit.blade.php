@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.todo.index')}}">وظایف</a></li>
    <li class="active"><a href="{{route('admin.todo.create', compact('todo'))}}">ویرایش وظیفه</a></li>

@endsection

@section('form_title')
    ویرایش وظیفه
@endsection

@section('form_attributes')
    action="{{route('admin.todo.update', $todo)}}" method="POST"
@endsection

@section('form_body')
    {{ method_field('PUT') }}
    <input type="hidden" name="id" value="{{ $todo->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">موضوع</span>
        <input class="form-control input-sm" name="subject" value="{{ $todo->subject }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">وضعیت</span>
        <select class="form-control input-sm" name="status">
            @foreach($statuses as $status_id => $status_title)
                <option @if($status_id == $todo->status) selected
                        @endif value="{{$status_id}}">{{$status_title}}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
