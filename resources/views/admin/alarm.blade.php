@extends('layout')
@section('content')
<style>
    .loginReport {
    width:122px;
}
#customerWithOutAlarmBuyOrNot {
    display:none;
}
</style>
<div class="container-fluid containerDiv">
      <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 sideBar alarmSidebar">
                   <fieldset class="border rounded mt-5 sidefieldSet">
                        <legend  class="float-none w-auto legendLabel mb-0"> فیلتر آلارمها </legend>
                        @if(hasPermission(Session::get("asn"),"alarmoppN") > -1)
                        <form action="{{url('/filteralarms')}}" method="get" id="filterAlarmsForm">
                            @if(hasPermission(Session::get("asn"),"allalarmoppN") > -1)
                            <div class="form-check">
                                <input class="form-check-input p-2 float-end alarmRighRdios" type="radio" value="0" name="alarmState" id="customerWithAlarm" checked>
                                <label class="form-check-label me-4" for="assesPast"> آلارمها </label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"donealarmoppN") >-1)
                            <div class="form-check">
                                <input class="form-check-input p-2 float-end alarmRighRdios" type="radio" value="1" name="alarmState" id="customerDoneAlarms">
                                <label class="form-check-label me-4" for="assesDone"> آلارمهای انجام شده  </label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"noalarmoppN") > -1)
                            <div class="form-check">
                                <input class="form-check-input p-2 float-end alarmRighRdios" type="radio"  value="2" name="alarmState" id="customerWithOutAlarm">
                                <label class="form-check-label me-4" for="assesDone"> مشتریان فاقد آلارم </label>
                            </div>
                            @endif
                            @if(hasPermission(Session::get("asn"),"donealarmoppN") > -1 or hasPermission(Session::get("asn"),"allalarmoppN") > -1 or hasPermission(Session::get("asn"),"noalarmoppN") > -1)
                            <div class="form-group col-sm-12 mb-2" id="customerWithOutAlarmBuyOrNot">
                                <select class="form-select form-select-sm" name="buyOrNot" id="buyOrNot">
                                    <option value="-1"> خرید  </option>
                                    <option value="1"> دارد </option>
                                    <option value="0"> ندارد </option>
                                    <option value="2"> همه </option>
                                </select>
                            </div>
                            <div class="row" id="alarmDates">
                                <div class="form-group col-sm-12 mb-1">
                                    <input type="text" name="firstDateAlarm" placeholder=" از  تاریخ آلارم گذاری"    class="form-control form-control-sm" id="firstDateReturned">
                                </div>
                                <div class="form-group col-sm-12 mb-2">
                                    <input type="text" name="secondDateAlarm" placeholder="  تا تاریخ آلارم گذاری" class="form-control form-control-sm" id="secondDateReturned">
                                </div>
                            </div>
							 <label class="form-lable">کاربر</label>
                            <select class="form-select form-select-sm" name="asn" id="employeeId">
                                @foreach($employies as $employee)
                                    <option @if($employee->id==$adminId) selected @endif value="{{$employee->id}}">{{$employee->name.' '.$employee->lastName}}</option>
                                @endforeach
                            </select>
                            <div class="row" id="alarmBuysDates" style="display:none">
                                <div class="form-group col-sm-12 mb-1">
                                    <input type="text" name="firstDateBuy" placeholder="از  تاریخ خرید"    class="form-control form-control-sm" id="firstDate">
                                </div>
                                <div class="form-group col-sm-12 mb-2">
                                    <input type="text" name="secondDateBuy" placeholder="تا تاریخ خرید" class="form-control form-control-sm" id="secondDate">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 mb-1">
                                    <button class='btn btn-primary btn-sm text-warning' type="submit" id="submitFilterAlarmFormBtn"> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                                </div>
                            </div>
                            @endif
                        
                        @endif
                        
                    </fieldset>
                  </div>
                <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                    <div class="row contentHeader alarmContentHeader">
                        <div class="col-sm-5 text-end">
                            <div class="row">
                                <div class="form-group col-sm-3 mt-2 px-1">
                                    <input type="text" name="namePhoneCode" placeholder="جستجو" class="form-control form-control-sm " id="searchAlarmName">
                                </div>
                                <div class="form-group col-sm-3 mt-2 px-1 forMobile-hide ">
                                    <select class="form-select form-select-sm " id="searchAlarmByCity">
                                       <option value="">--</option>
                                        <option value="80"> تهران </option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3 mt-2 px-1 forMobile-hide ">
                                    <select class="form-select form-select-sm" name="snMantagheh" id="searchAlarmByMantagheh">
                                    <option value="" hidden>منطقه</option>
                                    <option value="0">همه</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3 mt-2 px-1 forMobile-hide ">
                                    <select class="form-select form-select-sm alarmBtn" name="orderOption" id="orderAlarms">
                                        <option value="-1">مرتب سازی</option>
                                        <option value="FactDate">تاریخ آخرین فاکتور </option>
                                        <option value="Name">نام مشتری</option>
                                        <option value="alarmDate"> تاریخ آلارم </option>
                                        <option value="n.poshtibanName"> نام کاربر  </option>
                                    </select>
                                    <select class="form-select form-select-sm forMobile-hide " style="display:none" id="orderUnAlarms">
                                        <option value="-1">مرتب سازی</option>
                                        <option value="FactDate">تاریخ آخرین فاکتور </option>
                                        <option value="Name">نام مشتری</option>
                                        <option value="adminName"> نام کاربر  </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        </form>
                        <div class="col-sm-7 text-start">
                            <input type="text" id="customerSn" style="display: none" name="customerSn" value="" />
                            <input type="text" id="adminSn" style="display: none" name="adminSn" value="" />
                            @if(hasPermission(Session::get("asn"),"alarmoppN") > 0)
                               <button class='enableBtn btn btn-sm btn-primary text-warning' disabled type="button" id='openDashboardForAlarm'> داشبورد <i class="fal fa-dashboard "></i></button>
                            @endif
                            @if(hasPermission(Session::get("asn"),"alarmoppN") > 0)
                                <button class='enableBtn btn btn-sm btn-primary text-warning' disabled type="button" onclick="takhsisCustomerAlarm()"> تخصیص <i class="fa-solid fa-list-check"></i> </button>
                                <button class='enableBtn btn btn-sm btn-primary text-warning' disabled type="button" onclick="changeAdminAlarm()"> تغییر کاربر  <i class="fa-solid fa-edit"></i> </button>
                                <button class='enableBtn btn btn-sm btn-primary text-warning alarmBtn' disabled type="button"  onclick="changeAlarm()"> تغیر آلارم  <i class="fal fa-warning "></i></button>
                                <button class='enableBtn btn btn-sm btn-primary text-warning alarmBtn' disabled type="button"  onclick="alarmHistory()"> گردش آلارم  <i class="fal fa-history "></i></button>
                            @endif
                            @if(hasPermission(Session::get("asn"),"alarmoppN") > 0)
                             <button class='enableBtn btn btn-sm btn-primary text-warning' disabled type="button" id="inactiveButton">غیر فعال <i class="fal fa-ban"></i> </button>
                            @endif
                            <input type="text" id="customerSn" style="display: none" name="customerSn" value="" />
                        </div>
                   </div>
                    <div class="row mainContent">
                        <div class="col-lg-12 px-0" id="alarmedCustomers">
                            <table id="strCusDataTable" class="myDataTable table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr >
											<th> ردیف </th>
											<th>اسم</th>
											<th> شماره تماس</th>
											<th class="forMobile-hide">تاریخ ثبت</th>
											<th class="forMobile-hide" >  گردش  </th>
											<th class="forMobile-hide">منطقه </th>
											<th class="forMobile-hide"> تعیین </th>
                                            <th class="forMobile-hide">تاریخ فاکتور</th>
											<th class="forMobile-hide"> تاریخ نمایش  </th>
											<th>انتخاب</th>
                                       </tr>
                                    </thead>
                                    <tbody class="select-highlight" id="alarmsbody">
                                        @foreach ($customers as $customer)
                                            <tr onClick="setAlarmCustomerStuff(this,{{$customer->id}}); selectTableRow(this);">
                                                <td >{{$loop->iteration}}</td>
                                                <td>{{trim($customer->Name)}}</td>
                                                <td>{{trim($customer->PhoneStr)}}</td>
                                                <td class="forMobile-hide">{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($customer->TimeStamp))->format("Y/m/d")}}</td>
                                                <td  class="forMobile-hide">{{$customer->countCycle}}</td>
                                                <td class="forMobile-hide">{{trim($customer->NameRec)}}</td>
                                                <td class="forMobile-hide" >{{$customer->assignedDays}}</td>
                                                <td class="forMobile-hide" >{{$customer->FactDate}}</td>
                                                <td class="forMobile-hide"  style="color:red">{{$customer->alarmDate}}</td>
                                                <td><input class="customerList form-check-input" name="customerId" type="radio" value="{{$customer->PSN.'_'.$customer->adminSn.'_'.$customer->SerialNoHDS}}"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                             </table>
                             <div class="grid-today rounded-2" style="margin-bottom:5px">
                                <div class="today-item"> <span style="color:red; font-weight:bold;">  آخرین کامنت: </span> <span id="alarmLastComment"></span>  </div>
                            </div>
                        </div>

                        <div class="col-lg-12 px-0 mx-0" id="unAlarmedCustomers" style="display:none">
                            <table id="strCusDataTable" class='table table-bordered table-striped table-sm myDataTable'>
                                    <thead class="tableHeader">
                                        <tr >
											<th> ردیف </th>
											<th >کد</th>
											<th >اسم</th>
											<th> شماره تماس</th>
											<th class="forMobile-hide" style="width:77px">منطقه </th>
                                            <th class="forMobile-hide" style="width:111px">تاریخ آخرین</th>
											<th class="forMobile-hide" style="width:166px"> کاربر  </th>
											<th>انتخاب</th>
                                       </tr>
                                    </thead>
                                    <tbody class="select-highlight tableBody" id="unalarmsbody">
                                    </tbody>
                            </table>
                            <div class="grid-today rounded-2"  style="margin-bottom:5px">
                                <div class="today-item"> <span style="color:red; font-weight:bold;">کامنت اختصاصی: </span> <span id="unAlarmLastComment"></span>  </div>
                            </div>
                        </div>
                    </div>
                    <div class="row contentFooter">
                        <div class="col-lg-12 text-start" id="alarmDoneButtonsHistoryDiv" style="display:none">
                            <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getDoneAlarmHistory('TODAY')"> امروز  : </button>
                            <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getDoneAlarmHistory('YESTERDAY')"> دیروز : </button>
                            <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getDoneAlarmHistory('LASTHUNDRED')"> صد تای آخر : 100</button>
                            <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getDoneAlarmHistory('ALLALARMS')"> همه : </button>
                        </div>
                        <div class="col-lg-12 text-start" id="alamButtonsHistoryDiv">
                            <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getAlarmHistory('TODAY')"> امروز  : </button>
                            <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getAlarmHistory('YESTERDAY')"> دیروز : </button>
                            <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getAlarmHistory('LASTHUNDRED')"> صد تای آخر : 100</button>
                            <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getAlarmHistory('ALLALARMS')"> همه : </button>
                        </div>
                        <div class="col-lg-12 text-start" id="noAlarmButtonsHistoryDiv" style="display:none">
                            <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getUnAlarmHistory('TODAY')"> خرید امروز  : </button>
                            <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getUnAlarmHistory('YESTERDAY')"> خرید دیروز : </button>
                            <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getUnAlarmHistory('LASTHUNDRED')"> صد خرید آخر : 100</button>
                            <button type="button" class="btn btn-sm btn-primary loginReport" onclick="getUnAlarmHistory('ALLUNALARMS')"> همه : </button>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    
    <!-- for takhsis change modal -->
    <div class="modal fade dragableModal" id="changeAdminModal" role="dialog"   data-backdrop="static" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable  modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header py-2 text-white" style="margin:0; border:none">
                            <button type="button" class="btn-close bg-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                            <h5 class="modal-title" id="exampleModalLongTitle"> انتقال مشتری از کاربر به کاربر  </h5>
                        </div>
                        <div class="modal-body pt-0">
                            <table class="table table-bordered ">
                                <thead class="text-warning tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th> مشتری </th>
                                        <th> کاربر </th>
                                        <th>شماره تماس</th>
                                        <th style="display:none">انتخاب</th>
                                  </tr>
                                </thead>
                                <tbody id="customerToMoveBody" class="tableBody" style="height:160px !important;">

                                </tbody>
                            </table>
                                <input type="hidden" id="adminID" >
                            <table class="table table-bordered">
                                <thead class="text-warning tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>نام کاربر </th>
                                        <th>نقش کاربر </th>
                                        <th>توضیحات</th>
                                        <th>انتخاب </th>
                                  </tr>
                                </thead>
                                <tbody id="selectKarbarToMove" class="tableBody">

                                </tbody>
                            </table>
                            <div class="col-lg-12 text-start">
                                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" id="cancelMoveKarbar"> انصراف <i class="fa-solid fa-xmark"> </i> </button>
                                <button type="button" class="btn btn-danger btn-sm"  onclick="moveCustomerToAdmin()" > انتقال <i class="fa fa-sync"></i> </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end change modal -->

                <!-- for takhsis modal -->
    <div class="modal fade dragableModal" id="takhsisCustomerModal" role="dialog"   data-backdrop="static" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable  modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header py-2 text-white">
                            <button type="button" class="btn-close bg-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                            <h5 class="modal-title" id="exampleModalLongTitle"> انتقال مشتری از کاربر به کاربر  </h5>
                        </div>
                        <div class="modal-body pt-0">
                            <table class="table table-bordered">
                                <thead class="text-warning tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th> مشتری </th>
                                        <th>شماره تماس</th>
                                        <th style="display:none">انتخاب</th>
                                  </tr>
                                </thead>
                                <tbody id="customerToTakhsisBody" class="tableBody" style="height:160px !important;">

                                </tbody>
                            </table>
                                <input type="hidden" id="adminID" >
                            <table class="table table-bordered">
                                <thead class="text-warning tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>نام کاربر </th>
                                        <th>نقش کاربر </th>
                                        <th>توضیحات</th>
                                        <th>انتخاب </th>
                                  </tr>
                                </thead>
                                <tbody id="selectKarbarToTakhsis" class="tableBody">

                                </tbody>
                            </table>
                            <div class="col-lg-12 text-start">
                                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" id="cancelMoveKarbar"> انصراف <i class="fa-solid fa-xmark"> </i> </button>
                                <button type="button" class="btn btn-danger btn-sm"  onclick="moveCustomerToAdmin()" > انتقال <i class="fa fa-sync"></i> </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end change modal -->

    <div class="modal fade notScroll" id="customerDashboard" data-bs-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable  modal-xl">
                <div class="modal-content">
                    <div class="modal-header py-2">
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
                                        <button class="btn btn-sm btn-primary d-inline" id="openAddCommentModal" type="button" value="" name="" style="float:left; display:inline;" > کامنت <i class="fas fa-comment fa-lg"> </i> </button>
                                            <form action="https://starfoods.ir/crmLogin" target="_blank"  method="get" style="display:inine !important;">
                                                <input type="text" id="customerSnLogin" style="display: none" name="psn" value="" />
                                                <button class="btn btn-sm btn-primary d-inline" type="submit" style="float:left;"> ورود جعلی  <i class="fas fa-sign-in fa-lg"> </i> </button>
                                                <input type="text"  style="display: none" name="otherName" value="{{trim(Session::get('username'))}}" />
                                            </form>
                                        <div class="mb-2"> <br> <br>
                                            <label for="exampleFormControlTextarea1" class="form-label mb-0">یاداشت</label>
                                            <textarea  style="background-color:blanchedalmond" class="form-control" id="customerProperty" onblur="saveCustomerCommentProperty(this)" rows="2"></textarea>
                                        </div>
                                    </div>
                            </div>

                        <div class="c-checkout container tab-header" style=" padding:0.5% !important; border-radius:10px 10px 2px 2px;">
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
                            <div class="c-checkout tab-content" style="margin:0;padding:0.3%; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" >
                                    <div class="col-sm-12 px-0">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead class="tableHeader">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام راننده</th>
                                                <th>مبلغ </th>
                                                <th>مشاهد   </th>
                                            </tr>
                                            </thead>
                                            <tbody  id="factorTable" class="tableBody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="c-checkout tab-pane" id="moRagiInfo" style="margin:0; border-radius:10px 10px 2px 2px;">
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

                                <div class="c-checkout tab-pane" id="userLoginInfo1" style="margin:0; border-radius:10px 10px 2px 2px;">
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

                                <div class="c-checkout tab-pane" id="customerLoginInfo" style="margin:0; border-radius:10px 10px 2px 2px;">
                                    <div class="row c-checkout rounded-3 tab-pane">
                                        <div class="col-sm-12 px-0">
                                            <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                                <thead class="tableHeader">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th>نوع پلتفورم</th>
                                                    <th style="width:160px;">مرورگر</th>
                                                </tr>
                                                </thead>
                                                <tbody id="customerLoginInfoBody" class="tableBody">
                                                
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="c-checkout tab-pane" id="returnedFactors1" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane">
                                        <div class="col-sm-12 px-0">
                                            <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                                <thead class="tableHeader">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> نام راننده</th>
                                                    <th style="width:115px">مبلغ </th>
                                                </tr>
                                                </thead>
                                                <tbody id="returnedFactorsBody" class="tableBody">
                                               
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="c-checkout tab-pane" id="comments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                    <div class="row c-checkout rounded-3 tab-pane">
                                        <div class="col-sm-12 px-0">
                                            <table class="table table-bordered table-striped table-sm">
                                                <thead class="tableHeader">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> کامنت</th>
                                                    <th> کامنت بعدی</th>
                                                    <th> تاریخ بعدی </th>
                                                </tr>
                                                </thead>
                                                <tbody id="customerComments" class="tableBody">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="c-checkout tab-pane" id="assesments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                    <div class="row c-checkout rounded-3 tab-pane active">
                                        <div class="col-sm-12 px-0">
                                            <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                                <thead class="tableHeader">
                                                <tr>
                                                    <th> ردیف</th>
                                                    <th>تاریخ</th>
                                                    <th> کامنت</th>
                                                    <th> برخورد راننده</th>
                                                    <th class="for-mobil"> مشکل در بارگیری</th>
                                                    <th style="width:77px;"> کالاهای برگشتی</th>
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
    <div class="modal" id="inactiveCustomer"  tabindex="-1" data-bs-backdrop="static" >
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="exampleModalLabel"> غیر فعالسازی </h6>
                </div>
                <form action="{{url('/inactiveCustomerAlarm')}}" id="inactiveCustomerForm" method="get">
                    <div class="modal-body">
                        <label class="dashboardLabel form-label">دلیل غیر فعالسازی</label>
                        <textarea  style="background-color:blanchedalmond" class="form-control" required name="comment" id="" cols="30" rows="6"></textarea>
                        <input type="text" name="customerId" required style="display:none" id="inactiveId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" id="cancelinActive">بستن <i class="fa fa-xmark fa-lg"></i></button>
                        <button type="submit" class="btn btn-sm btn-primary" >ذخیره <i class="fa fa-save fa-lg"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal" id="changeAlarm"  tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title"> تغییر آلارم </h5>
                </div>
                <form action="{{url('/changeAlarm')}}" id="changeAlarmForm" method="get">
                    <div class="modal-body">
                        <label class="dashboardLabel form-label">دلیل</label>
                        <textarea  style="background-color:blanchedalmond" class="form-control" required name="comment" id="" cols="30" rows="6"></textarea>
                        <label class="dashboardLabel form-label">تاریخ بعدی</label>
                        <input class="form-control" required placeholder="تاریخ بعدی" name="alarmDate" id="commentDate2">
                        <input class="form-control" style="display:none" id="factorAlarm" name="factorId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" id="cancelSetAlarm">بستن <i class="fa fa-xmark fa-lg"></i></button>
                        <button type="submit" class="btn btn-sm btn-primary" >ذخیره <i class="fa fa-save fa-lg"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal" id="alarmHistoryModal"  tabindex="-1" data-bs-backdrop="static" >
        <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header py-2">
                <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title" id="exampleModalLabel">گردش آلارم</h5>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped table-sm">
                    <thead class="tableHeader">
                    <tr>
                        <th> ردیف</th>
                        <th>تاریخ</th>
                        <th> کامنت</th>
						 <th style="width:220px;">پیگیری</th>
                    </tr>
                    </thead>
                    <tbody class="tableBody" id="alarmHistoryBody">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
            </div>
        </div>
        </div>
    </div>
            <!-- Modal for reading factorDetails-->
    <div class="modal fade" id="viewFactorDetail" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog  modal-dialog   modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel">جزئیات فاکتور</h5>
                </div>
                <div class="modal-body" id="readCustomerComment">
                    <div class="container">
                       <div class="flex-container">
                            <div style="flex-grow: 1"> <b>تاریخ فاکتور   :  </b> <span id="factorDate">  </span>   </div>
                            <div style="flex-grow: 1"> <b> مشتری  :  </b> <span id="customerNameFactor"> </div>
                            <div style="flex-grow: 1"> <b>  شماره فاکتور :</b>  <span id="factorSnFactor"> </span> </div>
                            
                        </div>
                        <div class="flex-container">
                            <div style="flex-grow: 1"> <b> تلفن :</b>    <span id="customerPhoneFactor"> </span>  </div>
                            <div style="flex-grow: 1"> <b> کاربر :  </b>   <span id="Admin"> </span> </div>
                            <div style="flex-grow: 1"> <b> آدرس  :  </b> <span id="customerAddressFactor1"> </span>  </div>
                        </div>

                            <div class="row">
                                <table id="strCusDataTable" class='table table-bordered table-striped table-sm' >
                                    <thead class="tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>نام کالا </th>
                                        <th>تعداد/مقدار</th>
                                        <th>واحد کالا</th>
                                        <th>فی (تومان)</th>
                                        <th style="width:120px;">مبلغ (تومان)</th>
                                    </tr>
                                    </thead>
                                    <tbody class="tableBody" id="productList">

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
    {{-- modal for adding comments --}}
    <div class="modal" id="addComment" data-bs-backdrop="static" data-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable ">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <button type="button" class="btn-close btn-danger" id="cancelCommentButton" data-dismiss="modal" aria-label="Close" style="background-color:red;"></button>
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
                        <input type="text" name="customerIdForComment" id="customerIdForComment" style="display:none;">
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

                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <label for="tahvilBar" >کامنت </label>
                        <textarea  style="background-color:blanchedalmond" class="form-control" style="position:relative" required name="firstComment" rows="3" ></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 fw-bold">
                        <label for="tahvilBar" >زمان تماس بعدی </label>
                            <input class="form-control" autocomplete="off" required name="nextDate" id="commentDate3">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <label for="tahvilBar">کامنت بعدی</label>
                        <textarea  style="background-color:blanchedalmond" class="form-control" name="secondComment" required rows="5" ></textarea>
                        <input class="form-control" type="text" style="display: none;" name="place" value="admins"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save"></i></button>
            </div>
        </form>
        </div>
        </div>
    </div>



      <!-- Modal for reading comments-->
    <div class="modal fade" id="viewComment" tabindex="1" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel">کامنت ها</h5>
                </div>
                <div class="modal-body">
                    <h3 id="readCustomerComment1"></h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
