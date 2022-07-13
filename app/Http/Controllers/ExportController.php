<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\Payment;
use App\Model\Country;
use App\Model\Client_po;
use App\Model\Challan_per_io;
use App\Model\Delivery_challan;
use App\Model\Client_po_consignee;
use App\Model\Client_po_party;
use App\Model\Consignee;
use App\Model\POD_upload;
use App\Model\Vehicle;
use App\Model\Goods_Dispatch;
use App\Model\InternalOrder;
use App\Model\State;
use App\Model\Party;
use App\Model\Employee\NetSalary;
use App\Model\GatePasses;
use App\Model\Settings;
use App\Model\Binding_detail;
use App\Model\Binding_item;
use App\Model\Binding_form_labels;
use App\Model\JobCard;
use App\Model\ElementFeeder;
use App\Model\PaperType;
use App\Model\Dispatch_mode;
use App\Model\Raw_Material;
use App\Model\Users;
use App\Model\jobDetails;
use App\Model\ItemCategory;
use App\Model\Tax_Invoice;
use App\Model\Utilities\Internal_DC;
use App\Model\Tax_Dispatch;
use App\Model\Tax;
use App\Model\IoType;
use App\Model\Hsn;
use App\Model\Unit_of_measurement;
use App\Model\TaxPercentageApplicable;
use App\Model\Utilities\VehicleType;
use App\Model\Utilities\Material_inwarding;
use App\Model\Utilities\Material_outwarding;
use App\Model\City;
use App\Custom\CustomHelpers;
use App\Model\Employee\Assets;
use App\Model\Employee\EmployeeProfile;
use App\Model\Employee\EmployeePFESI;
use App\Model\Department;
use App\Model\Purchase\Indent;
use App\Model\Purchase\PurchaseOrder;
use App\Model\Purchase\MiscCat;
use App\Model\Purchase\Grn;
use App\Model\Purchase\Grn_Transport;
use App\Model\Purchase\Grn_Item;
use App\Model\Purchase\Grn_File;
use App\Model\Purchase\ItemSubCat;
use App\Model\Purchase\PurchaseOrderDetail;
use App\Model\Purchase\ReturnRequest;
use App\Model\Purchase\IndentPR;
use App\Model\Stock\UomType;
use App\Model\Vendor\vendors;
use App\Model\Purchase\PurchaseReq;
use App\Model\Purchase\PurchaseIo;
use App\Model\Purchase\InkChemicalCat;
use App\Model\Purchase\PlateCat;
use App\Model\HR\HR_Leave;
use \Carbon\Carbon;
use App\Exports\DataExport;
use App\Exports\DataExportSheet;
use App\Model\HR\HR_LeaveDetails;
use App\Model\Holiday;
class ExportController extends Controller
{
	//client master export\
	public function getTableData($table,$field)
	{
		// print_r($field);die;
		if($field=="created_time"){
			$data = DB::table($table)->select(DB::raw('DATE_FORMAT(created_time,"%Y-%m-%d") as data'))->distinct()->get();
		}
		else if($field=="closed_date"){
			$data = DB::table($table)->select(DB::raw('DATE_FORMAT(closed_date,"%Y-%m-%d") as data'))->distinct()->get();
		}
		else if($field=="created_at"){
			$data = DB::table($table)->select(DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d") as data'))->distinct()->get();
		}
		else if($table=="created_user"){
			$data = DB::table('users')->select('users.name as data')->distinct()->get();
		}
		else if($table=="closed_user"){
			$data = DB::table('users')->select('users.name as data')->distinct()->get();
		}
		else{
			$data = DB::table($table)->select($field.' as data')->distinct()->get();
		}
		
		return $data;
	}

	public function export_data_consignee(){
		return $this->export_data('consignee');
	}
	public function export_data_hsn(){
		return $this->export_data('hsn');
	}
	public function export_data_uom(){
		return $this->export_data('uom');
	}
	public function export_data_paymentTerm(){
		return $this->export_data('paymentterm');
	}
	public function export_data_goodsDispatchProfile(){
		return $this->export_data('goodsinvoicedispatch');
	}
	public function export_data_party(){
		return $this->export_data('client');
	}
	public function export_data_internalOrder(){
		return $this->export_data('internalorder');
	}
	public function export_data_emp_gatepass(){
		return $this->export_data('employeegatepass');
	}
	public function export_data_ret_gatepass(){
		return $this->export_data('returnablegatepass');
	}
	public function export_data_mat_gatepass(){
		return $this->export_data('materialgatepass');
	}
	public function export_data_deliverychallan(){
		return $this->export_data('deliverychallan');
	}
	public function export_data_taxdispatch(){
		return $this->export_data('taxdispatch');
	}
	public function export_data_taxnotdispatch(){
		return $this->export_data('taxnotdispatch');
	}
	public function export_data_pfregister(){
		if(isset($_GET["month"]))
			$from = $_GET["month"];
		else 
			$from = date('m');
		if(isset($_GET["year"]))
			$to = $_GET["year"];
		else 
			$to = date('Y');
		return $this->export_data('pfregister',$from,$to);
	}

	public function export_data_esiregister(){
		if(isset($_GET["month"]))
			$from = $_GET["month"];
		else 
			$from = date('m');
		if(isset($_GET["year"]))
			$to = $_GET["year"];
		else 
			$to = date('Y');
		return $this->export_data('esiregister',$from,$to);
	}

	public function export_data_leaveregister(){
		if(isset($_GET["emp"]))
			$from = $_GET["emp"];
		else 
			$from = 0;
		if(isset($_GET["year"]))
			$to = $_GET["year"];
		else 
			$to = date('Y');
		return $this->export_data('leaveregister',$from,$to);
	}

	public function export_data_salaryA(){
		if(isset($_GET["emp"]))
			$from = $_GET["emp"];
		else 
			$from = 0;
		if(isset($_GET["month"]))
			$to = $_GET["month"];
		else 
			$to = date('m');
		return $this->export_data('salaryA_export',$from,$to);
	}
	
	public function export_data_salaryB(){
		if(isset($_GET["emp"]))
			$from = $_GET["emp"];
		else 
			$from = 0;
		if(isset($_GET["month"]))
			$to = $_GET["month"];
		else 
			$to = date('m');
		return $this->export_data('salaryB_export',$from,$to);
	}

	public function export_data_salaryC(){
		if(isset($_GET["emp"]))
			$from = $_GET["emp"];
		else 
			$from = 0;
		if(isset($_GET["month"]))
			$to = $_GET["month"];
		else 
			$to = date('m');
		return $this->export_data('salaryC_export',$from,$to);
	}

	public function export_data_salaryRegister(){
		if(isset($_GET["emp"]))
			$from = $_GET["emp"];
		else 
			$from = 0;
		if(isset($_GET["year"]))
			$to = $_GET["year"];
		else 
			$to = date('m');
		return $this->export_data('salaryRegister',$from,$to);
	}	
	

	public function export_data_taxinvoice(){
		return $this->export_data('taxinvoice');
	}
	public function export_data_jobcard(){
		return $this->export_data('jobcard');
	}
	public function export_data_clientpo(){
		return $this->export_data('clientpo');
	}

	public function export_data_inward(){
		return $this->export_data('materialinward');
	}
	public function export_data_outward(){
		return $this->export_data('materialoutward');
	}
	public function export_data_internaldc(){
		return $this->export_data('internaldc');
	}
	public function export_data_proofofdelivery(){
		return $this->export_data('proofofdelivery');
	}
	public function export_data_proofofdeliverynot(){
		return $this->export_data('proofofdeliverynot');
	}

	public function export_data_tobeclosedios(){
		return $this->export_data('tobeclosedios');
	}
	//report order to collection

	public function export_data_pendingclientpo(){
		return $this->export_data('pendingclientpo');
	}
	public function export_data_businesstracker(){
		if(isset($_GET["from"]))
			$d1=$_GET["from"];
		else 
			$d1=NULL;
			
		if(isset($_GET["to"]))
			$d2=$_GET["to"];
		else 
			$d2=NULL;
		if(isset($_GET["ref"]))
			$ref=$_GET["ref"];
		else 
			$ref=NULL;
		if(isset($_GET["party"]))
			$party=$_GET["party"];
		else 
			$party=NULL;
		return $this->export_data('businesstracker',$d1,$d2,$ref,$party);
	}
	public function export_data_dispatchvsbilling(){
		return $this->export_data('dispatchvsbilling');
	}
	public function export_data_saletracker(){
		return $this->export_data('saletracker');
	}
	public function export_data_pendingjobcard(){
		return $this->export_data('pendingjobcard');
	}
	public function export_data_pendingtaxinvoice(){
		return $this->export_data('pendingtaxinvoice');
	}
	public function export_data_pendingtaxdispatch(){
		return $this->export_data('pendingtaxdispatch');
	}
	public function export_data_pendingfdispatch(){
		return $this->export_data('pendingfdispatch');
	}

	public function export_data_ordervsbilling(){
		return $this->export_data('ordervsbilling');
	}

	public function export_data_purchase_requisition(){
		return $this->export_data('purchasereq');
	}

	public function export_data_purchase_indent(){
		return $this->export_data('purchaseindent');
	}

	public function export_data_purchase_order(){
		return $this->export_data('purchaseorder');
	}

	public function export_data_purchase_return(){
		return $this->export_data('purchasereturn');
	}
	public function export_data_ksamplingandfocorder(){
		return $this->export_data('ksamplingandfocorder');
	}
	public function export_data_noworkdoneio(){
		return $this->export_data('noworkdoneio');
	}
	public function export_data_noworkdoneiofinancial(){
		return $this->export_data('noworkdoneiofinancial');
	}
	public function export_data_employee(){
		return $this->export_data('employee');
	}
	public function export_data($id,$d1='',$d2='',$ref='',$party='')
	{
		$form=$id;
		$table = array(
			'consignee'=>'consignee'
				);
        $name=array(
			"consignee"=>"Consignee",
            "client"=>"Client",
			"hsn"=>"HSN",
            "uom"=>"Unit Of Measurement",
			"paymentterm"=>"Payment Term",
            "goodsinvoicedispatch"=>"Goods Invoice Dispatch",
            "internalorder"=>"Internal Order",
			"taxdispatch"=>"Tax Invoice Dispatch",
			"taxnotdispatch"=>"Tax Invoice Not Dispatch",
            "deliverychallan"=>"Delivery Challan",
			"employeegatepass"=>"Employee Gatepass",
			"returnablegatepass"=>"Returnable Gatepass",
			"materialgatepass"=>"Material Gatepass",
			"materialinward"=>"Material Inward",
			"internaldc"=>"Internal Delivery Challan",
			"materialoutward"=>"Material Outward",
			"taxinvoice"=>"Tax Invoice",
			"clientpo"=>"Client P.O.",
			"pendingclientpo"=>"Pending Client Po",
			"saletracker"=>"Sale Tracker",
			"pendingjobcard"=>'Pending Job Card',
			'businesstracker'=>'Business Tracker',
			"pendingtaxinvoice"=>'Unbilled Service IOs',
			"pendingtaxdispatch"=>'Pending Tax Dispatch',
			"pendingfdispatch"=>'Pending Dispatch Order Financial',
			'ordervsbilling'=>'Order vs Billing',
			'dispatchvsbilling'=>'Dispatch Vs Billing',
			"jobcard"=>"Job Card",
			"tobeclosedios"=>"To Be Cloded IOs",
			"pfregister"=>"PF Register",
			"esiregister"=>"ESI Register",
			"leaveregister" => "Leave Register",
			'salaryA_export' => "Salary A",
			'salaryB_export' => "Salary B",
			'salaryC_export' => "Salary C",
			'salaryRegister' => 'Salary Register',
			
			"purchasereq"=>"Purchase Requisition",
			"purchaseindent"=>"Purchase Indent",
			"purchaseorder"=>"Purchase Order",
			"purchasereturn"=>"Purchase Return",
			"employee"=>"Employee",
			"ksamplingandfocorder"=>"K Sampling And FOC",
			"noworkdoneio"=>"No Work Done IO",
			"noworkdoneiofinancial"=>"No Work Done IO Financial",
			"proofofdelivery"=>"Proof Of Delivery",
			"proofofdeliverynot"=>"Proof Of Delivery Not Uploaded"
		);
		$sheet_name=array(
			"consignee"=>array('Consignee'),
            "client"=>array('Client'),
			"hsn"=>array('HSN/SAC'),
            "uom"=>array('Unit Of Measurement'),
			"paymentterm"=>array('Payment Term'),
            "goodsinvoicedispatch"=>array('Goods Invoice Dispatch'),
			"internalorder"=>array('Internal Order'),
			"employeegatepass"=>array('Employee Gatepass'),
			"returnablegatepass"=>array('Returnable Gatepass'),
			"materialgatepass"=>array('Material Gatepass'),
			"materialinward"=>array("Material Inward"),
			"materialoutward"=>array("Material Outward"),
			"internaldc"=>array("Internal Delivery Challan"),
			"taxdispatch"=>array('Tax Invoice Dispatch'),
			"taxnotdispatch"=>array('Tax Invoice','Delivery Challan'),
            "deliverychallan"=>array('Delivery Challan','Delivery Challan Per IO'),
			"taxinvoice"=>array('Tax Invoice','Delivery Challan'),
			"clientpo"=>array('Client PO','Client PO Party','Client PO Consignee'),
			"pendingclientpo"=>array('Pending Client Po'),
			"saletracker"=>array('Tax Invoice','Delivery Challan'),
			"pendingjobcard"=>array('Pending Job Card'),
			"pendingtaxinvoice"=>array('Unbilled Service IOs'),
			"pendingtaxdispatch"=>array('Pending Tax Dispatch'),
			"pendingfdispatch"=>array('Pending Dispatch Order Financial'),
			'ordervsbilling'=>array('Order vs Billing'),
			'dispatchvsbilling'=>array('Dispatch Vs Billing'),
			"jobcard"=>array('Job card','Element Feeder','Raw material','Binding Process'),
			'pfregister'=>array('PF Register'),
			'esiregister'=>array('ESI Register'),
			'leaveregister' => array('Leave Register','No. of days present in each month','Dates of leaves adjusted every month','No. of leaves adjusted every month','Closing monthly leave balance'),
			'salaryA_export' => array('Salary A '),
			'salaryB_export' => array('Salary B '),
			'salaryC_export' => array('Salary C '),
			'salaryRegister' => array('Salary Register'),
			"businesstracker"=>array('Business Tracker','Details'),
			"purchasereq"=>array('Purchase Requisition','Details'),
			"purchaseindent"=>array('Purchase Indent','Details'),
			"purchaseorder"=>array('Purchase Order','Details'),
			"purchasereturn"=>array('Purchase Return'),
			"tobeclosedios"=>array("To Be Cloded IOs"),
			"employee"=>array('Employee'),
			"ksamplingandfocorder"=>array("K Sampling And FOC"),
			"noworkdoneio"=>array("No Work Done IO"),
			"noworkdoneiofinancial"=>array("No Work Done IO Financial"),
			"proofofdelivery"=>array("Proof Of Delivery"),
			"proofofdeliverynot"=>array("Proof Of Delivery Not Uploaded")
			)
			;
		$column =
		array(
			"consignee"	=>array(array('consignee.id'=>'Id','consignee.consignee_name'=>'Consignee Name','party.partyname'=>'Client Name',
							'consignee.gst'=>'GST Number','consignee.pan'=>'PAN Number','consignee.address'=>'Address','cities.city'=>'City',
							'states.name as states'=>'State','countries.name'=>'Country','consignee.pincode'=>'Pincode')),
			"client"   	=>array(array('party.id'=>'Id','party.partyname'=>'Client Name','party.contact_person'=>'Contact Person',
							'party.contact'=>'Contact','party.alt_contact'=>'Alternate Contact','party.email'=>'Email',
							'party_reference.referencename'=>'Reference Name','payment_term.value'=>'Payment Term','party.gst'=>'GST',
							'party.pan'=>'PAN','party.address'=>'Address','cities.city'=>'City','states.name as states'=>'State',
							'countries.name'=>'Country','party.pincode'=>'Pincode')),
			"hsn"		=>array(array('hsn.id'=>'Id','item_category.name'=>'Item Name','hsn.hsn'=>'HSN Code',
						'hsn.gst_rate'=>'Gst rate(%)')),

			"employeegatepass"=>array(array('gatepass.id'=>'Id','gatepass.gatepass_number'=>'Gatepass Number','gatepass.gatepass_for'=>'Gatepass For',
						'employee__profile.name'=>'Employee Name','gatepass.reason'=>'Reason','gatepass.desc'=>'Description',
						'gatepass.est_duration'=>'Estimated Duration','gatepass.created_at'=>'Timestamp')),

			"returnablegatepass"=>array(array('gatepass.id'=>'Id','gatepass.gatepass_number'=>'Gatepass Number','gatepass.gatepass_for'=>'Gatepass For',
			'gatepass.challan_type'=>'Challan Type','delivery_challan.challan_number'=>'Challan Number','internal_dc.idc_number'=>'Challan Number',
			'gatepass.remark'=>'Remark','gatepass.return_date'=>'Return Date','gatepass.created_at'=>'Timestamp')),
			
			"materialgatepass"=>array(array('gatepass.id'=>'Id','gatepass.gatepass_number'=>'Gatepass Number','gatepass.gatepass_for'=>'Gatepass For',
								'gatepass.challan_type'=>'Challan Type','delivery_challan.challan_number'=>'Challan Number','internal_dc.idc_number'=>'Challan Number',
								'dispatch_mode.name'=>'Dispatch Mode','goods_dispatch.courier_name'=>'Courier Name','gatepass.created_at'=>'Timestamp')),

			"materialinward"=>array(array('material_inward.id'=>'Id','material_inward.material_inward_number'=>'Material Inward Number',
			'material_inward.entry_for'=>'Entry For','material_inward.date'=>'Date','material_inward.vehicle_no'=>'Vehicle No','vehicle_type.name'=>'Vehicle Type',
			'material_inward.company'=>'Company Name','material_inward.item_name'=>'Item Name','material_inward.qty'=>'Quantity','material_inward.dimension'=>'Dimension',
			'material_inward.time'=>'Time','material_inward.doc_for'=>'Doc For','material_inward.invoice'=>'Invoice No','material_inward.challan'=>'Challan No',
			'material_inward.bilty'=>'Bilty','material_inward.other'=>'Other Doc','material_inward.driver_name'=>'Driver Name',
			'material_inward.driver_number'=>'Driver Number','material_inward.remark'=>'Remark','material_inward.created_at'=>'Timestamp')),


			"materialoutward"=>array(array('material_outward.id'=>'Id','material_outward.material_outward_number'=>'Material Outward Number',
			'material_outward.date'=>'Date','gatepass.gatepass_number'=>'GatePass Number','material_outward.carrier'=>'Carrier','material_outward.vehicle_no'=>'Vehicle No','vehicle_type.name'=>'Vehicle Type',
			'material_outward.mode'=>'Mode','material_outward.dispatch_to'=>'Dispatch to','item_category.name'=>'Item Name',
			'material_outward.other_item_desc'=>'Other Item','material_outward.qty'=>'Quantity','material_outward.dimension'=>'Dimension',
			'material_outward.time'=>'Time','material_outward.driver_name'=>'Driver Name','material_outward.driver_number'=>'Driver Number','material_outward.remark'=>'Remark',
			'material_outward.created_at'=>'Timestamp')),

			"internaldc"=>array(array('internal_dc.id'=>'Id',
			'internal_dc.idc_number'=>'Internal DC Number',
			'internal_dc.for'=>'For',
			'internal_dc.outsource_no'=>'Outsource No',
			'internal_dc.rate'=>'Rate',
			'hsn.hsn'=>'hsn',
			'internal_dc.date'=>'Date',
			'internal_dc.item_desc'=>'Item Description',
			'internal_dc.item_qty'=>'Quantity',
			'unit_of_measurement.uom_name'=>'Quantity Unit',
			'internal_dc.packing_desc'=>'Packing Description',
			'internal_dc.dispatch_to'=>'Dispatch to',
			'internal_dc.mode'=>'Mode',
			'goods_dispatch.courier_name'=>'Carrier',
			'internal_dc.reason'=>'Reason',
			'internal_dc.created_at'=>'Timestamp')),

			
			"uom"		=>array(array('unit_of_measurement.id'=>'Id','unit_of_measurement.uom_name'=>'Unit Of Measurement')),
			"paymentterm"=>array(array('payment_term.id'=>'Id','payment_term.value'=>'Payment Terms')),
			"goodsinvoicedispatch"=>array(array('goods_dispatch.id'=>'Id','dispatch_mode.name'=>'Dispatch By',
										'goods_dispatch.courier_name'=>'Carrier/company Name',
										'goods_dispatch.contact'=>'Contact','goods_dispatch.gst'=>'GST Number',
										'goods_dispatch.address'=>'Address')),
			"internalorder"=>array(array('internal_order.io_number'=>'Internal Order Number','internal_order.status'=>'Status',
									'internal_order.created_time'=>'Created Time','party.partyname'=>'Party Name','item_category.name as e'=>'Item Name',
									'hsn.hsn as f'=>'HSN Name','hsn.gst_rate as g'=>'GST Rate(%)','io_type.name as h'=>'IO Type',
									'unit_of_measurement.uom_name'=>'Unit Of Measurement','job_details.job_date'=>'Job Date',
									'job_details.delivery_date'=>'Delivery Date','job_details.qty'=>'Quantity','job_details.job_size'=>'Job Size',
									'job_details.dimension'=>'Dimension','job_details.rate_per_qty'=>'Rate Per Quantity','users.name as i'=>'Marketing Person',
									'job_details.details'=>'Job Details','job_details.front_color'=>'Front Colour','job_details.back_color'=>'Back Colour',
									'job_details.is_supplied_paper'=>'Paper Supplied','job_details.is_supplied_plate'=>'Plate Supplied',
									'job_details.remarks'=>'Remarks','job_details.transportation_charge'=>'Transportation charges',
									'job_details.other_charge'=>'Other Charges','bool_values.value as advanced_received'=>'Advance Received',
									'advance_io.amount'=>'Amount','mode_of_payment.value as j'=>'mode_of_payment','advance_io.date as m'=>'Received Date',
									'created_user.name as k'=>'Created By','closed_user.name as l'=>'Closed By','internal_order.closed_date'=>'Closed Date')),

			"taxdispatch"=>array(array('tax_dispatch.id'=>'Id', 'tax_invoice.invoice_number'=>'Invoice Number',
								'tax_dispatch.dispatch_mode'=>'Dispatch Mode',
								'tax_dispatch.docket_number'=>'Docket Number',
							'tax_dispatch.dispatch_date'=>'Dispatch Date'
				)),

				"tobeclosedios"=>array(
				array(
					'internal_order.io_number'=>'Internal Order Number',
					'party_reference.referencename'=>'Reference_name',
					'tax_invoice.invoice_number'=>'Tax Invoice Number',
					'item_category.name as e'=>'Item Name',
					'job_details.job_date'=>'Job Date'

				)),

				"taxnotdispatch"=>array(
					array('tax_invoice.id'=>'Id','tax_invoice.invoice_number'=>'Tax Invoice Number', 'tax_invoice.date'=>'Tax Invoice Date',
						'party.partyname'=>'Client Name','consignee.consignee_name'=>'Consignee Name',
						'tax_invoice.terms_of_delivery'=>'Terms Of Delivery','tax_invoice.gst_type'=>'GST Type',
						'tax_invoice.transportation_charge'=>'Transportation Charges','tax_invoice.other_charge'=>'Other Charges',
						'tax_invoice.total_amount'=>'Total Amount'),
					array(
						'tax_invoice.invoice_number'=>'Tax Invoice Number','delivery_challan.challan_number'=>'Delivery challan Number',
						'internal_order.io_number'=>'Internal Order Number',
						'tax.goods'=>'Description Of Goods','tax.qty'=>'Quantity','tax.rate'=>'Rate Per Peice',
						'unit_of_measurement.uom_name'=>'Unit Of Measutrement','tax.discount'=>'Discount','hsn.hsn'=>'HSN/SAC',
						'tax.transport_charges'=>'Transportation Charges',
						'tax.other_charges'=>'Other Charges','payment_term.value'=>'Payment Term','tax.amount'=>'Amount')
					),
				"pfregister"=>array( array('employee__profile.name' => 'Employee Name',
	          'employee__pfesi.pf_no' => 'PF Number')),
				"esiregister"=>array( array('employee__profile.name' => 'Employee Name',
	          'employee__pfesi.esi_no' => 'ESI Number')),
				"leaveregister" => array(
					// array('employee__profile.name' => 'Employee Name'),
	          		// array('employee__profile.employee_number' => 'Employee Number'),
	          		// array('employee__profile.employee_number' => 'Employee Number'),
	          		// array('employee__profile.employee_number' => 'Employee Number'),
	          		// array('employee__profile.employee_number' => 'Employee Number'),
	          	),

	          	"salaryA_export" => array( array('employee__profile.name' => 'Employee Name','employee__bank.acc_number' => 'Account Number','employee__bank.acc_ifsc' => 'IFSC Code','payroll__salary.net_salary' => 'Net Salary')),
	          	"salaryB_export" => array( array('employee__profile.name' => 'Employee Name','employee__bank.acc_number' => 'Account Number','employee__bank.acc_ifsc' => 'IFSC Code','payroll__salary.net_salary' => 'Net Salary')),
	          	"salaryC_export" => array( array('employee__profile.name' => 'Employee Name','employee__bank.acc_number' => 'Account Number','employee__bank.acc_ifsc' => 'IFSC Code','payroll__salary.net_salary' => 'Net Salary')),

	          	'salaryRegister' => array(
	          	 // array('employee__profile.name' => 'Employee Name','employee__profile.employee_number' => 'Employee Number','employee__profile.local_address' => 'Address','employee__profile.designation' => 'Designation','department.department' => 'Department','employee__profile.father_name' => 'Father')
	          	),

			
			"deliverychallan"=>array(
				array(
					'delivery_challan.challan_number'=>'Delivery Challan Number','party.partyname'=>'Client Name','consignee.consignee_name'=>'Consignee Name',
					'delivery_challan.total_amount'=>'Total Amount','dispatch_mode.name'=>'Dispatch By',
					'goods_dispatch.courier_name'=>'carrier/Company Name',
					'delivery_challan.bilty_docket'=>'Bilty Docket','delivery_challan.docket_date'=>'Docket Date',
					'vehicle.vehicle_number'=>'Vehicle Number','delivery_challan.delivery_date'=>'Delivery Date'),
				array(
					'delivery_challan.challan_number'=>'Delivery Challan Number','internal_order.io_number'=>'Internal Order Number',
					'item_category.name'=>'Item Name',
					'challan_per_io.good_qty'=>'Quantity','unit_of_measurement.uom_name'=>'Per','hsn.hsn'=>'HSN/SAC',
					'challan_per_io.good_desc'=>'Goode Description','challan_per_io.packing_details'=>'Packing Details',
					'challan_per_io.rate'=>'Rate Per Peice','hsn.gst_rate'=>'GST Rate(%)','challan_per_io.amount'=>'Amount')
				),
			"taxinvoice"=>array(
				array('tax_invoice.id'=>'Id','tax_invoice.invoice_number'=>'Tax Invoice Number', 'tax_invoice.date'=>'Tax Invoice Date',
					'party.partyname'=>'Client Name','consignee.consignee_name'=>'Consignee Name',
					'tax_invoice.terms_of_delivery'=>'Terms Of Delivery','tax_invoice.gst_type'=>'GST Type',
					'tax_invoice.transportation_charge'=>'Transportation Charges','tax_invoice.other_charge'=>'Other Charges',
					'tax_invoice.total_amount'=>'Total Amount'),
				array(
					'tax_invoice.invoice_number'=>'Tax Invoice Number','delivery_challan.challan_number'=>'Delivery challan Number',
					'internal_order.io_number'=>'Internal Order Number',
					'tax.goods'=>'Description Of Goods','tax.qty'=>'Quantity','tax.rate'=>'Rate Per Peice',
					'unit_of_measurement.uom_name'=>'Unit Of Measutrement','tax.discount'=>'Discount','hsn.hsn'=>'HSN/SAC',
					'tax.transport_charges'=>'Transportation Charges',
					'tax.other_charges'=>'Other Charges','payment_term.value'=>'Payment Term','tax.amount'=>'Amount')
				),
			"jobcard"=>array(
				array(
					'job_card.id'=>'Id','job_card.job_number'=>'Job Number','internal_order.io_number'=>'Internal Order Number','job_card.job_qty'=>'Quantity',
					'job_card.creative_name'=>'Creative Name','job_card.open_size'=>'Open Size','job_card.close_size'=>'Close Size',
					'job_card.dimension'=>'Dimension','bool_values.value'=>'Job Sample Received','job_card.remarks'=>'Remarks',
					'item_category.name'=>'Item Name','job_card.description'=>'Description','created_user.name'=>'Created By',
					'job_card.status'=>'Status','closed_user.name'=>'Closed By','job_card.closed_date'=>'Closed Date'),
				array(
					'job_card.job_number'=>'Job Card Number','element_type.name'=>'Element Name','element_feeder.plate_size'=>'Plate Size',
					'element_feeder.plate_sets'=>'Plate Sets','element_feeder.impression_per_plate'=>'Impression Per Plate',
					'element_feeder.front_color'=>'Front Colour','element_feeder.back_color'=>'Back Colour',
					'element_feeder.no_of_pages'=>'No Of Pages'
				),
				array(
					'job_card.job_number'=>'Job Card Number','element_type.name'=>'Element Name','raw_material.paper_size'=>'Paper Size',
					'paper_type.name as b'=>'Paper Name','raw_material.paper_gsm'=>'Paper GSM','raw_material.paper_mill'=>'Paper Mill',
					'raw_material.paper_brand'=>'Paper Brand','raw_material.no_of_sheets'=>'No Of Pages'),
				array(
					'job_card.job_number'=>'Job Card Number','element_type.name'=>'Element Name',
					'binding_details.value'=>'Bindng Process Details For',
					'binding_details.remark'=>'Bindng Process Remark For')
				),
			"clientpo"=>array(
				array( 
					'client_po.id'=>'Id','party_reference.referencename'=>'Reference_name','internal_order.io_number'=>'Internal Order Number',
					'bool_values.value as a'=>'PO Provided','client_po.po_number'=>'PO Number','client_po.po_date'=>'PO Date',
					'hsn.hsn'=>'HSN/SAC','client_po.item_desc'=>'Item Description','client_po.delivery_date'=>'Delivery Date',
					'client_po.qty'=>'Quantity','unit_of_measurement.uom_name'=>'Per','client_po.per_unit_price'=>'Per Unit Price',
					'client_po.discount'=>'Discount'
				),
				array( 
					'client_po_party.party_name' => 'Party Id','client_po.po_number' => 'PO Number',
					'party_reference.referencename' => 'Reference Name','internal_order.io_number' => 'Internal Order Number',
					'party.partyname' => 'Client Name','payment_term.value' => 'Payment Terms',
					'bool_values.value  as  a'=>'Consignee Exists'	
				),
				array(
					'consignee.id'=>'Consignee Id','party_reference.referencename' => 'Reference Name',
					'client_po.po_number'=>'PO Nummber','internal_order.io_number'=>'Internal Order Number',
					'party.partyname' => 'Client Name','consignee.consignee_name'=>'Consignee Name',
					'client_po_consignee.qty'=>'Quantity'
				)
				),
				"pendingclientpo"=>array(array(
					'internal_order.id'=>'Id',
					'internal_order.io_number'=>'Internal Order Number',
					'io_type.name as io_type'=>'IO Type',
				'internal_order.created_time'=>'IO Date',
				'party_reference.referencename'=>'Reference Name',
				'item_category.name'=>'Item Name',
				'job_details.qty'=>'Quantity')),
				"saletracker"=>array(
					array('tax_invoice.id'=>'Id',
					'tax_invoice.invoice_number'=>'Tax Invoice Number',
						'party.partyname'=>'Client Name',
						'consignee.consignee_name'=>'Consignee Name',
						'tax_invoice.date'=>'Tax Invoice Date',
						'tax.amount'=>'Amount(Without Tax)',
						'tax_invoice.total_amount'=>'Total Amount'
					),
					array(
						'tax_invoice.invoice_number'=>'Tax Invoice Number','delivery_challan.challan_number'=>'Delivery challan Number',
						'internal_order.io_number'=>'Internal Order Number','item_category.name'=>'Item Name')
					),
					"businesstracker"=>array(
						array('tax_invoice.id'=>'Id',
						'tax_invoice.invoice_number'=>'Tax Invoice Number',
							'tax_invoice.date'=>'Tax Invoice Date',
							'tax.amount'=>'Amount(Without Tax)',
							'tax_invoice.total_amount'=>'Total Amount'
						),
						array(
							'tax_invoice.invoice_number'=>'Tax Invoice Number','delivery_challan.challan_number'=>'Delivery challan Number',
							'internal_order.io_number'=>'Internal Order Number','item_category.name'=>'Item Name')
						),
					"pendingjobcard"=>array(array('internal_order.id'=>'Id','internal_order.io_number'=>'Internal Order Number','party_reference.referencename'=>'Reference Name')),
					"pendingtaxinvoice"=>array(array('internal_order.id'=>'Id','internal_order.io_number'=>'Internal Order Number','job_details.qty'=>'Quantity')),
					"pendingtaxdispatch"=>array(array(
						'internal_order.id'=>'Id',
						'internal_order.io_number'=>'Internal Order Number',
						'job_details.qty'=>'Quantity')),
					"pendingfdispatch"=>array(array(
						'internal_order.io_number'=>'Internal Order Number',
						'io_type.name as iotype'=>'IO Type',
						'job_details.job_date as created_time'=>'Job Date',
						'item_category.name as item_name'=>'Item Name',
						'party_reference.referencename'=>'Reference Name',
						'job_details.qty'=>'Quantity',
						'challan_per_io.good_qty as dispatch_qty' => 'Dispatch Quantity',
						'job_details.rate_per_qty'=>'IO Rate',
						'challan_per_io.good_qty as remaining_qty'=>'Remaining Quantity'
					)),
					"dispatchvsbilling"=>array(array('internal_order.id'=>'Id','internal_order.io_number'=>'Internal Order Number','job_details.qty'=>'Quantity')),
					"ordervsbilling"=>array(array('internal_order.id'=>'Id','internal_order.io_number'=>'Internal Order Number','job_details.qty'=>'Quantity')),

					
					"purchasereq"=>array(
						array(
							'pur_purchase_req.purchase_req_number'=>'Purchase Requisition Number','employee__profile.name'=>'Requested By','pur_purchase_req.item_req_for'=>'Item Requirement For',
							'internal_order.io_number'=>'Internal Order','pur_purchase_req.required_date'=>'Required Date',
							'users.name'=>'Created By','pur_purchase_req.created_time'=>'Created Time'),
						array(
							'pur_purchase_req.purchase_req_number'=>'Purchase Requisition Number','pur_purchase_io.item_desc'=>'Item Desc',
							'pur_purchase_io.item_qty'=>'Item Qty','unit_of_measurement.uom_name'=>'UOM')
						),
						"purchaseindent"=>array(
							array(
								'pur_indent.indent_num'=>'Purchase Indent Number','master_item_category.name'=>'Item Requirement For','item_sub_category.name'=>'Sub Category','stock.item_name'=>'Item Name','pur_indent.item_qty'=>'Item Qty','unit_of_measurement.uom_name'=>'Qty Unit',
								'pur_indent.item_req_date'=>'Item Required Date','pur_indent.for'=>'For',
								'users.name'=>'Created By'),
							array(
								'pur_indent.indent_num'=>'Purchase Indent Number','pur_purchase_req.purchase_req_number'=>'Purchase Requitision Number',
								'pur_indent_pr.qty'=>'Qty')
						),
						"purchaseorder"=>array(
							array(
								'pr_purchase_order.po_num'=>'Purchase Order','pr_purchase_order.po_date'=>'PO Date','pur_indent.indent_num'=>'Purchase Indent Number','vendor.name'=>'Vendor','payment_term.value'=>'Payment Term','master_item_category.name'=>'Master Item','pr_purchase_order.remark'=>'Remark','pr_purchase_order.status'=>'Status',
								'users.name'=>'Created By'),
							array(
								'pr_purchase_order.po_num'=>'Purchase Order','item_sub_category.name'=>'Sub Category','stock.item_name'=>'Item Name',
								'pr_purchase_order_details.item_qty'=>'Qty','unit_of_measurement.uom_name'=>'Qty Unit','tax_per_applicable.value'=>'Tax Percent','pr_purchase_order_details.delivery_date'=>'Delivery Date',
								'pr_purchase_order_details.item_rate'=>'Rate','job_card.job_number'=>'Job Card')
						),
						"purchasereturn"=>array(
							array(
								'pur_return_request.return_number'=>'Purchase Return Number','pur_return_request.date'=>'Date','users.name'=>'Approved By','pr_purchase_order.po_num'=>'purchase Order','pur_grn.grn_number'=>'GRN Number',
								'pur_return_request.supp_name'=>'Supplier Name','pur_return_request.reason'=>'Reason','pur_return_request.item_desc'=>'Item Desc','pur_return_request.item_qty_received'=>'Qty Received',
								'pur_return_request.item_qty_returned'=>'Qty Returned','unit_of_measurement.uom_name'=>'Qty Unit','pur_return_request.payment_desc'=>'Payment Desc')
						),
			"employee"=>array(

					array('employee__profile.employee_number'=>'Employee No.','employee__profile.name'=>'Name','employee__profile.father_name'=>'Father Name','employee__profile.dob'=>'Date Of Birth','employee__profile.local_address'=>'Local Address','employee__profile.permanent_address'=>'Permanent Address','employee__profile.home_landline'=>'Home Landline','employee__profile.mobile'=>'Mobile','employee__profile.family_number'=>'Family Number','employee__profile.relation_with_emp'=>'Relation with Employee','employee__profile.doj'=>'Joining Date','employee__profile.designation'=>'Designation','employee__profile.employee_skill'=>'Skill','employee__profile.shifting_timing'=>'Shift Timing','employee__profile.email'=>'Email','employee__profile.aadhar'=>'Aadhar No.','department.department'=>'Department','users.name as users'=>'Reporting Head')
			),
			"ksamplingandfocorder"=>array(
				array('internal_order.id'=>'ID','internal_order.io_number'=>'IO Number',
					'io_type.name as io_type'=>'IO Type',
					'party_reference.referencename'=>'Reference_name',
					'item_category.name as item_name'=>'Item Name',
					'job_details.qty as io_qty'=>'IO Quantity',
					'job_details.rate_per_qty as io_rate'=>'IO Rate',
					'advance_io.amount AS advance_amt'=>'Advance Amount',
					'advance_io.mode_of_receive as advance_mode'=>'Advance Mode')

			),
			"noworkdoneio"=>array(
				array('internal_order.id'=>'Id',
					'internal_order.io_number'=>'IO Number',
					'io_type.name'=>'IO Type',
					'party_reference.referencename'=>'Reference_name',
					'internal_order.created_time'=>'IO Date',
					'item_category.name as item_name'=>'Item Name',
					'job_details.qty as io_qty'=>'IO Quantity'
				)
			),
			"noworkdoneiofinancial"=>array(
				array('internal_order.id'=>'Id',
					'internal_order.io_number'=>'IO Number',
					'io_type.name'=>'IO Type',
					'party_reference.referencename'=>'Reference_name',
					'internal_order.created_time'=>'IO Date',
					'item_category.name as item_name'=>'Item Name',
					'job_details.qty as io_qty'=>'IO Quantity',
					'job_details.rate_per_qty as io_rate'=>'IO Rate','amount'=>'Amount')
			),
			"proofofdelivery"=>array(array(
				'delivery_challan.challan_number'=>'Challan Number',
				'pod_upload.pod_recieved'=>'POD Recieved on'
			)),
			"proofofdeliverynot"=>array(array(
				'delivery_challan.challan_number'=>'Challan Number',
				'party_reference.referencename'=>'Reference_name',
				'party.partyname'=>'Client Name',
				'delivery_challan.delivery_date'=>'Delivery Date',
				'delivery_challan.total_amount'=>'Amount'
			))

		);
        if(isset($name[$id]))
        {
			$title='Export '.$name[$id];
			$columns = $column[$id];
            $data=array(
                'layout'=>'layouts.main',
				'title'=>$title,
				'sheet_name'=>$sheet_name[$id],
				'form'=>$form,
				'd1'=>$d1,
				'd2'=>$d2,
				'ref'=>$ref,
				'party'=>$party,
				'columns'=>$columns,
            );
            return view('sections.export_form', $data);
		}
        else
            return abort(404);

	}
	public function proofofdeliverynot(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['delivery_challan.challan_number'=>'Challan Number',
		'party_reference.referencename'=>'Reference_name',
		'party.partyname'=>'Client Name',
		'delivery_challan.delivery_date'=>'Delivery Date',
		'delivery_challan.total_amount'=>'Amount'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['delivery_challan.challan_number',
			'party_reference.referencename',
			'party.partyname',
			'delivery_challan.delivery_date',
			'delivery_challan.total_amount'];
			$outcolumn =['Challan Number',
			'Reference_name',
			'Client Name',
			'Delivery Date',
			'Amount','Item Name','Quantity'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = Delivery_challan::leftjoin('pod_upload','delivery_challan.id','pod_upload.dc_id')
		->leftjoin('challan_per_io','challan_per_io.delivery_challan_id','delivery_challan.id')
        ->leftjoin('internal_order','challan_per_io.io','internal_order.id')
        ->leftJoin('item_category',function($join){
            $join->on('internal_order.item_category_id','=','item_category.id');
       })
        ->leftJoin('party_reference','delivery_challan.reference_name','party_reference.id')
        ->leftjoin('party','delivery_challan.party_id','party.id')
        ->whereNull('pod_upload.dc_id')->select(
	DB::raw('group_concat(DISTINCT(delivery_challan.challan_number)) as challan_number'),
	DB::raw('group_concat(DISTINCT(party_reference.referencename)) as referencename'),
	DB::raw('group_concat(DISTINCT(party.partyname)) as partyname'),
			DB::raw('group_concat(DISTINCT(delivery_challan.delivery_date)) as delivery_date'),
			DB::raw('group_concat(DISTINCT(delivery_challan.total_amount)) as total_amount'),
			
		
			DB::raw('group_concat(DISTINCT(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name))) as itemss'),
			DB::raw('(SUM(challan_per_io.good_qty))as qtys')
		   )->groupBy('delivery_challan.id');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('delivery_challan.id','desc');			
		}
		$db_data=$db_data->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'proofofdeliverynotuploaded'), 'proof of delivery not uploaded.xlsx');
	}
	public function proofofdelivery(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['delivery_challan.challan_number'=>'Challan Number',
		'pod_upload.pod_recieved'=>'POD Recieved on'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['delivery_challan.challan_number','delivery_challan.delivery_date',
			'pod_upload.pod_recieved','party.partyname'];
			$outcolumn =['Challan Number','Delivery Date',
			'POD Recieved on','Client Name','Item Name','Quantity'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data =POD_upload::leftjoin('delivery_challan','delivery_challan.id','pod_upload.dc_id')
        ->leftjoin('challan_per_io','challan_per_io.delivery_challan_id','delivery_challan.id')
        ->leftjoin('internal_order','challan_per_io.io','internal_order.id')
        ->leftjoin('party','delivery_challan.party_id','party.id')
        ->leftJoin('item_category',function($join){
            $join->on('internal_order.item_category_id','=','item_category.id');
       })
		->select(
			'delivery_challan.challan_number',
			'delivery_challan.delivery_date',
			'pod_upload.pod_recieved',
			'party.partyname',
			DB::raw('group_concat(DISTINCT(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name))) as itemss'),
        DB::raw('(SUM(challan_per_io.good_qty))as qtys')
       )->groupBy('delivery_challan.id');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('pod_upload.id','desc');			
		}
		$db_data=$db_data->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'proofofdelivery'), 'proof of delivery.xlsx');
	}
	public function pendingclientpo(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['internal_order.id'=>'Id','internal_order.io_number'=>'Internal Order Number','io_type.name'=>'IO Type',
		'internal_order.created_time'=>'IO Date','party_reference.referencename'=>'Reference Name','item_category.name'=>'Item Name','job_details.qty'=>'Quantity'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['internal_order.id','internal_order.io_number','io_type.name as io_type','internal_order.created_time','party_reference.referencename','item_category.name as item',
			'job_details.qty'];
			$outcolumn =['Id','Internal Order Number','Reference Name','IO Type','IO Quantity','Created Time','Item Name'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data =  $userlog = InternalOrder::leftJoin('client_po','client_po.io','internal_order.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->where('io_type.name','<>','Scrap Sale')
        ->where('io_type.name','<>','K Sampling')
        ->where('io_type.name','<>','FOC')
        ->where('internal_order.status','Open')
        ->where('client_po.io', null);
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('internal_order.id','desc');			
		}
		$db_data=$db_data->select('internal_order.id',
        'internal_order.io_number',
		'party_reference.referencename',
		'io_type.name as ioType',
		'job_details.qty',
        'internal_order.created_time',
        DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name')
        )->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'pendingclientpo'), 'pending client po.xlsx');
	}
	public function pendingjobcard(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['internal_order.id'=>'Id','internal_order.io_number'=>'Internal Order Number','party_reference.referencename'=>'Reference Name'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['internal_order.id','internal_order.io_number','party_reference.referencename',
			'io_type.name','internal_order.created_time','job_details.qty'];
			$outcolumn =['Id','Internal Order Number','Reference Name','IO Type','IO Date','Item Name','IO Quantity'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = InternalOrder::leftJoin('job_card','job_card.io_id','internal_order.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->whereNotIn('job_details.io_type_id',array('2','3','8','9'))
        ->where('job_card.io_id', null)
        ->where('internal_order.status','Open');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('internal_order.id','desc');			
		}
		$db_data=$db_data->select('internal_order.id',
        'internal_order.io_number',
        'party_reference.referencename','io_type.name',
        DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as created_time'),
        DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
        'job_details.qty'
        )->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'pendingjobcard'), 'pending job card.xlsx');
	}
	public function pendingtaxinvoice(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['internal_order.id'=>'Id','internal_order.io_number'=>'Internal Order Number','job_details.qty'=>'Quantity'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['internal_order.id','internal_order.io_number','job_details.qty'];
			$outcolumn =['Id','Internal Order Number','Reference Name','IO Type','Item Name','IO Quantity','Tax Quantity','Remaining Quantity','IO Date'];	
			
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data  = InternalOrder::leftjoin('job_details','job_details.id','internal_order.job_details_id')
        ->leftJoin('tax','tax.io_id','internal_order.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('internal_order.id','desc');			
		}
		$db_data=$db_data    ->select('internal_order.id','internal_order.io_number',
		
		
	'party_reference.referencename',
	'io_type.name',
	DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
	'job_details.qty',DB::raw('SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END) as taxqty'),
		
		DB::raw('(job_details.qty - SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END)) as diffqty'),
		DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as created_time')		
	)
	->where('job_details.io_type_id',8)
	// ->where('tax.io_id',NULL)
	->HavingRaw('(job_details.qty - SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END))  > 0')->groupBy('internal_order.id')
   ->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'pendingtaxinvoice'), 'pending taxinvoice.xlsx');
	}
	public function pendingtaxdispatch(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['internal_order.id'=>'Id',
		'internal_order.io_number'=>'Internal Order Number',
		'job_details.qty'=>'Quantity'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['internal_order.id',
			'internal_order.io_number',
			'party_reference.referencename',
			'io_type.name as iotype',
			'item_category.name as item',
			'internal_order.created_time','job_details.qty'];
			$outcolumn =['Id',
			'Internal Order Number',
			'Reference',
			'IO Type',
			'Item Name',
			'IO Date',
			'IO Quantity',
			'Dispatch Quantity',
			'Remaining Quantity'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data =InternalOrder::where('internal_order.status','Open')
        ->where('job_details.io_type_id','<>',8)
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('tax','tax.io_id','internal_order.id')
        ->leftjoin('challan_per_io','challan_per_io.io','internal_order.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id') 
        ;
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('internal_order.id','desc');			
		}
		$db_data=$db_data ->select(
			'internal_order.id',
        'internal_order.io_number',
        'party_reference.referencename',
        'io_type.name as iotype',
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item'),
        DB::raw('DATE_FORMAT(internal_order.created_time,"%d-%m-%Y") as created_time'),
		'job_details.qty',
        DB::raw('SUM(CASE WHEN challan_per_io.good_qty Is NULL THEN "0" ELSE challan_per_io.good_qty END) as dispatch_qty'),
        DB::raw('(job_details.qty - SUM(CASE WHEN challan_per_io.good_qty Is NULL THEN "0" ELSE challan_per_io.good_qty END))as remaining_qty')
        )
        ->HavingRaw('(job_details.qty - SUM(CASE WHEN challan_per_io.good_qty Is NULL THEN "0" ELSE challan_per_io.good_qty END)) > 0')->groupBy('internal_order.id')
        ->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'pendingtaxdispatch'), 'pending taxdispatch.xlsx');
	}

	public function tobeclosedios(Request $request,$column=[]){
		$outcolumn = [];
		$outcolumn1 = ['internal_order.io_number'=>'Internal Order Number',
		'party_reference.referencename'=>'Reference_name',
		'tax_invoice.invoice_number'=>'Tax Invoice Number',
		'item_category.name as e'=>'Item Name',
		'job_details.job_date'=>'Job Date'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['internal_order.io_number',
			'party_reference.referencename',
			'tax_invoice.invoice_number',
			'item_category.name as e',
			'job_details.job_date'];
			$outcolumn =[
				'Internal Order Number',
				'Reference_name',
				'Tax Invoice Number',
				'Item Name',
				'Job Date',
			'IO Qty',
			'Tax Qty',
			'Total Invoice Qty','Total Invoice Amount',
			'Payment Received','Payment Left'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = InternalOrder::leftjoin('job_details','job_details.id','internal_order.job_details_id')
        ->leftjoin('tax',function($join){
            $join->on('tax.io_id', '=','internal_order.id');
            $join->where('tax.is_cancelled',0);
        })
        ->leftJoin('tax_invoice','tax_invoice.id','tax.tax_invoice_id')
        ->leftJoin('tax as tx','tax_invoice.id','tx.tax_invoice_id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftJoin('coll__payment_recieved','coll__payment_recieved.tax_invoice_id','tax.tax_invoice_id')
        ;
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('tax_invoice.id','desc');			
		}
		$db_data=$db_data  ->select(
		
		'io_number',
		'party_reference.referencename',
		DB::raw('group_concat(DISTINCT(invoice_number)) as invoice_number'),
		DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
	    DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as job_date'),

	   'job_details.qty as io_qty',
	   DB::raw('(IFNULL(
		(SELECT SUM(m.qty) FROM tax m 
		WHERE m.io_id=internal_order.id
			GROUP BY internal_order.id) ,0 ) 
		) AS tax_qty'),
		DB::raw('(IFNULL(
			(SELECT SUM(m.qty) FROM tax m 
			WHERE m.tax_invoice_id=tax_invoice.id
				GROUP BY tax_invoice.id) ,0 ) 
			) AS total_tax_qty'),
			DB::raw('(IFNULL(
				(SELECT SUM(m.total_amount) FROM tax_invoice m 
				WHERE m.id=tax.tax_invoice_id
					GROUP BY tax_invoice.id) ,0 ) 
				) AS total_amount'),
	

		DB::raw('(IFNULL(
			(SELECT SUM(p.pr_amount) FROM coll__payment_recieved p 
			WHERE p.tax_invoice_id = tax_invoice.id
				GROUP BY p.tax_invoice_id) ,0 ) 
			) as amt_received'),
		// 	DB::raw('(IFNULL(
		// 		(SELECT (p.status) FROM coll__payment_recieved p 
		// 		WHERE p.tax_invoice_id = tax_invoice.id
		// 			GROUP BY p.tax_invoice_id) ,"pending") 
		// 		) as status'),
		// DB::raw('(job_details.qty - 
		// (IFNULL(
		// 	(SELECT SUM(m.qty) FROM tax m 
		// 	WHERE m.io_id=internal_order.id
		// 		GROUP BY internal_order.id) ,0 ) 
		// 	)
		// )  as binding_qty_left'),
	
		

		
		DB::raw('(IFNULL(
			(SELECT SUM(m.total_amount) FROM tax_invoice m 
			WHERE m.id=coll__payment_recieved.tax_invoice_id
				GROUP BY coll__payment_recieved.tax_invoice_id) ,0 ) 
			)-(IFNULL(
				(SELECT SUM(p.pr_amount) FROM coll__payment_recieved p 
				WHERE p.tax_invoice_id = tax_invoice.id
					GROUP BY p.tax_invoice_id) ,0 ) 
				) AS amt_left')

				

		// DB::raw('(SUM(tax_invoice.total_amount)-(IFNULL(SUM(coll__payment_recieved.pr_amount),"0")))as leftamt'),
		// DB::raw('(job_details.qty - SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END)) as diffqty'),
		// // 'job_details.left_qty as diffqty',
		// 'internal_order.io_number'
	)
	->where('coll__payment_recieved.tax_invoice_id','!=',NULL)
	// ->orwhere('coll__payment_recieved.status',"=",'closed')
	->WhereRaw('(((IFNULL(
		(SELECT SUM(m.total_amount) FROM tax_invoice m 
		WHERE m.id=coll__payment_recieved.tax_invoice_id
			GROUP BY coll__payment_recieved.tax_invoice_id) ,0 ) 
		)-(IFNULL(
			(SELECT SUM(p.pr_amount) FROM coll__payment_recieved p 
			WHERE p.tax_invoice_id = tax_invoice.id
				GROUP BY p.tax_invoice_id) ,0 ) 
			) < 1) AND (job_details.qty - 
			(IFNULL(
				(SELECT SUM(m.qty) FROM tax m 
				WHERE m.io_id=internal_order.id
					GROUP BY internal_order.id) ,0 ) 
				)
			) = 0) OR (IFNULL(
				(SELECT p.status FROM coll__payment_recieved p 
				WHERE p.tax_invoice_id = tax_invoice.id
					GROUP BY p.tax_invoice_id) ,"pending" ) 
				) = "closed"')
				->groupBy('internal_order.id','coll__payment_recieved.tax_invoice_id')
		->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'tobeclosedios'), 'to be closed ios.xlsx');
	}
	public function pendingfdispatch(Request $request,$column=[]){
		$outcolumn = [];
		$outcolumn1 = ['internal_order.io_number'=>'Internal Order Number',
						'io_type.name as iotype'=>'IO Type',
						'job_details.job_date as created_time'=>'Job Date',
						'item_category.name as item_name'=>'Item Name',
						'party_reference.referencename'=>'Reference Name',
						'job_details.qty'=>'Quantity',
						'challan_per_io.good_qty as dispatch_qty' => 'Dispatch Quantity',
						'job_details.rate_per_qty'=>'IO Rate',
						'challan_per_io.good_qty as remaining_qty'=>'Remaining Quantity'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['internal_order.io_number',
			'io_type.name as iotype','job_details.job_date as created_time',
			'item_category.name as item_name',
			'party_reference.referencename',
			'job_details.qty',
			'challan_per_io.good_qty as dispatch_qty','job_details.rate_per_qty',
			'challan_per_io.good_qty as remaining_qty'];
			$outcolumn =[
			'Internal Order Number',
			'IO Type',
			'Job Date',
			'Item Name',
			'Reference Name',
			'Quantity',
			'Dispatch Quantity','IO Rate',
			'Remaining Quantity'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data =InternalOrder::where('internal_order.status','Open')
        ->where('job_details.io_type_id','<>',8)
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('tax','tax.io_id','internal_order.id')
        ->leftjoin('challan_per_io','challan_per_io.io','internal_order.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id') 
        ;
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('internal_order.id','desc');			
		}
		$db_data=$db_data ->select(
			'internal_order.id',
        'internal_order.io_number',
        'party_reference.referencename',
        'io_type.name as iotype',
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
        DB::raw('DATE_FORMAT(internal_order.created_time,"%d-%m-%Y") as created_time'),
		'job_details.qty',
        DB::raw('SUM(CASE WHEN challan_per_io.good_qty Is NULL THEN "0" ELSE challan_per_io.good_qty END) as dispatch_qty'),
        DB::raw('(job_details.qty - SUM(CASE WHEN challan_per_io.good_qty Is NULL THEN "0" ELSE challan_per_io.good_qty END))as remaining_qty'),'job_details.rate_per_qty as io_rate',
        DB::raw('(job_details.qty)*(job_details.rate_per_qty) as amount')
        )
        ->HavingRaw('(job_details.qty - SUM(CASE WHEN challan_per_io.good_qty Is NULL THEN "0" ELSE challan_per_io.good_qty END)) > 0')->groupBy('internal_order.id')
        ->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'pendingfdispatch'), 'pending dispatch financial.xlsx');
	}
	public function dispatchvsbilling(Request $request,$column=[]){
		$outcolumn = [];
		$outcolumn1 = ['internal_order.id'=>'Id','internal_order.io_number'=>'Internal Order Number','job_details.qty'=>'Quantity'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['internal_order.id','internal_order.io_number','party_reference.referencename',
			'io_type.name','job_details.qty',];
			$outcolumn =['Id','Internal Order Number','Reference','IO Type','Item Name',
			'IO Quantity',
			'Dispatch Quantity',
			'Tax Invoice Quantity',
			'Dispatch Unbilled Quantity','Unbilled Order Quantity','IO Date'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = InternalOrder::where('internal_order.status','Open')
        ->where('job_details.io_type_id','<>',8)
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
		->leftjoin('tax',function($join){
            $join->on('tax.io_id', '=','internal_order.id');
            $join->where('tax.is_cancelled',0);
        })
        ->leftjoin('challan_per_io','challan_per_io.io','internal_order.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('internal_order.id','desc');			
		}
		$db_data=$db_data   ->select('internal_order.id',
		'internal_order.io_number',
		'party_reference.referencename',
		'io_type.name',
		DB::raw('(CASE WHEN item_category.name = "Other" THEN internal_order.other_item_name ELSE item_category.name END) AS item_name'),
		'job_details.qty as io_qty',
		
        DB::raw('group_concat(DISTINCT(concat(challan_per_io.id,":",challan_per_io.good_qty))) as dispatch_qty'),
		DB::raw('group_concat(DISTINCT(concat(tax.id,":",tax.qty))) as taxqty'),
		DB::raw('(SUM(IFNULL(challan_per_io.good_qty,0))-SUM(IFNULL(tax.qty,0)))as unbilled_qty'),
		DB::raw('(job_details.qty-SUM(IFNULL(tax.qty,0)))as unbilled_order_qty'),
		DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as created_time')
        ) 
        // ->HavingRaw('(SUM(IFNULL(challan_per_io.good_qty,0))-SUM(IFNULL(tax.qty,0))) <> 0  ')
		->groupBy('tax.io_id','challan_per_io.io')->get();
		
		foreach($db_data as $value){
			$dispatch_qty=0;
			$dispatch=$value['dispatch_qty'];
			$dispatch_ext=explode(',',$dispatch);

			$tax_qty=0;
			$tax=$value['taxqty'];
			$tax_ext=explode(',',$tax);

			if($dispatch_ext[0]!=''){
				foreach($dispatch_ext as $item){
					$dis=explode(':',$item);
					if(isset($dis[1])){
						$dispatch_qty=$dispatch_qty+$dis[1];
						}
					
				}
			}
			
			if($tax_ext[0]!=''){
				foreach($tax_ext as $item){
					
					$tx=explode(':',$item);
					if(isset($tx[1])){
					$tax_qty=$tax_qty+$tx[1];
					}
					
					// print_r($tx[1]);
				}
			}

			$value['dispatch_qty']=$dispatch_qty;
			$value['taxqty']=$tax_qty;
			$value['unbilled_qty']=$value['dispatch_qty']-$value['taxqty'];
			$value['unbilled_order_qty']=$value['io_qty']-$value['taxqty'];
		}
		return Excel::download(new DataExport($db_data,$outcolumn,'dispatchvsbilling'), 'dispatch vs billing.xlsx');
	}
	public function ordervsbilling(Request $request,$column=[]){
		$outcolumn = [];
		$outcolumn1 = ['internal_order.id'=>'Id','internal_order.io_number'=>'Internal Order Number','job_details.qty'=>'Quantity'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['internal_order.id','internal_order.io_number','job_details.qty','party_reference.referencename',
			'io_type.name'];
			$outcolumn =['Id','Internal Order Number','Reference','IO Type','Item Name','IO Quantity','Tax Quantity',
			'Unbilled Quantity','Tax Invoice','IO Date'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data =InternalOrder::where('internal_order.status','Open')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftjoin('tax',function($join){
            $join->on('tax.io_id', '=','internal_order.id');
            $join->where('tax.is_cancelled',0);
        })
        ->leftjoin('tax_invoice','tax.tax_invoice_id','tax_invoice.id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->whereNotIn('job_details.io_type_id',array('5','6'));
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('internal_order.id','desc');			
		}
		$db_data=$db_data   ->select('internal_order.id',
        'internal_order.io_number',
        
		'party_reference.referencename',
		'io_type.name',
        DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
		'job_details.qty as io_qty', 
		DB::raw('SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END) as taxqty'),
        DB::raw('(job_details.qty - SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END))as unbilled_qty'),
		DB::raw('group_concat(tax_invoice.invoice_number) as tax_invoice_no') ,
		DB::raw('DATE_FORMAT(job_details.job_date,"%d-%m-%Y") as created_time')
        )
		->HavingRaw('(job_details.qty - SUM(CASE WHEN tax.qty Is NULL THEN "0" ELSE tax.qty END)) > 0')
		->groupBy('internal_order.id')
		->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'ordervsbilling'), 'Order vs Billing.xlsx');
	}
	public function saletracker(Request $request,$column=[])
    {
		$sheet = array('tax_invoice','delivery_challan');
		$outcolumn1 = array(
			'tax_invoice' =>array('tax_invoice.id'=>'Id','tax_invoice.invoice_number'=>'Tax Invoice Number',
				'party.partyname'=>'Client Name','consignee.consignee_name'=>'Consignee Name',
				'tax_invoice.date'=>'Tax Invoice Date'),
			'delivery_challan' =>array(
				'tax_invoice.invoice_number'=>'Tax Invoice Number','delivery_challan.challan_number'=>'Delivery challan Number',
				'internal_order.io_number'=>'Internal Order Number','item_category.name'=>'Item Name'
			)
		)
		;

		$col =array(
			'tax_invoice' =>array(
				'tax_invoice.id',
		'tax_invoice.invoice_number',
		'party.partyname',
		'consignee.consignee_name',
		'tax_invoice.date',
		DB::raw('SUM((tax.rate * tax.qty) - ((tax.rate * tax.qty) * (tax.discount/100)) ) as amount'),
		'tax_invoice.total_amount'),
			'delivery_challan'=>array(
				'tax_invoice.invoice_number','delivery_challan.challan_number','internal_order.io_number','item_category.name')	
		);
		$outcol =array(
			'tax_invoice' =>array(
				'Id','Tax Invoice Number','Client Name','Consignee Name','Tax Invoice Date','Amount(Without Tax)','Total Amount'),
			'delivery_challan' =>array(
				'Tax Invoice Number','Delivery challan Number','Internal Order Number','Item Name'
			)
		);		

		for($i=0;$i<count($sheet);$i++)
		{
			$column[$sheet[$i]]=[];
			$outcolumn[$sheet[$i]]=[];
			
			if($request->input('columns_in_excel'.$i)=='')
			{
				$column[$sheet[$i]] = $col[$sheet[$i]];
				$outcolumn[$sheet[$i]] =$outcol[$sheet[$i]];
			}
			else
			{
				$column[$sheet[$i]] = $request->input('columns_in_excel'.$i);
				foreach($column[$sheet[$i]] as $k)
					$outcolumn[$sheet[$i]] =array_merge($outcolumn[$sheet[$i]],array($outcolumn1[$sheet[$i]][$k]));
			}
		}
		$db_data['tax_invoice'] = Tax_Invoice::leftJoin('consignee','tax_invoice.consignee_id', 'consignee.id')
		->leftJoin('party','tax_invoice.party_id', 'party.id')
		->rightJoin('tax','tax_invoice.id',  'tax.tax_invoice_id')
		->leftJoin('hsn','hsn.id','=','tax.hsn')
		// ->rightJoin('internal_order','internal_order.id',  'tax.io_id')
		// ->leftjoin('delivery_challan','tax.delivery_challan_id', 'delivery_challan.id')
		->where('tax_invoice.is_cancelled',0)
		->where('tax_invoice.is_active',1)
		->groupBy('tax_invoice.id',
		'tax_invoice.invoice_number',
			'party.partyname',
			'consignee.consignee_name',
			'tax_invoice.terms_of_delivery',
			'tax_invoice.total_amount')
	   ;;

		$db_data['delivery_challan'] = Tax_Invoice::where('tax_invoice.is_active','=',1)
		->where('tax.is_active','=','1')
		->leftJoin('tax','tax_invoice.id','=','tax.tax_invoice_id')
		->leftJoin('delivery_challan','tax.delivery_challan_id','=','delivery_challan.id')
		->rightJoin('internal_order','internal_order.id',  'tax.io_id')
		->leftjoin('item_category','internal_order.item_category_id','item_category.id')
		->where('tax_invoice.is_cancelled',0)
		->select('tax_invoice.invoice_number','delivery_challan.challan_number','internal_order.io_number','item_category.name');
		;
		for($i=0;$i<count($sheet);$i++)
			if($request->input('search_in_excel'.$i)!='')
			{
				$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
			}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy($col,$by);
				$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy($col,$by);
				}
			}
		else
		{
			$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy('tax_invoice.id','desc');			
			$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy('tax_invoice.id','desc');			
		}
		$data = $db_data[$sheet[0]]->select(DB::raw('tax_invoice.id'))->get();
		$db_data[$sheet[0]] = $db_data[$sheet[0]]->select($column[$sheet[0]])->get();
		for($i=1;$i<count($sheet);$i++)
			$db_data[$sheet[$i]] = $db_data[$sheet[$i]]
			->whereIn('tax_invoice.id',$data)
			->select($column[$sheet[$i]])->get();
		return Excel::download(new DataExportSheet($db_data,$outcolumn,$sheet), 'sale tracker.xlsx');
	}
	public function businesstracker(Request $request,$column=[])
    {
		$d1=$_GET["from"];
		$d2=$_GET["to"];
		$ref=$_GET["ref"];
		$party=$_GET["party"];
		
		$sheet = array('tax_invoice','delivery_challan');
		$outcolumn1 = array(
			'tax_invoice' =>array('tax_invoice.id'=>'Id','tax_invoice.invoice_number'=>'Tax Invoice Number',
				'party.partyname'=>'Client Name','consignee.consignee_name'=>'Consignee Name',
				'tax_invoice.date'=>'Tax Invoice Date','amount'=>'Amount(Without Tax)','tax_invoice.total_amount'=>'Total Amount'),
			'delivery_challan' =>array(
				'tax_invoice.invoice_number'=>'Tax Invoice Number','delivery_challan.challan_number'=>'Delivery challan Number',
				'internal_order.io_number'=>'Internal Order Number','item_category.name'=>'Item Name'
			)
		)
		;

		$col =array(
			'tax_invoice' =>array(
				'tax_invoice.id',
		'tax_invoice.invoice_number',
		'party.partyname',
		'consignee.consignee_name',
		'tax_invoice.date',
		DB::raw('SUM((tax.rate * tax.qty) - ((tax.rate * tax.qty) * (tax.discount/100)) ) as amount'),
		'tax_invoice.total_amount'),
			'delivery_challan'=>array(
				'tax_invoice.invoice_number','delivery_challan.challan_number','internal_order.io_number','item_category.name')	
		);
		$outcol =array(
			'tax_invoice' =>array(
				'Id','Tax Invoice Number','Client Name','Consignee Name','Tax Invoice Date','Amount(Without Tax)','Total Amount'),
			'delivery_challan' =>array(
				'Tax Invoice Number','Delivery challan Number','Internal Order Number','Item Name'
			)
		);		

		for($i=0;$i<count($sheet);$i++)
		{
			$column[$sheet[$i]]=[];
			$outcolumn[$sheet[$i]]=[];
			
			if($request->input('columns_in_excel'.$i)=='')
			{
				$column[$sheet[$i]] = $col[$sheet[$i]];
				$outcolumn[$sheet[$i]] =$outcol[$sheet[$i]];
			}
			else
			{
				$column[$sheet[$i]] = $request->input('columns_in_excel'.$i);
				foreach($column[$sheet[$i]] as $k)
					$outcolumn[$sheet[$i]] =array_merge($outcolumn[$sheet[$i]],array($outcolumn1[$sheet[$i]][$k]));
			}
		}
		$db_data['tax_invoice'] = Tax_Invoice::leftJoin('consignee','tax_invoice.consignee_id', 'consignee.id')
		->leftJoin('party','tax_invoice.party_id', 'party.id')
		->rightJoin('tax','tax_invoice.id',  'tax.tax_invoice_id')
		->leftJoin('hsn','hsn.id','=','tax.hsn')
		->where('tax_invoice.is_cancelled',0)
		// ->rightJoin('internal_order','internal_order.id',  'tax.io_id')
		// ->leftjoin('delivery_challan','tax.delivery_challan_id', 'delivery_challan.id')

		->where('tax_invoice.is_active',1)
		->groupBy('tax_invoice.id',
		'tax_invoice.invoice_number',
			'party.partyname',
			'consignee.consignee_name',
			'tax_invoice.terms_of_delivery',
			'tax_invoice.total_amount')
	   ;;
	  

		$db_data['delivery_challan'] = Tax_Invoice::where('tax_invoice.is_active','=',1)
		->where('tax.is_active','=','1')
		->leftJoin('tax','tax_invoice.id','=','tax.tax_invoice_id')
		->leftJoin('delivery_challan','tax.delivery_challan_id','=','delivery_challan.id')
		->rightJoin('internal_order','internal_order.id',  'tax.io_id')
		->leftjoin('item_category','internal_order.item_category_id','item_category.id')
		->where('tax_invoice.is_cancelled',0)
		->select('tax_invoice.invoice_number','delivery_challan.challan_number','internal_order.io_number','item_category.name');
		;
		if(!empty($ref)){
			$db_data['tax_invoice']->where(function($query) use ($ref){
				$query->where('party.reference_name','=',$ref);
			}); 
			$db_data['delivery_challan']->where(function($query) use ($ref){
				$query->where('delivery_challan.reference_name','=',$ref);
			});       
		}
		if(!empty($party)){
			$db_data['tax_invoice']->where(function($query) use ($party){
				$query->where('tax_invoice.party_id','=',$party);
			}); 
			$db_data['delivery_challan']->where(function($query) use ($party){
				$query->where('delivery_challan.party_id','=',$party);
			});       
		}
		if(!empty($d1) && empty($d2))
		{
		 $db_data['tax_invoice']->where(function($query) use ($d1){
				$query->where('tax_invoice.date','>=',$d1);
			}); 
			$db_data['delivery_challan']->where(function($query) use ($d1){
				$query->where('tax_invoice.date','>=',$d1);
			});                
		}else if(!empty($d2) && empty($d1)){
		 $db_data['tax_invoice']->where(function($query) use ($d2){
				$query->where('tax_invoice.date','<=',$d2);
			});   
			$db_data['delivery_challan']->where(function($query) use ($d2){
				$query->where('tax_invoice.date','<=',$d2);
			});   
		}else if(!empty($d2) && !empty($d1)){
		 $db_data['tax_invoice']->where(function($query) use ($d1,$d2){
				$query->whereBetween('tax_invoice.date',array($d1,$d2));
			});
			$db_data['delivery_challan']->where(function($query) use ($d1,$d2){
				$query->whereBetween('tax_invoice.date',array($d1,$d2));
			});   
		}
		for($i=0;$i<count($sheet);$i++)
			if($request->input('search_in_excel'.$i)!='')
			{
				$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
			}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy($col,$by);
				$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy($col,$by);
				}
			}
		else
		{
			$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy('tax_invoice.id','desc');			
			$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy('tax_invoice.id','desc');			
		}
		$data = $db_data[$sheet[0]]->select(DB::raw('tax_invoice.id'))->get();
		$db_data[$sheet[0]] = $db_data[$sheet[0]]->select($column[$sheet[0]])->get();
		for($i=1;$i<count($sheet);$i++)
			$db_data[$sheet[$i]] = $db_data[$sheet[$i]]
			->whereIn('tax_invoice.id',$data)
			->select($column[$sheet[$i]])->get();
		return Excel::download(new DataExportSheet($db_data,$outcolumn,$sheet), 'businesstracker.xlsx');
    }
	public function clientpo(Request $request,$column=[])
    {
		$sheet = array('client_po','client_po_party','client_po_consignee');
		$outcolumn1 = array(
			'client_po' =>array( 
				'client_po.id'=>'Id','party_reference.referencename'=>'Reference_name','internal_order.io_number'=>'Internal Order Number',
				'bool_values.value as a'=>'PO Provided','client_po.po_number'=>'PO Number','client_po.po_date'=>'PO Date',
				'hsn.hsn'=>'HSN/SAC','client_po.item_desc'=>'Item Description',
				'client_po.delivery_date'=>'Delivery Date','client_po.qty'=>'Quantity','unit_of_measurement.uom_name'=>'Per',
				'client_po.per_unit_price'=>'Per Unit Price','client_po.discount'=>'Discount',
			),
			'client_po_party'=>array(
				'client_po_party.party_name' => 'Party Id',
				'party_reference.referencename' => 'Reference Name','internal_order.io_number' => 'Internal Order Number',
				'party.partyname' => 'Client Name','payment_term.value' => 'Payment Terms',
				'bool_values.value  as  a'=>'Consignee Exists'
			),
			'client_po_consignee' =>array(
				'consignee.id'=>'Consignee Id','party_reference.referencename' => 'Reference Name',
				'internal_order.io_number'=>'Internal Order Number',
				'party.partyname' => 'Client Name','consignee.consignee_name'=>'Consignee Name',
				'client_po_consignee.qty'=>'Quantity'
			)
		);
		
		$col = array(
			'client_po' =>array(
				'client_po.id','party_reference.referencename','internal_order.io_number','bool_values.value as a','client_po.po_number',
				'client_po.po_date','hsn.hsn','client_po.item_desc','client_po.delivery_date',
				'client_po.qty','unit_of_measurement.uom_name','client_po.per_unit_price','client_po.discount',
			),
			'client_po_party'=>array(
				'client_po_party.party_name','party_reference.referencename','internal_order.io_number',
				'party.partyname','payment_term.value','bool_values.value as a'
			),
			'client_po_consignee' =>array(
				'consignee.id','party_reference.referencename','internal_order.io_number',
				'party.partyname','consignee.consignee_name','client_po_consignee.qty'
				)
			);
		$outcol =array(
			'client_po' =>array('Id','Reference Name','Internal Order Number','PO Provided','PO Number','PO Date','HSN/SAC','Item Description'
				,'Delivery Date','Quantity','Per','Per Unit Price','Discount'
			),
			'client_po_party'=>array('Party Id','Reference Name','Internal Order Number','Client Name',
				'Payment Terms','Consignee Exists'
			),
			'client_po_consignee' =>array(
				'Consignee Id','Reference Name','Internal Order Number','Client Name','Consignee Name','Quantity'
			)
		);		

		for($i=0;$i<count($sheet);$i++)
		{
			$column[$sheet[$i]]=[];
			$outcolumn[$sheet[$i]]=[];
			
			if($request->input('columns_in_excel'.$i)=='')
			{
				$column[$sheet[$i]] = $col[$sheet[$i]];
				$outcolumn[$sheet[$i]] =$outcol[$sheet[$i]];
			}
			else
			{
				$column[$sheet[$i]] = $request->input('columns_in_excel'.$i);
				foreach($column[$sheet[$i]] as $k)
					$outcolumn[$sheet[$i]] =array_merge($outcolumn[$sheet[$i]],array($outcolumn1[$sheet[$i]][$k]));
			}
		}
		$db_data['client_po'] = Client_po::where('client_po.is_active','=',1)
			->leftJoin('party_reference','party_reference.id','client_po.reference_name')
			->leftJoin('internal_order','internal_order.id','=','client_po.io')
			->leftJoin('bool_values', 'bool_values.id','=', 'client_po.is_po_provided')
			->leftJoin('hsn', 'hsn.id','=', 'client_po.hsn')
			->leftJoin('unit_of_measurement', 'unit_of_measurement.id','=', 'client_po.unit_of_measure');
	
		$db_data['client_po_party'] = Client_po_party::where('client_po.is_active','=','1')
			->leftJoin('client_po','client_po.id','client_po_party.client_po_id')
			->leftJoin('party_reference','party_reference.id','client_po.reference_name')
			->leftJoin('party','party.id','client_po_party.party_name')
			->leftJoin('internal_order','internal_order.id','client_po.io')
			->leftJoin('payment_term','payment_term.id','client_po_party.payment_terms')
			->leftJoin('bool_values','bool_values.id','client_po_party.is_consignee');
		
		$db_data['client_po_consignee'] = Client_po_consignee::where('client_po.is_active','=','1')
			->leftJoin('client_po_party','client_po_party.id','=','client_po_consignee.client_po_party_id')
			->leftJoin('client_po','client_po.id','client_po_party.client_po_id')
			->leftJoin('party_reference','party_reference.id','client_po.reference_name')
			->leftJoin('party','party.id','client_po_consignee.party_id')
			->leftJoin('internal_order','internal_order.id','=','client_po.io')		
			->leftJoin('consignee','consignee.id','=','client_po_consignee.consignee_id');
		for($i=0;$i<count($sheet);$i++)
			if($request->input('search_in_excel'.$i)!='')
			{
				$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
			}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy($col,$by);
				$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy($col,$by);
				$db_data[$sheet[2]] = $db_data[$sheet[2]]->orderBy($col,$by);
				}
			}
		else
		{
			$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy('client_po.id','desc');			
			$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy('client_po.id','desc');			
			$db_data[$sheet[2]] = $db_data[$sheet[2]]->orderBy('client_po.id','desc');			
		}
		$data = $db_data[$sheet[0]]->select(DB::raw('client_po.id'))->get();
		$db_data[$sheet[0]] = $db_data[$sheet[0]]->select($column[$sheet[0]])->get();
		for($i=1;$i<count($sheet);$i++)
			$db_data[$sheet[$i]] = $db_data[$sheet[$i]]
			->whereIn('client_po.id',$data)
			->select($column[$sheet[$i]])->get();
			return Excel::download(new DataExportSheet($db_data,$outcolumn,$sheet), 'client po.xlsx');
    }

	public function jobcard(Request $request,$column=[])
    {
		$sheet = array('job_card_details','element_details','raw_material_details','binding_process_details');
		$outcolumn1 = array(
			'job_card_details' =>array(
				'job_card.id'=>'Id','job_card.job_number'=>'Job Number','internal_order.io_number'=>'Internal Order Number','job_card.job_qty'=>'Quantity',
				'job_card.creative_name'=>'Creative Name','job_card.open_size'=>'Open Size','job_card.close_size'=>'Close Size',
				'job_card.dimension'=>'Dimension','bool_values.value'=>'Job Sample Received','job_card.remarks'=>'Remarks',
				'item_category.name'=>'Item Name','job_card.description'=>'Description','created_user.name'=>'Created By',
				'job_card.status'=>'Status','closed_user.name'=>'Closed By','job_card.closed_date'=>'Closed Date'),
			'element_details' =>array(
				'job_card.job_number'=>'Job Card Number','element_type.name'=>'Element Name','element_feeder.plate_size'=>'Plate Size',
				'element_feeder.plate_sets'=>'Plate Sets','element_feeder.impression_per_plate'=>'Impression Per Plate',
				'element_feeder.front_color'=>'Front Colour','element_feeder.back_color'=>'Back Colour',
				'element_feeder.no_of_pages'=>'No Of Pages'
			),
			'raw_material_details' =>array(
				'job_card.job_number'=>'Job Card Number','element_type.name'=>'Element Name','raw_material.paper_size'=>'Paper Size',
				'paper_type.name as b'=>'Paper Name','raw_material.paper_gsm'=>'Paper GSM','raw_material.paper_mill'=>'Paper Mill',
				'raw_material.paper_brand'=>'Paper Brand','raw_material.no_of_sheets'=>'No Of Pages'),
			'binding_process_details' =>array(
				'job_card.job_number'=>'Job Card Number','element_type.name'=>'Element Name',
				'binding_details.value'=>'Bindng Process Details For',
				'binding_details.remark'=>'Bindng Process Remark For')
		);

		$col =array(
			'job_card_details' =>array('job_card.id','job_card.job_number','internal_order.io_number','job_card.job_qty',
				'job_card.creative_name','job_card.open_size','job_card.close_size','job_card.dimension','bool_values.value',
				'job_card.remarks','item_category.name','job_card.description','created_user.name as created_user','job_card.status',
				'closed_user.name as closed_user','job_card.closed_date'),
			'element_details' =>array('job_card.job_number','element_type.name','element_feeder.plate_size',
				'element_feeder.plate_sets','element_feeder.impression_per_plate','element_feeder.front_color',
				'element_feeder.back_color','element_feeder.no_of_pages'
			),
			'raw_material_details' =>array('job_card.job_number','element_type.name','raw_material.paper_size',
				'paper_type.name as b','raw_material.paper_gsm','raw_material.paper_mill','raw_material.paper_brand',
				'raw_material.no_of_sheets'),
			'binding_process_details' =>array('job_card.job_number','element_type.name','binding_details.value','binding_details.remark'),
			
			);
		$outcol =array(
			'job_card_details' =>array(
				'Id','Job Number','Internal Order Number','Quantity','Creative Name','Open Size','Close Size','Dimension',
				'Job Sample Received','Remarks','Item Name','Description','Created By','Status','Closed By','Closed Date'),
			'element_details' =>array('Job Card Number','Element Name','Plate Size','Plate Sets','Impression Per Plate',
				'Front Colour','Back Colour','No Of Pages'
			),
			'raw_material_details' =>array('Job Card Number','Element Name','Paper Size','Paper Name','Paper GSM','Paper Mill',
				'Paper Brand','No Of Sheets'),
			'binding_process_details' =>array('Job Card Number','Element Name','Bindng Process Details For','Bindng Process Remark For' )
			);		

		for($i=0;$i<count($sheet);$i++)
		{
			$column[$sheet[$i]]=[];
			$outcolumn[$sheet[$i]]=[];
			
			if($request->input('columns_in_excel'.$i)=='')
			{
				$column[$sheet[$i]] = $col[$sheet[$i]];
				$outcolumn[$sheet[$i]] =$outcol[$sheet[$i]];
			}
			else
			{
				$column[$sheet[$i]] = $request->input('columns_in_excel'.$i);
				foreach($column[$sheet[$i]] as $k)
					$outcolumn[$sheet[$i]] =array_merge($outcolumn[$sheet[$i]],array($outcolumn1[$sheet[$i]][$k]));
			}
		}
		$db_data['job_card_details'] = JobCard::where('job_card.is_active','=',1)
		->leftJoin('internal_order','internal_order.id','=','job_card.io_id')
		->leftJoin('bool_values', 'bool_values.id','=', 'job_card.job_sample_received')
		->leftJoin('item_category', 'item_category.id','=', 'job_card.item_category_id')
		->leftJoin('users as created_user', 'created_user.id','=', 'job_card.created_by')
		->leftJoin('users as closed_user', 'closed_user.id','=', 'job_card.closed_by');

		$db_data['element_details'] = ElementFeeder::where('job_card.is_active','=','1')
		->leftJoin('job_card','job_card.id','=','element_feeder.job_card_id')
		->leftJoin('element_type','element_type.id','=','element_feeder.element_type_id');

		$db_data['raw_material_details'] = Raw_Material::where('job_card.is_active','=','1')
		->leftJoin('job_card','job_card.id','=','raw_material.job_card_id')
		->leftJoin('element_type','element_type.id','=','raw_material.element_type_id')
		->leftJoin('paper_type','paper_type.id','=','raw_material.paper_type_id');

		$db_data['binding_process_details'] = Binding_detail::where('job_card.is_active','=','1')
		->leftJoin('job_card','job_card.id','=','binding_details.job_card_id')
		->leftJoin('element_type','element_type.id','=','binding_details.element_type_id')		
		;
		for($i=0;$i<count($sheet);$i++)
			if($request->input('search_in_excel'.$i)!='')
			{
				$x=explode(' ',$request->input('search_in_excel'.$i))[0];
				if($x=="job_card.created_time" || $x=="job_card.closed_date"){
					$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],'LIKE',"%".$request->input('search_val_in_excel'.$i)."%"); 
				}
				else{
					$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
				}
				
			}
			
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy($col,$by);
				$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy($col,$by);
				$db_data[$sheet[2]] = $db_data[$sheet[2]]->orderBy($col,$by);
				$db_data[$sheet[3]] = $db_data[$sheet[3]]->orderBy($col,$by);
				}
			}
		else
		{
			$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy('job_card.id','desc');			
			$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy('job_card.id','desc');			
			$db_data[$sheet[2]] = $db_data[$sheet[2]]->orderBy('job_card.id','desc');			
			$db_data[$sheet[3]] = $db_data[$sheet[3]]->orderBy('job_card.id','desc');			
		}
		$data = $db_data[$sheet[0]]->select(DB::raw('job_card.id'))->get();
		$db_data[$sheet[0]] = $db_data[$sheet[0]]->select($column[$sheet[0]])->get();
		for($i=1;$i<count($sheet);$i++)
			$db_data[$sheet[$i]] = $db_data[$sheet[$i]]
			->whereIn('job_card.id',$data)
			->select($column[$sheet[$i]])->get();
			return Excel::download(new DataExportSheet($db_data,$outcolumn,$sheet), 'job_card.xlsx');
    }

	public function taxinvoice(Request $request,$column=[])
    {
		$sheet = array('tax_invoice','delivery_challan');
		$outcolumn1 = array(
			'tax_invoice' =>array('tax_invoice.id'=>'Id','tax_invoice.invoice_number'=>'Tax Invoice Number','tax_invoice.date'=>'Tax Invoice Date',
				'party.partyname'=>'Client Name','consignee.consignee_name'=>'Consignee Name',
				'tax_invoice.terms_of_delivery'=>'Terms Of Delivery','tax_invoice.gst_type'=>'GST Type',
				'tax_invoice.transportation_charge'=>'Transportation Charges','tax_invoice.other_charge'=>'Other Charges',
				'tax_invoice.created_at'=>'Created Time','tax_invoice.total_amount'=>'Total Amount'),
			'delivery_challan' =>array(
				'tax_invoice.invoice_number'=>'Tax Invoice Number','delivery_challan.challan_number'=>'Delivery challan Number',
				'internal_order.io_number'=>'Internal Order Number',
				'tax.goods'=>'Description Of Goods','tax.qty'=>'Quantity','tax.rate'=>'Rate Per Peice',
				'unit_of_measurement.uom_name'=>'Unit Of Measutrement','tax.discount'=>'Discount','hsn.hsn'=>'HSN/SAC',
				'tax.transport_charges'=>'Transportation Charges',
				'tax.other_charges'=>'Other Charges','payment_term.value'=>'Payment Term','tax.amount'=>'Amount'
			)
		)
		;

		$col =array(
			'tax_invoice' =>array(
				'tax_invoice.id','tax_invoice.invoice_number','tax_invoice.date','party.partyname','consignee.consignee_name',
				'tax_invoice.terms_of_delivery','tax_invoice.gst_type','tax_invoice.transportation_charge',
				'tax_invoice.other_charge','tax_invoice.created_at','tax_invoice.total_amount'),
			'delivery_challan'=>array(
				'tax_invoice.invoice_number','delivery_challan.challan_number','internal_order.io_number',
				'tax.goods','tax.qty','tax.rate','unit_of_measurement.uom_name','tax.discount','hsn.hsn','tax.transport_charges',
				'tax.other_charges','payment_term.value','tax.amount')	
		);
		$outcol =array(
			'tax_invoice' =>array(
				'Id','Tax Invoice Number','Tax Invoice Date','Client Name','Consignee Name','Terms Of Delivery','GST Type',
				'Transportation Charges','Other Charges','Created Time','Total Amount'),
			'delivery_challan' =>array(
				'Tax Invoice Number','Delivery challan Number','Internal Order Number','Description Of Goods','Quantity',
				'Rate Per Peice','Unit Of Measutrement','Discount','HSN/SAC','Transportation Charges','Other Charges',
				'Payment Term','Amount'
			)
		);		

		for($i=0;$i<count($sheet);$i++)
		{
			$column[$sheet[$i]]=[];
			$outcolumn[$sheet[$i]]=[];
			
			if($request->input('columns_in_excel'.$i)=='')
			{
				$column[$sheet[$i]] = $col[$sheet[$i]];
				$outcolumn[$sheet[$i]] =$outcol[$sheet[$i]];
			}
			else
			{
				$column[$sheet[$i]] = $request->input('columns_in_excel'.$i);
				foreach($column[$sheet[$i]] as $k)
					$outcolumn[$sheet[$i]] =array_merge($outcolumn[$sheet[$i]],array($outcolumn1[$sheet[$i]][$k]));
			}
		}
		$db_data['tax_invoice'] = Tax_Invoice::where('tax_invoice.is_active','=',1)
		->leftJoin('party', 'party.id','=', 'tax_invoice.party_id')
		->leftJoin('consignee', 'consignee.id','=', 'tax_invoice.consignee_id');
		$db_data['delivery_challan'] = Tax_Invoice::where('tax_invoice.is_active','=',1)
		->where('tax.is_active','=','1')
		->leftJoin('tax','tax_invoice.id','=','tax.tax_invoice_id')
		->leftJoin('delivery_challan','tax.delivery_challan_id','=','delivery_challan.id')
		->leftJoin('internal_order','internal_order.id','=','tax.io_id')
		->leftJoin('hsn', 'tax.hsn','=', 'hsn.id')
		->leftJoin('payment_term', 'tax.payment','=', 'payment_term.id')
		->leftJoin('unit_of_measurement', 'unit_of_measurement.id','=', 'tax.per')
		;
		for($i=0;$i<count($sheet);$i++)
			if($request->input('search_in_excel'.$i)!='')
			{
				$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
			}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy($col,$by);
				$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy($col,$by);
				}
			}
		else
		{
			$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy('tax_invoice.id','desc');			
			$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy('tax_invoice.id','desc');			
		}
		$data = $db_data[$sheet[0]]->select(DB::raw('tax_invoice.id'))->get();
		$db_data[$sheet[0]] = $db_data[$sheet[0]]->select($column[$sheet[0]])->get();
		for($i=1;$i<count($sheet);$i++)
			$db_data[$sheet[$i]] = $db_data[$sheet[$i]]
			->whereIn('tax_invoice.id',$data)
			->select($column[$sheet[$i]])->get();
		return Excel::download(new DataExportSheet($db_data,$outcolumn,$sheet), 'tax invoice.xlsx');
    }
	public function taxnotdispatch(Request $request,$column=[])
    {
		$sheet = array('tax_invoice','delivery_challan');
		$outcolumn1 = array(
			'tax_invoice' =>array('tax_invoice.id'=>'Id','tax_invoice.invoice_number'=>'Tax Invoice Number','tax_invoice.date'=>'Tax Invoice Date',
				'party.partyname'=>'Client Name','consignee.consignee_name'=>'Consignee Name',
				'tax_invoice.terms_of_delivery'=>'Terms Of Delivery','tax_invoice.gst_type'=>'GST Type',
				'tax_invoice.transportation_charge'=>'Transportation Charges','tax_invoice.other_charge'=>'Other Charges',
				'tax_invoice.created_at'=>'Created Time','tax_invoice.total_amount'=>'Total Amount'),
			'delivery_challan' =>array(
				'tax_invoice.invoice_number'=>'Tax Invoice Number','delivery_challan.challan_number'=>'Delivery challan Number',
				'internal_order.io_number'=>'Internal Order Number',
				'tax.goods'=>'Description Of Goods','tax.qty'=>'Quantity','tax.rate'=>'Rate Per Peice',
				'unit_of_measurement.uom_name'=>'Unit Of Measutrement','tax.discount'=>'Discount','hsn.hsn'=>'HSN/SAC',
				'tax.transport_charges'=>'Transportation Charges',
				'tax.other_charges'=>'Other Charges','payment_term.value'=>'Payment Term','tax.amount'=>'Amount'
			)
		)
		;

		$col =array(
			'tax_invoice' =>array(
				'tax_invoice.id','tax_invoice.invoice_number','tax_invoice.date','party.partyname','consignee.consignee_name',
				'tax_invoice.terms_of_delivery','tax_invoice.gst_type','tax_invoice.transportation_charge',
				'tax_invoice.other_charge','tax_invoice.created_at','tax_invoice.total_amount'),
			'delivery_challan'=>array(
				'tax_invoice.invoice_number','delivery_challan.challan_number','internal_order.io_number',
				'tax.goods','tax.qty','tax.rate','unit_of_measurement.uom_name','tax.discount','hsn.hsn','tax.transport_charges',
				'tax.other_charges','payment_term.value','tax.amount')	
		);
		$outcol =array(
			'tax_invoice' =>array(
				'Id','Tax Invoice Number','Tax Invoice Date','Client Name','Consignee Name','Terms Of Delivery','GST Type',
				'Transportation Charges','Other Charges','Created Time','Total Amount'),
			'delivery_challan' =>array(
				'Tax Invoice Number','Delivery challan Number','Internal Order Number','Description Of Goods','Quantity',
				'Rate Per Peice','Unit Of Measutrement','Discount','HSN/SAC','Transportation Charges','Other Charges',
				'Payment Term','Amount'
			)
		);		

		for($i=0;$i<count($sheet);$i++)
		{
			$column[$sheet[$i]]=[];
			$outcolumn[$sheet[$i]]=[];
			
			if($request->input('columns_in_excel'.$i)=='')
			{
				$column[$sheet[$i]] = $col[$sheet[$i]];
				$outcolumn[$sheet[$i]] =$outcol[$sheet[$i]];
			}
			else
			{
				$column[$sheet[$i]] = $request->input('columns_in_excel'.$i);
				foreach($column[$sheet[$i]] as $k)
					$outcolumn[$sheet[$i]] =array_merge($outcolumn[$sheet[$i]],array($outcolumn1[$sheet[$i]][$k]));
			}
		}
		$db_data['tax_invoice'] = Tax_Invoice::where('tax_invoice.is_active','=',1)
		->leftJoin('tax_dispatch','tax_dispatch.tax_invoice_id', 'tax_invoice.id')
		->leftJoin('party', 'party.id','=', 'tax_invoice.party_id')
		->leftJoin('consignee', 'consignee.id','=', 'tax_invoice.consignee_id')->where('tax_dispatch.id','=',NULL)->where('tax_invoice.is_cancelled','=',0);

		$db_data['delivery_challan'] = Tax_Invoice::where('tax_invoice.is_active','=',1)
		->where('tax.is_active','=','1')
		->leftJoin('tax_dispatch','tax_dispatch.tax_invoice_id', 'tax_invoice.id')
		->leftJoin('tax','tax_invoice.id','=','tax.tax_invoice_id')
		->leftJoin('delivery_challan','tax.delivery_challan_id','=','delivery_challan.id')
		->leftJoin('internal_order','internal_order.id','=','tax.io_id')
		->leftJoin('hsn', 'tax.hsn','=', 'hsn.id')
		->leftJoin('payment_term', 'tax.payment','=', 'payment_term.id')
		->leftJoin('unit_of_measurement', 'unit_of_measurement.id','=', 'tax.per')
		->where('tax_dispatch.id','=',NULL)->where('tax_invoice.is_cancelled','=',0);
		for($i=0;$i<count($sheet);$i++)
			if($request->input('search_in_excel'.$i)!='')
			{
				$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
			}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy($col,$by);
				$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy($col,$by);
				}
			}
		else
		{
			$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy('tax_invoice.id','desc');			
			$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy('tax_invoice.id','desc');			
		}
		$data = $db_data[$sheet[0]]->select(DB::raw('tax_invoice.id'))->get();
		$db_data[$sheet[0]] = $db_data[$sheet[0]]->select($column[$sheet[0]])->get();
		for($i=1;$i<count($sheet);$i++)
			$db_data[$sheet[$i]] = $db_data[$sheet[$i]]
			->whereIn('tax_invoice.id',$data)
			->select($column[$sheet[$i]])->get();
		return Excel::download(new DataExportSheet($db_data,$outcolumn,$sheet), 'tax invoice.xlsx');
    }
	public function taxdispatch(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['tax_dispatch.id'=>'Id', 'tax_invoice.invoice_number'=>'Invoice Number','party.partyname'=>'Client','consignee.consignee_name'=>'Consignee',
		'tax_dispatch.dispatch_mode'=>'Dispatch Mode','goods_dispatch.courier_name'=>'Courier/Company Name',
		'tax_dispatch.docket_number'=>'Docket Number','employee__profile.name'=>'Person Name',
		'tax_dispatch.dispatch_date'=>'Dispatch Date'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['tax_dispatch.id', 'tax_invoice.invoice_number','party.partyname','consignee.consignee_name','tax_dispatch.dispatch_mode',
			'goods_dispatch.courier_name','tax_dispatch.docket_number','employee__profile.name',
			'tax_dispatch.dispatch_date'];
			$outcolumn =['Id','Invoice Number','Client','Consignee','Dispatch Mode','Courier/Company Name','Docket Number','Person Name',
			'Dispatch Date'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data =  Tax_Dispatch::leftJoin('tax_invoice', function($join) {
			$join->on('tax_invoice.id', '=', 'tax_dispatch.tax_invoice_id');})
			->leftJoin('party', function($join) {
				$join->on('tax_invoice.party_id', '=', 'party.id');})
				->leftJoin('consignee', function($join) {
					$join->on('tax_invoice.consignee_id', '=', 'consignee.id');})
				->leftJoin('employee__profile', function($join) {
						$join->on('tax_dispatch.person', '=', 'employee__profile.id');})
				->leftJoin('goods_dispatch', function($join) {
					$join->on('tax_dispatch.courier_company', '=', 'goods_dispatch.id');})
							   ->where('tax_dispatch.is_active',1);

		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('tax_dispatch.id','desc');			
		}
		$db_data=$db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'client'), 'tax dispatch.xlsx');
    }
	public function materialinward(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['material_inward.id'=>'Id','material_inward.material_inward_number'=>'Material Inward Number',
		'material_inward.entry_for'=>'Entry For','material_inward.date'=>'Date','material_inward.vehicle_no'=>'Vehicle No','vehicle_type.name'=>'Vehicle Type',
		'material_inward.company'=>'Company Name','material_inward.item_name'=>'Item Name','material_inward.qty'=>'Quantity','material_inward.dimension'=>'Dimension',
		'material_inward.time'=>'Time','material_inward.doc_for'=>'Doc For','material_inward.invoice'=>'Invoice No','material_inward.challan'=>'Challan No',
		'material_inward.bilty'=>'Bilty','material_inward.other'=>'Other Doc','material_inward.driver_name'=>'Driver Name',
		'material_inward.driver_number'=>'Driver Number','material_inward.remark'=>'Remark','material_inward.created_at'=>'Timestamp'];
		
		if($request->input('columns_in_excel0')=='')
		{
			$column =['material_inward.id','material_inward.material_inward_number','material_inward.entry_for','material_inward.date',
            'material_inward.vehicle_no','vehicle_type.name','material_inward.company','material_inward.item_name','material_inward.qty',
            'material_inward.dimension','material_inward.time','material_inward.doc_for',
            'material_inward.invoice','material_inward.challan','material_inward.bilty',
            'material_inward.other','material_inward.driver_name','material_inward.driver_number','material_inward.remark','material_inward.created_at'];
			
			$outcolumn =[ 'Id','Material Inward Number','Entry For','Date','Vehicle No','Vehicle Type','Company Name','Item Name','Quantity','Dimension',
			'Time','Doc For','Invoice No','Challan No','Bilty','Other Doc','Driver Name','Driver Number',
			'Remark','Timestamp'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = Material_inwarding::leftJoin('vehicle_type','vehicle_type.id','material_inward.vehicle_type');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('material_inward.id','desc');			
		}
		$db_data=$db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'materialinward'), 'material inward.xlsx');
	}
	public function materialoutward(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['material_outward.id'=>'Id',
		'material_outward.material_outward_number'=>'Material Outward Number',
		'material_outward.date'=>'Date',
		'gatepass.gatepass_number'=>'GatePass Number',
		'material_outward.carrier'=>'Carrier',
		'material_outward.vehicle_no'=>'Vehicle No',
		'vehicle_type.name'=>'Vehicle Type',
		'material_outward.mode'=>'Mode',
		'material_outward.dispatch_to'=>'Dispatch to',
		'item_category.name'=>'Item Name',
		'material_outward.other_item_desc'=>'Other Item',
		'material_outward.qty'=>'Quantity',
		'material_outward.dimension'=>'Dimension',
		'material_outward.time'=>'Time',
		'material_outward.driver_name'=>'Driver Name',
		'material_outward.driver_number'=>'Driver Number',
		'material_outward.remark'=>'Remark',
		'material_outward.created_at'=>'Timestamp'
		];
		
		if($request->input('columns_in_excel0')=='')
		{
			$column =['material_outward.id',
			'material_outward.material_outward_number',
			'material_outward.date',
			'gatepass.gatepass_number',
			'material_outward.carrier',
			'material_outward.vehicle_no',
			'vehicle_type.name as vehicle',
			'material_outward.mode',
			'material_outward.dispatch_to',
			'item_category.name',
			'material_outward.other_item_desc',
			'material_outward.qty',
			'material_outward.dimension',
			'material_outward.time',
			'material_outward.driver_name',
			'material_outward.driver_number',
			'material_outward.remark',
			'material_outward.created_at'];
			
			$outcolumn =[ 'Id',
			'Material Outward Number',
			'Date',
			'GatePass Number',
			'Carrier',
			'Vehicle No',
			'Vehicle Type',
			'Mode',
			'Dispatch to',
			'Item Name',
			'Other Item',
			'Quantity',
			'Dimension',
			'Time',
			'Driver Name',
			'Driver Number',
			'Remark',
			'Timestamp'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = Material_outwarding::leftJoin('vehicle_type','vehicle_type.id','material_outward.vehicle_type')
		->leftJoin('item_category','item_category.id','material_outward.item')
		->leftJoin('gatepass','gatepass.id','material_outward.gatepass');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('material_outward.id','desc');			
		}
		$db_data=$db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'materialoutward'), 'material outward.xlsx');
	}
	public function internaldc(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['internal_dc.id'=>'Id',
		'internal_dc.idc_number'=>'Internal DC Number',
		'internal_dc.for'=>'For',
		'internal_dc.outsource_no'=>'Outsource No',
		'internal_dc.rate'=>'Rate',
		'hsn.hsn'=>'hsn',
		'internal_dc.date'=>'Date',
		'internal_dc.item_desc'=>'Item Description',
		'internal_dc.item_qty'=>'Quantity',
		'unit_of_measurement.uom_name'=>'Quantity Unit',
		'internal_dc.packing_desc'=>'Packing Description',
		'internal_dc.dispatch_to'=>'Dispatch to',
		'internal_dc.mode'=>'Mode',
		'goods_dispatch.courier_name'=>'Carrier',
		'internal_dc.reason'=>'Reason',
		'internal_dc.created_at'=>'Timestamp'
		];
		
		if($request->input('columns_in_excel0')=='')
		{
			$column =['internal_dc.id',
			'internal_dc.idc_number',
			'internal_dc.for',
			'internal_dc.outsource_no',
			'internal_dc.rate',
			'hsn.hsn',
			'internal_dc.date',
			'internal_dc.item_desc',
			'internal_dc.item_qty',
			'unit_of_measurement.uom_name',
			'internal_dc.packing_desc',
			'internal_dc.dispatch_to',
			'internal_dc.mode',
			'goods_dispatch.courier_name',
			'internal_dc.reason',
			'internal_dc.created_at'];
			
			$outcolumn =[ 'Id',
			'Internal DC Number',
			'For',
			'Outsource No',
			'rate',
			'hsn',
			'Date',
			'Item Description',
			'Quantity',
			'Quantity Unit',
			'Packing Description',
			'Dispatch to',
			'Mode',
			'Carrier',
			'Reason',
			'Timestamp'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = Internal_DC::leftJoin('hsn','hsn.id','internal_dc.hsn')
		->leftJoin('unit_of_measurement','unit_of_measurement.id','internal_dc.qty_unit')
		->leftJoin('goods_dispatch','goods_dispatch.id','internal_dc.carrier_name_id');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('internal_dc.id','desc');			
		}
		$db_data=$db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'internaldc'), 'internal dc.xlsx');
	}
	public function employeegatepass(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['gatepass.id'=>'Id','gatepass.gatepass_number'=>'Gatepass Number','gatepass.gatepass_for'=>'Gatepass For',
		'employee__profile.name'=>'Employee Name','gatepass.reason'=>'Reason','gatepass.desc'=>'Description',
		'gatepass.est_duration'=>'Estimated Duration','gatepass.created_at'=>'Timestamp'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['gatepass.id','gatepass.gatepass_number','gatepass.gatepass_for',
			'employee__profile.name','gatepass.reason','gatepass.desc',
			'gatepass.est_duration','gatepass.created_at'];
			$outcolumn =['Id','Gatepass Number','Gatepass For','Employee Name','Reason','Description',
			'Estimated Duration','Timestamp'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = GatePasses::leftJoin('employee__profile','employee__profile.id','gatepass.employee_id')
		->where('gatepass.gatepass_for','Employee');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('gatepass.id','desc');			
		}
		$db_data=$db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'employeegatepass'), 'employee gatepass.xlsx');
	}
	public function returnablegatepass(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['gatepass.id'=>'Id','gatepass.gatepass_number'=>'Gatepass Number','gatepass.gatepass_for'=>'Gatepass For',
		'gatepass.challan_type'=>'Challan Type','delivery_challan.challan_number'=>'Challan Number','internal_dc.idc_number'=>'Challan Number',
		'gatepass.remark'=>'Remark','gatepass.return_date'=>'Return Date','gatepass.created_at'=>'Timestamp'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['gatepass.id','gatepass.gatepass_number','gatepass.gatepass_for',
			'gatepass.challan_type','delivery_challan.challan_number','internal_dc.idc_number',
			'gatepass.remark','gatepass.return_date','gatepass.created_at'];

			$outcolumn =['Id','Gatepass Number','Gatepass For','Challan Type','Challan Number','Internal Challan Number','Remark','Return Date','Timestamp'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = GatePasses::leftJoin('internal_dc', function($join) {
			$join->on('internal_dc.id', '=', 'gatepass.challan_id');
			$join->where('gatepass.challan_type', '=', 'internal_dc');
		})
		->leftJoin('delivery_challan', function($join) {
			$join->on('delivery_challan.id', '=', 'gatepass.challan_id');
			$join->where('gatepass.challan_type', '=', 'delivery_challan');
		})
		->where('gatepass.gatepass_for','Returnable');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('gatepass.id','desc');			
		}
		$db_data=$db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'returnablegatepass'), 'returnable gatepass.xlsx');
	}
	public function materialgatepass(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['gatepass.id'=>'Id','gatepass.gatepass_number'=>'Gatepass Number','gatepass.gatepass_for'=>'Gatepass For',
		'gatepass.challan_type'=>'Challan Type','delivery_challan.challan_number'=>'Challan Number','internal_dc.idc_number'=>'Challan Number',
		'dispatch_mode.name'=>'Dispatch Mode','goods_dispatch.courier_name'=>'Courier Name','gatepass.created_at'=>'Timestamp'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['gatepass.id','gatepass.gatepass_number','gatepass.gatepass_for',
			'gatepass.challan_type','delivery_challan.challan_number','internal_dc.idc_number',
			'dispatch_mode.name','goods_dispatch.courier_name','gatepass.created_at'];

			$outcolumn =['Id','Gatepass Number','Gatepass For','Challan Type','Challan Number','Internal Challan Number',
			'Dispatch Mode','Courier Name','Timestamp'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = GatePasses::leftJoin('internal_dc', function($join) {
			$join->on('internal_dc.id', '=', 'gatepass.challan_id');
			$join->where('gatepass.challan_type', '=', 'PPML/IDC/');
		})
		->leftJoin('delivery_challan', function($join) {
			$join->on('delivery_challan.id', '=', 'gatepass.challan_id');
			$join->where('gatepass.challan_type', '=', 'PPML/DCN/');
		})
		->leftJoin('dispatch_mode','dispatch_mode.value','gatepass.mode_id')
		->leftJoin('goods_dispatch','goods_dispatch.id','gatepass.carrier_id')
		->where('gatepass.gatepass_for','Material');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('gatepass.id','desc');			
		}
		$db_data=$db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'materialgatepass'), 'material gatepass.xlsx');
	}

	public function party(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 = ['party.id'=>'Id','party.partyname'=>'Client Name','party.contact_person'=>'Contact Person',
		'party.contact'=>'Contact','party.alt_contact'=>'Alternate Contact','party.email'=>'Email',
		'party_reference.referencename'=>'Reference Name','payment_term.value'=>'Payment Term','party.gst'=>'GST',
		'party.pan'=>'PAN','party.address'=>'Address','cities.city'=>'City','states.name as states'=>'State',
		'countries.name'=>'Country','party.pincode'=>'Pincode'];
		if($request->input('columns_in_excel0')=='')
		{
		
			$column =['party.id','party.partyname','party.contact_person','party.contact','party.alt_contact',
			'party.email','party_reference.referencename','payment_term.value','party.gst','party.pan','party.address',
			'cities.city','states.name as states','countries.name','party.pincode'];
			$outcolumn =['Id','Party Name','Contact Person','Contact','Alternate Contact','Email',
			'Reference Name','Payment Term','GST','PAN','Address','City','State','Country','Pincode'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = Party::leftJoin('states','states.id','party.state_id')
				->leftJoin('party_reference','party_reference.id','party.reference_name')
				->leftJoin('payment_term','payment_term.id','party.payment_term_id')
				->leftJoin('cities','cities.id','party.city_id')
				->leftJoin('countries','countries.id','party.country_id');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
	
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('party.id','desc');			
		}
		$db_data=$db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'client'), 'client.xlsx');
    }
	public function consignee(Request $request,$column=[])
    {
		$outcolumn = [];
		$outcolumn1 =['consignee.id'=>'Id','consignee.consignee_name'=>'Consignee Name','party.partyname'=>'Client Name','consignee.gst'=>'GST'
		,'consignee.pan'=>'PAN','consignee.address'=>'Address','cities.city'=>'City','states.name as states'=>'State',
		'countries.name'=>'Country','consignee.pincode'=>'Pincode'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['consignee.id','consignee.consignee_name','party.partyname','consignee.gst','consignee.pan','consignee.address','cities.city','states.name as states',
			'countries.name','consignee.pincode'];
			$outcolumn =['Id','Consignee Name','Client Name','GST','PAN','Address','City','State','Country','Pincode'];
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = Consignee::leftJoin('states','states.id','consignee.state')
			->leftJoin('cities','cities.id','consignee.city')
			->leftJoin('party','party.id','consignee.party_id')
			->leftJoin('countries','countries.id','consignee.country');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
	
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('consignee.id','desc');			
		}
		$db_data=$db_data->select($column)->get();

		return Excel::download(new DataExport($db_data,$outcolumn,'consignee'), 'consignee.xlsx');
    }
    public function Employee(Request $request,$column=[]){
    	$outcolumn = [];
		$outcolumn1 =['employee__profile.employee_number'=>'Employee No.','employee__profile.name'=>'Name','employee__profile.father_name'=>'Father Name',
		'employee__profile.dob'=>'Date Of Birth',
		'employee__profile.local_address'=>'Local Address',
		'employee__profile.permanent_address'=>'Permanent Address',
		'employee__profile.home_landline'=>'Home Landline','employee__profile.mobile'=>'Mobile','employee__profile.family_number'=>'Family Number',
		'employee__profile.relation_with_emp'=>'Relation with Employee',
		'employee__profile.doj'=>'Joining Date',
		'employee__profile.designation'=>'Designation',
		'employee__profile.employee_skill'=>'Skill','employee__profile.shifting_timing'=>'Shift Timing',
		'employee__profile.email'=>'Email','employee__profile.aadhar'=>'Aadhar No.',
		'department.department'=>'Department','users.name as users'=>'Reporting Head'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =['employee__profile.employee_number','employee__profile.name as employee','employee__profile.father_name','employee__profile.dob','employee__profile.local_address','employee__profile.permanent_address','employee__profile.home_landline','employee__profile.mobile','employee__profile.family_number','employee__profile.relation_with_emp',
			'employee__profile.doj','employee__profile.designation','employee__profile.employee_skill','employee__profile.shifting_timing','employee__profile.email','employee__profile.aadhar','department.department','users.name'];
			$outcolumn =['Employee No.','Name','Father Name','Date Of Birth','Local Address','Permanent Address','Home Landline','Mobile','Family Number','Relation with Employee','Joining Date','Designation','Skill','Shift Timing','Email','Aadhar No.','Department','Reporting Head'];
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			// print_r($column);die;
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = EmployeeProfile::leftJoin('users','users.id','employee__profile.reporting')
			->leftJoin('department','department.id','employee__profile.department_id');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
	
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('employee__profile.id','desc');			
		}
		$db_data=$db_data->select($column)->get();
		// print($db_data);die;

		return Excel::download(new DataExport($db_data,$outcolumn,'employee'), 'employee.xlsx');
    }
   	//order to collection export

   	public function ksamplingandfocorder(Request $request,$column=[]){
   		$outcolumn = [];
   		$outcolumn1 = [
   			'internal_order.id'=>'ID',
   			'internal_order.io_number'=>'IO Number',
			'ob_details.job_date'=>'IO Date',
			'io_type.name as io_type'=>'IO Type',
			'party_reference.referencename'=>'Reference_name',
			'item_category.name as item_name'=>'Item Name',
			'job_details.qty as io_qty'=>'IO Quantity',
			'job_details.rate_per_qty as io_rate'=>'IO Rate',
			'advance_io.amount AS advance_amt'=>'Advance Amount',
			'advance_io.mode_of_receive as advance_mode'=>'Advance Mode'
   		];
   		if($request->input('columns_in_excel0')=='')
		{
			$column =['internal_order.id',
				'internal_order.io_number','job_details.job_date','io_type.name as io_type','party_reference.referencename',DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
				'job_details.qty as io_qty','job_details.rate_per_qty as io_rate',DB::raw('(job_details.qty)*(job_details.rate_per_qty) as amount'),
				DB::raw('IFNULL(advance_io.amount, 0) AS advance_amt'),DB::raw('CASE WHEN advance_io.mode_of_receive = 0 THEN "Cash"
            WHEN advance_io.mode_of_receive = 1 THEN "Cheque" ELSE "RTGS" END as advance_mode'),
				DB::raw('(((job_details.qty)*(job_details.rate_per_qty)) - IFNULL(advance_io.amount, 0))as balance')
			];
			$outcolumn =[
				'ID','IO Number','IO Date','IO Type','Reference_name','Item Name','IO Quantity','IO Rate','Amount',
				'Advance Amount','Advance Mode','Balance'
			];
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data=InternalOrder::where('internal_order.status','Open')
        ->whereIn('job_details.io_type_id', [5, 6])
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('advance_io','advance_io.id','job_details.advance_io_id')
        ->leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id');
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		// item_category\.name
		else
		{
			$db_data = $db_data->orderBy('internal_order.id','desc');			
		}
		if($request->input('search_in_excel0')!='')
		{
			$x=explode(' ',$request->input('search_in_excel0'))[0];
			if($x=="internal_order.created_time" || $x=="internal_order.closed_date"){
				$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],'LIKE',"%".$request->input('search_val_in_excel0')."%"); 
			}
			else{
				$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
			}
			
		}
	
		$db_data=$db_data->select($column)->get();		
			return Excel::download(new DataExport($db_data,$outcolumn,'ksamplingandfocorder'), 'K Sampling and FOC.xlsx');
   	}
   	public function noworkdoneiofinancial(Request $request,$column=[]){
   		$outcolumn = [];
		$outcolumn1 =['internal_order.id'=>'Id',
					'internal_order.io_number'=>'IO Number',
					'io_type.name'=>'IO Type',
					'party_reference.referencename'=>'Reference_name',
					'job_details.job_date'=>'IO Date',
					'item_category.name as item_name'=>'Item Name',
					'job_details.qty as io_qty'=>'IO Quantity',
					'job_details.rate_per_qty as io_rate'=>'IO Rate','amount'=>'Amount'
					];
   		if($request->input('columns_in_excel0')=='')
		{
			$column =['internal_order.id',
				'internal_order.io_number','io_type.name','party_reference.referencename','job_details.job_date',DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
				'job_details.qty as io_qty','job_details.rate_per_qty as io_rate',DB::raw('(job_details.qty)*(job_details.rate_per_qty) as amount')
			];
			$outcolumn =[
				'Id','IO Number','IO Type','Reference_name','IO Date','Item Name','IO Quantity','IO Rate','Amount'
			];
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data=InternalOrder::leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
        ->leftjoin('item_category','internal_order.item_category_id','item_category.id')
		->leftJoin('tax','tax.io_id','internal_order.id')
		->leftJoin('challan_per_io','challan_per_io.io','internal_order.id')
        ->where('internal_order.status','Open')
        ->whereNull('tax.id')
        ->whereNull('challan_per_io.id')
        ->where('internal_order.status','Open');
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		// item_category\.name
		else
		{
			$db_data = $db_data->orderBy('internal_order.id','desc');			
		}
		if($request->input('search_in_excel0')!='')
		{
			$x=explode(' ',$request->input('search_in_excel0'))[0];
			if($x=="internal_order.created_time" || $x=="internal_order.closed_date"){
				$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],'LIKE',"%".$request->input('search_val_in_excel0')."%"); 
			}
			else{
				$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
			}
			
		}
	
		$db_data=$db_data->select($column)->get();		
			return Excel::download(new DataExport($db_data,$outcolumn,'noworkdoneiofinancial'), 'No Work Done IO Financial.xlsx');
   	}
   	public function noworkdoneio(Request $request,$column=[]){
   		$outcolumn = [];
		$outcolumn1 =['internal_order.id'=>'Id',
					'internal_order.io_number'=>'IO Number',
					'io_type.name'=>'IO Type',
					'party_reference.referencename'=>'Reference_name',
					'job_details.job_date'=>'IO Date',
					'item_category.name as item_name'=>'Item Name',
					'job_details.qty as io_qty'=>'IO Quantity'
					];
   		if($request->input('columns_in_excel0')=='')
		{
			$column =['internal_order.id',
				'internal_order.io_number','io_type.name','party_reference.referencename','job_details.job_date',DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),
				'job_details.qty as io_qty'
			];
			$outcolumn =[
				'Id','IO Number','IO Type','Reference_name','IO Date','Item Name','IO Quantity'
			];
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data=InternalOrder::leftJoin('party_reference','internal_order.reference_name','party_reference.id')
        ->leftJoin('job_details','internal_order.job_details_id','job_details.id')
        ->leftJoin('io_type','job_details.io_type_id','io_type.id')
		->leftjoin('item_category','internal_order.item_category_id','item_category.id')
		->leftJoin('tax','tax.io_id','internal_order.id')
		->leftJoin('challan_per_io','challan_per_io.io','internal_order.id')
        ->where('internal_order.status','Open')
        ->whereNull('tax.id')
        ->whereNull('challan_per_io.id')
        ;
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		// item_category\.name
		else
		{
			$db_data = $db_data->orderBy('internal_order.id','desc');			
		}
		if($request->input('search_in_excel0')!='')
		{
			$x=explode(' ',$request->input('search_in_excel0'))[0];
			if($x=="internal_order.created_time" || $x=="internal_order.closed_date"){
				$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],'LIKE',"%".$request->input('search_val_in_excel0')."%"); 
			}
			else{
				$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
			}
			
		}
	
		$db_data=$db_data->select($column)->get();		
			return Excel::download(new DataExport($db_data,$outcolumn,'noworkdoneio'), 'No Work Done IO.xlsx');
   	}
   	public function internalOrder(Request $request,$column=[])
    {
		// print_r($request->input());die;
		$outcolumn = [];
		$outcolumn1 =[
			'internal_order.io_number'=>'Internal Order Number','internal_order.status'=>'Status',
			'internal_order.created_time'=>'Created Time','party_reference.referencename'=>'Reference Name','item_category.name as e'=>'Item Name',
			'internal_order.other_item_name as aaa'=>'Other Item Name','hsn.hsn as f'=>'HSN Name','hsn.gst_rate as g'=>'GST Rate(%)','io_type.name as h'=>'IO Type',
			'unit_of_measurement.uom_name'=>'Unit Of Measurement','job_details.job_date'=>'Job Date',
			'job_details.delivery_date'=>'Delivery Date','job_details.qty'=>'Quantity','job_details.job_size'=>'Job Size',
			'job_details.dimension'=>'Dimension','job_details.rate_per_qty'=>'Rate Per Quantity','users.name as i'=>'Marketing Person',
			'job_details.details'=>'Job Details','job_details.front_color'=>'Front Colour','job_details.back_color'=>'Back Colour',
			'job_details.is_supplied_paper'=>'Paper Supplied','job_details.is_supplied_plate'=>'Plate Supplied',
			'job_details.remarks'=>'Remarks','job_details.transportation_charge'=>'Transportation charges',
			'job_details.other_charge'=>'Other Charges','bool_values.value as advanced_received'=>'Advance Received',
			'advance_io.amount'=>'Amount','mode_of_payment.value as j'=>'mode_of_payment','advance_io.date as m'=>'Received Date',
			'created_user.name as k'=>'Created By','closed_user.name as l'=>'Closed By','internal_order.closed_date'=>'Closed Date'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =[
				'internal_order.io_number','internal_order.status','internal_order.created_time','party_reference.referencename','item_category.name as item_category',
				'internal_order.other_item_name as aaa','hsn.hsn as hsn_name','hsn.gst_rate as gst','io_type.name as io_type',
				'unit_of_measurement.uom_name','job_details.job_date','job_details.delivery_date','job_details.qty',
				'job_details.job_size','job_details.dimension','job_details.rate_per_qty','users.name as marketing_name',
				'job_details.details','job_details.front_color','job_details.back_color','job_details.is_supplied_paper',
				'job_details.is_supplied_plate','job_details.remarks','job_details.transportation_charge',
				'job_details.other_charge','bool_values.value as advanced_received','advance_io.amount',
				'mode_of_payment.value as mode_of_payment','advance_io.date as amount_received_date',
				'created_user.name as created_by','closed_user.name as closed_by','internal_order.closed_date'         
			];
			$outcolumn =[
				'Internal Order Number','Status','Created Time','Reference Name','Item Name','Other Item Name',
				'HSN Name','GST Rate(%)','IO Type','Unit Of Measurement','Job Date','Delivery Date','Quantity','Job Size',
				'Dimension','Rate Per Quantity','Marketing Person','Job Details','Front Colour','Back Colour','Paper Supplied',
				'Plate Supplied','Remarks','Transportation charges','Other Charges','Advance Received','Amount',
				'mode_of_payment','Received Date','Created By','Closed By ','Closed Date'
			];
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data=InternalOrder::leftJoin('job_details','internal_order.job_details_id','=','job_details.id')
        ->leftJoin('users','users.id','=','job_details.marketing_user_id')
        ->leftJoin('users as created_user','created_user.id','=','internal_order.created_by')
        ->leftJoin('users as closed_user','closed_user.id','=','internal_order.closed_by')
		->leftJoin('advance_io','job_details.advance_io_id','=','advance_io.id')
		->leftJoin('bool_values','job_details.advanced_received','=','bool_values.id')
		->leftJoin('mode_of_payment','mode_of_payment.id','=','advance_io.mode_of_receive')
		->leftJoin('party_reference','party_reference.id','internal_order.reference_name')
        ->leftJoin('item_category','internal_order.item_category_id','=','item_category.id')
        ->leftJoin('io_type','job_details.io_type_id','=','io_type.id')
        ->leftJoin('unit_of_measurement','job_details.unit','=','unit_of_measurement.id')
		->leftJoin('hsn','hsn.id','=','job_details.hsn_code');
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		// item_category\.name
		else
		{
			$db_data = $db_data->orderBy('internal_order.id','desc');			
		}
		if($request->input('search_in_excel0')!='')
		{
			$x=explode(' ',$request->input('search_in_excel0'))[0];
			if($x=="internal_order.created_time" || $x=="internal_order.closed_date"){
				$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],'LIKE',"%".$request->input('search_val_in_excel0')."%"); 
			}
			else{
				$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
			}
			
		}
	
		$db_data=$db_data->select($column)->get();		
			return Excel::download(new DataExport($db_data,$outcolumn,'internal_order'), 'Internal Order.xlsx');
	}
	public function deliveryChallan(Request $request,$column=[])
	{
		$sheet=['delivery_challan','challan_per_io'];
		$outcolumn1 = array(
			'delivery_challan'=>array(
				'delivery_challan.challan_number'=>'Delivery Challan Number','party.partyname'=>'Client Name','consignee.consignee_name'=>'Consignee Name',
				'delivery_challan.total_amount'=>'Total Amount','dispatch_mode.name'=>'Dispatch By',
				'goods_dispatch.courier_name'=>'carrier/Company Name',
				'delivery_challan.bilty_docket'=>'Bilty Docket','delivery_challan.docket_date'=>'Docket Date',
				'vehicle.vehicle_number'=>'Vehicle Number','delivery_challan.delivery_date'=>'Delivery Date','delivery_challan.created_time'=>'Created Time'),
							
			'challan_per_io'=>array(
				'delivery_challan.challan_number'=>'Delivery Challan Number','internal_order.io_number'=>'Internal Order Number',
				'item_category.name'=>'Item Name',
				'challan_per_io.good_qty'=>'Quantity','unit_of_measurement.uom_name'=>'Per','hsn.hsn'=>'HSN/SAC',
				'challan_per_io.good_desc'=>'Goode Description','challan_per_io.packing_details'=>'Packing Details',
				'challan_per_io.rate'=>'Rate Per Peice','hsn.gst_rate'=>'GST Rate(%)','challan_per_io.amount'=>'Amount')
			);
		$col=array(
			'delivery_challan'=>array('delivery_challan.challan_number','party.partyname','consignee.consignee_name',
				'delivery_challan.total_amount','dispatch_mode.name',DB::raw('group_concat(goods_dispatch.courier_name) as io'),
				'delivery_challan.bilty_docket','delivery_challan.docket_date','vehicle.vehicle_number',
				'delivery_challan.delivery_date','delivery_challan.created_time'),
			'challan_per_io'=> array('delivery_challan.challan_number','internal_order.io_number',
			DB::raw('(concat(item_category.name,if(`item_category`.name = "Other"," : ",""),internal_order.other_item_name)) as item_name'),'challan_per_io.good_qty',
				'unit_of_measurement.uom_name','hsn.hsn','challan_per_io.good_desc','challan_per_io.packing_details',
				'challan_per_io.rate','hsn.gst_rate','challan_per_io.amount'
			)
		);
		$outcol = array(
			'delivery_challan'=>array('Delivery Challan Number','Client Name','Consignee Name','Total Amount',
				'Dispatch By','carrier/Company Name','Bilty Docket','Docket Date','Vehicle Number','Delivery Challan Date','Created Time'),
			'challan_per_io'=>array('Delivery Challan Number','Internal Order Number','Item Name','Quantity','Per','HSN/SAC','Goode Description','Packing Details','Rate Per Peice','GST Rate(%)','Amount'));
		for($i=0;$i<count($sheet);$i++)
		{
			$column[$sheet[$i]]=[];
			$outcolumn[$sheet[$i]]=[];
			
			if($request->input('columns_in_excel'.$i)=='')
			{
				$column[$sheet[$i]] = $col[$sheet[$i]];
				$outcolumn[$sheet[$i]] =$outcol[$sheet[$i]];
			}
			else
			{
				$column[$sheet[$i]] = $request->input('columns_in_excel'.$i);
				foreach($column[$sheet[$i]] as $k)
					$outcolumn[$sheet[$i]] =array_merge($outcolumn[$sheet[$i]],array($outcolumn1[$sheet[$i]][$k]));
				if($i==0)
				{
					if (in_array("goods_dispatch.courier_name", $request->input('columns_in_excel0')))
					{
						$p = array_search('goods_dispatch.courier_name',$column[$sheet[0]]);
						$column[$sheet[0]]=array_replace($column[$sheet[0]],array($p=>DB::raw('group_concat(goods_dispatch.courier_name) as io')));
					}
				}
			}
		}
		$db_data['delivery_challan'] = Delivery_challan::where('delivery_challan.is_active','=','1')
        ->leftJoin('party','party.id','delivery_challan.party_id')
        ->leftJoin('consignee','consignee.id','delivery_challan.consignee_id')
        ->leftJoin('dispatch_mode','dispatch_mode.id','delivery_challan.dispatch')
        ->leftJoin('vehicle','vehicle.id','delivery_challan.vehicle_id')
        ->leftJoin('goods_dispatch',function($join){
            $join->on(DB::raw("find_in_set(goods_dispatch.id,delivery_challan.dispatch_id)"),'goods_dispatch.id','','');
        })
		->groupBy('delivery_challan.id');
		
		$db_data['challan_per_io']=Challan_per_io::where('delivery_challan.is_active','=',1)
		->leftJoin('internal_order','challan_per_io.io','internal_order.id')
		->leftJoin('delivery_challan','challan_per_io.delivery_challan_id','delivery_challan.id')
		->leftJoin('unit_of_measurement','challan_per_io.uom_id','unit_of_measurement.id')
		->leftJoin('item_category','item_category.id','internal_order.item_category_id')
		->leftJoin('job_details','job_details.id','internal_order.job_details_id')
		->leftJoin('hsn','hsn.id','job_details.hsn_code');
		for($i=0;$i<count($sheet);$i++)
		{
			if($request->input('search_in_excel'.$i)!='')
			{
			
					$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i));
				
				if($i==0)
				{
					for($j=1;$j<count($sheet);$j++)
					{

							$db_data[$sheet[$j]] = $db_data[$sheet[$j]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i));
						 
					}
				}
			}
		}
		// die;
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data['delivery_challan'] = $db_data['delivery_challan']->orderBy($col,$by);
				$db_data['challan_per_io'] = $db_data['challan_per_io']->orderBy($col,$by);
			}
		}
		else
		{
			$db_data['delivery_challan'] = $db_data['delivery_challan']->orderBy('delivery_challan.id','desc');
			$db_data['challan_per_io'] = $db_data['challan_per_io']->orderBy('delivery_challan.id','desc');
		}
		
		$data = $db_data[$sheet[0]]->select(DB::raw('delivery_challan.id'))->get();
		$db_data[$sheet[0]] = $db_data[$sheet[0]]->select($column[$sheet[0]])->get();
		for($i=1;$i<count($sheet);$i++)
			$db_data[$sheet[$i]] = $db_data[$sheet[$i]]
			->whereIn('delivery_challan.id',$data)
			->select($column[$sheet[$i]])->get();
		
		return Excel::download(new DataExportSheet($db_data,$outcolumn,$sheet), 'DeliveryChallan.xlsx');	
	}
   //master list export
	public function paymentTerm(Request $request,$column=[])
	{
		$outcolumn = [];
		$outcolumn1 = ['payment_term.id'=>'Id','payment_term.value'=>'Payment Terms'];
		if($request->input('columns_in_excel0')=='')
		{	
			$column =['payment_term.id','payment_term.value'];	
			$outcolumn =['Id','Payment Terms'];
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = Payment::select($column);
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('payment_term.id','desc');			
		}
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
	
		$db_data=$db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'payment_term'), 'Payment Term.xlsx');
	}
	public function uom(Request $request,$column=[])
	{
		
		$outcolumn = [];
		$outcolumn1 = ['unit_of_measurement.id'=>'Id','unit_of_measurement.uom_name'=>'Unit Of Measurement'];
		if($request->input('columns_in_excel0')=='')
		{	
			$column =['unit_of_measurement.id','unit_of_measurement.uom_name'];
			$outcolumn =['Id','Unit Of Measurement'];
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = Unit_of_measurement::select($column);
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('unit_of_measurement.id','desc');			
		}
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
	
		$db_data=$db_data->select($column)->get();

		return Excel::download(new DataExport($db_data,$outcolumn,'uom'), 'Unot Of Measurement.xlsx');
	}
	public function goodsDispatchProfile(Request $request,$column=[])
	{
		$outcolumn = [];
		$outcolumn1 = ['goods_dispatch.id'=>'Id','dispatch_mode.name'=>'Dispatch By',
					'goods_dispatch.courier_name'=>'Carrier/company Name','goods_dispatch.contact'=>'Contact',
					'goods_dispatch.gst'=>'GST Number','goods_dispatch.address'=>'Address'];
		if($request->input('columns_in_excel0')=='')
		{	
			$column =['goods_dispatch.id','dispatch_mode.name','goods_dispatch.courier_name','goods_dispatch.contact',
						'goods_dispatch.gst','goods_dispatch.address'];
			$outcolumn =['Id','Dispatch By','Carrier/company Name','Contact','GST Number','Address'];
			}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = Goods_Dispatch::leftJoin('dispatch_mode','dispatch_mode.id','goods_dispatch.mode');
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('goods_dispatch.id','desc');			
		}
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
	
		$db_data=$db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'goods_dispatch_profile'), 'Goods Dispatch Profile.xlsx');
	}
		
	public function hsn(Request $request,$column=[])
	{
		$outcolumn = [];
		$outcolumn1 = ['hsn.id'=>'Id','item_category.name'=>'Item Name',
						'hsn.hsn'=>'HSN Code','hsn.gst_rate'=>'Gst rate(%)'];
		if($request->input('columns_in_excel0')=='')
		{	
			$column =['hsn.id','item_category.name','hsn.hsn','hsn.gst_rate'];
			$outcolumn =['Id','Item Name','HSN Code','Gst rate(%)'];
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = hsn::leftJoin('item_category','item_category.id','hsn.item_id');
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('hsn.id','desc');			
		}
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
	
		$db_data=$db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,"HSN"), 'HSN.xlsx');
	}




	public function purchase_requisition(Request $request,$column=[])
	{
		$sheet=['pur_purchase_req','pur_purchase_io'];
		$outcolumn1 = array(
			'pur_purchase_req'=>array(
				'pur_purchase_req.purchase_req_number'=>'Purchase Requisition Number','employee__profile.name'=>'Requested By','pur_purchase_req.item_req_for'=>'Item Requirement For',
				'internal_order.io_number'=>'Internal Order','pur_purchase_req.required_date'=>'Required Date',
				'users.name'=>'Created By','pur_purchase_req.created_time'=>'Created Time'),
							
			'pur_purchase_io'=>array(
					'pur_purchase_req.purchase_req_number'=>'Purchase Requisition Number','pur_purchase_io.item_desc'=>'Item Desc',
					'pur_purchase_io.item_qty'=>'Item Qty','unit_of_measurement.uom_name'=>'UOM')
			);

		$col=array(
			'pur_purchase_req'=>array(
				'pur_purchase_req.purchase_req_number','employee__profile.name','pur_purchase_req.item_req_for',
				'internal_order.io_number','pur_purchase_req.required_date',
				'users.name as uname','pur_purchase_req.created_time'),
			'pur_purchase_io'=>array(
					'pur_purchase_req.purchase_req_number','pur_purchase_io.item_desc',
					'pur_purchase_io.item_qty','unit_of_measurement.uom_name')
			
		);
		$outcol = array(
			'pur_purchase_req'=>array(
				'Purchase Requisition Number','Requested By','Item Requirement For',
				'Internal Order','Required Date',
				'Created By','Created Time'),
			'pur_purchase_io'=>array(
					'Purchase Requisition Number','Item Desc',
					'Item Qty','UOM')
			
		);
		for($i=0;$i<count($sheet);$i++)
		{
			$column[$sheet[$i]]=[];
			$outcolumn[$sheet[$i]]=[];
			
			if($request->input('columns_in_excel'.$i)=='')
			{
				$column[$sheet[$i]] = $col[$sheet[$i]];
				$outcolumn[$sheet[$i]] =$outcol[$sheet[$i]];
			}
			else
			{
				$column[$sheet[$i]] = $request->input('columns_in_excel'.$i);
				foreach($column[$sheet[$i]] as $k)
					$outcolumn[$sheet[$i]] =array_merge($outcolumn[$sheet[$i]],array($outcolumn1[$sheet[$i]][$k]));
			}
		}
		$db_data['pur_purchase_req'] = PurchaseReq::leftJoin('employee__profile','employee__profile.id','=','pur_purchase_req.requested_by') 
		->leftJoin('internal_order','internal_order.id','=','pur_purchase_req.io')
		->leftJoin('users','users.id','=','pur_purchase_req.created_by') ;
		
		$db_data['pur_purchase_io']=PurchaseIo::leftJoin('pur_purchase_req','pur_purchase_req.id','=','pur_purchase_io.purchase_id') 
		->leftJoin('unit_of_measurement','pur_purchase_io.item_qty_unit','unit_of_measurement.id');
		for($i=0;$i<count($sheet);$i++)
		{
			if($request->input('search_in_excel'.$i)!='')
			{

				$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
				if($i==0)
				{
					for($j=1;$j<count($sheet);$j++)
					{
						$db_data[$sheet[$j]] = $db_data[$sheet[$j]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
					}
				}
			}
		}

		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data['pur_purchase_req'] = $db_data['pur_purchase_req']->orderBy($col,$by);
				$db_data['challan_per_io'] = $db_data['challan_per_io']->orderBy($col,$by);
			}
		}
		else
		{
			$db_data['pur_purchase_req'] = $db_data['pur_purchase_req']->orderBy('pur_purchase_req.id','desc');
			$db_data['pur_purchase_io'] = $db_data['pur_purchase_io']->orderBy('pur_purchase_io.id','desc');
		}
		
		$data = $db_data[$sheet[0]]->select(DB::raw('pur_purchase_req.id'))->get();
		$db_data[$sheet[0]] = $db_data[$sheet[0]]->select($column[$sheet[0]])->get();
		for($i=1;$i<count($sheet);$i++)
			$db_data[$sheet[$i]] = $db_data[$sheet[$i]]
			->whereIn('pur_purchase_req.id',$data)
			->select($column[$sheet[$i]])->get();
		
		return Excel::download(new DataExportSheet($db_data,$outcolumn,$sheet), 'purchase_req.xlsx');	
	}

	public function purchase_indent(Request $request,$column=[])
	{
		$sheet=['pur_indent','pur_indent_pr'];
		
		
		$outcolumn1 = array(
			'pur_indent'=>array(
				'pur_indent.indent_num'=>'Purchase Indent Number','master_item_category.name'=>'Item Requirement For','item_sub_category.name'=>'Sub Category','stock.item_name'=>'Item Name','pur_indent.item_qty'=>'Item Qty','unit_of_measurement.uom_name'=>'Qty Unit',
				'pur_indent.item_req_date'=>'Item Required Date','pur_indent.for'=>'For',
				'users.name'=>'Created By','pur_indent.created_at'=>'Created Time'),
							
			'pur_indent_pr'=>array(
				'pur_indent.indent_num'=>'Purchase Indent Number','pur_purchase_req.purchase_req_number'=>'Purchase Requitision Number',
				'pur_indent_pr.qty'=>'Qty')
			);

		$col=array(
			'pur_indent'=>array(
				'pur_indent.indent_num','master_item_category.name as master','item_sub_category.name as sub_master','stock.item_name','pur_indent.item_qty','unit_of_measurement.uom_name',
				'pur_indent.item_req_date','pur_indent.for',
				'users.name','pur_indent.created_at'),
							
			'pur_indent_pr'=>array(
				'pur_indent.indent_num','pur_purchase_req.purchase_req_number',
				'pur_indent_pr.qty')
			
			
		);
		$outcol = array(
			'pur_indent'=>array(
				'Purchase Indent Number','Item Requirement For','Sub Category','Item Name','Item Qty','Qty Unit',
				'Item Required Date','For',
				'Created By','Created Time'),
							
			'pur_indent_pr'=>array(
				'Purchase Indent Number','Purchase Requitision Number',
				'Qty')
			
		);
		for($i=0;$i<count($sheet);$i++)
		{
			$column[$sheet[$i]]=[];
			$outcolumn[$sheet[$i]]=[];
			
			if($request->input('columns_in_excel'.$i)=='')
			{
				$column[$sheet[$i]] = $col[$sheet[$i]];
				$outcolumn[$sheet[$i]] =$outcol[$sheet[$i]];
			}
			else
			{
				$column[$sheet[$i]] = $request->input('columns_in_excel'.$i);
				foreach($column[$sheet[$i]] as $k)
					$outcolumn[$sheet[$i]] =array_merge($outcolumn[$sheet[$i]],array($outcolumn1[$sheet[$i]][$k]));
			}
		}
		$db_data['pur_indent'] = Indent::leftJoin('unit_of_measurement','pur_indent.qty_unit','=','unit_of_measurement.id') 
		->leftJoin('master_item_category','master_item_category.id','pur_indent.master_cat_id')
		->leftJoin('item_sub_category','item_sub_category.id','pur_indent.sub_cat_id')
		->leftJoin('stock','stock.id','pur_indent.stock_id')
		->leftJoin('users','users.id','=','pur_indent.created_by') ;
		
		$db_data['pur_indent_pr']=IndentPR::leftJoin('pur_indent','pur_indent_pr.indent_id','pur_indent.id')
		->leftJoin('pur_purchase_req','pur_purchase_req.id','=','pur_indent_pr.pr_id');
		for($i=0;$i<count($sheet);$i++)
		{
			if($request->input('search_in_excel'.$i)!='')
			{

				$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
				if($i==0)
				{
					for($j=1;$j<count($sheet);$j++)
					{
						$db_data[$sheet[$j]] = $db_data[$sheet[$j]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
					}
				}
			}
		}

		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data['pur_indent'] = $db_data['pur_indent']->orderBy($col,$by);
				$db_data['pur_indent_pr'] = $db_data['pur_indent_pr']->orderBy($col,$by);
			}
		}
		else
		{
			$db_data['pur_indent'] = $db_data['pur_indent']->orderBy('pur_indent.id','desc');
			$db_data['pur_indent_pr'] = $db_data['pur_indent_pr']->orderBy('pur_indent_pr.id','desc');
		}
		
		$data = $db_data[$sheet[0]]->select(DB::raw('pur_indent.id'))->get();
		$db_data[$sheet[0]] = $db_data[$sheet[0]]->select($column[$sheet[0]])->get();
		for($i=1;$i<count($sheet);$i++)
			$db_data[$sheet[$i]] = $db_data[$sheet[$i]]
			->where('pur_indent.id',$data)
			->select($column[$sheet[$i]])->get();
		
		return Excel::download(new DataExportSheet($db_data,$outcolumn,$sheet), 'purchase_indent.xlsx');	
	}

	public function purchase_order(Request $request,$column=[])
	{
		$sheet=['pr_purchase_order','pr_purchase_order_details'];
		
		
		
		$outcolumn1 = array(
			'pr_purchase_order'=>array(
				'pr_purchase_order.po_num'=>'Purchase Order','pr_purchase_order.po_date'=>'PO Date','pur_indent.indent_num'=>'Purchase Indent Number','vendor.name'=>'Vendor','payment_term.value'=>'Payment Term','master_item_category.name'=>'Master Item','pr_purchase_order.remark'=>'Remark','pr_purchase_order.status'=>'Status',
				),
							
			'pr_purchase_order_details'=>array(
				'item_sub_category.name'=>'Sub Category','stock.item_name'=>'Item Name',
				'pr_purchase_order_details.item_qty'=>'Qty','unit_of_measurement.uom_name'=>'Qty Unit','tax_per_applicable.value'=>'Tax Percent','pr_purchase_order_details.delivery_date'=>'Delivery Date',
				'pr_purchase_order_details.item_rate'=>'Rate','job_card.job_number'=>'Job Card')
			);

		$col=array(
			'pr_purchase_order'=>array(
				'pr_purchase_order.po_num','pr_purchase_order.po_date','pur_indent.indent_num','vendor.name','payment_term.value','master_item_category.name','pr_purchase_order.remark','pr_purchase_order.status',
				),
							
			'pr_purchase_order_details'=>array(
				'item_sub_category.name','stock.item_name',
				'pr_purchase_order_details.item_qty','unit_of_measurement.uom_name','tax_per_applicable.value','pr_purchase_order_details.delivery_date',
				'pr_purchase_order_details.item_rate','job_card.job_number')
			
			
		);
		$outcol = array(
			'pr_purchase_order'=>array(
				'Purchase Order','PO Date','Purchase Indent Number','Vendor','Payment Term','Master Item','Remark','Status',
				),
							
			'pr_purchase_order_details'=>array(
				'Sub Category','Item Name',
				'Qty','Qty Unit','Tax Percent','Delivery Date',
				'Rate','Job Card')
			
		);
		for($i=0;$i<count($sheet);$i++)
		{
			$column[$sheet[$i]]=[];
			$outcolumn[$sheet[$i]]=[];
			
			if($request->input('columns_in_excel'.$i)=='')
			{
				$column[$sheet[$i]] = $col[$sheet[$i]];
				$outcolumn[$sheet[$i]] =$outcol[$sheet[$i]];
			}
			else
			{
				$column[$sheet[$i]] = $request->input('columns_in_excel'.$i);
				foreach($column[$sheet[$i]] as $k)
					$outcolumn[$sheet[$i]] =array_merge($outcolumn[$sheet[$i]],array($outcolumn1[$sheet[$i]][$k]));
			}
		}
			$db_data['pr_purchase_order'] = PurchaseOrder::leftJoin('vendor','pr_purchase_order.vendor_id','vendor.id')
		->leftJoin('payment_term','pr_purchase_order.payment_term_id','=','payment_term.id') 
		->leftJoin('master_item_category','pr_purchase_order.master_cat_id','=','master_item_category.id') 
		->leftJoin('pur_indent','pr_purchase_order.indent_num_id','=','pur_indent.id')        
	      ;
		
		$db_data['pr_purchase_order_details']=PurchaseOrderDetail::leftJoin('pr_purchase_order','pr_purchase_order_details.pr_po_id','pr_purchase_order.id')
		->leftJoin('item_sub_category','pr_purchase_order_details.sub_cat_id','item_sub_category.id')
		->leftJoin('stock','pr_purchase_order_details.item_name_id','stock.id')
		->leftJoin('unit_of_measurement','pr_purchase_order_details.uom_id','unit_of_measurement.id')
		->leftJoin('tax_per_applicable','pr_purchase_order_details.tax_percent_id','tax_per_applicable.id')
		->leftJoin('job_card','pr_purchase_order_details.job_card_id','job_card.id');

		// print_r($db_data['pr_purchase_order']->get());die;
		for($i=0;$i<count($sheet);$i++)
		{
			if($request->input('search_in_excel'.$i)!='')
			{

				$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
				if($i==0)
				{
					for($j=1;$j<count($sheet);$j++)
					{
						$db_data[$sheet[$j]] = $db_data[$sheet[$j]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
					}
				}
			}
		}

		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data['pr_purchase_order'] = $db_data['pr_purchase_order']->orderBy($col,$by);
				$db_data['pr_purchase_order_details'] = $db_data['pr_purchase_order_details']->orderBy($col,$by);
			}
		}
		else
		{
			$db_data['pr_purchase_order'] = $db_data['pr_purchase_order']->orderBy('pr_purchase_order.id','desc');
			$db_data['pr_purchase_order_details'] = $db_data['pr_purchase_order_details']->orderBy('pr_purchase_order_details.id','desc');
		}
		
		$data = $db_data[$sheet[0]]->select(DB::raw('pr_purchase_order.id'))->get();
		$db_data[$sheet[0]] = $db_data[$sheet[0]]->select($column[$sheet[0]])->get();
		// $db_data[$sheet[1]] = $db_data[$sheet[1]]->select($column[$sheet[1]])->get();

		print_r($data);die;
		for($i=1;$i<count($sheet);$i++)
			$db_data[$sheet[$i]] = $db_data[$sheet[$i]]
			// ->where('pr_purchase_order_details.pr_po_id',$data)
			->select($column[$sheet[$i]])->get();
		
		return Excel::download(new DataExportSheet($db_data,$outcolumn,$sheet), 'purchase_indent.xlsx');	
	}

	public function purchase_return(Request $request,$column=[]) {
		$outcolumn = [];
		$outcolumn1 = ['pur_return_request.return_number'=>'Purchase Return Number','pur_return_request.date'=>'Date','users.name'=>'Approved By','pr_purchase_order.po_num'=>'purchase Order','pur_grn.grn_number'=>'GRN Number',
		'pur_return_request.supp_name'=>'Supplier Name','pur_return_request.reason'=>'Reason','pur_return_request.item_desc'=>'Item Desc','pur_return_request.item_qty_received'=>'Qty Received',
		'pur_return_request.item_qty_returned'=>'Qty Returned','unit_of_measurement.uom_name'=>'Qty Unit','pur_return_request.payment_desc'=>'Payment Desc','pur_return_request.created_at'=>'Created Time'];
		if($request->input('columns_in_excel0')=='')
		{
			$column =[	'pur_return_request.return_number','pur_return_request.date','users.name','pr_purchase_order.po_num','pur_grn.grn_number',
			'pur_return_request.supp_name','pur_return_request.reason','pur_return_request.item_desc','pur_return_request.item_qty_received',
			'pur_return_request.item_qty_returned','unit_of_measurement.uom_name','pur_return_request.payment_desc','pur_return_request.created_at'];
			
			$outcolumn =['Purchase Return Number','Date','Approved By','purchase Order','GRN Number',
			'Supplier Name','Reason','Item Desc','Qty Received',
			'Qty Returned','Qty Unit','Payment Desc','Created Time'];		
		}
		else
		{
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn =array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data =ReturnRequest::leftJoin('users','users.id','pur_return_request.approved_by')
		->leftJoin('pr_purchase_order','pr_purchase_order.id','pur_return_request.po_num_id')
		->leftJoin('pur_grn','pur_grn.id','pur_return_request.grn_num_id')
		->leftJoin('unit_of_measurement','unit_of_measurement.id','pur_return_request.item_unit');
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		}
		else
		{
			$db_data = $db_data->orderBy('pur_return_request.id','desc');			
		}
		$db_data = $db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'pur_return_request'), 'pur_return_request.xlsx');
	}

	public function pfregister(Request $request,$column=[]) {
		$outcolumn = [];
		$month = $_GET["from"];
		$year = $_GET["to"];
		if ($month != null) {
			$month = $_GET["from"];
		}else{
			$month = date('m');
		}
		if ($year != null) {
			$year = $_GET["to"];
		}else{
			$year = date('Y');
		}
		$dd=date('t',strtotime($month));
		$outcolumn1 = [	
			'employee__profile.employee_number' => 'Employee Number',
			  'employee__profile.name' => 'Employee Name',
	          'employee__pfesi.pf_no' => 'PF Number'
		];
		if($request->input('columns_in_excel0') == '') {
			$column = [
				'employee__profile.employee_number',
			  'employee__profile.name',
                'employee__pfesi.pf_no'
			];
			
		$outcolumn = [
			'Employee Number',
			  'Employee name',
	          'PF Number',
	          'Gross Wages',
	          'EPF Wages',
              'EPS Wages',
              'EDLI Wages',
              'EPF Contribution',
              'EPS Contribution',
              'EPF EPS Difference',
              'NCP Days'
		];		

		} else {
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn = array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = EmployeeProfile::leftJoin('employee__pfesi',function($join){
            $join->on('employee__pfesi.emp_id','=','employee__profile.id');
            $join->where('employee__pfesi.pf','<>',NULL);
        })
        ->leftJoin('payroll__salary',function($join) use($month,$year){
            $join->on('payroll__salary.emp_id','=','employee__profile.id');
            $join->where('payroll__salary.salary_type','salaryA');
            $join->whereMonth('payroll__salary.month',$month);
            $join->whereYear('payroll__salary.month',$year);
        })
        ->leftJoin('payroll as salaryA',function($join){
                        $join->on('salaryA.emp_id','=','employee__profile.id');
                        $join->where('salaryA.salary_type','salaryA');
                    })
            ->leftJoin('payroll as salaryC',function($join){
                        $join->on('salaryC.emp_id','=','employee__profile.id');
                        $join->where('salaryC.salary_type','salaryC');
                    })
           
            ->leftJoin('payroll__attendance',function($join) use($month,$year){
                $join->on('payroll__attendance.emp_id','=','employee__profile.id');
                $join->whereMonth('payroll__attendance.date',$month);
                $join->whereYear('payroll__attendance.date',$year);
                $join->where('payroll__attendance.status','P');
            })
            ->where('employee__pfesi.pf','<>',NULL)->select(
				'employee__profile.employee_number' ,
                'employee__profile.name',
                // 'employee__profile.employee_number',
                'employee__pfesi.pf_no',
                DB::raw('(IFNULL(salaryC.basic_salary+salaryC.dearness_allowance,"0")) as gross_wages '),
				DB::raw('(IFNULL(payroll__salary.effective_present,"0")) as  epf'),

						DB::raw('(IFNULL(salaryC.basic_salary+salaryC.dearness_allowance,"0")) as eps '),
						DB::raw('(IFNULL(salaryC.basic_salary+salaryC.dearness_allowance,"0")) as edli '),
						DB::raw('(IFNULL(salaryC.basic_salary+salaryC.dearness_allowance,"0")) as epf_con '),
						DB::raw('(IFNULL(salaryC.basic_salary+salaryC.dearness_allowance,"0")) as eps_con '),
						DB::raw('(IFNULL(salaryC.basic_salary+salaryC.dearness_allowance,"0")) as epf_eps '),
                DB::raw('(IFNULL(payroll__salary.effective_absent,"0")) as ncp'))->groupBy('employee__profile.id');
		if($request->input('search_in_excel0') != '') {
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0') != '') {
			foreach($request->input('order_in_excel0') as $order) {
				$col = explode(' ',$order)[0];
				$by = explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		} else {
			$db_data = $db_data->orderBy('employee__profile.id','desc');			
		}
		if($request->input('search_in_excel0')!='')
		{
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		$db_data = $db_data->get();
		foreach($db_data as $key){
			
				$key['epf']=$key['gross_wages']/$dd*$key['epf'];
			$key['eps']=$key['epf'];
			$key['edli']=$key['epf'];
			$key['epf_con']=$key['epf']*0.12;
			$key['eps_con']=$key['epf']*0.0833;
			$key['epf_eps']=$key['epf_con']-$key['eps_con'];
			if($key['epf']==0){$key['epf']="0";}
			if($key['eps']==0){$key['eps']="0";}
			if($key['epf_con']==0){$key['epf_con']="0";}
			if($key['eps_con']==0){$key['eps_con']="0";}
			if($key['epf_eps']==0){$key['epf_eps']="0";}
		}
		// print_r($db_data);die;
		return Excel::download(new DataExport($db_data,$outcolumn,'pf_register'), 'pf_register.xlsx');
		
	}

	public function esiregister(Request $request,$column=[]) {
		$outcolumn = [];
		$month = $_GET["from"];
		$year = $_GET["to"];
		if ($month != null) {
			$month = $_GET["from"];
		}else{
			$month = date('m');
		}
		if ($year != null) {
			$year = $_GET["to"];
		}else{
			$year = date('Y');
		}
		$dd=date('t',strtotime($month));
		$outcolumn1 = [	
			'employee__profile.employee_number' => 'Employee Number',
			  'employee__profile.name' => 'Employee Name',
	          'employee__pfesi.pf_no' => 'PF Number'
		];
		if($request->input('columns_in_excel0') == '') {
			$column = [
				'employee__profile.employee_number',
			    'employee__profile.name',
                'employee__pfesi.esi_no'    
			];
			
		$outcolumn = [
			'Employee Number',
			  'Employee name',
	          'ESI Number',
	          'Wages',
	          'Total monthly wage'
		];		

		} else {
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn = array_merge($outcolumn,array($outcolumn1[$k]));
		}
		$db_data = EmployeeProfile::leftJoin('employee__pfesi',function($join){
            $join->on('employee__pfesi.emp_id','=','employee__profile.id');
            $join->where('employee__pfesi.esi','<>',NULL);
        })
        ->leftJoin('payroll__salary',function($join) use($month,$year){
            $join->on('payroll__salary.emp_id','=','employee__profile.id');
            $join->where('payroll__salary.salary_type','salaryA');
            $join->whereMonth('payroll__salary.month',$month);
            $join->whereYear('payroll__salary.month',$year);
        })
        ->leftJoin('payroll as salaryA',function($join){
                        $join->on('salaryA.emp_id','=','employee__profile.id');
                        $join->where('salaryA.salary_type','salaryA');
                    })
            ->leftJoin('payroll as salaryC',function($join){
                        $join->on('salaryC.emp_id','=','employee__profile.id');
                        $join->where('salaryC.salary_type','salaryC');
                    })
           
            ->leftJoin('payroll__attendance',function($join) use($month,$year){
                $join->on('payroll__attendance.emp_id','=','employee__profile.id');
                $join->whereMonth('payroll__attendance.date',$month);
                $join->whereYear('payroll__attendance.date',$year);
                $join->where('payroll__attendance.status','P');
            })
            ->where('employee__pfesi.esi','<>',NULL)
            ->select(
				'employee__profile.employee_number',
                'employee__profile.name',
				'employee__pfesi.esi_no',
				DB::raw('IFNULL(salaryC.basic_salary+salaryC.dearness_allowance,"0") as gross_wages '),
				DB::raw('(IFNULL(payroll__salary.effective_present,"0")) as monthly_wages'))->groupBy('employee__profile.id');
		if($request->input('search_in_excel0') != '') {
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0') != '') {
			foreach($request->input('order_in_excel0') as $order) {
				$col = explode(' ',$order)[0];
				$by = explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		} else {
			$db_data = $db_data->orderBy('employee__profile.id','desc');			
		}
		$db_data = $db_data->get();
		foreach($db_data as $key){
			
			$key['monthly_wages']=$key['gross_wages']/$dd*$key['monthly_wages'];
			if($key['monthly_wages']==0){$key['monthly_wages']="0";}
	}
		return Excel::download(new DataExport($db_data,$outcolumn,'esi_register'), 'esi_register.xlsx');
	}

	public function leaveregister(Request $request,$column=[]) {
		$from = $_GET["from"];
		$year = $_GET["to"];
		if ($from != null || $from != 0) {
			$emp = $_GET["from"];
		}else{
			$emp = 0;
		}
		if ($year != null || $year != 0) {
			$year = $_GET["to"];
		}else{
			$year = date('Y');
		}
        $date =  $year;
        $a_date =  $year;
        $less_yr = $a_date-1;

        $mon = ['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Decem'];

        $sheet = array('leave_register','present_day','leave_adjust_date','leave_adjust_no','monthly_leave_balance');

		$outcolumn1 = array(
			  'leave_register' => array('employee__profile.name' => 'Employee Name'),
			  'present_day' => array('employee__profile.name' => 'Employee Name'),
			  'leave_adjust_date' => array('employee__profile.name' => 'Employee Name'),
			  'leave_adjust_no' => array('employee__profile.name' => 'Employee Name'),
			  'monthly_leave_balance' => array('employee__profile.name' => 'Employee Name')
		);
		
		$col = array(
			'leave_register' => array('employee__profile.name','employee_number'),
			'present_day' => array('employee__profile.name','employee_number'),
			'leave_adjust_date' => array('employee__profile.name','employee_number'),
			'leave_adjust_no' => array('employee__profile.name','employee_number'),
			'monthly_leave_balance' => array('employee__profile.name','employee_number')
	  );

		$outcol = array(
			'leave_register' => array('Employee name','Employee Code','Address','Designation','Department','Father Name','Opening Leave balance for the year','Total days present in current year','No of leaves calculated for the year','Total leaves (Closing balance for the year+no of leaves calculated)','Leaves carried forward','Leaves Paid'),
			'present_day' => array('Employee Name','Employee Code','January ','February','March','April','May','June','July','August','September','October','November','December'),
			'leave_adjust_date' => array('Employee Name','Employee Code','January ','February','March','April','May','June','July','August','September','October','November','December'),
			'leave_adjust_no' => array('Employee Name','Employee Code','January ','February','March','April','May','June','July','August','September','October','November','December'),
			'monthly_leave_balance' => array('Employee Name','Employee Code','Total Leaves','January ','February','March','April','May','June','July','August','September','October','November','December')
		);	
		for($i=0;$i<count($sheet);$i++)
		{
			$column[$sheet[$i]]=[];
			$outcolumn[$sheet[$i]]=[];
			
			if($request->input('columns_in_excel'.$i)=='')
			{
				$column[$sheet[$i]] = $col[$sheet[$i]];
				$outcolumn[$sheet[$i]] =$outcol[$sheet[$i]];
			}
			else
			{
				$column[$sheet[$i]] = $request->input('columns_in_excel'.$i);
				foreach($column[$sheet[$i]] as $k)
					$outcolumn[$sheet[$i]] =array_merge($outcolumn[$sheet[$i]],array($outcolumn1[$sheet[$i]][$k]));
			}
		}

		$mon = ['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Decem'];
		for ($j = 1; $j <= 12 ; $j++) {
			// $emp_id = $user[$j]['party_id'];
			$md=$mon[$j];
			$query[$j] = "IFNULL((SELECT count(att.status) FROM payroll__attendance att 
			WHERE  att.emp_id=employee__profile.id AND att.status<>'A' AND YEAR(att.date)=".$a_date.".  AND MONTH(att.date) = ".$j." ),'') as ".$mon[$j]." ";	 
			$leaves[$j]="IFNULL((SELECT group_concat(att.date) FROM hr__leave_details att 
			WHERE  att.emp_id=employee__profile.id AND att.is_adjusted='1' AND att.status='Approved' AND YEAR(att.date)=".$a_date.".  AND MONTH(att.date) = ".$j." ),'') as ".$mon[$j]." ";

			$leaves_count[$j]="IFNULL((SELECT count(att.date) FROM hr__leave_details att 
			WHERE att.emp_id=employee__profile.id AND att.is_adjusted='1' AND att.status='Approved' AND YEAR(att.date)=".$a_date.".  AND MONTH(att.date) = ".$j." ),'') as ".$mon[$j]." ";

			$leaves_balance[$j]="IFNULL((SELECT count(att.date) FROM hr__leave_details att 
			WHERE att.emp_id=employee__profile.id AND att.is_adjusted='1' AND att.status='Approved' AND YEAR(att.date)=".$a_date.".  AND MONTH(att.date) <= ".$j." ),'') as ".$mon[$j]." ";
			
		}
		$query = join(",",$query);
		$leaves=join(",",$leaves);
		$leaves_count=join(",",$leaves_count);
		$leaves_balance=join(",",$leaves_balance);
		$db_data['leave_register'] =EmployeeProfile::leftJoin('payroll__attendance', function($join) use ($a_date){
			 $join->on('payroll__attendance.emp_id','=','employee__profile.id');
			 $join->whereYear('payroll__attendance.date','=',$a_date);
			 $join->where('payroll__attendance.status','!=',"A");
			 $join->orWhere('payroll__attendance.emp_id','=',NULL);
		})
		->leftJoin('department','department.id','employee__profile.department_id')
		
		 ->leftJoin('leave__enhancement', function($join) use ($a_date){
			 $join->on('leave__enhancement.emp_id','=','employee__profile.id');
			 $join->where('leave__enhancement.year','=',$a_date);
		})->select('name','employee_number', 
		DB::raw('IFNULL(local_address,"-") as local_address'),
		DB::raw('IFNULL(designation,"-") as designation'),
		DB::raw('IFNULL(department.department,"-") as department'),
		DB::raw('IFNULL(father_name,"-") as father_name'),
		
		 DB::raw('ROUND(
			(IFNULL((SELECT count(m.id) FROM payroll__attendance m WHERE employee__profile.id=m.emp_id AND m.status<>"A" AND YEAR(m.date)='.$less_yr.' GROUP BY employee__profile.id) ,0 ))/20
			) + (IFNULL(carried_leave,"0"))   as opening_l'),
			DB::raw('(IFNULL(
				(SELECT count(m.id) FROM payroll__attendance m 
				WHERE employee__profile.id=m.emp_id
				AND m.status<>"A"
				AND YEAR(m.date)='.$a_date.'
					GROUP BY employee__profile.id) ,"0" ) 
				) as total_present_current'),
		   DB::raw('ROUND(
			(IFNULL((SELECT count(m.id) FROM payroll__attendance m WHERE employee__profile.id=m.emp_id AND m.status<>"A" AND YEAR(m.date)='.$less_yr.' GROUP BY employee__profile.id) ,0 ))/20
			)  as no_of_leaves'),  

			DB::raw('ROUND(
				(IFNULL((SELECT count(m.id) FROM payroll__attendance m WHERE employee__profile.id=m.emp_id AND m.status<>"A" AND YEAR(m.date)='.$less_yr.' GROUP BY employee__profile.id) ,0 ))/20
				) + (IFNULL(carried_leave,"0"))   as total_leaves'),
	
				 DB::raw('IFNULL(carried_leave,"0") as carried_leave'),
				 DB::raw('IFNULL(paid_leave,"-") as paid_leave'))->GroupBy('employee__profile.id');

				//  print_r($db_data['leave_register']);die;
		  
			$db_data['present_day']=EmployeeProfile::leftJoin('payroll__attendance', function($join) use ($a_date){
            $join->on('payroll__attendance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__attendance.date',$a_date);
            $join ->where('payroll__attendance.status','!=',"A");
			})->select('employee__profile.name',
			'employee_number',DB::raw($query))->GroupBy('employee__profile.id');

			$db_data['leave_adjust_date']=EmployeeProfile::leftJoin('hr__leave_details', function($join) use ($a_date){
			$join->on('hr__leave_details.emp_id','=','employee__profile.id');
			$join->WhereYear('hr__leave_details.date',$a_date);
			$join ->where('hr__leave_details.status','=',"Approved");
			})->select('employee__profile.name',
			'employee_number',DB::raw($leaves))->GroupBy('employee__profile.id');

			$db_data['leave_adjust_no']=EmployeeProfile::leftJoin('hr__leave_details', function($join) use ($a_date){
			$join->on('hr__leave_details.emp_id','=','employee__profile.id');
			$join->WhereYear('hr__leave_details.date',$a_date);
			$join ->where('hr__leave_details.status','=',"Approved");
			})->select('employee__profile.name',
			'employee_number',DB::raw($leaves_count))->GroupBy('employee__profile.id');
			
			$db_data['monthly_leave_balance']=EmployeeProfile::leftJoin('hr__leave_details', function($join) use ($a_date){
				$join->on('hr__leave_details.emp_id','=','employee__profile.id');
				$join->WhereYear('hr__leave_details.date',$a_date);
				$join ->where('hr__leave_details.status','=',"Approved");
				})
				->leftJoin('payroll__attendance', function($join) use ($a_date){
					$join->on('payroll__attendance.emp_id','=','employee__profile.id');
					$join->whereYear('payroll__attendance.date','=',$a_date);
					$join->where('payroll__attendance.status','!=',"A");
					$join->orWhere('payroll__attendance.emp_id','=',NULL);
			   })
			   ->leftJoin('leave__enhancement', function($join) use ($a_date){
				$join->on('leave__enhancement.emp_id','=','employee__profile.id');
				$join->where('leave__enhancement.year','=',$a_date);
		   })
				->select('employee__profile.name',
				'employee_number',DB::raw('IFNULL(ROUND(
					(IFNULL((SELECT count(m.id) FROM payroll__attendance m WHERE employee__profile.id=m.emp_id AND m.status<>"A" AND YEAR(m.date)='.$less_yr.' GROUP BY employee__profile.id) ,0 ))/20
					) + (IFNULL(carried_leave,"0")),"0" )  as total_leaves')
					,DB::raw($leaves_balance))->GroupBy('employee__profile.id');
		if($emp!=0){
			$db_data['leave_register']->where(function($query) use ($emp){
				$query->where('employee__profile.id','=',$emp);
			});
			$db_data['present_day']->where(function($query) use ($emp){
				$query->where('employee__profile.id','=',$emp);
			});
			$db_data['leave_adjust_date']->where(function($query) use ($emp){
				$query->where('employee__profile.id','=',$emp);
			});
			$db_data['leave_adjust_no']->where(function($query) use ($emp){
				$query->where('employee__profile.id','=',$emp);
			});
			$db_data['monthly_leave_balance']->where(function($query) use ($emp){
				$query->where('employee__profile.id','=',$emp);
			}); 
		}
		for($i=0;$i<count($sheet);$i++)
			if($request->input('search_in_excel'.$i)!='')
			{
				$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->where(explode(' ',$request->input('search_in_excel'.$i))[0],$request->input('search_val_in_excel'.$i)); 
			}
		if($request->input('order_in_excel0')!='')
		{
			foreach($request->input('order_in_excel0') as $order)
			{
				$col = explode(' ',$order)[0];
				$by= explode(' ',$order)[1];
				$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy($col,$by);
				$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy($col,$by);
				}
			}
		// else
		// {
		// 	$db_data[$sheet[0]] = $db_data[$sheet[0]]->orderBy('tax_invoice.id','desc');			
		// 	$db_data[$sheet[1]] = $db_data[$sheet[1]]->orderBy('tax_invoice.id','desc');			
		// }
		$data = $db_data[$sheet[0]]->get();
		$db_data[$sheet[0]] = $db_data[$sheet[0]]->get();
		for($i=1;$i<count($sheet);$i++){
			$db_data[$sheet[$i]] = $db_data[$sheet[$i]]->get();
		}

			foreach($db_data['monthly_leave_balance'] as $key=>$value){
			
				$value['Jan']=$value['total_leaves']-$value['Jan'];

				$value['Feb']=$value['total_leaves']-$value['Feb'];

				$value['Mar']=$value['total_leaves']-$value['Mar'];

				$value['Apr']=$value['total_leaves']-$value['Apr'];

				$value['May']=$value['total_leaves']-$value['May'];

				$value['Jun']=$value['total_leaves']-$value['Jun'];

				$value['Jul']=$value['total_leaves']-$value['Jul'];

				$value['Aug']=$value['total_leaves']-$value['Aug'];

				$value['Sep']=$value['total_leaves']-$value['Sep'];

				$value['Oct']=$value['total_leaves']-$value['Oct'];

				$value['Nov']=$value['total_leaves']-$value['Nov'];

				$value['Decem']=$value['total_leaves']-$value['Decem'];
				
			}
			
		return Excel::download(new DataExportSheet($db_data,$outcolumn,$sheet), 'leaveregister.xlsx');
    }


    public function salaryA_export(Request $request,$column=[]) {
		$outcolumn = [];
		$emp = $_GET["from"];
		$month = $_GET["to"];
		if ($month != null) {
			$month = $_GET["to"];
		}else{
			$month = date('m');
		}
		$outcolumn1 = [	
			  'employee__profile.name' => 'Employee Name','employee__bank.acc_number' => 'Account Number','employee__bank.acc_ifsc' => 'IFSC Code','payroll__salary.net_salary' => 'Net Salary'
		];
		if($request->input('columns_in_excel0') == '') {
			$column = [
			    'employee__profile.name',
                'employee__bank.acc_number',
                'employee__bank.acc_ifsc',
                'payroll__salary.net_salary'
			];
			
		$outcolumn = [
			  'Employee name',
	          'Account Number',
	          'IFSC',
	          'Net Salary'
		];		

		} else {
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn = array_merge($outcolumn,array($outcolumn1[$k]));
		}
		if ($emp == 0) {
			$db_data = NetSalary::leftJoin('employee__profile','payroll__salary.emp_id','employee__profile.id')
	            ->leftjoin('employee__bank','employee__bank.emp_id','payroll__salary.emp_id')
	            ->whereMonth('payroll__salary.month', date($month))
	            ->where('payroll__salary.salary_type','SalaryA');
		}else{
			$db_data = NetSalary::leftJoin('employee__profile','payroll__salary.emp_id','employee__profile.id')
            ->leftjoin('employee__bank','employee__bank.emp_id','payroll__salary.emp_id')
            ->whereMonth('payroll__salary.month', date($month))
            ->where('employee__profile.id',$emp)
            ->where('payroll__salary.salary_type','SalaryA');
		}
		if($request->input('search_in_excel0') != '') {
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0') != '') {
			foreach($request->input('order_in_excel0') as $order) {
				$col = explode(' ',$order)[0];
				$by = explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		} else {
			$db_data = $db_data->orderBy('employee__profile.id','desc');			
		}
		$db_data = $db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'salaryA_export'), 'salaryA_export.xlsx');
	}

	public function salaryB_export(Request $request,$column=[]) {
		$outcolumn = [];
		$emp = $_GET["from"];
		$month = $_GET["to"];
		if ($month != null) {
			$month = $_GET["to"];
		}else{
			$month = date('m');
		}
		$outcolumn1 = [	'employee__profile.name' => 'Employee Name','employee__bank.acc_number' => 'Account Number','employee__bank.acc_ifsc' => 'IFSC Code','payroll__salary.net_salary' => 'Net Salary'
		];
		if($request->input('columns_in_excel0') == '') {
			$column = [
			    'employee__profile.name',
                'employee__bank.acc_number',
                'employee__bank.acc_ifsc',
                'payroll__salary.net_salary'
			];
			
		$outcolumn = [
			  'Employee name',
	          'Account Number',
	          'IFSC',
	          'Net Salary'
		];		

		} else {
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn = array_merge($outcolumn,array($outcolumn1[$k]));
		}
		if ($emp == 0) {
			$db_data = NetSalary::leftJoin('employee__profile','payroll__salary.emp_id','employee__profile.id')
	            ->leftjoin('employee__bank','employee__bank.emp_id','payroll__salary.emp_id')
	            ->whereMonth('payroll__salary.month', date($month))
	            ->where('payroll__salary.salary_type','SalaryB');
		}else{
			$db_data = NetSalary::leftJoin('employee__profile','payroll__salary.emp_id','employee__profile.id')
            ->leftjoin('employee__bank','employee__bank.emp_id','payroll__salary.emp_id')
            ->whereMonth('payroll__salary.month', date($month))
            ->where('employee__profile.id',$emp)
            ->where('payroll__salary.salary_type','SalaryB');
		}
		if($request->input('search_in_excel0') != '') {
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0') != '') {
			foreach($request->input('order_in_excel0') as $order) {
				$col = explode(' ',$order)[0];
				$by = explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		} else {
			$db_data = $db_data->orderBy('employee__profile.id','desc');			
		}
		$db_data = $db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'salaryB_export'), 'salaryB_export.xlsx');
	}

	public function salaryC_export(Request $request,$column=[]) {
		$outcolumn = [];
		$emp = $_GET["from"];
		$month = $_GET["to"];
		if ($month != null) {
			$month = $_GET["to"];
		}else{
			$month = date('m');
		}
		$outcolumn1 = [	'employee__profile.name' => 'Employee Name','employee__bank.acc_number' => 'Account Number','employee__bank.acc_ifsc' => 'IFSC Code','payroll__salary.net_salary' => 'Net Salary'
		];
		if($request->input('columns_in_excel0') == '') {
			$column = [
			    'employee__profile.name',
                'employee__bank.acc_number',
                'employee__bank.acc_ifsc',
                'payroll__salary.net_salary'
			];
			
		$outcolumn = [
			  'Employee name',
	          'Account Number',
	          'IFSC',
	          'Net Salary'
		];		

		} else {
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn = array_merge($outcolumn,array($outcolumn1[$k]));
		}
		if ($emp == 0) {
			$db_data = NetSalary::leftJoin('employee__profile','payroll__salary.emp_id','employee__profile.id')
	            ->leftjoin('employee__bank','employee__bank.emp_id','payroll__salary.emp_id')
	            ->whereMonth('payroll__salary.month', date($month))
	            ->where('payroll__salary.salary_type','SalaryC');
		}else{
			$db_data = NetSalary::leftJoin('employee__profile','payroll__salary.emp_id','employee__profile.id')
            ->leftjoin('employee__bank','employee__bank.emp_id','payroll__salary.emp_id')
            ->whereMonth('payroll__salary.month', date($month))
            ->where('employee__profile.id',$emp)
            ->where('payroll__salary.salary_type','SalaryC');
		}
		if($request->input('search_in_excel0') != '') {
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0') != '') {
			foreach($request->input('order_in_excel0') as $order) {
				$col = explode(' ',$order)[0];
				$by = explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		} else {
			$db_data = $db_data->orderBy('employee__profile.id','desc');			
		}
		$db_data = $db_data->select($column)->get();
		return Excel::download(new DataExport($db_data,$outcolumn,'salaryC_export'), 'salaryC_export.xlsx');
	}

	public function salaryRegister(Request $request,$column=[]) {
		$outcolumn = [];
		 $emp = $_GET["from"];
		$yr = $_GET["to"];
		if ($yr != null) {
			$yr = $_GET["to"];
		}else{
			$yr = date('m-Y');
		}
        $yr="01-".$yr;
        $days = date('t',strtotime($yr));
        $month_name = date('M',strtotime($yr));
        $mon = date('m',strtotime($yr));
        $year = date('Y',strtotime($yr));

        $days_arr=Array();
        for ($j = 1; $j <= $days ; $j++) {
            if($j<10){$md="0".$j."_".$month_name;}  
            else{$md=$j."_".$month_name;}
                
            $date=$j."-".$mon."-".$year;
            $date=date('Y-m-d',strtotime($date));
           $days_arr[$md]=$md;

        }



		$outcolumn1 = [	
			  	'employee__profile.name' => 'Employee Name'
		];
		if($request->input('columns_in_excel0') == '') {
			$column = [
			    'employee__profile.name'
			];
			$col = array(
				'Employee Name' => 'Employee Name',
				'Employee Code' => 'Employee Code',
				'Designation'=> 'Designation',
		        'Department' => 'Department',
		        'Father Name' => 'Father Name',
		        'Address' => 'Address',
		        'Total Present days' => 'Total Present days',
		        'Total Absent Days' => 'Total Absent Days',
		       	'Total Salary C paid' => 'Total Salary C paid',
		       	'PF Deduction' => 'PF Deduction',
		       	'ESI Deduction' => 'ESI Deduction',
		       	'Opening advance' => 'Opening advance',
		       	'Advance deducted' => 'Advance deducted',
		       	'Advance balance' => 'Advance balance',
			);
			$coll = array_merge($col,$days_arr);
		$outcolumn = $coll;
			
		} else {
			$column = $request->input('columns_in_excel0');
			foreach($column as $k)
				$outcolumn = array_merge($outcolumn,array($outcolumn1[$k]));
		}
       

      
        $leavess=Array();
        for ($j = 1; $j <= $days ; $j++) {
            if($j<10){$md="0".$j."_".$month_name;}  
            else{$md=$j."_".$month_name;}
                
            $date=$j."-".$mon."-".$year;
            $date=date('Y-m-d',strtotime($date));
            $query[$j] = "IFNULL((SELECT att.status FROM payroll__attendance att WHERE  att.emp_id = payroll__attendance.emp_id AND YEAR(att.date)=".$year.".  AND att.date = '".$date."' ),'') as ".$md." ";
            $leave=HR_LeaveDetails::where('emp_id',$emp)
            ->where('date','=',$date)
            ->where('is_adjusted','=','1')
            ->select('date as '.$md.'')
            ->get()->first();
            $leavess[$md]=$leave[$md];

        }
        $leavess = array_filter($leavess); 
        $arr_leaves=Array();
        foreach($leavess as $key=>$value){
            $arr_leaves[$key]="L";
        }
        
        $query = join(",",$query);
        $holiday=Holiday::whereMonth('holiday.start_date', '<=', $mon)
        ->whereMonth('holiday.end_date', '>=', $mon)
        ->whereYear('holiday.start_date', '<=', $year)
        ->whereYear('holiday.end_date', '>=', $year)
        ->select('name','start_date','end_date')
        ->get();

        $arr=Array();
        foreach($holiday as $key){
            $diff = strtotime($key['end_date']) - strtotime($key['start_date']);
            $diff=abs(round($diff / 86400)) + 1;
            
            $date=date('Y-m-d', strtotime('-1 day', strtotime($key['start_date']))); 
           
            for($i=0;$i<$diff;$i++){
                $date=date('Y-m-d', strtotime('+1 day', strtotime($date)));
                $get_mon=date('m', strtotime($date));
                $Mon=date('M', strtotime($date));
                $get_day=date('d', strtotime($date));
                
                $x=$get_day."_".$Mon;
                
                if($get_mon==$mon){
                    $arr[$x]=$key['name'];
                }
                
               
            }
            
        }

        $db_data = EmployeeProfile::leftJoin('payroll__attendance', function($join) use ($year,$mon){
            $join->on('payroll__attendance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__attendance.date','=',$year);
            $join->WhereMonth('payroll__attendance.date','=',$mon);
       })
       ->leftJoin('department','department.id','employee__profile.department_id')
        ->leftJoin('payroll__advance', function($join) use ($year,$mon){
            $join->on('payroll__advance.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__advance.given_date','=',$year);
            $join->WhereMonth('payroll__advance.given_date','=',$mon);
            
        })
        ->leftJoin('payroll__salary', function($join) use ($year,$mon){
            $join->on('payroll__salary.emp_id','=','employee__profile.id');
            $join->WhereYear('payroll__salary.month','=',$year);
            $join->WhereMonth('payroll__salary.month','=',$mon);
            $join->Where('payroll__salary.salary_type','=',"SalaryC");
            
        })
        ->leftJoin('payroll_salary_detail as pf_ded', function($join) use ($year,$mon){
            $join->on('pf_ded.payroll_salary_id','=','payroll__salary.id');
            $join->Where('pf_ded.name','=',"PF");
            
        })
        ->leftJoin('payroll_salary_detail as esi_ded', function($join) use ($year,$mon){
            $join->on('esi_ded.payroll_salary_id','=','payroll__salary.id');
            $join->Where('esi_ded.name','=',"ESI");
            
        })
        ->leftJoin('payroll_salary_detail as adv_ded', function($join) use ($year,$mon){
            $join->on('adv_ded.payroll_salary_id','=','payroll__salary.id');
            $join->Where('adv_ded.name','=',"Advance");
            
        })
        ->leftJoin('payroll__paid_advance', function($join) use ($year,$mon){
            $join->on('payroll__paid_advance.advance_id','=','payroll__advance.id');
        })
        ->select(
            'employee__profile.name as emp_name',
            'employee__profile.employee_number',
            'employee__profile.designation',
        	'department.department',
        	'employee__profile.father_name',
        	'employee__profile.local_address',
            DB::raw('(IFNULL(
                (SELECT count(m.id) FROM payroll__attendance m 
                WHERE employee__profile.id=m.emp_id
                AND m.status="A"
                AND YEAR(m.date)='.$year.'
                AND MONTH(m.date)='.$mon.'
                    GROUP BY employee__profile.id) ,"0" ) 
                ) as total_absent_current'),
                DB::raw('(IFNULL(
                    (SELECT count(m.id) FROM payroll__attendance m 
                    WHERE employee__profile.id=m.emp_id
                    AND m.status<>"A"
                    AND YEAR(m.date)='.$year.'
                    AND MONTH(m.date)='.$mon.'
                        GROUP BY employee__profile.id) ,"0" ) 
                    ) as total_present_current'),
	            DB::raw('IFNULL(payroll__salary.net_salary,"0") as total_salaryC'),

	            DB::raw('IFNULL(pf_ded.amount,"0") as pf_ded'),
	            DB::raw('IFNULL(esi_ded.amount,"0") as esi_ded'),
	            DB::raw('IFNULL(adv_ded.amount,"0") as adv_ded'),
               
                            DB::raw('(IFNULL(
                                (SELECT sum(m.advance_amount) FROM payroll__advance m 
                                WHERE employee__profile.id=m.emp_id
                                    GROUP BY employee__profile.id) ,"0" ) 
                                ) as opening_advance'),
                                DB::raw('(IFNULL(
                                    (SELECT sum(m.advance_paid) FROM payroll__advance m 
                                    WHERE employee__profile.id=m.emp_id
                                        GROUP BY employee__profile.id) ,"0" ) 
                                    ) as balance_advance'),
                                    DB::raw($query)
        )->GroupBy('employee__profile.id');

        if($emp != 0) {
            $db_data->where(function($query) use ($emp){
                $query->where('employee__profile.id','=',$emp);
            });
        } 


		if($request->input('search_in_excel0') != '') {
			$db_data = $db_data->where(explode(' ',$request->input('search_in_excel0'))[0],$request->input('search_val_in_excel0')); 
		}
		if($request->input('order_in_excel0') != '') {
			foreach($request->input('order_in_excel0') as $order) {
				$col = explode(' ',$order)[0];
				$by = explode(' ',$order)[1];
				$db_data = $db_data->orderBy($col,$by);
			}
		} else {
			$db_data = $db_data->orderBy('employee__profile.id','desc');			
		}
		$db_data = $db_data->get();
		$jj = $db_data;
		if(count($arr_leaves)!=0){
            $arr=$arr_leaves+$arr;   
        }


		foreach ($db_data as $key=>$value) {
			$data = $value->toArray();
			$keydata = array_keys($data);
			$arrkey = array_keys($arr);
			for ($j = 1; $j <= $days ; $j++) {
	            if($j < 10){
	            	$md="0".$j."_".$month_name;
	            }else{
	            	$md=$j."_".$month_name;
	            }
	            if (array_key_exists($md,$arr)) {
	            	$value[$md] = $arr[$md];
	            }else{
	            	$value[$md] = $value[$md];
	            }
	           
	            if ($value[$md] == 'WO') {
	             	 $value[$md] ="Sunday";
	             }else{
	             	$value[$md] = $value[$md];
	             } 
	                         

	        }
			
		}
		
		
		// $jj = $db_data;
  //       if(count($arr_leaves)!=0){
  //           $arr=$arr_leaves+$arr;   
  //       }

  //       foreach($db_data as $key=>$value){

  //           $jj[$key]=$arr+$jj[$key];
  //       }
  //       $db_data = $jj;
		return Excel::download(new DataExport($db_data,$outcolumn,'salaryRegister'), 'salaryRegister.xlsx');
	}
}
?>
