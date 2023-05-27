<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Response;
use DB;
use Session;
use Carbon\Carbon;
use \Morilog\Jalali\Jalalian;
class Product extends Controller
{
    public function getProducts(Request $request)
    {
        $products=DB::table("Shop.dbo.PubGoods")->where("GoodSn","!=",0)->where("GoodName","!=","")->where("GoodGroupSn",">","69")->get();
        return Response::json($products);
    }
    public function getTenLastSales(Request $request)
    {
        $kalaId=$request->get("kalaId");
        $lastTenSales=DB::select("SELECT * FROM (SELECT * FROM (
            SELECT * FROM (
            SELECT TOP 10 SnGood,Amount,PackType,Fi,Price,SnFact FROM Shop.dbo.FactorBYS  WHERE SnGood=$kalaId order by FactorBYS.TimeStamp desc)a
            JOIN (SELECT SerialNoHDS,CustomerSn,FactDate,FactType FROM Shop.dbo.FactorHDS)b on a.SnFact=b.SerialNoHDS)c
            JOIN (SELECT Name,PCode,PSN FROM Shop.dbo.Peopels )d on c.CustomerSn=d.PSN)A where FactType=3");
        return Response::json($lastTenSales);
    }
    public function getProductMainGroups(Request $request)
    {
        $maingGroups=DB::select("SELECT title,id FROM NewStarfood.dbo.Star_Group_Def WHERE selfGroupId=0");
        return Response::json($maingGroups);
    }
    public function getSubGroups(Request $request)
    {
        $superGroupId=$request->get("mainGroupId");
        $suBGroups=DB::select("SELECT * FROM NewStarfood.dbo.Star_Group_Def WHERE selfGroupId=$superGroupId");
        return Response::json($suBGroups);
    }
    public function filterAllKala(Request $request)
    {
        $kalaNameCode= $request->get("kalaNameCode");
        $mainGroup  = $request->get("mainGroup");
        $subGroup  = $request->get("subGroup");
        $searchKalaStock  = $request->get("searchKalaStock");
        $searchKalaActiveOrNot  = $request->get("searchKalaActiveOrNot");
        $searchKalaExistInStock  = $request->get("searchKalaExistInStock");
        $assesFirstDate  = $request->get("assesFirstDate");
        $assesSecondDate='1490/01/01';
        if(strlen($request->get("assesSecondDate"))>3){
        $assesSecondDate  = $request->get("assesSecondDate");
        }
        $existanceQuery="";
        if($searchKalaExistInStock==1){
            $existanceQuery=">=1";
        }
        if($searchKalaExistInStock==0){
            $existanceQuery="=0";
        }

        if($searchKalaExistInStock==-1){
            $existanceQuery=">=-2000";
        }



        $listKala=DB::select("SELECT * FROM(
            SELECT PubGoods.GoodName,PubGoods.GoodSn,CRM.dbo.getGoodPrice(GoodSn,'Price3') AS Price3,
            CRM.dbo.getGoodPrice(GoodSn,'Price4') AS Price4,CRM.dbo.getGoodExistance(GoodSn,1399,$searchKalaStock) AS Amount
            ,GoodCde,PubGoods.GoodGroupSn,PubGoods.CompanyNo,
            FORMAT(CONVERT(DATE,CRM.dbo.getLastDateGoodSale(GoodSn)),'yyyy/MM/dd','fa-IR') AS lastDate,
            CRM.dbo.getGoodHiddenState(GoodSn) AS hideKala,CRM.dbo.getGoodMainGroupStarfood(GoodSn) AS firstGroupName,CRM.dbo.getGoodSubGroupStarfood(GoodSn) AS secondGroupName FROM Shop.dbo.PubGoods 
            )a
            WHERE GoodName!='' AND a.GoodSn!=0 AND GoodGroupSn >49 AND CompanyNo=5 AND (GoodName LIKE '%$kalaNameCode%' OR GoodCde LIKE '%$kalaNameCode%')
            AND lastDate >='$assesFirstDate' AND lastDate <='$assesSecondDate' AND hideKala LIKE '%$searchKalaActiveOrNot%' AND Amount $existanceQuery AND firstGroupName LIKE N'%$mainGroup%' AND secondGroupName LIKE N'%$subGroup%'
            ORDER BY Amount desc");
        return Response::json($listKala);
    }

    public function getReturnedKala(Request $request)
    {

        $kalaNameCode= $request->get("kalaNameCode");
        $mainGroup  = $request->get("mainGroup");
        $subGroup  = $request->get("subGroup");
        $searchKalaStock  = $request->get("searchKalaStock");
        $searchKalaActiveOrNot  = $request->get("searchKalaActiveOrNot");
        $searchKalaExistInStock  = $request->get("searchKalaExistInStock");
        $returnedFirstDate  = $request->get("returnFirstDate");
        $returnedSecondDate='1490/01/01';
        if(strlen($request->get("returnSecondDate"))>3){
        $returnedSecondDate  = $request->get("returnSecondDate");
        }
        $existanceQuery="";
        if($searchKalaExistInStock==1){
            $existanceQuery=">=1";
        }
        if($searchKalaExistInStock==0){
            $existanceQuery="=0";
        }

        if($searchKalaExistInStock==-1){
            $existanceQuery=">=-2000";
        }

        $listKala=DB::select("SELECT * from(
			SELECT * FROM(
            SELECT PubGoods.GoodName,PubGoods.GoodSn,CRM.dbo.getGoodPrice(GoodSn,'Price3') AS Price3,
            CRM.dbo.getGoodPrice(GoodSn,'Price4') AS Price4,CRM.dbo.getGoodExistance(GoodSn,1399,$searchKalaStock) AS Amount
            ,GoodCde,PubGoods.GoodGroupSn,PubGoods.CompanyNo,
            CRM.dbo.getGoodLastDateReturn(GoodSn) AS lastDate,
            CRM.dbo.getGoodHiddenState(GoodSn) AS hideKala,CRM.dbo.getGoodMainGroupStarfood(GoodSn) AS firstGroupName,CRM.dbo.getGoodSubGroupStarfood(GoodSn) AS secondGroupName FROM Shop.dbo.PubGoods 
            )a
            JOIN (SELECT  DISTINCT SnGood FROM SHop.dbo.FactorHDS Join Shop.dbo.FactorBYS on SerialNoHDS=FactorBYS.SnFact  where FactType=4 and FactDate>='$returnedFirstDate' AND FactDate <='$returnedSecondDate')r on a.GoodSn=r.SnGood)c
            WHERE  GoodGroupSn >49 AND CompanyNo=5 AND (GoodName LIKE '%$kalaNameCode%' OR GoodCde LIKE '%$kalaNameCode%')
            AND  hideKala LIKE '%$searchKalaActiveOrNot%' AND Amount $existanceQuery AND firstGroupName LIKE N'%$mainGroup%' AND secondGroupName LIKE N'%$subGroup%'
            ORDER BY Amount desc");
        return Response::json($listKala);
    }

    public function getRakidKala(Request $request)
    {
        $kalaNameCode= $request->get("kalaNameCode");
        $mainGroup  = $request->get("mainGroup");
        $subGroup  = $request->get("subGroup");
        $searchKalaStock  = $request->get("searchKalaStock");
        $searchKalaActiveOrNot  = $request->get("searchKalaActiveOrNot");
        $searchKalaExistInStock  = $request->get("searchKalaExistInStock");
        $rakidFirstDate  = $request->get("rakidFirstDate");
        $rakidSecondDate='1490/01/01';
        if(strlen($request->get("rakidSecondDate"))>3){
        $rakidSecondDate  = $request->get("rakidSecondDate");
        }
        $existanceQuery="";
        if($searchKalaExistInStock==1){
            $existanceQuery=">=1";
        }
        if($searchKalaExistInStock==0){
            $existanceQuery="=0";
        }

        if($searchKalaExistInStock==-1){
            $existanceQuery=">=-2000";
        }

        $listKala=DB::select("SELECT * FROM(
            SELECT PubGoods.GoodName,PubGoods.GoodSn,CRM.dbo.getGoodPrice(GoodSn,'Price3') AS Price3,
            CRM.dbo.getGoodPrice(GoodSn,'Price4') AS Price4,CRM.dbo.getGoodExistance(GoodSn,1399,$searchKalaStock) AS Amount
            ,GoodCde,PubGoods.GoodGroupSn,PubGoods.CompanyNo,
            Format(CONVERT(DATE,CRM.dbo.getLastDateGoodSale(GoodSn)),'yyyy/MM/dd','fa-ir') AS lastDate,
            CRM.dbo.getGoodHiddenState(GoodSn) AS hideKala,CRM.dbo.getGoodMainGroupStarfood(GoodSn) AS firstGroupName,CRM.dbo.getGoodSubGroupStarfood(GoodSn) AS secondGroupName FROM Shop.dbo.PubGoods 
            )a
			WHERE CompanyNo=5
		    and not exists (SELECT  SnGood FROM Shop.dbo.FactorHDS Join Shop.dbo.FactorBYS on SerialNoHDS=FactorBYS.SnFact  WHERE FactType=3 AND FactDate>='$rakidFirstDate' AND FactDate <='$rakidSecondDate' AND SnGood=GoodSn)
            AND  GoodGroupSn >49 AND (GoodName LIKE '%$kalaNameCode%' OR GoodCde LIKE '%$kalaNameCode%')
            AND  hideKala LIKE '%$searchKalaActiveOrNot%' AND Amount $existanceQuery AND firstGroupName LIKE N'%$mainGroup%' AND secondGroupName LIKE N'%$subGroup%' ORDER BY Amount desc");
        return Response::json($listKala);
    }

    public function getReturnedFactors(Request $request)
    {
        $firstDate="";
        if(strlen($request->get("firstDate"))>3){
            $firstDate=$request->get("firstDate");
        }
        $secondDate='1490/01/01';
        if(strlen($request->get("secondDate"))>3){
            $secondDate=$request->get("secondDate");
        }
        $firstTime="";
        if(strlen($request->get("firstTime"))>3){
            $firstTime=$request->get("firstTime");
        }
        $secondTime='23:59:59';
        if(strlen($request->get("secondTime"))>3){
            $secondTime=$request->get("secondTime");
        }
        $firstFactNo=0;
        if($request->get("firstFactNo")>0){
            $firstFactNo=$request->get("firstFactNo");
        }
        $secondFactNo=20000000;
        if($request->get("secondFactNo")>0){
            $secondFactNo=$request->get("secondFactNo");
        }
        $customreName=$request->get("customreName");
        $setterName=$request->get("setterName");
        $stockSn=$request->get("stockSn");
        $factNo=$request->get("FactNo");
        $goodName=$request->get("goodName");
        $allReturnedFactors=DB::select("SELECT * FROM (SELECT NameUser,Name,FactType,FactorHDS.CompanyNo,FactDate,PCode,FactNo,TotalPriceHDS,FactDesc,DateBargiri,TimeBargiri,SnUser1,FactTime,SerialNoHDS,SnStockIn,NameStock FROM Shop.dbo.FactorHDS JOIN Shop.dbo.Peopels ON CustomerSn=PSN
        JOIN Shop.dbo.Users ON Users.SnUser=SnUser1
		Join Shop.dbo.Stocks on SnStock=SnStockIn
		)A
        WHERE FactType=4 AND CompanyNo=5  AND NameStock LIKE '%$stockSn%'
        AND FactDate>='$firstDate' AND FactDate<='$secondDate' AND NameUser LIKE '%$setterName%' AND Name LIKE '%$customreName%'
        And EXISTS (SELECT GoodSn FROM shop.dbo.FactorBYS JOIN SHop.dbo.PubGoods ON FactorBYS.SnGood=PubGoods.GoodSn WHERE PubGoods.GoodName LIKE '%$goodName%' and SnFact=SerialNoHDS)
        AND FactNo>=$firstFactNo AND FactNo<=$secondFactNo AND FactTime>='$firstTime' AND FactTime<='$secondTime' order by FactDate desc");
        return Response::json($allReturnedFactors);
    }
    public function getStocks(Request $request)
    {
        $stocks=DB::select("SELECT * FROM Shop.dbo.Stocks WHERE CompanyNo=5");
        return Response::json($stocks);
    }
    public function getFactorSetter(Request $request)
    {
        $factorSetters=DB::select("SELECT SnUser,NameUser from Shop.dbo.Users where IsSupervisor !=1 and CompanyNo=5");
        return Response::json($factorSetters);
    }
    public function getGoodSalseRounds(Request $request)
    {
        $productId=$request->get("productId");
        $salesRound=DB::select("SELECT Amount,GoodCde,Name,SnGood,Fi,PubGoods.GoodName,SnFact,FactorBYS.Price,Format(CONVERT(Date,FactorBYS.TimeStamp),'yyyy/MM/dd','fa-ir') as saleDate from Shop.dbo.FactorBYS join Shop.dbo.FactorHDS on SnFact=FactorHDS.SerialNoHDS join Shop.dbo.Peopels on PSN=CustomerSn
        Join Shop.dbo.PubGoods on SnGood=GoodSn
        where SnGood=$productId and FactType=3 and Peopels.SaleLevel=3 order by saleDate desc");
        return Response::json($salesRound);
    }
    public function getReturnedFactorsHistory(Request $request)
    {
        $history=$request->get("HISTORY");
        $factors;
        if($history=="TODAY"){

            $toDay = Jalalian::fromCarbon(Carbon::today())->format('Y/m/d');
            $factors=DB::select("SELECT * FROM (SELECT NameUser,Name,FactType,FactorHDS.CompanyNo,FactDate,PCode,FactNo,TotalPriceHDS,FactDesc,DateBargiri,TimeBargiri,SnUser1,FactTime,SerialNoHDS,SnStockIn,NameStock FROM Shop.dbo.FactorHDS JOIN Shop.dbo.Peopels ON CustomerSn=PSN
            JOIN Shop.dbo.Users ON Users.SnUser=SnUser1
            Join Shop.dbo.Stocks on SnStock=SnStockIn
            )A
            WHERE FactType=4 AND CompanyNo=5
            AND FactDate='$toDay' order by FactDate desc");
        }
        if($history=="YESTERDAY"){
            $yesterdayOfWeek = Jalalian::fromCarbon(Carbon::yesterday())->getDayOfWeek();
            $yesterday;
            if($yesterdayOfWeek==6){
                $yesterday = Jalalian::fromCarbon(Carbon::yesterday()->subDays(1))->format('Y/m/d');
            }else{
                $yesterday = Jalalian::fromCarbon(Carbon::yesterday())->format('Y/m/d');
            }
            $factors=DB::select("SELECT * FROM (SELECT NameUser,Name,FactType,FactorHDS.CompanyNo,FactDate,PCode,FactNo,TotalPriceHDS,FactDesc,DateBargiri,TimeBargiri,SnUser1,FactTime,SerialNoHDS,SnStockIn,NameStock FROM Shop.dbo.FactorHDS JOIN Shop.dbo.Peopels ON CustomerSn=PSN
            JOIN Shop.dbo.Users ON Users.SnUser=SnUser1
            Join Shop.dbo.Stocks on SnStock=SnStockIn
            )A
            WHERE FactType=4 AND CompanyNo=5
            AND FactDate='$yesterday' order by FactDate desc");
        }
        if($history=="ALL"){
            $factors=DB::select("SELECT * FROM (SELECT NameUser,Name,FactType,FactorHDS.CompanyNo,FactDate,PCode,FactNo,TotalPriceHDS,FactDesc,DateBargiri,TimeBargiri,SnUser1,FactTime,SerialNoHDS,SnStockIn,NameStock FROM Shop.dbo.FactorHDS JOIN Shop.dbo.Peopels ON CustomerSn=PSN
            JOIN Shop.dbo.Users ON Users.SnUser=SnUser1
            Join Shop.dbo.Stocks on SnStock=SnStockIn
            )A
            WHERE FactType=4 AND CompanyNo=5
             order by FactDate desc");
        }
        if($history=="LASTHUNDRED"){
            $factors=DB::select("SELECT TOP 100 * FROM (SELECT NameUser,Name,FactType,FactorHDS.CompanyNo,FactDate,PCode,FactNo,TotalPriceHDS,FactDesc,DateBargiri,TimeBargiri,SnUser1,FactTime,SerialNoHDS,SnStockIn,NameStock FROM Shop.dbo.FactorHDS JOIN Shop.dbo.Peopels ON CustomerSn=PSN
            JOIN Shop.dbo.Users ON Users.SnUser=SnUser1
            Join Shop.dbo.Stocks on SnStock=SnStockIn
            )A
            WHERE FactType=4 AND CompanyNo=5
            order by FactDate desc");
        }
        return Response::json($factors);
    }



    public function salesReport(){
        $regions=DB::select("SELECT * FROM Shop.dbo.MNM WHERE CompanyNo=5 and FatherMNM=80");
        $cities=DB::select("SELECT * FROM Shop.dbo.MNM WHERE CompanyNo=5 and FatherMNM=79");
        $salesExpert=new SalseExper;
        $adminType=$salesExpert->getAdminType(Session::get('asn'));
        $allEmployies=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE adminType!=4 and deleted=0");
            if($adminType!=5){
                $poshtibans=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE (bossId=".Session::get('asn')." or id=".Session::get('asn').")  AND deleted=0");
            }else{
                $poshtibans=$allEmployies;
            }
    
        return view("reports.salesReport",['regions'=>$regions,'poshtibans'=>$poshtibans,'cities'=>$cities]);
    }

    public function getSalesReportInfo(Request $request)
    {
        $firstDate='';
        $secondDate='1499/01/01';
        if(strlen($request->get("firstDate"))>3){
            $firstDate=$request->get("firstDate");
        }
        if(strlen($request->get("secondDate"))>3){
            $secondDate=$request->get("secondDate");
        }

        $nameCode=$request->get("nameCode");
        $adminId=$request->get("adminId");
        $mantaghehName=$request->get("mantaghehName");
        $factType=$request->get("factType");
        $factTypeQuery="FactType=$factType AND";
        if(strlen($factType)<1){
            $factTypeQuery="";
        }
        $adminType=Session::get("adminType");
        $joinType="INNER JOIN";
        if($adminType==5){
            $joinType="LEFT JOIN";
        }
        $adminId=Session::get("asn");
        $reportInfo=array();
        if($adminId==0){
            $reportInfo=DB::select("SELECT * FROM (SELECT PSN,PCode,Peopels.CompanyNo,FactDate,FactType,Name,sumAllMoney,ISNULL(adminName,'') AS adminName,adminId,CRM.dbo.getCustomerMantagheh(SnMantagheh) AS MantaghehName FROM Shop.dbo.Peopels 
                                    INNER JOIN(SELECT (NetPriceHDS/10) AS sumAllMoney,CustomerSn,FactDate,FactType FROM Shop.dbo.FactorHDS  WHERE $factTypeQuery FactDate>='$firstDate' AND FactDate<='$secondDate')a ON a.CustomerSn=PSN 
                                    $joinType (SELECT CONCAT(name,lastName) AS adminName,admin.id AS adminId,customer_id  FROM CRM.dbo.crm_admin admin JOIN CRM.dbo.crm_customer_added added ON admin.id=added.admin_id AND returnState=0  and admin.id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))admins ON admins.customer_id=PSN)b
                                    WHERE (PCode LIKE '%$nameCode%' OR Name LIKE '%$nameCode%') AND CompanyNo=5  AND MantaghehName LIKE '%$mantaghehName%' ORDER BY PSN");
        }else{
            $reportInfo=DB::select("SELECT * FROM (SELECT PSN,PCode,Peopels.CompanyNo,FactDate,FactType,Name,sumAllMoney,ISNULL(adminName,'') AS adminName,adminId,CRM.dbo.getCustomerMantagheh(SnMantagheh) AS MantaghehName FROM Shop.dbo.Peopels 
                                    INNER JOIN(SELECT (NetPriceHDS/10) AS sumAllMoney,CustomerSn,FactDate,FactType FROM Shop.dbo.FactorHDS  WHERE $factTypeQuery FactDate>='$firstDate' AND FactDate<='$secondDate')a ON a.CustomerSn=PSN 
                                    $joinType (SELECT CONCAT(name,lastName) AS adminName,admin.id AS adminId,customer_id  FROM CRM.dbo.crm_admin admin JOIN CRM.dbo.crm_customer_added added ON admin.id=added.admin_id AND returnState=0  and admin.id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))admins ON admins.customer_id=PSN)b
                                    WHERE (PCode LIKE '%$nameCode%' OR Name LIKE '%$nameCode%') AND CompanyNo=5  AND MantaghehName LIKE '%$mantaghehName%' AND adminId = $adminId ORDER BY PSN");
        }
        return Response::json($reportInfo);
    }
}
