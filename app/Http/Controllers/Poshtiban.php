<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use DB;
use Response;
use Carbon\Carbon;
use \Morilog\Jalali\Jalalian;
use Session;
class Poshtiban extends Controller
{
    public function getPostibanList(Request $request)
    {
        $poshtibans=DB::table("CRM.dbo.crm_admin")->where("adminType",2)->orwhere("adminType",4)->where("deleted",0)->get();
        return view("poshtiban.poshtibanList",['poshtibans'=>$poshtibans]);
    }

    public function poshtibanActionInfo(Request $request){ 

        $adminId=$request->get("subPoshtibanId");
        $adminSecondId=$request->get("subPoshtibanId");
        $exactAdminInfo=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->where('deleted',"False")->get()[0];
        
        if($exactAdminInfo->poshtibanType==4){
            $adminId=$exactAdminInfo->driverId;
        }
        $poshtibanType=$exactAdminInfo->poshtibanType;
        $generalBonuses;
        $targets;
        //
        $count_All_aghlam=0;
        $istallComTg="هیچکدام";
        $aghlamComTg="هیچکدام";
        $monyComTg="هیچکدام";
        $countBuyComTg="هیچکدام";
        $strongServiceComTg="هیچکدام";
        $mediumServiceComTg="هیچکدام";
        $weakServiceComTg="هیچکدام";
        $countFactorComTg="هیچکدام";
        $driverBonusInfo=null;
        //تارگت ها تکمیل شده
        $istallComTgBonus=0;
        $aghlamComTgBonus=0;
        $monyComTgBonus=0;
        $countBuyComTgBonus=0;
        $strongServiceComTgBonus=0;
        $mediumServiceComTgBonus=0;
        $countFactorComTgBonus=0;
		//برای همان روز
        
        $count_New_buy_Today=0;
        $count_aghlam_today=0;
        $sum_today_money=0;
        $countToday_StrongService=0;
        $today_MediumService=0;
        $today_WeakService=0;
        $today_factor=0;
		//در طول زمان بعد از تخلیه کاربر
        $all_bonus_since_Empty=0;
        $count_All_aghlam=0;
        $count_All_Install=0;
        $count_All_New_buys=0;
        $count_all_StrongService=0;
        $count_all_MediumService=0;
        $count_all_WeakService=0;
        $count_all_Factor=0;
        $sum_all_money=0;
        $all_Strong_Service=0;
        $all_Medium_Service=0;

		//امتیازات این ماه بعد از تخلیه
        $bonus_All_aghlam=0;
        $bonus_All_Install=0;

        $all_Strong_Service_Bonus=0;
        $all_Weak_Service_Bonus=0;
        $all_Medium_Service_Bonus=0;
        $bonus_All_New_buys=0;
        $bonus_all_money=0;
        //برای تعیین تاریخ از روی جدول تخلیه
        $EMPTYDATE='2022-11-11';
        $EMPTYDATEHEJRI='1401/01/01';

        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $emptyDateInfo=DB::select("SELECT CONVERT(DATE,timeStamp) AS emptyDate FROM CRM.dbo.crm_adminHistory WHERE id=(SELECT MAX(id) FROM CRM.dbo.crm_adminHistory WHERE adminId=$adminSecondId)");
        if($emptyDateInfo){
            $EMPTYDATE=$emptyDateInfo[0]->emptyDate;
        }
        $EMPTYDATEHEJRI=Jalalian::fromCarbon(Carbon::createFromFormat('Y-m-d', $EMPTYDATE))->format('Y/m/d');
        if($exactAdminInfo->poshtibanType==3){
            $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("userType",3)->get();
                    //اکمال تارگت های فروش
            $targets=DB::select("SELECT * FROM CRM.dbo.crm_generalTargets where userType=3");
        }
        if($exactAdminInfo->poshtibanType==2){
            $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("userType",2)->get();
                    //اکمال تارگت های فروش
            $targets=DB::select("SELECT * FROM CRM.dbo.crm_generalTargets where userType=2");
        }
        if($exactAdminInfo->poshtibanType==1){
            $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("userType",1)->get();
                    //اکمال تارگت های فروش
            $targets=DB::select("SELECT * FROM CRM.dbo.crm_generalTargets where userType=1");
        }

        if($exactAdminInfo->poshtibanType==4){
            
            $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("userType",4)->get();
                    //تارگت های راننده ها
            $targets=DB::select("SELECT * FROM CRM.dbo.crm_generalTargets where userType=4");
        }
 
        if($exactAdminInfo->poshtibanType==1 or $exactAdminInfo->poshtibanType==2 or $exactAdminInfo->poshtibanType==3){
            foreach($generalBonuses as $general){
                if($general->id==1 or $general->id==4 or $general->id==7 or $general->id==10){
                    //اقلام
                    $count_All_aghlamR=DB::select("SELECT count(countGoods) as countAghlam,admin_id from (			
                                                    SELECT count(SnGood) as countGoods,admin_id,SnGood from (
                                                    SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood,CustomerSn from(
                                                    SELECT * FROM(
                                                    SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood,CustomerSn FROM Shop.dbo.FactorHDS
                                                    JOIN Shop.dbo.FactorBYS  on FactorHDS.SerialNoHDS=FactorBYS.SnFact where FactDate>='$EMPTYDATEHEJRI')a
                                                    )g  group by SnGood,CustomerSn)c
                                                    join (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)d on c.CustomerSn=d.customer_id)f group by admin_id,SnGood
                                                    )e  group by admin_id");
                    if(count($count_All_aghlamR)>0){
                        $count_All_aghlam=$count_All_aghlamR[0]->countAghlam;
                    }

                    $count_aghlam_todayR=DB::select("SELECT count(countGoods) as countAghlam,admin_id from (			
                                                    SELECT count(SnGood) as countGoods,admin_id,SnGood from (
                                                    SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood,CustomerSn from(
                                                    SELECT * FROM(
                                                    SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood,CustomerSn FROM Shop.dbo.FactorHDS
                                                    JOIN Shop.dbo.FactorBYS  on FactorHDS.SerialNoHDS=FactorBYS.SnFact where FactDate='$todayDate')a
                                                    )g  group by SnGood,CustomerSn)c
                                                    join (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)d on c.CustomerSn=d.customer_id)f group by admin_id,SnGood
                                                    )e  group by admin_id");

                    if(count($count_aghlam_todayR)>0){
                        $count_aghlam_today=$count_aghlam_todayR[0]->countAghlam;
                    }

                    $instAghlamBonus=((int)($count_All_aghlam/$general->limitAmount)) * $general->Bonus;
                    $all_bonus_since_Empty+=$instAghlamBonus;
                    $bonus_All_aghlam=$instAghlamBonus;
                }

                if($general->id==2 or $general->id==5 or $general->id==8 or $general->id==11){
                    //مبلغ
                    $allMoney_till_now=DB::select("SELECT SUM(NetPriceHDS) AS SumOfMoney,admin_id FROM Shop.dbo.factorHds
                                                    JOIN (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)d ON factorHds.CustomerSn=d.customer_id
                                                    WHERE FactType=3 and FactDate>='$EMPTYDATEHEJRI' GROUP BY admin_id");
                    if(count($allMoney_till_now)>0){
                        $sum_all_money=$allMoney_till_now[0]->SumOfMoney;
                    }

                    $today_money=DB::select("SELECT SUM(NetPriceHDS) AS SumOfMoney,admin_Id FROM Shop.dbo.factorHds
                                            JOIN (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)d ON factorHds.CustomerSn=d.customer_id
                                            WHERE FactType=3 AND FactDate='$todayDate' GROUP BY admin_Id");
                    
                    if(count($today_money)>0){
                        $sum_today_money=$today_money[0]->SumOfMoney;
                    }
                    
                    $allMoneyBonus=((int)($sum_all_money/10/$general->limitAmount)) * $general->Bonus;
                    $all_bonus_since_Empty+=$allMoneyBonus;
                    $bonus_all_money=$allMoneyBonus;
                }

                if($general->id==3 or $general->id==6 or $general->id==9){
                    //زنده کردن مشتری
                    //امروز
                    $countNewBuys=0;
                    $count_New_buy_Today=DB::select("SELECT count(CustomerSn) as countLive FROM(
                        select MAX(FactDate)as lastTime,CustomerSn  from Shop.dbo.FactorHDS where FactDate='$todayDate' group by CustomerSn )A
                        JOIN (select customer_id from CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)c on A.CustomerSn=c.customer_id");
                    
                    if(count($count_New_buy_Today)<1){
                        $count_New_buy_Today=0;
                    }else{
                        $count_New_buy_Today=$count_New_buy_Today[0]->countLive;
                    }
                    //همه           
                    $count_All_New_buysR=DB::select("SELECT count(CustomerSn) as countLive FROM(
                        select MAX(FactDate)as lastTime,CustomerSn  from Shop.dbo.FactorHDS where FactDate>='$EMPTYDATEHEJRI' group by CustomerSn )A
                        JOIN (select customer_id from CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)c on A.CustomerSn=c.customer_id");
                    
                    if(count($count_All_New_buysR)>0){
                        $countNewBuys=$count_All_New_buysR[0]->countLive;
                    }
                    
                    $allBuyBonus=((int)($countNewBuys/$general->limitAmount)) * $general->Bonus;

                    $all_bonus_since_Empty+=$allBuyBonus;
                    $bonus_All_New_buys=$allBuyBonus;
                    $count_All_New_buys+=$countNewBuys;
                    
                }
                //پشتیبانها
                $general->count_New_buy_Today=$count_New_buy_Today;
                $general->count_All_New_buys=$count_All_New_buys;
                $general->count_All_aghlam=$count_All_aghlam;
                $general->count_aghlam_today=$count_aghlam_today;
                $general->sum_all_money=$sum_all_money;
                $general->sum_today_money=$sum_today_money;
                $general->$count_All_Install=0;
                //راننده ها
                $general->count_All_Factor=0;
                $general->count_All_Factor_Today=0;
                $general->count_All_StrongService=0;
                $general->count_All_MediumService=0;
                $general->count_All_WeakService=0;
                
            }
        }elseif($exactAdminInfo->poshtibanType==4){
            foreach($generalBonuses as $general){
                if($general->id==21){
                    //اقلام
                    $count_All_aghlamR=DB::select("SELECT COUNT(SnGood) as CountGood,SnDriver FROM(
                                                    SELECT SnGood,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster
                                                    JOIN Shop.dbo.FactorBYS ON BargiryBYS.SnFact=FactorBYS.SnFact
                                                    WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper>'$EMPTYDATEHEJRI')A WHERE SnDriver=$adminId  group by SnDriver");
                    if(count($count_All_aghlamR)>0){
                        $count_All_aghlam=$count_All_aghlamR[0]->CountGood;
                    }

                    $count_aghlam_todayR=DB::select("SELECT COUNT(SnGood) as CountGood,SnDriver FROM(
                                                        SELECT SnGood,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster
                                                        JOIN Shop.dbo.FactorBYS ON BargiryBYS.SnFact=FactorBYS.SnFact
                                                        WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper='$todayDate')A WHERE SnDriver=$adminId  group by SnDriver");
                    if(count($count_aghlam_todayR)>0){
                        $count_aghlam_today=$count_aghlam_todayR[0]->CountGood;
                    }
                    $instAghlamBonus=((int)($count_All_aghlam/$general->limitAmount)) * $general->Bonus;
                    $all_bonus_since_Empty+=$instAghlamBonus;
                    $bonus_All_aghlam=$instAghlamBonus;
                }

                if($general->id==23){
                    //سرویس قوی

                    $strongServices=DB::select("SELECT COUNT(ServiceSn) AS CountStrongService
                        FROM CRM.dbo.crm_driverservice where crm_driverservice.serviceType=1 and CONVERT(DATE,TimeStamp)>'$EMPTYDATE' and adminId=$adminId");
                    if(count($strongServices)>0){
                        $all_Strong_Service= $strongServices[0]->CountStrongService;
                    }

                    $all_Strong_Service_Bonus=((int)($all_Strong_Service/$general->limitAmount)) * $general->Bonus;
                    $all_bonus_since_Empty+=$all_Strong_Service_Bonus;

                    $countToday_StrongServices=DB::select("SELECT COUNT(ServiceSn) AS CountStrongService
                        FROM CRM.dbo.crm_driverservice where crm_driverservice.serviceType=1 and adminId=$adminId
                        AND CONVERT(date,TimeStamp)=Convert(date,CURRENT_TIMESTAMP)");
                    if(count($countToday_StrongServices)>0){
                        $countToday_StrongService=$countToday_StrongServices[0]->CountStrongService;
                    }
                    
                }

                if($general->id==26){
                    // //سرویس متوسط
                    $mediumServices=DB::select("SELECT COUNT(ServiceSn) AS CountMediumService
                        FROM CRM.dbo.crm_driverservice where crm_driverservice.serviceType=2 and CONVERT(DATE,TimeStamp)>'$EMPTYDATE' and adminId=$adminId");

                    $all_Medium_Service=0;

                    if(count($mediumServices)>0){
                        $all_Medium_Service=$mediumServices[0]->CountMediumService;
                    }
                    
                    $all_Medium_Service_Bonus=((int)($all_Medium_Service/$general->limitAmount)) * $general->Bonus;
                    $all_bonus_since_Empty+=$all_Medium_Service_Bonus;

                    $countToday_MediumServices=DB::select("SELECT COUNT(ServiceSn) AS CountMediumService
                        FROM CRM.dbo.crm_driverservice where crm_driverservice.serviceType=2 and adminId=$adminId
                        AND CONVERT(date,TimeStamp)=Convert(date,CURRENT_TIMESTAMP)");
                    if(count($countToday_MediumServices)>0){
                        $today_MediumService=$countToday_MediumServices[0]->CountMediumService;
                    }
                }

                if($general->id==27){
                    // //سرویس ضعیف
                    $weakServices=DB::select("SELECT COUNT(ServiceSn) AS CountWeakService
                        FROM CRM.dbo.crm_driverservice where crm_driverservice.serviceType=3 and CONVERT(DATE,TimeStamp)>'$EMPTYDATE' and adminId=$adminId");
                    $count_all_WeakService=0;
                    if(count($weakServices)>0){
                        $count_all_WeakService=$weakServices[0]->CountWeakService;
                        
                    }
                    $all_Weak_Service_Bonus=((int)($count_all_WeakService/$general->limitAmount)) * $general->Bonus;
                    $all_bonus_since_Empty+=$all_Weak_Service_Bonus;
                    //امروز
                    $countToday_WeakServices=DB::select("SELECT COUNT(ServiceSn) AS CountWeakService
                        FROM CRM.dbo.crm_driverservice where crm_driverservice.serviceType=3 and adminId=$adminId
                        AND CONVERT(date,TimeStamp)=Convert(date,CURRENT_TIMESTAMP)");
                    $today_WeakService=0;
                    if(count($countToday_WeakServices)>0){
                        $today_WeakService=$countToday_WeakServices[0]->CountWeakService;
                    }

                }

                if($general->id==29){
                    // تعداد فاکتور
                    $count_all_Factors=DB::select(" SELECT COUNT(SnFact) AS countFactor,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper>'$EMPTYDATEHEJRI'
                    AND SnDriver=$adminId	GROUP BY SnDriver");
                    if(count($count_all_Factors)>0){
                        $count_all_Factor=$count_all_Factors[0]->countFactor;
                    }

                    $today_factors=DB::select(" SELECT COUNT(SnFact) AS countFactor,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper='$todayDate'
                    AND SnDriver=$adminId GROUP BY SnDriver");
                    
                    if(count($today_factors)>0){
                        $today_factor=$today_factors[0]->countFactor;
                    }
                    
                    $count_all_Factor_Bonus=((int)($count_all_Factor/$general->limitAmount)) * $general->Bonus;
                    $all_bonus_since_Empty+=$count_all_Factor_Bonus;
                }
                if($general->id==30){
                    $sum_all_moneis=DB::select("SELECT SUM(NetPriceHDS) AS SumAllMoney,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster JOIN Shop.dbo.FactorHDS on SnFact=FactorHDS.SerialNoHDS WHERE BargiryHDS.CompanyNo=5 AND FactorHDS.FiscalYear=1399 AND DatePeaper>'$EMPTYDATEHEJRI'
                    AND SnDriver=$adminId	GROUP BY SnDriver");
                    if(count($sum_all_moneis)>0){
                        $sum_all_money=$sum_all_moneis[0]->SumAllMoney;
                    }

                    $sum_today_moneis=DB::select("SELECT SUM(NetPriceHDS) AS SumTodayMoney,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster JOIN Shop.dbo.FactorHDS on SnFact=FactorHDS.SerialNoHDS WHERE BargiryHDS.CompanyNo=5 AND FactorHDS.FiscalYear=1399 AND DatePeaper='$todayDate'
                    AND SnDriver=$adminId	GROUP BY SnDriver");
                    if(count($sum_today_moneis)>0){
                        $sum_today_money=$sum_today_moneis[0]->SumTodayMoney;
                    }

                    $sum_all_Money_Bonus=((int)(($sum_all_money/10)/$general->limitAmount)) * $general->Bonus;
                    $all_bonus_since_Empty+=$sum_all_Money_Bonus;
                    $bonus_all_money=$sum_all_Money_Bonus;
                }
                //راننده ها
                $general->count_aghlam_Today=$count_New_buy_Today;
                $general->count_All_Factor=$count_all_Factor;
                $general->count_All_Factor_Today=$today_factor;
                $general->count_All_Install=0;
                $general->count_All_aghlam=$count_All_aghlam;
                $general->count_aghlam_today=$count_aghlam_today;
                $general->count_All_StrongService=$all_Strong_Service;
                $general->count_All_MediumService=$all_Medium_Service;
                $general->count_All_WeakService=$count_all_WeakService;
                $general->countToday_StrongService=$countToday_StrongService;
                $general->today_MediumService=$today_MediumService;
                $general->today_WeakService=$today_WeakService;

                //مال پشتیبان ها
                $general->count_New_buy_Today=0;
                $general->count_All_New_buys=0;
                $general->sum_all_money=$sum_all_money;
                $general->sum_today_money=$sum_today_money;
                $general->count_all_Factor=$count_all_Factor;
                $general->today_all_Factor=$today_factor;
                
            }

            $driverBonusInfo=self::getDriverActionInfo($adminSecondId);
            

        }
        //محاسبه امتیازات اضافی بازاریابها
        $all_monthly_bonuses=0;
        $historyExist=DB::select("select sum(positiveBonus)-sum(negativeBonus) as sumAllBonus from CRM.dbo.crm_adminUpDownBonus  where adminId=$adminId and isUsed=0");
        if($historyExist){
            $all_monthly_bonuses=$historyExist[0]->sumAllBonus;
        }

        $all_bonus_since_Empty+=$all_monthly_bonuses;

        $selfHistory=DB::table("CRM.dbo.crm_adminHistory")->where('adminId',$adminSecondId)->get();

        if($exactAdminInfo->poshtibanType!=4){
            //ارزیابی تارگت‌ها
            foreach($targets as $target){
                //تارگت‌های اقلام خرید
                if($target->SnBase==1 or $target->SnBase==4 or $target->SnBase==7){
                    if($count_All_aghlam >= $target->thirdTarget){
                        $aghlamComTg="تارگیت سوم";
                        $aghlamComTgBonus=$target->thirdTargetBonus;
                    }else{
                        if($count_All_aghlam >= $target->secondTarget){
                            $aghlamComTg="تارگیت دوم";
                            $aghlamComTgBonus=$target->secondTargetBonus;
                        }else{
                            if($count_All_aghlam >= $target->firstTarget){
                                $aghlamComTg="تارگیت اول";
                                $aghlamComTgBonus=$target->firstTargetBonus;
                            }
                        }
                    }
                }
                if(count($allMoney_till_now)>0){
                    //تارگت‌های مبلغ خرید
                    if($target->SnBase==2 or $target->SnBase==5 or $target->SnBase==8){
                        if(($allMoney_till_now[0]->SumOfMoney/10) >= $target->thirdTarget){
                            $monyComTg="تارگیت سوم";
                            $monyComTgBonus=$target->thirdTargetBonus;
                        }else{
                            if(($allMoney_till_now[0]->SumOfMoney/10) >= $target->secondTarget){
                                $monyComTg="تارگیت دوم";
                                $monyComTgBonus=$target->secondTargetBonus;
                            }else{
                                if(($allMoney_till_now[0]->SumOfMoney/10) >= $target->firstTarget){
                                    $monyComTg="تارگیت اول";
                                    $monyComTgBonus=$target->firstTargetBonus;
                                }
                            }
                        }
                    }
                }
                    //تارگت‌های تعداد زنده
                if($target->SnBase==3 or $target->SnBase==6 or $target->SnBase==9){
                    if($count_All_New_buys >= $target->thirdTarget){
                        $countBuyComTg="تارگیت سوم";
                        $countBuyComTgBonus=$target->thirdTargetBonus;
                    }else{
                        if($count_All_New_buys >= $target->secondTarget){
                            $countBuyComTg="تارگیت دوم";
                            $countBuyComTgBonus=$target->secondTargetBonus;
                        }else{
                            if($count_All_New_buys >= $target->firstTarget){
                                $countBuyComTg="تارگیت اول";
                                $countBuyComTgBonus=$target->firstTargetBonus;
                            }
                        }
                    }
                }
            }
        }else{
        }
        return view("poshtiban.poshtibanAction",
        ['specialBonuses'=>$generalBonuses,
        'adminId'=>$adminSecondId,
        'exactAdminInfo'=>$exactAdminInfo,
        'all_bonus_since_Empty'=>$all_bonus_since_Empty,
        'count_All_aghlam'=>$count_All_aghlam,
        'count_All_Install'=>$count_All_Install,
        'count_All_New_buys'=>$count_All_New_buys,
        'sum_all_money'=>$sum_all_money,
        'bonus_All_aghlam'=>$bonus_All_aghlam,
        'bonus_All_Install'=>$bonus_All_Install,
        'bonus_All_New_buys'=>$bonus_All_New_buys,
        'bonus_all_money'=>$bonus_all_money,
        'emptydate'=>$EMPTYDATE,
        'selfHistory'=>$selfHistory,
        'all_monthly_bonuses'=>$all_monthly_bonuses,
        'aghlamComTg'=>$aghlamComTg,
        'monyComTg'=>$monyComTg,
        'countBuyComTg'=>$countBuyComTg,
        'istallComTg'=>$istallComTg,
        'driverBonusInfo'=>$driverBonusInfo,
        'poshtibanType'=>$poshtibanType,
        'count_all_Factor'=>$count_all_Factor]);
    }
    public function getTodayBuyAghlamPoshtiban(Request $request)
    {
        $adminId=$request->get("adminID");

        return Response::json(self::getTodayAghlamByAdmin($adminId));
    }
    public function getAllBuyAghlamPoshtiban(Request $request)
    {
        $adminId=$request->get("adminId");
        $emptyDate=$request->get("emptydate");

        return Response::json(self::getAllBuyAghlamByAdmin($adminId,$emptyDate));
    }
    public function getTodayAghlamByAdmin($adminId)
    {//کالا های خریده شده توسط پشتیبان فروش
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');

        $aghlams=DB::select("SELECT * FROM(
                    SELECT count(SnGood) as countGoods,admin_id,SnGood from (
                    SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood,CustomerSn from(
                    SELECT * FROM(
                    SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood,CustomerSn FROM Shop.dbo.FactorHDS
                    JOIN Shop.dbo.FactorBYS  on FactorHDS.SerialNoHDS=FactorBYS.SnFact WHERE FactDate='$todayDate' and FactType=3)a
                    )g  group by SnGood,CustomerSn)c
                    join (SELECT * FROM CRM.dbo.crm_customer_added WHERE returnState=0 and admin_id=$adminId)d on c.CustomerSn=d.customer_id)f group by admin_id,SnGood
                    )a join (SELECT GoodName,GoodSn FROM Shop.dbo.PubGoods)f on a.SnGood=f.GoodSn");
        return $aghlams;
    }
    public function getAllBuyMoneyPoshtiban(Request $request)
    {
        $adminId=$request->get("adminId");
        $emptyDate=$request->get("adminDate");
        return Response::json(self::getAllCustomerBuyByAdmin($adminId,$emptyDate));
    }

    public function getAllBuyMoneyTodayPoshtiban(Request $request)
    {
        $adminId=$request->get("adminId");
        return Response::json(self::getAllCustomerBuyByAdminToday($adminId));
    }

    public function getAllCustomerBuyByAdmin($adminId,$emptyDate)
    {
        $emptyHijri=self::getHejriDate($emptyDate);
        $customers=DB::select("SELECT SumOfMoney,Name,PSN FROM(
            SELECT SUM(NetPriceHDS) AS SumOfMoney,CustomerSn FROM Shop.dbo.factorHds
            JOIN (select * from CRM.dbo.crm_customer_added where returnState=0)d ON CustomerSN=d.customer_id
            WHERE FactType=3 AND d.admin_id=$adminId and FactDate>='$emptyHijri' GROUP BY CustomerSn)a 
            JOIN Shop.dbo.Peopels ON a.CustomerSn=PSN");
        return $customers;
    }
    public function getAllCustomerBuyByAdminToday($adminId)
    {
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $customers=DB::select("SELECT SumOfMoney,Name,PSN FROM(
            SELECT SUM(NetPriceHDS) AS SumOfMoney,CustomerSn FROM Shop.dbo.factorHds
            JOIN (select * from CRM.dbo.crm_customer_added where returnState=0)d ON CustomerSN=d.customer_id
            WHERE FactType=3 AND d.admin_id=$adminId and FactDate='".$todayDate."' GROUP BY CustomerSn)a 
            JOIN Shop.dbo.Peopels ON a.CustomerSn=PSN");
        return $customers;
    }
    public function getAllBuyAghlamByAdmin($adminId,$emptyDate)
    {//تمام کالاهای خریده شده توسط ادمین بعد از تخلیه
        $emptyHijri=self::getHejriDate($emptyDate);
        $aghlams=DB::select("SELECT * FROM(
                    SELECT count(SnGood) as countGoods,admin_id,SnGood from (
                    SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood,CustomerSn from(
                    SELECT * FROM(
                    SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood,CustomerSn FROM Shop.dbo.FactorHDS
                    JOIN Shop.dbo.FactorBYS  on FactorHDS.SerialNoHDS=FactorBYS.SnFact WHERE FactDate>='$emptyHijri' and FactType=3)a
                    )g  group by SnGood,CustomerSn)c
                    join (SELECT * FROM CRM.dbo.crm_customer_added WHERE returnState=0 and admin_id=$adminId)d on c.CustomerSn=d.customer_id)f group by admin_id,SnGood
                    )a join (SELECT GoodName,GoodSn FROM Shop.dbo.PubGoods)f on a.SnGood=f.GoodSn");
        return $aghlams;
        
    }
    public function getAllNewBuyPoshtiban(Request $request)
    {
        $adminId=$request->get("adminId");
        $emptyDate=$request->get("emptyDate");
        return Response::json(self::getAllCustomerBuyByAdmin($adminId,$emptyDate));
    }
    public function getAllNewTodayBuyPoshtiban(Request $request)
    {
        $adminId=$request->get("adminId");
        
        return Response::json(self::getAllNewCustomerTodayBuyByAdmin($adminId));
    }

    public function getAllNewCustomerTodayBuyByAdmin($adminId)
    {
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $customers=DB::select("SELECT * FROM(
            SELECT * FROM(
                        SELECT MAX(FactDate)as lastTime,CustomerSn,sum(NetPriceHDS) as totalMoney  from Shop.dbo.FactorHDS where FactDate='$todayDate' group by CustomerSn )A
                        JOIN (SELECT customer_id from CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)c on A.CustomerSn=c.customer_id)B JOIN Shop.dbo.Peopels On B.customer_id=Peopels.PSN");
        return $customers;
       
    }

    public function getHejriDate($miladiDate)
    {
        $hijriDate=Jalalian::fromCarbon(Carbon::createFromFormat('Y-m-d', $miladiDate))->format('Y/m/d');
        return $hijriDate;
    }

    public function getPoshtibanActionInfo($poshtibanID){

        $adminId=$poshtibanID;
        $exactAdminInfo=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->get()[0];
        $generalBonuses;
        //
        $count_All_aghlam=0;
        $istallComTg="هیچکدام";
        $aghlamComTg="هیچکدام";
        $monyComTg="هیچکدام";
        $countBuyComTg="هیچکدام";
        //تارگت ها تکمیل شده
        $istallComTgBonus=0;
        $aghlamComTgBonus=0;
        $monyComTgBonus=0;
        $countBuyComTgBonus=0;
		//برای همان روز
        
        $count_New_buy_Today=0;
        $count_aghlam_today=0;
        $sum_today_money=0;
		//در طول زمان بعد از تخلیه کاربر
        $all_bonus_since_Empty=0;
        $count_All_aghlam=0;
        $count_All_Install=0;
        $count_All_New_buys=0;
        $sum_all_money=0;
		//امتیازات این ماه بعد از تخلیه
        $bonus_All_aghlam=0;
        $bonus_All_Install=0;
        $bonus_All_New_buys=0;
        $bonus_all_money=0;
        //برای تعیین تاریخ از روی جدول تخلیه
        $EMPTYDATE='2022-11-11';
        $EMPTYDATEHEJRI='1401/01/01';

        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $emptyDateInfo=DB::select("SELECT CONVERT(DATE,timeStamp) AS emptyDate FROM CRM.dbo.crm_adminHistory WHERE id=(SELECT MAX(id) FROM CRM.dbo.crm_adminHistory WHERE adminId=$adminId)");
        if($emptyDateInfo){
            $EMPTYDATE=$emptyDateInfo[0]->emptyDate;
        }
        $EMPTYDATEHEJRI=Jalalian::fromCarbon(Carbon::createFromFormat('Y-m-d', $EMPTYDATE))->format('Y/m/d');
        if($exactAdminInfo->poshtibanType==3){
            $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("userType",1)->get();
        }
        if($exactAdminInfo->poshtibanType==2){
            $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("userType",2)->get();
        }
        if($exactAdminInfo->poshtibanType==1){
            $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("userType",3)->get();
        }

        foreach($generalBonuses as $general){
            
            
            if($general->id==1 or $general->id==4 or $general->id==7 or $general->id==10){
                //اقلام
                //اقلام
                $count_All_aghlamR=DB::select("SELECT COUNT(SnGood) as CountGood,SnDriver FROM(
                    SELECT  SnGood,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster
                    JOIN Shop.dbo.FactorBYS ON BargiryBYS.SnFact=FactorBYS.SnFact
                    WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper>'$EMPTYDATEHEJRI')A WHERE SnDriver=$adminId  group by SnDriver");
                    if(count($count_All_aghlamR)>0){
                    $count_All_aghlam=$count_All_aghlamR[0]->CountGood;
                    }

                    $count_aghlam_todayR=DB::select("SELECT COUNT(SnGood) as CountGood,SnDriver FROM(
                                            SELECT  SnGood,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster
                                            JOIN Shop.dbo.FactorBYS ON BargiryBYS.SnFact=FactorBYS.SnFact
                                            WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper='$todayDate')A WHERE SnDriver=$adminId  group by SnDriver");

                                    if(count($count_aghlam_todayR)>0){
                                        $count_aghlam_today=$count_aghlam_todayR[0]->countAghlam;
                                    }

                                    $instAghlamBonus=((int)($count_All_aghlam/$general->limitAmount)) * $general->Bonus;
                                    $all_bonus_since_Empty+=$instAghlamBonus;
                                    $bonus_All_aghlam=$instAghlamBonus;
                }

            if($general->id==2 or $general->id==5 or $general->id==8 or $general->id==11){
                //مبلغ
                $allMoney_till_now=DB::select("SELECT SUM(NetPriceHDS) AS SumOfMoney,admin_id FROM Shop.dbo.factorHds
                JOIN (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)d ON factorHds.CustomerSn=d.customer_id
                WHERE FactType=3 and FactDate>='$EMPTYDATEHEJRI' GROUP BY admin_id");
                if(count($allMoney_till_now)>0){
                    $sum_all_money=$allMoney_till_now[0]->SumOfMoney;
                }

                $today_money=DB::select("SELECT SUM(NetPriceHDS) AS SumOfMoney,admin_Id FROM Shop.dbo.factorHds
                                        JOIN (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)d ON factorHds.CustomerSn=d.customer_id
                                        WHERE FactType=3 AND FactDate='$todayDate' GROUP BY admin_Id");
                
                if(count($today_money)>0){
                    $sum_today_money=$today_money[0]->SumOfMoney;
                }
                
                $allMoneyBonus=((int)($sum_all_money/10/$general->limitAmount)) * $general->Bonus;
                $all_bonus_since_Empty+=$allMoneyBonus;
                $bonus_all_money=$allMoneyBonus;
            }

            if($base->id==3 or $base->id==6 or $base->id==9 or $base->id==12){
                
                //زنده کردن مشتری
                //امروز
                $countNewBuys=0;
                $count_New_buy_Today=DB::select("SELECT count(CustomerSn) as countLive FROM(
                    select MAX(FactDate)as lastTime,CustomerSn  from Shop.dbo.FactorHDS where FactDate='$todayDate' group by CustomerSn )A
                    JOIN (select customer_id from CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)c on A.CustomerSn=c.customer_id");
                
                if(count($count_New_buy_Today)<1){
                    $count_New_buy_Today=0;
                }else{
                    $count_New_buy_Today=$count_New_buy_Today[0]->countLive;
                }
                //همه           
                $count_All_New_buysR=DB::select("SELECT count(CustomerSn) as countLive FROM(
                    select MAX(FactDate)as lastTime,CustomerSn  from Shop.dbo.FactorHDS where FactDate>='$EMPTYDATEHEJRI' group by CustomerSn )A
                    JOIN (select customer_id from CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)c on A.CustomerSn=c.customer_id");
                
                if(count($count_All_New_buysR)>0){
                    $countNewBuys=$count_All_New_buysR[0]->countLive;
                }
                
                $allBuyBonus=((int)($countNewBuys/$general->limitAmount)) * $general->Bonus;
                $all_bonus_since_Empty+=$allBuyBonus;
                $bonus_All_New_buys=$allBuyBonus;
                $count_All_New_buys+=$countNewBuys;
                
            }
            //buys
            $general->count_New_buy_Today=$count_New_buy_Today;
            $general->count_All_New_buys=$count_All_New_buys;
            $general->count_All_aghlam=$count_All_aghlam;
            $general->count_aghlam_today=$count_aghlam_today;
            $general->sum_all_money=$sum_all_money;
            $general->sum_today_money=$sum_today_money;
            
        }
        //محاسبه امتیازات اضافی بازاریابها
        $all_monthly_bonuses=0;
        $historyExist=DB::select("select sum(positiveBonus)-sum(negativeBonus) as sumAllBonus from CRM.dbo.crm_adminUpDownBonus  where adminId=$adminId and isUsed=0");
        if($historyExist){
            $all_monthly_bonuses=$historyExist[0]->sumAllBonus;
        }

        $all_bonus_since_Empty+=$all_monthly_bonuses;

        $selfHistory=DB::table("CRM.dbo.crm_adminHistory")->where('adminId',$adminId)->get();

        //اکمال تارگت های فروش
        $targets=DB::select("SELECT * FROM CRM.dbo.crm_targets");
        //ارزیابی تارگت‌ها
        foreach($targets as $target){
            //تارگت‌های نصب
            if($target->id==4){
                if($count_All_Install >= $target->thirdTarget){
                    $istallComTg="تارگیت سوم";
                    $istallComTgBonus=$target->thirdTargetBonus;
                }else{
                    if($count_All_Install >= $target->secondTarget){
                        $istallComTg="تارگیت دوم";
                        $istallComTgBonus=$target->scondTargetBonus;
                    }else{
                        if($count_All_Install >= $target->firstTarget){
                            $istallComTg="تارگیت اول";
                            $istallComTgBonus=$target->firstTargetBonus;
                        }
                    }
                }
            }
                //تارگت‌های تعداد خرید اولیه
            if($target->id==6){
                if($count_All_New_buys >= $target->thirdTarget){
                    $countBuyComTg="تارگیت سوم";
                    $countBuyComTgBonus=$target->thirdTargetBonus;
                }else{
                    if($count_All_New_buys >= $target->secondTarget){
                        $countBuyComTg="تارگیت دوم";
                        $countBuyComTgBonus=$target->thirdTargetBonus;
                    }else{
                        if($count_All_New_buys >= $target->firstTarget){
                            $countBuyComTg="تارگیت اول";
                            $countBuyComTgBonus=$target->thirdTargetBonus;
                        }
                    }
                }
            }
            if(count($allMoney_till_now)>0){
                //تارگت‌های مبلغ خرید
                if($target->id==7){
                    if(($allMoney_till_now[0]->SumOfMoney/10) >= $target->thirdTarget){
                        $monyComTg="تارگیت سوم";
                        $monyComTgBonus=$target->thirdTargetBonus;
                    }else{
                        if(($allMoney_till_now[0]->SumOfMoney/10) >= $target->secondTarget){
                            $monyComTg="تارگیت دوم";
                            $monyComTgBonus=$target->thirdTargetBonus;
                        }else{
                            if(($allMoney_till_now[0]->SumOfMoney/10) >= $target->firstTarget){
                                $monyComTg="تارگیت اول";
                                $monyComTgBonus=$target->thirdTargetBonus;
                            }
                        }
                    }
                }
            }
                //تارگت‌های اقلام خرید
            if($target->id==5){
                if($count_All_aghlam >= $target->thirdTarget){
                    $aghlamComTg="تارگیت سوم";
                    $aghlamComTgBonus=$target->thirdTargetBonus;
                }else{
                    if($count_All_aghlam >= $target->secondTarget){
                        $aghlamComTg="تارگیت دوم";
                        $aghlamComTgBonus=$target->thirdTargetBonus;
                    }else{
                        if($count_All_aghlam >= $target->firstTarget){
                            $aghlamComTg="تارگیت اول";
                            $aghlamComTgBonus=$target->firtTargetBonus;
                        }
                    }
                }
            }
        }

        $all_bonus_since_Empty+=($countBuyComTgBonus+$monyComTgBonus+$aghlamComTgBonus);
        return  ['specialBonuses'=>$generalBonuses,
        'adminId'=>$adminId,
        'exactAdminInfo'=>$exactAdminInfo,
        'all_bonus_since_Empty'=>$all_bonus_since_Empty,
        'count_All_aghlam'=>$count_All_aghlam,
        'count_All_Install'=>$count_All_Install,
        'count_All_New_buys'=>$count_All_New_buys,
        'sum_all_money'=>$sum_all_money,
        'bonus_All_aghlam'=>$bonus_All_aghlam,
        'bonus_All_Install'=>$bonus_All_Install,
        'bonus_All_New_buys'=>$bonus_All_New_buys,
        'bonus_all_money'=>$bonus_all_money,
        'emptydate'=>$EMPTYDATE,
        'selfHistory'=>$selfHistory,
        'all_monthly_bonuses'=>$all_monthly_bonuses,
        'aghlamComTg'=>$aghlamComTg,
        'monyComTg'=>$monyComTg,
        'countBuyComTg'=>$countBuyComTg,
        'istallComTg'=>$istallComTg];
    }

    public function getPoshtibanActionInformation($adminId)
    {
        
        $adminId=$adminId;//آی دی پشتیبان
        $exactAdminInfo=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->get()[0];//اطلاعات پشتیبان گرفته می شود
        $generalBonuses;//لیست امتیازات بر اساس نوعیت آن
        $targets;//تارگت ها
        //تارگت های که قرار است تکمیل شود.
        $count_All_aghlam=0;
        $istallComTg="هیچکدام";
        $aghlamComTg="هیچکدام";
        $monyComTg="هیچکدام";
        $countBuyComTg="هیچکدام";
        //امتیازات تارگت های تکمیل شده
        $istallComTgBonus=0;
        $aghlamComTgBonus=0;
        $monyComTgBonus=0;
        $countBuyComTgBonus=0;
		//برای همان روز
        
        $count_New_buy_Today=0;
        $count_aghlam_today=0;
        $sum_today_money=0;
		//در طول زمان بعد از تخلیه کاربر
        $all_bonus_since_Empty=0;
        $count_All_aghlam=0;
        $count_All_Install=0;
        $count_All_New_buys=0;
        $sum_all_money=0;
		//امتیازات این ماه بعد از تخلیه
        $bonus_All_aghlam=0;
        $bonus_All_Install=0;
        $bonus_All_New_buys=0;
        $bonus_all_money=0;
        //برای تعیین تاریخ از روی جدول تخلیه
        $EMPTYDATE='2022-11-11';
        $EMPTYDATEHEJRI='1401/01/01';

        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $emptyDateInfo=DB::select("SELECT CONVERT(DATE,timeStamp) AS emptyDate FROM CRM.dbo.crm_adminHistory WHERE id=(SELECT MAX(id) FROM CRM.dbo.crm_adminHistory WHERE adminId=$adminId)");
        if($emptyDateInfo){
            $EMPTYDATE=$emptyDateInfo[0]->emptyDate;
        }
        $EMPTYDATEHEJRI=Jalalian::fromCarbon(Carbon::createFromFormat('Y-m-d', $EMPTYDATE))->format('Y/m/d');
        if($exactAdminInfo->poshtibanType==3){
            $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("userType",3)->get();
                    //اکمال تارگت های فروش
            $targets=DB::select("SELECT * FROM CRM.dbo.crm_generalTargets where userType=3");
        }
        if($exactAdminInfo->poshtibanType==2){
            $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("userType",2)->get();
                    //اکمال تارگت های فروش
            $targets=DB::select("SELECT * FROM CRM.dbo.crm_generalTargets where userType=2");
        }
        if($exactAdminInfo->poshtibanType==1){
            $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("userType",1)->get();
                    //اکمال تارگت های فروش
            $targets=DB::select("SELECT * FROM CRM.dbo.crm_generalTargets where userType=1");
        }
        
        foreach($generalBonuses as $general){
            
            
            if($general->id==1 or $general->id==4 or $general->id==7 or $general->id==10){
                //اقلام
                $count_All_aghlamR=DB::select("SELECT COUNT(SnGood) as CountGood,SnDriver FROM(
                    SELECT  SnGood,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster
                    JOIN Shop.dbo.FactorBYS ON BargiryBYS.SnFact=FactorBYS.SnFact
                    WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper>'$EMPTYDATEHEJRI')A WHERE SnDriver=$adminId  group by SnDriver");
                    if(count($count_All_aghlamR)>0){
                    $count_All_aghlam=$count_All_aghlamR[0]->CountGood;
                    }

                    $count_aghlam_todayR=DB::select("SELECT COUNT(SnGood) as CountGood,SnDriver FROM(
                                            SELECT  SnGood,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster
                                            JOIN Shop.dbo.FactorBYS ON BargiryBYS.SnFact=FactorBYS.SnFact
                                            WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper='$todayDate')A WHERE SnDriver=$adminId  group by SnDriver");

                                    if(count($count_aghlam_todayR)>0){
                                        $count_aghlam_today=$count_aghlam_todayR[0]->countAghlam;
                                    }

                                    $instAghlamBonus=((int)($count_All_aghlam/$general->limitAmount)) * $general->Bonus;
                                    $all_bonus_since_Empty+=$instAghlamBonus;
                                    $bonus_All_aghlam=$instAghlamBonus;
                }

            if($general->id==2 or $general->id==5 or $general->id==8 or $general->id==11){
                //مبلغ
                $allMoney_till_now=DB::select("SELECT SUM(NetPriceHDS) AS SumOfMoney,admin_id FROM Shop.dbo.factorHds
                                                JOIN (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)d ON factorHds.CustomerSn=d.customer_id
                                                WHERE FactType=3 and FactDate>='$EMPTYDATEHEJRI' GROUP BY admin_id");
                if(count($allMoney_till_now)>0){
                    $sum_all_money=$allMoney_till_now[0]->SumOfMoney;
                }

                $today_money=DB::select("SELECT SUM(NetPriceHDS) AS SumOfMoney,admin_Id FROM Shop.dbo.factorHds
                                        JOIN (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)d ON factorHds.CustomerSn=d.customer_id
                                        WHERE FactType=3 AND FactDate='$todayDate' GROUP BY admin_Id");
                
                if(count($today_money)>0){
                    $sum_today_money=$today_money[0]->SumOfMoney;
                }
                
                $allMoneyBonus=((int)($sum_all_money/10/$general->limitAmount)) * $general->Bonus;
                $all_bonus_since_Empty+=$allMoneyBonus;
                $bonus_all_money=$allMoneyBonus;
            }

            if($general->id==3 or $general->id==6 or $general->id==9){
                //زنده کردن مشتری
                //امروز
                $countNewBuys=0;
                $count_New_buy_Today=DB::select("SELECT count(CustomerSn) as countLive FROM(
                    select MAX(FactDate)as lastTime,CustomerSn  from Shop.dbo.FactorHDS where FactDate='$todayDate' group by CustomerSn )A
                    JOIN (select customer_id from CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)c on A.CustomerSn=c.customer_id");
                
                if(count($count_New_buy_Today)<1){
                    $count_New_buy_Today=0;
                }else{
                    $count_New_buy_Today=$count_New_buy_Today[0]->countLive;
                }
                //همه           
                $count_All_New_buysR=DB::select("SELECT count(CustomerSn) as countLive FROM(
                    select MAX(FactDate)as lastTime,CustomerSn  from Shop.dbo.FactorHDS where FactDate>='$EMPTYDATEHEJRI' group by CustomerSn )A
                    JOIN (select customer_id from CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)c on A.CustomerSn=c.customer_id");
                
                if(count($count_All_New_buysR)>0){
                    $countNewBuys=$count_All_New_buysR[0]->countLive;
                }
                
                $allBuyBonus=((int)($countNewBuys/$general->limitAmount)) * $general->Bonus;

                $all_bonus_since_Empty+=$allBuyBonus;
                $bonus_All_New_buys=$allBuyBonus;
                $count_All_New_buys+=$countNewBuys;
                
            }
            //buys
            $general->count_New_buy_Today=$count_New_buy_Today;
            $general->count_All_New_buys=$count_All_New_buys;
            $general->count_All_aghlam=$count_All_aghlam;
            $general->count_aghlam_today=$count_aghlam_today;
            $general->sum_all_money=$sum_all_money;
            $general->sum_today_money=$sum_today_money;
            
        }
        //محاسبه امتیازات اضافی بازاریابها
        $all_monthly_bonuses=0;
        $historyExist=DB::select("select sum(positiveBonus)-sum(negativeBonus) as sumAllBonus from CRM.dbo.crm_adminUpDownBonus  where adminId=$adminId and isUsed=0");
        if($historyExist){
            $all_monthly_bonuses=$historyExist[0]->sumAllBonus;
        }

        $all_bonus_since_Empty+=$all_monthly_bonuses;

        $selfHistory=DB::table("CRM.dbo.crm_adminHistory")->where('adminId',$adminId)->get();


        //ارزیابی تارگت‌ها
        foreach($targets as $target){


            //تارگت‌های اقلام خرید
            if($target->SnBase==1 or $target->SnBase==4 or $target->SnBase==7){
                if($count_All_aghlam >= $target->thirdTarget){
                    $aghlamComTg="تارگیت سوم";
                    $aghlamComTgBonus=$target->thirdTargetBonus;
                }else{
                    if($count_All_aghlam >= $target->secondTarget){
                        $aghlamComTg="تارگیت دوم";
                        $aghlamComTgBonus=$target->secondTargetBonus;
                    }else{
                        if($count_All_aghlam >= $target->firstTarget){
                            $aghlamComTg="تارگیت اول";
                            $aghlamComTgBonus=$target->firstTargetBonus;
                        }
                    }
                }
            }


            if(count($allMoney_till_now)>0){
                //تارگت‌های مبلغ خرید
                if($target->SnBase==2 or $target->SnBase==5 or $target->SnBase==8){
                    if(($allMoney_till_now[0]->SumOfMoney/10) >= $target->thirdTarget){
                        $monyComTg="تارگیت سوم";
                        $monyComTgBonus=$target->thirdTargetBonus;
                    }else{
                        if(($allMoney_till_now[0]->SumOfMoney/10) >= $target->secondTarget){
                            $monyComTg="تارگیت دوم";
                            $monyComTgBonus=$target->secondTargetBonus;
                        }else{
                            if(($allMoney_till_now[0]->SumOfMoney/10) >= $target->firstTarget){
                                $monyComTg="تارگیت اول";
                                $monyComTgBonus=$target->firstTargetBonus;
                            }
                        }
                    }
                }
            }


                //تارگت‌های تعداد زنده
            if($target->SnBase==3 or $target->SnBase==6 or $target->SnBase==9){
                if($count_All_New_buys >= $target->thirdTarget){
                    $countBuyComTg="تارگیت سوم";
                    $countBuyComTgBonus=$target->thirdTargetBonus;
                }else{
                    if($count_All_New_buys >= $target->secondTarget){
                        $countBuyComTg="تارگیت دوم";
                        $countBuyComTgBonus=$target->secondTargetBonus;
                    }else{
                        if($count_All_New_buys >= $target->firstTarget){
                            $countBuyComTg="تارگیت اول";
                            $countBuyComTgBonus=$target->firstTargetBonus;
                        }
                    }
                }
            }
        }
        $all_bonus_since_Empty+=($countBuyComTgBonus+$monyComTgBonus+$aghlamComTgBonus);
        return ['specialBonuses'=>$generalBonuses,
        'adminId'=>$adminId,
        'exactAdminInfo'=>$exactAdminInfo,
        'all_bonus_since_Empty'=>$all_bonus_since_Empty,
        'count_All_aghlam'=>$count_All_aghlam,
        'count_All_Install'=>$count_All_Install,
        'count_All_New_buys'=>$count_All_New_buys,
        'sum_all_money'=>$sum_all_money,
        'bonus_All_aghlam'=>$bonus_All_aghlam,
        'bonus_All_Install'=>$bonus_All_Install,
        'bonus_All_New_buys'=>$bonus_All_New_buys,
        'bonus_all_money'=>$bonus_all_money,
        'emptydate'=>$EMPTYDATE,
        'selfHistory'=>$selfHistory,
        'all_monthly_bonuses'=>$all_monthly_bonuses,
        'aghlamComTg'=>$aghlamComTg,
        'monyComTg'=>$monyComTg,
        'countBuyComTg'=>$countBuyComTg,
        'istallComTg'=>$istallComTg,
        'countBuyComTgBonus'=>$countBuyComTgBonus,
        'monyComTgBonus'=>$monyComTgBonus,
        'aghlamComTgBonus'=>$aghlamComTgBonus
    ];
    }

    public function getDriverActionInfo($adminId){
        $count_All_aghlam=0;
        $istallComTg="هیچکدام";
        $aghlamComTg="هیچکدام";
        $monyComTg="هیچکدام";
        $countBuyComTg="هیچکدام";
        $strongServiceComTg="هیچکدام";
        $mediumServiceComTg="هیچکدام";
        $weakServiceComTg="هیچکدام";
        $countFactorComTg="هیچکدام";
        //تارگت ها تکمیل شده
        $istallComTgBonus=0;
        $aghlamComTgBonus=0;
        $monyComTgBonus=0;
        $countBuyComTgBonus=0;
        $strongServiceComTgBonus=0;
        $mediumServiceComTgBonus=0;
        $countFactorComTgBonus=0;
		//در طول زمان بعد از تخلیه کاربر
        $all_bonus_since_Empty=0;
        $count_All_aghlam=0;
        $count_All_Install=0;
        $count_All_New_buys=0;
        $count_all_StrongService=0;
        $countToday_StrongService=0;
        $weakServiceComTgBonus=0;
        $today_MediumService=0;
        $today_WeakService=0;
        $all_Weak_Service=0;
        $all_monthly_bonuses=0;
        $today_MediumService=0;
        $count_all_MediumService=0;
        $count_all_WeakService=0;
        $count_all_Factor=0;
        $sum_all_money=0;
        $sum_today_money=0;
        $all_Strong_Service=0;
        $all_Medium_Service=0;
		//امتیازات این ماه بعد از تخلیه
        $bonus_All_aghlam=0;
        $bonus_All_Install=0;
        $all_Strong_Service_Bonus=0;
        $all_Weak_Service_Bonus=0;
        $all_Medium_Service_Bonus=0;
        $bonus_All_New_buys=0;
        $bonus_all_money=0;
        $EMPTYDATE='2022-11-11';
        $EMPTYDATEHEJRI='1401/01/01';

        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $emptyDateInfo=DB::select("SELECT CONVERT(DATE,timeStamp) AS emptyDate FROM CRM.dbo.crm_adminHistory WHERE id=(SELECT MAX(id) FROM CRM.dbo.crm_adminHistory WHERE adminId=$adminId)");
        if($emptyDateInfo){
            $EMPTYDATE=$emptyDateInfo[0]->emptyDate;
        }
        $exactAdminInfo=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->get()[0];
        $adminId=$exactAdminInfo->driverId;
        $EMPTYDATEHEJRI=Jalalian::fromCarbon(Carbon::createFromFormat('Y-m-d', $EMPTYDATE))->format('Y/m/d');
        $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("userType",4)->get();
                //تارگت های راننده ها
        $targets=DB::select("SELECT * FROM CRM.dbo.crm_generalTargets where userType=4");
        foreach($generalBonuses as $general){
            if($general->id==21){
                //اقلام
                $count_All_aghlamR=DB::select("SELECT COUNT(SnGood) as CountGood,SnDriver FROM(
                                                SELECT  SnGood,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster
                                                JOIN Shop.dbo.FactorBYS ON BargiryBYS.SnFact=FactorBYS.SnFact
                                                WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper>'$EMPTYDATEHEJRI')A WHERE SnDriver=$adminId  group by SnDriver");
                if(count($count_All_aghlamR)>0){
                    $count_All_aghlam=$count_All_aghlamR[0]->CountGood;
                }

                $count_aghlam_todayR=DB::select("SELECT COUNT(SnGood) as CountGood,SnDriver FROM(
                                                    SELECT  SnGood,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster
                                                    JOIN Shop.dbo.FactorBYS ON BargiryBYS.SnFact=FactorBYS.SnFact
                                                    WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper='$todayDate')A WHERE SnDriver=$adminId  group by SnDriver");
                
                if(count($count_aghlam_todayR)>0){
                    $count_aghlam_today=$count_aghlam_todayR[0]->CountGood;
                }
                $instAghlamBonus=((int)($count_All_aghlam/$general->limitAmount)) * $general->Bonus;
                $all_bonus_since_Empty+=$instAghlamBonus;
                $bonus_All_aghlam=$instAghlamBonus;
            }

            if($general->id==23){
                //سرویس قوی

                $strongServices=DB::select("SELECT COUNT(ServiceSn) AS CountStrongService
                    FROM CRM.dbo.crm_driverservice where crm_driverservice.serviceType=1 and CONVERT(DATE,TimeStamp)>'$EMPTYDATE' and adminId=$adminId");
                if(count($strongServices)>0){
                    $all_Strong_Service= $strongServices[0]->CountStrongService;
                }

                $all_Strong_Service_Bonus=((int)($all_Strong_Service/$general->limitAmount)) * $general->Bonus;
                $all_bonus_since_Empty+=$all_Strong_Service_Bonus;

                $countToday_StrongServices=DB::select("SELECT COUNT(ServiceSn) AS CountStrongService
                    FROM CRM.dbo.crm_driverservice where crm_driverservice.serviceType=1 and adminId=$adminId
                    AND CONVERT(date,TimeStamp)=Convert(date,CURRENT_TIMESTAMP)");
                if(count($countToday_StrongServices)>0){
                    $countToday_StrongService=$countToday_StrongServices[0]->CountStrongService;
                }
                
            }

            if($general->id==26){
                // //سرویس متوسط
                $mediumServices=DB::select("SELECT COUNT(ServiceSn) AS CountMediumService
                    FROM CRM.dbo.crm_driverservice where crm_driverservice.serviceType=2 and CONVERT(DATE,TimeStamp)>'$EMPTYDATE' and adminId=$adminId");

                

                if(count($mediumServices)>0){
                    $all_Medium_Service=$mediumServices[0]->CountMediumService;
                }
                
                $all_Medium_Service_Bonus=((int)($all_Medium_Service/$general->limitAmount)) * $general->Bonus;
                $all_bonus_since_Empty+=$all_Medium_Service_Bonus;

                $countToday_MediumServices=DB::select("SELECT COUNT(ServiceSn) AS CountMediumService
                    FROM CRM.dbo.crm_driverservice where crm_driverservice.serviceType=2 and adminId=$adminId
                    AND CONVERT(date,TimeStamp)=Convert(date,CURRENT_TIMESTAMP)");
                if(count($countToday_MediumServices)>0){
                    $today_MediumService=$countToday_MediumServices[0]->CountMediumService;
                }
            }

            if($general->id==27){
                // //سرویس ضعیف
                $weakServices=DB::select("SELECT COUNT(ServiceSn) AS CountWeakService
                    FROM CRM.dbo.crm_driverservice where crm_driverservice.serviceType=3 and CONVERT(DATE,TimeStamp)>'$EMPTYDATE' and adminId=$adminId");
                
                if(count($weakServices)>0){
                    $all_Weak_Service=$weakServices[0]->CountWeakService;
                    
                }
                $all_Weak_Service_Bonus=((int)($all_Weak_Service/$general->limitAmount)) * $general->Bonus;
                $all_bonus_since_Empty+=$all_Weak_Service_Bonus;
                //امروز
                $countToday_WeakServices=DB::select("SELECT COUNT(ServiceSn) AS CountWeakService
                    FROM CRM.dbo.crm_driverservice where crm_driverservice.serviceType=3 and adminId=$adminId
                    AND CONVERT(date,TimeStamp)=Convert(date,CURRENT_TIMESTAMP)");
                $today_WeakService=0;
                if(count($countToday_WeakServices)>0){
                    $today_WeakService=$countToday_WeakServices[0]->CountWeakService;
                }

            }

            if($general->id==29){
                // تعداد فاکتور
                $count_all_Factors=DB::select(" SELECT COUNT(SnFact) AS countFactor,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper>'$EMPTYDATEHEJRI'
                AND SnDriver=$adminId	GROUP BY SnDriver");
                if(count($count_all_Factors)>0){
                    $count_all_Factor=$count_all_Factors[0]->countFactor;
                }

                $today_factors=DB::select(" SELECT COUNT(SnFact) AS countFactor,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper='$todayDate'
                AND SnDriver=$adminId GROUP BY SnDriver");
                
                if(count($today_factors)>0){
                    $today_factor=$today_factors[0]->countFactor;
                }
                
                $count_all_Factor_Bonus=((int)($count_all_Factor/$general->limitAmount)) * $general->Bonus;
                $all_bonus_since_Empty+=$count_all_Factor_Bonus;
            }

            if($general->id==30){
                $sum_all_moneis=DB::select("SELECT SUM(NetPriceHDS) AS SumAllMoney,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster JOIN Shop.dbo.FactorHDS on SnFact=FactorHDS.SerialNoHDS WHERE BargiryHDS.CompanyNo=5 AND FactorHDS.FiscalYear=1399 AND DatePeaper>'$EMPTYDATEHEJRI'
                AND SnDriver=$adminId	GROUP BY SnDriver");
                if(count($sum_all_moneis)>0){
                    $sum_all_money=$sum_all_moneis[0]->SumAllMoney;
                }

                $sum_today_moneis=DB::select("SELECT SUM(NetPriceHDS) AS SumTodayMoney,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster JOIN Shop.dbo.FactorHDS on SnFact=FactorHDS.SerialNoHDS WHERE BargiryHDS.CompanyNo=5 AND FactorHDS.FiscalYear=1399 AND DatePeaper='$todayDate'
                AND SnDriver=$adminId	GROUP BY SnDriver");
                if(count($sum_today_moneis)>0){
                    $sum_today_money=$sum_today_moneis[0]->SumTodayMoney;
                }

                $sum_all_Money_Bonus=((int)(($sum_all_money/10)/$general->limitAmount)) * $general->Bonus;
                $all_bonus_since_Empty+=$sum_all_Money_Bonus;
                $bonus_all_money=$sum_all_Money_Bonus;
            }
            //راننده ها
            $general->count_All_Factor=$count_all_Factor;
            $general->count_All_Install=0;
            $general->count_All_aghlam=$count_All_aghlam;
            $general->count_All_StrongService=$all_Strong_Service;
            $general->count_All_MediumService=$all_Medium_Service;
            $general->count_All_WeakService=$count_all_WeakService;
            $general->countToday_StrongService=$countToday_StrongService;
            $general->today_MediumService=$today_MediumService;
            $general->today_WeakService=$today_WeakService;

            //مال پشتیبان ها
            $general->count_New_buy_Today=0;
            $general->count_All_New_buys=0;
            $general->sum_all_money=$sum_all_money;
            $general->sum_today_money=$sum_today_money;
            
        }

        //ارزیابی تارگت‌ها
        foreach($targets as $target){
            //تارگت‌های اقلام خرید
            if($target->SnBase==13){
                if($count_All_aghlam >= $target->thirdTarget){
                    $aghlamComTg="تارگیت سوم";
                    $aghlamComTgBonus=$target->thirdTargetBonus;
                }else{
                    if($count_All_aghlam >= $target->secondTarget){
                        $aghlamComTg="تارگیت دوم";
                        $aghlamComTgBonus=$target->secondTargetBonus;
                    }else{
                        if($count_All_aghlam >= $target->firstTarget){
                            $aghlamComTg="تارگیت اول";
                            $aghlamComTgBonus=$target->firstTargetBonus;
                        }
                    }
                }
                $all_bonus_since_Empty+=$aghlamComTgBonus;
            }
            //تارگت‌های سرویس قوی
            if($target->SnBase==10){
                if($all_Strong_Service >= $target->thirdTarget){
                    $strongServiceComTg="تارگیت سوم";
                    $strongServiceComTgBonus=$target->thirdTargetBonus;
                }else{
                    if($all_Strong_Service >= $target->secondTarget){
                        $strongServiceComTg="تارگیت دوم";
                        $strongServiceComTgBonus=$target->secondTargetBonus;
                    }else{
                        if($all_Strong_Service >= $target->firstTarget){
                            $strongServiceComTg="تارگیت اول";
                            $strongServiceComTgBonus=$target->firstTargetBonus;
                        }
                    }
                }
                $all_bonus_since_Empty+=$strongServiceComTgBonus;
            }
                //تارگت‌های سرویس متوسط
            if($target->SnBase==12){
                if($all_Medium_Service >= $target->thirdTarget){
                    $mediumServiceComTg="تارگیت سوم";
                    $mediumServiceComTgBonus=$target->thirdTargetBonus;
                }else{
                    if($all_Medium_Service >= $target->secondTarget){
                        $mediumServiceComTg="تارگیت دوم";
                        $mediumServiceComTgBonus=$target->secondTargetBonus;
                    }else{
                        if($all_Medium_Service >= $target->firstTarget){
                            $mediumServiceComTg="تارگیت اول";
                            $mediumServiceComTgBonus=$target->firstTargetBonus;
                        }
                    }
                }
                $all_bonus_since_Empty+=$mediumServiceComTgBonus;
            }
            //تارگت‌های سرویس ضعیف
            if($target->SnBase==11){
                if($all_Weak_Service >= $target->thirdTarget){
                    $weakServiceComTg="تارگیت سوم";
                    $weakServiceComTgBonus=$target->thirdTargetBonus;
                }else{
                    if($all_Weak_Service >= $target->secondTarget){
                        $weakServiceComTg="تارگیت دوم";
                        $weakServiceComTgBonus=$target->secondTargetBonus;
                    }else{
                        if($all_Weak_Service >= $target->firstTarget){
                            $weakServiceComTg="تارگیت اول";
                            $weakServiceComTgBonus=$target->firstTargetBonus;
                        }
                    }
                }
                $all_bonus_since_Empty+=$weakServiceComTgBonus;
            }

            //تارگت‌های تعداد فاکتور
            if($target->SnBase==14){
                if($count_all_Factor >= $target->thirdTarget){
                    $countFactorComTg="تارگیت سوم";
                    $countFactorComTgBonus=$target->thirdTargetBonus;
                }else{
                    if($count_all_Factor >= $target->secondTarget){
                        $countFactorComTg="تارگیت دوم";
                        $countFactorComTgBonus=$target->secondTargetBonus;
                    }else{
                        if($count_all_Factor >= $target->firstTarget){
                            $countFactorComTg="تارگیت اول";
                            $countFactorComTgBonus=$target->firstTargetBonus;
                        }
                    }
                }
                $all_bonus_since_Empty+=$countFactorComTgBonus;
            }

            if($target->SnBase==15){
                if($sum_all_money/10 >= $target->thirdTarget){
                    $monyComTg="تارگیت سوم";
                    $monyComTgBonus=$target->thirdTargetBonus;
                }else{
                    if($sum_all_money/10 >= $target->secondTarget){
                        $monyComTg="تارگیت دوم";
                        $monyComTgBonus=$target->secondTargetBonus;
                    }else{
                        if($sum_all_money/10 >= $target->firstTarget){
                            $monyComTg="تارگیت اول";
                            $monyComTgBonus=$target->firstTargetBonus;
                        }
                    }
                }
                $all_bonus_since_Empty+=$monyComTgBonus;
            }
        }

        return [
        'all_bonus_since_Empty'=>$all_bonus_since_Empty,
        'count_All_aghlam'=>$count_All_aghlam,
        'count_All_Install'=>0,
        'count_All_New_buys'=>0,
        'sum_all_money'=>$sum_all_money,
        'bonus_All_aghlam'=>$bonus_All_aghlam,
        'bonus_All_Install'=>0,
        'bonus_All_New_buys'=>0,
        'bonus_all_money'=>$bonus_all_money,
        'all_monthly_bonuses'=>$all_monthly_bonuses,
        'aghlamComTg'=>$aghlamComTg,
        'monyComTg'=>$monyComTg,
        'countBuyComTg'=>"هیچکدام",
        'istallComTg'=>"هیچکدام",
        'countBuyComTgBonus'=>0,
        'monyComTgBonus'=>$monyComTgBonus,
        'aghlamComTgBonus'=>$aghlamComTgBonus,
        'countFactorComTg'=>$countFactorComTg,
        'weakServiceComTg'=>$weakServiceComTg,
        'mediumServiceComTg'=>$mediumServiceComTg,
        'strongServiceComTg'=>$strongServiceComTg,
        'countFactorComTgBonus'=>$countFactorComTgBonus,
        'weakServiceComTgBonus'=>$weakServiceComTgBonus,
        'mediumServiceComTgBonus'=>$mediumServiceComTgBonus,
        'strongServiceComTgBonus'=>$strongServiceComTgBonus,
        'count_all_Factor'=>$count_all_Factor,
        'all_Strong_Service'=>$all_Strong_Service,
        'all_Medium_Service'=>$all_Medium_Service,
        'count_all_WeakService'=>$count_all_WeakService,
        'count_all_Factor_Bonus'=>$count_all_Factor_Bonus,
        'all_Weak_Service_Bonus'=>$all_Weak_Service_Bonus,
        'all_Strong_Service_Bonus'=>$all_Strong_Service_Bonus,
        'all_Medium_Service_Bonus'=>$all_Medium_Service_Bonus
    ];
    }
    public function getDriverTodayAghlam(Request $request)
    {
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $driverId=$request->get("driverId");
        $emptyDate=$request->get("emptyDate");
        $aghlams=DB::select("SELECT SnDriver,SnGood,GoodName FROM (
            SELECT DISTINCT SnGood,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster
            JOIN Shop.dbo.FactorBYS ON BargiryBYS.SnFact=FactorBYS.SnFact
            WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper='$todayDate')a JOIN Shop.dbo.PubGoods ON a.SnGood=PubGoods.GoodSn WHERE SnDriver=$driverId");

        return Response::json($aghlams);
    }
    public function getDriverAllAghlam(Request $request)
    {
        $driverId=$request->get("driverId");
        $emptyDate=Jalalian::fromCarbon(Carbon::parse($request->get("emptyDate")))->format('Y/m/d');
        $aghlams=DB::select("SELECT SnDriver,SnGood,GoodName FROM (
                            SELECT DISTINCT SnGood,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster
                            JOIN Shop.dbo.FactorBYS ON BargiryBYS.SnFact=FactorBYS.SnFact
                            WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 AND DatePeaper >'$emptyDate')a JOIN Shop.dbo.PubGoods ON a.SnGood=PubGoods.GoodSn WHERE SnDriver=$driverId");
        return Response::json($aghlams);
    }
    public function getAllFactorDriver(Request $request)
    {
        $driverId=$request->get("driverId");
        $emptyDate=Jalalian::fromCarbon(Carbon::parse($request->get("emptyDate")))->format('Y/m/d');
        $allFactor=DB::select("SELECT SnFact,SnDriver,Name,PSN,FactDate FROM(
            SELECT SnFact,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 
            AND SnDriver=$driverId AND DatePeaper>'$emptyDate')A JOIN Shop.dbo.FactorHDS on A.SnFact=FactorHDS.SerialNoHDS JOIN Shop.dbo.Peopels on FactorHDS.CustomerSn=Peopels.PSN");
        return Response::json($allFactor);
        
    }
    public function getTodayDriverFactors(Request $request)
    {
        $driverId=$request->get("driverId");
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $allFactor=DB::select("SELECT SnFact,SnDriver,Name,PSN,FactDate FROM(
            SELECT SnFact,SnDriver FROM Shop.dbo.BargiryHDS JOIN Shop.dbo.BargiryBYS ON BargiryHDS.SnMasterBar=BargiryBYS.SnMaster WHERE BargiryHDS.CompanyNo=5 AND FiscalYear=1399 
            AND SnDriver=$driverId AND DatePeaper='$todayDate')A JOIN Shop.dbo.FactorHDS on A.SnFact=FactorHDS.SerialNoHDS JOIN Shop.dbo.Peopels on FactorHDS.CustomerSn=Peopels.PSN");
        return Response::json($allFactor);
    }

}
