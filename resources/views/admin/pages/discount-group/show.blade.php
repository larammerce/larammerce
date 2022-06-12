@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.discount-group.index')}}">پلن های تخفیف</a></li>
    <li class="active"><a href="{{route('admin.discount-group.show', $discount_group)}}">نمایش گروه
            تخفیف {{$discount_group->title}}</a>
    </li>

@endsection

@section('main_content')

@endsection