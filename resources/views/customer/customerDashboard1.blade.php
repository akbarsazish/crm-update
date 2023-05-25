 @extends('layout')
@section('content')
<style>
    input[type="text"]{
       background-color: #e7e9eb;
       pointer-events: none;
    }

    .fa-comment-dots:hover{
        color:rgb(126, 1, 1);
    }

    input.mydate{
        font-size: 14px;
    }
</style>

        <!-- Modal for comments -->
    <div class="modal fade" id="customerDashboard" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <button class="btn btn-sm buttonHover crmButtonColor mx-3" id="customerEdit" style=" float:left;">ذخیره <i class="fa fa-save"> </i> </button>
                        <Button class="btn btn-sm buttonHover crmButtonColor float-end" data-toggle="modal" data-target="#addComment" type="button" value="" name="" > کامنت <i class="fas fa-comment fa-lg"> </i> </Button>
                        <span class="fw-bold fs-4"> ({{$customer->Name}})</span>
                    <div class=" tab-pane active" id="custInfo" style="border-radius:10px 10px 2px 2px; padding:0">
                        <fieldset class="row c-checkout rounded-3 m-0" style='padding-right:0; padding-top:0.5%'>
                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-4 mb-2">
                                            <div class="form-outline" style="padding-bottom:1%">
                                                <label class="dashboardLabel form-label">کد</label>
                                                <input type="text" class="form-control form-control-sm"  name="" value="{{$customer->PCode}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="form-outline " style="padding-bottom:1%">
                                                <label class="dashboardLabel form-label">نام و نام خانوادگی</label>
                                                <input type="text" class="form-control form-control-sm"  name=""  value="{{trim($customer->Name)}}" >
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="dashboardLabel form-label"> تعداد فاکتور </label>
                                                <input type="text" class="form-control form-control-sm" name="" >
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                            <div class="form-group">
                                                <label class="dashboardLabel form-label"> آدرس </label>
                                                <input type="text" class="form-control form-control-sm" name="" value="{{trim($customer->peopeladdress)}}">
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="dashboardLabel form-label">  تلفن ثابت </label>
                                                <input class="form-control " type="text" name="" >
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="form-group">
                                                <label class="dashboardLabel form-label">  تلفن همراه 1 </label>
                                                <input class="form-control " type="text" name="" >
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 mb-3">
                                            <div class="form-group">
                                                <label class="dashboardLabel form-label">  تلفن همراه 2 </label>
                                                <input class="form-control " type="text" name="" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <div class="mb-3" style="width:300px;">
                                        <label for="exampleFormControlTextarea1" class="form-label fw-bold">یاداشت  </label>
                                        <textarea style="background-color:blanchedalmond" class="form-control" id="exampleFormControlTextarea1" rows="3" ></textarea>
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
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments"> کامنت ها </a></li>
                            </ul>
                        </div>
                        <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead>
                                            <tr>
                                                <th style="width:44px;"> ردیف</th>
                                                <th style="width:111px;">تاریخ</th>
                                                <th style="width:170px;"> نام راننده</th>
                                                <th style="width:300px;">مبلغ </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td style="width:44px;"> 1 </td>
                                                <td style="width:111px;"></td>
                                                <td style="width:170px;"></td>
                                                <td style="width:300px;">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead>
                                            <tr>
                                                <th style="width:44px;"> ردیف</th>
                                                <th style="width:111px;">تاریخ</th>
                                                <th style="width:170px;"> نام کالا</th>
                                                <th style="width:70px;">تعداد </th>
                                                <th style="width:100px;">فی</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td style="width:44px;"> 1 </td>
                                                <td style="width:111px;"></td>
                                                <td style="width:170px;"></td>
                                                <td style="width:70px;">
                                                <td style="width:100px;">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="userLoginInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead>
                                            <tr>
                                                <th style="width:44px;"> ردیف</th>
                                                <th style="width:111px;">تاریخ</th>
                                                <th style="width:170px;"> نام کالا</th>
                                                <th style="width:70px;">تعداد </th>
                                                <th style="width:100px;">فی</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td style="width:44px;"> 1 </td>
                                                <td style="width:111px;"></td>
                                                <td style="width:170px;"></td>
                                                <td style="width:70px;">
                                                <td style="width:100px;">

                                                </td>
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
                                            <thead>
                                            <tr>
                                                <th style="width:44px;"> ردیف</th>
                                                <th style="width:111px;">تاریخ</th>
                                                <th style="width:170px;"> نام کالا</th>
                                                <th style="width:70px;">تعداد </th>
                                                <th style="width:100px;">فی</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td style="width:44px;"> 1 </td>
                                                <td style="width:111px;"></td>
                                                <td style="width:170px;"></td>
                                                <td style="width:70px;">
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
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead>
                                            <tr>
                                                <th style="width:44px;"> ردیف</th>
                                                <th style="width:80px;">تاریخ</th>
                                                <th style="width:300px;"> کامنت</th>
                                                <th style="width:300px;"> کامنت بعدی</th>
                                                <th style="width:80px;"> تاریخ بعدی </th>
                                                <th style="width:40px;"> انتخاب </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td style="width:44px;"> 1 </td>
                                                <td style="width:80px;"> </td>
                                                <td style="width:300px;"  data-bs-toggle="modal" data-bs-target="#viewComment"> <i class="fas fa-comment-dots float-end"></i> </td>
                                                <td style="width:300px;"> <i class="fas fa-comment-dots float-end"></i>  </td>
                                                <td style="width:80px;"> </td>
                                                <td style="width:40px;"> </td>
                                            </tr>
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


            {{-- modal for adding comments --}}
            <div class="modal fade" id="addComment" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <label for="tahvilBar">نوع تماس</label>
                                <select class="form-select">
                                    <option value="">موبایل  </option>
                                    <option value=""> تلفن ثابت </option>
                                    <option value=""> واتساپ</option>
                                    <option value="">حضوری </option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                               <input class="date mt-3 fw-bold p-2 mydate" type="text" value="2/12/1401">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar">کامنت </label>
                                <textarea style="background-color:blanchedalmond" class="form-control" id="exampleFormControlTextarea1" rows="7"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 m-3 fw-bold">
                                <span style="float:right;">زمان تماس بعدی
                                    <input class="card" type="date"span>
                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar">کامنت بعدی</label>
                                <textarea style="background-color:blanchedalmond" class="form-control" id="exampleFormControlTextarea1" rows="5" ></textarea>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="deleteConfirm()">بستن</button>
                            <button type="button" class="btn btn-primary">ذخیره </button>
                    </div>
                </div>
                </div>
            </div>
    <script>


        $(document).ready(function() {
            $("#customerDashboard").modal("show");
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
    });
</script>

<script>
    function deleteConfirm() {
      swal({
              title: "مطمئین هستید؟",
              text: "پس از خذف نمی توانید این فایل را بازیابی نمایید!",
              icon: "warning",
              buttons: true,
              dangerMode: true,
              })

      .then((willDelete) => {
          if (willDelete) {
              swal("فایل شما حذف گردید!", {
              icon: "success",
              });
          }
          });
     }

  </script>
@endsection
