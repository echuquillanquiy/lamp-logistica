<tfoot>
  <tr>
<?php $scripttipos='' ?>
@foreach($searchs as $value)
    <th class="with-form-control">
    @if($value!='')
    <form action="javascript:;" onsubmit="search_table()">
      <?php $list = explode(':',$value); ?>
      @if(count($list)>1)
        @if($list[0]=='date')
          <input type="date" value="{{ isset($_GET[$list[1]])?$_GET[$list[1]]:'' }}" id="search-table-{{ $list[1] }}" class="form-control" placeholder="Buscar..." onchange="search_table()">        
          <?php $inputvalue = $list[1]; ?>
        @elseif($list[0]=='select')
          <?php $listdata = explode('/',$list[1]); ?>
          <select id="search-table-{{ $listdata[0] }}" class="form-control" onchange="search_table()">
            <option value="">Buscar...</option>
            <?php $listdataget = explode(',',$listdata[1]); ?>
            @foreach($listdataget as $value)
              <?php $listval = explode('=',$value); ?>
              <option value="{{$listval[0]}}" <?php echo $listval[0]==(isset($_GET[$listdata[0]])?$_GET[$listdata[0]]:'')?'selected':'' ?>>{{$listval[1]}}</option>
            @endforeach
          </select>
          <?php $inputvalue = $listdata[0]; ?>
        @endif
      @else
      <?php $inputvalue = $value; ?>
      <input type="text" value="{{ isset($_GET[$value])?$_GET[$value]:'' }}" id="search-table-{{ $value }}" class="form-control" placeholder="Buscar...">
      @endif
    </form>
    <?php
    $scripttipos = $scripttipos.'&'.$inputvalue.'=\'+$("#search-table-'.$inputvalue.'").val()+\'';
    ?>
    @endif
    </th>
@endforeach
  </tr>
</tfoot>
<script>
function search_table(){
    location.href = '{{ $search_url }}?<?php echo $scripttipos ?>';   
}
</script>