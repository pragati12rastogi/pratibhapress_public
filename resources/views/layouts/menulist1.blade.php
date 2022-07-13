
        
  <tr>
      @php
          print_r($menu);
          die;
      @endphp
            @if ($menu['menu']['map_id']=='all')
            <input type="hidden" name="menu[]" value="{{$menu['menu']['id']}}">
            @else                       
                @if($menu['menu']['pid']==0)
                 <td colspan="7">{{ $menu['menu']['name'] }}</td>
                 <td><input type="checkbox" name="menu[]" value="{{$menu['menu']['id']}}{{$menu['menu']['map_id']!=0 ?','.$menu['menu']['map_id']:''}}" {{$menu['menu']['user_id'] != NULL?'checked':''}}></td>
                @else
                    <td>{{ $menu['menu']['name']}}</td>
                    <td><input type="checkbox" name="menu[]" value="{{$menu['menu']['id']}}{{$menu['menu']['map_id']!=0 ?','.$menu['menu']['map_id']:''}}" {{$menu['menu']['user_id'] != NULL?'checked':''}}></td> 
                    @isset($menu['menu']['children'])
                        @if($menu['menu'][''])
                        <input type="checkbox" name="menu[]" value="{{$menu['menu']['id']}}{{$menu['menu']['map_id']!=0 ?','.$menu['menu']['map_id']:''}}" {{$menu['menu']['user_id'] != NULL?'checked':''}}>
                        <label class="1custom-unchecked ">{{ $menu['name'] }}</label>
                        @endif
                    @endisset
                @endif 
                @php                    
                    $menu['var']=($menu['var']+1)%5;    
                @endphp
            @endif

        
    </tr>  
