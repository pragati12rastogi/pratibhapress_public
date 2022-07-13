@php
    if(!isset($arr)){
        if(!isset($arr['Die Cutting'])){ $arr['Die Cutting']="";}
    if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
    if(!isset($arr['Packing'])){ $arr['Packing']="";}
    if(!isset($arr['UV'])){ $arr['UV']="";}   
    if(!isset($arr['Pasting'])){ $arr['Pasting']="";}  
    }
    if(isset($arr)){
    
    if(!isset($arr['Die Cutting'])){ $arr['Die Cutting']="";}
    if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
    if(!isset($arr['Packing'])){ $arr['Packing']="";}
    if(!isset($arr['UV'])){ $arr['UV']="";}   
    if(!isset($arr['Pasting'])){ $arr['Pasting']="";}   
}
    if(!isset($arr1)){
        $arr1['Die Cutting']="";
        $arr1['Lamination']="";
        $arr1['Packing']="";
        $arr1['UV']="";
        $arr1['Pasting']="";
    }
@endphp

<div class="box">
        <div class="box-header with-border">
              
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Die Cutting<span class="span"></span></label>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Die Cutting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="14[Die Cutting]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Die Cutting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="14[Die Cutting]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Die Cutting']}}" placeholder="Remark"  name="14[remark][Die Cutting]">
                                </div>
                            </div>
                        </div>
                

                        <div class="col-md-6">
                            <label>Packing<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="14[Packing]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="14[Packing]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Packing']}}" placeholder="Remark"  name="14[remark][Packing]">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                    
                        <div class="col-md-6">
                                <label>Lamination<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="14[Lamination]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="14[Lamination]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Lamination']}}" placeholder="Remark"  name="14[remark][Lamination]">
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6">
                                <label>UV<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="14[UV]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="14[UV]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['UV']}}" placeholder="Remark"  name="14[remark][UV]">
                                    </div>
                                </div>
                        </div>

                        
                    </div>
                            
                </div>
                <div class="row">
                        <div class="col-md-12">
                        
                            <div class="col-md-6">
                                    <label>Pasting<span class="span"></span></label>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input  autocomplete="off" type="radio" {{$arr['Pasting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="14[Pasting]">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input  autocomplete="off" type="radio" {{$arr['Pasting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="14[Pasting]">No</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Pasting']}}" placeholder="Remark"  name="14[remark][Pasting]">
                                        </div>
                                    </div>
                            </div>
                          
                            
                        </div>
                                
                    </div>
        </div>
</div> 