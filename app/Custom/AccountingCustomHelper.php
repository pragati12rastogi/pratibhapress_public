<?php
namespace App\Custom;
use App\Model\Userlog;
use App\Model\Users;
use App\Model\Settings;
use App\Model\Accounting\AcSetting;
use App\Model\Accounting\AcCompany;
use App\Model\Accounting\AcCompanySetting;
/*
use App\Model\Accounting\AcSettings;
use App\Model\Accounting\AcSettings;
use App\Model\Accounting\AcSettings;
use App\Model\Accounting\AcSettings;
use App\Model\Accounting\AcSettings;
use App\Model\Accounting\AcSettings;
use App\Model\Accounting\AcSettings;
use App\Model\Accounting\AcSettings;
 */
use Auth;

class AccountingCustomHelper{

    
    public static function getCompanyName($cid='default')
    {
        /**
         * function to return name of company.
         * $cid = id of company as in AcCompany.
         */
        if($cid=='default')
        {
            return AcCompany::where('id',session('Active_comp'))
            ->first('name')['name'] ;
        }
        else
        {
            return AcCompany::where('id',$cid)
            ->first('name')['name'] ;
        }
    }

    public static function getLocalSettingByName($name,$settinFor)
    {
        /**
         * function to return specific Local Setting from AcCompanySetting.
         * $name = name of settting as in AcCompanySetting
         * $settingFor = name of setting as in AcSetting
         */
        $settin_for=AcSetting::where('name','=',$settinFor)->first('id')['id'];
        $result = AcCompanySetting::where('comp_id',session('Active_comp'))
        ->where('name','=',$name)
        ->where('setting_for','=',$settin_for)
        ->select(
            'name',
            'value',
            'message',
            'allowed_values'
        );
        return $result;
    }
    public static function getLocalSetting($settinFor)
    {
        /**
         * function to return all Local Setting from AcCompanySetting.
         * $settingFor = name of setting as in AcSetting
         */
        $settin_for=AcSetting::where('name','=',$settinFor)->first('id')['id'];
        $result = AcCompanySetting::where('comp_id',session('Active_comp'))
        ->where('setting_for','=',$settin_for)
        ->select(
            'name',
            'value',
            'message',
            'allowed_values'
        );
        return $result;
    }
}