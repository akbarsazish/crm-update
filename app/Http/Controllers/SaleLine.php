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
class SaleLine extends Controller
{
    //
    public function index(Request $request)
    {
        $saleLines=DB::select("SELECT * FROM CRM.dbo.crm_SaleLine where deleted=0");
        return view("saleLine.saleLine",['saleLines'=>$saleLines]);
    }
    public function addSaleLine(Request $request)
    {
        $lineName=$request->get("name");
        DB::table("CRM.dbo.crm_SaleLine")->insert(['LineName'=>"".$lineName.""]);
        $saleLines=DB::table("CRM.dbo.crm_saleLine")->where("deleted",0)->get();
        return Response::json($saleLines);
    }
    public function getSaleLine(Request $request)
    {
        $saleLineSn=$request->get("saleLineSn");
        $saleLines=DB::table("CRM.dbo.crm_saleLine")->where("SaleLineSn",$saleLineSn)->get();
        return Response::json($saleLines);
    }
    public function editSaleLine(Request $request)
    {
        $lineName=$request->get("name");
        $saleLineSn=$request->get("snSaleLine");
        DB::table("CRM.dbo.crm_SaleLine")->where("SaleLineSn",$saleLineSn)->update(['LineName'=>"".$lineName.""]);
        $saleLines=DB::table("CRM.dbo.crm_saleLine")->where("deleted",0)->get();
        return Response::json($saleLines);
    }
    public function deleteSaleLine(Request $request)
    {
        $saleLineSn=$request->get("saleLineSn");
        DB::table("CRM.dbo.crm_SaleLine")->where("SaleLineSn",$saleLineSn)->update(['deleted'=>1]);
        $saleLines=DB::table("CRM.dbo.crm_saleLine")->where("deleted",0)->get();
        return Response::json($saleLines);
    }
    public function getEmployees(Request $request)
    {
        $headId=$request->get("headId");
        $admins=DB::table("CRM.dbo.crm_admin")->where('bossId',$headId)->where("deleted",0)->get();
        return Response::json($admins);
    }
}