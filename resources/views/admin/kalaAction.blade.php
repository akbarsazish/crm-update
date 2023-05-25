@extends('layout')
@section('content')
<style>
  #kalaSalesReport, #returnedKalaTable, #notExistKalaTable, #rocketKalaTable, #footerBtn{
    display:none;
  }
  .forPrint{
    display:none;
  }

@media print {
  body {
    margin: 0;
    color: #000;
    background-color: #fff;
  }
}

.header-list li a{
    color:#000;
    font-size:14px;
    font-weight:bold;
}

input[type=checkbox] {
        padding:5px;
        margin-left:8px;
    }
.modalTableBody {
    height:244px !important;
}

.kalaImage {
    width:144px;
    height: auto;
}

</style>
<div class="modalBackdrop">
    <div id='unitStuffContainer' class="alert alert-danger" style="max-width: 200px; background-color: #ffffff66; padding: 5px; width: 100%; max-height: 85vh; overflow: auto;">
    </div>
</div>
<div class="container-fluid containerDiv">
      <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                   <fieldset class="border rounded mt-5 sidefieldSet">
                        <legend  class="float-none w-auto legendLabel mb-0"> تنظیمات </legend>
                          <div class="row">
                             @if(hasPermission(Session::get("asn"),"goodsReportN") > 0)
                                <div class="form-check">
                                    <input class="form-check-input p-2 float-end" type="radio" name="settings" id="allKalaRadio" checked>
                                    <label class="form-check-label me-4" for="assesPast"> همه کالا ها </label>
                                </div>
                                <!-- <div class="form-check">
                                    <input class="form-check-input p-2 float-end" type="radio" name="settings" id="salesKalaReportRadio">
                                    <label class="form-check-label me-4" for="assesPast"> گزارش فروش کالا </label>
                                </div> -->
                                <div class="form-check">
                                    <input class="form-check-input p-2 float-end" type="radio" name="settings" id="returnKalaReportRadio">
                                    <label class="form-check-label me-4" for="assesPast"> کالا های برگشتی  </label>
                                </div>
                                <!-- <div class="form-check">
                                    <input class="form-check-input p-2 float-end" type="radio" name="settings" id="notExistKalaRadio">
                                    <label class="form-check-label me-4" for="assesPast"> کالاهای فاقد موجودی </label>
                                </div> -->
                                <div class="form-check mb-2">
                                    <input class="form-check-input p-2 float-end" type="radio" name="settings" id="rakidKalaReportRadio">
                                    <label class="form-check-label me-4" for="assesPast"> کالاهای راکد </label>
                                </div>
                                    <div class="col-sm-12">
                                        <input type="text" id="searchKalaNameCode"  class="form-control form-control-sm" autocomplete="off"  placeholder="اسم یا کد کالا" id="searchKalaNameCode">
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="input-group input-group-sm mt-2">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">گروه اصلی</span>
                                                <select name="original" class="form-select" id="superGroup"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="input-group input-group-sm mt-2">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">گروه فرعی</span>
                                                <select name="subGroups" class="form-select" id="subGroup">
                                                <option value="">همه</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-group input-group-sm mt-2">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">انبار</span>
                                            <select class="form-select form-select-sm" id="searchKalaStock">
                                                <option value="0" selected>همه</option>
                                                @foreach ($stocks as $stock)
                                                <option value="{{$stock->SnStock}}">{{trim($stock->NameStock)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-group input-group-sm mt-2">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">فعال</span>
                                            <select class="form-select form-select-sm" id="searchKalaActiveOrNot">
                                                <option value="" > همه </option>
                                                <option value="0"> فعال </option>
                                                <option value="1"> غیر فعال </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-group input-group-sm mt-2">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">موجودی</span>
                                            <select class="form-select form-select-sm" id="searchKalaExistInStock">
                                                <option value="-1">همه</option>
                                                <option value="0"> موجودی صفر </option>
                                                <option value="1"> موجودی عدم صفر </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 allKalaTools">
                                        <div class="input-group input-group-sm mt-2">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"> از تاریخ خرید </span>
                                            <input type="text" class="form-control" id="assesFirstDate">
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"> تا تاریخ  خرید</span>
                                            <input type="text" class="form-control" id="assesSecondDate">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 mb-1 allKalaTools">
                                        <button class='btn btn-primary btn-sm text-warning kalaBtn'  onclick="filterAllKala()" type="button"> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                                    </div>

                                    <div class="col-sm-12 rakidKalaTools" style="display:none">
                                        <div class="input-group input-group-sm mt-2">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"> از تاریخ </span>
                                            <input type="text" class="form-control" id="firstDateRakid">
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"> تا تاریخ </span>
                                            <input type="text" class="form-control" id="secondDateRakid">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 mb-1 rakidKalaTools" style="display:none">
                                        <button class='btn btn-primary btn-sm text-warning kalaBtn'  onclick="filterRakidKala()" type="button"> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                                    </div>

                                    <div class="col-sm-12 returnedKalaTools" style="display:none">
                                        <div class="input-group input-group-sm mt-2">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"> از تاریخ  </span>
                                            <input type="text" class="form-control" id="firstDateReturn">
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"> تا تاریخ  </span>
                                            <input type="text" class="form-control" id="secondDateReturn">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 mb-1 returnedKalaTools" style="display:none">
                                        <button class='btn btn-primary btn-sm text-warning kalaBtn'  onclick="filterReturnedKala()" type="button"> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                                    </div>

                            @endif
                            <div class="col-lg-12">
                                @if(hasPermission(Session::get("asn"),"goodsReportN") > 0)
                                    <button type="button" class="btn btn-primary btn-sm text-warning" id="kalaSettingsBtn" disabled> تنظیمات کالا  <i class="fal fa-cog" aria-hidden="true"></i></button>
                                @endif
                            </div>
                        </div>
                    </fieldset>
                  </div>
                <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                    <div class="row contentHeader kalaActionContentHeader"> 
                        <div class="col-sm-12 text-start">
                            @if(hasPermission(Session::get("asn"),"goodsReportN") > -1)
                             <form action="#" method="#" style="display: inline;">
                               <button type="button" class="btn btn-primary btn-sm text-warning" disabled id="openViewTenSalesModal"> 10 گردش آخر <i class="fal fa-history" aria-hidden="true"></i></button>
                               <button type="submit" class="btn btn-primary btn-sm text-warning"> رویت <i class="fal fa-eye" aria-hidden="true"></i></button>
                               <button type="button" onClick="getgoodSaleRound(this.value)"  class="kalaBtn btn btn-primary btn-sm text-warning"> گردش کالا <i class="fal fa-history" aria-hidden="true"></i></button>
                               <button type="submit" class="btn btn-primary btn-sm text-warning" onclick="window.print()"> پرنت <i class="fal fa-print" aria-hidden="true"></i></button>
                               <button type="submit" class="btn btn-primary btn-sm text-warning"> ارسال به اکسل  <i class="fal fa-file-excel" aria-hidden="true"></i></button>
                            </form>
                            @endif
                       </div>
                    </div>
                    <div class="row mainContent">
                        <table class="table table-bordered table-striped table-sm" id="allKala">
                            <thead class="tableHeader">
                                <tr>
                                    <th >ردیف</th>
                                    <th class="forMobile-hide" style="width:66px">کد</th>
                                    <th>اسم</th>
                                    <th id="lastDateSaleOrOther">آخرین فروش</th>
                                    <th class="forMobile-hide">غیرفعال</th>
                                    <th> موجودی </th>
                                    <th>انتخاب </th>
                                </tr>
                            </thead>
                            <tbody id='allKalaContainer' class="select-highlight Kala tableBody">
                            </tbody>
                        </table>

                        <!-- kala sales table -->
                        <table class="table table-bordered table-striped table-sm" id="kalaSalesReport">
                            <thead class="tableHeader">
                                <tr>
                                    <th >ردیف</th>
                                    <th style="width:88px">کد</th>
                                    <th style="width:333px">اسم</th>
                                    <th> تعداد فروش </th>
                                    <th >انتخاب </th>
                                </tr>
                            </thead>
                            <tbody id='kalaContainer' class="select-highlightKala tableBody">
                            </tbody>
                        </table>

                        <!-- return back kala table -->
                          <table class="table table-bordered table-striped table-sm" id="returnedKalaTable">
                            <thead class="tableHeader">
                                <tr>
                                    <th >ردیف</th>
                                    <th style="width:88px">کد</th>
                                    <th style="width:333px">اسم</th>
                                    <th> تاریخ برگشتی  </th>
                                    <th> مشتری </th>
                                    <th> تعداد </th>
                                    <th >انتخاب </th>
                                </tr>
                            </thead>
                            <tbody id='kalaContainer' class="select-highlightKala tableBody">
                            </tbody>
                        </table>

                        <!-- not existing Kalas -->
                          <table class="table table-bordered table-striped table-sm" id="notExistKalaTable">
                            <thead class="tableHeader">
                                <tr>
                                    <th >ردیف</th>
                                    <th style="width:333px">اسم</th>
                                    <th> آخرین فروش </th>
                                    <th >انتخاب </th>
                                </tr>
                            </thead>
                            <tbody id='kalaContainer' class="select-highlightKala tableBody">
                            </tbody>
                        </table>

                        <!-- rocket kala table -->
                        <table class="table table-bordered table-striped table-sm" id="rocketKalaTable">
                            <thead class="tableHeader">
                                <tr>
                                    <th >ردیف</th>
                                    <th  style="width:88px"> کد </th>
                                    <th style="width:333px">اسم</th>
                                    <th> آخرین فروش </th>
                                    <th > موجودی </th>
                                    <th >انتخاب </th>
                                </tr>
                            </thead>
                            <tbody id='kalaContainer' class="select-highlightKala tableBody">
                            </tbody>
                        </table>
                    </div>
                    <div class="row contentFooter"> 
                        <div class="col-lg-12 text-start" id="footerBtn">
                            <button type="button" class="btn btn-sm btn-primary footerButton"> نظرات امروز  <i class="fa fa-comments"></i> </button>
                            <button type="button" class="btn btn-sm btn-primary footerButton"> دیروز  <i class="fa fa-comments"></i> </button>
                            <button type="button" class="btn btn-sm btn-primary footerButton"> صدتای آخر  <i class="fa fa-comments"></i></button>
                            <button type="button" class="btn btn-sm btn-primary footerButton"> همه <i class="fa fa-comments"></i></button>
                        </div>
                    </div>
                </div>
        </div>
    </div>

<!-- modal of 10 last sales -->
<div class="modal fade dragAbleModal" id="viewTenSales" data-bs-backdrop="static"  tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title">ده فروش آخر</h5>
                    <button type="button" class="close btn bg-danger" data-BS-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body p-1">
                    <table class="table table-bordered">
                        <thead class="tableHeader bg-success">
                            <tr>
                                <th>ردیف</th>
                               
                                <th>نام</th>
                                <th>تاریخ خ</th>
                                <th>فی</th>
                                <th>تعداد</th>
                                <th>کل مبلغ</th>
								 <th>کد</th>
                            </tr>
                        </thead>
                        <tbody class="tableBody" id="lastTenSaleBody">
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- modal of sale round kala-->
<div class="modal fade dragAbleModal" id="goodSalesRound" data-bs-backdrop="static"  tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title"> گردش فروش <span id="kalaNameRound"></span></h5>
                    <button type="button" class="close btn bg-danger" data-BS-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body p-1">
                    <table class="table table-bordered">
                        <thead class="tableHeader bg-success">
                            <tr>
                                <th>ردیف</th>
                               
                                <th>نام</th>
                                <th>تاریخ خ</th>
                                <th>فی</th>
                                <th>تعداد</th>
                                <th>کل مبلغ</th>
								 <th>کد</th>
                            </tr>
                        </thead>
                        <tbody class="tableBody" id="salesRoundBody">
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- kalaSettings Modal -->

    
<div class="modal fade dragAbleModal" id="kalaSettingModal"  data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
            <div class="modal-header py-2 myModalHeader">
                <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" id="closeEditModal" aria-label="Close"></button> 
                <h6 class="modal-title" id="editKalaTitle"> </h6>
            </div>
            <div class="modal-body py-1">
                    <div class="row mb-1">
                        <div class="col-lg-12 px-0">
                            <div class="traz-today rounded-2 mx-0 mt-1">
                            <div class="traz-item"> <span style="color:red;">  گروه اصلی : </span> <span id="original"></span>  </div>
                            <div class="traz-item"> <span style="color:red;"> گروه فرعی : </span> <span  id="subsidiary"></span>  </div>
                            <div class="traz-item"> <span style="color:red;"> قیمت اصلی : </span> <span  id="mainPrice"></span> ریال  </div>
                            <div class="traz-item"> <span style="color:red;"> قیمت خط خورده : </span> <del> <span id="overLinePrice"></span> </del> ریال </div>
                        </div>
                    </div>
               
       
        <div class='card' style="background-color:#97c7f7; padding-top:2px; ">
            <div class="container">
                <ul class="header-list nav nav-tabs" data-tabs="tabs">
                    <li><a data-toggle="tab" class="active" href="#parts">دسته بندی</a></li>
                    <li><a data-toggle="tab" href="#yellow">تنظیمات اختصاصی</a></li>
                    <li><a data-toggle="tab" href="#green"> ویژگی های کالا</a></li>
                    <li><a data-toggle="tab" href="#pictures">تصاویر </a></li>
                    <li><a data-toggle="tab" href="#orange">گردش قیمت</a></li>
                </ul>
                <div class="c-checkout tab-content" style="background-color:#97c7f7; margin:0; margin-bottom:1%; padding:2px; border-radius:4px 4px 1px 1px;">
                    <div class="tab-pane active" id="parts">
                        <div class="c-checkout" style="border-radius:10px 10px 2px 2px;">
                            <div class="container" style=" height: 333px !important; overflow-y: scroll !important; display: block !important;">
                             <form action="{{url('/addOrDeleteKalaFromSubGroup')}}" style="display: inline" method="GET" id="groupSubgoupCategory">
                                     @csrf
                                    <input type="text" style="display: none;" name="kalaId" id="kalaIdEdit" value=""/>
                                <div class="row px-0">
                                    <div class="col-sm-6">
                                            <div class="well" style="margin-top:2%;">
                                                <h6 style="font-size:15px;">گروه های اصلی</h6>
                                            </div>
										<!---
                                            <div class="alert">
                                                <input type="text" class="form-control" style="margin-top:10px;" name="search_mainPart" placeholder="جستجو">
                                            </div>
!--->
                                            <table class="table table-bordered table-sm">
                                                <thead class="tableHeader">
                                                    <tr>
                                                        <th>ردیف</th>
                                                        <th>گروه اصلی </th>
                                                        <th>فعال</th>
                                                        <th>انتخاب</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="tableBody modalTableBody" id="maingroupTableBody">
                                                </tbody>
                                            </table>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="well" style="margin-top:2%;">
                                                <h6 style="font-size:15px;">گروه های فرعی</h6>
                                                <table class="table table-bordered table-sm">
                                                    <thead class="tableHeader">
                                                        <tr>
                                                            <th>ردیف</th>
                                                            <th>گروه فرعی </th>
                                                            <th>انتخاب</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="tableBody modalTableBody" id="subGroup1">
                                                    </tbody>
                                             </table>
                                        </div>
                                    </div>
                                </div>
                              </form> 
                            </div>
                        </div>
                    </div>
                    <div class="c-checkout tab-pane" id="orange" style="border-radius:10px 10px 2px 2px;">
                        <div class="container"  style=" height: 333px !important; overflow-y: scroll !important; display: block !important;">
                        <div class="row" style="padding: 1%">
                            <div class="col-sm-12">
                                    <input type="text" class="form-control form-control-sm" style="width:40%" name="search_mainPart" placeholder="جستجو">
                                    <table class="table table-bordered table-hover table-sm text-center">
                                        <thead class="tableHeader bg-success">
                                            <tr>
                                                <th>ردیف</th>
                                                <th>اسم کاربر </th>
                                                <th>برنامه</th>
                                                <th>تاریخ</th>
                                                <th>قیمت قبلی اول</th>
                                                <th>قیمت بعدی اول</th>
                                                <th>قیمت دوم قبلی</th>
                                                <th>قیمت دوم بعدی</th>
                                                <th>انتخاب</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tableBody modalTableBody" id="priceCycle">
                                        </tbody>
                                    </table>
                           </div>
                        </div>
                        </div>
                    </div>

                    <div class="c-checkout tab-pane" id="pictures" style="border-radius:10px 10px 2px 2px;">
                           <div class="container"  style=" height: 333px !important; overflow-y: scroll !important; display: block !important;">
                        
                            <div class="row">
                                <div class='modal-body'style="display:flex;  justify-content: flex-end; float:right;">
                                    <div id='pslider' class=' swiper-container swiper-container-horizontal swiper-container-rtl'>
                                      
                                        <form action="{{ url('/addpicture') }}" target="votar" id="kalaPicForm" enctype="multipart/form-data" method="POST">
                                            @csrf
                                            <input type="text" style="display: none;" name="kalaId" id="kalaIdChangePic" value="">

                                            <table class="table align-middle text-center">
                                                <thead class="bg-success">
                                                    <tr>
                                                        <th style="width:144px !important">تصویر اصلی </th>
                                                        <th>تصویر اول</th>
                                                        <th>تصویر دوم</th>
                                                        <th> تصویر سوم</th>
                                                        <th style="width:144px">تصویر چهارم </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="modalTableBody">
                                                     <tr>
                                                        <td style="width:144px !important">
                                                            <div class='product-item swiper-slide' style='width:100%;'>
                                                                <img class="kalaImage" id="mainPicEdit" src="" />
                                                            </div>
                                                            <label for="mainPic" class="btn btn-primary btn-sm kalaEditbtn">ویرایش<i class="fa-light fa-image fa-lg"></i></label>
                                                            <input type="file"  onchange='document.getElementById("mainPicEdit").src = window.URL.createObjectURL(this.files[0]);' style="display:none" class="form-control" name="firstPic" id="mainPic">
                                                        </td>

                                                        <td>
                                                            <div class='product-item swiper-slide' style='width:100%;'>
                                                                 <img  class="kalaImage" id="secondPic" src="" />
                                                            </div>
                                                            <label for="secondPic" class="btn btn-primary btn-sm kalaEditbtn">  ویرایش <i class="fa-light fa-image fa-lg"></i></label>
                                                            <input type="file" onchange='document.getElementById("secondPic").src = window.URL.createObjectURL(this.files[1]);' style="display:none" class="form-control" name="secondPic" id="secondPic">
                                                        </td>

                                                        <td>
                                                            <div class='product-item swiper-slide' style='width:100%;'>
                                                                <img  class="kalaImage" id="2PicEdit" src="" />
                                                            </div>
                                                            <label for="2Pic" class="btn btn-primary btn-sm kalaEditbtn"> ویرایش <i class="fa-light fa-image fa-lg"></i></label>
                                                            <input type="file"    style="display: none" class="form-control" name="thirthPic" >
                                                        </td>
                                                        <td>
                                                            <div class='product-item swiper-slide' style='width:100%;'>
                                                                <img   class="kalaImage" id="3PicEdit" src="" />
                                                            </div>
                                                            <label for="3Pic" class="btn btn-primary btn-sm kalaEditbtn"> ویرایش <i class="fa-light fa-image fa-lg"></i></label>
                                                            <input type="file"   style="display: none" class="form-control" name="fourthPic" >
                                                        </td>
                                                        <td style="width:144px">
                                                            <div class='product-item swiper-slide' style='width:100%;'>
                                                                <img  class="kalaImage" id="4PicEdit" src="" />
                                                            </div>
                                                            <label for="4Pic" class="btn btn-primary btn-sm kalaEditbtn"> ویرایش <i class="fa-light fa-image fa-lg"></i></label>
                                                            <input type="file"   style="display: none" class="form-control" name="fifthPic" >
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                          </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="c-checkout tab-pane" id="yellow" style="border-radius:10px 10px 2px 2px;">
                        <div class="container"  style=" height: 333px !important; overflow-y: scroll !important; display: block !important;">
                            <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-check-input" disabled type="checkbox" value="" id="defaultCheck1">
                                            <label>علامت گذاری کالای جدید</label>
                                        </div>
                                        <div class="form-group mb-3">
                                            <input class="form-check-input"  type="checkbox" value="" id="stockTakhsis">
                                            <label for="whereHouse">تخصیص انبار</label>
                                        </div>
                                        <div class="form-group">
                                            <button id="minimamSale" onclick="SetMinQty()" class="btn-add-to-cart">حد اقل فروش <i class="far fa-shopping-cart text-white ps-2"></i></button>
                                            <span id="minSaleValue"> </span>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <button id="maximamSale" onclick="SetMaxQty()" class="btn-add-to-cart">حد اکثر فروش <i class="far fa-shopping-cart text-white ps-2"></i></button>
                                            <span id="maxSaleValue"> </span>
                                        </div>
                                    </div>
                                
                                <div class="col-sm-4">
                                    <form action="{{url('/restrictSale')}}" id="restrictFormStuff" method="get">
                                          @csrf
                                        <input type="text" style="display: none" name="kalaId" id="kalaIdSpecialRest" value="">
                                        <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="form-group input-group-sm">
                                                    <label for="exampleFormControlInput1" class="form-label"> حد ایرور هزینه </label>
                                                    <input type="number" onchange="activeSubmitButton(this)" value="" name="costLimit" class="form-control form-control-sm keyRestriction" id="costLimit">
                                                </div>
                                                </div>
                                                <div class="col-lg-7">
                                                    <div class="form-group input-group-sm">
                                                    <label for="exampleFormControlInput1" class="form-label"> نوع هزینه </label>
                                                   <select id="costTypeInfo"  onchange="activeSubmitButton(this)" class="form-select form-control-sm keyRestriction" name="infors"> </select>
                                               </div>
                                           </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group input-group-sm">
                                                   <label for="exampleFormControlInput1" class="form-label">  مقدار هزینه </label>
                                                   <input type="number" onchange="activeSubmitButton(this)" value="" name="costAmount" class="form-control form-control-sm keyRestriction" id="costAmount">
                                               </div>
                                            </div>
                                            <div class="col-lg-6">
                                                 <div class="form-group input-group-sm">
                                                   <label for="exampleFormControlInput1" class="form-label">   نقطه هشدار کالا  </label>
                                                    <input type="number" class="form-control form-control-sm keyRestriction" value="" required onclick="activeSubmitButton(this)" id="existanceAlarm" name='alarmAmount'>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group input-group-sm">
                                             <label for="exampleFormControlInput1" class="form-label">  متن ایرور هزینه </label>
                                             <textarea style="background-color:blanchedalmond"  id="costContent"  class="form-control keyRestriction" onchange="activeSubmitButton(this)" name="costErrorContent" rows="2" cols="24"></textarea>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group input-group-sm">
                                                <input class="form-check-input restriction" type="checkbox" onchange="activeSubmitButton(this)" value="" id="callOnSale" name="callOnSale[]"/>
                                                <label class="form-check-label">تماس جهت خرید کالا </label>
                                        </div>

                                        <div class="form-group input-group-sm">
                                                <input class="form-check-input restriction" type="checkbox"  onchange="activeSubmitButton(this)" value="" id="zeroExistance" name="zeroExistance[]" />
                                                <label class="form-check-label"> صفر کردن موجودی کالا </label>
                                        </div>

                                        <div class="form-group input-group-sm">
                                                <input class="form-check-input restriction" type="checkbox"  onchange="activeSubmitButton(this)" value="" id="showTakhfifPercent" name="activeTakhfifPercent[]" />
                                                <label class="form-check-label" for="showTakhfifPercent">  نمایش درصد تخفیف </label>
                                        </div>

                                        <div class="form-group input-group-sm">
                                                <input class="form-check-input restriction" type="checkbox"  onchange="activeSubmitButton(this)" value="" id="showFirstPrice" name="overLine[]" />
                                                <label class="form-check-label" for="showFirstPrice"> نمایش قیمت خط خورده </label>
                                        </div>
                                    <div class="form-group input-group-sm">
                                            <input class="form-check-input restiction"  onchange="activeSubmitButton(this)" type="checkbox" value="" id="inactiveAll" name="hideKala[]">
                                            <label class="form-check-label"> غیر فعال </label>
                                    </div>
                                    <div class="form-group input-group-sm">
                                            <input class="form-check-input restriction"  onchange="activeSubmitButton(this)" type="checkbox" value="" name="freeExistance[]" id="freeExistance"  >
                                            <label class="form-check-label"> آزادگذاری فروش </label>
                                    </div>
                                    <div class="form-group input-group-sm">
                                            <input class="form-check-input restriction"  onchange="activeSubmitButton(this)" type="checkbox" value="" name="activePishKharid[]" id="activePreBuy"  >
                                            <label class="form-check-label"> فعالسازی پیش خرید </label>
                                    </div>
                                </form>
                                </div>
                            </div>
                            
                            <div class="row" >
                                <div class="col-sm-5" id="allStock" style="display:none">
                                    <div class='modal-body'>
                                        <input type="text" class="form-control" style="margin-top:5px;" id=""  placeholder="جستجو">
                                         <table class="table table-bordered table table-hover table-sm">
                                            <thead class="tableHeader">
                                                <tr>
                                                    <th>ردیف</th>
                                                    <th>اسم </th>
                                                    <th>انتخاب</th>
                                                </tr>
                                            </thead>
                                            <tbody class="tableBody modalTableBody" id="allStockForList"> </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-sm-2"  id="addAndDeleteStock" style="display:none">
                                    <div class='' style="position:relative; right: 5%; top: 30%;">
                                        <div >
                                            <a id="addStockToList">
                                                <i class="fa-regular fa-circle-chevron-left fa-3x chevronHover"></i>
                                            </a>
                                            <br />
                                            <a id="removeStockFromList">
                                                <i class="fa-regular fa-circle-chevron-right fa-3x chevronHover"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-5" id="addedStock">
                                    <div class='modal-body'>
                                    <form action="{{url('/addStockToList')}}" method="POST" id="submitStockToList" style="display: inline" >
                                            <input type="text" name="kalaId" value="" style="display: none" id="kalaIdForAddStock">
                                            @csrf
                                            <input type="text" class="form-control" style="margin-top:5x;" id="serachKalaOfSubGroup"  placeholder="جستجو">
                                               <table class="table table-bordered table table-hover table-sm">
                                                    <thead class="tableHeader">
                                                        <tr >
                                                            <th>ردیف</th>
                                                            <th>انبار</th>
                                                            <th>انتخاب</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="tableBody modalTableBody" id="allstockOfList">
                                                    </tbody>
                                                </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="c-checkout tab-pane" id="green" style="border-radius:10px 10px 2px 2px;">
                     <div class="container"  style=" height: 333px !important; overflow-y: scroll !important; display: block !important;">
                        <div class="row">
                             <div class=" col-sm-3"  style="margin-top: 1%">
                                  <iframe name="votar" style="display:none;"></iframe>
                                    <form action="{{url('/addDescKala')}}" target="votar" id="completDescription" method="post">
                                        @csrf
                                        <input type="text" style="display:none" name="kalaId" id="kalaIdDescription" value=""/>
                                        <label class="fs-6" for="description">توضیحات کامل کالا</label>
                                        <textarea style="background-color:blanchedalmond" class="form-control" name="discription" id="descriptionKala" rows="2"></textarea>
                                    </form>
                                </div>
                                <div class="col-sm-3 form-group" style="margin-top: 1%">
                                    <label  class="fs-6"  for="shortExpain">توضیحات مختصر کالا</label>
                                    <textarea style="background-color:blanchedalmond" disabled class="form-control" id="shortExpain" rows="2"></textarea>
                                </div>
                                <div class="col-sm-3 form-group" style="margin-top: 1%">
                                    <label  class="fs-6"  for="kalaTags"> تگ کردن کالای مترادف </label>
                                    <input type="email" disabled class="form-control" id="kalaTags" placeholder="">
                                </div>
                                <div class="col-sm-3 form-group" style="margin-top: 2%">
                                    <input type="checkbox" class="form-check-input" id="sameKalaList" />
                                    <label  class="fs-6"  for="exampleFormControlTextarea1">لیست کالاهای مشابه</label>
                                </div>
                         </div>
                        <div class="row" >
                            <div class="col-sm-5" id="addKalaToList" style="display:none">
                                <div class='modal-body'>
                                  <input type="text" class="form-control" style="margin-top:5px;" id="serachKalaForAssameList" placeholder="جستجو">
                                    <table class="table table-bordered table table-hover table-sm">
                                        <thead class="tableHeader">
                                            <tr>
                                                <th>ردیف</th>
                                                <th>اسم </th>
                                                <th><input type="checkbox" name=""  class="selectAllFromTop form-check-input"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="tableBody modalTableBody" id="allKalaForList">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-sm-2"  id="addAndDelete" style="display:none">
                                <div class='' style="position:relative; left: 5%; top: 30%;">
                                    <div>
                                        <a id="addDataToList">
                                            <i class="fa-regular fa-circle-chevron-left fa-3x chevronHover"></i>
                                        </a>
                                        <br />
                                        <a id="removeDataFromList">
                                            <i class="fa-regular fa-circle-chevron-right fa-3x chevronHover"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-5" id="addedList">
                                <div class='modal-body'>
                                    <iframe name="votar" style="display:none;"></iframe>
                                    <form action="{{url('/addKalaToList')}}" target="votar"  method="GET" style="display: inline" id="sameKalaForm">
                                        <input type="text" name="mainKalaId" value="" style="display: none" id="kalaIdSameKala">
                                         @csrf
                                        <input type="text" class="form-control form-control-sm" style="margin-top:5px;" id="serachKalaOfSubGroup"  placeholder="جستجو">
                                         <table class="table table-bordered table table-hover table-sm">
                                            <thead class="tableHeader">
                                                <tr>
                                                    <th>ردیف</th>
                                                    <th>کالای مشابه</th>
                                                    <th><input type="checkbox" name=""  class="selectAllFromTop form-check-input"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="tableBody modalTableBody" id="allKalaOfList">
                                             </tbody>
                                         </table>
                                    </div>
                                 </form>
                                </div>
                              </div>
                             </div>
                           </div>
                         </div>
                       </div>
                     </div> 
                  </div>
                  </div>
                <div class="modal-footer py-1">
                    <button class="btn btn-sm btn-success buttonHover" type="submit" id="submitSubGroup"           form="groupSubgoupCategory" disabled> ذخیره <i class="fa fa-save fa-lg"></i> </Button>
                    <button class="btn btn-sm btn-success buttonHover" type="submit" id="stockSubmit"              form="submitStockToList" style="display:none"> ذخیره <i class="fa fa-save fa-lg"></i> </button>
                    <button class="btn btn-sm btn-success buttonHover" type="submit" id="kalaRestictionbtn"        form="restrictFormStuff" style="display:none"> ذخیره <i class="fa fa-save fa-lg"></i></button>
                    <button class="btn btn-sm btn-success buttonHover" type="submit" id="completDescriptionbtn"    form="completDescription" style="display:none">ذخیره <i class="fa fa-save fa-lg"></i></button>
                    <button class="btn btn-sm btn-success buttonHover" type="submit" id="addToListSubmit"          form="sameKalaForm" style="display:none">ذخیره <i class="fa fa-save fa-lg"></i> </button>
                    <button class="btn btn-sm btn-success buttonHover" type="submit" id="submitChangePic"          form="kalaPicForm" style="display:none"> ذخیره <i class="fa fa-save fa-lg"></i></button>
                    <button class="btn btn-sm btn-danger buttonHover"  type="button"data-bs-dismiss="modal" id="cancelEditModal"> انصراف <i class="fa fa-xmark"></i></button>
              </div>
            </div>
          </div>
        </div>
  


@endsection
