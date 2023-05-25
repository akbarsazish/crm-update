@extends('layout')
@section('content')
<main>
    <div class="container-xl px-4" style="margin-top:6%;">
        <span class="page-title">لیست مشتریان</span>
    <div class="card mb-4" style="margin: 0; padding:0;">
        <div class="card-body">
        <div class="row">
        <div class="col-sm-12">
                    <div class="well" style="margin-top:2%;">
                        <div class="c-checkout container p-1 pb-4 rounded-3" style="">
                            <span class="row" style="margin: 0;">
                                <div class="form-group col-sm-2">
                                    <label class="dashboardLabel form-label"> جستجو</label>
                                    <input type="text" name="" size="20" class="form-control" id="searchAllCName">
                                </div>
                                <div class="form-group col-sm-2">
                                    <label class="dashboardLabel form-label"> جستجوی کد حساب</label>
                                    <input type="text" name="" size="20" class="form-control" id="searchAllCCode">
                                </div>
                                <div class="form-group col-sm-2">
                                    <label class="dashboardLabel form-label"> مرتب سازی</label>
                                    <select class="form-select" id="orderAllByCName">
                                        <option value="1">اسم</option>
                                        <option value="0">کد</option>
                                    </select>
                                </div>
                            </span>
                        </div>
                        <div class="row" style="margin: 0; padding:0;">
                            <div class="alert col-sm-3" style="padding: 0; margin:0;">
                                <span class="row form-outline">
                                    <label class="dashboardLabel form-label" for="form1" style="margin: 0; padding:0;"></label>
                                    <input type="text" id="dataTableComplateSearch" class="form-control" placeholder="Complete Search"/>
                                </span>
                            </div>
                            <div class="col-sm-8" style="padding: 0; padding-right:2%; margin:0; display:flex; justify-content:flex-end;">
                                    <button class='enableBtn btn-primary btn-lg text-warning' type="button" id='openDashboard'> داشبورد <i class="fal fa-dashboard"></i></button>
                             </div>
                        </div>
                            <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">
                                <table id="" class='homeTables crmDataTable display table table-bordered table-striped table-sm' style='td:hover{ cursor:move;}; text-align:center; margin-top:-10 !important;'>
                                <thead style="position: sticky;top: 0;">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>کد</th>
                                        <th>اسم</th>
                                        <th>آدرس </th>
                                        <th>تلفن</th>
                                        <th>همراه</th>
                                        <th>منطقه </th>
                                        <th>درج </th>
                                        <th>انتخاب</th>
                                    </tr>
                                    </thead>
                                    <tbody class="select-highlight" id="customerListBody1">
                                        @foreach ($customers as $customer)
                                            <tr >
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{trim($customer->PCode)}}</td>
                                                <td>{{trim($customer->Name)}}</td>
                                                <td>{{trim($customer->peopeladdress)}}</td>
                                                <td><a href="tel:{{trim($customer->sabit)}}" style="color:black; font-size:14px">{{trim($customer->sabit)}} </a> </td>
                                                <td><a href="tel:{{trim($customer->sabit)}}" style="color:black; font-size:14px">{{trim($customer->hamrah)}} </a> </td>
                                                <td>{{trim($customer->NameRec)}}</td>
                                                <td>2</td>
                                                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="{{$customer->PSN}}"></td>
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
    <div class="modal fade" id="customerDashboard" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close btn-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel"> داشبورد </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 coll-sm-12">
                            <span class="fw-bold fs-4"  id="dashboardTitle"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 coll-sm-12">
                            <Button class="btn btn-sm buttonHover crmButtonColor float-end  mx-2" id="openAddCommentModal" type="button" value="" name="" > کامنت <i class="fas fa-comment fa-lg"> </i> </Button>
                            <form action="https://starfoods.ir/crmLogin" target="_blank"  method="get">
                                <input type="text" id="customerSn" style="display: none" name="psn" value="" />
                                    <input type="hidden" name="otherName" value="{{trim(Session::get('username'))}}" />
                                        <Button class="btn btn-md btn-success buttonHover float-end" type="submit"> ورود جعلی  <i class="fas fa-sign-in fa-lg"> </i> </Button>
                            </form>
                        </div>
                    </div><hr>
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
                                    <label for="exampleFormControlTextarea1" class="form-label fw-bold">یاداشت  </label>
                                    <textarea   style="background-color:blanchedalmond" class="form-control" id="customerProperty"  onblur="saveCustomerCommentProperty(this)" rows="6" ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                        <div class="col-sm-8" style="margin: 0; padding:0;">
                            <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                                <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری کرده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#userLoginInfo1"> کالاهای سبد خرید</a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#picture1"> فاکتور های برگشت داده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments"> کامنت ها </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments"> نظرسنجی ها</a></li>
                            </ul>
                        </div>
                        <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="crmDataTable dashbordTables table table-bordered table-striped" style="text-align:center;">
                                            <thead>
                                            <tr>
                                                <th style="width:200px"> ردیف </th>
                                                <th style="width:200px">تاریخ </th>
                                                <th style="width:200px"> نام راننده </th>
                                                <th style="width:200px">مبلغ </th>
                                                <th style="width:200px">مشاهده  </th>
                                            </tr>
                                            </thead>
                                            <tbody  id="factorTable"  style="height:150px; overflow-y:scroll; display:block;width:100%">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="crmDataTable dashbordTables tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead>
                                            <tr>
                                                <th style="width:200px;"> ردیف</th>
                                                <th style="width:200px;">تاریخ</th>
                                                <th style="width:200px;"> نام کالا</th>
                                            </tr>
                                            </thead>
                                            <tbody id="goodDetail" style="height:150px; overflow-y:scroll; display:block;">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="userLoginInfo1" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="crmDataTable dashbordTables tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead>
                                            <tr>
                                                <th style="width:44px;"> ردیف</th>
                                                <th style="width:111px;">تاریخ</th>
                                                <th style="width:170px;"> نام کالا</th>
                                                <th style="width:70px;">تعداد </th>
                                                <th style="width:100px;">فی</th>
                                            </tr>
                                            </thead>
                                            <tbody id="basketOrders" style="height:150px; overflow-y:scroll; display:block;">
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

                            <div class="c-checkout tab-pane" id="picture1" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="crmDataTable dashbordTables tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead>
                                            <tr>
                                                <th style="width:44px;"> ردیف</th>
                                                <th style="width:111px;">تاریخ</th>
                                                <th style="width:170px;"> نام کالا</th>
                                                <th style="width:70px;">تعداد </th>
                                                <th style="width:100px;">فی</th>
                                            </tr>
                                            </thead>
                                            <tbody style="height:150px; overflow-y:scroll;display:block;width:100%">
                                            <tr>
                                                <td style="width:44px;"> 1 </td>
                                                <td style="width:111px;"></td>
                                                <td style="width:170px;"></td>
                                                <td style="width:70px;"></td>
                                                <td style="width:100px;">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="c-checkout tab-pane" id="comments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="crmDataTable dashbordTables tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead>
                                            <tr>
                                                <th style="width:200px;"> ردیف</th>
                                                <th style="width:180px;">تاریخ</th>
                                                <th style="width:220px;"> کامنت</th>
                                                <th style="width:220px;"> کامنت بعدی</th>
                                                <th style="width:180px;"> تاریخ بعدی </th>
                                                <th style="width:40px;"> انتخاب </th>
                                            </tr>
                                            </thead>
                                            <tbody id="customerComments"   style="height:150px; overflow-y:scroll;display:block;width:100%">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="c-checkout tab-pane" id="assesments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="crmDataTable dashbordTables tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead>
                                            <tr>
                                                <th style="width:44px;"> ردیف</th>
                                                <th style="width:80px;">تاریخ</th>
                                                <th style="width:300px;"> کامنت</th>
                                                <th style="width:300px;"> برخورد راننده</th>
                                                <th style="width:80px;"> مشکل در بارگیری</th>
                                                <th style="width:80px;"> کالاهای برگشتی</th>
                                                <th style="width:40px;"> انتخاب </th>
                                            </tr>
                                            </thead>
                                            <tbody id="customerAssesments"   style="height:150px; overflow-y:scroll;display:block;width:100%">

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
    <div class="modal fade" id="viewComment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
        <h5 class="modal-title" id="exampleModalLabel">تغیر آلارم </h5>
        </div>
        <div class="modal-body" id="readCustomerComment">
                کامنت ها
        </div>
        <div class="modal-footer">
           <button type="button" class="btn btn-danger" data-bs-dismiss="modal">بستن</button>
        </div>
    </div>
    </div>
</div>
    <!-- Modal for returning customer-->
    <div class="modal fade" id="returnComment"  aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" style="background-color:red;"></button>
            <h5 class="modal-title" id="exampleModalLabel"> دلیل ارجاع </h5>
            </div>
            <form action="{{url('/returnCustomer')}}" id="returnCustomerForm" method="get">
            <div class="modal-body">
                <input type="text" name="returnCustomerId" id="returnCustomerId">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="tahvilBar">دلیل ارجاع</label>
                        <textarea  style="background-color:blanchedalmond" class="form-control" style="position:relative" name="returnComment" rows="3" ></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-primary"> ذخیره  <i class="fal fa-save"> </i> </button>
            </div>
        </form>
        </div>
        </div>
    </div>
            {{-- modal for adding comments --}}
        <div class="modal" id="addComment" data-bs-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel">
            <div class="modal-dialog modal-dialog-scrollable ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close" style="background-color:red;"></button>
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
                            <input type="text" name="customerIdForComment" id="customerIdForComment">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label for="tahvilBar" >کامنت </label>
                            <textarea  style="background-color:blanchedalmond" class="form-control" style="position:relative" name="firstComment" rows="3" ></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 fw-bold">
                            <label for="tahvilBar" >زمان تماس بعدی </label>
                                <input class="form-control" autocomplete="off" name="nextDate" id="commentDate2">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label for="tahvilBar">کامنت بعدی</label>
                            <textarea  style="background-color:blanchedalmond" class="form-control" name="secondComment" rows="5" ></textarea>
                            <input class="form-control" type="text" style="display: none;" name="place" value="customers"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="deleteConfirm()">بستن <i class="fa fa-xmark"></i></button>
                        <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save"></i></button>
                </div>
            </form>
            </div>
            </div>
        </div>
</main>
<script>
        $('#strCusDataTable').DataTable({
            "paging" :true,
            "scrollCollapse" :true,
            "searching" :true,
            "info" :true,
            "columnDefs": [ {
                "searchable": false,
                "orderable": false,
                "targets":[0,8],
            } ],

            "dom":"lrtip",
            "order": [[ 1, 'asc' ]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.12.1/i18n/fa.json"
            }
        } );


       let oTable = $('#strCusDataTable').DataTable();
       $('#dataTableComplateSearch').keyup(function(){
          oTable.search($(this).val()).draw() ;
    });

    $('.withQuality').select2({
        dropdownParent: $('#addComment'),
        width: '100%'
    });

    $('.noQuality').select2({
        dropdownParent: $('#addComment'),
        width: '100%'
    });

    $('.returned').select2({
        dropdownParent: $('#addComment'),
        width: '100%'
    });

    $.ajax({
        method: 'get',
        url: baseUrl + "/getProducts",
        data: {
            _token: "{{ csrf_token() }}"
        },
        async: true,
        success: function(arrayed_result) {
            $('#prductQuality').empty();
            $('#prductNoQuality').empty();
            $('#returnedProducts').empty();
            arrayed_result.forEach((element, index) => {

                $('#prductQuality').append(`
                    <option value="`+element.GoodSn+`">`+element.GoodName+`</option>
                `);

                $('#returnedProducts').append(`
                    <option value="`+element.GoodSn+`">`+element.GoodName+`</option>
                `);

                $('#prductNoQuality').append(`
                    <option value="`+element.GoodSn+`">`+element.GoodName+`</option>
                `);
            });
        },
        error: function(data) {
        }
    });

    </script>
@endsection
