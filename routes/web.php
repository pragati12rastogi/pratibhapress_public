<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth', 'rights']], function () {

    // servers source root :
    Route::get('/getroute', 'IO@getroute');

    Route::get('/', function () {
        return redirect()->intended('/dashboard');
    });
    // servers the main page after login :
    Route::get('/general-dashboard', 'OrderToCollectionController@summary_list');
    Route::get('/dashboard','WidgetAll@widgets');
    Route::get('/staff/directory','WidgetAll@staff');
    Route::get('/staff/directory/api','WidgetAll@staff_api');
    Route::get('/user/create', 'UserController@register_create');
    Route::post('/user/insert', 'UserController@register_insert');

    Route::get('/user/password/update','AdminController@admin_change_pass');
    Route::post('/user/password/update','AdminController@admin_change_pass_db');

    Route::get('/user/log','MastersController@userlog');
    Route::get('/user/log/api','MastersController@logdata');


    Route::get('/order/to/collection/fms','OrderToCollectionController@fms');
    Route::post('/order/to/collection/fms/api','OrderToCollectionController@try_api');

    Route::get('/order/to/collection/fms/old','OrderToCollectionController@fms_old');
    
    Route::post('/getdata','OrderToCollectionController@getdata');


    Route::get('/sendmail','Email\EmailController@sendmail');
    
    Route::post('/sendmail','Email\EmailController@sendmailDb');



    // creates a new master when supplied a valid {$masterName} :
    Route::post('/masters/create/{masterName}', 'MastersController@create');
    // consignee form
    Route::get('/consignee/view/{id}', 'HomeController@consignee_view');

    Route::get('/consignee/create', 'PartyController@consignee');
    Route::post('/consignee/insert', 'PartyController@consignee_insert');    
    // consignee api for datatable in list page
    // consignee update page :  
    Route::get('/consignee/update', 'HomeController@consignee_update');
    // consignee update page functional :  
    Route::post('/consignee/update', 'HomeController@do_consignee_update');

    Route::get('/consignee/list/api', 'HomeController@consignee_all');
    // consignee list page : 
    Route::get('/consignee/list', 'HomeController@consignee_list');

    //consignee form insert from excel
    Route::post('/consignee/insert/excel', 'PartyController@consignee_insert_excel');

    // party form
    Route::get('/client/create', 'PartyController@party_form');

    //party form insert
    Route::post('/client/insert', 'PartyController@party_insert_db');
    Route::get('/reference/filter/api', 'PartyController@reference_search');


    // a table contatinign all parties 
    Route::get('/client/list', 'PartyController@partylist');

    // api for data providing to partylist table 
    Route::get('/client/list/api', 'PartyController@partylistdata');

    // a table contatinign all parties 
    Route::get('/reference/list', 'PartyController@referencelist');

    // api for data providing to partylist table 
    Route::get('/reference/list/api', 'PartyController@referencelistdata');

    Route::get('/reference/update', 'PartyController@update_reference');
    // post for updation
    Route::post('/reference/update', 'PartyController@do_reference_update');

    Route::get('/reference/delete', 'PartyController@delete_reference');
    // post for delete
    Route::post('/reference/delete', 'PartyController@do_reference_delete');

    // update party data
    Route::get('/client/update', 'PartyController@update_party');
    // post for updation
    Route::post('/client/update', 'PartyController@do_party_update');

    // see all deatils of single party
    Route::get('/client/view', 'PartyController@view_party');

     //consignee form insert

     // country state form
     Route::get('/state/{id}', 'PartyController@states');
     // country city form
     Route::get('/city/{id}', 'PartyController@cities');

     // internal order page :
    Route::get('/internalorder', 'IO@internal_order_form');
    // Delivery challan Api
    Route::get('/deliverychallan/api','IO@delivery_challan_api');
    // Delivery challan listing
    Route::get('/deliverychallan/list','IO@delivery_challan_list');
    // Delivery challan View
    Route::get('/deliverychallan/view/{id}','IO@delivery_challan_view');
    // Delivery challan update
    Route::get('/deliverychallan/update/{id}','IO@delivery_challan_update');
    Route::get('/deliverychallan/delete/{id}/{do}','IO@delivery_challan_delete');
    Route::get('/taxinvoice/delete/{id}/{do}','IO@tax_invoice_delete');
    // Delivery challan update in DB
    Route::post('/deliverychallan/updatedb/{id}','IO@delivery_challan_updatedb');
    //internal listing
    Route::get('/internal/list/{status}', 'IO@internal_list');
    Route::get('/internal/stausupdate/{status}/{id}', 'IO@internal_statusupdate');
    //-----------------dispatch planning----------------------
    Route::get('/dispatch/list/api','IO@dispatch_list_api');
    Route::get('/dispatch/list','IO@dispatch_list');
    Route::post('/dispatch/plan/create/{id}','IO@dispatch_plan');

    Route::get('/dispatch/daily/planned/report/list','IO@daily_dispatch_report');
Route::get('/dispatch/daily/planned/report/list/api','IO@daily_dispatch_report_api');
Route::get('/dispatch/data/{id}','IO@dispatch_data');

Route::get('/dispatch/details/{id}','IO@dispatch_details');
    //internal form insert
    Route::post('/internal/insert', 'IO@internal_insert');

    //internal order list api
    Route::get('/internalorder/list','IO@internal_all');
    //internal order update
    
    Route::get('/internalorder/view/{id}','IO@internal_view');
    Route::get('/internalorder/update/{id}','IO@internal_update');
//------------io edit request---------------
    Route::get('/addreqiredpermission/internalorder/update/{id}','IO@io_update_request');
    Route::post('/addreqiredpermission/internalorder/update/{id}','IO@io_update_request_db');

    Route::get('/admin/auth/ioedit/req/api','IO@admin_auth_req_api');
    Route::get('/admin/auth/ioedit/req','IO@io_edit_auth_req');

    Route::get('/admin/io/edit/req/grant/{id}/{operation}','IO@io_edit_req_update');

    //internal order update
    Route::post('/internalorder/update/db/{id}','IO@internal_order_update_db');
//-------------------------------JOB CARD------------------------------------------------------------------------
     // job card page :
     Route::get('/jobcard/create', 'JobCardController@jobcard_form');
     Route::get('/internalorder/{id}','JobCardController@io');
     //Job Card form insert
     Route::post('/jobcard/insert', 'JobCardController@jobcard_insert');
     //Job Card details by io id
     Route::get('/jobcard/detail/{id}', 'JobCardController@job_card_details');
      // Element page :
      Route::get('/element/create/{jc_id}/{io_id}', 'JobCardController@element_form');
      //Element page
      Route::post('/element/insert', 'JobCardController@element_insert');
      // raw material page :
      Route::get('/rawmaterial/create/{jc_id}/{io_id}', 'JobCardController@raw_material_form');
      //Raw Material form insert
      Route::post('/rawmaterial/insert', 'JobCardController@raw_material_insert');
      // Binding page :
      Route::get('/binding/create/{jc_id}/{io_id}', 'JobCardController@binding_form');
      //Binding form insert
      Route::post('/binding/insert', 'JobCardController@binding_insert');

      //Job Card Update
      Route::get('/jobcardform/update/{id}', 'JobCardController@jobcard_update');
      Route::post('/jobcard/updateDB/{id}', 'JobCardController@jobcardupdateDB');

      Route::get('/elementform/update/{jc_id}/{io_id}', 'JobCardController@elementformupdate');
      Route::post('/elementform/updateDB/{jc_id}/{io_id}', 'JobCardController@elementformupdateDB');

      Route::get('/rawform/update/{jc_id}/{io_id}', 'JobCardController@rawformupdate');
      Route::post('/rawform/updateDB/{jc_id}/{io_id}', 'JobCardController@rawformupdateDB');

      Route::get('/bindingform/update/{jc_id}/{io_id}', 'JobCardController@bindingformupdate');
      Route::post('/bindingform/updateDB/{jc_id}/{io_id}', 'JobCardController@bindingformupdateDB');

      //job card list
      Route::get('/jobcard/api/{status}','JobCardController@job_card_api');
      Route::get('/JobCard/list/{status}','JobCardController@job_card_list');
      Route::get('/jobcard/status/prod', 'JobCardController@jobcard_statusupdate_prod');
      Route::get('/jobcard/status/prod/api/', 'JobCardController@jobcard_statusupdate_prod_api');
      Route::get('/jobcard/status/log', 'JobCardController@jobcard_statusupdate_log');
      Route::get('/jobcard/status/log/api/', 'JobCardController@jobcard_statusupdate_log_api');
      Route::get('/JobCard/stausupdate/{status}/{id}', 'JobCardController@jobcard_statusupdate');
      Route::get('/JobCard/view/{id}','JobCardController@jobcard_view');
            
//--------------------------------------END JOB CARD------------------------------------------------------------
     //PO form View
//      Route::get('/createpo', 'MastersController@po_form');

      //PO form insert
      Route::post('/POinsert', 'MastersController@po_insert');

      //Setting Form View
      Route::get('/settings', 'MastersController@setting');
      //Setting Form insert
      Route::post('/setting/addform', 'MastersController@settingaddform');
      
       //GET PO by party
       
              
       Route::get('/hsn/list/edit/{id}', 'MastersController@viewHSNListEdit');
      // Route::get('/hsn/list/del/{id}', 'MastersController@viewHSNListDelete');
       Route::get('/hsn/list/api', 'MastersController@viewHSNListApi');
       Route::get('/hsn/list', 'MastersController@viewHSNList');
       Route::post('/hsn/update/{id}', 'MastersController@hsnupdate');
       //GET HSN by item
       Route::get('/item/{id}', 'MastersController@get_hsn_from_id');
       //HSN form View
       Route::get('/hsn/create', 'MastersController@hsn_form');
       //HSN form insert
       Route::post('/hsn/insert', 'MastersController@hsn_insert');

       Route::get('/vehicle/create', 'MastersController@vehicle_form');
       //HSN form insert
       Route::post('/vehicle/insert', 'MastersController@vehicle_insert');
       Route::get('/vehicle/update/{id}', 'MastersController@vehicle_update');
       Route::post('/vehicle/update/db/{id}', 'MastersController@vehicle_update_db');
       // Route::get('/hsn/list/del/{id}', 'MastersController@viewHSNListDelete');
        Route::get('/vehicle/list/api', 'MastersController@vehicle_list_api');
        Route::get('/vehicle/list', 'MastersController@vehicle_list');
       
       
        //Goods Dispatch form View
        Route::get('/createdispatch', 'MastersController@dispatch_form');
        
        Route::get('/goodsdispatch/list', 'MastersController@goodsdispatch_list');
        Route::get('/goodsdispatch/list/api', 'MastersController@goodsdispatch_api');
        Route::get('/dispatch/edit/{id}', 'MastersController@dispatch_edit');
        Route::post('/GoodsDispatchupdate/{id}', 'MastersController@dispatch_update');
       
         //HSN form insert
         Route::post('/GoodsDispatch/insert', 'MastersController@GoodsDispatch_insert');
         
          //client PO form View
        Route::get('/clientpo', 'IO@client_po_form');
        
        Route::get('/clientpo/list', 'IO@client_po_list');
        Route::get('/clientpo/api', 'IO@client_po_api');
        Route::get('/clientpo/view/{id}', 'IO@client_po_view');
        Route::get('/clientpo/update/{id}', 'IO@client_po_update');
        Route::post('/clientpo/updatedb/{id}', 'IO@client_po_updatedb');
        Route::get('/clientpo/delete/{id}', 'IO@client_po_delete');

        // GET details by party
        Route::get('/details/internalOrder/{id}', 'IO@get_internal_order_by_partyid');
        
      
      Route::get('/details/consignee/{id}', 'PartyController@get_consignee_by_partyid');

      //GET details by party
      
      Route::get('/details/{id}', 'IO@client_po_details_by_refname');
      Route::get('/clientpo/details/party/{id}', 'IO@client_po_details_by_partyname');
       
       Route::get('/uom/details', 'MastersController@get_uom'); 
       Route::get('/io/details/{id}', function ($id){
            $details=\App\Model\InternalOrder::leftjoin('job_details','internal_order.job_details_id','=','job_details.id')
                                                ->leftJoin('hsn','hsn.id','=','job_details.hsn_code')
                                                ->where('internal_order.id',$id)
                                                ->where('internal_order.is_active',1)->get();
            $consignee_po=\App\Model\Client_po::where('io',$id)
                                                ->leftJoin('client_po_consignee','client_po_consignee.client_po_id','=','client_po.id')
                                                ->leftJoin('consignee','client_po_consignee.consignee_id','=','consignee.id')
                                                ->get();
            $qty=\App\Model\Challan_per_io::where('io',$id)->select(DB::raw('IFNULL(SUM(good_qty),0) as good_qty'))->get()->first();
            $arr=array([
                'details'=>$details,
                'consignee_po'=>$consignee_po,
                'qty'=>$qty
            ]);
            return $arr;
        });


        Route::get('/po/details/{id}', 'IO@get_io_details_by_po_io');
        Route::get('/po/details/po/{id}', 'IO@get_po_details_by_po');

        Route::get('/rate/{id}/{rate}', 'IO@update_rate');
        
    
    //client PO form insert
        Route::post('/clientPoInsert','IO@clientPoInsert');
        Route::get('/get/user/ip', 'IO@get_user_ip');
        
        
        Route::get('/summary', 'OrderToCollectionController@summry');
        Route::get('/proof/of/delivery/', 'OrderToCollectionController@pod');
        Route::post('/proof/of/delivery', 'OrderToCollectionController@pod_db');
        Route::get('/proof/of/delivery/summary', 'OrderToCollectionController@pod_summary');
        Route::get('/proof/of/delivery/summary/api', 'OrderToCollectionController@pod_summary_api');
        Route::get('/proof/of/delivery/notuploaded/summary', 'OrderToCollectionController@not_uploaded_pod_list');
        Route::get('/proof/of/delivery/notuploaded/summary/api', 'OrderToCollectionController@not_uploaded_pod_list_api');

        Route::get('/summary1/jobdet/{data_id}', 'OrderToCollectionController@summry_jobdet');
        
        Route::get('/summary/list', 'OrderToCollectionController@summary_list');
        
        //Tax invoice form View
        Route::get('/taxinvoice', 'IO@taxinvoice');
        Route::get('/taxinvoice/view/{id}', 'IO@tax_invoice_view');
        Route::get('/taxinvoice/update/{id}', 'IO@taxinvoiceupdate');
        Route::post('/taxinvoice/update/db/{id}', 'IO@tax_invoice_update_db');
        
        Route::get('/deliverychallan','IO@delivery_challan');
        Route::post('/deliveryinsert','IO@delivery_insert');

        // Unit Of Measurement
        Route::get('/uom/data','MastersController@uom_data_api');
        Route::get('/uom/list','MastersController@uom_list');
        Route::get('/uom/del/{id}','MastersController@uom_delete');
        Route::get('/uom/update/{id}','MastersController@uom_Update_form');
        Route::get('/uom/create','MastersController@uom_insert_form');
        Route::post('/uom/ins/db','MastersController@uom_insert');
        Route::post('/uom/up/db/{id}','MastersController@uom_update');

        //Paymant_term
        Route::get('/paymentterm/data','PartyController@payment_term_data_api');
        Route::get('/paymentterm/list','PartyController@payment_term_list');
        Route::get('/paymentterm/del/{id}','PartyController@payment_term_delete');
        Route::get('/paymentterm/update/{id}','PartyController@payment_term_update_form');
        Route::get('/paymentterm/create','PartyController@payment_term_insert_form');
        Route::post('/paymentterm/ins/db','PartyController@payment_term_insert');
        Route::post('/paymentterm/up/db/{id}','PartyController@payment_term_update');

    
        // User Permission
        Route::get('/checkreqiredpermission/{for}/{id}','AdminController@check_required_permission');
        Route::get('/addreqiredpermission/{for}/{id}/{reason}','AdminController@add_required_permission');
        Route::get('/admin/auth/req/api','AdminController@admin_auth_req_api');
        Route::get('/admin/auth/req','AdminController@admin_auth_req');
        Route::get('/admin/auth/req/grant/{id}/{do}','AdminController@admin_auth_req_grant');
        Route::get('/admin/auth/req/grant/update/{id}/{do}/{for}','AdminController@admin_auth_req_grant_update');

        Route::get('/admin','AdminController@admin');
        Route::get('/admin/view/{id}','AdminController@admin_view');
        Route::get('/user/update/{id}','UserController@user_update');
        // user profile change option
        Route::get('/profile/update','UserController@user_profile_update');
        Route::post('/user/updateDb/{id}','UserController@user_update_db');
        Route::get('/admindata','AdminController@admindata');
        Route::get('/admin/permission/denied','AdminController@permission_denied');
        Route::get('/admin/permission/{id}','AdminController@permission');
        Route::post('/admin/setpermission','AdminController@setpermission');
        // Route::get('/printing/{jc_id}/{io_id}','IO@printing');
        // Route::post('/printingInsert','IO@printing_insert');
        Route::post('/taxInsert','IO@taxInsert');
        
        Route::get('/taxinvoice/cancel/{id}','IO@tax_invoice_cancel');
        Route::post('/taxinvoice/cancel/{id}','IO@tax_invoice_cancel_db');
        
        Route::get('/delivery/{id}/{type}', 'IO@delivery_details');
        Route::get('/io/{id}/{type}/{id_type}', 'IO@delivery_io_details');
        Route::get('/party/delivery/{id}/{type}/{id_type}', 'IO@party_delivery');
        Route::get('/party/details/byref/{name}', 'IO@get_party_by_reference');
        
        Route::get('/template/{io_id}','Template@internal_order');
        Route::get('/templateJC/{jc_id}','Template@job_card');
        Route::post('/templateTax/{tax_id}','Template@tax_invoice');
        Route::get('/taxcreation_template/{tax_id}/{check_box}','Template@tax_invoice_aftercreate');
        Route::get('/templateDelivery/{del_id}','Template@delivery_challan_pdf');

        Route::get('/taxdispatch','IO@tax_dispatch');
        Route::post('/taxdispatch/create','IO@createtax_dispatch');
        Route::get('/taxinvoicedispatch/view/{id}','IO@tax_invoice_dispatch_view');
        Route::get('/taxinvoicedispatch/update/{id}','IO@tax_invoice_dispatch_update');
        Route::post('/taxdispatch/update/{id}','IO@tax_dispatch_update');
        Route::get('/taxinvoicedispatch/api/{mode}','IO@tax_invoice_dispatch_api');
        Route::get('/taxinvoicedispatch/list/{mode}','IO@tax_invoice_dispatch_list');
        Route::get('/tax/dispatch/status', 'IO@tax_dispatch_status');

        Route::get('/taxinvoice/notdispatch/api','IO@taxinvoice_not_dispatch_api');
        Route::get('/taxinvoice/notdispatch/list','IO@taxinvoice_not_dispatch_list');
        
        Route::get('/tax/details/{id}','IO@tax_details');
        Route::get('/import/data/client', 'ImportController@import_client');
        Route::get('/import/data/consignee', 'ImportController@import_consignee');
        Route::get('/import/data/clientpo', 'ImportController@import_client_po');      
        Route::get('/import/data/internalorder', 'ImportController@import_io');      
        Route::get('/import/data/deliverychallan', 'ImportController@import_dc');      
        Route::get('/import/data/taxinvoice', 'ImportController@import_tax_invoice');      
        Route::get('/import/data/taxdispatch', 'ImportController@import_tax_dispatch');      
        // Route::get('/import/data/jobcard', 'ImportController@import_job_card');      
        Route::get('/import/data/hsn', 'ImportController@import_hsn');      
        Route::get('/import/data/uom', 'ImportController@import_uom');      
        Route::get('/import/data/paymentterm', 'ImportController@import_payment_term');      
        Route::get('/import/data/goodsinvoicedispatch', 'ImportController@import_goods_invoice_dispatch'); 
        // Route::get('/get/import/status/{id}', 'ImportController@get_status');      
        // Route::get('/get/import/statusid/{text}', 'ImportController@get_status_id');   
        Route::get('/import/data/stock', 'ImportController@import_stock');   
        Route::get('/import/data/task', 'ImportController@import_task');
        Route::get('/import/data/attendance', 'ImportController@import_attendance');


        Route::get('/download/format/stock', 'ImportController@download_stock_format');
        Route::get('/download/format/client', 'ImportController@download_client_format');
        Route::get('/download/format/task', 'ImportController@download_task_format');
        Route::get('/download/format/clientpo', 'ImportController@download_client_po_format');      
        Route::get('/download/format/internalorder', 'ImportController@download_io_format');      
        Route::get('/download/format/deliverychallan', 'ImportController@download_dc_format');      
        Route::get('/download/format/taxinvoice', 'ImportController@download_tax_invoice_format');      
        Route::get('/download/format/taxdispatch', 'ImportController@download_tax_dispatch_format');      
        // Route::get('/download/format/jobcard', 'ImportController@download_job_card_format');      
        Route::get('/download/format/hsn', 'ImportController@download_hsn_format');      
        Route::get('/download/format/uom', 'ImportController@download_uom_format');      
        Route::get('/download/format/paymentterm', 'ImportController@download_payment_term_format');      
        Route::get('/download/format/goodsinvoicedispatch', 'ImportController@download_goods_invoice_dispatch_format');      
        Route::get('/download/format/consignee', 'ImportController@download_consignee_format');
        Route::get('/download/format/consigneewithparty', 'ImportController@download_consignee_format_withparty');
        Route::get('/download/format/clientpo/consignee', 'ImportController@download_client_po_consignee_format'); 
        Route::get('/download/format/attendance', 'ImportController@download_attendance_format'); 
        

        Route::post('/import/attendance/db', 'ImportController@import_attendance_db');
        Route::post('/import/stock/db', 'ImportController@import_stock_db');       
        // Route::get('/download/format/{name}', 'ImportController@download_format');
        Route::post('/import/client/db', 'ImportController@import_client_db');      
        Route::post('/import/consigneewithparty/db', 'ImportController@import_consignee_db');      
        Route::post('/import/clientpo/db', 'ImportController@import_client_po_db');      
        Route::post('/import/internalorder/db', 'ImportController@import_io_db');      
        Route::post('/import/deliverychallan/db', 'ImportController@import_dc_db');      
        Route::post('/import/taxinvoice/db', 'ImportController@import_tax_invoice_db');      
        Route::post('/import/taxdispatch/db', 'ImportController@import_tax_dispatch_db');      
        // Route::post('/import/jobcard/db', 'ImportController@import_job_card_db');      
        Route::post('/import/hsn/db', 'ImportController@import_hsn_db');      
        Route::post('/import/uom/db', 'ImportController@import_uom_db');      
        Route::post('/import/paymentterm/db', 'ImportController@import_payment_term_db');      
        Route::post('/import/goodsinvoicedispatch/db', 'ImportController@import_goods_invoice_dispatch_db');      
        Route::post('/import/task/db', 'ImportController@import_task_db');      
        

        //---------------Tax Invoice Summary-------------------------------------------------
        Route::get('/taxinvoice/api','IO@tax_invoice_api');
        Route::get('/taxinvoice/print/api','IO@tax_invoice_print_api');
        Route::get('/taxinvoice/list','IO@tax_invoice_list');
        Route::get('/get/taxinvoice_ios/{id}','IO@get_tax_invoice_ios');
        Route::get('/taxinvoice/cancelled/list/api','IO@tax_invoice_cancelled_list_api');

        Route::get('/getadminpermission/{id}','AdminController@getadminpermission');
//----------------------------------------------GATE PASS------------------------------------------------------------------------------------------
Route::get('/gatepass/material','Gatepass@gatepass_material');
Route::post('/gatepass/material/insert','Gatepass@gatepass_material_db');
Route::get('/gatepass/material/list','Gatepass@material_list');  
Route::get('/gatepass/material/api','Gatepass@material_api');  
Route::get('/mode/challan/{id}','Gatepass@mode_challan');
Route::get('/gatepass/material/update/{id}','Gatepass@gatepass_material_update');
Route::post('/gatepass/material/update/form/{id}','Gatepass@gatepass_material_update_db');

Route::get('/mgatepass/template/{id}','Template@material_gatepass');
Route::get('/egatepass/template/{id}','Template@employee_gatepass');
Route::get('/rgatepass/template/{id}','Template@returnable_gatepass');

Route::get('/gatepass/employee','Gatepass@gatepass_employee');
Route::post('/gatepass/employee/insert','Gatepass@gatepass_employee_db');
Route::get('/gatepass/employee/list','Gatepass@employee_list');  
Route::get('/gatepass/employee/api','Gatepass@employee_api'); 
Route::get('/gatepass/employee/update/{id}','Gatepass@gatepass_employee_update');
Route::post('/gatepass/employee/update/form/{id}','Gatepass@gatepass_employee_update_db'); 

Route::get('/gatepass/returnable','Gatepass@gatepass_returnable');
Route::post('/gatepass/returnable/insert','Gatepass@gatepass_returnable_db');
Route::get('/gatepass/returnable/list','Gatepass@returnable_list');  
Route::get('/gatepass/returnable/api','Gatepass@returnable_api');  
Route::get('/gatepass/returnable/update/{id}','Gatepass@gatepass_returnable_update');
Route::post('/gatepass/returnable/update/form/{id}','Gatepass@gatepass_returnable_update_db');

//----------------------------------------------ASN Form-------------------------------------------------------------------------------------------
Route::get('/asn/create','AsnGrn@asn_form');
Route::get('/asn/create/{id}/{invoice}','AsnGrn@asn_form');
Route::get('/grn/create/{id}/{invoice}','AsnGrn@grn_form');
Route::post('/asn/insert','AsnGrn@asn_create');
Route::post('/asn/insert/{id}/{invoice}','AsnGrn@asn_create');
Route::post('/grn/insert/{id}/{invoice}','AsnGrn@grn_create');


Route::get('/asn/setting','AsnGrn@asn_client_create');
Route::post('/asn/setting/insert','AsnGrn@asn_client_insert');

Route::get('/asn/list','AsnGrn@asn_list');
Route::get('/asn/list/api','AsnGrn@asn_api');

Route::get('/party/asn/{id}', function ($id){
    $tax = \App\Model\Tax_Invoice::where('tax_invoice.party_id',$id)
    ->leftJoin('asn','asn.invoice_id','=','tax_invoice.id')
    ->where('asn.invoice_id','=',NULL)
    ->where('tax_invoice.is_active',1)->get([
           'tax_invoice.id',
           'tax_invoice.invoice_number'
    ]);
    return $tax;
  });
  //----------------------------------------------GRN Form-------------------------------------------------------------------------------------------
  Route::get('/grn/create','AsnGrn@grn_form');
  Route::get('/asngrnnotgenapi/{list_of}','AsnGrn@asn_not_created_api');
  Route::get('/asnnotcreated','AsnGrn@asn_not_created');
  Route::get('/grnnotcreated','AsnGrn@grn_not_created');
  

Route::post('/grn/insert','AsnGrn@grn_create');
Route::get('/grn/list','AsnGrn@grn_list');
Route::get('/grn/list/api','AsnGrn@grn_api');
Route::get('/party/grn/{id}', function ($id){
    $tax = \App\Model\Tax_Invoice::where('tax_invoice.party_id',$id)
   ->leftJoin('grn','grn.invoice_id','=','tax_invoice.id')
    ->where('grn.invoice_id','=',NULL)
    ->where('tax_invoice.is_active',1)->get([
           'tax_invoice.id',
           'grn.invoice_id as grn',
           'tax_invoice.invoice_number'
    ]);
    return $tax;
  });
 //-----------------------------WayBill----------------------------------------------------------------------------
 Route::get('/waybill/create/{delivery_id}/{text}/{gst}/{date}/{amount}/{refer}/{pointer}','MastersController@waybill_create');
 Route::get('/waybill/list','MastersController@waybill_list');
 Route::get('/report/waybillnotgen/list','ReportController@waybill_not_generated_list');
 Route::get('/waybill/create/data','MastersController@waybill_data');
 Route::get('/waybill/api','MastersController@waybill_api');
 Route::get('/report/waybillnotgen/api/{type}','ReportController@waybill_not_generated_api');
 Route::post('/waybill/createDb/{delivery_id}/{text}/{party}','MastersController@waybill_createDb');

 Route::get('/party/tax/{id}', function ($id){
    $today = \Carbon\Carbon::now()->toDateString();

    $party = \App\Model\Party::where('party.gst',$id)->get('id')->toArray();
    
    $tax=\App\Model\Tax_Invoice::whereIn('tax_invoice.party_id',$party)
    ->where('tax_invoice.is_active',1)->where('tax_invoice.waybill_status','!=',2)->get([
           'tax_invoice.id',
           'tax_invoice.invoice_number',
           'tax_invoice.total_amount'
    ]);
    return $tax;
  });
  Route::get('/party/challan/{id}', function ($id){
    $party = \App\Model\Party::where('party.gst',$id)->get('id')->toArray();

    $challan = \App\Model\Delivery_challan::whereIn('delivery_challan.party_id',$party)  
    ->where('delivery_challan.is_active',1)->where('delivery_challan.waybill_status','!=',2)->get([
           'delivery_challan.id',
           'delivery_challan.challan_number',
           'delivery_challan.total_amount'
    ]);
    return $challan;
  });
  
        

        Route::post('/export/employee','ExportController@Employee');
        Route::post('/export/consignee','ExportController@consignee');
        Route::post('/export/hsn','ExportController@hsn');
        Route::post('/export/uom','ExportController@uom');
        Route::post('/export/paymentterm','ExportController@paymentTerm');
        Route::post('/export/goodsinvoicedispatch','ExportController@goodsDispatchProfile');
        Route::post('/export/client','ExportController@party');
        Route::post('/export/internalorder','ExportController@internalOrder');
        Route::post('/export/deliverychallan','ExportController@deliveryChallan');
        Route::post('/export/taxdispatch','ExportController@taxdispatch');
        Route::post('/export/taxnotdispatch','ExportController@taxnotdispatch');
        Route::post('/export/taxinvoice','ExportController@taxinvoice');
        Route::post('/export/jobcard','ExportController@jobcard');
        Route::post('/export/clientpo','ExportController@clientpo');
        Route::post('/export/employeegatepass','ExportController@employeegatepass');
        Route::post('/export/returnablegatepass','ExportController@returnablegatepass');
        Route::post('/export/materialgatepass','ExportController@materialgatepass');
        Route::post('/export/materialinward','ExportController@materialinward');
        Route::post('/export/materialoutward','ExportController@materialoutward');
        Route::post('/export/internaldc','ExportController@internaldc');
        Route::post('/export/pendingclientpo','ExportController@pendingclientpo');
        Route::post('/export/dispatchvsbilling','ExportController@dispatchvsbilling');
        Route::post('/export/ordervsbilling','ExportController@ordervsbilling');
        Route::post('/export/businesstracker','ExportController@businesstracker');
        Route::post('/export/saletracker','ExportController@saletracker');
        Route::post('/export/pendingjobcard','ExportController@pendingjobcard');
        Route::post('/export/pendingtaxinvoice','ExportController@pendingtaxinvoice');
        Route::post('/export/pendingtaxdispatch','ExportController@pendingtaxdispatch');
        Route::post('/export/pendingfdispatch','ExportController@pendingfdispatch');

        Route::post('/export/noworkdoneio','ExportController@noworkdoneio');
        Route::post('/export/noworkdoneiofinancial','ExportController@noworkdoneiofinancial');
        Route::get('/export/data/noworkdoneio','ExportController@export_data_noworkdoneio');
        Route::get('/export/data/noworkdoneiofinancial','ExportController@export_data_noworkdoneiofinancial');
        
        Route::post('/export/ksamplingandfocorder','ExportController@ksamplingandfocorder');
        Route::get('/export/data/ksamplingandfocorder','ExportController@export_data_ksamplingandfocorder');

        Route::post('/export/purchasereq','ExportController@purchase_requisition');
        Route::get('/export/data/purchase/req','ExportController@export_data_purchase_requisition');

        Route::post('/export/purchaseindent','ExportController@purchase_indent');
        Route::get('/export/data/purchase/indent','ExportController@export_data_purchase_indent');

        Route::post('/export/purchaseorder','ExportController@purchase_order');
        Route::post('/export/proofofdelivery','ExportController@proofofdelivery');
        Route::get('/export/data/proofofdelivery','ExportController@export_data_proofofdelivery');

        Route::post('/export/proofofdeliverynot','ExportController@proofofdeliverynot');
        Route::get('/export/data/proofofdeliverynot','ExportController@export_data_proofofdeliverynot');

        Route::post('/export/tobeclosedios','ExportController@tobeclosedios');
        Route::get('/export/data/tobeclosedios','ExportController@export_data_tobeclosedios');


        Route::get('export/data/purchase/order','ExportController@export_data_purchase_order');

        Route::post('export/purchasereturn','ExportController@purchase_return');
        Route::get('/export/data/purchase/return','ExportController@export_data_purchase_return');
        Route::get('/export/data/pendingtaxdispatch','ExportController@export_data_pendingtaxdispatch');

        Route::get('/export/data/pendingtaxdispatch/financial','ExportController@export_data_pendingfdispatch');
        Route::get('/export/data/businesstracker','ExportController@export_data_businesstracker');
        Route::get('/export/data/pendingtaxinvoice','ExportController@export_data_pendingtaxinvoice');
        Route::get('/export/data/pendingjobcard','ExportController@export_data_pendingjobcard');
        Route::get('/export/data/saletracker','ExportController@export_data_saletracker');
        Route::get('/export/data/pendingclientpo','ExportController@export_data_pendingclientpo');
        Route::get('/export/data/dispatchvsbilling','ExportController@export_data_dispatchvsbilling');
        Route::get('/export/data/ordervsbilling','ExportController@export_data_ordervsbilling');
        Route::get('/export/data/consignee','ExportController@export_data_consignee');
        Route::get('/export/data/hsn','ExportController@export_data_hsn');
        Route::get('/export/data/uom','ExportController@export_data_uom');
        Route::get('/export/data/paymentterm','ExportController@export_data_paymentTerm');
        Route::get('/export/data/goodsinvoicedispatch','ExportController@export_data_goodsDispatchProfile');
        Route::get('/export/data/client','ExportController@export_data_party');
        Route::get('export/data/internalorder','ExportController@export_data_internalOrder');
        Route::get('/export/data/employee/gatepass','ExportController@export_data_emp_gatepass');
        Route::get('/export/data/returnable/gatepass','ExportController@export_data_ret_gatepass');
        Route::get('/export/data/material/gatepass','ExportController@export_data_mat_gatepass');

        Route::get('/export/data/material/inward','ExportController@export_data_inward');
        Route::get('/export/data/material/outward','ExportController@export_data_outward');
        Route::get('/export/data/internaldc','ExportController@export_data_internaldc');

        Route::get('/export/data/deliverychallan','ExportController@export_data_deliverychallan');
        Route::get('/export/data/taxdispatch','ExportController@export_data_taxdispatch');
        Route::get('/export/data/taxnotdispatch','ExportController@export_data_taxnotdispatch');
        Route::get('/export/data/taxinvoice','ExportController@export_data_taxinvoice');
        Route::get('/export/data/jobcard','ExportController@export_data_jobcard');
        Route::get('/export/data/clientpo','ExportController@export_data_clientpo');

        Route::get('/export/data/employee','ExportController@export_data_employee');
        Route::get('/export/data/pfregister','ExportController@export_data_pfregister');
        Route::post('/export/pfregister','ExportController@pfregister');

        Route::get('/export/data/esiregister','ExportController@export_data_esiregister');
        Route::post('/export/esiregister','ExportController@esiregister');

        Route::get('/export/data/leaveregister','ExportController@export_data_leaveregister');
        Route::post('/export/leaveregister','ExportController@leaveregister');

        Route::get('/export/data/salary/a','ExportController@export_data_salaryA');
        Route::post('/export/salaryA_export','ExportController@salaryA_export');

        Route::get('/export/data/salary/b','ExportController@export_data_salaryB');
        Route::post('/export/salaryB_export','ExportController@salaryB_export');

        Route::get('/export/data/salary/c','ExportController@export_data_salaryC');
        Route::post('/export/salaryC_export','ExportController@salaryC_export');

        Route::get('/export/data/salary/register','ExportController@export_data_salaryRegister');
        Route::post('/export/salaryRegister','ExportController@salaryRegister');

        
        //Route::post('export/','ExportController@');
        //Route::post('export/','ExportController@');
        //Route::post('export/','ExportController@');
        
        // Route::get('export/data/{table}','ExportController@export_data');
        Route::get('/getTableData/{table}/{cloumn}','ExportController@getTableData');

//---------------------------------------------------------------Purchase Module------------------------------------------------------------------------
        //purchase requitision
        Route::get('/purchase/indent/create','purchase\PurchaseAll@purchase_req');  
        Route::post('/purchase/indent/create','purchase\PurchaseAll@purchase_reqDb') ; 
        Route::get('/purchase/indent/list','purchase\PurchaseAll@purchase_req_list');
        Route::get('/purchase/indent/list/api/{type}','purchase\PurchaseAll@purchase_req_list_api'); 
        Route::get('/purchase/template/indent/{id}','purchase\PurchaseTemplate@purchase_req');
        Route::get('/purchase/indent/update/{id}','purchase\PurchaseAll@purchase_req_update');  
        Route::post('/purchase/indent/update/{id}','purchase\PurchaseAll@purchase_req_updateDb') ; 

        //purchase indent
        Route::get('/purchase/requisition/create','purchase\PurchaseAll@purchase_indent');  
        Route::post('/purchase/requisition/create','purchase\PurchaseAll@purchase_indentDb') ;  
        Route::get('/purchase/requisition/list','purchase\PurchaseAll@pur_indent_list'); 
        Route::get('/purchase/requisition/list/api/{type}','purchase\PurchaseAll@pur_indent_list_api');
         Route::get('/purchase/indent/get_item_name/api/{type}/{ty}','purchase\PurchaseAll@get_item_name'); 
         Route::get('/purchase/template/req/{id}','purchase\PurchaseTemplate@purchase_indent');
         Route::get('/purchase/requisition/update/{id}','purchase\PurchaseAll@purchase_indent_update');  
         Route::post('/purchase/requisition/update/{id}','purchase\PurchaseAll@purchase_indent_updateDb') ;

         //purchase indent
        Route::get('/purchase/po/create','purchase\PurchaseAll@purchase_po');  
        Route::post('/purchase/po/create','purchase\PurchaseAll@purchase_poDb') ;  
        Route::get('/purchase/po/list','purchase\PurchaseAll@pur_po_list'); 
        Route::get('/purchase/po/list/api/{type}','purchase\PurchaseAll@pur_po_list_api'); 

        //purchase order
        
        Route::get('/purchase/fms','purchase\PurchaseAll@fms'); 
        Route::post('/purchase/fms/api','purchase\PurchaseAll@fms_api'); 

        //Report
        Route::get('/report/ksampling/foc/order','ReportController@Ksamp_and_foc_report'); 
        Route::get('/report/ksampling/foc/order/api','ReportController@Ksamp_and_foc_report_api');

        Route::get('/report/noworkdone/io/list','ReportController@noworkdone_io_report'); 
        Route::get('/report/noworkdone/io/list/api','ReportController@noworkdone_io_report_api');

        Route::get('/report/noworkdone/io/financial/list','ReportController@noworkdone_financial_report'); 
        Route::get('/report/noworkdone/io/financial/list/api','ReportController@noworkdone_financial_report_api');

        Route::get('/report/pending/dispatchorder/financial/list','ReportController@pending_dispatchorder_financial'); 
        Route::get('/report/pending/dispatchorder/financial/list/api','ReportController@pending_dispatchorder_financial_api');

        Route::get('/report/pending/client/po','ReportController@pending_po_list'); 
        Route::get('/report/pending/client/po/list/api','ReportController@pending_po_list_api'); 
        Route::get('/report/pending/job/card','ReportController@pending_jobcard_list'); 
        Route::get('/report/pending/job/card/list/api','ReportController@pending_jobcard_list_api');
        Route::get('/report/pending/dispatch/order','ReportController@pending_dispatchorder_list'); 
        Route::get('/report/pending/dispatch/order/list/api','ReportController@pending_dispatchorder_list_api'); 
        Route::get('/report/pending/order','ReportController@pending_orders_list');
        Route::get('/report/pending/order/list/api','ReportController@pending_orders_list_api');
        Route::get('/report/pending/dispatchVsbilling/','ReportController@dispatch_vs_billing_report');
        Route::get('/report/pending/dispatchVsbilling/api','ReportController@dispatch_vs_billing_report_api');
        Route::get('/report/sale/tracker','ReportController@sale_tracker_list');
        Route::get('/report/sale/tracker/list/api','ReportController@sale_tracker_list_api');
        Route::get('/report/sale/tracker/date/list/api/{s_date}_{e_date}','ReportController@sale_tracker_datewise_list_api');
        Route::get('/report/client/po/tracker','ReportController@client_po_tracker');
        Route::get('/report/client/po/tracker/api/{ref}/{po}','ReportController@client_po_tracker_api');
        Route::get('/report/client/po/ref/tracker/api/{ref}','ReportController@client_po_ref_tracker_api');
        Route::get('/report/fetch/client/po/api/{ref_id}','ReportController@fetch_client_po');


        Route::get('/report/ordervsbilling','ReportController@Order_vs_billing');
        Route::get('/report/ordervsbilling/api','ReportController@Order_vs_billing_api');

        Route::get('/report/fetch/client/api/{ref_id}','ReportController@fetch_client');
        Route::get('/report/businesstracker','ReportController@business_tracker');
        Route::get('/report/businesstracker/api/{party_id}','ReportController@business_tracker_api');
        Route::get('/report/businesstracker/ref/api/{party_id}','ReportController@business_tracker_ref_api');
       

        Route::get('/purchase/order/create','purchase\PurchaseAll@purchase_order'); 
        Route::post('/purchase/order/create','purchase\PurchaseAll@purchase_orderDb'); 
        Route::get('/purchase/order/api/get_sub_category/{type}','purchase\PurchaseAll@get_sub_category');
        Route::get('/purchase/order/api/get_item_name/{type}/{ty}','purchase\PurchaseAll@get_item_name_ord');
        Route::get('/purchase/template/po/{id}','purchase\PurchaseTemplate@purchase_po');
        Route::post('/purchase/order/approval/{id}','purchase\PurchaseAll@purchase_order_approval'); 

        //TO BE CLOSED IOS
            Route::get('/collection/report','ReportController@to_be_closed_ios_report');  
            Route::get('/collection/report/closedios/api','ReportController@to_be_closed_ios_report_api');  

            Route::get('/collection/fms','CollectionController@fms');
            Route::post('/collection/fms/api','CollectionController@fms_api');

            Route::get('/collection/details/{party}','CollectionController@details');
            Route::get('/collection/details/api/{party}','CollectionController@details_api');

        //purchase return request

        Route::get('/purchase/return/create','purchase\PurchaseAll@purchase_return'); 
        Route::post('/purchase/return/create','purchase\PurchaseAll@purchase_return_db');

        Route::get('/purchase/return/summary','purchase\PurchaseAll@purchase_return_list'); 
        Route::get('/purchase/return/summary/api','purchase\PurchaseAll@purchase_return_list_api'); 

        Route::get('/purchase/template/return/{id}','purchase\PurchaseTemplate@purchase_return');

        Route::get('/purchase/return/update/{id}','purchase\PurchaseAll@purchase_return_update'); 
        Route::post('/purchase/return/update/{id}','purchase\PurchaseAll@purchase_return_updatedb');

        

        //purcase summary
        Route::get('/purchase/order/list','purchase\PurchaseAll@pur_order_list'); 
        Route::get('/purchase/order/list/api/{type}','purchase\PurchaseAll@pur_order_list_api');
        Route::get('/purchase/order/update/{type}','purchase\PurchaseAll@pur_order_update');
        Route::post('/purchase/order/update/{type}','purchase\PurchaseAll@purchase_order_updateDb');
        Route::get('/purchase/order/view/{type}','purchase\PurchaseAll@pur_order_list_view');

        //purchase grn

        Route::get('/purchase/grn/create','purchase\PurchaseAll@purchase_grn'); 
        Route::post('/purchase/grn/create','purchase\PurchaseAll@purchase_grn_db');
        Route::get('/paper/item/{name}','purchase\PurchaseAll@paper_item');
        Route::get('/ink/item/{name}','purchase\PurchaseAll@ink_item');
        Route::get('/plate/item/{name}','purchase\PurchaseAll@plate_item');
        Route::get('/misc/item/{name}','purchase\PurchaseAll@misc_item');

        Route::get('/purchase/grn/summary','purchase\PurchaseAll@purchase_grn_list'); 
        Route::get('/purchase/grn/list/api','purchase\PurchaseAll@purchase_grn_list_api'); 
        Route::get('/summary/mode/{data_id}', 'purchase\PurchaseAll@summry_mode');
        Route::get('/summary/item/{data_id}', 'purchase\PurchaseAll@summry_item');

        Route::get('/file-upload/download/{file}', 'FilesController@download');
        Route::get('/file-upload/download1/{file}', 'FilesController@download1');

        Route::get('/purchase/grn/update/{id}','purchase\PurchaseAll@purchase_grn_update'); 
        Route::post('/purchase/grn/update/{id}','purchase\PurchaseAll@purchase_grn_updatedb');
        Route::get('/purchase/grn/file/delete/{id}','purchase\PurchaseAll@purchase_grn_file_delete');

//-----------------------------------------------------Vendor Master------------------------------------------------------------------

        Route::get('/vendor/create','vendor\Vendor@vendor_create');  
        Route::post('/vendor/create','vendor\Vendor@vendor_createDb') ; 

        Route::get('/vendor/summary','vendor\Vendor@vendor_list');  
        Route::get('/vendor/summary/api','vendor\Vendor@vendor_list_api') ; 
         
        Route::get('/vendor/list/edit/{id}','vendor\Vendor@vendor_update');  
        Route::post('/vendor/list/updateDb/{id}','vendor\Vendor@vendor_updateDb') ;
//-----------------------------------------------------Stock Master--------------------------------------------------------------------
        Route::get('/stock/create','Stock\Stock@stock_create');  
        Route::post('/stock/create','Stock\Stock@stock_createDb') ;
        Route::get('/stock/summary','Stock\Stock@stock_list');  
        Route::get('/stock/summary/api/{type}','Stock\Stock@stock_list_api') ; 
        Route::get('/stock/update/{id}','Stock\Stock@stock_update');  
        Route::post('/stock/update/{id}','Stock\Stock@stock_updateDb') ; 

        //sub category master

        Route::get('/stock/subcat/create','Stock\Stock@sub_cat');  
        Route::post('/stock/subcat/create','Stock\Stock@sub_catDb') ; 

        Route::get('/stock/subcat/update/{id}','Stock\Stock@sub_cat_update');  
        Route::post('/stock/subcat/update/{id}','Stock\Stock@sub_cat_updateDb') ; 

        Route::get('/stock/subcat/list','Stock\Stock@sub_cat_list');  
        Route::get('/stock/subcat/list/api','Stock\Stock@sub_cat_api') ; 

//----------------------------------------------Plate master--------------------------------------------------------------------------

        Route::get('/master/create/plate/size','MastersController@create_plate_size');
        Route::post('/master/create/plate/size','MastersController@create_plate_sizeDB');
        Route::get('/master/plate/size/list','MastersController@plate_size_summary');
        Route::get('/master/plate/size/list/api','MastersController@plate_size_summaryApi');

//----------------------------------------------Machine master--------------------------------------------------------------------------

        Route::get('/master/machine/create','MastersController@create_machine');
        Route::post('/master/machine/create','MastersController@create_machineDB');
        Route::get('/master/machine/update/{id}','MastersController@update_machine');
        Route::post('/master/machine/update/{id}','MastersController@update_machineDB');
        Route::get('/master/machine/delete/{id}','MastersController@machine_delete');
        Route::get('/master/machine/list','MastersController@machine_summary');
        Route::get('/master/machine/list/api','MastersController@machine_summaryApi');

//-----------------------------------------------------Material in-warding and out-warding--------------------------------------------       
        
        Route::get('/material/inwarding/create','Utilities\UtilitiesAll@material_inward_create');
        Route::post('/material/inwarding/create','Utilities\UtilitiesAll@material_inward_createDb'); 
        Route::get('/material/inwarding/list','Utilities\UtilitiesAll@material_inward_list');
        Route::get('/material/inwarding/list/api/{type}','Utilities\UtilitiesAll@material_inward_list_api');
        Route::get('/summary/doc/{id}','Utilities\UtilitiesAll@summary_doc');
        Route::get('/material/inwarding/update/{id}','Utilities\UtilitiesAll@material_inward_update');
        Route::post('/material/inwarding/update/{id}','Utilities\UtilitiesAll@material_inward_updateDb'); 
        
        Route::get('/material/outwarding/create','Utilities\UtilitiesAll@material_outward_create'); 
        Route::post('/material/outwarding/create','Utilities\UtilitiesAll@material_outward_createDb');  
        Route::get('/material/outwarding/list','Utilities\UtilitiesAll@material_outward_list');
        Route::get('/material/outwarding/list/api','Utilities\UtilitiesAll@material_outward_list_api');
        Route::get('/material/outwarding/update/{id}','Utilities\UtilitiesAll@material_outward_update');
        Route::post('/material/outwarding/update/{id}','Utilities\UtilitiesAll@material_outward_updateDb'); 

//-----------------------------------------------------Material in-warding and out-warding--------------------------------------------       
        
        Route::get('/internal/deliverychallan/create','Utilities\UtilitiesAll@internal_dc_create');
        Route::post('/internal/deliverychallan/create','Utilities\UtilitiesAll@internal_dc_createDb');     
        
        Route::get('/internal/deliverychallan/update/{id}','Utilities\UtilitiesAll@internal_dc_update');
        Route::post('/internal/deliverychallan/update/{id}','Utilities\UtilitiesAll@internal_dc_updateDb');   
        Route::get('/internal/deliverychallan/template/{id}','Utilities\UtilitiesAll@internal_dc_template'); 
        
        Route::get('/internal/deliverychallan/list','Utilities\UtilitiesAll@internal_dc_list');
        Route::get('/internal/deliverychallan/list/api','Utilities\UtilitiesAll@internal_dc_list_api');
       
//---------------------------------------------------------------Accounting Module-------------------------------------------------------------------------------------------
        
        //accounting home dashboard
        Route::get('/accounting/dashboard','accounting\MasterController@dashboard');  
        Route::get('/accounting','accounting\MasterController@dashboard');  
        
        //accounting company
        Route::get('/accounting/company/create','accounting\MasterController@create_company');
        Route::post('/accounting/company/create','accounting\MasterController@create_company_db');
        Route::get('/accounting/select/company','accounting\MasterController@selectcompany');  
        Route::post('/accounting/select/company','accounting\MasterController@selectcompany_session');  
        
        //accounting group 

        Route::get('/accounting/group/create','accounting\MasterController@create_group');  
        Route::post('/accounting/group/create','accounting\MasterController@create_group_db');  

        Route::get('/accounting/group/list','accounting\MasterController@group_list');  
        Route::get('/accounting/group/list/api','accounting\MasterController@group_list_api');  

        Route::get('/accounting/group/view/{id}','accounting\MasterController@group_view');  
        Route::get('/accounting/group/update/{id}','accounting\MasterController@group_update');  
        Route::post('/accounting/group/update/{id}','accounting\MasterController@group_update_db');  

        
        //accounting ledger

        Route::get('/accounting/ledger/create','accounting\MasterController@create_ledger');  
        Route::post('/accounting/ledger/create','accounting\MasterController@create_ledger_db');  
        Route::get('/accounting/ledger/list','accounting\MasterController@ledger_list');  
        Route::get('/accounting/ledger/list/api','accounting\MasterController@ledger_list_api');  
        Route::get('/accounting/ledger/view/{id}','accounting\MasterController@ledger_view');  
        Route::get('/accounting/ledger/update/{id}','accounting\MasterController@ledger_update');  
        Route::post('/accounting/ledger/update/{id}','accounting\MasterController@ledger_update_db');  
        //apis
            //list-api
            Route::get('/accounting/ledger/get/table/element/{id}','accounting\MasterController@legder_avail_get_table_date');  
            Route::get('/accounting/ledger/avail/children/{value}/{id}','accounting\MasterController@legder_avail_children_api');  
            Route::get('/accounting/ledger/avail/modal/children/{value}/{id}','accounting\MasterController@legder_avail_modal_children_api');  
            Route::get('/accounting/ledger/avail/modal/newline/children/{value}/{id}','accounting\MasterController@legder_avail_modal_newline_children_api');  
                
            //create-api/update-api
            Route::get('/accounting/getledgerform/{id}','accounting\MasterController@legder_form_by_Group');  


        //accounting cost categories
        Route::get('/accounting/costcategory/create','accounting\MasterController@create_cost_category');
        Route::post('/accounting/costcategory/create','accounting\MasterController@create_cost_category_db');
        Route::get('/accounting/costcategory/list','accounting\MasterController@costcategory_list');  
        Route::get('/accounting/costcategory/list/api','accounting\MasterController@costcategory_list_api');  
        Route::get('/accounting/costcategory/view/{id}','accounting\MasterController@cost_category_view');  
        Route::get('/accounting/costcategory/update/{id}','accounting\MasterController@costcategory_update');  
        Route::post('/accounting/costcategory/update/{id}','accounting\MasterController@costcategory_update_db');  



        //accountin Cost Center 
        Route::get('/accounting/costcenter/create','accounting\MasterController@create_cost_center');
        Route::post('/accounting/costcenter/create','accounting\MasterController@create_cost_center_db');
        Route::get('/accounting/costcenter/list','accounting\MasterController@cost_center_list');
        Route::get('/accounting/costcenter/list/api','accounting\MasterController@cost_center_list_api');
        Route::get('/accounting/costcenter/view/{id}','accounting\MasterController@cost_center_view');
        Route::get('/accounting/costcenter/update/{id}','accounting\MasterController@cost_center_update');
        Route::post('/accounting/costcenter/update/{id}','accounting\MasterController@cost_center_update_db');


        //accounting budgets
        Route::get('/accounting/budget/create','accounting\MasterController@create_budget');
        Route::post('/accounting/budget/create','accounting\MasterController@create_budget_db');


        //accounting scenario
        Route::get('/accounting/scenario/create','accounting\MasterController@create_scenario');
        Route::post('/accounting/scenario/create','accounting\MasterController@create_scenario_db');
        Route::get('/accounting/scenario/list','accounting\MasterController@scenario_list');
        Route::get('/accounting/scenario/list/api','accounting\MasterController@scenario_list_api');
        Route::get('/accounting/scenario/view/{id}','accounting\MasterController@scenario_view');
        Route::get('/accounting/scenario/update/{id}','accounting\MasterController@scenario_update');
        Route::post('/accounting/scenario/update/{id}','accounting\MasterController@scenario_update_db');

        //accounting anonymous task
        Route::get('/accounting/anonymous/getledgerview_metaid','accounting\MasterController@get_ledger_view_metaid');


        //accounting Currencies
        Route::get('/accounting/currency/create','accounting\MasterController@create_currency');
        Route::post('/accounting/currency/create','accounting\MasterController@create_currency_db');
        Route::get('/accounting/currency/list','accounting\MasterController@currency_list');
        Route::get('/accounting/currency/list/api','accounting\MasterController@currency_list_api');
        Route::get('/accounting/currency/view/{id}','accounting\MasterController@currency_view');
        Route::get('/accounting/currency/update/{id}','accounting\MasterController@currency_update');
        Route::post('/accounting/currency/update/{id}','accounting\MasterController@currency_update_db');


        //accounting voucher-type
        
        Route::get('/accounting/voucher/type/get/table/element/{id}','accounting\MasterController@voucher_type_avail_get_table_date');  
        Route::get('/accounting/vouchertype/create','accounting\MasterController@create_voucher_type');  
        Route::post('/accounting/vouchertype/create','accounting\MasterController@create_voucher_type_db');  
        Route::get('/accounting/vouchertype/list','accounting\MasterController@voucher_type_list');  
        Route::get('/accounting/vouchertype/list/api','accounting\MasterController@voucher_type_list_api');  
        Route::get('/accounting/vouchertype/avail/{id}','accounting\MasterController@voucher_type_avail_api');
        Route::get('/accounting/vouchertype/avail/children/{value}/{id}','accounting\MasterController@voucher_type_avail_children_api');
        Route::get('/accounting/vouchertype/view/{id}','accounting\MasterController@voucher_type_view');  
        Route::get('/accounting/vouchertype/update/{id}','accounting\MasterController@voucher_type_update');  
        Route::post('/accounting/vouchertype/update/{id}','accounting\MasterController@voucher_type_update_db');  

        // accounting date
        Route::get('/accounting/compdate/change','accounting\MasterController@change_date');  

        //accounting voucher-entry
        Route::get('/accounting/voucher/entry','accounting\MasterController@voucher_entry');  
        Route::post('/accounting/voucher/entry','accounting\MasterController@voucher_entry_db');  
        Route::get('/accounting/voucher/entry/view/{data}','accounting\MasterController@get_vouccher_view');
        Route::get('/accounting/voucher/update/{id}','accounting\MasterController@voucher_update');  
        Route::post('/accounting/voucher/update/{id}','accounting\MasterController@voucher_update_form_db');  
        
            //api
            Route::get('/accounting/voucher/update/form/{id}/{type}/{form_type}','accounting\MasterController@get_voucher_update_form');
            Route::get('/accounting/voucher/form/{type}/{form_type}','accounting\MasterController@get_vouccher_form');  
            Route::get('/accounting/voucher/Number/{type}','accounting\MasterController@get_voucher_entry_number');  
            Route::get('/accounting/voucher/checkBillWise/{id}','accounting\MasterController@check_bill_wise_of_ledger');  
            Route::get('/accounting/voucher/checkCostCenter/{id}','accounting\MasterController@check_cost_center_of_ledger');  
            Route::get('/accounting/voucher/costcentersbycategory/{id}/{not_req_id}','accounting\MasterController@get_cost_center_of_cost_category');  
            Route::get('/accounting/voucher/checkbankledger/{id}','accounting\MasterController@check_bank_ledger');  
            Route::get('/accounting/item/data/{id}','accounting\MasterController@get_item_detail');

            //    Route::post('/insert/purchase_req','Purchase@purchase_reqDb') ; 

        // Display

            // Day Book
            Route::get('/accounting/display/daybook','accounting\MasterController@daybook');  
            Route::get('/accounting/display/daybook/api','accounting\MasterController@daybook_api');  




//----------------------------------------------------------Employee---------------------------------------------------------------------------------------------------
Route::get('/employee/profile/create/','Employee\EmployeeAll@create_employee');  
Route::post('/employee/profile/create/','Employee\EmployeeAll@create_employeeDb');  

//EMPLOYEE VIEW
Route::get('/employee/profile/view/{id}','Employee\EmployeeAll@view_employee');  

//EMPLOYEE UPDATE
Route::get('/employee/profile/update/{id}','Employee\EmployeeAll@update_employee');  
Route::post('/employee/profile/update/{id}','Employee\EmployeeAll@update_employeeDb');  


//PFESI
Route::get('/employee/pfesi/update/{id}','Employee\EmployeeAll@update_pfesi');  
Route::post('/employee/pfesi/update/{id}','Employee\EmployeeAll@update_pfesiDb');

Route::get('/employee/pfesi/report','Employee\EmployeeAll@not_pfesi_report');  
Route::get('/employee/pfesi/report/both/api','Employee\EmployeeAll@not_pfesi_report_api'); 

Route::get('/employee/pfesi/report/pf','Employee\EmployeeAll@in_pf_report');  
Route::get('/employee/pfesi/report/pf/api','Employee\EmployeeAll@in_pf_report_api');  
 
Route::get('/employee/pfesi/report/esi/api','Employee\EmployeeAll@in_esi_report_api');  
Route::get('/employee/pfesi/report/esi','Employee\EmployeeAll@in_esi_report');  

Route::get('/employee/pfesi/report/notesi','Employee\EmployeeAll@not_esi_report');  
Route::get('/employee/pfesi/report/notesi/api','Employee\EmployeeAll@not_esi_report_api');  

Route::get('/employee/pfesi/report/notpf','Employee\EmployeeAll@not_pf_report');  
Route::get('/employee/pfesi/report/notpf/api','Employee\EmployeeAll@not_pf_report_api');

//F&F Summery
Route::get('/employee/fnf/settlement/list','Employee\EmployeeAll@employee_fnf_settlement');  
Route::get('/employee/fnf/settlement/list/api','Employee\EmployeeAll@employee_fnf_settlement_api');

Route::get('/employee/fnf/settlement/details/{emp}', 'Employee\EmployeeAll@employee_fnf_settlement_details'); 


//EMPLOYEE List
Route::get('/employee/profile/list','Employee\EmployeeAll@employee_list');  
Route::get('/employee/profile/list/api','Employee\EmployeeAll@employee_list_api'); 

//BANK
Route::get('/employee/bank/update/{id}','Employee\EmployeeAll@update_bank');  
Route::post('/employee/bank/update/{id}','Employee\EmployeeAll@update_bankDb');  

//Relieving Details
Route::get('/employee/relieving/update/{id}','Employee\EmployeeAll@update_relieving');  
Route::post('/employee/relieving/update/{id}','Employee\EmployeeAll@update_relievingDB');
Route::get('/emp/relieving/pdf/template/{id}/{name}','Template@Relieving_letters_format');

//Employee Category
Route::get('/employee/category/update/{id}','Employee\EmployeeAll@update_emp_category');  
Route::post('/employee/category/update/{id}','Employee\EmployeeAll@update_emp_categoryDB'); 
Route::get('/emp/download/template/pdf/{id}/{name}','Template@Appointment_letter_format');

Route::get('/emp/category/report/all','Employee\EmployeeAll@employee_category_report');
Route::get('/emp/category/report/all/api','Employee\EmployeeAll@empl_category_report_api');


//Document Upload
Route::get('/employee/document/update/{id}','Employee\EmployeeAll@update_document');  
Route::post('/employee/document/update/{id}','Employee\EmployeeAll@update_documentDB'); 
 
//Appointment Format
Route::get('/employee/appointment/format/','Employee\EmployeeAll@appointment_format');  
Route::post('/employee/appointment/format/','Employee\EmployeeAll@appointment_format_DB'); 

//Birthday and Anniversary List
Route::get('/employee/bday/anniversary/list','Employee\EmployeeAll@bday_anniversaryList');  
Route::get('/employee/bday/list/api','Employee\EmployeeAll@bday_List_api'); 
Route::get('/employee/anniversary/list/api','Employee\EmployeeAll@anniversary_List_api'); 

//gone and working employee
Route::get('/employee/left/list/api','Employee\EmployeeAll@employee_left_api'); 
Route::get('/employee/working/list/api','Employee\EmployeeAll@employee_working_api'); 

//one year completed report
Route::get('/employee/year/completed/report','Employee\EmployeeAll@employee_compl_oneyear'); 
Route::get('/employee/year/completed/report/api','Employee\EmployeeAll@employee_compl_oneyear_api'); 

//six month emp report
// Route::get('/employee/sixmonth/report/','Employee\EmployeeAll@emp_compl_sixmonth'); 
// Route::get('/employee/sixmonth/report/api','Employee\EmployeeAll@emp_compl_sixmonth_api'); 

//no dues 
Route::get('/employee/nodues/print','Employee\EmployeeAll@nodues_print');
Route::get('/employee/nodues/upload','Employee\EmployeeAll@nodues_upload');
Route::post('/employee/nodues/upload','Employee\EmployeeAll@nodues_upload_db');
Route::get('/emp/download/template/nodues','Template@nodue_print');


//----------------------------------------------------------design---------------------------------------------------------------------------------------------------
Route::get('/design/order/create','Design\DesignAll@create_design_order');  
Route::post('/design/order/create','Design\DesignAll@create_design_orderDb');  
Route::get('/design/order/getio/{id}','Design\DesignAll@get_io_by_reference');  
Route::get('/design/order/status/{id}','Design\DesignAll@design_order_status');  
Route::post('/design/order/status/{id}','Design\DesignAll@design_order_statusDb');  

Route::get('/design/order/update/{id}','Design\DesignAll@update_design_order');  
Route::post('/design/order/update/{id}','Design\DesignAll@update_design_orderDb');  

Route::get('/design/order/list','Design\DesignAll@design_order_list');  
Route::get('/design/order/list/api','Design\DesignAll@design_order_api'); 


Route::get('/design/work/create','Design\DesignAll@create_design_work');  
Route::post('/design/work/create','Design\DesignAll@create_design_workDb');  
Route::get('/design/details/{id}','Design\DesignAll@design_details');  
Route::get('/design/work/details/{id}','Design\DesignAll@design_workdetails');  

Route::get('/design/work/status/{id}','Design\DesignAll@design_work_status');  
Route::post('/design/work/status/{id}','Design\DesignAll@design_work_statusDb');  

Route::get('/design/work/update/{id}','Design\DesignAll@update_design_work');  
Route::post('/design/work/update/{id}','Design\DesignAll@update_design_workDb');  

Route::get('/design/work/list','Design\DesignAll@design_work_list');  
Route::get('/design/work/list/api','Design\DesignAll@design_work_api'); 

Route::get('/design/dashboard','Design\DesignAll@design_dashboard');  
Route::get('/design/dashboard/order/{emp_id}','Design\DesignAll@design_dashboard_order'); 
Route::get('/design/dashboard/api/{id}/{emp_id}','Design\DesignAll@design_dashboard_api'); 
// Route::get('/design/status/update/{id}/{status}','Design\DesignAll@design_status_update');  

Route::get('/design/summary','Design\DesignAll@design_summary');  
Route::get('/design/summary/api','Design\DesignAll@design_summary_api');  
Route::get('/design/summary/alldata/{id}','Design\DesignAll@design_summary_all');  


Route::get('/design/fms','Design\DesignAll@fms');
Route::post('/design/fms/api','Design\DesignAll@design_fms_api');


//report
Route::get('/design/report/work','Design\DesignAll@design_work_report'); 
Route::get('/design/work/all/{id}','Design\DesignAll@design_work_all');  
Route::get('/work/status/all/{id}','Design\DesignAll@design_work_status_all'); 

Route::get('/work/status/update/{id}','Design\DesignAll@design_work_status_update'); 
Route::post('/work/status/update/{id}','Design\DesignAll@design_work_status_updateDb'); 

Route::get('/design/report/cancel','Design\DesignAll@design_cancel_report'); 
Route::get('/design/report/cancel/api','Design\DesignAll@design_cancel_report_api');  

Route::get('/design/report/closed','Design\DesignAll@design_closed_report'); 
Route::get('/design/report/closed/api','Design\DesignAll@design_closed_report_api');  


Route::get('/design/report/ctp','Design\DesignAll@design_ctp_report'); 
Route::get('/design/report/ctp/api','Design\DesignAll@design_ctp_report_api'); 

Route::get('/design/report/workover','Design\DesignAll@design_workover_report'); 
Route::get('/design/report/workover/api','Design\DesignAll@design_workover_report_api');

//--------------------------------------------------------master----------------------------------------------------------

//department
Route::get('/master/department', 'MastersController@create_department');
Route::post('/master/department', 'MastersController@create_departmentDb');
Route::get('/master/department/list', 'MastersController@department_list');
Route::get('/master/department/list/api', 'MastersController@department_list_api');
Route::get('/master/department/edit/{id}', 'MastersController@update_department');
Route::post('/master/department/edit/{id}', 'MastersController@update_departmentDb');


//assets
Route::get('/master/assets', 'MastersController@create_assets');
Route::post('/master/assets', 'MastersController@create_assetsDb');
Route::get('/master/assets/list', 'MastersController@assets_list');
Route::get('/master/validate/bill/no', 'MastersController@assets_unique_bill_no');

Route::get('/master/assets/list/api', 'MastersController@assets_list_api');
Route::get('/master/assets/view/{id}', 'MastersController@assets_view');
Route::get('/master/assets/edit/{id}', 'MastersController@update_assets');
Route::post('/master/assets/edit/{id}', 'MastersController@update_assetsDb');
Route::get('/master/assets/assign/employee', 'MastersController@asset_issue_to_employee');
Route::post('/master/assets/assign/employee', 'MastersController@asset_issue_to_employeeDb');
Route::get('/master/filter/assetcode/api', 'MastersController@filter_asset_code_api');
Route::get('/master/assets/assign/employee/list','MastersController@asset_issue_to_employee_list');
Route::get('/master/assets/assign/employee/list/api', 'MastersController@asset_issue_to_employee_api');
Route::get('/master/assets/disposal/list','MastersController@asset_disposal_list');
Route::get('/master/assets/disposal/list/api','MastersController@asset_disposal_list_api');
Route::get('/master/assets/disposal','MastersController@asset_disposal');
Route::post('/master/assets/disposal','MastersController@asset_disposal_db');
Route::get('/master/filter/allotcode/asset/api', 'MastersController@alloted_asset_code_api');
Route::get('/master/filter/employee/asset/api', 'MastersController@alloted_asset_emp_api');
//asset report
Route::get('/master/report/employee/asset', 'MastersController@employee_assets_report');
Route::get('/master/report/employee/asset/api', 'MastersController@employee_assets_api');

//asset issue
Route::get('/master/assets/assign/generate/form', 'Template@asset_assign_form');

// asset return
Route::get('/asset/return/{id}', 'MastersController@return_asset');
Route::post('/asset/return/{id}', 'MastersController@return_asset_db');

//----------------------------------------------production---------------------------------
//pre-press module
//----------------------------------------------plate by party-------------------------------------------------------
Route::get('/production/PlateByParty/notGenerated/summary', 'Production\ProductionAll@plate_by_party_notgen');
Route::get('/production/PlateByParty/notGenerated/api', 'Production\ProductionAll@plate_by_party_notgen_api');

Route::get('/production/platebyparty/create/{id}', 'Production\ProductionAll@plate_by_party_create');
Route::post('/production/platebyparty/create/{id}', 'Production\ProductionAll@plate_by_party_createDb');

Route::get('/production/PlateByParty/creation/summary', 'Production\ProductionAll@plate_by_party_creation');
Route::get('/production/PlateByParty/creation/api', 'Production\ProductionAll@plate_by_party_creation_api');

//---------------------------------------------plate by press------------------------------------------------------------
Route::get('/production/platebypress/summary','Production\ProductionAll@plate_by_press_notgen');
Route::get('/production/platebypress/summary/api','Production\ProductionAll@plate_by_press_notgen_api');
Route::get('/production/dailyprocessplanning/list','Production\ProductionAll@daily_process_planning');
Route::get('/production/dailyprocessplanning/list/api','Production\ProductionAll@dialy_process_planning_api');
Route::post('/Production/dailyprocess/creation','Production\ProductionAll@dailyprocesss_creationDB');
Route::post('/Production/dailyprocess/updation','Production\ProductionAll@dailyprocesss_updationDB');
Route::get('/production/daily/plate/actual/list/{jc}/{ele}','Production\ProductionAll@daily_planned_actual_listing');
Route::get('/production/daily/plate/actual/list/api/{jc}/{ele}','Production\ProductionAll@daily_planned_actual_listing_api');


Route::get('/production/daily/plate/report/list','Production\ProductionAll@daily_plate_report');
Route::get('/production/daily/plate/report/list/api','Production\ProductionAll@daily_plate_report_api');
Route::post('/prod/dailyplatereport/submitted','Production\ProductionAll@dailyplatereport_creationDB');

Route::get('/prod/platebypress/creation/{id}','Production\ProductionAll@platesbypress_creation');
Route::post('/prod/platebypress/submitted','Production\ProductionAll@platesbypress_creationDB');
Route::get('/prod/platebypress/created/list','Production\ProductionAll@platebypress_creation_list');
Route::get('/prod/platebypress/created/list/api','Production\ProductionAll@platebypress_creation_list_api');
Route::get('/prod/platebypress/report','Production\ProductionAll@platebypress_report');
Route::get('/prod/platebypress/report/api','Production\ProductionAll@platebypress_report_api');

//---------------------press module-------------------------------------------
Route::get('/production/press/job/summary','Production\ProductionAll@press_job_list');
Route::get('/production/press/job/summary/api','Production\ProductionAll@press_job_list_api');
Route::get('/production/rawmaterial/alldata/{jc}/{elem}','Production\ProductionAll@raw_material_data');
//daily planning
Route::get('/production/press/dailyplanning/summary','Production\ProductionAll@press_dailyplanning_list');
Route::get('/production/press/dailyplanning/process/summary/api','Production\ProductionAll@press_dailyplanning_list_api');
Route::get('/production/press/dailyplanning/completed/summary/api','Production\ProductionAll@press_dailyplanning_completed_list_api');

Route::post('/Production/press/dailyprocess/creation','Production\ProductionAll@press_dailyprocesss_create');

Route::get('/production/press/dailyplanning/report','Production\ProductionAll@press_dailyplanning_report');
Route::get('/production/press/dailyplanning/report/api','Production\ProductionAll@press_dailyplanning_report_api');
Route::post('/prod/press/dailyplanning/report/submitted','Production\ProductionAll@press_dailyplanning_creationDB');
Route::post('/production/press/dailyprocess/updation','Production\ProductionAll@press_dailyprocesss_updationDB');

Route::get('/production/press/dailyplanning/actual/list/{jc}/{ele}','Production\ProductionAll@dailyplanned_listing');
Route::get('/production/press/dailyplanning/actual/list/api/{jc}/{ele}','Production\ProductionAll@dailyplanned_listing_api');

Route::get('/production/press/machineplanning/','Production\ProductionAll@press_machinewise');
Route::get('/production/press/machineplanning/api/{date}/{machine}','Production\ProductionAll@press_machinewise_api');

Route::get('/press/planned/date/{id}','Production\ProductionAll@getDate');

Route::get('/production/press/machineplanning/print/{date}/{machine}','Production\ProductionAll@print_press');

Route::get('/production/press/summary','Production\ProductionAll@press_prod_summary');
Route::get('/production/press/summary/api','Production\ProductionAll@press_prod_summary_api');

Route::get('/production/planning/alldata/{id}','Production\ProductionAll@plan_data');

//city

Route::get('/master/city', 'MastersController@create_city');
Route::post('/master/city', 'MastersController@create_city_DB');
Route::get('/master/city/search', 'MastersController@city_search');
//---------------------------------------widget-----------------------------------------------

Route::get('/widget/all/', 'WidgetAll@widgets');

Route::get('/dashboard/album/create','WidgetAll@album_insert');
Route::post('/dashboard/album/create','WidgetAll@album');
Route::get('/gallary/{id}','WidgetAll@gallary');
Route::get('/dashboard/photos/insert','WidgetAll@photos_insert');
Route::post('/dashboard/photos/insert','WidgetAll@photos');
Route::get('/dashboard/photos/gallary','WidgetAll@photos_gallary');

Route::get('/widget/permission/denied','WidgetAll@permission_denied');
Route::get('/widget/permission/{id}','WidgetAll@permission');
Route::post('/widget/setpermission','WidgetAll@setpermission');
Route::get('/widget/getadminpermission/{id}','WidgetAll@getadminpermission');
Route::get('/events/basis/date/select','WidgetAll@datewise_event');

Route::get('/dashboard/all','WidgetAll@dash_all');
 
//----------------------------------financial year----------------------------------------
Route::get('/financial/year/create', 'MastersController@create_financial');
Route::post('/financial/year/create', 'MastersController@create_financialDb');

Route::get('/financial/year/summary', 'MastersController@financial_list');
Route::get('/financial/year/summary/api', 'MastersController@financial_list_api');

Route::get('/financialYear/edit/{id}','MastersController@update_financial');
Route::post('/financialYear/edit/{id}','MastersController@update_financialDb');
//--------------------------------------------------hr module--------------------------------------------------------------
Route::get('/hr/doinsert', 'HR\HRAll@addleave');
Route::get('/hr/insertdata', 'HR\HRAll@insertdata');

Route::get('/hr/leave/count/list', 'HR\HRAll@leave_count_list');
Route::get('/hr/leave/count/list/api', 'HR\HRAll@leave_count_list_api');

Route::get('/hr/leave/enhancement/list', 'HR\HRAll@leave_enhancement_list');
Route::get('/hr/leave/enhancement/list/api', 'HR\HRAll@leave_enhancement_list_api');

Route::post('/hr/leave/enhancement/form', 'HR\HRAll@leave_enhancement_form');


Route::get('/hr/leave/create', 'HR\HRAll@create_leave');
Route::post('/hr/leave/create', 'HR\HRAll@create_leaveDb');

Route::get('/hr/events/create', 'HR\HRAll@create_events');
Route::post('/hr/events/create', 'HR\HRAll@create_eventsDb');

Route::get('/hr/announcements/create', 'HR\HRAll@create_announcements');
Route::post('/hr/announcements/create', 'HR\HRAll@create_announcementsDb');

Route::get('/get/pic/{id}', 'HR\HRAll@pic');
Route::get('/get/department/{id}', 'HR\HRAll@department');

Route::get('/get/emp/shift/time/api','HR\HRAll@emp_shift');


Route::get('/events', 'HR\HRAll@index');
Route::get('/announcements', 'HR\HRAll@announcements');

Route::get('/hr/leave/list', 'HR\HRAll@leave_list');
Route::get('/hr/leave/list/api', 'HR\HRAll@leave_list_api');

Route::get('/hr/setting', 'HR\HRAll@setting');
Route::post('/hr/setting', 'HR\HRAll@setting_Db');

Route::post('/hr/leave/approve/{id}', 'HR\HRAll@leave_approve');
Route::get('/hr/leave/print/{id}','HR\HRAll@leave_print');

Route::get('/hr/leave/setting/list', 'HR\HRAll@leave_setting_list');
Route::get('/hr/leave/setting/list/api', 'HR\HRAll@leave_setting_list_api');

//recruitment
Route::get('/hr/recruitment/data/create', 'HR\HRAll@recruitment');
Route::post('/hr/recruitment/data/create', 'HR\HRAll@recruitmentDb');

Route::get('/hr/recruitment/data/update/{id}', 'HR\HRAll@recruitment_update');
Route::post('/hr/recruitment/data/update/{id}', 'HR\HRAll@recruitment_updateDb');

Route::get('/hr/recruitment/not/list', 'HR\HRAll@recruitment_not_list');
Route::get('/hr/recruitment/not/list/api', 'HR\HRAll@recruitment_not_list_api');

Route::post('/hr/recruitment/assess/create', 'HR\HRAll@recruitment_interview_assess');

Route::get('/hr/recruitment/interview/log', 'HR\HRAll@recruitment_interview_log');
Route::get('/hr/recruitment/assess/update/', 'HR\HRAll@recruitment_interview_update');
Route::get('/hr/recruitment/interview/log/api', 'HR\HRAll@recruitment_interview_log_api');
Route::get('/hr/recruitment/pf/register', 'HR\HRAll@pf_register');
Route::get('/hr/recruitment/pf/register/api', 'HR\HRAll@pf_register_api');

Route::get('/hr/recruitment/esi/register', 'HR\HRAll@esi_register');
Route::get('/hr/recruitment/esi/register/api', 'HR\HRAll@esi_register_api');



Route::get('/hr/interview/assess/data/{id}', 'HR\HRAll@interview_assess_data');
Route::get('/hr/interview/assess/log/{id}', 'HR\HRAll@interview_assess_data_log');
Route::get('/hr/interview/assess/print/{id}','HR\HRAll@interview_assess_print');
Route::post('hr/signed/offer/letter','HR\HRAll@upload_offer_letter');

Route::get('/recruitment/template/pdf/{id}/{name}','HR\HRAll@offerletter_generate');

Route::get('/hr/leave/register/list', 'HR\HRAll@leave_register_list');
Route::get('/hr/leave/register/api', 'HR\HRAll@leave_register_api');

Route::get('/hr/leave/register/print/{id}/{yr}','HR\HRAll@leave_register_print');


Route::get('/get/employee/{yr}', 'HR\HRAll@get_emp');

Route::get('/hr/leave/register/details/{emp}/{yr}', 'HR\HRAll@leave_reg_details');

//------------------------------------binding module-------------------------------------------------


Route::get('/binding/bills/create', 'Production\ProductionAll@create_binding_bills');
Route::post('/binding/bills/create/db', 'Production\ProductionAll@create_binding_billsDb');

Route::get('/binding/bills/update/{id}', 'Production\ProductionAll@update_binding_bills');
Route::post('/binding/bills/update/{id}', 'Production\ProductionAll@update_binding_billsDb');

Route::get('/binding/bills/list/{status}', 'Production\ProductionAll@binding_bills_list');
Route::get('/binding/bills/list/api/{status}', 'Production\ProductionAll@binding_bills_list_api');

Route::get('/binding/setting', 'Production\ProductionAll@setting');
Route::post('/binding/setting', 'Production\ProductionAll@setting_Db');

Route::get('/binding/bills/setting/list', 'Production\ProductionAll@setting_list');
Route::get('/binding/bills/setting/list/api', 'Production\ProductionAll@setting_list_api');

Route::post('/binding/bills/approve/{id}', 'Production\ProductionAll@approve_binding_bills');
Route::get('/binding/bills/approve/data/{id}','Production\ProductionAll@approve_binding_bills_data');

Route::get('/binding/bills/report', 'Production\ProductionAll@binding_report');
Route::get('/binding/bills/report/api/{id}', 'Production\ProductionAll@binding_report_api');

Route::get('/binding/bills/report/binder', 'Production\ProductionAll@binding_report_binder');
Route::get('/binding/bills/report/binder/api/{bind_id}/{io_id}', 'Production\ProductionAll@binding_report_binder_api');

Route::get('/binding/bills/getbinder/{id}', 'Production\ProductionAll@get_binder');

Route::get('/binding/bills/status/{id}', 'Production\ProductionAll@status_binder');

//-------------------------------------------binder master--------------------------------------------
Route::get('/binder/create','Production\ProductionAll@binder_create');  
Route::post('/binder/create','Production\ProductionAll@binder_createDb') ; 

Route::get('/binder/summary','Production\ProductionAll@binder_list');  
Route::get('/binder/summary/api','Production\ProductionAll@binder_list_api') ; 
 
Route::get('/binder/update/{id}','Production\ProductionAll@binder_update');  
Route::post('/binder/update/{id}','Production\ProductionAll@binder_updateDb') ;


// -------------------------------------------checklist-------------------------------------------------

Route::get('/checklist/task/list','Employee\Checklist@tasklist'); 
Route::get('/checklist/task/list/api','Employee\Checklist@tasklist_api');  

Route::get('/checklist/superadmin/task/status/list','Employee\Checklist@sup_taskstatus_list'); 
Route::get('/checklist/superadmin/task/status/list/api','Employee\Checklist@sup_taskstatus_api');
Route::get('/checklist/superadmin/status/pending/list/api','Employee\Checklist@sup_taskstatus_pending_api');

Route::get('/checklist/employee/status/list','Employee\Checklist@emp_taskstatus_list');
Route::get('/checklist/employee/status/list/api','Employee\Checklist@emp_taskstatus_list_api');
Route::get('/checklist/employee/status/pending/list/api','Employee\Checklist@emp_taskstatus_pending_api');

Route::get('/employee/checklist/task/score','Employee\Checklist@task_score');
Route::get('/employee/checklist/task/score/api','Employee\Checklist@task_score_api');

Route::get('/chklist/emp/status/upd/{status}/{id}','Employee\Checklist@update_empstatus');
Route::get('/chklist/super/status/upd/{status}/{id}','Employee\Checklist@update_supstatus');

/*Route::get('/chklist/super','Employee\Checklist@future_task_status'); //Auto_update Testing purpose not for use */

// ---------------------------------------Delegation-----------------------------------

Route::get('/create/delegation','Employee\Delegation@create_delegation'); 
Route::post('/create/delegation','Employee\Delegation@create_delegation_db');

Route::get('/delegation/employee/summary','Employee\Delegation@delegation_emp_summary');
Route::get('/delegation/employee/summary/api','Employee\Delegation@delegation_emp_summary_api');

Route::get('/delegation/summary','Employee\Delegation@delegation_summary');
Route::get('/delegation/summary/api','Employee\Delegation@delegation_summary_api');

Route::get('/add/completion','Employee\Delegation@completion_date_db');
Route::post('/add/status/completion','Employee\Delegation@completion_status_db');

Route::get('/all/completion/date/{id}','Employee\Delegation@completion_details');

Route::get('/delegation/score','Employee\Delegation@delegation_score');
Route::get('/delegation/score/api','Employee\Delegation@delegation_score_api');

Route::get('/delegation/report/completiondate','Employee\Delegation@report_bycompletiondate');
Route::get('/delegation/report/completiondate/api','Employee\Delegation@report_bycompletiondate_api');

Route::get('/delegation/evaluation/summary','Employee\Delegation@completed_evaluation');
Route::get('/delegation/employee/completed/api','Employee\Delegation@pending_evaluation_api');
Route::get('/delegation/ea/completed/api','Employee\Delegation@done_evaluated_api');

Route::post('/delegation/final/status/update','Employee\Delegation@update_evaluated_db');

Route::get('/delegation/status/details/summary/{id}','Employee\Delegation@status_details');
Route::get('/delegation/status/details/summary/api/{id}','Employee\Delegation@status_details_api');

// =================================================Tally==================================================
Route::get('/voucher/insert/sale/invoice','accounting\TallyController@voucher_insert_sale_invoice');
Route::get('/voucher/insert/purchase/invoice','accounting\TallyController@voucher_insert_purchase_invoice');

// ================================================collection_module===============================================

Route::get('/collection/taxinvoice/dispatch','CollectionController@tax_inv_dispatch');
Route::get('/collection/taxinvoice/dispatch/api','CollectionController@tax_inv_dispatch_api');
Route::get('/collection/tax/dispatch/reciept','CollectionController@tax_invoice_receipt_date');
Route::get('/collection/payment/date/summary','CollectionController@payment_date_summary');
Route::get('/collection/payment/date/summary/api','CollectionController@payment_date_summary_api');
Route::get('/collection/update/payment/date','CollectionController@update_payment_date');

Route::get('/collection/billrecieve/summary','CollectionController@bill_recievable');
Route::get('/collection/billrecieve/summary/api','CollectionController@bill_recievable_api');
Route::post('/collection/billrecieve/entry/db','CollectionController@bill_recievable_ondate_db');

Route::get('/tcpdf', 'Template@createPDF')->name('createPDF');
Route::get('/collection/paymentrecieved/summary','CollectionController@payment_recieved_summary');
Route::get('/collection/paymentrecieved/summary/api','CollectionController@payment_recieved_summary_api');
Route::get('/collection/recievedbytax/{tax_id}','CollectionController@bytax_details');
Route::get('/collection/paymentrecievedbytax/api/{tax_id}','CollectionController@bytax_details_api');

Route::get('/collection/paymentrecievedbytax/status', 'CollectionController@paymentrecieved_status');

Route::get('/collection/engine/followupsheet','CollectionController@collection_engine');
Route::get('/collection/engine/follow/up/sheet/api','CollectionController@collection_engine_api');
Route::post('/collection/engine/status/update','CollectionController@submit_collection_status');


// Route::get('/collection/report','CollectionController@report');
// Route::get('/collection/report/api','CollectionController@report_api');



// =================================================daily_report===================================================

Route::get('/admin/daily/report','CollectionController@dailyreport');
Route::get('/admin/daily/report/api','CollectionController@dailyreport_api');
Route::get('/email/daily/report','Email\EmailController@daily_reports');
// Route::get('/admin/daily/report','AdminController@dailyreport');
Route::get('/template/daily/report','Template@dailyreport_template');

Route::get('/sendmsg','Email\EmailController@sendMsgform');
Route::post('/sendmsg','Email\EmailController@sendMesg');

// =============================================Payroll=========================================

Route::get('/create/holiday','HR\HRAll@create_holiday');
Route::post('/create/holiday','HR\HRAll@create_holiday_db');
Route::get('/holiday/summary','HR\HRAll@holiday_summary');
Route::get('/holiday/summary/api','HR\HRAll@holiday_summary_api');
Route::get('/update/holiday/{id}','HR\HRAll@update_holiday');
Route::post('/update/holiday/{id}','HR\HRAll@update_holiday_db');

// ==========================================remuneration=======================================

// -------------------------------------------salary--------------------------------------------
Route::get('/employee/salary/list','Employee\Remuneration@emp_list_for_salary');
Route::get('/employeeworking/list/api','Employee\Remuneration@working_employee_api'); 

Route::get('/employee/salary/form/{id}','Employee\Remuneration@salary_form');
Route::post('/employee/salary/form/{id}','Employee\Remuneration@salary_form_db');

Route::get('/salary/list/a/b','Employee\Remuneration@salary_list_a_b');
Route::get('/salary/list/a/b/api','Employee\Remuneration@salary_list_a_b_api');

Route::get('/salary/list/c','Employee\Remuneration@salary_list_c');
Route::get('/salary/list/c/api','Employee\Remuneration@salary_list_c_api');

Route::get('/employee/salary/list/a','Employee\Remuneration@SalaryListA');
Route::get('/employee/salary/list/a/api','Employee\Remuneration@SalaryListAapi');

Route::get('/employee/salary/list/a/payment/details/{id}/{type}','Employee\Remuneration@GetPayDetails_Salary');
Route::get('/employee/salary/list/a/payment','Employee\Remuneration@Payment_Salary');
Route::get('/salary/payment/details/{id}/{type}','Employee\Remuneration@GetPayDetails');



Route::get('/employee/salary/list/b/payment/details/{id}/{type}','Employee\Remuneration@GetPayDetails_Salary');
Route::get('/employee/salary/list/b/payment','Employee\Remuneration@Payment_Salary');

Route::get('/employee/salary/list/c/payment/details/{id}/{type}','Employee\Remuneration@GetPayDetails_Salary');
Route::get('/employee/salary/list/c/payment','Employee\Remuneration@Payment_Salary');

Route::get('/employee/salary/list/b','Employee\Remuneration@SalryListB');
Route::get('/employee/salary/list/b/api','Employee\Remuneration@SalryListBapi');

Route::get('/employee/salary/list/c','Employee\Remuneration@SalryListC');
Route::get('/employee/salary/list/c/api','Employee\Remuneration@SalryListCapi');

Route::get('/employee/full&final/settlement', 'Employee\Remuneration@full_final_settlement');
Route::get('/employee/full&final/settlement/api', 'Employee\Remuneration@full_final_settlement_api');
Route::post('/employee/full&final/settlement/create', 'Employee\Remuneration@full_final_settlement_create');

Route::get('/salary/ctc/calculator','Employee\Remuneration@ctc_calculator');

Route::get('/salaryA/calculation','Employee\Remuneration@salaryA_cal_form');
Route::get('/salaryA/calculation/api','Employee\Remuneration@salaryA_cal');
Route::post('/salaryA/create','Employee\Remuneration@salaryA');

Route::get('/salaryB/calculation','Employee\Remuneration@salaryB_cal_form');
Route::get('/salaryB/calculation/api','Employee\Remuneration@salaryB_cal');
Route::post('/salaryB/create','Employee\Remuneration@salaryB');

Route::get('/salaryC/calculation','Employee\Remuneration@salaryC_cal_form');
Route::get('/salaryC/calculation/api','Employee\Remuneration@salaryC_cal');
Route::post('/salaryC/create','Employee\Remuneration@salaryC');

Route::get('/hr/salary/register', 'Employee\Remuneration@salary_register_list');
Route::post('/hr/salary/register/api', 'Employee\Remuneration@salary_register_list_api');
Route::get('/hr/salary/register/print/{id}/{yr}','Employee\Remuneration@salary_register_print');

// -------------------------------------increment-------------------------------------------
Route::get('/remuneration/increment/add','Employee\Remuneration@add_increment');
Route::post('/remuneration/increment/add','Employee\Remuneration@add_increment_db');

Route::get('/increment/history/list','Employee\Remuneration@increment_history');
Route::get('/increment/history/list/api','Employee\Remuneration@increment_history_api');

Route::get('/increment/month/wise/report','Employee\Remuneration@increment_month_report');
Route::get('/increment/month/wise/report/api','Employee\Remuneration@increment_month_report_api');

Route::get('/increment/salary/c/report','Employee\Remuneration@inc_salary_c_report');
Route::get('/increment/salary/c/report/api','Employee\Remuneration@inc_salary_c_report_api');
// ------------------------------------------DA increment----------------------------------------------
Route::get('/da/increment/add','Employee\Remuneration@add_da_increment');
Route::post('/da/increment/add','Employee\Remuneration@add_da_increment_db');

Route::get('/da/increment/summary','Employee\Remuneration@da_increment_summary');
Route::get('/da/salary/summary/api','Employee\Remuneration@da_increment_summary_api');

// ===========================================Bonus======================================================
Route::get('/bonus/calculator','Employee\Remuneration@bonus_calculator');
Route::get('/bonus/calculator/api','Employee\Remuneration@bonus_calculator_api');
Route::get('/bonus/to/be/paid','Employee\Remuneration@bonus_to_be_paid');
Route::get('/bonus/to/be/paid/api','Employee\Remuneration@bonus_to_be_paid_api');
Route::get('/getbonus/{id}','Employee\Remuneration@bonus_get');


Route::get('/bonus/calculator/left','Employee\Remuneration@bonus_calculator_left');
Route::get('/bonus/calculator/left/api','Employee\Remuneration@bonus_calculator_left_api');

Route::get('/totalsal/a/twelve/month/{empid}','Employee\Remuneration@twelve_month_salary');

// ========================================Advance======================================
Route::get('/advance/create','Employee\Remuneration@advance_create');
Route::post('/advance/create','Employee\Remuneration@advance_create_db');

Route::get('/advance/summary','Employee\Remuneration@advance_summary');
Route::get('/advance/summary/api','Employee\Remuneration@advance_summary_open_api');
Route::get('/advance/summary/close/api','Employee\Remuneration@advance_summary_close_api');
Route::get('/advance/deduction','Employee\Remuneration@advance_deduction');
Route::post('/advance/deduction','Employee\Remuneration@advance_deduction_db');
Route::get('/advance/deduction/employee/record','Employee\Remuneration@advance_fetch_record');

Route::get('/advance/approval','Employee\Remuneration@advance_approval');
Route::get('/advance/paid/list','Employee\Remuneration@advance_paid_summary');
//============================================Attendance==================================

Route::get('/hr/attendance/summary','Employee\Remuneration@emp_attendance_summary');
Route::get('/hr/attendance/summary/api','Employee\Remuneration@emp_attendance_summary_api');
Route::get('/hr/get/attendance/{id}','Employee\Remuneration@get_emp_attendance');
Route::post('/hr/attendance/update','Employee\Remuneration@emp_attendance_update');
Route::post('/hr/attendance/create','Employee\Remuneration@emp_attendance_create');

Route::get('/hr/attendance/overtime/summary','Employee\Remuneration@emp_attendance_overtime_summary');
Route::get('/hr/attendance/overtime/summary/api','Employee\Remuneration@emp_attendance_overtime_summary_api');
Route::get('/hr/attendance/late/summary','Employee\Remuneration@emp_attendance_late_summary');
Route::get('/hr/attendance/late/summary/api','Employee\Remuneration@emp_attendance_late_summary_api');

Route::get('/hr/attendance/create/summary','Employee\Remuneration@emp_attendance_create_summary');
Route::get('/hr/attendance/create/summary/api','Employee\Remuneration@emp_attendance_create_summary_api');







});
//bahar nii aana h




/*--------------------------------------------------------------------------*/

// do not remove the below comment
// for future purpose
// Route::get('/', function (Request $request) {
    // print_r(\Request::get('userAlloweds'));
    // Auth::check()
    // $email = "singhrajnish.rk@gmail.com";
    // $password = '12345';
    // $user = new \App\Model\Users();
    // $user->email = $email;
    // $user->password = Hash::make($password);
    // $user->name = 'Rajnish';
    // $user->login= 1;
    // $user->active= 1;
    // $user->phone= 9651478523;
    // try{
    //     $user->save();
    // }
    // catch(Exception $e) {
    //     echo "Failed to create new user!". $e->getMessage();
    // }
    // return view('sections.test', ['layout' => 'layouts.main']);
// });

Route::get ('/google/login', function(){
    return Socialite::driver('google')->redirect();
});

Route::get ('/callback/google', function(){
    $user = Socialite::with('google')->user();
    $user = \App\Model\Users::where('email', '=', $user->email)->first();
    if (Auth::login($user)) {
        return redirect()->intended('/dashboard');
    }else
        return redirect()->intended('/login'); // TODO: say user that email not specified
});

Auth::routes();

Route::get('/pdf', 'HomeController@pdf');
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
