@php
    if(!isset($arr)){

        if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
        if(!isset($arr['Eyelet'])){ $arr['Eyelet']="";}
        if(!isset($arr['Dorl'])){ $arr['Dorl']="";}   
    }
    if(isset($arr)){
        if(!isset($arr['Cutting'])){ $arr['Cutting']="";}
        if(!isset($arr['Packing'])){ $arr['Packing']="";}
        if(!isset($arr['Eyelet'])){ $arr['Eyelet']="";}
        if(!isset($arr['Dorl'])){ $arr['Dorl']="";}   
    }
    if(!isset($arr1)){
        $arr1['Cutting']="";
        $arr1['Packing']="";
        $arr1['Eyelet']="";
        $arr1['Dorl']="";

    }
@endphp

<div class="box">
        <div class="box-header with-border">
              
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <label>Cutting<span class="span"></span></label>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio"  class="radios" {{$arr['Cutting']=="Yes" ? 'checked="checked"' : ''}} value="Yes" name="12[Cutting]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio"  class="radios"{{$arr['Cutting']=="No" ? 'checked="checked"' : ''}} value="No" name="12[Cutting]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Cutting']}}" placeholder="Remark"  name="12[remark][Cutting]">
                                </div>
                            </div>
                        </div>
                

                        <div class="col-md-6">
                            <label>Packing<span class="span"></span></label>
                            <div class="col-md-2">
                            <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="12[Packing]">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="radio">
                                    <label><input  autocomplete="off" type="radio" {{$arr['Packing']=="No" ? 'checked="checked"' : ''}} class="radios" value="No" name="12[Packing]">No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="radio">
                                    <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Packing']}}" placeholder="Remark"  name="12[remark][Packing]">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                    
                        <div class="col-md-6">
                                <label>Eyelet<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Eyelet']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="12[Eyelet]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Eyelet']=="No" ? 'checked="checked"' : ''}} class="radios" value="No" name="12[Eyelet]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Eyelet']}}" placeholder="Remark"  name="12[remark][Eyelet]">
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6">
                                <label>Dorl<span class="span"></span></label>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio" {{$arr['Dorl']=="Yes" ? 'checked="checked"' : ''}} class="radios" value="Yes" name="12[Dorl]">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label><input  autocomplete="off" type="radio"{{$arr['Dorl']=="No" ? 'checked="checked"' : ''}}  class="radios" value="No" name="12[Dorl]">No</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="radio">
                                        <input  autocomplete="off" type="text" class="form-control input-css" value="{{$arr1['Dorl']}}" placeholder="Remark"  name="12[remark][Dorl]">
                                    </div>
                                </div>
                        </div>

                        
                    </div>
                            
                </div>
        </div>
</div> 