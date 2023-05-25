@extends('layout')
@section('content')
<style> 
@media (max-width:920px){
	.salesExpertMobile{
		margin-top:22% !important;
	}
}


.fa-dashboard:hover{
        color:rgb(251, 162, 54)
    }
 
    #chartdiv {
    width: 100%;
    height: 500px;
    text-align: center;
    }

    #ohclChart {
      width: 100%;
      height: 500px;
      max-width: 100%;
      text-align: right;
    }
	
#waitToDashboard {
	margin:0 auto;
	padding:20px;
	}
.amalKardGrid {
  display: grid;
  grid-template-columns: auto auto auto;
  padding: 5px;
}
.amalkard-item {
  padding: 8px;
  font-size: 14px;
  text-align: center;
  border-radius:6px;
 background-color:#b6d5f3;
 margin:5px;
text-align:right;
}
	
</style>
    <div class="container-fluid containerDiv">
         <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 sideBar employeeActionSidebar">
                    <fieldset class="border rounded mt-5 sidefieldSet">
                      <legend  class="float-none w-auto legendLabel mb-0"> عملکرد کارمندان </legend>
                       @if(hasPermission(Session::get("asn"),"amalkardReportN") > -1)
                        <form action="{{url('/getPersonals')}}" id="getPersonalsForm" method="get" style="display:inline">
                          @if(hasPermission(Session::get("asn"),"amalkardReportN") > -1)
                            <div class="form-check">
                                <input class="form-check-input p-2 float-end" type="radio" name="personal" value="all" id="karbarnRadioBtn">
                                <label class="form-check-label me-4" for="assesPast">همه</label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"managerreportN") > -1)
                            <div class="form-check">
                                <input class="form-check-input p-2 float-end" type="radio" name="personal" value="1" id="karbarnRadioBtn">
                                <label class="form-check-label me-4" for="assesPast">مدیران</label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"HeadreportN") > -1)
                            <div class="form-check">
                                <input class="form-check-input p-2 float-end" type="radio" name="personal" value="2" id="karbarnRadioBtn">
                                <label class="form-check-label me-4" for="assesPast">سرپرستان</label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"poshtibanreportN") > -1)
                            <div class="form-check">
                                <input class="form-check-input p-2 float-end" type="radio" name="personal" value="p2" id="settingAndTargetRadio">
                                <label class="form-check-label me-4" for="assesPast">پشتیبانها</label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"bazaryabreportN") > -1)
                            <div class="form-check">
                                <input class="form-check-input p-2 float-end" type="radio" name="personal" value="b3">
                                <label class="form-check-label me-4" for="assesPast">بازاریابها</label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"reportDriverN") > -1)
                            <div class="form-check">
                                <input class="form-check-input p-2 float-end" type="radio" name="personal"  value="d4" id="settingAndTargetRadio">
                                <label class="form-check-label me-4" for="assesPast">راننده ها</label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"amalkardReportN") > -1)
                            <div class="form-group col-lg-12">
                                <input type="text" name="searchTerm" placeholder="جستجو" class="form-control form-control-sm publicTop" id="searchAdminNameCode"/>
                            </div>
                              <div class="form-group col-sm-6 col-lg-6 col-6 mb-1 mt-2">
                                  <button class='btn btn-primary btn-sm text-warning' type="submit"> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                              </div>
                            @endif
                        </form>
                        @endif
                      
                          <div class="col-lg-6 col-sm-6 col-6" id="bazaryabInfo">
                              <form action="{{url('/saleExpertActionInfo')}}" method="get">
                                  <input type="hidden" id="subBazaryabId" name="subId">
                                    @if((hasPermission(Session::get("asn"),"amalkardReportN") > -1) )
                                      <button class="btn btn-sm btn-primary" disabled id="subListDashboardBtn">   جزئیات <i class="fa fa-info-circle" aria-hidden="true"></i> </button>
                                    @endif
                                </form>
                          </div>
                      
                            <div class="col-lg-12" id="poshtibanInfo" style="display:none">
                                <form action="{{url('/poshtibanActionInfo')}}" method="get">
                                    <input type="text" style="display:none" id="PoshtibanId" name="subPoshtibanId">
                                    <button class="btn btn-sm btn-primary" disabled id="subListDashboardBtnPoshtiban">   جزئیات <i class="fa fa-info-circle" aria-hidden="true"></i> </button>
                                </form>
                            </div>
                    </fieldset>
                  </div>
                <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                    <div class="row contentHeader">
                        <div class="col-lg-12 text-start">
                            <input type="text" name="" id="adminSn" style="display: none">
                            @if((hasPermission(Session::get("asn"),"amalkardReportN") > -1) )
                              <button class='enableBtn btn-sm btn btn-primary mx-1 text-warning' id="openkarabarDashboard" type="button">عملکرد <i class="fas fa-balance-scale fa-lg"></i></button>
                              <button class='enableBtn btn-sm btn btn-primary mx-1 text-warning' id="chart" type="button" data-toggle="modal" data-bs-target="#karbarChart">نمودار عملکرد <i class="fas fa-bar-chart fa-lg"></i></button>
                            @endif
                          </div>
                    </div>
                    <div class="row mainContent">
    <!-- start of low level manager or bazaryab -->
                        

<!-- karbaran action code start -->
        
                    <div class="row px-0 mx-0" id="karbaranActionContainer">
                        <div class="col-sm-12 px-0">
                            <table class="table table-bordered table-hover table-striped myDataTable" id="tableGroupList">
                                <thead class="tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>نام کاربر</th>
                                        <th>نقش کاربری</th>
                                        <th class="descriptionForMobile">توضیحات</th>
                                        <th>انتخاب</th>
                                    </tr>
                                </thead>
                                <tbody class="tableBody" id="adminList">
                                    @forelse ($admins as $admin)
                                      <tr onclick="setAdminStuffForAdmin(this,{{$admin->adminTypeId}},{{$admin->driverId}}); selectTableRow(this);">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$admin->name." ".$admin->lastName}}</td>
                                        <td>{{$admin->adminType}}</td>
                                        <td class="descriptionForMobile"></td>
                                        <td>
                                          <input class="mainGroupId" type="radio" name="AdminId[]" value="{{$admin->id.'_'.$admin->adminTypeId}}">
                                        </td>
                                      </tr>
                                    @empty

                                    @endforelse
                                </tbody>
                            </table> 

                            <div class="grid-today rounded-2">
                                <div class="today-item">ساعت ورود: <span id="loginTimeToday"></span>  </div>
                                <div class="today-item"> کامنت های امروز: <span id="countCommentsToday"></span>  </div>
                                <div class="today-item"> فاکتور های امروز: <span id="countFactorsToday"></span>  </div>  
                                <div class="today-item"> مشتریان: <span id="countCustomersToday"></span>  </div>
                            </div>

                            <table class="table table-bordered table-hover table-sm myDataTable" id="tableGroupList">
                                <thead class="tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>نام مشتری</th>
                                        <th>ساعت کامنت </th>
                                        <th> فاکتور</th>
                                    </tr>
                                </thead>
                                <tbody class="tableBody" id="adminCustomers"  style="height:250px !important;">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
             
            <div class="row contentFooter"> </div>
            </div>
        </div>
    </div>



    {{-- modal for karabarn action  --}}
            <div class="modal fade dragableModal" id="karbarAction" data-bs-keyboard="false"  data-bs-backdrop="static"  aria-hidden="true">
              <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close" style="display: inline; background-color:red;"></button>
                    <h5 class="modal-title" style="text-align: center;">عملکرد <span id="adminNameModal"></span></h5>
                  </div>
                  <div class="modal-body">
                    <div class="row rounded-5 shadow bg-light">
                      <div class="col-lg-9 col-md-9 col-sm-12">
                        <div class="amalKardGrid">
                        <div class="amalkard-item"> تاریخ تخصیص مشتری : <span id="assignCustomerDateAll" > </span>  </div>
                        <div class="amalkard-item"> تعداد مشتری ها   : <span id="countCustomerAll"> </span>  </div>  
                        <div class="amalkard-item"> کل فاکتور فروش : <span id="countFactorsAll"> </span>  </div>
                        <div class="amalkard-item"> جمع کل فروش : <span id="allMoneyFactorAll"> </span>  </div>
                        <div class="amalkard-item"> فاکتور های برگشتی : <span id="countReturnedFactorAll"> </span>  </div>  
                        <div class="amalkard-item"> مبلغ فاکتور های برگشتی : <span id="allMoneyReturnedFactorAll" > </span>  </div>
                        <div class="amalkard-item"> روزهای که وارد CRM نشده : <span id="notlogedInAll" > </span>  </div>
                          <div class="amalkard-item"> فاکتور های ماه قبل این کارتابل : <span id="lastMonthAllFactorMoneyAll" > </span>  </div>
                          <div class="amalkard-item" style="font-weight: bold; color:red;">   فاکتور های برگشتی ماه قبل این کارتابل :  <span id="lastMonthAllFactorMoneyReturnedAll" > </span></div>
                        </div>
                        <h6 class="text-primary">عملکرد مشتریان تخصیصی در ماه قبل </h6>
                        <table class="table table-bordered table-striped" >
                          <thead style="background: linear-gradient(#b6d5f3, #b6d5f3, #b6d5f3) !important; color:#000 ! important;">
                            <tr>
                              <th> ردیف </th>
                              <th style="width:133px">تعداد مشتری</th>
                              <th>فاکتورها</th>
                              <th>مبلغ فاکتورها </th>
                              <th>برگشتی </th>
                              <th style="width:133px">جمع کل مبلغ </th>
                            </tr>
                          </thead>
                          <tbody id="lastMonthActionsAll">
                          </tbody>
                        </table>
                      </div>
                      <div class="col-lg-3 col-md-3 col-sm-12">
                        <label class="dashboardLabel form-label">یاداشت  </label>
                        <textarea class="form-control" id="comment" rows="3" style="background-color:#b6d5f3" ></textarea>
                        <label class="dashboardLabel form-label"> توضیحات  </label>
                        <textarea class="form-control" id="comment" rows="4" style="background-color:#b6d5f3" disabled></textarea>
                      </div>
                    </div> <hr>

                    <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                      <div class="col-sm-12">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs">
                          <li><a class="active" data-toggle="tab" style="font-size:16px; font-weight:bold; color:#000;"  href="#adminHistoryInfo"> تاریخچه عملکرد </a></li>
                          <li><a  data-toggle="tab" style="font-size:16px; font-weight:bold; color:#000;"  href="#publicAmalkardInfo"> عملکرد کارمندان </a></li>
                          <li><a  data-toggle="tab" style="font-size:16px; font-weight:bold; color:#000;"  href="#personalAmalkardInfo"> عملکرد شخصی </a></li>
                        </ul>
                      </div>
                      <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                        <!-- تب تاریخچه اطلاعات شخصی -->
                        <div class="row c-checkout rounded-3 tab-pane active" id="adminHistoryInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                          <div class="col-sm-12">
                            <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                              <thead class="tableHeader">
                                <tr>
                                  <th> ردیف</th>
                                  <th> تعداد مشتری  </th>
                                  <th> خرید کردها  </th>
                                  <th>  تعداد فاکتور فروش </th>
                                  <th>  مبلغ برگشتی  </th>
                                  <th>  خالص کل فاکتور فروش  </th>
                                  <th>خالص خرید ماه قبلی مشتریان </th>
                                  <th>میانگین رشد  </th>
                                  <th>م بدون کامنت </th>
                                  <th>ک انجام نشده </th>
                                  <th>کامنت </th>
                                </tr>
                              </thead>
                              <tbody id="factorTable" class="tableBody">
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <!-- تب اطلاعات عمومی -->
                        <div class="row c-checkout rounded-3 tab-pane" id="publicAmalkardInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                          <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="amalKardGrid">
                              <div class="amalkard-item"> تاریخ تخصیص مشتری : <span id="assignCustomerDatePub" > </span>  </div>
                              <div class="amalkard-item"> تعداد مشتری ها   : <span id="countCustomerPub"> </span>  </div>  
                              <div class="amalkard-item"> کل فاکتور فروش : <span id="countFactorsPub"> </span>  </div>
                              <div class="amalkard-item"> جمع کل فروش : <span id="allMoneyFactorPub"> </span>  </div>
                              <div class="amalkard-item"> فاکتور های برگشتی : <span id="countReturnedFactorPub"> </span>  </div>  
                              <div class="amalkard-item"> مبلغ فاکتور های برگشتی : <span id="allMoneyReturnedFactorPub" > </span>  </div>
                              <div class="amalkard-item"> روزهای که وارد CRM نشده : <span id="notlogedInPub" > </span>  </div>
                              <div class="amalkard-item"> فاکتور های ماه قبل این کارتابل : <span id="lastMonthAllFactorMoneyPub" > </span>  </div>
                              <div class="amalkard-item" style="font-weight: bold; color:red;">   فاکتور های برگشتی ماه قبل این کارتابل :  <span id="lastMonthAllFactorMoneyReturnedPub" > </span></div>
                            </div>
                            <h6 class="text-primary">عملکرد مشتریان تخصیصی در ماه قبل </h6>
                            <table class="table table-bordered table-striped" >
                              <thead style="background: linear-gradient(#b6d5f3, #b6d5f3, #b6d5f3) !important; color:#000 ! important;">
                                <tr>
                                  <th> ردیف </th>
                                  <th style="width:133px">تعداد مشتری</th>
                                  <th>فاکتورها</th>
                                  <th>مبلغ فاکتورها </th>
                                  <th>برگشتی </th>
                                  <th style="width:133px">جمع کل مبلغ </th>
                                </tr>
                              </thead>
                              <tbody id="lastMonthActionsPub">
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <!-- تب اطلاعات شخصی -->

                      <div class="row c-checkout rounded-3 tab-pane" id="personalAmalkardInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="amalKardGrid">
                            <div class="amalkard-item"> تاریخ تخصیص مشتری : <span id="assignCustomerDate" > </span>  </div>
                            <div class="amalkard-item"> تعداد مشتری ها   : <span id="countCustomer"> </span>  </div>  
                            <div class="amalkard-item"> کل فاکتور فروش : <span id="countFactors"> </span>  </div>
                            <div class="amalkard-item"> جمع کل فروش : <span id="allMoneyFactor"> </span>  </div>
                            <div class="amalkard-item"> فاکتور های برگشتی : <span id="countReturnedFactor"> </span>  </div>  
                            <div class="amalkard-item"> مبلغ فاکتور های برگشتی : <span id="allMoneyReturnedFactor" > </span>  </div>
                            <div class="amalkard-item"> روزهای که وارد CRM نشده : <span id="notlogedIn" > </span>  </div>
                            <div class="amalkard-item"> فاکتور های ماه قبل این کارتابل : <span id="lastMonthAllFactorMoney" > </span>  </div>
                            <div class="amalkard-item" style="font-weight: bold; color:red;">   فاکتور های برگشتی ماه قبل این کارتابل :  <span id="lastMonthAllFactorMoneyReturned" > </span></div>
                          </div>
                          <h6 class="text-primary">عملکرد مشتریان تخصیصی در ماه قبل </h6>
                          <table class="table table-bordered table-striped" >
                            <thead style="background: linear-gradient(#b6d5f3, #b6d5f3, #b6d5f3) !important; color:#000 ! important;">
                              <tr>
                                <th> ردیف </th>
                                <th style="width:133px">تعداد مشتری</th>
                                <th>فاکتورها</th>
                                <th>مبلغ فاکتورها </th>
                                <th>برگشتی </th>
                                <th style="width:133px">جمع کل مبلغ </th>
                              </tr>
                            </thead>
                            <tbody id="lastMonthActions">
                            </tbody>
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

            <div class="modal fade" id="readDiscription" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-scrollable modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    <div class="modal-body" style="background-color: #d2e9ff;">
                        <h3 id="discription"></h3>
                    </div>
                </div>
            </div>

            {{-- modal for karabarn action  --}}
            <div class="modal fade" id="karbarChart" data-bs-keyboard="false"  data-bs-backdrop="static"  aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-xl" >
                    <div class="modal-content"  style="background-color:#d4d4d4;">
                        <div class="modal-header" style="border-bottom:1px solid rgb(7, 42, 214);">
                            <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close" style="display: inline; background-color:red;"></button>
                            <h5 class="modal-title" style="text-align: center;">نمودار عملکرد </h5>
                        </div>

                        <div class="modal-body"  style="background-color:#d4d4d4;;">
                            <div class="c-checkout container-fluid" style="background-image: linear-gradient(to right, #ffffff,#3fa7ef,#3fa7ef); margin:0.2% 0; margin-bottom:0; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                                <div class="col-sm-6" style="margin: 0; padding:0;">
                                    <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                                        <li><a class="active" data-toggle="tab" style="color:black;"  href="#siteAdmin"> عملکرد ماهای قبل </a></li>
                                        <li><a data-toggle="tab" style="color:black;"  href="#moRagiInfo">  عملکرد کاربران   </a></li>
                                        <li><a data-toggle="tab" style="color:black;"  href="#kalaInfo"> عملکرد </a></li>
                                    </ul>
                                </div>
                                    <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="background-color:#f5f5f5; width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-4 col-sm-12 fs-6">
                                                            <div class="row mt-1">
                                                                <span style="width:30px; height:30px; background-color:#67b7dc; margin-right:11px;"></span> &nbsp;  عملکرد 
                                                            </div> <br>
                                                            <div class="row">
                                                                <span style="width:30px; height:30px; background-color:#6794dc;  margin-right:11px;"></span>  &nbsp; عملکرد کاربران دیگر
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-8 col-md-8 col-sm-12 card">
                                                            <div id="chartdiv"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="row c-checkout rounded-3 tab-pane active" id="siteAdmin" style="background-color:#f5f5f5; width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                            <div class="col-lg-3 col-md-3 col-sm-3"></div>
                                            <div class="col-lg-9 col-md-9 col-sm-9">
                                                <div id="ohclChart"></div>
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















<link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet" />
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.12.1/sorting/persian.js"></script> -->



<script>
    am5.ready(function() {

    // Create root element
    var root = am5.Root.new("chartdiv");
    root._logo.dispose();

    // Set themes
    root.setThemes([
      am5themes_Animated.new(root)
    ]);

    // Create chart
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
      panX: true,
      panY: false,
      wheelX: "panX",
      wheelY: "zoomX",
      layout: root.verticalLayout
    }));

    // Add scrollbar
    chart.set("scrollbarX", am5.Scrollbar.new(root, {
      orientation: "horizontal"
    }));

    var data = [{
      "country": "کاربر فعلی",
      "year2004": 3.5,
      "year2005": 4.2
    }, {
      "country": "دیگر کاربران",
      "year2004": 1.7,
      "year2005": 3.1
    }, {
      "country": "کابرفعلی",
      "year2004": 2.8,
      "year2005": 2.9
    }, {
      "country": "کاربران دیگر ",
      "year2004": 2.6,
      "year2005": 2.3
    }, {
      "country": "کاربرفعلی ",
      "year2004": 1.4,
      "year2005": 2.1
    }, {
      "country": "کاربران دیگر",
      "year2004": 2.6,
      "year2005": 4.9
    }];

    // Create axes
    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
      categoryField: "country",
      renderer: am5xy.AxisRendererX.new(root, {}),
      tooltip: am5.Tooltip.new(root, {
        themeTags: ["axis"],
        animationDuration: 200
      })
    }));

    xAxis.data.setAll(data);

    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
      min: 0,
      renderer: am5xy.AxisRendererY.new(root, {})
    }));

    // Add series

    var series0 = chart.series.push(am5xy.ColumnSeries.new(root, {
      name: "Income",
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: "year2004",
      categoryXField: "country",
      clustered: false,
      tooltip: am5.Tooltip.new(root, {
        labelText: "2004: {valueY}"
      })
    }));

    series0.columns.template.setAll({
      width: am5.percent(80),
      tooltipY: 0
    });


    series0.data.setAll(data);


    var series1 = chart.series.push(am5xy.ColumnSeries.new(root, {
      name: "Income",
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: "year2005",
      categoryXField: "country",
      clustered: false,
      tooltip: am5.Tooltip.new(root, {
        labelText: "2005: {valueY}"
      })
    }));

    series1.columns.template.setAll({
      width: am5.percent(50),
      tooltipY: 0,

    });

    series1.data.setAll(data);

    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));


    // Make stuff animate on load
    chart.appear(1000, 100);
    series0.appear();
    series1.appear();

    }); // end am5.ready()

    </script>






{{-- script of OHCL Chart --}}

<script>
    am5.ready(function() {
    // Create root element
    var root = am5.Root.new("ohclChart");
    root._logo.dispose();

    // Set themes
    root.setThemes([
      am5themes_Animated.new(root)
    ]);

    function generateChartData() {
      var chartData = [];
      var firstDate = new Date();
      firstDate.setDate(firstDate.getDate() - 1000);
      firstDate.setHours(0, 0, 0, 0);
      var value = 1200;
      for (var i = 0; i < 5000; i++) {
        var newDate = new Date(firstDate);
        newDate.setDate(newDate.getDate() + i);

        value += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
        var open = value + Math.round(Math.random() * 16 - 8);
        var low = Math.min(value, open) - Math.round(Math.random() * 5);
        var high = Math.max(value, open) + Math.round(Math.random() * 5);

        chartData.push({
          date: newDate.getTime(),
          value: value,
          open: open,
          low: low,
          high: high,
        });
      }
      return chartData;
    }

    var data = generateChartData();

    // Create chart
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
      focusable: true,
      panX: true,
      panY: true,
      wheelX: "panX",
      wheelY: "zoomX"
    }));


    // Create axes
    var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
      maxDeviation:0.5,
      groupData: true,
      baseInterval: { timeUnit: "day", count: 1 },
      renderer: am5xy.AxisRendererX.new(root, {pan:"zoom"}),
      tooltip: am5.Tooltip.new(root, {})
    }));

    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
      maxDeviation:1,
      renderer: am5xy.AxisRendererY.new(root, {pan:"zoom"})
    }));


    var color = root.interfaceColors.get("background");

    // Add series
    var series = chart.series.push(am5xy.OHLCSeries.new(root, {
      fill: color,
      calculateAggregates: true,
      stroke: color,
      name: "CRM",
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: "value",
      openValueYField: "open",
      lowValueYField: "low",
      highValueYField: "high",
      valueXField: "date",
      lowValueYGrouped: "low",
      highValueYGrouped: "high",
      openValueYGrouped: "open",
      valueYGrouped: "close",
      legendValueText: "open: {openValueY} low: {lowValueY} high: {highValueY} close: {valueY}",
      legendRangeValueText: "{valueYClose}",
      tooltip: am5.Tooltip.new(root, {
      pointerOrientation: "horizontal",
    labelText: "open: {openValueY}\nlow: {lowValueY}\nhigh: {highValueY}\nclose: {valueY}"
      })
    }));


    // Add cursor
    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
      xAxis: xAxis
    }));
    cursor.lineY.set("visible", false);

    // Stack axes vertically
    chart.leftAxesContainer.set("layout", root.verticalLayout);

    // Add scrollbar
    var scrollbar = am5xy.XYChartScrollbar.new(root, {
      orientation: "horizontal",
      height: 50
    });
    chart.set("scrollbarX", scrollbar);

    var sbxAxis = scrollbar.chart.xAxes.push(am5xy.DateAxis.new(root, {
      groupData: true,
      groupIntervals: [{ timeUnit: "week", count: 1 }],
      baseInterval: { timeUnit: "day", count: 1 },
      renderer: am5xy.AxisRendererX.new(root, {
        opposite: false,
        strokeOpacity: 0
      })
    }));

    var sbyAxis = scrollbar.chart.yAxes.push(am5xy.ValueAxis.new(root, {
      renderer: am5xy.AxisRendererY.new(root, {})
    }));

    var sbseries = scrollbar.chart.series.push(am5xy.LineSeries.new(root, {
      xAxis: sbxAxis,
      yAxis: sbyAxis,
      valueYField: "value",
      valueXField: "date"
    }));

    // Add legend
    var legend = yAxis.axisHeader.children.push(
      am5.Legend.new(root, {})
    );

    legend.data.push(series);

    legend.markers.template.setAll({
      width: 10
    });

    legend.markerRectangles.template.setAll({
      cornerRadiusTR: 0,
      cornerRadiusBR: 0,
      cornerRadiusTL: 0,
      cornerRadiusBL: 0
    });

    series.data.setAll(data);
    sbseries.data.setAll(data);

    // Make stuff animate on load
    series.appear(1000);
    chart.appear(1000, 100);

    }); // end am5.ready()
    </script> 
    @stop