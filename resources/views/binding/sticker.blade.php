@php
    if(!isset($arr)){
        if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
        if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
        if(!isset($arr['UV'])){ $arr['UV']="";}   
        if(!isset($arr['Half Cutting'])){ $arr['Half Cutting']="";}  
        if(!isset($arr['Die Cutting'])){ $arr['Die Cutting']="";}  
    }
    if(isset($arr)){
        if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
        if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
        if(!isset($arr['UV'])){ $arr['UV']="";}   
        if(!isset($arr['Half Cutting'])){ $arr['Half Cutting']="";}  
        if(!isset($arr['Die Cutting'])){ $arr['Die Cutting']="";}  
    }
    if(!isset($arr1)){
        
        $arr1['Cutting']="";
        $arr1['Lamination']="";
        $arr1['Packing']="";
        $arr1['UV']="";
        $arr1['Half Cutting']="";
        $arr1['Die Cutting']="";
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
                                        <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="10[Lamination]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="10[Lamination]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Lamination']}}" placeholder="Remark"  name="10[remark][Lamination]">
                                    </div>
                                </div>
                        </div>

                        <div class="col-md-6">
                                <label>UV<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="10[UV]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="10[UV]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['UV']}}" placeholder="Remark"  name="10[remark][UV]">
                                    </div>
                                </div>
                        </div>
                    </div>
                       
                </div>

                <div class="row">
                    <div class="col-md-12">
                   
                        <div class="col-md-6">
                                <label>Half Cutting<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Half Cutting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="10[Half Cutting]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Half Cutting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="10[Half Cutting]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Half Cutting']}}" placeholder="Remark"  name="10[remark][Half Cutting]">
                                    </div>
                                </div>
                        </div>

                        <div class="col-md-6">
                                <label>Die Cutting<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Die Cutting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="10[Die Cutting]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio"  {{$arr['Die Cutting']=="No" ? 'checked="checked"' : ''}}class="radio" value="No" name="10[Die Cutting]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Die Cutting']}}" placeholder="Remark"  name="10[remark][Die Cutting]">
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
                                        <label><input  autocomplete="off" type="radio" {{$arr['Cutting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="10[Cutting]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Cutting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="10[Cutting]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Cutting']}}" placeholder="Remark"  name="10[remark][Cutting]">
                                    </div>
                                </div>
                            </div>
                    

                            <div class="col-md-6">
                                    <label>Packing<span class="span"></span></label>
                                    <div class="col-md-2">
                                    <div class="radio">
                                            <label><input  autocomplete="off" type="radio"{{$arr['Packing']=="Yes" ? 'checked="checked"' : ''}}  class="radio" value="Yes" name="10[Packing]">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input  autocomplete="off" type="radio"{{$arr['Packing']=="No" ? 'checked="checked"' : ''}}  class="radio" value="No" name="10[Packing]">No</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Packing']}}" placeholder="Remark"  name="10[remark][Packing]">
                                        </div>
                                    </div>
                            </div>
                    </div>
                </div>  
        </div>
</div> 