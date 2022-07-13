@extends($layout)

@section('title', 'Dispatch Planned Details')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Dispatch Planned Details</a></li>
   
@endsection

@section('main_section')
    <section class="content">
        <!-- Default box -->
        <div class="box-header with-border">
            <div class='box box-default'><br>
                <div class="container-fluid">
        <div class="row box-body" >
        <table id="table" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>IO No.</th>
                          <th>Reference</th>
                          <th>Item</th>
                          <th>Dispatch planned Qty</th>
                          <th>Dispatch Date</th>
                          <th>Dispatch Time</th>
                          <th>Priority</th>
                        </tr>
                      </thead>
                      <tbody>
                            @foreach($dis as $key)
                                <tr>
                                <td>{{$key['io_number']}}</td>
                                <td>{{$key['referencename']}}</td>
                                <td>{{$key['itemss']}}</td>
                                <td>{{$key['qty']}}</td>
                                <td>{{$key['date']}}</td>
                                <td>{{$key['time']}}</td>
                                <td>{{$key['priority']}}</td>
                                </tr>
                            @endforeach
                      </tbody>
               
                  </table>
        </div>
        </div>
        </div>
        </div>
       
 
      
      </section>
@endsection
