@extends('layout')
@section('content')
<style>
        ul, #myUL {
        list-style-type: none;
        }

        #myUL {
        margin: 0;
        padding: 0;
        }

        .caret {
        cursor: pointer;
        -webkit-user-select: none; /* Safari 3.1+ */
        -moz-user-select: none; /* Firefox 2+ */
        -ms-user-select: none; /* IE 10+ */
        user-select: none;
        }
        span.caret {
            font-size:18px;
            font-weight:bold;

        }

        .lowLevelManager{
            font-size:16px;
            color:#0b2d62;
        }
        .caret::before {
        content: "\002B";
        color: black;
        display: inline-block;
        margin-right: 6px;
        }

        .caret-down::before {
        -ms-transform: rotate(90deg); /* IE 9 */
        -webkit-transform: rotate(90deg); /* Safari */'
        transform: rotate(90deg);  
        }

        .nested {
        display: none;
        }

        .active {
        display: block;
        }
</style>
    <div class="container-fluid containerDiv">
        <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                <fieldset class="border rounded mt-5 sidefieldSet">
                    <legend  class="float-none w-auto legendLabel mb-0"> کاربران </legend>
                    <div class="col-lg-12" style="margin-top:40vh">
                        <div class="row px-3">
                            <button type="button" class="btn btn-primary btn-sm text-warning buttonHover" id="takhsisToAdminBtn" disabled> تخصیص مشتری <i class="fa fa-plus fa-lg" aria-hidden="true"></i></a>
                            <button type="button" class="btn btn-primary btn-sm text-warning buttonHover" id="adminTasviyahBtn" disabled> تسویه <i class="fa fa-plus fa-lg" aria-hidden="true"></i></a>
                            <input type="hidden" id="AdminForAdd"/>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                <div class="row contentHeader"> </div>
                    <div class="row mainContent">
                        <!-- start tree view -->
                        <div class="col-lg-12 rounded shadow bg-light">
                            <ul id="myUL">
                            @foreach($saleLines as $line)
                                <li><span class="caret ">{{$line->LineName}} </span>
                                    <ul class="nested">
                                        @foreach($line->manager as $manager)
                                            <li><span class="caret" onclick="setManagerStuff(this,{{$manager->id}})">{{$manager->name .' '.$manager->lastName }} <input type="radio" class="form-check-input" style="display:none"  value="{{$manager->id}}" name="manager" id=""></span>
                                                <ul class="nested">
                                                    @foreach($manager->head as $head)
                                                        <li>
                                                            <span class="caret"  onclick="setHeadOpStuff(this,{{$head->id}})">{{$head->name .' '.$head->lastName }} <input type="radio" class="form-check-input"  style="display:none"   value="{{$head->id}}" name="head" id=""></span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach  
                                    </ul>
                                </li>
                            @endforeach
                            </ul>
                        </div>

                        <!-- end tree view -->
                        <div class="col-lg-12">
                            <div class="row">
                                <table class="select-highlight table table-bordered table-striped">
                                    <thead class="tableHeader">
                                        <tr>
                                            <th>ردیف</th>
                                            <th>نام </th>
                                            <th>شماره تماس</th>
                                            <th>توضیحات</th>
                                            <th>انتخاب</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tableBody" id="customerListBody" style="height:300px">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <div class="row contentFooter"> </div>
            </div>
        </div>
    </div>       <!-- modal of takhsis -->
    <div class="modal fade dragableModal" id="takhsisCustomerModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog   modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel"> تخصیص مشتری به <span id="takhsisAdminName"></span> </h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row" id="customerContainer">
                                <div class="col-sm-5">
                                        <span class="row" style="margin: 0;">
                                        <div class="form-group col-sm-4">
                                            <input type="text" style="display:none" id="asn"/>
                                                <select name=""  class="form-select publicTop" id="searchCity">
                                                <option value="" hidden>شهر</option>
                                                    <option value="0">همه</option>
                                                    @foreach($cities as $city)
                                                        <option value="{{$city->SnMNM}}">{{trim($city->NameRec)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <select name="" class="form-select publicTop" id="searchMantagheh">
                                                    <option hidden>منطقه</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <select name="" class="form-select publicTop" disabled id="activeOrInActive">
                                                    <option hidden>نوعیت مشتری</option>
                                                    <option value="1">فعال</option>
                                                    <option value="2">غیر فعال</option>
                                                    <option value="3">جدیدی ها</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-sm-4">
                                                <input type="text" name="" placeholder="اسم" size="20" class="form-control publicTop" id="searchNameByMNM">
                                            </div>
                                        </span> <br>
                                        <input type="text" id="AdminForAdd" style="display: none" >
                                        <div class='c-checkout'>
                                            <table class="table table-bordered table-striped table-hover" id="allCustomers">
                                                <thead class="tableHeader">
                                                    <tr>
                                                        <th>ردیف</th>
                                                        <th>کد مشتری</th>
                                                        <th> نام و نام خانوادگی</th>
                                                        <th>
                                                            <input type="checkbox" name="" class="selectAllFromTop form-check-input" id="selectAllTopRight"  >
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="allCustomer" class="tableBody">

                                                </tbody>
                                            </table>
                                        </div>
                                </div>

                                <div class="col-sm-2" style="">
                                    <div class='modal-body' style="position:relative; right: 33%; top: 30%;">
                                        <div style="">
                                            <a id="addCustomerToAdminOp">
                                                <i class="fa-regular fa-circle-chevron-left fa-3x"></i>
                                            </a>
                                            <br />
                                            <a id="removeCustomerFromAdminOp">
                                                <i class="fa-regular fa-circle-chevron-right fa-3x"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                        <span class="row" style="margin: 0;">
                                            <div class="form-group col-sm-4">
                                                <select name=""  class="form-select publicTop" id="searchAddedCity">
                                                <option value="0" hidden>شهر</option>
                                                <option value="1" >همه</option>
                                                    @foreach($cities as $city)

                                                    <option value="{{$city->SnMNM}}">{{trim($city->NameRec)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <select name="" class="form-select publicTop" id="searchAddedMantagheh">
                                                    <option value="" hidden>منطقه</option>
                                                    <option value="0">همه</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-sm-4">
                                                <input type="text" name="" size="20" placeholder="اسم" class="form-control publicTop" id="searchAddedNameByMNM">
                                            </div>
                                        </span> <br>

                                        <div class='c-checkout'>
                                            <table class="table table-bordered table-striped table-hover"  id="addedCustomers">
                                                <thead class="tableHeader">
                                                    <tr>
                                                        <th>ردیف</th>
                                                        <th>کد مشتری</th>
                                                        <th> نام و نام خانوادگی</th>
                                                        <th>
                                                            <input type="checkbox" name="" class="selectAllFromTop form-check-input" id="selectAllTopLeft">
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="addedCustomer" class="tableBody">
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
    </div>
</div>

                <!-- end of takhsis -->
            </div>
        </div>
    </div>
</section>

<script>
	$("#newAdminBtn").on("click", ()=>{
		   if (!($('.modal.in').length)) {
                $('.modal-dialog').css({
                  top: 0,
                  left: 0
                });
              }
              $('#newAdmin').modal({
                backdrop: false,
                show: true
              });
              
              $('.modal-dialog').draggable({
                  handle: ".modal-header"
                });
		$("#newAdmin").modal("show");
	});
	

var toggler = document.getElementsByClassName("caret");
var i;

for (i = 0; i < toggler.length; i++) {
  toggler[i].addEventListener("click", function() {
    this.parentElement.querySelector(".nested").classList.toggle("active");
    this.classList.toggle("caret-down");
  });
}
</script>
@endsection
