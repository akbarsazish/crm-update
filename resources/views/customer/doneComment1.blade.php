@extends('layout')
@section('content')
<main>
    <div class="container-xl px-4 mt-n10" style="margin-top:6%;">
        <h3 class="page-title">نظرات انجام شده  </h3>
        <div class="card mb-4" style="margin: 0; padding:0;">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="well" style="margin-top:1%;">
                            <span class="row" style="margin: 0; margin-bottom:10px;">
                                <div class="form-group col-sm-2">
                                    <input type="text" name="" size="20" class="form-control publicTop" value="جستجو" id="allKalaFirst">
                                </div>
                                <div class="form-group col-sm-2 px-0">
                                    <input type="text" name="" size="20" class="form-control publicTop" value="از تاریخ" id="firstDateDoneComment">
                                </div>
                                <div class="form-group col-sm-1" style="text-align:center">
                                    <label class="dashboardLabel form-label" >الی</label>
                                </div>
                                <div class="form-group col-sm-2 px-0">
                                    <input type="text" name="" size="20" class="form-control publicTop" value="تا تاریخ" id="secondDateDoneComment">
                                </div> 
                            </span>

                            <div class="row" style="margin: 0; padding:0;">
                                <div class="alert col-sm-8" style="padding: 0; padding-right:2%; margin:0;">
                                    <form action="{{ url('/assesCustomer') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="text" id="customerSn" style="display: none" name="customerSn" value="" />
                                        <input type="text" id="customerGroup" style="display: none" name="customerGRP" value="" />
                                    </form>
                                </div>
                            </div>
                            <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">
                                <table id="strCusDataTable" class='table table-bordered table-striped table-sm'>
                                    <thead class="tableHeader">
                                        <tr>
                                            <th>ردیف</th>
                                            <th>اسم</th>
                                            <th>همراه</th>
                                            <th>تاریخ </th>
                                            <th>کامنت </th>
                                            <th> نظر دهنده</th>
											 <th>عودتی</th>
                                        </tr>
                                    </thead>
                                    <tbody class="select-highlight tableBody" id="customerListBodyDone">
                                        @foreach ($customers as $customer)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{trim($customer->Name)}}</td>
                                            <td>{{trim($customer->hamrah)}}</td>
                                            <td>{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($customer->TimeStamp))->format('Y/m/d H:i:s')}}</td>
                                            <td onclick='showAssesComment("{{$customer->assesId}}")'>{{substr($customer->comment,0,10).'...'}} <i class="fas fa-comment-dots float-end"> </i></td>
                                            <td>{{trim($customer->AdminName.' '.$customer->lastName)}}</td>
											 <td data-toggle="modal" data-target="#owdati"> <i class="fas fa-dolly-flatbed "> </i></td>
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
<div class="modal fade" id="doneComments" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable  modal-xl">
        <div class="modal-content"  style="background-color:#d2e9ff;">
            <div class="modal-header" style="border-bottom:1px solid rgb(7, 42, 214);">
                <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"  style="background-color:#d2e9ff;">
                    <button class="btn btn-sm buttonHover crmButtonColor mx-3" id="customerEdit" style=" float:left;">ذخیره <i class="fa fa-save"> </i> </button>
                    <Button class="btn btn-sm buttonHover crmButtonColor float-end" data-toggle="modal" data-target="#addComment" type="button" value="" name="" > افزودن نظر <i class="fa fa-address-card fa-lg"> </i> </Button>
                    <span class="fw-bold fs-4"  id="dashboardTitle"></span>
                <div class=" tab-pane active" id="custInfo" style="border-radius:10px 10px 2px 2px; padding:0; background-color:#d2e9ff">
                    <fieldset class="row c-checkout rounded-3 m-0" style='padding-right:0; padding-top:0.5%; background-color:#d2e9ff'>
                        <div class="row" style="background-color:#d2e9ff;">
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <div class="row mt-2">
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
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 mt-2">
                                <div class="mb-3" style="width:300px;">
                                    <label for="exampleFormControlTextarea1" class="form-label fw-bold">یاداشت  </label>
                                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" style="background-color:#d2e9ff" ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                    <div class="col-sm-8" style="margin: 0; padding:0;">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                            <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری کرده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#userLoginInfo"> کالاهای سبد خرید</a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#pictures"> فاکتور های برگشت داده </a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments">کامنت ها</a></li>
                            <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments">  نظرسنجی ها </a></li>
                        </ul>
                    </div>
                    <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                        <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="col-sm-12">
                                <table class="homeTables table table-bordered table-striped table-sm" style="text-align:center;">
                                    <thead style="position: sticky;top: 0;">
                                    <tr>
                                        <th style="width:44px;"> ردیف</th>
                                        <th style="width:111px;">تاریخ</th>
                                        <th style="width:170px;"> نام راننده</th>
                                        <th style="width:300px;">مبلغ </th>
                                    </tr>
                                    </thead>
                                    <tbody  id="factorTable">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="col-sm-12">
                                    <table class="homeTables table table-bordered table-striped table-sm" style="text-align:center;">
                                        <thead style="position: sticky;top: 0;">
                                            <tr>
                                                <th style="width:44px;"> ردیف</th>
                                                <th style="width:111px;">تاریخ</th>
                                                <th style="width:170px;"> نام کالا</th>
                                                <th style="width:70px;">تعداد </th>
                                                <th style="width:100px;">فی</th>
                                            </tr>
                                        </thead>
                                        <tbody id="goodDetail">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row c-checkout rounded-3 tab-pane" id="userLoginInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="col-sm-12">
                                    <table class="homeTables table table-bordered table-striped table-sm" style="text-align:center;">
                                        <thead style="position: sticky;top: 0;">
                                        <tr>
                                            <th style="width:44px;"> ردیف</th>
                                            <th style="width:111px;">تاریخ</th>
                                            <th style="width:170px;"> نام کالا</th>
                                            <th style="width:70px;">تعداد </th>
                                            <th style="width:100px;">فی</th>
                                        </tr>
                                        </thead>
                                        <tbody id="basketOrders">
                                        <tr>
                                            <td style="width:44px;"> 1 </td>
                                            <td style="width:111px;"></td>
                                            <td style="width:170px;"></td>
                                            <td style="width:70px;"></td>
                                            <td style="width:100px;"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="c-checkout tab-pane" id="pictures" style="margin:0; border-radius:10px 10px 2px 2px;">
                            <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                        <thead class="tableHeader">
                                        <tr>
                                            <th > ردیف</th>
                                            <th>تاریخ</th>
                                            <th> نام کالا</th>
                                            <th>تعداد </th>
                                            <th>فی</th>
                                        </tr>
                                        </thead>
                                        <tbody class="tableBody">
                                        <tr>
                                            <td ></td>
                                            <td></td>
                                            <td></td>
                                            <td style="width:70px;"></td>
                                            <td style="width:100px;"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="c-checkout tab-pane" id="comments" style="margin:0; border-radius:10px 10px 2px 2px;">
                            <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="col-sm-12">
                                    <table class="homeTables table table-bordered table-striped table-sm" style="text-align:center;">
                                        <thead  style="position: sticky;top: 0;">
                                        <tr>
                                            <th style="width:44px;"> ردیف</th>
                                            <th style="width:80px;">تاریخ</th>
                                            <th style="width:300px;"> کامنت</th>
                                            <th style="width:300px;"> کامنت بعدی</th>
                                            <th style="width:80px;"> تاریخ بعدی </th>
                                            <th style="width:40px;"> انتخاب </th>
                                        </tr>
                                        </thead>
                                        <tbody id="customerComments">
                                        </tbody>
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
                                        <tbody id="customerAssesments"  >
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
        {{-- modal for reading comments --}}
        <div class="modal fade" id="readAssesComment" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-scrollable  modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                <div class="modal-body" style="background-color: #d2e9ff;">
                    <h3 id="assesComment"></h3>
                </div>
            </div>
        </div>
        {{-- modal for reading owdati --}}
        <div class="modal fade" id="owdati2" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-scrollable  modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                <div class="modal-body" style="background-color: #d2e9ff;">کامنت</div>
            </div>
        </div>
    </div>
</main>
<!-- <link rel="stylesheet" href="http://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script> -->

@endsection
