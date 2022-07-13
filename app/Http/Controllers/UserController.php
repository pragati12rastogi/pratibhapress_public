<?php

namespace App\Http\Controllers;

use App\User;
use App\Custom\CustomHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use Illuminate\Http\Request;
use App\Model\Users;
use App\Model\Userlog; 
use App\Model\Party; 

use App\Model\UserLayoutRight;
use App\Model\UserSectionRight;
use App\Model\Department;
use Auth;
use File;


class UserController extends Controller
{
    function register_create()
    {
        $depart = Department::all();
        $data = array(
            'dept'=>$depart,
            'layout'=>'layouts.main'
        );
        return view('sections.registration',$data);
    }    
    function register_insert(Request $request)
    {
       try {
        $this->validate($request,[
            'username'=>'required',
            'email'=>'required|email|unique:users,email',
            'pass'=>'required|min:6',
            're_pass'=>'required_with:pass|same:pass',
            'user_type'=>'required',
            'depart'=>'required',
            'phone'=>'required|digits:10|unique:users,phone',
            'landline'=>'nullable|numeric',
            'profile_pic' => 'nullable|image|max:'.CustomHelpers::getfilesize()
        ],[
            'username.required'=> 'User Name is required.',
            'email.required'=> 'Email is required.',
            'email.email'=> 'Email must be of valid  format.',
            'pass.required'=> 'Password is required.',
            'pass.min'=> 'Password min lenght is :min.',
            're_pass.required_with'=> 'Confirm Password is required with Password.',
            're_pass.same'=> 'Confirm Password should be same as Password.',
            'user_type.required'=> 'User Type is required.',
            'phone.required'=> 'Phone Number is required.',
            'landline.digits'=> 'Landline Number contains digits only.',
            'phone.digits'=> 'phone Number contains digits only.',
            'phone.unique'=> 'phone Number already taken.',
            'depart.required'=> 'Department is required.',
            'profile_pic.max'=>'Please upload image less than 2 MB'

        ]);
        if($request->hasFile('profile_pic'))
        {
            // $this->validate($request, ['profile_pic' => 'image|max:1048']);

            $file = $request->file('profile_pic');
            $destinationPath = public_path().'/userimages';
            // Get filename with extension            
            $filenameWithExt = $request->file('profile_pic')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);      
            // Get just ext
            $extension = $request->file('profile_pic')->getClientOriginalExtension();
            //Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;  
            // Upload Image
            $path = $file->move($destinationPath, $fileNameToStore);
        }
        else{
            $fileNameToStore="";
        }
            $id = Users::insertGetId(
                [
                    'id'=>NULL,
                    'active'=>'1',
                    'login'=>'0',
                    'created_at'=>date('Y-m-d G:i:s'),
                    'user_type'=>$request->input('user_type'),
                    'password'=>Hash::make($request->input('pass')),
                    'name'=>$request->input('username'),
                    'department_id'=>2,
                    'profile_photo'=>$fileNameToStore,
                    'email'=>$request->input('email'),
                    'phone'=>$request->input('phone'),
                    'home_landline'=>$request->input('landline'),
                    'department_id'=>$request->input('depart')
                ]
            ); 
            if($id==NULL)
            {
                DB::rollback();
                return redirect('/user/create/')->with('error','Some Unexpected Error occurred.');  
            } 
            else{
                return redirect('/admin/permission/'.$id)->with("success","User registered successfully.Pls set user acesss permission.");
            }  
        // }
        // else{
        //     return redirect('/user/create/')->with("error","User registered is not successfull.");
        // }
       } catch(\Illuminate\Database\QueryException $ex) {
        return redirect('/user/create/')->with('error','some error occurred'.$ex->getMessage());
       }
            
    }    
    public function user_profile_update()
    {
        return $this->user_update(Auth::id(),"user");
    }
    public function user_update($id,$type="admin")
    {
       
        $detail=Users::where('id',$id)->get()->first();
        $depart = Department::all();
        $auth_id=Auth::id();
        $auth_type=Auth::user()->user_type;
        if($auth_type!='superadmin' && $detail->user_type=="superadmin"){
            return redirect('/admin')->with('error','You Cannot Update Superadmin Profile.');
        }
            if($detail->toArray()){
                $data = array(
                    'detail'=>$detail,
                    'department'=>$depart,
                    'id'=>$id,   
                    'layout'=>'layouts.main',
                    'type'=>$type,
                    'auth_type'=>$auth_type
                );
                // return $detail;
                return view('sections.update_user', $data);  
            }
            else{
                return redirect('/user/create/')->with('error','No Form With this ID Exist.');
            }     
    }
    function user_update_db(Request $request,$id)
    {
        try {
            $this->validate($request,[
                'username'=>'required',
                'email'=>'required|email|unique:users,email,'.$id,
                'type'=>'required',
                'user_type'=>'required_if:type,admin',
                'phone'=>'required|digits:10|unique:users,phone,'.$id,
                'landline'=>'nullable|numeric',
                'profile_pic' => 'image|max:'.CustomHelpers::getfilesize(),
                'depart'=>'required'
            ],[
                'username.required'=> 'User Name is required.',
                'email.required'=> 'Email is required.',
                'email.email'=> 'Email must be of valid  format.',
                'user_type.required'=> 'User Type is required.',
                'phone.required'=> 'Phone Number is required.',
                'landline.digits'=> 'Landline Number contains digits only.',
                'phone.digits'=> 'phone Number contains digits only.',
                'phone.unique'=> 'phone Number already taken.',
                'depart.required'=> 'Department is required.'
            ]);
            $update_data =[];
            
            $image_name=$request->input('hidden_image');
            $file=$request->file('profile_pic');
            if($file != ''){
              
                $request->validate([
                   'profile_pic'=>'required|image|max:2048000'
                ]);
                
                $file = $request->file('profile_pic');
                $destinationPath = public_path().'/userimages';
                // Get filename with extension            
                $filenameWithExt = $request->file('profile_pic')->getClientOriginalName();
                // Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);      
                // Get just ext
                $extension = $request->file('profile_pic')->getClientOriginalExtension();
                //Filename to store
                $fileNameToStore = $filename.'_'.time().'.'.$extension;  
                // Upload Image
                $path = $file->move($destinationPath, $fileNameToStore);
                if($filename!='')
                {
                    $update_data =array('profile_photo'=>$fileNameToStore) ;
                }     
                    
            }
            if($request->input('type')=='admin')
            {
                 $update_data = array_merge($update_data,array(
                    'user_type'=>$request->input('user_type')
                ));
                $redirect_to='/user/update/'.$id;
            }
            else
                $redirect_to='/profile/update';
                if($request->input('pass')){
                    $this->validate($request,[
                        'pass'=>'required|min:6',
                        're_pass'=>'required_with:pass|same:pass',
                    ],[
                        'pass.required'=> 'Password is required.',
                        'pass.min'=> 'Password min lenght is :min.',
                        're_pass.required_with'=> 'Confirm Password is required with Password.',
                        're_pass.same'=> 'Confirm Password should be same as Password.'
                    ]); 
                    $update_data = array_merge($update_data,array(
                        'password'=>Hash::make($request->input('pass')),
                        'name'=>$request->input('username'),
                        'email'=>$request->input('email'),
                        'phone'=>$request->input('phone'),
                        'home_landline'=>$request->input('landline'),
                        'department_id'=>$request->input('depart')
                    ));
                    

                    $user = Users::where('id',$id)->update($update_data); 
                    // print_r($request->input('pass'));die;
                    
                    if($user==NULL) 
                    {
                        DB::rollback();
                        return redirect($redirect_to)->with('error','Some Unexpected Error occurred.');
                    }
                    else{
                        
                        $dept =Department::where('id',$request->input('depart'))->select('department')->get()->first();
                      
                        $log_array=array(
                            'User name'=>$request->input('username'),
                            'Email'=>$request->input('email'),
                            'Phone'=>$request->input('phone'),
                            'Home Landline'=>$request->input('landline'),
                            'Department'=>$dept['department'],
                            'User Type'=>$request->input('user_type')
                            
                        );
                        CustomHelpers::userLog($request->input()['update_reason'],$id,"User Updated",$log_array);
                        return redirect($redirect_to)->with("success","User Updated successfully.");
                    }       
    
                }
                else{
                    $update_data = array_merge($update_data,array(
                        'name'=>$request->input('username'),
                        'email'=>$request->input('email'),
                        'phone'=>$request->input('phone'),
                        'home_landline'=>$request->input('landline'),
                        'department_id'=>$request->input('depart')
                    ));
                    $user = Users::where('id',$id)->update($update_data);
                    if($user==NULL) 
                {
                    DB::rollback();
                    return redirect($redirect_to)->with('error','Some Unexpected Error occurred.');
                }
                else{
                    $dept =Department::where('id',$request->input('depart'))->select('department')->get()->first();
                      
                    $log_array=array(
                        'User name'=>$request->input('username'),
                        'Email'=>$request->input('email'),
                        'Phone'=>$request->input('phone'),
                        'Home Landline'=>$request->input('landline'),
                        'Department'=>$dept['department'],
                        'User Type'=>$request->input('user_type')
                        
                    );

                    CustomHelpers::userLog($request->input()['update_reason'],$id,"User Updated",$log_array);
                    
                    return redirect($redirect_to)->with("success","User Updated successfully.");
                 }      
                }  
        } catch(\Illuminate\Database\QueryException $ex) {
            return redirect($redirect_to)->with('error','some error occurred'.$ex->getMessage());
        }
    } 


}
