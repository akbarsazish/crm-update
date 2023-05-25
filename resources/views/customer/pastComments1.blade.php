@extends('layout')
@section('content')
<main>
    <div class="container-xl px-4 mt-n10" style="margin-top:4%;">
        <h3 class="page-title">لیست مشتریان باقی مانده</h3>
           <div class="card mb-4" style="margin: 0; padding:0;">
              <div class="card-body">
                 <div class="row">
                    <div class="col-sm-7">
                       <div class="row">
                            <div class="form-group col-sm-3 px-0">
                                <input type="text" name="" size="20" class="form-control publicTop" id="firstDate" placeholder="از تاریخ ">
                            </div>
                            <div class="form-group col-sm-1" style="text-align:center">
                              <label class="dashboardLabel form-label" >الی</label>
                            </div>
                            <div class="form-group col-sm-3 px-0">
                                <input type="text" name="" size="20" class="form-control publicTop" id="secondDate" placeholder="تا تاریخ " />
                            </div>
                          </div>
                        </div>
                    <div class="col-sm-5" style="display:flex; justify-content:flex-end">
                       <div class="alert" style="padding: 0; padding-right:1%; margin:0;">
                            <input type="text" id="customerSn" style="display:none"  value="" />
                            <input type="text" id="factorSn"   style="display:none"  value="" />
                            <button class='enableBtn btn btn-primary btn-md text-warning' type="button" disabled id='openDashboard'>داشبورد<i class="fal fa-dashboard fa-lg"></i></button>
                            <Button class="btn buttonHover btn-primary text-warning"  id="openAssesmentModal" disabled onclick="openAssesmentStuff()"  type="button"  > افزودن نظر <i class="fa fa-address-card"> </i> </Button>
                        </div>
                    </div>
                 </div>
               <div class="row">
                  <div class="col-sm-12">
                    <div class="well" style="margin-top:2%;">
                        <span class="row" style="margin: 0;">
                            <div class="col-sm-12 " style="padding:0;  margin-top: 0;">
                                <table class='table-bordered table-striped text-center' style="width:100%">
                                <thead class="tableHeader">
                                    <tr>
                                        <th >ردیف</th>
                                        <th>اسم</th>
                                        <th>مبلغ</th>
                                        <th >تاریخ</th>
                                        <th >انتخاب</th>
                                    </tr>
                                    </thead>
                                    <tbody class="select-highlight tableBody" id="customerListBody1">
                                        @foreach ($customers as $customer)
                                            <tr onclick="assesmentStuff(this)">
                                                <td >{{$loop->iteration}}</td>
                                                <td>{{trim($customer->Name)}}</td>
                                                <td>{{number_format($customer->TotalPriceHDS/10)}} تومان</td>
                                                <td >{{$customer->FactDate}}</td>
                                                <td > <input class="customerList form-check-input" name="factorId" type="radio" value="{{$customer->PSN.'_'.$customer->SerialNoHDS}}"></td>
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


{{-- dashbor modal --}}

<div class="modal fade dragableModal" id="customerDashboard" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable  modal-xl">
        <div class="modal-content"  style="background-color:#d2e9ff;">
            <div class="modal-header" style="border-bottom:1px solid rgb(7, 42, 214);">
                <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                          <span class="fw-bold fs-4" id="dashboardTitle"></span>
                        </div>
                        <div class="col-lg-6">
                            <button class="btn btn-sm btn-primary float-start" onclick="openAssesmentStuff()"  type="button" > افزودن نظر <i class="fa fa-address-card fa-lg"> </i> </button>
                           <form action="https://starfoods.ir/crmLogin" target="_blank"  method="get">
                                    <input type="text" id="customerSnLogin" style="display: none" name="psn" value="" />
                                    <input type="text"  style="display: none" name="otherName" value="{{trim(Session::get('username'))}}" />
                                    <button type="button" class="btn btn-sm btn-primary float-start" type="submit"> ورود جعلی  <i class="fas fa-sign-in fa-lg"> </i> </button>
                                </form>
                        </div>
                    </div>
                    <div class=" tab-pane active" id="custInfo" style="border-radius:10px 10px 2px 2px; padding:0;">
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
											  <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" id="customerName" disabled>
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
                                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" ></textarea>
                                    </div>
                                </div>
                            </div> <hr>

                </div>
                <div class="c-checkout container" style="padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                    <div class="col-sm-8" style="margin: 0; padding:0;">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                            <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری کرده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#shoppingList"> کالاهای سبد خرید</a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#returnedFactors"> فاکتور های برگشت داده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments">  کامنت ها </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments"> نظرسنجی </a></li>
                        </ul>
                    </div>
                    <div class="c-checkout tab-content talbeDashboardTop">
                            <div class="row c-checkout rounded-3 tab-pane active tableDashboardMiddle" id="custAddress" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead class="tableHeader">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ </th>
                                                <th> نام راننده </th>
                                                <th>مبلغ </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="factorTable" class="tableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="moRagiInfo">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead class="tableHeader">
                                        <tr>
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

                        <div class="row c-checkout rounded-3 tab-pane talbeDashboardTop" id="shoppingList">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped table-sm">
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
                                        <tr>
                                          
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="c-checkout tab-pane talbeDashboardTop" id="returnedFactors">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead class=="tableHeader">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ </th>
                                            <th>نام راننده</th>
                                            <th>مبلغ </th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody id="returnedFactorsBody" class="tableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="c-checkout tab-pane talbeDashboardTop" id="comments">
                            <div class="row c-checkout rounded-3 tab-pane tableDashboardMiddle" id="custAddress">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead class="tablHeader">
                                        <tr class="theadTr">
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
                            <div class="row c-checkout rounded-3 tab-pane active" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="col-sm-12">
                                    <table class="table-bordered table-striped table-sm">
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
        <!-- Modal for reading comments-->
        <div class="modal fade" id="viewComment" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
        
        <!-- Modal for reading factor Detail -->
        <div class="modal fade" id="viewFactorDetail" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel">جزئیات فاکتور</h5>
                </div>
                <div class="modal-body" id="readCustomerComment">
                    <div class="container">
                        <div class="row" style=" border:1px solid #dee2e6; padding:10px">
                                <h6 style="padding:10px; border-bottom: 1px solid #dee2e6; text-align:center;">فاکتور فروش </h6>
									 <div class="grid-container">
											<div class="item1"> <b>تاریخ فاکتور   :  </b> <span id="factorDate">  </span> </div>
											<div class="item2"> <b> مشتری  :  </b> <span  id="customerNameFactor"> </span>    </div>
											<div class="item3"> <b> آدرس  :  </b> <span id="customerAddressFactor"> </span>   </div>
											<div class="item4"><span> تلفن :</span>    <span id="customerPhoneFactor"> </span></div>
											<div class="item5"><span> کاربر :  </span>   <span id="Admin1"> </span></div>
											<div class="item6"><span>  شماره فاکتور :</span>  <span id="factorSnFactor">  </span></div>
										</div>
                                    </div>
                            <div class="row">
                                <table id="strCusDataTable"  class='table table-bordered table-striped table-sm'>
                                    <thead class="tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th >نام کالا </th>
                                        <th>تعداد/مقدار</th>
                                        <th>واحد کالا</th>
                                        <th>فی (تومان)</th>
                                        <th style="width:122px;">مبلغ (تومان)</th>
                                    </tr>
                                    </thead>
                                    <tbody id="productList1" class="tableBody">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </div>



            {{-- modal for adding comments --}}
            <div class="modal fade" id="assesmentDashboard" data-bs-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-scrollable  modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" id="cancelAssesment"></button>
                            <h5 class="modal-title" id="exampleModalLabel"> افزودن نظر </h5>
                        </div>
                    <div class="modal-body">
                        <form action="{{url('/addAssessmentPast')}}" id="addAssesmentPast" method="get" style="background-color:transparent; box-shadow:none;">
                            @csrf
                            <div class="row mb-2">
                            <div class="col-lg-10">
                              <label for="tahvilBar"> مشتری: &nbsp; </label>
                                <span id="customerComenter" style="font-size:18px;margin-bottom:11px;"></span>
                            </div>
                            <div class="col-lg-2" style="display:flex; justify-content:flex-end;">
                                <button type="submit" class="btn btn-primary btn-sm">ذخیره <i class="fa fa-save"></i></button>
                            </div>
                        </div>	
						 <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
								 <div class="col-lg-3">
                                  <div class="col-lg-12 mb-1">
                                        <select class="form-select form-select-sm" name="shipmentProblem">
                                            <option hidden>مشکل در بار</option>
                                            <option value="1">بلی</option>
                                            <option value="0">خیر</option>
                                        </select>
                                     <input type="text" name="customerId" id="customerIdForAssesment" style="display:none;">
                                    <input type="text" name="factorId" id="factorIdForAssesment" style="display:none;">
                                 </div>
                                <div class="col-lg-12 mb-2">
                                    <select class="form-select form-select-sm" name="behavior">
                                        <option hidden>برخورد راننده</option>
                                        <option value="1">عالی</option>
                                        <option value="2">خوب</option>
                                        <option value="3">متوسط </option>
                                        <option value="4">بد</option>
                                    </select>
                                </div>
                                <div class="col-lg-12">
                                    <input class="form-control form-control-sm" name="alarmDate" required autocomplete="off" id="commentDate2" placeholder="آلارم خرید بعدی">
                                </div>
                            </div>
                            <div class="col-lg-9">
							 <div class="row">
                                <div class="col-lg-6">
                                    <label for="tahvilBar" >کلاهای عودتی  </label>
                                    <textarea class="form-control  bg-light" style="position:relative" name="firstComment" rows="3"  ></textarea>
                                </div>
                                <div class="col-lg-6">
                                    <label for="tahvilBar"> کامنت</label>
                                    <textarea class="form-control bg-light" required name="comment" rows="3" ></textarea>
                                </div>
                             </div>
                         </div>
                         <div class="row mt-3" style=" border:1px solid #dee2e6; padding:5px; margin-right:3px;">
                              <h4 style="padding:10px; text-align:center;">فاکتور فروش </h4>
							    <div class="grid-container">
									<div class="item1"> <b>تاریخ فاکتور   :  </b> <span id="factorDate">  </span> </div>
									<div class="item2"> <b> مشتری  :  </b> <span  id="customerNameFactor"> </span>    </div>
									<div class="item3"> <b> آدرس  :  </b> <span id="customerAddressFactor"> </span>   </div>
									<div class="item4"><span> تلفن :</span>    <span id="customerPhoneFactor"> </span></div>
									<div class="item5"><span> کاربر :  </span>   <span id="Admin1"> </span></div>
									<div class="item6"><span>  شماره فاکتور :</span>  <span id="factorSnFactor">  </span></div>
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
										<th style="width:122px">مبلغ (تومان)</th>
									  </tr>
									</thead>
									<tbody id="productList" class="tableBody">

									</tbody>
								  </table>
                        	</div>
                   		 </div>
                   		</form>
                 	</div>
               	 </div>

                          <div class="row mt-3" style=" border:1px solid #dee2e6; padding:5px; background-color:#dee2e6; margin-right:3px;">
                            <h4 style="padding:10px; text-align:center;">فاکتور فروش </h4>
                                <table class="table table-bordered" style="justify-content:flex-start;">
                                    <tbody>
                                      <tr>
                                        <td>مشتری:</td>
                                        <td id="customerNameFactor" colspan="2"></td>
                                    
                                        <td>آدرس:</td>
                                        <td id="customerAddressFactor" colspan="4"> </td>
                                      </tr>
                                        <tr>
                                            <td>تلفن :</td>
                                            <td id="customerPhoneFactor"></td>
                                            <td>کاربر :</td>
                                            <td id="Admin1"></td>
                                            <td>شماره فاکتور :</td>
                                            <td id="factorSnFactor"></td>
                                            <td>تاریخ فاکتور:</td>
                                           <td id="factorDate"></td>
                                      </tr>
                                    </tbody>
                                  </table>
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
                                    <th>مبلغ (تومان)</th>
                                  </tr>
                                </thead>
                                <tbody id="productList" class="tableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
						
						
        </div>
    </div>
</main>
@endsection
