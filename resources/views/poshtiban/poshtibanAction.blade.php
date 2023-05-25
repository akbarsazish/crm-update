@extends('layout')
@section('content')
<style>
@media only screen and (max-width: 920px){
    .contentHeader {
         height: 15% !important;
     }
}
</style>

<div class="container-fluid containerDiv">
    <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                <fieldset class="border rounded mt-5 sidefieldSet">
                    <legend  class="float-none w-auto legendLabel mb-0"> عملکرد: {{$exactAdminInfo->name.' '.$exactAdminInfo->lastName}} </legend>
                </fieldset>
            </div>
            <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                 <div class="row contentHeader">
                    <div class="form-group col-sm-2 mt-2">
                        <input type="hidden" id="adminId" value="{{$adminId}}">
                    </div> 
                    <div class="form-group col-sm-2 mt-2">
                    </div> 
                    <div class="col-sm-8 text-start">
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#selfHistoryModal"> تاریخچه عملکرد </button>
                        <button class="btn btn-primary btn-sm" id="showEmtiyazHistoryBtn"> تاریخچه امتیاز <i class="fa fa-history" aria-hidden="true"></i> </button>
                        <button class="btn btn-primary btn-sm" type="button" id="totalEmtyazBtn"> جمع کل امتیازات </button>
                        <input type="hidden" id="adminSn" value="{{$adminId}}">
                    </div>
                </div>
                <div class="row mainContent">
                        <div class="col-lg-12 px-0 pd-0">
                    <div class="forChangeState text-center" id="salesExpertTask" style=" height: 500px !important; overflow-y: scroll !important; display: block !important;">
                        <div class="accordion accordion-flush AmalKardAccordion" id="accordionFlushExample" >
                            <!-- item1 -->
                            @foreach($specialBonuses as $base)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        @if($base->id==1 or $base->id==4 or $base->id==7 or $base->id==10) onclick="getTodayBuyAghlamPoshtiban({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseOne" @endif
                                        @if($base->id==21) onclick="getTodayBuyAghlamDriver({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseOne" @endif
                                        @if($base->id==2 or $base->id==5 or $base->id==8 or $base->id==11) onclick="getAllBuyMoneyTodayPoshtiban({{$adminId}})" data-bs-target="#flush-collapseTwo"  @endif
                                        @if($base->id==3 or $base->id==6 or $base->id==9) onclick="getTodayPoshtibanBuy({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseThree" @endif
                                        @if($base->id==23) onclick="getTodayStrongService({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseFour" @endif
                                        @if($base->id==26) onclick="getTodayStrongService({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseFive" @endif
                                        @if($base->id==27) onclick="getTodayStrongService({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseSix" @endif
                                        @if($base->id==29) onclick="getTodayDriverFactors({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseFifteen" @endif
                                        @if($base->id==30) onclick="" data-bs-target="#flush-collapseSixteen" @endif
                                        >
                                        {{$base->BaseName}} امروز
                                        <span class="count" @if($base->id==11) id="new_install_today"   @endif>
                                            @if($base->id==3 or $base->id==6 or $base->id==9 ) {{$base->count_New_buy_Today}} @endif
                                            @if($base->id==1 or $base->id==4 or $base->id==7 or $base->id==10 or $base->id==21) {{$base->count_aghlam_today}} @endif
                                            @if($base->id==2 or $base->id==5 or $base->id==8 or $base->id==11) {{number_format($base->sum_today_money/10)}}@endif
                                            @if($base->id==23) {{number_format($base->countToday_StrongService)}} @endif
                                            @if($base->id==26) {{number_format($base->today_MediumService)}} @endif
                                            @if($base->id==27) {{number_format($base->today_WeakService)}} @endif
                                            @if($base->id==29) {{number_format($base->count_All_Factor_Today)}} @endif
                                            @if($base->id==30) {{number_format($base->sum_today_money/10)}} @endif
                                        </span>
                                    </button>
                                    </h2>
                                    <div class="accordion-collapse collapse"
                                    @if($base->id==1 or $base->id==4 or $base->id==7 or $base->id==10 or $base->id==21)  id="flush-collapseOne"      @endif
                                    @if($base->id==14)  id="flush-collapseTwo"    @endif
                                    @if($base->id==3 or $base->id==6 or $base->id==9)  id="flush-collapseThree" @endif
                                    @if($base->id==23)  id="flush-collapseFour"   @endif
                                    @if($base->id==26)  id="flush-collapseFive"   @endif
                                    @if($base->id==27)  id="flush-collapseSix"   @endif
                                    @if($base->id==29)  id="flush-collapseFifteen"   @endif
                                    @if($base->id==30)  id="flush-collapseSixteen"   @endif
                                    data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body pe-0">
                                            <div class="row mb-2 me-1 rounded bg-primary text-white text-center">
                                                <div class="col-9 col-sm-9"> هر {{number_format($base->limitAmount)}} {{$base->BaseName}} {{$base->Bonus}} امتیاز  </div>
                                                <div class="col-3 col-sm-3">@if($base->id==11) {{(int)($base->count_All_New_buys/$base->limitAmount)*$base->Bonus}} @endif
                                                    @if($base->id==1 or $base->id==4 or $base->id==7 or $base->id==10) {{(int)($base->count_aghlam_today/$base->limitAmount)*$base->Bonus}} @endif
                                                    @if($base->id==2 or $base->id==5 or $base->id==8 or $base->id==11) {{(int)($base->sum_today_money/10/$base->limitAmount)*$base->Bonus}} @endif
                                                    @if($base->id==3 or $base->id==6 or $base->id==9) {{(int)($base->count_New_buy_Today/$base->limitAmount)*$base->Bonus}}@endif 
                                                    @if($base->id==23) {{(int)($base->count_New_buy_Today/$base->limitAmount)*$base->Bonus}}@endif 
                                                    @if($base->id==26) {{(int)($base->count_New_buy_Today/$base->limitAmount)*$base->Bonus}}@endif 
                                                    @if($base->id==27) {{(int)($base->count_New_buy_Today/$base->limitAmount)*$base->Bonus}}@endif 
                                                    @if($base->id==29) {{(int)($base->today_all_Factor/$base->limitAmount)*$base->Bonus}}@endif 
                                                    @if($base->id==30) {{number_format((int)(($base->sum_today_money/10)/$base->limitAmount)*$base->Bonus)}}@endif 
                                                </div>
                                            </div>
                                            <div    @if($base->id==11) id="new_customer_today_div" @endif
                                                    @if($base->id==3 or $base->id==6 or $base->id==9) id="new_buy_today_div"     @endif
                                                    @if($base->id==1 or $base->id==4 or $base->id==7 or $base->id==10 or $base->id==21) id="today_aghlam_list" @endif
                                                    @if($base->id==2 or $base->id==5 or $base->id==8 or $base->id==11) id="today_mablagh_list"  @endif 
                                                    @if($base->id==23) id="today_strong_list" @endif
                                                    @if($base->id==26) id="today_strong_list" @endif
                                                    @if($base->id==27) id="today_meium_list"  @endif
                                                    @if($base->id==29) id="today_factor_list" @endif
                                                    @if($base->id==30) id="today_driverMoney_list" @endif
                                                    >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- item -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <!-- کلیک شود تا نمایش داده شود -->
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                @if($base->id==11) onclick="getAllNewInstallSelf({{$adminId}},{{$base->Bonus}},{{$base->limitAmount}})" data-bs-target="#flush-collapseSeven"    @endif
                                                @if($base->id==1 or $base->id==4 or $base->id==7 or $base->id==10) onclick="getAllBuyAghlamPoshtiban({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})"  data-bs-target="#flush-collapseEight"    @endif
                                                @if($base->id==21) onclick="getAllBuyAghlamDriver({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})"  data-bs-target="#flush-collapseEight"    @endif
                                                @if($base->id==2 or $base->id==5 or $base->id==8 or $base->id==11) onclick="getAllBuyMoneyPoshtiban({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})"  data-bs-target="#flush-collapseNine"  @endif
                                                @if($base->id==3 or $base->id==6 or $base->id==9) onclick="getAllNewBuyPoshtiban({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseTeen"  @endif
                                                @if($base->id==23) onclick="getAllNewBuyPoshtiban({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseEleven"  @endif
                                                @if($base->id==26) onclick="getAllNewBuyPoshtiban({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseTwelv"  @endif
                                                @if($base->id==27) onclick="getAllNewBuyPoshtiban({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseTherteen"  @endif
                                                @if($base->id==29) onclick="getAllFactorDriver({{$adminId}},{{"'".$emptydate."'"}},{{$base->limitAmount}})" data-bs-target="#flush-collapseFourteen"  @endif 
                                                @if($base->id==30) onclick="" data-bs-target="#flush-collapseSeventeen"  @endif  aria-expanded="false">
                                                {{$base->BaseName}}
                                        <!-- نمایش مقادر اساس -->
                                            <span class="count" 
                                                @if($base->id==11) id="all-installs"   @endif
                                                @if($base->id==1) id="all-aqlam"       @endif 
                                                @if($base->id==2 or $base->id==5 or $base->id==8 or $base->id==11) id="all-mablagh"     @endif 
                                                @if($base->id==3 or $base->id==6 or $base->id==9) id="all-buys"        @endif> 
                                                @if($base->id==11) {{$base->count_All_Install}}     @endif 
                                                @if($base->id==1 or $base->id==4 or $base->id==7 or $base->id==10  or $base->id==21) {{$base->count_All_aghlam}}      @endif 
                                                @if($base->id==2 or $base->id==5 or $base->id==8 or $base->id==11) {{number_format($base->sum_all_money/10)}}      @endif 
                                                @if($base->id==3 or $base->id==6 or $base->id==9) {{$base->count_All_New_buys}} @endif 
                                                @if($base->id==23) {{$base->count_All_StrongService}} @endif 
                                                @if($base->id==26) {{$base->count_All_MediumService}} @endif 
                                                @if($base->id==27) {{$base->count_All_WeakService}} @endif 
                                                @if($base->id==29) {{$base->count_All_Factor}} @endif 
                                                @if($base->id==30) {{number_format($base->sum_all_money/10)}} @endif 
                                            </span>
                                        </button>
                                    </h2>
                                    <div  class="accordion-collapse collapse" 
                                        @if($base->id==12)  id="flush-collapseSeven"    @endif 
                                                @if($base->id==1 or $base->id==4 or $base->id==7 or $base->id==10  or $base->id==21)  id="flush-collapseEight"     @endif 
                                                @if($base->id==2 or $base->id==5 or $base->id==8 or $base->id==11)  id="flush-collapseNine"   @endif 
                                                @if($base->id==3 or $base->id==6 or $base->id==9)  id="flush-collapseTeen"    @endif 
                                                @if($base->id==23)  id="flush-collapseEleven"    @endif 
                                                @if($base->id==26)  id="flush-collapseTwelv"    @endif 
                                                @if($base->id==27)  id="flush-collapseTherteen"    @endif 
                                                @if($base->id==29)  id="flush-collapseFourteen"    @endif 
                                                @if($base->id==30)  id="flush-collapseSeventeen"    @endif 
                                                data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body pe-0">
                                            <div class="row mb-2 me-1 rounded bg-primary text-white text-center"> 
                                                <div class="col-9 col-sm-9">  هر {{number_format($base->limitAmount)}} {{$base->BaseName}} {{$base->Bonus}} امتیاز  </div>
                                                <div class="col-3 col-sm-3">@if($base->id==11) {{(int)($base->count_All_Install/$base->limitAmount)*$base->Bonus}} @endif
                                                    @if($base->id==1 or $base->id==4 or $base->id==7 or $base->id==10  or $base->id==21) {{(int)($base->count_All_aghlam/$base->limitAmount)*$base->Bonus}} @endif
                                                    @if($base->id==2 or $base->id==5 or $base->id==8 or $base->id==11) {{(int)($base->sum_all_money/10/$base->limitAmount)*$base->Bonus}} @endif
                                                    @if($base->id==3 or $base->id==6 or $base->id==9) {{(int)($base->count_All_New_buys/$base->limitAmount)*$base->Bonus}} @endif 
                                                    @if($base->id==23) {{(int)($base->count_All_StrongService/$base->limitAmount)*$base->Bonus}} @endif 
                                                    @if($base->id==26) {{(int)($base->count_All_MediumService/$base->limitAmount)*$base->Bonus}} @endif 
                                                    @if($base->id==27) {{(int)($base->count_All_WeakService/$base->limitAmount)*$base->Bonus}} @endif
                                                    @if($base->id==29) {{(int)($base->count_all_Factor/$base->limitAmount)*$base->Bonus}} @endif
                                                    @if($base->id==30) {{number_format((int)(($base->sum_all_money/10)/$base->limitAmount)*$base->Bonus)}} @endif </div>
                                                <input type="hidden" id="firstDateFilter">
                                                <input type="hidden" id="secondDateFilter">
                                            </div>
                                            <div @if($base->id==11) id="all_new_install" @endif
                                                    @if($base->id==1 or $base->id==4 or $base->id==7 or $base->id==10  or $base->id==21) id="all_aghlam_list" @endif
                                                    @if($base->id==2 or $base->id==5 or $base->id==8 or $base->id==11) id="all_mablagh_list" @endif
                                                    @if($base->id==3 or $base->id==6 or $base->id==9) id="all_new_buys_list" @endif 
                                                    @if($base->id==23) id="allStrongService_list" @endif 
                                                    @if($base->id==26) id="allMediumService_list" @endif 
                                                    @if($base->id==27) id="allWeakService_list" @endif
                                                    @if($base->id==29) id="all_factor_list" @endif
                                                    @if($base->id==30) id="sum_driverAllMoney_list" @endif
                                                    
                                                    ></div>
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
</div>


<!-- Modal for total  Emtyaz -->
<div class="modal fade dragableModal" id="totalEmtyaz" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="totalEmtyazLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h6 class="modal-title" id="totalEmtyazLabel">  جمع کل امتیاز:  </h6>
                </div>
                <div class="modal-body">
                    <div class="totalEmteyaz-container">
                        @if($poshtibanType!=4)
                            <div class="totalEmteyaz-item"> نصب خالص : <b> {{$count_All_Install}} </b>  </div>
                            <div class="totalEmteyaz-item"> امتیاز نصب :  <b>{{$bonus_All_Install}} </b>  </div>
                            <div class="totalEmteyaz-item"> امتیازات اضافی :  <b>{{$all_monthly_bonuses}} </b> </div>  
                            <div class="totalEmteyaz-item"> اقلام:  <b>{{$count_All_aghlam}} </b> </div>
                            <div class="totalEmteyaz-item"> امتیاز اقلام:  <b>{{$bonus_All_aghlam}} </b> </div>
                            <div class="totalEmteyaz-item"> مبلغ خرید :  <b>{{number_format($sum_all_money/10)}} </b></div>  
                            <div class="totalEmteyaz-item"> امتیاز مبلغ خرید :  <b>{{$bonus_all_money}} </b> </div>
                            <div class="totalEmteyaz-item"> خرید اولیه:   <b>{{$count_All_New_buys}} </b> </div>
                            <div class="totalEmteyaz-item"> امتیاز خرید اولیه :  <b>{{$bonus_All_New_buys}} </b></div>  
                            <div class="totalEmteyaz-item"> تارگت های نصب :  </div>  
                            <div class="totalEmteyaz-item"> هیچ تارگت تکمیل نشده است</div>  
                            <div class="totalEmteyaz-item"> تارگت های اقلام :  </div>  
                            <div class="totalEmteyaz-item"> هیچ تارگت تکمیل نشده است</div>  
                            <div class="totalEmteyaz-item"> هیچ تارگت تکمیل نشده است</div>   
                            <div class="totalEmteyaz-item"> تارگت های خرید : </div>  
                            <div class="totalEmteyaz-item"> هیچ تارگت تکمیل نشده است</div>   
                            <div class="totalEmteyaz-item"> تارگت های مبلغ : </div> 
                        @else
                            <div class="totalEmteyaz-item">امتیاز سرویس دور:  <b class="elemValue">{{$driverBonusInfo["all_Strong_Service_Bonus"]}} </b>  </div>
                            <div class="totalEmteyaz-item">امتیاز سرویس متوسط:  <b class="elemValue">{{$driverBonusInfo["all_Medium_Service_Bonus"]}} </b>  </div>
                            <div class="totalEmteyaz-item">امتیاز سرویس نزدیک:  <b class="elemValue">{{$driverBonusInfo["all_Weak_Service_Bonus"]}} </b> </div> 
                           
                           
                            <div class="totalEmteyaz-item"> امتیاز تارگت اقلام:  <b class="elemValue target">{{$driverBonusInfo["aghlamComTgBonus"]}} </b></div> 
                            <div class="totalEmteyaz-item"> امتیاز تارگت مبلغ:  <b class="elemValue target">{{$driverBonusInfo["monyComTgBonus"]}} </b></div> 
                            <div class="totalEmteyaz-item">امتیاز تارگت فاکتور ها:   <b class="elemValue target">{{$driverBonusInfo["countFactorComTgBonus"]}} </b> </div>
                           
                            <div class="totalEmteyaz-item bg-info totalEmtyazDiv">مجموع کل امتیازات : <b class="elemValue">{{$driverBonusInfo["all_bonus_since_Empty"]}} </b></div>


                        @endif
                    </div>

                    <div class="targetSection totalEmteyaz-container">
                    <div class="totalEmteyaz-item">تارگت سرویس دور:  <b class="elemValue target">{{$driverBonusInfo["strongServiceComTg"]}} </b></div>  
                            <div class="totalEmteyaz-item">تارگت سروریس متوسط:  <b class="elemValue target">{{$driverBonusInfo["mediumServiceComTg"]}} </b></div>  
                            <div class="totalEmteyaz-item">تارگت سرویس نزدیک:  <b class="elemValue target">{{$driverBonusInfo["weakServiceComTg"]}} </b></div> 
                            <div class="totalEmteyaz-item">تارگت اقلام:  <b class="elemValue target">{{$driverBonusInfo["aghlamComTg"]}} </b></div> 
                            <div class="totalEmteyaz-item">تارگت مبلغ:  <b class="elemValue target">{{$driverBonusInfo["monyComTg"]}} </b></div> 
                            <div class="totalEmteyaz-item">تارگت فاکتور ها:   <b class="elemValue target">{{$driverBonusInfo["countFactorComTg"]}} </b> </div>
                    </div>
             </div>
       </div>
    </div>

    

    <div class="modal fade notScroll" id="customerDashboard" data-bs-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close btn-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel"> داشبورد </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <span class="fw-bold fs-4"  id="dashboardTitle" style="display:none;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <Button class="btn btn-sm buttonHover crmButtonColor float-end  mx-2" id="openAddCommentModal" type="button" value="" name="" > کامنت <i class="fas fa-comment fa-lg"> </i> </Button>
                            <form action="https://starfoods.ir/crmLogin" target="_blank"  method="get">
                                <input type="text" id="customerSn" style="display: none" name="psn" value="" />
                                <input type="text" style="display: none" name="otherName" value="{{trim(Session::get('username'))}}" />
                                <Button class="btn btn-sm buttonHover crmButtonColor float-end" type="submit"> ورود جعلی  <i class="fas fa-sign-in fa-lg"> </i> </Button>
                            </form>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-2">
                                    <div class="form-outline" style="padding-bottom:1%">
                                        <label class="dashboardLabel form-label">کد</label>
                                        <input type="text" class="form-control form-control-sm noChange" id="customerCode" value="">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-outline " style="padding-bottom:1%">
                                        <label class="dashboardLabel form-label">نام و نام خانوادگی</label>
                                        <input type="text" class="form-control form-control-sm noChange" id="customerName"  value="علی حسینی" >
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> تعداد فاکتور </label>
                                        <input type="text" class="form-control form-control-sm noChange" id="countFactor">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> شماره های تماس </label>
                                        <input class="form-control noChange" type="text" id="mobile1" >
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label">نام کاربری</label>
                                        <input class="form-control noChange" type="text" id="username" >
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label">رمز کاربری</label>
                                        <input class="form-control noChange" type="text" id="password" >
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-12 col-md-12">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> آدرس </label>
                                        <input type="text" class="form-control form-control-sm noChange" id="customerAddress" value="آدرس">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="mb-3" style="width:350px;">
                                    <label for="exampleFormControlTextarea1" class="form-label fw-bold">یاداشت</label>
                                    <textarea class="form-control" id="customerProperty" onblur="saveCustomerCommentProperty(this)" rows="6"></textarea>
                                </div>
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
                                    <table class="homeTables factor crmDataTable tableSection4 table table-bordered table-striped table-sm">
                                        <thead  style="position: sticky;top: 0;">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام راننده</th>
                                                <th>مبلغ </th>
                                                <th>مشاهد جزئیات </th>
                                            </tr>
                                        </thead>
                                        <tbody  id="factorTable">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="homeTables crmDataTable buyiedKala tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="position: sticky;top: 0;">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> نام کالا</th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="goodDetail"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="userLoginInfo1" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="homeTables crmDataTable basketKala tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="position: sticky;top: 0;">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> نام کالا</th>
                                                    <th>تعداد </th>
                                                    <th>فی</th>
                                                </tr>
                                            </thead>
                                            <tbody id="basketOrders">
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
                                        <table class="homeTables crmDataTable returnedFactor tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="position: sticky;top: 0;">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th>نوع پلتفورم</th>
                                                <th>مرورگر</th>
                                            </tr>
                                            </thead>
                                            <tbody id="customerLoginInfoBody">
                                            <tr>
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

                            <div class="row c-checkout rounded-3 tab-pane" id="returnedFactors1"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="homeTables crmDataTable comments tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="position: sticky;top: 0;">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام راننده</th>
                                                <th>مبلغ </th>
                                            </tr>
                                            </thead>
                                            <tbody id="returnedFactorsBody">
                                            <tr>
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

                            <div class="c-checkout tab-pane" id="comments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="homeTables crmDataTable nazarSanji tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="position: sticky;top: 0;">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> کامنت</th>
                                                    <th> کامنت بعدی</th>
                                                    <th> تاریخ بعدی </th>
                                                </tr>
                                            </thead>
                                            <tbody id="customerComments"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="c-checkout tab-pane" id="assesments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="homeTables crmDataTable myCustomer tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="position: sticky;top: 0;">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> کامنت</th>
                                                    <th> برخورد راننده</th>
                                                    <th> مشکل در بارگیری</th>
                                                    <th> کالاهای برگشتی</th>
                                                </tr>
                                            </thead>
                                            <tbody id="customerAssesments"  ></tbody>
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
    </div>
    </div>




    <div class="modal fade" id="selfHistoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-fullscreen" role="document" >
        <div class="modal-content">
        <div class="modal-header py-2">
            <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
            <h5 class="modal-title" id="exampleModalLabel">تاریخچه عملکرد  {{$exactAdminInfo->name.' '.$exactAdminInfo->lastName}}  </h5>
        </div>
            <div class="modal-body">
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
                                        <span class="content"> مبلغ خالص خرید: </span> @if($history->factorAllMoney!=1){{number_format($history->factorAllMoney/10)}} @else 0 @endif  تومان
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content"> مبلغ خرید  برگشتی ماه قبل   : </span> @if($history->lastMonthReturnedAllMoney!=1){{number_format($history->lastMonthReturnedAllMoney/10)}} @else 0 @endif  تومان
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content">  تاریخ  : </span> {{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($history->timeStamp))->format('Y/m/d')}}
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content">  نرخ رشد : </span> {{$history->meanIncrease*100}} %
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
                                        <span class="content">  تعداد خرید برگشتی : </span> {{number_format($history->countReturnedFactor)}}
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content">  تعدا خرید ماه قبل : </span> {{number_format($history->countLastMonthFactor)}}
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
                                        <span class="content">   آخرین تارگت تکمیل شده مبلغ خرید : </span> {{$history->lastComMoneyTg}}
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content">   آخرین تارگت تکمیل شده اقلام: </span> {{$history->lastComAghlamTg}}
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content">  امتیاز نصب :  </span> {{$history->installBonus}}
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content">  امتیاز خرید اولیه:  </span> {{$history->primeBuyBonus}}
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content">  امتیاز مبلغ خرید:  </span> {{$history->totalMoneyBonus}}
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content">  امتیاز اقلام کالا : </span> {{$history->totalAghlamBonus}}
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content">  امتیاز تارگت نصب : </span>  {{$history->lastComInstallTgB}}
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content">  امتیاز تارگت خرید اولیه : </span> {{$history->lastComPrimBuyTgB}}
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content">  امتیاز تارگت مبلغ: </span> {{$history->lastComMoneyTgB}}
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content">  امتیاز تارگت اقلام خرید :  </span> {{$history->lastComAghlamTgB}}
                                    </div>
                                    <div class="actionHistoryItem">
                                        <span class="content"> امتیاز اضافی :   </span> {{$history->extraBonus}}
                                    </div>
                                    
                                    <div class="actionHistoryItem">
                                        <span class="content"> مجموع امتیازات :   </span> {{$history->allBonus}}
                                    </div>
                                </div>
                            </fieldset>  <br> <br>
                        @endforeach
                    </div>
                </div>
            </div>
         </div>

    <!-- Modal for factor detail-->
    <div class="modal fade" id="viewFactorDetail" tabindex="0" data-bs-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog   modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel">جزئیات فاکتور</h5>
                </div>
                <div class="modal-body" id="readCustomerComment">
                    <div class="container">
                        <div class="row" style=" border:1px solid #dee2e6; padding:10px">
                                <h4 style="padding:10px; border-bottom: 1px solid #dee2e6; text-align:center;">فاکتور فروش </h4>
                                <div class="col-sm-6">
                                    <table class="crmDataTable table table-borderless" style="background-color:#dee2e6">
                                        <tbody>
                                        <tr>
                                            <td>تاریخ فاکتور:</td>
                                            <td id="factorDate"></td>
                                        </tr>
                                        <tr>
                                            <td>مشتری:</td>
                                            <td id="customerNameFactor"></td>
                                        </tr>
                                        <tr>
                                            <td>آدرس:</td>
                                            <td id="customerAddressFactor"> </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-6">
                                    <table class="table table-borderless" style="background-color:#dee2e6">
                                        <tbody>
                                            <tr>
                                                <td>تلفن :</td>
                                                <td id="customerPhoneFactor"></td>
                                            </tr>
                                        <tr>
                                            <td>کاربر :</td>
                                            <td id="Admin"></td>
                                        </tr>
                                        <tr>
                                            <td>شماره فاکتور :</td>
                                            <td id="factorSnFactor"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <table id="strCusDataTable"  class='crmDataTable dashbordTables table table-bordered table-striped table-sm' style="background-color:#dee2e6">
                                    <thead  style="position: sticky;top: 0;">
                                    <tr>
                                        <th scope="col">ردیف</th>
                                        <th scope="col">نام کالا </th>
                                        <th scope="col">تعداد/مقدار</th>
                                        <th scope="col">واحد کالا</th>
                                        <th scope="col">فی (تومان)</th>
                                        <th scope="col">مبلغ (تومان)</th>
                                    </tr>
                                    </thead>
                                    <tbody id="productList"></tbody>
                                </table>
                            </div>

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
                <div class="modal-header">
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
                        <div class="col-lg-4 fw-bold">
                            <label for="tahvilBar">علت تماس</label>
                            <select class="form-select form-select-sm" name="callReason">
                                <option value="firstInstall"> نصب اولیه  </option>
                                <option value="firstFollowUp">  پیگیری  </option>
                                <option value="secondFollowUp"> پیگیری 2 </option>
                                <option value="toGetFirstOrder"> سفارش گیری  </option>
                                <option value="toGetSecondOrder"> سفارش گیری 2 </option>
                                <option value="cutOff"> قطع ارتباط </option>
                            </select>
                            <input type="hidden" name="" id="">
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


<!-- Modal for adding Emtyaz -->
<div class="modal fade" id="creditSetting" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="creditSettingLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header py-2">
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
<div class="modal fade" id="adminEmtyazHistory" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="emtyazHistoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-2">
          <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
          <h6 class="modal-title" id="emtyazHistoryLabel"> تاریخچه امتیاز </h6>
      </div>
        <div class="modal-body">
                  <table class="table-bordered">
                        <thead>
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
                        <tbody id="adminEmtyasHistoryBody">
                                <tr>
                                    <td>1</td>
                                    <td> احمد پور </td>
                                    <td> ادمین </td>
                                    <td> 10 </td>
                                    <td> 8 </td>
                                    <td> سرعت و دقت در کار باعث افزایش امتیاز گردید</td>
                                    <td>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editingEmtyaz"> <i class="fa fa-edit"></i>  </button>
                                    </td>
                                </tr>
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
<div class="modal fade" id="editingEmtyaz" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editingEmtyazLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
          <h6 class="modal-title" id="editingEmtyazLabel"> ویرایش امتیاز </h6>
      </div>
      <div class="modal-body">
            
        <form action="{{url('/editEmtiyazHistory')}}" id="editingEmtyazForm" method="get">
            <input type="text" name="adminId" id="adminId" value="{{$adminId}}">
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





<script>
    $("#salesExpert").on("click", ()=>{
       $("#salesExpertTask").toggleClass("forChangeState");
    });

    $("#advancedSearchBtn").on("click", ()=>{
        $("#advancedSearch").toggleClass("searchAdvance");
    });

    $("#totalEmtyazBtn").on("click", ()=>{
        if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#totalEmtyaz").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#totalEmtyaz").modal("show");
    })

    $(".target:contains('هیچکدام')").css("color", "red");
    
  
</script>

@endsection



