@extends('layout')
@section('content')

<main>
    <div class="container-xl px-4" style="margin-top:6%;">
         <h3 class="page-title">لیست ارجاعات کاربر</h3>
    <div class="card mb-4" style="margin: 0; padding:0;">
        <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <div class="form-group col-sm-2 mb-2">
                        <input type="text" name="" placeholder="کد" class="form-control publicTop" id="searchPCode">
                    </div>
                    <div class="form-group col-sm-5 mb-2">
                        <input type="text" name="" placeholder="ازتاریخ" class="form-control publicTop" id="firstDateReturned">
                    </div>
                    
                    <div class="form-group col-sm-5 mb-2">
                         <input type="text" name="" placeholder="تا تاریخ" class="form-control publicTop" id="secondDateReturned">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6 mb-2">
                        <input type="text" name="" placeholder="اسم یا آدرس"  class="form-control publicTop" id="searchReferedName">
                    </div> 
                    <div class="form-group col-sm-6 mb-2">
						 <select class="form-select publicTop" id="searchByReturner">
                            <option value="0">کاربر ارجاع دهنده</option>
                            @foreach ($returners as $returner)
                                <option value="{{$returner->id}}">{{$returner->name.' '.$returner->lastName}}</option>  
                            @endforeach
                        </select>
                      
                    </div>
                </div>
            </div>
               
           <div class="col-sm-6">
                <button class='enableBtn btn btn-primary btn-sm text-warning buttonHover'  disabled  id='openDashboard' >داشبورد <i class="fal fa-dashboard fa-lg"> </i> </button>
                 <button class='enableBtn btn btn-primary btn-sm text-warning buttonHover' disabled id="returnComment">علت ارجاع<i class="fal fa-eye fa-lg"> </i> </button>
                  <button class='enableBtn btn btn-primary btn-sm text-warning buttonHover' disabled id="inactiveButton">غیر فعال<i class="fal fa-ban fa-lg"> </i> </button>
                     <button class='enableBtn btn btn-primary btn-sm text-warning buttonHover'   style="width:160px;"  disabled id="takhsisButton">تخصیص کاربر  <i class="fal fa-tasks fa-lg"> </i> </button>
                   
                </div>

                <input type="text" id="customerSn"  value="" style="display: none;" />
                <input type="text" id="adminSn"  value="" style="display: none;"/>
            </div>
        </div>
                      <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">
                                <table class='table table-bordered table-striped table-sm'>
                                <thead class="tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>اسم</th>
                                        <th style="width:66px;">کد</th>
                                        <th>آدرس </th>
                                        <th>همراه</th>
                                        <th>ارجاع دهنده</th>
                                        <th>تاریخ ارجاع</th>
                                        <th>انتخاب</th>
                                    </tr>
                                    </thead>
                                    <tbody class="select-highlight tableBody" id="returnedCustomerList">
                                        @foreach ($customers as $customer)
                                            <tr onclick="returnedCustomerStuff(this)">
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$customer->Name}}</td>
                                                <td style="width:66px;">{{$customer->PCode}}</td>
                                                <td class="scrollTd">{{$customer->peopeladdress}} </td>
                                                <td>{{$customer->hamrah}}</td>
                                                <td>{{$customer->adminName.' '.$customer->adminLastName}}</td>
                                                <td>{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($customer->returnDate))->format('Y/m/d')}}</td>
                                                <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="{{$customer->PSN.'_'.$customer->adminId}}"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="customerDashboard" data-bs-keyboard="false" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close" style="background-color:red;"></button>
                    <h5 class="modal-title"> ارجاعات </h5>
                </div>

                <div class="modal-body">
					
					           <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-2">
											<div class="input-group input-group-sm mb-2">
											  <span class="input-group-text" id="inputGroup-sizing-sm">کد</span>
											  <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" id="customerCode" value="" disabled>
											</div>
                                        </div>
                                        <div class="col-lg-7 col-md-7 col-sm-7">
											<div class="input-group input-group-sm mb-2">
											  <span class="input-group-text" id="inputGroup-sizing-sm">نام و نام خانوادگی </span>
											  <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"  id="customerName" disabled>
											</div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3">
											
											<div class="input-group input-group-sm mb-2">
											  <span class="input-group-text" id="inputGroup-sizing-sm"> تعداد فاکتور </span>
											  <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm"  id="countFactor" disabled>
											</div>
                                        </div>
                                    </div>
                                    <div class="row">
										 <div class="col-lg-4 col-md-4 col-sm-4">
											<div class="input-group input-group-sm mb-2">
												 <span class="input-group-text" id="inputGroup-sizing-sm">تلفن ثابت </span>
											  <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" disabled>
										</div>
									 </div>
										
                                        <div class="col-lg-4 col-md-4 col-sm-4">
											<div class="input-group input-group-sm mb-2">
											  <span class="input-group-text" id="inputGroup-sizing-sm"> تلفن همراه 1 </span>
											  <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" id="mobile1" disabled>
											</div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
											<div class="input-group input-group-sm mb-2">
											  <span class="input-group-text" id="inputGroup-sizing-sm"> تلفن همراه 2 </span>
											  <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" disabled>
											</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
										<div class="input-group input-group-sm mb-2">
										  <span class="input-group-text" id="inputGroup-sizing-sm"> آدرس  </span>
										  <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" id="customerAddress" disabled>
										</div>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <div style="width:300px;">
                                        <label for="exampleFormControlTextarea1" class="form-label fw-bold">یاداشت  </label>
                                        <textarea style="background-color:blanchedalmond" class="form-control" id="exampleFormControlTextarea1" rows="3" ></textarea>
                                    </div>
                                </div>
                            </div> <hr>
					
					
					
                          
                    <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                    <div class="col-sm-8" style="margin: 0; padding:0;">
                            <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                                <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری کرده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#ShoppingBasckets"> کالاهای سبد خرید</a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#referedReturnFactor"> فاکتور های برگشت داده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments"> کامنت ها </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments2"> نظر سنجی ها</a></li>
                            </ul>
                        </div>
                        <div class="c-checkout tab-content talbeDashboardTop">
                                <div class="row c-checkout rounded-3 tab-pane active tableDashboardMiddle" id="custAddress">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead class="tableHeader">
                                            <tr class="theadTr">
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام راننده</th>
                                                <th>مبلغ </th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody  id="factorTable" class="tableBody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="moRagiInfo">
                                <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead  class="tableHeader">
                                            <tr class="theadTr">
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام کالا</th>
                                                <th>تعداد </th>
                                                <th>فی</th>
                                            </tr>
                                            </thead>
                                            <tbody id="goodDetail" class="tableBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="ShoppingBasckets">
                                <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm" >
                                            <thead class="tableHeader">
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

                            <div class="c-checkout tab-pane talbeDashboardTop" id="referedReturnFactor">
                                <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead class="tableHeader">
                                            <tr class="theadTr">
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام کالا</th>
                                                <th>تعداد </th>
                                                <th>فی</th>
                                            </tr>
                                            </thead>
                                            <tbody class="tableBody">
                                            <tr>
                                                <td> 1 </td>
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

                            <div class="c-checkout tab-pane talbeDashboardTop" id="comments">
                                <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead class="tableHeader">
                                            <tr class="thead">
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

                            <div class="c-checkout tab-pane talbeDashboardTop" id="assesments2">
                                <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead class="tableHeader">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> کامنت</th>
                                                <th> برخورد راننده</th>
                                                <th> مشکل در بارگیری</th>
                                                <th> کالاهای برگشتی</th>
                                                <th> انتخاب </th>
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

    <div class="modal fade dragableModal" id="takhsesKarbar" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" style="background-color:red;"></button>
                <h5 class="modal-title"> تخصیص </h5>
            </div>
            <div class="modal-body" id="readCustomerComment">
                <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">
                    @if(isset($customer))
                    <div class="card px-3"> <h3> تخصیص ({{$customer->Name}}) به کاربر دیگر</h3></div>
                    <table class="table table-bordered table-hover" id="tableGroupList">
                        <thead class="tableHeader">
                            <tr>
                                <th>ردیف</th>
                                <th>نام کاربر</th>
                                <th>نقش کاربری</th>
                                <th>فعال</th>
                            </tr>
                        </thead>
                        <tbody id="mainGroupList" class="tableBody">
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
                    @endif
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="cancelTakhsis">انصراف <i class="fa fa-xmark"></i></button>
            <button type="button" onclick="takhsisCustomer()" class="btn btn-primary" >ذخیره<i class="fa fa-save"></i></button>
            </div>
        </div>
       </div>
    </div>

            <!-- Modal for reading comments-->
    <div class="modal fade dragableModal" id="viewFactorDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> غیر فعالسازی </h5>
                </div>
                <form action="{{url('/inactiveCustomer')}}" id="inactiveCustomerForm" method="get">
                    <div class="modal-body">
                        <label for="">دلیل غیر فعالسازی</label>
                        <textarea style="background-color:blanchedalmond" class="form-control" name="comment" id="" cols="30" rows="6"></textarea>
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
        <!-- Modal for reading comments-->
        <div class="modal fade dragableModal" id="viewComment" tabindex="1"  data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel">کامنت ها</h5>
                </div>
                <div class="modal-body" >
                    <h3 id="readCustomerComment1"></h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>

        <!-- modal of view return comment -->
        <div class="modal fade dragableModal" id="returnViewComment"  tabindex="-1"   data-bs-backdrop="static" >
            <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
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
@endsection
