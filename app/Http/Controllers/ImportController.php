<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Auth;
use Lang;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\advanceIO;
use App\Model\Payment;
use App\Model\Country;
use App\Model\Client_po;
use App\Model\Client_po_party;
use App\Model\Challan_per_io;
use App\Model\Delivery_challan;
use App\Model\Client_po_consignee;
use App\Model\Client_po_data;
use App\Model\Consignee;
use App\Model\Vehicle;
use App\Model\Master_item_category;
use App\Model\Stock\Stocks;
use App\Model\Goods_Dispatch;
use App\Model\InternalOrder;
use App\Model\State;
use App\Model\Party;
use App\Model\Reference;
use App\Model\Settings;
use App\Model\Binding_detail;
use App\Model\Binding_item;
use App\Model\Binding_form_labels;
use App\Model\JobCard;
use App\Model\ElementFeeder;
use App\Model\MasterMarketingPerson;
use App\Model\PaperType;
use App\Model\Dispatch_mode;
use App\Model\Raw_Material;
use App\Model\Users;
use App\Model\jobDetails;
use App\Model\ItemCategory;
use App\Model\Tax_Invoice;
use App\Model\Tax_Dispatch;
use App\Model\Tax;
use App\Model\IoType;
use App\Model\Hsn;
use App\Model\Unit_of_measurement;
use App\Model\TaxPercentageApplicable;
use App\Model\City;
use App\Custom\CustomHelpers;
use App\Model\Employee\EmployeeProfile;
use App\Model\Employee\EmployeeTask;
use App\Model\Employee\Attendance;
use App\Imports\Import;
use URL;
use \Carbon\Carbon;
use DateTime;


class ImportController extends Controller
{
    // 1 for stoping automatic genetation of challan number // bypass.
    // 0 for  automatic genetation of challan number // no  bypass.
    private $import_type=1;
    
    function get_status_id($text)
    {
        $arr = [
            "client"=>"0",
            "hsn"=>"1",
            "uom"=>"2",
            "paymentterm"=>"3",
            "goodsinvoicedispatch"=>"4",
            "taxinvoice"=>"5",
            "taxdispatch"=>"6",
            "clientpo"=>"7",
            "jobcard"=>"8",
            "internalorder"=>"9",
            "deliverychallan"=>"10",
            "task"=>"11",
            "attendance"=>"12"
        ];
        return $arr['$text'];
    }
    
    function getNameFromNumber($num) {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return $this->getNameFromNumber($num2 - 1) . $letter;
        }
        else {
            return $letter;
        }
    }
    public function validate_excel_format($a,$column_name,$sheet="")
    {
        ini_set('max_execution_time', 18000);
        $index=0;
        $column_name_err=0;
        $char = '0';
        $side=0;
        $error="";
        $data_inserted=0;
        if(count($a)==count($column_name))
        {
            foreach($a as $p=>$q)
            {
                
                if($q!=$column_name[$index])
                {
                    $column_name_err++;
                    $error=$error."Column Name not in provided format. Error At ".$sheet." ".$this->getNameFromNumber($char)."1.";
                }
                $index++;
                $char++;
            }
        }
        else
        {
            $column_name_err++;
            $error=$error."Column Name not in provided format. Please re-download the format and try again";
            $side=1;
        }
        return $column_name_err.'---'.$error.'---'.$side;
    }
    public function import_attendance(){
        return $this->import_data('attendance');
    }
    public function import_client(){
        return $this->import_data('client');
    }
    public function import_task(){
        return $this->import_data('task');
    }
    public function import_consignee(){
        return $this->import_data('consignee');
    }
    public function import_client_po(){
        return $this->import_data('clientpo');
    }
    public function import_io(){
        return $this->import_data('internalorder');
    }
    public function import_dc(){
        return $this->import_data('deliverychallan');
    }
    public function import_tax_invoice(){
        return $this->import_data('taxinvoice');
    }
    public function import_tax_dispatch(){
        return $this->import_data('taxdispatch');
    }
    public function import_job_card(){
        return $this->import_data('jobcard');
    }
    public function import_hsn(){
        return $this->import_data('hsn');
    }
    public function import_uom(){
        return $this->import_data('uom');
    }
    public function import_payment_term(){
        return $this->import_data('paymentterm');
    }
    public function import_goods_invoice_dispatch(){
        return $this->import_data('goodsinvoicedispatch');
    }
    public function import_stock(){
        return $this->import_data('stock');
    }

    public function import_data($id){
            if($id=="consignee"){
                $form="consigneewithparty";
            }
            else{
                 $form=$id;
            }
           
            $name=array(
                "client"=>"Client",
                "consignee"=>"Consignee",
                "hsn"=>"HSN",
                "uom"=>"Unit Of Measurement",
                "paymentterm"=>"Payment Term",
                "goodsinvoicedispatch"=>"Goods Invoice Dispatch",
                "taxinvoice"=>"Tax Invoice",
                "taxdispatch"=>"Tax Invoice Dispatch",
                "clientpo"=>"Client P.O.",
                "jobcard"=>"Job Card",
                "internalorder"=>"Internal Order",
                "stock"=>"Stock",
                "deliverychallan"=>"Delivery Challan",
                "task"=>"Task",
                "attendance"=>"Attendance"
            );
            $depend=array( 
                "client"=>"0",
                "consignee"=>"0",
                "hsn"=>"0",
                "uom"=>"0",
                "paymentterm"=>"0",
                "goodsinvoicedispatch"=>"0",
                "clientpo"=>"0",
                "taxinvoice"=>"0",
                "taxdispatch"=>"0",
                "jobcard"=>"0",
                "internalorder"=>"0",
                "deliverychallan"=>"0",
                "stock"=>"0",
                "task"=>"task",
                "attendance"=>"0"
            );
            if(isset($name[$id]))
            {
                $title='Import '.$name[$id];
                $dependent=$depend[$id];  
                $data=array(
                    'layout'=>'layouts.main',
                    'title'=>$title,'form'=>$form,
                    'depend'=>$dependent
                );
                if($depend[$id]=="party")
                {
                    $party=Party::select('id','partyname')->get();
                    $data = array_merge($data,['party'=>$party]);
                }
                if($depend[$id]=="task")
                {
                    $task=EmployeeProfile::select('id','name')->get();
                    $data = array_merge($data,['task'=>$task,'form'=>$form]);
                }
                return view('sections.import_form', $data);
            }
            else
                return abort(404);
        
    }
    public function download_client_po_consignee_format(Request $req){
        return $this->download_format($req,'consignee_cpo');
    }
    public function download_client_format(Request $req){
        return $this->download_format($req,'client');
    }
    public function download_task_format(Request $req){
        return $this->download_format($req,'task');
    }
    public function download_attendance_format(Request $req){
        return $this->download_format($req,'attendance');
    }
    public function download_consignee_format_withparty(Request $req){
        return $this->download_format($req,'consignee_withparty');
    }
    public function download_client_po_format(Request $req){
        return $this->download_format($req,'clientpo');
    }
    public function download_io_format(Request $req){
        return $this->download_format($req,'internalorder');
    }
    public function download_dc_format(Request $req){
        return $this->download_format($req,'deliverychallan');
    }
    public function download_tax_invoice_format(Request $req){
        return $this->download_format($req,'taxinvoice');
    }
    public function download_tax_dispatch_format(Request $req){
        return $this->download_format($req,'taxdispatch');
    }
    public function download_job_card_format(Request $req){
        return $this->download_format($req,'jobcard');
    }
    public function download_hsn_format(Request $req){
        return $this->download_format($req,'hsn');
    }
    public function download_uom_format(Request $req){
        return $this->download_format($req,'uom');
    }
    public function download_payment_term_format(Request $req){
        return $this->download_format($req,'paymentterm');
    }
    public function download_goods_invoice_dispatch_format(Request $req){
        return $this->download_format($req,'goodsinvoicedispatch');
    }
    public function download_consignee_format(Request $req){
        return $this->download_format($req,'consignee');
    }
    public function download_stock_format(Request $req){
        return $this->download_format($req,'stock');
    }


    public function download_format(Request $request,$name)
    {
        // print_r($name);die;
        $format_array = array(
            "client"=>"client_import_format.xlsx",
            "hsn"=>"hsn_import_format.xlsx",
            "uom"=>"unit_of_measurement_import_format.xlsx",
            "paymentterm"=>"payment_term_import_format.xlsx",
            "goodsinvoicedispatch"=>"goods_invoice_dispatch_import_format.xlsx",
            "taxinvoice"=>"tax_invoice_import_format.xlsx",
            "internalorder"=>"io_import_format.xlsx",
            "taxdispatch"=>"tax_dispatch_import_format.xlsx",
            "jobcard"=>"job_card_import_format.xlsx",
            "consignee_withparty"=>"consignee_import_format_withparty.xlsx",
            "consignee"=>"consignee_import_format.xlsx",
            "clientpo"=>"client_po_import_format.xlsx",
            "stock"=>"stock_import_format.xlsx",
            "deliverychallan"=>"delivery_challan_import_format.xlsx",
            "task"=>"employee_tasks_import_format.xlsx",
            'consignee_cpo'=>'consignee_import_format.xlsx',
            "attendance"=>'attendance_import_format.xls'
        );
        
        $action = Url::previous();
        $filename = "";
        if(array_key_exists($name,$format_array))
        {
            $filename = $format_array[$name];
        }
        else
        {
            return redirect($action)->with('error', 'File is not available.');
        }
        $file= public_path(). "/download/".$filename;
        $headers = array(
                'Content-Type: application/xlsx'
                );
        return response()->download($file, $filename, $headers);
    }
    public function import_goods_invoice_dispatch_db(Request $request)
    {
       $redirect_to = '/import/data/goodsinvoicedispatch';
       $this->validate($request,
           [
               'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
           ],
           [
               'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
               'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
           ]
       );
       try
       {
           if($request->file('excel'))                
           {
               $path = $request->file('excel');
               $data = Excel::toArray(new Import(),$path);
               $error="";
               $total_error=0;
               if($data && count($data)>=1)
               {
                   $v= $data[0];
               }
               else
               {
                   $request->session()->flash('importerrors',"");
                   DB::rollBack();
                   return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
               }
                $column_name_format=array('dispatch_mode','name','contact_number','gst_number','address');
                

                $out= $this->validate_excel_format($v[0],$column_name_format,'hsn');
                $error= $error.explode("---",$out)[1];
                $column_name_err=explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);
                $dispatch_mode1 = array_search("dispatch_mode",$column_name_format);
                $name1 = array_search("name",$column_name_format);
                $contact_number1 = array_search("contact_number",$column_name_format);
                $gst_number1 = array_search("gst_number",$column_name_format);
                $address1 = array_search("address",$column_name_format);
   
               if($column_name_err==0)
               {
                   DB::beginTransaction();
                   $data_inserted=0;
                   for($i=1;$i<count($v);$i++)   
                   {
                       $char = '0';
                       $fl=0;
                       foreach ($v[$i] as $k=>$v1)
                       {
                            if($char==3 && strtolower($v[$i][$dispatch_mode1])=='self' )
                                break;
                            if($v1 == "")
                            {    
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            }
                           $char++;
                       }
                       $total_error=$total_error+$fl;
                       if($fl==0)
                       {
                           $data_err=0;
                           $item = Dispatch_mode::where('name','like',$v[$i][$dispatch_mode1])
                           ->get('value')->first();
                           if(!$item)
                           {
                               $error = $error." Dispatch Mode at ".$this->getNameFromNumber($dispatch_mode1).($i+1)." Not Exist.<br/>";
                               $data_err++;
                           }
                            if($this->import_type==1 && ($v[$i][$gst_number1]=='' || $v[$i][$gst_number1]==NULL ))
                            {
                                $v[$i][$gst_number1]='';
                            }
                            
                           $contact =Goods_dispatch::where('contact','like',$v[$i][$contact_number1])->get('id')->first(); 
                           $gst =Goods_dispatch::where('gst','like',$v[$i][$gst_number1])->get('id')->first(); 
                           if($this->import_type==0)
                           {
                                if($gst)
                                {                           
                                    $error = $error." GST Number at ".$this->getNameFromNumber($gst_number1).($i+1)." Already Exist.<br/>";
                                    $data_err++;
                                }
                                if($contact)
                                {                           
                                    $error = $error." Contact Number at ".$this->getNameFromNumber($contact_number1).($i+1)." Already Exist.<br/>";
                                    $data_err++;
                                }
                                if(strlen($v[$i][$gst_number1])!=15)
                                {
                                    $error = $error." GST Number at ".$this->getNameFromNumber($gst_number1).($i+1)." must have 15 characters.<br/>";
                                    $data_err++;
                                }
                                if(!ctype_alnum($v[$i][$gst_number1]))
                                {
                                    $error = $error." GST Number at ".$this->getNameFromNumber($gst_number1).($i+1)." must be alphanumeric.<br/>";
                                    $data_err++;
                                }
                            }
                            else
                            {
                                if($v[$i][$contact_number1] == 'NA')
                                    $v[$i][$contact_number1]=NULL;
                            }
                           $total_error = $total_error + $data_err;
                           if($data_err==0)
                            { 
                                $timestamp = date('Y-m-d G:i:s');   
                                Goods_Dispatch::insert([
                                    'id' => NULL,
                                    'mode' => $item->value,
                                    'courier_name' => $v[$i][$name1],
                                    'contact' => $v[$i][$contact_number1],
                                    'gst' =>  $v[$i][$gst_number1],
                                    'address' =>  $v[$i][$address1],
                                    'created_by' => Auth::id(),
                                    'is_active' =>1,
                                    'created_time' => $timestamp,
                                ]);
                                $data_inserted++;
                            
                            }
                       }
                       else
                           $error=$error."<br/>";
                   }
               }
               else
                   $error = $error." No data is inserted.";
           }
           else
           {
               $request->session()->flash('importerrors',"");
               DB::rollBack();
               return redirect($redirect_to)->with('error', 'Please upload file');
           }
           if($total_error==0)
           {
               $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
               DB::commit();
               return redirect($redirect_to)->with('success', 'Goods Dispatch Invoice has been imported!');
           }
           else if($column_name_err!=0)
           {
               $request->session()->flash('importerrors', $error);
               DB::rollBack();
               return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
           }
           else
           {   
               $request->session()->flash('importerrors', $error);
               DB::rollBack();
               return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
           }
       }
       catch (Exception $e)
       {   
       }
    }

    public function import_payment_term_db(Request $request)
    {
        $redirect_to = '/import/data/paymentterm';
        $this->validate($request,
            [
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
            ]
        );
        try
        {
            if($request->file('excel'))                
            {
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path);
                $error="";
                $total_error=0;
                if($data && count($data)>=1)
                {
                    $v= $data[0];
                }
                else
                {
                    $request->session()->flash('importerrors',"");
                    DB::rollBack();
                    return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                }
    
                $column_name_format=array('payment_term');
                $data_inserted=0;

                $out= $this->validate_excel_format($v[0],$column_name_format,'payment_term');
                $error= $error.explode("---",$out)[1];
                $column_name_err=explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);
                    $payment_term1 = array_search('payment_term',$column_name_format);

                if($column_name_err==0)
                {
                    DB::beginTransaction();
                    for($i=1;$i<count($v);$i++)   
                    {
                        $char = '0';
                        $fl=0;
                        foreach ($v[$i] as $k=>$v1)
                        {
                            if($v1 == "")
                            {    
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            }
                            $char++;
                        }
                        $total_error=$total_error+$fl;
                        if($fl==0)
                        {
                                $timestamp = date('Y-m-d G:i:s');
                                Payment::insert(
                                    [
                                        'id' => NULL,
                                        'created_by' => Auth::id(),
                                        'is_active' =>1,
                                        'value'=> $v[$i][$payment_term1],
                                        'created_at' => $timestamp,
                                    ]
                                );
                                $data_inserted++;

                        }
                        else
                            $error=$error."<br/>";
                    }
                }
                else
                    $error = $error." No data is inserted.";
            }
            else
            {
                $request->session()->flash('importerrors',"");
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Please upload file');
            }
            if($total_error==0)
            {
                $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
                DB::commit();
                return redirect($redirect_to)->with('success', 'Payment Terms has been imported!');
            }
            else if($column_name_err!=0)
            {
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
            else
            {   
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
        }
        catch (Exception $e)
        {   
        }
    }
    public function import_uom_db(Request $request)
    {
        $redirect_to = '/import/data/uom';
        $this->validate($request,
            [
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
            ]
        );
        try
        {
            if($request->file('excel'))                
            {
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path);
                $error="";
                $total_error=0;
                if($data && count($data)>=1)
                {
                    $v= $data[0];
                }
                else
                {
                    $request->session()->flash('importerrors',"");
                    DB::rollBack();
                    return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                }
    
                $column_name_format=array('unit_of_measurement_name');
                
                $out= $this->validate_excel_format($v[0],$column_name_format,'uom');
                $error= $error.explode("---",$out)[1];
                $column_name_err=explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);
                    $uom1 = array_search('unit_of_measurement_name',$column_name_format);

                if($column_name_err==0)
                {
                    $data_inserted=0;

                    DB::beginTransaction();
                    for($i=1;$i<count($v);$i++)   
                    {
                        $char = '0';
                        $fl=0;
                        foreach ($v[$i] as $k=>$v1)
                        {
                            if($v1 == "")
                            {    
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            }
                            $char++;
                        }
                        $total_error=$total_error+$fl;
                        if($fl==0)
                        {
                            $data_err=0;
                            if($data_err==0)
                                {
                                    $timestamp = date('Y-m-d G:i:s');
                                    Unit_of_measurement::insert(
                                        [
                                            'id' => NULL,
                                            'created_by' => Auth::id(),
                                            'is_active' =>1,
                                            'uom_name'=> $v[$i][$uom1],
                                            'created_at' => $timestamp,
                                        ]
                                    );
                                    $data_inserted++;

                        
                                }
                        }
                        else
                            $error=$error."<br/>";
                    }
                }
                else
                    $error = $error." No data is inserted.";
            }
            else
            {
                $request->session()->flash('importerrors',"");
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Please upload file');
            }
            if($total_error==0)
            {
                $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
                DB::commit();
                return redirect($redirect_to)->with('success', 'Unit Of Measurement has been imported!');
            }
            else if($column_name_err!=0)
            {
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
            else
            {   
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
        }
        catch (Exception $e)
        {   
        }
    }
    public function import_hsn_db(Request $request)
    {
        $redirect_to = '/import/data/hsn';
        $this->validate($request,
            [
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
            ]
        );
        try
        {
            if($request->file('excel'))                
            {
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path);
                $error="";
                $total_error=0;
                if($data && count($data)>=1)
                {
                    $v= $data[0];
                }
                else
                {
                    $request->session()->flash('importerrors',"");
                    DB::rollBack();
                    return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                }
    
                $column_name_format=array('item_name','hsn','gst_rate','item_description');
                
                $out= $this->validate_excel_format($v[0],$column_name_format,'hsn');
                $error= $error.explode("---",$out)[1];
                $column_name_err=explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);
                    $item_name1 = array_search('item_name',$column_name_format);
                    $hsn1 = array_search('hsn',$column_name_format);
                    $gst_rate1 = array_search('gst_rate',$column_name_format);
                    $item_description1 = array_search('item_description',$column_name_format);
                    
                if($column_name_err==0)
                {
                    $data_inserted=0;

                    DB::beginTransaction();
                    for($i=1;$i<count($v);$i++)   
                    {
                        $char = '0';
                        $fl=0;
                        foreach ($v[$i] as $k=>$v1)
                        {
                            if($char==2 && $v1=="")
                            {
                                $char++;
                                    $v[$i][$gst_rate1]=0;
                                continue;
                            }
                            if($v1 == "")
                            {    
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            }
                            $char++;
                        }
                        $total_error=$total_error+$fl;
                        if($fl==0)
                        {
                            $data_err=0;
                            
                            if($v[$i][$item_name1]=='')
                            {
                                $error = $error." Item Name at ".$this->getNameFromNumber($item_name1).($i+1)." Cannot be Empty.<br/>";
                                $data_err++;
                            }
                            $gst = $v[$i][$gst_rate1];
                            if($gst=="0" || $gst=="5" || $gst=="12" || $gst=="18" || $gst=="28" ){

                            }
                            else
                            {
                                $error = $error." GST Rate at ".$this->getNameFromNumber($gst_rate1).($i+1)." Not Exist.<br/>";
                                $data_err++;

                            }

                            $total_error = $total_error + $data_err;
                            if($data_err==0)
                                {
                                    $timestamp = date('Y-m-d G:i:s');
                                    Hsn::insert([
                                        'id' => NULL,
                                        'item_id' =>$v[$i][$item_name1],
                                        'hsn' => $v[$i][$hsn1],
                                        'gst_rate' => $v[$i][$gst_rate1],
                                        'item_description' => $v[$i][$item_description1],
                                        'created_by' => Auth::id(),
                                        'is_active' =>1,
                                        'created_time' => $timestamp,
                                    ]);
                                    $data_inserted++;

                                }
                        }
                        else
                            $error=$error."<br/>";
                    }
                }
                else
                    $error = $error." No data is inserted.";
            }
            else
            {
                $request->session()->flash('importerrors',"");
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Please upload file');
            }
            if($total_error==0)
            {
                $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
                DB::commit();
                return redirect($redirect_to)->with('success', 'HSN has been imported!');
            }
            else if($column_name_err!=0)
            {
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
            else
            {   
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
        }
        catch (Exception $e)
        {   
        }
    }

   
//    job card import commented not reuired
    /*
        public function import_job_card_db(Request $request)
        {
            $redirect_to = '/import/data/jobcard';
            $this->validate($request,
                [
                    'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
                ],
                [
                    'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                    'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
                ]
            );
            try
            {
                if($request->file('excel'))                
                {

                    $path = $request->file('excel');
                    $data = Excel::toArray(new Import(),$path);
                    $error="";
                    $total_error=0;
                    if($data && count($data)>=3)
                    {
                        $v= $data[0];
                        $w= $data[1];
                        $x= $data[2];
                        $y= $data[3];
                    }
                    else
                    {
                        $request->session()->flash('importerrors',"");
                        return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                    }
                    $column_name_format=array('internal_order_number','job_quantity','creative_name','open_size',
                    'open_size_dimension','close_size','close_size_dimension','remarks_if_any','job_sample_received',
                    'Packing_instruction_description','created_date'
                    );
                    if($this->import_type==1)
                    {
                        $jc_num=array('job_card_number');
                        $column_name_format=array_merge($column_name_format,$jc_num);
                    }
                    $out= $this->validate_excel_format($v[0],$column_name_format,'job_card');
                    $error= $error.explode("---",$out)[1];
                    $column_name_err=explode("---",$out)[0];
                    $stop=explode("---",$out)[2];
                    if($stop==1)    
                        return redirect($redirect_to)->with('error',$error);

                    $total_error=$total_error+$column_name_err;
                    $internal_order_number1=array_search("internal_order_number",$column_name_format);
                    $job_quantity1=array_search("job_quantity",$column_name_format);
                    $creative_name1=array_search("creative_name",$column_name_format);
                    $open_size1=array_search("open_size",$column_name_format);
                    $close_size1=array_search("close_size",$column_name_format);
                    $open_size_dimension1=array_search("open_size_dimension",$column_name_format);
                    $close_size_dimension1=array_search("close_size_dimension",$column_name_format);
                    
                    $remarks_if_any1=array_search("remarks_if_any",$column_name_format);
                    $job_sample_received1=array_search("job_sample_received",$column_name_format);
                    $job_card_number1=array_search("job_card_number",$column_name_format);
                    $created_date1=array_search("created_date",$column_name_format);
                    
                    $Packing_instruction_description1=array_search("Packing_instruction_description",$column_name_format);
                    $column_name_format=array('internal_order_number','text','text_plate_size','text_plate_sets',
                        'text_impression_plate_sets','text_front_colour','text_back_colour','text_no_of_pages','cover',
                        'cover_plate_size','cover_plate_sets','cover_impression_plate_sets','cover_front_colour',
                        'cover_back_colour','posteen','posteen_plate_size','posteen_plate_sets','posteen_impression_plate_sets',
                        'posteen_front_colour','posteen_back_colour','seperator','seperator_plate_size','seperator_plate_sets',
                        'seperator_impression_plate_sets','seperator_front_colour','seperator_back_colour','hard_case_stand',
                        'hard_case_stand_plate_size','hard_case_stand_plate_sets','hard_case_stand_impression_plate_sets',
                        'hard_case_stand_front_colour','hard_case_stand_back_colour'
                    );
                    $out=$this->validate_excel_format($w[0],$column_name_format,'element');
                    $error= $error.explode("---",$out)[1];
                    $column_name_err=explode("---",$out)[0];
                    $stop=explode("---",$out)[2];
                    if($stop==1)    
                        return redirect($redirect_to)->with('error',$error);
                    else
                    {
                        $total_error=$total_error+$column_name_err;
                        $internal_order_number2=0;$text2=1;$text_plate_size2=2;$text_plate_sets2=3;$text_impression_plate_sets2=4;
                        $text_front_colour2=5;$text_back_colour2=6;$text_no_of_pages2=7;$cover2=8;$cover_plate_size2=9;
                        $cover_plate_sets2=10;$cover_impression_plate_sets2=11;$cover_front_colour2=12;$cover_back_colour2=13;
                        $posteen2=14;$posteen_plate_size2=15;$posteen_plate_sets2=16;$posteen_impression_plate_sets2=17;
                        $posteen_front_colour2=18;$posteen_back_colour2=19;$seperator2=20;$seperator_plate_size2=21;
                        $seperator_plate_sets2=22;$seperator_impression_plate_sets2=23;$seperator_front_colour2=24;
                        $seperator_back_colour2=25;$hard_case_stand2=26;$hard_case_stand_plate_size2=27;$hard_case_stand_plate_sets2=28;
                        $hard_case_stand_impression_plate_sets2=29;$hard_case_stand_front_colour2=30;$hard_case_stand_back_colour2=31;
                    }
                    $column_name_format=array('internal_order_number','text','text_paper_size','text_paper_type',
                        'text_paper_gsm','text_paper_brand','text_no_of_sheet','cover','cover_paper_size',
                        'cover_paper_type','cover_paper_gsm','cover_paper_brand','cover_no_of_sheet','posteen',
                        'posteen_paper_size','posteen_paper_type','posteen_paper_gsm','posteen_paper_brand',
                        'posteen_no_of_sheet','seprator','seprator_paper_size','seprator_paper_type',
                        'seprator_paper_gsm','seprator_paper_brand','seprator_no_of_sheet','hard_case_stand',
                        'hard_case_stand_paper_size','hard_case_stand_paper_type','hard_case_stand_paper_gsm',
                        'hard_case_stand_paper_brand','hard_case_stand_no_of_sheet','common','common_paper_size',
                        'common_paper_type','common_paper_gsm','common_paper_mill','common_no_of_sheet'
                    );
                                    
                    $out= $this->validate_excel_format($x[0],$column_name_format,'raw_material');
                    $error= $error.explode("---",$out)[1];
                    $column_name_err=explode("---",$out)[0];
                    $stop=explode("---",$out)[2];
                    if($stop==1)    
                        return redirect($redirect_to)->with('error',$error);
            
                    $total_error=$total_error+$column_name_err;

                        $internal_order_number3=0;$text3=1;$text_paper_size3=2;$text_paper_type3=3;
                        $text_paper_gsm3=4;$text_paper_brand3=5;$text_no_of_sheet3=6;$cover3=7;$cover_paper_size3=8;
                        $cover_paper_type3=9;$cover_paper_gsm3=10;$cover_paper_brand3=11;$cover_no_of_sheet3=12;$posteen3=13;
                        $posteen_paper_size3=14;$posteen_paper_type3=15;$posteen_paper_gsm3=16;$posteen_paper_brand3=17;
                        $posteen_no_of_sheet3=18;$seperator3=19;$seperator_paper_size3=20;$seperator_paper_type3=21;
                        $seperator_paper_gsm3=22;$seperator_paper_brand3=23;$seperator_no_of_sheet3=24;$hard_case_stand3=25;
                        $hard_case_stand_paper_size3=26;$hard_case_stand_paper_type3=27;$hard_case_stand_paper_gsm3=28;
                        $hard_case_stand_paper_brand3=29;$hard_case_stand_no_of_sheet3=30;$common3=31;$common_paper_size3=32;
                        $common_paper_type3=33;$common_paper_gsm3=34;$common_paper_mill3=35;$common_no_of_sheet3=36;
                        
                    $column_name_format=array('internal_order_number','text','cover','posteen','seperator','hard_case_stand',
                    'form','leaflet/pamplet','folder','dangler','sticker','poster','banner','tent_card','paper_bags',
                    'other'
                    );
                    $out= $this->validate_excel_format($y[0],$column_name_format,'binding_details');
                    $error= $error.explode("---",$out)[1];
                    $column_name_err=explode("---",$out)[0];
                    $stop=explode("---",$out)[2];
                    if($stop==1)    
                        return redirect($redirect_to)->with('error',$error);
            
                    $total_error=$total_error+$column_name_err;

                    $internal_order_number4=0;$text4=1;$cover4=2;$posteen4=3;$seperator4=4;$hard_case_stand4=5;
                    $form4=6;$leaflet_pamplet4=7;$folder4=8;$dangler4=9;$sticker4=10;$poster4=11;$banner4=12;
                    $tent_card4=13;$paper_bags4=15;$other4=16;
                    $last_excel_cursor=array(1,1,1,1,1
                                            ,1,1,1,1,1
                                            ,1,1,1,1,1);
                    $binding_form_label = Binding_form_labels::all()->toArray();
                    for($i=4;$i<count($data);$i++)
                    {
                        $char=1;
                        $binding_details =explode(',',Binding_item::where('id','=',($i-3))->get('form_labels_id')->first()->form_labels_id);
                        $till=count($binding_details);
                        for($j=0;$j<count($binding_details);$j++)
                        {
                            if($data[$i][0][(($j*2)+1)]!=strtolower(str_replace(' ','_',$binding_form_label[$binding_details[$j]-1]['labels'])))  
                            {
                                $column_name_err++;
                                $error=$error."Column Name not in provided format. Error At binding_details_".$column_name_format[$i-3]." ".$this->getNameFromNumber($char)."1.";
            
                            }
                            $char++;
                            if($data[$i][0][($j*2+2)]!=strtolower(str_replace(' ','_',$binding_form_label[$binding_details[$j]-1]['labels'])."_remark"))  
                            {
                                $column_name_err++;
                                $error=$error."Column Name not in provided format. Error At binding_details_".$column_name_format[$i-3]." ".$this->getNameFromNumber($char)."1.";
            
                            }
                            $char++;

                        }
                    }
                    $total_error=$total_error+$column_name_err;
                    $jc_id_global=-1;
                    $io_id_global=-1;
                    $item_id_global=-1;
                    $data_inserted=0;
                    if($column_name_err==0)
                    {
                        DB::beginTransaction();
                        for($i=1;$i<count($v);$i++)   
                        {
                            $char = '0';
                            $fl=0;
                            foreach ($v[$i] as $k=>$v1)
                            {
                                if($char==$created_date1)
                                    continue;
                                if($v1 == "")
                                {    
                                    $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                    $fl++;
                                }
                                $char++;
                            }
                            $total_error=$total_error+$fl;
                            if($fl==0)
                            {
                                $data_err=0;
                                $io =InternalOrder::join('job_details','internal_order.job_details_id', '=', 'job_details.id')
                                ->leftJoin('item_category','internal_order.item_category_id','=','item_category.id')
                                ->where('io_number','like',$v[$i][$internal_order_number1])
                                ->get([
                                    'internal_order.id',
                                    'internal_order.item_category_id',
                                    'internal_order.job_details_id',
                                    'job_details.qty',
                                    'item_category.name',
                                    'job_details.marketing_user_id'
                                    ])->first();
                                if(!$io)
                                {
                                    $error = $error." Internal Order Number at ".$this->getNameFromNumber($internal_order_number1).($i+1)." Not Exist.<br/>";
                                    $data_err++;
                                }else
                                {
                                    $jc = JobCard::where('io_id','=',$io->id)->get('id')->toArray();
                                    
                                    if($jc)
                                    {
                                        $error = $error." Internal Order Number at ".$this->getNameFromNumber($internal_order_number1).($i+1)." already contains a job card.<br/>";
                                        $data_err++;
                                        $total_error = $total_error + $data_err;

                                        continue;
                                    }
                                }
                                if(strtolower($v[$i][$job_sample_received1])=='yes')
                                    $sample_receieved=1;
                                else if(strtolower($v[$i][$job_sample_received1])=='no')
                                    $sample_receieved=0;
                                else
                                {
                                    $error = $error." Job Sample Received at ".$this->getNameFromNumber($job_sample_received1).($i+1)." can be 'yes' or 'no'.<br/>";
                                    $data_err++;   
                                }
                                if($v[$i][$created_date1]!='')
                                {
                                    $timestamp=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$created_date1]);
                                    $timestamp=date('Y-m-d G:i:s',$timestamp);
                                }
                                else
                                    $timestamp =  date('Y-m-d G:i:s');
                                
                                $total_error = $total_error + $data_err;
                                if($data_err==0)
                                {
                                    try {                                    
                                            
                                            $jc_number = Settings::where('name','Job_Card_Prefix')->first()->value;
                                            $jc_id= JobCard::insertGetId(
                                                [
                                                    'id' => NULL,
                                                    'job_number'=>'',
                                                    'io_id' =>$io->id,
                                                    'creative_name' => $v[$i][$creative_name1],
                                                    'job_qty' =>$v[$i][$job_quantity1],
                                                    'open_size' => $v[$i][$open_size1],
                                                    'close_size' =>$v[$i][$close_size1],
                                                    'dimension' =>$v[$i][$dimension1],
                                                    'job_sample_received' => $sample_receieved,
                                                    'remarks' => $v[$i][$remarks_if_any1],
                                                    'description' => $v[$i][$Packing_instruction_description1],
                                                    'item_category_id' =>$io->item_category_id,
                                                    'created_by' =>Auth::id(),
                                                    'is_active' =>1,
                                                    'created_time' => $timestamp,
                                                ]
                                            );
                                            $jc_id_global = $jc_id;
                                            $io_id_global = $io->id;
                                            $item_id_global = $io->item_category_id;
                                            
                                            if ( ! $jc_id)
                                            {
                                                DB::rollBack();
                                                return redirect('/jobcard/create')->with('error','some error occurred')->withInput();
                                            }
                                            else{
                                                if($this->import_type==0)
                                                    $prefix = $jc_number ."/".$jc_id;
                                                else
                                                {
                                                    $prefix = $v[$i][$job_card_number1];//sheet data
                                                }
                                                $jc_number= JobCard::where('id',$jc_id)->update(
                                                    [
                                                        'job_number'=> $prefix
                                                    ]
                                                );
                                            

                                            }
                                        $data_inserted = $data_inserted+1;
                                    }catch(Exception $e)
                                    {

                                    }
                                }
                            }
                            else
                                $error=$error."<br/>";
                            $char=0;
                            for($j=0;$j<count($w[$i]);$j++)
                            {
                                if($j==$text2 && $w[$i][$j] == 0)
                                    $j=$cover2-1;
                                else if($j==$cover2 && $w[$i][$j] == 0)
                                    $j=$posteen2-1;
                                else if($j==$posteen2 && $w[$i][$j] == 0)
                                    $j=$seperator2-1;
                                else if($j==$seperator2 && $w[$i][$j] == 0)
                                    $j=$hard_case_stand2-1;
                                else if($j==$hard_case_stand2 && $w[$i][$j] == 0)
                                    break;
                                else if($w[$i][$j] == "")
                                {    
                                    $error=$error."Empty Cell at element".$this->getNameFromNumber($char).($i+1).". "; 
                                    $fl++;
                                }
                                $char++;
                            }
                            $total_error=$total_error+$fl;
                            if($fl==0)
                            {
                                $data_err=0;
                                if($v[$i][$internal_order_number1] != $w[$i][$internal_order_number2])
                                {
                                    $error=$error."Internal Order Number at element ".$this->getNameFromNumber($internal_order_number2).($i+1)." Not Exist. "; 
                                    $data_err++;

                                }
                                if($w[$i][$text2]==1)
                                {
                                    $text_entry=1;
                                    if(!is_numeric($w[$i][$text_front_colour2]))
                                    {
                                        $error=$error."Text Front Colour at element ".$this->getNameFromNumber($text_front_colour2).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    if(!is_numeric($w[$i][$text_back_colour2]))
                                    {
                                        $error=$error."Text Back Colour at element ".$this->getNameFromNumber($text_back_colour2).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    if(!is_numeric($w[$i][$text_no_of_pages2]) || $w[$i][$text_no_of_pages2]<0)
                                    {
                                        $error=$error."Text No. of pages at element ".$this->getNameFromNumber($text_no_of_pages2).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    
                                }
                                else if($w[$i][$text2]==0)
                                {
                                    $text_entry=0;
                                }
                                else
                                {
                                    $error = $error." text at element".$this->getNameFromNumber($text2).($i+1)." can be 1 or 0.<br/>";
                                    $data_err++;
        
                                }
                                if($w[$i][$cover2]==1)
                                {
                                    $cover_entry=1;
                                    if(!is_numeric($w[$i][$cover_front_colour2]))
                                    {
                                        $error=$error."Cover Front Colour at element ".$this->getNameFromNumber($cover_front_colour2).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    if(!is_numeric($w[$i][$cover_back_colour2]))
                                    {
                                        $error=$error."Cover Back Colour at element ".$this->getNameFromNumber($cover_back_colour2).($i+1)." should be number. "; 
                                        $data_err++;
                                    }

                        
                                }
                                else if($w[$i][$cover2]==0)
                                {
                                    $cover_entry=0;

                                }
                                else
                                {
                                    $error = $error." Cover at element".$this->getNameFromNumber($cover2).($i+1)." can be 1 or 0.<br/>";
                                    $data_err++;
        
                                }
                                if($w[$i][$posteen2]==1)
                                {
                                    $posteen_entry=1;
                                    if(!is_numeric($w[$i][$posteen_front_colour2]))
                                    {
                                        $error=$error."Posteen Front Colour at element ".$this->getNameFromNumber($posteen_front_colour2).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    if(!is_numeric($w[$i][$posteen_back_colour2]))
                                    {
                                        $error=$error."Posteen Back Colour at element ".$this->getNameFromNumber($posteen_back_colour2).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                        
                                }
                                else if($w[$i][$posteen2]==0)
                                {
                                    $posteen_entry=0;
                                }
                                else
                                {
                                    $error = $error." Posteen at element".$this->getNameFromNumber($posteen2).($i+1)." can be 1 or 0.<br/>";
                                    $data_err++;
        
                                }
                                if($w[$i][$seperator2]==1)
                                {
                                    $seperator_entry=1;
                                    if(!is_numeric($w[$i][$seperator_front_colour2]))
                                    {
                                        $error=$error."Seperator Front Colour at element ".$this->getNameFromNumber($seperator_front_colour2).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    if(!is_numeric($w[$i][$seperator_back_colour2]))
                                    {
                                        $error=$error."Seperator Back Colour at element ".$this->getNameFromNumber($seperator_back_colour2).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                        
                        
                                }
                                else if($w[$i][$seperator2]==0)
                                {
                                    $seperator_entry=0;
                                }
                                else
                                {
                                    $error = $error." Seperator at element".($seperator2).($i+1)." can be 1 or 0.<br/>";
                                    $data_err++;
        
                                }
                                if($w[$i][$hard_case_stand2]==1)
                                {
                                    $hard_case_stand_entry=1;
                                    if(!is_numeric($w[$i][$hard_case_stand_front_colour2]))
                                    {
                                        $error=$error."Hard Case Stand Front Colour at element A".$this->getNameFromNumber($hard_case_stand_front_colour2-26).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    if(!is_numeric($w[$i][$hard_case_stand_back_colour2]))
                                    {
                                        $error=$error."Hard Case Stand Back Colour at element A".$this->getNameFromNumber($hard_case_stand_back_colour2-26).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                        
                        
                                }
                                else if($w[$i][$hard_case_stand2]==0)
                                {
                                    $hard_case_stand_entry=0;
                                }
                                else
                                {
                                    $error = $error." Hard Case Stand at ".$this->getNameFromNumber($hard_case_stand2).($i+1)." can be 1 or 0.<br/>";
                                    $data_err++;
                                }
                                if($data_err==0)
                                {
                                    if($text_entry==1)
                                    {
                                        ElementFeeder::insert(
                                            [
                                                'id' => NULL,
                                                'element_type_id' => '1',
                                                'job_card_id' =>$jc_id_global,
                                                'plate_size' =>          $w[$i][$text_plate_size2],
                                                'plate_sets' =>          $w[$i][$text_plate_sets2],
                                                'impression_per_plate' =>$w[$i][$text_impression_plate_sets2],
                                                'front_color' =>         $w[$i][$text_front_colour2],
                                                'back_color' =>          $w[$i][$text_back_colour2],
                                                'no_of_pages' =>         $w[$i][$text_no_of_pages2],
                                                'created_by' =>Auth::id(),
                                                'created_time' => $timestamp,
                                            ]
                                        );
                                    }
                                    if($cover_entry==1)
                                    {
                                        ElementFeeder::insert(
                                            [
                                                'id' => NULL,
                                                'element_type_id' => '2',
                                                'job_card_id' =>$jc_id_global,
                                                'plate_size' =>          $w[$i][$cover_plate_size2],
                                                'plate_sets' =>          $w[$i][$cover_plate_sets2],
                                                'impression_per_plate' =>$w[$i][$cover_impression_plate_sets2],
                                                'front_color' =>         $w[$i][$cover_front_colour2],
                                                'back_color' =>          $w[$i][$cover_back_colour2],
                                                'no_of_pages' =>         NULL,
                                                'created_by' =>Auth::id(),
                                                'created_time' =>$timestamp,
                                            ]
                            
                                        );
                                    }
                                    if($posteen_entry==1)
                                    {
                                        ElementFeeder::insert(
                                            [
                                                'id' => NULL,
                                                'element_type_id' => '3',
                                                'job_card_id' =>$jc_id_global,
                                                'plate_size' =>          $w[$i][$posteen_plate_size2],
                                                'plate_sets' =>          $w[$i][$posteen_plate_sets2],
                                                'impression_per_plate' =>$w[$i][$posteen_impression_plate_sets2],
                                                'front_color' =>         $w[$i][$posteen_front_colour2],
                                                'back_color' =>          $w[$i][$posteen_back_colour2],
                                                'no_of_pages' =>         NULL,
                                                'created_by' =>Auth::id(),
                                                'created_time' =>$timestamp,
                                            ]
                            
                                        );
                                    }
                                    if($seperator_entry==1)
                                    {
                                        ElementFeeder::insert(
                                            [
                                                'id' => NULL,
                                                'element_type_id' => '4',
                                                'job_card_id' =>$jc_id_global,
                                                'plate_size' =>          $w[$i][$seperator_plate_size2],
                                                'plate_sets' =>          $w[$i][$seperator_plate_sets2],
                                                'impression_per_plate' =>$w[$i][$seperator_impression_plate_sets2],
                                                'front_color' =>         $w[$i][$seperator_front_colour2],
                                                'back_color' =>          $w[$i][$seperator_back_colour2],
                                                'no_of_pages' =>         NULL,
                                                'created_by' =>Auth::id(),
                                                'created_time' =>$timestamp,
                                            ]
                            
                                        );
                                    }
                                    if($hard_case_stand_entry==1)
                                    {
                                        ElementFeeder::insert(
                                            [
                                                'id' => NULL,
                                                'element_type_id' => '5',
                                                'job_card_id' =>$jc_id_global,
                                                'plate_size' =>          $w[$i][$hard_case_stand_plate_size2],
                                                'plate_sets' =>          $w[$i][$hard_case_stand_plate_sets2],
                                                'impression_per_plate' =>$w[$i][$hard_case_stand_impression_plate_sets2],
                                                'front_color' =>         $w[$i][$hard_case_stand_front_colour2],
                                                'back_color' =>          $w[$i][$hard_case_stand_back_colour2],
                                                'no_of_pages' =>         NULL,
                                                'created_by' =>Auth::id(),
                                                'created_time' =>$timestamp,
                                            ]
                            
                                        );
                                    }

                                }

                                $total_error = $total_error + $data_err;
                            
                            }
                            else
                                $error=$error."<br/>";

                            $char=0;
                            for($j=0;$j<count($x[$i]);$j++)
                            {
                                if($j==$text3 && $x[$i][$j] == 0)
                                    $j=$cover3-1;
                                else if($j==$cover3 && $x[$i][$j] == 0)
                                    $j=$posteen3-1;
                                else if($j==$posteen3 && $x[$i][$j] == 0)
                                    $j=$seperator3-1;
                                else if($j==$seperator3 && $x[$i][$j] == 0)
                                    $j=$hard_case_stand3-1;
                                else if($j==$hard_case_stand3 && $x[$i][$j] == 0)
                                    $j=$common3-1;
                                else if($j==$common3 && $x[$i][$j] == 0)
                                    break;                            
                                else if($x[$i][$j] == "")
                                {    
                                    $error=$error."Empty Cell at raw_material ".$this->getNameFromNumber($char).($i+1).". "; 
                                    $fl++;
                                }
                                $char++;

                            }
                            $total_error=$total_error+$fl;
                            if($fl==0)
                            {
                                $data_err=0;
                                if($v[$i][$internal_order_number1] != $x[$i][$internal_order_number3])
                                {
                                    $error=$error."Internal Order Number at raw_material ".$this->getNameFromNumber($internal_order_number3).($i+1)." not exist. "; 
                                    $data_err++;

                                }

                                if($x[$i][$text3]==1)
                                {
                                    $text_entry1=2;
                                    if(!is_numeric($x[$i][$text_no_of_sheet3]) || $x[$i][$text_no_of_sheet3]<0)
                                    {
                                        $error=$error."Text no of sheet at raw_material ".$this->getNameFromNumber($text_no_of_sheet3).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    $text_paper_type = PaperType::where('name','like',$x[$i][$text_paper_type3] )->get('id')->first();
                                    if(!$text_paper_type)
                                    {
                                        $error=$error."Text Paper Type at raw_material ".$this->getNameFromNumber($text_paper_type3).($i+1)." not exist. "; 
                                        $data_err++;
                                    }
                                    else
                                        $text_paper_type =  $text_paper_type->id;                 
                                }
                                else if($x[$i][$text3]==0)
                                {
                                    $text_entry1=0;
                                }
                                else
                                {
                                    $error = $error." text at raw_material".$this->getNameFromNumber($text3).($i+1)." can be 1 or 0.<br/>";
                                    $data_err++;
            
                                }
                                if($x[$i][$cover3]==1)
                                {
                                    $cover_entry1=2;
                                    if(!is_numeric($x[$i][$cover_no_of_sheet3]) || $x[$i][$cover_no_of_sheet3]<0)
                                    {
                                        $error=$error."Cover no of sheet at raw_material ".$this->getNameFromNumber($cover_no_of_sheet3).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    $cover_paper_type = PaperType::where('name','like',$x[$i][$cover_paper_type3] )->get('id')->first();
                                    if(!$cover_paper_type)
                                    {
                                        $error=$error."Cover Paper Type at raw_material ".$this->getNameFromNumber($cover_paper_type3).($i+1)." not exist. "; 
                                        $data_err++;
                                    }
                                    else
                                        $cover_paper_type =  $cover_paper_type->id;                 
                                }
                                else if($x[$i][$cover3]==0)
                                {
                                    $cover_entry1=0;
                                }
                                else
                                {
                                    $error = $error." Cover at raw_material".$this->getNameFromNumber($cover3).($i+1)." can be 1 or 0.<br/>";
                                    $data_err++;
                                }
                                if($x[$i][$posteen3]==1)
                                {
                                    $posteen_entry1=2;
                                    if(!is_numeric($x[$i][$posteen_no_of_sheet3]) || $x[$i][$posteen_no_of_sheet3]<0)
                                    {
                                        $error=$error."Posteen no of sheet at raw_material ".$this->getNameFromNumber($posteen_no_of_sheet3).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    $posteen_paper_type = PaperType::where('name','like',$x[$i][$posteen_paper_type3] )->get('id')->first();
                                    if(!$posteen_paper_type)
                                    {
                                        $error=$error."Posteen Paper Type at raw_material ".$this->getNameFromNumber($posteen_paper_type3).($i+1)." not exist. "; 
                                        $data_err++;
                                    }
                                    else
                                        $posteen_paper_type =  $posteen_paper_type->id;                 
                                }
                                else if($x[$i][$posteen3]==0)
                                {
                                    $posteen_entry1=0;
                                }
                                else
                                {
                                    $error = $error."Posteen at raw_material".$this->getNameFromNumber($posteen3).($i+1)." can be 1 or 0.<br/>";
                                    $data_err++;
                                }
                                if($x[$i][$seperator3]==1)
                                {
                                    $seperator_entry1=2;
                                    if(!is_numeric($x[$i][$seperator_no_of_sheet3]) || $x[$i][$seperator_no_of_sheet3]<0)
                                    {
                                        $error=$error."Seperator no of sheet at raw_material ".$this->getNameFromNumber($seperator_no_of_sheet3).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    $seperator_paper_type = PaperType::where('name','like',$x[$i][$seperator_paper_type3] )->get('id')->first();
                                    if(!$seperator_paper_type)
                                    {
                                        $error=$error."Seperator Paper Type at raw_material ".$this->getNameFromNumber($seperator_paper_type3).($i+1)." not exist. "; 
                                        $data_err++;
                                    }
                                    else
                                        $seperator_paper_type =  $seperator_paper_type->id;                 
                                }
                                else if($x[$i][$seperator3]==0)
                                {
                                    $seperator_entry1=0;
                                }
                                else
                                {
                                    $error = $error."Seperator at raw_material".$this->getNameFromNumber($posteen3).($i+1)." can be 1 or 0.<br/>";
                                    $data_err++;
                                }
                                if($x[$i][$hard_case_stand3]==1)
                                {
                                    $hard_case_stand_entry1=2;
                                    if(!is_numeric($x[$i][$hard_case_stand_no_of_sheet3]) || $x[$i][$hard_case_stand_no_of_sheet3]<0)
                                    {
                                        $error=$error."Hard Case Stand no of sheet at raw_material ".$this->getNameFromNumber($hard_case_stand_no_of_sheet3).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    $hard_case_stand_paper_type = PaperType::where('name','like',$x[$i][$hard_case_stand_paper_type3] )->get('id')->first();
                                    if(!$hard_case_stand_paper_type)
                                    {
                                        $error=$error."Hard Case Stand Paper Type at raw_material ".$this->getNameFromNumber($hard_case_stand_paper_type3).($i+1)." not exist. "; 
                                        $data_err++;
                                    }
                                    else
                                        $hard_case_stand_paper_type =  $hard_case_stand_paper_type->id;                 
                                }
                                else if($x[$i][$hard_case_stand3]==0)
                                {$hard_case_stand_entry1=0;}
                                else
                                {
                                    $error = $error."Hard Case Stand at raw_material".$this->getNameFromNumber($posteen3).($i+1)." can be 1 or 0.<br/>";
                                    $data_err++;
                                }
                                if($x[$i][$common3]==1)
                                {
                                    $common_entry1=2;
                                    if(!is_numeric($x[$i][$common_no_of_sheet3]) || $x[$i][$common_no_of_sheet3]<0)
                                    {
                                        $error=$error."Common no of sheet at raw_material ".$this->getNameFromNumber($common_no_of_sheet3).($i+1)." should be number. "; 
                                        $data_err++;
                                    }
                                    $common_paper_type = PaperType::where('name','like',$x[$i][$common_paper_type3] )->get('id')->first();
                                    if(!$common_paper_type)
                                    {
                                        $error=$error."Common Paper Type at raw_material ".$this->getNameFromNumber($common_paper_type3).($i+1)." not exist. "; 
                                        $data_err++;
                                    }
                                    else
                                        $common_paper_type =  $common_paper_type->id;                 
                                }
                                else if($x[$i][$common3]==0)
                                {
                                    $common_entry1=0;
                                }
                                else
                                {
                                    $error = $error."Common at raw_material".$this->getNameFromNumber($common3).($i+1)." can be 1 or 0.<br/>";
                                    $data_err++;
                                }
                                if($data_err==0)
                                {
                                    if($text_entry1==2)
                                    {    
                                        $element_feeder[]= Raw_Material::insert(
                                        [
                                            'id' => NULL,
                                            'job_card_id' =>$jc_id_global,
                                            'element_type_id' => '1',
                                            'paper_size' =>     $x[$i][$text_paper_size3],
                                            'paper_type_id' =>         $text_paper_type,
                                            'paper_gsm' =>      $x[$i][$text_paper_gsm3],
                                            'paper_mill' =>    NULL,
                                            'paper_brand' =>    $x[$i][$text_paper_brand3],
                                            'no_of_sheets' =>   $x[$i][$text_no_of_sheet3],
                                            'created_by' =>Auth::id(),
                                            'created_time' =>$timestamp,
                                        ]                      
                                    );
                                    }
                                    if($cover_entry1==2)
                                    {
                                        $element_feeder[]= Raw_Material::insert(
                                            [
                                                'id' => NULL,
                                                'job_card_id' =>$jc_id_global,
                                                'element_type_id' => '2',
                                                'paper_size' =>     $x[$i][$cover_paper_size3],
                                                'paper_type_id' =>         $cover_paper_type,
                                                'paper_gsm' =>      $x[$i][$cover_paper_gsm3],
                                                'paper_mill' =>     NULL,
                                                'paper_brand' =>    $x[$i][$cover_paper_brand3],
                                                'no_of_sheets' =>   $x[$i][$cover_no_of_sheet3],
                                                'created_by' =>Auth::id(),
                                                'created_time' =>$timestamp,
                                                ]                      
                                        );

                                    }
                                    if($posteen_entry1==2)
                                    {
                                        $element_feeder[]= Raw_Material::insert(
                                            [
                                                'id' => NULL,
                                                'job_card_id' =>$jc_id_global,
                                                'element_type_id' => '3',
                                                'paper_size' =>     $x[$i][$posteen_paper_size3],
                                                'paper_type_id' =>         $posteen_paper_type,
                                                'paper_gsm' =>      $x[$i][$posteen_paper_gsm3],
                                                'paper_mill' =>     NULL,
                                                'paper_brand' =>    $x[$i][$posteen_paper_brand3],
                                                'no_of_sheets' =>   $x[$i][$posteen_no_of_sheet3],
                                                'created_by' => Auth::id(),
                                                'created_time' =>$timestamp,
                                            ]                      
                                        );

                                    }
                                    if($seperator_entry1==2)
                                    {
                                        $element_feeder[]= Raw_Material::insert(
                                            [
                                                'id' => NULL,
                                                'job_card_id' =>$jc_id_global,
                                                'element_type_id' => '4',
                                                'paper_size' =>     $x[$i][$seperator_paper_size3],
                                                'paper_type_id' =>         $seperator_paper_type,
                                                'paper_gsm' =>      $x[$i][$seperator_paper_gsm3],
                                                'paper_mill' =>     NULL,
                                                'paper_brand' =>    $x[$i][$seperator_paper_brand3],
                                                'no_of_sheets' =>   $x[$i][$seperator_no_of_sheet3],
                                                'created_by' =>Auth::id(),
                                                'created_time' =>$timestamp,
                                            ]                      
                                        );                              
                                    }
                                    if($hard_case_stand_entry1==2)
                                    {
                                        $element_feeder[]= Raw_Material::insert(
                                            [
                                                'id' => NULL,
                                                'job_card_id' =>$jc_id_global,
                                                'element_type_id' => '5',
                                                'paper_size' =>     $x[$i][$hard_case_stand_paper_size3],
                                                'paper_type_id' =>         $hard_case_stand_paper_type,
                                                'paper_gsm' =>      $x[$i][$hard_case_stand_paper_gsm3],
                                                'paper_mill' =>     NULL,
                                                'paper_brand' =>    $x[$i][$hard_case_stand_paper_brand3],
                                                'no_of_sheets' =>   $x[$i][$hard_case_stand_no_of_sheet3],
                                                'created_by' =>Auth::id(),
                                                'created_time' =>$timestamp,
                                            ]                      
                                        );    
                                    }
                                    if($common_entry1==2)
                                    {
                                        $element_feeder[]= Raw_Material::insert(
                                            [
                                                'id' => NULL,
                                                'job_card_id' =>$jc_id_global,
                                                'element_type_id' => '6',
                                                'paper_size' =>     $x[$i][$common_paper_size3],
                                                'paper_type_id' =>         $common_paper_type,
                                                'paper_gsm' =>      $x[$i][$common_paper_gsm3],
                                                'paper_mill' =>     $x[$i][$common_paper_mill3],
                                                'paper_brand' =>    NULL,
                                                'no_of_sheets' =>   $x[$i][$common_no_of_sheet3],
                                                'created_by' =>Auth::id(),
                                                'created_time' =>$timestamp,

                                            ]                      
                                        );    
                                    }
                                }
                                $total_error = $total_error + $data_err;   
                            }
                            else
                                $error=$error."<br/>";
                            if($fl==0)
                            {
                                $data_err=0;
                                if($v[$i][$internal_order_number1] != $y[$i][$internal_order_number4])
                                {
                                    $error=$error."Internal Order Number at binding_details ".$this->getNameFromNumber($internal_order_number4).($i+1)." Not Exist. "; 
                                    $data_err++;    
                                }
                                else
                                {
                                    $item_element =ItemCategory::where('id','=',$item_id_global)->get('elements')->first()->elements;
                                    if($item_element=='1,6')
                                    {
                                        $item_element = $item_id_global;
                                    }
                                    $item_element=explode(',',$item_element);
                                    for($j=0;$j<count($item_element);$j++)
                                    {
                                        if($y[$i][$item_element[$j]]==0)
                                        {}
                                        else if($y[$i][$item_element[$j]]==1)
                                        {
                                            $value="{";
                                            $remark="{";    
                                            if($data[(intval($item_element[$j])+3)][$last_excel_cursor[intval($item_element[$j])-1]][0]!= $y[$i][$internal_order_number4])
                                            {
                                                $error=$error."Internal Order Number at binding_details_".$column_name_format[$item_element[$j]]." ".$this->getNameFromNumber($internal_order_number4).($i+1)." Not Exist. "; 
                                                $data_err++;
                                            }
                                            for($k=1;$k< count($data[(intval($item_element[$j])+3)][$last_excel_cursor[intval($item_element[$j])-1]]);$k=$k+2)
                                            {
                                                if($data[(intval($item_element[$j])+3)][$last_excel_cursor[$item_element[$j]-1]][$k]==0)
                                                {
                                                    $value=$value.'"'.ucwords(str_replace('_',' ',$data[(intval($item_element[$j])+3)][0][$k])).'":"No",';   
                                                }
                                                else if( $data[(intval($item_element[$j])+3)][$last_excel_cursor[$item_element[$j]-1]][$k]==1)
                                                {
                                                    $value=$value.'"'.ucwords(str_replace('_',' ',$data[(intval($item_element[$j])+3)][0][$k])).'":"Yes",';   
                                                }
                                                else
                                                {
                                                    $error=$error.str_replace('_',' ',$data[(intval($item_element[$j])+3)][0][$k])." at binding_details_".$column_name_format[$item_element[$j]]." "
                                                            .$this->getNameFromNumber($internal_order_number4).($i+1)." can be 0 or 1. "; 
                                                    $data_err++;            
                                                }
                                                if($data[(intval($item_element[$j])+3)][$last_excel_cursor[intval($item_element[$j])-1]][$k+1]!="")
                                                {
                                                    $remark = $remark.'"'.ucwords(str_replace('_',' ',$data[(intval($item_element[$j])+3)][0][$k])).'":"'
                                                                .$data[(intval($item_element[$j])+3)][$last_excel_cursor[intval($item_element[$j])-1]][$k+1].'",';   
                                                        
                                                }
                                                else
                                                {
                                                    $error=$error.str_replace('_',' ',$data[(intval($item_element[$j])+3)][0][$k])." remark at binding_details_".$column_name_format[$item_element[$j]]." "
                                                            .$this->getNameFromNumber($internal_order_number4).($i+1)." can be 0 or 1. "; 
                                                    $data_err++;            
                                                }

                                            }
                                            $value=str_replace('","Uv":"','","UV":"',substr($value,0,-1))."}";
                                            $remark=str_replace('","Uv":"','","UV":"',substr($remark,0,-1))."}";
                                            $last_excel_cursor[intval($item_element[$j])-1]=$last_excel_cursor[intval($item_element[$j])-1]+1;
                                            if($data_err==0)
                                            {
                                                $binding_details= Binding_detail::insert(
                                                    [
                                                        'id' => NULL,
                                                        'value' => $value,
                                                        'remark' => $remark,
                                                        'job_card_id' =>$jc_id_global,
                                                        'element_type_id' => $item_element[$j],
                                                        'created_by' =>Auth::id(),
                                                        'created_time' => $timestamp
                                                    ]
                                                );
                                            }
                                        }
                                        else
                                        {
                                            $error=$error.$column_name_format[$item_element[$j]]." at binding_details ".$this->getNameFromNumber($internal_order_number4).($i+1)." can be 0 or 1. "; 
                                            $data_err++;
                                        }
                                    }
                                }
                            }
                            else
                                $error=$error."<br/>";
                        }
                    }
                    else
                        $error = $error." No data is inserted.";
                }
                else
                {
                    $request->session()->flash('importerrors',"");
                    DB::rollBack();
                    return redirect($redirect_to)->with('error', 'Please upload file');
                }
                if($total_error==0)
                {
                    $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
                    DB::commit();
                    return redirect($redirect_to)->with('success', 'Job Card has been imported!');
                }
                else if($column_name_err!=0)
                {
                    $request->session()->flash('importerrors', $error);
                    DB::rollBack();
                    return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
                }
                else
                {   
                    $request->session()->flash('importerrors', $error);
                    DB::rollBack();
                    return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
                }
            }
            catch(Exception $e)
            {
        
            }
        
        }
    */
    public function import_tax_dispatch_db(Request $request)
    {
        $redirect_to = '/import/data/taxdispatch';
        $this->validate($request,
            [
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
            ]
        );
        try
        {
            if($request->file('excel'))                
            {
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path);
                $error="";
                $total_error=0;
                if($data && count($data)>=1)
                    $v= $data[0];
                else
                {
                    $request->session()->flash('importerrors',"");
                    return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                }
                $column_name_format=array('tax_invoice_number','tax_invoice_dispatch_date','dispatch_mode',
                'person_name','courier_company','docket_number','created_date'
            );    
                $tax_invoice_number1=array_search('tax_invoice_number',$column_name_format);	
                $tax_invoice_dispatch_date1=array_search('tax_invoice_dispatch_date',$column_name_format);	
                $dispatch_mode1=array_search('dispatch_mode',$column_name_format);	
                $person_name1=array_search('person_name',$column_name_format);
                $courier_company1=array_search('courier_company',$column_name_format);	
                $docket_number1=array_search('docket_number',$column_name_format);
                $created_date1= array_search('created_date',$column_name_format);

                $column_name_format=array('tax_invoice_number','tax_invoice_dispatch_date','dispatch_mode',
                'person_name','courier_company','docket_number'
                );
                $out= $this->validate_excel_format($v[0],$column_name_format,'Sheet1');
                $error= $error.explode("---",$out)[1];
                $column_name_err=explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);
        
                $total_error=$total_error+$column_name_err;
                $data_inserted=0;
                if($column_name_err==0)
                {
                    DB::beginTransaction();
                    for($i=1;$i<count($v);$i++)   
                    {
                        $char = '0';
                        $fl=0;
                        foreach ($v[$i] as $k=>$v1)
                        {
                            if($char==3 && strtolower($v[$i][$dispatch_mode1]) == "courier")
                            {
                                continue;
                            }
                            if($char==3 && strtolower($v[$i][$dispatch_mode1]) == "hand")
                            {
                                break;
                            }
                            if($char==$created_date1)
                                continue;
                            if($v1 == "")
                            {    
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            }
                            $char++;
                        }
                        $total_error=$total_error+$fl;
                        if($fl==0)
                        {
                            $data_err=0;
                            $tax_inv = Tax_invoice::where('invoice_number','=',$v[$i][$tax_invoice_number1])
                            ->get('id')->first();
                            if(!$tax_inv)
                            {
                                $error = $error." Tax Invoice Number at ".$this->getNameFromNumber($tax_invoice_number1).($i+1)." Not Exist.<br/>";
                                $data_err++;
                            }
                            $dispatch_date=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$tax_invoice_dispatch_date1]);
                            $dispatch_date=date('Y-m-d',$dispatch_date);
                            if($dispatch_date=='1970-01-01')
                            {
                                $error = $error." Dispatch Date at ".$this->getNameFromNumber($tax_invoice_dispatch_date1).($i+1)." Not Exist.<br/>";
                                $data_err++;
                            }
                            if(strtolower($v[$i][$dispatch_mode1])=="hand")
                            {
                                $person = $v[$i][$person_name1];
                                $docket=NULL;
                                $courier=NULL;
                            }
                            else if(strtolower($v[$i][$dispatch_mode1])=="courier")
                            {
                                $courier = $v[$i][$courier_company1];
                                $docket=$v[$i][$docket_number1];
                                $person=NULL;
                            }
                            else
                            {
                                $error = $error." Dispatch Mode at ".$this->getNameFromNumber($dispatch_mode1).($i+1)." is wrong.<br/>";
                                $data_err++;
                            }
                            if($v[$i][$created_date1]!='')
                            {
                                $timestamp=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$created_date1]);
                                $timestamp=date('Y-m-d G:i:s',$timestamp);
                            }
                            else
                                $timestamp =  date('Y-m-d G:i:s');
                            $total_error = $total_error + $data_err;
                            if($data_err==0)
                            {
                                $dispatch=Tax_Dispatch::insert([
                                    'id' => NULL,
                                    'tax_invoice_id' =>$tax_inv->id,
                                    'dispatch_date' =>$dispatch_date,
                                    'dispatch_mode' =>$v[$i][$dispatch_mode1],
                                    'courier_company' =>$courier,
                                    'docket_number' =>$docket,
                                    'person'=>$person,
                                    'created_by' =>Auth::id(),
                                    'is_active'=>1,
                                    'created_time'=>$timestamp
                                ]);
                                $data_inserted = $data_inserted+1;
                            }
                        }
                        else
                            $error=$error."<br/>";
                    }
                }
                else
                    $error = $error." No data is inserted.";
            }
            else
            {
                $request->session()->flash('importerrors',"");
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Please upload file');
            }
            if($total_error==0)
            {
                $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
                DB::commit();
                return redirect($redirect_to)->with('success', 'Tax Invoice Dispatch has been imported!');
            }
            else if($column_name_err!=0)
            {
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
            else
            {   
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }

        }
        catch(Exception $e)
        {

        }
    }
    public function import_tax_invoice_db(Request $request)
    {
        $redirect_to = '/import/data/taxinvoice';
        $this->validate($request,
            [
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
            ]
        );
        try
        {
            if($request->file('excel'))                
            {
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path);
                $error="";
                $total_error=0;
                if($data && count($data)>=2)
                {
                    $v= $data[0];
                    $w= $data[1];                
                }
                else
                {
                    $request->session()->flash('importerrors',"");
                    DB::rollBack();
                    return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                }

                $column_name_format=array('client_name','consignee_name','terms_of_delivery','gst_type',
                    'transportation_charges','other_charges','created_date',
                );
                if($this->import_type ==1)
                {
                    $ti_num=array('tax_invoice_number');
                    $column_name_format=array_merge($column_name_format,$ti_num);
                }
                // print_r($column_name_format);die;
                $client_id1=array_search("client_name",$column_name_format);	
                $consignee_id1=array_search('consignee_name',$column_name_format);	
                $terms_of_delivery1=array_search('terms_of_delivery',$column_name_format);	
                $gst_type1=array_search('gst_type',$column_name_format);
                $transportation_charges1=array_search('transportation_charges',$column_name_format);	
                $other_charges1=array_search('other_charges',$column_name_format);
                $created_date1=array_search('created_date',$column_name_format);
                $tax_invoice_number1=array_search('tax_invoice_number',$column_name_format);
                // print_r($v[0]);
                // print_r($column_name_format);die;
                $out= $this->validate_excel_format($v[0],$column_name_format,'Sheet1');
                $error= $error.explode("---",$out)[1];
                $column_name_err=explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);
        
                $column_name_format = array('client_name','consignee_name','delivery_challan_no','internal_order_number',
                    'description_of_goods','quantity','rate','unit_of_measurement','discount',
                    'hsn/sac','tansportation_charges','other_charges','payment_term'
                );
                // print_r($column_name_format);die;
                if($this->import_type ==1)
                {
                    $ti_num=array('gst_rate','tax_invoice_no');
                    $column_name_format=array_merge($column_name_format,$ti_num);
                }
                $out= $this->validate_excel_format($w[0],$column_name_format,'Sheet2');
                $error= $error.explode("---",$out)[1];
                $column_name_err=$column_name_err+ explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);
        
                $client_id2 = 0;
                $consignee_id2 =1;
                $tax_invoice_number2=14;
                $delivery_challan_no2=2;
                $internal_order_number2=3;
                $description_of_goods2=4;
                $quantity2=5;
                $rate2=6;
                $unit_of_measurement2=7;
                $discount2=8;
                $hsn_sac2=9;
                $tansportation_charges2=10;
                $other_charges2=11;	
                $payment_term2=12;
                $total_error=$total_error+$column_name_err;
                $lastinsertrow=1;
                $data_inserted=0;
                if($column_name_err==0)
                {
                    $counts=count($v);
                    DB::beginTransaction();
                    for($i=1;$i<$counts;$i++)   
                    {
                        $char = '0';
                        $fl=0;
                        foreach ($v[$i] as $k=>$v1)
                        {
                            if($char==$created_date1)
                                continue;
                            if($char==$transportation_charges1)
                                continue;
                            if($char==$other_charges1)
                                continue;

                            if($v1 == "")
                            {    
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            }
                            
                            $char++;
                        }
                        $total_error=$total_error+$fl;
                        if($fl==0 || $this->import_type==1)
                        {
                            $data_err=0;
                            $party = Party::where('partyname','=',$v[$i][$client_id1])
                            ->get('id')->first();
                            if(!$party)
                            {
                                $error = $error." Entry For at ".$this->getNameFromNumber($client_id1).($i+1)." Not Exist.<br/>";
                                $data_err++;
                            }
                            else
                            $party = $party->toArray();

                            
                            $consignee = Consignee::leftJoin('party','consignee.party_id','party.id')
                                ->where('consignee_name','=',$v[$i][$consignee_id1])
                                ->where('partyname','=',$v[$i][$client_id1])
                                ->get('consignee.id')->first();

                            // print($v[$i][$consignee_id1]);die;
                            if(!$consignee)
                            {
                                $error = $error." Consignee Id at ".$this->getNameFromNumber($consignee_id1).($i+1)." Not Exist.<br/>";
                                $data_err++;                      
                            }
                            else
                            $consignee = $consignee->toArray();
                            $gst_type=strtoupper($v[$i][$gst_type1]);
                            if(!($gst_type=="CGST/SGST" || $gst_type=="IGST"))
                            {
                                $error = $error." GST Type For at ".$this->getNameFromNumber($gst_type1).($i+1)." can be IGST or CGST/SGST.<br/>";
                                $data_err++;
                            }
                            if($v[$i][$transportation_charges1]<0)
                            {
                                $error = $error." Transportation Charges at ".$this->getNameFromNumber($transportation_charges1).($i+1)." must be more than or equal to 0.<br/>";
                                $data_err++;     
                            }
                            if($v[$i][$transportation_charges1]==''|| $v[$i][$transportation_charges1] == NULL || $v[$i][$transportation_charges1] == 0)
                                    $v[$i][$transportation_charges1] = 0;
                            
                            if($v[$i][$other_charges1]<0)
                            {
                                $error = $error." Other Charges at ".$this->getNameFromNumber($other_charges1).($i+1)." must be more than or equal to 0.<br/>";
                                $data_err++;     
                            }
                            if($v[$i][$other_charges1]==''|| $v[$i][$other_charges1] == NULL|| $v[$i][$other_charges1] == 0 )
                                $v[$i][$other_charges1] = 0;
                            if($v[$i][$created_date1]!='')
                            {
                                $timestamp=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$created_date1]);
                                $timestamp1=date('Y-m-d',$timestamp);
                                $timestamp=date('Y-m-d G:i:s',$timestamp);
                            }
                            else
                                $timestamp =  date('Y-m-d G:i:s');
                                $timestamp1 =  date('Y-m-d');
                            $total_error = $total_error + $data_err;
                            if($data_err==0)
                            {
                                $settings = Settings::where('name','Tax_Invoice_Prefix')->first();
                                $tax_number = $settings->value;
                                $tax=Tax_Invoice::insertGetId([
                                    'id' => NULL,
                                    'invoice_number'=>'',
                                    'party_id' =>$party['id'],
                                    'consignee_id' =>$consignee['id'],
                                    'terms_of_delivery' =>$v[$i][$terms_of_delivery1],
                                    'gst_type' =>$gst_type,
                                    'transportation_charge' =>$v[$i][$transportation_charges1],
                                    'other_charge' =>$v[$i][$other_charges1],
                                    'total_amount'=>'0',
                                    'created_by' =>Auth::id(),
                                    'is_active'=>1,
                                    'is_cancelled'=>0,
                                    'cancellation_reason'=>'',
                                    'cancellation_advised_by'=>'',                                    
                                    'date'=>$timestamp1,
                                    'created_at' =>$timestamp
                                ]
                                );
                                if($this->import_type==0)
                                   { $old_tax=Tax_Invoice::get('invoice_number')->last();
                                    $taxs=explode('/',$old_tax['invoice_number']);
                                    $vs = (int)$taxs[count($taxs)-1];
                                    if($vs<1){
                                        $vs=1013;
                                    }
                                    $taxs=$vs+1;
                                    $prefix = $tax_number ."/".$taxs;}
                                else{
                                    $prefix = $v[$i][$tax_invoice_number1];
                                }
                                    
                                $tax_prefix= Tax_Invoice::where('id',$tax)->update(
                                    [
                                        'invoice_number'=>$prefix,
                                    ]
                                );
                                $taxinv=$v[$i][$tax_invoice_number1];
                                // print_r($taxinv);die;
                                $data_inserted = $data_inserted+1;
                                $backup_i=$i; 
                                $l=$i;
                                // print_r(count($w));die;
                                $dc_arr=Array();
                                for(;$lastinsertrow<count($w);$lastinsertrow++)
                                {
                                    // print_r($i);die;
                                    // print_r($w[$lastinsertrow][$tax_invoice_number2]);die;
                                    if($taxinv!=$w[$lastinsertrow][$tax_invoice_number2]){
                                        break;
                                    }
                                        
                                    $i=$lastinsertrow;
                                    $char = '0';
                                    $fl=0;
                                    foreach ($w[$i] as $k=>$v1)
                                    {
                                        if($v1==0)
                                            continue;

                                        if($v1 == '')
                                        {    
                                            $error=$error."Empty Cell at Sheet2".$this->getNameFromNumber($char).($i+1).". "; 
                                            $fl++;
                                        }
                                        $char++;
                                    }
                                    $total_error=$total_error+$fl;
                                    
                                    // print_r($fl);die;
                                    if($fl==0 || $this->import_type==1)
                                    { 
                                        if($w[$i][$delivery_challan_no2]=="Not Applicable"){
                                            $dc=$w[$i][$delivery_challan_no2];
                                            $io =InternalOrder::where('internal_order.io_number','=',$w[$i][$internal_order_number2])
                                            ->get('internal_order.id')->first();
                                        }
                                        if($w[$i][$delivery_challan_no2]!="Not Applicable"){
                                            $delivery = Delivery_challan::where('challan_number','like',$w[$i][$delivery_challan_no2])
                                            ->leftJoin('party','party.id','delivery_challan.party_id')
                                            // ->leftJoin('consignee','consignee.id','delivery_challan.consignee_id')
                                            ->where('partyname','like',$w[$i][$client_id2])
                                            // ->where('consignee_name','like',$w[$i][$consignee_id2])
                                            ->get('delivery_challan.id')->first();
                                            // print_r($delivery->id);die;
                                            $dc=$delivery['id'];

                                            if(!$delivery)
                                            {
                                                $error = $error." Delivery Challan Number at Sheet2 ".$this->getNameFromNumber($delivery_challan_no2).($i)." Not found.<br/>";
                                                $data_err++;
                                            }
                                            $io = Challan_per_io::leftJoin('internal_order',function($join){
                                                $join->on('challan_per_io.io','=','internal_order.id');
                                            })
                                            ->where('internal_order.io_number','=',$w[$i][$internal_order_number2])
                                            ->where('challan_per_io.delivery_challan_id','=',$delivery['id'])
                                            ->get('internal_order.id')->first();
                                        }
                                       
                                        if(!$io)
                                        {
                                            $error = $error." Internal Order Number at Sheet2 ".$this->getNameFromNumber($internal_order_number2).($i)." Not found.<br/>";
                                            $data_err++;
                                        }
                                        else
                                        {
                                            $data1=Client_po::where('io',$io->id)
                                            ->get()->first();
                                            // print_r($data1);die;
                                            if($data1 && $data1['is_po_provided']==1){
                                                $data = Client_po::leftJoin('client_po_party','client_po_party.client_po_id','client_po.id')
                                                ->leftJoin('party','party.id','client_po_party.party_name')
                                                ->where('partyname','like',$w[$i][$client_id2])
                                                ->where('io',$io->id)
                                                ->get()->first();
                                                if(!isset($data) || count($data->toArray())<=0)
                                                {
                                                    $msg = $error." Internal Order ".$io_number." at Sheet2 ".$this->getNameFromNumber($internal_order_number2).($i)." does not have Client Purchase Order.<br>";
                                                }
                                                else{
                                                    $msg='';
                                                }
                                            }
                                           
                                            $msg='';
                                            if(!isset($data1))
                                            {
                                                $io_number = InternalOrder::where('id',$io->id)->first('io_number')['io_number'];
                                                $error = $error." Internal Order ".$io_number." at Sheet2 ".$this->getNameFromNumber($internal_order_number2).($i)." does not have Client Purchase Order.<br>";
                                                $data_err++;
                                            }
                                            if(($data1 && $data1['is_po_provided']==0)){
                                                $msg='';
                                            }

                                        }
                                       
                                        if($w[$i][$quantity2]<0)
                                        {
                                            $error = $error." Quantity at Sheet2 ".$this->getNameFromNumber($quantity2).($i)." Must be greater than or equal to 0.<br/>";
                                            $data_err++;
                                        }
                                        $uom = Unit_of_measurement::where('uom_name','=',$w[$i][$unit_of_measurement2])
                                        ->get('id')->first();
                                        if(!$uom)
                                        {
                                            $error = $error." Unit of measure at Sheet2 ".$this->getNameFromNumber($unit_of_measurement2).($i)." Not found.<br/>";
                                            $data_err++;
                                        }
                                        if($w[$i][$discount2]<0)
                                        {
                                            $error = $error." Quantity at Sheet2 ".$this->getNameFromNumber($discount2).($i)." Must be greater than or equal to 0.<br/>";
                                            $data_err++;
                                        }
                                        $hsn = Hsn::where('hsn','=',$w[$i][$hsn_sac2])
                                        ->get(['id','gst_rate'])->first();
                                        if(!$hsn)
                                        {
                                            $error = $error." HSN/SAC at Sheet2 ".$this->getNameFromNumber($hsn_sac2).($i)." Not found.<br/>";
                                            $data_err++;
                                        }
                                        if($w[$i][$tansportation_charges2]<0)
                                        {
                                            $error = $error." Transportation Charges at Sheet2 ".$this->getNameFromNumber($tansportation_charges2).($i)." Must be greater than or equal to 0.<br/>";
                                            $data_err++;
                                        }
                                        if($w[$i][$other_charges2]<0)
                                        {
                                            $error = $error." Other Charges at Sheet2 ".$this->getNameFromNumber($other_charges2).($i)." Must be greater than or equal to 0.<br/>";
                                            $data_err++;
                                        }
                                        $payment = Payment::where('value','=',$w[$i][$payment_term2])
                                        ->get('id')->first();
                                        if(!$payment)
                                        {
                                            $error = $error." Payment Term at Sheet2 ".$this->getNameFromNumber($payment_term2).($i)." Not found.<br/>";
                                            $data_err++;
                                        }
                                        // if(in_array($delivery->id,$dc_arr)){
                                        //     $error = $error." Duplicate Delivery  at Sheet2 ".$this->getNameFromNumber($delivery_challan_number1).($i)." Not found.<br/>";
                                        //     $data_err++;
                                        // }
                                        $total_error = $total_error + $data_err;
                                        if($data_err==0)
                                        {
                                            $amount=($w[$i][$rate2] * $w[$i][$quantity2]);
                                            $gst=($amount*$hsn->gst_rate)/100;
                                            $total=$amount+$gst;
                                            $discount_amt=$total-(($total*$w[$i][$discount2])/100);
                                            $total_amount=$discount_amt;
                                            $tot_amnt = Tax_Invoice::where('id',$tax)->get('total_amount')
                                            ->first()->total_amount;
                                            $tot_amnt=$tot_amnt+$total_amount;
                                            
                                            Tax::insert([
                                                'id' => NULL,
                                                'tax_invoice_id' =>$tax,
                                                'delivery_challan_id'=>$dc,
                                                'io_id' =>$io->id,
                                                'goods' =>$w[$i][$description_of_goods2],
                                                'qty' =>$w[$i][$quantity2],
                                                'rate' =>$w[$i][$rate2],
                                                'per' =>$uom->id,
                                                'discount' =>$w[$i][$discount2],
                                                'hsn' =>$hsn->id,
                                                'transport_charges' =>$w[$i][$tansportation_charges2],
                                                'other_charges' =>$w[$i][$other_charges2],
                                                'payment' =>$payment->id,
                                                'amount'=>$total_amount

                                            ]);
                                            $tot=$tot_amnt+$w[$i][$tansportation_charges2]+$w[$i][$other_charges2];
                                            $totalamt=$tot+($tot*$hsn->gst_rate/100);
                                            $tax_prefix= Tax_Invoice::where('id',$tax)->update(
                                                [
                                                    'total_amount'=>$totalamt
                                                ]
                                            );
                            
                                        }
                                    }
                                    else
                                        $error=$error."<br/>";
                                }
                                
                                $i=$backup_i;                       
                            }
                        }
                        else
                            $error=$error."<br/>";
                    }
                }
                else
                    $error = $error." No data is inserted.";
            }
            else
            {
                $request->session()->flash('importerrors',"");
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Please upload file');
            }
            if($total_error==0)
            {
                $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
                DB::commit();
                return redirect($redirect_to)->with('success', 'Tax Invoice has been imported!');
            }
            else if($column_name_err!=0)
            {
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
            else
            {   
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
        }
        catch (Exception $e)
        {   
        }
    }
    public function import_dc_db(Request $request)
    {
        $redirect_to = '/import/data/deliverychallan';
        $this->validate($request,
            [
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
            ]
        );
        try
        {
            if($request->file('excel'))                
            {
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path);
                $error="";
                $total_error=0;
                if($data && count($data)>=2)
                {
                    $v= $data[0];
                    $w= $data[1];                
                }
                else
                {
                    $request->session()->flash('importerrors',"");
                    return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                }
                if(count($v)==1)
                {
                    $request->session()->flash('importerrors',"");
                    return redirect($redirect_to)->with('error', 'Please fill delivery_challan sheet.');   
                }
                if(count($w)==1)
                {
                    $request->session()->flash('importerrors',"");
                    return redirect($redirect_to)->with('error', 'Please fill internal_order sheet.');   
                }
                $data_inserted=0;
                $column_name_format = array('client_reference_name','client_id','consignee_id',
                    'delivery_challan_date','goods_dispatch_mode','courier_name','company_name',
                    'bilty_docket_number','bilty_docket_date','vehicle_train_number','created_date'
                );
                if($this->import_type == 1)
                {
                    $dc_num = array("delivery_challan_number");
                    $column_name_format = array_merge($column_name_format,$dc_num);
                }
                // print_r($column_name_format);
                // print_r($v[0]);die;
                $out= $this->validate_excel_format($v[0],$column_name_format,'delivery_challan');
                $error= explode("---",$out)[1];
                $column_name_err= explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)  
                    // print_r($out);die;   
                    return redirect($redirect_to)->with('error',$error);
                $client_reference_name1 = array_search('client_reference_name',$column_name_format);
                $client_id1=array_search('client_id',$column_name_format);
                $consignee_id1=array_search('consignee_id',$column_name_format);
                $rate1=array_search('rate',$column_name_format);
                $goods_dispatch_mode1=array_search('goods_dispatch_mode',$column_name_format);
                $courier_name1=array_search('courier_name',$column_name_format);
                $company_name1=array_search('company_name',$column_name_format);
                $bilty_docket_number1=array_search('bilty_docket_number',$column_name_format);
                $bilty_docket_date1=array_search('bilty_docket_date',$column_name_format);
                $vehicle_train_number1=array_search('vehicle_train_number',$column_name_format);
                $created_date1=array_search('created_date',$column_name_format);
                $delivery_challan_number1=array_search('delivery_challan_number',$column_name_format);
                $delivery_challan_date1=array_search('delivery_challan_date',$column_name_format);
                
               
                
                $column_name_format = array('internal_order_number','client_reference_name','client_id','consignee_id',
                'delivery_challan_date','Uom','packing_details','goods_descrioption',
                'good_quantity'
            );
                if($this->import_type == 1)
                {
                    $dc_num = array("delivery_challan_map_num");
                    $column_name_format = array_merge($dc_num,$column_name_format);
                }
            
                $out= $this->validate_excel_format($w[0],$column_name_format,'internal_order');
                $error= $error.explode("---",$out)[1];
                $column_name_err=$column_name_err+ explode("---",$out)[0];
                $stop=explode("---",$out)[2];
               
                if($stop==1)   
                
                    return redirect($redirect_to)->with('error',$error);
                    
                $delivery_challan_map_num2=array_search('delivery_challan_map_num',$column_name_format);
                $internal_order_number2=array_search('internal_order_number',$column_name_format);
                $client_reference_name2=array_search('client_reference_name',$column_name_format);
                $client_id2=array_search('client_id',$column_name_format);
                $consignee_id2=array_search('consignee_id',$column_name_format);
                $delivery_challan_date2=array_search('delivery_challan_date',$column_name_format);
                $Uom2=array_search('Uom',$column_name_format);
                $packing_details2=array_search('packing_details',$column_name_format);
                $goods_descrioption2=array_search('goods_descrioption',$column_name_format);
                $good_quantity2=array_search('good_quantity',$column_name_format);
                // $rate2=array_search('rate',$column_name_format);
                
                $total_error=$total_error+$column_name_err;
                $lastinsertrow=1;
                if($column_name_err==0)
                {
                    DB::beginTransaction();
                    for($i=1;$i<count($v);$i++)   
                    {
                        $check_dependent_sheet=0;
                        $char = '0';
                        $fl=0;
                        for($p=0;$p<3;$p++)
                        {
                            if($char==$created_date1)
                                continue;
                            if($v[$i][$p] == "")
                            {    
                                $error=$error."Empty Cell at delivery_challan ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            }
                            $char++;
                        }
                        if(strtolower($v[$i][$goods_dispatch_mode1])=='self')
                        {
                            if($v[$i][$courier_name1] == "")
                            {    
                                if($this->import_type==1)
                                    $v[$i][$courier_name1] = 'Na';
                                else
                                {
                                    $error=$error."Empty Cell at delivery_challan ".$this->getNameFromNumber($courier_name1).($i+1).". "; 
                                    $fl++;
                                }
                            }
                            if($v[$i][$vehicle_train_number1] == "" ||$v[$i][$vehicle_train_number1] == NULL )
                            {    
                                if($this->import_type==1)
                                    $v[$i][$vehicle_train_number1] = '';
                                else
                                {
                                    $error=$error."Empty Cell at delivery_challan ".$this->getNameFromNumber($vehicle_train_number1).($i+1).". "; 
                                    $fl++;
                                }
                            }
                        }
                        else if($v[$i][$goods_dispatch_mode1]=='')
                        {
                            $error=$error."Empty Cell at delivery_challan ".$this->getNameFromNumber($goods_dispatch_mode1).($i+1).". "; 
                            $fl++;
                        }
                        else
                        {
                            if($v[$i][$company_name1] == "")
                            {    
                                $error=$error."Empty Cell at delivery_challan ".$this->getNameFromNumber($company_name1).($i+1).". "; 
                                $fl++;
                            }
                            if($v[$i][$bilty_docket_number1] !=0)
                            {    
                                $error=$error."Empty Cell at delivery_challan ".$this->getNameFromNumber($bilty_docket_number1).($i+1).". "; 
                                $fl++;
                            }
                            if($this->import_type==1 && ($v[$i][$bilty_docket_date1] == "" ||$v[$i][$bilty_docket_date1] == "date ??" ))
                                $v[$i][$bilty_docket_date1] = "35341";
                            elseif($v[$i][$bilty_docket_date1] == "")
                            {    
                                $error=$error."Empty Cell at delivery_challan ".$this->getNameFromNumber($bilty_docket_date1).($i+1).". "; 
                                $fl++;
                            }
                        }
                        
                        $total_error=$total_error+$fl;
                        if($fl==0)
                        { 
                            $data_err = 0;
                            $reference_name = Reference::where('referencename',$v[$i][$client_reference_name1])
                                ->get('id')->first();
                            if(!$reference_name)
                            {
                                $error = $error." Client Reference Name at delivery_challan ".$this->getNameFromNumber($client_reference_name1).($i+1)." Not Exist.<br/>";
                                $data_err++;
                            }
                            else
                            {
                                
                                $party = Party::where('partyname','like',$v[$i][$client_id1])
                                    ->where('reference_name',$reference_name->id)
                                    ->get('id')->first();
                                if(!$party)
                                {
                                    $error = $error." Client Name at delivery_challan ".$this->getNameFromNumber($client_id1).($i+1)." Not Exist.<br/>";
                                    $data_err++;
                                }
                            }
                                $consignee = Consignee::leftJoin('party','consignee.party_id','party.id')
                                ->where('consignee.consignee_name','like',trim($v[$i][$consignee_id1]))
                                ->where('party.partyname','like',trim($v[$i][$client_id1]))
                                ->get('consignee.id')->first();
                            if(!$consignee)
                            {
                                $error = $error." Consignee Name at delivery_challan ".$this->getNameFromNumber($consignee_id1).($i+1)." Not Exist.- - - -".$v[$i][$client_id1]."- -".$v[$i][$consignee_id1]."<br/>";
                                $data_err++;                      
                            }
                            $dispatch="";
                            if(strtolower($v[$i][$goods_dispatch_mode1])=="self")
                                $dispatch=2;
                            else if(strtolower($v[$i][$goods_dispatch_mode1])=="courier")
                                $dispatch=3;
                            else if(strtolower($v[$i][$goods_dispatch_mode1])=="transporter")
                                $dispatch=1;
                            else
                            {
                                $error = $error." Goods Dispatch Mode For at delivery_challan ".$this->getNameFromNumber($goods_dispatch_mode1).($i+1)." Not Exist.<br/>";
                                $data_err++;
                            }
                            if($this->import_type==1  && ($v[$i][$courier_name1]==''|| $v[$i][$courier_name1]==NULL))
                            {
                                $v[$i][$courier_name1] = 'NA';
                            }

                            if($dispatch==2)//self
                            {
                                $veh_no=Vehicle::where('vehicle_number','like', $v[$i][$vehicle_train_number1])->get('id')->first();    
                                if(!$veh_no)
                                {
                                    if($this->import_type==1)
                                    {
                                        $veh_no=0;
                                    }
                                    else
                                    {
                                        $error = $error." Vehicle Number at delivery_challan ".$this->getNameFromNumber($vehicle_train_number1).($i+1)." Not Exist.<br/>";
                                        $data_err++;
                                    }
                                }
                                else
                                    $veh_no=$veh_no->id;
                                $docket_no=NULL;
                                $doc_date="1970-01-01";
                                if($this->import_type==1  && ( $v[$i][$courier_name1] == '' || strtolower($v[$i][$courier_name1]) == 'na') )
                                    $company_name = array('id' =>'0,');
                                else
                                {
                                    $names = explode(',',$v[$i][$courier_name1]);
                                    $company_name = '';
                                    foreach($names as $n)
                                    {
                                        $company_name_id=Goods_Dispatch::where('courier_name','=',trim($n))
                                            ->where('mode','=',$dispatch)
                                            ->get('id')->first();
                                        if(isset($company_name_id['id']))
                                        {
                                            $company_name =$company_name.$company_name_id['id'].',';
                                        }
                                        else
                                        {
                                            $var = ($dispatch==2? $this->getNameFromNumber($courier_name1) : $this->getNameFromNumber($company_name1));
                                            $error = $error." Carrier Name at delivery_challan ".$var.($i+1)." Not Exist.- - -".$n."- - - - -".isset($company_name_id['id'])."<br/>";
                                            $data_err++;                                             
                                        }
                                    }
                                    $company_name = array('id' =>$company_name);
                                }
                            }
                            else //courier
                            {
                                $docket_no=$v[$i][$bilty_docket_number1];
                            
                                if(strpos($v[$i][$bilty_docket_date1], '/') !== false)
                                    $doc_date = strtotime(str_replace('/','-',$v[$i][$bilty_docket_date1]));
                                else
                                    $doc_date=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$bilty_docket_date1]);
                                $doc_date=date('Y-m-d',$doc_date);
                                $company_name=Goods_Dispatch::where('courier_name','=',$v[$i][$company_name1])
                                ->where('mode','=',$dispatch)
                                ->get('id')->first();
                                $veh_no=0;
                            }
                            if(strpos($v[$i][$delivery_challan_date1], '/') !== false)
                                $delivery_challan_date_data = strtotime(str_replace('/','-',$v[$i][$delivery_challan_date1]));
                            else
                                $delivery_challan_date_data=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$delivery_challan_date1]);
                            $delivery_challan_date_data=date('Y-m-d',$delivery_challan_date_data);
                                

                            if(!$company_name)
                            {
                                $var = ($dispatch==2? $this->getNameFromNumber($courier_name1) : $this->getNameFromNumber($company_name1));
                                $error = $error."  Company Name at delivery_challan ".$var.($i+1)." Not Exist.- - - -".$v[$i][$company_name1]."<br/>";
                                $data_err++;                                             
                            }
                            if($v[$i][$created_date1]!='')
                            {
                                $timestamp=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$created_date1]);
                                $timestamp1=date('Y-m-d',$timestamp);
                                $timestamp=date('Y-m-d G:i:s',$timestamp);
                            }
                            else
                                $timestamp =  date('Y-m-d G:i:s');
                            $timestamp1 =  date('Y-m-d');
                            $total_error = $total_error + $data_err;

                            if($data_err==0)
                            {
                                $total_amount=0;
                                
                                $settings=Settings::where('name','Delivery_Challan_Prefix')
                                ->first();
                                $insert_array = [
                                    'id' => NULL,
                                    'reference_name' => $reference_name->id,
                                    'challan_number'=>'',
                                    'party_id' => $party->id,
                                    'consignee_id' => $consignee->id,
                                    'delivery_date' => $delivery_challan_date_data,
                                    'total_amount'=>'0',
                                    'dispatch' =>$dispatch,
                                    'dispatch_id' =>$company_name['id'],
                                    'bilty_docket' =>$docket_no,
                                    'docket_date' => $doc_date,
                                    'created_by' =>Auth::id(),
                                    'is_active'=>1,
                                    'vehicle_id' => $veh_no,
                                    'date'=>$timestamp1,
                                    'created_time' =>$timestamp                      
                                ];
                                $delivery=Delivery_challan::insertGetId($insert_array);
                                if($this->import_type==0)
                                    $prefix = $settings->value ."/".$delivery;
                                else
                                    $prefix = $v[$i][$delivery_challan_number1];

                                $number= Delivery_challan::where('id',$delivery)->update(
                                    [
                                        'challan_number'=> $prefix,
                                    ]
                                );
                                $data_inserted = $data_inserted+1;
                                $backup_i=$i; 
                                $map_id=0;
                                $dc_nums=$v[$i][$delivery_challan_number1];
                                for(;$lastinsertrow<count($w);$lastinsertrow++)
                                {
                                    $total=0;
                                    // print_r($w[$lastinsertrow][$delivery_challan_map_num2]);die;
                                    $map_id=0;
                                    if($lastinsertrow>2 && explode('/',$w[$lastinsertrow][$delivery_challan_map_num2])<$w[$lastinsertrow-1][$delivery_challan_map_num2])
                                    {
                                        $error=$error."Delivery Challan Map Number at internal_order ".$this->getNameFromNumber($delivery_challan_map_num2).($i+1)." must follow ascending order. "; 
                                        $fl++;
                                    }
                                    if($w[$lastinsertrow][$delivery_challan_map_num2]==1)
                                    {
                                        $error=$error."Delivery Challan Map Number at internal_order ".$this->getNameFromNumber($delivery_challan_map_num2).($i+1)." must be greatetr than 1. "; 
                                        $fl++;
                                    }
                                    $temp_i=$backup_i;
                                   
                                    // if((int)explode('/',$w[$lastinsertrow][$delivery_challan_map_num2])==$temp_i)
                                    // {   
                                        
                                       
                                    // }
                                    // else
                                    // {
                                    //     $map_id=1;
                                    //     break;
                                    // }
                                    
                                    if($dc_nums!=$w[$lastinsertrow][$delivery_challan_map_num2])
                                    {  
                                        break;
                                    }
                                    else
                                    {
                                        $check_dependent_sheet++;
                                    }
                                                                    
                                    $i=$lastinsertrow;
                                    $char = '0';
                                    $fl=0;
                                   
                                    foreach ($w[$i] as $k=>$v1)
                                    {
                                        if($char==8 && $v1=="")
                                        {
                                            $v[$i][$good_quantity2]=0;
                                            $char++;
                                            continue;
                                        }
                                        // if($char==9 && $v1=="")
                                        // {
                                        //     $v[$i][$rate2]=0;
                                        //     $char++;
                                        //     continue;                                            
                                        // }
                        
                                        if($v1 == "")
                                        {    
                                            $error=$error."Empty Cell at internal_order ".$this->getNameFromNumber($char).($i+1).". "; 
                                            $fl++;
                                        }
                                        $char++;
                                    }
                                    $total_error=$total_error+$fl;
                                    if($fl==0)
                                    { 
                                        // print_r($w[$i][$client_reference_name2]);die;
                                        $client_ref_name = Reference::where('party_reference.referencename',$w[$i][$client_reference_name2])
                                        ->select('party_reference.id')->get()->first();
                                        print_r($client_ref_name['id']);
                                        if(!$client_ref_name['id'])
                                        {
                                            $error = $error." Client Reference Name at internal_order ".$this->getNameFromNumber($client_reference_name1).($i+1)." Not Exist.<br/>";
                                            $data_err++;
                                        }
                                        DB::enableQueryLog();
                                        // $intern=InternalOrder::->get()->first();
                                        // print_r($queries = DB::getQueryLog());die;  
                                        $io = InternalOrder::leftJoin('job_details','internal_order.job_details_id','=','job_details.id')
                                        ->leftJoin('hsn','job_details.hsn_code','=','hsn.id')
                                        ->where('io_number','LIKE','%'.$w[$i][$internal_order_number2].'%')
                                        ->where('reference_name',$client_ref_name['id'])
                                        ->select(
                                            'internal_order.id',
                                            'job_details.left_qty',
                                            'job_details.rate_per_qty',
                                            'job_details.qty',
                                            'hsn.gst_rate'
                                        )->get()->first();
                                       
                                        if(!$io)
                                        {
                                            $error = $error." Internal Order at internal_order ".$this->getNameFromNumber($internal_order_number2).($i)." Not found.<br/>".$w[$i][$internal_order_number2];
                                            $data_err++;
                                        }
                                        else
                                        {
                                            if($io->left_qty<$w[$i][$good_quantity2])
                                            {
                                                $error = $error." Goods Quantity at internal_order ".$this->getNameFromNumber($good_quantity2).($i)." is more than Ordered Quantity.<br/>"."--".$w[$i][$internal_order_number2];
                                                $data_err++;
                                            }
                                        }
                                        $delivery_challan_date=date('Y-m-d',
                                            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($w[$i][$delivery_challan_date2])
                                        );
                                        if($delivery_challan_date=='1970-01-01')
                                        {
                                            $error = $error." Delivery Challan Date at internal_order ".$this->getNameFromNumber($delivery_challan_date2).($i)." Not found.<br/>";
                                            $data_err++;                                    
                                        }
                                        $uom = Unit_of_measurement::where('uom_name','=',$w[$i][$Uom2])
                                        ->get('id')->first();
                                        if(!$uom)
                                        {
                                            $error = $error." Unit Of measure at internal_order ".$this->getNameFromNumber($Uom2).($i)." Not found.<br/>";
                                            $data_err++;
                                        }
                                        // if(!is_numeric($w[$i][$rate2]) || $w[$i][$rate2]<0)
                                        // {
                                        //     $error = $error." Rate at internal_order ".$this->getNameFromNumber($rate2).($i)." should be numeric and more than 0.<br/>";
                                        //     $data_err++;
                                        // }

                                        $total_error = $total_error + $data_err;
                                       
                                        if($data_err==0)
                                        {
                                            // print_r($w[$i][$rate2]."sadsadsa");
                                            // print_r($w[$i][$good_quantity2]);die;
                                            
                                            
                                            $amount=($io->rate_per_qty * $w[$i][$good_quantity2]);
                                            $total=$amount+($amount*$io->gst_rate)/100;
                                            $old_amt =Delivery_challan::where('id',$delivery)
                                            ->get('total_amount')
                                            ->first()->total_amount;
                                            $total_amount=$old_amt+$total;
                                            // print_r($total_amount);die;
                                            Challan_per_io::insert([
                                                'id' => NULL,
                                                'io' =>$io->id,
                                                'delivery_challan_id' => $delivery,
                                                'delivery_challan_date' => $delivery_challan_date,
                                                'uom_id' => $uom->id,
                                                'rate'=>$io->rate_per_qty,
                                                'good_desc' => $w[$i][$goods_descrioption2],
                                                'good_qty' => $w[$i][$good_quantity2],
                                                'packing_details' => $w[$i][$packing_details2],
                                                'amount'=>$total
                                            ]);
                                            $jd_id = InternalOrder::leftJoin('job_details','job_details.id','internal_order.job_details_id')
                                                ->where('internal_order.id','=',$io->id)
                                                ->get(['job_details.id','job_details.left_qty'])
                                                ->first();
                                            $lqty = $jd_id->left_qty-$w[$i][$good_quantity2];
                                            JobDetails::where('id','=',$jd_id->id)->update(['left_qty'=>$lqty]);
                                            $number= Delivery_challan::where('id',$delivery)->update(
                                                [
                                                    'total_amount'=> $total_amount,
                                                ]
                                            );
                                            $data_inserted=$data_inserted+1;                                    
                                        }
                                    }
                                    else
                                        $error=$error."<br/>";
                                }
                                $i=$backup_i;
                                $partyid=$party->id;
                                $party=Party::where('id',$partyid)->first();
                                $getAmount=Delivery_challan::where('delivery_challan.party_id',$partyid)
                                    ->leftJoin('party','delivery_challan.party_id','=','party.id')
                                    ->where('party.gst',$party['gst'])
                                    ->where('delivery_challan.waybill_status','!=','2')
                                    ->whereDate('date', Carbon::today())
                                    ->get([
                                        'party.gst',
                                        'delivery_challan.id',
                                        'delivery_challan.total_amount'
                                    ]);
                                $Todatdate=date('Y-m-d');
                                if($getAmount){
                                    $waybill = Settings::where('name','delivery_amount')->first();
                                    $waybill_value = $waybill->value;
                                    $total_ByDate=0;
                                    foreach($getAmount as $key){
                                        $total_ByDate=$total_ByDate+$key['total_amount'];
                                        $delivery_id[]=$key['id'];
                                    }
                                    if($total_ByDate>=$waybill_value)
                                    {
                                        foreach($getAmount as $key){
                                            $way_id=$key['id'];
                                            Delivery_challan::where('id',$way_id)->update(
                                                [
                                                    'waybill_status'=> 1
                                                ]
                                            );
                                        }
                                    }
                                }
                                // if($map_id==0 && $check_dependent_sheet==0)
                                // {
                                //     $error=$error."Delivery challan at delivery_challan A".($i+1)." must contain at least one Internal Order.<br>";
                                //     $data_err++;
                                //     $total_error = $total_error+$data_err;
                                // }
                            }
                        }
                        else
                            $error=$error."<br/>";
                    }
                }
                else
                    $error = $error." No data is inserted.";
            }
            else
            {
                $request->session()->flash('importerrors',"");
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Please upload file');
            }
    
            if($total_error==0)
            {
                $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
                DB::commit();
                return redirect($redirect_to)->with('success', 'Delivery Challan has been imported!');
            }
            else if($column_name_err!=0)
            {
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
            else
            {   
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
        }
        catch (Exception $e)
        {
            $request->session()->flash('importerrors', $error);
            DB::rollBack();
            return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
    
        }
    }
    public function import_client_po_db(Request $request)
    {
        $this->validate($request,
            [
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
            ]
        );
        $redirect_to = '/import/data/clientpo';

        try
        {
            if($request->file('excel'))                
            {
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path);
                $error="";
                $total_error=0;
                if($data && count($data)>=3)
                {
                    $v= $data[0];
                    $t =$data[1];
                    $w= $data[2];
                    $cd=$data[3];                
                }
                else
                {
                    $request->session()->flash('importerrors',"");
                    return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                }
                $lastfillindex_consignee=1;
                $lastfillindex_party=1;
                $lastfillindex_po=1;

                $data_inserted=0;
                $column_name_format = array('reference_name','internal_order_number','po_provided',
                    'po_number','hsn_code','item_description','delivery_date','quantity',
                    'unit_of_measurement','per_unit_price','discount','created_date'
                );

              
                $out= $this->validate_excel_format($v[0],$column_name_format,'Sheet1');
                $error= $error.explode("---",$out)[1];
                $column_name_err=explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);

                $reference_name1 = array_search('reference_name',$column_name_format);
                $internal_order_number1 = array_search('internal_order_number',$column_name_format);
                $po_provided1 = array_search('po_provided',$column_name_format);
                $po_number1 = array_search('po_number',$column_name_format);
                // $po_date1 = array_search('po_date',$column_name_format);
                $hsn_code1 = array_search('hsn_code',$column_name_format);
                $item_description1 = array_search('item_description',$column_name_format);
                $delivery_date1 = array_search('delivery_date',$column_name_format);
                $quantity1 = array_search('quantity',$column_name_format);
                $unit_of_measurement1 = array_search('unit_of_measurement',$column_name_format);
                $per_unit_price1 = array_search('per_unit_price',$column_name_format);
                $discount1 = array_search('discount',$column_name_format);
                $created_date1 = array_search('created_date',$column_name_format);
             
                $column_name_format = array('reference_name','internal_order_number','client_name',
                    'payment_term','is_consignee'
                );

                $out= $this->validate_excel_format($t[0],$column_name_format,'party_details');
                $error= $error.explode("---",$out)[1];
                $column_name_err=$column_name_err+ explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);
                
                $reference_name2 = array_search('reference_name',$column_name_format);
                $internal_order_number2 = array_search('internal_order_number',$column_name_format);
                $client_name2 = array_search('client_name',$column_name_format);
                $payment_term2 = array_search('payment_term',$column_name_format);
                $is_consignee2 = array_search('is_consignee',$column_name_format);

                $column_name_format = array('reference_name','internal_order_number','client_name',
                    'consignee_name','gst','pan','address','pincode','country','state','city','qty'
                );

                $out= $this->validate_excel_format($w[0],$column_name_format,'consignee');
                $error= $error.explode("---",$out)[1];
                $column_name_err=$column_name_err+ explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);
                
                $reference_name3 = array_search('reference_name',$column_name_format);
                $internal_order_number3 = array_search('internal_order_number',$column_name_format);
                $client_name3 = array_search('client_name',$column_name_format);
                $consignee_name3 = array_search('consignee_name',$column_name_format);
                $gst3 = array_search('gst',$column_name_format);
                $pan3 = array_search('pan',$column_name_format);
                $address3 = array_search('address',$column_name_format);
                $pincode3 = array_search('pincode',$column_name_format);
                $country3 = array_search('country',$column_name_format);
                $state3 = array_search('state',$column_name_format);
                $city3 = array_search('city',$column_name_format);
                $qty3 = array_search('qty',$column_name_format);


                $column_name_format = array('internal_order_number',
                'po_number','po_date','quantity');
                $internal_order_number4 = array_search('internal_order_number',$column_name_format);
                $po_number4 = array_search('po_number',$column_name_format);
                $po_date4 = array_search('po_date',$column_name_format);
                $quantity4 = array_search('quantity',$column_name_format);

                $out= $this->validate_excel_format($cd[0],$column_name_format,'po_details');
                $error= $error.explode("---",$out)[1];
                $column_name_err=$column_name_err+ explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);

                
            
                $total_error=$total_error+$column_name_err;
                if($column_name_err==0)
                {
                    DB::beginTransaction();
                    for($i=1;$i<count($v);$i++)   
                    {
                        $char = '0';
                        $fl = 0;
                        foreach ($v[$i] as $k=>$v1)
                        {
                            if($char == $po_provided1 && strtolower($v1) == "verbal")
                                break;
                            if($char == $discount1 && $v1 == "")
                            {
                                $v[$i][$discount1]=0;
                                $char++;
                                continue;
                            }
                            if($char == $quantity1 && $v1 == "")
                            {
                                $v[$i][$quantity1]=0; 
                                $char++;                           
                                continue;
                            }
                            if($char == $created_date1)
                                continue;

                            if(empty($v1))
                            {    
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            }
                            $char++;    
                        }
                        $total_error=$total_error+$fl;
                        if($fl==0)
                        {
                            $data_err=0;
                            $reference_name = Reference::where('referencename','like',$v[$i][$reference_name1])->get()->first() ;
                            if(!isset($reference_name))
                            {
                                $error = $error." Reference Name at ".$this->getNameFromNumber($reference_name1).($i+1)." Not Exist.<br/>";
                                $data_err++;
                            }
                            else
                            {
                                $InternalOrder = InternalOrder::leftJoin('party_reference','party_reference.id','internal_order.reference_name')
                                    ->where('internal_order.io_number','like',$v[$i][$internal_order_number1])
                                    ->where('party_reference.referencename','=',$v[$i][$reference_name1])
                                    ->get('internal_order.id')->first();
                                if(!isset($InternalOrder))
                                {
                                    $error = $error." Internal Order at ".$this->getNameFromNumber($internal_order_number1).($i+1)." Not Exist.<br/>";
                                    $data_err++;
                                }
                                else
                                {
                                    $client_po_io = Client_po::where('io','=',$InternalOrder->id)->get('id')->first();
                                    if(isset($client_po_io))
                                    {
                                        $error = $error." Internal Order at ".$this->getNameFromNumber($internal_order_number1).($i+1)." already contains a Client Purchase Order.<br/>";
                                        $data_err++;
                                    }
                                }
                            }
                            
                            if(strtolower($v[$i][$po_provided1])=="yes")
                                $po_provided=1;
                            else if(strtolower($v[$i][$po_provided1])=="verbal")
                                $po_provided=0;
                            else
                            {
                                $error = $error." Client Provided a PO at ".$this->getNameFromNumber($po_provided1).($i+1)." Not Exist.<br/>";
                                $data_err++;
                            }
                            if($po_provided==1)
                            {
                                $hsn = Hsn::where("hsn","like",$v[$i][$hsn_code1])
                                    ->get('id')->first();
                                if(!isset($hsn))
                                {
                                    $error = $error." Hsn at ".$this->getNameFromNumber($hsn_code1).($i+1)." Not Exist.<br/>";
                                    $data_err++;
                                }
                                // if(strpos($v[$i][$po_date1], '/') !== false)
                                //     $podate = strtotime(str_replace('/','-',$v[$i][$po_date1]));
                                // else
                                //     $podate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$po_date1]);
                                // $podate=date('Y-m-d',$podate);
                                // if($podate=='1970-01-01')
                                // {
                                //     $error = $error." Party Order Date at ".$this->getNameFromNumber($po_date1).($i+1)." Not in Format.<br/>";
                                //     $data_err++;
                                // }
                                if(strpos($v[$i][$delivery_date1], '/') !== false)
                                    $deldate = strtotime(str_replace('/','-',$v[$i][$delivery_date1]));
                                else
                                    $deldate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$delivery_date1]);
                                $deldate=date('Y-m-d',$deldate);
                                if($deldate=='1970-01-01')
                                {
                                    $error = $error." Delivery Date  at ".$this->getNameFromNumber($delivery_date1).($i+1)." Not in Format.<br/>";
                                    $data_err++;
                                }
                                if(!is_numeric($v[$i][$quantity1]) || $v[$i][$quantity1]<0 )
                                {
                                    $error = $error." Quantity at ".$this->getNameFromNumber($quantity1).($i+1)." should be numeric and more than or equal to 0.<br/>";
                                    $data_err++;
                                }
                                if(!is_numeric($v[$i][$quantity1]) || $v[$i][$discount1]<0 )
                                {
                                    $error = $error." Discount at ".$this->getNameFromNumber($discount1).($i+1)." should be numeric and more than or equal to 0.<br/>";
                                    $data_err++;
                                }
                                $uom = Unit_of_measurement::where('uom_name','like',$v[$i][$unit_of_measurement1])
                                ->get('id')->first();
                                if(!isset($uom))
                                {
                                    $error = $error." Job Quantity Unit at ".$this->getNameFromNumber($unit_of_measurement1).($i+1)." Not Exist.<br/>";                            $data_err++;
                                }
                                $total_error=$total_error+$data_err;
                            }
                            else
                            {
                                $hsn=NULL;
                                $deldate=NULL;
                                $podate=NULL;
                                $uom=NULL;
                            }
                                if(strpos($v[$i][$created_date1], '/') !== false)
                                    $timestamp = strtotime(str_replace('/','-',$v[$i][$created_date1]));
                                else
                                    $timestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$created_date1]);
                                $timestamp=date('Y-m-d G:i:s',$timestamp);
                                if($timestamp == '1970-01-01')  
                                    $timestamp =  date('Y-m-d G:i:s');
                                // print_r($timestamp);die;
                            if($data_err==0)
                            {
                                if($po_provided==0)
                                {
                                    $client_po = Client_po::insertGetId([
                                        'id'=>NULL,
                                        'reference_name'=>$reference_name->id,
                                        'io'=>$InternalOrder->id,
                                        'is_po_provided'=>$po_provided,
                                        'created_by'=>Auth::id(),
                                        'is_active'=>'1',
                                        'created_at'=>$timestamp
                                    ]);
                                }
                                else if($po_provided==1)
                                {
                                    $client_po = Client_po::insertGetId([
                                        'id'=>NULL,
                                        'reference_name'=>$reference_name->id,
                                        'io' => $InternalOrder->id,
                                        'is_po_provided' => $po_provided,
                                        'po_number' => $v[$i][$po_number1],
                                        // 'po_date' => $podate,
                                        'hsn' => $hsn->id,
                                        'item_desc' => $v[$i][$item_description1],
                                        'delivery_date' => $deldate,
                                        'qty' => $v[$i][$quantity1],
                                        'unit_of_measure' => $uom->id,
                                        'per_unit_price' => $v[$i][$per_unit_price1],
                                        'discount' => $v[$i][$discount1],
                                        'created_by' => Auth::id(),
                                        'is_active' => '1',
                                        'created_at' => $timestamp
                                            // 'payment_terms'=>$payment->id,
                                            // 'tax_perc_applicable'=>NULL,
                                            // 'is_consignee'=>$v[$i][$is_consignee1]==0?'0':'1',
                                    ]);
                                    $data_inserted = $data_inserted+1;
                                    $backup_i=$i;
                                    for(;$lastfillindex_po<count($cd);$lastfillindex_po++)
                                    {
                                        $f=$lastfillindex_po;
                                        if(strcmp($v[$i][$internal_order_number1],$cd[$f][$internal_order_number4])!=0)
                                        {
                                            break;
                                        }
                                        $char = '0';
                                        $fl=0;   
                                        foreach ($cd[$f] as $k=>$v1)
                                        {
                                            if($v1 == "")
                                            {
                                                $error=$error."Empty Cell at po_details ".$this->getNameFromNumber($char).($j+1).". ";
                                                $fl++;
                                            }
                                            $char++;
                                        }
                                        if($fl==0)
                                        {
                                            $data_err=0;
                                            $InternalOrder = InternalOrder::where('internal_order.io_number','like',$cd[$f][$internal_order_number4])
                                            ->get('internal_order.id')->first();
                                            if(!isset($InternalOrder))
                                            {
                                                $error = $error." Internal Order at Sheet4".$this->getNameFromNumber($internal_order_number4).($i+1)." Not Exist.<br/>";
                                                $data_err++;
                                            }
                                            if(!$cd[$f][$po_number4])
                                            {
                                                $error = $error." PO Number at ".$this->getNameFromNumber($quantity4).($i+1)." is Empty.<br/>";
                                                $data_err++;
                                            }
                                           
                                            if(!is_numeric($cd[$f][$quantity4]) || $cd[$f][$quantity4]<0 )
                                            {
                                                $error = $error." Quantity at ".$this->getNameFromNumber($quantity4).($i+1)." should be numeric and more than or equal to 0.<br/>";
                                                $data_err++;
                                            }
                                             if(strpos($cd[$f][$po_date4], '/') !== false)
                                                $podate = strtotime(str_replace('/','-',$cd[$f][$po_date4]));
                                            else
                                                $podate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($cd[$f][$po_date4]);
                                            $podate=date('Y-m-d',$podate);
                                            if($podate=='1970-01-01')
                                            {
                                                $error = $error." PO Date at ".$this->getNameFromNumber($po_date4).($i+1)." Not in Format.<br/>";
                                                $data_err++;
                                            }
                                            $client_po_io = Client_po::where('io','=',$InternalOrder->id)->get('id')->first();
                                            if(!isset($client_po_io))
                                            {
                                                $error = $error." Internal Order at ".$this->getNameFromNumber($internal_order_number4).($i+1)." does not have Client Purchase Order.<br/>";
                                                $data_err++;
                                            }
                                            
                                            $total_error = $total_error + $data_err;
                                            if($data_err==0)
                                            {
                                                
                                                $client_po_data=Client_po_data::insertGetId([
                                                    'client_po_id'=>$client_po_io->id,
                                                    'po_number'=>$cd[$f][$po_number4],
                                                    'po_date'=>$podate,
                                                    'po_qty'=>$cd[$f][$quantity4],
                                                ]);
                                                $client_po_num=Client_po_data::where('client_po_id',$client_po_io->id)->get('po_number')->toArray();
                                                
                                                if(count($client_po_num)>0){
                                                    foreach($client_po_num as $key=>$value){
                                                        if($key==0){
                                                            $client_num=$value['po_number'];
                                                        }
                                                        else{
                                                            $client_num=$client_num.','.$value['po_number'];
                                                        }
                                                       
                                                    }
                                                }

                                                else{
                                                    $client_num=$cd[$f][$po_number4];
                                                }
                                                // print_r($client_num);die;
                                                $cp=Client_po::where('id',$client_po_io->id)->update(['po_number'=>$client_num]);
                                             
                                            }
                                        }
                                    }
                                    for(;$lastfillindex_party<count($t);$lastfillindex_party++)
                                    {
                                        $h=$lastfillindex_party;
                                        if( strcmp($v[$i][$reference_name1],$t[$h][$reference_name2])!=0 || strcmp($v[$i][$internal_order_number1],$t[$h][$internal_order_number2])!=0)
                                        {
                                            break;
                                        }
                                        $char = '0';
                                        $fl=0;   
                                        foreach ($t[$h] as $k=>$v1)
                                        {
                                            if($v1 == "")
                                            {
                                                $error=$error."Empty Cell at party_details ".$this->getNameFromNumber($char).($j+1).". ";
                                                $fl++;
                                            }
                                            $char++;
                                        }
                                        if($fl==0)
                                        {
                                            $data_err=0;
                                            $client_data = Party::leftjoin('party_reference','party_reference.id','party.reference_name')
                                                ->where('party.partyname','=',$t[$h][$client_name2])
                                                ->where('party_reference.referencename',$t[$h][$reference_name2])
                                                ->get('party.id')->first();
                                            if(!$client_data)
                                            {
                                                $error = $error."Client Name at Sheet2 ".$this->getNameFromNumber($internal_order_number2).($h)." Not belong to Reference_name.<br/>";
                                                $data_err++;
                                            }
                                            $payment = Payment::where('value','like',$t[$h][$payment_term2])
                                                ->get('id')->first();
                                            if(!$payment)
                                            {
                                                $error = $error."Payment term at ".$this->getNameFromNumber($payment_term2).($h)." Not found.<br/>";
                                                $data_err++;
                                            }
                                            if(!(strtolower($t[$h][$is_consignee2])=="yes" || strtolower($t[$h][$is_consignee2])=="no"))
                                            {
                                                $error = $error." Is Consignee at ".$this->getNameFromNumber($is_consignee2).($h)." can be 'Yes' or 'No'.<br/>";
                                                $data_err++;
                                            }
                                            $total_error = $total_error + $data_err;
                                            if($data_err==0)
                                            {
                                                $client_po_party=Client_po_party::insertGetId([
                                                    'client_po_id'=>$client_po,
                                                    'party_name'=>$client_data->id,
                                                    'payment_terms'=>$payment->id,
                                                    'is_consignee'=>strtolower($t[$h][$is_consignee2])=='yes'?1:0,
                                                ]);
                                                if(strtolower($t[$h][$is_consignee2]) == 'yes')
                                                {
                                                    for(;$lastfillindex_consignee<count($w);$lastfillindex_consignee++)
                                                    {
                                                        $j=$lastfillindex_consignee;
                                                        
                                                        if( strcmp($t[$h][$reference_name2],$w[$j][$reference_name3])!=0 
                                                            || strcmp($t[$h][$internal_order_number2],$w[$j][$internal_order_number3])!=0 
                                                            || strcmp($t[$h][$client_name2],$w[$j][$client_name3])!=0 )
                                                        {
                                                            break;
                                                        }
                                                        $char = '0';
                                                        $fl=0;
                                                        foreach ($w[$j] as $k=>$v1)
                                                        {
                                                            if($v1 == "")
                                                            {    
                                                                $error=$error."Empty Cell at Sheet2 ".$this->getNameFromNumber($char).($j+1).". "; 
                                                                $fl++;
                                                            }
                                                            $char++;
                                                        }
                                                        
                                                        $total_error=$total_error+$fl;
                                                        if($fl==0)
                                                        {
                                                
                                                            $data_err=0;
                                                            $party = Party::leftjoin('party_reference','party_reference.id','party.reference_name')
                                                            ->where('party.partyname','=',$t[$h][$client_name3])
                                                            ->where('party_reference.referencename',$t[$h][$reference_name3])
                                                            ->get('party.id')->first();
                                            
                                                            $io = InternalOrder::where('io_number','=',$w[$j][$internal_order_number3])
                                                            ->get('id')->first();
                                                            if(!$io)
                                                            {
                                                                $error = $error."Internal Order at Sheet2 ".$this->getNameFromNumber($internal_order_number3).($j)." Not found.<br/>";
                                                                $data_err++;
                                                            }
                                                            $country = Country::where('name','like',$w[$j][$country3])
                                                            ->get('id')->first();
                                                            if(!$country){
                                                                $error = $error."Country at Sheet2 ".$this->getNameFromNumber($country3).($j)." Not found.<br/>";
                                                                $data_err++;
                                                            }
                                                            else
                                                                $state = State::where('country_id','=',$country->id)->
                                                                where('name','like',$w[$j][$state3])
                                                                ->get('id')->first();
                                                            if(!$country || !$state){
                                                                $error = $error."State at Sheet2 ".$this->getNameFromNumber($state3).($j)." Not found.<br/>";
                                                                $data_err++;
                                                            }
                                                            else
                                                                $city = City::where('state_id','=',$state->id)
                                                                            ->where('city','like',$w[$j][$city3])
                                                                            ->get('id')->first();
                                                            if(!$country || !$state || !$city )
                                                            {
                                                                $error = $error."City at Sheet2 ".$this->getNameFromNumber($city3).($j)." Not found.<br/>";
                                                                $data_err++;
                                                            }
                                                            if(strtolower($w[$j][$gst3]) == 'na' || strlen($w[$j][$gst3])==15)
                                                            {
                                                                $gst_no = $w[$j][$gst3];
                                                            }
                                                            else
                                                            {
                                                                $error = $error."GST No at ".($j)." Must be of 15 character or 'na'.<br/>";
                                                                $data_err++;   
                                                            }
                                                            if(strtolower($w[$j][$pan3]) == 'na' || strlen($w[$j][$pan3])==10)
                                                            {
                                                                $pan_no = $w[$j][$pan3];
                                                            }
                                                            else
                                                            {
                                                                $error = $error."PAN No at ".($j)." Must be of 10 character or 'na'.<br/>";
                                                                $data_err++;
                                                            }
                                                            $total_error = $total_error + $data_err;
                                                            if($data_err==0)
                                                            {
                                                                $consg_name_id=Consignee::where('party_id',$party->id)->where('consignee_name','=',$w[$j][$consignee_name3])
                                                                ->get('id')->first();
                                                                
                                                                if(!isset($consg_name_id)){
                                                                    $consg_name_id = Consignee::insertGetId([
                                                                        'id' => NULL,
                                                                        'consignee_name' => $w[$j][$consignee_name3],
                                                                        'party_id' =>$party->id,
                                                                        'gst' =>$gst_no,
                                                                        'pan' =>$pan_no,
                                                                        'address' =>$w[$j][$address3],
                                                                        'city' =>$city->id,
                                                                        'pincode' =>$w[$j][$pincode3],
                                                                        'state' =>$state->id,
                                                                        'country' =>$country->id,
                                                                        'created_by'=>Auth::id(),
                                                                        'is_active'=>'1',
                                                                        'created_time'=>$timestamp,
                                                                    ]);
                                                                }
                                                                else{
                                                                    $consg_name_id=$consg_name_id->id;
                                                                }
                                                                
                                                                
                                                                Client_po_consignee::insert([
                                                                    'id'=>NULL,
                                                                    'consignee_id' => $consg_name_id,
                                                                    'qty' => $w[$j][$qty3],
                                                                    'client_po_party_id'=>$client_po_party,
                                                                    'party_id'=>$client_data->id,
                                                                    'client_po_id' => $client_po,
                                                                    'created_at'=>$timestamp
                                                                ]);
                                                            }
                                                        }
                                                        else
                                                            $error=$error."<br/>";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $i=$backup_i;
                                }
                            }
                        }
                        else
                            $error=$error."<br/>";
                    }
                }
                else
                    $error = $error." No data is inserted.";
            }
            else
            {
                $request->session()->flash('importerrors',"");
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Please upload file');
            }
            
            if($total_error==0)
            {
                $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
                DB::commit();
                return redirect($redirect_to)->with('success', 'Client Purchase Order data has been imported!');
            }
            else if($column_name_err!=0)
            {
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
            else
            {   
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
        }
        catch (Exception $e)
        {   
            die($e->getMessage());
        }
    }

    public function import_io_db(Request $request)
    {
        $import_type = 0;
        $this->validate($request,
            [
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
            ]
        );
        $redirect_to = '/import/data/internalorder';
        try{
            if($request->file('excel'))                
            {
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path);
                $error="";
                $total_error=0;
                
                if($data && count($data)>=1)
                {
                    $v= $data[0];         
                }
                else
                {
                    $request->session()->flash('importerrors',"");
                    return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                }

                $column_name_format = array('item_name','io_type','job_date','hsn_code','delivery_date',
                    'job_qty','job_qty_unit','final_job_size','dimension','job_rate_per_quantity',
                    'marketing_person','job_details','front_colour','back_colour',
                    'plate_supply_by','paper_supply_by','transportation_charges',
                    'other_charges','remark_or_instruction','advance_received','amount',
                    'mode_of_receive','amount_receive_date','reference_name','created_date','other_specify'
                );
                
                if($this->import_type==1)
                {
                    $io_num = array("internal_order_number");
                    $column_name_format=array_merge($column_name_format,$io_num);
                }
                $out= $this->validate_excel_format($v[0],$column_name_format);
                $error= $error.explode("---",$out)[1];
                $column_name_err=explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);       
                $item_name1=array_search("item_name",$column_name_format);
                $io_type1=array_search("io_type",$column_name_format);
                $job_date1=array_search("job_date",$column_name_format);
                $hsn_code1=array_search("hsn_code",$column_name_format);
                $delivery_date1=array_search("delivery_date",$column_name_format);
                $job_qty1=array_search("job_qty",$column_name_format);
                $job_qty_unit1=array_search("job_qty_unit",$column_name_format);
                $final_job_size1=array_search("final_job_size",$column_name_format);
                $job_rate_per_quantity1=array_search("job_rate_per_quantity",$column_name_format);
                $marketing_person1=array_search("marketing_person",$column_name_format);
                $job_details1=array_search("job_details",$column_name_format);
                $dimension1=array_search("dimension",$column_name_format);
                $front_colour1=array_search("front_colour",$column_name_format);
                $back_colour1=array_search("back_colour",$column_name_format);
                $plate_supply_by1=array_search("plate_supply_by",$column_name_format);
                $paper_supply_by1=array_search("paper_supply_by",$column_name_format);
                $transportation_charges1=array_search("transportation_charges",$column_name_format);
                $other_charges1=array_search("other_charges",$column_name_format);
                $remark_or_instruction1=array_search("remark_or_instruction",$column_name_format);
                $advance_received1=array_search("advance_received",$column_name_format);
                $amount1=array_search("amount",$column_name_format);
                $mode_of_receive1=array_search("mode_of_receive",$column_name_format);
                $amount_receive_date1=array_search("amount_receive_date",$column_name_format); 
                $internal_order_number1=array_search("internal_order_number",$column_name_format);
                $reference_name1=array_search("reference_name",$column_name_format);
                $created_date1=array_search("created_date",$column_name_format);
                $other_specify1=array_search("other_specify",$column_name_format);
                $data_inserted=0;
                $total_error=$total_error+$column_name_err;
                if($column_name_err==0)
                {
                    DB::beginTransaction();
                    // for($i=1;$i<10;$i++)   
                    for($i=1;$i<count($v);$i++)   
                    {
                    $char = '0';
                        $fl=0;
                        foreach ($v[$i] as $k=>$v1)
                        {
                            if($this->import_type==0)
                            {
                    
                                if( strtolower($v[$i][$advance_received1]) == "no" && ($char==$amount1 || $char==$mode_of_receive1 || $char==$amount_receive_date1) )
                                {
                                  continue;
                                }
                                if($char==$created_date1 )
                                    continue;
                                if($char == $front_colour1 && $v1 == "")
                                {
                                    $v[$i][$front_colour1]=0;
                                }
                                if($char == $back_colour1 && $v1 == "")
                                {
                                    $v[$i][$back_colour1]=0;
                                }
                                if($char == $transportation_charges1 && $v1 == "")
                                {
                                    $v[$i][$transportation_charges1]=0;
                                }
                                if($char == $other_charges1 && $v1 == "")
                                {
                                    $v[$i][$other_charges1]=0;
                                }
                                if($char==$created_date1 )
                                
                                if($v1 == "")
                                {    
                                    $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                    $fl++;
                                }
                                $char++;
                            }
                        }
                        $total_error=$total_error+$fl;
                        if($fl==0)
                        {
                            $data_err=0;
                            $item = ItemCategory::where('name','like',$v[$i][$item_name1])
                                ->get('id')->first();
                            if(!isset($item))
                            {
                                if($this->import_type==1)
                                {
                                    $item = ItemCategory::where('name','like','other')
                                    ->get('id')->first();
                                    $other_item_name = $v[$i][$item_name1];
                                }
                                else
                                {
                                    $error = $error." Item at ".$this->getNameFromNumber($item_name1).($i+1)." Not Exist.<br/>";
                                    $data_err++;
                                    $item = ['id'=>'0'];
                                }
                            }
                            else
                            {
                                if(strtolower($v[$i][$item_name1])=='other')
                                    $other_item_name = $v[$i][$other_specify1];
                                else
                                    $other_item_name = '';
                            }
                            $io_type = IoType::where('name','like',$v[$i][$io_type1])
                            ->get('id')->first();
                            if(!isset($io_type))
                            {
                                $error = $error." IO Type at ".$this->getNameFromNumber($io_type1).($i+1)." Not Exist.<br/>";
                                $data_err++;
                            }
                            if($this->import_type==1 && $v[$i][$job_date1] == '')
                            {
                                $v[$i][$job_date1] = '1970-01-01';
                            }
                            else
                            {
                                if(strpos($v[$i][$job_date1], '/') !== false)
                                    $jobdate = strtotime(str_replace('/','-',$v[$i][$job_date1]));
                                else
                                    $jobdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$job_date1]);
                                $jobdate = date('Y-m-d',$jobdate);
                            }
                            if($this->import_type==0 && $jobdate=='1970-01-01')
                            {
                                $error = $error." Job Date at ".$this->getNameFromNumber($job_date1).($i+1)." Not in Format.<br/>";
                                $data_err++;
                            }
                            if($this->import_type==1 && ($v[$i][$hsn_code1]==NULL||$v[$i][$hsn_code1]==''))
                                $v[$i][$hsn_code1]='na';
                            
                            $hsn = Hsn::where('hsn','like',trim($v[$i][$hsn_code1]))
                            ->first('id');
                            if(!isset($hsn['id']))
                            {
                                if($this->import_type==1)
                                {
                                }
                                else
                                {
                                    $error = $error." Hsn at ".$this->getNameFromNumber($hsn_code1).($i+1)." Not Exist.<br/>";
                                    $data_err++;
                                }
                            }
                            if($this->import_type==1 && $v[$i][$delivery_date1] == '')
                            {
                                $v[$i][$delivery_date1] = '1970-01-01';
                            }
                            else
                            {
                                if(strpos($v[$i][$delivery_date1], '/') !== false)
                                    $deldate = strtotime(str_replace('/','-',$v[$i][$delivery_date1]));
                                else
                                    $deldate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$delivery_date1]);
                                $deldate = date('Y-m-d',$deldate);
                            }
                            if($this->import_type==0 && ($deldate)=='1970-01-01')
                            {
                                $error = $error." Delivery Date  at ".$this->getNameFromNumber($delivery_date1).($i+1)." Not in Format.<br/>";
                                $data_err++;
                            }
                    
                            $uom = Unit_of_measurement::where('uom_name','like',$v[$i][$job_qty_unit1])
                            ->first('id');
                            
                        
                            if(!isset($uom['id']))
                            {
                                // if($this->import_type==1)
                                // {
                                //     $uom = array("id" => Unit_of_measurement::insertGetId([
                                //         'uom_name'=>$v[$i][$job_qty_unit1],
                                //         'created_by'=>Auth::id(),
                                //         'is_active'=>1
                                //     ]));
                                // }
                                // else
                                // {
                                    $error = $error." Job Quantity Unit at ".$this->getNameFromNumber($job_qty_unit1).($i+1)." Not Exist.<br/>";                            $data_err++;
                                    $data_err++;
                                // }
                            }
                            

                            if($this->import_type==0)
                            {
                                $User = MasterMarketingPerson::where('name','like',$v[$i][$marketing_person1])
                                ->first('id');
                                if(!isset($User['id']))
                                {
                                    $error = $error." User at ".$this->getNameFromNumber($marketing_person1).($i+1)." Not Exist.<br/>";
                                    $data_err++;
                                }
                            }
                            else
                                $User = array('id'=>0);
                            $v[$i][$paper_supply_by1]= strtolower($v[$i][$paper_supply_by1]);
                            if($v[$i][$paper_supply_by1]=="party")
                                $platesup='Party';
                            else if($v[$i][$paper_supply_by1]=="press")
                                $platesup='Press';
                            else if($v[$i][$paper_supply_by1]=="oldplates")
                                $platesup='Old Plates';
                            else if($v[$i][$paper_supply_by1]=="na")
                                $platesup='NA';
                            else
                            {
                                $error = $error." Plate Supply By at ".$this->getNameFromNumber($paper_supply_by1).($i+1)." Not Exist.<br/>";
                                $data_err++;
                            }
                            $v[$i][$plate_supply_by1]= strtolower($v[$i][$plate_supply_by1]);
                        
                            if($v[$i][$plate_supply_by1]=="party")
                                $papersup='Party';
                            else if($v[$i][$plate_supply_by1]=="press")
                                $papersup='Press';
                            else if($v[$i][$plate_supply_by1]=="na")
                                $papersup='NA';
                            else
                            {
                                $error = $error." Paper Supply By at ".$this->getNameFromNumber($plate_supply_by1).($i+1)." Not Exist.<br/>";
                                $data_err++;
                            }
                            $v[$i][$advance_received1]= strtolower($v[$i][$advance_received1]);
                            if(strtolower($v[$i][$advance_received1]=="yes"))
                                $adv_received=1;
                            else if(strtolower($v[$i][$advance_received1]=="no"))
                                $adv_received=0;
                            else if($this->import_type==1 && ($v[$i][$advance_received1]=="" || $v[$i][$advance_received1]==NULL))
                            $adv_received=0;
                            else
                            {
                                $error = $error." Advance Received By at ".$this->getNameFromNumber($advance_received1).($i+1)." Not Exist.<br/>";
                                $data_err++;
                                $adv_received='';
                            }
                            if( $adv_received== 1)
                            {
                                $v[$i][$mode_of_receive1]= strtolower($v[$i][$mode_of_receive1]);
                                if($v[$i][$mode_of_receive1]=="cash")
                                    $mode_of_receive=0;
                                else if($v[$i][$mode_of_receive1]=="cheque")
                                    $mode_of_receive=1;
                                else if($v[$i][$mode_of_receive1]=="rtgs")
                                    $mode_of_receive=2;
                                else
                                {
                                    $error = $error." Mode of receive at ".$this->getNameFromNumber($mode_of_receive1).($i+1)." Not Exist.<br/>";
                                    $data_err++;
                                }
                                if($this->import_type==1 && ($v[$i][$amount_receive_date1]==''))
                                    $v[$i][$amount_receive_date1] = '1970-01-01';
                                else
                                {
                                    if(strpos($v[$i][$amount_receive_date1], '/') !== false)
                                        $ardate = strtotime(str_replace('/','-',$v[$i][$amount_receive_date1]));
                                    else
                                        $ardate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp( $v[$i][$amount_receive_date1]);
                                    $ardate = date('Y-m-d',$ardate);
                                }
                                if($this->import_type==0 && ($ardate)=='1970-01-01')
                                {
                                    $error = $error." Advanced Received Date at ".$this->getNameFromNumber($amount_receive_date1).($i+1)." Not in Format.<br/>";
                                    $data_err++;
                                }
                            }
                            $reference_name = Reference::where('referencename','like',$v[$i][$reference_name1])->get('id')->first();
                            if(isset($reference_name))
                            {
                                $reference_name=$reference_name->id;
                            }
                            else
                            {
                                $error = $error." Reference Name at ".$this->getNameFromNumber($reference_name1).($i+1)." Not Found.<br/>";
                                $data_err++;
                            }
                        
                            if( $this->import_type==1 && $v[$i][$created_date1]!='')
                            {
                                
                                if(strpos($v[$i][$created_date1], '/') !==false)
                                {
                                    $timestamp = str_replace('/','-',$v[$i][$created_date1]); 
                                    $timestamp = date('Y-m-d',strtotime($timestamp));
                                }
                                else
                                {
                                    $timestamp=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v[$i][$created_date1]);
                                    $timestamp = date('Y-m-d',($timestamp));
                                }
                            }
                            else
                                $timestamp =  date('Y-m-d G:i:s');
                            if( $this->import_type==1 && ($v[$i][$dimension1] = '' || $v[$i][$dimension1] = NULL))
                                $dimension = '';
                            else
                            $dimension = $v[$i][$dimension1];
                            $total_error=$total_error+$data_err;
                            if($data_err==0)
                            {
                                $data_inserted = $data_inserted+1;      
                                if($adv_received==1)
                                {
                                    $amount_id= advanceIO::insertGetId(
                                        [
                                            'id' => NULL,
                                            'amount' => $v[$i][$amount1],
                                            'mode_of_receive' =>$mode_of_receive,
                                            'date' => $ardate,
                                        ]
                                    );
                                }
                                else
                                    $amount_id=NULL;
                                $jobdetail_id= jobDetails::insertGetId([
                                    'id' => NULL,
                                    'io_type_id' => $io_type->id,//1
                                    'job_date' =>$jobdate,//2
                                    'hsn_code' => $hsn['id'],//3
                                    'delivery_date' => $deldate,//4
                                    'qty' =>$v[$i][$job_qty1],
                                    'left_qty' =>$v[$i][$job_qty1],
                                    'unit' => $uom['id'],//6
                                    'job_size' => $v[$i][$final_job_size1],
                                    'dimension' => $dimension,
                                    'rate_per_qty' =>$v[$i][$job_rate_per_quantity1],
                                    'marketing_user_id' =>$User['id'],//9
                                    'details' => $v[$i][$job_details1],
                                    'front_color' =>$v[$i][$front_colour1],
                                    'back_color' => $v[$i][$back_colour1],
                                    'is_supplied_paper' =>$papersup,//13
                                    'is_supplied_plate' =>$platesup,//14
                                    'transportation_charge' =>$v[$i][$transportation_charges1],
                                    'other_charge' => $v[$i][$other_charges1],
                                    'remarks' => $v[$i][$remark_or_instruction1],
                                    'advanced_received' => $adv_received,//18
                                    'advance_io_id' =>$amount_id
                                ]); 
                                                        
                                $settings = Settings::where('name','internal_order_prefix')->first();
                                $io_num = explode('/',InternalOrder::orderBy("id",'desc')->limit(1)->first("io_number")["io_number"]);
                                $ionum = intval($io_num[count($io_num)-1])+1;
                                $io_number = $settings->value.'/'.$ionum;
                                if($this->import_type==0)
                                    $prefix = $io_number;
                                else
                                $prefix = $v[$i][$internal_order_number1];
                                
                                $party_id = Party::where('reference_name','like',$v[$i][$reference_name1])->first('id')['id'];
                                
                                $io_id= InternalOrder::insertGetId(
                                    [
                                        'id' => NULL,
                                        'io_number'=> $prefix,
                                        'party_id' =>$party_id,
                                        'reference_name'=>$reference_name,
                                        'item_category_id' =>$item->id,
                                        'other_item_name'=> $other_item_name,
                                        'job_details_id' => $jobdetail_id,
                                        'created_by' => Auth::id(),
                                        'is_active' =>1,
                                        'created_time' => $timestamp,
                                    ]
                                );
                            }
                        }
                        else
                            $error=$error."<br/>";
                    }
                }
                else
                $error = $error." No data is inserted.";
            }
            else
            {
                $request->session()->flash('importerrors',"");
                return redirect($redirect_to)->with('error', 'Please upload file');
            }
            
            if($total_error==0)
            {
                $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
                DB::commit();
                return redirect($redirect_to)->with('success', 'Internal Order has been imported!');
            }
            else if($column_name_err!=0)
            {
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
            else
            {   
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
        }
        catch (Exception $e)
        {   

        }

    }

    public function import_consignee_db(Request $request)
    {
        $this->validate($request,
            [
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
            ]
        );
        $redirect_to = '/import/data/consignee';
        $error="";
        $total_error=0;
        try{
            if($request->file('excel'))                
            {
                DB::beginTransaction();
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path,'UTF-8');
                if($data)
                {    
                    $v= $data[0];
                }
                else
                {
                    $request->session()->flash('importerrors',"");
                    return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                }

                $column_name_format = array('consignee_name','gst','pan','address',
                            'pincode','country','state','city','client_name'
                        );
                $consignee_name1 = array_search('consignee_name',$column_name_format);
                $gst1 = array_search('gst',$column_name_format);
                $pan1 = array_search('pan',$column_name_format);
                $address1 = array_search('address',$column_name_format);
                $pincode1 = array_search('pincode',$column_name_format);
                $country1 = array_search('country',$column_name_format);
                $state1 = array_search('state',$column_name_format);
                $city1 = array_search('city',$column_name_format);
                $client_name1 = array_search('client_name',$column_name_format);
                $out= $this->validate_excel_format($v[0],$column_name_format);
                $error= explode("---",$out)[1];
                $column_name_err= explode("---",$out)[0];
                $data_inserted=0;
                $total_error=$total_error+$column_name_err;
                // print_r(count($v));die;
                if(count($v)<=1){
                    $request->session()->flash('importerrors',"");
                    return redirect($redirect_to)->with('error', 'Please insert some data in excel sheet.');
                }
                if($column_name_err==0)
                {  
                    for($i=1;$i<count($v);$i++)   
                    {
                        $char = '0';
                        $fl=0;
                        if($this->import_type==0)
                        {
                            foreach ($v[$i] as $k=>$v1)
                            {
                                if($v1 == "")
                                {    
                                    $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                    $fl++;
                                }
                                $char++; 
                            }
                        }
                        $total_error=$total_error+$fl;                      
                        if($fl==0)
                        {
                            $data_err=0;
                            if($this->import_type==1)
                            {
                                if(utf8_encode($v[$i]['2'])==NULL)
                                    $v[$i]['2']='';
                                if(utf8_encode($v[$i]['3'])==NULL)
                                    $v[$i]['3']='';
                                if(utf8_encode($v[$i]['4'])==NULL)
                                    $v[$i]['4']=0;
                                if(utf8_encode($v[$i]['5'])==NULL)
                                $v[$i]['5']='India';
                                if(utf8_encode($v[$i]['6'])==NULL)
                                    $v[$i]['6']='';
                                if(utf8_encode($v[$i]['7'])==NULL)
                                    $v[$i]['7']='';
                                if(utf8_encode($v[$i]['8'])==NULL )
                                    $v[$i]['8']='';
                            
                            }
                            $party_id = Party::where('partyname','like',$v[$i]['8'])->get('id')->first();
                                if(!$party_id)
                                {
                                    $error = $error."Client at I".($i+1)." Not Exist. <br/>";
                                    $data_err++;
                                }
                                else 
                                    $party_id=$party_id->id;
                            $country = Country::where('name','like',$v[$i]['5'])
                            ->get('id')->first();
                            if(!$country)
                            {
                                $error = $error."Country at F".($i+1)." Not found.<br/>";
                                $data_err++;
                            }
                            else
                                $state = State::where('country_id','=',$country->id)->
                                where('name','like',$v[$i]['6'])
                                ->get('id')->first();
                            if(!$country || !$state)
                            {
                                $error = $error."State at G".($i+1)." Not found.<br/>";
                                $data_err++;
                            }
                            else    
                            { 
                                if($this->import_type==1  && $v[$i]['7']=='')
                                    $city=array('id'=>'0'); 
                                else
                                    $city = City::where('state_id','=',$state->id)->
                                    where('city','like',trim($v[$i]['7']))
                                    ->get('id')->first();
                            }
                            if(!$country || !$state || !isset($city) )
                            {
                                $error = $error."City at H".($i+1)." Not found.<br/> ";
                                $data_err++;
                            }
                            else
                                $city = $city['id'];
                            if(strtolower($v[$i][$gst1]) == 'na' || strlen($v[$i][$gst1])==15)
                            {
                                $gst_no1 = $v[$i][$gst1];
                            }
                            else
                            {
                                $error = $error."GST No at B".($i+1)." Must be of 15 character or 'na'.<br/>";
                                $data_err++;   
                            }
                            if(strtolower($v[$i][$pan1]) == 'na' || strlen($v[$i][$pan1])==10)
                            {
                                $pan_no1 = $v[$i][$pan1];
                            }
                            else
                            {
                                $error = $error."PAN No at C".($i+1)." Must be of 10 character or 'na'.<br/>";
                                $data_err++;
                            }
                            $total_error=$total_error+$data_err;
                            if($data_err==0)
                            {
                                Consignee::insert([
                                    'id' => NULL,
                                    'consignee_name' => $v[$i][0],
                                    'party_id' =>$party_id,
                                    'gst' =>$gst_no1,
                                    'pan' =>$pan_no1,
                                    'address' =>$v[$i][3],
                                    'city' =>$city,
                                    'pincode' =>$v[$i][4],
                                    'state' =>$state->id,
                                    'country' =>$country->id,
                                    'created_by'=>Auth::id(),
                                    'is_active'=>'1',
                                    'created_time'=>date('Y-m-d G:i:s'),
                                ]);
                            }
                        }
                        else
                            $error=$error."<br/>";
                    }
                }
                else
                $error = $error." No data is inserted.";
            }
            else
            {
                $request->session()->flash('importerrors',"");
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Please upload file');    
            }
            if($total_error==0)
            {
                $request->session()->flash('importerrors',"");
                DB::commit();
                return redirect($redirect_to)->with('success', 'Consignee data has been imported.');
            }
            else
            {   
                $request->session()->flash('importerrors', $error."No Data is Inserted.");
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found.');        
            }
        }
        catch (Exception $e)
        {    
            $request->session()->flash('importerrors', $error);
            DB::rollBack();
            return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
        }
    }
 
    public function import_client_db(Request $request)
    {
        $this->validate($request,
            [
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
            ]
        );
        $redirect_to = '/import/data/client';
        try{
            if($request->file('excel'))                
            {
                DB::beginTransaction();
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path,'UTF-8');
                $error="";
                $total_error=0;
                if($data && count($data)>=1)
                {
                    $v= $data[0]; 
                                  
                }
                else
                {
                    $request->session()->flash('importerrors',"");
                    return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                }

                $column_name_format = array('name','client_reference_name',
                    'contact_person','address','pincode','country',
                    'state','city','contact_no','alternate_contact_no',
                    'email_id','payment_term','gst_no','pan_it'
                );
                $name1 = array_search('name',$column_name_format);
                $client_reference_name1 = array_search('client_reference_name',$column_name_format);
                $contact_person1 = array_search('contact_person',$column_name_format);
                $address1 = array_search('address',$column_name_format);
                $pincode1 = array_search('pincode',$column_name_format);
                $country1 = array_search('country',$column_name_format);
                $state1 = array_search('state',$column_name_format);
                $city1 = array_search('city',$column_name_format);
                $contact_no1 = array_search('contact_no',$column_name_format);
                $alternate_contact_no1 = array_search('alternate_contact_no',$column_name_format);
                $email_id1 = array_search('email_id',$column_name_format);
                $payment_term1 = array_search('payment_term',$column_name_format);
                $gst_no1 = array_search('gst_no',$column_name_format);
                $pan_it1 = array_search('pan_it',$column_name_format);
                $out= $this->validate_excel_format($v[0],$column_name_format);
                $error= explode("---",$out)[1];
                $column_name_err= explode("---",$out)[0];
                $data_inserted=0;
                $total_error=$total_error+$column_name_err;
                if($column_name_err==0)
                {
                    for($i=1;$i<count($v);$i++)   
                    {
                        $char = '0';
                        $fl=0;
                        if($this->import_type==0)
                        {
                            foreach ($v[$i] as $k=>$v1)
                            {
                                if($v1 == "" && $char!=9)
                                {    
                                    $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                    $fl++;
                                }
                                $char++; 
                            }
                        }
                        $total_error=$total_error+$fl;
                        if($fl==0)
                        {  
                            $data_err=0;
                            if($this->import_type==0)
                            {
                                $mail = Party::where('email','like',$v[$i]['10'])
                                    ->get('id')->first();
                                if($mail)
                                {
                                    $error = $error."Email at K".($i+1)." Already Exist.<br/>";
                                    $data_err++;
                                }
                                $contact = Party::where('contact','=',$v[$i]['8'])
                                ->get('id')->first();
                                if($contact)
                                {
                                    $error = $error."Contact at I".($i+1)." Already Exist.<br/>";
                                    $data_err++;
                                }
                            }
                            else if($this->import_type==1)
                            {
                                if(utf8_encode($v[$i]['2'])==NULL)
                                    $v[$i]['2']='';
                                if(utf8_encode($v[$i]['3'])==NULL)
                                    $v[$i]['3']='';
                                if(utf8_encode($v[$i]['4'])==NULL)
                                    $v[$i]['4']=0;
                                if(utf8_encode($v[$i]['5'])==NULL)
                                $v[$i]['5']='India';
                                if(utf8_encode($v[$i]['6'])==NULL)
                                    $v[$i]['6']='';
                                if(utf8_encode($v[$i]['7'])==NULL)
                                    $v[$i]['7']='';
                                if(utf8_encode($v[$i]['8'])==NULL || strtolower($v[$i]['8'])=='na')
                                    $v[$i]['8']=0;
                                if(utf8_encode($v[$i]['10'])==NULL)
                                    $v[$i]['10']='';
                                if(utf8_encode($v[$i]['11'])==NULL || strtolower($v[$i]['11'])=='na'|| strtolower($v[$i]['11'])=='n/a')
                                    $v[$i]['11']='';
                                if(utf8_encode($v[$i]['12'])==NULL)
                                    $v[$i]['12']='';
                    
                                if($v[$i]['13']==NULL)
                                    $v[$i]['13']='';
                            }
                            $payment = Payment::where('value','like',$v[$i]['11'])
                            ->get('id')->first();
                            if(!$payment)
                            {
                                $error = $error."Payment_term at L".($i+1)." Not found.<br/>";
                                $data_err++;
                            }
                        
                            $country = Country::where('name','like',$v[$i]['5'])
                            ->get('id')->first();
                            if(!$country)
                            {
                                $error = $error."Country at F".($i+1)." Not found.<br/>";
                                $data_err++;
                            }
                            else
                                $state = State::where('country_id','=',$country->id)->
                                where('name','like',$v[$i]['6'])
                                ->get('id')->first();
                            if(!$country || !$state)
                            {
                                $error = $error."State at G".($i+1)." Not found.<br/>";
                                $data_err++;
                            }
                            else    
                            {
                                if($v[$i]['7']=='')
                                    $id1=0;
                                else   
                                    $id1=$state->id;
                                $city = City::where('state_id','=',$id1)->
                                where('city','like',$v[$i]['7'])
                                ->get('id')->first();
                            }
                            if(!$country || !$state || !$city )
                            {
                                $error = $error."City at H".($i+1)." Not found.<br/>";
                                $data_err++;
                            }
                            if(strtolower($v[$i][$gst_no1])=='na') 
                            {
                                $gst_no = $v[$i][$gst_no1];
                                $gst_type=1;
                            }
                            else if (strlen($v[$i][$gst_no1])==15)
                            {
                                $gst_no = $v[$i][$gst_no1];
                                $gst_type=0;
                            }
                            else
                            {
                                $error = $error."GST No at ".$this->getNameFromNumber($gst_no1).($i+1)." Must be of 15 character or 'na'.<br/>";
                                $data_err++;
                            }
                            if(strtolower($v[$i][$pan_it1])=='na' || strlen($v[$i][$pan_it1])==10)
                            {    
                                $pan_no = $v[$i][$pan_it1];
                            }
                            else
                            {
                                $error = $error."Pan No at ".$this->getNameFromNumber($pan_it1).($i+1)." Must be of 10 character or 'na'.<br/>";
                                $data_err++;
                            }
                            $total_error=$total_error+$data_err;
                            if($data_err==0)
                            {
                                $data_inserted = $data_inserted+1;
                                $ref = Reference::insertGetId([
                                    'referencename'=>utf8_encode($v[$i][1])
                                ]);
                                $party= Party::insertGetId([
                                    'id' => NULL,
                                    'partyname' =>utf8_encode($v[$i][0]),
                                    'address' =>utf8_encode($v[$i][3]),
                                    'pincode' =>utf8_encode($v[$i][4]),
                                    'city_id' =>$city->id,
                                    'state_id' =>$state->id,
                                    'country_id' =>$country->id,
                                    'contact_person' =>utf8_encode($v[$i][2]),
                                    'contact' =>$v[$i][8],
                                    'alt_contact' =>utf8_encode($v[$i][9]),
                                    'email' =>utf8_encode($v[$i][10]),
                                    'payment_term_id' =>$payment->id,
                                    'gst' =>utf8_encode($gst_no),
                                    'gst_pointer' => $gst_type,
                                    'pan' =>utf8_encode($pan_no),
                                    'reference_name' =>$ref,
                                    'created_by'=>Auth::id(),
                                    'is_active'=>'1',
                                    'created_time'=>date('Y-m-d G:i:s')
                                ]);
                                if($this->import_type==0)
                                {
                                    $consignee= Consignee::insert([
                                        'consignee_name' =>utf8_encode($v[$i][0]),
                                        'party_id' =>$party,
                                        'gst' =>utf8_encode($gst_no),
                                        'pan' =>utf8_encode($pan_no),
                                        'address' =>utf8_encode($v[$i][3]),
                                        'city' => $city->id,
                                        'pincode' => utf8_encode($v[$i][4]),
                                        'state' =>$state->id,
                                        'country' =>$country->id,
                                        'created_by'=>Auth::id(),
                                        'is_active'=>1,
                                        'created_time'=>date('Y-m-d G:i:s'),
                                    ]);
                                }
                            }
                        }
                        else
                            $error=$error."<br/>";
                    }
                }
                else
                    $error = $error." No data is inserted.";
            }
            else
            {
                $request->session()->flash('importerrors',"");
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Please upload file');
            }
            if($total_error==0)
            {
                $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
                DB::commit();
                return redirect($redirect_to)->with('success', 'Client data has been imported!');
            }
            else
            {   
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
        }
        catch (Exception $e)
        {   
            $request->session()->flash('importerrors', $error);
            DB::rollBack();
            return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
        }
    }
    public function import_attendance_db(Request $request){
        $this->validate($request,
            [
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [   
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err')
            ]
        ); 
        $redirect_to = '/import/data/attendance';
        try {
            DB::beginTransaction();
            $get_employee = EmployeeProfile::orderBy('id','ASC')->select('id as emp_id','department_id','name')->get()->toArray();
            $emp_data = [];
            foreach($get_employee as $k => $v){
                $emp_data[$v['emp_id']] = $v;
            }
            if(empty($emp_data)){
                return back()->with('error','User Record Not Found');
            }

            if($request->file('excel')){
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path,'UTF-8');
                // print_r($data);die;
                if(count($data) > 0 && $data && isset($data[0][2])){
                    // fetching from and to date
                    $date = $data[0][2][1];
                    $date = explode(' To ',$date);
                    if(!isset($date[0]) || !isset($date[1])){
                        return back()->with('error','Please Upload Correct Excel File Format.');
                    }

                    $from_date = trim($date[0]); 
                    $to_date = trim($date[1]); 

                    $from_date = explode(' ',$from_date);
                    $to_date = explode(' ',$to_date);
                    
                    // check from and to date
                    if(!isset($from_date[0]) || !isset($from_date[1]) || !isset($from_date[2]) || !isset($to_date[0]) || !isset($to_date[1]) || !isset($to_date[2])){
                        return back()->with('error','Please Upload Correct Date Format.')->withInput();
                    }
                    $from_date = $from_date[0].' '.$from_date[1].' '.$from_date[2];
                    $to_date = $to_date[0].' '.$to_date[1].' '.$to_date[2];

                    $from_date = date('Y-m-d', strtotime($from_date));
                    $to_date = date('Y-m-d', strtotime($to_date));

                    // calculate number of days
                    $count_day = CustomHelpers::getDay($from_date,$to_date);
                    $count_day = $count_day+1;
                    // -------------------
                    
                    $days = [];
                    foreach($data as $data_key => $data_value) {
                        // print_r($data_value);
                        foreach ($data_value as $key => $value) {
                            if($value[0] == 'Days')
                            {
                                $days = $value;
                            }
                            // print_r($days);
                            if($value[0] == 'Employee:')
                            {
                                $col_end = $count_day;
                                $emp_id = trim($value[3]);
                                $emp_id = explode(' : ',$emp_id);
                                
                                if(!isset($emp_id)){
                                    return back()->with('error','Please Upload Correct Excel File Format.')->withInput();
                                }else if($emp_data[$emp_id[0]]['name']!=$emp_id[1]){
                                    return back()->with('error','Please Upload Correct Employee ID and Name for '.$emp_id[1])->withInput();
                                }

                                $status_row = $key+1;
                                $inTime_row = $status_row+1;
                                $outTime_row = $inTime_row+1;
                                $duration_row = $outTime_row+1;
                                $lateby_row = $duration_row+1;
                                $earlyby_row = $lateby_row+1;
                                $ot_row = $earlyby_row+1;
                                $shift_row = $ot_row+1;
                                $index = 2;
                                // check employee id,name exist in our employee_profile table for respective department 

                                // array_key_exists($emp_id[0],$emp_data)
                                if($emp_data[$emp_id[0]]['name']==$emp_id[1]){

                                    if(!empty($emp_data[$emp_id[0]]['department_id'])){
                                        $department_id = $emp_data[$emp_id[0]]['department_id'];
                                    }else{
                                        $department_id = 0;
                                    }
                                    $arr_day = 0;
                                     $counter = 0;
                                    for($i = 0; $i < $col_end ; $i++){
                                        if(isset($days[$index])){
                                            $status = $data_value[$status_row][$index];
                                            $inTime = $data_value[$inTime_row][$index];
                                            $outTime = $data_value[$outTime_row][$index];
                                            $duration = $data_value[$duration_row][$index];
                                            $lateby = $data_value[$lateby_row][$index];
                                            $earlyby = $data_value[$earlyby_row][$index];
                                            $ot = $data_value[$ot_row][$index];
                                            $shift = $data_value[$shift_row][$index];

                                            $d = explode(' ',$days[$index])[0];
                                            $day = explode(' ',$days[$index])[1];
                                            
                                            $m = date('m',strtotime($from_date));
                                           
                                            $y = date('Y',strtotime($from_date));
                                            if($d == 1 && $i > 0){
                                                $m = date('m',strtotime($to_date));
                                            }
                                            $atten_date = $y.'-'.$m.'-'.$d;

                                            $time = strtotime($atten_date);
                                            $timestamp = strtotime($atten_date);
                                            $day = date('l', $timestamp);
                                           
                                            if (in_array($day, ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'])) {
                                                if ($status == 'P') {
                                                    $counter += 1;
                                                } 
                                            }
                                            if (in_array($day, ['Sunday']) && $counter < 4 ) {
                                                $status = 'A';
                                                $counter = 0;
                                            }
                                            if(!empty($d)) {
                                                // check in database
                                                $check = Attendance::where('date',$atten_date)
                                                                        ->where('emp_id',$emp_id)
                                                                        ->first();
                                                $row_data    =   [
                                                    'status'    =>  (empty($status) ? 'A' : $status ),
                                                    'duration'    =>  (empty($duration) ? '00:00' : $duration),
                                                    'shift'    =>  (empty($shift) ? 'NS' : $shift),
                                                ];
                                                if(!empty($inTime)){
                                                    $row_data = array_merge($row_data,['in_time' =>  $inTime]);
                                                }
                                                if(!empty($outTime)){
                                                    $row_data = array_merge($row_data,['out_time' =>  $outTime]);
                                                }
                                                if(!empty($lateby)){
                                                    $row_data = array_merge($row_data,['late_by' =>  $lateby]);
                                                }
                                                if(!empty($earlyby)){
                                                    $row_data = array_merge($row_data,['early_by' =>  $earlyby]);
                                                }
                                                if(!empty($ot)){
                                                    $row_data = array_merge($row_data,['ot' =>  $ot]);
                                                }
                                                if(isset($check->id)){
                                                    $update = Attendance::where('id',$check->id)
                                                                            ->update($row_data);
                                                }else{
                                                    $row_data = array_merge($row_data,[
                                                        'emp_id'    =>  $emp_id[0],
                                                        'date'  =>  $atten_date,
                                                        'department_id'  =>  $department_id,
                                                        'created_by'=> Auth::id()
                                                    ]);
                                                    // print_r($row_data);die();
                                                    $insert =   Attendance::insertGetId($row_data);
                                                }
                                            }

                                        }elseif(empty($days[$index])){
                                            $col_end = $col_end+1;
                                        }
                                        $index++;
                                    }
                                }
                            }
                        }
                    }

                }else{
                    DB::rollback();
                    return back()->with('error', 'Some Error Occurred Please Try again.');
                }
            }
            else{
                DB::rollback();
                return back()->with('error', 'Some Error Occurred Please Try again.');
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            return redirect($redirect_to)->with('error','some error occurred'.$ex->getMessage())->withInput();
        }
        DB::commit();
        return redirect($redirect_to)->with('success','Successfully Uploaded.');

    }
    public function import_task_db(Request $request){
       $this->validate($request,
            [
                'emp'=>'required',
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [   
                'emp.required'=>'This Field is required',
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err')
            ]
        ); 
       $redirect_to = '/import/data/task';
       try {

           if($request->file('excel')) {
                DB::beginTransaction();
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path,'UTF-8');
                $error="";
                $total_error=0;
                if($data && count($data)>=1)
                {
                    $v= $data[0];  

                }
                else
                {
                    $request->session()->flash('importerrors',"");
                    return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                }
                $column_name_format = array('task_name','frequency',
                    'day','task_date','month'
                );
                $task_name1 = array_search('task_name',$column_name_format);
                $frequency1 = array_search('frequency',$column_name_format);
                $day1 = array_search('day',$column_name_format);
                $task_date1 = array_search('task_date',$column_name_format);
                $month1 = array_search('month',$column_name_format);

                $out= $this->validate_excel_format($v[0],$column_name_format);
                $error= explode("---",$out)[1];
                $column_name_err= explode("---",$out)[0];
                $data_inserted=0;
                $total_error=$total_error+$column_name_err;
                if($column_name_err==0)
                {
                    // print_r(count($v));die();
                    $tsk = array();
                    $freq = array();
                    $day = array();
                    $date = array();
                    $month= array();
                    $data_err=0;
                    
                    for($i=8;$i<count($v);$i++)   
                    {
                        $char = '0';
                        $fl=0;
                        if($this->import_type==0)
                        {
                            foreach ($v[$i] as $k=>$x)
                            {
                                if($v == "")
                                {    
                                    $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                    $fl++;
                                }
                                 $char++;
                            }
                        }
                    
                        if($fl==0)
                        {                             
                            
                            if($this->import_type==0)
                            {
                                if($v[$i]['0'] == "")
                                {    
                                    $error=$error."Task name at A".($i+1)."cant be empty. "; 
                                    $data_err++;
                                }
                                if($v[$i]['1'] == ""){
                                    
                                    $error=$error."Frequency at B".($i+1)."empty. "; 
                                    $data_err++;
                                }else
                                {   
                                    
                                    if($v[$i]['1'] == "daily" || $v[$i]['1'] == "Daily"){
                                        $v[$i]['2'] = "";
                                        $v[$i]['3'] = "";
                                        $v[$i]['4'] = "";
                                    }else if($v[$i]['1'] == "weekly" || $v[$i]['1'] == "Weekly"){
                                       
                                        $v[$i]['3'] = "";
                                        $v[$i]['4'] = "";
                                        if($v[$i]['2'] == ""){
                                            $error=$error."For Weekly day at C".($i+1)."empty. "; 
                                            $data_err++;
                                        }
                                    }else if($v[$i]['1'] == "fortnightly"|| $v[$i]['1'] == "Fortnightly"){
                                        $v[$i]['4'] = "";
                                        $v[$i]['2'] = "";
                                        if($v[$i]['3'] == ""){
                                            $error=$error."For Fortnightly Task date at D".($i+1)." empty. "; 
                                            $data_err++;
                                        }
                                       
                                    }else if($v[$i]['1'] == "monthly"||$v[$i]['1'] == "Monthly"){
                                        $v[$i]['2'] = "";
                                        $v[$i]['4'] = "";
                                        if($v[$i]['3'] == ""){
                                            $error=$error."For Monthly Task date at D".($i+1)."empty. "; 
                                            $data_err++;
                                        }
                                    }else if($v[$i]['1'] == "quarterly"|| $v[$i]['1'] == "half yearly"||$v[$i]['1'] == "annually"|| $v[$i]['1'] == "Quarterly"|| $v[$i]['1'] == "Half Yearly"||$v[$i]['1'] == "Annually"){
                                        $v[$i]['2'] ="";
                                        if($v[$i]['3'] == ""){
                                            $error=$error."Task date at D".($i+1)."empty. "; 
                                            $data_err++;
                                        }
                                        if($v[$i]['4'] == ""){
                                            $error=$error."Month at E".($i+1)."empty. "; 
                                            $data_err++;
                                        }
                                    }

                                }
                                // if(utf8_encode($v[$i]['2'])==NULL)
                                //     $v[$i]['2']='';
                                // if(utf8_encode($v[$i]['3'])==NULL)
                                //     $v[$i]['3']='';
                                // if(utf8_encode($v[$i]['4'])==NULL)
                                //     $v[$i]['4']='';
                                
                            }
                            // print_r(count($v));die();
                            $total_error=$total_error+$data_err;
                            
                            if($data_err==0)
                            {
                                $data_inserted = $data_inserted+1;
                                $tsk= array_merge($tsk,array($v[$i][0]));
                                $freq = array_merge($freq,array($v[$i][1]));
                                $day = array_merge($day,array($v[$i][2]));
                                $date = array_merge($date,array($v[$i][3]));
                                $month= array_merge($month,array($v[$i][4]));
                                
                            }           
                        }
                        else
                            $error=$error."<br/>";
                    }

                    if($data_err==0)
                    {   
                        for($i=0;$i<count($tsk);$i++){
                            
                            $task= EmployeeTask::insertGetId([
                                    'id' => NULL,
                                    'emp_id' =>$request->input('emp'),
                                    'task_name' =>utf8_encode($tsk[$i]),
                                    'frequency' =>strtolower(utf8_encode($freq[$i])),
                                    'day' =>strtolower(utf8_encode($day[$i])),
                                    'task_date' =>utf8_encode($date[$i]),
                                    'month' =>strtolower(utf8_encode($month[$i])),
                                    'created_by'=>Auth::id(),
                                    'created_at'=>date('Y-m-d G:i:s')
                                ]);
                          
                        }
                    
                    }
                    
                }
                else{
                    $error = $error." No data is inserted.";
                }
           }else
            {
                $request->session()->flash('importerrors',"");
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Please upload file');
            }
            if($total_error==0)
            {
                $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
                DB::commit();
                return redirect($redirect_to)->with('success', 'Employees Task Data has been imported!');
            }
            else
            {   
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
       } catch (Exception $e) {
            $request->session()->flash('importerrors', $error);
            DB::rollBack();
            return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');
       }
    } 
    public function import_stock_db(Request $request)
    {
        $import_type = 0;
        $this->validate($request,
            [
                'excel'=>'required|mimes:xls,xlt,xltm,xltx,xlsm,xlsx'
            ],
            [
                'excel.required'=> Lang::get('consignee_form.Excel_Require_Err'),
                'excel.mimes'=> Lang::get('consignee_form.Excel_Format_Err'),
            ]
        );
        $redirect_to = '/import/data/stock';
        try{
            if($request->file('excel'))                
            {
                $path = $request->file('excel');
                $data = Excel::toArray(new Import(),$path);
                $error="";
                $total_error=0;
                
                if($data && count($data)>=2)
                {
                    $v= $data[0]; 
                    $w= $data[1];         
                }
                else
                {
                    $request->session()->flash('importerrors',"");
                    return redirect($redirect_to)->with('error', 'Please upload file of correct format.');
                }

                $column_name_format = array('master_cat_id','sub_cat_id','item_name','item_stand_pack','quantity_standard_packing',
                    'unit_of_qty','stock_unit','length','breadth','dimension',
                    'gsm','brand','color','size',
                    'item_location','opening_stock','min_entry_level');
                
                // if($this->import_type==1)
                // {
                //     $io_num = array("internal_order_number");
                //     $column_name_format=array_merge($column_name_format,$io_num);
                // }
                // print_r($v);die;
                $out= $this->validate_excel_format($v[0],$column_name_format,'stock');
                $error= $error.explode("---",$out)[1];
                $column_name_err=explode("---",$out)[0];
                $stop=explode("---",$out)[2];
                if($stop==1)    
                    return redirect($redirect_to)->with('error',$error);       
                $master_cat_id1=array_search("master_cat_id",$column_name_format);
                $sub_cat_id1=array_search("sub_cat_id",$column_name_format);
                $item_name1=array_search("item_name",$column_name_format);
                $item_stand_pack1=array_search("item_stand_pack",$column_name_format);
                $qty_sp1=array_search("quantity_standard_packing",$column_name_format);
                $unit_of_qty1=array_search("unit_of_qty",$column_name_format);
                $job_qty_unit1=array_search("job_qty_unit",$column_name_format);
                $stock_unit1=array_search("stock_unit",$column_name_format);
                $length1=array_search("length",$column_name_format);
                $breadth1=array_search("breadth",$column_name_format);
                $job_details1=array_search("job_details",$column_name_format);
                $dimension1=array_search("dimension",$column_name_format);
                $gsm1=array_search("gsm",$column_name_format);
                $brand1=array_search("brand",$column_name_format);
                $color1=array_search("color",$column_name_format);
                $size1=array_search("size",$column_name_format);
                $item_location1=array_search("item_location",$column_name_format);
                $opening_stock1=array_search("opening_stock",$column_name_format);
                $min_entry_level1=array_search("min_entry_level",$column_name_format);
               
                $data_inserted=0;
                $total_error=$total_error+$column_name_err;

                $sub_cat_paper = 1;
                $sub_cat_ink =2;
                $sub_cat_plate=3;
                $sub_cat_misc=4;
                $item_stand_pack_paper=5;
                $item_stand_pack_ink=6;
                $item_stand_pack_plate=7;
                $item_stand_pack_misc=8;
                $unit_of_qty_paper=9;
                $unit_of_qty_ink=10;
                $unit_of_qty_plate=11;
                $unit_of_qty_misc=12;
                $stock_unit_paper=13;
                $stock_unit_ink=14;
                $stock_unit_plate=15;
                $stock_unit_misc=16;
              
                if($column_name_err==0)
                {
                    DB::beginTransaction();
                    // for($i=1;$i<10;$i++)   
                    for($i=1;$i<count($v);$i++)   
                    {
                        $char = '0';
                        $fl=0;
                        foreach ($v[$i] as $k=>$v1)
                        {
                            if($v[$i][$master_cat_id1]==""){
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            } 
                            if($v[$i][$sub_cat_id1]==""){
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            } 
                            if($v[$i][$item_stand_pack1]==""){
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            } 
                            if($v[$i][$unit_of_qty1]==""){
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            } 
                            if($v[$i][$qty_sp1]==""){
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            } 
                            if($v[$i][$stock_unit1]==""){
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            } 
                            if($v[$i][$item_location1]==""){
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            } 
                            if($v[$i][$opening_stock1]==""){
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            } 
                            if($v[$i][$min_entry_level1]==""){
                                $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                $fl++;
                            } 
                            if($this->import_type==0)
                            {
                                if( $v[$i][$master_cat_id1] == "Paper")
                                {
                                  foreach($w as $l)
                                  {
                                    if($l[1]==$v[$i][$sub_cat_id1])
                                          continue;
                                    if($l[5]==$v[$i][$item_stand_pack1])
                                        continue;
                                    if($l[9]==$v[$i][$unit_of_qty1])
                                        continue;
                                    if($l[13]==$v[$i][$stock_unit1])
                                        continue;
                                    }
                                    if($v[$i][$length1]==""){
                                        $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                        $fl++;
                                    } 
                                    if($v[$i][$breadth1]==""){
                                        $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                        $fl++;
                                    }
                                    if($v[$i][$gsm1]==""){
                                        $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                        $fl++;
                                    } 
                                    if($v[$i][$brand1]==""){
                                        $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                        $fl++;
                                    }   
                                }
                                if( $v[$i][$master_cat_id1] == "Inks & Chemicals")
                                {
                                
                                  foreach($w as $l){
                                    if($l[2]==$v[$i][$sub_cat_id1])
                                          continue;
                                    if($l[6]==$v[$i][$item_stand_pack1])
                                        continue;
                                    if($l[10]==$v[$i][$unit_of_qty1])
                                        continue;
                                    if($l[14]==$v[$i][$stock_unit1])
                                        continue;
                                  }
                                    if($v[$i][$brand1]==""){
                                        $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                        $fl++;
                                    } 
                                    if($v[$i][$color1]==""){
                                        $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                        $fl++;
                                    }   
                                }
                                if( $v[$i][$master_cat_id1] == "Plate")
                                {
                               
                                  foreach($w as $l){
                                    if($l[3]==$v[$i][$sub_cat_id1])
                                          continue;
                                    if($l[7]==$v[$i][$item_stand_pack1])
                                        continue;
                                    if($l[11]==$v[$i][$unit_of_qty1])
                                        continue;
                                    if($l[15]==$v[$i][$stock_unit1])
                                        continue;
                                  }  
                                  if($v[$i][$size1]==""){
                                    $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                    $fl++;
                                    }   
                                }
                                if( $v[$i][$master_cat_id1] == "Miscellaneous")
                                {
                              
                                  foreach($w as $l)
                                  {
                                    if($l[4]==$v[$i][$sub_cat_id1])
                                          continue;
                                    if($l[8]==$v[$i][$item_stand_pack1])
                                        continue;
                                    if($l[9]==$v[$i][$unit_of_qty1])
                                        continue;
                                    if($l[16]==$v[$i][$stock_unit1])
                                        continue;
                                    
                                    }
                                    if($v[$i][$item_name1]==""){
                                        $error=$error."Empty Cell at ".$this->getNameFromNumber($char).($i+1).". "; 
                                        $fl++;
                                    } 
                                }
                                
                                $char++;
                            }
                        }
                        $total_error=$total_error+$fl;
                        if($fl==0)
                        {
                            $data_err=0;
                            $master_cat = Master_item_category::where('name','like',$v[$i][$master_cat_id1])
                                ->get('id')->first();
                            if(!isset($master_cat))
                            {
                                $error = $error." Master Category at ".$this->getNameFromNumber($master_cat_id1).($i+1)." Not Exist.<br/>";
                                $data_err++;
                                $item = ['id'=>'0'];
                            }
                           
                            
                            $item_pack = Unit_of_measurement::where('uom_name','like',trim($v[$i][$item_stand_pack1]))
                            ->first('id');
                            if(!isset($item_pack['id']))
                            {
                                    $error = $error." Item Standard Packing at ".$this->getNameFromNumber($item_stand_pack1).($i+1)." Not Exist.<br/>";
                                    $data_err++;
                               
                            }
                            $unit_qty = Unit_of_measurement::where('uom_name','like',trim($v[$i][$unit_of_qty1]))
                            ->first('id');
                            if(!isset($unit_qty['id']))
                            {
                                    $error = $error." Unit Of Quantity at ".$this->getNameFromNumber($unit_of_qty1).($i+1)." Not Exist.<br/>";
                                    $data_err++;
                               
                            }
                            $stock_unit = Unit_of_measurement::where('uom_name','like',trim($v[$i][$stock_unit1]))
                            ->first('id');
                            if(!isset($stock_unit['id']))
                            {
                                    $error = $error." Stock Unit at ".$this->getNameFromNumber($stock_unit1).($i+1)." Not Exist.<br/>";
                                    $data_err++;
                               
                            }
                            $timestamp =  date('Y-m-d G:i:s');
                            $dimension = $v[$i][$dimension1];
                            
                            $total_error=$total_error+$data_err;
                            if($data_err==0)
                            {
                                if($v[$i][$master_cat_id1]=="Paper"){
                                    $item_name = $v[$i][$sub_cat_id1]." (".$v[$i][$length1]."*".$v[$i][$breadth1].")".$dimension." ".$v[$i][$qty_sp1]." ".$v[$i][$gsm1]." ".$v[$i][$brand1];
                                }
                                if($v[$i][$master_cat_id1]=="Inks & Chemicals"){
                                    $item_name = $request->input('ink_brand')." ".$ink_cat['name']." ".$request->input('ink_color')." ". $request->input('ink_item_qty');
                                }
                                if($v[$i][$master_cat_id1]=="Plate"){
                                    $item_name = $plate_cat['name']." ".$request->input('plate_item_qty')."".$request->input('plate_size');
                                }
                                if($v[$i][$master_cat_id1]=="Miscellaneous"){
                                    $item_name = $v[$i][$item_name1];
                                }
                                $data_inserted = $data_inserted+1;      
                                $stock=Stocks::insertGetId([
                                    'id'=>NULL,
                                    'master_cat_id'=>$master_cat,
                                    'sub_cat_id' => $sub_cat_id1,    
                                    'item_name' =>$item_name,
                                    'item_stand_pack' => $item_pack['id'],
                                    'qty_sp' => $v[$i][$qty_sp1],
                                    'unit_of_qty' => $unit_qty['id'],  
                                    'stock_unit' => $stock_unit['id'],    
                                    'length' =>$v[$i][$length1],
                                    'breadth' => $v[$i][$breadth1],
                                    'dimension' =>  $dimension,
                                    'gsm' => $v[$i][$gsm1],  
                                    'brand' =>$v[$i][$brand1],  
                                    'item_location' => $v[$i][$item_location1],    
                                    'opening_stock' =>$v[$i][$opening_stock1],
                                    'min_entry_level' => $v[$i][$min_entry_level1],
                                    'created_by' => Auth::id(),
                                    'created_at' => $timestamp,     
                                ]);
                            }
                        }
                        else
                            $error=$error."<br/>";
                    }
                }
                else
                $error = $error." No data is inserted.";
            }
            else
            {
                $request->session()->flash('importerrors',"");
                return redirect($redirect_to)->with('error', 'Please upload file');
            }
            
            if($total_error==0)
            {
                $request->session()->flash('importerrors',$data_inserted." Rows Inserted.");
                DB::commit();
                return redirect($redirect_to)->with('success', 'Stock has been imported!');
            }
            else if($column_name_err!=0)
            {
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
            else
            {   
                $request->session()->flash('importerrors', $error);
                DB::rollBack();
                return redirect($redirect_to)->with('error', 'Errors Found. No data inserted.');        
            }
        }
        catch (Exception $e)
        {   

        }

    }
}