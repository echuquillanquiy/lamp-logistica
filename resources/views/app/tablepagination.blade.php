<?php 
$url = Request::fullUrl(); 
$counturl = explode('?',$url);
$addurl = '';
if(isset($_GET['page'])){
    $addurl = '&'.str_replace('page='.$_GET['page'],'',$counturl[1]);
}else{
    if(count($counturl)>1){
        $addurl = '&'.$counturl[1];
    }
}
?>
@if ($results->hasPages())
<nav>
  <ul class="pagination">
    @if ($results->onFirstPage())
    <li class="page-item disabled"><a class="page-link" href="javascript:;" tabindex="-1" aria-disabled="true">‹‹</a></li>
    @else
    <li class="page-item"><a class="page-link" href="{{$results->url(1)}}{{$addurl}}" tabindex="-1" aria-disabled="true">‹‹</a></li>
    @endif
    @foreach ($elements as $element)
        @if (is_string($element))
            <li class="page-item disabled"><a class="page-link" href="javascript:;" tabindex="-1" aria-disabled="true">{{ $element }}</a></li>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $urlpage)
                @if ($page == $results->currentPage())
                    <li class="page-item active"><a class="page-link" href="javascript:;" tabindex="-1" aria-disabled="true">{{ $page }}</a></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{$results->url($page)}}{{$addurl}}" tabindex="-1" aria-disabled="true">{{ $page }}</a></li>
                @endif
            @endforeach
        @endif
    @endforeach
    @if ($results->onFirstPage())
    <li class="page-item"><a class="page-link" href="{{$results->url($page)}}{{$addurl}}" tabindex="-1" aria-disabled="true">››</a></li>
    @else
    <li class="page-item disabled"><a class="page-link" href="javascript:;" tabindex="-1" aria-disabled="true">››</a></li>
    @endif
  </ul>
</nav> 
@endif 
