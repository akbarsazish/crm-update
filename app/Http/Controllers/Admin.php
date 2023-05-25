<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use App\Http\Controllers\Poshtiban;
use App\Http\Controllers\SalseExper;
use DB;
use Response;
use Carbon\Carbon;
use \Morilog\Jalali\Jalalian;
use Session;
class Admin extends Controller
{
    public function index(Request $request){
        $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin
        LEFT JOIN (SELECT MIN(addedTime) takhsisDate,admin_id,COUNT(customer_id) AS countCustomer  FROM CRM.dbo.crm_customer_added
        JOIN CRM.dbo.crm_admin ON crm_admin.id=crm_customer_added.admin_id WHERE returnState=0 group by admin_id)a on a.admin_id=crm_admin.id
        WHERE deleted =0");
        $regions=DB::select("SELECT * FROM Shop.dbo.MNM WHERE CompanyNo=5 and SnMNM>82");
        $cities=DB::select("Select * FROM Shop.dbo.MNM WHERE  CompanyNo=5 and RecType=1 AND FatherMNM=79");
        $adminList=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where('deleted',0)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","crm_admin.discription")->orderby("admintype")->get();
        // dd($adminList);
        return View('admin.assignCustomer',['admins'=>$admins,'regions'=>$regions,'cities'=>$cities,'adminList'=>$adminList]);
    }
    
    public function editAssignCustomer(Request $request) {
        $adminId=$request->get("adminId");
        $customers=DB::select("SELECT Name,NameRec,PSN FROM Shop.dbo.Peopels 
        JOIN Shop.dbo.MNM on Peopels.SnMantagheh=SnMNM
        WHERE PSN NOT IN ( SELECT distinct customer_id FROM CRM.dbo.crm_customer_added where returnState=0 and customer_id is not null)
        AND PSN not in (SELECT customerId FROM CRM.dbo.crm_inactiveCustomer where customerId is not null and state=1)
        AND PSN not in(SELECT customerId FROM CRM.dbo.crm_returnCustomer where customerId is not null and returnState=1)
        AND Peopels.CompanyNo=5 AND IsActive=1
        AND GroupCode IN ( ".implode(",",Session::get("groups")).") And Name!=''");

        // dd($customers);

        $addedCustomers=DB::select("SELECT Name,NameRec,PSN FROM Shop.dbo.Peopels 
        JOIN Shop.dbo.MNM on Peopels.SnMantagheh=SnMNM WHERE Peopels.PSN IN (SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE admin_id=".$adminId." and crm_customer_added.returnState=0)  AND Peopels.CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).")");
        $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where("crm_admin.id",$adminId)->where('deleted',0)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","crm_admin.discription")->orderby("admintype")->get();
Session::put("adminName",$admins[0]->name.' '.$admins[0]->lastName);
        $regions=DB::select("SELECT * FROM Shop.dbo.MNM WHERE CompanyNo=5 and SnMNM>82");
        $cities=DB::select("Select * FROM Shop.dbo.MNM WHERE  CompanyNo=5 and RecType=1 AND FatherMNM=79");
        $adminList=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where('deleted',0)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","crm_admin.discription")->orderby("admintype")->get();
        
        return view('admin.editAssignCustomer', ['admins'=>$admins[0],'regions'=>$regions,'cities'=>$cities,'adminList'=>$adminList,'customers'=>$customers,'addedCustomers'=>$addedCustomers,'adminId'=>$adminId]);
    }


    public function listKarbaran(Request $request)
    {
        $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where('deleted',0)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","crm_admin.discription")->orderby("admintype")->get();
        $regions=DB::select("SELECT * FROM Shop.dbo.MNM WHERE CompanyNo=5 and SnMNM>82");
        $cities=DB::select("Select * FROM Shop.dbo.MNM WHERE  CompanyNo=5 and RecType=1 AND FatherMNM=79");
        $saleLines=DB::select("SELECT * FROM CRM.dbo.crm_SaleLine where deleted=0");
        foreach ($saleLines as $line) {
            $managers=DB::table("CRM.dbo.crm_admin")->where('saleLineId',$line->SaleLineSn)->where("deleted",0)->where("employeeType",1)->get();
            $line->manager=$managers;
            foreach($line->manager as $manager){
                $heads=DB::table("CRM.dbo.crm_admin")->where('bossId',$manager->id)->where("deleted",0)->get();
                $manager->head=$heads;
                foreach($manager->head as $head){
                    $employee=DB::table("CRM.dbo.crm_admin")->where('bossId',$head->id)->where("deleted",0)->get();
                    $head->employee=$employee;
                }
            }
        }
        $managers=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE employeeType=1 and deleted=0");
        $heads=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE employeeType=2  and deleted=0");
        return View('admin.listKarbaran',['admins'=>$admins,'regions'=>$regions,'cities'=>$cities,'saleLines'=>$saleLines,'managers'=>$managers,'heads'=>$heads]);
    }

    public function karbaranOperations(Request $request)
    {
        $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where('deleted',0)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","crm_admin.discription")->orderby("admintype")->get();
        $regions=DB::select("SELECT * FROM Shop.dbo.MNM WHERE CompanyNo=5 and SnMNM>82");
        $cities=DB::select("Select * FROM Shop.dbo.MNM WHERE  CompanyNo=5 and RecType=1 AND FatherMNM=79");
        $saleLines=DB::select("SELECT * FROM CRM.dbo.crm_SaleLine where deleted=0");
        foreach ($saleLines as $line) {
            $managers=DB::table("CRM.dbo.crm_admin")->where('saleLineId',$line->SaleLineSn)->where("employeeType",1)->get();
            $line->manager=$managers;
            foreach($line->manager as $manager){
                $heads=DB::table("CRM.dbo.crm_admin")->where('bossId',$manager->id)->get();
                $manager->head=$heads;
                foreach($manager->head as $head){
                    $employee=DB::table("CRM.dbo.crm_admin")->where('bossId',$head->id)->get();
                    $head->employee=$employee;
                }
            }
        }
        $managers=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE employeeType=1 and deleted=0");
        $heads=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE employeeType=2  and deleted=0");

        return View('admin.karbaranOperation',['admins'=>$admins,'regions'=>$regions,'cities'=>$cities,'saleLines'=>$saleLines,'managers'=>$managers,'heads'=>$heads]);
    }
	
	public function crmTerminal(Request $request)
    {
        switch (Session::get("adminType")) {
            case 1:
                return redirect('/home');
                break;
            case 2:
                return redirect('/calendar');
                break;
            case 3:
                return redirect('/calendar');
                break; 
            case 4:
                return redirect('/crmDriver?asn='.Session::get("dsn"));
                break;   
            case 5:
                return redirect('/home');
                break;       
            case 6:
                return redirect('/home');
                break;  
            case 7:
                return redirect('/home');
                break;        
            default:
                return redirect('/notfound');
                break;
        }
    }
	    public function addProvinceCode(Request $request)
    {
        $provinceCode=$request->get('provinceCode');
        $provinceCodes=DB::table("NewStarfood.dbo.star_provincePhoneCode")->where('provinceCode',$provinceCode)->get();
        if(count($provinceCodes)<1){
            DB::table("NewStarfood.dbo.star_provincePhoneCode")->insert(['provinceCode'=>''.$provinceCode.'']);
        }
        $provinceCodes=DB::table("NewStarfood.dbo.star_provincePhoneCode")->get();
        return Response::json($provinceCodes);
    }

    public function dashboard() {
        $allCustomerCount=DB::select("SELECT COUNT(PSN) as countAllCustomers FROM Shop.dbo.Peopels WHERE  Peopels.CompanyNo=5 and Peopels.GroupCode IN ( ".implode(",",Session::get("groups")).")");
        
        $allCustomers=$allCustomerCount[0]->countAllCustomers;
        
        $allActiveCustomerCount=DB::select("SELECT COUNT(PSN) AS countActiveCustomers FROM Shop.dbo.Peopels  WHERE  IsActive=1 AND NOT EXISTS (SELECT customerId FROM CRM.dbo.crm_inactiveCustomer WHERE customerId=PSN AND state=0)
        AND NOT EXISTS(SELECT customerId FROM CRM.dbo.crm_returnCustomer WHERE returnState=1 AND customerId=PSN) AND CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).")");
        
        $allActiveCustomers=$allActiveCustomerCount[0]->countActiveCustomers;
        
        $allInActiveCustomerCount=DB::select("SELECT COUNT(PSN) AS countInActiveCustomers FROM Shop.dbo.Peopels WHERE  exists (SELECT customerId FROM CRM.dbo.crm_inactiveCustomer WHERE  state=1 and customerId=PSN) AND  Peopels.CompanyNo=5 AND Peopels.GroupCode IN ( ".implode(",",Session::get("groups")).")");
        
        $allInActiveCustomers=$allInActiveCustomerCount[0]->countInActiveCustomers;

        $allInReturnedCustomerCount=DB::select("SELECT COUNT(PSN) AS allInReturnedCustomerCount FROM Shop.dbo.Peopels WHERE  exists (SELECT customerId FROM CRM.dbo.crm_returnCustomer WHERE  returnState=1 and customerId=PSN) AND  Peopels.CompanyNo=5 AND Peopels.GroupCode IN ( ".implode(",",Session::get("groups")).")");
        
        $allInReturnedCustomerCount=$allInReturnedCustomerCount[0]->allInReturnedCustomerCount;
        
        $allAddedCustomersCount=DB::select("SELECT COUNT(PSN) as countHasAdminCustomers FROM Shop.dbo.Peopels  WHERE  exists (select customer_id FROM CRM.dbo.crm_customer_added where PSN=customer_id and returnState=0) ");
       
        $allAddedCustomersCount=$allAddedCustomersCount[0]->countHasAdminCustomers;
        
        $allGoodsCount=DB::select("SELECT COUNT(GoodSn) countAllGoods FROM Shop.dbo.PubGoods WHERE  PubGoods.GoodGroupSn>49 and PubGoods.CompanyNo=5");
        
        $allGoods=$allGoodsCount[0]->countAllGoods;
        
        $allPrebuyableCount=DB::select("SELECT COUNT(GoodSn) countPrepbuyables FROM Shop.dbo.PubGoods WHERE  GoodSn in(SELECT productId FROM NewStarfood.dbo.star_GoodsSaleRestriction WHERE  activePishKharid=1) and PubGoods.GoodGroupSn>49 and PubGoods.CompanyNo=5");
        
        $allPrebuyableGoods=$allPrebuyableCount[0]->countPrepbuyables;
        
        $allBoughtGoodsCount=DB::select("SELECT COUNT(GoodSn) countBoughtGoods FROM Shop.dbo.PubGoods WHERE  GoodSn in(SELECT DISTINCT SnGood FROM Shop.dbo.FactorBYS JOIN   Shop.dbo.PubGoods ON FactorBYS.SnGood=PubGoods.GoodSn WHERE  PubGoods.GoodGroupSn>49 and PubGoods.CompanyNo=5) and PubGoods.GoodGroupSn>49 and PubGoods.CompanyNo=5 
         ");
        
        $boughtGoods=$allBoughtGoodsCount[0]->countBoughtGoods;
        
        $allGoodsInBrandsCount=DB::select("SELECT COUNT(productId) as countBrandProducts FROM(
                                    SELECT DISTINCT productId FROM NewStarfood.dbo.star_add_prod_brands WHERE  brandId>6)a");
        
        $allBrandGoods=$allGoodsInBrandsCount[0]->countBrandProducts;
        
        $allBrandsCount=DB::select("SELECT COUNT(id) as countBrands FROM NewStarfood.dbo.star_brands");
        
        $allBrands=$allBrandsCount[0]->countBrands;
        
        $allMainGroupCount=DB::select("SELECT COUNT(id) as countMainGroups FROM NewStarfood.dbo.Star_Group_Def WHERE  selfGroupId=0");
        
        $allmainGroup=$allMainGroupCount[0]->countMainGroups;
        
        $allSubGroupCount=DB::select("SELECT COUNT(id) as countSubGroups FROM NewStarfood.dbo.Star_Group_Def WHERE  selfGroupId>0");
        
        $allSubGroups=$allSubGroupCount[0]->countSubGroups;
        
        $allReturnedCustomers=DB::select("SELECT COUNT(id) as countReturnedCustomers FROM CRM.dbo.crm_returnCustomer WHERE  returnState=1");
        
        $allReturnedCustomer=$allReturnedCustomers[0]->countReturnedCustomers;
        
        $admins=DB::select("SELECT * FROM(
                        SELECT crm_admin.id as adminId,crm_admin.name,deleted,crm_admin.lastName,crm_admin.phone,crm_admin.address,crm_adminType.adminType FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id)a
                        left JOIN(SELECT COUNT(id) as countCustomer,admin_id FROM CRM.dbo.crm_customer_added WHERE  returnState=0 GROUP BY admin_id)b ON a.adminId=b.admin_id
                        where deleted=0
                        ORDER BY a.adminType asc");
        
        return view('admin.dashboard',['allCustomers'=>$allCustomers,'allActiveCustomers'=>$allActiveCustomers,'allInActiveCustomers'=>$allInActiveCustomers,
                'allAddedCustomersCount'=>$allAddedCustomersCount,'allGoods'=>$allGoods,'prebuyableGoods'=>$allPrebuyableGoods,'allboughtGoods'=>$boughtGoods,'allBrandGoods'=>$allBrandGoods
                ,'allBrands'=>$allBrands,'allmainGroup'=>$allmainGroup,'allSubGroups'=>$allSubGroups,'allReturnedCustomer'=>$allReturnedCustomer,'admins'=>$admins]);
    }
    public function AddAdmin(Request $request){
        $name=$request->post("name");
        $userName=$request->post("userName");
        $lastName=$request->post("lastName");
        $password=$request->post("password");
        $phone=$request->post("phone");
        $address=$request->post("address");
        $sex=$request->post("sex");
        $discription=$request->post("discription");
        $picture=$request->file('picture');
        $manager=$request->post("manager");
        $head=$request->post("head");
        $saleLine=$request->post("saleLine");
        $employeeType=$request->post("employeeType");
        $poshtibanType=$request->post("poshtibanType");
        $adminType=$request->post("adminType");
        if($employeeType==1){
            $adminType=6;
        }
        if($employeeType==2){
            $adminType=7;
        }
        if($employeeType==3){
            $adminType=$poshtibanType;
        }


        // اگر اطلاعات پایه آن بود 
        $baseInfoN = $request->post("baseInfoN");
        // اطلاعات پایه============
        $baseInfoProfileN;
        $infoRdN;
        $specialSettingN;
        $baseInfoSettingN;
        $InfoSettingAccessN;
        $InfoSettingTargetN;
        $rdSentN;
        $rdNotSentN;
        //==========================
        if($baseInfoN=="on"){
            $baseInfoN = 1;
                    // پروفایل با سه تا عنصر اش چک میشوند
                    $baseInfoProfileN = $request->post("baseInfoProfileN");

                    $deleteProfileN = $request->post("deleteProfileN");
                    $editProfileN = $request->post("editProfileN");
                    $seeProfileN = $request->post("seeProfileN");

                if($baseInfoProfileN=="on"){
                    if($deleteProfileN=="on"){
                        $baseInfoProfileN=2;
                    }elseif($editProfileN=="on" and $deleteProfileN!="on"){
                        $baseInfoProfileN=1;
                    }elseif($editProfileN !="on" and $seeProfileN =="on"){
                        $baseInfoProfileN=0;
                    }else{
                        $baseInfoProfileN=-1;
                    }
                }else{
                    $baseInfoProfileN=-1;
                }

            //  اگر آر و دی آن بود وارد شده و وارد نشده چک گردد
            $infoRdN = $request->post("infoRdN");

                if($infoRdN=="on"){
                    $infoRdN = 1;
                    // وارد شده ها چک میگردد
                    $rdSentN = $request->post("rdSentN");

                    $deleteSentRdN = $request->post("deleteSentRdN");
                    $editSentRdN = $request->post("editSentRdN");
                    $seeSentRdN = $request->post("seeSentRdN");

                            
                    if($rdSentN=="on"){
                        $rdSentN=1;
                        if($deleteSentRdN=="on"){
                            $rdSentN=2;
                        }elseif($editSentRdN=="on" and $deleteSentRdN!="on"){
                            $rdSentN=1;
                        }elseif($editSentRdN!="on" and $seeSentRdN=="on"){
                            $rdSentN=0;
                        }else{
                            $rdSentN=-1;
                        }
                    }else{
                        $rdSentN=-1;
                    }

                    // وراد نشده ها چک میگردد
                    $rdNotSentN = $request->post("rdNotSentN");

                    $deleteRdNotSentN = $request->post("deleteRdNotSentN");
                    $editRdNotSentN = $request->post("editRdNotSentN");
                    $seeRdNotSentN = $request->post("seeRdNotSentN");

                    if($rdNotSentN=="on"){
                        $rdNotSentN=1;
                        if($deleteRdNotSentN=="on"){
                            $rdNotSentN=2;
                        }elseif($editRdNotSentN=="on" and $deleteRdNotSentN!="on"){
                            $rdNotSentN=1;
                        }elseif($editRdNotSentN!="on" and $seeRdNotSentN=="on"){
                            $rdNotSentN=0;
                        }else{
                            $rdNotSentN=-1;
                        }
                    }else{
                        $rdNotSentN=-1;
                    }

                }else{
                    $infoRdN=-1;
                    $rdSentN=-1;
                    $rdNotSentN=-1;
                }
                
                // افزودن خط فروش چک میگردد
                $specialSettingN = $request->post("specialSettingN");
                $deleteSaleLineN = $request->post("deleteSaleLineN");
                $editSaleLineN = $request->post("editSaleLineN");
                $seeSaleLineN = $request->post("seeSaleLineN");

                if($specialSettingN=="on"){
                    $specialSettingN=1;
                    if($deleteSaleLineN=="on"){
                        $specialSettingN=2;
                    }elseif($editSaleLineN=="on" and $deleteSaleLineN!="on"){
                        $specialSettingN=1;
                    }elseif($editSaleLineN!="on" and  $seeSaleLineN=="on"){
                        $specialSettingN=0;
                    }else{
                        $specialSettingN=-1;
                    }
                }else{
                    $specialSettingN=-1;
                }
            
            // اگر تنظیمات آن بود 
            $baseInfoSettingN = $request->post("baseInfoSettingN");
            if($baseInfoSettingN=="on"){
                $baseInfoSettingN=1;
                // سطح دسترسی با عناصر اش چک میگردد
                $InfoSettingAccessN = $request->post("InfoSettingAccessN");

                $deleteSettingAccessN = $request->post("deleteSettingAccessN");
                $editSettingAccessN = $request->post("editSettingAccessN");
                $seeSettingAccessN = $request->post("seeSettingAccessN");
                if($InfoSettingAccessN=="on"){
                    $InfoSettingAccessN=1;
                    if($deleteSettingAccessN=="on"){
                    $InfoSettingAccessN=2;
                    }elseif($editSettingAccessN=="on" and $deleteSettingAccessN!="on"){
                    $InfoSettingAccessN=1;
                    }elseif($editSettingAccessN!="on" and $seeSettingAccessN=="on"){
                        $InfoSettingAccessN=0;
                    }else{
                        $InfoSettingAccessN=-1;
                    }
                }else{
                    $InfoSettingAccessN=-1;
                }

                // تارگیت ها و امتیازات چک میگردد
               $InfoSettingTargetN = $request->post("InfoSettingTargetN");

                $deleteSettingTargetN = $request->post("deleteSettingTargetN");
                $editSettingTargetN = $request->post("editSettingTargetN");
                $seeSettingTargetN = $request->post("seeSettingTargetN");
                if($InfoSettingTargetN=="on"){
                    $InfoSettingTargetN=1;
                    if($deleteSettingTargetN=="on"){
                        $InfoSettingTargetN=2;
                    }elseif($editSettingTargetN=="on" and $deleteSettingTargetN!="on"){
                        $InfoSettingTargetN=1;
                    }elseif($editSettingTargetN!="on" and $seeSettingTargetN=="on"){
                        $InfoSettingTargetN=0;
                    }else{
                        $InfoSettingTargetN=-1;
                    }
                }else{
                    $InfoSettingTargetN=-1;
                }


            }else {
                $InfoSettingAccessOpN=-1;
                $InfoSettingTargetN=-1;
                $baseInfoSettingN=-1;
                $InfoSettingAccessN=-1;
            }

        }else{
            $InfoSettingAccessOpN=-1;
            $InfoSettingAccessN=-1;
            $InfoSettingTargetN=-1;
            $baseInfoSettingN=-1;
            //
            $baseInfoProfileN = -1;
            $infoRdN=-1;
            $rdSentN=-1;
            $rdNotSentN=-1;
            //
            $specialSettingN = -1;
            $baseInfoN=-1;
        }

    //   چک کردن تعریف عناصر با سه عناصر اش
    //تعریف عناصر===============
    $declareElementOppN;
    // ==========================
        $declareElementN = $request->post("declareElementN");
        if($declareElementN=="on"){
            $declareElementOppN=1;
            $deletedeclareElementN = $request->post("deletedeclareElementN");
            $editdeclareElementN = $request->post("editdeclareElementN");
            $seedeclareElementN = $request->post("seedeclareElementN");

            if($deletedeclareElementN=="on"){
            $declareElementOppN=2;
            }elseif($editdeclareElementN=="on" and $deletedeclareElementN!="on"){
            $declareElementOppN=1;
            }elseif($editdeclareElementN!="on" and $seedeclareElementN=="on"){
                $declareElementOppN=0;
            }else{
                $declareElementOppN=-1;
            }
        }else{
            $declareElementOppN=-1;
        }

       // اگر عملیات آن بود 
        $oppN = $request->post("oppN");
        $oppManagerN;
        $oppHeadN;
        $oppBazaryabN;
        $oppTakhsisN;
        $oppDriverServiceN;
        $oppBargiriN;
        $oppDriverN;
        $todayoppNazarsanjiN;
        $pastoppNazarsanjiN;
        $DoneoppNazarsanjiN;
        $oppNazarSanjiN;
        $AddOppupDownBonusN;
        $SubOppupDownBonusN;
        $OppupDownBonusN;
        $AddedoppRDN;
        $NotAddedoppRDN;
        $oppRDN;
        $oppjustCalendarN;
        $oppCustCalendarN;
        $oppCalendarN;
        $allalarmoppN;
        $donealarmoppN;
        $NoalarmoppN;
        $alarmoppN;
        $massageOppN;
        $justBargiriOppN;

        if($oppN=="on"){
            $oppN = 1;
            //
            // و اگر تخصسص به کاربر آن بود
            $oppTakhsisN = $request->post("oppTakhsisN");
            if($oppTakhsisN=="on"){
                $oppTakhsisN=-1;
                // مدیران چک گردد
                $oppManagerN = $request->post("oppManagerN");
                $deleteManagerOppN = $request->post("deleteManagerOppN");
                $editManagerOppN = $request->post("editManagerOppN");
                $seeManagerOppN = $request->post("seeManagerOppN");
                if($oppManagerN=="on"){
                    $oppManagerN = 1;
                    if($deleteManagerOppN=="on"){
                    $oppManagerN=2;
                    }elseif($editManagerOppN=="on" and $deleteManagerOppN!="on"){
                    $oppManagerN=1;
                    }elseif($editManagerOppN!="on" and $seeManagerOppN=="on"){
                        $oppManagerN=0;
                    }else{
                        $oppManagerN=-1;
                    }
                }else{
                    $oppManagerN=-1;
                }
                // سرپرستان چک گردد
                $oppHeadN = $request->post("oppHeadN");
                $deleteHeadOppN = $request->post("deleteHeadOppN");
                $editHeadOppN = $request->post("editHeadOppN");
                $seeHeadOppN = $request->post("seeHeadOppN");

                if($oppHeadN == "on"){
                    $oppHeadN = 1;
                    if($deleteHeadOppN=="on"){
                    $oppHeadN=2;
                    }elseif($editHeadOppN=="on" and $deleteHeadOppN!="on"){
                    $oppHeadN=1;
                    }elseif($editHeadOppN!="on" and $seeHeadOppN=="on"){
                        $oppHeadN=0;
                    }else{
                        $oppHeadN=-1;
                    }
                }else{
                    $oppHeadN=-1; 
                }

                // بازاریابها چک گردد
                $oppBazaryabN = $request->post("oppBazaryabN");

                $deleteBazaryabOppN = $request->post("deleteBazaryabOppN");
                $editBazaryabOppN = $request->post("editBazaryabOppN");
                $seeBazaryabOppN = $request->post("seeBazaryabOppN");

                if($oppBazaryabN=="on"){
                    $oppBazaryabN = 1;
                    if($deleteBazaryabOppN=="on"){
                    $oppBazaryabN=2;
                    }elseif($editBazaryabOppN=="on" and $deleteBazaryabOppN!="on"){
                    $oppBazaryabN=1;
                    }elseif($editBazaryabOppN!="on" and $seeBazaryabOppN=="on"){
                        $oppBazaryabN=0;
                    }else{
                        $oppBazaryabN=-1;
                    }
                }else{
                    $oppBazaryabN=-1;
                }

            }else{
                $oppBazaryabN=-1;
                $oppHeadN=-1; 
                $oppManagerN=-1;
                $oppTakhsisN=-1;
            }

            //   راننده ها چگ میگردد
            $oppDriverN = $request->post("oppDriverN");
            if($oppDriverN=="on"){
                $oppDriverN=1;
               // سرویس راننده ها چک میگردد
                $oppDriverServiceN = $request->post("oppDriverServiceN");

                $deleteoppDriverServiceN = $request->post("deleteoppDriverServiceN");
                $editoppDriverServiceN = $request->post("editoppDriverServiceN");
                $seeoppDriverServiceN = $request->post("seeoppDriverServiceN");

                if($oppDriverServiceN=="on"){
                    $oppDriverServiceN = 1;
                    if($deleteoppDriverServiceN=="on"){
                        $oppDriverServiceN=2;
                    }elseif($editoppDriverServiceN=="on" and $deleteoppDriverServiceN!="on"){
                        $oppDriverServiceN=1;
                    }elseif($editoppDriverServiceN!="on" and $seeoppDriverServiceN=="on"){
                        $oppDriverServiceN=0;
                    }else{
                        $oppDriverServiceN=-1;
                    }
                }else{
                    $oppDriverServiceN=-1;
                }


                // بارگیری چک می گردد
                $oppBargiriN = $request->post("oppBargiriN");

                $deleteoppBargiriN = $request->post("deleteoppBargiriN");
                $editoppBargiriN = $request->post("editoppBargiriN");
                $seeoppBargiriN = $request->post("seeoppBargiriN");

                if($oppBargiriN=="on"){
                    $oppBargiriN = 1;
                    if($deleteoppBargiriN=="on"){
                    $oppBargiriN=2;
                    }elseif($editoppBargiriN=="on" and $deleteoppBargiriN!="on"){
                    $oppBargiriN=1;
                    }elseif($editoppBargiriN!="on" and $seeoppBargiriN=="on"){
                        $oppBargiriN=0;
                    }else{
                        $oppBargiriN=-1;

                    }
                }else{
                    $oppBargiriN=-1;
                }
            }else{
                $oppBargiriN=-1;
                $oppDriverServiceN=-1;
                $oppDriverN=1;
            }

           // اگر نظر سنجی آن بود 
            $oppNazarSanjiN = $request->post("oppNazarSanjiN");
            if($oppNazarSanjiN=="on"){
                $oppNazarSanjiN=1;
              // نظرات امروز چک گردد
                $todayoppNazarsanjiN = $request->post("todayoppNazarsanjiN");

                $deletetodayoppNazarsanjiN = $request->post("deletetodayoppNazarsanjiN");
                $edittodayoppNazarsanjiN = $request->post("edittodayoppNazarsanjiN");
                $seetodayoppNazarsanjiN = $request->post("seetodayoppNazarsanjiN");
                if($todayoppNazarsanjiN=="on"){
                    $todayoppNazarsanjiN = 1;
                    if($deletetodayoppNazarsanjiN=="on"){
                        $todayoppNazarsanjiN=2;
                    }elseif($edittodayoppNazarsanjiN=="on" and $deletetodayoppNazarsanjiN!="on"){
                        $todayoppNazarsanjiN=1;
                    }elseif($edittodayoppNazarsanjiN!="on" and $seetodayoppNazarsanjiN=="on"){
                        $todayoppNazarsanjiN=0;
                    }else{
                        $todayoppNazarsanjiN=-1;
                    }
                }else{
                    $todayoppNazarsanjiN=-1;
                }

                // نظرات گذشته چک گردد
                $pastoppNazarsanjiN = $request->post("pastoppNazarsanjiN");

                $deletepastoppNazarsanjiN = $request->post("deletepastoppNazarsanjiN");
                $editpastoppNazarsanjiN = $request->post("editpastoppNazarsanjiN");
                $seepastoppNazarsanjiN = $request->post("seepastoppNazarsanjiN");

                if($pastoppNazarsanjiN=="on"){
                    $pastoppNazarsanjiN = 1;
                    if($deletepastoppNazarsanjiN=="on"){
                    $pastoppNazarsanjiN=2;
                    }elseif($editpastoppNazarsanjiN=="on" and $deletepastoppNazarsanjiN!="on"){
                    $pastoppNazarsanjiN=1;
                    }elseif($editpastoppNazarsanjiN!="on" and $seepastoppNazarsanjiN=="on"){
                        $pastoppNazarsanjiN=0;
                    }else{
                        $pastoppNazarsanjiN=-1;
                    }
                }else{
                    $pastoppNazarsanjiN=-1;
                }
            
                // نظرات انجام شده چک میگردد
                $DoneoppNazarsanjiN = $request->post("DoneoppNazarsanjiN");

                $deleteDoneoppNazarsanjiN = $request->post("deleteDoneoppNazarsanjiN");
                $editDoneoppNazarsanjiN = $request->post("editDoneoppNazarsanjiN");
                $seeDoneoppNazarsanjiN = $request->post("seeDoneoppNazarsanjiN");
                if($DoneoppNazarsanjiN=="on"){
                    $DoneoppNazarsanjiN = 1;
                    if($deleteDoneoppNazarsanjiN=="on"){
                    $DoneoppNazarsanjiN=2;
                    }elseif($editDoneoppNazarsanjiN=="on" and $deleteDoneoppNazarsanjiN!="on"){
                    $DoneoppNazarsanjiN=1;
                    }elseif($editDoneoppNazarsanjiN!="on" and $seeDoneoppNazarsanjiN=="on"){
                        $DoneoppNazarsanjiN=0;
                    }else{
                        $DoneoppNazarsanjiN=-1;
                    }
                }else{
                    $DoneoppNazarsanjiN=-1;
                }
            }else{
                $DoneoppNazarsanjiN=-1;
                $pastoppNazarsanjiN=-1;
                $todayoppNazarsanjiN=-1;
                $oppNazarSanjiN=-1;
            }


            // اگر افزایش و کا هش آن بود
            $OppupDownBonusN = $request->post("OppupDownBonusN");
            if($OppupDownBonusN=="on"){
                $OppupDownBonusN=1;
                // امتیاز های اضافه شده چک گردد
                $AddOppupDownBonusN = $request->post("AddOppupDownBonusN");

                $deleteAddOppupDownBonusN = $request->post("deleteAddOppupDownBonusN");
                $editAddOppupDownBonusN = $request->post("editAddOppupDownBonusN");
                $seeAddOppupDownBonusN = $request->post("seeAddOppupDownBonusN");

                if($AddOppupDownBonusN=="on"){
                    $AddOppupDownBonusN = 1;
                    if($deleteAddOppupDownBonusN=="on"){
                    $AddOppupDownBonusN=2;
                    }elseif($editAddOppupDownBonusN=="on" and $deleteAddOppupDownBonusN!="on"){
                    $AddOppupDownBonusN=1;
                    }elseif($editAddOppupDownBonusN!="on" and $seeAddOppupDownBonusN=="on"){
                        $AddOppupDownBonusN=0;
                    }else{
                        $AddOppupDownBonusN=-1;
                    }
                }else{
                    $AddOppupDownBonusN=-1;
                }

               // امتیاز های کاهش یافته چک گردد
                $SubOppupDownBonusN = $request->post("SubOppupDownBonusN");

                $deleteSubOppupDownBonusN = $request->post("deleteSubOppupDownBonusN");
                $editSubOppupDownBonusN = $request->post("editSubOppupDownBonusN");
                $seeSubOppupDownBonusN = $request->post("seeSubOppupDownBonusN");

                if($SubOppupDownBonusN=="on"){
                     $SubOppupDownBonusN = 1;
                    if($deleteSubOppupDownBonusN=="on"){
                        $SubOppupDownBonusN=2;
                    }elseif($editSubOppupDownBonusN=="on" and $deleteSubOppupDownBonusN!="on"){
                        $SubOppupDownBonusN=1;
                    }elseif($editSubOppupDownBonusN!="on" and $seeSubOppupDownBonusN=="on"){
                        $SubOppupDownBonusN=0;
                    }else{
                        $SubOppupDownBonusN=-1;
                    }
                }else{
                    $SubOppupDownBonusN=-1;
                }
            }else{
                $SubOppupDownBonusN=-1;
                $AddOppupDownBonusN=-1;
                $OppupDownBonusN=-1;
            }

               // اگر آر و دی آن بود 
            $oppRDN = $request->post("oppRDN");
            if($oppRDN =="on"){
                $oppRDN=1;
                // وارد شده ها چک میگردد
                $AddedoppRDN = $request->post("AddedoppRDN");

                $deleteAddedoppRDN = $request->post("deleteAddedoppRDN");
                $editAddedoppRDN = $request->post("editAddedoppRDN");
                $seeAddedoppRDN = $request->post("seeAddedoppRDN");

                if($AddedoppRDN=="on"){
                    $AddedoppRDN = 1;
                    if($deleteAddedoppRDN=="on"){
                        $AddedoppRDN=2;
                    }elseif($editAddedoppRDN=="on" and $deleteAddedoppRDN!="on"){
                        $AddedoppRDN=1;
                    }elseif($editAddedoppRDN!="on" and $seeAddedoppRDN=="on"){
                        $AddedoppRDN=0;
                    }else{
                        $AddedoppRDN=-1;
                    }
                }else{
                    $AddedoppRDN=-1;
                }

                // وارد نشده ها چک میگردد
                $NotAddedoppRDN = $request->post("NotAddedoppRDN");

                $deleteNotAddedoppRDN = $request->post("deleteNotAddedoppRDN");
                $editNotAddedoppRDN = $request->post("editNotAddedoppRDN");
                $seeNotAddedoppRDN = $request->post("seeNotAddedoppRDN");
                if($NotAddedoppRDN=="on"){
                    $NotAddedoppRDN = 1;
                    if($deleteNotAddedoppRDN=="on"){
                    $NotAddedoppRDN=2;
                    }elseif($editNotAddedoppRDN=="on" and $deleteNotAddedoppRDN!="on"){
                    $NotAddedoppRDN=1;
                    }elseif($editNotAddedoppRDN!="on" and $seeNotAddedoppRDN=="on"){
                        $NotAddedoppRDN=0;
                    }else{
                        $NotAddedoppRDN=-1;
                    }
                }else{
                    $NotAddedoppRDN=-1;
                }
            }else{
                $AddedoppRDN = -1;
                $NotAddedoppRDN = -1;
                $oppRDN=-1;
            }


              // اگر تقویم روزانه آن بود 
            $oppCalendarN = $request->post("oppCalendarN");
            if($oppCalendarN=="on"){
                $oppCalendarN=1;
                // تقویم روزانه چک می گردد
                $oppjustCalendarN = $request->post("oppjustCalendarN");

                $deleteoppjustCalendarN = $request->post("deleteoppjustCalendarN");
                $editoppjustCalendarN = $request->post("editoppjustCalendarN");
                $seeoppjustCalendarN = $request->post("seeoppjustCalendarN");

                if($oppjustCalendarN=="on"){
                    $oppjustCalendarN = 1;
                    if($deleteoppjustCalendarN=="on"){
                    $oppjustCalendarN=2;
                    }elseif($editoppjustCalendarN=="on" and $deleteoppjustCalendarN!="on"){
                    $oppjustCalendarN=1;
                    }elseif($editoppjustCalendarN!="on" and $seeoppjustCalendarN=="on"){
                        $oppjustCalendarN=0;
                    }else{
                        $oppjustCalendarN=-1;
                    }
                }else{
                    $oppjustCalendarN=-1;
                    $oppCalendarN=1;
                }

                 //لیست مشتریان چک گردد
                $oppCustCalendarN = $request->post("oppCustCalendarN");

                $deleteoppCustCalendarN = $request->post("deleteoppCustCalendarN");
                $editoppCustCalendarN = $request->post("editoppCustCalendarN");
                $seeoppCustCalendarN = $request->post("seeoppCustCalendarN");

                if($oppCustCalendarN=="on"){
                    $oppCustCalendarN = 1;
                    if($deleteoppCustCalendarN=="on"){
                    $oppCustCalendarN=2;
                    }elseif($editoppCustCalendarN=="on" and $deleteoppCustCalendarN!="on"){
                    $oppCustCalendarN=1;
                    }elseif($editoppCustCalendarN!="on" and $seeoppCustCalendarN=="on"){
                        $oppCustCalendarN=0;
                    }else{
                        $oppCustCalendarN=-1;
                    }
                }else{
                    $oppCustCalendarN=-1;
                }

            }else {
                $oppjustCalendarN = -1;
                $oppCustCalendarN = -1;
                $oppCalendarN=-1;
            }


              // اگر آلارمها آن بود 
            $alarmoppN = $request->post("alarmoppN");
            if($alarmoppN=="on"){
                $alarmoppN = 1;
                // آلارمها چک گردد
                $allalarmoppN = $request->post("allalarmoppN");

                $deleteallalarmoppN = $request->post("deleteallalarmoppN");
                $editallalarmoppN = $request->post("editallalarmoppN");
                $seeallalarmoppN = $request->post("seeallalarmoppN");

                if($allalarmoppN=="on"){
                    $allalarmoppN = 1;
                    if($deleteallalarmoppN=="on"){
                        $allalarmoppN=2;
                    }elseif($editallalarmoppN=="on" and $deleteallalarmoppN!="on"){
                        $allalarmoppN=1;
                    }elseif($editallalarmoppN!="on" and $seeallalarmoppN=="on"){
                        $allalarmoppN=0;
                    }else{
                        $allalarmoppN=-1;
                    }
                }else{
                    $allalarmoppN=-1;
                }

                    // آلارمهای انجام شده چک میگردد
                $donealarmoppN = $request->post("donealarmoppN");

                $deletedonealarmoppN = $request->post("deletedonealarmoppN");
                $editdonealarmoppN = $request->post("editdonealarmoppN");
                $seedonealarmoppN = $request->post("seedonealarmoppN");

                if($donealarmoppN=="on"){
                    $donealarmoppN = 1;
                    if($deletedonealarmoppN=="on"){
                    $donealarmoppN=2;
                    }elseif($editdonealarmoppN=="on" and $deletedonealarmoppN!="on"){
                    $donealarmoppN=1;
                    }elseif($editdonealarmoppN!="on" and $seedonealarmoppN=="on"){
                        $donealarmoppN=0;
                    }else{
                        $donealarmoppN=-1;
                    }
                }else{
                    $donealarmoppN=-1;
                }

                // مشتریان فاقد آلارم چک میگردد
                $NoalarmoppN = $request->post("NoalarmoppN");

                $deleteNoalarmoppN = $request->post("deleteNoalarmoppN");
                $editNoalarmoppN = $request->post("editNoalarmoppN");
                $seeNoalarmoppN = $request->post("seeNoalarmoppN");

                if($NoalarmoppN=="on"){
                        $NoalarmoppN = 1;
                    if($deleteNoalarmoppN=="on"){
                    $NoalarmoppN=2;
                    }elseif($editNoalarmoppN=="on" and $deleteNoalarmoppN!="on"){
                    $NoalarmoppN=1;
                    }elseif($editNoalarmoppN!="on" and $seeNoalarmoppN=="on"){
                        $NoalarmoppN=0;
                    }else{
                        $NoalarmoppN=-1;
                    }
                }else{
                    $NoalarmoppN=-1;
                }

            }else {
                $allalarmoppN = -1;
                $donealarmoppN = -1;
                $NoalarmoppN = -1;
                $alarmoppN=-1;
            }

            // پیامها چک میگردد
            $massageTopOppN = $request->post("massageOppN");

            $deletemassageOppN = $request->post("deletemassageOppN");
            $editmassageOppN = $request->post("editmassageOppN");
            $seemassageOppN = $request->post("seemassageOppN");
            if($massageTopOppN=="on"){
                $massageTopOppN = 1;
                $massageOppN= 0;
                if($deletemassageOppN=="on"){
                    $massageOppN=2;
                }elseif($editmassageOppN=="on" and $deletemassageOppN!="on"){
                    $massageOppN=1;
                }elseif($editmassageOppN!="on" and $seemassageOppN=="on"){
                    $massageOppN=0;
                }else{
                    $massageOppN=-1;
                }
            }else{
                $massageOppN=-1;
                $massageTopOppN=-1;
            }

            // بارگیری چک میگردد
            $justBargiriTopOppN = $request->post("justBargiriOppN");
            if($justBargiriTopOppN=="on"){
                $justBargiriTopOppN = 1;
                $deletejustBargiriOppN = $request->post("deletejustBargiriOppN");
                $editjustBargiriOppN = $request->post("editjustBargiriOppN");
                $seejustBargiriOppN = $request->post("seejustBargiriOppN");

                $justBargiriOppN= 0;
                if($deletejustBargiriOppN=="on"){
                $justBargiriOppN=2;
                }elseif($editjustBargiriOppN=="on" and $deletejustBargiriOppN!="on"){
                $justBargiriOppN=1;
                }elseif($editjustBargiriOppN!="on" and $seejustBargiriOppN=="on"){
                    $justBargiriOppN=0;
                }else{
                    $justBargiriOppN=-1;
                }
            }else{
                $justBargiriOppN=-1;
                $justBargiriTopOppN=-1;
            }

        }else{
            $oppManagerN=-1;
            $oppHeadN=-1;
            $oppBazaryabN=-1;
            $oppTakhsisN=-1;
            $oppDriverServiceN=-1;
            $oppBargiriN=-1;
            $oppDriverN=-1;
            $todayoppNazarsanjiN=-1;
            $pastoppNazarsanjiN=-1;
            $DoneoppNazarsanjiN=-1;
            $oppNazarSanjiN=-1;
            $AddOppupDownBonusN=-1;
            $SubOppupDownBonusN=-1;
            $OppupDownBonusN=-1;
            $AddedoppRDN=-1;
            $NotAddedoppRDN=-1;
            $oppRDN=-1;
            $oppjustCalendarN=-1;
            $oppCustCalendarN=-1;
            $oppCalendarN=-1;
            $allalarmoppN=-1;
            $donealarmoppN=-1;
            $NoalarmoppN=-1;
            $alarmoppN=-1;
            $massageOppN=-1;
            $justBargiriOppN=-1;
            $oppN=-1;

        }

        
        // اگر گزارشات آن بود 
        $reportN = $request->post("reportN");
        $amalKardreportN;
        $managerreportN;
        $HeadreportN;
        $poshtibanreportN;
        $bazaryabreportN;
        $reportDriverN;
        $amalkardCustReportN;
        $loginCustRepN;
        $inActiveCustRepN;
        $noAdminCustRepN;
        $returnedCustRepN;
        $trazEmployeeReportN;
        $nosalegoodsReportN;
        $NoExistgoodsReportN;
        $returnedgoodsReportN;
        $salegoodsReportN;
        $goodsReportN;
        $returnedNTasReportgoodsReportN;
        $tasgoodsReprtN;
        $returnedReportgoodsReportN;
        $goodsbargiriReportN;
        if($reportN =="on"){
            $reportN=1;
            // اگر عملکرد کاربران آن بود 
            $amalKardreportN = $request->post("amalKardreportN");
            if($amalKardreportN=="on"){
                $amalKardreportN=1;
                // مدیران با سه تا عناصر اش چک میگردد
                $managerreportN = $request->post("managerreportN");

                $deletemanagerreportN = $request->post("deletemanagerreportN");
                $editmanagerreportN = $request->post("editmanagerreportN");
                $seemanagerreportN = $request->post("seemanagerreportN");

                if($managerreportN=="on"){
                    if($deletemanagerreportN=="on"){
                        $managerreportN=2;
                    }elseif($editmanagerreportN=="on" and $deletemanagerreportN!="on"){
                        $managerreportN=1;
                    }elseif($editmanagerreportN!="on" and $seemanagerreportN=="on"){
                        $managerreportN=0;
                    }else{
                        $managerreportN=-1;
                    }
                }else{
                    $managerreportN=-1;
                }

                // سرپرستان با سه تا عناصر اش چک میگردد
                    $HeadreportN = $request->post("HeadreportN");

                $deleteHeadreportN = $request->post("deleteHeadreportN");
                $editHeadreportN = $request->post("editHeadreportN");
                $seeHeadreportN = $request->post("seeHeadreportN");

                if($HeadreportN=="on"){
                    if($deleteHeadreportN=="on"){
                        $HeadreportN=2;
                    }elseif($editHeadreportN=="on" and $deleteHeadreportN!="on"){
                        $HeadreportN=1;
                    }elseif($editHeadreportN!="on" and $seeHeadreportN=="on"){
                        $HeadreportN=0;
                    }else{
                        $HeadreportN=-1;
                    }
                }else{
                    $HeadreportN=-1;
                }

                // پشتیبانها با سه از عناصر اش چک میگردد
                $poshtibanreportN = $request->post("poshtibanreportN");

                $deleteposhtibanreportN = $request->post("deleteposhtibanreportN");
                $editposhtibanreportN = $request->post("editposhtibanreportN");
                $seeposhtibanreportN = $request->post("seeposhtibanreportN");

                if($poshtibanreportN=="on"){
                    if($deleteposhtibanreportN=="on"){
                        $poshtibanreportN=2;
                    }elseif($editposhtibanreportN=="on" and $deleteposhtibanreportN!="on"){
                        $poshtibanreportN=1;
                    }elseif($editposhtibanreportN!="on" and $seeposhtibanreportN=="on"){
                        $poshtibanreportN=0;
                    }else{
                        $poshtibanreportN=-1;
                    }
                }else{
                    $poshtibanreportN=-1;
                }

                // بازار یابها چک میگردد
                $bazaryabreportN = $request->post("bazaryabreportN");

                $deletebazaryabreportN = $request->post("deletebazaryabreportN");
                $editbazaryabreportN = $request->post("editbazaryabreportN");
                $seebazaryabreportN = $request->post("seebazaryabreportN");

                if($bazaryabreportN=="on"){
                    if($deletebazaryabreportN=="on"){
                    $bazaryabreportN=2;
                    }elseif($editbazaryabreportN=="on" and $deletebazaryabreportN!="on"){
                    $bazaryabreportN=1;
                    }elseif($editbazaryabreportN!="on" and $seebazaryabreportN=="on"){
                        $bazaryabreportN=0;
                    }else{
                        $bazaryabreportN=-1;
                    }
                }else{
                    $bazaryabreportN=-1;
                }

                // راننده ها چک میگردد
                    $reportDriverN = $request->post("reportDriverN");

                $deletereportDriverN = $request->post("deletereportDriverN");
                $editreportDriverN = $request->post("editreportDriverN");
                $seereportDriverN = $request->post("seereportDriverN");

                if($reportDriverN=="on"){
                    if($deletereportDriverN=="on"){
                        $reportDriverN=2;
                    }elseif($editreportDriverN=="on" and $deletereportDriverN!="on"){
                        $reportDriverN=1;
                    }elseif($editreportDriverN!="on" and $seereportDriverN=="on"){
                        $reportDriverN=0;
                    }else{
                        $reportDriverN=-1;
                    }
                }else{
                    $reportDriverN=-1;
                }

            }else{
                $amalKardreportN=-1;
                $managerreportN = -1;
                $HeadreportN = -1;
                $poshtibanreportN = -1;
                $bazaryabreportN = -1;
                $reportDriverN = -1;
            }
            //  تراز کاربران 
              $trazEmployeeReportN = $request->post("trazEmployeeReportN");

                $deletetrazEmployeeReportN = $request->post("deletetrazEmployeeReportN");
                $edittrazEmployeeReportN = $request->post("edittrazEmployeeReportN");
                $seetrazEmployeeReportN = $request->post("seetrazEmployeeReportN");

                if($trazEmployeeReportN=="on"){
                    if($deletetrazEmployeeReportN=="on"){
                        $trazEmployeeReportN=2;
                    }elseif($edittrazEmployeeReportN=="on" and $deletetrazEmployeeReportN!="on"){
                        $trazEmployeeReportN=1;
                    }elseif($edittrazEmployeeReportN!="on" and $seetrazEmployeeReportN=="on"){
                        $trazEmployeeReportN=0;
                    }else{
                        $trazEmployeeReportN=-1;
                    }
                }else{
                    $trazEmployeeReportN=-1;
                }

                // اگر عملکرد مشتریان آن بود
                 $amalkardCustReportN = $request->post("amalkardCustReportN");
                 if($amalkardCustReportN=="on"){
                    $amalkardCustReportN = 1;
                    // گزارش ورود چک گردد
                       $loginCustRepN = $request->post("loginCustRepN");

                        $deleteloginCustRepN = $request->post("deleteloginCustRepN");
                        $editloginCustRepN = $request->post("editloginCustRepN");
                        $seeloginCustRepN = $request->post("seeloginCustRepN");

                        if($loginCustRepN=="on"){
                            if($deleteloginCustRepN=="on"){
                            $loginCustRepN=2;
                            }elseif($editloginCustRepN=="on" and $deleteloginCustRepN!="on"){
                            $loginCustRepN=1;
                            }elseif($editloginCustRepN!="on" and $seeloginCustRepN=="on"){
                                $loginCustRepN=0;
                            }else{
                                $loginCustRepN=-1;
                            }
                        }else{
                            $loginCustRepN=-1;
                        }

                        // مشتریان غیر فعال چک گردد
                         $inActiveCustRepN = $request->post("inActiveCustRepN");

                        $deleteinActiveCustRepN = $request->post("deleteinActiveCustRepN");
                        $editinActiveCustRepN = $request->post("editinActiveCustRepN");
                        $seeinActiveCustRepN = $request->post("seeinActiveCustRepN");

                        if($inActiveCustRepN=="on"){
                            if($deleteinActiveCustRepN=="on"){
                                $inActiveCustRepN=2;
                            }elseif($editinActiveCustRepN=="on" and $deleteinActiveCustRepN!="on"){
                                $inActiveCustRepN=1;
                            }elseif($editinActiveCustRepN!="on" and $seeinActiveCustRepN=="on"){
                                $inActiveCustRepN=0;
                            }else{
                                    $inActiveCustRepN=-1;
                            }
                        }else{
                            $inActiveCustRepN=-1;
                        }


                        // فاقد کاربر چک گردد
                        $noAdminCustRepN = $request->post("noAdminCustRepN");

                        $deletenoAdminCustRepN = $request->post("deletenoAdminCustRepN");
                        $editnoAdminCustRepN = $request->post("editnoAdminCustRepN");
                        $seenoAdminCustRepN = $request->post("seenoAdminCustRepN");

                        if($noAdminCustRepN=="on"){
                            if($deletenoAdminCustRepN=="on"){
                                $noAdminCustRepN=2;
                            }elseif($editnoAdminCustRepN=="on" and $deletenoAdminCustRepN!="on"){
                                $noAdminCustRepN=1;
                            }elseif($editnoAdminCustRepN!="on" and $seenoAdminCustRepN=="on"){
                                $noAdminCustRepN=0;
                            }else{
                                $noAdminCustRepN=-1;
                            }
                        }else{
                            $noAdminCustRepN=-1;
                        }

                        // مشتریان ارجاعی چک گردد
                        $returnedCustRepN = $request->post("returnedCustRepN");
                        $deletereturnedCustRepN = $request->post("deletereturnedCustRepN");
                        $editreturnedCustRepN = $request->post("editreturnedCustRepN");
                        $seereturnedCustRepN = $request->post("seereturnedCustRepN");

                        if($returnedCustRepN=="on"){
                            if($deletereturnedCustRepN=="on"){
                                $returnedCustRepN=2;
                            }elseif($editreturnedCustRepN=="on" and $deletereturnedCustRepN!="on"){
                                $returnedCustRepN=1;
                            }elseif($editreturnedCustRepN!="on" and $seereturnedCustRepN=="on"){
                                $returnedCustRepN=0;
                            }else{
                                    $returnedCustRepN=-1;
                            }
                        }else{
                            $returnedCustRepN=-1;
                        }

                 }else {
                    $amalkardCustReportN=-1;
                    $loginCustRepN = -1;
                    $inActiveCustRepN = -1;
                    $noAdminCustRepN = -1;
                    $returnedCustRepN = -1;
                 }


                // اگر عملکرد کالا آن بود 
                 $goodsReportN = $request->post("goodsReportN");
                 if($goodsReportN=="on"){
                    $goodsReportN = 1;
                    //گزارش فروش کالا چک گردد
                    $salegoodsReportN = $request->post("salegoodsReportN");
                    $deletesalegoodsReportN = $request->post("deletesalegoodsReportN");
                    $editsalegoodsReportN = $request->post("editsalegoodsReportN");
                    $seesalegoodsReportN = $request->post("seesalegoodsReportN");

                    if($salegoodsReportN=="on"){
                        if($deletesalegoodsReportN=="on"){
                            $salegoodsReportN=2;
                        }elseif($editsalegoodsReportN=="on" and $deletesalegoodsReportN!="on"){
                            $salegoodsReportN=1;
                        }elseif($editsalegoodsReportN!="on" and $seesalegoodsReportN=="on"){
                            $salegoodsReportN=0;
                        }else{
                            $salegoodsReportN=-1;
                        }
                    }else{
                        $salegoodsReportN=-1;
                    }

                    // کالاهای برگشتی چک گردد
                    $returnedgoodsReportN = $request->post("returnedgoodsReportN");
                        $deletereturnedgoodsReportN = $request->post("deletereturnedgoodsReportN");
                        $editreturnedgoodsReportN = $request->post("editreturnedgoodsReportN");
                        $seereturnedgoodsReportN = $request->post("seereturnedgoodsReportN");

                        if($returnedgoodsReportN=="on"){
                            if($deletereturnedgoodsReportN=="on"){
                                $returnedgoodsReportN=2;
                            }elseif($editreturnedgoodsReportN=="on" and $deletereturnedgoodsReportN!="on"){
                                $returnedgoodsReportN=1;
                            }elseif($editreturnedgoodsReportN!="on" and $seereturnedgoodsReportN=="on"){
                                $returnedgoodsReportN=0;
                            }else{
                                $returnedgoodsReportN=-1;
                            }
                        }else{
                            $returnedgoodsReportN=-1;
                        }

                    // کالاهای فاقد موجودی چک گردد
                    $NoExistgoodsReportN = $request->post("NoExistgoodsReportN");

                        $deleteNoExistgoodsReportN = $request->post("deleteNoExistgoodsReportN");
                        $editNoExistgoodsReportN = $request->post("editNoExistgoodsReportN");
                        $seeNoExistgoodsReportN = $request->post("seeNoExistgoodsReportN");

                        if($NoExistgoodsReportN=="on"){
                            if($deleteNoExistgoodsReportN=="on"){
                                $NoExistgoodsReportN=2;
                            }elseif($editNoExistgoodsReportN=="on" and $deleteNoExistgoodsReportN!="on"){
                                $NoExistgoodsReportN=1;
                            }elseif($editNoExistgoodsReportN!="on" and $seeNoExistgoodsReportN=="on"){
                                $NoExistgoodsReportN=0;
                            }else{
                                $NoExistgoodsReportN=-1;
                            }
                        }else{
                            $NoExistgoodsReportN=-1;
                        }

                        // کالاهای راکت چک گردد

                        $nosalegoodsReportN = $request->post("nosalegoodsReportN");

                        $deletenosalegoodsReportN = $request->post("deletenosalegoodsReportN");
                        $editnosalegoodsReportN = $request->post("editnosalegoodsReportN");
                        $seenosalegoodsReportN = $request->post("seenosalegoodsReportN");

                        if($nosalegoodsReportN=="on"){
                            if($deletenosalegoodsReportN=="on"){
                                $nosalegoodsReportN=2;
                            }elseif($editnosalegoodsReportN=="on" and $deletenosalegoodsReportN!="on"){
                                $nosalegoodsReportN=1;
                            }elseif($editnosalegoodsReportN!="on" and $seenosalegoodsReportN=="on"){
                                $nosalegoodsReportN=0;
                            }else{
                                $nosalegoodsReportN=-1;
                            }
                        }else{
                            $nosalegoodsReportN=-1;
                        }
                 }else {
                    $nosalegoodsReportN=-1;
                    $NoExistgoodsReportN=-1;
                    $returnedgoodsReportN=-1;
                    $salegoodsReportN=-1;
                    $goodsReportN=-1;
                 }
          
            //    اگر گزارش برگشتی کالا آن بود 
            $returnedReportgoodsReportN = $request->post("returnedReportgoodsReportN");
                if($returnedReportgoodsReportN=="on"){
                    $returnedReportgoodsReportN = 1;
                    // تسویه نشده ها چک گردد
                    $returnedNTasReportgoodsReportN = $request->post("returnedNTasReportgoodsReportN");
                    $deletereturnedNTasReportgoodsReportN = $request->post("deletereturnedNTasReportgoodsReportN");
                    $editreturnedNTasReportgoodsReportN = $request->post("editreturnedNTasReportgoodsReportN");
                    $seereturnedNTasReportgoodsReportN = $request->post("seereturnedNTasReportgoodsReportN");

                    if($returnedNTasReportgoodsReportN=="on"){
                        if($deletereturnedNTasReportgoodsReportN=="on"){
                            $returnedNTasReportgoodsReportN=2;
                        }elseif($editreturnedNTasReportgoodsReportN=="on" and $deletereturnedNTasReportgoodsReportN!="on"){
                            $returnedNTasReportgoodsReportN=1;
                        }elseif($editreturnedNTasReportgoodsReportN!="on" and $seereturnedNTasReportgoodsReportN=="on"){
                            $returnedNTasReportgoodsReportN=0;
                        }else{
                            $returnedNTasReportgoodsReportN=-1;
                        }
                    }else{
                        $returnedNTasReportgoodsReportN=-1;
                    }

                        // تسویه شده ها چک گردد
                    $tasgoodsReprtN = $request->post("tasgoodsReprtN");

                    $deletetasgoodsReprtN = $request->post("deletetasgoodsReprtN");
                    $edittasgoodsReprtN = $request->post("edittasgoodsReprtN");
                    $seetasgoodsReprtN = $request->post("seetasgoodsReprtN");

                    if($tasgoodsReprtN=="on"){
                        if($deletetasgoodsReprtN=="on"){
                            $tasgoodsReprtN=2;
                        }elseif($editreturnedNTasReportgoodsReportN=="on" and $deletetasgoodsReprtN!="on"){
                            $tasgoodsReprtN=1;
                        }elseif($editreturnedNTasReportgoodsReportN!="on" and $seereturnedNTasReportgoodsReportN=="on"){
                            $tasgoodsReprtN=0;
                        }else{
                            $tasgoodsReprtN=-1;
                        }
                    }else{
                        $tasgoodsReprtN=-1;
                    }
                }else{
                    $returnedNTasReportgoodsReportN=-1;
                    $tasgoodsReprtN=-1;
                    $returnedReportgoodsReportN=-1;
                }
  
                // گزارش بارگیری چک گردد

                $goodsbargiriReportN = $request->post("goodsbargiriReportN");
        
                $deletegoodsbargiriReportN = $request->post("deletegoodsbargiriReportN");
                $editgoodsbargiriReportN = $request->post("editgoodsbargiriReportN");
                $seegoodsbargiriReportN = $request->post("seegoodsbargiriReportN");

                if($goodsbargiriReportN=="on"){
                    if($deletegoodsbargiriReportN=="on"){
                        $goodsbargiriReportN=2;
                    }elseif($editgoodsbargiriReportN=="on" and $deletegoodsbargiriReportN!="on"){
                        $goodsbargiriReportN=1;
                    }elseif($editgoodsbargiriReportN!="on" and $seegoodsbargiriReportN=="on"){
                        $goodsbargiriReportN=0;
                    }else{
                        $goodsbargiriReportN=-1;
                    }
                }else{
                    $goodsbargiriReportN=-1;
                }

        }else {
            $reportN=-1;
            $amalKardreportN=-1;
            $managerreportN=-1;
            $HeadreportN=-1;
            $poshtibanreportN=-1;
            $bazaryabreportN=-1;
            $reportDriverN=-1;
            $amalkardCustReportN=-1;
            $loginCustRepN=-1;
            $inActiveCustRepN=-1;
            $noAdminCustRepN=-1;
            $returnedCustRepN=-1;
            $trazEmployeeReportN=-1;
            $nosalegoodsReportN=-1;
            $NoExistgoodsReportN=-1;
            $returnedgoodsReportN=-1;
            $salegoodsReportN=-1;
            $goodsReportN=-1;
            $returnedNTasReportgoodsReportN=-1;
            $tasgoodsReprtN=-1;
            $returnedReportgoodsReportN=-1;
            $goodsbargiriReportN=-1;
        }
        $bossId=0;
        $saleLineSn=0;

        if($manager){
            $bossId=$manager;  
        }

        if($head){
            $bossId=$head;  
        }
        
        if($saleLine){
            $saleLineSn=$saleLine;
        }
        
        if($picture){
            $fileName=$picture->getClientOriginalName();
            $maxId=0;
            $maxId=DB::table("CRM.dbo.crm_admin")->max('id');
            if($maxId>1){
                $maxId=$maxId+1;
            }else{
                $maxId=1;
            }
            $fileName=$maxId.".jpg";
            $picture->move("resources/assets/images/admins/",$fileName);
        }
		
		$usernameExistance=DB::select("select * from CRM.dbo.crm_admin where username='$userName'");
		
		if(count($usernameExistance)<1){
		
        DB::table("CRM.dbo.crm_admin")->insert(['username'=>"".$userName."",'name'=>"".$name."",'lastName'=>"".$lastName."",
        'adminType'=>$adminType,'password'=>"".$password."",'activeState'=>1,'phone'=>$phone,'address'=>$address,
        'sex'=>"".$sex."",'discription'=>"".$discription."",'driverId'=>0,
        'bossId'=>$bossId,'employeeType'=>$employeeType,'SaleLineId'=>$saleLineSn,'poshtibanType'=>$poshtibanType]);

        $lastId=DB::table("CRM.dbo.crm_admin")->max('id');

          DB::table("CRM.dbo.crm_hasAccess")->insert(
            [  "adminId"=>$lastId
           , "declareElementOppN"=>$declareElementOppN
            
            ,"baseInfoN"=>$baseInfoN
                    
            ,"baseInfoProfileN"=>$baseInfoProfileN
             
            ,"infoRdN"=>$infoRdN
                      
            ,"specialSettingN"=>$specialSettingN
              
            ,"baseInfoSettingN"=>$baseInfoSettingN
             
            ,"InfoSettingAccessN"=>$InfoSettingAccessN
           
            ,"InfoSettingTargetN"=>$InfoSettingTargetN
           
            ,"rdSentN"=>$rdSentN
                      
            ,"rdNotSentN"=>$rdNotSentN
                   
            ,"oppManagerN"=>$oppManagerN
                  
            ,"oppHeadN"=>$oppHeadN
                     
            ,"oppBazaryabN"=>$oppBazaryabN
                 
            ,"oppTakhsisN"=>$oppTakhsisN
                  
            ,"oppDriverServiceN"=>$oppDriverServiceN
            
            ,"oppBargiriN"=>$oppBargiriN
                  
            ,"oppDriverN"=>$oppDriverN
                   
            ,"todayoppNazarsanjiN"=>$todayoppNazarsanjiN
          
            ,"pastoppNazarsanjiN"=>$pastoppNazarsanjiN
           
            ,"DoneoppNazarsanjiN"=>$DoneoppNazarsanjiN
           
            ,"oppNazarSanjiN"=>$oppNazarSanjiN
               
            ,"AddOppupDownBonusN"=>$AddOppupDownBonusN
           
            ,"SubOppupDownBonusN"=>$SubOppupDownBonusN
           
            ,"OppupDownBonusN"=>$OppupDownBonusN
              
            ,"AddedoppRDN"=>$AddedoppRDN
                  
            ,"NotAddedoppRDN"=>$NotAddedoppRDN
               
            ,"oppRDN"=>$oppRDN
                       
            ,"oppjustCalendarN"=>$oppjustCalendarN
             
            ,"oppCustCalendarN"=>$oppCustCalendarN
             
            ,"oppCalendarN"=>$oppCalendarN
                 
            ,"allalarmoppN"=>$allalarmoppN
                 
            ,"donealarmoppN"=>$donealarmoppN
                
            ,"NoalarmoppN"=>$NoalarmoppN
                  
            ,"alarmoppN"=>$alarmoppN
                    
            ,"massageOppN"=>$massageOppN
                  
            ,"justBargiriOppN"=>$justBargiriOppN
              
            ,"oppN"=>$oppN
                         
            ,"reportN"=>$reportN
                      
            ,"amalKardreportN"=>$amalKardreportN
              
            ,"managerreportN"=>$managerreportN
               
            ,"HeadreportN"=>$HeadreportN
                  
            ,"poshtibanreportN"=>$poshtibanreportN
             
            ,"bazaryabreportN"=>$bazaryabreportN
              
            ,"reportDriverN"=>$reportDriverN
                
            ,"amalkardCustReportN"=>$amalkardCustReportN
          
            ,"loginCustRepN"=>$loginCustRepN
                
            ,"inActiveCustRepN"=>$inActiveCustRepN
             
            ,"noAdminCustRepN"=>$noAdminCustRepN
              
            ,"returnedCustRepN"=>$returnedCustRepN
             
            ,"trazEmployeeReportN"=>$trazEmployeeReportN
          
            ,"nosalegoodsReportN"=>$nosalegoodsReportN
           
            ,"NoExistgoodsReportN"=>$NoExistgoodsReportN
          
            ,"returnedgoodsReportN"=>$returnedgoodsReportN
         
            ,"salegoodsReportN"=>$salegoodsReportN
             
            ,"goodsReportN"=>$goodsReportN
                 
            ,"returnedNTasReportgoodsReportN"=>$returnedNTasReportgoodsReportN
            ,"tasgoodsReprtN"=>$tasgoodsReprtN
               
            ,"returnedReportgoodsReportN"=>$returnedReportgoodsReportN
   
            ,"goodsbargiriReportNrtN"=>$goodsbargiriReportN
       
            ,"goodsbargiriReportN"=>$goodsbargiriReportN
          ]);

        return Response::json("done");
		}else{
			return Response::json('exist');
		}
    }

    public function AddCustomerToAdmin(Request $request)
    {
        $adminId=$request->get("adminId");
        $customerIDs=$request->get("customerIDs");
        foreach ($customerIDs as $customerId) {
            DB::table("CRM.dbo.crm_customer_added")->insert(['admin_id'=>$adminId,'customer_id'=>$customerId,'returnState'=>0]);
        }
        $customers=DB::select("SELECT * FROM Shop.dbo.Peopels WHERE  Peopels.PSN IN  (SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE  admin_id=".$adminId." and returnState!=1)  AND CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).")");
        DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(["emptyState"=>0]);
       return Response::json($customers);
    }
    public function RemoveCustomerFromAdmin(Request $request)
    {
        $customerIds=$request->get("customerIDs");
        $adminId=$request->get("adminId");
        $countCustomers=DB::table("CRM.dbo.crm_customer_added")->where("admin_id",$adminId)->where("returnState",0)->count();
        $isEqualEmty=0;
        if($countCustomers == count($customerIds)){
            $isEqualEmty=1;  
        }
        if($isEqualEmty==0){
            foreach ($customerIds as $customerId) {
            DB::table("CRM.dbo.crm_customer_added")->where("customer_id",$customerId)->update(['returnState'=>1,'gotEmpty'=>1,'removedTime'=>"".Carbon::now().""]);
            }
        }
        if($isEqualEmty==1){
            return Response::json($isEqualEmty);
        }
        // $customers=DB::select("SELECT * FROM Shop.dbo.Peopels WHERE  Peopels.PSN 
        // not IN  (SELECT DISTINCT customer_id FROM CRM.dbo.crm_customer_added WHERE  customer_id not in(SELECT customerId FROM CRM.dbo.crm_inactiveCustomer WHERE  state=0) and customer_id not in(SELECT customerId
        // FROM CRM.dbo.crm_returnCustomer WHERE  returnState=1) and returnState=0)  AND CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).") And Name!=''");
        $customers=DB::select("SELECT * FROM Shop.dbo.Peopels where Peopels.PSN in (SELECT customer_id FROM CRM.dbo.crm_customer_added where admin_id=".$adminId." and crm_customer_added.returnState=0)  AND CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).")");
        return Response::json($customers);
    }

    public function RemoveCustomerAndEmpty(Request $request)
    {
        $customerIds=$request->get("customerIDs");
        $adminId=$request->get("adminId");
        $countCustomers=DB::table("CRM.dbo.crm_customer_added")->where("admin_id",$adminId)->where("returnState",0)->count();
        
        foreach ($customerIds as $customerId) {
            DB::table("CRM.dbo.crm_customer_added")->where("customer_id",$customerId)->update(['returnState'=>1,'gotEmpty'=>1,'removedTime'=>"".Carbon::now().""]);
        }

        DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(['emptyState'=>1]);

        // $customers=DB::select("SELECT * FROM Shop.dbo.Peopels WHERE  Peopels.PSN 
        // not IN  (SELECT DISTINCT customer_id FROM CRM.dbo.crm_customer_added WHERE  customer_id not in(SELECT customerId FROM CRM.dbo.crm_inactiveCustomer WHERE  state=0) and customer_id not in(SELECT customerId
        // FROM CRM.dbo.crm_returnCustomer WHERE  returnState=1) and returnState=0)  AND CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).") And Name!=''");
        $customers=DB::select("SELECT * FROM Shop.dbo.Peopels where Peopels.PSN in (SELECT customer_id FROM CRM.dbo.crm_customer_added where admin_id=".$adminId." and crm_customer_added.returnState=0)  AND CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).")");
        return Response::json($customers);
    }


    public function myCalendar(){
        $adminId=Session::get('asn');
        $now = Jalalian::fromCarbon(Carbon::now());
        $month= $now->getMonth();
        $year= $now->getYear();
        $salesExpert=new SalseExper;
        $adminType=$salesExpert->getAdminType(Session::get('asn'));
        $allEmployies=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE adminType!=4 and deleted=0");
        if($adminType!=5){
            $employies=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE (bossId=".Session::get('asn')." or id=".Session::get('asn').")  AND deleted=0");
        }else{
            $employies=$allEmployies;
        }
        $workList=DB::select("SELECT count(a.workId) as count,a.specifiedDate FROM (SELECT DISTINCT crm_workList.id as workId, crm_workList.specifiedDate FROM CRM.dbo.crm_workList Join CRM.dbo.crm_comment ON crm_workList.commentId=crm_comment.id
        JOIN   CRM.dbo.crm_customer_added ON crm_comment.customerId=crm_customer_added.customer_id WHERE  crm_customer_added.admin_id=".$adminId." and crm_workList.doneState=0 and crm_customer_added.returnState=0)a GROUP BY    a.specifiedDate");

        //جدول مشتریان
        $todayDate=Carbon::now()->format('Y-m-d');
        $customers=DB::select("SELECT *,CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr,CRM.dbo.getPeopelSabtDate(PSN) as sabtDate FROM(
                        SELECT * FROM(SELECT * FROM(
                        SELECT * FROM(
                        SELECT PSN,Name,IsActive,SnMantagheh,admin_id,returnState,PCode,peopeladdress,GroupCode FROM Shop.dbo.Peopels JOIN (SELECT * FROM CRM.dbo.crm_customer_added)a ON Peopels.PSN=a.customer_id)b
                        where  b.admin_id=".$adminId." AND b.returnState=0)e
                        JOIN(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f on e.SnMantagheh=f.SnMNM)g
                        left JOIN (SELECT  maxTime,customerId FROM(
                        SELECT customerId,Max(TimeStamp) as maxTime FROM(
                        SELECT crm_comment.TimeStamp,customerId FROM CRM.dbo.crm_comment
                        JOIN CRM.dbo.crm_workList 
                        on crm_comment.id=crm_workList.commentId where doneState=0 and crm_workList.specifiedDate>'".$todayDate."'
                        )a group by customerId)b)h on g.PSN=h.customerId)i  where IsActive=1 and PSN not in(select customerId from CRM.dbo.crm_inactiveCustomer where state=1 and customerId is not null) order by i.maxTime asc");
        
        $cities=DB::table("Shop.dbo.MNM")->where("RecType",1)->where("FatherMNM",79)->get();


        //مشتریان جدید درختی 
        $eachdays=DB::select("SELECT count(addedDate) as countPeopels,addedDate from(
            SELECT Convert(date,addedTime) as addedDate,customer_id from CRM.dbo.crm_customer_added  
            WHERE  crm_customer_added.admin_id=$adminId  and returnState=0  and exists(select customer_id from CRM.dbo.crm_inserted_customers where customerId=customer_id))a group by addedDate  order by addedDate desc");

        $admins=DB::select("select * from CRM.dbo.crm_admin where adminType=2 or adminType=3 and deleted=0");
        $mantagheh=DB::table("Shop.dbo.MNM")->where("FatherMNM",80)->get();
        $phoeCodes=DB::table("NewStarfood.dbo.star_provincePhoneCode")->get();
        $todayDate=Carbon::now()->format('Y-m-d');
        $duplicateCustomer=DB::select("SELECT CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr,peopeladdress,Peopels.Name as Name,star_duplicatCustomer.Name as dupName,PSN,address,PCode from NewStarfood.dbo.star_duplicatCustomer inner join SHop.dbo.Peopels on customerId=PSN where adminId=".Session::get("asn")."");
        return view ("admin.calendar",['commenDates'=>$workList,'duplicateCustomer'=>$duplicateCustomer,'month'=>$month,'year'=>$year,'employies'=>$employies,'adminId'=>$adminId,'customers'=>$customers,'cities'=>$cities,'eachdays'=>$eachdays,'admins'=>$admins,'mantagheh'=>$mantagheh,'phoeCodes'=>$phoeCodes,'todayDate'=>$todayDate]);
    }

    public function changeDate(Request $request)
    {
        $month=$request->post("month");
        $year=$request->post("year");
        $adminId=$request->post("asn");
        $salesExpert=new SalseExper;
        $adminType=$salesExpert->getAdminType(Session::get('asn'));
        $allEmployies=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE adminType!=5 and adminType!=4 and deleted=0");
        if($adminType!=5){
            $employies=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE (bossId=".Session::get('asn')." or id=".Session::get('asn').") AND deleted=0");
        }else{
            $employies=$allEmployies;
        }
        $workList=DB::select("SELECT count(a.workId) as count,a.specifiedDate FROM (SELECT crm_workList.id as workId, crm_workList.specifiedDate FROM CRM.dbo.crm_workList Join CRM.dbo.crm_comment ON crm_workList.commentId=crm_comment.id
                              JOIN CRM.dbo.crm_customer_added ON crm_comment.customerId=crm_customer_added.customer_id 
                              WHERE  crm_customer_added.admin_id=".$adminId." AND crm_workList.doneState=0 and crm_customer_added.returnState=0 and crm_comment.customerId not IN  (SELECT customerId FROM CRM.dbo.crm_returnCustomer WHERE  crm_returnCustomer.returnState=1))a GROUP BY    a.specifiedDate");
        //جدول مشتریان
        $todayDate=Carbon::now()->format('Y-m-d');
        $customers=DB::select("SELECT *,CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr,CRM.dbo.getPeopelSabtDate(PSN) as sabtDate FROM(
                        SELECT * FROM(SELECT * FROM(
                        SELECT * FROM(
                        SELECT PSN,Name,SnMantagheh,admin_id,returnState,PCode,peopeladdress,GroupCode FROM Shop.dbo.Peopels JOIN (SELECT * FROM CRM.dbo.crm_customer_added)a ON Peopels.PSN=a.customer_id)b
                        where  b.admin_id=".$adminId." AND b.returnState=0)e
                        JOIN(SELECT SnMNM,NameRec FROM Shop.dbo.MNM )f on e.SnMantagheh=f.SnMNM)g
                        left JOIN (SELECT  maxTime,customerId FROM(
                        SELECT customerId,Max(TimeStamp) as maxTime FROM(
                        SELECT crm_comment.TimeStamp,customerId FROM CRM.dbo.crm_comment
                        JOIN CRM.dbo.crm_workList 
                        on crm_comment.id=crm_workList.commentId where doneState=0 and crm_workList.specifiedDate>'".$todayDate."'
                        )a group by customerId)b)h on g.PSN=h.customerId)i  where PSN not in(select customerId from CRM.dbo.crm_inactiveCustomer where state=1 and customerId is not null) order by i.maxTime asc");
        
        $cities=DB::table("Shop.dbo.MNM")->where("RecType",1)->where("FatherMNM",79)->get();
        //مشتریان جدید درختی 
        $eachdays=DB::select("SELECT count(addedDate) as countPeopels,addedDate from(
            SELECT Convert(date,addedTime) as addedDate,customer_id from CRM.dbo.crm_customer_added  
            WHERE  crm_customer_added.admin_id=$adminId  and returnState=0  and exists(select customer_id from CRM.dbo.crm_inserted_customers where customerId=customer_id))a group by addedDate  order by addedDate desc");

        $admins=DB::select("select * from CRM.dbo.crm_admin where adminType=2 or adminType=3 and deleted=0");
        $mantagheh=DB::table("Shop.dbo.MNM")->where("FatherMNM",80)->get();
        $phoeCodes=DB::table("NewStarfood.dbo.star_provincePhoneCode")->get();
        $todayDate=Carbon::now()->format('Y-m-d');
        $duplicateCustomer=DB::select("SELECT CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr,peopeladdress,Peopels.Name as Name,star_duplicatCustomer.Name as dupName,PSN,address,PCode from NewStarfood.dbo.star_duplicatCustomer inner join SHop.dbo.Peopels on customerId=PSN where adminId=$adminId");

        return view ("admin.calendar",['commenDates'=>$workList,'duplicateCustomer'=>$duplicateCustomer,'month'=>$month,'year'=>$year,'employies'=>$employies,'adminId'=>$adminId,'customers'=>$customers,'cities'=>$cities,'eachdays'=>$eachdays,'admins'=>$admins,'mantagheh'=>$mantagheh,'phoeCodes'=>$phoeCodes,'todayDate'=>$todayDate]);
    }
    public function takhsisCustomer(Request $request)
    {
        $customerId=$request->get("csn");
        $adminId=$request->get("asn");
        $firstAdminId=$request->get("asn");
        // add to customer update two places
        $admin=DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->first();
        DB::table("CRM.dbo.crm_customer_added")->where('customer_id',$customerId)->where('returnState',0)->update(['removedTime'=>"".Carbon::now()."",'returnState'=>1]);
        DB::table("CRM.dbo.crm_customer_added")->insert(['admin_id'=>$adminId,'customer_id'=>$customerId,'returnState'=>0]);
        DB::update("UPDATE CRM.dbo.crm_returnCustomer SET returnState=0 WHERE  customerId=".$customerId." and returnState=1");
        DB::update("UPDATE CRM.dbo.crm_inactiveCustomer SET state=0 WHERE  customerId=".$customerId." and state=1");
        DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->where('emptyState',1)->update(['emptyState'=>0]);
        $customers=DB::table("Shop.dbo.Peopels")->join("CRM.dbo.crm_returnCustomer","Peopels.PSN","=","crm_returnCustomer.customerId")->join("Shop.dbo.PhoneDetail","Peopels.PSN","=","PhoneDetail.SnPeopel")->where("crm_returnCustomer.returnState",1)->select("Peopels.PSN","Peopels.PCode","Peopels.Name","PhoneDetail.PhoneStr","Peopels.peopeladdress")->get();
        return Response::json($customers);

    }
    public function takhsisNewCustomer(Request $request)
    {
        $customerId=$request->get("csn");
        $adminId=$request->get("asn");
        // add to customer update two places
        $admin=DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->first();
        if($admin->emptyState==1){
            DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(["emptyState"=>0]);
        }
        DB::table("CRM.dbo.crm_customer_added")->where('customer_id',$customerId)->where('returnState',0)->update(['removedTime'=>"".Carbon::now()."",'returnState'=>1]);
        DB::table("CRM.dbo.crm_customer_added")->insert(['admin_id'=>$adminId,'customer_id'=>$customerId,'returnState'=>0]);
        DB::table("CRM.dbo.crm_customer_added")->where('customer_id',$customerId)->update(['gotEmpty'=>0]);
        DB::update("UPDATE CRM.dbo.crm_returnCustomer SET returnState=0 WHERE  customerId=".$customerId." and returnState=1");
        DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->where('emptyState',1)->update(['emptyState'=>0]);

        return Response::json(1);

    }


    public function activateCustomer(Request $request)
    {
        $customerId=$request->get("csn");
        $adminId=$request->get("asn");
        // add to customer update two places
        // $result1=DB::table("CRM.dbo.crm_customer_added")->where('customer_id',$customerId)->where("returnState",1)->update(['admin_id'=>$adminId,'customer_id'=>$customerId,'returnState'=>0]);
        DB::table("CRM.dbo.crm_customer_added")->insert(['admin_id'=>$adminId,'customer_id'=>$customerId,'returnState'=>0]);
        DB::update("UPDATE CRM.dbo.crm_inactiveCustomer SET state=0 WHERE  customerId=".$customerId." and state=1");

        $customers=DB::select("SELECT * FROM (
                        SELECT * FROM (
                        SELECT * FROM CRM.dbo.crm_inactiveCustomer
                        join(SELECT name,lastName,id as admin_id FROM CRM.dbo.crm_admin)a ON a.admin_id=adminId)b
                        JOIN   (SELECT Name as CustomerName,PSN,PCode FROM Shop.dbo.Peopels)c ON c.PSN=b.customerId)d
                        JOIN   (SELECT SnPeopel,PhoneStr FROM Shop.dbo.PhoneDetail)e ON e.SnPeopel=d.PSN
                        WHERE  state=1");
        return Response::json($customers);
    }

    public function report(){
        $adminType= Session::get("adminType");
        $managerId=Session::get('asn');
        $queryPart="";
        if($adminType!=5){
            $queryPart=" and crm_admin.id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($managerId))";
        }
		$amdins=DB::select("Select * FROM CRM.dbo.crm_admin WHERE  adminType=2 and deleted=0");

        $cities=DB::table("Shop.dbo.MNM")->where("Rectype",1)->where("FatherMNM",79)->get();

            $admins=DB::select("SELECT crm_admin.id,crm_admin.name,crm_admin.lastName,crm_admin.adminType AS adminTypeId,crm_adminType.adminType FROM CRM.dbo.crm_admin INNER JOIN CRM.dbo.crm_adminType ON crm_adminType.id=crm_admin.adminType WHERE deleted=0 AND crm_admin.adminType!=4 $queryPart");

            $evacuatedAdmins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where('crm_admin.adminType','!=',1)->where('crm_admin.adminType','!=',4)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType")->get();
            // referencial customer query 
            $referencialAdmins=DB::table("CRM.dbo.crm_admin")
            ->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')
                ->where('crm_admin.adminType',2)->where('deleted',0)
                ->select("crm_admin.id","crm_admin.name","crm_admin.lastName",
                "crm_admin.adminType as adminTypeId","crm_adminType.adminType")->get();
            $returnerAdmins=DB::select("SELECT * FROM CRM.dbo.crm_admin 
                                JOIN(SELECT DISTINCT CRM.dbo.crm_returnCustomer.adminId
                            FROM CRM.dbo.crm_returnCustomer WHERE returnState=1)b ON CRM.dbo.crm_admin.id=b.adminId");
            $inActiverAdmins=DB::select("Select * FROM CRM.dbo.crm_admin WHERE  adminType !=4 and deleted=0");

            $loginCustomers=DB::select("SELECT lastVisit,PSN,countLogin,CRM.dbo.getAdminName(PSN) as adminName,Name,platform,browser,firstVisit,visitDate,countSameTime FROM(
                SELECT * FROM(
                SELECT * FROM(
                SELECT * FROM(
                SELECT * FROM(
                SELECT MAX(visitDate) as lastVisit,customerId FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)a
                JOIN   (SELECT Name,PSN,GroupCode FROM Shop.dbo.Peopels)b
                ON a.customerId=b.PSN)c
                JOIN   (SELECT COUNT(id) as countLogin,customerId as csn FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)d ON c.customerId=d.csn)e
                JOIN   (SELECT visitDate,browser,platform,customerId as cid FROM NewStarfood.dbo.star_customerTrack)f ON e.lastVisit=f.visitDate)g
                JOIN   (SELECT MIN(visitDate) as firstVisit,customerId as CUSTOMERID2 FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)h ON g.PSN=h.CUSTOMERID2)i
                left join (select count(customerId) as countSameTime,customerId from NewStarfood.dbo.star_customerSession1 group by customerId)j on j.customerId=i.PSN
                where CONVERT(date,lastVisit)=convert(date,CURRENT_TIMESTAMP)
                order by lastVisit desc");

        return view ("reports.listReport",['cities'=>$cities, 'admins'=>$admins,'evacuatedAdmins'=>$evacuatedAdmins, 'referencialAdmins'=>$referencialAdmins,'returners'=>$returnerAdmins,'inActiverAdmins'=>$inActiverAdmins,'loginCustomers'=>$loginCustomers]);
    }

    public function login(){
       return view ("admin.login");
    }


    public function alarm(){

        $adminType= Session::get("adminType");
        $adminId=Session::get('asn');
        $queryPart="LEFT JOIN";
        if($adminType!=5){
            $queryPart="INNER JOIN";
        }
        $maxFacors=DB::select("SELECT max(SerialNoHDS) AS MaxFactorId,CustomerSn FROM Shop.dbo.FactorHDS WHERE CompanyNo=5 and FactDate>='1401/08/08' and not exists(select * from CRM.dbo.crm_alarm where factorId=SerialNoHDS) GROUP BY CustomerSn");

        $inAlarmFactors=DB::select("SELECT factorId,CustomerSn FROM CRM.dbo.crm_alarm 
        JOIN Shop.dbo.FactorHDS ON crm_alarm.factorId=FactorHDS.SerialNoHDS where state=0");

        foreach ($maxFacors as $factor) {
            foreach ($inAlarmFactors as $alarm) {
                if($factor->CustomerSn==$alarm->CustomerSn and ($factor->MaxFactorId>$alarm->factorId)){
                    DB::table("CRM.dbo.crm_alarm")->where("factorId",$alarm->factorId)->update(["state"=>1]);
                }
            }
        }
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $customers=DB::select("SELECT *,CRM.dbo.getCustomerMantagheh(SnMantagheh) as NameRec,CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr FROM (
            SELECT * FROM (
                SELECT * FROM (
                    SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,IsActive FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0 and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on k.PSN=n.customer_id
    JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm group by factorId)b on b.factorSn=factorId
                                WHERE IsActive=1 and  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0 and not exists(select customerId from CRM.dbo.crm_inactiveCustomer where customerId=PSN and crm_inactiveCustomer.state=1) ORDER BY alarmDate desc" );
        foreach ($customers as $customer) {
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);
			
            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
        
        }
		 $allEmployies=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE adminType!=5 and adminType!=4 and deleted=0");
		 if($adminType!=5){
            $employies=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE (bossId=".Session::get('asn')." or id=".Session::get('asn').")  AND deleted=0");
        }else{
            $employies=$allEmployies;
        }
		
		
        return view ("admin.alarm",['customers'=>$customers, 'employies'=>$employies, 'adminId'=>$adminId]);
    }

    public function getAlarms(Request $request)
    {
        $adminType= Session::get("adminType");
        $adminId=Session::get('asn');
        $queryPart="LEFT JOIN";
        if($adminType!=5){
            $queryPart="INNER JOIN";
        }

        $maxFacors=DB::select("SELECT max(SerialNoHDS) AS MaxFactorId,CustomerSn FROM Shop.dbo.FactorHDS WHERE CompanyNo=5 and FactDate>='1401/08/08' and not exists(select * from CRM.dbo.crm_alarm where factorId=SerialNoHDS) GROUP BY CustomerSn");

        $inAlarmFactors=DB::select("SELECT factorId,CustomerSn FROM CRM.dbo.crm_alarm 
        JOIN Shop.dbo.FactorHDS ON crm_alarm.factorId=FactorHDS.SerialNoHDS where state=0");

        foreach ($maxFacors as $factor) {
            foreach ($inAlarmFactors as $alarm) {
                if($factor->CustomerSn==$alarm->CustomerSn and ($factor->MaxFactorId>$alarm->factorId)){
                    DB::table("CRM.dbo.crm_alarm")->where("factorId",$alarm->factorId)->update(["state"=>1]);
                }
            }
        }
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $customers=DB::select("SELECT * FROM (
                        SELECT * FROM (
                            SELECT * FROM (
                                SELECT * FROM (
                                    SELECT * FROM (
                                        SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                                    JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                                JOIN (SELECT id AS admin_Id,name AS AdminName,lastName FROM CRM.dbo.crm_admin)d ON c.adminId=d.Admin_Id)e
                            JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=e.factorId)g
                        JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
                    JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
                $queryPart JOIN (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0 and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on m.CustomerSn=n.customer_id
                JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)a on n.customer_id=a.SnPeopel
            JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm group by factorId)b on b.factorSn=factorId
                    WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0
                    and PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added where returnState=0) ORDER BY alarmDate desc");
        foreach ($customers as $customer) {
        $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

        $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

        }
        return Response::json($customers);
    }
    public function customerDashboardForAdmin(Request $request)
    {
        $psn=$request->get("csn");
        $customers=DB::select("SELECT * FROM(
            SELECT * FROM(
            SELECT * FROM
            (SELECT Peopels.CompanyNo,Peopels.Name,Peopels.PSN,Peopels.GroupCode,Peopels.peopeladdress,Peopels.PCode,SnMantagheh FROM Shop.dbo.Peopels)a
            LEFT JOIN (SELECT COUNT(Shop.dbo.FactorHDS.SerialNoHDS)AS countFactor,CustomerSn 
            FROM Shop.dbo.FactorHDS WHERE FactType=3 GROUP BY CustomerSn)b ON b.CustomerSn=a.PSN)c
            JOIN (SELECT customer_id,crm_admin.id,name AS adminName,lastName,returnState
            FROM CRM.dbo.crm_customer_added JOIN CRM.dbo.crm_admin ON crm_customer_added.admin_id=crm_admin.id)d ON d.customer_id=c.PSN)e
			right join(select * from NewStarfood.dbo.star_CustomerPass)g on g.customerId=PSN
			join (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr
			FROM Shop.dbo.PhoneDetail
			GROUP BY SnPeopel)a on g.customerId=a.SnPeopel
                            WHERE e.CompanyNo=5 AND e.GroupCode IN ( ".implode(",",Session::get("groups")).") AND e.PSN=".$psn);
        $exactCustomer=0;

        foreach ($customers as $customer) {
            $sabit="";
            $hamrah="";
            $phones=DB::select("SELECT * FROM Shop.dbo.PhoneDetail WHERE  SnPeopel=".$customer->PSN);
            foreach ($phones as $phone) {
                if($phone->PhoneType==1){
                    $sabit.=$phone->PhoneStr."\n";
                }else{
                    $hamrah.=$phone->PhoneStr."\n";    
                }
            }
            $customer->hamrah=$hamrah;
            $customer->sabit=$sabit;
        }
        $exactCustomer=$customers[0];
        $factors=DB::select("SELECT * FROM Shop.dbo.FactorHDS WHERE  FactType=3 and CustomerSn=".$psn." ORDER BY FactDate desc");
        $returnedFactors=DB::select("SELECT * FROM Shop.dbo.FactorHDS WHERE  FactType=4 and CustomerSn=".$psn." ORDER BY FactDate desc");

        $GoodsDetail=DB::select("SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood FROM(
                                SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood FROM Shop.dbo.FactorHDS
                                JOIN Shop.dbo.FactorBYS ON FactorHDS.SerialNoHDS=FactorBYS.SnFact
                                WHERE  FactorHDS.CustomerSn=".$psn.")g GROUP BY    SnGood)c
                                JOIN (SELECT * FROM Shop.dbo.PubGoods)d ON d.GoodSn=c.SnGood order by maxTime desc");

        $basketOrders=DB::select("SELECT orderStar.TimeStamp,PubGoods.GoodName,orderStar.Amount,orderStar.Fi 
                                FROM newStarfood.dbo.FactorStar 
                                JOIN newStarfood.dbo.orderStar ON FactorStar.SnOrder=orderStar.SnHDS
                                JOIN Shop.dbo.PubGoods ON orderStar.SnGood=PubGoods.GoodSn  
                                WHERE  orderStatus=0 and CustomerSn=".$psn);

        $comments=DB::select("SELECT  crm_comment.id,newComment,nexComment,TimeStamp,customerId,adminId,specifiedDate FROM CRM.dbo.crm_comment 
                                JOIN CRM.dbo.crm_workList ON crm_comment.id=crm_workList.commentId WHERE customerId=".$psn."  order by TimeStamp desc");

        $specialComment=DB::table("CRM.dbo.crm_customerProperties")->where("customerId",$psn)->select("comment")->get();
        
        $assesments=DB::select("SELECT crm_assesment.comment,crm_assesment.factorId,crm_assesment.TimeStamp,crm_assesment.shipmentProblem,crm_assesment.driverBehavior FROM CRM.dbo.crm_assesment
                                JOIN   Shop.dbo.FactorHDS ON crm_assesment.factorId=FactorHDS.SerialNoHDS JOIN   Shop.dbo.Peopels ON Peopels.PSN=FactorHDS.CustomerSn WHERE  PSN=".$psn." order by TimeStamp desc");
        $lotteryAndTakhfif=DB::select("select * from(
            select * from (SELECT customerId,Cast(money As varchar(200)) gift,changeDate  FROM NewStarfood.dbo.star_takhfifHistory where isUsed=0)a union (select  customerId,wonPrize,format(timestam,'yyyy/MM/dd','fa-ir') from NewStarfood.dbo.star_TryLottery ))b where customerId=$psn");
        return Response::json([$exactCustomer,$factors,$GoodsDetail,$basketOrders,$comments,$specialComment,$assesments,$returnedFactors,$lotteryAndTakhfif]);
    }

    // ======================
    public function customerDashboardForAlarm(Request $request)
    {
        $psn=$request->get("csn");
        $asn=$request->get("asn");
        $customer=DB::select("SELECT * FROM(
                        SELECT * FROM (
                        SELECT COUNT(Shop.dbo.FactorHDS.SerialNoHDS)as countFactor,CustomerSn FROM Shop.dbo.FactorHDS GROUP BY    CustomerSn
                        )a
                        JOIN   (SELECT customer_id,returnState,admin_id FROM CRM.dbo.crm_customer_added)b ON a.CustomerSn=b.customer_id
                        )c
                        JOIN   (SELECT Name,PSN,PCode,CompanyNo FROM Shop.dbo.Peopels)d ON c.customer_id=d.PSN
                        WHERE   PSN=".$psn);
        $exactCustomer=0;
        foreach ($customer as $cust) {
            $exactCustomer=$cust;
        }
        $factors=DB::select("SELECT * FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$psn." ORDER BY     FactDate desc");
        
        $GoodsDetail=DB::select("SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood FROM(
                            SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood FROM Shop.dbo.FactorHDS
                            JOIN Shop.dbo.FactorBYS ON FactorHDS.SerialNoHDS=FactorBYS.SnFact
                            WHERE FactorHDS.CustomerSn=".$psn.")g GROUP BY    SnGood)c
                            JOIN (SELECT * FROM Shop.dbo.PubGoods)d ON d.GoodSn=c.SnGood");

        $basketOrders=DB::select("SELECT orderStar.TimeStamp,PubGoods.GoodName,orderStar.Amount,orderStar.Fi FROM newStarfood.dbo.FactorStar 
                            JOIN newStarfood.dbo.orderStar ON FactorStar.SnOrder=orderStar.SnHDS
                            JOIN Shop.dbo.PubGoods ON orderStar.SnGood=PubGoods.GoodSn  WHERE  orderStatus=0 and CustomerSn=".$psn);
        
        $comments=DB::select("SELECT  crm_comment.newComment,crm_comment.nexComment,crm_comment.TimeStamp,customerId,adminId,specifiedDate,doneState,crm_comment.id 
                            FROM CRM.dbo.crm_comment JOIN CRM.dbo.crm_workList ON crm_comment.id=crm_workList.id 
                            WHERE customerId=".$psn);
        
        $specialComment=DB::table("CRM.dbo.crm_customerProperties")->where("customerId",$psn)->select("comment")->get();
        
        $assesments=DB::select("SELECT crm_assesment.comment,crm_assesment.factorId,crm_assesment.TimeStamp,crm_assesment.shipmentProblem,crm_assesment.driverBehavior FROM CRM.dbo.crm_assesment
                                JOIN Shop.dbo.FactorHDS ON crm_assesment.factorId=FactorHDS.SerialNoHDS JOIN   Shop.dbo.Peopels ON Peopels.PSN=FactorHDS.CustomerSn WHERE  PSN=".$psn);
        
        return Response::json([$exactCustomer,$factors,$GoodsDetail,$basketOrders,$comments,$specialComment,$assesments]);
    }

    public function getAlarmInfo(Request $request)
    {
       $factorId=$request->get("factorId");
       $alarmInfo=DB::select("SELECT * FROM CRM.dbo.crm_alarm where factorId=$factorId and state=0");
        return Response::json($alarmInfo[0]);
    }
     
    public function searchAlarms(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $snMantagheh=$request->get("snMantagheh");
        $adminType= Session::get("adminType");
        $adminId=Session::get('asn');
        $queryPart="LEFT JOIN";
        if($adminType!=5){
            $queryPart="INNER JOIN";
        }

        $maxFacors=DB::select("SELECT max(SerialNoHDS) AS MaxFactorId,CustomerSn FROM Shop.dbo.FactorHDS WHERE CompanyNo=5 and FactDate>='1401/08/08' and not exists(select * from CRM.dbo.crm_alarm where factorId=SerialNoHDS) GROUP BY CustomerSn");

        $inAlarmFactors=DB::select("SELECT factorId,CustomerSn FROM CRM.dbo.crm_alarm 
        JOIN Shop.dbo.FactorHDS ON crm_alarm.factorId=FactorHDS.SerialNoHDS where state=0");

        foreach ($maxFacors as $factor) {
            foreach ($inAlarmFactors as $alarm) {
                if($factor->CustomerSn==$alarm->CustomerSn and ($factor->MaxFactorId>$alarm->factorId)){
                    DB::table("CRM.dbo.crm_alarm")->where("factorId",$alarm->factorId)->update(["state"=>1]);
                }
            }
        }
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $customers=DB::select("SELECT *,CRM.dbo.getCustomerMantagheh(SnMantagheh) as NameRec,CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr FROM (
            SELECT * FROM (
                SELECT * FROM (
                    SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
    $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0  and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on k.PSN=n.customer_id
    JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm group by factorId)b on b.factorSn=factorId
                                WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0 and not exists(select customerId from CRM.dbo.crm_inactiveCustomer where customerId=PSN)
                     and Name like N'%$searchTerm%' AND SnMantagheh LIKE N'%$snMantagheh%'");
        foreach ($customers as $customer) {
        $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

        $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

        }
        return Response::json($customers);
    }
    public function searchDoneAlarm(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $snMantagheh=$request->get("snMantagheh");
        $adminType= Session::get("adminType");
        $adminId=Session::get('asn');
        $queryPart="LEFT JOIN";
        if($adminType!=5){
            $queryPart="INNER JOIN";
        }
        $maxFacors=DB::select("SELECT max(SerialNoHDS) AS MaxFactorId,CustomerSn FROM Shop.dbo.FactorHDS WHERE CompanyNo=5 and FactDate>='1401/08/08' and not exists(select * from CRM.dbo.crm_alarm where factorId=SerialNoHDS) GROUP BY CustomerSn");

        $inAlarmFactors=DB::select("SELECT factorId,CustomerSn FROM CRM.dbo.crm_alarm 
        JOIN Shop.dbo.FactorHDS ON crm_alarm.factorId=FactorHDS.SerialNoHDS where state=0");

        foreach ($maxFacors as $factor) {
            foreach ($inAlarmFactors as $alarm) {
                if($factor->CustomerSn==$alarm->CustomerSn and ($factor->MaxFactorId>$alarm->factorId)){
                    DB::table("CRM.dbo.crm_alarm")->where("factorId",$alarm->factorId)->update(["state"=>1]);
                }
            }
        }

        $customers=DB::select("SELECT * FROM (
            SELECT * FROM (
                SELECT * FROM (
                    SELECT * FROM (
                        SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                    JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                    JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
                JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
            JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)a on PSN=a.SnPeopel
            $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0  and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on m.CustomerSn=n.customer_id
        JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm   group by factorId HAVING COUNT(id)>1)b on b.factorSn=factorId
            WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and state=0 and Name like N'%$searchTerm%' AND SnMantagheh LIKE N'%$snMantagheh%'" );
        foreach ($customers as $customer) {
        $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

        $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

        }
        return Response::json($customers);
    }
    public function searchAlarmByMantagheh(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $snMantagheh=$request->get("snMantagheh");
        $maxFacors=DB::select("SELECT max(SerialNoHDS) AS MaxFactorId,CustomerSn FROM Shop.dbo.FactorHDS WHERE CompanyNo=5 and FactDate>='1401/08/08' and not exists(select * from CRM.dbo.crm_alarm where factorId=SerialNoHDS) GROUP BY CustomerSn");
        $adminType= Session::get("adminType");
        $adminId=Session::get('asn');
        $queryPart="LEFT JOIN";
        if($adminType!=5){
            $queryPart="INNER JOIN";
        }
        $inAlarmFactors=DB::select("SELECT factorId,CustomerSn FROM CRM.dbo.crm_alarm 
        JOIN Shop.dbo.FactorHDS ON crm_alarm.factorId=FactorHDS.SerialNoHDS where state=0");

        foreach ($maxFacors as $factor) {
            foreach ($inAlarmFactors as $alarm) {
                if($factor->CustomerSn==$alarm->CustomerSn and ($factor->MaxFactorId>$alarm->factorId)){
                    DB::table("CRM.dbo.crm_alarm")->where("factorId",$alarm->factorId)->update(["state"=>1]);
                }
            }
        }
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $customers=DB::select("SELECT *,CRM.dbo.getCustomerMantagheh(SnMantagheh) as NameRec,CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr FROM (
            SELECT * FROM (
                SELECT * FROM (
                    SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0 and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on k.PSN=n.customer_id
    JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm group by factorId)b on b.factorSn=factorId
                                WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and state=0 and not exists(select customerId from CRM.dbo.crm_inactiveCustomer where customerId=PSN)
                     and Name like N'%$searchTerm%' AND SnMantagheh=$snMantagheh" );
        foreach ($customers as $customer) {
        $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

        $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

        }
        return Response::json($customers);
    }

    public function getDoneAlarmsHistory(Request $request)
    {
        $history=$request->get("history");
        $yesterdayOfWeek = Jalalian::fromCarbon(Carbon::yesterday())->getDayOfWeek();
        $yesterday;
        $customers;
        $adminType= Session::get("adminType");
        $adminId=Session::get('asn');
        $queryPart="LEFT JOIN";
        if($adminType!=5){
            $queryPart="INNER JOIN";
        }

        if($yesterdayOfWeek==6){
            $yesterday = Jalalian::fromCarbon(Carbon::yesterday()->subDays(1))->format('Y/m/d');
        }else{
            $yesterday = Jalalian::fromCarbon(Carbon::yesterday())->format('Y/m/d');
        }

        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        if($history=='YESTERDAY'){
            $customers=DB::select("SELECT * FROM (
                SELECT * FROM (
                    SELECT * FROM (
                        SELECT * FROM (
                            SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                        JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                        JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
                    JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
                JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
                JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)a on PSN=a.SnPeopel
            $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0 and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on m.CustomerSn=n.customer_id
            JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm   group by factorId HAVING COUNT(id)>1)b on b.factorSn=factorId
                WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and FORMAT(TimeStamp,'yyyy/MM/dd','fa-ir')='".$yesterday."' and state=0" );
            foreach ($customers as $customer) {
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

            }

        }

        if($history=='TODAY'){
            $customers=DB::select("SELECT * FROM (
                SELECT * FROM (
                    SELECT * FROM (
                        SELECT * FROM (
                            SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                        JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                        JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
                    JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
                JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
                JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)a on PSN=a.SnPeopel
                $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0 and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on m.CustomerSn=n.customer_id
            JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm   group by factorId HAVING COUNT(id)>1)b on b.factorSn=factorId
                WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and FORMAT(TimeStamp,'yyyy/MM/dd','fa-ir')='".$todayDate."' and state=0" );
            foreach ($customers as $customer) {
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
            }
        }

        if($history=='LASTHUNDRED'){
            $customers=DB::select("SELECT TOP 100 * FROM (
                SELECT * FROM (
                    SELECT * FROM (
                        SELECT * FROM (
                            SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                        JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                        JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
                    JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
                JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
                JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)a on PSN=a.SnPeopel
                $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0 and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on m.CustomerSn=n.customer_id
            JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm   group by factorId HAVING COUNT(id)>1)b on b.factorSn=factorId
                WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5 and state=0" );
            foreach ($customers as $customer) {
                $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);
                $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
            }
        }

        if($history=='ALLALARMS'){
            $customers=DB::select("SELECT * FROM (
                SELECT * FROM (
                    SELECT * FROM (
                        SELECT * FROM (
                            SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                        JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                        JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
                    JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
                JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
                JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)a on PSN=a.SnPeopel
                $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0 and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on m.CustomerSn=n.customer_id
            JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm   group by factorId HAVING COUNT(id)>1)b on b.factorSn=factorId
                WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5 and state=0" );
            foreach ($customers as $customer) {
                $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);
                $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
            }
        }
    return Response::json($customers);
    }

    public function orderAlarms(Request $request)
    {
        $adminType= Session::get("adminType");
        $adminId=Session::get('asn');
        $queryPart="LEFT JOIN";
        if($adminType!=5){
            $queryPart="INNER JOIN";
        }
        
        $baseName=$request->get("baseName");
        $searchTerm=$request->get("searchTerm");
        $snMantagheh=$request->get("snMantagheh");
        $maxFacors=DB::select("SELECT max(SerialNoHDS) AS MaxFactorId,CustomerSn FROM Shop.dbo.FactorHDS WHERE CompanyNo=5 and FactDate>='1401/08/08' and not exists(select * from CRM.dbo.crm_alarm where factorId=SerialNoHDS) GROUP BY CustomerSn");

        $inAlarmFactors=DB::select("SELECT factorId,CustomerSn FROM CRM.dbo.crm_alarm 
        JOIN Shop.dbo.FactorHDS ON crm_alarm.factorId=FactorHDS.SerialNoHDS where state=0");

        foreach ($maxFacors as $factor) {
            foreach ($inAlarmFactors as $alarm) {
                if($factor->CustomerSn==$alarm->CustomerSn and ($factor->MaxFactorId>$alarm->factorId)){
                    DB::table("CRM.dbo.crm_alarm")->where("factorId",$alarm->factorId)->update(["state"=>1]);
                }
            }
        }
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        $customers=DB::select("SELECT *,CRM.dbo.getCustomerMantagheh(SnMantagheh) as NameRec,CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr FROM (
            SELECT * FROM (
                SELECT * FROM (
                    SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0 and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on k.PSN=n.customer_id
    JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm group by factorId)b on b.factorSn=factorId
    WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' AND not exists(select customerId from CRM.dbo.crm_inactiveCustomer where customerId=PSN) AND state=0 and Name like N'%$searchTerm%' AND SnMantagheh like N'%$snMantagheh%' order by $baseName");
        foreach ($customers as $customer) {
        $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

        $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

        }
        return Response::json($customers);
    }

    public function orderDoneAlarms(Request $request)
    {
        $baseName=$request->get("baseName");
        $searchTerm=$request->get("searchTerm");
        $snMantagheh=$request->get("snMantagheh");
        $searchTerm=$request->get("searchTerm");
        $snMantagheh=$request->get("snMantagheh");
        $adminType= Session::get("adminType");
        $adminId=Session::get('asn');
        $queryPart="LEFT JOIN";
        if($adminType!=5){
            $queryPart="INNER JOIN";
        }
        $maxFacors=DB::select("SELECT max(SerialNoHDS) AS MaxFactorId,CustomerSn FROM Shop.dbo.FactorHDS WHERE CompanyNo=5 and FactDate>='1401/08/08' and not exists(select * from CRM.dbo.crm_alarm where factorId=SerialNoHDS) GROUP BY CustomerSn");

        $inAlarmFactors=DB::select("SELECT factorId,CustomerSn FROM CRM.dbo.crm_alarm 
        JOIN Shop.dbo.FactorHDS ON crm_alarm.factorId=FactorHDS.SerialNoHDS where state=0");

        foreach ($maxFacors as $factor) {
            foreach ($inAlarmFactors as $alarm) {
                if($factor->CustomerSn==$alarm->CustomerSn and ($factor->MaxFactorId>$alarm->factorId)){
                    DB::table("CRM.dbo.crm_alarm")->where("factorId",$alarm->factorId)->update(["state"=>1]);
                }
            }
        }

        $customers=DB::select("SELECT * FROM (
            SELECT * FROM (
                SELECT * FROM (
                    SELECT * FROM (
                        SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                    JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                    JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
                JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
            JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
            JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)a on PSN=a.SnPeopel
            $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0  and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on m.CustomerSn=n.customer_id
        JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm   group by factorId HAVING COUNT(id)>1)b on b.factorSn=factorId
            WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and state=0 and Name like N'%$searchTerm%' AND SnMantagheh LIKE N'%$snMantagheh%'  order by $baseName");
        foreach ($customers as $customer) {
        $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

        $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

        }
        return Response::json($customers);
    }
    public function getAlarmsHistory(Request $request)
    {
        $history=$request->get("history");
        $yesterdayOfWeek = Jalalian::fromCarbon(Carbon::yesterday())->getDayOfWeek();
        $adminType= Session::get("adminType");
        $adminId=Session::get('asn');
        $queryPart="LEFT JOIN";
        if($adminType!=5){
            $queryPart="INNER JOIN";
        }
        $yesterday;
        $customers;
        if($yesterdayOfWeek==6){
            $yesterday = Jalalian::fromCarbon(Carbon::yesterday()->subDays(1))->format('Y/m/d');
        }else{
            $yesterday = Jalalian::fromCarbon(Carbon::yesterday())->format('Y/m/d');
        }
        $maxFacors=DB::select("SELECT max(SerialNoHDS) AS MaxFactorId,CustomerSn FROM Shop.dbo.FactorHDS WHERE CompanyNo=5 and FactDate>='1401/08/08' and not exists(select * from CRM.dbo.crm_alarm where factorId=SerialNoHDS) GROUP BY CustomerSn");

        $inAlarmFactors=DB::select("SELECT factorId,CustomerSn FROM CRM.dbo.crm_alarm 
        JOIN Shop.dbo.FactorHDS ON crm_alarm.factorId=FactorHDS.SerialNoHDS where state=0");

        foreach ($maxFacors as $factor) {
            foreach ($inAlarmFactors as $alarm) {
                if($factor->CustomerSn==$alarm->CustomerSn and ($factor->MaxFactorId>$alarm->factorId)){
                    DB::table("CRM.dbo.crm_alarm")->where("factorId",$alarm->factorId)->update(["state"=>1]);
                }
            }
        }
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        if($history=='YESTERDAY'){
            $customers=DB::select("SELECT *,CRM.dbo.getCustomerMantagheh(SnMantagheh) as NameRec,CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr FROM (
                SELECT * FROM (
                    SELECT * FROM (
                        SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                    JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                    JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
                JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,IsActive FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
        $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0 and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on k.PSN=n.customer_id
        JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm group by factorId)b on b.factorSn=factorId
                                    WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate='".$yesterday."' and state=0 and IsActive=1  and not exists(select customerId from CRM.dbo.crm_inactiveCustomer where customerId=PSN and crm_inactiveCustomer.state=1)");
            foreach ($customers as $customer) {
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

            }
        }

        if($history=='TODAY'){
                        $customers=DB::select("SELECT *,CRM.dbo.getCustomerMantagheh(SnMantagheh) as NameRec,CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr FROM (
                            SELECT * FROM (
                                SELECT * FROM (
                                    SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                                JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                                JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
                            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,IsActive FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
                    $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0 and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on k.PSN=n.customer_id
                    JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm group by factorId)b on b.factorSn=factorId
                                                WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate='".$todayDate."' and state=0 and IsActive=1  and not exists(select customerId from CRM.dbo.crm_inactiveCustomer where customerId=PSN and crm_inactiveCustomer.state=1)");
            foreach ($customers as $customer) {
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

            }
        }

        if($history=='LASTHUNDRED'){
                        $customers=DB::select("SELECT TOP 100 *,CRM.dbo.getCustomerMantagheh(SnMantagheh) as NameRec,CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr FROM (
                            SELECT * FROM (
                                SELECT * FROM (
                                    SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                                JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                                JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
                            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,IsActive FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
                    $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0 and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on k.PSN=n.customer_id
                    JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm group by factorId)b on b.factorSn=factorId
                                                WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5 and alarmDate<='".$todayDate."' and IsActive=1 and state=0 and not exists(select customerId from CRM.dbo.crm_inactiveCustomer where customerId=PSN and crm_inactiveCustomer.state=1)");
            foreach ($customers as $customer) {
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

            }
        }

        if($history=='ALLALARMS'){
            $customers=DB::select("SELECT *,CRM.dbo.getCustomerMantagheh(SnMantagheh) as NameRec,CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr FROM (
                SELECT * FROM (
                    SELECT * FROM (
                        SELECT DISTINCT * FROM (SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                    JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                    JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS,FactDate FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=c.factorId)g
                JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh,IsActive FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
        $queryPart (select name poshtibanName,lastName as poshtibanLastName,customer_id,admin_id as adminSn from CRM.dbo.crm_customer_added join CRM.dbo.crm_admin on admin_id=crm_admin.id where returnState=0 and admin_id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($adminId)))n on k.PSN=n.customer_id
        JOIN(select COUNT(id) as countCycle,factorId as factorSn from CRM.dbo.crm_alarm group by factorId)b on b.factorSn=factorId
                                    WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and IsActive=1 and state=0 and not exists(select customerId from CRM.dbo.crm_inactiveCustomer where customerId=PSN and crm_inactiveCustomer.state=1)");
foreach ($customers as $customer) {
$customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

$customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

}
}

        return Response::json($customers);
    }
    // ======================

    public function getAssesComment(Request $request)
    {
        $id=$request->get("assesId");
        $comment=DB::table("CRM.dbo.crm_assesment")->where("id",$id)->first();
        return Response::json($comment);
    }
    
    // ======================

    public function message(){
         $admins=DB::select("SELECT crm_admin.id,crm_admin.name,crm_admin.lastName,crm_admin.adminType as adminTypeId,crm_adminType.adminType FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_adminType.id=crm_admin.adminType WHERE deleted=0 and  crm_admin.id!=".Session::get('asn'));
         
         $messages=DB::select("SELECT * FROM (
                        SELECT * FROM(
                        SELECT MAX(id) maxID,senderId FROM CRM.dbo.crm_message WHERE  getterId=".Session::get('asn')." GROUP BY    crm_message.senderId)a
                        JOIN   CRM.dbo.crm_admin ON a.senderId=crm_admin.id )b
                        JOIN   CRM.dbo.crm_message ON b.maxID=crm_message.id WHERE  crm_message.senderId!=".Session::get('asn')."  and crm_message.getterId=".Session::get('asn'));
        return view("admin.message",['admins'=>$admins,'messages'=>$messages]);
    }

    public function loginUser(Request $request)
    {
        
        $userName=$request->post("userName");
        $password=$request->post("password");
        $result=DB::table("CRM.dbo.crm_admin")->where("username",$userName)->where("password",$password)->count();
        if($result>0){
            $admin=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE username='".$userName."' and password='".$password."'");
            $exactAdmin;
			
            foreach ($admin as $ad) {
                $exactAdmin=$ad;
            }
             $isLogedIn=DB::table("CRM.dbo.crm_loginTrack")->where('adminId',$exactAdmin->id)->where('loginDate',Carbon::now()->format('Y-m-d'))->count();
            if($isLogedIn>0){
                DB::table('CRM.dbo.crm_loginTrack')->where('adminId',$exactAdmin->id)->update(['loginTime'=>Carbon::now()]);
            }else{
                DB::table('CRM.dbo.crm_loginTrack')->insert(['adminId'=>$exactAdmin->id,'loginDate'=>Carbon::now()->format('Y-m-d'),'loginTime'=>Carbon::now()]);
            }
			
            Session::put("username",$exactAdmin->name.' '.$exactAdmin->lastName);
            Session::put("asn",$exactAdmin->id);
            Session::put("dsn",$exactAdmin->driverId);
            Session::put("adminType",$exactAdmin->adminType);
            Session::put("activeState",$exactAdmin->activeState);
            Session::put("hasAsses",$exactAdmin->hasAsses);
           // return $exactAdmin->hasAlarm;
            Session::put("hasAlarm",$exactAdmin->hasAlarm);
            Session::put("hasAllCustomer",$exactAdmin->hasAllCustomer);
            Session::put('groups',[291,297,299,312,313,314]);
            switch (Session::get("adminType")) {
                case 1:
                    return redirect('/home');
                    break;
                case 2:
                    return redirect('/calendar');
                    break;
                case 3:
                    return redirect('/calendar');
                    break; 
                case 4:
                    return redirect('/crmDriver?asn='.Session::get("dsn"));
                    break;   
                case 5:
                    return redirect('/home');
                    break;       
                case 6:
                    return redirect('/home');
                    break;  
                case 7:
                    return redirect('/home');
                    break;        
                default:
                    return redirect('/notfound');
                    break;
            }
            
            
        }else{
            return view('admin.login',['loginError'=>"نام کاربری و یا رمز ورود اشتباه است"]);
        }
    }
        
    // ======================

    public function logoutUser(Request $request)
    {
        Session::forget("username");
        Session::forget("asn");
        Session::forget("adminType");
        Session::forget("hasAsses");
        return view('admin.login');
    }
        
    // ======================

    public function kalaAction(){
                            
        // $products=DB::select("SELECT TOP 20  PubGoods.GoodName,PubGoods.GoodCde,PubGoods.GoodSn,star_GoodsSaleRestriction.hideKala,ViewGoodExists.Amount,a.maxFactDate FROM
        //                 Shop.dbo.PubGoods 
        //                 JOIN NewStarfood.dbo.star_GoodsSaleRestriction ON PubGoods.GoodSn=star_GoodsSaleRestriction.productId
        //                 JOIN Shop.dbo.ViewGoodExists ON PubGoods.GoodSn=ViewGoodExists.SnGood
        //                 JOIN(
        //                 Select MAX(Shop.dbo.FactorHDS.FactDate) as maxFactDate,FactorBYS.SnGood
        //                 FROM Shop.dbo.FactorHDS JOIN Shop.dbo.FactorBYS ON FactorBYS.SnFact=FactorHDS.SerialNoHDS
        //                 GROUP BY    FactorBYS.SnGood)a
        //                 ON a.SnGood=PubGoods.GoodSn
        //                 WHERE  ViewGoodExists.CompanyNo=5 and ViewGoodExists.FiscalYear=1399 and PubGoods.GoodGroupSn>49");
        
        $stocks=DB::select("SELECT * FROM Shop.dbo.Stocks WHERE  CompanyNo=5");
        $mainGroups=DB::select("SELECT * FROM NewStarfood.dbo.Star_Group_Def WHERE  selfGroupId=0");
        //return view ("admin.kalaAction",['products'=>$products,'stocks'=>$stocks,'mainGroups'=>$mainGroups]);
        return view ("admin.kalaAction",['stocks'=>$stocks,'mainGroups'=>$mainGroups]);
    }

    
    // ======================

    public function userProfile(){
        $adminId=Session::get("asn");
        $admin=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE  id=".$adminId);
        $exactAdmin;
        foreach ($admin as $admin) {
            $exactAdmin=$admin;
        }
        return view ("admin.userProfile",['admin'=>$exactAdmin]);
    }
    
    // ======================

    public function editProfile(){
        return view ("admin.editProfile");
    }
        
    // ======================

    public function editOwnAdmin(Request $request)
    {
        $adminId=Session::get("asn");
        $userName=$request->post("userName");
        $picture=$request->file("picture");
        $phone=$request->post("phone");
        $address=$request->post("address");
        $password=$request->post("password");
        $fileName=$picture->getClientOriginalName();
        $fileName=$adminId.".jpg";
        $picture->move("resources/assets/images/admins/",$fileName);
        $result=DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(['username'=>"".$userName."",'password'=>"".$password."",'address'=>"".$address."",'phone'=>"".$phone.""]);
        return redirect("/userProfile");
    }
    
    // ======================

    public function crmSetting(){
        return view ("admin.crmSetting");
    }
        
    // ======================

    public function karbarAction(Request $request)
    {
        $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where("crm_admin.adminType",2)->orwhere("crm_admin.adminType",3)->where('deleted',0)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType")->get();
        $adminTypes=DB::select("SELECT * FROM CRM.dbo.crm_adminType WHERE  id=2 or id=3");
        return view("admin.karbarAction",['admins'=>$admins,'adminTypes'=>$adminTypes]);
    }
        
    // ======================

    public function adminDashboard(Request $request)
    {
        $adminId=$request->get("asn");
        $admin=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->first();
       // if($admin->emptyState==0){
            $admins=DB::select("SELECT id, minDate,countPeopel,adminId,discription,name,lastName FROM(
                SELECT MIN(addedTime) as minDate,COUNT(customer_id) as countPeopel,adminId 
                FROM(SELECT crm_admin.id as adminId,k.addedTime,k.customer_id 
                FROM CRM.dbo.crm_admin LEFT JOIN  (SELECT * FROM CRM.dbo.crm_customer_added WHERE returnState=0)k ON crm_admin.id=k.admin_id 
                )d  GROUP BY    adminId)d
                LEFT JOIN   (SELECT * FROM CRM.dbo.crm_admin)a ON a.id=d.adminId WHERE  adminId=".$adminId);
            
            $info=DB::select("SELECT COUNT(customer_id)as countPeopels,crm_customer_added.admin_id 
                            FROM CRM.dbo.crm_customer_added where returnState=0 and crm_customer_added.admin_id=$adminId GROUP BY admin_id");
            
            $customers=DB::select("SELECT customer_id,returnState,addedTime,removedTime 
                            FROM CRM.dbo.crm_customer_added WHERE  returnState=0 and crm_customer_added.admin_id=".$adminId);
            
            $countAllFactor=0;
            $sumAllFactor=0;
            $countAllReturnedFactor=0;
            $sumAllReturnedFactor=0;
            $seenCustomers=array();
            foreach ($customers as $customer) {
                $removedDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
                $addedDate=Jalalian::fromCarbon(Carbon::parse($customer->addedTime))->format('Y/m/d');
                $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=3 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."'  GROUP BY    FactorHDS.SerialNoHDS");
                $returendFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=4 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."'  GROUP BY    FactorHDS.SerialNoHDS");
                foreach ($factors as $factor) {
                    if($factor->countFactors>0 and !in_array($customer->customer_id, $seenCustomers)){
                    array_push($seenCustomers,$customer->customer_id);
                    }
                    $countAllFactor+=$factor->countFactors;
                    $sumAllFactor+=$factor->sumFactors;
                }
                foreach ($returendFactors as $factor) {
                    $countAllReturnedFactor+=$factor->countFactors;
                    $sumAllReturnedFactor+=$factor->sumFactors;
                }
            }
            $boughtPeopelsCount=count($seenCustomers);
            $lastFactorAllMoney=DB::select("SELECT factorAllMoney,lastMonthReturnedAllMoney FROM CRM.dbo.crm_adminHistory WHERE   timeStamp=(
                SELECT MAX(timeStamp)from CRM.dbo.crm_adminHistory WHERE  adminId=".$adminId." GROUP BY    crm_adminHistory.adminId)");
            $lastFactorAllMoney1=0;
            $lastMonthReturnAllMoney1=0;
            foreach ($lastFactorAllMoney as $last) {
                $lastFactorAllMoney1=$last->factorAllMoney;
                $lastMonthReturnAllMoney1=$last->lastMonthReturnedAllMoney;
            }

            foreach ($info as $infor) {
                $infor->countFactor=$countAllFactor;
                $infor->totalMoneyHds=$sumAllFactor;
                $infor->countReturnFactor=$countAllReturnedFactor;
                $infor->totalReturnMoneyHds=$sumAllReturnedFactor;
                $infor->boughtPeopelsCount=$boughtPeopelsCount;
                $infor->lastMonthFactorAllMoney=$lastFactorAllMoney1;
                $infor->lastMonthReturnedAllMoney=$lastMonthReturnAllMoney1;
            }

            $history=DB::table("CRM.dbo.crm_adminHistory")->where("adminId",$adminId)->get();
            $minDate=DB::table("CRM.dbo.crm_customer_added")->where("returnState",0)->where("admin_id",$adminId)->min("addedTime");
            $countAllFactor=0;
            $sumAllFactor=0;
            $countAllReturnedFactor=0;
            $sumAllReturnedFactor=0;
            $customers=DB::select("SELECT customer_id,addedTime FROM CRM.dbo.crm_customer_added WHERE  admin_id=".$adminId." and returnState=0");
            $endDate=Jalalian::fromCarbon(Carbon::parse($minDate))->format('Y/m/d');
            $startDate=Jalalian::fromCarbon(Carbon::parse($minDate)->subdays(30))->format('Y/m/d');
			

            foreach ($customers as $customer) {
                $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactType=3 and FactorHDS.FactDate>='".$startDate."' and FactorHDS.FactDate<='".$endDate."'  GROUP BY    FactorHDS.CustomerSn");
                foreach ($factors as $factor) {
                    $countAllFactor+=$factor->countFactors;
                    $sumAllFactor+=$factor->sumFactors;
                }

                $returnFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactType=4 and FactorHDS.FactDate>='".$startDate."' and FactorHDS.FactDate<='".$endDate."'  GROUP BY    FactorHDS.CustomerSn");
                foreach ($returnFactors as $factor) {
                    $countAllReturnedFactor+=$factor->countFactors;
                    $sumAllReturnedFactor+=$factor->sumFactors;
                }
            }
            // DB::table("CRM.dbo.crm_adminHistory")->where("adminId",$adminId)->update(['noCommentCust'=>0,'noDoneWork'=>0]);
            $customers=array(array('countCustomers'=>count($customers),'countAllFactor'=>$countAllFactor,'sumAllFactor'=>$sumAllFactor,'countAllReturnedFactor'=>$countAllReturnedFactor,'sumAllReturnedFactor'=>$sumAllReturnedFactor));
            $justSubTreeInfo=self::getAllSubTreeInfo($adminId,0);
            $allTreeInfo=self::getAllSubTreeInfo($adminId,1);
            return Response::json([$admins,$info,$history,$customers,$justSubTreeInfo,$allTreeInfo]);
    }

    public function getAllSubTreeInfo($bossId,$isAllInfo)
    {
        $subTreeAdmins=DB::select("SELECT * from CRM.dbo.getAdminsOrBosses($bossId)");
        //  اطلاعات عمومی زیر مجموعه ها در ماه فعلی
        $minDateCustomerCurrMonthPub=0;
        $countAllFactorCurrMonthPub=0;
        $sumAllFactorCurrMonthPub=0;
        $countAllReturnedFactorCurrMonthPub=0;
        $sumAllReturnedFactorCurrMonthPub=0;
        $boughtPeopelsCountCurrMonthPub=0;
        $lastMonthReturnedTotalMoney = 0;
        $lastMonthTotalMoney = 0;
        $countAllCustomerCurrMonthPub=0;
        $lastMonthAllMoney=0;
        $lastMonthReturnAllMoney=0;

        //  اطلاعات عمومی زیر مجموعه ها در ماه قبلی

        $countAllFactorLastMonthPub=0;
        $sumAllFactorLastMonthPub=0;
        $countAllReturnedFactorLastMonthPub=0;
        $sumAllReturnedFactorLastMonthPub=0;

        //

        foreach ($subTreeAdmins as $adminId) {
            
                if($adminId->id==$bossId){
                    if($isAllInfo==0){

                    continue;

                    }
                }

                $adminId=$adminId->id;
                $admin=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->first();
            // if($admin->emptyState==0){
                $admins=DB::select("SELECT id, minDate,countPeopel,adminId,discription,name,lastName FROM(
                    SELECT MIN(addedTime) as minDate,COUNT(customer_id) as countPeopel,adminId 
                    FROM(SELECT crm_admin.id as adminId,k.addedTime,k.customer_id 
                    FROM CRM.dbo.crm_admin LEFT JOIN  (SELECT * FROM CRM.dbo.crm_customer_added WHERE returnState=0)k ON crm_admin.id=k.admin_id 
                    )d  GROUP BY    adminId)d
                    LEFT JOIN   (SELECT * FROM CRM.dbo.crm_admin)a ON a.id=d.adminId WHERE  adminId=".$adminId);
                
                $info=DB::select("SELECT COUNT(customer_id)as countPeopels,crm_customer_added.admin_id 
                                FROM CRM.dbo.crm_customer_added where returnState=0 and crm_customer_added.admin_id=$adminId GROUP BY admin_id");
                
                $customers=DB::select("SELECT customer_id,returnState,addedTime,removedTime 
                                FROM CRM.dbo.crm_customer_added WHERE  returnState=0 and crm_customer_added.admin_id=".$adminId);
                $countAllFactor=0;
                $sumAllFactor=0;
                $countAllReturnedFactor=0;
                $sumAllReturnedFactor=0;
                $seenCustomers=array();
                foreach ($customers as $customer) {
                    $removedDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
                    $addedDate=Jalalian::fromCarbon(Carbon::parse($customer->addedTime))->format('Y/m/d');
                    $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=3 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."'  GROUP BY    FactorHDS.SerialNoHDS");
                    $returendFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=4 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."'  GROUP BY    FactorHDS.SerialNoHDS");
                    foreach ($factors as $factor) {
                        if($factor->countFactors>0 and !in_array($customer->customer_id, $seenCustomers)){
                        array_push($seenCustomers,$customer->customer_id);
                        }
                        $countAllFactor+=$factor->countFactors;
                        $sumAllFactor+=$factor->sumFactors;
                    }
                    foreach ($returendFactors as $factor) {
                        $countAllReturnedFactor+=$factor->countFactors;
                        $sumAllReturnedFactor+=$factor->sumFactors;
                    }
                }
                $boughtPeopelsCount=count($seenCustomers);
                $lastMonthInfo=DB::select("SELECT factorAllMoney,lastMonthReturnedAllMoney FROM CRM.dbo.crm_adminHistory WHERE   timeStamp=(
                    SELECT MAX(timeStamp)from CRM.dbo.crm_adminHistory WHERE  adminId=".$adminId." GROUP BY    crm_adminHistory.adminId)");
                $lastFactorAllMoney1=0;
                $lastMonthReturnAllMoney1=0;
                foreach ( $lastMonthInfo as $last) {
                    $lastMonthAllMoney=$last->factorAllMoney;
                    $lastMonthReturnAllMoney=$last->lastMonthReturnedAllMoney;
                }

                // foreach ($info as $infor) {
                //     $infor->countFactor=$countAllFactor;
                //     $infor->totalMoneyHds=$sumAllFactor;
                //     $infor->countReturnFactor=$countAllReturnedFactor;
                //     $infor->totalReturnMoneyHds=$sumAllReturnedFactor;
                //     $infor->boughtPeopelsCount=$boughtPeopelsCount;
                //     $infor->lastMonthFactorAllMoney=$lastFactorAllMoney1;
                //     $infor->lastMonthReturnedAllMoney=$lastMonthReturnAllMoney1;
                // }

                $countAllFactorCurrMonthPub += $countAllFactor;
                $sumAllFactorCurrMonthPub += $sumAllFactor;
                $countAllReturnedFactorCurrMonthPub += $countAllReturnedFactor;
                $sumAllReturnedFactorCurrMonthPub += $sumAllReturnedFactor;
                $boughtPeopelsCountCurrMonthPub += $boughtPeopelsCount;
                $countAllCustomerCurrMonthPub += count($customers);
                $lastMonthReturnedTotalMoney += $lastMonthReturnAllMoney; 
                $lastMonthTotalMoney += $lastMonthAllMoney;
                if(count($admins)>0){
                    if('"'.$admins[0]->minDate.'"' < '"'.$minDateCustomerCurrMonthPub.'"'){
                        $minDateCustomerCurrMonthPub=$admins[0]->minDate;
                    }
                }


                $history=DB::table("CRM.dbo.crm_adminHistory")->where("adminId",$adminId)->get();
                $minDate=DB::table("CRM.dbo.crm_customer_added")->where("returnState",0)->where("admin_id",$adminId)->min("addedTime");
                $countAllFactor=0;
                $sumAllFactor=0;
                $countAllReturnedFactor=0;
                $sumAllReturnedFactor=0;
                $customers=DB::select("SELECT customer_id,addedTime FROM CRM.dbo.crm_customer_added WHERE  admin_id=".$adminId." and returnState=0");
                $endDate=Jalalian::fromCarbon(Carbon::parse($minDate))->format('Y/m/d');
                $startDate=Jalalian::fromCarbon(Carbon::parse($minDate)->subdays(30))->format('Y/m/d');
                

                foreach ($customers as $customer) {
                    
                    $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactType=3 and FactorHDS.FactDate>='".$startDate."' and FactorHDS.FactDate<='".$endDate."'  GROUP BY    FactorHDS.CustomerSn");
                    
                    foreach ( $factors as $factor ) {

                        $countAllFactor += $factor->countFactors;

                        $sumAllFactor += $factor->sumFactors;

                    }

                    $returnFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors , SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactType=4 and FactorHDS.FactDate>='".$startDate."' and FactorHDS.FactDate<='".$endDate."'  GROUP BY    FactorHDS.CustomerSn");
                    
                    foreach ($returnFactors as $factor) {

                        $countAllReturnedFactor += $factor->countFactors;

                        $sumAllReturnedFactor += $factor->sumFactors;

                    }

                }

                // DB::table("CRM.dbo.crm_adminHistory")->where("adminId",$adminId)->update(['noCommentCust'=>0,'noDoneWork'=>0]);//
                // اطلاعات مشتریان ماه قبل
                //      $customers

              //  $customers=array(array('countCustomers'=>count($customers),'countAllFactor'=>$countAllFactor,'sumAllFactor'=>$sumAllFactor,'countAllReturnedFactor'=>$countAllReturnedFactor,'sumAllReturnedFactor'=>$sumAllReturnedFactor));
                
                $countAllFactorLastMonthPub += $countAllFactor;
                $sumAllFactorLastMonthPub += $sumAllFactor;
                $countAllReturnedFactorLastMonthPub += $countAllReturnedFactor;
                $sumAllReturnedFactorLastMonthPub += $sumAllReturnedFactor;

                // اطلاعات ماه فعلی
                //$info
                //تاریخچه این ادمین
                //$admins
                // 
                // 

            }
            return ([
            'minDateCustomerCurrMonthPub'=>$minDateCustomerCurrMonthPub,
            'countAllFactorCurrMonthPub'=> $countAllFactorCurrMonthPub,
            'sumAllFactorCurrMonthPub'=> $sumAllFactorCurrMonthPub,
            'countAllReturnedFactorCurrMonthPub'=> $countAllReturnedFactorCurrMonthPub,
            'sumAllReturnedFactorCurrMonthPub'=> $sumAllReturnedFactorCurrMonthPub,
            'boughtPeopelsCountCurrMonthPub'=> $boughtPeopelsCountCurrMonthPub,
            'countAllFactorLastMonthPub'=> $countAllFactorLastMonthPub,
            'sumAllFactorLastMonthPub'=> $sumAllFactorLastMonthPub,
            'countAllReturnedFactorLastMonthPub'=> $countAllReturnedFactorLastMonthPub,
            'sumAllReturnedFactorLastMonthPub'=> $sumAllReturnedFactorLastMonthPub,
            'countAllCustomerCurrMonthPub' =>$countAllCustomerCurrMonthPub,
            'lastMonthReturnedTotalMoney' => $lastMonthReturnedTotalMoney,
            'lastMonthTotalMoney'=> $lastMonthTotalMoney,
    		'customers'=>[array('countCustomers'=>$countAllCustomerCurrMonthPub,'countAllFactor'=>$countAllFactorLastMonthPub,'sumAllFactor'=>$sumAllFactorLastMonthPub,'countAllReturnedFactor'=>$countAllReturnedFactorLastMonthPub,'sumAllReturnedFactor'=>$sumAllReturnedFactorLastMonthPub)]
        ]);
    }

    public function getAdminHistoryComment(Request $request)
    {
        $adminId=$request->get("id");
        $timeStamp=$request->get("timeStamp");
        $info=DB::table("CRM.dbo.crm_adminHistory")->where("adminId",$adminId)->where("timeStamp",$timeStamp)->first();
        return Response::json($info);
    }
    
    // ======================

    public function searchAdminByNameCode(Request $request)
    {
        $searchTerm=$request->get("searchTerm");

        $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType,crm_admin.id AS id 
                        FROM CRM.dbo.crm_admin
                        JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id
                        WHERE ( crm_admin.adminType=2 and crm_admin.deleted=0) AND (crm_admin.name LIKE '%$searchTerm%' OR crm_admin.lastName LIKE '%$searchTerm%')");
        return Response::json($admins);
    }
        
    // ======================

    public function searchAdminByType(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm!=0){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin
                            JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id
                            WHERE deleted=0 and crm_admin.adminType=".$searchTerm);
            return Response::json($admins);
        }else{
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin
                            JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id where deleted=0");
            return Response::json($admins);
        }
    }
        
    // ======================

    public function searchAdminByActivation(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm==0){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin
                            JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id WHERE  crm_admin.adminType=2 and deleted=0");
            return Response::json($admins);
        }
        if($searchTerm==1){
            $searchTerm=$request->get("searchTerm");
            
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin
                            JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id
                            WHERE  crm_admin.activeState=1 AND  (crm_admin.adminType=2 or deleted=0)");
            
            return Response::json($admins);
        }
        if($searchTerm==2){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin
                            JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id
                            WHERE  crm_admin.activeState=0 AND  (crm_admin.adminType=2 and deleted=0)");
            
            return Response::json($admins);
        }
    }
    
    // ======================

    public function searchAdminFactorOrNot(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        
        if($searchTerm==1){
            $admins=DB::select("SELECT * FROM (
                            SELECT DISTINCT admin_id FROM(
                            SELECT * FROM (
                            SELECT * FROM CRM.dbo.crm_customer_added WHERE  admin_id not IN  (
                            SELECT DISTINCT admin_id FROM (
                            SELECT COUNT(SerialNoHDS)countFactor,CustomerSn FROM Shop.dbo.FactorHDS WHERE  FactDate='1401/05/6' GROUP BY    CustomerSn)a JOIN   (SELECT * FROM CRM.dbo.crm_customer_added WHERE  returnState=0)l ON a.CustomerSn=l.customer_id
                            ))b
                            WHERE  returnState=0)c)d
                            JOIN   (SELECT crm_admin.adminType as adminKind,name,lastName,crm_adminType.adminType,crm_admin.id FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id where deleted=0)e ON d.admin_id=e.id");
            return Response::json($admins);
        }

        if($searchTerm==2){
            $admins=DB::select("SELECT * FROM (
                            SELECT DISTINCT admin_id FROM(
                            SELECT * FROM (
                            SELECT * FROM CRM.dbo.crm_customer_added WHERE  admin_id IN  (
                            SELECT DISTINCT admin_id FROM (
                            SELECT COUNT(SerialNoHDS)countFactor,CustomerSn FROM Shop.dbo.FactorHDS WHERE  FactDate='1401/05/6' GROUP BY    CustomerSn)a JOIN   (SELECT * FROM CRM.dbo.crm_customer_added WHERE  returnState=0)l ON a.CustomerSn=l.customer_id
                            ))b
                            WHERE  returnState=0)c)d
                            JOIN   (SELECT crm_admin.adminType as adminKind,name,lastName,crm_adminType.adminType,crm_admin.id FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id where deleted=0)e ON d.admin_id=e.id");
            return Response::json($admins);
        }

        if($searchTerm==0){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin
            JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id WHERE  crm_admin.adminType=2 and deleted=0");
                return Response::json($admins);
                }
    }
        
    // ======================

    public function searchAdminLoginOrNot(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        if($searchTerm==2){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin JOIN CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id WHERE crm_admin.id NOT IN(
                            SELECT DISTINCT adminId FROM CRM.dbo.crm_loginTrack) and (crm_admin.adminType=2 ) and deleted=0");
            return Response::json($admins);
        }
        if($searchTerm==1){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin JOIN CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id WHERE crm_admin.id IN(
                            SELECT DISTINCT adminId FROM CRM.dbo.crm_loginTrack) and (crm_admin.adminType=2) and deleted=0");
            return Response::json($admins);
        }
        if($searchTerm==0){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin JOIN CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id  and (crm_admin.adminType=2) and deleted=0");
            return Response::json($admins);
        }
    }
    
    // ======================

    public function searchAdminCustomerLoginOrNot(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        
        if($searchTerm==2){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id
                            WHERE deleted=0 and  crm_admin.id not in(
                            SELECT * FROM(
                            SELECT DISTINCT admin_id FROM(
                            SELECT * FROM (
                            SELECT * FROM(
                            SELECT PSN FROM Shop.dbo.Peopels WHERE  PSN in(
                            SELECT customerId FROM NewStarfood.dbo.star_customerSession1))a
                            JOIN   (SELECT customer_id,admin_id FROM CRM.dbo.crm_customer_added WHERE  returnState=0 )b ON a.PSN=b.customer_id)c)f)g
                            )  and (crm_admin.adminType=2 or crm_admin.adminType=3)");
            return Response::json($admins);
        }
        
        if($searchTerm==1){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id
                            WHERE deleted=0 and crm_admin.id in(
                            SELECT * FROM(
                            SELECT DISTINCT admin_id FROM(
                            SELECT * FROM (
                            SELECT * FROM(
                            SELECT PSN FROM Shop.dbo.Peopels WHERE  PSN in(
                            SELECT customerId FROM NewStarfood.dbo.star_customerSession1))a
                            JOIN   (SELECT customer_id,admin_id FROM CRM.dbo.crm_customer_added WHERE  returnState=0 )b ON a.PSN=b.customer_id)c)f)g
                            )  and (crm_admin.adminType=2 or crm_admin.adminType=3)");
            return Response::json($admins);
        }
        if($searchTerm==0){
            $admins=DB::select("SELECT name,lastName,address,password,activeState,
            sex,discription,emptyState,driverId,crm_admin.adminType as adminTypeId,crm_adminType.adminType,crm_admin.id as id 
            FROM CRM.dbo.crm_admin JOIN   CRM.dbo.crm_adminType ON crm_admin.adminType=crm_adminType.id  WHERE  crm_admin.adminType=2 and deleted=0");
            return Response::json($admins);
        }
    }
    
    // ======================

    public function getAdminTodayInfo(Request $request)
    {
        $adminId=$request->get("asn");
        $todayDate=Carbon::now()->format('Y-m-d');
        $today=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        
        $todayInfo=DB::select("SELECT * FROM (SELECT countPeopel,countFactor,countComment,a.admin_id FROM(
                            SELECT COUNT(crm_customer_added.id)as countPeopel,crm_customer_added.admin_id FROM CRM.dbo.crm_customer_added WHERE  crm_customer_added.returnState=0 GROUP BY    admin_id)
                            a
                            left JOIN   (SELECT COUNT(FactorHDS.SerialNoHDS) as countFactor,admin_id FROM Shop.dbo.FactorHDS JOIN   CRM.dbo.crm_customer_added ON FactorHDS.CustomerSn=crm_customer_added.customer_id  WHERE  FactorHDS.FactDate='".$today."'  GROUP BY    admin_id)
                            b
                            ON a.admin_id=b.admin_id
                            left JOIN   (SELECT COUNT(id) as countComment,crm_comment.adminId FROM CRM.dbo.crm_comment WHERE  crm_comment.TimeStamp='".$todayDate."' GROUP BY    adminId)
                            c
                            ON a.admin_id=c.adminId)d
                            JOIN   CRM.dbo.crm_admin ON d.admin_id=crm_admin.id
                            WHERE  d.admin_id=".$adminId);
        
        $todayAdminInfo=DB::select("SELECT count(customerId) countComment FROM(
                                SELECT * FROM (SELECT * FROM (
                                SELECT Peopels.Name,Peopels.PSN,admin_id,returnState FROM Shop.dbo.Peopels
                                JOIN   CRM.dbo.crm_customer_added ON Peopels.PSN=CRM.dbo.crm_customer_added.customer_id)
                                a
                                left JOIN   (SELECT COUNT(FactorHDS.SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS WHERE  FactorHDS.FactDate='".$today."'  GROUP BY    FactorHDS.CustomerSn)
                                b
                                ON a.PSN=b.CustomerSn
                                WHERE  returnState=0 AND  admin_id=".$adminId.")c
                                JOIN   (SELECT MAX(timeStamp) as maxHour,customerId FROM CRM.dbo.crm_comment WHERE  CAST(timeStamp as DATE)='".$todayDate."' GROUP BY    customerId)d
                                ON d.customerId=c.PSN)d");
        
                                $countFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactor FROM Shop.dbo.FactorHDS WHERE  FactorHDS.CustomerSn in(SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE  admin_id=".$adminId." and returnState=0) and FactorHDS.FactDate='".$today."'");
        $countCustomers=DB::select("SELECT COUNT(id) as countCustomer FROM CRM.dbo.crm_customer_added WHERE  returnState=0 and admin_id=".$adminId);
        $countComment=0;
        $countFactor=0;
        $countCustomer=0;
        
        foreach ($todayAdminInfo as $info) {
            $countComment=$info->countComment;
        }
        
        foreach ($countFactors as $fact) {
            $countFactor=$fact->countFactor;
        }
        
        foreach ($countCustomers as $cust) {
            $countCustomer=$cust->countCustomer;
        }
        $loginTime=DB::table("CRM.dbo.crm_loginTrack")->where('adminId',$adminId)->select('loginTime')->first();
        $loginTime1=0;
        
        if($loginTime){
        $loginTime1=$loginTime->loginTime;
        }
        
        $adminTodayInfo=array(array('countComments'=>$countComment,'countFctors'=>$countFactor,'countCustomers'=>$countCustomer,'loginTime'=>$loginTime1));
        
        $customers=DB::select("SELECT * FROM (SELECT * FROM (
                        SELECT Peopels.Name,Peopels.PSN,admin_id,returnState FROM Shop.dbo.Peopels
                        JOIN   CRM.dbo.crm_customer_added ON Peopels.PSN=CRM.dbo.crm_customer_added.customer_id)
                        a
                        left JOIN   (SELECT COUNT(FactorHDS.SerialNoHDS) as countFactor,FactorHDS.CustomerSn FROM Shop.dbo.FactorHDS WHERE  FactorHDS.FactDate='".$today."'  GROUP BY    FactorHDS.CustomerSn)
                        b
                        ON a.PSN=b.CustomerSn
                        WHERE  returnState=0 and admin_id=".$adminId.")c
                        JOIN   (SELECT MAX(timeStamp) as maxHour,customerId FROM CRM.dbo.crm_comment WHERE  CAST(timeStamp as DATE)='".$todayDate."' GROUP BY    customerId)d
                        ON d.customerId=c.PSN");
        return Response::json([$todayInfo,$customers,$adminTodayInfo]);
    }
        
    // ======================

    public function checkUserNameExistance(Request $request)
    {
        $username=$request->get("username");
        $countExistance=DB::table("CRM.dbo.crm_admin")->where('username',$username)->count();
        return Response::json($countExistance);
    }
        
    // ======================

    public function getAdminForEmpty(Request $request)
    {
        $adminId=$request->get("asn");
        $admin=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->get();
        return Response::json($admin);
    }
        
    // ======================

    public function getAdminForMove(Request $request)
    {
        $adminId=$request->get("asn");
        $admin=DB::select("SELECT * FROM CRM.dbo.crm_admin admin JOIN CRM.dbo.crm_hasAccess access ON admin.id=access.adminId WHERE id =$adminId AND deleted=0");
        $otherAdmins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE id !=".$adminId." and (adminType!=5 and  adminType!=1 and  adminType!=4 and deleted=0 )");
        $bossAdmins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE id !=".$adminId." and deleted=0 and (adminType!=4)");


        return Response::json([$admin,$otherAdmins,$bossAdmins]);
    }
    
    // ======================

    public function emptyAdmin(Request $request)
    {
        $adminId=$request->get("asn");
        $personType=DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->get()[0];
        //برای محاسبه اطلاعات تاریخچه بازاریابها
        //نصب‌ها---1
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
        //امتیاز های اختصاصی
        $istallBonus=0;
        $aghlamBonus=0;
        $monyBonus=0;
        $countBuyBonus=0;
        //امتیاز اضافی فرد
        $extarBonus=0;
        //مجموع کل امتیازات
        $allBonuses=0;
        $all_bonus_since_Empty=0;


//بازاریابها
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
//تخلیه راننده ها
        $countFactorComTg=0;
        $weakServiceComTg=0;
        $mediumServiceComTg=0;
        $strongServiceComTg=0;
        $countFactorComTgBonus=0;
        $weakServiceComTgBonus=0;
        $mediumServiceComTgBonus=0;
        $strongServiceComTgBonus=0;
        $count_all_Factor=0;
        $all_Strong_Service=0;
        $all_Medium_Service=0;
        $count_all_WeakService=0;
        $count_all_Factor_Bonus=0;
        $all_Weak_Service_Bonus=0;
        $all_Strong_Service_Bonus=0;
        $all_Medium_Service_Bonus=0;
//  مشتریان مربوط به پشتیبان اختصاصی
        $customers=DB::select("SELECT customer_id,returnState,addedTime FROM CRM.dbo.crm_customer_added 
        WHERE  returnState=0 and crm_customer_added.admin_id=".$adminId." and customer_id is not null");
        $countAllFactor=0;
        $sumAllFactor=1;
        $countAllReturnedFactor=0;
        $sumAllReturnedFactor=1;
        $seenCustomers=array();
        // تخلیه همه ادمین ها

        foreach ($customers as $customer) {

            $removedDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
            $addedDate=Jalalian::fromCarbon(Carbon::parse($customer->addedTime))->format('Y/m/d');
            $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=3 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."' GROUP BY    FactorHDS.SerialNoHDS");
            $returnedFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=4 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."' GROUP BY    FactorHDS.SerialNoHDS");

            foreach ($factors as $factor) {
                if($factor->countFactors>0 and !in_array($customer->customer_id, $seenCustomers)){
                array_push($seenCustomers,$customer->customer_id);
                }
                $countAllFactor+=$factor->countFactors;
                $sumAllFactor+=$factor->sumFactors;
            }
            
            foreach ($returnedFactors as $factor) {
                $countAllReturnedFactor+=$factor->countFactors;
                $sumAllReturnedFactor+=$factor->sumFactors;
            }
        }

        $boughtPeopelsCount=count($seenCustomers);


        $info=DB::select("SELECT COUNT(customer_id)as countPeopels,crm_customer_added.admin_id FROM CRM.dbo.crm_customer_added 
            JOIN CRM.dbo.crm_admin ON crm_customer_added.admin_id=crm_admin.id 
            WHERE returnState=0 and crm_customer_added.admin_id=".$adminId." GROUP BY    admin_id");

        foreach ($info as $infor) {
            $infor->countFactor=$countAllFactor;
            $infor->totalMoneyHds=$sumAllFactor;
            $infor->boughtPeopelsCount=$boughtPeopelsCount;
            $infor->sumAllReturnedFactor=$sumAllReturnedFactor;
        }
        $allActiveCustomerCount=0;
        if(count($info)>0){
            $allActiveCustomerCount=$info[0]->countPeopels;
        }
        

//برای اطلاعات ماه قبل همین مشتریان
        $history=DB::table("CRM.dbo.crm_adminHistory")->where("adminId",$adminId)->get();
        $minDate=DB::table("CRM.dbo.crm_customer_added")->where("returnState",0)->where("admin_id",$adminId)->min("addedTime");
        $countAllFactor2=0;
        $sumAllFactor2=0;
        $countAllReturnedFactor2=0;
        $sumAllReturnedFactor2=0;
        $customers=DB::select("SELECT customer_id,addedTime FROM CRM.dbo.crm_customer_added WHERE  admin_id=".$adminId." and returnState=0");
        $endDate=Jalalian::fromCarbon(Carbon::parse($minDate))->format('Y/m/d');
        $startDate=Jalalian::fromCarbon(Carbon::parse($minDate)->subdays(30))->format('Y/m/d');

        foreach ($customers as $customer) {
            $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactType=3 and FactorHDS.FactDate>='".$startDate."' and FactorHDS.FactDate<='".$endDate."'  GROUP BY    FactorHDS.CustomerSn");
            foreach ($factors as $factor) {
                $countAllFactor2+=$factor->countFactors;
                $sumAllFactor2+=$factor->sumFactors;
            }

            $returnFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactType=4 and FactorHDS.FactDate>='".$startDate."' and FactorHDS.FactDate<='".$endDate."'  GROUP BY    FactorHDS.CustomerSn");
            foreach ($returnFactors as $factor) {
                $countAllReturnedFactor2+=$factor->countFactors;
                $sumAllReturnedFactor2+=$factor->sumFactors;
            }
        }

        //اطلاعات ماه قبل تاریخچه
        $lastHistoryInfo=DB::select("SELECT * FROM CRM.DBO.crm_adminHistory WHERE crm_adminHistory.timeStamp=(SELECT MAX(timeStamp) as MaxDate FROM CRM.dbo.crm_adminHistory WHERE adminId=".$adminId." GROUP BY    adminId)");
        
        $meanIncreas=0;
        
        if($sumAllFactor2!=0){
            $meanIncreas=($sumAllFactor-$sumAllFactor2)/$sumAllFactor2;
            }
        //اطلاعات مربوط به کامنت
        $comment=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->first();
		$adminComment="";
		if($comment){
		$adminComment=$comment->discription;
		}
        $countAllCommentedCustomers=DB::select("select COUNT(customerId) AS countComment from(
                                                select distinct customerId from CRM.dbo.crm_comment where adminId=$adminId and TimeStamp>=(select min(addedTime) from CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId )
                                                and TimeStamp<=(select CURRENT_TIMESTAMP  
                                                )
                                                )a");
        $countAllComments=0;
        if(count($countAllCommentedCustomers)>0){
            $countAllComments=$countAllCommentedCustomers[0]->countComment;
        }
        $nocommentedCustomers=$allActiveCustomerCount -$countAllComments;

        if($nocommentedCustomers<0){
            $nocommentedCustomers=0; 
        }

        $todayDate=Carbon::now()->format('Y-m-d');
        $countNoDoneWork=\DB::select("SELECT count(countJob) as countJob from(
            SELECT COUNT(id) as countJob,customer_id from (
            SELECT * from (
            SELECT crm_workList.commentId,crm_workList.id,crm_workList.doneState,crm_workList.specifiedDate,crm_comment.customerId from CRM.dbo.crm_workList 
            JOIN CRM.dbo.crm_comment on crm_workList.commentId=crm_comment.id where doneState=0)a
            JOIN (SELECT customer_id,returnState,admin_id from CRM.dbo.crm_customer_added )c on a.customerId =c.customer_id where c.returnState=0 and admin_id=$adminId)b
            where specifiedDate<='".$todayDate."'
            group by customer_id)a");
        if($personType->adminType==3){
            //در صورتیکه بازاریاب باشد.
            $saleExperInfo=new SalseExper;
            $poshtibanBonusInfo=$saleExperInfo->getBazaryabActionInformation($adminId);
            $count_All_Install=$poshtibanBonusInfo['count_All_Install'];
            $count_All_aghlam=$poshtibanBonusInfo['count_All_aghlam'];
            $count_All_New_buys=$poshtibanBonusInfo['count_All_New_buys'];
            $targetInfo=$poshtibanBonusInfo['targetsCompletion'];
            // اکمال تارگت ها
            $istallComTg=$targetInfo['istallComTg'];
            $aghlamComTg=$targetInfo['aghlamComTg'];
            $monyComTg=$targetInfo['monyComTg'];
            $countBuyComTg=$targetInfo['countBuyComTg'];
            //امتیاز تارگت ها
            $istallComTgBonus=$targetInfo['istallComTgBonus'];
            $aghlamComTgBonus=$targetInfo['aghlamComTgBonus'];
            $monyComTgBonus=$targetInfo['monyComTgBonus'];
            $countBuyComTgBonus=$targetInfo['countBuyComTgBonus'];
            //امتیاز های اختصاصی
            $istallBonus=$poshtibanBonusInfo['bonus_All_Install'];
            $aghlamBonus=$poshtibanBonusInfo['bonus_All_aghlam'];
            $monyBonus=$poshtibanBonusInfo['bonus_all_money'];
            $countBuyBonus=$poshtibanBonusInfo['bonus_All_New_buys'];
            //امتیاز اضافی فرد
            if($poshtibanBonusInfo['all_monthly_bonuses']){
                $extarBonus=$poshtibanBonusInfo['all_monthly_bonuses'];
            }else{
                $extarBonus=0;
            }
            //مجموع کل امتیازات
            $all_bonus_since_Empty=$poshtibanBonusInfo['all_bonus_since_Empty'];
        }

    if($personType->adminType==2){
    // در صورتیکه پشتیبان باشد
    $poshtiban=new Poshtiban;
    $poshtibanBonusInfo=$poshtiban->getPoshtibanActionInformation($adminId);
    $count_All_aghlam=$poshtibanBonusInfo['count_All_aghlam'];
    $count_All_New_buys=$poshtibanBonusInfo['count_All_New_buys'];
    $istallComTg=$poshtibanBonusInfo['istallComTg'];
    $aghlamComTg=$poshtibanBonusInfo['aghlamComTg'];
    $monyComTg=$poshtibanBonusInfo['monyComTg'];
    $countBuyComTg=$poshtibanBonusInfo['countBuyComTg'];
    //تارگت ها تکمیل شده

    $istallComTgBonus=0;
    $aghlamComTgBonus=$poshtibanBonusInfo['aghlamComTgBonus'];
    $monyComTgBonus=$poshtibanBonusInfo['monyComTgBonus'];
    $countBuyComTgBonus=$poshtibanBonusInfo['countBuyComTgBonus'];
    //امتیاز های اختصاصی
    $istallBonus=$poshtibanBonusInfo['bonus_All_Install'];
    $aghlamBonus=$poshtibanBonusInfo['bonus_All_aghlam'];
    $monyBonus=$poshtibanBonusInfo['bonus_all_money'];
    $countBuyBonus=$poshtibanBonusInfo['bonus_All_New_buys'];
    //امتیاز اضافی فرد
    if($poshtibanBonusInfo['all_monthly_bonuses']){
        $extarBonus=$poshtibanBonusInfo['all_monthly_bonuses'];
    }else{
        $extarBonus=0;
    }
    //مجموع کل امتیازات
    $all_bonus_since_Empty=$poshtibanBonusInfo['all_bonus_since_Empty'];
    }
    if($personType->adminType==4){
// در صورتیکه راننده باشد
    $driverInfo=new Poshtiban;
    $driverBonusInfo=$driverInfo->getDriverActionInfo($adminId);
   // return Response::json($driverBonusInfo);
    $all_bonus_since_Empty=$driverBonusInfo["all_bonus_since_Empty"];
    $count_All_aghlam=$driverBonusInfo["count_All_aghlam"];
    $count_All_Install=$driverBonusInfo["count_All_Install"];
    $count_All_New_buys=$driverBonusInfo["count_All_New_buys"];
    $aghlamBonus=$driverBonusInfo["bonus_All_aghlam"];
    $istallBonus=$driverBonusInfo["bonus_All_Install"];
    $countBuyBonus=$driverBonusInfo["bonus_All_New_buys"];
    $monyBonus=$driverBonusInfo["bonus_all_money"];
    $extarBonus=$driverBonusInfo["all_monthly_bonuses"];
    $aghlamComTg=$driverBonusInfo["aghlamComTg"];
    $monyComTg="هیچکدام";
    $countBuyComTg="هیچکدام";
    $istallComTg="هیچکدام";
    $countBuyComTgBonus=$driverBonusInfo["countBuyComTgBonus"];
    $monyComTgBonus=$driverBonusInfo["monyComTgBonus"];
    $aghlamComTgBonus=$driverBonusInfo["aghlamComTgBonus"];
    //راننده ها
    $countFactorComTg=$driverBonusInfo["countFactorComTg"];
    $weakServiceComTg=$driverBonusInfo["weakServiceComTg"];
    $mediumServiceComTg=$driverBonusInfo["mediumServiceComTg"];
    $strongServiceComTg=$driverBonusInfo["strongServiceComTg"];
    $countFactorComTgBonus=$driverBonusInfo["countFactorComTgBonus"];
    $weakServiceComTgBonus=$driverBonusInfo["weakServiceComTgBonus"];
    $mediumServiceComTgBonus=$driverBonusInfo["mediumServiceComTgBonus"];
    $strongServiceComTgBonus=$driverBonusInfo["strongServiceComTgBonus"];
    $count_all_Factor=$driverBonusInfo["count_all_Factor"];
    $all_Strong_Service=$driverBonusInfo["all_Strong_Service"];
    $all_Medium_Service=$driverBonusInfo["all_Medium_Service"];
    $count_all_WeakService=$driverBonusInfo["count_all_WeakService"];
    $count_all_Factor_Bonus=$driverBonusInfo["count_all_Factor_Bonus"];
    $all_Weak_Service_Bonus=$driverBonusInfo["all_Weak_Service_Bonus"];
    $all_Strong_Service_Bonus=$driverBonusInfo["all_Strong_Service_Bonus"];
    $all_Medium_Service_Bonus=$driverBonusInfo["all_Medium_Service_Bonus"];


    }

    if($personType->adminType==6){
        //در صورتیکه مدیر باشد
    }
    if($personType->adminType==7){
        //در صورتیکه سرپرست باشد
    }
        //ثبت تاریخچه
        DB::table("CRM.dbo.crm_adminHistory")->insert(['adminId'=>$adminId
													   ,'countPeople'=>$allActiveCustomerCount
													   ,'countFactor'=>$countAllFactor
													   ,'countBuyPeople'=>$boughtPeopelsCount
													   ,'returnedFactorAllMoney'=>$sumAllReturnedFactor
													   ,'factorAllMoney'=>$sumAllFactor
        											   ,'lastMonthAllMoney'=>$sumAllFactor2
													   ,'lastMonthReturnedAllMoney'=>$sumAllReturnedFactor2
													   ,'meanIncrease'=>$meanIncreas
													   ,'comment'=>"".$adminComment.""
													   ,'noCommentCust'=>$nocommentedCustomers
													   ,'noDoneWork'=>$countNoDoneWork[0]->countJob
													   ,'countReturnedFactor'=>$countAllReturnedFactor
													   ,'countLastMonthFactor'=>$countAllFactor2
													   ,'countLastMonthReturnedFactor'=>$countAllReturnedFactor2
													   ,'allInstall'=>$count_All_Install
													   ,'allPrimaryBuy'=>$count_All_New_buys
													   ,'allAghlam'=>$count_All_aghlam
													   ,'lastComInstallTg'=>"".$istallComTg.""
													   ,'lastComPrimBuyTg'=>"هیچکدام"
													   ,'lastComAghlamTg'=>"".$aghlamComTg.""
													   ,'lastComMoneyTg'=>"".$monyComTg.""
													   ,'installBonus'=> $istallBonus
													   ,'primeBuyBonus'=> $countBuyBonus
													   ,'totalMoneyBonus'=>$monyBonus
													   ,'totalAghlamBonus'=>$aghlamBonus
													   ,'lastComInstallTgB'=>$istallComTgBonus
													   ,'lastComPrimBuyTgB'=>0
													   ,'lastComAghlamTgB'=>$aghlamComTgBonus 
													   ,'lastComMoneyTgB'=>$countBuyComTgBonus
													   ,'extraBonus'=>$extarBonus
													   ,'allBonus'=>$all_bonus_since_Empty,
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
													   'all_Medium_Service_Bonus'=>$all_Medium_Service_Bonus,
													   'money_com_TgBonus'=>$monyComTgBonus
                                                    ]);
        //ثبت تاریخ حذف مشتریان
        DB::update("UPDATE CRM.dbo.crm_customer_added set removedTime='".Carbon::now()."' WHERE  returnState=0 and admin_id=".$adminId);
        //تخیلیه مشتریان
        DB::update("UPDATE CRM.dbo.crm_customer_added set returnState=1, gotEmpty=1 WHERE  returnState=0 and admin_id=".$adminId);
        // ادمین به عنوان خالی
        if($personType->adminType!=4){
            DB::update("UPDATE CRM.dbo.crm_admin set emptyState=1 WHERE  id=".$adminId);
        }

        DB::update("update CRM.dbo.crm_adminUpDownBonus set isUsed=1 where adminId=$adminId");
        return Response::json(1);


    
        //ختم تخلیه بازاریابها

        
    }   
    // ======================

    public function getAdminEmptyState(Request $request)
    {
       $adminId=$request->get("adminId");
       $admin=DB::select("SELECT emptyState FROM CRM.dbo.crm_admin WHERE id=".$adminId);
       $addedCustomers=DB::select("SELECT Name,NameRec,PSN,PCode,CRM.dbo.getCustomerMantagheh(SnMantagheh) AS Mantagheh  FROM Shop.dbo.Peopels 
       JOIN Shop.dbo.MNM ON Peopels.SnMantagheh=SnMNM WHERE Peopels.PSN IN (SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE admin_id=".$adminId." AND crm_customer_added.returnState=0) 
        AND Peopels.CompanyNo=5 AND GroupCode IN ( ".implode(",",Session::get("groups")).")");
       return Response::json(['emptyState'=>$admin[0]->emptyState,'customers'=>$addedCustomers]);
    }

    public function moveCustomerToAdmin(Request $request)
    {
        $holderAdmin=$request->get("holderID");
        $giverAdmin=$request->get("giverID");
        $adminId=$giverAdmin;
        
        //  مشتریان مربوط به پشتیبان اختصاصی
        $customers=DB::select("SELECT customer_id,returnState,addedTime FROM CRM.dbo.crm_customer_added 
        WHERE  returnState=0 and crm_customer_added.admin_id=".$adminId);
        $countAllFactor=0;
        $sumAllFactor=1;
        $countAllReturnedFactor=0;
        $sumAllReturnedFactor=1;
        $seenCustomers=array();
        foreach ($customers as $customer) {
            $removedDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
            $addedDate=Jalalian::fromCarbon(Carbon::parse($customer->addedTime))->format('Y/m/d');
            $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=3 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."' GROUP BY    FactorHDS.SerialNoHDS");
            $returnedFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  FactType=4 and CustomerSn=".$customer->customer_id." and FactorHDS.FactDate>='".$addedDate."' and FactorHDS.FactDate<='".$removedDate."' GROUP BY    FactorHDS.SerialNoHDS");

            foreach ($factors as $factor) {
                if($factor->countFactors>0 and !in_array($customer->customer_id, $seenCustomers)){
                array_push($seenCustomers,$customer->customer_id);
                }
                $countAllFactor+=$factor->countFactors;
                $sumAllFactor+=$factor->sumFactors;
            }
            
            foreach ($returnedFactors as $factor) {
                $countAllReturnedFactor+=$factor->countFactors;
                $sumAllReturnedFactor+=$factor->sumFactors;
            }
        }
        $boughtPeopelsCount=count($seenCustomers);

        $info=DB::select("SELECT COUNT(customer_id)as countPeopels,crm_customer_added.admin_id FROM CRM.dbo.crm_customer_added 
            JOIN CRM.dbo.crm_admin ON crm_customer_added.admin_id=crm_admin.id 
            WHERE returnState=0 and crm_customer_added.admin_id=".$adminId." GROUP BY    admin_id");

        foreach ($info as $infor) {
            $infor->countFactor=$countAllFactor;
            $infor->totalMoneyHds=$sumAllFactor;
            $infor->boughtPeopelsCount=$boughtPeopelsCount;
            $infor->sumAllReturnedFactor=$sumAllReturnedFactor;
        }
        $exactInfo=$info[0];

//برای اطلاعات ماه قبل همین مشتریان
        $history=DB::table("CRM.dbo.crm_adminHistory")->where("adminId",$adminId)->get();
        $minDate=DB::table("CRM.dbo.crm_customer_added")->where("returnState",0)->where("admin_id",$adminId)->min("addedTime");
        $countAllFactor2=0;
        $sumAllFactor2=0;
        $countAllReturnedFactor2=0;
        $sumAllReturnedFactor2=0;
        $customers=DB::select("SELECT customer_id,addedTime FROM CRM.dbo.crm_customer_added WHERE  admin_id=".$adminId." and returnState=0");
        $endDate=Jalalian::fromCarbon(Carbon::parse($minDate))->format('Y/m/d');
        $startDate=Jalalian::fromCarbon(Carbon::parse($minDate)->subdays(30))->format('Y/m/d');

        foreach ($customers as $customer) {
            $factors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactType=3 and FactorHDS.FactDate>='".$startDate."' and FactorHDS.FactDate<='".$endDate."'  GROUP BY    FactorHDS.CustomerSn");
            foreach ($factors as $factor) {
                $countAllFactor2+=$factor->countFactors;
                $sumAllFactor2+=$factor->sumFactors;
            }

            $returnFactors=DB::select("SELECT COUNT(FactorHDS.SerialNoHDS) as countFactors,SUM(FactorHDS.TotalPriceHDS) as sumFactors FROM Shop.dbo.FactorHDS WHERE  CustomerSn=".$customer->customer_id." and FactType=4 and FactorHDS.FactDate>='".$startDate."' and FactorHDS.FactDate<='".$endDate."'  GROUP BY    FactorHDS.CustomerSn");
            foreach ($returnFactors as $factor) {
                $countAllReturnedFactor2+=$factor->countFactors;
                $sumAllReturnedFactor2+=$factor->sumFactors;
            }
        }
        //اطلاعات ماه قبل تاریخچه
        $lastHistoryInfo=DB::select("SELECT * FROM CRM.DBO.crm_adminHistory WHERE crm_adminHistory.timeStamp=(SELECT MAX(timeStamp) as MaxDate FROM CRM.dbo.crm_adminHistory WHERE adminId=".$adminId." GROUP BY    adminId)");
        
        $meanIncreas=0;
        
        if($sumAllFactor2!=0){
        $meanIncreas=($sumAllFactor-$sumAllFactor2)/$sumAllFactor2;
        }
       // if($meanIncreas<0){
       //     $meanIncreas=0;
       // }

        //اطلاعات مربوط به کامنت
        $comment=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->first();
		$adminComment="";
		if($comment){
		$adminComment=$comment->discription;
		}
        $countAllCommentedCustomers=DB::select("select COUNT(customerId) AS countComment from(
                                                select distinct customerId from CRM.dbo.crm_comment where adminId=$adminId and TimeStamp>=(select min(addedTime) from CRM.dbo.crm_customer_added where returnState=0 and admin_id=$adminId )
                                                and TimeStamp<=(select CURRENT_TIMESTAMP  
                                                )
                                                )a");
        $allActiveCustomerCount=$exactInfo->countPeopels;
        $countAllComments=0;
        if(count($countAllCommentedCustomers)>0){
            $countAllComments=$countAllCommentedCustomers[0]->countComment;
        }
        $nocommentedCustomers=$allActiveCustomerCount -$countAllComments;

        if($nocommentedCustomers<0){
            $nocommentedCustomers=0; 
        }

        $todayDate=Carbon::now()->format('Y-m-d');
        $countNoDoneWork=\DB::select("select count(countJob) as countJob from(
            select COUNT(id) as countJob,customer_id from (
            select * from (
            select crm_workList.commentId,crm_workList.id,crm_workList.doneState,crm_workList.specifiedDate,crm_comment.customerId from CRM.dbo.crm_workList 
            join CRM.dbo.crm_comment on crm_workList.commentId=crm_comment.id where doneState=0)a
            join (select customer_id,returnState,admin_id from CRM.dbo.crm_customer_added )c on a.customerId =c.customer_id where c.returnState=0 and admin_id=$adminId)b
            where specifiedDate<='".$todayDate."'
            group by customer_id)a");
         //برای محاسبه اطلاعات تاریخچه بازاریابها
        //نصب‌ها---1
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
        //امتیاز های اختصاصی
        $istallBonus=0;
        $aghlamBonus=0;
        $monyBonus=0;
        $countBuyBonus=0;
        //امتیاز اضافی فرد
        $extarBonus=0;
        //مجموع کل امتیازات
        $allBonuses=0;
        //معیارهای مخصوص
        $specialBases=DB::select("SELECT * FROM CRM.dbo.crm_specialBonus");
        //تارگیت‌ها
        $targets=DB::select("SELECT * FROM CRM.dbo.crm_targets");
        //اطلاعات ادمین در حال تخلیه
        $adminInfo=DB::select("SELECT * FROM CRM.dbo.crm_admin where id=$adminId");
        //امتیاز اختصاصی و اضافی کاربر
        $extarBonus=$adminInfo[0]->extraBonus; 
        if(!$extarBonus){
            $extarBonus=0;
        }
        


        $count_All_Install=DB::select("SELECT count(id) as countAllInstall from( SELECT * from CRM.dbo.crm_inserted_customers WHERE Convert(date,crm_inserted_customers.addedDate)>=Convert(date,'$minDate'))a where  adminId=$adminId");
        $count_All_Install=$count_All_Install[0]->countAllInstall;


        //2--تعداد اقلام
        $count_All_aghlamR=DB::select("SELECT count(countGoods) as countAghlam,adminId from (			
                                        SELECT count(SnGood) as countGoods,adminId,SnGood from (
                                        SELECT * FROM (SELECT MAX(TimeStamp)as maxTime,SnGood,CustomerSn from(
                                        SELECT * FROM(
                                            SELECT FactorBYS.TimeStamp,FactorBYS.Fi,FactorBYS.Amount,FactorBYS.SnGood,CustomerSn FROM Shop.dbo.FactorHDS
                                            JOIN Shop.dbo.FactorBYS on FactorHDS.SerialNoHDS=FactorBYS.SnFact)a where CONVERT(DATE,TimeStamp)>=Convert(date,'$minDate')
                                            )g group by SnGood,CustomerSn)c
                                            join (select * from CRM.dbo.crm_inserted_customers)d on c.CustomerSn=d.customerId)f group by adminId,SnGood
                                            )e where e.adminId=$adminId group by adminId");
        if(count($count_All_aghlamR)>0){
            $count_All_aghlam=$count_All_aghlamR[0]->countAghlam;
        }

        //تعداد خرید اولیه
        $count_All_New_buys=DB::select("SELECT count(CustomerSn) as countNewFactor,adminId from (
            SELECT CustomerSn from (SELECT * from Shop.dbo.FactorHds where FactType=3 AND CONVERT(DATE,TimeStamp)>=Convert(date,'$minDate'))b group by CustomerSn having  COUNT(SerialNoHDS)>1 
            )c join CRM.dbo.crm_inserted_customers on c.CustomerSn=crm_inserted_customers.customerId where adminId=$adminId  group by adminId");

        if(count($count_All_New_buys)>0){
            $count_All_New_buys=$count_All_New_buys[0]->countNewFactor;
        }else{
            $count_All_New_buys=0;
        }
        //ارزیابی معیارهای اختصاصی
        foreach($specialBases as $special){
            if($special->id==11){//نصب امتیاز
                $istallBonus=((int)($count_All_Install/$special->limitAmount)) * $special->Bonus;
            }            
            if($special->id==12){//اقلام امتیاز
                $aghlamBonus=((int)($count_All_aghlam/$special->limitAmount)) * $special->Bonus;
            }
            if($special->id==13){//مبلغ امتیاز
                $countBuyBonus=((int)($count_All_New_buys/$special->limitAmount)) * $special->Bonus;
            }
            if($special->id==14){//خرید اولیه امتیاز
                $monyBonus=((int)($sumAllFactor/10/$special->limitAmount)) * $special->Bonus;
            }            

        }
        //ارزیابی تارگت‌ها
        foreach($targets as $target){
                //تارگت‌های نصب
            if($target->id==5){
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
            if($target->id==7){
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
                //تارگت‌های مبلغ خرید
            if($target->id==8){
                if($sumAllFactor >= $target->thirdTarget){
                    $monyComTg="تارگیت سوم";
                    $monyComTgBonus=$target->thirdTargetBonus;
                }else{
                    if($sumAllFactor >= $target->secondTarget){
                        $monyComTg="تارگیت دوم";
                        $monyComTgBonus=$target->thirdTargetBonus;
                    }else{
                        if($sumAllFactor >= $target->firstTarget){
                            $monyComTg="تارگیت اول";
                            $monyComTgBonus=$target->thirdTargetBonus;
                        }
                    }
                }
            }
                //تارگت‌های اقلام خرید
            if($target->id==9){
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
                            $aghlamComTgBonus=$target->thirdTargetBonus;
                        }
                    }
                }
            }
        }
        $allBonuses=$extarBonus+$istallBonus+$aghlamBonus+$monyBonus+$countBuyBonus+$istallComTgBonus+$aghlamComTgBonus+$monyComTgBonus+$countBuyComTgBonus;
            //ختم تارگت‌ها
        
        //ختم کد‌های بازاریابها
        //ثبت تاریخچه
        DB::table("CRM.dbo.crm_adminHistory")->insert(['adminId'=>$adminId,'countPeople'=>$exactInfo->countPeopels,'countFactor'=>$countAllFactor,'countBuyPeople'=>$boughtPeopelsCount,'factorAllMoney'=>$sumAllFactor
        ,'lastMonthAllMoney'=>$sumAllFactor2,'lastMonthReturnedAllMoney'=>$sumAllReturnedFactor,'meanIncrease'=>$meanIncreas,'comment'=>"".$adminComment."",'noCommentCust'=>$nocommentedCustomers,'noDoneWork'=>$countNoDoneWork[0]->countJob,
        'countReturnedFactor'=>$countAllReturnedFactor,'countLastMonthFactor'=>$countAllFactor2,'countLastMonthReturnedFactor'=>$countAllReturnedFactor2
        ,'allInstall'=>$count_All_Install
        ,'allPrimaryBuy'=>$count_All_New_buys
        ,'allAghlam'=>$count_All_aghlam
        ,'lastComInstallTg'=>"".$istallComTg.""
        ,'lastComPrimBuyTg'=>"".$aghlamComTg.""
        ,'lastComAghlamTg'=>"".$monyComTg.""
        ,'lastComMoneyTg'=>"".$countBuyComTg.""
        ,'installBonus'=> $istallBonus
        ,'primeBuyBonus'=> $countBuyBonus
        ,'totalMoneyBonus'=>$monyBonus
        ,'totalAghlamBonus'=>$aghlamBonus
        ,'lastComInstallTgB'=>$istallComTgBonus
        ,'lastComPrimBuyTgB'=>$aghlamComTgBonus
        ,'lastComAghlamTgB'=>$monyComTgBonus
        ,'lastComMoneyTgB'=>$countBuyComTgBonus
        ,'extraBonus'=>$extarBonus
        ,'allBonus'=>$allBonuses]);
        
        $customersToMove=DB::select("SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE admin_id=".$giverAdmin." and returnState!=1");
        
        foreach ($customersToMove as $customer) {
            DB::table("CRM.dbo.crm_customer_added")->insert(['admin_id'=>$holderAdmin,'customer_id'=>$customer->customer_id,'returnState'=>0]);
        }
        
        DB::table("CRM.dbo.crm_customer_added")->where('admin_id',$giverAdmin)->update(['returnState'=>1,'removedTime'=>Carbon::now()]);
        DB::table("CRM.dbo.crm_admin")->where('id',$giverAdmin)->update(['emptyState'=>1]);
        DB::table("CRM.dbo.crm_admin")->where('id',$holderAdmin)->update(['emptyState'=>0]);
        return Response::json(1);
    }
        

        
    // ======================

    public function editAdmintListStuff(Request $request)
    {
        $name=$request->post("name");
        $userName=$request->post("userName");
        $lastName=$request->post("lastName");
        $password=$request->post("password");
        $employeeType=$request->post("employeeType");
        $poshtibanType=$request->post("poshtibanType");
        $adminType=$request->post("adminType");
        if($employeeType==1){
            $adminType=6;
        }
        if($employeeType==2){
            $adminType=7;
        }
        if($employeeType==3){
            $adminType=$poshtibanType;
        }
        $phone=$request->post("phone");
        $address=$request->post("address");
        $sex=$request->post("sex");
        $discription=$request->post("discription");
        $adminId=$request->post('adminId');
        $bossId=0;
        $manager=$request->post("manager");
        $head=$request->post("head");
        $saleLine=$request->post("saleLine");

        // اگر اطلاعات پایه آن بود 
        $baseInfoED = $request->post("baseInfoED");
        $baseInfoProfileED;
        $infoRdED;
        $specialSettingED;
        $baseInfoSettingED;
        $InfoSettingAccessED;
        $InfoSettingTargetED;
        $rdSentED;
        $rdNotSentED;
        //==========================
        if($baseInfoED=="on"){
            $baseInfoED = 1;
                    // پروفایل با سه تا عنصر اش چک میشوند
                    $baseInfoProfileED = $request->post("baseInfoProfileED");
                    $deleteProfileED = $request->post("deleteProfileED");
                    $editProfileED = $request->post("editProfileED");
                    $seeProfileED = $request->post("seeProfileED");

                if($baseInfoProfileED=="on"){
                    if($deleteProfileED=="on"){
                        $baseInfoProfileED=2;
                    }elseif($editProfileED=="on" and $deleteProfileED!="on"){
                        $baseInfoProfileED=1;
                    }elseif($editProfileED !="on" and $seeProfileED =="on"){
                        $baseInfoProfileED=0;
                    }else{
                        $baseInfoProfileED=-1;
                    }
                }else{
                    $baseInfoProfileED=-1;
                }

            //  اگر آر و دی آن بود وارد شده و وارد نشده چک گردد
            $infoRdED = $request->post("infoRdED");

                if($infoRdED=="on"){
                    $infoRdED = 1;
                    // وارد شده ها چک میگردد
                    $rdSentED = $request->post("rdSentED");

                    $deleteSentRdED = $request->post("deleteSentRdED");
                    $editSentRdED = $request->post("editSentRdED");
                    $seeSentRdED = $request->post("seeSentRdED");

                            
                    if($rdSentED=="on"){
                        $rdSentED=1;
                        if($deleteSentRdED=="on"){
                            $rdSentED=2;
                        }elseif($editSentRdED=="on" and $deleteSentRdED!="on"){
                            $rdSentED=1;
                        }elseif($editSentRdED!="on" and $seeSentRdED=="on"){
                            $rdSentED=0;
                        }else{
                            $rdSentED=-1;
                        }
                    }else{
                        $rdSentED=-1;
                    }

                    // وراد نشده ها چک میگردد
                    $rdNotSentED = $request->post("rdNotSentED");

                    $deleteRdNotSentED = $request->post("deleteRdNotSentED");
                    $editRdNotSentED = $request->post("editRdNotSentED");
                    $seeRdNotSentED = $request->post("seeRdNotSentED");

                    if($rdNotSentED=="on"){
                        $rdNotSentED=1;
                        if($deleteRdNotSentED=="on"){
                            $rdNotSentED=2;
                        }elseif($editRdNotSentED=="on" and $deleteRdNotSentED!="on"){
                            $rdNotSentED=1;
                        }elseif($editRdNotSentED!="on" and $seeRdNotSentED=="on"){
                            $rdNotSentED=0;
                        }else{
                            $rdNotSentED=-1;
                        }
                    }else{
                        $rdNotSentED=-1;
                    }

                }else{
                    $infoRdED=-1;
                    $rdSentED=-1;
                    $rdNotSentED=-1;
                }
                
                // افزودن خط فروش چک میگردد
                $specialSettingED = $request->post("specialSettingED");
                $deleteSaleLineED = $request->post("deleteSaleLineED");
                $editSaleLineED = $request->post("editSaleLineED");
                $seeSaleLineED = $request->post("seeSaleLineED");

                if($specialSettingED=="on"){
                    $specialSettingED=1;
                    if($deleteSaleLineED=="on"){
                        $specialSettingED=2;
                    }elseif($editSaleLineED=="on" and $deleteSaleLineED!="on"){
                        $specialSettingED=1;
                    }elseif($editSaleLineED!="on" and  $seeSaleLineED=="on"){
                        $specialSettingED=0;
                    }else{
                        $specialSettingED=-1;
                    }
                }else{
                    $specialSettingED=-1;
                }
            
            // اگر تنظیمات آن بود 
            $baseInfoSettingED = $request->post("baseInfoSettingED");
            if($baseInfoSettingED=="on"){
                $baseInfoSettingED=1;
                // سطح دسترسی با عناصر اش چک میگردد
                $InfoSettingAccessED = $request->post("InfoSettingAccessED");

                $deleteSettingAccessED = $request->post("deleteSettingAccessED");
                $editSettingAccessED = $request->post("editSettingAccessED");
                $seeSettingAccessED = $request->post("seeSettingAccessED");
                if($InfoSettingAccessED=="on"){
                    $InfoSettingAccessED=1;
                    if($deleteSettingAccessED=="on"){
                        $InfoSettingAccessED=2;
                    }elseif($editSettingAccessED=="on" and $deleteSettingAccessED!="on"){
                        $InfoSettingAccessED=1;
                    }elseif($editSettingAccessED!="on" and $seeSettingAccessED=="on"){
                        $InfoSettingAccessED=0;
                    }else{
                        $InfoSettingAccessED=-1;
                    }
                }else{
                    $InfoSettingAccessOpED=-1;
                    $InfoSettingAccessED=-1;
                }

                // تارگیت ها و امتیازات چک میگردد
                $InfoSettingTargetED = $request->post("InfoSettingTargetED");

                $deleteSettingTargetED = $request->post("deleteSettingTargetED");
                $editSettingTargetED = $request->post("editSettingTargetED");
                $seeSettingTargetED = $request->post("seeSettingTargetED");
                if($InfoSettingTargetED=="on"){
                    $InfoSettingTargetED=1;
                    if($deleteSettingTargetED=="on"){
                        $InfoSettingTargetED=2;
                    }elseif($editSettingTargetED=="on" and $deleteSettingTargetED!="on"){
                        $InfoSettingTargetED=1;
                    }elseif($editSettingTargetED!="on" and $seeSettingTargetED=="on"){
                        $InfoSettingTargetED=0;
                    }else{
                        $InfoSettingTargetED=-1;
                    }
                }else{
                    $InfoSettingTargetED=-1;
                }


            }else {
                $InfoSettingAccessED=-1;
                $InfoSettingTargetED=-1;
                $baseInfoSettingED=-1;
            }

        }else{
            $InfoSettingAccessOpED=-1;
            $InfoSettingAccessED=-1;
            $InfoSettingTargetOpED=-1;
            $baseInfoSettingED=-1;
            //
            $baseInfoProfileED = -1;
            $infoRdED=-1;
            $rdSentED=-1;
            $rdNotSentED=-1;
            //
            $specialSettingED = -1;
            $baseInfoED=-1;
            $InfoSettingAccessED=-1;
            $InfoSettingTargetED=-1;
        }

    //   چک کردن تعریف عناصر با سه عناصر اش
    //تعریف عناصر===============
    $declareElementOppED;
    // ==========================
        $declareElementED = $request->post("declareElementED");
        if($declareElementED=="on"){
            $declareElementOppED=1;
            $deletedeclareElementED = $request->post("deletedeclareElementED");
            $editdeclareElementED = $request->post("editdeclareElementED");
            $seedeclareElementED = $request->post("seedeclareElementED");

            if($deletedeclareElementED=="on"){
            $declareElementOppED=2;
            }elseif($editdeclareElementED=="on" and $deletedeclareElementED!="on"){
            $declareElementOppED=1;
            }elseif($editdeclareElementED!="on" and $seedeclareElementED=="on"){
                $declareElementOppED=0;
            }else{
                $declareElementOppED=-1;
            }
        }else{
            $declareElementOppED=-1;
        }

        // اگر عملیات آن بود 
        $oppED = $request->post("oppED");
        $oppManagerED;
        $oppHeadED;
        $oppBazaryabED;
        $oppTakhsisED;
        $oppDriverServiceED;
        $oppBargiriED;
        $oppDriverED;
        $todayoppNazarsanjiED;
        $pastoppNazarsanjiED;
        $DoneoppNazarsanjiED;
        $oppNazarSanjiED;
        $AddOppupDownBonusED;
        $SubOppupDownBonusED;
        $OppupDownBonusED;
        $AddedoppRDED;
        $NotAddedoppRDED;
        $oppRDED;
        $oppjustCalendarED;
        $oppCustCalendarED;
        $oppCalendarED;
        $allalarmoppED;
        $donealarmoppED;
        $NoalarmoppED;
        $alarmoppED;
        $massageOppED;
        $justBargiriOppED;

        if($oppED=="on"){
            $oppED = 1;
            //
            // و اگر تخصسص به کاربر آن بود
            $oppTakhsisED = $request->post("oppTakhsisED");
            if($oppTakhsisED=="on"){
                $oppTakhsisED=1;
                // مدیران چک گردد
                $oppManagerED = $request->post("oppManagerED");
                $deleteManagerOppED = $request->post("deleteManagerOppED");
                $editManagerOppED = $request->post("editManagerOppED");
                $seeManagerOppED = $request->post("seeManagerOppED");
                if($oppManagerED=="on"){
                    $oppManagerED = 1;
                    if($deleteManagerOppED=="on"){
                    $oppManagerED=2;
                    }elseif($editManagerOppED=="on" and $deleteManagerOppED!="on"){
                    $oppManagerED=1;
                    }elseif($editManagerOppED!="on" and $seeManagerOppED=="on"){
                        $oppManagerED=0;
                    }else{
                        $oppManagerED=-1;
                    }
                }else{
                    $oppManagerED=-1;
                }
                // سرپرستان چک گردد
                $oppHeadED = $request->post("oppHeadED");
                $deleteHeadOppED = $request->post("deleteHeadOppED");
                $editHeadOppED = $request->post("editHeadOppED");
                $seeHeadOppED = $request->post("seeHeadOppED");

                if($oppHeadED == "on"){
                    $oppHeadED = 1;
                    if($deleteHeadOppED=="on"){
                    $oppHeadED=2;
                    }elseif($editHeadOppED=="on" and $deleteHeadOppED!="on"){
                    $oppHeadED=1;
                    }elseif($editHeadOppED!="on" and $seeHeadOppED=="on"){
                        $oppHeadED=0;
                    }else{
                        $oppHeadED=-1;
                    }
                }else{
                    $oppHeadED=-1; 
                }

                // بازاریابها چک گردد
                $oppBazaryabED = $request->post("oppBazaryabED");

                $deleteBazaryabOppED = $request->post("deleteBazaryabOppED");
                $editBazaryabOppED = $request->post("editBazaryabOppED");
                $seeBazaryabOppED = $request->post("seeBazaryabOppED");

                if($oppBazaryabED=="on"){
                    $oppBazaryabED = 1;
                    if($deleteBazaryabOppED=="on"){
                    $oppBazaryabED=2;
                    }elseif($editBazaryabOppED=="on" and $deleteBazaryabOppED!="on"){
                    $oppBazaryabED=1;
                    }elseif($editBazaryabOppED!="on" and $seeBazaryabOppED=="on"){
                        $oppBazaryabED=0;
                    }else{
                        $oppBazaryabED=-1;
                    }
                }else{
                    $oppBazaryabED=-1;
                }

            }else{
                $oppBazaryabED=-1;
                $oppHeadED=-1; 
                $oppManagerED=-1;
                $oppTakhsisED=-1;
            }

            //   راننده ها چگ میگردد
            $oppDriverED = $request->post("oppDriverED");
            if($oppDriverED=="on"){
                $oppDriverED=1;
                // سرویس راننده ها چک میگردد
                $oppDriverServiceED = $request->post("oppDriverServiceED");

                $deleteoppDriverServiceED = $request->post("deleteoppDriverServiceED");
                $editoppDriverServiceED = $request->post("editoppDriverServiceED");
                $seeoppDriverServiceED = $request->post("seeoppDriverServiceED");

                if($oppDriverServiceED=="on"){
                    $oppDriverServiceED = 1;
                    if($deleteoppDriverServiceED=="on"){
                        $oppDriverServiceED=2;
                    }elseif($editoppDriverServiceED=="on" and $deleteoppDriverServiceED!="on"){
                        $oppDriverServiceED=1;
                    }elseif($editoppDriverServiceED!="on" and $seeoppDriverServiceED=="on"){
                        $oppDriverServiceED=0;
                    }else{
                        $oppDriverServiceED=-1;
                    }
                }else{
                    $oppDriverServiceED=-1;
                }

                // بارگیری چک می گردد
                $oppBargiriED = $request->post("oppBargiriED");

                $deleteoppBargiriED = $request->post("deleteoppBargiriED");
                $editoppBargiriED = $request->post("editoppBargiriED");
                $seeoppBargiriED = $request->post("seeoppBargiriED");

                if($oppBargiriED=="on"){
                    $oppBargiriED = 1;
                    if($deleteoppBargiriED=="on"){
                    $oppBargiriED=2;
                    }elseif($editoppBargiriED=="on" and $deleteoppBargiriED!="on"){
                    $oppBargiriED=1;
                    }elseif($editoppBargiriED!="on" and $seeoppBargiriED=="on"){
                        $oppBargiriED=0;
                    }else{
                        $oppBargiriED=-1;
                    }
                }else{
                    $oppBargiriED=-1;
                }
            }else{
                $oppBargiriED=-1;
                $oppDriverServiceED=-1;
                $oppDriverED=-1;
            }

            // اگر نظر سنجی آن بود 
            $oppNazarSanjiED = $request->post("oppNazarSanjiED");
            if($oppNazarSanjiED=="on"){
                $oppNazarSanjiED=1;
                // نظرات امروز چک گردد
                $todayoppNazarsanjiED = $request->post("todayoppNazarsanjiED");

                $deletetodayoppNazarsanjiED = $request->post("deletetodayoppNazarsanjiED");
                $edittodayoppNazarsanjiED = $request->post("edittodayoppNazarsanjiED");
                $seetodayoppNazarsanjiED = $request->post("seetodayoppNazarsanjiED");
                if($todayoppNazarsanjiED=="on"){
                    $todayoppNazarsanjiED = 1;
                    if($deletetodayoppNazarsanjiED=="on"){
                        $todayoppNazarsanjiED=2;
                    }elseif($edittodayoppNazarsanjiED=="on" and $deletetodayoppNazarsanjiED!="on"){
                        $todayoppNazarsanjiED=1;
                    }elseif($edittodayoppNazarsanjiED!="on" and $seetodayoppNazarsanjiED=="on"){
                        $todayoppNazarsanjiED=0;
                    }else{
                        $todayoppNazarsanjiED=-1;
                    }
                }else{
                    $todayoppNazarsanjiED=-1;
                }

                // نظرات گذشته چک گردد
                $pastoppNazarsanjiED = $request->post("pastoppNazarsanjiED");

                $deletepastoppNazarsanjiED = $request->post("deletepastoppNazarsanjiED");
                $editpastoppNazarsanjiED = $request->post("editpastoppNazarsanjiED");
                $seepastoppNazarsanjiED = $request->post("seepastoppNazarsanjiED");

                if($pastoppNazarsanjiED=="on"){
                    $pastoppNazarsanjiED = 1;
                    if($deletepastoppNazarsanjiED=="on"){
                    $pastoppNazarsanjiED=2;
                    }elseif($editpastoppNazarsanjiED=="on" and $deletepastoppNazarsanjiED!="on"){
                    $pastoppNazarsanjiED=1;
                    }elseif($editpastoppNazarsanjiED!="on" and $seepastoppNazarsanjiED=="on"){
                        $pastoppNazarsanjiED=0;
                    }else{
                        $pastoppNazarsanjiED=-1;
                    }
                }else{
                    $pastoppNazarsanjiED=-1;
                }
            
                // نظرات انجام شده چک میگردد
                $DoneoppNazarsanjiED = $request->post("DoneoppNazarsanjiED");

                $deleteDoneoppNazarsanjiED = $request->post("deleteDoneoppNazarsanjiED");
                $editDoneoppNazarsanjiED = $request->post("editDoneoppNazarsanjiED");
                $seeDoneoppNazarsanjiED = $request->post("seeDoneoppNazarsanjiED");
                if($DoneoppNazarsanjiED=="on"){
                    $DoneoppNazarsanjiED = 1;
                    if($deleteDoneoppNazarsanjiED=="on"){
                    $DoneoppNazarsanjiED=2;
                    }elseif($editDoneoppNazarsanjiED=="on" and $deleteDoneoppNazarsanjiED!="on"){
                    $DoneoppNazarsanjiED=1;
                    }elseif($editDoneoppNazarsanjiED!="on" and $seeDoneoppNazarsanjiED=="on"){
                        $DoneoppNazarsanjiED=0;
                    }else{
                        $DoneoppNazarsanjiED=-1;
                    }
                }else{
                    $DoneoppNazarsanjiED=-1;
                }
            }else{
                $DoneoppNazarsanjiED=-1;
                $pastoppNazarsanjiED=-1;
                $todayoppNazarsanjiED=-1;
                $oppNazarSanjiED=-1;
            }


            // اگر افزایش و کا هش آن بود
            $OppupDownBonusED = $request->post("OppupDownBonusED");
            if($OppupDownBonusED=="on"){
                $OppupDownBonusED=1;
                // امتیاز های اضافه شده چک گردد
                $AddOppupDownBonusED = $request->post("AddOppupDownBonusED");

                $deleteAddOppupDownBonusED = $request->post("deleteAddOppupDownBonusED");
                $editAddOppupDownBonusED = $request->post("editAddOppupDownBonusED");
                $seeAddOppupDownBonusED = $request->post("seeAddOppupDownBonusED");

                if($AddOppupDownBonusED=="on"){
                    $AddOppupDownBonusED = 1;
                    if($deleteAddOppupDownBonusED=="on"){
                    $AddOppupDownBonusED=2;
                    }elseif($editAddOppupDownBonusED=="on" and $deleteAddOppupDownBonusED!="on"){
                    $AddOppupDownBonusED=1;
                    }elseif($editAddOppupDownBonusED!="on" and $seeAddOppupDownBonusED=="on"){
                        $AddOppupDownBonusED=0;
                    }else{
                        $AddOppupDownBonusED=-1;
                    }
                }else{
                    $AddOppupDownBonusED=-1;
                }

                // امتیاز های کاهش یافته چک گردد
                $SubOppupDownBonusED = $request->post("SubOppupDownBonusED");

                $deleteSubOppupDownBonusED = $request->post("deleteSubOppupDownBonusED");
                $editSubOppupDownBonusED = $request->post("editSubOppupDownBonusED");
                $seeSubOppupDownBonusED = $request->post("seeSubOppupDownBonusED");

                if($SubOppupDownBonusED=="on"){
                        $SubOppupDownBonusED = 1;
                    if($deleteSubOppupDownBonusED=="on"){
                        $SubOppupDownBonusED=2;
                    }elseif($editSubOppupDownBonusED=="on" and $deleteSubOppupDownBonusED!="on"){
                        $SubOppupDownBonusED=1;
                    }elseif($editSubOppupDownBonusED!="on" and $seeSubOppupDownBonusED=="on"){
                        $SubOppupDownBonusED=0;
                    }else{
                        $SubOppupDownBonusED=-1;
                    }
                }else{
                    $SubOppupDownBonusED=-1;
                }
            }else{
                $SubOppupDownBonusED=-1;
                $AddOppupDownBonusED=-1;
                $OppupDownBonusED=-1;
            }

                // اگر آر و دی آن بود 
            $oppRDED = $request->post("oppRDED");
            if($oppRDED =="on"){
                $oppRDED=1;
                // وارد شده ها چک میگردد
                $AddedoppRDED = $request->post("AddedoppRDED");

                $deleteAddedoppRDED = $request->post("deleteAddedoppRDED");
                $editAddedoppRDED = $request->post("editAddedoppRDED");
                $seeAddedoppRDED = $request->post("seeAddedoppRDED");

                if($AddedoppRDED=="on"){
                    $AddedoppRDED = 1;
                    if($deleteAddedoppRDED=="on"){
                        $AddedoppRDED=2;
                    }elseif($editAddedoppRDED=="on" and $deleteAddedoppRDED!="on"){
                        $AddedoppRDED=1;
                    }elseif($editAddedoppRDED!="on" and $seeAddedoppRDED=="on"){
                        $AddedoppRDED=0;
                    }else{
                        $AddedoppRDED=-1;
                    }
                }else{
                    $AddedoppRDED=-1;
                }

                // وارد نشده ها چک میگردد
                $NotAddedoppRDED = $request->post("NotAddedoppRDED");

                $deleteNotAddedoppRDED = $request->post("deleteNotAddedoppRDED");
                $editNotAddedoppRDED = $request->post("editNotAddedoppRDED");
                $seeNotAddedoppRDED = $request->post("seeNotAddedoppRDED");
                if($NotAddedoppRDED=="on"){
                    $NotAddedoppRDED = 1;
                    if($deleteNotAddedoppRDED=="on"){
                    $NotAddedoppRDED=2;
                    }elseif($editNotAddedoppRDED=="on" and $deleteNotAddedoppRDED!="on"){
                    $NotAddedoppRDED=1;
                    }elseif($editNotAddedoppRDED!="on" and $seeNotAddedoppRDED=="on"){
                        $NotAddedoppRDED=0;
                    }else{
                        $NotAddedoppRDED=-1;
                    }
                }else{
                    $NotAddedoppRDED=-1;
                }
            }else{
                $AddedoppRDED = -1;
                $NotAddedoppRDED = -1;
                $oppRDED=-1;
            }

                // اگر تقویم روزانه آن بود 
            $oppCalendarED = $request->post("oppCalendarED");
            if($oppCalendarED=="on"){
                $oppCalendarED=1;
                // تقویم روزانه چک می گردد
                $oppjustCalendarED = $request->post("oppjustCalendarED");

                $deleteoppjustCalendarED = $request->post("deleteoppjustCalendarED");
                $editoppjustCalendarED = $request->post("editoppjustCalendarED");
                $seeoppjustCalendarED = $request->post("seeoppjustCalendarED");

                if($oppjustCalendarED=="on"){
                    $oppjustCalendarED = 1;
                    if($deleteoppjustCalendarED=="on"){
                    $oppjustCalendarED=2;
                    }elseif($editoppjustCalendarED=="on" and $deleteoppjustCalendarED!="on"){
                    $oppjustCalendarED=1;
                    }elseif($editoppjustCalendarED!="on" and $seeoppjustCalendarED=="on"){
                        $oppjustCalendarED=0;
                    }else{
                        $oppjustCalendarED=-1;
                    }
                }else{
                    $oppjustCalendarED=-1;
                    $oppCalendarED=1;
                }

                    //لیست مشتریان چک گردد
                $oppCustCalendarED = $request->post("oppCustCalendarED");

                $deleteoppCustCalendarED = $request->post("deleteoppCustCalendarED");
                $editoppCustCalendarED = $request->post("editoppCustCalendarED");
                $seeoppCustCalendarED = $request->post("seeoppCustCalendarED");

                if($oppCustCalendarED=="on"){
                    $oppCustCalendarED = 1;
                    if($deleteoppCustCalendarED=="on"){
                    $oppCustCalendarED=2;
                    }elseif($editoppCustCalendarED=="on" and $deleteoppCustCalendarED!="on"){
                    $oppCustCalendarED=1;
                    }elseif($editoppCustCalendarED!="on" and $seeoppCustCalendarED=="on"){
                        $oppCustCalendarED=0;
                    }else{
                        $oppCustCalendarED=-1;
                    }
                }else{
                    $oppCustCalendarED=-1;
                }

            }else {
                $oppjustCalendarED = -1;
                $oppCustCalendarED = -1;
                $oppCalendarED=-1;
            }


                // اگر آلارمها آن بود 
            $alarmoppED = $request->post("alarmoppED");
            if($alarmoppED=="on"){
                $alarmoppED = 1;
                // آلارمها چک گردد
                $allalarmoppED = $request->post("allalarmoppED");

                $deleteallalarmoppED = $request->post("deleteallalarmoppED");
                $editallalarmoppED = $request->post("editallalarmoppED");
                $seeallalarmoppED = $request->post("seeallalarmoppED");

                if($allalarmoppED=="on"){
                    $allalarmoppED = 1;
                    if($deleteallalarmoppED=="on"){
                        $allalarmoppED=2;
                    }elseif($editallalarmoppED=="on" and $deleteallalarmoppED!="on"){
                        $allalarmoppED=1;
                    }elseif($editallalarmoppED!="on" and $seeallalarmoppED=="on"){
                        $allalarmoppED=0;
                    }else{
                        $allalarmoppED=-1;
                    }
                }else{
                    $allalarmoppED=-1;
                }

                // آلارمهای انجام شده چک میگردد
                $donealarmoppED = $request->post("donealarmoppED");

                $deletedonealarmoppED = $request->post("deletedonealarmoppED");
                $editdonealarmoppED = $request->post("editdonealarmoppED");
                $seedonealarmoppED = $request->post("seedonealarmoppED");

                if($donealarmoppED=="on"){
                    $donealarmoppED = 1;
                    if($deletedonealarmoppED=="on"){
                    $donealarmoppED=2;
                    }elseif($editdonealarmoppED=="on" and $deletedonealarmoppED!="on"){
                    $donealarmoppED=1;
                    }elseif($editdonealarmoppED!="on" and $seedonealarmoppED=="on"){
                        $donealarmoppED=0;
                    }else{
                        $donealarmoppED=-1;
                    }
                }else{
                    $donealarmoppED=-1;
                }

                // مشتریان فاقد آلارم چک میگردد
                $NoalarmoppED = $request->post("NoalarmoppED");

                $deleteNoalarmoppED = $request->post("deleteNoalarmoppED");
                $editNoalarmoppED = $request->post("editNoalarmoppED");
                $seeNoalarmoppED = $request->post("seeNoalarmoppED");

                if($NoalarmoppED=="on"){
                        $NoalarmoppED = 1;
                    if($deleteNoalarmoppED=="on"){
                    $NoalarmoppED=2;
                    }elseif($editNoalarmoppED=="on" and $deleteNoalarmoppED!="on"){
                    $NoalarmoppED=1;
                    }elseif($editNoalarmoppED!="on" and $seeNoalarmoppED=="on"){
                        $NoalarmoppED=0;
                    }else{
                        $NoalarmoppED=-1;
                    }
                }else{
                    $NoalarmoppED=-1;
                }

            }else {
                $allalarmoppED = -1;
                $donealarmoppED = -1;
                $NoalarmoppED = -1;
                $alarmoppED=-1;
            }

            // پیامها چک میگردد
            $massageTopOppED = $request->post("massageOppED");

            $deletemassageOppED = $request->post("deletemassageOppED");
            $editmassageOppED = $request->post("editmassageOppED");
            $seemassageOppED = $request->post("seemassageOppED");
            if($massageTopOppED=="on"){
                $massageTopOppED = 1;
                $massageOppED= 0;
                if($deletemassageOppED=="on"){
                    $massageOppED=2;
                }elseif($editmassageOppED=="on" and $deletemassageOppED!="on"){
                    $massageOppED=1;
                }elseif($editmassageOppED!="on" and $seemassageOppED=="on"){
                    $massageOppED=0;
                }else{
                    $massageOppED=-1;
                }
            }else{
                $massageOppED=-1;
                $massageTopOppED=-1;
            }

            // بارگیری چک میگردد
            $justBargiriTopOppED = $request->post("justBargiriOppED");
            if($justBargiriTopOppED=="on"){
                $justBargiriTopOppED = 1;
                $deletejustBargiriOppED = $request->post("deletejustBargiriOppED");
                $editjustBargiriOppED = $request->post("editjustBargiriOppED");
                $seejustBargiriOppED = $request->post("seejustBargiriOppED");

                $justBargiriOppED= 0;
                if($deletejustBargiriOppED=="on"){
                $justBargiriOppED=2;
                }elseif($editjustBargiriOppED=="on" and $deletejustBargiriOppED!="on"){
                $justBargiriOppED=1;
                }elseif($editjustBargiriOppED!="on" and $seejustBargiriOppED=="on"){
                    $justBargiriOppED=0;
                }else{
                    $justBargiriOppED=-1;
                }
            }else{
                $justBargiriOppED=-1;
                $justBargiriTopOppED=-1;
            }

        }else{
            $oppManagerED=-1;
            $oppHeadED=-1;
            $oppBazaryabED=-1;
            $oppTakhsisED=-1;
            $oppDriverServiceED=-1;
            $oppBargiriED=-1;
            $oppDriverED=-1;
            $todayoppNazarsanjiED=-1;
            $pastoppNazarsanjiED=-1;
            $DoneoppNazarsanjiED=-1;
            $oppNazarSanjiED=-1;
            $AddOppupDownBonusED=-1;
            $SubOppupDownBonusED=-1;
            $OppupDownBonusED=-1;
            $AddedoppRDED=-1;
            $NotAddedoppRDED=-1;
            $oppRDED=-1;
            $oppjustCalendarED=-1;
            $oppCustCalendarED=-1;
            $oppCalendarED=-1;
            $allalarmoppED=-1;
            $donealarmoppED=-1;
            $NoalarmoppED=-1;
            $alarmoppED=-1;
            $massageOppED=-1;
            $justBargiriOppED=-1;
            $oppED=-1;

        }

        
        // اگر گزارشات آن بود 
        $reportED = $request->post("reportED");
        $amalKardreportED;
        $managerreportED;
        $HeadreportED;
        $poshtibanreportED;
        $bazaryabreportED;
        $reportDriverED;
        $amalkardCustReportED;
        $loginCustRepED;
        $inActiveCustRepED;
        $noAdminCustRepED;
        $returnedCustRepED;
        $trazEmployeeReportED;
        $nosalegoodsReportED;
        $NoExistgoodsReportED;
        $returnedgoodsReportED;
        $salegoodsReportED;
        $goodsReportED;
        $returnedEDTasReportgoodsReportED;
        $tasgoodsReprtED;
        $returnedReportgoodsReportED;
        $goodsbargiriReportED;
        $returnedNTasReportgoodsReportED;
        if($reportED =="on"){
            $reportED=1;
            // اگر عملکرد کاربران آن بود 
            $amalKardreportED = $request->post("amalKardreportED");
            if($amalKardreportED=="on"){
                $amalKardreportED=1;
                // مدیران با سه تا عناصر اش چک میگردد
                $managerreportED = $request->post("managerreportED");

                $deletemanagerreportED = $request->post("deletemanagerreportED");
                $editmanagerreportED = $request->post("editmanagerreportED");
                $seemanagerreportED = $request->post("seemanagerreportED");

                if($managerreportED=="on"){
                    if($deletemanagerreportED=="on"){
                        $managerreportED=2;
                    }elseif($editmanagerreportED=="on" and $deletemanagerreportED!="on"){
                        $managerreportED=1;
                    }elseif($editmanagerreportED!="on" and $seemanagerreportED=="on"){
                        $managerreportED=0;
                    }else{
                        $managerreportED=-1;
                    }
                }else{
                    $managerreportED=-1;
                }

                // سرپرستان با سه تا عناصر اش چک میگردد
                    $HeadreportED = $request->post("HeadreportED");

                $deleteHeadreportED = $request->post("deleteHeadreportED");
                $editHeadreportED = $request->post("editHeadreportED");
                $seeHeadreportED = $request->post("seeHeadreportED");

                if($HeadreportED=="on"){
                    if($deleteHeadreportED=="on"){
                        $HeadreportED=2;
                    }elseif($editHeadreportED=="on" and $deleteHeadreportED!="on"){
                        $HeadreportED=1;
                    }elseif($editHeadreportED!="on" and $seeHeadreportED=="on"){
                        $HeadreportED=0;
                    }else{
                        $HeadreportED=-1;
                    }
                }else{
                    $HeadreportED=-1;
                }

                // پشتیبانها با سه از عناصر اش چک میگردد
                $poshtibanreportED = $request->post("poshtibanreportED");

                $deleteposhtibanreportED = $request->post("deleteposhtibanreportED");
                $editposhtibanreportED = $request->post("editposhtibanreportED");
                $seeposhtibanreportED = $request->post("seeposhtibanreportED");

                if($poshtibanreportED=="on"){
                    if($deleteposhtibanreportED=="on"){
                        $poshtibanreportED=2;
                    }elseif($editposhtibanreportED=="on" and $deleteposhtibanreportED!="on"){
                        $poshtibanreportED=1;
                    }elseif($editposhtibanreportED!="on" and $seeposhtibanreportED=="on"){
                        $poshtibanreportED=0;
                    }else{
                        $poshtibanreportED=-1;
                    }
                }else{
                    $poshtibanreportED=-1;
                }

                // بازار یابها چک میگردد
                $bazaryabreportED = $request->post("bazaryabreportED");

                $deletebazaryabreportED = $request->post("deletebazaryabreportED");
                $editbazaryabreportED = $request->post("editbazaryabreportED");
                $seebazaryabreportED = $request->post("seebazaryabreportED");

                if($bazaryabreportED=="on"){
                    if($deletebazaryabreportED=="on"){
                    $bazaryabreportED=2;
                    }elseif($editbazaryabreportED=="on" and $deletebazaryabreportED!="on"){
                    $bazaryabreportED=1;
                    }elseif($editbazaryabreportED!="on" and $seebazaryabreportED=="on"){
                        $bazaryabreportED=0;
                    }else{
                        $bazaryabreportED=-1;
                    }
                }else{
                    $bazaryabreportED=-1;
                }

                // راننده ها چک میگردد
                    $reportDriverED = $request->post("reportDriverED");

                $deletereportDriverED = $request->post("deletereportDriverED");
                $editreportDriverED = $request->post("editreportDriverED");
                $seereportDriverED = $request->post("seereportDriverED");

                if($reportDriverED=="on"){
                    if($deletereportDriverED=="on"){
                        $reportDriverED=2;
                    }elseif($editreportDriverED=="on" and $deletereportDriverED!="on"){
                        $reportDriverED=1;
                    }elseif($editreportDriverED!="on" and $seereportDriverED=="on"){
                        $reportDriverED=0;
                    }else{
                        $reportDriverED=-1;
                    }
                }else{
                    $reportDriverED=-1;
                }

            }else{
                $amalKardreportED=-1;
                $managerreportED = -1;
                $HeadreportED = -1;
                $poshtibanreportED = -1;
                $bazaryabreportED = -1;
                $reportDriverED = -1;
            }
            //  تراز کاربران 
                $trazEmployeeReportED = $request->post("trazEmployeeReportED");

                $deletetrazEmployeeReportED = $request->post("deletetrazEmployeeReportED");
                $edittrazEmployeeReportED = $request->post("edittrazEmployeeReportED");
                $seetrazEmployeeReportED = $request->post("seetrazEmployeeReportED");

                if($trazEmployeeReportED=="on"){
                    if($deletetrazEmployeeReportED=="on"){
                        $trazEmployeeReportED=2;
                    }elseif($edittrazEmployeeReportED=="on" and $deletetrazEmployeeReportED!="on"){
                        $trazEmployeeReportED=1;
                    }elseif($edittrazEmployeeReportED!="on" and $seetrazEmployeeReportED=="on"){
                        $trazEmployeeReportED=0;
                    }else{
                        $trazEmployeeReportED=-1;
                    }
                }else{
                    $trazEmployeeReportED=-1;
                }

                // اگر عملکرد مشتریان آن بود
                    $amalkardCustReportED = $request->post("amalkardCustReportED");
                    if($amalkardCustReportED=="on"){
                    $amalkardCustReportED = 1;
                    // گزارش ورود چک گردد
                        $loginCustRepED = $request->post("loginCustRepED");

                        $deleteloginCustRepED = $request->post("deleteloginCustRepED");
                        $editloginCustRepED = $request->post("editloginCustRepED");
                        $seeloginCustRepED = $request->post("seeloginCustRepED");

                        if($loginCustRepED=="on"){
                            if($deleteloginCustRepED=="on"){
                            $loginCustRepED=2;
                            }elseif($editloginCustRepED=="on" and $deleteloginCustRepED!="on"){
                            $loginCustRepED=1;
                            }elseif($editloginCustRepED!="on" and $seeloginCustRepED=="on"){
                                $loginCustRepED=0;
                            }else{
                                $loginCustRepED=-1;
                            }
                        }else{
                            $loginCustRepED=-1;
                        }

                        // مشتریان غیر فعال چک گردد
                            $inActiveCustRepED = $request->post("inActiveCustRepED");

                        $deleteinActiveCustRepED = $request->post("deleteinActiveCustRepED");
                        $editinActiveCustRepED = $request->post("editinActiveCustRepED");
                        $seeinActiveCustRepED = $request->post("seeinActiveCustRepED");

                        if($inActiveCustRepED=="on"){
                            if($deleteinActiveCustRepED=="on"){
                                $inActiveCustRepED=2;
                            }elseif($editinActiveCustRepED=="on" and $deleteinActiveCustRepED!="on"){
                                $inActiveCustRepED=1;
                            }elseif($editinActiveCustRepED!="on" and $seeinActiveCustRepED=="on"){
                                $inActiveCustRepED=0;
                            }else{
                                    $inActiveCustRepED=-1;
                            }
                        }else{
                            $inActiveCustRepED=-1;
                        }

                        // فاقد کاربر چک گردد
                        $noAdminCustRepED = $request->post("noAdminCustRepED");

                        $deletenoAdminCustRepED = $request->post("deletenoAdminCustRepED");
                        $editnoAdminCustRepED = $request->post("editnoAdminCustRepED");
                        $seenoAdminCustRepED = $request->post("seenoAdminCustRepED");

                        if($noAdminCustRepED=="on"){
                            if($deletenoAdminCustRepED=="on"){
                                $noAdminCustRepED=2;
                            }elseif($editnoAdminCustRepED=="on" and $deletenoAdminCustRepED!="on"){
                                $noAdminCustRepED=1;
                            }elseif($editnoAdminCustRepED!="on" and $seenoAdminCustRepED=="on"){
                                $noAdminCustRepED=0;
                            }else{
                                $noAdminCustRepED=-1;
                            }
                        }else{
                            $noAdminCustRepED=-1;
                        }

                        // مشتریان ارجاعی چک گردد
                        $returnedCustRepED = $request->post("returnedCustRepED");
                        $deletereturnedCustRepED = $request->post("deletereturnedCustRepED");
                        $editreturnedCustRepED = $request->post("editreturnedCustRepED");
                        $seereturnedCustRepED = $request->post("seereturnedCustRepED");

                        if($returnedCustRepED=="on"){
                            if($deletereturnedCustRepED=="on"){
                                $returnedCustRepED=2;
                            }elseif($editreturnedCustRepED=="on" and $deletereturnedCustRepED!="on"){
                                $returnedCustRepED=1;
                            }elseif($editreturnedCustRepED!="on" and $seereturnedCustRepED=="on"){
                                $returnedCustRepED=0;
                            }else{
                                    $returnedCustRepED=-1;
                            }
                        }else{
                            $returnedCustRepED=-1;
                        }

                    }else {
                    $amalkardCustReportED=-1;
                    $loginCustRepED = -1;
                    $inActiveCustRepED = -1;
                    $noAdminCustRepED = -1;
                    $returnedCustRepED = -1;
                    }


                // اگر عملکرد کالا آن بود 
                    $goodsReportED = $request->post("goodsReportED");
                    if($goodsReportED=="on"){
                    $goodsReportED = 1;
                    //گزارش فروش کالا چک گردد
                    $salegoodsReportED = $request->post("salegoodsReportED");
                    $deletesalegoodsReportED = $request->post("deletesalegoodsReportED");
                    $editsalegoodsReportED = $request->post("editsalegoodsReportED");
                    $seesalegoodsReportED = $request->post("seesalegoodsReportED");

                    if($salegoodsReportED=="on"){
                        if($deletesalegoodsReportED=="on"){
                            $salegoodsReportED=2;
                        }elseif($editsalegoodsReportED=="on" and $deletesalegoodsReportED!="on"){
                            $salegoodsReportED=1;
                        }elseif($editsalegoodsReportED!="on" and $seesalegoodsReportED=="on"){
                            $salegoodsReportED=0;
                        }else{
                            $salegoodsReportED=-1;
                        }
                    }else{
                        $salegoodsReportED=-1;
                    }

                    // کالاهای برگشتی چک گردد
                    $returnedgoodsReportED = $request->post("returnedgoodsReportED");
                        $deletereturnedgoodsReportED = $request->post("deletereturnedgoodsReportED");
                        $editreturnedgoodsReportED = $request->post("editreturnedgoodsReportED");
                        $seereturnedgoodsReportED = $request->post("seereturnedgoodsReportED");

                        if($returnedgoodsReportED=="on"){
                            if($deletereturnedgoodsReportED=="on"){
                                $returnedgoodsReportED=2;
                            }elseif($editreturnedgoodsReportED=="on" and $deletereturnedgoodsReportED!="on"){
                                $returnedgoodsReportED=1;
                            }elseif($editreturnedgoodsReportED!="on" and $seereturnedgoodsReportED=="on"){
                                $returnedgoodsReportED=0;
                            }else{
                                $returnedgoodsReportED=-1;
                            }
                        }else{
                            $returnedgoodsReportED=-1;
                        }

                    // کالاهای فاقد موجودی چک گردد
                    $NoExistgoodsReportED = $request->post("NoExistgoodsReportED");

                        $deleteNoExistgoodsReportED = $request->post("deleteNoExistgoodsReportED");
                        $editNoExistgoodsReportED = $request->post("editNoExistgoodsReportED");
                        $seeNoExistgoodsReportED = $request->post("seeNoExistgoodsReportED");

                        if($NoExistgoodsReportED=="on"){
                            if($deleteNoExistgoodsReportED=="on"){
                                $NoExistgoodsReportED=2;
                            }elseif($editNoExistgoodsReportED=="on" and $deleteNoExistgoodsReportED!="on"){
                                $NoExistgoodsReportED=1;
                            }elseif($editNoExistgoodsReportED!="on" and $seeNoExistgoodsReportED=="on"){
                                $NoExistgoodsReportED=0;
                            }else{
                                $NoExistgoodsReportED=-1;
                            }
                        }else{
                            $NoExistgoodsReportED=-1;
                        }

                        // کالاهای راکت چک گردد

                        $nosalegoodsReportED = $request->post("nosalegoodsReportED");

                        $deletenosalegoodsReportED = $request->post("deletenosalegoodsReportED");
                        $editnosalegoodsReportED = $request->post("editnosalegoodsReportED");
                        $seenosalegoodsReportED = $request->post("seenosalegoodsReportED");

                        if($nosalegoodsReportED=="on"){
                            if($deletenosalegoodsReportED=="on"){
                                $nosalegoodsReportED=2;
                            }elseif($editnosalegoodsReportED=="on" and $deletenosalegoodsReportED!="on"){
                                $nosalegoodsReportED=1;
                            }elseif($editnosalegoodsReportED!="on" and $seenosalegoodsReportED=="on"){
                                $nosalegoodsReportED=0;
                            }else{
                                $nosalegoodsReportED=-1;
                            }
                        }else{
                            $nosalegoodsReportED=-1;
                        }
                    }else {
                    $nosalegoodsReportED=-1;
                    $NoExistgoodsReportED=-1;
                    $returnedgoodsReportED=-1;
                    $salegoodsReportED=-1;
                    $goodsReportED=-1;
                    }
            
            //    اگر گزارش برگشتی کالا آن بود 
            $returnedReportgoodsReportED = $request->post("returnedReportgoodsReportED");
                if($returnedReportgoodsReportED=="on"){
                    $returnedReportgoodsReportED = 1;
                    // تسویه نشده ها چک گردد
                    $returnedNTasReportgoodsReportED = $request->post("returnedNTasReportgoodsReportED");
                    $deletereturnedNTasReportgoodsReportED = $request->post("deletereturnedNTasReportgoodsReportED");
                    $editreturnedNTasReportgoodsReportED = $request->post("editreturnedNTasReportgoodsReportED");
                    $seereturnedNTasReportgoodsReportED = $request->post("seereturnedNTasReportgoodsReportED");

                    if($returnedNTasReportgoodsReportED=="on"){
                        if($deletereturnedNTasReportgoodsReportED=="on"){
                            $returnedNTasReportgoodsReportED=2;
                        }elseif($editreturnedNTasReportgoodsReportED=="on" and $deletereturnedNTasReportgoodsReportED!="on"){
                            $returnedNTasReportgoodsReportED=1;
                        }elseif($editreturnedNTasReportgoodsReportED!="on" and $seereturnedNTasReportgoodsReportED=="on"){
                            $returnedNTasReportgoodsReportED=0;
                        }else{
                            $returnedNTasReportgoodsReportED=-1;
                        }
                    }else{
                        $returnedNTasReportgoodsReportED=-1;
                    }

                        // تسویه شده ها چک گردد
                    $tasgoodsReprtED = $request->post("tasgoodsReprtED");

                    $deletetasgoodsReprtED = $request->post("deletetasgoodsReprtED");
                    $edittasgoodsReprtED = $request->post("edittasgoodsReprtED");
                    $seetasgoodsReprtED = $request->post("seetasgoodsReprtED");

                    if($tasgoodsReprtED=="on"){
                        if($deletetasgoodsReprtED=="on"){
                            $tasgoodsReprtED=2;
                        }elseif($editreturnedNTasReportgoodsReportED=="on" and $deletetasgoodsReprtED!="on"){
                            $tasgoodsReprtED=1;
                        }elseif($editreturnedNTasReportgoodsReportED!="on" and $seereturnedNTasReportgoodsReportED=="on"){
                            $tasgoodsReprtED=0;
                        }else{
                            $tasgoodsReprtED=-1;
                        }
                    }else{
                        $tasgoodsReprtED=-1;
                    }
                }else{
                    $returnedEDTasReportgoodsReportED=-1;
                    $tasgoodsReprtED=-1;
                    $returnedReportgoodsReportED=-1;
                    $returnedNTasReportgoodsReportED=-1;
                }
    
                // گزارش بارگیری چک گردد

                $goodsbargiriReportED = $request->post("goodsbargiriReportED");
        
                $deletegoodsbargiriReportED = $request->post("deletegoodsbargiriReportED");
                $editgoodsbargiriReportED = $request->post("editgoodsbargiriReportED");
                $seegoodsbargiriReportED = $request->post("seegoodsbargiriReportED");

                if($goodsbargiriReportED=="on"){
                    if($deletegoodsbargiriReportED=="on"){
                        $goodsbargiriReportED=2;
                    }elseif($editgoodsbargiriReportED=="on" and $deletegoodsbargiriReportED!="on"){
                        $goodsbargiriReportED=1;
                    }elseif($editgoodsbargiriReportED!="on" and $seegoodsbargiriReportED=="on"){
                        $goodsbargiriReportED=0;
                    }else{
                        $goodsbargiriReportED=-1;
                    }
                }else{
                    $goodsbargiriReportED=-1;
                }

        }else {
            $reportED=-1;
            $amalKardreportED=-1;
            $managerreportED=-1;
            $HeadreportED=-1;
            $poshtibanreportED=-1;
            $bazaryabreportED=-1;
            $reportDriverED=-1;
            $amalkardCustReportED=-1;
            $loginCustRepED=-1;
            $inActiveCustRepED=-1;
            $noAdminCustRepED=-1;
            $returnedCustRepED=-1;
            $trazEmployeeReportED=-1;
            $nosalegoodsReportED=-1;
            $NoExistgoodsReportED=-1;
            $returnedgoodsReportED=-1;
            $salegoodsReportED=-1;
            $goodsReportED=-1;
            $returnedNTasReportgoodsReportED=-1;
            $tasgoodsReprtED=-1;
            $returnedReportgoodsReportED=-1;
            $goodsbargiriReportED=-1;
        }
       // $bossId=0;
       $saleLineSn=0;

       if($manager){
           $bossId=$manager;  
       }

       if($head){
           $bossId=$head;  
       }
       
       if($saleLine){
           $saleLineSn=$saleLine;
       }


       if($request->file('picture')){
           $picture=$request->file('picture');
           $fileName=$adminId.".jpg";
           $picture->move("resources/assets/images/admins/",$fileName);
       }
       
       DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(['username'=>"".$userName."",'name'=>"".$name."",'lastName'=>"".$lastName."",'poshtibanType'=>$poshtibanType,
                                                                       'adminType'=>$adminType,'password'=>"".$password."",'activeState'=>1,'phone'=>$phone,"bossId"=>$bossId,
                                                                       'address'=>$address,'sex'=>"".$sex."",'discription'=>"".$discription."","SaleLineId"=>$saleLine,"employeeType"=>$employeeType]);
                                                                       
                                       // اطلاعات پایه============

        DB::table("CRM.dbo.crm_hasAccess")->WHERE("adminId",$adminId)->update(
            [ "declareElementOppN"=>$declareElementOppED
            
            ,"baseInfoN"=>$baseInfoED
                    
            ,"baseInfoProfileN"=>$baseInfoProfileED
             
            ,"infoRdN"=>$infoRdED
                      
            ,"specialSettingN"=>$specialSettingED
              
            ,"baseInfoSettingN"=>$baseInfoSettingED
             
            ,"InfoSettingAccessN"=>$InfoSettingAccessED
           
            ,"InfoSettingTargetN"=>$InfoSettingTargetED
           
            ,"rdSentN"=>$rdSentED
                      
            ,"rdNotSentN"=>$rdNotSentED
                   
            ,"oppManagerN"=>$oppManagerED
                  
            ,"oppHeadN"=>$oppHeadED
                     
            ,"oppBazaryabN"=>$oppBazaryabED
                 
            ,"oppTakhsisN"=>$oppTakhsisED
                  
            ,"oppDriverServiceN"=>$oppDriverServiceED
            
            ,"oppBargiriN"=>$oppBargiriED
                  
            ,"oppDriverN"=>$oppDriverED
                   
            ,"todayoppNazarsanjiN"=>$todayoppNazarsanjiED
          
            ,"pastoppNazarsanjiN"=>$pastoppNazarsanjiED
           
            ,"DoneoppNazarsanjiN"=>$DoneoppNazarsanjiED
           
            ,"oppNazarSanjiN"=>$oppNazarSanjiED
               
            ,"AddOppupDownBonusN"=>$AddOppupDownBonusED
           
            ,"SubOppupDownBonusN"=>$SubOppupDownBonusED
           
            ,"OppupDownBonusN"=>$OppupDownBonusED
              
            ,"AddedoppRDN"=>$AddedoppRDED
                  
            ,"NotAddedoppRDN"=>$NotAddedoppRDED
               
            ,"oppRDN"=>$oppRDED
                       
            ,"oppjustCalendarN"=>$oppjustCalendarED
             
            ,"oppCustCalendarN"=>$oppCustCalendarED
             
            ,"oppCalendarN"=>$oppCalendarED
                 
            ,"allalarmoppN"=>$allalarmoppED
                 
            ,"donealarmoppN"=>$donealarmoppED
                
            ,"NoalarmoppN"=>$NoalarmoppED
                  
            ,"alarmoppN"=>$alarmoppED
                    
            ,"massageOppN"=>$massageOppED
                  
            ,"justBargiriOppN"=>$justBargiriOppED
              
            ,"oppN"=>$oppED
                         
            ,"reportN"=>$reportED
                      
            ,"amalKardreportN"=>$amalKardreportED
              
            ,"managerreportN"=>$managerreportED
               
            ,"HeadreportN"=>$HeadreportED
                  
            ,"poshtibanreportN"=>$poshtibanreportED
             
            ,"bazaryabreportN"=>$bazaryabreportED
              
            ,"reportDriverN"=>$reportDriverED
                
            ,"amalkardCustReportN"=>$amalkardCustReportED
          
            ,"loginCustRepN"=>$loginCustRepED
                
            ,"inActiveCustRepN"=>$inActiveCustRepED
             
            ,"noAdminCustRepN"=>$noAdminCustRepED
              
            ,"returnedCustRepN"=>$returnedCustRepED
             
            ,"trazEmployeeReportN"=>$trazEmployeeReportED
          
            ,"nosalegoodsReportN"=>$nosalegoodsReportED
           
            ,"NoExistgoodsReportN"=>$NoExistgoodsReportED
          
            ,"returnedgoodsReportN"=>$returnedgoodsReportED
         
            ,"salegoodsReportN"=>$salegoodsReportED
             
            ,"goodsReportN"=>$goodsReportED
                 
            ,"returnedNTasReportgoodsReportN"=>$returnedNTasReportgoodsReportED
            ,"tasgoodsReprtN"=>$tasgoodsReprtED
               
            ,"returnedReportgoodsReportN"=>$returnedReportgoodsReportED
   
            ,"goodsbargiriReportNrtN"=>$goodsbargiriReportED
       
            ,"goodsbargiriReportN"=>$goodsbargiriReportED
          ]);
        return redirect("/listKarbaran");
    }
    public function getHeads(Request $request)
    {
        $heads=DB::select("SELECT * FROM CRM.dbo.crm_admin where employeeType=2 and deleted=0");
        return Response::json($heads);
    }

    
    // ======================

    public function deleteAdmin(Request $request)
    {
        $asn=$request->get("asn");
        DB::table("CRM.dbo.crm_admin")->where("id",$asn)->update(['deleted'=>1,'bossId'=>0]);
        // $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where('deleted',0)->where('crm_admin.adminType',2)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","crm_admin.discription")->orderby("admintype")->get();
        return Response::json(1);
    }
	    public function deleteAdmin1(Request $request)
    {
        $asn=$request->get("asn");
        DB::table("CRM.dbo.crm_admin")->where("id",$asn)->update(['deleted'=>1]);
        $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where('deleted',0)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","crm_admin.discription")->orderby("admintype")->get();
        return Response::json($admins);
    }
        
    // ======================

    public function changeAlarm(Request $request){
        $comment=$request->get("comment");
        $adminId=Session::get("asn");
        $alarmDate=$request->get("alarmDate");
        $factorId=$request->get("factorId");
        DB::table("CRM.dbo.crm_alarm")->where('factorId',$factorId)->update(['state'=>1]);
        DB::table("CRM.dbo.crm_alarm")->insert(['comment'=>"".$comment."",'adminId'=>$adminId,'state'=>0,'alarmDate'=>"".$alarmDate."",'factorId'=>$factorId]);
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
        
        $customers=DB::select("SELECT *,CRM.dbo.getCustomerPhoneNumbers(PSN) as PhoneStr,CRM.dbo.getCustomerMantagheh(SnMantagheh) as Mantagheh,CRM.dbo.getAdminName(PSN) as PoshtibanName FROM (
            SELECT * FROM (
                SELECT * FROM (
                    SELECT * FROM (
                        SELECT * FROM (
                            SELECT DISTINCT * FROM (
                                SELECT alarmDate, TimeStamp,factorId,state,adminId,comment,id from CRM.dbo.crm_alarm)a
                        JOIN (SELECT factorId AS factorNumber FROM CRM.dbo.crm_assesment)b ON a.factorId=b.factorNumber)c
                    JOIN (SELECT id AS admin_id,name AS AdminName,lastName FROM CRM.dbo.crm_admin)d ON c.adminId=d.Admin_id)e
                JOIN (SELECT SerialNoHDS,CustomerSn,NetPriceHDS FROM Shop.dbo.FactorHds )f ON f.SerialNoHDS=e.factorId)g
            JOIN (SELECT PSN,Name,CompanyNo,peopeladdress,GroupCode,SnMantagheh FROM Shop.dbo.Peopels)j ON j.PSN=g.CustomerSn)k
        JOIN (SELECT SnMNM,NameRec FROM Shop.dbo.MNM WHERE  CompanyNo=5)l ON k.SnMantagheh=l.SnMNM)m
        WHERE  GroupCode IN ( ".implode(",",Session::get("groups")).") and CompanyNo=5  and alarmDate<='".$todayDate."' and not exists(select customerId from CRM.dbo.crm_inactiveCustomer where customerId=PSN) AND state=0" );
        foreach ($customers as $customer) {
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);
            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());
        }
        return Response::json($customers);
    }

    // ======================

    public function getAlarmHistory(Request $request)
    {
        $fsn=$request->get("fsn");
        $history=DB::select("SELECT Format(a.TimeStamp,'yyyy/MM/dd','fa-ir')as TimeStamp,a.adminId,a.alarmDate,a.comment,a.state,a.id,CONCAT(name,lastName) as adminName from CRM.dbo.crm_alarm a join CRM.dbo.crm_admin ad on adminId=ad.id where factorId=$fsn");
        return Response::json($history);
    }
    // ======================

    // public function gotEmpty(Request $request)
    // {
    //     $customers=DB::select("SELECT * FROM Shop.dbo.Peopels
    //                     JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr
	// 						FROM Shop.dbo.PhoneDetail
	// 						GROUP BY SnPeopel)a on PSN=a.SnPeopel
    //                     where PSN not in ( SELECT distinct customer_id FROM CRM.dbo.crm_customer_added where returnState=0 and customer_id is not null)
    //     and PSN not in (SELECT customerId FROM CRM.dbo.crm_inactiveCustomer where customerId is not null and state=1)
    //     and PSN not in(SELECT customerId FROM CRM.dbo.crm_returnCustomer where customerId is not null and returnState=1)
    //     AND CompanyNo=5 AND IsActive=1
    //     AND GroupCode IN(291,297,299,312,313,314)");
            
    //     $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where('crm_admin.adminType','!=',1)->where('crm_admin.adminType','!=',4)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType")->get();
        
    //     return view('admin.gotEmpty',['customers'=>$customers,'admins'=>$admins]);
    // }
        
    // ======================
    





    public function getAdminInfo(Request $request)
    {
        $id=$request->get("id");
        $appositId=$id;
        $myId=Session::get('asn');
        $admin=DB::table("CRM.dbo.crm_admin")->where('id',$id)->first();
        $sendedMessages=DB::select("SELECT *,DATEDIFF (day,EndDate,messageDate) as diffDate from(
            SELECT *,Lag(messageDate, 1) OVER(
                ORDER BY messageDate ASC) AS EndDate from(
            SELECT * FROM ( SELECT * FROM(
                            SELECT * FROM CRM.dbo.crm_message)a
                            JOIN(SELECT name,id as adminId FROM CRM.dbo.crm_admin)c ON a.senderId=c.adminId )b)c
                            WHERE  (senderId=".$myId." and getterId=".$appositId.") or (senderId=".$appositId." and getterId=".$myId."))d order by messageDate desc");

        DB::update("UPDATE CRM.dbo.crm_message set readState=1 WHERE senderId=".$appositId." and getterId=".$myId);
        $heads=DB::select("SELECT * FROM CRM.dbo.crm_admin where bossId=$id and deleted=0 and employeeType=2");

       
        
        return Response::json([$sendedMessages,$appositId,$myId,$admin,$heads]);
    }
    

    public function getOrgChart(Request $request){
        $managerid=$request->get("managerId");
            $managers=DB::table("CRM.dbo.crm_admin")->where('id',$managerid)->where("employeeType",1)->get();
            $managers=DB::select("SELECT concat(TRIM(name),SPACE(1),TRIM(lastName)) as name,id as idField FROM CRM.dbo.crm_admin WHERE id=$managerid and employeeType=1");
            foreach($managers as $manager){
                // $heads=DB::table("CRM.dbo.crm_admin")->where('bossId',$manager->id)->get();
                $heads=DB::select("SELECT concat(TRIM(name),SPACE(1),TRIM(lastName)) as name,id as idField FROM CRM.dbo.crm_admin WHERE bossId=$manager->idField and deleted=0");
                $manager->children=$heads;
                foreach($manager->children as $head){
                  //  $employee=DB::table("CRM.dbo.crm_admin")->where('bossId',$head->id)->get();
                    $employee=DB::select("SELECT concat(TRIM(name),SPACE(1),TRIM(lastName)) as name,id as idField FROM CRM.dbo.crm_admin WHERE bossId=$head->idField and deleted=0");
                    $head->children=$employee;
                }
            }
        return Response::json($managers);
    }


    // ======================

    public function getDiscusstion(Request $request)
    {
        $appositId=$request->get("sendId");
        $myId=Session::get('asn');
        $sendedMessages=DB::select("SELECT *,DATEDIFF (day,EndDate,messageDate) as diffDate from(

            select *,Lag(messageDate, 1) OVER(
                   ORDER BY messageDate ASC) AS EndDate from(
            SELECT * FROM (
                                            SELECT * FROM(
                                            SELECT * FROM CRM.dbo.crm_message)a
                                            JOIN   (SELECT name,id as adminId FROM CRM.dbo.crm_admin)c ON a.senderId=c.adminId )b)c
            
                                            WHERE  (senderId=".$myId." and getterId=".$appositId.") or (senderId=".$appositId." and getterId=".$myId."))d order by messageDate desc");
              
        DB::update("UPDATE CRM.dbo.crm_message set readState=1 WHERE senderId=".$appositId." and getterId=".$myId);
              
        return Response::json([$sendedMessages,$appositId,$myId]);
    }
    
    // ======================

    public function addMessage(Request $request)
    {
        $senderId=Session::get("asn");
        $getterId=$request->get("getterId");
        $messageContent=$request->get("messageContent");
        DB::table("CRM.dbo.crm_message")->insert(['messageContent'=>"".$messageContent."",'readState'=>0,'senderId'=>$senderId,'getterId'=>$getterId]);
        $sendedMessages=DB::select("SELECT * FROM (
                                SELECT * FROM(
                                SELECT * FROM CRM.dbo.crm_message)a
                                JOIN   (SELECT name,id as adminId FROM CRM.dbo.crm_admin)c ON a.senderId=c.adminId )b
                                WHERE  (senderId=".$senderId." and getterId=".$getterId.") or (senderId=".$getterId." and getterId=".$senderId.") ORDER BY     messageDate desc");
        return Response::json([$sendedMessages,$getterId,$senderId]);
    }
    
    // ======================

    public function addDiscussion(Request $request)
    {
        $senderId=Session::get("asn");
        $getterId=$request->get("getterId");
        $messageContent=$request->get("messageArea");
        DB::table("CRM.dbo.crm_message")->insert(['messageContent'=>"".$messageContent."",'readState'=>0,'senderId'=>$senderId,'getterId'=>$getterId]);
        $sendedMessages=DB::select("								select *,DATEDIFF (day,EndDate,messageDate) as diffDate from(

            select *,Lag(messageDate, 1) OVER(
                   ORDER BY messageDate ASC) AS EndDate from(
            SELECT * FROM (
                                            SELECT * FROM(
                                            SELECT * FROM CRM.dbo.crm_message)a
                                            JOIN   (SELECT name,id as adminId FROM CRM.dbo.crm_admin)c ON a.senderId=c.adminId )b)c
            WHERE  (senderId=".$senderId." and getterId=".$getterId.") or (senderId=".$getterId." and getterId=".$senderId."))d  ORDER BY messageDate desc");
        return Response::json([$sendedMessages,$getterId,$senderId]);
    }
    
    // ======================

    public function addAlarmClock(Request $request)
    {
        $comment=$request->get("comment");
        $adminId=Session::get("asn");
        $dateTime=$request->get("dateTime");
        DB::table("CRM.dbo.crm_alarmClock")->insert(['comment'=>"".$comment."",'TimeStamp'=>"".$dateTime."",'adminId'=>$adminId,'doneState'=>0]);
        return Response::json("1");
    }
        
    // ======================

    public function getAlarmTime(Request $request)
    {
        $adminId=Session::get("asn");
        $nowTime=Carbon::now();
        $alarmTime=DB::table("CRM.dbo.crm_alarmClock")->where('doneState',0)->where('adminId',$adminId)->first();
        $alarmTime1=list($t,$d)=explode(" ",$alarmTime->TimeStamp);
        $newTime=$d.' '.$t;
        $alarmTime2=Jalalian::fromFormat('Y/m/d H:i:s', $newTime)->toCarbon();
        $result=$nowTime->gte($alarmTime2);
        $diff = $nowTime->diffInSeconds($alarmTime2);
        return Response::json([$result,$diff,$newTime,$alarmTime]);
    }
        
    // ======================

    public function offAlarmClock(Request $request)
    {
        $adminId=Session::get('asn');
        DB::table("CRM.dbo.crm_alarmClock")->where("adminId",$adminId)->update(["doneState"=>1]);
        return Response::json(1);
    }

    // ======================

    // public function visitorReport(){
    //     $visitors=DB::select("SELECT * FROM (
    //             SELECT CONVERT(date,lastVisit) as lastV,lastVisit,PSN,countLogin,Name,platform,browser,firstVisit,visitDate,countSameTime FROM(
    //             SELECT * FROM(
    //             SELECT * FROM(
    //             SELECT * FROM(
    //             SELECT * FROM(
    //             SELECT MAX(visitDate) as lastVisit,customerId FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)a
    //             JOIN   (SELECT Name,PSN,GroupCode FROM Shop.dbo.Peopels)b
    //             ON a.customerId=b.PSN)c
    //             JOIN   (SELECT COUNT(id) as countLogin,customerId as csn FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)d ON c.customerId=d.csn)e
    //             JOIN   (SELECT visitDate,browser,platform,customerId as cid FROM NewStarfood.dbo.star_customerTrack)f ON e.lastVisit=f.visitDate)g
    //             JOIN   (SELECT MIN(visitDate) as firstVisit,customerId as CUSTOMERID2 FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)h ON g.PSN=h.CUSTOMERID2)i
    //             LEFT JOIN (SELECT count(customerId) as countSameTime,customerId from NewStarfood.dbo.star_customerSession1 group by customerId)j on j.customerId=i.PSN)j
    //             WHERE lastV=Convert(date,getDate())
    //             order by lastVisit desc");
        
    //     return view("admin.visitorReport",['visitors'=>$visitors]);
    // }
    public function searchVisotrsByDate(Request $request)
    {
        $firstDate=Jalalian::fromFormat('Y/m/d', $request->get("firstDate"))->toCarbon()->format('Y-m-d');
        $secondDate=Jalalian::fromFormat('Y/m/d',$request->get("secondDate"))->toCarbon()->format('Y-m-d');

        $visitors=DB::select("select * from(SELECT lastVisit,PSN,countLogin,Name,platform,browser,firstVisit,visitDate,countSameTime,CONVERT(date,lastVisit) AS lastVis FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT MAX(visitDate) as lastVisit,customerId FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)a
            JOIN   (SELECT Name,PSN,GroupCode FROM Shop.dbo.Peopels)b
            ON a.customerId=b.PSN)c
            JOIN   (SELECT COUNT(id) as countLogin,customerId as csn FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)d ON c.customerId=d.csn)e
            JOIN   (SELECT visitDate,browser,platform,customerId as cid FROM NewStarfood.dbo.star_customerTrack)f ON e.lastVisit=f.visitDate)g
            JOIN   (SELECT MIN(visitDate) as firstVisit,customerId as CUSTOMERID2 FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)h ON g.PSN=h.CUSTOMERID2)i
            left join (select count(customerId) as countSameTime,customerId from NewStarfood.dbo.star_customerSession1 group by customerId)j on j.customerId=i.PSN)a
            where a.lastVis>='$firstDate' and a.lastVis<='$secondDate'
            order by lastVisit desc");
        return Response::json($visitors);
    }

    public function searchVisotrsLoginFrom(Request $request)
    {
        $loginFrom=$request->get("loginFrom");

        $visitors=DB::select("SELECT lastVisit,PSN,countLogin,Name,platform,browser,firstVisit,visitDate,countSameTime FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT MAX(visitDate) as lastVisit,customerId FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)a
            JOIN   (SELECT Name,PSN,GroupCode FROM Shop.dbo.Peopels)b
            ON a.customerId=b.PSN)c
            JOIN   (SELECT COUNT(id) as countLogin,customerId as csn FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)d ON c.customerId=d.csn)e
            JOIN   (SELECT visitDate,browser,platform,customerId as cid FROM NewStarfood.dbo.star_customerTrack)f ON e.lastVisit=f.visitDate)g
            JOIN   (SELECT MIN(visitDate) as firstVisit,customerId as CUSTOMERID2 FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)h ON g.PSN=h.CUSTOMERID2)i
            left join (select count(customerId) as countSameTime,customerId from NewStarfood.dbo.star_customerSession1 group by customerId)j on j.customerId=i.PSN
            where countLogin >= $loginFrom
            order by lastVisit desc ");
        return Response::json($visitors);
    }

    public function searchVisotrsLoginTo(Request $request)
    {
        $loginTo=$request->get("loginTo");

        $visitors=DB::select("SELECT lastVisit,PSN,countLogin,Name,platform,browser,firstVisit,visitDate,countSameTime FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT MAX(visitDate) as lastVisit,customerId FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)a
            JOIN   (SELECT Name,PSN,GroupCode FROM Shop.dbo.Peopels)b
            ON a.customerId=b.PSN)c
            JOIN   (SELECT COUNT(id) as countLogin,customerId as csn FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)d ON c.customerId=d.csn)e
            JOIN   (SELECT visitDate,browser,platform,customerId as cid FROM NewStarfood.dbo.star_customerTrack)f ON e.lastVisit=f.visitDate)g
            JOIN   (SELECT MIN(visitDate) as firstVisit,customerId as CUSTOMERID2 FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)h ON g.PSN=h.CUSTOMERID2)i
            left join (select count(customerId) as countSameTime,customerId from NewStarfood.dbo.star_customerSession1 group by customerId)j on j.customerId=i.PSN
            where countLogin <= $loginTo
            order by lastVisit desc ");
        return Response::json($visitors);
    }
    public function searchVisotrsPlatform(Request $request)
    {
        $platform=$request->get("platform");
        if($platform !=0){
        $visitors=DB::select("SELECT lastVisit,PSN,countLogin,Name,platform,browser,firstVisit,visitDate,countSameTime FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT MAX(visitDate) as lastVisit,customerId FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)a
            JOIN   (SELECT Name,PSN,GroupCode FROM Shop.dbo.Peopels)b
            ON a.customerId=b.PSN)c
            JOIN   (SELECT COUNT(id) as countLogin,customerId as csn FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)d ON c.customerId=d.csn)e
            JOIN   (SELECT visitDate,browser,platform,customerId as cid FROM NewStarfood.dbo.star_customerTrack)f ON e.lastVisit=f.visitDate)g
            JOIN   (SELECT MIN(visitDate) as firstVisit,customerId as CUSTOMERID2 FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)h ON g.PSN=h.CUSTOMERID2)i
            left join (select count(customerId) as countSameTime,customerId from NewStarfood.dbo.star_customerSession1 group by customerId)j on j.customerId=i.PSN
            where platform ='$platform'
            order by lastVisit desc ");
            return Response::json($visitors);
        }else{
            $visitors=DB::select("SELECT lastVisit,PSN,countLogin,Name,platform,browser,firstVisit,visitDate,countSameTime FROM(
                SELECT * FROM(
                SELECT * FROM(
                SELECT * FROM(
                SELECT * FROM(
                SELECT MAX(visitDate) as lastVisit,customerId FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)a
                JOIN   (SELECT Name,PSN,GroupCode FROM Shop.dbo.Peopels)b
                ON a.customerId=b.PSN)c
                JOIN   (SELECT COUNT(id) as countLogin,customerId as csn FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)d ON c.customerId=d.csn)e
                JOIN   (SELECT visitDate,browser,platform,customerId as cid FROM NewStarfood.dbo.star_customerTrack)f ON e.lastVisit=f.visitDate)g
                JOIN   (SELECT MIN(visitDate) as firstVisit,customerId as CUSTOMERID2 FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)h ON g.PSN=h.CUSTOMERID2)i
                left join (select count(customerId) as countSameTime,customerId from NewStarfood.dbo.star_customerSession1 group by customerId)j on j.customerId=i.PSN
                order by lastVisit desc ");
            return Response::json($visitors);
        }
    }
    public function searchSameTimeCountLogin(Request $request)
    {
        $contSameTimeLogin=$request->get('countSameTimeLogin');
        $visitors=DB::select("SELECT lastVisit,PSN,countLogin,Name,platform,browser,firstVisit,visitDate,countSameTime FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT * FROM(
            SELECT MAX(visitDate) as lastVisit,customerId FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)a
            JOIN   (SELECT Name,PSN,GroupCode FROM Shop.dbo.Peopels)b
            ON a.customerId=b.PSN)c
            JOIN   (SELECT COUNT(id) as countLogin,customerId as csn FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)d ON c.customerId=d.csn)e
            JOIN   (SELECT visitDate,browser,platform,customerId as cid FROM NewStarfood.dbo.star_customerTrack)f ON e.lastVisit=f.visitDate)g
            JOIN   (SELECT MIN(visitDate) as firstVisit,customerId as CUSTOMERID2 FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)h ON g.PSN=h.CUSTOMERID2)i
            left join (select count(customerId) as countSameTime,customerId from NewStarfood.dbo.star_customerSession1 group by customerId)j on j.customerId=i.PSN
            where countSameTime>=$contSameTimeLogin
            order by lastVisit desc ");
            return Response::json($visitors);
    }
        
    // ======================

    public function getCustomerLoginInfo(Request $request)
    {
        $searchTerm=$request->get("searchTerm");
        $visitors=DB::select("SELECT lastVisit,PSN,countLogin,Name,platform,browser,firstVisit FROM(
                        SELECT * FROM(
                        SELECT * FROM(
                        SELECT * FROM(
                        SELECT * FROM(
                        SELECT MAX(visitDate) as lastVisit,customerId FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)a
                        JOIN   (SELECT Name,PSN,GroupCode FROM Shop.dbo.Peopels)b
                        ON a.customerId=b.PSN)c
                        JOIN   (SELECT COUNT(id) as countLogin,customerId as csn FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)d ON c.customerId=d.csn)e
                        JOIN   (SELECT visitDate,browser,platform FROM NewStarfood.dbo.star_customerTrack)f ON e.lastVisit=f.visitDate)g
                        JOIN   (SELECT MIN(visitDate) as firstVisit,customerId as CUSTOMERID2 FROM NewStarfood.dbo.star_customerTrack GROUP BY    customerId)h ON g.PSN=h.CUSTOMERID2)i
                        WHERE Name LIKE '%".$searchTerm."%'");
        
        return Response::json($visitors);
    }

    public function tempRoute(Request $request)
    {
        $drivers=DB::table("Shop.dbo.sla_Drivers")->get();
        $i=8;
        foreach ($drivers as $driver) {
            $i++;
            $userName='driver'.$i;
            DB::table("CRM.dbo.crm_admin")->insert(['username'=>"".$userName."",'name'=>"".$driver->NameDriver."",'lastName'=>"",'adminType'=>4,'password'=>"111",'activeState'=>1,'phone'=>'0910','address'=>"آدرس",'sex'=>1,'discription'=>"دستی وارد شده است",'hasAsses'=>0,'driverId'=>$driver->SnDriver,'hasAllCustomer'=>0]);
        }
        
        return $drivers;
        $adminId=19;
        $countAllCommentedCustomers=DB::select("select COUNT(customerId) AS countComment from(
            select distinct customerId from CRM.dbo.crm_comment where adminId=$adminId and TimeStamp>=(select min(addedTime) from CRM.dbo.crm_customer_added where returnState=1 and admin_id=$adminId and customer_id not in(select customerId from CRM.dbo.crm_returnCustomer where returnState=1)
            and customer_id not in(select customerId from CRM.dbo.crm_inactiveCustomer where state=1)
            )
            and TimeStamp<=(select max(removedTime) from CRM.dbo.crm_customer_added where returnState=1 and admin_id=$adminId and customer_id not in(select customerId from CRM.dbo.crm_returnCustomer where returnState=1)
            and customer_id not in(select customerId from CRM.dbo.crm_inactiveCustomer where state=1)
            )
            )a ");
        $allActiveCustomerCount=DB::select("SELECT COUNT(PSN) as countActiveCustomers FROM Shop.dbo.Peopels WHERE  PSN in(SELECT customer_id FROM CRM.dbo.crm_customer_added WHERE  returnState=1  and admin_id=$adminId
        and PSN not in(select customerId from CRM.dbo.crm_returnCustomer where returnState=1)
            and PSN not in(select customerId from CRM.dbo.crm_inactiveCustomer where state=1)
        ) and Peopels.CompanyNo=5 and Peopels.GroupCode IN ( ".implode(",",Session::get("groups")).")");
        $nocommentedCustomers=$allActiveCustomerCount[0]->countActiveCustomers - $countAllCommentedCustomers[0]->countComment;
        if($nocommentedCustomers<0){
            $nocommentedCustomers=0; 
        }
        $todayDate=Carbon::now()->format('Y-m-d');
        $countNoDoneWork=\DB::select("select sum(countJob) as countJob from(
                                    select COUNT(id) as countJob,specifiedDate from (
                                    select * from (
                                    select crm_workList.commentId,crm_workList.id,crm_workList.doneState,crm_workList.specifiedDate,crm_comment.customerId from CRM.dbo.crm_workList join CRM.dbo.crm_comment on crm_workList.commentId=crm_comment.id where doneState=0)a
                                    join (select customer_id,returnState,admin_id from CRM.dbo.crm_customer_added )c on a.customerId =c.customer_id where c.returnState=0 and admin_id=".$adminId.")b
                                    where specifiedDate<='".$todayDate."'
                                    group by specifiedDate)a");
        return $countNoDoneWork[0]->countJob;
// 'noCommentCust'=>$nocommentedCustomers,
        DB::table("CRM.dbo.crm_adminHistory")->where('adminId',$adminId)->update(['noDoneWork'=>$countNoDoneWork[0]->countJob]);
    }
	
    public function downloadApk(Request $request)
    {
        $headers =  [
                'Content-Type'=>'application/vnd.android.package-archive',
                'Content-Disposition'=> 'attachment; filename="starfood.apk"',
            ];
       // return response()->download(base_path('resources\\assets\\apks\\starfood.apk'));
        return response()->file(base_path('resources\\assets\\apks\\starfoodCRM.apk') , $headers);
    }


    public function showAdminEmtyazHistory(Request $request)
    {
        $adminId=$request->get("adminID");
        $adminHistory=DB::select("SELECT name,lastName,TimeStamp,positiveBonus,negativeBonus,crm_adminUpDownBonus.discription,crm_adminUpDownBonus.id FROM CRM.dbo.crm_admin JOIN CRM.dbo.crm_adminUpDownBonus  ON crm_admin.id=crm_adminUpDownBonus.adminId WHERE isUsed=0 AND adminId=$adminId");
        return Response::json($adminHistory);
    }
    public function getAdminHistory(Request $request)
    {
       $historyID=$request->get("historyID");
       $history=DB::table("CRM.dbo.crm_adminUpDownBonus")->where("id",$historyID)->get();
       return Response::json($history);
    }
    public function editEmtiyazHistory(Request $request)
    {
        $negativeEmtiyasEdit=$request->get("negativeEmtiyasEdit");
        $historyIDEmtiyasEdit=$request->get("historyIDEmtiyasEdit");
        $positiveEmtiyasEdit=$request->get("positiveEmtiyasEdit");
        $discriptionEmtiyasEdit=$request->get("discriptionEmtiyasEdit");
        $adminId=$request->get("adminId");
        DB::table("CRM.dbo.crm_adminUpDownBonus")->where("id",$historyIDEmtiyasEdit)
                                                            ->update(["positiveBonus"=>$positiveEmtiyasEdit
                                                            ,"negativeBonus"=>$negativeEmtiyasEdit
                                                            ,"discription"=>"$discriptionEmtiyasEdit"]);

        $adminHistory=DB::select("SELECT name,lastName,TimeStamp,positiveBonus,negativeBonus,
                                crm_adminUpDownBonus.discription,crm_adminUpDownBonus.id FROM CRM.dbo.crm_admin JOIN CRM.dbo.crm_adminUpDownBonus
                                ON crm_admin.id=crm_adminUpDownBonus.adminId WHERE isUsed=0 AND adminId=$adminId");

        return Response::json($adminHistory);
    }
    public function addToHeadEmployee(Request $request)
    {
        $adminIDs=$request->get("adminID");
        $headId=$request->get("headId");
        foreach($adminIDs as $adminId){
            DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(['bossId'=>$headId]);
        }
        return Response::json("good");
    }


  public function subTrees(Request $request)
    {
        //بازاریابهای زیر نظر سرپرست
        $admins=DB::table("CRM.dbo.crm_admin")->where("bossId",Session::get("asn"))->where('deleted',0)->get();
        //لیست سرپرستها
        $bosses=DB::table("CRM.dbo.crm_admin")->where('adminType','!=',4)->where('adminType','!=',5)->where('deleted',0)->get();
        return Response::json(['admins'=>$admins,'bosses'=>$bosses]);

    }



public function amalKardKarbarn(Request $request){
     //بازاریابهای زیر نظر سرپرست
        $exactAdmin=DB::table("CRM.dbo.crm_admin")->where('id',SESSION::get("asn"))->get();
        $adminId=SESSION::get("asn");
        $admins;
        
        $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where("crm_admin.adminType","!=",5)->where('deleted',0)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","driverId")->get();

        if($exactAdmin[0]->adminType!=5){
            $admins=DB::table("CRM.dbo.crm_admin")->join("CRM.dbo.crm_adminType",'crm_adminType.id','=','crm_admin.adminType')->where("bossId",Session::get("asn"))->where("crm_admin.adminType","!=",5)->where('deleted',0)->select("crm_admin.id","crm_admin.name","crm_admin.lastName","crm_admin.adminType as adminTypeId","crm_adminType.adminType","driverId")->get();
        }
        //لیست سرپرستها
        $bosses=DB::table("CRM.dbo.crm_admin")->where('adminType','!=',5)->where('deleted',0)->get();

        $adminTypes=DB::select("SELECT * FROM CRM.dbo.crm_adminType WHERE  id=2 or id=3");

        $exactAdmin=DB::table("CRM.dbo.crm_admin")->where('id',SESSION::get("asn"))->get();
        $adminId=SESSION::get("asn");
        $admins;
        $saleLine;
                // همه 
        $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 and adminType!=5 and employeeType=1");
        $saleLine=DB::select("SELECT * FROM CRM.dbo.crm_SaleLine WHERE deleted=0");

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

    return view("admin.amalKardKarbaran",
       ['admins'=>$admins,
        'bosses'=>$bosses, 
        'admins'=>$admins,
        'adminTypes'=>$adminTypes,
        'admins'=>$admins,
        'saleLine'=>$saleLine,
        'specialBonuses'=>$specialBonuses,
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
        'all_monthly_bonuses'=>$all_monthly_bonuses
    ]);
    }

    public function getEmployies(Request $request)
    {
        $employeeType=$request->get("employeeType");
        if($employeeType=="all"){
             $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin
                    LEFT JOIN (SELECT MIN(addedTime) takhsisDate,admin_id,COUNT(customer_id) AS countCustomer  FROM CRM.dbo.crm_customer_added
                    JOIN CRM.dbo.crm_admin ON crm_admin.id=crm_customer_added.admin_id WHERE returnState=0 group by admin_id)a on a.admin_id=crm_admin.id
                    WHERE deleted =0");
        
        return Response::json($admins);

        }else{
        $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin
                    LEFT JOIN (SELECT MIN(addedTime) takhsisDate,admin_id,COUNT(customer_id) AS countCustomer  FROM CRM.dbo.crm_customer_added
                    JOIN CRM.dbo.crm_admin ON crm_admin.id=crm_customer_added.admin_id WHERE returnState=0 group by admin_id)a on a.admin_id=crm_admin.id
                    WHERE employeeType=$employeeType AND deleted =0");
        
        return Response::json($admins);
    }
    }


    public function EditAdminComment(Request $request)
    {
        $adminId=$request->get("adminId");
        $comment=$request->get("comment");
        DB::table("CRM.dbo.crm_admin")->where("id",$adminId)->update(['discription'=>"$comment"]);
        return Response::json('good');
    }



    public function getCustomerAndAdminInfo(Request $request)
    {
        $csn=$request->get("csn");
        $asn=$request->get("asn");
        $info=0;
        if($asn>0){
            $info=DB::select("SELECT * FROM CRM.dbo.crm_admin JOIN CRM.dbo.crm_customer_added ON crm_admin.id=crm_customer_added.admin_id JOIN Shop.dbo.Peopels ON PSN=crm_customer_added.customer_id
            JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)a on PSN=a.SnPeopel WHERE
            customer_id=$csn AND admin_id=$asn  and returnState=0");
        }else{
            $info=DB::select("SELECT * FROM Shop.dbo.Peopels
            JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)a on PSN=a.SnPeopel WHERE
            PSN=$csn");  
        }
        $otherAdmins=DB::select("SELECT * FROM CRM.dbo.crm_admin where deleted=0 and id!=$asn and adminType!=5 and adminType!=4");
        return Response::json([$info,$otherAdmins]);
    }
    public function filteralarms(Request $request)
    {
        $adminType= Session::get("adminType");
        $managerId=Session::get('asn');
        $namePhoneCode=$request->get("namePhoneCode");
        $orderOption=$request->get("orderOption");
        $snMantagheh=$request->get("snMantagheh");
        $joinType="LEFT JOIN";
        if($adminType!=5){
            $joinType=" INNER JOIN";
        }
        $alarmStat=$request->get("alarmState");
        $customers;
        $firstDateAlarm="";
        $secondDateAlarm="1490/01/01";
        if(strlen($request->get("firstDateAlarm"))>3){
            $firstDateAlarm=$request->get("firstDateAlarm");
        }
        if(strlen($request->get("secondDateAlarm"))>3){
            $secondDateAlarm=$request->get("secondDateAlarm");
        }

        $firstDateBuy="";
        $secondDateBuy="1490/01/01";
        if(strlen($request->get("secondDateBuy"))>3){
            $secondDateBuy=$request->get("secondDateBuy");
        }
        if(strlen($request->get("firstDateBuy"))>3){
            $firstDateBuy=$request->get("firstDateBuy");
        }
        $buyOrNot=$request->get("buyOrNot");
        $maxFacors=DB::select("SELECT max(SerialNoHDS) AS MaxFactorId,CustomerSn FROM Shop.dbo.FactorHDS WHERE CompanyNo=5 and FactDate>='1401/08/08' and not exists(select * from CRM.dbo.crm_alarm where factorId=SerialNoHDS) GROUP BY CustomerSn");

        $inAlarmFactors=DB::select("SELECT factorId,CustomerSn FROM CRM.dbo.crm_alarm 
        JOIN Shop.dbo.FactorHDS ON crm_alarm.factorId=FactorHDS.SerialNoHDS where state=0");

        foreach ($maxFacors as $factor) {
            foreach ($inAlarmFactors as $alarm) {
                if($factor->CustomerSn==$alarm->CustomerSn and ($factor->MaxFactorId>$alarm->factorId)){
                    DB::table("CRM.dbo.crm_alarm")->where("factorId",$alarm->factorId)->update(["state"=>1]);
                }
            }
        }
        $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');

        if($alarmStat==0){
            //آلارم های که در آلارم نمایش داده می شود
            $customers=DB::select("EXEC CRM.dbo.getAllAlrams $managerId,'$snMantagheh','$namePhoneCode','$orderOption',$adminType,'$firstDateAlarm','$secondDateAlarm'");
            foreach ($customers as $customer) {
            $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

            $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

            }
        }
        if($alarmStat==1){
            //آلارم های که یک دفعه تاریخچه دارند.
                $customers=DB::select("EXEC CRM.dbo.getDoneAlarms $managerId,'$firstDateAlarm','$secondDateAlarm','$namePhoneCode','$snMantagheh','$orderOption',$adminType" );
                foreach ($customers as $customer) {
                $customer->assignedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays($customer->TimeStamp);

                $customer->PassedDays=\Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y/m/d',trim($customer->alarmDate))->diffInDays(Carbon::now());

                }
        }

        
        if($alarmStat==2){
            //مشتریانی که آلارم ندارند
            $customers=DB::select("EXEC CRM.dbo.getNoAlarmCustomers $buyOrNot,$managerId,'$namePhoneCode','$snMantagheh','$orderOption',$adminType");
            // if($buyOrNot==2 or $buyOrNot==-1){
            //     //آنهاییکه آلارم ندارند و همه خرید و ناخرید
            //         $customers=DB::select("SELECT PSN,PCode,Name,NameRec,lastFactorSn AS SerialNoHDS,adminId,adminName,FactDate,PhoneStr FROM(
            //             SELECT * FROM (SELECT MAX(SerialNoHDS) AS lastFactorSn,CustomerSn FROM Shop.dbo.FactorHDS GROUP BY CustomerSn)b WHERE b.lastFactorSn NOT IN(SELECT factorId FROM CRM.dbo.crm_alarm WHERE state=0))c RIGHT JOIN Shop.dbo.Peopels ON c.CustomerSn=Peopels.PSN 
            //             JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)g ON PSN=g.SnPeopel
            //             JOIN Shop.dbo.MNM ON SnMNM=SnMantagheh
            //             LEFT JOIN Shop.dbo.FactorHDS ON lastFactorSn=FactorHDS.SerialNoHDS
            //             $joinType   (SELECT CONCAT(name,lastName) AS adminName,crm_admin.id AS adminId,customer_id AS customerId FROM CRM.dbo.crm_admin JOIN CRM.dbo.crm_customer_added ON crm_admin.id=crm_customer_added.admin_id WHERE returnState=0)b ON b.customerId=PSN
            //             WHERE IsActive=1 AND PSN NOT IN(SELECT customerId FROM CRM.dbo.crm_inactiveCustomer WHERE state=1 AND customerId IS NOT NULL) AND Peopels.CompanyNo=5");
            // }
            // if($buyOrNot==1){
            //     //آنهاییکه آلارم ندارند و خرید کرده اند
                
            //         $customers=DB::select("SELECT PSN,adminId,Name,PCode,lastFactorSn as SerialNoHDS,PhoneStr,FactDate,NameRec,adminName from(
            //                                     SELECT * FROM (SELECT MAX(SerialNoHDS) AS lastFactorSn,CustomerSn from Shop.dbo.FactorHDS group by CustomerSn)a join Shop.dbo.Peopels on a.CustomerSn=Peopels.PSN )a
            //                                     JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail group by SnPeopel)g ON a.CustomerSn=g.SnPeopel
            //                                     JOIN Shop.dbo.FactorHDS on lastFactorSn=FactorHDS.SerialNoHDS
            //                                     JOIN Shop.dbo.MNM on SnMNM=a.SnMantagheh
            //                                     $joinType   (select CONCAT(name,lastName) as adminName,crm_admin.id as adminId,customer_id as customerId from CRM.dbo.crm_admin join CRM.dbo.crm_customer_added on crm_admin.id=crm_customer_added.admin_id where returnState=0  and crm_admin.id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($managerId)))b on b.customerId=a.PSN
            //                                     WHERE a.lastFactorSn not in(SELECT factorId from CRM.dbo.crm_alarm where state=0)
            //                                     and IsActive=1 and a.CustomerSn not in(SELECT customerId from CRM.dbo.crm_inactiveCustomer where state=1 and customerId is not null) and a.CompanyNo=5  and FactDate>='$firstDateBuy' and FactDate<='$secondDateBuy'");     
            
            // }
            // if($buyOrNot==0){
            //     //آنهاییکه آلارم ندارند و خرید نکرده اند
            //     $customers=DB::select("SELECT PSN,PCode,Name,NameRec,adminId,adminName,PhoneStr, 'no FactDate' as FactDate,'Not SerialNo' as SerialNoHDS from Shop.dbo.Peopels 
            //                                 JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail group by SnPeopel)g ON PSN=g.SnPeopel
            //                                 JOIN Shop.dbo.MNM on SnMNM=SnMantagheh
            //                                 $joinType   (SELECT CONCAT(name,lastName) as adminName,crm_admin.id as adminId,customer_id as customerId from CRM.dbo.crm_admin JOIN CRM.dbo.crm_customer_added on crm_admin.id=crm_customer_added.admin_id where returnState=0  and crm_admin.id in(SELECT * FROM CRM.dbo.getAdminsOrBosses($managerId)))b on b.customerId=PSN
            //                                 WHERE IsActive=1 AND PSN NOT IN( select customerId from CRM.dbo.crm_inactiveCustomer where state=1 AND customerId IS NOT NULL) and Peopels.CompanyNo=5 and PSN NOT IN(select CustomerSn FROM Shop.dbo.FactorHDS WHERE FactType=3)");     
            // }
        }

        return Response::json($customers);
    }

    public function searchUnAlarmByMantagheh(Request $request)
    {
        $snMantagheh=$request->get("snMantagheh");
        $name=$request->get("searchTerm");
        $notAlarmedCustomers=DB::select("SELECT PSN,PCode,Name,NameRec,lastFactorSn AS SerialNoHDS,CRM.dbo.getAdminName(PSN) as adminName,FactDate,PhoneStr FROM(
                                            SELECT * FROM (SELECT MAX(SerialNoHDS) AS lastFactorSn,CustomerSn FROM Shop.dbo.FactorHDS GROUP BY CustomerSn)b WHERE b.lastFactorSn NOT IN(SELECT factorId FROM CRM.dbo.crm_alarm WHERE state=0))c RIGHT JOIN Shop.dbo.Peopels ON c.CustomerSn=Peopels.PSN 
                                        JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)g ON PSN=g.SnPeopel
                                    JOIN Shop.dbo.MNM ON SnMNM=SnMantagheh
                                LEFT JOIN Shop.dbo.FactorHDS ON lastFactorSn=FactorHDS.SerialNoHDS
                                WHERE IsActive=1 AND PSN NOT IN(SELECT customerId FROM CRM.dbo.crm_inactiveCustomer WHERE state=1 AND customerId IS NOT NULL) AND Peopels.CompanyNo=5 AND Peopels.SaleLevel=3 and SnMantagheh LIKE '%$snMantagheh%' and Name LIKE N'%$name%'");
        return Response::json($notAlarmedCustomers);
    }
    public function searchUnAlarmedCustomer(Request $request)
    {
        $snMantagheh=$request->get("snMantagheh");
        $name=$request->get("searchTerm");
        $notAlarmedCustomers=DB::select("SELECT PSN,PCode,Name,NameRec,lastFactorSn AS SerialNoHDS,CRM.dbo.getAdminName(PSN) as adminName,FactDate,PhoneStr FROM(
                                            SELECT * FROM (SELECT MAX(SerialNoHDS) AS lastFactorSn,CustomerSn FROM Shop.dbo.FactorHDS GROUP BY CustomerSn)b WHERE b.lastFactorSn NOT IN(SELECT factorId FROM CRM.dbo.crm_alarm WHERE state=0))c RIGHT JOIN Shop.dbo.Peopels ON c.CustomerSn=Peopels.PSN 
                                        JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)g ON PSN=g.SnPeopel
                                    JOIN Shop.dbo.MNM ON SnMNM=SnMantagheh
                                LEFT JOIN Shop.dbo.FactorHDS ON lastFactorSn=FactorHDS.SerialNoHDS
                                WHERE IsActive=1 AND PSN NOT IN(SELECT customerId FROM CRM.dbo.crm_inactiveCustomer WHERE state=1 AND customerId IS NOT NULL) AND Peopels.CompanyNo=5 AND Peopels.SaleLevel=3 and SnMantagheh LIKE '%$snMantagheh%' and Name LIKE N'%$name%'");
        return Response::json($notAlarmedCustomers);
    }
    public function orderUnAlarms(Request $request)
    {
        $snMantagheh=$request->get("snMantagheh");
        $name=$request->get("searchTerm");
        $baseName=$request->get("baseName");
        $notAlarmedCustomers=DB::select("SELECT PSN,PCode,Name,NameRec,lastFactorSn AS SerialNoHDS,adminId,adminName,FactDate,PhoneStr FROM(
                                            SELECT * FROM (SELECT MAX(SerialNoHDS) AS lastFactorSn,CustomerSn FROM Shop.dbo.FactorHDS GROUP BY CustomerSn)b WHERE b.lastFactorSn NOT IN(SELECT factorId FROM CRM.dbo.crm_alarm WHERE state=0))c RIGHT JOIN Shop.dbo.Peopels ON c.CustomerSn=Peopels.PSN 
                                        JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail GROUP BY SnPeopel)g ON PSN=g.SnPeopel
                                    JOIN Shop.dbo.MNM ON SnMNM=SnMantagheh
                                LEFT JOIN Shop.dbo.FactorHDS ON lastFactorSn=FactorHDS.SerialNoHDS
                            LEFT JOIN (SELECT CONCAT(name,lastName) AS adminName,crm_admin.id AS adminId,customer_id AS customerId FROM CRM.dbo.crm_admin JOIN CRM.dbo.crm_customer_added ON crm_admin.id=crm_customer_added.admin_id WHERE returnState=0)b ON b.customerId=PSN
                        WHERE IsActive=1 AND PSN NOT IN(SELECT customerId FROM CRM.dbo.crm_inactiveCustomer WHERE state=1 AND customerId IS NOT NULL) AND Peopels.CompanyNo=5 AND Peopels.SaleLevel=3 AND SnMantagheh LIKE '%$snMantagheh%' and Name LIKE N'%$name%' order by $baseName DESC");
        return Response::json($notAlarmedCustomers);
    }

    public function getUnAlarmHistory(Request $request)
    {
            $history=$request->get("history");
            $yesterdayOfWeek = Jalalian::fromCarbon(Carbon::yesterday())->getDayOfWeek();
            $yesterday;
            $customers;
            if($yesterdayOfWeek==6){
                $yesterday = Jalalian::fromCarbon(Carbon::yesterday()->subDays(1))->format('Y/m/d');
            }else{
                $yesterday = Jalalian::fromCarbon(Carbon::yesterday())->format('Y/m/d');
            }
            $todayDate=Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
            $notAlarmedCustomers;
            if($history=="TODAY"){
                $notAlarmedCustomers=DB::select("SELECT PSN,adminId,Name,PCode,lastFactorSn as SerialNoHDS,PhoneStr,FactDate,NameRec,adminName from(
                    SELECT * FROM (SELECT MAX(SerialNoHDS) AS lastFactorSn,CustomerSn from Shop.dbo.FactorHDS group by CustomerSn)a join Shop.dbo.Peopels on a.CustomerSn=Peopels.PSN )a
                    JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail group by SnPeopel)g ON a.CustomerSn=g.SnPeopel
                    JOIN Shop.dbo.FactorHDS on lastFactorSn=FactorHDS.SerialNoHDS
                    JOIN Shop.dbo.MNM on SnMNM=a.SnMantagheh
                    LEFT JOIN (select CONCAT(name,lastName) as adminName,crm_admin.id as adminId,customer_id as customerId from CRM.dbo.crm_admin join CRM.dbo.crm_customer_added on crm_admin.id=crm_customer_added.admin_id where returnState=0)b on b.customerId=a.PSN
                    WHERE a.lastFactorSn not in(SELECT factorId from CRM.dbo.crm_alarm where state=0)
                    and IsActive=1 and a.CustomerSn not in(SELECT customerId from CRM.dbo.crm_inactiveCustomer where state=1 and customerId is not null) and a.CompanyNo=5 AND FactDate='$todayDate'");
            }
            if($history=="YESTERDAY"){
                $notAlarmedCustomers=DB::select("SELECT PSN,adminId,Name,PCode,lastFactorSn as SerialNoHDS,PhoneStr,FactDate,NameRec,adminName from(
                    SELECT * FROM (SELECT MAX(SerialNoHDS) AS lastFactorSn,CustomerSn from Shop.dbo.FactorHDS group by CustomerSn)a join Shop.dbo.Peopels on a.CustomerSn=Peopels.PSN )a
                    JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail group by SnPeopel)g ON a.CustomerSn=g.SnPeopel
                    JOIN Shop.dbo.FactorHDS on lastFactorSn=FactorHDS.SerialNoHDS
                    JOIN Shop.dbo.MNM on SnMNM=a.SnMantagheh
                    LEFT JOIN (select CONCAT(name,lastName) as adminName,crm_admin.id as adminId,customer_id as customerId from CRM.dbo.crm_admin join CRM.dbo.crm_customer_added on crm_admin.id=crm_customer_added.admin_id where returnState=0)b on b.customerId=a.PSN
                    WHERE a.lastFactorSn not in(SELECT factorId from CRM.dbo.crm_alarm where state=0)
                    and IsActive=1 and a.CustomerSn not in(SELECT customerId from CRM.dbo.crm_inactiveCustomer where state=1 and customerId is not null) and a.CompanyNo=5 AND FactDate='$yesterday'");
            }
            if($history=="LASTHUNDRED"){
                $notAlarmedCustomers=DB::select("SELECT TOP 100 PSN,adminId,Name,PCode,lastFactorSn as SerialNoHDS,PhoneStr,FactDate,NameRec,adminName from(
                    SELECT * FROM (SELECT MAX(SerialNoHDS) AS lastFactorSn,CustomerSn from Shop.dbo.FactorHDS group by CustomerSn)a join Shop.dbo.Peopels on a.CustomerSn=Peopels.PSN )a
                    JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail group by SnPeopel)g ON a.CustomerSn=g.SnPeopel
                    JOIN Shop.dbo.FactorHDS on lastFactorSn=FactorHDS.SerialNoHDS
                    JOIN Shop.dbo.MNM on SnMNM=a.SnMantagheh
                    LEFT JOIN (select CONCAT(name,lastName) as adminName,crm_admin.id as adminId,customer_id as customerId from CRM.dbo.crm_admin join CRM.dbo.crm_customer_added on crm_admin.id=crm_customer_added.admin_id where returnState=0)b on b.customerId=a.PSN
                    WHERE a.lastFactorSn not in(SELECT factorId from CRM.dbo.crm_alarm where state=0)
                    and IsActive=1 and a.CustomerSn not in(SELECT customerId from CRM.dbo.crm_inactiveCustomer where state=1 and customerId is not null) and a.CompanyNo=5");
            }
            if($history=="ALLUNALARMS"){
                $notAlarmedCustomers=DB::select("SELECT PSN,adminId,Name,PCode,lastFactorSn as SerialNoHDS,PhoneStr,FactDate,NameRec,adminName from(
                    SELECT * FROM (SELECT MAX(SerialNoHDS) AS lastFactorSn,CustomerSn from Shop.dbo.FactorHDS group by CustomerSn)a join Shop.dbo.Peopels on a.CustomerSn=Peopels.PSN )a
                    JOIN (SELECT SnPeopel, STRING_AGG(PhoneStr, '-') AS PhoneStr FROM Shop.dbo.PhoneDetail group by SnPeopel)g ON a.CustomerSn=g.SnPeopel
                    JOIN Shop.dbo.FactorHDS on lastFactorSn=FactorHDS.SerialNoHDS
                    JOIN Shop.dbo.MNM on SnMNM=a.SnMantagheh
                    LEFT JOIN (select CONCAT(name,lastName) as adminName,crm_admin.id as adminId,customer_id as customerId from CRM.dbo.crm_admin join CRM.dbo.crm_customer_added on crm_admin.id=crm_customer_added.admin_id where returnState=0)b on b.customerId=a.PSN
                    WHERE a.lastFactorSn not in(SELECT factorId from CRM.dbo.crm_alarm where state=0)
                    and IsActive=1 and a.CustomerSn not in(SELECT customerId from CRM.dbo.crm_inactiveCustomer where state=1 and customerId is not null) and a.CompanyNo=5");
            }
        return Response::json($notAlarmedCustomers);
    }
    public function getPersonals(Request $request)
    {
        $personal=$request->get("personal");
        $searchTerm=$request->get("searchTerm");
        $exactAdmin=DB::table("CRM.dbo.crm_admin")->where('id',SESSION::get("asn"))->get();
        $adminId=SESSION::get("asn");
        $admins;
        if($exactAdmin[0]->adminType==5){
            if($personal=='all'){
                // همه 
                $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 and adminType!=5 and name LIKE N'%$searchTerm%'");
            }

            if($personal==1){
            //همه مدیران 
            $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 AND employeeType=1 and name LIKE N'%$searchTerm%'");
            }

            if($personal==2){
                //همه سرپرست ها
                $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 AND employeeType=2 and name LIKE N'%$searchTerm%'");
            }

            if($personal=='p2'){
                //همه پشتیبانها
                $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 AND adminType=2 and name LIKE N'%$searchTerm%'");
            }

            if($personal=='b3'){
                //همه بازاریابها
                $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 AND adminType=3 and name LIKE N'%$searchTerm%'");
            }

            if($personal=='d4'){
                //همه راننده ها
                $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 AND adminType=4 and name LIKE N'%$searchTerm%'");
            }
        }else{
            if($personal=='all'){
                // همه 
                $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 and bossId=$adminId and name LIKE N'%$searchTerm%'");
            }

            if($personal==1){
            //همه مدیران 
            $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 AND employeeType=1 and bossId=$adminId and name LIKE N'%$searchTerm%'");
            }

            if($personal==2){
                //همه سرپرست ها
                $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 AND employeeType=2 and bossId=$adminId and name LIKE N'%$searchTerm%'");
            }

            if($personal=='p2'){
                //همه پشتیبانها
                $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 AND adminType=2 and bossId=$adminId and name LIKE N'%$searchTerm%'");
            }

            if($personal=='b3'){
                //همه بازاریابها
                $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 AND adminType=3 and bossId=$adminId and name LIKE N'%$searchTerm%'");
            }

            if($personal=='d4'){
                //همه راننده ها
                $admins=DB::select("SELECT * FROM CRM.dbo.crm_admin WHERE deleted=0 AND adminType=4 and bossId=$adminId and name LIKE N'%$searchTerm%'");
            }
        }
    return Response::json($admins);
    }

    
public function sendBackReport(Request $request){
    return view("admin.sendBackReport");
}

// kala settings 
   public function kalaSettings(Request $request){
        $kalaId=$request->get('kalaId');
   
        $exactKalaId=$kalaId;
        $maxSaleOfAll=0;
        $showTakhfifPercent=0;
        $kala=DB::select("SELECT PubGoods.GoodName,PubGoods.GoodSn,PubGoods.Price,PubGoods.price2,B.SUNAME,B.AmountUnit, GoodGroups.NameGRP,PUBGoodUnits.UName,star_desc_product.descProduct from Shop.dbo.PubGoods
        join Shop.dbo.GoodGroups on PubGoods.GoodGroupSn=GoodGroups.GoodGroupSn
        inner join Shop.dbo.PUBGoodUnits on PubGoods.DefaultUnit=PUBGoodUnits.USN
        LEFT JOIN NewStarfood.dbo.star_desc_product ON PubGoods.GoodSn=star_desc_product.GoodSn
        left JOIN (SELECT GoodUnitSecond.AmountUnit,GoodUnitSecond.SnGoodUnit,PUBGoodUnits.UName as SUNAME,GoodUnitSecond.SnGood from Shop.dbo.GoodUnitSecond join Shop.dbo.PUBGoodUnits on GoodUnitSecond.SnGoodUnit=PUBGoodUnits.USN) B on PubGoods.GoodSn=B.SnGood
        where PubGoods.GoodSn=".$exactKalaId);
        $exactKala;
        foreach ($kala as $k) {
            $exactKala=$k;
           $subUnitStuff= DB::select("SELECT GoodUnitSecond.AmountUnit,PUBGoodUnits.UName AS secondUnit FROM Shop.dbo.GoodUnitSecond JOIN Shop.dbo.PUBGoodUnits ON GoodUnitSecond.SnGoodUnit=PUBGoodUnits.USN WHERE GoodUnitSecond.SnGood=".$k->GoodSn);
            if(count($subUnitStuff)>0){
                foreach ($subUnitStuff as $stuff) {
                    $exactKala->secondUnit=$stuff->secondUnit;
                    $exactKala->amountUnit=$stuff->AmountUnit;
                }
            }else{
                $exactKala->secondUnit="تعریف نشده است";
                $exactKala->amountUnit="تعریف نشده است";
            }
           $priceStuff= DB::select("SELECT GoodPriceSale.Price3,GoodPriceSale.Price4 FROM Shop.dbo.GoodPriceSale WHERE GoodPriceSale.SnGood=".$k->GoodSn);
           foreach ($priceStuff as $stuff) {
            $exactKala->mainPrice=$stuff->Price3;
            $exactKala->overLinePrice=$stuff->Price4;
            }
            $webSpecialSettings=DB::table("NewStarfood.dbo.star_webSpecialSetting")->select('maxSale')->get();
            foreach ($webSpecialSettings as $special) {
                $maxSaleOfAll=$special->maxSale;
            }
            $restrictSaleStuff=DB::table("NewStarfood.dbo.star_GoodsSaleRestriction")->where("productId",$k->GoodSn)->select("minSale","maxSale","overLine","callOnSale","zeroExistance","hideKala",
                                                                                            "activeTakhfifPercent",'inforsType',"freeExistance",'costLimit','costError','costAmount','activePishKharid','alarmAmount')->get();
            if(count($restrictSaleStuff)>0){
                foreach ($restrictSaleStuff as $saleStuff) {
                $exactKala->minSale=$saleStuff->minSale;
                $exactKala->showTakhfifPercent=$saleStuff->activeTakhfifPercent;
                    if($saleStuff->maxSale>-1){
                        $exactKala->maxSale=$saleStuff->maxSale;

                    }else{
                        $exactKala->maxSale=$maxSaleOfAll;
                    }
                $exactKala->callOnSale=$saleStuff->callOnSale;
                $exactKala->overLine=$saleStuff->overLine;
                $exactKala->zeroExistance=$saleStuff->zeroExistance;
                $exactKala->hideKala=$saleStuff->hideKala;
                $exactKala->freeExistance=$saleStuff->freeExistance;
                $exactKala->costLimit=$saleStuff->costLimit;
                $exactKala->costError=$saleStuff->costError;
                $exactKala->costAmount=$saleStuff->costAmount;
                $exactKala->inforsType=$saleStuff->inforsType;
                $exactKala->activePishKharid=$saleStuff->activePishKharid;
                $exactKala->alarmAmount=$saleStuff->alarmAmount;
                }
            }else{
                $exactKala->freeExistance=0;
                $exactKala->minSale=1;
                $exactKala->maxSale=$maxSaleOfAll;
                $exactKala->callOnSale=0;
                $exactKala->overLine=0;
                $exactKala->zeroExistance=0;
                $exactKala->hideKala=0;
                $exactKala->showTakhfifPercent=0;
                $exactKala->costLimit=0;
                $exactKala->costError="ندارد";
                $exactKala->costAmount=0;
                $exactKala->inforsType=0;
                $exactKala->activePishKharid=0;
                $exactKala->alarmAmount=0;
            }
        }
        $mainGroupList=DB::select("select id,title,show_hide from NewStarfood.dbo.Star_Group_Def where selfGroupId=0");
        $addedKala=DB::select("select firstGroupId,product_id from NewStarfood.dbo.star_add_prod_group");
        $exist="";
        foreach($kala as $kl){
            foreach($mainGroupList as $group){
                foreach($addedKala as $addkl){
                    if($addkl->firstGroupId==$group->id and $kl->GoodSn==$addkl->product_id){
                        $exist='ok';
                        break;
                    }else{
                        $exist='no';
                    }
                }
                $group->exist=$exist;
            }
        }
        $kalaPriceHistory=DB::table("NewStarfood.dbo.star_KalaPriceHistory")->join("NewStarfood.dbo.admin",'admin.id','=','star_KalaPriceHistory.userId')->where('productId',$kalaId)->select("admin.*","star_KalaPriceHistory.*")->get();
        $infors=DB::select("select * from Shop.dbo.infors where CompanyNo=5 and TypeInfor=5");
        $assameKalas=DB::table("NewStarfood.dbo.star_assameKala")->where("mainId",$kalaId)->leftjoin("Shop.dbo.PubGoods","assameId","=","GoodSn")->select("*")->get();
        $stocks=DB::select("SELECT SnStock,CompanyNo,CodeStock,NameStock from Shop.dbo.Stocks where SnStock not in(select stockId from NewStarfood.dbo.star_addedStock where productId=".$kalaId.") and SnStock!=0 and NameStock!='' and CompanyNo=5");
        $addedStocks=DB::select("SELECT SnStock,CompanyNo,CodeStock,NameStock from Shop.dbo.Stocks
                        JOIN NewStarfood.dbo.star_addedStock on Stocks.SnStock=NewStarfood.dbo.star_addedStock.stockId where NewStarfood.dbo.star_addedStock.productId=".$kalaId);
        return Response::json([$exactKala,$mainGroupList, $stocks, $assameKalas,$addedStocks,$infors, $kalaPriceHistory]);
   
    }


    // subgroup method


     public function subGroupsEdit(Request $request)
    {
        $id=$request->get('id');
        $kalaId=$request->get('kalaId');
        $kala=DB::select('SELECT PubGoods.GoodName,PubGoods.GoodSn,PubGoods.Price,PubGoods.price2, GoodGroups.NameGRP,PUBGoodUnits.UName from Shop.dbo.PubGoods inner join Shop.dbo.GoodGroups on PubGoods.GoodGroupSn=GoodGroups.GoodGroupSn inner join Shop.dbo.PUBGoodUnits on PubGoods.DefaultUnit=PUBGoodUnits.USN where PubGoods.GoodSn='.$kalaId);
        $exactKala;
        foreach ($kala as $k) {
            $exactKala=$k;
        }
        $subGroupList=DB::select("select id,title,show_hide,selfGroupId from NewStarfood.dbo.Star_Group_Def  where selfGroupId=".$id);
        $addedKala=DB::select('select firstGroupId,product_id,secondGroupId from NewStarfood.dbo.star_add_prod_group WHERE product_id='.$kalaId);
        $exist="";
        
            foreach($subGroupList as $group){
                foreach($addedKala as $addkl){
                    if($addkl->secondGroupId==$group->id and $kalaId==$addkl->product_id){
                        $exist='ok';
                        break;
                    }else{
                        $exist='no';
                    }
                }
                $group->exist=$exist;
            }
        return $subGroupList;
    }
    


    // set quantity for kalal amount
    public function getUnitsForSettingMinSale(Request $request){
        $kalaId=$request->get('Pcode');
        $secondUnit;
        $defaultUnit;
        $amountUnit;
        $amountExist=0;
        $kalas=DB::select("SELECT PubGoods.GoodName,PubGoods.GoodSn,PUBGoodUnits.UName,V.Amount FROM Shop.dbo.PubGoods
                JOIN Shop.dbo.PUBGoodUnits ON PubGoods.DefaultUnit=PUBGoodUnits.USN
                JOIN (SELECT * FROM Shop.dbo.ViewGoodExists WHERE ViewGoodExists.FiscalYear=1399) V on PubGoods.GoodSn=V.SnGood WHERE PubGoods.CompanyNo=5 AND PubGoods.GoodSn=".$kalaId);
        foreach ($kalas as $kala) {
            $kala->Amount+=DB::select("select SUM(Amount) as SumAmount from Shop.dbo.ViewGoodExistsInStock where ViewGoodExistsInStock.SnStock in(select stockId from NewStarfood.dbo.star_addedStock where productId=".$kala->GoodSn.") and SnGood=".$kala->GoodSn)[0]->SumAmount;
        }         
        foreach ($kalas as $k) {
            $defaultUnit=$k->UName;
            $amountExist=$k->Amount;
        }
        $subUnitStuff= DB::select("SELECT GoodUnitSecond.AmountUnit,PUBGoodUnits.UName AS secondUnit FROM Shop.dbo.GoodUnitSecond JOIN Shop.dbo.PUBGoodUnits
                                ON GoodUnitSecond.SnGoodUnit=PUBGoodUnits.USN WHERE GoodUnitSecond.SnGood=".$kalaId);
        foreach ($subUnitStuff as $stuff) {
            $secondUnit=$stuff->secondUnit;
            $amountUnit=$stuff->AmountUnit;
        }
        $code=" ";
          for ($i= 1; $i <= 500; $i++) {
            $code.="<span class='d-none'>31</span>
            <span id='Count1_0_239' class='d-none'>".($i*$amountUnit)."</span>
             <span id='CountLarge_0_239' class='d-none'>".$i."</span>
             <input value='' style='display:none' class='SnOrderBYS'/>
             <button style='font-weight: bold;  font-size: 17px;' value='".$i.'_'.$kalaId.'_'.$defaultUnit."' class='setMinSale btn-add-to-cart w-100 mb-2'> ".$i."".$secondUnit."  معادل ".($i*$amountUnit)."".$defaultUnit."</button>
             ";
          }
        return Response::json($code);
    }


  public function setMinimamSaleKala(Request $request) {
        $productId=$request->get('kalaId');
        $minSale=$request->get('amountUnit');
        $maxSaleOfAll=0;
        $webSpecialSettings=DB::table("NewStarfood.dbo.star_webSpecialSetting")->select('maxSale')->get();
        foreach ($webSpecialSettings as $special) {
            $maxSaleOfAll=$special->maxSale;
        }
        $checkExistance = DB::select("SELECT * FROM NewStarfood.dbo.star_GoodsSaleRestriction where productId=".$productId);
        if(!(count($checkExistance)>0)){
            DB::insert("INSERT INTO NewStarfood.dbo.star_GoodsSaleRestriction(maxSale, minSale, productId,overLine,callOnSale,zeroExistance) VALUES(".$maxSaleOfAll.", 1, ".$productId.",0,0,0)");

        }else{
            DB::update("UPDATE NewStarfood.dbo.star_GoodsSaleRestriction  SET minSale=".$minSale." WHERE productId=".$productId);
        }
        return Response::json($minSale);
    }


       public function getUnitsForSettingMaxSale(Request $request){
        $kalaId=$request->get('Pcode');
        $secondUnit;
        $defaultUnit;
        $amountUnit;
        $amountExist=0;
        $kalas=DB::select("SELECT PubGoods.GoodName,PubGoods.GoodSn,PUBGoodUnits.UName,V.Amount FROM Shop.dbo.PubGoods
                    JOIN Shop.dbo.PUBGoodUnits ON PubGoods.DefaultUnit=PUBGoodUnits.USN
                    JOIN (SELECT * FROM Shop.dbo.ViewGoodExists WHERE ViewGoodExists.FiscalYear=1399) V on PubGoods.GoodSn=V.SnGood WHERE PubGoods.CompanyNo=5 AND PubGoods.GoodSn=".$kalaId);
        
        foreach ($kalas as $kala) {
            $kala->Amount+=DB::select("select SUM(Amount) as SumAmount from Shop.dbo.ViewGoodExistsInStock where ViewGoodExistsInStock.SnStock in(select stockId from NewStarfood.dbo.star_addedStock where productId=".$kala->GoodSn.") and SnGood=".$kala->GoodSn)[0]->SumAmount;
        }
        
        foreach ($kalas as $k) {
            $defaultUnit=$k->UName;
            $amountExist=$k->Amount;
        }
        $subUnitStuff= DB::select("SELECT GoodUnitSecond.AmountUnit,PUBGoodUnits.UName AS secondUnit FROM Shop.dbo.GoodUnitSecond JOIN Shop.dbo.PUBGoodUnits
                                ON GoodUnitSecond.SnGoodUnit=PUBGoodUnits.USN WHERE GoodUnitSecond.SnGood=".$kalaId);
        
        foreach ($subUnitStuff as $stuff) {
            $secondUnit=$stuff->secondUnit;
            $amountUnit=$stuff->AmountUnit;
        }
        $code=" ";
        for ($i= 1; $i <= 500; $i++) {
        $code.="<span class='d-none'>31</span>
            <span id='Count1_0_239' class='d-none'>".($i*$amountUnit)."</span>
            <span id='CountLarge_0_239' class='d-none'>".$i."</span>
            <input value='' style='display:none' class='SnOrderBYS'/>
            <button style='font-weight: bold;  font-size: 17px;' value='".$i.'_'.$kalaId.'_'.$defaultUnit."' class='setMaxSale btn-add-to-cart w-100 mb-2'> ".$i."".$secondUnit."  معادل ".($i*$amountUnit)."".$defaultUnit."</button>
            ";
        }
        return Response::json($code);
        
    }
    public function setMaximamSaleKala(Request $request)
    {
        $productId=$request->get('kalaId');
        $maxSale=$request->get('amountUnit');
        $checkExistance = DB::select("SELECT * FROM NewStarfood.dbo.star_GoodsSaleRestriction where productId=".$productId);
        if(!(count($checkExistance)>0)){
            DB::insert("INSERT INTO NewStarfood.dbo.star_GoodsSaleRestriction(maxSale, minSale, productId,overLine,callOnSale,zeroExistance) VALUES(".$maxSaleOfAll.", 1, ".$productId.",0,0,0)");

        }else{
            DB::update("UPDATE NewStarfood.dbo.star_GoodsSaleRestriction  SET maxSale=".$maxSale." WHERE productId=".$productId);
        }
        return Response::json($maxSale);
    }


    public function restrictSale(Request $request){
        $overLine1=$request->get('overLine');
        $freeExistance=$request->get('freeExistance');
        $callOnSale=$request->get('callOnSale');
        $zeroExistance=$request->get('zeroExistance');
        $hideKala=$request->get("hideKala");
        $productId=$request->get('kalaId');
        $showTakhfifPercent=$request->get('activeTakhfifPercent');
        $costLimit=$request->get('costLimit');
        $costAmount=$request->get('costAmount');
        $inforsType=$request->get('infors');
        $costErrorContent=$request->get('costErrorContent');
        $activePishKharid=$request->get('activePishKharid');
        $alarmAmount=$request->get('alarmAmount');
        $overLine=0;

        if($showTakhfifPercent){
            $showTakhfifPercent=1;
            $overLine=1;
        }else{
            $showTakhfifPercent=0;
        }

        if($freeExistance){
            $freeExistance=1;
        }else{
            $freeExistance=0;
        }

        if($hideKala){
            $hideKala=1;
        }else{
            $hideKala=0;
        }
        if($activePishKharid){
            $activePishKharid=1;
        }else{
            $activePishKharid=0;
        }

        if($overLine1 or $overLine==1){
            $overLine=1;
        }else{
            $overLine=0;
        }

        if($callOnSale){
            $callOnSale=1;
        }else{
            $callOnSale=0;
        }

        if($zeroExistance){
            $zeroExistance=1;
        }else{
            $zeroExistance=0;
        }

        $maxSaleOfAll=0;
        $webSpecialSettings=DB::table("NewStarfood.dbo.star_webSpecialSetting")->select('maxSale')->get();
        foreach ($webSpecialSettings as $special) {
            $maxSaleOfAll=$special->maxSale;
        }
        $checkExistance = DB::select("SELECT * FROM NewStarfood.dbo.star_GoodsSaleRestriction where productId=".$productId);
        if((count($checkExistance)>0)){
            DB::update("UPDATE NewStarfood.dbo.star_GoodsSaleRestriction  SET overLine=".$overLine.",callOnSale=".$callOnSale.",zeroExistance=".$zeroExistance.",
            hideKala=".$hideKala.",freeExistance=".$freeExistance.",activeTakhfifPercent=".$showTakhfifPercent.",costLimit=".$costLimit."
            ,costError='$costErrorContent',costAmount=$costAmount,inforsType=$inforsType,activePishKharid=$activePishKharid,alarmAmount=$alarmAmount  WHERE productId=".$productId);
        }else{
            DB::insert("INSERT INTO NewStarfood.dbo.star_GoodsSaleRestriction(maxSale, minSale, productId,overLine,callOnSale,zeroExistance,hideKala,activeTakhfifPercent) VALUES(".$maxSaleOfAll.", 1, ".$productId.",".$overLine.",".$callOnSale.",".$zeroExistance.",".$hideKala.",".$showTakhfifPercent.")");
        }
        return Response::json($hideKala);
    }



    
    public function changeKalaPic(Request $request) {
        $kalaId=$request->get('kalaId');
        $picture1=$request->file('firstPic');
        $picture2=$request->file('secondPic');
        $picture3=$request->file('thirthPic');
        $picture4=$request->file('fourthPic');
        $picture5=$request->file('fifthPic');
        $filename1="";
        $filename2="";
        $filename3="";
        $filename4="";
        $filename5="";
        if($picture1){
        $filename1=$picture1->getClientOriginalName();
        $filename1=$kalaId.'_1.'.'jpg';
        $picture1->move("resources/assets/images/kala/",$filename1);
        }
        if($picture2){
        $filename2=$picture2->getClientOriginalName();
        $filename2=$kalaId.'_2.'.'jpg';
        $picture2->move("resources/assets/images/kala/",$filename2);
        }
        if($picture3){
        $filename3=$picture3->getClientOriginalName();
        $filename3=$kalaId.'_3.'.'jpg';
        $picture3->move("resources/assets/images/kala/",$filename3);
        }
        if($picture4){
        $filename4=$picture4->getClientOriginalName();
        $filename4=$kalaId.'_4.'.'jpg';
        $picture4->move("resources/assets/images/kala/",$filename4);
        }
        if($picture5){
        $filename5=$picture5->getClientOriginalName();
        $filename5=$kalaId.'_5.'.'jpg';
        $picture5->move("resources/assets/images/kala/",$filename5);
        }
        DB::insert("INSERT INTO NewStarfood.dbo.starPicAddress(goodId,picAddress,picAddress2,picAddress3,picAddress4,picAddress5) VALUES(".$kalaId.",'".$filename1."','".$filename2."','".$filename3."','".$filename4."','".$filename5."')");
        return Response::json('good');
    }



   public function addStockToList(Request $request){
        $kalaId=$request->post("kalaId");
        $stockIds=$request->post("addedStockToList");
        $removableStocks=$request->post("removeStockFromList");
        if($stockIds){
            foreach ($stockIds as $stockId) {
                DB::insert("INSERT INTO NewStarfood.dbo.star_addedStock (productId, stockId)
                VALUES(".$kalaId.",".$stockId.")");
            }
        }
        if($removableStocks){
            foreach ($removableStocks as $stockId) {
                DB::delete("DELETE from NewStarfood.dbo.star_addedStock where stockId=".$stockId);
            }
        }
        return Response::json('good');
    }


   public function getAllKalas(Request $request) {
        $mainKalaId=$request->post("mainKalaId");
        $allKalas=DB::select("SELECT * FROM Shop.dbo.PubGoods WHERE GoodName!='' and GoodSn!=0 and GoodSn not in( Select mainId from NewStarfood.dbo.star_assameKala join Shop.dbo.PubGoods on PubGoods.GoodSn=star_assameKala.assameId)");

        return Response::json($allKalas);
    }


     public function addKalaToList(Request $request){
        $mainKalaId=$request->get("mainKalaId");
        $addableKalas=$request->get('addedKalaToList');
        $removeableKalas=$request->get('removeKalaFromList');

        if($addableKalas){
            foreach ($addableKalas as $kalaId) {
                list($kalaId,$title)=explode('_',$kalaId);
                DB::insert("INSERT INTO NewStarfood.dbo.star_assameKala (mainId, assameId)
                VALUES(".$mainKalaId.",".$kalaId.")");
            }
        }

       if($removeableKalas){
            //delete data from Group
           foreach ($removeableKalas as $kalaId) {
             if($kalaId !="on"){
              list($id,$title)=explode('_',$kalaId);
             DB::delete("DELETE FROM NewStarfood.dbo.star_assameKala WHERE assameId=".$id." and mainId=".$mainKalaId);
                }
            }
        }
        return Response::json($removeableKalas);

    }


  public function addOrDeleteKalaFromSubGroup(Request $request) {
        $addbles=$request->get("addables");
        $kalaId=$request->get("ProductId");
        $x=0;
        $removables=$request->get('removables');
        if($addbles){
            foreach ($addbles as $addble) {
                list($subGroupId,$firstGroupId)=explode('_',$addble);
                $exitsanceResult=DB::table("NewStarfood.dbo.star_add_prod_group")
                ->where('firstGroupId',$firstGroupId)
                ->where('secondGroupId',$subGroupId)->where('product_id',$kalaId)->get();
                if(count($exitsanceResult)<1){
                    $x=1;
                    DB::table("NewStarfood.dbo.star_add_prod_group")
                    ->insert(['firstGroupId'=>$firstGroupId,'product_id'=>$kalaId,
                    'secondGroupId'=>$subGroupId,'thirdGroupId'=>0,'fourthGroupId'=>0]);
                }

            }
        }

        if($removables){
            foreach ($removables as $removable) {
            list($subGroupId,$firstGroupId)=explode('_',$removable);
            $exitsanceResult=DB::table("NewStarfood.dbo.star_add_prod_group")
                ->where('firstGroupId',$firstGroupId)
                ->where('secondGroupId',$subGroupId)->where('product_id',$kalaId)->get();
                if(count($exitsanceResult)>0){
                    $x=2;
                    DB::table("NewStarfood.dbo.star_add_prod_group")->where("secondGroupId",$subGroupId)->where('product_id',$kalaId)->delete();
                }

        }
    }
        // return Response::json('done');
     return Response::json($kalaId);
    }



 public function setDescribeKala(Request $request){
        $kalaId=$request->post('kalaId');
        $discription=$request->post('discription');
        $checkDiscription=DB::select("SELECT COUNT(id) as countDiscription from NewStarfood.dbo.star_desc_product where GoodSn=".$kalaId);
        $checkDisc=0;
        foreach ($checkDiscription as $checkDisc) {
            $checkDisc=$checkDisc->countDiscription;
        }
        if($checkDisc>0){
        DB::update("UPDATE NewStarfood.dbo.star_desc_product set descProduct='".$discription."' WHERE GoodSn=".$kalaId);
        }else{
            DB::insert("INSERT INTO NewStarfood.dbo.star_desc_product  VALUES('".$discription."',".$kalaId.")");
        }
        return Response::json("good");
    }
    public function getManagerByLine(Request $request){
        $lineId=$request->get("lineId");
        $managers=DB::select("SELECT * FROM CRM.dbo.crm_admin where SaleLineId=$lineId");
        return Response::json($managers);
    }

    public function getEmployeeInfo(Request $request){
        $adminId=$request->get('adminId');
        $adminInfo=DB::table("CRM.dbo.crm_admin")->where('id',$adminId)->get()[0];
        return Response::json($adminInfo);
    }

}


