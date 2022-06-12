<?php
$currentPage = PaginationService::getRecord($modelName, (isset($parentId) ? $parentId : null))->getPage();
$url = route('admin.null') . "?pagination_model={$modelName}&" . (isset($parentId) ? "pagination_parent_id={$parentId}&" : "");
?>
<div class="pagination-bar">
    <span>صفحات :</span>
    <ul>
        @if($currentPage > 2)
            <li @if($currentPage == 1) class="active" @else act="link" href="{{$url}}pagination_page=1"@endif>
                <a href="#">1</a>
            </li>
            @if($currentPage >= 4)
                <li>
                    <a>...</a>
                </li>
            @endif
        @endif
        @for($i= max([$currentPage-1, 1]); $i <= min([$currentPage+1, $lastPage]); $i++)
            <li @if($currentPage == $i) class="active" @else act="link" href="{{$url}}pagination_page={{$i}}"@endif>
                <a href="#">{{$i}}</a>
            </li>
        @endfor
        @if($currentPage < $lastPage-1)
            @if($currentPage <= $lastPage-3)
                <li>
                    <a>...</a>
                </li>
            @endif
            <li @if($currentPage == $lastPage) class="active" @else act="link" href="{{$url}}pagination_page={{$lastPage}}"@endif>
                <a href="#">{{$lastPage}}</a>
            </li>
        @endif
    </ul>
    <span>دسترسی سریع:</span>
    <form action="{{route('admin.null')}}" method="GET" style="display: inline">
        <input type="number" name="pagination_page" max="{{$lastPage}}" min="1">
        <input type="hidden" name="pagination_model" value="{{$modelName}}">
        @if(isset($parentId))
            <input type="hidden" name="pagination_parent_id" value="{{$parentId}}">
        @endif
    </form>

    <span>نمایش:</span>
    <span>{{ min(($currentPage-1)*$count + 1, $total) }} تا {{ min($currentPage*$count, $total) }} از {{ $total }} آیتم</span>
</div>