@extends('layout')
@section('content')
<style>
    @media only screen and (max-width: 920px){
    .contentHeader {
         height: 16% !important;
     }
}
</style>
<div class="container-fluid containerDiv">
    <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                <fieldset class="border rounded mt-5 sidefieldSet">
                    <legend  class="float-none w-auto legendLabel mb-0"> عملکرد: {{$exactAdminInfo->name.' '.$exactAdminInfo->lastName}}  </legend>
                   
                    <!-- <div class="form-check">
                        <input class="form-check-input p-2 float-end" type="radio" name="settings" id="settingAndTargetRadio">
                        <label class="form-check-label me-4" for="assesPast"> تارگت ها و امتیازات </label>
                    </div> -->
                </fieldset>
                </div>
            <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                <div class="row contentHeader">
                        <div class="col-lg-12 col-sm-12 text-start">
                                <button class="btn btn-primary btn-sm" type="button" onclick="openHistoryModal()"> تاریخچه عملکرد </button>
                                @if(hasPermission(Session::get("asn"),"trazEmployeeReportN") > 1)
                                    <button class="btn btn-primary btn-sm " id="addingEmtyazBtn"> افزودن امتیاز  <i class="fa fa-plus" aria-hidden="true"></i> </button>
                                @endif
                                <input type="hidden" id="adminSn" value="{{$adminId}}">
                                <button class="btn btn-primary btn-sm" id="showEmtiyazHistoryBtn"> تاریخچه امتیاز <i class="fa fa-history" aria-hidden="true"></i> </button>
                                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#totalEmtyaz"> جمع کل امتیازات </button>
                               
                        </div>
                </div>
                <div class="row mainContent">
                <div class=" text-center" id="salesExpertTask" style="height:500px; display:block; overflow-y:scroll; ">
                  <div class="accordion accordion-flush AmalKardAccordion" id="accordionFlushExample" >
                    <!-- item1 -->
                     @foreach($specialBonuses as $base)
                        <div class="accordion-item">
                            <h2 class="accordion-header" 
                            @if($base->id==11) id="flush-headingOne"    @endif
                            @if($base->id==12) id="flush-headingTwo"    @endif
                            @if($base->id==13) id="flush-headingThree"  @endif
                            @if($base->id==14) id="flush-headingFour"   @endif>
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                 @if($base->id==11) onclick="getTodaySelfInstalls({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseOne" @endif
                                 @if($base->id==14) onclick="getTodaySelfBuyToday({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseTwo" @endif
                                 @if($base->id==12) onclick="getTodayBuyAghlamSelf({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseThree" @endif
                                 @if($base->id==13) onclick="getTodayBuyMoneySelf({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseFour"  @endif>
                                     {{$base->BaseName}} امروز
                                    <span class="count" @if($base->id==11) id="new_install_today"   @endif>                                
                                        @if($base->id==11) {{$base->count_New_Install}}             @endif 
                                        @if($base->id==12) {{$base->count_aghlam_today}}            @endif 
                                        @if($base->id==13) {{number_format($base->sum_today_money/10)}}            @endif 
                                        @if($base->id==14) {{$base->count_New_buy_Today}}            @endif 
                                    </span>
                                </button>
                            </h2>
                            <div class="accordion-collapse collapse" 
                            @if($base->id==11)  id="flush-collapseOne"      @endif
                            @if($base->id==14)  id="flush-collapseTwo"      @endif
                            @if($base->id==12)  id="flush-collapseThree"    @endif
                            @if($base->id==13)  id="flush-collapseFour"     @endif
                            data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body p-0" >
                                    <div class="row mb-2 me-1 rounded bg-primary text-white text-center"> 
                                        <div class="col-9 col-sm-9"> هر {{number_format($base->limitAmount)}} {{$base->BaseName}} {{$base->Bonus}} امتیاز  </div>
                                        <div class="col-3 col-sm-3">
                                            @if($base->id==11) {{(int)($base->count_All_New_buys/$base->limitAmount)*$base->Bonus}} @endif
                                            @if($base->id==12) {{(int)($base->count_aghlam_today/$base->limitAmount)*$base->Bonus}} @endif
                                            @if($base->id==13) {{(int)($base->sum_today_money/10/$base->limitAmount)*$base->Bonus}} @endif
                                            @if($base->id==14) {{(int)($base->count_New_buy_Today/$base->limitAmount)*$base->Bonus}}@endif </div>
                                    </div>
                                    <div    @if($base->id==11) id="new_customer_today_div" @endif
                                            @if($base->id==14) id="new_buy_today_div"     @endif
                                            @if($base->id==12) ID="today_aghlam_list" @endif
                                            @if($base->id==13) id="today_mablagh_list"  @endif></div>
                                </div>
                            </div>
                        </div>

                        <!-- item -->
                        <div class="accordion-item">
                            <h2 class="accordion-header"  @if($base->id==11) id="flush-headingTwo" @endif >
                                <!-- کلیک شود تا نمایش داده شود -->
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                
                                        @if($base->id==11) onclick="getAllNewInstallSelf({{$adminId}},{{$base->Bonus}},{{$base->limitAmount}},{{"'".$emptydate."'"}})" data-bs-target="#flush-collapseFive"    @endif
                                        @if($base->id==12) onclick="getAllBuyAghlamSelf({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})"  data-bs-target="#flush-collapseSix"    @endif
                                        @if($base->id==13) onclick="getAllBuyMoneySelf({{$adminId}},{{$base->Bonus}},{{$base->limitAmount}})"  data-bs-target="#flush-collapseSeven"                                                                                       @endif
                                        @if($base->id==14) onclick="getAllNewBuySelf({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseEight"                                                                                       @endif
                                        aria-expanded="false">
                                         {{$base->BaseName}}
                                   <!-- نمایش مقادر اساس -->
                                    <span class="count" 
                                        @if($base->id==11) id="all-installs"    @endif
                                        @if($base->id==12) id="all-aqlam"       @endif 
                                        @if($base->id==13) id="all-mablagh"     @endif 
                                        @if($base->id==14) id="all-buys"        @endif> 
                                        @if($base->id==11) {{$base->count_All_Install}}     @endif 
                                        @if($base->id==12) {{$base->count_All_aghlam}}      @endif 
                                        @if($base->id==13) {{number_format($base->sum_all_money/10)}}      @endif 
                                        @if($base->id==14) {{$base->count_All_New_buys}}    @endif 
                                    </span>
                                </button>
                            </h2>
                            <div  class="accordion-collapse collapse" 
                                @if($base->id==11)  id="flush-collapseFive"     @endif 
                                @if($base->id==12)  id="flush-collapseSix"      @endif 
                                @if($base->id==13)  id="flush-collapseSeven"    @endif 
                                @if($base->id==14)  id="flush-collapseEight"    @endif 
                                data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body p-0">
                                    <div class="row mb-2 me-1 rounded bg-primary text-white text-center"> 
                                        <div class="col-9 col-sm-9">  هر {{number_format($base->limitAmount)}} {{$base->BaseName}} {{$base->Bonus}} امتیاز  </div>
                                        <div class="col-3 col-sm-3">@if($base->id==11) {{(int)($base->count_All_Install/$base->limitAmount)*$base->Bonus}} @endif
                                                                    @if($base->id==12) {{(int)($base->count_All_aghlam/$base->limitAmount)*$base->Bonus}} @endif
                                                                    @if($base->id==13) {{(int)($base->sum_all_money/10/$base->limitAmount)*$base->Bonus}} @endif
                                                                    @if($base->id==14) {{(int)($base->count_All_New_buys/$base->limitAmount)*$base->Bonus}} @endif </div>
                                        <input type="hidden" id="firstDateFilter">
                                        <input type="hidden" id="secondDateFilter">
                                    </div>
                                    <div    @if($base->id==11) id="all_new_install" @endif
                                            @if($base->id==12) id="all_aghlam_list" @endif
                                            @if($base->id==13) id="all_mablagh_list" @endif
                                            @if($base->id==14) id="all_new_buys_div" @endif ></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                <!-- end -->
                </div>
            </div>

                </div>
                <div class="row contentFooter"> </div>
            </div>
    </div>
</div>




    <div class="modal fade notScroll dragableModal" id="customerDashboard" data-bs-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close btn-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel"> داشبورد </h5>
                </div>
                <div class="modal-body py-0">
                    <div class="row">
                          <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="flex-container">
                                    <div style="flex-grow: 1"> کد:  <span id="customerCode"></span> </div>
                                    <div style="flex-grow: 1">  نام و نام خانوادگی : <span id="customerName"> </span>  </div>
                                    <div style="flex-grow: 1"> تعداد فاکتور : <span id="countFactor"> </span>  </div>
                                    <div style="flex-grow: 1"> شماره های تماس :  <span id="mobile1"> </span>  </div>
                                </div>
                                <div class="flex-container">
                                    <div style="flex-grow: 1">  نام کاربری: <span id="username"> </span>  </div>
                                    <div style="flex-grow: 1"> رمز کاربری:   <span  id="password"> </span>  </div>
                                    <div style="flex-grow: 2"> ادرس :   <span id="customerAddress"> </span>  </div>
                                </div>
                            </div>
                          <div class="col-lg-4 col-md-4 col-sm-4 text-start">
                                <button  class="btn btn-sm btn-primary" id="openAddCommentModal" type="button" value="" name="" > کامنت <i class="fas fa-comment fa-lg"> </i> </Button>
                                <form action="https://starfoods.ir/crmLogin" target="_blank"  method="get" style="display:inline;">
                                    <input type="text" id="customerSn" style="display: none" name="psn" value="" />
                                    <input type="text" style="display: none" name="otherName" value="{{trim(Session::get('username'))}}" />
                                    <button class="btn btn-sm btn-primary" type="submit"> ورود جعلی  <i class="fas fa-sign-in fa-lg"> </i> </button>
                                </form>
                                <div class="mb-3">
                                    <label for="exampleFormControlTextarea1" class="form-label float-end mb-0 pb-0">یاداشت</label>
                                    <textarea class="form-control" id="customerProperty" onblur="saveCustomerCommentProperty(this)" rows="2"></textarea>
                                </div>
                          </div>
                    </div>

                    <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                        <div class="col-sm-12" style="margin: 0; padding:0;">
                            <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                                <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری شده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#userLoginInfo1"> کالاهای سبد خرید</a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#customerLoginInfo">ورود به سیستم</a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#returnedFactors1"> فاکتور های برگشت داده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments"> کامنت ها </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments"> نظرسنجی ها</a></li>
                            </ul>
                        </div>
                        <div class="c-checkout tab-content"   style="background-color:#f5f5f5; margin:0;padding:0.3%; border-radius:10px 10px 2px 2px;">
                            <div class="row c-checkout rounded-3 tab-pane active" id="custAddress"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead  class="tableHeader">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام راننده</th>
                                                <th>مبلغ </th>
                                                <th> جزئیات </th>
                                            </tr>
                                        </thead>
                                        <tbody class="tableBody" id="factorTable">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead class="tableHeader">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> نام کالا</th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="tableBody" id="goodDetail">
											</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="userLoginInfo1" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead class="tableHeader">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> نام کالا</th>
                                                    <th>تعداد </th>
                                                    <th>فی</th>
                                                </tr>
                                            </thead>
                                            <tbody class="tableBody" id="basketOrders">
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="customerLoginInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead class="tableHeader">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th>نوع پلتفورم</th>
                                                <th>مرورگر</th>
                                            </tr>
                                            </thead>
                                            <tbody class="tableBody" id="customerLoginInfoBody">
                                        
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="returnedFactors1"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead class="tableHeader">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام راننده</th>
                                                <th>مبلغ </th>
                                            </tr>
                                            </thead>
                                            <tbody class="tableBody" id="returnedFactorsBody">
                                          
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="c-checkout tab-pane" id="comments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead class="tableHeader">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> کامنت</th>
                                                    <th> کامنت بعدی</th>
                                                    <th> تاریخ بعدی </th>
                                                </tr>
                                            </thead>
                                            <tbody class="tableBody" id="customerComments"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="c-checkout tab-pane" id="assesments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead class="tableHeader">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> کامنت</th>
                                                    <th> برخورد راننده</th>
                                                    <th> مشکل در بارگیری</th>
                                                    <th> کالاهای برگشتی</th>
                                                </tr>
                                            </thead>
                                            <tbody class="tableBody" id="customerAssesments"  ></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<div class="modal fade dragableModal" id="selfHistoryModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-fullscreen" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">تاریخچه عملکرد</h5>
        <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div class="row">       
           @foreach($selfHistory as $history)  
              <fieldset class="rounded">
                    <legend  class="float-none w-auto"> عملکرد ({{$loop->iteration}})  {{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($history->timeStamp))->format('Y/m/d')}}  </legend>		  
                        <div class="actionHistory">
                            <div class="actionHistoryItem">
                                <span class="content"> تعداد مشتری : </span> {{$history->countPeople}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content"> تعداد فاکتور : </span> {{$history->countFactor}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content"> تعداد مشتریان خرید کرده : </span> {{$history->countBuyPeople}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content"> مبلغ خالص خرید: </span> @if($history->lastMonthAllMoney!=1){{number_format($history->lastMonthAllMoney)}} @else 0 @endif  تومان
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content"> مبلغ خرید ماه قبل   : </span> @if($history->lastMonthReturnedAllMoney!=1){{number_format($history->lastMonthReturnedAllMoney)}} @else 0 @endif  تومان
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content"> مبلغ برگشت ماه قبل  : </span> 
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  تاریخ  : </span> {{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($history->timeStamp))->format('Y/m/d')}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  نرخ رشد : </span> {{number_format($history->meanIncrease)}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  توضیح خاص : </span> {{$history->comment}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  کامنت داده نشده : </span> {{$history->noCommentCust}}
                            </div>

                            <div class="actionHistoryItem">
                                <span class="content"> کارهای انجام نشده : </span> {{$history->noDoneWork}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  تعداد خرید برگشتی : </span> {{number_format($history->countReturnedFactor)}} تومان
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  تعدا خرید ماه قبل : </span> {{number_format($history->countLastMonthFactor)}} تومان
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  تعداد نصب  : </span> {{$history->allInstall}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  تعداد خرید اولیه  : </span> {{$history->allPrimaryBuy}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content"> تعداد اقلام :   </span> {{$history->allAghlam}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  آخرین تارگیت نصب تکمیل شده : </span> {{$history->lastComInstallTg}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  آخرین تارگت خرید اولیه تکمیل شده:  </span> {{$history->lastComPrimBuyTg}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">   آخرین تارگت تکمیل شده مبلغ خرید : </span> {{$history->lastComAghlamTg}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  امتیاز نصب :  </span> {{$history->lastComMoneyTg}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  امتیاز خرید اولیه:  </span> {{$history->installBonus}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  امتیاز مبلغ خرید:  </span> {{$history->primeBuyBonus}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  امتیاز اقلام کالا : </span> {{$history->totalMoneyBonus}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  امتیاز تارگت نصب : </span>  {{$history->totalAghlamBonus}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  امتیاز تارگت خرید اولیه : </span> {{$history->lastComInstallTgB}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  امتیاز تارگت اقلام خرید :  </span> {{$history->lastComPrimBuyTgB}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content">  امتیاز مبلغ خرید : </span> {{$history->lastComAghlamTgB}}
                            </div>
                            <div class="actionHistoryItem">
                                <span class="content"> امتیاز اضافی :   </span> {{$history->lastComMoneyTgB}}
                            </div>
                            
                            <div class="actionHistoryItem">
                                <span class="content"> مجموع امتیازات :   </span> {{$history->allBonus}}
                            </div>
                        </div>
                    </fieldset>     
             @endforeach
           </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">بستن</button>
      </div>
    </div>
  </div>
</div>


    <!-- Modal for factor detail-->
    <div class="modal fade dragableModal" id="viewFactorDetail" tabindex="0" data-bs-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel">جزئیات فاکتور</h5>
                </div>
                 <div class="modal-body" id="readCustomerComment">
                           <div class="row">
                                 <div class="flex-container">
                                    <div style="flex-grow: 1"> تاریخ فاکتور:  <span id="factorDate"></span> </div>
                                    <div style="flex-grow: 1">  مشتری: <span id="customerNameFactor"> </span>  </div>
                                    <div style="flex-grow: 1"> آدرس: <span id="customerAddressFactor"> </span>  </div>
                                </div>
                                 <div class="flex-container">
                                    <div style="flex-grow: 1"> تلفن : <span > </span id="customerPhoneFactor">  </div>
                                    <div style="flex-grow: 1"> کاربر: <span > </span id="Admin">   </div>
                                    <div style="flex-grow: 1"> شماره فاکتور <span id="factorSnFactor"> </span>  </div>
                                </div>
                            </div>
                            <div class="row">
                                <table id="strCusDataTable"  class='table table-bordered table-striped table-sm'>
                                    <thead class="tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>نام کالا </th>
                                        <th>تعداد/مقدار</th>
                                        <th>واحد کالا</th>
                                        <th>فی (تومان)</th>
                                        <th style="width:111px">مبلغ (تومان)</th>
                                    </tr>
                                    </thead>
                                    <tbody class="tableBody" id="productList"></tbody>
                                </table>
                            </div>
                      </div>
                 </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>






            <!--- modal for adding comments -->
    <div class="modal" id="addComment" data-bs-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog modal-dialog-scrollable ">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="exampleModalLabel"> افزودن کامنت </h5>
                </div>
                <div class="modal-body">
                    <form action="{{url('/addComment')}}" id="addCommentForm" method="get">
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar">نوع تماس</label>
                                <select class="form-select" name="callType">
                                    <option value="1">موبایل</option>
                                    <option value="2">تلفن ثابت</option>
                                    <option value="3">واتساپ</option>
                                    <option value="4">حضوری</option>
                                </select>
                                <input type="text" style="display:none" name="customerIdForComment" id="customerIdForComment">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar" >کامنت </label>
                                <textarea class="form-control" style="position:relative" required name="firstComment" id="firstComment" rows="3" ></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 fw-bold">
                                <label for="tahvilBar" >زمان تماس بعدی </label>
                                    <input class="form-control" autocomplete="off" required name="nextDate" id="commentDate2">
                                    <input class="form-control" autocomplete="off" style="display:none" value="0" required name="mantagheh" id="mantaghehId">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar">کامنت بعدی</label>
                                <textarea class="form-control" name="secondComment" required id="secondComment" rows="5" ></textarea>
                                <input class="form-control" type="text" style="display: none;" name="place" value="customers"/>
                            </div>
                        </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" id="cancelComment">انصراف<i class="fa fa-xmark"></i></button>
                            <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



</div>

<!-- Modal for adding Emtyaz -->
<div class="modal fade dragableModal" id="creditSetting" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="creditSettingLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
          <h6 class="modal-title" id="creditSettingLabel"> افزایش و کاهش امتیاز </h6>
      </div>
      <div class="modal-body">

            <form action="{{url('/addUpDownBonus')}}" id="addingEmtyaz" method="get">
                @csrf
                        <input type="hidden" name="adminId" value="{{$adminId}}">
                        <div class="row">
                                <div class="col-lg-6">
                                    <label for="pwd" class="form-label">کاهش امتیاز </label>
                                    <input type="text" name="negative" class="form-control" id="pwd" placeholder="کاهش امتیاز">
                                </div>
                                <div class="col-lg-6">
                                    <label for="pwd" class="form-label">افزایش امتیاز </label>
                                    <input type="text" name="positive" class="form-control" id="pwd" placeholder="افزایش امتیاز">
                                </div>
                        </div>
                        <div class="row mt-2">
                            <label for="comment">توضیحات </label>
                            <textarea class="form-control" rows="3" id="comment" name="discription"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">بستن <i class="fa fa-xmark"> </i></button>
                            <button type="submit" class="btn btn-primary btn-sm"> ذخیره <i class="fa fa-save"> </i> </button>
                        </div>
                </form>
                            </div>
    </div>
  </div>
</div>




<!-- Modal for listing of history  -->
<div class="modal fade dragableModal" id="adminEmtyazHistory" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="emtyazHistoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
          <h6 class="modal-title" id="emtyazHistoryLabel"> تاریخچه امتیاز </h6>
      </div>
        <div class="modal-body">
                  <table class="table-bordered">
                        <thead class="tableHeader">
                            <tr>
                                <th>ردیف</th>
                                <th>نام کاربر</th>
                                <th>نقش کاربری</th>
                                <th> افزایش امتیاز </th>
                                <th> کاهش امتیاز </th>
                                <th>توضیحات</th>
                                <th>ویرایش</th>
                            </tr>
                        </thead>
                        <tbody class="tableBody" id="adminEmtyasHistoryBody" style="height:255px !important;">
                             
                        </tbody>
                    </table> 
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">بستن <i class="fa fa-xmark"> </i></button>
        <button type="button" class="btn btn-primary btn-sm"> ذخیره <i class="fa fa-save"> </i> </button>
      </div>
    </div>
  </div>
</div>




<!-- Modal for editing  Emtyaz -->
<div class="modal fade dragableModal" id="editingEmtyaz" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editingEmtyazLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
          <h6 class="modal-title" id="editingEmtyazLabel"> ویرایش امتیاز </h6>
      </div>
      <div class="modal-body">
            
        <form action="{{url('/editEmtiyazHistory')}}" id="editingEmtyazForm" method="get">
            <input type="hidden" name="adminId" id="adminId" value="{{$adminId}}">
            <div class="row">
                <div class="col-lg-6">
                        <label for="pwd" class="form-label">کاهش امتیاز </label>
                        <input type="text"  class="form-control" id="negativeEmtiyasEdit" placeholder="کاهش امتیاز" name="negativeEmtiyasEdit">
                        <input type="hidden"  class="form-control" id="historyIDEmtiyasEdit" placeholder="کاهش امتیاز" name="historyIDEmtiyasEdit">
                </div>
                <div class="col-lg-6">
                        <label for="pwd" class="form-label">افزایش امتیاز </label>
                        <input type="text" class="form-control"  id="positiveEmtiyasEdit" placeholder="افزایش امتیاز" name="positiveEmtiyasEdit">
                </div>
            </div>
            <div class="row mt-2">
                <label for="comment">توضیحات </label>
                <textarea class="form-control" rows="3" id="discriptionEmtiyasEdit" name="discriptionEmtiyasEdit"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">بستن <i class="fa fa-xmark"> </i></button>
                <button type="submit" class="btn btn-primary btn-sm"> ذخیره <i class="fa fa-save"> </i> </button>
            </div>
        </form>
    </div>
  </div>
</div>
</div>


<!-- Modal for total  Emtyaz -->
<div class="modal fade dragableModal" id="totalEmtyaz" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="totalEmtyazLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h6 class="modal-title" id="totalEmtyazLabel">  جمع کل امتیاز: {{$all_bonus_since_Empty}} </h6>
                </div>
                <div class="modal-body">
                    <div class="totalEmteyaz-container">
                        <div class="totalEmteyaz-item"> نصب خالص : {{$count_All_Install}}  </div>
                        <div class="totalEmteyaz-item"> امتیاز نصب : {{$bonus_All_Install}}  </div>
                        <div class="totalEmteyaz-item"> امتیازات اضافی : {{$all_monthly_bonuses}} </div>  
                        <div class="totalEmteyaz-item"> اقلام: {{$count_All_aghlam}} </div>
                        <div class="totalEmteyaz-item"> امتیاز اقلام: {{$bonus_All_aghlam}} </div>
                        <div class="totalEmteyaz-item"> مبلغ خرید : {{number_format($sum_all_money/10)}}</div>  
                        <div class="totalEmteyaz-item"> امتیاز مبلغ خرید : {{$bonus_all_money}} </div>
                        <div class="totalEmteyaz-item"> خرید اولیه:  {{$count_All_New_buys}} </div>
                        <div class="totalEmteyaz-item"> امتیاز خرید اولیه : {{$bonus_All_New_buys}}</div>  
                        <div class="totalEmteyaz-item"> تارگت های نصب :  </div>  
                        <div class="totalEmteyaz-item"> هیچ تارگت تکمیل نشده است  </div>  
                        <div class="totalEmteyaz-item"> تارگت های اقلام :  </div>  
                        <div class="totalEmteyaz-item"> هیچ تارگت تکمیل نشده است  </div>  
                        <div class="totalEmteyaz-item"> هیچ تارگت تکمیل نشده است  </div>   
                        <div class="totalEmteyaz-item"> تارگت های خرید : </div>  
                        <div class="totalEmteyaz-item"> هیچ تارگت تکمیل نشده است  </div>   
                        <div class="totalEmteyaz-item"> تارگت های مبلغ : </div> 
                        <div class="totalEmteyaz-item" style="color:red; font-weight:bold;"> جمع کل امتیاز ها :  {{$all_bonus_since_Empty}} </div> 
                    </div>
             </div>
       </div>
    </div>







<script>
    $("#salesExpert").on("click", ()=>{
       $("#salesExpertTask").toggleClass("forChangeState");
    });

    $("#advancedSearchBtn").on("click", ()=>{
        $("#advancedSearch").toggleClass("searchAdvance");
    });
	
	 $("#addingEmtyazBtn").on("click", ()=>{
		   if (!($('.modal.in').length)) {
                $('.modal-dialog').css({
                  top: 0,
                  left: 0
                });
              }
              $('#creditSetting').modal({
                backdrop: false,
                show: true
              });
              
              $('.modal-dialog').draggable({
                  handle: ".modal-header"
                });
        $("#creditSetting").modal("show");
    });
</script>

@endsection



