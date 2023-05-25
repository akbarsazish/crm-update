@extends('layout')
@section('content')
    <div class="container-fluid containerDiv">
             <div class="row">
                    <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                            <fieldset class="border rounded">
                                <legend  class="float-none w-auto legendLabel mb-0"> نظر سنجی  </legend>
                                <form action="{{url('/getAsses')}}" method="get">
                                    @if(hasPermission(Session::get("asn"),"todayoppNazarsanjiN") > -1)
                                    <div class="form-check">
                                        <input class="form-check-input p-2 float-end" type="radio" name="assessName" id="assesToday" checked>
                                        <label class="form-check-label me-4" for="assesToday"> نظرات امروز </label>
                                    </div>
                                    @endif
                                    @if(hasPermission(Session::get("asn"),"pastoppNazarsanjiN") > -1)
                                    <div class="form-check">
                                        <input class="form-check-input p-2 float-end" type="radio" name="assessName" id="assesPast">
                                        <label class="form-check-label me-4" for="assesPast"> نظرات گذشته </label>
                                    </div>
                                    @endif
                                    @if(hasPermission(Session::get("asn"),"doneoppNazarsanjiN") > -1)
                                    <div class="form-check">
                                        <input class="form-check-input p-2 float-end" type="radio" name="assessName" id="assesDone">
                                        <label class="form-check-label me-4" for="assesDone"> نظرات انجام شده </label>
                                    </div>
                                    @endif
                                    @if(hasPermission(Session::get("asn"),"todayoppNazarsanjiN") > -1 or hasPermission(Session::get("asn"),"pastoppNazarsanjiN") > -1 or hasPermission(Session::get("asn"),"doneoppNazarsanjiN") > -1)
                                    <div class="input-group input-group-sm mb-1">
                                        <span class="input-group-text" id="inputGroup-sizing-sm">تاریخ </span>
                                        <input type="text" class="form-control" id="assesFirstDate">
                                    </div>
                                    <div class="input-group input-group-sm mb-1">
                                        <span class="input-group-text" id="inputGroup-sizing-sm"> الی </span>
                                        <input type="text" class="form-control" id="assesSecondDate">
                                    </div>
                                    <button class='btn btn-primary btn-sm text-warning' type="button" id='getAssesBtn'> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                                    @endif
                                </form>

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
                            <div class="row contentHeader forMobileHieght">
                                <div class="col-lg-8 text-end mt-2">
                                    <div class="col-sm-2 mb-1">
                                        <!-- <input type="text" id="assescustomerName" placeholder="جستجو " class="form-control form-control-sm"> -->
                                    </div>
                                </div>
                                <div class="col-lg-4 text-start mt-2">
                                    <input type="text" id="customerSn" style="display:none"  value="" />
                                    <input type="text" id="factorSn" style="display:none"  value="" />
                                    <form action="https://starfoods.ir/crmLogin" target="_blank"  method="get">
                                        <input type="text" id="customerSnLogin" style="display: none" name="psn" value="" />
                                        <input type="text"  style="display: none" name="otherName" value="{{trim(Session::get('username'))}}" />
                                         @if(hasPermission(Session::get("asn"),"oppNazarSanjiN") > 0)
                                        <Button class="btn btn-primary btn-sm float-start" id="fakeLogin" disabled type="submit"> ورود جعلی  <i class="fas fa-sign-in fa-lg"> </i> </Button>
                                        @endif
                                    </form>
                                      @if(hasPermission(Session::get("asn"),"oppNazarSanjiN") > 0)
                                       <button class='btn btn-primary btn-sm text-warning' type="button" disabled id='openDashboard' >داشبورد <i class="fal fa-dashboard fa-lg"></i></button>
                                      @endif
                                      @if(hasPermission(Session::get("asn"),"oppNazarSanjiN") >0)
                                       <button class="btn btn-primary btn-sm text-warning" onclick="openAssesmentStuff()" id="openAssessmentModal1"  disabled  type="button"  > افزودن نظر <i class="fa fa-address-card"> </i> </button>
                                       @endif
                                    </div>
                            </div>
                            <div class="row mainContent">
                            <div id="assesNotDone" class="px-0 mx-0">
                              <table class='table table-striped table-bordered table-sm myDataTable'>
                                <thead class="tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>اسم</th>
                                        <th>مبلغ</th>
                                        <th>تاریخ</th>
                                        <th>شماره فاکتور</th>
                                        <th>انتخاب</th>
                                    </tr>
                                    </thead>
                                    <tbody class="select-highlight tableBody" id="customersAssesBody" style="height:250px !important">
                                        @forelse ($customers as $customer)
                                            <tr onclick="assesmentStuff(this); getCustomerInformation({{$customer->PSN}});">
                                                <td class="no-sort">{{$loop->iteration}}</td>
                                                <td>{{trim($customer->Name)}}</td>
                                                <td>{{number_format($customer->TotalPriceHDS/10)}} تومان</td>
                                                <td>{{$customer->FactDate}}</td>
                                                <td>{{trim($customer->FactNo)}}</td>
                                                <td> <input class="customerList form-check-input" name="factorId" type="radio" value="{{$customer->PSN.'_'.$customer->SerialNoHDS}}"></td>
                                            </tr>
                                            @empty
                                        @endforelse
                                    </tbody>
                                </table> 
                                <hr>
                                <div id="factorInfo">
                                    <div class="row">
                                        <div class="col-lg-12 rounded-2">
                                        <div class="grid-container mx-1">
                                            <div class="item1"> <b style="color:red; bold"> تاریخ فاکتور   :  </b> <span id="factorDateP">  </span> </div>
                                            <div class="item2"> <b style="color:red; bold"> مشتری  :  </b> <span  id="customerNameFactorP"> </span>    </div>
                                            <div class="item3"> <b style="color:red; bold"> آدرس  :  </b> <span id="customerAddressFactorP"> </span>   </div>
                                            <div class="item4"> <b style="color:red; bold"> تلفن : </b>    <span id="customerPhoneFactorP"> </span></div>
                                            <div class="item5"> <b style="color:red; bold"> کاربر :  </b>   <span id="Admin1P"> </span></div>
                                            <div class="item6"> <b style="color:red; bold">  شماره فاکتور : </b>  <span id="factorSnFactorP">  </span></div>
                                        </div>
                                    </div>
                                </div> <br>
                            <div class="row">
                                <div class="col-lg-12">
                                    <table id="strCusDataTable"  class='table table-bordered table-striped table-sm myDataTable'>
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
                                        <tbody id="productListP" class="tableBody" style="height:200px !important;">

                                        </tbody>
                                    </table>
                               </div>
                            </div>
                        </div>
                    </div>
                
                            <div id="assesDoneT" style="display:none"  class="px-0 mx-0">
                                <table  class='table table-bordered table-striped table-sm myDataTable'>
                                    <thead class="tableHeader">
                                        <tr>
                                            <th>ردیف</th>
                                            <th>اسم</th>
                                            <th>شماره تماس</th>
                                            <th>تاریخ </th>
                                            <th>نظر دهنده</th>
											<th>انتخاب</th>
                                        </tr>
                                    </thead>
                                    <tbody class="select-highlight" id="customerListBodyDone">
                                    </tbody>
                                </table>
                                <hr>

                                <div class="row rounded-3" style=" border:1px solid #dee2e6; padding:10px">
                                    <div class="grid-container">
                                        <div class="item1"> <b style="color:red; bold"> تاریخ   : </b> <span id="doneCommentDate">  </span> </div>
                                        <div class="item3"> <b style="color:red; bold"> آلارم    : </b> <span id="doneCommentAlarm"> </span> </div>
                                        <div class="item2"> <b style="color:red; bold"> کامنت   : </b> <span  id="doneCommentComment"> </span> </div>
                                    </div>
                                </div>
                                
                                <table id="" class='table table-bordered table-striped table-sm myDataTable'>
                                    <thead class="tableHeader">
                                        <tr>
                                            <th>ردیف</th>
											<th>عودتی</th>
                                            <th> انتخاب </th>
                                        </tr>
                                    </thead>
                                    <tbody class="select-highlight tableBody" id="customerListBodyDoneDetail" style="height:250px !important">
                                    </tbody>
                                </table>
                            </div>
                            </div>
                                <div class="row contentFooter">
                                    <div class="col-lg-12 text-start">
                                        <button type="button" class="btn btn-sm btn-primary footerButton donComment" style="display:none" onclick="getDonComment('TODAY')"> نظرات امروز  <i class="fa fa-comments"></i> </button>
                                        <button type="button" class="btn btn-sm btn-primary footerButton donComment" style="display:none" onclick="getDonComment('YESTERDAY')"> دیروز  <i class="fa fa-comments"></i> </button>
                                        <button type="button" class="btn btn-sm btn-primary footerButton donComment" style="display:none"  onclick="getDonComment('LASTHUNDRED')"> صدتای آخر  <i class="fa fa-comments"></i></button>
                                        <button type="button" class="btn btn-sm btn-primary footerButton donComment" style="display:none"  onclick="getDonComment('ALL')"> همه <i class="fa fa-comments"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    

{{-- dashbor modal --}}
<div class="modal fade notScroll" id="customerDashboard" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog  modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2">
                <button type="button" class="btn-close btn-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title" id="exampleModalLabel"> داشبورد </h5>
            </div>
            <div class="modal-body py-1">
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
                               <button class="btn openAddCommentModal btn-sm btn-primary d-inline"  type="button" value="" name="" style="float:left; display:inline;" > کامنت <i class="fas fa-comment fa-lg"> </i> </button>
                                <form action="https://starfoods.ir/crmLogin" target="_blank"  method="get" style="display:inine !important;">
                                    <input type="text" class="customerSnLogin" style="display:none" name="psn" />
                                    <button class="btn btn-sm btn-primary d-inline" type="submit" style="float:left;"> ورود جعلی  <i class="fas fa-sign-in fa-lg"> </i> </button>
                                    <input type="text"  style="display: none" name="otherName" value="{{trim(Session::get('username'))}}" />
                                </form>
                            <div class="mb-2"> <br> <br>
                                <label for="exampleFormControlTextarea1" class="form-label mb-0">یاداشت</label>
                                <textarea style="background-color:blanchedalmond" class="form-control" id="customerProperty" onblur="saveCustomerCommentProperty(this)" rows="2"></textarea>
                            </div>
                        </div>
                  </div>


                <div class="c-checkout container" style="background-image: linear-gradient(to right, #ffffff,#3fa7ef,#3fa7ef); padding:0.5% !important; border-radius:10px 10px 2px 2px;">
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
                        <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" >
                            <div class="col-sm-12 px-0">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead class="tableHeader">
                                    <tr>
                                        <th> ردیف</th>
                                        <th>تاریخ</th>
                                        <th> نام راننده</th>
                                        <th>مبلغ </th>
                                        <th style="width:88px; !important;"> جزئیات </th>
                                    </tr>
                                    </thead>
                                    <tbody class="tableBody" id="factorTable">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="c-checkout rounded-3 tab-pane" id="moRagiInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="row c-checkout rounded-3 tab-pane">
                                <div class="col-sm-12 px-0">
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
                                        <tbody class="tableBody" id="goodDetail"> </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="c-checkout rounded-3 tab-pane" id="userLoginInfo1" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="row c-checkout rounded-3 tab-pane" >
                                <div class="col-sm-12 px-0">
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

                        <div class="c-checkout rounded-3 tab-pane" id="customerLoginInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="row c-checkout rounded-3 tab-pane">
                                <div class="col-sm-12 px-0">
                                    <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                        <thead class="tableHeader">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th>نوع پلتفورم</th>
                                            <th style="width:166px !important;">مرورگر</th>
                                        </tr>
                                        </thead>
                                        <tbody class="tableBody" id="customerLoginInfoBody">
                                    
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="c-checkout rounded-3 tab-pane" id="returnedFactors1"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
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
                                        <tbody class="tableBody" id="returnedFactorsBody">
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
                            <div class="row c-checkout rounded-3 tab-pane active">
                                <div class="col-sm-12 px-0">
                                    <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                        <thead class="tableHeader">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> کامنت</th>
                                            <th> کامنت بعدی</th>
                                            <th style="width:111px !important;"> تاریخ بعدی </th>
                                        </tr>
                                        </thead>
                                        <tbody class="tableBody" id="customerComments"  >

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
                                            <th> مشکل در بارگیری</th>
                                            <th style="width:110px;"> کالاهای برگشتی</th>
                                        </tr>
                                        </thead>
                                        <tbody class="tableBody" id="customerAssesments"  >
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


        <!-- Modal for reading factor Detail -->
        <div class="modal fade dragableModal" id="viewFactorDetail" tabindex="-1" data-backdrop="static" aria-hidden="true">
            <div class="modal-dialog  modal-xl">
                <div class="modal-content">
                    <div class="modal-header py-2 text-white">
                        <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h5 class="modal-title" id="exampleModalLabel">جزئیات فاکتور</h5>
                    </div>
                    <div class="modal-body py-1" id="readCustomerComment">
                        <div class="container">
                            <div class="row rounded-3" style=" border:1px solid #dee2e6; padding:10px">
                                    <div class="grid-container">
                                        <div class="item1"> <b>تاریخ فاکتور   :  </b> <span id="factorDate1">  </span> </div>
                                        <div class="item2"> <b> مشتری  :  </b> <span id="customerNameFactor1"> </span>    </div>
                                        <div class="item3"> <b> آدرس  :  </b> <span id="customerAddressFactor1"> </span>   </div>
                                        <div class="item4"><span> تلفن :</span>    <span id="customerPhoneFactor1"> </span></div>
                                        <div class="item5"><span> کاربر :  </span>   <span id="Admin2"> </span></div>
                                        <div class="item6"><span>  شماره فاکتور :</span>  <span id="factorSnFactor1">  </span></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <table id="strCusDataTable" class='table table-bordered table-striped table-sm'>
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
            <div class="modal fade dragableModal" id="assesmentDashboard" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-scrollable  modal-dialog-scrollable modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header py-2">
                            <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" id="cancelAssesment" style="background-color:red;"></button>
                            <h5 class="modal-title" style="float:left;">افزودن نظر </h5>
                        </div>
                    <div class="modal-body">
                        <form action="{{url('/addAssessment')}}" id="addAssesment" method="get" style="background-color:transparent; box-shadow:none;">
                        <div class="row mb-2">
                            <div class="col-lg-10">
                              <label for="tahvilBar"> مشتری: &nbsp;</label>
                                <span id="customerComenter" style="font-size:18px;margin-bottom:11px;"></span>
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
                                 <input type="text" name="assesType" id="assesType">
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
                                    <label for="tahvilBar" >کالاهای عودتی  </label>
                                    <textarea style="background-color:blanchedalmond"  class="form-control  bg-light" style="position:relative" name="firstComment" rows="3"  ></textarea>
                                </div>
                                <div class="col-lg-6">
                                    <label for="tahvilBar"> کامنت</label>
                                    <textarea style="background-color:blanchedalmond"  class="form-control bg-light" required name="comment" rows="3" ></textarea>
                                </div>
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-lg-12 text-start">
                                <button type="submit" class="btn btn-sm btn-primary">ذخیره <i class="fa fa-save"></i></button>
                             </div>
                         </div>
                    </div>
                </form>
             </div>
           </div>
        </div>

@endsection
