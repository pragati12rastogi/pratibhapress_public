@php
    if(!isset($arr)){
        if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
    if(!isset($arr['Form Folding'])){ $arr['Form Folding']="";}
    if(!isset($arr['Pasting'])){ $arr['Pasting']="";}
      
      
      
    }
    if(isset($arr)){
    if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
    if(!isset($arr['Form Folding'])){ $arr['Form Folding']="";}
    if(!isset($arr['Pasting'])){ $arr['Pasting']="";}
    }
    if(!isset($arr1)){
        $arr1['Cutting']="";
        $arr1['Form Folding']="";
        $arr1['Pasting']="";
    }
@endphp

<div class="box">
        <div class="box-header with-border">
                <h2>Seperator</h2>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Cutting<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Cutting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="4[Cutting]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Cutting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="4[Cutting]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Cutting']}}" placeholder="Remark"  name="4[remark][Cutting]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <label>Form Folding<span class="span"></span></label>
                                <div class="col-md-2">
                                <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Form Folding']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="4[Form Folding]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Form Folding']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="4[Form Folding]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Form Folding']}}" placeholder="Remark"  name="4[remark][Form Folding]">
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
                                    <label><input  autocomplete="off" type="radio" {{$arr['Pasting']=="Yes" ? 'checked="checked"' : ''}} class="radio" value="Yes" name="4[Pasting]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Pasting']=="No" ? 'checked="checked"' : ''}} class="radio" value="No" name="4[Pasting]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Pasting']}}" placeholder="Remark"  name="4[remark][Pasting]">
                                </div>
                            </div>
                        </div>
                          
                </div>

        </div>
    </div>  
</div>   