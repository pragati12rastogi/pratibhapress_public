<?php
namespace App;
namespace App\Http\Controllers;
use App\Model\ItemCategory;
use App\Model\jobDetails;
use App\Imports\Import;
use Illuminate\Validation\Rule;
use App\Model\IoType;
use App\Model\PoNumber;
use App\Model\Printing;
use App\Model\Tax_Invoice;
use App\Model\Tax;
use App\Model\Unit_of_measurement;
use App\Model\Challan_per_io;
use App\Model\Delivery_challan;
use App\Model\Goods_Dispatch;
use App\Model\Settings;
use App\Model\Utilities\Internal_DC;
use App\Model\ElementFeeder;
use App\Model\Tax_Print;
use App\Model\Utilities\Material_inwarding;
use App\Model\Hsn;
use App\Model\Client_po;
use App\Model\advanceIO;
use App\Model\Client_po_consignee;
use App\Model\Binding_detail;
use App\Model\Raw_Material;
use App\Model\PaperType;
use App\Model\ElementType;
use App\Model\InternalOrder;
use App\Model\Binding_form_labels;
use App\Model\Binding_item;
use App\Model\JobCard;
use App\Model\Party;
use App\Model\GatePasses;
use App\Model\Users;
use App\Model\State;
use App\Model\Country;
use App\Model\City;
use App\Model\JobDetailsView;
use App\Model\Employee\EmployeeProfile;
use App\Model\Employee\EmployeeCategory;
use App\Model\Employee\EmployeeRelieving;
use App\Model\Employee\Assets;
use App\Model\Employee\AssetAssign;
use App\Model\Employee\AssetCategory;
use App\Model\FinancialYear;
use App\Model\Purchase\Grn;
use App\Model\Design\DesignOrder;
use App\Model\Production\DailyPlateProcess;
use App\Model\Production\PressDailyProcess;
use App\Model\Purchase\PurchaseOrder;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use PDF;  
use App\Model\Employee\EmployeeAppoint;
use App\dkerp;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\MYPDF;


class Template extends Controller
{
    public function asset_assign_form(Request $request){
        try {
            
            $this->validate($request,[
                'assets_category'=>'required',
                'assets_code'=>'required',
                'assets_emp'=>'required',
                'assets_from_date'=>'required',
                // 'assets_to_date'=>'required'
               
            ],
            [
                'assets_category.required'=>'This Field is required',
                'assets_code.required'=>'This Field is required',
                'assets_emp.required'=>'This Field is required',
                'assets_from_date.required'=>'This Field is required',
                // 'assets_to_date.required'=>'This Field is required'
            ]);

            $emp =EmployeeProfile::where('employee__profile.id',$request->input('assets_emp'))
            ->leftJoin('department','department.id','employee__profile.department_id')
            ->select('employee__profile.id',
                'employee__profile.name',
                'employee__profile.employee_number',
                'employee__profile.mobile',
                'employee__profile.designation',
                'department.department')->get()->first();
            $category = AssetCategory::where('ac_id',$request->input('assets_category'))->get()->first();
            $code = Assets::where('asset_id',$request->input('assets_code'))->get()->first();
            $last_id = Assets::get()->last()->asset_id;
            $auto_gen_no ="";
            if($last_id < 10){
                $auto_gen_no = "00".$last_id;
            }else if($last_id < 100){
                $auto_gen_no = "0".$last_id;
            }else{
                $auto_gen_no = $last_id;
            }
            $form_no = "PPML/ASS/".$auto_gen_no;
             
            $allprev_Assign_Asset = AssetAssign::where('employee_id',$request->input('assets_emp'))
            ->leftjoin('assets','asset_assign.asset_id','assets.asset_id')
            ->leftjoin('assets_category','asset_assign.asset_category_id','assets_category.ac_id')
            ->get()->toArray();
            
            if(isset($emp) && isset($category) && isset($code)){
                $data = [
                    'foo' => 'bar',
                    'emp'=>$emp,
                    'category'=>$category,
                    'code'=>$code,
                    'form'=>$form_no,
                    'recieved'=>date("d-m-Y",strtotime($request->input('assets_from_date'))),
                    'recieved_by'=>date('d-m-Y')
                ];
                $pdfFilePath = "asset form.pdf";
                $pdf = PDF::loadView('employee.assetformTemplate',$data);
                return $pdf->stream($pdfFilePath);
    
            }else{

                $message="No format exist!!";
                return redirect('/master/assets/assign/generate/form')->with('error',$message);
        
            }
            
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect('/master/assets/assign/generate/form')->with('error','some error occurred'.$ex->getMessage());  
        }
        
    }
    public function Appointment_letter_format(Request $request,$id,$name){
        $print_type = array(
            '1'=>'Original for Recipient',
            '2'=>'Duplicate for Transporter',
            '3'=>'Triplicate',
            '4'=>'Extra Copy'
        );

        if($name =="Offer Letter"){
            $validerrarr =[
                'offer_date'=>'required',
                'designation'=>'required',
                'joining'=>'required',
                
            ];
            $validmsgarr =[
                'offer_date.required'=>'This field is required',
                'designation.required'=>'This field is required',
                'joining.required'=>'This field is required',
            ];
            $this->validate($request,$validerrarr,$validmsgarr);
            $cat_type_id = DB::table('employee__category_master')
            ->where('name',$name)->first("id");
            $format = EmployeeAppoint::leftjoin('employee__category_master','employee__appointment_format.letter_type','employee__category_master.id')
            ->where('letter_type',$cat_type_id->id)
            ->select('employee__appointment_format.*','employee__category_master.name')
            ->get()->first();
            $employee = EmployeeProfile::where('id',$id)->get()->first();
            $createdby=Users::where('id',$format['created_by'])->get()->first();
            $checkCat =(!empty($request->input('designation')))?$request->input('designation'):$employee['designation'];

            $find = array('{{Candidate_Name}}','{{Candidate_Address}}','{{Letter_Date}}','{{Designation}}','{{Joining_Date}}');
            $replace = array($employee['name'],nl2br($employee['permanent_address']), date("d-m-Y", strtotime($request->input('offer_date'))),$employee['designation'],date("d-m-Y", strtotime($request->input('joining'))));
            $replacement = str_replace($find,$replace,html_entity_decode($format['content']));
            if($format != null){
                $data = [
                    'foo' => 'bar',
                    'format' => $replacement,
                    'print_type'=>$print_type
                    ];
                $pdfFilePath = $name.".pdf";
                $pdf = PDF::loadView('sections.letter_format', $data);
                return $pdf->download($pdfFilePath);
            }else{
                $message="No Appointment Letter exist!!";
                return redirect('/employee/category/update/'.$id)->with('error',$message);
            }
        }
        elseif($name =="Trainee Appointment Letter"){
            $validerrarr =[
                't_app_date'=>'required',
                'stipend'=>'required'
                
            ];
            $validmsgarr =[
                't_app_date.required'=>'This field is required',
                'stipend.required'=>'This field is required'
            ];
            $this->validate($request,$validerrarr,$validmsgarr);
            $cat_type_id = DB::table('employee__category_master')
            ->where('name',$name)->first("id");
            $format = EmployeeAppoint::leftjoin('employee__category_master','employee__appointment_format.letter_type','employee__category_master.id')
            ->where('letter_type',$cat_type_id->id)
            ->select('employee__appointment_format.*','employee__category_master.name')
            ->get()->first();
            $employee = EmployeeProfile::where('id',$id)->get()->first();
            $createdby=Users::where('id',$format['created_by'])->get()->first();
            $find = array('{{Candidate_Name}}','{{Candidate_Address}}','{{Letter_Date}}','{{Stipend}}','{{Created_Date}}');
            $replace = array($employee['name'],$employee['permanent_address'], date("d-m-Y", strtotime($request->input('t_app_date'))),$request->input('stipend'),date("d-m-Y"));
            $replacement = str_replace($find,$replace,html_entity_decode($format['content']));
            if($format != null){
                $data = [
                    'foo' => 'bar',
                    'format' => $replacement,
                    'print_type'=>$print_type
                    ];
                $pdfFilePath = $name.".pdf";
                $pdf = PDF::loadView('sections.letter_format', $data);
                return $pdf->download($pdfFilePath);
            }else{
                $message="No Appointment Letter exist!!";
                return redirect('/employee/category/update/'.$id)->with('error',$message);
            }
        }
        elseif($name =="Probation Appointment Letter"){
            $validerrarr =[
                'prob_design'=>'required',
                'prob_sal'=>'required',
                'prob_date'=>'required'
            ];
            $validmsgarr =[
                'prob_design.required'=>'This field is required',
                'prob_sal.required'=>'This field is required',
                'prob_date.required'=>'This field is required'
            ];
            $this->validate($request,$validerrarr,$validmsgarr);
            $cat_type_id = DB::table('employee__category_master')
            ->where('name',$name)->first("id");
            $format = EmployeeAppoint::leftjoin('employee__category_master','employee__appointment_format.letter_type','employee__category_master.id')
            ->where('letter_type',$cat_type_id->id)
            ->select('employee__appointment_format.*','employee__category_master.name')
            ->get()->first();
            $employee = EmployeeProfile::where('id',$id)->get()->first();
            $createdby=Users::where('id',$format['created_by'])->get()->first();
            $checkCat =(!empty($request->input('prob_design')))?$request->input('prob_design'):$employee['designation'];
            $find = array('{{Candidate_Name}}','{{Candidate_Address}}','{{Letter_Date}}','{{Designation}}','{{Stipend}}');
            $replace = array($employee['name'],$employee['permanent_address'], date("d-m-Y", strtotime($request->input('prob_date'))),$employee['designation'],$request->input('prob_sal'));
            $replacement = str_replace($find,$replace,html_entity_decode($format['content']));
            if($format != null){
                $data = [
                    'foo' => 'bar',
                    'format' => $replacement,
                    'print_type'=>$print_type
                    ];
                $pdfFilePath = $name.".pdf";
                $pdf = PDF::loadView('sections.letter_format', $data);
                return $pdf->download($pdfFilePath);
            }else{
                $message="No Appointment Letter exist!!";
                return redirect('/employee/category/update/'.$id)->with('error',$message);
            }
        }
        elseif($name =="Confirmation Letter"){
            $validerrarr =[
                'conf_desig'=>'required',
                'conf_date'=>'required'
            ];
            $validmsgarr =[
                'conf_desig.required'=>'This field is required',
                'conf_date.required'=>'This field is required'
            ];
            $this->validate($request,$validerrarr,$validmsgarr);
            $cat_type_id = DB::table('employee__category_master')
            ->where('name',$name)->first("id");
            $format = EmployeeAppoint::leftjoin('employee__category_master','employee__appointment_format.letter_type','employee__category_master.id')
            ->where('letter_type',$cat_type_id->id)
            ->select('employee__appointment_format.*','employee__category_master.name')
            ->get()->first();
            $employee = EmployeeProfile::where('id',$id)->get()->first();
            $createdby=Users::where('id',$format['created_by'])->get()->first();
            $checkCat =(!empty($request->input('conf_desig')))?$request->input('conf_desig'):$employee['designation'];
            $find = array('{{Candidate_Name}}','{{Candidate_Address}}','{{Letter_Date}}','{{Designation}}');
            $replace = array($employee['name'],$employee['permanent_address'], date("d-m-Y", strtotime($request->input('conf_date'))),$employee['designation']);
            $replacement = str_replace($find,$replace,html_entity_decode($format['content']));
            if($format != null){
                $data = [
                    'foo' => 'bar',
                    'format' => $replacement,
                    'print_type'=>$print_type
                    ];
                $pdfFilePath = $name.".pdf";
                $pdf = PDF::loadView('sections.letter_format', $data);
                return $pdf->download($pdfFilePath);
            }else{
                $message="No Appointment Letter exist!!";
                return redirect('/employee/category/update/'.$id)->with('error',$message);
            }
        }
        elseif($name =="Fixed Term Appointment Letter"){
            $validerrarr =[
                'fx_date'=>'required',
                'fx_desig'=>'required',
                'fx_per_date'=>'required',
                'fx_date_six'=>'required',
                'fx_sal'=>'required'
            ];
            $validmsgarr =[
                'fx_date.required'=>'This field is required',
                'fx_desig.required'=>'This field is required',
                'fx_per_date.required'=>'This field is required',
                'fx_date_six.required'=>'This field is required',
                'fx_sal.required'=>'This field is required'
            ];
            $this->validate($request,$validerrarr,$validmsgarr);
            $cat_type_id = DB::table('employee__category_master')
            ->where('name',$name)->first("id");
            $format = EmployeeAppoint::leftjoin('employee__category_master','employee__appointment_format.letter_type','employee__category_master.id')
            ->where('letter_type',$cat_type_id->id)
            ->select('employee__appointment_format.*','employee__category_master.name')
            ->get()->first();
            $employee = EmployeeProfile::where('id',$id)->get()->first();
            $createdby=Users::where('id',$format['created_by'])->get()->first();
            $checkCat =(!empty($request->input('fx_desig')))?$request->input('fx_desig'):$employee['designation'];
            $find = array('{{Candidate_Name}}','{{Candidate_Address}}','{{Letter_Date}}','{{Designation}}','{{Stipend}}','{{Period_Date}}','{{Period_after_six_Months}}');
            $replace = array($employee['name'],$employee['permanent_address'], date("d-m-Y", strtotime($request->input('fx_date'))),$employee['designation'],$request->input('fx_sal'), date("d-m-Y", strtotime($request->input('fx_per_date'))), date("d-m-Y", strtotime($request->input('fx_date_six'))));
            $replacement = str_replace($find,$replace,html_entity_decode($format['content']));
            if($format != null){
                $data = [
                    'foo' => 'bar',
                    'format' => $replacement,
                    'print_type'=>$print_type
                    ];
                $pdfFilePath = $name.".pdf";
                $pdf = PDF::loadView('sections.letter_format', $data);
                return $pdf->download($pdfFilePath);
            }else{
                $message="No Appointment Letter exist!!";
                return redirect('/employee/category/update/'.$id)->with('error',$message);
            }
        }
    }
    public function Relieving_letters_format(Request $request,$id,$name){
        if($name =="Hindi No Dues Letter"){
            $cat_type_id = DB::table('employee__category_master')
            ->where('name',$name)->first("id");
            $format = EmployeeAppoint::leftjoin('employee__category_master','employee__appointment_format.letter_type','employee__category_master.id')
            ->where('letter_type',$cat_type_id->id)
            ->select('employee__appointment_format.*','employee__category_master.name')
            ->get()->first();
         
            $employee = EmployeeProfile::where('id',$id)->get()->first();
            
            $emp_relieve = EmployeeRelieving::where('employee__relieving.emp_id',$id)->get()->first();
           
            $find = array('{{Candidate_Name}}','{{Candidate_Address}}','{{Designation}}','{{Resignation Date}}','{{Father Name}}','{{Date}}');
            $rel_date = "";
            if($request->input('resignation_d') == "1970-01-01" || $request->input('resignation_d') == ""){
                $rel_date = date("d-m-Y");
            }else{
                $rel_date = date("d-m-Y",strtotime($request->input('resignation_d')));
            }
            $cre_date =date("d-m-Y");
            
            $replace = array($employee['name'],$employee['permanent_address'],$employee['designation'],$rel_date,$employee['father_name'],$cre_date);
           
            $replacement = str_replace($find,$replace,html_entity_decode($format['content']));
      
            if($format != null){
                $data = [
                    'foo' => 'bar',
                    'format' => $replacement
                    ];
                $pdfFilePath = $name.".pdf";
                $pdf = PDF::loadView('sections.letter_format', $data);
                return $pdf->download($pdfFilePath);
                // return view('sections.letter_format',$data);
            }
            else{
                $message="No Appointment Letter exist!!";
                return redirect('/employee/relieving/update/'.$id)->with('error',$message);
            }
        }
        elseif($name =="English No Dues Letter"){
            $cat_type_id = DB::table('employee__category_master')
            ->where('name',$name)->first("id");
            $format = EmployeeAppoint::leftjoin('employee__category_master','employee__appointment_format.letter_type','employee__category_master.id')
            ->where('letter_type',$cat_type_id->id)
            ->select('employee__appointment_format.*','employee__category_master.name')
            ->get()->first();
         
            $employee = EmployeeProfile::where('id',$id)->get()->first();
            $emp_relieve = EmployeeRelieving::where('employee__relieving.emp_id',$id)->get()->first();
            
            $find = array('{{Candidate_Name}}','{{Candidate_Address}}','{{Designation}}','{{Resignation Date}}','{{Father Name}}','{{Date}}');
            $rel_date = "";
            if($request->input('resignation_d') == "1970-01-01" || $request->input('resignation_d') == ""){
                $rel_date = date("d-m-Y");
            }else{
                $rel_date = date("d-m-Y",strtotime($request->input('resignation_d')));
            }
            $cre_date =date("d-m-Y");
            
            $replace = array($employee['name'],$employee['permanent_address'],$employee['designation'],$rel_date,$employee['father_name'],$cre_date);
           
            $replacement = str_replace($find,$replace,html_entity_decode($format['content']));
      
            if($format != null){
                $data = [
                    'foo' => 'bar',
                    'format' => $replacement
                    ];
                $pdfFilePath = $name.".pdf";
                $pdf = PDF::loadView('sections.letter_format', $data);
                return $pdf->download($pdfFilePath);
                // return view('sections.letter_format',$data);
            }
            else{
                $message="No Appointment Letter exist!!";
                return redirect('/employee/relieving/update/'.$id)->with('error',$message);
            }
        }
        elseif($name =="Employee Relieving Letter"){
            $cat_type_id = DB::table('employee__category_master')
            ->where('name',$name)->first("id");
            $format = EmployeeAppoint::leftjoin('employee__category_master','employee__appointment_format.letter_type','employee__category_master.id')
            ->where('letter_type',$cat_type_id->id)
            ->select('employee__appointment_format.*','employee__category_master.name')
            ->get()->first();
         
            $employee = EmployeeProfile::where('id',$id)->get()->first();
            $createdby=Users::where('id',$format['created_by'])->get()->first();
            $emp_relieve = EmployeeRelieving::where('employee__relieving.emp_id',$id)->get()->first();
            $asset= Assets::whereIn('asset_id',explode(',',$request->input('leaving_assets')))->select('name')->get()->toArray();
            $len_asset= count($asset);
            $str_asset =array();
            foreach ($asset as $key => $value) {
                $str_asset[$key]= $value['name'];
            }
            $find = array('{{Candidate_Name}}','{{Candidate_Address}}','{{Designation}}','{{Resignation Date}}','{{Father Name}}','{{Hand over to employee}}','{{Relieving Date one month later}}','{{Date}}');
            $rel_date = "";
            if($request->input('resignation_d') == "1970-01-01" || $request->input('resignation_d') == ""){
                $rel_date = date("d-m-Y");
            }else{
                $rel_date = date("d-m-Y",strtotime($request->input('resignation_d')));
            }
            $cre_date =date("d-m-Y");
            
            $replace = array($employee['name'],$employee['permanent_address'],$employee['designation'],$rel_date,$employee['father_name'],implode(',', $str_asset),date("d-m-Y",strtotime("+1 month",strtotime($rel_date))),$cre_date);
           
            $replacement = str_replace($find,$replace,html_entity_decode($format['content']));
      
            if($format != null){
                $data = [
                    'foo' => 'bar',
                    'format' => $replacement
                    ];
                $pdfFilePath = $name.".pdf";
                $pdf = PDF::loadView('sections.letter_format', $data);
                return $pdf->download($pdfFilePath);
                // return view('sections.letter_format',$data);
            }else{
                $message="No Appointment Letter exist!!";
                return redirect('/employee/relieving/update/'.$id)->with('error',$message);
            }
        }
    }
    public function nodue_print(Request $request){
        try {
            $this->validate($request,[
                'fromdate'=>'required'
            ],[
                'fromdate.required'=>'From Date is required'
            ]);
            
            $date = $request->input('fromdate');
            $todate=date("d-m-Y",strtotime("+6 month",strtotime($date)));
        
            if($date){
            $data = [
                'foo' => 'bar',
                'date'=>$date,
                'todate'=> date('d-m-Y', strtotime('-1 day', strtotime($todate))),
                
                ];
              
            $pdfFilePath = "no_dues.pdf";
            $pdf = PDF::loadView('employee.nodues_template', $data);
            return $pdf->stream($pdfFilePath);
            // return view('employee.nodues_template',$data);
        }
        else{
            $message="No format exist!!";
            return redirect('/employee/nodues/print')->with('error',$message);
        }
        } catch (\Illuminate\Database\QueryException $ex) {
             return redirect('/employee/nodues/print')->with('error','some error occurred'.$ex->getMessage());
        }
    }
    public function internal_order($id){
     
       
        $internal=InternalOrder::where('internal_order.id',$id)->where('internal_order.is_active','=',1)
        ->leftJoin('job_details','internal_order.job_details_id','=','job_details.id')
        ->leftJoin('master__marketing_person','master__marketing_person.id','=','job_details.marketing_user_id')
        ->leftJoin('advance_io','job_details.advance_io_id','=','advance_io.id')
        ->leftJoin('party','internal_order.party_id','=','party.id')
        ->leftJoin('countries','party.country_id','=','countries.id')
        ->leftJoin('states','party.state_id','=','states.id')
        ->leftJoin('cities','party.city_id','=','cities.id')
        ->leftJoin('item_category','internal_order.item_category_id','=','item_category.id')
        ->leftJoin('payment_term','party.payment_term_id','=','payment_term.id')
        ->leftJoin('io_type','job_details.io_type_id','=','io_type.id')
        ->leftJoin('unit_of_measurement','job_details.unit','=','unit_of_measurement.id')
        ->leftJoin('hsn','hsn.id','=','job_details.hsn_code')
        ->leftJoin('party_reference','party_reference.id','internal_order.reference_name')
        ->get([
            'internal_order.id as io_id',
            'internal_order.io_number',
            'internal_order.status',
            'internal_order.party_id',
            'internal_order.item_category_id',
            'internal_order.job_details_id',
            DB::raw('(DATE_FORMAT(internal_order.closed_date ,"%d-%m-%Y")) as closed_date'),
            'internal_order.created_by',
            'internal_order.other_item_name',
            'party.address',
            'party.pincode',
            'payment_term.value',
            'party_reference.referencename as partyname',
            'cities.city as city',
            'states.name as states',
            'countries.name as country',
            'item_category.name as item_category',
            'io_type.name as io_type',
            'unit_of_measurement.uom_name',
             DB::raw('(DATE_FORMAT(internal_order.created_time ,"%d-%m-%Y %r")) as created_time'),
             DB::raw('(DATE_FORMAT(job_details.job_date ,"%d-%m-%Y")) as job_date'),
            DB::raw('(DATE_FORMAT(job_details.delivery_date ,"%d-%m-%Y")) as delivery_date'),
            'job_details.qty',
            'job_details.job_size',
            'job_details.rate_per_qty',
            'job_details.dimension',
            'job_details.marketing_user_id',
            'job_details.details',
            'job_details.front_color',
            'job_details.back_color',
            'job_details.is_supplied_paper',
            'job_details.is_supplied_plate',
            'job_details.remarks',
            'job_details.transportation_charge',
            'job_details.other_charge',
            'job_details.advanced_received',
            'master__marketing_person.id as user_id',
            'master__marketing_person.name as marketing_name',
            'advance_io.amount',
            'advance_io.mode_of_receive',
            DB::raw('(DATE_FORMAT(advance_io.date ,"%d-%m-%Y")) as amount_received_date'),
            'hsn.hsn as hsn_name',
            'hsn.gst_rate as gst'
        ])->first();
        $created=Users::where('id',$internal['created_by'])->get()->first();
        if($internal){
            $data = [
                'foo' => 'bar',
                'internal'=>$internal,
                'created'=>$created
                ];
               // return $created;
            $pdfFilePath = "internal_order.pdf";
            $pdf = PDF::loadView('sections.io_templates', $data);
            return $pdf->stream($pdfFilePath);
            //return view('sections.io_templates',$data);
        }
        else{
            $message="No internal order exist!!";
            return redirect('/internalorder/list')->with('error',$message);
        }
    }
    public function job_card($id){

        $jobcard=[];
        $jobcard['job']=JobCard::where('job_card.id',$id)->where('job_card.is_active','=',1)
        ->leftJoin('internal_order','job_card.io_id','=','internal_order.id')
        ->leftJoin('job_details','internal_order.job_details_id','=','job_details.id')
        ->leftJoin('master__marketing_person','master__marketing_person.id','=','job_details.marketing_user_id')
        ->leftJoin('party_reference','party_reference.id','internal_order.reference_name')
        ->leftJoin('item_category','job_card.item_category_id','=','item_category.id')
        ->where('job_card.id','!=',NULL)
                    ->get([
                        'job_card.id as job_id',
                        'job_details.marketing_user_id',
                        'master__marketing_person.id as user_id',
                        'job_card.closed_by as closed_by',
                        'job_card.created_by',
                        'job_card.status as status',
                        'job_card.description as description',
                        'master__marketing_person.name as marketing_name',
                        'job_card.job_number',
                        'job_card.job_qty as job_qty',
                        'job_card.creative_name',
                        'job_card.open_size',
                        'job_card.close_size',
                        'job_card.dimension',
                        'job_card.job_sample_received',
                        'job_card.remarks',
                        'job_card.item_category_id',
                        'job_card.created_time',
                        'internal_order.io_number',
                        'job_details.is_supplied_paper',
                        'job_details.is_supplied_plate',
                        'job_details.delivery_date',
                        'internal_order.created_time as io_created_time',
                        'item_category.name',
                        'job_card.other_item_desc',
                        'party_reference.referencename as partyname',
                        'job_details.is_supplied_paper',
                        'job_details.is_supplied_plate',
                        
                    ])->first();
                    // return $jobcard['job'];
                    $jobcard['created']=Users::where('id',$jobcard['job']->created_by)->get('name as created_by_name')->first();
                     $jobcard['element']=JobCard::where('job_card.id',$id)->where('job_card.is_active','=',1)
                     ->leftJoin('element_feeder','job_card.id','=','element_feeder.job_card_id')
                    ->where('element_feeder.id','!=',NULL)
                     ->get(['element_feeder.element_type_id as elementfeeder_type_id',
                     'element_feeder.plate_size',
                     'element_feeder.plate_sets',
                     'element_feeder.impression_per_plate',
                     'element_feeder.front_color',
                     'element_feeder.back_color',
                     'element_feeder.no_of_pages'
                     ]);

                     $jobcard['raw']=JobCard::where('job_card.id',$id)
                    ->leftJoin('raw_material','job_card.id','=','raw_material.job_card_id')
                    ->leftJoin('paper_type','paper_type.id','=','raw_material.paper_type_id')
                    ->where('raw_material.id','!=',NULL)
                     ->get([
                        'raw_material.element_type_id as element_type_id',
                        'raw_material.paper_size',
                        'raw_material.paper_type_id',
                        'raw_material.paper_gsm',
                        'raw_material.paper_mill',
                        'raw_material.paper_brand',

                        'raw_material.is_option',
                        'raw_material.item_name',
                        'raw_material.size',
                        'raw_material.size_dimension',
                        'raw_material.thickness',
                        'raw_material.thickness_dimension',
                        'raw_material.specification',
                        'raw_material.no_of_sheets',
                        'paper_type.name'
                        ]);

                        $jobcard['bind']=JobCard::where('job_card.id',$id)
                        ->leftJoin('binding_details','binding_details.job_card_id','=','job_card.id')
                        ->leftJoin('binding_item','binding_details.element_type_id','=','binding_item.id')
                       ->where('binding_details.id','!=',NULL)
                       ->get([
                          'binding_details.id',
                          'binding_details.value',
                          'binding_details.remark',
                          'binding_details.element_type_id' ,
                          'binding_item.item_name'
                       ]);
                       $jobcard['closed_by'] = Users::where('id','=',$jobcard['job']->closed_by)->value('name');
                       if($jobcard['job']!=NULL){
                        // return  $jobcard['raw'];
                    $pdfFilePath = "jobcard.pdf";
                     $pdf = PDF::loadView('sections.jobcard_template', $jobcard);
                    return $pdf->stream($pdfFilePath);
                    return view('sections.jobcard_template',$jobcard);
                }
                else{
                    $message="No Job Card exist!!";
                    return redirect('/jobcard/create')->with('error',$message);
                }
    }
    
    public function delivery_challan_pdf($id){
        $dc = [];
       $dc['dc'] = Delivery_challan::where('delivery_challan.id', '=', $id)->where('delivery_challan.is_active','=',1)
               ->leftJoin('challan_per_io', 'delivery_challan.id','=', 'challan_per_io.delivery_challan_id')
               ->leftJoin('internal_order as io', 'io.id','=', 'challan_per_io.io')
               ->leftJoin('item_category', 'item_category.id','=', 'io.item_category_id')
               ->leftJoin('job_details as jd', 'io.job_details_id','=', 'jd.id')
               ->leftJoin('hsn', 'jd.hsn_code','=', 'hsn.id')
               ->leftJoin('unit_of_measurement','challan_per_io.uom_id','=','unit_of_measurement.id')
               ->leftJoin('advance_io', 'jd.advance_io_id','=', 'advance_io.id')
               ->select('delivery_challan.*', 'challan_per_io.io', 'challan_per_io.delivery_challan_date', 'unit_of_measurement.uom_name',
                       'challan_per_io.good_desc', 'challan_per_io.good_qty','challan_per_io.rate as challan_rate','challan_per_io.packing_details', DB::raw('DATE_FORMAT(io.created_time,"%d-%m-%Y %r") as  io_time'), 'io.io_number','io.other_item_name','item_category.name as cat',
                       'hsn.hsn as hsn', 'hsn.gst_rate as gst', 'jd.rate_per_qty as qty_rate', 'advance_io.amount as amount')
               ->get();
               $dc['party'] = Delivery_challan::where('delivery_challan.id', '=', $id)->where('delivery_challan.is_active','=',1)
               ->leftJoin('party', 'party.id','=', 'delivery_challan.party_id') // party started
               ->leftJoin('cities as cp', 'party.city_id','=', 'cp.id')
               ->leftJoin('states as sp', 'sp.id','=', 'party.state_id')
               ->leftJoin('countries as ccp', 'ccp.id','=', 'party.country_id') // party ennded
               ->leftJoin('consignee', 'consignee.id','=', 'delivery_challan.consignee_id') // consg started
               ->leftJoin('cities as cc', 'consignee.city','=', 'cc.id')
               ->leftJoin('vehicle', 'vehicle.id','=', 'delivery_challan.vehicle_id')
               ->leftJoin('states as sc', 'sc.id','=', 'consignee.state')
               ->leftJoin('countries as ccc', 'ccc.id','=', 'consignee.country') // consg ennede
               ->leftJoin('goods_dispatch', 'goods_dispatch.id','=', 'delivery_challan.dispatch_id')
               ->leftJoin('dispatch_mode', 'goods_dispatch.mode','=', 'dispatch_mode.id')
               ->leftJoin('users', 'delivery_challan.created_by','=', 'users.id')
               ->select('party.partyname as pname', 'party.address as paddr',
               'party.pincode as ppcode', 'cp.city as pcity', 'sp.name as pstate', 'ccp.name as pcountry',
               'consignee.address as caddr', 'consignee.consignee_name as cname', 'consignee.pincode as cpcode',
               'cc.city as ccity', 'sc.name as cstate','ccc.name as ccountry','party.gst as pgst','party.gst_pointer as gst_pointer','party.pan as ppan','consignee.gst as cgst','consignee.pan as cpan', 'dispatch_mode.name as mode',
               'goods_dispatch.address','users.name as created_by_name','goods_dispatch.gst as dp_gst','vehicle.vehicle_number as vehicle')
               ->first();
          
           $dc['layout']='layouts.main' ;

        if($dc!=NULL && count($dc['dc'])!=0){
            $dc['goods_dispatch'] = Goods_Dispatch::whereIn('goods_dispatch.id',explode(',',$dc['dc'][0]->dispatch_id))
            ->select( 'goods_dispatch.courier_name')->get()->toArray();
        $pdfFilePath = "delivery_challan.pdf";
        $pdf = PDF::loadView('sections.delivery_template', $dc);
        return $pdf->stream($pdfFilePath);
        //return $dc;
        //return view('sections.delivery_template',$dc);
        }
        else{
            $message="No Delivery Challan exist!!";
            return redirect('/deliverychallan/list')->with('error',$message);
        }
    }
    public function tax_invoice(Request $request,$id){
        // return 'ddhcdg';
        // print_r($id);die;
        // print_r($request->input());die;
        if($request->input('type')=="print"){
            $tax_p=$request->input('print_id');
            $iop=Tax_Print::where('id',$request->input('print_id'))->select('io_po_number')->get()->first();
            $ios=json_decode($iop['io_po_number']);
            $io=array();
            // print_r($ios);die;
            foreach($ios as $key){
                $io[$key->io]=$key->client_po;
                
            }
           
        }
        else{
            $io=$request->input('io');
        }
        $print_type_option = array(
            '1'=>'Original for Recipient',
            '2'=>'Duplicate for Transporter',
            '3'=>'Triplicate',
            '4'=>'Extra Copy'
        );
        // 
        
        $checkbox=$request->input('check_box');
        // print_r($io);die();
        $x=array();
        $y=array();
        $client_po_date=array();
        $i=0;
        $j=0;
        // print_r($io);die;
        if(isset($io)){
            $i=0;
            foreach ($io as $key=>$value) {
                // $value=explode(',',$value);
                $z=InternalOrder::where('id',$key)->get('io_number')->first();
                $x[$i]=$z['io_number'];
                $io_id=$z['io_number'];
                $y[$i]=$value;
                // print_r($value);die;
                
                    
                    foreach($value as $item){
                        // print_r($item);die;
                    if($item!="Verbal"){
                    $client_pos=Client_po::where('io',$key)
                    ->leftJoin('client_po_data','client_po_data.client_po_id','client_po.id')
                    ->select(DB::raw('DATE_FORMAT(client_po_data.po_date,"%d-%m-%Y") as po_date'))
                    ->where('client_po_data.po_number',$item)->get()->first();
                    $client_po_date[$io_id][$item]=$client_pos['po_date'];
                    $j++;
                    }
                    else{
                        $client_po_date[$io_id][$item]="-";
                    }
                
                }
               

              
                $var[$i]=array('io'=>$key,'client_po'=>$value);
                $i++;
                }   
                $variable=json_encode($var);     
               
                $vars = Tax_Print::where('invoice_id',$id)->where('io_po_number', $variable)->get('io_po_number');
                //  print_r($vars);die;     
                    if(!isset($var) || count($vars->toArray())==0){
                        $print=Tax_Print::insertGetId([
                            'id'=>NULL,
                            'invoice_id'=>$id,
                            'io_po_number'=>$variable
                        ]); 
                    }
            // }
        }
       
        // print_r($client_po_date);die;
        // else{
        //     $i=0;
        //     $variable=Tax_Print::where('invoice_id',$id)->leftJoin('internal_order','internal_order.id','tax_print.io')->get();
        //     foreach ($variable as $key) {
        //         $x[$i]=$key['io_number'];
        //         $y[$i]=$key['client_po'];
        //         $i++;
        //     }
           
        // }
        // print_r($client_po_date);die;
        $tax_detail = Tax_Invoice::where('tax_invoice.id',$id)->where('tax_invoice.is_active','=',1)
            ->leftJoin('tax','tax_invoice.id','=','tax.tax_invoice_id')
            ->leftJoin('settings','tax_invoice.gst_type','=','settings.name')
            ->leftJoin('payment_term as tax_pyment','tax.payment','=','tax_pyment.id')
            ->leftJoin('delivery_challan','delivery_challan.id','=','tax.delivery_challan_id')
            ->leftJoin('internal_order','internal_order.id','=','tax.io_id')
            ->leftJoin('hsn','hsn.id','=','tax.hsn')
        
            ->leftJoin('client_po','tax.io_id','=','client_po.io')
            //->leftJoin('client_po_data','client_po_data.client_po_id','client_po.id')

            ->leftJoin('party', 'party.id','=', 'tax_invoice.party_id')
            ->leftJoin('consignee', 'consignee.id','=', 'tax_invoice.consignee_id')
            ->leftJoin('countries','party.country_id','=','countries.id')
            ->leftJoin('states','party.state_id','=','states.id')
            ->leftJoin('cities','party.city_id','=','cities.id')

            ->leftJoin('countries as con_country','consignee.country','=','con_country.id')
            ->leftJoin('states as con_states','consignee.state','=','con_states.id')
            ->leftJoin('cities as con_cities','consignee.city','=','con_cities.id')

            ->leftJoin('item_category','internal_order.item_category_id','=','item_category.id')
            ->leftJoin('payment_term','party.payment_term_id','=','payment_term.id')
            ->leftJoin('unit_of_measurement','tax.per','=','unit_of_measurement.id')
        
            ->select(
                'tax_invoice.id as tax_invoice_id',
                'tax_invoice.invoice_number',
                'tax_invoice.terms_of_delivery',
                'tax_invoice.gst_type',
                'tax_invoice.transportation_charge',
                'tax_invoice.other_charge',
                'settings.value as gst_type_rate',
                DB::raw('DATE_FORMAT(tax_invoice.date,"%d-%m-%Y") as  date'),
                'tax_invoice.party_id as tax_party_id',
                'tax_invoice.consignee_id as tax_consignee_id',
                'tax.delivery_challan_id',
                'tax.io_id',
                'tax.goods',
                'tax.qty',
                'tax_pyment.value as tax_payment',
                'party.gst as party_gst',
                'party.gst_pointer as gst_pointer',
                'party.pan as party_pan',
                'consignee.gst as consignee_gst',
                'consignee.pan as consignee_pan',
                'tax.rate',
                'tax.per',
                'tax.discount',
                'tax.hsn',
                'unit_of_measurement.uom_name',
                'internal_order.id as internalorder_id',
                'internal_order.io_number',
                'item_category.name as item',
                'payment_term.value as payment_term',
                'states.name as state',
                'countries.name as country',
                'cities.city',

                'con_states.name as con_states',
                'con_country.name as con_country',
                'con_cities.city as con_cities',
                'party.id as party_id',
                'party.partyname',
                'party.address as party_address',
                'party.pincode',
                'hsn.hsn',
                'hsn.gst_rate as hsn_gst',
                
                'delivery_challan.challan_number',
                'client_po.is_po_provided',
                'client_po.po_number',
                'client_po.po_date',
                'delivery_challan.dispatch',
                'consignee.id as con_id',
                'consignee.consignee_name as con_name',
                'consignee.address as con_address',
                'consignee.pincode as con_pincode'
            )->get();
        // print_r($tax_detail);die;
        if(count($tax_detail)!==0){
            // print_r($request->input('inv'));die;
            if($request->input('inv')==2){
                $certificate = 'file://'.base_path().'/public/images/alice.crt';
                $private_key  = 'file://'.base_path().'/public/images/alice.key';
                $letterhead= 'file://'.base_path().'/public/images/Pratibha Letterhead.pdf';
                $header= 'file://'.base_path().'/public/images/logo.jpg';
                $data = [
                    'foo' => 'bar',
                    'io' => $x,
                    'cpo' => $y,
                    'client_po_date'=>$client_po_date,
                    'tax_detail'=>$tax_detail,
                    'print_type_option'=>$print_type_option,
                    'print_type'=>$checkbox
                    ];
                $pdf = new MYPDF();
                // set document signature
                $pdf->setSignature($certificate, $private_key , 'tcpdfdemo', '', 2, $data);
                $pdf->SetPrintFooter(true);  
                $pdf->SetPrintHeader(true);
                    // print_r($app);die;
                $pdf->SetMargins(10, 40, 10, true);
                $pdf->AddPage();
                // $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                // $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                $pdf->SetFooterMargin(13);
                $pdf->SetHeaderMargin(10);
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                // $pdf->setSourceFile($letterhead);
                // $pdfdata = file_get_contents('file://'.base_path().'/public/images/Pratibha Letterhead.pdf');
                // $pagecount = $pdf->setSourceData($pdfdata);
                // for ($i = 1; $i <= $pagecount; $i++) {
                //     $tplidx = $pdf->importPage($i);
                //     $pdf->AddPage();
                //     $pdf->useTemplate($tplidx,null, null, 0, 0, true);
                // }
                $pdf->setCellPadding(0.5);
                // $pdf->setCellHeightRatio( 1 );
                $pdf->SetAutoPageBreak( true, 0 );
                $pdf->SetTitle('Tax Invoice');
                // $pdf->SetFont('helvetica', 16);
                // $img_file = 'file://'.base_path().'/public/images/logo.jpg';
                // $pdf->Image($img_file, 15, 140, 75, 113, 'JPG', 'http://www.tcpdf.org', '', true, 150, '', false, false, 1, false, false, false);
                
                // $pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
                // $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                $pdf->writeHTML(view('tax_invoice_template',$data)->render());
                $pdf->Output(public_path('tax_invoice.pdf'), 'I');
            }
            $data = [
                'foo' => 'bar',
                'io' => $x,
                'cpo' => $y,
                'client_po_date'=>$client_po_date,
                'tax_detail'=>$tax_detail,
                'print_type_option'=>$print_type_option,
                'print_type'=>$checkbox
                ];
            $pdfFilePath = "tax_invoice.pdf";
            $pdf = PDF::loadView('sections.tax_invoice_template', $data);
            return $pdf->stream($pdfFilePath);
            // return $data;
                return view('sections.tax_invoice_template');
        }
        else{
            $message="No Tax Invoice exist!!";
            return redirect('/taxinvoice/list')->with('error',$message);
        }
    }
    public function createPDF(Request $request)
    {
        // set certificate file
        $certificate = 'file://'.base_path().'/public/images/alice.crt';
        $private_key  = 'file://'.base_path().'/public/images/alice.key';

        // set additional information in the signature
        $info = array(
            'Name' => 'TCPDF',
            'Location' => 'Office',
            'Reason' => 'Testing TCPDF',
            'ContactInfo' => 'http://www.tcpdf.org',
        );
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document signature
        $pdf->setSignature($certificate, $private_key , 'tcpdfdemo', '', 2, $info);
        
   
        // $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTitle('Hello World');
      
        // print a line of text
        // $text = view('tcpdf');
        // $html = $text->render();
        // add view content
        $pdf->AddPage();
        // add image for signature
        $pdf->Image('file://'.base_path().'/public/images/tcpdf.png', 180, 60, 15, 15, 'PNG');
          $pdf->writeHTML("nmnmnmbm");

        // define active area for signature appearance
        // $pdf->setSignatureAppearance(180, 60, 15, 15);
        // $pdf ->loadView('tcpdf', $info);
        // save pdf file
        $pdf->Output(public_path('hello_world.pdf'), 'I');


        // dd('pdf created');
    }
    public function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
    
    
    public function tax_invoice_aftercreate(Request $request,$id,$check_box){
        // return 'ddhcdg';
        $print_type_option = array(
            '1'=>'Original for Recipient',
            '2'=>'Duplicate for Transporter',
            '3'=>'Triplicate',
            '4'=>'Extra Copy'
        );

        $tax_detail = Tax_Invoice::where('tax_invoice.id',$id)->where('tax_invoice.is_active','=',1)
            ->leftJoin('tax','tax_invoice.id','=','tax.tax_invoice_id')
            ->leftJoin('settings','tax_invoice.gst_type','=','settings.name')
            ->leftJoin('payment_term as tax_pyment','tax.payment','=','tax_pyment.id')
            ->leftJoin('delivery_challan','delivery_challan.id','=','tax.delivery_challan_id')
            ->leftJoin('internal_order','internal_order.id','=','tax.io_id')
            ->leftJoin('hsn','hsn.id','=','tax.hsn')
        
            ->leftJoin('client_po','tax.io_id','=','client_po.io')
            ->leftJoin('party', 'party.id','=', 'tax_invoice.party_id')
            ->leftJoin('consignee', 'consignee.id','=', 'tax_invoice.consignee_id')
            ->leftJoin('countries','party.country_id','=','countries.id')
            ->leftJoin('states','party.state_id','=','states.id')
            ->leftJoin('cities','party.city_id','=','cities.id')

            ->leftJoin('countries as con_country','consignee.country','=','con_country.id')
            ->leftJoin('states as con_states','consignee.state','=','con_states.id')
            ->leftJoin('cities as con_cities','consignee.city','=','con_cities.id')

            ->leftJoin('item_category','internal_order.item_category_id','=','item_category.id')
            ->leftJoin('payment_term','party.payment_term_id','=','payment_term.id')
            ->leftJoin('unit_of_measurement','tax.per','=','unit_of_measurement.id')
        
            ->select(
                'tax_invoice.id as tax_invoice_id',
                'tax_invoice.invoice_number',
                'tax_invoice.terms_of_delivery',
                'tax_invoice.gst_type',
                'tax_invoice.transportation_charge',
                'tax_invoice.other_charge',
                'settings.value as gst_type_rate',
                DB::raw('DATE_FORMAT(tax_invoice.date,"%d-%m-%Y") as  date'),
                'tax_invoice.party_id as tax_party_id',
                'tax_invoice.consignee_id as tax_consignee_id',
                'tax.delivery_challan_id',
                'tax.io_id',
                'tax.goods',
                'tax.qty',
                'tax_pyment.value as tax_payment',
                'party.gst as party_gst',
                'party.gst_pointer as gst_pointer',
                'party.pan as party_pan',
                'consignee.gst as consignee_gst',
                'consignee.pan as consignee_pan',
                'tax.rate',
                'tax.per',
                'tax.discount',
                'tax.hsn',
                'unit_of_measurement.uom_name',
                'internal_order.id as internalorder_id',
                'internal_order.io_number',
                'item_category.name as item',
                'payment_term.value as payment_term',
                'states.name as state',
                'countries.name as country',
                'cities.city',

                'con_states.name as con_states',
                'con_country.name as con_country',
                'con_cities.city as con_cities',
                'party.id as party_id',
                'party.partyname',
                'party.address as party_address',
                'party.pincode',
                'hsn.hsn',
                'hsn.gst_rate as hsn_gst',
                
                'delivery_challan.challan_number',
                'client_po.is_po_provided',
                'client_po.po_number',
                'client_po.po_date',
                'delivery_challan.dispatch',
                'consignee.id as con_id',
                'consignee.consignee_name as con_name',
                'consignee.address as con_address',
                'consignee.pincode as con_pincode'
            )->get();
            // print_r($tax_detail[0]['io_id']);
        $io=array();
        for ($i=0;$i<count($tax_detail);$i++) {
            $io[$tax_detail[$i]['io_id']] = "Verbal";
        }
        // print_r($io);die();
        $checkbox=explode(',',$check_box);
        $x=array();
        $y=array();
        $client_po_date=array();
        $i=0;
        if(isset($io)){
            $i=0;
            foreach ($io as $key=>$value) {
                $z=InternalOrder::where('id',$key)->get('io_number')->first();
                $x[$i]=$z['io_number'];
                $y[$i]=$value;
                $client_po_date[$i]=Client_po::where('io',$key)->select(DB::raw('DATE_FORMAT(po_date,"%d-%m-%Y") as po_date'))->get()->first();
                $var[$i]=array('io'=>$key,'client_po'=>$value);
                $i++;
                }   
                $variable=json_encode($var);          
                $vars = Tax_Print::where('invoice_id',$id)->where('io_po_number', $variable)->get('io_po_number');
                    if(!isset($var) || count($vars->toArray())==0){
                        $print=Tax_Print::insertGetId([
                            'id'=>NULL,
                            'invoice_id'=>$id,
                            'io_po_number'=>$variable
                        ]); 
                    }
        }
        
        
        if(count($tax_detail)!==0){
            $data = [
                'foo' => 'bar',
                'io' => $x,
                'cpo' => $y,
                'client_po_date'=>$client_po_date,
                'tax_detail'=>$tax_detail,
                'print_type_option'=>$print_type_option,
                'print_type'=>$checkbox
                ];
            $pdfFilePath = "tax_invoice.pdf";
            $pdf = PDF::loadView('sections.tax_invoice_template', $data);
            return $pdf->stream($pdfFilePath);
            // return $data;
                return view('sections.tax_invoice_template');
        }
        else{
            $message="No Tax Invoice exist!!";
            return redirect('/taxinvoice/list')->with('error',$message);
        }
    }
    public function tax_words($number){
        $no = floor($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array('0' => '', '1' => 'One', '2' => 'Two',
         '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
         '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
         '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
         '13' => 'Thirteen', '14' => 'Fourteen',
         '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
         '18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
         '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
         '60' => 'Sixty', '70' => 'Seventy',
         '80' => 'Eighty', '90' => 'Ninety');
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        while ($i < $digits_1) {
          $divider = ($i == 2) ? 10 : 100;
          $number = floor($no % $divider);
          $no = floor($no / $divider);
          $i += ($divider == 10) ? 1 : 2;
          if ($number) {
             $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
             $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
             $str [] = ($number < 21) ? $words[$number] .
                 " " . $digits[$counter] . $plural . " " . $hundred
                 :
                 $words[floor($number / 10) * 10]
                 . " " . $words[$number % 10] . " "
                 . $digits[$counter] . $plural . " " . $hundred;
          } else $str[] = null;
       }
       $str = array_reverse($str);
       $result = implode('', $str);
       $points = ($point) ?
         "." . $words[$point / 10] . " " . 
               $words[$point = $point % 10] : '';
       if($points != ''){    
           $res=$result . "Rupees  " . $points . " Paise Only";    
       return $res;
     } else {
     $res=$result . "Rupees Only";
         return $res;
     }
    }

    public function material_gatepass($id)
    {
        return $this->gatepass($id);
    } 
    public function returnable_gatepass($id)
    {
        return $this->gatepass($id);
    } 
    public function employee_gatepass($id)
    {
        return $this->gatepass($id);
    } 
    public function gatepass($id){
        $pass=GatePasses::where('gatepass.id',$id)->leftJoin('users', 'gatepass.created_by','=', 'users.id')
        ->leftJoin('goods_dispatch', 'gatepass.carrier_id','=', 'goods_dispatch.id')->get([
            'gatepass.*',
            'users.name',
            'goods_dispatch.*'
        ])->first();

        if($pass){
            if($pass['gatepass_for']=="Material"){
                $challan=explode(',',$pass['challan_id']);
                   for($i=0;$i<count($challan);$i++){
                       $x=$challan[$i];
                       $detail[$i]=delivery_challan::where('delivery_challan.id',$x)->leftJoin('party','delivery_challan.party_id','=','party.id')
                       ->leftJoin('challan_per_io', 'delivery_challan.id','=', 'challan_per_io.delivery_challan_id')
                       ->leftJoin('internal_order as io', 'io.id','=', 'challan_per_io.io')
                       ->leftJoin('job_details as jd', 'io.job_details_id','=', 'jd.id')
                       ->leftJoin('item_category','io.item_category_id','=','item_category.id')
                       ->where('challan_per_io.delivery_challan_id','!=',NULL)
                       ->select(
                           
                       )
                       ->get();
                  $mat[$i]=Internal_DC::where('internal_dc.id',$x)
                   ->select(
                   DB::raw("GROUP_CONCAT(DISTINCT(internal_dc.idc_number)) as idc_number"),
                   DB::raw("GROUP_CONCAT(DISTINCT(internal_dc.item_desc)) as item_desc"),
                   DB::raw("GROUP_CONCAT(DISTINCT(internal_dc.item_qty)) as item_qty"),
                   DB::raw("GROUP_CONCAT(DISTINCT(internal_dc.packing_desc)) as packing_desc"),
                   DB::raw("GROUP_CONCAT(DISTINCT(internal_dc.dispatch_to)) as dispatch_to")
                   )->get();
                   }
                   
                    // return view('sections.gatepass.material_templates', $data);
                    $del['pass']=$pass;
                    $del['dc']=$detail;
                    $del['mat']=$mat;
                //    return $del['pass'];
                $pdfFilePath = "gatepass_material.pdf";
                $paper_size = array(0,0,419.53,595.28);
                //return view('sections.gatepass.material_templates',$data);
                $pdf = PDF::loadView('sections.gatepass.material_templates', $del,[], [
                    'format' => 'A5-L'
                  ]);
                return $pdf->stream($pdfFilePath);
            }
            if($pass['gatepass_for']=="Employee"){
                $employee=GatePasses::where('gatepass.id',$id)->leftJoin('employee__profile','employee__profile.id','=','gatepass.employee_id')
                ->leftJoin('department','employee__profile.department_id','=','department.id')->get()->first();
                $pdfFilePath = "gatepass_employee.pdf";
                $paper_size = array(0,0,419.53,595.28);
                $data=['employee'=>$employee];
                //return $employee;
                //return view('sections.gatepass.material_templates',$data);
                $pdf = PDF::loadView('sections.gatepass.employee_template', $data,[], [
                    'format' => 'A5-L'
                  ]);
                return $pdf->stream($pdfFilePath);
            }
            if($pass['gatepass_for']=="Returnable"){
                $returnable=GatePasses::where('gatepass.id',$id)->leftJoin('delivery_challan','delivery_challan.id','=','gatepass.challan_id')
                        ->leftJoin('party','delivery_challan.party_id','=','party.id')
                        ->leftJoin('dispatch_mode','dispatch_mode.id','=','delivery_challan.dispatch')
                        ->leftJoin('goods_dispatch','goods_dispatch.id','=','delivery_challan.dispatch_id')
                        ->leftJoin('challan_per_io', 'delivery_challan.id','=', 'challan_per_io.delivery_challan_id')
                        ->leftJoin('internal_order as io', 'io.id','=', 'challan_per_io.io')
                        ->leftJoin('job_details as jd', 'io.job_details_id','=', 'jd.id')
                        ->leftJoin('item_category','io.item_category_id','=','item_category.id')
                        ->get();
                $ret=GatePasses::where('gatepass.id',$id)->where('gatepass.challan_type','internal_dc')
                ->leftJoin('internal_dc','internal_dc.id','=','gatepass.challan_id')
                ->leftjoin("goods_dispatch as gd",\DB::raw("FIND_IN_SET(gd.id,internal_dc.carrier_name_id)"),">",\DB::raw("'0'"))
                ->select(DB::raw("GROUP_CONCAT(gd.courier_name) as courier_name"),
                DB::raw("GROUP_CONCAT(DISTINCT(internal_dc.mode)) as mode"),
                DB::raw("GROUP_CONCAT(DISTINCT(internal_dc.idc_number)) as idc_number"),
                DB::raw("GROUP_CONCAT(DISTINCT(internal_dc.item_desc)) as item_desc"),
                DB::raw("GROUP_CONCAT(DISTINCT(internal_dc.item_qty)) as item_qty"),
                DB::raw("GROUP_CONCAT(DISTINCT(internal_dc.dispatch_to)) as dispatch_to")
                )->get();
                $pdfFilePath = "gatepass_employee.pdf";
                $paper_size = array(0,0,419.53,595.28);
                $data=['returnable'=>$returnable,'ret'=>$ret];
                // return $returnable;
                //return view('sections.gatepass.material_templates',$data);
                $pdf = PDF::loadView('sections.gatepass.returnable_template', $data,[], [
                    'format' => 'A5-L'
                  ]);
                return $pdf->stream($pdfFilePath);
            }
        }
        
        else{
            $message="No internal order exist!!";
            return redirect('/internalorder/list')->with('error',$message);
        }
        
    }

    public function dailyreport_template(Request $request){
        $selected_date = date('Y-m-d',strtotime($request->input('date')));
        $ref=$request->input('reference_name');
        $party=$request->input('party_name');
        
        $fromDate= date('Y-m',strtotime($selected_date));
        $dates=date_create($fromDate);
        $finan_yr = FinancialYear::where('from', '<=', $fromDate)->where('to', '>=', $fromDate)->first();
        
        $totalSalefn = Tax_Invoice::rightJoin('tax','tax_invoice.id',  'tax.tax_invoice_id')
                        ->leftJoin('hsn','hsn.id','=','tax.hsn')
                        ->where('tax_invoice.is_active',1)
                        ->whereRaw('(tax_invoice.date BETWEEN "'.$finan_yr->from.'-01" And "'.$finan_yr->to.'-31") and (tax_invoice.date BETWEEN "'.$finan_yr->from.'-01" And Now())')
                        ->select(
                            'tax_invoice.id',
                            DB::raw('group_concat(tax.qty) as tax_qty'),
                            DB::raw('group_concat(tax.rate) as tax_rate'),
                            DB::raw('group_concat(hsn.gst_rate) as hsn_gst'),
                            DB::raw('group_concat(tax.discount) as tax_dis'),
                            DB::raw('SUM((tax.rate * tax.qty) - ((tax.rate * tax.qty) * (tax.discount/100)) ) as amount'),
                            'tax_invoice.total_amount',
                            DB::raw('group_concat(DISTINCT(tax_invoice.date)) as tax_date')
                        )->groupBy('tax_invoice.id',
                            'tax_invoice.total_amount')->get()->toArray()
                       ;
        $withtax =0;
        $withouttax =0;
        foreach ($totalSalefn as $tsf) {
            $withouttax = $withouttax + $tsf['amount'];
            $withtax = $withtax + $tsf['total_amount'];
        }

        $totalSaleMn = Tax_Invoice::rightJoin('tax','tax_invoice.id',  'tax.tax_invoice_id')
                        ->leftJoin('hsn','hsn.id','=','tax.hsn')
                        ->where('tax_invoice.is_active',1)
                        ->whereRaw("Month(tax_invoice.date)= Month('".$selected_date."') and (tax_invoice.date BETWEEN CONCAT(DATE_FORMAT('".$selected_date."', '%Y-%m'),'-01') And Now())")
                        ->select(
                            'tax_invoice.id',
                            DB::raw('group_concat(tax.qty) as tax_qty'),
                            DB::raw('group_concat(tax.rate) as tax_rate'),
                            DB::raw('group_concat(hsn.gst_rate) as hsn_gst'),
                            DB::raw('group_concat(tax.discount) as tax_dis'),
                            DB::raw('SUM((tax.rate * tax.qty) - ((tax.rate * tax.qty) * (tax.discount/100)) ) as amount'),
                            'tax_invoice.total_amount',
                            DB::raw('group_concat(DISTINCT(tax_invoice.date)) as tax_date')
                        )->groupBy('tax_invoice.id',
                            'tax_invoice.total_amount')->get()->toArray()
                       ;
        $withtaxformonth =0;
        $withouttaxformonth =0;
        foreach ($totalSaleMn as $tsm) {
            $withouttaxformonth = $withouttaxformonth + $tsm['amount'];
            $withtaxformonth = $withtaxformonth + $tsm['total_amount'];
        }
        
        //io today
        $countIO = InternalOrder::whereRaw('job_details.job_date = "'.$selected_date.'"')->leftJoin('job_details','job_details.id','internal_order.job_details_id')
            ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
            ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
            ->leftJoin('io_type','job_details.io_type_id','io_type.id')
            ->select('internal_order.io_number',
                'io_type.name',
                'party_reference.referencename',
                DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
                'job_details.qty as IO_qty','rate_per_qty',DB::raw('(job_details.qty * rate_per_qty) as amount'));
            
        // party billed 
           
        $tax_invoice = Tax_Invoice::whereRaw('tax_invoice.date = "'.$selected_date.'"')
            ->where('tax_invoice.is_active',1)
            ->leftjoin('party','party.id','tax_invoice.party_id')
            ->leftJoin('party_reference','party.reference_name','party_reference.id')
            ->leftjoin('tax','tax_invoice.id','tax.tax_invoice_id')
            ->leftjoin('internal_order','internal_order.id','tax.io_id')
            ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
            ->leftjoin('waybill','waybill.invoice_id','tax_invoice.id')
            ->select('tax_invoice.id',DB::raw('SUM(tax.qty) as tot_qty'),DB::raw('SUM(tax.rate) as tot_rate'),'tax_invoice.invoice_number',DB::raw('group_concat(DISTINCT(concat(item_category.name,if(`item_category`.name = "Other",":",""),internal_order.other_item_name))) as item_name'),'waybill.waybill_number','party.partyname','total_amount','waybill_status','tax_invoice.is_active','tax_invoice.status','tax_invoice.date',
                DB::raw('(select SUM(tt.total_amount) from tax_invoice tt 
                            LEFT JOIN party p ON p.`id` = tt.`party_id`
                    where tt.date ="'.$selected_date.'" and tt.party_id =`tax_invoice`.`party_id` GROUP by p.gst )AS wa_s'),'waybill.id as waybill_id'
            )->groupBy('tax_invoice.id')
            // ->get()
            ;
           
        //material recieved today
        $mir_today= Material_inwarding::whereRaw('material_inward.date = "'.$selected_date.'"')
            ->leftjoin('pur_grn','pur_grn.material_inward_id','material_inward.id')
            ->select(
               'material_inward.material_inward_number',
               'material_inward.company',
               'material_inward.item_name',
               'material_inward.qty',
               'material_inward.date',
               'material_inward.driver_name',
               DB::raw('Group_Concat(DISTINCT(pur_grn.grn_number)) as pur_grn_no'),
               DB::raw('Group_Concat(DISTINCT(pur_grn.received_by)) as received_by')
            )->groupBy('material_inward.id')->get()->toArray();
            
        
        //material_dispatched_today
             // DB::enableQueryLog();
        $mat_dispatch_dc = Delivery_challan::leftJoin('challan_per_io','challan_per_io.delivery_challan_id','delivery_challan.id')->leftJoin('party','delivery_challan.party_id', 'party.id')
               ->leftJoin('gatepass', function($join) {
                $join->on('delivery_challan.id', '=', 'gatepass.challan_id');
                $join->where('gatepass.challan_type', '=', 'PPML/DCN/');
                })
                ->leftJoin('internal_order','internal_order.id','challan_per_io.io')
                ->leftJoin('item_category',function($join){
                    $join->on('internal_order.item_category_id','=','item_category.id');
                })
               ->leftJoin('waybill','waybill.challan_id','delivery_challan.id')
               ->leftJoin('material_outward','material_outward.gatepass','gatepass.id')
               ->select(DB::raw('group_concat(DISTINCT(challan_number)) as challan_number'),DB::raw('group_concat(DISTINCT(partyname)) as partyname'),
               DB::raw('group_concat((concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name))) as good_desc'),
               DB::raw('SUM(challan_per_io.good_qty) as good_qty'),
               DB::raw('group_concat(DISTINCT(gatepass.gatepass_number)) as gatepass_number'),'waybill_number','waybill_status',
               DB::raw('group_concat(DISTINCT(material_outward.material_outward_number)) as material_outward_number'),
               DB::raw('(select SUM(dc.total_amount) from delivery_challan dc 
                     LEFT JOIN party p ON p.`id` = dc.`party_id`
                    where dc.date ="'.$selected_date.'" and dc.party_id =`delivery_challan`.`party_id` GROUP by p.gst )AS wa_s'),
               'waybill.id as waybill_id'
            )->groupBy('delivery_challan.id')->whereRaw('delivery_date = "'.$selected_date.'"')
               // ->get()
               ;
               // print_r(DB::getQueryLog());die();
        $mat_idc_today=Internal_DC::leftJoin('gatepass', function($join) {
                $join->on('internal_dc.id', '=', 'gatepass.challan_id');
                $join->where('gatepass.challan_type', '=', 'PPML/IDC/');
                })
               // ->leftJoin('waybill','waybill.challan_id','internal_dc.id')
               ->leftJoin('material_outward','material_outward.gatepass','gatepass.id')
               ->select('idc_number as challan_number','internal_dc.dispatch_to as partyname',DB::raw('group_concat(DISTINCT(internal_dc.item_desc)) as good_desc'),
               DB::raw('SUM(internal_dc.item_qty) as good_qty'),
               DB::raw('group_concat(DISTINCT(gatepass.gatepass_number)) as gatepass_number'),
               // 'waybill_number','waybill.status as waybill_status',
               DB::raw('group_concat(DISTINCT(material_outward.material_outward_number)) as material_outward_number'))->groupBy('internal_dc.id')->whereRaw('internal_dc.date = "'.$selected_date.'"');
          
        //production done today
        $dailyprepresslog =DailyPlateProcess::where('job_details.is_supplied_plate','Press')
                ->where('is_plate_size','<>',0)
                ->whereRaw('prod__dailyprocess_planning.planned_date = "'.$selected_date.'"')
                ->leftjoin('job_card','prod__dailyprocess_planning.jc_id','job_card.id')
                ->leftjoin('internal_order','internal_order.id','job_card.io_id')
                ->leftjoin('prod__dailyplate_report','prod__dailyplate_report.plan_id','prod__dailyprocess_planning.id')
                ->leftjoin('job_details','job_details.id','internal_order.job_details_id')
                ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
                ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
                ->leftJoin('element_feeder','element_feeder.job_card_id','prod__dailyprocess_planning.jc_id')
                ->leftjoin('element_type','prod__dailyprocess_planning.element_id','element_type.id')
                ->select('job_card.id','job_card.job_number',
                'internal_order.reference_name',
                'internal_order.item_category_id',
                'party_reference.referencename','job_card.creative_name',
                'element_type.name as element_name','element_type.id as element_id',
                DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
                'element_feeder.plate_size as e_plate_size',
                DB::raw('(element_feeder.plate_sets * element_feeder.front_color) + (element_feeder.plate_sets * element_feeder.back_color) as total_plates'),
                'prod__dailyprocess_planning.no_of_plates as planned_plates',
                DB::raw('SUM(CASE WHEN prod__dailyplate_report.actual Is NULL THEN "0" ELSE prod__dailyplate_report.actual END) AS actual'),
                DB::raw('SUM(CASE WHEN prod__dailyplate_report.wastage Is NULL THEN "0" ELSE prod__dailyplate_report.wastage END) AS wastage'),
                DB::raw('group_concat(DISTINCT(CASE WHEN prod__dailyplate_report.reason Is NULL THEN "" ELSE prod__dailyplate_report.reason END)) AS reason')
                )
                 ->groupBy('prod__dailyplate_report.plan_id')
                ;
        $dailyprocesslog =PressDailyProcess::where('job_details.is_supplied_plate','Press')
            ->whereRaw('prod__press_dailyplanning.planned_date = "'.$selected_date.'"')
                ->leftjoin('job_card','prod__press_dailyplanning.jc_id','job_card.id')
                ->leftjoin('internal_order','internal_order.id','job_card.io_id')
                ->leftJoin('prod__machine_name','prod__machine_name.id','prod__press_dailyplanning.machine_id')
                ->leftjoin('prod__press_dailyplate_report','prod__press_dailyplate_report.plan_id','prod__press_dailyplanning.id')
                ->leftjoin('job_details','job_details.id','internal_order.job_details_id')
                ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
                ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
                ->leftJoin('element_feeder','element_feeder.job_card_id','prod__press_dailyplanning.jc_id')
                ->leftjoin('element_type','prod__press_dailyplanning.element_id','element_type.id')
                ->select('job_card.id','job_card.job_number',
                'internal_order.reference_name',
                'internal_order.item_category_id',
                'party_reference.referencename','job_card.creative_name',
                'element_type.name as element_name','element_type.id as element_id',
                DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
                'element_feeder.plate_size as e_plate_size',
                'prod__press_dailyplanning.left_imp',
                'prod__press_dailyplanning.id as plan_id',
                'prod__press_dailyplanning.planned_date',
                DB::raw('DATE_FORMAT(prod__press_dailyplanning.planned_date, "%d-%m-%Y") as planneddate'),
                'element_feeder.plate_sets as e_plate_set',
                'element_feeder.front_color as e_front_color',
                'element_feeder.back_color as e_back_color',
                DB::raw('(element_feeder.plate_sets * element_feeder.impression_per_plate) as total_plates'),
                'prod__press_dailyplanning.no_of_imp as planned_plates',
                DB::raw('SUM(CASE WHEN prod__press_dailyplate_report.actual_11am Is NULL THEN "0" ELSE prod__press_dailyplate_report.actual_11am END) AS actual_11am'),
                DB::raw('SUM(CASE WHEN prod__press_dailyplate_report.actual_2pm Is NULL THEN "0" ELSE prod__press_dailyplate_report.actual_2pm END) AS actual_2pm'),
                DB::raw('SUM(CASE WHEN prod__press_dailyplate_report.actual_6pm Is NULL THEN "0" ELSE prod__press_dailyplate_report.actual_6pm END) AS actual_6pm'),
                'prod__machine_name.name as machine',
                DB::raw('SUM(CASE WHEN prod__press_dailyplate_report.actual Is NULL THEN "0" ELSE prod__press_dailyplate_report.actual END) AS actual'),
                DB::raw('group_concat(DISTINCT(CASE WHEN prod__press_dailyplate_report.reason_11am Is NULL THEN "-" ELSE prod__press_dailyplate_report.reason_11am END)) AS reason_11am'),
                DB::raw('group_concat(DISTINCT(CASE WHEN prod__press_dailyplate_report.reason_2pm Is NULL THEN "-" ELSE prod__press_dailyplate_report.reason_2pm END)) AS reason_2pm'),
                DB::raw('group_concat(DISTINCT(CASE WHEN prod__press_dailyplate_report.reason_6pm Is NULL THEN "-" ELSE prod__press_dailyplate_report.reason_6pm END)) AS reason_6pm')
                )
                 ->groupBy('prod__press_dailyplanning.id')
                ;
       
        

        $purchase = PurchaseOrder::whereRaw('pr_purchase_order.po_date = "'.$selected_date.'"')
        ->leftjoin('vendor','vendor.id','pr_purchase_order.vendor_id')
        ->leftjoin('pur_purchase_req','pur_purchase_req.id','pr_purchase_order.indent_num_id')
        ->leftjoin('pr_purchase_order_details','pr_purchase_order.id','pr_purchase_order_details.pr_po_id')
        ->leftjoin('stock','stock.id','pr_purchase_order_details.item_name_id')
        ->leftjoin('tax_per_applicable','tax_per_applicable.id','pr_purchase_order_details.tax_percent_id')
        
        ->select('pr_purchase_order.po_num as po_number','pur_purchase_req.indent_num as pr_no','vendor.name as vendor',DB::raw('Group_Concat(stock.item_name) as item_name'),DB::raw('Sum(pr_purchase_order_details.item_qty) as qty'),DB::raw('sum(pr_purchase_order_details.item_rate) as rate'),DB::raw('(sum(pr_purchase_order_details.item_qty) * sum(pr_purchase_order_details.item_rate))+ (sum(pr_purchase_order_details.item_qty) * sum(pr_purchase_order_details.item_rate) * sum(tax_per_applicable.value/100)) as amount'))->groupBy('pr_purchase_order.id')->get()->toArray();
        

        $design_order =DesignOrder::whereRaw('DATE_FORMAT(design__order.created_at,"%Y-%m-%d") = "'.$selected_date.'"')          ->leftjoin('party_reference','design__order.reference_name','party_reference.id')
            ->leftJoin('internal_order','internal_order.id','design__order.io')
            ->leftJoin('item_category','item_category.id','design__order.item')
            ->leftJoin('design__work_allotment','design__work_allotment.design_id','design__order.id')
            ->leftJoin('employee__profile','employee__profile.id','design__work_allotment.work_emp_id')
            ->select('design__order.do_number','party_reference.referencename','internal_order.io_number',
            DB::raw('(CASE WHEN item_category.name = "Other" THEN CONCAT(item_category.name,":",IFNULL(design__order.other_item_desc,"")) ELSE item_category.name END) as itemname'),'design__order.no_pages','design__order.left_pages','design__order.other_item_desc','design__order.creative','employee__profile.name as alloted');
        

        // if($ref!=0)
        // {
        //     $countIO->where(function($query) use ($ref){
        //         $query->where('internal_order.reference_name','=',$ref);
        //     });
        //     $tax_invoice->where(function($query) use ($ref){
        //         $query->where('party.reference_name','=',$ref);
        //     });     
        //     $mat_dispatch_dc->where(function($query) use ($ref){
        //         $query->where('delivery_challan.reference_name','=',$ref);
        //     });
        //     $dailyprepresslog->where(function($query) use ($ref){
        //         $query->where('party_reference.id','=',$ref);
        //     });
        //     $dailyprocesslog->where(function($query) use ($ref){
        //         $query->where('party_reference.id','=',$ref);
        //     });
        //     $design_order->where(function($query) use ($ref){
        //         $query->where('design__order.reference_name','=',$ref);
        //     });  

        // }
        // if($party!=0)
        // {
        //     $tax_invoice->where(function($query) use ($party){
        //         $query->where('tax_invoice.party_id','=',$party);
        //     });  
        //     $mat_dispatch_dc->where(function($query) use ($party){
        //         $query->where('delivery_challan.party_id','=',$party);
        //     });              
        // }

        $countIO=$countIO->get()->toArray();
        $tax_invoice=$tax_invoice->get()->toArray();
        $mat_dispatch_dc=$mat_dispatch_dc->get()->toArray();
        $mat_idc_today = $mat_idc_today->get()->toArray();
        $dailyprepresslog=$dailyprepresslog->get()->toArray();
        $dailyprocesslog = $dailyprocesslog->get()->toArray();
        $design_order=$design_order->get()->toArray();

        if(!empty($request->input('date'))){
            $data = [
                    'foo' => 'bar',
                    'tstax'=>number_format($withtax, 2),
                    'tsnotax'=>number_format($withouttax, 2),
                    'tsmonth'=>number_format($withtaxformonth, 2),
                    'tsmonth_notax'=>number_format($withouttaxformonth, 2),
                    'total_IO'=>$countIO,
                    'total_tax'=>$tax_invoice,
                    'mir_today'=>$mir_today,
                    'do'=>$design_order,
                    'mat_dispatch_dc'=>$mat_dispatch_dc,
                    'mat_idc_today'=>$mat_idc_today,
                    'pre_press'=>$dailyprepresslog,
                    'dailyprocess'=>$dailyprocesslog,
                    'pur'=>$purchase,
                    'sel_date'=>$selected_date
                ];

                $pdfFilePath = "dailyreport(".$selected_date.").pdf";
                $pdf = PDF::loadView('email.emailtemplate', $data);

                return $pdf->stream($pdfFilePath);
                 // return view('email.emailtemplate',$data);
        }else{
            $message="Error Occurred!!";
            return redirect('/admin/daily/report')->with('error',$message);       
        }
    }
}

