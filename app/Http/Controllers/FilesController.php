<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
class FilesController extends Controller
{
    public function download($file)
    {
            $destinationPath = public_path().'/Purchase/GRN/'.$file; 
              //print_r($destinationPath);die;
              if(File::exists($destinationPath)){
                return response()->download($destinationPath);
            } else { 
                return redirect('/purchase/grn/summary')->with('error','File Not Exist');
                }
    }
    public function download1($file)
    {
            $destinationPath = public_path().'/Purchase/MaterialInwardBill/'.$file; 
              // print_r($destinationPath);die;
              if(File::exists($destinationPath)){
                return response()->download($destinationPath);
            } else { 
                return redirect('/purchase/inwarding/list')->with('error','File Not Exist');
                }
    }
}
