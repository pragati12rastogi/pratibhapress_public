@php
    if(!isset($arr)){
        if(!isset($arr['Creasing'])){ $arr['Creasing']="";}
        if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
        if(!isset($arr['UV'])){ $arr['UV']="";}   
        if(!isset($arr['Taping'])){ $arr['Taping']="";}    
    }
    if(isset($arr)){

        if(!isset($arr['Creasing'])){ $arr['Creasing']="";}
        if(!isset($arr['Lamination'])){ $arr['Lamination']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
        if(!isset($arr['UV'])){ $arr['UV']="";}   
        if(!isset($arr['Taping'])){ $arr['Taping']="";}  
    }
    if(!isset($arr1)){  
        $arr1['Creasing']="";
        $arr1['Lamination']="";
        $arr1['Packing']="";
        $arr1['UV']="";
        $arr1['Taping']="";
    }
@endphp

<div class="box">
        <div class="box-header with-border">
              
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Creasing<span class="span"></span></label>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Creasing']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="13[Creasing]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Creasing']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="13[Creasing]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Creasing']}}" placeholder="Remark"  name="13[remark][Creasing]">
                                </div>
                            </div>
                        </div>
                

                        <div class="col-md-6">
                            <label>Packing<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="Yes" ? 'checked="checked"' : ''}}  class="radio" value="Yes" name="13[Packing]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio"  {{$arr['Packing']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="13[Packing]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Packing']}}" placeholder="Remark"  name="13[remark][Packing]">
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
                                        <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="13[Lamination]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Lamination']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="13[Lamination]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Lamination']}}" placeholder="Remark"  name="13[remark][Lamination]">
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6">
                                <label>UV<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="13[UV]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['UV']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="13[UV]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['UV']}}" placeholder="Remark"  name="13[remark][UV]">
                                    </div>
                                </div>
                        </div>

                        
                    </div>
                            
                </div>
                <div class="row">
                        <div class="col-md-12">
                        
                            <div class="col-md-6">
                                    <label>Taping<span class="span"></span></label>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input  autocomplete="off" type="radio" {{$arr['Taping']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="13[Taping]">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio">
                                            <label><input  autocomplete="off" type="radio" {{$arr['Taping']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="13[Taping]">No</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Taping']}}" placeholder="Remark"  name="13[remark][Taping]">
                                        </div>
                                    </div>
                            </div>
                          
                            
                        </div>
                                
                    </div>
        </div>
</div> 