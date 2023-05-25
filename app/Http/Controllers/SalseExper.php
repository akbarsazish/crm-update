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
class SalseExper extends Controller
{
      public function salesExpertAction(){
        $adminId=Session::get("asn");
        $exactAdmin=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->get();
        $exactAdminInfo=$exactAdmin[0];
        $specialBonuses=DB::table("CRM.dbo.crm_specialBonus")->get();
//برای همان روز
        $count_New_Install=0;
        $count_New_buy_Today=0;
        $count_aghlam_today=0;
        $sum_today_money=0;
//در طول زمان بعد از تخلیه کاربر
        $all_monthly_bonuses=0;
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
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
		  $EMPTYDATEHEJRI=Jalalian::fromCarbon(Carbon::createFromFormat('Y-m-d', $EMPTYDATE))->format('Y/m/d');
        $emptyDateInfo=DB::select("SELECT CONVERT(DATE,timeStamp) AS emptyDate FROM CRM.dbo.crm_adminHistory WHERE id=(SELECT MAX(id) FROM CRM.dbo.crm_adminHistory WHERE adminId=$adminId)");
        if($emptyDateInfo){
            $EMPTYDATE=$emptyDateInfo[0]->emptyDate;
        }

        foreach($specialBonuses as $special){
            if($special->id==11){
                //نصب
                $count_New_Install=DB::select("SELECT count(id) as countNewInstall from(
                    SELECT *, convert(date,addedDate) as justDate from CRM.dbo.crm_inserted_customers)a where a.justDate=CAST( GETDATE() AS Date ) and adminId=$adminId");
        
                $count_All_Install=DB::select("SELECT count(id) as countAllInstall from(
                    SELECT * from CRM.dbo.crm_inserted_customers where CONVERT(DATE,crm_inserted_customers.addedDate)>='$EMPTYDATE')a where  adminId=$adminId");
				if(count($count_All_Install)>0){
                    
                    $count_All_Install=$count_All_Install[0]->countAllInstall;
				}else{
					$count_All_Install=0;
				}
				if(count($count_New_Install)>0){
                    
                    $count_New_Install=$count_New_Install[0]->countNewInstall;
				}else{
					$count_New_Install=0;
				}
                    
                $installBonus=((int)($count_All_Install/$special->limitAmount)) * $special->Bonus;
                $all_bonus_since_Empty+=$installBonus;
                $bonus_All_Install=$installBonus;
            }
            
            if($special->id==12){
                //اقلام
                $count_All_aghlamR=DB::select("SELECT count(countGoods) as countAghlam,admin_id from (			
                    SELECT count(SnGood) as countGoods,admin_id,SnGood from (
                    SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood,CustomerSn from(
                    SELECT * FROM(
                        SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood,CustomerSn FROM Shop.dbo.FactorHDS
                        JOIN Shop.dbo.FactorBYS  on FactorHDS.SerialNoHDS=FactorBYS.SnFact where FactDate>='$EMPTYDATEHEJRI' and FactType=3)a
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
                        JOIN Shop.dbo.FactorBYS  on FactorHDS.SerialNoHDS=FactorBYS.SnFact where FactType=3)a
                        )g  group by SnGood,CustomerSn)c
                        JOIN (SELECT * FROM CRM.dbo.crm_customer_added WHERE returnState=0)d on c.CustomerSn=d.customer_id  WHERE CONVERT(date,maxTime)=CONVERT(date,CURRENT_TIMESTAMP))f group by admin_id,SnGood
                        )e where admin_id=$adminId group by admin_id");

                if(count($count_aghlam_todayR)>0){
                    $count_aghlam_today=$count_aghlam_todayR[0]->countAghlam;
                }

                $instAghlamBonus=((int)($count_All_aghlam/$special->limitAmount)) * $special->Bonus;
                $all_bonus_since_Empty+=$instAghlamBonus;
                $bonus_All_aghlam=$instAghlamBonus;
            }

            if($special->id==13){
                //مبلغ
                $allMoney_till_now=DB::select("SELECT SUM(NetPriceHDS) AS SumOfMoney,admin_id FROM Shop.dbo.factorHds
                JOIN (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0)d ON factorHds.CustomerSn=d.customer_id
                WHERE FactType=3 AND admin_id=$adminId and CONVERT(DATE,timestamp)>='$EMPTYDATE' GROUP BY admin_id");
                if(count($allMoney_till_now)>0){
                    $sum_all_money=$allMoney_till_now[0]->SumOfMoney;
                }

                $today_money=DB::select("SELECT SUM(NetPriceHDS) AS SumOfMoney,admin_Id FROM Shop.dbo.factorHds
                                        JOIN (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0)d ON factorHds.CustomerSn=d.customer_id
                                        WHERE FactType=3 AND admin_id=$adminId AND CONVERT(date,timestamp)=CONVERT(date,CURRENT_TIMESTAMP) GROUP BY admin_Id");
                if(count($today_money)>0){
                    $sum_today_money=$today_money[0]->SumOfMoney;
                }
                $allMoneyBonus=((int)($sum_all_money/10/$special->limitAmount)) * $special->Bonus;
                $all_bonus_since_Empty+=$allMoneyBonus;
                $bonus_all_money=$allMoneyBonus;
            }

            if($special->id==14){
                //خرید اولیه
                //امروز
                $count_New_buy_Today=DB::select("SELECT count(CustomerSn) as countNewFactor,admin_id from (
                    SELECT distinct CustomerSn from (SELECT * from Shop.dbo.FactorHds
					JOIN CRM.dbo.crm_inserted_customers on FactorHDS.CustomerSn=crm_inserted_customers.customerId 
					where FactType=3 AND DATEDIFF(hour,CONVERT(DATE,addedDate), CONVERT(DATE,timestamp))<=72 and crm_inserted_customers.adminId=$adminId and CONVERT(DATE,timestamp)=CONVERT(DATE,CURRENT_TIMESTAMP))b
                    )c  join CRM.dbo.crm_customer_added on c.CustomerSn=customer_id where admin_id=$adminId  group by admin_id");
                
                if(count($count_New_buy_Today)<1){
                    $count_New_buy_Today=0;
                }else{
                    $count_New_buy_Today=$count_New_buy_Today[0]->countNewFactor;
                }
                //همه           
                $count_All_New_buys=DB::select("SELECT count(CustomerSn) as countNewFactor,admin_id from (
                    SELECT distinct CustomerSn,admin_id from (SELECT * from Shop.dbo.FactorHds
					JOIN (select * from CRM.dbo.crm_customer_added where returnState=0) a on FactorHDS.CustomerSn=a.customer_id 
					where FactType=3 AND DATEDIFF(hour,CONVERT(DATE,a.addedTime), CONVERT(DATE,timestamp))<=72 and a.admin_id=$adminId and CONVERT(DATE,timestamp)>='$EMPTYDATE')b
                    )c   group by admin_id");
                if(count($count_All_New_buys)>0){
                $count_All_New_buys=$count_All_New_buys[0]->countNewFactor;
                }else{
                $count_All_New_buys=0;
                }
               
                $allBuyBonus=((int)($count_All_New_buys/$special->limitAmount)) * $special->Bonus;
                $all_bonus_since_Empty+=$allBuyBonus;
                $bonus_All_New_buys=$allBuyBonus;
            }

            //installs
            $special->count_New_Install=$count_New_Install;
            $special->count_All_Install=$count_All_Install;
            //buys
            $special->count_New_buy_Today=$count_New_buy_Today;
            $special->count_All_New_buys=$count_All_New_buys;
            $special->count_All_aghlam=$count_All_aghlam;
            $special->count_aghlam_today=$count_aghlam_today;
            $special->sum_all_money=$sum_all_money;
            $special->sum_today_money=$sum_today_money;
        }

        //محاسبه امتیازات اضافی بازاریابها
        $all_monthly_bonuses=0;
        $historyExist=DB::select("select sum(positiveBonus)-sum(negativeBonus) as sumAllBonus from CRM.dbo.crm_adminUpDownBonus  where adminId=$adminId and isUsed=0");
        if($historyExist){
            $all_monthly_bonuses=$historyExist[0]->sumAllBonus;
        }
        $all_bonus_since_Empty+=$all_monthly_bonuses;
        $selfHistory=DB::table("CRM.dbo.crm_adminHistory")->where('adminId',$adminId)->get();

        return view("salesExpert.salesExpertAction",
        ['specialBonuses'=>$specialBonuses,
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
        'all_monthly_bonuses'=>$all_monthly_bonuses]);
    }

    
    public function saleExpertActionInfo(Request $request){
        $adminId=$request->get("subId");
        $exactAdmin=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->get();
        $exactAdminInfo=$exactAdmin[0];
        $specialBonuses=DB::table("CRM.dbo.crm_specialBonus")->get();
//برای همان روز
        $count_New_Install=0;
        $count_New_buy_Today=0;
        $count_aghlam_today=0;
        $sum_today_money=0;
//در طول زمان بعد از تخلیه کاربر
        $all_monthly_bonuses=0;
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
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
		  $EMPTYDATEHEJRI=Jalalian::fromCarbon(Carbon::createFromFormat('Y-m-d', $EMPTYDATE))->format('Y/m/d');
        $emptyDateInfo=DB::select("SELECT CONVERT(DATE,timeStamp) AS emptyDate FROM CRM.dbo.crm_adminHistory WHERE id=(SELECT MAX(id) FROM CRM.dbo.crm_adminHistory WHERE adminId=$adminId)");
        if($emptyDateInfo){
            $EMPTYDATE=$emptyDateInfo[0]->emptyDate;
        }

        foreach($specialBonuses as $special){
            if($special->id==11){
                //نصب
                $count_New_Install=DB::select("SELECT count(id) as countNewInstall from(
                    SELECT *, convert(date,addedDate) as justDate from CRM.dbo.crm_inserted_customers)a where a.justDate=CAST( GETDATE() AS Date ) and adminId=$adminId");
        
                $count_All_Install=DB::select("SELECT count(id) as countAllInstall from(
                    SELECT * from CRM.dbo.crm_inserted_customers where CONVERT(DATE,crm_inserted_customers.addedDate)>='$EMPTYDATE')a where  adminId=$adminId");
				if(count($count_All_Install)>0){
                    
                    $count_All_Install=$count_All_Install[0]->countAllInstall;
				}else{
					$count_All_Install=0;
				}
				if(count($count_New_Install)>0){
                    
                    $count_New_Install=$count_New_Install[0]->countNewInstall;
				}else{
					$count_New_Install=0;
				}
                    
                $installBonus=((int)($count_All_Install/$special->limitAmount)) * $special->Bonus;
                $all_bonus_since_Empty+=$installBonus;
                $bonus_All_Install=$installBonus;
            }
            
            if($special->id==12){
                //اقلام
                $count_All_aghlamR=DB::select("SELECT count(countGoods) as countAghlam,admin_id from (			
                    SELECT count(SnGood) as countGoods,admin_id,SnGood from (
                    SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood,CustomerSn from(
                    SELECT * FROM(
                        SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood,CustomerSn FROM Shop.dbo.FactorHDS
                        JOIN Shop.dbo.FactorBYS  on FactorHDS.SerialNoHDS=FactorBYS.SnFact where FactDate>='$EMPTYDATEHEJRI' and FactType=3)a
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
                        JOIN Shop.dbo.FactorBYS  on FactorHDS.SerialNoHDS=FactorBYS.SnFact where FactType=3)a
                        )g  group by SnGood,CustomerSn)c
                        JOIN (SELECT * FROM CRM.dbo.crm_customer_added WHERE returnState=0)d on c.CustomerSn=d.customer_id  WHERE CONVERT(date,maxTime)=CONVERT(date,CURRENT_TIMESTAMP))f group by admin_id,SnGood
                        )e where admin_id=$adminId group by admin_id");

                if(count($count_aghlam_todayR)>0){
                    $count_aghlam_today=$count_aghlam_todayR[0]->countAghlam;
                }

                $instAghlamBonus=((int)($count_All_aghlam/$special->limitAmount)) * $special->Bonus;
                $all_bonus_since_Empty+=$instAghlamBonus;
                $bonus_All_aghlam=$instAghlamBonus;
            }

            if($special->id==13){
                //مبلغ
                $allMoney_till_now=DB::select("SELECT SUM(NetPriceHDS) AS SumOfMoney,admin_id FROM Shop.dbo.factorHds
                JOIN (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0)d ON factorHds.CustomerSn=d.customer_id
                WHERE FactType=3 AND admin_id=$adminId  GROUP BY admin_id");
                if(count($allMoney_till_now)>0){
                    $sum_all_money=$allMoney_till_now[0]->SumOfMoney;
                }

                $today_money=DB::select("SELECT SUM(NetPriceHDS) AS SumOfMoney,admin_Id FROM Shop.dbo.factorHds
                                        JOIN (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0)d ON factorHds.CustomerSn=d.customer_id
                                        WHERE FactType=3 AND admin_id=$adminId AND CONVERT(date,timestamp)=CONVERT(date,CURRENT_TIMESTAMP) GROUP BY admin_Id");
                if(count($today_money)>0){
                    $sum_today_money=$today_money[0]->SumOfMoney;
                }
                $allMoneyBonus=((int)($sum_all_money/10/$special->limitAmount)) * $special->Bonus;
                $all_bonus_since_Empty+=$allMoneyBonus;
                $bonus_all_money=$allMoneyBonus;
            }

            if($special->id==14){
                //خرید اولیه
                //امروز
                $count_New_buy_Today=DB::select("SELECT count(CustomerSn) as countNewFactor,admin_id from (
                    SELECT distinct CustomerSn from (SELECT * from Shop.dbo.FactorHds
					JOIN CRM.dbo.crm_inserted_customers on FactorHDS.CustomerSn=crm_inserted_customers.customerId 
					where FactType=3 AND DATEDIFF(hour,CONVERT(DATE,addedDate), CONVERT(DATE,timestamp))<=72 and crm_inserted_customers.adminId=$adminId and CONVERT(DATE,timestamp)=CONVERT(DATE,CURRENT_TIMESTAMP))b
                    )c  join CRM.dbo.crm_customer_added on c.CustomerSn=customer_id where admin_id=$adminId  group by admin_id");
                
                if(count($count_New_buy_Today)<1){
                    $count_New_buy_Today=0;
                }else{
                    $count_New_buy_Today=$count_New_buy_Today[0]->countNewFactor;
                }
                //همه           
                $count_All_New_buys=DB::select("SELECT count(CustomerSn) as countNewFactor,admin_id from (
                    SELECT distinct CustomerSn,admin_id from (SELECT * from Shop.dbo.FactorHds
					JOIN (select * from CRM.dbo.crm_customer_added where returnState=0) a on FactorHDS.CustomerSn=a.customer_id 
					where FactType=3 AND DATEDIFF(hour,CONVERT(DATE,a.addedTime), CONVERT(DATE,timestamp))<=72 and a.admin_id=$adminId and CONVERT(DATE,timestamp)>='$EMPTYDATE')b
                    )c   group by admin_id");
                if(count($count_All_New_buys)>0){
                $count_All_New_buys=$count_All_New_buys[0]->countNewFactor;
                }else{
                $count_All_New_buys=0;
                }
               
                $allBuyBonus=((int)($count_All_New_buys/$special->limitAmount)) * $special->Bonus;
                $all_bonus_since_Empty+=$allBuyBonus;
                $bonus_All_New_buys=$allBuyBonus;
            }

            //installs
            $special->count_New_Install=$count_New_Install;
            $special->count_All_Install=$count_All_Install;
            //buys
            $special->count_New_buy_Today=$count_New_buy_Today;
            $special->count_All_New_buys=$count_All_New_buys;
            $special->count_All_aghlam=$count_All_aghlam;
            $special->count_aghlam_today=$count_aghlam_today;
            $special->sum_all_money=$sum_all_money;
            $special->sum_today_money=$sum_today_money;
        }

        //محاسبه امتیازات اضافی بازاریابها
        $all_monthly_bonuses=0;
        $historyExist=DB::select("select sum(positiveBonus)-sum(negativeBonus) as sumAllBonus from CRM.dbo.crm_adminUpDownBonus  where adminId=$adminId and isUsed=0");
        if($historyExist){
            $all_monthly_bonuses=$historyExist[0]->sumAllBonus;
        }
        $all_bonus_since_Empty+=$all_monthly_bonuses;
        $selfHistory=DB::table("CRM.dbo.crm_adminHistory")->where('adminId',$adminId)->get();

        return view("salesExpert.salesExpertAction",
        ['specialBonuses'=>$specialBonuses,
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
        'all_monthly_bonuses'=>$all_monthly_bonuses]);
    }
    
    public function getBazaryabActionInformation($adminId){
        $adminId=$adminId;
        $specialBonuses=DB::table("CRM.dbo.crm_specialBonus")->get();
//برای همان روز
        $count_New_Install=0;
        $count_New_buy_Today=0;
        $count_aghlam_today=0;
        $sum_today_money=0;
//در طول زمان بعد از تخلیه کاربر
        $all_monthly_bonuses=0;
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
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
		  $EMPTYDATEHEJRI=Jalalian::fromCarbon(Carbon::createFromFormat('Y-m-d', $EMPTYDATE))->format('Y/m/d');
        $emptyDateInfo=DB::select("SELECT CONVERT(DATE,timeStamp) AS emptyDate FROM CRM.dbo.crm_adminHistory WHERE id=(SELECT MAX(id) FROM CRM.dbo.crm_adminHistory WHERE adminId=$adminId)");
        if($emptyDateInfo){
            $EMPTYDATE=$emptyDateInfo[0]->emptyDate;
        }

        foreach($specialBonuses as $special){
            if($special->id==11){
                //نصب
                $count_New_Install=DB::select("SELECT count(id) as countNewInstall from(
                    SELECT *, convert(date,addedDate) as justDate from CRM.dbo.crm_inserted_customers)a where a.justDate=CAST( GETDATE() AS Date ) and adminId=$adminId");
        
                $count_All_Install=DB::select("SELECT count(id) as countAllInstall from(
                    SELECT * from CRM.dbo.crm_inserted_customers where CONVERT(DATE,crm_inserted_customers.addedDate)>='$EMPTYDATE')a where  adminId=$adminId");
				if(count($count_All_Install)>0){
                    
                    $count_All_Install=$count_All_Install[0]->countAllInstall;
				}else{
					$count_All_Install=0;
				}
				if(count($count_New_Install)>0){
                    
                    $count_New_Install=$count_New_Install[0]->countNewInstall;
				}else{
					$count_New_Install=0;
				}
                    
                $installBonus=((int)($count_All_Install/$special->limitAmount)) * $special->Bonus;
                $all_bonus_since_Empty+=$installBonus;
                $bonus_All_Install=$installBonus;
            }
            
            if($special->id==12){
                //اقلام
                $count_All_aghlamR=DB::select("SELECT count(countGoods) as countAghlam,admin_id from (			
                    SELECT count(SnGood) as countGoods,admin_id,SnGood from (
                    SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood,CustomerSn from(
                    SELECT * FROM(
                        SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood,CustomerSn FROM Shop.dbo.FactorHDS
                        JOIN Shop.dbo.FactorBYS  on FactorHDS.SerialNoHDS=FactorBYS.SnFact where FactDate>='$EMPTYDATEHEJRI' and FactType=3)a
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
                        JOIN Shop.dbo.FactorBYS  on FactorHDS.SerialNoHDS=FactorBYS.SnFact where FactType=3)a
                        )g  group by SnGood,CustomerSn)c
                        JOIN (SELECT * FROM CRM.dbo.crm_customer_added WHERE returnState=0)d on c.CustomerSn=d.customer_id  WHERE CONVERT(date,maxTime)=CONVERT(date,CURRENT_TIMESTAMP))f group by admin_id,SnGood
                        )e where admin_id=$adminId group by admin_id");

                if(count($count_aghlam_todayR)>0){
                    $count_aghlam_today=$count_aghlam_todayR[0]->countAghlam;
                }

                $instAghlamBonus=((int)($count_All_aghlam/$special->limitAmount)) * $special->Bonus;
                $all_bonus_since_Empty+=$instAghlamBonus;
                $bonus_All_aghlam=$instAghlamBonus;
            }

            if($special->id==13){
                //مبلغ
                $allMoney_till_now=DB::select("SELECT SUM(NetPriceHDS) AS SumOfMoney,admin_id FROM Shop.dbo.factorHds
                JOIN (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0)d ON factorHds.CustomerSn=d.customer_id
                WHERE FactType=3 AND admin_id=$adminId and CONVERT(DATE,timestamp)>='$EMPTYDATE' GROUP BY admin_id");
                if(count($allMoney_till_now)>0){
                    $sum_all_money=$allMoney_till_now[0]->SumOfMoney;
                }

                $today_money=DB::select("SELECT SUM(NetPriceHDS) AS SumOfMoney,admin_Id FROM Shop.dbo.factorHds
                                        JOIN (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0)d ON factorHds.CustomerSn=d.customer_id
                                        WHERE FactType=3 AND admin_id=$adminId AND CONVERT(date,timestamp)=CONVERT(date,CURRENT_TIMESTAMP) GROUP BY admin_Id");
                if(count($today_money)>0){
                    $sum_today_money=$today_money[0]->SumOfMoney;
                }
                $allMoneyBonus=((int)($sum_all_money/10/$special->limitAmount)) * $special->Bonus;
                $all_bonus_since_Empty+=$allMoneyBonus;
                $bonus_all_money=$allMoneyBonus;
            }

            if($special->id==14){
                //خرید اولیه
                //امروز
                $count_New_buy_Today=DB::select("SELECT count(CustomerSn) as countNewFactor,admin_id from (
                    SELECT distinct CustomerSn from (SELECT * from Shop.dbo.FactorHds
					JOIN CRM.dbo.crm_inserted_customers on FactorHDS.CustomerSn=crm_inserted_customers.customerId 
					where FactType=3 AND DATEDIFF(hour,CONVERT(DATE,addedDate), CONVERT(DATE,timestamp))<=72 and crm_inserted_customers.adminId=$adminId and CONVERT(DATE,timestamp)=CONVERT(DATE,CURRENT_TIMESTAMP))b
                    )c  join CRM.dbo.crm_customer_added on c.CustomerSn=customer_id where admin_id=$adminId  group by admin_id");
                
                if(count($count_New_buy_Today)<1){
                    $count_New_buy_Today=0;
                }else{
                    $count_New_buy_Today=$count_New_buy_Today[0]->countNewFactor;
                }
                //همه           
                $count_All_New_buys=DB::select("SELECT count(CustomerSn) as countNewFactor,admin_id from (
                    SELECT distinct CustomerSn,admin_id from (SELECT * from Shop.dbo.FactorHds
					JOIN (select * from CRM.dbo.crm_customer_added where returnState=0) a on FactorHDS.CustomerSn=a.customer_id 
					where FactType=3 AND DATEDIFF(hour,CONVERT(DATE,a.addedTime), CONVERT(DATE,timestamp))<=72 and a.admin_id=$adminId and CONVERT(DATE,timestamp)>='$EMPTYDATE')b
                    )c   group by admin_id");
                if(count($count_All_New_buys)>0){
                $count_All_New_buys=$count_All_New_buys[0]->countNewFactor;
                }else{
                $count_All_New_buys=0;
                }
               
                $allBuyBonus=((int)($count_All_New_buys/$special->limitAmount)) * $special->Bonus;
                $all_bonus_since_Empty+=$allBuyBonus;
                $bonus_All_New_buys=$allBuyBonus;
            }
        }
        $targetsCompletion=self::getBazaryabTargets($count_All_Install,$count_All_New_buys,$sum_all_money,$count_All_aghlam);
        //محاسبه امتیازات اضافی بازاریابها
        $all_monthly_bonuses=0;
        $historyExist=DB::select("SELECT sum(positiveBonus)-sum(negativeBonus) as sumAllBonus from CRM.dbo.crm_adminUpDownBonus  where adminId=$adminId and isUsed=0");
        if($historyExist){
            $all_monthly_bonuses=$historyExist[0]->sumAllBonus;
        }
        $all_bonus_since_Empty+=$all_monthly_bonuses;
        return ['targetsCompletion'=>$targetsCompletion,
                'all_bonus_since_Empty'=>$all_bonus_since_Empty,
                'count_All_aghlam'=>$count_All_aghlam,
                'count_All_Install'=>$count_All_Install,
                'count_All_New_buys'=>$count_All_New_buys,
                'sum_all_money'=>$sum_all_money,
                'bonus_All_aghlam'=>$bonus_All_aghlam,
                'bonus_All_Install'=>$bonus_All_Install,
                'bonus_All_New_buys'=>$bonus_All_New_buys,
                'bonus_all_money'=>$bonus_all_money,
                'all_monthly_bonuses'=>$all_monthly_bonuses];
    }

    public function getSalesExpertSelfInfo(Request $request)
    {
        $adminId=$request->get('adminId');
        $EMPTYDATE='2022-11-11';
        $emptyDateInfo=DB::select("SELECT CONVERT(DATE,timeStamp) AS emptyDate FROM CRM.dbo.crm_adminHistory WHERE id=(SELECT MAX(id) FROM CRM.dbo.crm_adminHistory WHERE adminId=$adminId)");
        if($emptyDateInfo){
            $EMPTYDATE=$emptyDateInfo[0]->emptyDate;
        }
        $count_New_Install=DB::select("SELECT count(id) as countNewInstall from(
            SELECT *, convert(date,addedDate) as justDate from CRM.dbo.crm_inserted_customers)a where a.justDate=CAST( GETDATE() AS Date ) and adminId=$adminId");

        $count_All_Install=DB::select("SELECT count(id) as countAllInstall from(
            SELECT * from CRM.dbo.crm_inserted_customers where CONVERT(DATE,crm_inserted_customers.addedDate)>='$EMPTYDATE')a where  adminId=$adminId");
            if(count($count_All_Install)>0){
            $count_All_Install=$count_All_Install[0]->countAllInstall;
            }else{
            $count_All_Install=0;
            }

        $count_New_buy_Today=DB::select("SELECT count(countBoughtCustomer) as countbaughtToday,adminId from (
                                        SELECT COUNT(CustomerSN) as countBoughtCustomer,CustomerSn from (SELECT * from Shop.dbo.FactorHds where FactDate=FORMAT(CAST(getDate() as date),'yyyy/MM/dd','fa') and FactType=3)b group by CustomerSn
                                        )c join CRM.dbo.crm_inserted_customers on c.CustomerSn=crm_inserted_customers.customerId where adminId=$adminId group by adminId");
            if(count($count_New_buy_Today)<1){
                $count_New_buy_Today=0;
            }else{
                $count_New_buy_Today=$count_New_buy_Today[0]->countbaughtToday;
            }

        $count_All_New_buys=DB::select("SELECT count(CustomerSn) as countNewFactor,admin_id from (
            SELECT distinct CustomerSn from (SELECT * from Shop.dbo.FactorHds
            JOIN CRM.dbo.crm_inserted_customers on FactorHDS.CustomerSn=crm_inserted_customers.customerId 
            where FactType=3 AND DATEDIFF(hour,CONVERT(DATE,addedDate), CONVERT(DATE,timestamp))<=72 and crm_inserted_customers.adminId=$adminId and CONVERT(DATE,timestamp)>='$EMPTYDATE')b
            )c  join CRM.dbo.crm_customer_added on c.CustomerSn=customer_id where admin_id=$adminId  group by admin_id");
        if(count($count_All_New_buys)>0){
            $count_All_New_buys=$count_All_New_buys[0]->countNewFactor;
        }else{
            $count_All_New_buys=0;
        }
        //محاسبه امتیازات اضافی بازاریابها
        $all_monthly_bonuses=0;
        $historyExist=DB::select("select sum(positiveBonus)-sum(negativeBonus) as sumAllBonus from CRM.dbo.crm_adminUpDownBonus  where adminId=$adminId and isUsed=0");
        if($historyExist){
            $all_monthly_bonuses=$historyExist[0]->sumAllBonus;
        }

        return Response::json([$count_New_Install[0]->countNewInstall,$count_New_buy_Today,$count_All_Install,$count_All_New_buys,$all_monthly_bonuses]);
    }

    public function getTodaySelfBuyToday(Request $request)
    {
        $adminId=$request->get('adminId');

        $customers=DB::select("SELECT CustomerSn,Name,admin_id,PSN,PhoneStr from (
                                SELECT CustomerSn from (SELECT * from Shop.dbo.FactorHds
                                JOIN CRM.dbo.crm_inserted_customers on FactorHDS.CustomerSn=crm_inserted_customers.customerId 
                                where FactType=3 AND DATEDIFF(hour,CONVERT(DATE,addedDate), CONVERT(DATE,timestamp))<=72 and crm_inserted_customers.adminId=$adminId and CONVERT(DATE,timestamp)=CONVERT(DATE,CURRENT_TIMESTAMP))b
                                )c  join CRM.dbo.crm_customer_added on c.CustomerSn=customer_id
                                
                                join Shop.dbo.Peopels on c.CustomerSn=Peopels.PSN
                                join (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr
                                                        FROM Shop.dbo.PhoneDetail
                                                        GROUP BY SnPeopel)a on PSN=a.SnPeopel
                                where admin_id=$adminId ");
        return Response::json($customers);
    }

    public function getAllNewBuySelf(Request $request)
    {
        $adminId=$request->get('adminId');
        $emptyDate=$request->get('emptyDate');
        $customers=DB::select("SELECT Name,PSN,PhoneStr from (
            SELECT distinct CustomerSn,admin_id from (SELECT * from Shop.dbo.FactorHds
            JOIN (select * from CRM.dbo.crm_customer_added where returnState=0 )a on FactorHDS.CustomerSn=a.customer_id
            where FactType=3 AND DATEDIFF(hour,CONVERT(DATE,a.addedTime), CONVERT(DATE,timestamp))<=72 and a.admin_id=$adminId and CONVERT(DATE,timestamp)>='$emptyDate')b
            )c
            join SHop.dbo.Peopels on c.CustomerSn=PSN
			join (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr
									FROM Shop.dbo.PhoneDetail
									GROUP BY SnPeopel)b on PSN=b.SnPeopel");
        return Response::json($customers);
    }
	
	    public function getTodaySelfInstalls(Request $request)
    {
        $adminId=$request->get('adminId');
        $allCustomerInf=DB::select("SELECT * from(
                                            SELECT *, convert(date,addedDate) as justDate from CRM.dbo.crm_inserted_customers 
                                            join Shop.dbo.Peopels on customerId=PSN)a
                                            
                                            join (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr
                                        FROM Shop.dbo.PhoneDetail
                                        GROUP BY SnPeopel)b on a.PSN=b.SnPeopel
                                        left join Shop.dbo.GroupPeopelSecondHDS on a.SnGroupSecond=GroupPeopelSecondHDS.SnGPSecond
                                        where a.justDate=CAST( GETDATE() AS Date ) and adminId=$adminId order by addedDate desc");
        
        return Response::json($allCustomerInf);
    }

    public function getAllNewInstallSelf(Request $request)
    {
        $adminId=$request->get('adminId');
		$emptyDate=$request->get('emptyDate');
        $customers=DB::select("SELECT * from(
            SELECT *, convert(date,addedTime) as justDate from CRM.dbo.crm_customer_added 
            JOIN Shop.dbo.Peopels on customer_id=PSN where returnState=0 and admin_id=$adminId)a
            
            JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr
        FROM Shop.dbo.PhoneDetail
        GROUP BY SnPeopel)b on a.PSN=b.SnPeopel
        left JOIN Shop.dbo.GroupPeopelSecondHDS on a.SnGroupSecond=GroupPeopelSecondHDS.SnGPSecond
        where admin_id=$adminId and justDate>='$emptyDate'  order by justDate desc");
        return Response::json($customers);
    }


    public function getSalesExpertSelfInfoByDates(Request $request)
    {
        $adminId=$request->get('adminId');
        $firstDate=$request->get("firstDate");
        $secondDate=$request->get("secondDate");
        $firstDateMiladi=Jalalian::fromFormat('Y/m/d', $request->get("firstDate"))->toCarbon()->format('Y-m-d');
        $secondDateMiladi=Jalalian::fromFormat('Y/m/d',$request->get("secondDate"))->toCarbon()->format('Y-m-d');

        $count_All_Install=DB::select("SELECT count(id) AS countAllInstall FROM(
            SELECT * FROM CRM.dbo.crm_inserted_customers WHERE CONVERT(DATE,addedDate) >='$firstDateMiladi' AND CONVERT(DATE,addedDate) <= '$secondDateMiladi')a WHERE  a.adminId=$adminId");
        if(count($count_All_Install)>0){
            $count_All_Install=$count_All_Install[0]->countAllInstall;
        }else{
            $count_All_Install=0;
        }

        $count_All_New_buys=DB::select("select COUNT(CustomerSn) as countFactor,adminId from(
            SELECT * from Shop.dbo.FactorHds
                 join CRM.dbo.crm_inserted_customers on FactorHds.CustomerSn=crm_inserted_customers.customerId where DATEDIFF(hour,FactorHDS.TimeStamp,addedDate)<=72)a where FactDate>='$firstDate' and FactDate<='$secondDate' and adminId=$adminId  and FactType=3 group by adminId");
        if(count($count_All_New_buys)>0){
            $count_All_New_buys=$count_All_New_buys[0]->countNewFactor;
        }else{
            $count_All_New_buys=0;
        }

        return Response::json([$count_All_Install,$count_All_New_buys]);
    }
    public function addTarget(Request $request)
    {
        $baseName=$request->get("baseName");
        $firstTarget=$request->get("firstTarget");
        $secondTarget=$request->get("secondTarget");
        $thirdTarget=$request->get("thirdTarget");
        $firstTargetBonus=$request->get("firstTargetBonus");
        $secondTargetBonus=$request->get("secondTargetBonus");
        $thirdTargetBonus=$request->get("thirdTargetBonus");
        DB::table("CRM.dbo.crm_targets")->where('BaseName','!=',$baseName)->insert([
            'BaseName'=>"$baseName"
            ,'firstTarget'=>$firstTarget
            ,'secondTarget'=>$secondTarget
            ,'thirdTarget'=>$thirdTarget
            ,'firstTargetBonus'=>$firstTargetBonus
            ,'secondTargetBonus'=>$secondTargetBonus
            ,'thirdTargetBonus'=>$thirdTargetBonus]);
        $targets=DB::table("CRM.dbo.crm_targets")->get();

        return Response::json($targets);
    }
    public function defineTarget() {
        $targets=DB::table("CRM.dbo.crm_targets")->get();
        return view("salesExpert.bazarYabTarget",['targets'=>$targets]);
    }
    public function getTargetInfo(Request $request)
    {
        $targetId=$request->get('targetId');
        $target=DB::table("CRM.dbo.crm_targets")->where('id',$targetId)->get();
        return Response::json($target);
    }
    public function editTarget(Request $request)
    {
        $targetId=$request->get("targetId");
        $firstTarget=str_replace(",","",$request->get("firstTarget"));
        $secondTarget=str_replace(",","",$request->get("secondTarget"));
        $thirdTarget=str_replace(",","",$request->get("thirdTarget"));
        $firstTargetBonus=$request->get("firstTargetBonus");
        $secondTargetBonus=$request->get("secondTargetBonus");
        $thirdTargetBonus=$request->get("thirdTargetBonus");
        DB::table("CRM.dbo.crm_targets")->where('id','=',$targetId)->update([
            'firstTarget'=>$firstTarget
            ,'secondTarget'=>$secondTarget
            ,'thirdTarget'=>$thirdTarget
            ,'firstTargetBonus'=>$firstTargetBonus
            ,'secondTargetBonus'=>$secondTargetBonus
            ,'thirdTargetBonus'=>$thirdTargetBonus]);
        $targets=DB::table("CRM.dbo.crm_targets")->get();

        return Response::json($targets); 
    }
	public function bonusSetting(Request $request){
        $admins=DB::table("CRM.dbo.crm_admin")->where('adminType',3)->get();
        $targets=DB::table("CRM.dbo.crm_targets")->get();
        $generalTargets=DB::table("CRM.dbo.crm_generalTargets")->get();
        $specialBonuses=DB::table("CRM.dbo.crm_specialBonus")->get();
        $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->get();
        return View('salesExpert.specialSetting',['admins'=>$admins,'targets' => $targets,
        'specialBonuses'=>$specialBonuses,'generalTargets'=>$generalTargets,'generalBonuses'=>$generalBonuses]);
    }
    
    public function getBazaryabTargets($count_All_Install,$count_All_New_buys,$allMoney_till_now,$count_All_aghlam){
        $istallComTg="هیچکدام";
        $istallComTgBonus=0;
        $countBuyComTg="هیچکدام";
        $countBuyComTgBonus=0;
        $monyComTg="هیچکدام";
        $monyComTgBonus=0;
        $aghlamComTg="هیچکدام";
        $aghlamComTgBonus=0;
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
                if($allMoney_till_now>0){
                    //تارگت‌های مبلغ خرید
                if($target->id==7){
                    if(($allMoney_till_now/10) >= $target->thirdTarget){
                        $monyComTg="تارگیت سوم";
                        $monyComTgBonus=$target->thirdTargetBonus;
                    }else{
                        if(($allMoney_till_now/10) >= $target->secondTarget){
                            $monyComTg="تارگیت دوم";
                            $monyComTgBonus=$target->thirdTargetBonus;
                        }else{
                            if(($allMoney_till_now/10) >= $target->firstTarget){
                                $monyComTg="تارگیت اول";
                                $monyComTgBonus=$target->thirdTargetBonus;
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
        }
        return ['istallComTg'=>$istallComTg,
        'istallComTgBonus'=>$istallComTgBonus,
        'countBuyComTg'=>$countBuyComTg,
        'countBuyComTgBonus'=>$countBuyComTgBonus,
        'monyComTg'=>$monyComTg,
        'monyComTgBonus'=>$monyComTgBonus,
        'aghlamComTg'=>$aghlamComTg,
        'aghlamComTgBonus'=>$aghlamComTgBonus];
    }
    public function editSpecialBonus(Request $request)
    {
        $baseId=$request->get("baseId");
        $bonus=$request->get("bonus");
        $limitAmount=str_replace(",", "",$request->get("limitAmount"));
        DB::table("CRM.dbo.crm_specialBonus")->where('id',$baseId)->update(['Bonus'=>$bonus,'limitAmount'=>$limitAmount]);
        $specialBonuses=DB::table("CRM.dbo.crm_specialBonus")->get();
        return Response::json($specialBonuses);
    }
    public function getSpecialBonusInfo(Request $request)
    {
        $baseId=$request->get("bonusId");
        $bonuses=DB::table("CRM.dbo.crm_specialBonus")->where('id',$baseId)->get();
        return Response::json($bonuses);
    }
    public function deleteSpecialBonus(Request $request)
    {
       $baseId=$request->get("baseId");
       DB::table("CRM.dbo.crm_specialBonus")->where('id',$baseId)->delete();
       $specialBonuses=DB::table("CRM.dbo.crm_specialBonus")->get();
       return Response::json($specialBonuses);
    }
    public function deleteTarget(Request $request)
    {
        $baseId=$request->get("baseId");
        DB::table("CRM.dbo.crm_targets")->where('id',$baseId)->delete();
        $targets=DB::table("CRM.dbo.crm_targets")->get();
        return Response::json($targets);
    }
      public function subTrees(Request $request)
    {
        //بازاریابهای زیر نظر سرپرست
        $exactAdmin=DB::table("CRM.dbo.crm_admin")->where('id',SESSION::get("asn"))->get();
        $adminId=SESSION::get("asn");
        $admins;
        if($exactAdmin[0]->adminType==5){
            $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where("crm_admin.adminType","!=",5)->where('deleted',0)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","driverId")->get();
        }
        if($exactAdmin[0]->adminType!=5){
            $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where("bossId",Session::get("asn"))->where("crm_admin.adminType","!=",5)->where('deleted',0)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","driverId")->get();
        }
        //لیست سرپرستها
        $bosses=DB::table("CRM.dbo.crm_admin")->where('adminType','!=',5)->where('deleted',0)->get();


        $adminTypes=DB::select("SELECT * FROM CRM.dbo.crm_adminType");

        return View("salesExpert.subList",['admins'=>$admins,'bosses'=>$bosses, 'admins'=>$admins,'adminTypes'=>$adminTypes]);
    }


    public function getAllBuyAghlamSelf(Request $request)
    {
        $adminId=$request->get("adminId");
        $EMPTYDATE=$request->get("emptydate");
        $EMPTYDATEHEJRI=Jalalian::fromCarbon(Carbon::createFromFormat('Y-m-d', $EMPTYDATE))->format('Y/m/d');

        $all_aghlams=DB::select("SELECT GoodName,GoodSn,admin_id from (								
								SELECT count(SnGood) as countGoods,admin_id,SnGood from (
								SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood,CustomerSn from(
								SELECT * FROM(
									SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood,CustomerSn FROM Shop.dbo.FactorHDS
									JOIN Shop.dbo.FactorBYS  on FactorHDS.SerialNoHDS=FactorBYS.SnFact where FactDate>='$EMPTYDATEHEJRI')a
									)g  group by SnGood,CustomerSn)c
									join (SELECT * FROM CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId)d on c.CustomerSn=d.customer_id)f group by admin_id,SnGood)b
									join SHop.dbo.PubGoods on b.SnGood=PubGoods.GoodSn");
        return Response::json($all_aghlams);
    }
	

    public function getTodayBuyAghlamSelf(Request $request)
    {
        $adminId=$request->get("adminId");
        $today_aghlams=DB::select("SELECT * FROM (
                                    SELECT distinct SnGood FROM (
                                    SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood,CustomerSn FROM(
                                    SELECT * FROM(
                                        SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood,CustomerSn FROM Shop.dbo.FactorHDS
                                        JOIN Shop.dbo.FactorBYS on FactorHDS.SerialNoHDS=FactorBYS.SnFact where FactType=3)a
                                        )g group by SnGood,CustomerSn)c
                                        JOIN (SELECT * FROM CRM.dbo.crm_customer_added WHERE returnState=0)d on c.CustomerSn=d.customer_id  WHERE CONVERT(date,maxTime)=CONVERT(date,CURRENT_TIMESTAMP) 
                                            AND d.admin_id=$adminId )e)f
                                            JOIN (SELECT GoodName,GoodSn FROM SHop.dbo.PubGoods)b on f.SnGood=b.GoodSn");
        return Response::json($today_aghlams);
    }
    public function getAllBuyMoneySelf(Request $request)
    {
        $adminId=$request->get("adminId");
        $all_money_by_customer=DB::select("SELECT SumOfMoney,Name,PSN FROM(
								SELECT SUM(NetPriceHDS) AS SumOfMoney,CustomerSn FROM Shop.dbo.factorHds
                                JOIN (select * from CRM.dbo.crm_customer_added where returnState=0)c ON c.customer_id=CustomerSn
                                WHERE FactType=3 AND c.admin_id=$adminId and FactDate>='1401/09/01' GROUP BY CustomerSn)a 
                                JOIN Shop.dbo.Peopels ON a.CustomerSn=PSN");
        return Response::json($all_money_by_customer);
    }
    public function getTodayBuyMoneySelf(Request $request)
    {
        $adminId=$request->get("adminId");
        $today_money_by_customer=DB::select("SELECT SumOfMoney,Name,PSN FROM(
            SELECT SUM(NetPriceHDS) AS SumOfMoney,CustomerSn FROM Shop.dbo.factorHds
            JOIN (select * from CRM.dbo.crm_customer_added where returnState=0 )d ON CustomerSN=d.customer_id
            WHERE FactType=3 AND admin_id=$adminId and CONVERT(date,timestamp)=CONVERT(date,CURRENT_TIMESTAMP) GROUP BY CustomerSn)a 
            JOIN Shop.dbo.Peopels ON a.CustomerSn=PSN");
        return Response::json($today_money_by_customer);
    }

    public function getBossBazarYab(Request $request)
   {
        $bossId=$request->get("bossId");
        $admins=DB::table("CRM.dbo.crm_admin")->where("bossId",$bossId)->where('deleted',0)->get();
        return Response::json($admins);
   }
   
    public function getGeneralBase(Request $request)
   {
        $baseSn=$request->get('baseSn');
        $bases=DB::table("CRM.dbo.crm_generalTargets")->where("SnBase",$baseSn)->get();
        return Response::json($bases);
   }

   public function editGeneralTarget(Request $request)
   {
        $baseGName=str_replace(",","",$request->post("baseGName"));
        $firstTarget=str_replace(",","",$request->post("firstTarget"));
        $firstTargetBonus=str_replace(",","",$request->post("firstTargetBonus"));
        $secondTarget=str_replace(",","",$request->post("secondTarget"));
        $secondTargetBonus=str_replace(",","",$request->post("secondTargetBonus"));
        $thirdTarget=str_replace(",","",$request->post("thirdTarget"));
        $thirdTargetBonus=str_replace(",","",$request->post("thirdTargetBonus"));
        $baseId=$request->post("baseId");
        $userTypeID=$request->post("userTypeID");

        DB::table("CRM.dbo.crm_generalTargets")
            ->where("SnBase",$baseId)
            ->update([
            'firstTarget'=>$firstTarget
            ,'secondTarget'=>$secondTarget
            ,'thirdTarget'=>$thirdTarget
            ,'firstTargetBonus'=>$firstTargetBonus
            ,'secondTargetBonus'=>$secondTargetBonus
            ,'thirdTargetBonus'=>$thirdTargetBonus]);

        $bases=DB::table("CRM.dbo.crm_generalTargets")->where("userType",$userTypeID)->get();

        return Response::json($bases);
    }
    public function getGeneralBonus(Request $request)
    {
        $generalBonusID=$request->get("generalBonusID");
        $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("id",$generalBonusID)->get();
        return Response::json($generalBonuses[0]);
    }
    public function editGeneralBonus(Request $request)
    {
        $baseGName=$request->get("baseName");
        $userType=$request->get("userType");

        $baseId=$request->get("baseId");

        $limitAmount=str_replace(",","",$request->get("limitAmount"));

        $bonus=str_replace(",","",$request->get("bonus"));

        DB::table("CRM.dbo.crm_generalBonus")->where("id",$baseId)->update(["limitAmount"=>$limitAmount,"Bonus"=>$bonus]);
        $generalBonuses=DB::table("CRM.dbo.crm_generalBonus")->where("userType",$userType)->get();
        return Response::json($generalBonuses);

    }
	

public function bonusIncreaseDecrease(Request $request){
      //
    $admins=DB::select("SELECT FORMAT(TimeStamp,'yyyy/M/d','fa-ir') as TimeStamp,CONCAT(crm_admin.name,crm_admin.lastName) AS adminName,CONCAT(a.name,a.lastName) as superName,positiveBonus,negativeBonus,crm_adminUpDownBonus.id as historyId FROM CRM.dbo.crm_adminUpDownBonus join CRM.dbo.crm_admin ON crm_adminUpDownBonus.adminId=crm_admin.id
                            LEFT JOIN (SELECT * from CRM.dbo.crm_admin)a  ON a.id=crm_adminUpDownBonus.supervisorId
                            WHERE isUsed=0");
    $allEmployies=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE adminType!=5 and deleted=0");
    $admintype= self::getAdminType(Session::get('asn'));

    if($admintype!=5){
        $employies=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE bossId=".Session::get('asn')." AND deleted=0");
    }else{
        $employies=$allEmployies;
    }
      //لیست سرپرستها


   return view("admin.bonusIncreaseDecrease", ['admins'=>$admins,'employies'=>$employies]);
}
public function getUpDownBonusInfo(Request $request)
{
   $historyId=$request->get("historyID");
   $historyInfo=DB::table("CRM.dbo.crm_adminUpDownBonus")->where('id',$historyId)->get();

   $admins=DB::select("SELECT FORMAT(TimeStamp,'yyyy/M/d','fa-ir') as TimeStamp,CONCAT(crm_admin.name,crm_admin.lastName) AS adminName,CONCAT(a.name,a.lastName) as superName,positiveBonus,negativeBonus,crm_adminUpDownBonus.id as historyId FROM CRM.dbo.crm_adminUpDownBonus join CRM.dbo.crm_admin ON crm_adminUpDownBonus.adminId=crm_admin.id
   LEFT JOIN (SELECT * from CRM.dbo.crm_admin)a  ON a.id=crm_adminUpDownBonus.supervisorId
   WHERE isUsed=0");

    $allEmployies=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE adminType!=5 and deleted=0");

    $admintype= self::getAdminType(Session::get('asn'));

    if($admintype!=5){

        $employies=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE bossId=".Session::get('asn')." AND deleted=0");

    }else{

        $employies=$allEmployies;

    }
   return Response::json([$historyInfo,$employies]);
}
public function getUpDownBonusHistory(Request $request)
{

    $yesterdayOfWeek = Jalalian::fromCarbon(Carbon::yesterday())->getDayOfWeek();

    $yesterday;

    if($yesterdayOfWeek==6){

        $yesterday = Jalalian::fromCarbon(Carbon::yesterday()->subDays(1))->format('Y/m/d');

    }else{

        $yesterday = Jalalian::fromCarbon(Carbon::yesterday())->format('Y/m/d');

    }

    $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');

    $history;

    $flag=$request->get("flag");

    if($flag=="TODAY"){

        $history=DB::select("SELECT FORMAT(TimeStamp,'yyyy/M/d','fa-ir') as TimeStamp,CONCAT(crm_admin.name,crm_admin.lastName) AS adminName,CONCAT(a.name,a.lastName) as superName,positiveBonus,negativeBonus,crm_adminUpDownBonus.id as historyId FROM CRM.dbo.crm_adminUpDownBonus join CRM.dbo.crm_admin ON crm_adminUpDownBonus.adminId=crm_admin.id
                            LEFT JOIN (SELECT * from CRM.dbo.crm_admin)a  ON a.id=crm_adminUpDownBonus.supervisorId
                            WHERE isUsed=0 AND FORMAT(TimeStamp,'yyyy/M/d','fa-ir')='$todayDate'");
    }
    if($flag=="YESTERDAY"){

        $history=DB::select("SELECT FORMAT(TimeStamp,'yyyy/M/d','fa-ir') as TimeStamp,CONCAT(crm_admin.name,crm_admin.lastName) AS adminName,CONCAT(a.name,a.lastName) as superName,positiveBonus,negativeBonus,crm_adminUpDownBonus.id as historyId FROM CRM.dbo.crm_adminUpDownBonus join CRM.dbo.crm_admin ON crm_adminUpDownBonus.adminId=crm_admin.id
                            LEFT JOIN (SELECT * from CRM.dbo.crm_admin)a  ON a.id=crm_adminUpDownBonus.supervisorId
                            WHERE isUsed=0  AND FORMAT(TimeStamp,'yyyy/M/d','fa-ir')='$yesterday'");        
    }
    if($flag=="LASTHUNDRED"){

        $history=DB::select("SELECT TOP 100 FORMAT(TimeStamp,'yyyy/M/d','fa-ir') as TimeStamp,CONCAT(crm_admin.name,crm_admin.lastName) AS adminName,CONCAT(a.name,a.lastName) as superName,positiveBonus,negativeBonus,crm_adminUpDownBonus.id as historyId FROM CRM.dbo.crm_adminUpDownBonus join CRM.dbo.crm_admin ON crm_adminUpDownBonus.adminId=crm_admin.id
                            LEFT JOIN (SELECT * from CRM.dbo.crm_admin)a  ON a.id=crm_adminUpDownBonus.supervisorId
                            WHERE isUsed=0");        
    }
    if($flag=="ALL"){

        $history=DB::select("SELECT FORMAT(TimeStamp,'yyyy/M/d','fa-ir') as TimeStamp,CONCAT(crm_admin.name,crm_admin.lastName) AS adminName,CONCAT(a.name,a.lastName) as superName,positiveBonus,negativeBonus,crm_adminUpDownBonus.id as historyId FROM CRM.dbo.crm_adminUpDownBonus join CRM.dbo.crm_admin ON crm_adminUpDownBonus.adminId=crm_admin.id
                            LEFT JOIN (SELECT * from CRM.dbo.crm_admin)a  ON a.id=crm_adminUpDownBonus.supervisorId
                            WHERE isUsed=0");        
    }

    return Response::json($history);
}

public function getHistorySearch(Request $request)
{
    $name=$request->get("name");
    $orderBase=$request->get("orderBase");
    $bonusType=$request->get("bonusType");
    $firstDate="";
    $secondDate="1490/01/01";
    if(strlen($request->get("firstDate"))>3){
        $firstDate=$request->get("firstDate");
    }
    if(strlen($request->get("secondDate"))>3){
        $secondDate=$request->get("secondDate");
    }

    if($bonusType=='negative'){
        $history=DB::select("SELECT *,FORMAT(TimeStamp,'yyyy/MM/dd','fa-ir') as TimeStampH from (SELECT TimeStamp,CONCAT(crm_admin.name,crm_admin.lastName) AS adminName,CONCAT(a.name,a.lastName) as superName,positiveBonus,negativeBonus,crm_adminUpDownBonus.id as historyId,IsUsed FROM CRM.dbo.crm_adminUpDownBonus join CRM.dbo.crm_admin ON crm_adminUpDownBonus.adminId=crm_admin.id
        LEFT JOIN (select * from CRM.dbo.crm_admin)a  on a.id=crm_adminUpDownBonus.supervisorId)b
        WHERE isUsed=0 and negativeBonus>0 and adminName like '%$name%' and FORMAT(TimeStamp,'yyyy/MM/dd','fa-ir') >= '$firstDate' and FORMAT(TimeStamp,'yyyy/M/d','fa-ir') <= '$secondDate' order by $orderBase");
    }

    if($bonusType=='positive'){
        $history=DB::select("SELECT *,FORMAT(TimeStamp,'yyyy/MM/dd','fa-ir') as TimeStampH from (SELECT TimeStamp,CONCAT(crm_admin.name,crm_admin.lastName) AS adminName,CONCAT(a.name,a.lastName) as superName,positiveBonus,negativeBonus,crm_adminUpDownBonus.id as historyId,IsUsed FROM CRM.dbo.crm_adminUpDownBonus join CRM.dbo.crm_admin ON crm_adminUpDownBonus.adminId=crm_admin.id
        LEFT JOIN (select * from CRM.dbo.crm_admin)a  on a.id=crm_adminUpDownBonus.supervisorId)b
        WHERE isUsed=0 and positiveBonus>0 and adminName like '%$name%' and FORMAT(TimeStamp,'yyyy/MM/dd','fa-ir') >= '$firstDate' and FORMAT(TimeStamp,'yyyy/M/0d','fa-ir') <='$secondDate' order by $orderBase");
    }

    if($bonusType=='all'){
        $history=DB::select("SELECT *,FORMAT(TimeStamp,'yyyy/MM/dd','fa-ir') as TimeStampH from (SELECT TimeStamp,CONCAT(crm_admin.name,crm_admin.lastName) AS adminName,CONCAT(a.name,a.lastName) as superName,positiveBonus,negativeBonus,crm_adminUpDownBonus.id as historyId,IsUsed FROM CRM.dbo.crm_adminUpDownBonus join CRM.dbo.crm_admin ON crm_adminUpDownBonus.adminId=crm_admin.id
        LEFT JOIN (select * from CRM.dbo.crm_admin)a  on a.id=crm_adminUpDownBonus.supervisorId)b
        WHERE isUsed=0 and adminName like '%$name%' and FORMAT(TimeStamp,'yyyy/MM/dd','fa-ir') >= '$firstDate' and FORMAT(TimeStamp,'yyyy/M/0d','fa-ir') <='$secondDate' order by $orderBase");
    }

    return Response::json($history);
}



public function editUpDownBonus(Request $request)
{
$positiveBonus=$request->get("positive");
$negativeBonus=$request->get("negative");
$adminId=$request->get("adminId");
$comment=$request->get("discription");
$historyIDEmtiyasEdit=$request->get("historyId");
if(!$positiveBonus){
    $positiveBonus=0;
}
if(!$negativeBonus){
    $negativeBonus=0;
}
    DB::table("CRM.dbo.crm_adminUpDownBonus")->where("id",$historyIDEmtiyasEdit)->update(["positiveBonus"=>$positiveBonus
                                                                                        ,"negativeBonus"=>$negativeBonus
                                                                                        ,"discription"=>"$comment"
                                                                                        ,"supervisorId"=>Session::get("asn")]);
    $admins=DB::select("SELECT FORMAT(TimeStamp,'yyyy/M/d','fa-ir') as TimeStamp,CONCAT(crm_admin.name,crm_admin.lastName) AS adminName,CONCAT(a.name,a.lastName) as superName,positiveBonus,negativeBonus,crm_adminUpDownBonus.id as historyId FROM CRM.dbo.crm_adminUpDownBonus join CRM.dbo.crm_admin ON crm_adminUpDownBonus.adminId=crm_admin.id
                        LEFT JOIN (SELECT * from CRM.dbo.crm_admin)a  ON a.id=crm_adminUpDownBonus.supervisorId
                        WHERE isUsed=0");  
    return Response::json($admins);
}
public function addUpDownBonus(Request $request)
{
    $positiveBonus=$request->get("positiveBonus");
    $negativeBonus=$request->get("negativeBonus");
    if(!$positiveBonus){
        $positiveBonus=0;
    }
    if(!$negativeBonus){
        $negativeBonus=0;
    }
    $discriptionBonus=$request->get("discription");
    $adminId=$request->get("adminId");
    DB::table("CRM.dbo.crm_adminUpDownBonus")
                ->insert(["positiveBonus"=>$positiveBonus
                ,"negativeBonus"=>$negativeBonus
                ,"discription"=>"$discriptionBonus"
                ,"adminId"=>$adminId
                ,'superVisorId'=>Session::get('asn')]);

    $admins=DB::select("SELECT FORMAT(TimeStamp,'yyyy/M/d','fa-ir') as TimeStamp,CONCAT(crm_admin.name,crm_admin.lastName) AS adminName,CONCAT(a.name,a.lastName) as superName,positiveBonus,negativeBonus,crm_adminUpDownBonus.id as historyId FROM CRM.dbo.crm_adminUpDownBonus join CRM.dbo.crm_admin ON crm_adminUpDownBonus.adminId=crm_admin.id
    LEFT JOIN (SELECT * from CRM.dbo.crm_admin)a  ON a.id=crm_adminUpDownBonus.supervisorId
    WHERE isUsed=0");   
    return Response::json($admins);
}
public function deleteUpDownBonus(Request $request)
{
    $historyId=$request->get("historyId");
    DB::table("CRM.dbo.crm_adminUpDownBonus")->where("id",$historyId)->delete();
    $admins=DB::select("SELECT FORMAT(TimeStamp,'yyyy/M/d','fa-ir') as TimeStamp,CONCAT(crm_admin.name,crm_admin.lastName) AS adminName,CONCAT(a.name,a.lastName) as superName,positiveBonus,negativeBonus,crm_adminUpDownBonus.id as historyId FROM CRM.dbo.crm_adminUpDownBonus join CRM.dbo.crm_admin ON crm_adminUpDownBonus.adminId=crm_admin.id
                        LEFT JOIN (SELECT * from CRM.dbo.crm_admin)a  ON a.id=crm_adminUpDownBonus.supervisorId
                        WHERE isUsed=0");  
    return Response::json($admins);
}

public function getAdminType($adminId)
{
    $adminType=DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->get()[0]->adminType;
    return $adminType;
}
}
