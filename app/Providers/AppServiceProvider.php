<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use \Morilog\Jalali\Jalalian;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer("layout",function($view){
            $countNotDone=0;
            $countDoneWorks=0;
            $todayDate=Carbon::now()->format('Y-m-d');
            $countDoneWork=\DB::select("select COUNT(id) as countJob,specifiedDate from (
                select * from (
                select crm_workList.commentId,crm_workList.id,crm_workList.doneState,crm_workList.specifiedDate,crm_comment.customerId from CRM.dbo.crm_workList join CRM.dbo.crm_comment on crm_workList.commentId=crm_comment.id where doneState=1)a
                join (select customer_id,returnState,admin_id from CRM.dbo.crm_customer_added)c on a.customerId =c.customer_id where c.returnState=0 and admin_id=".\Session::get('asn').")b
                where specifiedDate='".$todayDate."'
                group by specifiedDate");
            $countNoDoneWork=\DB::select("select sum(countJob) as countJob from(
                select COUNT(id) as countJob,specifiedDate from (
                                select * from (
                                select crm_workList.commentId,crm_workList.id,crm_workList.doneState,crm_workList.specifiedDate,crm_comment.customerId from CRM.dbo.crm_workList join CRM.dbo.crm_comment on crm_workList.commentId=crm_comment.id where doneState=0)a
                                join (select customer_id,returnState,admin_id from CRM.dbo.crm_customer_added )c on a.customerId =c.customer_id where c.returnState=0 and admin_id=".\Session::get('asn').")b
                                where specifiedDate<='".$todayDate."'
                                group by specifiedDate)a");
            if(count($countNoDoneWork)>0){
               $countNotDone= $countNoDoneWork[0]->countJob;
            }
            if(count($countDoneWork)>0){
                $countDoneWorks=$countDoneWork[0]->countJob;
            }
            $countInbox=\DB::select("select COUNT(maxMessage) as countMessage
                from(
                select * from(
                select MAX(id) as maxMessage,senderId from CRM.dbo.crm_message where getterId=".\Session::get('asn')." and readState=0 group by senderId)a
                join CRM.dbo.crm_admin on a.senderId=crm_admin.id)b");
            $inbox=$countInbox[0]->countMessage;
            $alarmTimes=\DB::table("CRM.dbo.crm_alarmClock")->where("doneState",0)->where("adminId",\Session::get('asn'))->get()->first();
            if($alarmTimes){
                $alarmTime=$alarmTimes->TimeStamp;
            }else{
                $alarmTime="ثبت نشده است";
            }
            $countReffs=\DB::select("select COUNT(id) as countRefs from CRM.dbo.crm_returnCustomer where returnState=1");
            $reffs=0;
            if(count($countReffs)>0){
                $reffs=$countReffs[0]->countRefs;
            }
            $countInActives=\DB::select("select count(customerId) as countInactiveCustomer from (select distinct customerId from CRM.dbo.crm_inactiveCustomer where state=1)a");
            $inactives=0;
            if(count($countInActives)>0){
            $inactives=$countInActives[0]->countInactiveCustomer;
            }
            $todayDate=\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::now())->format("Y/m/d");
            $countAlarms=\DB::select("select COUNT(id) as countAlarm  from CRM.dbo.crm_alarm where factorId in (select max(FactorHDS.SerialNoHDS) from Shop.dbo.FactorHDS group by customerSn) and state=0 and alarmDate<'$todayDate'");

            $countAlarm=$countAlarms[0]->countAlarm;
            $countNewCustomers=\DB::select("select COUNT(customerId) as countNewCustomers from CRM.dbo.crm_inserted_customers where IsCustomer=1");
            $countNewCustomers=$countNewCustomers[0]->countNewCustomers;
            $view->with(['doneWorks'=>$countDoneWorks,'remainedWorks'=>$countNotDone,'countNewCustomers'=>$countNewCustomers,
            'inbox'=>$inbox,'alarmTime'=>$alarmTime,'reffs'=>$reffs,'countAlarms'=>$countAlarm,'countInactives'=>$inactives]);
        });
    }
}
