<?php 

namespace App;
use App\Model\Userlog;
use App\Model\Users;
use App\Model\Settings;
use App\Model\Employee\EmployeeProfile;
use App\Model\Employee\EmployeeTask;
use App\Model\Employee\EmployeeTaskStatus;
use App\Model\FinancialYear;
use DB;
use Auth;
use \Carbon\Carbon;
use TCPDF;

// $app=require_once __DIR__.'/public/images/';
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
               // get the current page break margin
               $bMargin = 38;
            //    print_r($bMargin);die;
               // get current auto-page-break mode
               $auto_page_break = $this->AutoPageBreak;
               // disable auto-page-break
               $this->SetAutoPageBreak(false, 0);
               // set bacground image
               $img_file = K_PATH_IMAGES.'/letterhead.jpg';
               $this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
               // restore auto-page-break status
               $this->SetAutoPageBreak($auto_page_break, $bMargin);
               // set the starting point for the page content
               $this->setPageMark();
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}