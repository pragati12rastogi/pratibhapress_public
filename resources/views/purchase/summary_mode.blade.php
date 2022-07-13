
<link rel="stylesheet" href="/css/common.css">

<div class="summary-outline" >
  <div class="row " >
    @foreach($mode as $item)
        
    @endforeach
  </div> 
  
  <div class="row">
  @php
      $mode=$item->mode_of_trans;
  @endphp
 @if ($mode=="By Self")
  <div class="row">
      <h4>Mode Of Transport : {{$item->mode_of_trans}}</h4>
        <div class="col-md-12">
                <label for=""><strong>Name :</strong> {{$item->name}}</label>     
            </div>  
</div> 
 @endif
 @if ($mode=="By Transport")
 <div class="row">
        <div class="col-md-4">
                <label for=""><strong>Transport Name :</strong> {{$item->name}}</label>     
            </div> 
            <div class="col-md-4">
                   <label for=""><strong>Vehicle Name :</strong> {{$item->vehicle_name}}</label>     
               </div>
               <div class="col-md-4">
                       <label for=""><strong>Vehicle Number :</strong> {{$item->vehicle_num}}</label>     
                   </div>      
</div>
<div class="row">
        <div class="col-md-4">
                <label for=""><strong>Bilty Number :</strong> {{$item->bilty_num}}</label>     
            </div> 
            <div class="col-md-4">
                   <label for=""><strong>Bilty Date :</strong> {{$item->bilty_date}}</label>     
               </div>
              
</div> 
@endif

@if ($mode=="By Courier")
 <div class="row">
        <div class="col-md-4">
                <label for=""><strong>Courier Company Name :</strong> {{$item->name}}</label>     
            </div> 
            <div class="col-md-4">
                   <label for=""><strong>Docket Number :</strong> {{$item->docket_num}}</label>     
               </div>
               <div class="col-md-4">
                       <label for=""><strong>Docket Date :</strong> {{$item->docket_date}}</label>     
                   </div>      
</div>
@endif
  </div>
</div>