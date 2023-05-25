@extends('layout')
@section('content')
<style>
    table th, td {
        font-size:14px;
    }
    .labelText{
        font-size:12px;
    }

.inActiveBtn{
    display:none;
}

.evcuatedCustomer {
    display:none;
}
.referencialTools {
    display:none;
}
.loginReport, .referencialReport, .inactiveReport {
    display:none;
    width:122px;
}
</style>

<div class="container-fluid containerDiv">
        <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                   <fieldset class="border rounded sidefieldSet">
                        <legend  class="float-none w-auto legendLabel mb-0"> عملکرد مشتریان </legend>
                           @if(hasPermission(Session::get("asn"),"amalkardCustReportN") > 0)
                            <div class="form-check bg-gray">
                                <input class="reportRadio form-check-input p-2 float-end" value="all" type="radio" name="reportRadio" id="allCustomerReportRadio" checked>
                                <label class="form-check-label me-4" for="assesPast"> همه  </label>
                            </div>

                            @if(hasPermission(Session::get("asn"),"loginCustRepN") > 0)
                            <div class="form-check bg-gray">
                                <input class="reportRadio form-check-input p-2 float-end" value="login" type="radio" name="reportRadio" id="customerLoginReportRadio">
                                <label class="form-check-label me-4" for="assesPast">  گزارش ورود </label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"inActiveCustRepN") > 0)
                            <div class="form-check bg-gray">
                                <input class="reportRadio form-check-input p-2 float-end" value="inactive" type="radio" name="reportRadio" id="customerInactiveRadio">
                                <label class="form-check-label me-4" for="assesPast"> غیرفعال </label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"noAdminCustRepN") > 0)
                            <div class="form-check bg-gray">
                                <input class="reportRadio form-check-input p-2 float-end"  value="noAdmin" type="radio" name="reportRadio" id="evacuatedCustomerRadio">
                                <label class="form-check-label me-4" for="assesPast"> فاقد کاربر </label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"returnedCustRepN") > 0)
                            <div class="form-check bg-gray">
                                <input class="reportRadio form-check-input p-2 float-end"  value="returned" type="radio" name="reportRadio" id="referentialCustomerRadio">
                                <label class="form-check-label me-4" for="assesPast"> ارجاعی</label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"returnedCustRepN") > 0)
                            <div class="form-check bg-gray">
                                <input class="reportRadio form-check-input p-2 float-end"  value="newCustomers" type="radio" name="reportRadio" id="newCustomerRadio">
                                <label class="form-check-label me-4" for="assesPast"> مشتریان جدید</label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"returnedCustRepN") > 0)
                            <div class="form-check bg-gray">
                                <input class="reportRadio form-check-input p-2 float-end"  value="customerLocation" type="radio" name="reportRadio" id="customerLocationRadio">
                                <label class="form-check-label me-4" for="assesPast"> موقعیت مشتریان </label>
                            </div>
                            @endif
                            <div class="row" id="allCustomerStaff">
                                <div class="col-sm-12 col-6">
                                    <label for="" class="form-label">موقعیت</label>
                                    <select class="form-select form-select-sm  " id="AllLocationOrNot">
                                        <option value="-1">همه</option>
                                        <option value="1">موقعیت دار </option>
                                        <option value="0">بدون موقعیت</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-6">
                                    <label for="" class="form-label">وضعیت خرید  </label>
                                    <select class="form-select form-select-sm " id="buyStatus">
                                        <option value="-1">همه</option>
                                        <option value="1">خرید کرده </option>
                                        <option value="0">خرید نکرده </option>
                                    </select>
                                </div>

                                <div class="col-sm-12 col-6">
                                    <input type="text" name="firstDate" placeholder="از تاریخ" class="form-control form-control-sm" id="firstDateBuyOrNot" autocomplete="off">
                                </div>
                                <div class="col-sm-12 col-6 mb-2">
                                    <input type="text" name="secondDate" placeholder="تا تاریخ" class="form-control form-control-sm" id="secondDateBuyOrNot" autocomplete="off">
                                </div>
                                <div class="col-sm-12 col-6">
                                    <label class="form-label">وضعیت سبد</label>
                                    <select class="form-select form-select-sm " id="AllBasketOrNot">
                                        <option value="-1">همه</option>
                                        <option value="1"> سبد پر </option>
                                        <option value="0">سبد خالی </option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-6">
                                    <label for="" class="form-label">پشتیبان</label>
                                    <select class="form-select form-select-sm " id="AllByAdmin">
                                        <option value="-1"> همه</option>
                                        <option value="0">بدون پشتیبان</option>
                                        @foreach($admins as $admin)
                                        <option value="{{$admin->id}}"> {{trim($admin->name)}} {{trim($admin->lastName)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12">
                                    <button class='btn btn-primary btn-sm text-warning'  onclick="getAllCustomerInfos()" type="button"> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                                </div>
                            </div>

                        <!-- related to visitor -->
                        <div class="row" id="staffVisitor" style="display:none">
                            
                            <div class="col-lg-12 col-6 mb-1 mt-2">
                                <div class="form-group">
                                    <label class="labelText" for="visitorPlatform">پلتفورم</label>
                                    <select type="text" class="form-control form-control-sm" id="visitorPlatform">
                                        <option value=''>همه</option>
                                        <option value='Android'>اندروید</option>
                                        <option value='iOS'>ios</option>
                                        <option value='Windows'>windows</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12 col-6 mb-1">
                                <div class="form-group">
                                    <label class="labelText" for="LoginDate1">از تاریخ</label>
                                    <input type="text" placeholder="تاریخ" id="LoginDate1" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-lg-12 col-6 mb-1">
                                <div class="form-group">
                                    <label class="labelText" for="LoginDate2">الی تاریخ</label>
                                    <input type="text" placeholder="تاریخ" id="LoginDate2" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-lg-12 col-6 mb-1">
                                <div class="form-group">
                                    <label class="labelText" for="LoginDate2">تعدا ورود از:</label>
                                    <input type="number" placeholder="تعداد" id="LoginFrom" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-lg-12 col-6 mb-1">
                                <div class="form-group">
                                    <label class="labelText" for="LoginDate2">تعداد ورود تا:</label>
                                    <input type="number" placeholder="تعداد" id="LoginTo" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-lg-12 col-6 mb-1">
                                <div class="form-group">
                                    <label class="labelText" for="countSameTime">تعداد همزمان هر مشتری از:</label>
                                    <input type="number" placeholder="تعداد" id="countSameTime" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-lg-12 col-6 mb-1">
                                <div class="form-group">
                                    <label class="labelText" for="countSameTime">تعداد همزمان هر مشتری تا:</label>
                                    <input type="number" placeholder="تعداد" id="countSameTimeTo" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="form-group col-sm-12 mb-1">
                                <button class='btn btn-primary btn-sm text-warning' id="filterAllLoginsBtn" type="button"> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                            </div>
                        </div>

                    <!-- Inactive Customer  -->
                    <div class="row" id="inActiveTools" style="display:none">
                       
                        <div class="form-group col-sm-12 mb-1 mt-3">
                            <label class="form-label">کاربر غیر فعال کننده</label>
                            <select class="form-select form-select-sm" id="inactiverAdmin">
                                <option value="-1"> همه </option>
                                @foreach ($inActiverAdmins as $inActiver)
                                    <option value="{{$inActiver->id}}">{{$inActiver->name.' '.$inActiver->lastName}}</option>  
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-12 mb-1 mt-3">
                            <label class="form-label">وضعیت خرید</label>
                            <select class="form-select form-select-sm" id="boughtState">
                                <option value="-1"> همه </option>
                                <option value="1"> خریده کرده </option>
                                <option value="0"> خرید نکرده </option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12 mb-1">
                            <button class='btn btn-primary btn-sm text-warning' type="button" id="filterInActivesBtn"> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                        </div>
                    </div>

                    <!-- evacuated Customers tools -->
                    <div class="row evcuatedCustomer">
                        <div class="form-group col-sm-12 mb-1">
                            <label class="form-label">وضعیت خرید</label>
                            <select class="form-select form-select-sm" id="buyOrNot">
                                <option value="-1">همه</option>
                                <option value="1">دارد</option>
                                <option value="0">ندارد</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12 mb-1">
                            <input type="text" name=""  placeholder="از تاریخ" class="form-control form-control-sm" id="searchEmptyFirstDate">
                        </div>
                        <div class="form-group col-sm-12 mb-1">
                            <input type="text" name=""  placeholder="تا تاریخ" class="form-control form-control-sm" id="searchEmptySecondDate">
                        </div>
                        <div class="form-group col-sm-12 mb-1">
                            <button class='btn btn-primary btn-sm text-warning' type="button" id="filterNoAdminsBtn"> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                        </div>
                    </div>

                 <!-- referencial tools  -->
                        <div class="row referencialTools">
                            <div class="form-group col-sm-12 mb-1">
                                <label class="form-label">وضعیت خرید</label>
                                <select class="form-select form-select-sm" id="buyState">
                                    <option value="-1"> همه  </option>
                                    <option value="1"> دارد </option>
                                    <option value="0"> ندارد </option>
                                </select>
                            </div>
                            <div class="form-group col-sm-12 mb-2">
                                <label class="form-label">کاربر ارجاع دهنده</label>
                                <select class="form-select form-select-sm" id="returner">
                                    <option value="">همه</option>  
                                    @foreach ($returners as $returner)
                                        <option value="{{$returner->name}}">{{$returner->name.' '.$returner->lastName}}</option>  
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-12 mb-1">
                                <button class='btn btn-primary btn-sm text-warning' id="filterReturnedsBtn" type="button"> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                            </div>
                        </div>
                                         <!-- newCustomers tools  -->
                        <div class="row" id="newCustomerTools" style="display:none">
                            <div class="form-group col-sm-12 mb-1">
                                <label class="form-label">وضعیت خرید</label>
                                <select class="form-select form-select-sm" id="newCustomerBuyState">
                                    <option value="-1"> همه  </option>
                                    <option value="1"> دارد </option>
                                    <option value="0"> ندارد </option>
                                </select>
                            </div>
                            <div class="form-group col-sm-12 mb-2">
                                <label class="form-label">نصب کننده</label>
                                <select class="form-select form-select-sm" id="installer">
                                    <option value="">همه</option>  
                                    @foreach ($admins as $admin)
                                        <option value="{{trim($admin->name)}}">{{$admin->name.' '.$admin->lastName}}</option>  
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-12 mb-1">
                                <button class='btn btn-primary btn-sm text-warning' id="filterNewCustomerBtn" type="button"> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                            </div>
                        </div>
                        <div class="row" id="locationTools" style="display:none">
                            <div class="form-group col-sm-12 mb-1">
                                <button class='btn btn-primary btn-sm text-warning' id="filterLocationBtn" type="button"> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                            </div>
                        </div>

                        @endif

                        <div class="quick-access mt-2">
                            <div class="quick-acess-item"> <span> اسم مشتری : </span><span class="quick-access-label text-danger" id="quick_CustomerName"> </span> </div>
                            <div class="quick-acess-item"> <span>  تعداد فاکتور : </span> <span class="quick-access-label" id="quick_countFactor"> </span> </div>
                            <div class="quick-acess-item"> <span>  آخرین مبلغ خرید : </span> <span class="quick-access-label" id="quick_lastBuyMoney"> </span> </div>
                            <div class="quick-acess-item"> <span>  جمع مبلغ خرید : </span> <span class="quick-access-label" id="quick_BuyAllMoney"> </span> </div>
                            <div class="quick-acess-item"> <span>  وضعیت سبد : </span> <span class="quick-access-label" id="quick_basketState"> </span> </div>
                            <div class="quick-acess-item"> <span>  آخرین خرید : </span> <span class="quick-access-label" id="quick_lastFactDate"> </span></div>
                            <div class="quick-acess-item"> <span>  آخرین ورود : </span> <span class="quick-access-label" id="quick_lastLoginDate"> </span></div>
                            <div class="quick-acess-item"> <span>  آدرس  : </span> <span class="quick-access-label" id="quick_address"> </span></div>
                            <div class="quick-acess-item"> <span>  شماره تماس  : </span> <span class="quick-access-label" id="quick_Phone"> </span> </div>
                        </div>
                    </fieldset>
                </div>

                <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                    <div class="row contentHeader reportListContentHeader"> 
                        <div class="col-sm-8 text-end buttonArea">
                            <div class="row">
                                <div class="col-6 col-sm-2 mt-2 px-2">
                                    <input type="text" name="" placeholder="کد- اسم- شماره تماس" class="form-control form-control-sm " id="searchAllName">
                                </div>
                                <div class="col-6 col-sm-2 mt-2 px-2">
                                    <select class="form-select form-select-sm " id="searchByCity">
                                       <option value="0" hidden> شهر</option>
                                       <option value="0"> همه</option>
                                        @foreach($cities as $city)
                                        <option value="{{$city->SnMNM}}"> {{trim($city->NameRec)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-sm-2 mt-2 px-2">
                                    <select class="form-select form-select-sm " id="searchByMantagheh">
                                    <option value="0">همه</option>
                                    </select>
                                </div>
                                <div class="col-6 col-sm-2 mt-2 px-2" id="orderAll">
                                    <select class="orderReport form-select form-select-sm" id="orderAllCustomers">
                                        <option value="-1">مرتب سازی</option>
                                        <option value="Name">اسم</option>
                                        <option value="lastDate"> تاریخ فاکتور  </option>
                                        <option value="adminName"> کاربر </option>
                                    </select>
                                </div>
                                <div class="col-6 col-sm-2 mt-2 px-2" style="display:none" id="orderLogins">
                                    <select class="orderReport form-select form-select-sm" id="orderLoginCustomers">
                                        <option value="-1">مرتب سازی</option>
                                        <option value="Name">اسم</option>
                                        <option value="adminName">ادمین</option>
                                        <option value="browser"> مرورگر </option>
                                        <option value="platform"> سیستم </option>
                                    </select>
                                </div>
                                <div class="col-6 col-sm-2 mt-2 px-2" style="display:none" id="orderNoAdmins">
                                    <select class="orderReport form-select form-select-sm" id="orderNoAdminCustomers">
                                        <option value="-1">مرتب سازی</option>
                                        <option value="Name">اسم</option>
                                        <option value="PCode"> کد </option>
                                    </select>
                                </div>
                                <div class="col-6 col-sm-2 mt-2 px-2" style="display:none" id="orderInActives">
                                    <select class="orderReport form-select form-select-sm" id="orderInActiveCustomers">
                                        <option value="-1">مرتب سازی</option>
                                        <option value="CustomerName">اسم</option>
                                        <option value="name">کاربر</option>
                                    </select>
                                </div>
                                <div class="col-6 col-sm-2 mt-2 px-2"  style="display:none" id="orderReturn">
                                    <select class="orderReport form-select form-select-sm" id="orderReportCustomers">
                                        <option value="-1">مرتب سازی</option>
                                        <option value="Name">اسم</option>
                                        <option value="returnDate"> تاریخ   </option>
                                        <option value="adminName"> کاربر </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                              <!-- Button trigger modal -->
                         @if(hasPermission(Session::get("asn"),"amalkardCustReportN") > 0)
                            <button class='enableBtn btn btn-sm btn-primary text-warning' type="button"  onclick="openDashboard(this.value)" disabled> داشبورد <i class="fal fa-dashboard"></i></button>
                             <!-- evacuated customer buttons -->
                            <button class='enableBtn btn btn-primary btn-sm text-warning evcuatedCustomer' disabled id="inactiveButton">غیر فعال کردن <i class="fal fa-ban fa-lg"> </i> </button>
                            <input type="text" id="customerSn"  value="" style="display: none;" />
                            <input type="text" id="adminSn"  value="" style="display: none;"/>
                            <!-- referencial customer buttons -->
                            <button class='enableBtn btn btn-primary btn-sm text-warning referencialTools' disabled id="returnComment">علت ارجاع<i class="fal fa-eye fa-lg"> </i> </button>
                        @endif
                         @if(hasPermission(Session::get("asn"),"amalkardCustReportN") > 1)
                            <button class='enableBtn btn btn-sm btn-primary text-warning' id="takhsisButton" disabled>تخصیص کاربر  <i class="fal fa-tasks fa-lg"> </i> </button>
                         @endif
                           
                        </div>
                    </div>
                    <div class="row mainContent">
                        <div class="col-sm-12 px-0 mx-0" style="display:none" id="customerLocationDiv">
                            <div id="map">  </div>
                        </div>
                        <table class='table table-bordered table-striped table-hover myDataTable' id="customerActionTable">
                            <thead class="tableHeader">
                                <tr>
                                    <th>ردیف</th>
                                    <th class="forMobileDisplay">کد</th>
                                    <th>اسم</th>
                                    <th>همراه</th>
                                    <th>تاریخ فاکتور</th>
                                    <th class="forMobileDisplay">کاربر</th>
                                    <th> انتخاب</th>
                                    <th class="forMobileDisplay"> فعال</th>
                                </tr>
                            </thead>
                            <tbody class="select-highlight tableBody" id="allCustomerReportyBody">

                            </tbody>
                        </table>


                   <div class="c-checkout container-fluid" id="loginTosystemReport" style="background-image: linear-gradient(to right, #ffffff,#3fa7ef,#3fa7ef); margin:0.2% 0; margin-bottom:0; padding:0.5% !important; border-radius:10px 10px 2px 2px; display:none;">
                    <div class="col-sm-6" style="margin: 0; padding:0;">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                            <li><a class="active" data-toggle="tab" style="color:black;"  href="#karbarLogin">  گزارش ورود به سیستم (اشخاص)  </a></li>
                            <li><a data-toggle="tab" style="color:black;"  href="#custAddress"> گزارش ورود به سیستم (نموداری) </a></li>
                        </ul>
                    </div>
                    <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                      <div class="row c-checkout rounded-3 tab-pane active" id="karbarLogin" style="background-color:#f5f5f5; width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                       
                             <div class="col-sm-12 px-0">
                                <table class='table table-bordered table-striped table-sm'>
                                    <thead class="tableHeader">
                                        <tr>
                                            <th> ردیف</th>
                                            <th style="width:244px"> نام مشتری</th>
                                            <th> کاربر مربوطه </th>
                                            <th>آخرین ورود</th>
                                            <th>سیستم </th>
                                            <th>مرورگر</th>
                                            <th style="width:77px">تعداد ورود </th>
                                            <th>  همزمان</th>
                                            <th style="width:66px"> انتخاب</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listVisitorBody" class="tableBody">
                                        @foreach ($loginCustomers as $element)
                                            <tr  onclick="setAmalkardStuff(this,{{ $element->PSN}}); selectTableRow(this)">
                                            <td>{{$loop->iteration}} </td>
                                            <td style="width:244px"> {{$element->Name}} </td>
                                            <td>{{$element->adminName}}</td>
                                            <td>{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($element->lastVisit))->format('Y/m/d    h:m:i')}} </td>
                                            <td>{{$element->platform}} </td>
                                            <td>{{$element->browser}} </td>
                                            <td style="width:77px">{{$element->countLogin}} </td>
                                            <td> {{$element->countSameTime}} </td>
                                                    <td  style="width:66px"> <input class="customerList form-check-input" name="customerId" type="radio" value="{{$element->PSN}}"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                         <div class="row c-checkout rounded-3 tab-pane" id="custAddress">
                            <div class="col-sm-12">
                                <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                   <span class="card p-4">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                   <input type="date" class="form-control">
                                                </div>
                                            </div>
                                        </div> <br>
                                        <div class="col-lg-12 col-md-12 col-sm-12 card">
                                             <div id="chartdiv"></div>
                                        </div>
                                    </span>
                                 </div>
                              </div>
                          </div>
                       </div>
                    </div>

                 <!-- in active customer table -->
                 <div class="col-lg-12 px-0"  id="inActiveCustomerTable" style="display:none;">
                     <table class='table table-bordered table-striped px-0'>
                            <thead class="tableHeader">
                                <tr>
                                    <th>ردیف</th>
                                    <th>کد</th>
                                    <th>اسم</th>
                                    <th style="width:99px"> همراه</th>
                                    <th style="width:133px">ت-غیرفعال</th>
                                    <th style="width:133px">ک-غیرفعال</th>
                                    <th> کامنت  </th>
                                    <th>انتخاب</th>
                                </tr>
                            </thead>
                            <tbody class="select-highlight tableBody" id="inactiveCustomerBody">
                            </tbody>
                        </table>
                        <div class="grid-today rounded-2">
                                <div class="today-item"> <span style="color:red; font-weight:bold;">  تاریخ آخرین فاکتور : </span> <span id="loginTimeToday"></span>  </div>
                               
                        </div>
                    </div>

                 <!-- evacuated customer table  -->
                    <div class="col-lg-12 px-0 evcuatedCustomer">
                        <table class='table table-bordered table-striped table-sm px-0'>
                            <thead class="tableHeader">
                                <tr>
                                    <th>ردیف</th>
                                    <th style="width:66px;">کد</th>
                                    <th>اسم</th>
                                    <th style="width:333px;">آدرس  </th>
                                    <th>همراه</th>
                                    <th>آخرین تاریخ </th>
                                    <th>انتخاب</th>
                                </tr>
                            </thead>
                            <tbody class="select-highlight tableBody" id="evacuatedCustomers">
                            </tbody>
                        </table>
                            <div class="grid-today rounded-2">
                                <div class="today-item"><span style="color:red; font-weight:bold;">تاریخ آخرین فاکتور:</span><span id="loginTimeToday"></span></div>
                            </div>
                        </div>

                        <!-- referencial customer table -->
                        <div class="col-lg-12 px-0 referencialTools">
                           <table class='table table-bordered table-striped table-sm px-0'>
                               <thead class="tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th style="width:188px;">اسم</th>
                                        <th style="width:144px;">همراه</th>
                                        <th style="width:133px;">ارجاع دهنده</th>
                                        <th style="width:88px;">تاریخ ارجاع</th>
                                        <th>انتخاب</th>
                                    </tr>
                                </thead>
                                <tbody class="select-highlight tableBody" id="returnedCustomerList">
                                </tbody>
                            </table> 
                            <div class="grid-today rounded-2">
                                <div class="today-item"> <span style="color:red; font-weight:bold;">  کامنت : </span> <span id="loginTimeToday"></span>  </div>
                                <div class="today-item"> <span style="color:red; font-weight:bold;">  تاریخ آخرین خرید : </span> <span id="loginTimeToday"></span>  </div>
                                
                            </div>
                        </div>
<!-- مشتریان جدید -->
                        <div class="col-lg-12 px-0 newCustomersContent">
                           <table class='table table-bordered table-striped table-sm px-0' id="newCustomerList" style="display:none">
                               <thead class="tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th style="width:66px;">کد</th> 
                                        <th style="width:188px;">اسم</th>
                                        <th style="width:166px;">پشتیبان</th>
                                        <th style="width:144px;">همراه</th>
                                        <th style="width:133px;"> تاریخ ثبت </th>
                                        <th style="width:133px;">آخرین تاریخ خرید</th>
                                        <th>انتخاب</th>
                                    </tr>
                                </thead>
                                <tbody class="select-highlight tableBody" id="customerBodyList">
                                </tbody>
                            </table> 
                        </div>
                      </div>
                       <div class="row contentFooter"> 
                            <div class="col-lg-12 text-start">
                                <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getLoginReport('TODAY')"> امروز  : </button>
                                <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getLoginReport('YESTERDAY')"> دیروز : </button>
                                <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getLoginReport('LASTHUNDRED')"> صد تای آخر : 100</button>
                                <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getLoginReport('ALL')"> همه : </button>

                                <button type="button" class="btn btn-sm btn-primary referencialReport" onclick="getReferencialReport('TODAY')"> امروز  : </button>
                                <button type="button" class="btn btn-sm btn-primary referencialReport" onclick="getReferencialReport('YESTERDAY')"> دیروز : </button>
                                <button type="button" class="btn btn-sm btn-primary referencialReport" onclick="getReferencialReport('LASTHUNDRED')"> صد تای آخر : 100 </button>
                                <button type="button" class="btn btn-sm btn-primary referencialReport" onclick="getReferencialReport('ALL')"> همه : </button>

                                <button type="button" class="btn btn-sm btn-primary inactiveReport" onclick="getInactiveReport('TODAY')"> امروز  : </button>
                                <button type="button" class="btn btn-sm btn-primary inactiveReport" onclick="getInactiveReport('YESTERDAY')"> دیروز : </button>
                                <button type="button" class="btn btn-sm btn-primary inactiveReport" onclick="getInactiveReport('LASTHUNDRED')"> صد تای آخر : 100 </button>
                                <button type="button" class="btn btn-sm btn-primary inactiveReport" onclick="getInactiveReport('ALL')"> همه : </button>
                           </div>
                    </div>
             </div>
          </div>
      </div>
    <div class="modal fade" id="customerDashboard" data-bs-keyboard="false" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close btn-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel"> داشبورد </h5>
                </div>
                    <div class="modal-body">
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
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <span class="fw-bold fs-4"  id="dashboardTitle" style="display:none;"></span>
                                @if(hasPermission(Session::get("asn"),"amalkardCustReportN") > -1)
                                    <button class="btn btn-sm btn-primary d-inline openAddCommentModal" id="openAddCommentModal" type="button" value="" name="" style="float:left; display:inline;" > کامنت <i class="fas fa-comment fa-lg"> </i> </button>
                                    <form action="https://starfoods.ir/crmLogin" target="_blank"  method="get" style="display:inine !important;">
                                        <input type="text" id="customerSnLogin" style="display: none" name="psn" value="" />
                                        <button class="btn btn-sm btn-primary d-inline" type="submit" style="float:left;"> ورود جعلی  <i class="fas fa-sign-in fa-lg"> </i> </button>
                                        <input type="text"  style="display: none" name="otherName" value="{{trim(Session::get('username'))}}" />
                                    </form>
                                    <div class="mb-2"> <br> <br>
                                        <label for="exampleFormControlTextarea1" class="form-label mb-0">یاداشت</label>
                                        <textarea class="form-control" id="customerProperty" onblur="saveCustomerCommentProperty(this)" rows="2"></textarea>
                                    </div>
                                @endif
                            </div>
                    </div>

                    <div class="c-checkout container" style="background-image: linear-gradient(to right, #ffffff,#3fa7ef,#3fa7ef); padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                        <div class="col-sm-12" style="margin: 0; padding:0;">
                            <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                                <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#factors"> فاکتور های ارسال شده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری شده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#userLoginInfo1"> کالاهای سبد خرید</a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#customerLoginInfo">ورود به سیستم</a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#returnedFactors1"> فاکتور های برگشت داده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments"> کامنت ها </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments"> نظرسنجی ها</a></li>
                            </ul>
                        </div>
                        <div class="c-checkout tab-content">
                            <div class="row c-checkout rounded-3 tab-pane active" id="factors">
                                <div class="col-sm-12 px-0">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead class="tableHeader">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> نام راننده</th>
                                            <th>مبلغ </th>
                                            <th>مشاهده</th>
                                        </tr>
                                        </thead>
                                        <tbody  id="factorTable" class="tableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="c-checkout tab-pane" id="moRagiInfo">
                                <div class="row c-checkout rounded-3 tab-pane">
                                    <div class="col-sm-12 px-0">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead class="tableHeader">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام کالا</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="goodDetail" class="tableBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="c-checkout tab-pane" id="userLoginInfo1">
                                <div class="row c-checkout rounded-3 tab-pane">
                                    <div class="col-sm-12 px-0">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  class="tableHeader">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام کالا</th>
                                                <th>تعداد </th>
                                                <th>فی</th>
                                            </tr>
                                            </thead>
                                            <tbody id="basketOrders" class="tableBody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="c-checkout tab-pane" id="customerLoginInfo">
                                <div class="row c-checkout rounded-3 tab-pane">
                                    <div class="col-sm-12 px-0">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead class="tableHeader">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th>نوع پلتفورم</th>
                                                <th class="forMobile-hide" style="width:155px;">مرورگر</th>
                                            </tr>
                                            </thead>
                                            <tbody id="customerLoginInfoBody" class="tableBody">
                                           
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="c-checkout tab-pane" id="returnedFactors1" >
                            <div class="row c-checkout rounded-3 tab-pane">
                                    <div class="col-sm-12 px-0">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead class="tableHeader">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام راننده</th>
                                                <th>مبلغ </th>
                                            </tr>
                                            </thead>
                                            <tbody id="returnedFactorsBody" class="tableBody">
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

                            <div class="c-checkout tab-pane" id="comments">
                                <div class="row c-checkout rounded-3 tab-pane active" >
                                    <div class="col-sm-12 px-0">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead class="tableHeader">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> کامنت</th>
                                                <th> کامنت بعدی</th>
                                                <th style="width:111px;"> تاریخ بعدی </th>
                                            </tr>
                                            </thead>
                                            <tbody id="customerComments" class="tableBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="c-checkout tab-pane" id="assesments">
                                <div class="row c-checkout rounded-3 tab-pane active">
                                    <div class="col-sm-12 px-0">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead class="tableHeader">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> کامنت</th>
                                                <th> برخورد راننده</th>
                                                <th> مشکل در بارگیری</th>
                                                <th style="width:111px;"> کالاهای برگشتی</th>
                                            </tr>
                                            </thead>
                                            <tbody id="customerAssesments" class="tableBody">
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
    <div class="modal fade" id="viewComment" tabindex="1"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2 text-white">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel">کامنت ها</h5>
                </div>
                <div class="modal-body">
                    <h3 id="readCustomerComment1"></h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="addComment" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog modal-dialog-scrollable ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
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

   <!-- Modal for reading factor details-->
    <div class="modal fade" id="viewFactorDetail" tabindex="-1"  data-bs-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog  modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header py-2 text-white">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel">جزئیات فاکتور</h5>
                </div>
                <div class="modal-body py-0" id="readCustomerComment">
                    <h6 style="border-bottom: 1px solid #dee2e6; text-align:center;">فاکتور فروش </h6>
                    <div class="flex-container">
                        <div style="flex-grow: 1"> تاریخ فاکتور:  <span id="factorDate"></span> </div>
                        <div style="flex-grow: 1">   مشتری : <span id="customerNameFactor"> </span>  </div>
                        <div style="flex-grow: 1">  شماره فاکتور  : <span id="factorSnFactor"> </span>  </div>
                        
                    </div>
                    <div class="flex-container">
                        <div style="flex-grow: 1"> آدرس : <span id="customerAddressFactor"> </span>  </div>
                        <div style="flex-grow: 1">  تلفن   :  <span id="customerPhoneFactor"> </span>  </div>
                    </div>
                    <div class="row">
                        <table id="strCusDataTable"  class='table table-bordered table-striped table-sm'>
                            <thead class="tableHeader">
                            <tr>
                                <th >ردیف</th>
                                <th>نام کالا </th>
                                <th>تعداد/مقدار</th>
                                <th>واحد کالا</th>
                                <th>فی (تومان)</th>
                                <th style="width:111px;">مبلغ (تومان)</th>
                            </tr>
                            </thead>
                            <tbody id="productList" class="tableBody">

                            </tbody>
                        </table>
                     </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
        <!-- Modal for reading comments-->



            {{-- modal for reading comments --}}
            <div class="modal fade" id="inactiveReadingComment" data-bs-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel">
                <div class="modal-dialog modal-dialog-scrollable ">
                    <div class="modal-content">
                        <div class="modal-header py-2">
                            <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    <div class="modal-body">
                        <p>کامنت غیر فعالی </p>
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar">کامنت مدیر</label>
                                <textarea class="form-control" rows="5" ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal" onclick="deleteConfirm()">بستن</button>
                            <button type="button" class="btn btn-info btn-sm crmButtonColor">ذخیره <i class="fa fa-save"> </i> </button>
                    </div>
                </div>
                </div>
            </div>

    <!-- evacuated customer modals -->
    <div class="modal fade dragableModal" id="takhsesKarbar" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 text-white">
            <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" style="background-color:red;"></button>
                <h5 class="modal-title"> تخصیص </h5>
            </div>
            <div class="modal-body p-1" id="readCustomerComment">
                    <table class="table table-bordered table-hover table-sm " id="tableGroupList">
                        <thead class="tableHeader">
                            <tr>
                                <th>ردیف</th>
                                <th>نام کاربر</th>
                                <th>نقش کاربری</th>
                                <th>فعال</th>
                            </tr>
                        </thead>
                        <tbody class="tableBody" id="mainGroupList">
                            @foreach ($admins as $admin)
                                <tr onclick="setAdminStuff(this)">
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$admin->name." ".$admin->lastName}}</td>
                                    <td>{{$admin->adminType}}</td>
                                    <td>
                                        <input class="mainGroupId" type="radio" name="AdminId" value="{{$admin->id}}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
              </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">انصراف <i class="fa fa-xmark"></i></button>
            <button type="button" onclick="takhsisCustomer()" class="btn btn-primary">ذخیره <i class="fa fa-save"></i></button>
            </div>
        </div>
        </div>
    </div>
            <!-- modal of inactive customer -->
            <div class="modal fade dragableModal" id="inactiveCustomer" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" >
                <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header py-2 text-white">
                      <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                       <h5 class="modal-title" id="exampleModalLabel"> غیر فعالسازی </h5>
                    </div>
                    <form action="{{url('/inactiveCustomer')}}" id="inactiveCustomerForm" method="get">
                    <div class="modal-body">
                        <label for="">دلیل غیر فعالسازی</label>
                    <textarea class="form-control" name="comment" id="" cols="30" rows="4"></textarea>
                    <input type="hidden" name="customerId" id="inactiveId">
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">بستن <i class="fa fa-xmark fa-lg"></i></button>
                    <button type="submit" class="btn btn-sm btn-success" >ذخیره <i class="fa fa-save fa-lg"></i></button>
                    </div>
                </form>
                </div>
                </div>
            </div>


            <!-- Modal for reading comments-->
            <div class="modal fade dragableModal" id="viewFactorDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2 text-white">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel">جزئیات فاکتور</h5>
                </div>
                <div class="modal-body" id="readCustomerComment">
                    <div class="container">
                        <div class="row" style=" border:1px solid #dee2e6; padding:10px">
                                <h4 style="padding:10px; border-bottom: 1px solid #dee2e6; text-align:center;">فاکتور فروش </h4>
                                <div class="col-sm-6">
                                    <table class="table table-borderless" style="background-color:#dee2e6">
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
                                            <td >3</td>
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
                                <table id="strCusDataTable"  class=' table table-bordered table-striped table-sm' style="background-color:#dee2e6">
                                    <thead class="tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>نام کالا </th>
                                        <th>تعداد/مقدار</th>
                                        <th>واحد کالا</th>
                                        <th>فی (تومان)</th>
                                        <th>مبلغ (تومان)</th>
                                    </tr>
                                    </thead>
                                    <tbody id="productList">
                                    </tbody>
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

    <!-- modal of inactive customer -->
    <div class="modal fade dragableModal" id="inactiveCustomer"  tabindex="-1"  data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2 text-white">
                    <h5 class="modal-title" id="exampleModalLabel"> غیر فعالسازی </h5>
                </div>
                <form action="{{url('/inactiveCustomer')}}" id="inactiveCustomerForm" method="get">
                    <div class="modal-body">
                        <label for="">دلیل غیر فعالسازی</label>
                        <textarea class="form-control" name="comment" id="" cols="30" rows="6"></textarea>
                        <input type="text" name="customerId" id="inactiveId" style="display:none">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal" id="cancelInActive">بستن <i class="fa fa-xmark fa-lg"></i></button>
                        <button type="submit" class="btn btn-primary btn-sm" >ذخیره <i class="fa fa-save fa-lg"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- modal of view return comment -->
        <div class="modal fade dragableModal" id="returnViewComment"  tabindex="-1"   data-bs-backdrop="static" >
            <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header py-2 text-white">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                         <h5 class="modal-title" id="exampleModalLabel"> دلیل ارجاع</h5>
                    </div>
                    <div class="modal-body" style="font-size:16px">
                        <div class="well">
                        <span id="returnView"></span>
                    </div>
                    </div>
                </div>
            </div>
        </div>

</main>

<script>
$.ajax({
        method:"GET",
        url:"https://star4.ir/searchMap",
        }).then(function(data){
    var map;
    if (L.DomUtil.get("map") !== undefined) {
        L.DomUtil.get("map")._leaflet_id = null;
    }
    map = L.map('map').setView([35.70163, 51.39211], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);
    var marker ={};
    data.forEach(function(item){
        if(item.LatPers>0 && item.LonPers>0){
            var popup =new  L.popup().setContent();
            marker = L.marker([item.LonPers,item.LatPers]).addTo(map);
            let btn = document.createElement('a');
            btn.innerText = '' +item.Name;
            btn.setAttribute('onclick', "openDashboard("+item.PSN+")");
            marker.bindPopup(btn, {
                Width: '300px'
            });
        }
    });
});

$("#filterLocationBtn").on("click", function () {
    let snMantagheh = $("#searchByMantagheh").val();
    let namePhoneCode=$("#searchAllName").val();
    if($("#customerLocationRadio").is(":checked")){
        $.get("https://star4.ir/filterMap",{namePhoneCode:namePhoneCode,snMantagheh:snMantagheh},function(data,statues){
            var map;
            if (L.DomUtil.get("map") !== undefined) {
                L.DomUtil.get("map")._leaflet_id = null;
            }
            map = L.map('map').setView([35.70163, 51.39211], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);
            var marker ={};
            data.forEach(function(item){
                if(item.LatPers>0 && item.LonPers>0){
                    var popup =new  L.popup().setContent();
                    marker = L.marker([item.LonPers,item.LatPers]).addTo(map);
                    let btn = document.createElement('a');
                    btn.innerText = '' +item.Name;
                    btn.setAttribute('onclick', "openDashboard("+item.PSN+")");
                    marker.bindPopup(btn, {
                        Width: '300px'
                    });
                }
            });

        })
    }
}); 

        
</script>
@endsection
