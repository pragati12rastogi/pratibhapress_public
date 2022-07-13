@php
    if(!isset($arr)){
    
        if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
        if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
        if(!isset($arr['UV'])){ $arr['UV']="";}   
    }
    if(isset($arr)){
    
    if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
    if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
    if(!isset($arr['Packing'])){ $arr['Packing']="";}
    if(!isset($arr['UV'])){ $arr['UV']="";}   
}
    if(!isset($arr1)){
        
        $arr1['Cutting']="";
        $arr1['Lamination']="";
        $arr1['Packing']="";
        $arr1['UV']="";
    }
@endphp

<div class="box">
        <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-12">
               
                        <div class="col-md-6">
                                <label>Lamination<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="7[Lamination]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="7[Lamination]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Lamination']}}" placeholder="Remark"  name="7[remark][Lamination]">
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6">
                                <label>UV<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="7[UV]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="7[UV]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['UV']}}" placeholder="Remark"  name="7[remark][UV]">
                                    </div>
                                </div>
                        </div>
                    </div>
                       
                </div>
                <div class="row">
                    <div class="col-md-12">
                            <div class="col-md-6">
                                    <label>Cutting<span class="span"></span></label>
                                    <div class="col-md-2">
                                    <div class="radio">
                                            <label><input  autocomplete="off" type="radio" {{$arr['Cutting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="7[Cutting]">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input  autocomplete="off" type="radio" {{$arr['Cutting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="7[Cutting]">No</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Cutting']}}" placeholder="Remark"  name="7[remark][Cutting]">
                                        </div>
                                    </div>
                                </div>
                        <div class="col-md-6">
                                <label>Packing<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="7[Packing]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="7[Packing]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Packing']}}" placeholder="Remark"  name="7[remark][Packing]">
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
        </div>
</div> 