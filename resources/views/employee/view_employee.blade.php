@extends($layout)

@section('title', 'Employee Profile')

{{-- TODO: fetch from auth --}}
@section('user', Auth::user()->name)

@section('breadcrumb')
    <li><a href="#"><i class=""></i> Employee Profile</a></li>
   
@endsection
@section('css')
    <style>
    hr {
    margin-top: 10px;
    margin-bottom: 10px;
    border: 0;
    border-top: 1px solid #eee;
}
.scrr::-webkit-scrollbar {
  width: 5px;
}

/* Track */
.scrr::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px #bfbaba; 
  border-radius: 10px;
}
 
/* Handle */
.scrr::-webkit-scrollbar-thumb {
  background: #bfbaba; 
  border-radius: 10px;
}

/* Handle on hover */
.scrr::-webkit-scrollbar-thumb:hover {
  background: #8a8787; 
}
    .list-group-unbordered>.list-group-item {
        
        display: flow-root;
    }
    </style>
@endsection
@section('main_section')
    <section class="content">
        <div class="row">
           <div class="inline margin">
               @if ($emp_pic)
                    @if (file_exists(public_path().'/upload/employee' .'/'. $emp_pic->emp_id .'/document/'. $emp_pic->document_file))
                        <img src="/upload/employee/{{$emp_pic->emp_id}}/document/{{$emp_pic->document_file}}" alt="Avatar" style="width:200px;border-radius: 50%;">
                    @else 
                        <img src="/images/avatar.png" alt="Avatar" style="width:100px;border-radius: 50%;">
                    @endif
                @else 
                    <img src="/images/avatar.png" alt="Avatar" style="width:100px;border-radius: 50%;">
               @endif
                
           </div>
            <div class="inline margin">
                <h3 class="inline">{{$emp->name}}</h3>
            </div>
        </div><br>
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile scrr" style="height: 478px;overflow: auto;">
                    <!-- <img class="profile-user-img img-responsive img-circle" src="/images/avatar.png" alt="User profile picture"> -->
                    <h4 class="profile-username text-center">About Employee</h4>
                    <ul class="list-group list-group-unbordered" style="margin-bottom: 8px">
                        <li class="list-group-item">
                            <b>Name:</b> <a class="pull-right">{{$emp->name}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Employee Number:</b> <a class="pull-right">{{$emp->employee_number}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Adhar Number:</b> <a class="pull-right">{{$emp->adhar}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Email:</b> <a class="pull-right">{{$emp->email}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Reporting Head:</b> <a class="pull-right">{{$emp->reportingH}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Date Of Joining:</b> <a class="pull-right">{{$emp->doj}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>{{__('view_party.contact')}}</b> <a class="pull-right">{{$emp->mobile}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Department</b> <a class="pull-right">{{$emp->dept_name}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Designation</b> <a class="pull-right">{{$emp->designation}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Date Of Birth:</b> <a class="pull-right">{{$emp->dob}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Father Name:</b> <a class="pull-right">{{$emp->father_name}}</a>
                        </li>
                        {{-- <li class="list-group-item">
                            <b>Local Address:</b> <a class="pull-right">{{$emp->local_address}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Permanent Address:</b> <a class="pull-right">{{$emp->permanent_address}}</a>
                        </li> --}}
                        <li class="list-group-item">
                            <b>Family Contact:</b> <a class="pull-right">{{$emp->family_number}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Relation With Family:</b> <a class="pull-right">{{$emp->relation_with_emp}}</a>
                        </li>
                        {{-- <li class="list-group-item">
                            <b>Is Joining Paper Signed:</b> <a class="pull-right">{{$emp->joining_paper_signed}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Is OT:</b> <a class="pull-right">{{$emp->is_OT}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>OverTime:</b> <a class="pull-right">{{$emp->overtime}}</a>
                        </li> --}}
                        
                    </ul>
        
                  
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

            </div>
            <!-- /.col-md-3 -->

            <div class="col-md-5">
                <div class="box box-primary scrr" style="height: 480px;overflow: auto;">
                    <div class="box-header with-border" style="padding-bottom: 0px;">
                        <h4 class="profile-username text-center"style="font-size:25px">{{__('view_party.mrd')}}</h4>
                        
                    </div>
                    <div class="box-body">
            
                        <div class="row">
                                <div class="col-md-4"> <b>Local Address:</b></div>
                                <div class="col-md-8" style="overflow-wrap: break-word">{{$emp->local_address}}</div>
                        </div>
                        {{-- end of row --}}
                        <hr>

                        <div class="row">
                                <div class="col-md-4"><b>Permanent Address:</b></div> 
                                <div class="col-md-8" style="overflow-wrap: break-word">{{$emp->permanent_address}}</div>
                        </div><hr>
                         <div class="row">
                                <div class="col-md-4"><b>Home Landline:</b></div> 
                                <div class="col-md-8" style="overflow-wrap: break-word">{{$emp->home_landline}}</div>
                            </div><hr>
                        <div class="row">
                            <div class="col-md-4"><b>Employee Skill:</b></div> 
                            <div class="col-md-8" style="overflow-wrap: break-word">{{$emp->employee_skill}}</div>
                        </div><hr>
                        <div class="row">
                            <div class="col-md-4"><b>Shift Timing:</b></div> 
                            <div class="col-md-8" style="overflow-wrap: break-word">{{$emp->shifting_timing}}</div>
                        </div><hr>
                        <div class="row">
                                <div class="col-md-4"><b>Is Joining Paper Signed:</b></div> 
                                <div class="col-md-8" style="overflow-wrap: break-word">{{$emp->joining_paper_signed}}</div>
                            </div><hr>
                            <div class="row">
                                    <div class="col-md-4"><b>Is OT:</b></div> 
                                    <div class="col-md-8" style="overflow-wrap: break-word">{{$emp->is_OT}}</div>
                            </div><hr>
                            <div class="row">
                                    <div class="col-md-4"><b>OverTime:</b></div> 
                                    <div class="col-md-8" style="overflow-wrap: break-word">{{$emp->overtime}}</div>
                            </div><hr>
                       
                        {{-- end of row --}}
            
                        {{-- end of row --}}
            
                    </div>
                    <!-- /.box-body -->
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Profile Image -->
                    <div class="box box-primary">
                        <div class="box-body box-profile scrr"style="height: 478px;overflow: auto;">
                        <!-- <img class="profile-user-img img-responsive img-circle" src="/images/avatar.png" alt="User profile picture"> -->
            
                        <h4 class="profile-username text-center">PF/ESI Details</h4>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item" style="padding: 17px 15px;">
                                <b>IS PF/ESI:</b> <a class="pull-right">
                                    @if ($pfesi && $pfesi->is_pfesi)
                                        {{$pfesi->is_pfesi}}
                                    @else 
                                        {{"-"}}
                                    @endif
                                    </a>
                            </li>
                            <li class="list-group-item" style="padding: 17px 15px;">
                                <b>PF Number:</b> <a class="pull-right"> @if ($pfesi)
                                        {{$pfesi->pf_no}}
                                    @else 
                                        {{"-"}}
                                    @endif</a>
                            </li>
                            <li class="list-group-item" style="padding: 17px 15px;">
                                <b>Enroll Date PF:</b> <a class="pull-right">@if ($pfesi)
                                        {{$pfesi->enroll_date_pf}}
                                    @else 
                                        {{"-"}}
                                    @endif</a>
                            </li>
                            <li class="list-group-item" style="padding: 17px 15px;">
                                <b>Leave Date PF:</b> <a class="pull-right">
                                        @if ($pfesi)
                                        {{$pfesi->leave_date_pf}}
                                    @else 
                                        {{"-"}}
                                    @endif
                                    </a>
                            </li>
                            <li class="list-group-item" style="padding: 17px 15px;">
                                <b>ESI Number:</b> <a class="pull-right">
                                        @if ($pfesi)
                                        {{$pfesi->esi_no}}
                                    @else 
                                        {{"-"}}
                                    @endif
                                    </a>
                            </li>
                           
                            <li class="list-group-item" style="padding: 17px 15px;">
                                <b>Enroll Date ESI:</b> <a class="pull-right">
                                        @if ($pfesi)
                                        {{$pfesi->enroll_date_esi}}
                                    @else 
                                        {{"-"}}
                                    @endif
                                    </a>
                            </li>
                            <li class="list-group-item" style="padding: 17px 15px;">
                                <b>Leave Date ESI:</b> <a class="pull-right">
                                        @if ($pfesi)
                                        {{$pfesi->leave_date_esi}}
                                    @else 
                                        {{"-"}}
                                    @endif
                                    </a>
                            </li>
                        </ul>
            
                      
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
    
                </div>
               
                
            </div>
            <div class="row">
                    <div class="col-md-4">
                            <!-- Profile Image -->
                            <div class="box box-primary">
                                <div class="box-body box-profile" >
                                <!-- <img class="profile-user-img img-responsive img-circle" src="/images/avatar.png" alt="User profile picture"> -->
                    
                                <h4 class="profile-username text-center">Bank Details</h4>
                                <ul class="list-group list-group-unbordered">
                                    <li class="list-group-item">
                                        <b>Bank Status:</b> <a class="pull-right">{{$emp->bank_status}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Account Number:</b> <a class="pull-right">{{$emp->acc_number}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Account Name:</b> <a class="pull-right">{{$emp->acc_name}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>IFSC Code</b> <a class="pull-right">{{$emp->acc_ifsc}}</a>
                                    </li>
                                   
                                </ul>
                    
                              
                                </div>
                                <!-- /.box-body -->
                            </div>
                            
                            <!-- /.box -->
            
                        </div>
                        <div class="col-md-4">
                                <div class="box box-primary">
                                        <div class="box-body box-profile scrr"style="height: 243px;overflow-y: auto;">
                                                <h4 class="profile-username text-center">Employee Uploaded Documents</h4>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <th>Document Category </th>
                                                    <th>View</th>
                                                    {{-- <th>Download</th> --}}
                                                </thead>
                                                <thead>
                                                    @foreach ($doc as $item)
                                                        <tr>
                                                        <td>{{$item->document_name}}
                                                        </td>
                                                    <td> <a href="/upload/employee/{{$item->emp_id}}/document/{{$item->document_file}}" target="_blank"><u>View File</u></a>&nbsp;&nbsp;&nbsp;</td>
                                                        </tr>
                                                    @endforeach
                                                </thead>
                                            </table>
                                        </div>
                                </div>
                           </div>
                        <div class="col-md-4">
                                <div class="box box-primary">
                                        <div class="box-body box-profile scrr"style="height: 243px;overflow-y: auto;">
                                                <h4 class="profile-username text-center">Employee Nodues</h4>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <th> Date </th>
                                                    <th>View</th>
                                                    {{-- <th>Download</th> --}}
                                                </thead>
                                                <thead>
                                                    @foreach ($nodues as $due)
                                                        <tr>
                                                        <td><b>{{date('d-m-Y',strtotime($due->upload_start_date))}}</b> to <b>{{date('d-m-Y',strtotime("+6 months",strtotime($due->upload_start_date)))}}</b>
                                                        </td>
                                                    <td> <a href="/upload/nodues_form/{{$due->format}}" target="_blank"><u>View File</u></a>&nbsp;&nbsp;&nbsp;</td>
                                                        </tr>
                                                    @endforeach
                                                </thead>
                                            </table>
                                        </div>
                                </div>
                           </div>
            </div>
           <div class="row">
               <div class="col-md-12" >
                    <div class="box box-primary">
                            <div class="box-body box-profile" style="overflow-x:auto;">
                                    <h4 class="profile-username text-center">Employee Category Details</h4>
                                <table class="table table-bordered table-striped" >
                                    <thead>
                                        <th>Employee Category </th>
                                        <th>Offer Date</th>
                                        <th>Designation</th>
                                        <th>Joining Date</th>
                                        <th>Stipend</th>
                                        <th>Period Date</th>
                                        <th>Period Date + 6 Months</th>
                                        <th>Is Letter Issue</th>
                                        <th>View</th>
                                        {{-- <th>Download</th> --}}
                                    </thead>
                                    <thead>
                                        @foreach ($cat as $item)
                                            <tr>
                                            <td>
                                                    @if ($item->cat)
                                                    {{$item->cat}}
                                                    @else
                                                    {{"-"}}
                                                    @endif
                                            </td>
                                            <td>
                                                    @if ($item->cat_date)
                                                    {{$item->cat}}
                                                    @else
                                                    {{"-"}}
                                                    @endif
                                            </td>
                                            <td>
                                                    @if ($item->cat_designation)
                                                    {{$item->cat_designation}}
                                                    @else
                                                    {{"-"}}
                                                    @endif
                                            </td>
                                            <td>
                                                @if ($item->joining)
                                                    {{$item->joining}}
                                                    @else
                                                    {{"-"}}
                                                    @endif
                                            </td>
                                            <td>
                                                @if ($item->stipend)
                                                    {{$item->stipend}}
                                                    @else
                                                    {{"-"}}
                                                    @endif
                                            </td>
                                            <td>
                                                @if ($item->period_date)
                                                    {{$item->period_date}}
                                                    @else
                                                    {{"-"}}
                                                    @endif
                                            </td>
                                            <td>
                                                @if ($item->p_six_date)
                                                    {{$item->p_six_date}}
                                                    @else
                                                    {{"-"}}
                                                    @endif
                                            </td>
                                            <td>@if ($item->is_letter_issue==1)
                                                    {{"Yes"}}
                                                    @else
                                                    {{"No"}}
                                            @endif</td>
                                            <td> <a href="/upload/appointment_letter/{{$item->apt_letter}}" target="_blank"><u>View File</u></a>&nbsp;&nbsp;&nbsp;</td>
                                            </tr>
                                        @endforeach
                                    </thead>
                                </table>
                            </div>
                    </div>
               </div>
              
           </div>
           @if(isset($relieve))
               <div class="row">
                   <div class="col-md-12" >
                        <div class="box box-primary">
                                <div class="box-body box-profile" style="overflow-x:auto;">
                                        <h4 class="profile-username text-center">Employee Relieving Details</h4>
                                    <table class="table table-bordered table-striped" >
                                        <thead>
                                            <th>Leaving Assets </th>
                                            <th>Leaving Date</th>
                                            <th>Leaving Reason</th>
                                            <th>Fnf Complete</th>
                                            <th>Fnf Date</th>
                                            <th>Certificate File</th>
                                            <th>Signed Copy File</th>
                                            <th>Resignation Letter</th>
                                        </thead>
                                        <thead>
                                            
                                                <tr>
                                                <td>
                                                        @if ($asset)
                                                        {{$asset}}
                                                        @else
                                                        {{"-"}}
                                                        @endif
                                                </td>
                                                <td>
                                                        @if ($relieve['leaving_date'])
                                                        {{$relieve['leaving_date']}}
                                                        @else
                                                        {{"-"}}
                                                        @endif
                                                </td>
                                                <td>
                                                        @if ($relieve['leaving_reason'])
                                                        {{$relieve['leaving_reason']}}
                                                        @else
                                                        {{"-"}}
                                                        @endif
                                                </td>
                                                <td>
                                                    @if ($relieve['fnf_complete'])
                                                        {{$relieve['fnf_complete']}}
                                                        @else
                                                        {{"-"}}
                                                        @endif
                                                </td>
                                                <td>
                                                    @if ($relieve['fnf_date'])
                                                        {{$relieve['fnf_date']}}
                                                        @else
                                                        {{"-"}}
                                                        @endif
                                                </td>
                                                <td>
                                                    @if ($relieve['certificate_file'])
                                                        <a href="{{ asset('upload/employee') }}/{{$relieve['emp_id']}}/relieving/{{$relieve['certificate_file']}}" target="_blank">See your Certificate</a>
                                                        @else
                                                        {{"-"}}
                                                        @endif
                                                </td>
                                                <td>
                                                    @if ($relieve['signed_copy_file'])
                                                        <a href="{{ asset('upload/employee') }}/{{$relieve['emp_id']}}/relieving/{{$relieve['signed_copy_file']}}" target="_blank">See Signed Copy</a>
                                                        @else
                                                        {{"-"}}
                                                        @endif
                                                </td>
                                                <td>@if ($relieve['resignation_latter_file'])
                                                        <a href="{{ asset('upload/employee') }}/{{$relieve['emp_id']}}/relieving/{{$relieve['resignation_latter_file']}}" target="_blank">See Resignation Letter</a>
                                                        @else
                                                        {{"-"}}
                                                @endif</td>
                                                </tr>
                                           
                                        </thead>
                                    </table>
                                </div>
                        </div>
                   </div>
                  
               </div>
           
           @endif 
            <!-- About Me Box -->
        
            <!-- /row -->

        </div>
      
        <!-- /.row -->
      
      </section>
@endsection
