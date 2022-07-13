<link rel="stylesheet" href="/css/common.css">
   <style>
   .div{
     border:1px solid black;
   }
   </style>          
                    @if(isset($rec['Preliminary']))
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4 div" ><strong>Round:</strong>{{$rec['Preliminary']['round']}}</div>
                                    <div class="col-md-4 div" ><strong>Round Conducted by:</strong>{{$rec['Preliminary']['name']}}</div>
                                    <div class="col-md-4 div" ><strong>Post Suited:</strong>{{$rec['Preliminary']['post_suited']}}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4 div" ><strong>Proposed Department:</strong>{{$rec['Preliminary']['department']}}</div>
                                    <div class="col-md-8 div">
                                        <strong>Interview Remarks:</strong>{{$rec['Preliminary']['remarks']}}
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4 div" ><strong>Salary Expected:</strong>{{$rec['Preliminary']['salary_expect']}}</div>
                                     <div class="col-md-4 div"><strong>Joining Date:</strong>{{$rec['Preliminary']['joining_date']}}</div>
                                </div>
                            </div> --> 
                    @endif
                     @if(isset($rec['Final']))
                     <hr style="margin-top: 5px;margin-bottom: 5px;">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4 div" ><strong>Round:</strong>{{$rec['Final']['round']}}</div>
                                    <div class="col-md-4 div" ><strong>Round Conducted by:</strong>{{$rec['Final']['name']}}</div>
                                    <div class="col-md-4 div" ><strong>Post Suited:</strong>{{$rec['Final']['post_suited']}}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4 div" ><strong>Proposed Department:</strong>{{$rec['Final']['department']}}</div>
                                    <div class="col-md-4 div" ><strong>Salary Expected:</strong>{{$rec['Final']['salary_expect']}}</div>
                                     <div class="col-md-4 div" ><strong>Interview Remarks:</strong>{{$rec['Final']['remarks']}}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4 div"><strong>Joining Date:</strong>{{$rec['Final']['joining_date']}}</div>
                                    <div class="col-md-8 div" ><strong>Final Interview Remarks by G.M. / President:</strong>{{$rec['Final']['final_remarks']}}</div>
                                </div>
                            </div>
                            
                    @endif