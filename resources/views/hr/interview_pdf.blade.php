
<!DOCTYPE html>
<html lang="en">

<head>

        <link rel="stylesheet" href="css/io_templates.css">
        <style>
        .tablestyle1 {
    border:none !important;
}
.tablestyle1 tr td{
border:none;
border-bottom: none;
font-size: 15px;
}
.tablestyle1 td{
        
}
        </style>
</head>
<body>
                    
    <div class="box">  
        <div class="box-body">
                            <div  style="float: left; margin-right: 100px;text-align: center;height:30px;width:100%">
                                <h2 style="padding-top:0px;"><b><u>Interview Assessment Sheet</u></b></h2>
                                
                                </div>
        </div>
        <!-- /.box-body -->
    
    </div>

<div class="box">
        <div class="box-body">
            <table class="tablestyle1" style="width:100%;">
            
                <tr>
                        <td><p><b>Employee Name : </b> {{$emp['name']}}</p></td>
                </tr>
                <tr>
                        <td><p><b>Date : </b></p></td>
                </tr>
                
                    <tr>
                            <td><p><b>Preliminary Round Conducted By : </b> </p></td>
                    </tr>
                
                    <tr>
                            <td><p><b>Final Round Conducted By : </b> </p></td>
                    </tr>
                
            </table>
        </div>
    </div>
<br>
    <div class="box">
            <div class="box-body">
                    <h3>Preliminary Interview Remarks</h3>
                    <div class="row" >
                        <table class="tablestyle1" style="width:100%;">
                                 
                                <tr>
                                        <td><p><b>Post Suited : </b> </p></td>
                                        <td><p><b>Proposed Department : </b> </p></td>
                                </tr>
                                
                                <tr>
                                        <td><p><b>Salary Expectation : </b> </p></td>
                                        
                                </tr>
                                <tr >
                                        
                                        <td colspan="2" ><p><b>Remark : </b> </p></td>
                                </tr>
                                
                            </table>
                    </div><br><br>
                   
            </div>
        </div>

        <br>

    <div class="box">
            <div class="box-body">
                    <h3>Final Interview Remarks</h3>
                    <div class="row" >
                        <table class="tablestyle1" style="width:100%;">
                                 
                                <tr>
                                    <td><p><b>Post Suited : </b> </p></td>
                                    <td><p><b>Proposed Department : </b> </p></td>
                                </tr>
                                <tr>
                                        <td><p><b>Salary Expectation : </b> </p></td>
                                        <td><p><b>Joining Date: </b> </p></td>
                                </tr>
                                <tr>
                                        
                                    <td colspan="2"><p><b>Remark : </b> </p></td>

                                </tr>
                            </table>
                    </div><br><br>
                   
            </div>
        </div>
        <br>
        <div class="box">
                <div class="box-body">
                        <h3>Final Interview Remarks By G.M. / President</h3>
                        <div class="row"  >
                            <table class="tablestyle1" style="width:100%;">
                               
                                <tr>
                                        
                                    <td colspan="2"><p><b>Remark : </b> </p></td>

                                </tr>
                            </table>
                            
                        </div>
                       
                </div>
            </div>

        

</body>

</html>