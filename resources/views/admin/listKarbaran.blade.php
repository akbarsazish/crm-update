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
        font-size:26px;
         margin: 5px 5px 0px 5px;
    }

  .caret-minus::before {
        content: "\2212";
        color: black;
        display: inline-block;
        margin: 5px 5px;
        color:red;
         transform: rotate(0deg) !important; 
         font-size:26px;
        }


    .caret-down-minus::before {
        -ms-transform: rotate(90deg); /* IE 9 */
        -webkit-transform: rotate(90deg); /* Safari */'
        transform: rotate(90deg);  
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

.form-input-dark {
    color: #000;
}
</style>
   <div class="container-fluid containerDiv">
      <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 sideBar listKarbaranSidebar">
                    <fieldset class="border rounded mt-5 sidefieldSet">
                        <legend  class="float-none w-auto legendLabel mb-0"> کاربران </legend>
                        <div class="col-lg-12">
                            <div class="row px-3">
                                 @if(hasPermission(Session::get("asn"),"declareElementOppN") > 1)
                                    <button type="button" class="btn btn-primary btn-sm text-warning buttonHover" id="newAdminBtn">جدید <i class="fa fa-plus fa-lg" aria-hidden="true"></i></a>
                                 @endif
                                 @if(hasPermission(Session::get("asn"),"declareElementOppN") > 0)
                                    <button type="button" class="btn btn-primary btn-sm text-warning buttonHover" disabled id="editAdmin" onclick="setKarbarEditStuff()"> ویرایش  <i class="fa fa-edit fa-lg" aria-hidden="true"></i></button>
                                @endif
                                @if(hasPermission(Session::get("asn"),"declareElementOppN") > 1)
                                    <button type="button" class="btn btn-primary btn-sm text-warning buttonHover" disabled id="deleteAdmin" onclick="deleteAdminList(this.value)"> حذف  <i class="fa fa-trash fa-lg" aria-hidden="true" style="color:red;"></i></button>
                                @endif
                                @if(hasPermission(Session::get("asn"),"declareElementOppN") > 1)
                                    <button type="button" class="btn btn-primary btn-sm text-warning buttonHover"  id="moveEmployee"> انتقال  <i class="fa fa-send fa-lg" aria-hidden="true" style="color:red;"></i></button>
                                    <input type="hidden" id="AdminForAdd"/>
                                @endif
                            </div>
                        </div>
                    </fieldset>
                  </div>
                 <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                    <div class="row contentHeader listkarbarnContentHeader"> </div>
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
                                                            <span class="caret"  onclick="setHeadStuff(this,{{$head->id}})">{{$head->name .' '.$head->lastName }} <input type="radio" class="form-check-input"  style="display:none"   value="{{$head->id}}" name="head" id=""></span>
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
                    
                        <div class="row px-0 mx-0">
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
                    <div class="row contentFooter"> </div>
                 </div>
           </div>
      </div>



        <!-- modal of new karabar -->
        <div class="modal fade dragableModal" id="newAdmin" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header py-2 text-white" style="margin:0; border:none">
                        <button type="button" class="btn-close bg-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h5 class="modal-title" id="exampleModalLongTitle"> کابر جدید</h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{url('/addAdmin')}}" method="POST" id="addNewAdminForm"  enctype="multipart/form-data">
                            {{ csrf_field() }}
                           <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> نام </label>
                                        <input type="text" required minlength="3" maxlength="12" class="form-control form-control-sm" autocomplete="off" name="name">
                                    </div>
                               </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> نام خانوادگی</label>
                                        <input type="text" required  minlength="3" maxlength="12" class="form-control form-control-sm" autocomplete="off" name="lastName">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label"> نام کاربری</label>
                                        <input type="text" id="userName"  minlength="3" maxlength="12" onblur="checkExistance(this)" required class="form-control form-control-sm" autocomplete="off" name="userName">
                                        <span id="existAlert" class="text-danger" style="font-size:14px!important"></span>
                                    </div>
                                </div>

                                 <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label"> رمز</label>
                                        <input type="text" onblur="clearRiplicateData()"  minlength="3" maxlength="12" required class="form-control form-control-sm" autocomplete="off" name="password" >
                                    </div>
                                </div> 
                                 <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> جنسیت  </label>
                                        <select class="form-select form-select-sm" name="sex">
                                                <option value="1" >زن </option>
                                                <option value="2" >مرد</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-lg-3">
                                        <label class="dashboardLabel form-label"> تلفن </label>
                                        <input type="number" required minlength="10" maxlength="12" class="form-control form-control-sm" autocomplete="off" name="phone">
                                   </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label"> عکس </label>
                                            <input type="file" class="form-control form-control-sm" name="picture" placeholder="">
                                       </div>
                                   </div>
                                    <div class="col-lg-6">
                                       <label class="dashboardLabel form-label"> آدرس </label>
                                       <input type="text" required minlength="3" class="form-control form-control-sm" autocomplete="off" name="address">
                                    </div>
                            </div>
                            <br>

                            <div class="row rounded px-0 mx-0" style="background-color:#abd2ed; padding-bottom:5px;"> 
                                   <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label"> نوع کاربر </label>
                                            <select class="form-select form-select-sm" name="employeeType"  id="employeeType">
                                                    <option value="0" > -- </option>
                                                    <option value="1" > مدیر </option>
                                                    <option value="2" > سرپرست </option>
                                                    <option value="3" > کارمند </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group" style="display:none" id="employeeJobDiv">
                                            <label class="dashboardLabel form-label">وظیفه کارمند</label>
                                            <select class="form-select form-select-sm" name="poshtibanType" id="poshtibanType">
                                                    <option value="0" >--</option>
                                                    <option value="4" >راننده</option>
                                                    <option value="2" >پشتیبان حضوری</option>
                                                    <option value="2" >پشتیبان هماهنگی</option>
                                                    <option value="2" >پشتیبان تلفنی</option>
                                                    <option value="3" >بازاریاب حضوری</option>
                                                    <option value="3" >بازاریاب هماهنگی</option>
                                                    <option value="3" >بازاریاب تلفنی</option>
                                            </select>
                                        </div>
                                </div> 

                                <div class="col-md-2">
                                    <div class="form-group"  style="display:none"  id="saleLineDive">
                                        <label class="dashboardLabel form-label"> خط فروش </label>
                                        <select class="form-select form-select-sm" name="saleLine">
                                                <option value="0" > -- </option>
                                            @foreach($saleLines as $saleLine)
                                                <option value="{{$saleLine->SaleLineSn}}" >{{$saleLine->LineName}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                             
                                <div class="col-md-2">
                                    <div class="form-group" style="display:none" id="managerDiv">
                                        <label class="dashboardLabel form-label"> مدیر </label>
                                        <select class="form-select form-select-sm" name="manager" id="manager">
                                                <option value="0" > -- </option>
                                            @foreach($managers as $manager)
                                                <option value="{{$manager->id}}" > {{$manager->name .' '. $manager->lastName}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" style="display:none" id="headDiv">
                                        <label class="dashboardLabel form-label"> سرپرست </label>
                                        <select class="form-select form-select-sm" name="head" id="head">
                                                <option value="0" > -- </option>
                                        @foreach($heads as $head)
                                                <option value="{{$head->id}}" > {{$head->name .' '. $head->lastName}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row text-end">
                                <div class="col-md-4 mb-2"> 
                                    <div class="form-group" id="">
                                        <label class="dashboardLabel form-label"> دسترسی به  </label>
                                        <select class="form-select form-select-sm" name="" id="">
                                                <option value="" >--</option>
                                                <option value="" > کل مشتریان </option>
                                                <option value="" > کل آلارمها </option>
                                                <option value="" > کل نظر سنجی </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8 mb-2">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label class="form-label mb-0"> توضیحات</label>
                                            <textarea style="background-color:blanchedalmond" class="form-control"  minlength="3" rows="2" name="discription" style="background-color:blanchedalmond"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div> 

                <div class='card mb-4 mt-1' style="background-color:#abd2ed; padding-top:1%; paddding:0;">
                   <div class="container">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs">
                            <li><a class="active" data-toggle="tab" style="color:black;" href="#baseInfoNEW"> اطلاعات پایه </a></li>
                            <li><a data-toggle="tab" style="color:black;"  href="#definElementNEW"> تعریف عناصر </a></li>
                            <li><a data-toggle="tab" style="color:black;"  href="#kalasTabNEW"> عملیات </a></li>
                            <li><a data-toggle="tab" style="color:black;"  href="#peoplesNEW"> گزارشات </a></li>
                        </ul>
                        <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0; margin-bottom:2%; padding:2%; border-radius:10px 10px 2px 2px; dir:ltr !important;">
                            <div class="c-checkout tab-pane active" id="baseInfoNEW" style="border-radius:10px 10px 2px 2px;">
                                <div class="container">
                                  <div class="row">
                                        <fieldset class="border rounded-3">
                                            <legend  class="float-none w-auto forLegend "> 
                                                 <input type="checkbox" name="baseInfoN" class="baseInfoFirstN form-check-input d-inline-block" id="baseInfoN"/> اطلاعات پایه </legend>
                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                <legend  class="float-none w-auto fs-6">
                                                     <input type="checkbox" name="baseInfoProfileN" id="baseInfoProfileN" class="baseInfoN form-check-input d-inline-block"/> پروفایل </legend>
                                                     
                                                <div class="form-check">
                                                    <input class="ProfileN form-check-input box-check" id="deleteProfileN" type="checkbox" name="deleteProfileN">
                                                    <label class="form-check-label ">حذف</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="ProfileN form-check-input box-check " id="editProfileN" type="checkbox" name="editProfileN">
                                                    <label class="form-check-label">تغییر</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="ProfileN form-check-input box-check" id="seeProfileN" type="checkbox" name="seeProfileN">
                                                    <label class="form-check-label">مشاهده</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" name="infoRdN" id="infoRdN" class="baseInfoN form-check-input d-inline-block"/> R & D  &nbsp;</legend>
                                                       <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" name="rdSentN" id="rdSentN" class="rdN form-check-input d-inline-block"/> وارد شده ها    &nbsp;</legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input box-check" id="deleteSentRdN" type="checkbox" name="deleteSentRdN">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input box-check" id="editSentRdN" type="checkbox" name="editSentRdN">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" id="seeSentRdN" type="checkbox"  name="seeSentRdN">
                                                                <label class="form-check-label">مشاهده</label>
                                                            </div>
                                                        </fieldset>
                                                       <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" name="rdNotSentN" id="rdNotSentN" class="rdN form-check-input d-inline-block"/> وارد نشده ها    &nbsp;</legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input box-check" id="deleteRdNotSentN" type="checkbox" name="deleteRdNotSentN">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input box-check" id="editRdNotSentN" type="checkbox" name="editRdNotSentN">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" id="seeRdNotSentN" type="checkbox"  name="seeRdNotSentN">
                                                                <label class="form-check-label">مشاهده</label>
                                                            </div>
                                                        </fieldset>
                                            </fieldset>
                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" id="addSaleLineN" class="baseInfoN form-check-input d-inline-block" name="specialSettingN" /> افزودن خط فروش </legend>
                                                <div class="form-check">
                                                     <input class="form-check-input" id="deleteSaleLineN" type="checkbox" name="deleteSaleLineN"> 
                                                    <label class="form-check-label box-check">حذف</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="adminN form-check-input" type="checkbox" id="editSaleLineN" name="editSaleLineN">
                                                    <label class="form-check-label box-check">تغییر</label>
                                                </div>
                                                <div class="form-check">
                                                     <input class="poshtibanN web form-check-input" id="seeSaleLineN" type="checkbox" name="seeSaleLineN">
                                                    <label class="form-check-label box-check">مشاهده</label>
                                                </div>
                                            </fieldset>

                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" id="baseInfoSettingN" class="baseInfoN form-check-input d-inline-block" name="baseInfoSettingN" /> تنظیمات </legend>
                                                     <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" id="InfoSettingAccessN" class="InfoSettingN form-check-input d-inline-block" name="InfoSettingAccessN" /> سطح دسترسی </legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input" id="deleteSettingAccessN" type="checkbox" name="deleteSettingAccessN"> 
                                                                <label class="form-check-label box-check">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input" type="checkbox" id="editSettingAccessN" name="editSettingAccessN">
                                                                <label class="form-check-label box-check">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN web form-check-input" id="seeSettingAccessN" type="checkbox" name="seeSettingAccessN">
                                                                <label class="form-check-label box-check">مشاهده</label>
                                                            </div>
                                                        </fieldset>
                                                     <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" id="InfoSettingTargetN" class="InfoSettingN form-check-input d-inline-block" name="InfoSettingTargetN" />  تارگت ها و امتیازها  </legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input" id="deleteSettingTargetN" type="checkbox" name="deleteSettingTargetN"> 
                                                                <label class="form-check-label box-check">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input" type="checkbox" id="editSettingTargetN" name="editSettingTargetN">
                                                                <label class="form-check-label box-check">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN web form-check-input" id="seeSettingTargetN" type="checkbox" name="seeSettingTargetN">
                                                                <label class="form-check-label box-check">مشاهده</label>
                                                            </div>
                                                        </fieldset>
                                                </fieldset>
                                              </fieldset>
                                        </fieldset>
                                      </div>
                                </div>
                            </div>

                             <div class="c-checkout tab-pane" id="definElementNEW" style="border-radius:10px 10px 2px 2px;">
                                  <div class="container">
                                      <div class="row">
                                            <fieldset class="border rounded-3">
                                                <legend  class="float-none w-auto fs-6"><input type="checkbox" id="declareElementN" class="declareElementN form-check-input d-inline-block" name="declareElementN" /> تعریف عناصر  </legend>
                                                <div class="form-check">
                                                    <input class="form-check-input box-check" id="deletedeclareElementN" type="checkbox" name="deletedeclareElementN">
                                                    <label class="form-check-label">حذف</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="adminN form-check-input box-check" type="checkbox" id="editdeclareElementN" name="editdeclareElementN">
                                                    <label class="form-check-label">تغییر</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="poshtibanN form-check-input box-check" type="checkbox" id="seedeclareElementN" name="seedeclareElementN">
                                                    <label class="form-check-label">مشاهده</label>
                                                </div>
                                             </fieldset>
                                        </div>
                                    </div>
                                </div>

                            <div class="c-checkout tab-pane" id="kalasTabNEW" style="border-radius:10px 10px 2px 2px;">
                                <div class="container">
                                    <div class="row">
                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                        <legend  class="float-none w-auto forLegend"><input type="checkbox" class="oppN form-check-input d-inline-block" name="oppN" id="oppN"/> عملیات </legend>

                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartN form-check-input d-inline-block" id="oppTakhsisN" name="oppTakhsisN"/> تخصیص به کاربر </legend>
                                              <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppTakhsisN form-check-input d-inline-block" id="oppManagerN" name="oppManagerN"/> مدیران  </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deleteManagerOppN" type="checkbox" name="deleteManagerOppN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editManagerOppN" name="editManagerOppN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeManagerOppN" name="seeManagerOppN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                 </fieldset>
                                              <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppTakhsisN form-check-input d-inline-block" id="oppHeadN" name="oppHeadN"/> سرپرستان  </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deleteHeadOppN" type="checkbox" name="deleteHeadOppN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editHeadOppN" name="editHeadOppN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeHeadOppN" name="seeHeadOppN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                 </fieldset>
                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppTakhsisN form-check-input d-inline-block" id="oppBazaryabN" name="oppBazaryabN"/> بازاریابها  </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deleteBazaryabOppN" type="checkbox" name="deleteBazaryabOppN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editBazaryabOppN" name="editBazaryabOppN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeBazaryabOppN" name="seeBazaryabOppN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                 </fieldset>
                                        </fieldset>


                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartN form-check-input d-inline-block" id="oppDriverN" name="oppDriverN"/>  راننده ها  </legend>
                                                  <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppDriverN form-check-input d-inline-block" id="oppDriverServiceN" name="oppDriverServiceN"/> سرویس راننده ها  </legend>
                                                    <div class="form-check">
                                                        <input class="form-check-input box-check" id="deleteoppDriverServiceN" type="checkbox" name="deleteoppDriverServiceN">
                                                        <label class="form-check-label">حذف</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="adminN form-check-input box-check " type="checkbox" id="editoppDriverServiceN" name="editoppDriverServiceN">
                                                        <label class="form-check-label">تغییر</label>
                                                    </div>
                                                    <div class="form-check">
                                                    <input class="poshtibanN form-check-input box-check " type="checkbox" id="seeoppDriverServiceN" name="seeoppDriverServiceN">
                                                        <label class="form-check-label">مشاهده</label>
                                                    </div>
                                                </fieldset>
                                                  <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppDriverN form-check-input d-inline-block" id="oppBargiriN" name="oppBargiriN"/>  بار گیری  </legend>
                                                    <div class="form-check">
                                                        <input class="form-check-input box-check" id="deleteoppBargiriN" type="checkbox" name="deleteoppBargiriN">
                                                        <label class="form-check-label">حذف</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="adminN form-check-input box-check " type="checkbox" id="editoppBargiriN" name="editoppBargiriN">
                                                        <label class="form-check-label">تغییر</label>
                                                    </div>
                                                    <div class="form-check">
                                                    <input class="poshtibanN form-check-input box-check " type="checkbox" id="seeoppBargiriN" name="seeoppBargiriN">
                                                        <label class="form-check-label">مشاهده</label>
                                                    </div>
                                                </fieldset>
                                        </fieldset>

                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartN form-check-input d-inline-block" id="oppNazarSanjiN" name="oppNazarSanjiN"/> نظر سنجی </legend>
                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                     <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppNazarSanjiN form-check-input d-inline-block" id="todayoppNazarsanjiN" name="todayoppNazarsanjiN"/> نظرات امروز  </legend>
                                                            <div class="form-check">
                                                            <input class="form-check-input box-check" id="deletetodayoppNazarsanjiN" type="checkbox" name="deletetodayoppNazarsanjiN">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="edittodayoppNazarsanjiN" name="edittodayoppNazarsanjiN">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seetodayoppNazarsanjiN" name="seetodayoppNazarsanjiN">
                                                                <label class="form-check-label">مشاهده</label>
                                                             </div>
                                                </fieldset>
                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                     <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppNazarSanjiN form-check-input d-inline-block" id="pastoppNazarsanjiN" name="pastoppNazarsanjiN"/> نظرات گذشته   </legend>
                                                            <div class="form-check">
                                                            <input class="form-check-input box-check" id="deletepastoppNazarsanjiN" type="checkbox" name="deletepastoppNazarsanjiN">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editpastoppNazarsanjiN" name="editpastoppNazarsanjiN">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seepastoppNazarsanjiN" name="seepastoppNazarsanjiN">
                                                                <label class="form-check-label">مشاهده</label>
                                                             </div>
                                                </fieldset>
                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                     <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppNazarSanjiN form-check-input d-inline-block" id="DoneoppNazarsanjiN" name="DoneoppNazarsanjiN"/> نظرات انجام شده    </legend>
                                                            <div class="form-check">
                                                            <input class="form-check-input box-check" id="deleteDoneoppNazarsanjiN" type="checkbox" name="deleteDoneoppNazarsanjiN">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editDoneoppNazarsanjiN" name="editDoneoppNazarsanjiN">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeDoneoppNazarsanjiN" name="seeDoneoppNazarsanjiN">
                                                                <label class="form-check-label">مشاهده</label>
                                                             </div>
                                                </fieldset>
                                        </fieldset>

                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartN form-check-input d-inline-block" id="OppupDownBonusN" name="OppupDownBonusN" /> افزایش و کاهش امتیازات  </legend>
                                              <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="OppupDownBonusN form-check-input d-inline-block" id="AddOppupDownBonusN" name="AddOppupDownBonusN" />    اضافه شده  </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deleteAddOppupDownBonusN" type="checkbox" name="deleteAddOppupDownBonusN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editAddOppupDownBonusN" name="editAddOppupDownBonusN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox"  id="seeAddOppupDownBonusN" name="seeAddOppupDownBonusN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                </fieldset>
                                              <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="OppupDownBonusN form-check-input d-inline-block" id="SubOppupDownBonusN" name="SubOppupDownBonusN" />  کاهش یافته  </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deleteSubOppupDownBonusN" type="checkbox" name="deleteSubOppupDownBonusN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editSubOppupDownBonusN" name="editSubOppupDownBonusN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox"  id="seeSubOppupDownBonusN" name="seeSubOppupDownBonusN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                </fieldset>
                                        </fieldset>

                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartN form-check-input d-inline-block" id="oppRDN" name="oppRDN"/>  R & D </legend>
                                             <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppRDN form-check-input d-inline-block" id="AddedoppRDN" name="AddedoppRDN"/>  وارده شده  </legend>
                                                    <div class="form-check">
                                                        <input class="form-check-input box-check" id="deleteAddedoppRDN" type="checkbox" name="deleteAddedoppRDN">
                                                        <label class="form-check-label">حذف</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editAddedoppRDN" name="editAddedoppRDN">
                                                        <label class="form-check-label">تغییر</label>
                                                    </div>
                                                    <div class="form-check">
                                                    <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeAddedoppRDN" name="seeAddedoppRDN">
                                                        <label class="form-check-label">مشاهده</label>
                                                    </div>
                                                </fieldset>
                                             <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppRDN form-check-input d-inline-block" id="NotAddedoppRDN" name="NotAddedoppRDN"/>  وارده نشده  </legend>
                                                    <div class="form-check">
                                                        <input class="form-check-input box-check" id="deleteNotAddedoppRDN" type="checkbox" name="deleteNotAddedoppRDN">
                                                        <label class="form-check-label">حذف</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editNotAddedoppRDN" name="editNotAddedoppRDN">
                                                        <label class="form-check-label">تغییر</label>
                                                    </div>
                                                    <div class="form-check">
                                                    <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeNotAddedoppRDN" name="seeNotAddedoppRDN">
                                                        <label class="form-check-label">مشاهده</label>
                                                    </div>
                                                </fieldset>
                                        </fieldset>

                                        
                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartN form-check-input d-inline-block" id="oppCalendarN" name="oppCalendarN"/> تقویم روزانه  </legend>
                                              <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppCalendarN form-check-input d-inline-block" id="oppjustCalendarN" name="oppjustCalendarN"/> تقویم روزانه </legend>
                                                    <div class="form-check">
                                                    <input class="form-check-input box-check" id="deleteoppjustCalendarN" type="checkbox" name="deleteoppjustCalendarN">
                                                        <label class="form-check-label">حذف</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editoppjustCalendarN" name="editoppjustCalendarN">
                                                        <label class="form-check-label">تغییر</label>
                                                    </div>
                                                    <div class="form-check">
                                                    <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeoppjustCalendarN" name="seeoppjustCalendarN">
                                                        <label class="form-check-label">مشاهده</label>
                                                    </div>
                                                </fieldset>
                                              <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppCalendarN form-check-input d-inline-block" id="oppCustCalendarN" name="oppCustCalendarN"/> لیست مشتریان </legend>
                                                    <div class="form-check">
                                                    <input class="form-check-input box-check" id="deleteoppCustCalendarN" type="checkbox" name="deleteoppCustCalendarN">
                                                        <label class="form-check-label">حذف</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editoppCustCalendarN" name="editoppCustCalendarN">
                                                        <label class="form-check-label">تغییر</label>
                                                    </div>
                                                    <div class="form-check">
                                                    <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeoppCustCalendarN" name="seeoppCustCalendarN">
                                                        <label class="form-check-label">مشاهده</label>
                                                    </div>
                                                </fieldset>
                                        </fieldset>
                                        
                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartN form-check-input d-inline-block" id="alarmoppN" name="alarmoppN" /> آلارم  </legend>
                                                 <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="alarmoppN form-check-input d-inline-block" id="allalarmoppN" name="allalarmoppN" /> آلارمها   </legend>
                                                        
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deleteallalarmoppN" type="checkbox" name="deleteallalarmoppN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editallalarmoppN" name="editallalarmoppN" >
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeallalarmoppN" name="seeallalarmoppN" >
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                                     <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="alarmoppN form-check-input d-inline-block" id="donealarmoppN" name="donealarmoppN" />  انجام شده </legend>
                                                        
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deletedonealarmoppN" type="checkbox" name="deletedonealarmoppN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editdonealarmoppN" name="editdonealarmoppN" >
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seedonealarmoppN" name="seedonealarmoppN" >
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                                     <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="alarmoppN form-check-input d-inline-block" id="NoalarmoppN" name="NoalarmoppN" />  مشتریان فاقد آلارم   </legend>
                                                        
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deleteNoalarmoppN" type="checkbox" name="deleteNoalarmoppN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editNoalarmoppN" name="editNoalarmoppN" >
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeNoalarmoppN" name="seeNoalarmoppN" >
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                        </fieldset>

                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartN form-check-input d-inline-block" id="massageOppN" name="massageOppN" /> پیام ها   </legend>
                                            
                                            <div class="form-check">
                                                 <input class="form-check-input box-check" id="deletemassageOppN" type="checkbox" name="deletemassageOppN">
                                                <label class="form-check-label">حذف</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="adminN form-check-input box-check" type="checkbox" id="editmassageOppN" name="editmassageOppN" >
                                                <label class="form-check-label">تغییر</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seemassageOppN" name="seemassageOppN" >
                                                <label class="form-check-label">مشاهده</label>
                                            </div>
                                        </fieldset>

                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartN form-check-input d-inline-block" id="justBargiriOppN" name="justBargiriOppN" /> بار گیری   </legend>
                                            <div class="form-check">
                                                 <input class="form-check-input box-check" id="deletejustBargiriOppN" type="checkbox" name="deletejustBargiriOppN">
                                                <label class="form-check-label">حذف</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="adminN form-check-input box-check" type="checkbox" id="editjustBargiriOppN" name="editjustBargiriOppN" >
                                                <label class="form-check-label">تغییر</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seejustBargiriOppN" name="seejustBargiriOppN" >
                                                <label class="form-check-label">مشاهده</label>
                                            </div>
                                        </fieldset>
                                    </fieldset>
                                    </div>
                                </div>
                            </div>

                            <div class="c-checkout tab-pane" id="peoplesNEW" style="border-radius:10px 10px 2px 2px;">
                                <div class="container">
                                    <div class="row">
                                    <fieldset class="border rounded-3">
                                      <legend  class="float-none w-auto "><input type="checkbox" class="reportN form-check-input d-inline-block" id="reportN" name="reportN"/> گزارشات  </legend>
                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="reportPartN form-check-input d-inline-block" id="amalKardreportN" name="amalKardreportN"/> عملکرد کاربران </legend>
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalKardreportN form-check-input d-inline-block" id="managerreportN" name="managerreportN"/> مدیران  </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deletemanagerreportN" type="checkbox" name="deletemanagerreportN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editmanagerreportN" name="editmanagerreportN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seemanagerreportN" name="seemanagerreportN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalKardreportN form-check-input d-inline-block" id="HeadreportN" name="HeadreportN"/>  سرپرستان   </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deleteHeadreportN" type="checkbox" name="deleteHeadreportN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editHeadreportN" name="editHeadreportN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeHeadreportN" name="seeHeadreportN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalKardreportN form-check-input d-inline-block" id="poshtibanreportN" name="poshtibanreportN"/>  پشتیبانها   </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deleteposhtibanreportN" type="checkbox" name="deleteposhtibanreportN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editposhtibanreportN" name="editposhtibanreportN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeposhtibanreportN" name="seeposhtibanreportN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalKardreportN form-check-input d-inline-block" id="bazaryabreportN" name="bazaryabreportN"/>  بازاریابها    </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deletebazaryabreportN" type="checkbox" name="deletebazaryabreportN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editbazaryabreportN" name="editbazaryabreportN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seebazaryabreportN" name="seebazaryabreportN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalKardreportN form-check-input d-inline-block" id="reportDriverN" name="reportDriverN"/>  راننده ها     </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deletereportDriverN" type="checkbox" name="deletereportDriverN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editreportDriverN" name="editreportDriverN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seereportDriverN" name="seereportDriverN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                            </fieldset>


                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="reportPartN form-check-input d-inline-block" id="trazEmployeeReportN" name="trazEmployeeReportN"/> تراز کاربران </legend>
                                                <div class="form-check">
                                                    <input class="form-check-input box-check" id="deletetrazEmployeeReportN" type="checkbox" name="deletetrazEmployeeReportN">
                                                    <label class="form-check-label">حذف</label>
                                                </div>
                                                <div class="form-check">
                                                     <input class="adminN form-check-input box-check" type="checkbox" id="edittrazEmployeeReportN" name="edittrazEmployeeReportN">
                                                    <label class="form-check-label">تغییر</label>
                                                </div>
                                                <div class="form-check">
                                                     <input class="poshtibanN form-check-input box-check" type="checkbox" id="seetrazEmployeeReportN" name="seetrazEmployeeReportN">
                                                    <label class="form-check-label">مشاهده</label>
                                                </div>
                                            </fieldset>
                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="reportPartN form-check-input d-inline-block" id="amalkardCustReportN" name="amalkardCustReportN"/>  عملکرد مشتریان </legend>
                                                   <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalkardCustReportN form-check-input d-inline-block" id="loginCustRepN" name="loginCustRepN"/>  گزارش ورود  </legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input box-check" id="deleteloginCustRepN" type="checkbox" name="deleteloginCustRepN">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input box-check" type="checkbox" id="editloginCustRepN" name="editloginCustRepN">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeloginCustRepN" name="seeloginCustRepN">
                                                                <label class="form-check-label">مشاهده</label>
                                                            </div>
                                                        </fieldset>
                                                   <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalkardCustReportN form-check-input d-inline-block" id="inActiveCustRepN" name="inActiveCustRepN"/>   غیر فعال   </legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input box-check" id="deleteinActiveCustRepN" type="checkbox" name="deleteinActiveCustRepN">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input box-check" type="checkbox" id="editinActiveCustRepN" name="editinActiveCustRepN">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeinActiveCustRepN" name="seeinActiveCustRepN">
                                                                <label class="form-check-label">مشاهده</label>
                                                            </div>
                                                        </fieldset>
                                                   <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalkardCustReportN form-check-input d-inline-block" id="noAdminCustRepN" name="noAdminCustRepN"/> فاقد کاربر </legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input box-check" id="deletenoAdminCustRepN" type="checkbox" name="deletenoAdminCustRepN">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input box-check" type="checkbox" id="editnoAdminCustRepN" name="editnoAdminCustRepN">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seenoAdminCustRepN" name="seenoAdminCustRepN">
                                                                <label class="form-check-label">مشاهده</label>
                                                            </div>
                                                        </fieldset>
                                                   <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalkardCustReportN form-check-input d-inline-block" id="returnedCustRepN" name="returnedCustRepN"/> ارجاعی  </legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input box-check" id="deletereturnedCustRepN" type="checkbox" name="deletereturnedCustRepN">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input box-check" type="checkbox" id="editreturnedCustRepN" name="editreturnedCustRepN">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seereturnedCustRepN" name="seereturnedCustRepN">
                                                                <label class="form-check-label">مشاهده</label>
                                                            </div>
                                                        </fieldset>
                                            </fieldset>
                                            
                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                              <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="reportPartN form-check-input d-inline-block" id="goodsReportN" name="goodsReportN"/> عملکرد کالا </legend>
                                                  <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="goodsReportN form-check-input d-inline-block" id="salegoodsReportN" name="salegoodsReportN"/> گزارش فروش کالا  </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deletesalegoodsReportN" type="checkbox" name="deletesalegoodsReportN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editsalegoodsReportN" name="editsalegoodsReportN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seesalegoodsReportN" name="seesalegoodsReportN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                                  <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="goodsReportN form-check-input d-inline-block" id="returnedgoodsReportN" name="returnedgoodsReportN"/> کالاهای برگشتی  </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deletereturnedgoodsReportN" type="checkbox" name="deletereturnedgoodsReportN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editreturnedgoodsReportN" name="editreturnedgoodsReportN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seereturnedgoodsReportN" name="seereturnedgoodsReportN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                                  <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="goodsReportN form-check-input d-inline-block" id="NoExistgoodsReportN" name="NoExistgoodsReportN"/> کالاهای فقد موجودی  </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deleteNoExistgoodsReportN" type="checkbox" name="deleteNoExistgoodsReportN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editNoExistgoodsReportN" name="editNoExistgoodsReportN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeNoExistgoodsReportN" name="seeNoExistgoodsReportN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                                  <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="goodsReportN form-check-input d-inline-block" id="nosalegoodsReportN" name="nosalegoodsReportN"/> کالاهای راکت </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deletenosalegoodsReportN" type="checkbox" name="deletenosalegoodsReportN">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editnosalegoodsReportN" name="editnosalegoodsReportN">
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seenosalegoodsReportN" name="seenosalegoodsReportN">
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                            </fieldset>

                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                              <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="reportPartN form-check-input d-inline-block" id="returnedReportgoodsReportN" name="returnedReportgoodsReportN"/> گزارش برگشتی کالا </legend>
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="returnedReportgoodsReportN form-check-input d-inline-block" id="returnedNTasReportgoodsReportN" name="returnedNTasReportgoodsReportN"/> تسویه نشده  </legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input box-check" id="deletereturnedNTasReportgoodsReportN" type="checkbox" name="deletereturnedNTasReportgoodsReportN">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input box-check" type="checkbox" id="editreturnedNTasReportgoodsReportN" name="editreturnedNTasReportgoodsReportN">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seereturnedNTasReportgoodsReportN" name="seereturnedNTasReportgoodsReportN">
                                                                <label class="form-check-label">مشاهده</label>
                                                            </div>
                                                        </fieldset>
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="returnedReportgoodsReportN form-check-input d-inline-block" id="tasgoodsReprtN" name="tasgoodsReprtN"/> تسویه شده  </legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input box-check" id="deletetasgoodsReprtN" type="checkbox" name="deletetasgoodsReprtN">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input box-check" type="checkbox" id="edittasgoodsReprtN" name="edittasgoodsReprtN">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seetasgoodsReprtN" name="seetasgoodsReprtN">
                                                                <label class="form-check-label">مشاهده</label>
                                                            </div>
                                                    </fieldset>
                                            </fieldset>

                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="goodsbargiriReportN form-check-input d-inline-block" id="goodsbargiriReportN" name="goodsbargiriReportN"/> گزارش بارگیری </legend>
                                                <div class="form-check">
                                                    <input class="form-check-input box-check" id="deletegoodsbargiriReportN" type="checkbox" name="deletegoodsbargiriReportN">
                                                    <label class="form-check-label">حذف</label>
                                                </div>
                                                <div class="form-check">
                                                     <input class="adminN form-check-input box-check" type="checkbox" id="editgoodsbargiriReportN" name="editgoodsbargiriReportN">
                                                    <label class="form-check-label">تغییر</label>
                                                </div>
                                                <div class="form-check">
                                                     <input class="poshtibanN form-check-input box-check" type="checkbox" id="seegoodsbargiriReportN" name="seegoodsbargiriReportN">
                                                    <label class="form-check-label">مشاهده</label>
                                                </div>
                                            </fieldset>
                                        </fieldset>
                                    </div>
                                 </div>
                              </div>
                            </div>
                         </div>
                       </div>
                     </div> 
                        <div class="modal-footer py-1">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" id="cancelAddAddmin"> انصراف <i class="fa-solid fa-xmark"> </i> </button>
                            <button type="submit" id="submitNewAdminbtn" class="btn btn-sm btn-primary">ذخیره <i class="fa fa-save" aria-hidden="true"> </i> </button> 
                        </div>
                  </div>
                </form>
            </div>
        </div>
    </div>
</div>

  <!-- Modal for reading comments-->
    <div class="modal fade dragableModal" id="moveEmployeeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog   modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel">انتقال کارمند</h5>
                </div>
                <div class="modal-body" id="readCustomerComment">
                    <div class="container">
                        <div class="row" style=" border:1px solid #dee2e6; padding:10px">
                        <div><button type="button" class="btn btn-sm btn-primary" id="moveEmployeeDoneBtn">انتقال <i class="fa fa-save fa-lg"></i></button></div>
                                <h4 style="padding:10px; border-bottom: 1px solid #dee2e6; text-align:center;">انتقال کارمند</h4>
                            </div>
                            <div class="row">
                                <table id="strCusDataTable"  class=' table table-bordered table-striped table-sm' style="background-color:#dee2e6">
                                    <thead class="tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>اسم</th>
                                        <th>شماره تماس</th>
                                        <th>انتخاب</th>
                                    </tr>
                                    </thead>
                                    <tbody id="headList">
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


    <!-- modal for editing user profile -->
    <div class="modal fade dragableModal" id="editProfile" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title">  ویرایش پروفایل  <span id="editProfileModalTitle"></span></h5>
            </div>
            <div class="modal-body">
                    <form action="{{url('/editAdmintListStuff')}}" method="POST"  enctype="multipart/form-data">
                        <input type="hidden" id="adminId" name="adminId">
                        @csrf
                            <div class="row"> 
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> نام </label>
                                        <input type="text" required class="form-control form-control-sm" autocomplete="off" name="name" id="adminName">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> نام خانوادگی</label>
                                        <input type="text" required class="form-control form-control-sm" autocomplete="off" name="lastName" id="adminLastName">
                                    </div>
                                </div>
                            
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> نام کاربری</label>
                                        <input type="text" required class="form-control form-control-sm" autocomplete="off" name="userName" id="adminUserName">
                                    </div>
                                </div>
                            
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> رمز کاربری</label>
                                        <input type="text" required class="form-control form-control-sm" autocomplete="off" name="password" id="adminPassword">
                                    </div>
                                </div>
                        
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> جنسیت  </label>
                                        <select class="form-select form-select-sm" name="sex" id="adminSex">
                                        </select>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label"> شماره تماس </label>
                                            <input type="number" required class="form-control form-control-sm" autocomplete="off" name="phone" id="adminPhone">
                                       </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                           <label class="dashboardLabel form-label"> عکس </label>
                                           <input type="file" class="form-control form-control-sm" name="picture">
                                         </div>
                                   </div>
                                    <div class="col-lg-6">
                                       <label class="dashboardLabel form-label"> آدرس </label>
                                       <input type="text" required minlength="3" id="adminAddress" class="form-control form-control-sm" autocomplete="off" name="address">
                                    </div>
                            </div>
                            <br>
                        <div class="row rounded px-0 mx-0" style="background-color:#abd2ed; padding-bottom:5px;"> 
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> نوع کاربر </label>
                                        <select class="form-select form-select-sm" name="employeeType"  id="employeeTypeEdit" required>
                                                <option id="managerEdit0" > -- </option>
                                                <option value="1" id="managerEdit" > مدیر </option>
                                                <option value="2" id="headEdit"> سرپرست </option>
                                                <option value="3" id="employeeEdit"> کارمند </option>
                                        </select>
                                    </div>
                                </div>
                            <div class="col-md-2">
                                <div class="form-group"  style="display:none" id="employeeJobDivEdit">
                                <label class="dashboardLabel form-label">وظیفه کارمند</label>
                                <select class="form-select form-select-sm" name="poshtibanType" id="poshtibanTypeEdit">
                                        <option value="0" id="jobEdit0">--</option>
                                        <option value="4" id="jobEdit1">راننده</option>
                                        <option value="2" id="jobEdit2">پشتیبان حضوری</option>
                                        <option value="2" id="jobEdit3">پشتیبان هماهنگی</option>
                                        <option value="2" id="jobEdit4">پشتیبان تلفنی</option>
                                        <option value="3" id="jobEdit5">بازاریاب حضوری</option>
                                        <option value="3" id="jobEdit6">بازاریاب هماهنگی</option>
                                        <option value="3" id="jobEdit7">بازاریاب تلفنی</option>
                                </select>
                            </div>
                        </div> 

                        <div class="col-md-2">
                            <div class="form-group"  style="display:none"  id="saleLineDivEdit">
                                <label class="dashboardLabel form-label"> خط فروش </label>
                                <select class="form-select form-select-sm" name="saleLine">
                                        <option value="0" > -- </option>
                                    @foreach($saleLines as $saleLine)
                                        <option value="{{$saleLine->SaleLineSn}}" id="saleLineWork{{$saleLine->SaleLineSn}}">{{$saleLine->LineName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                
                        <div class="col-md-2">
                            <div class="form-group"  style="display:none" id="managerDivEdit">
                                <label class="dashboardLabel form-label"> مدیر </label>
                                <select class="form-select form-select-sm" name="manager">
                                        <option id="managerIdEdit" value=""> -- </option>
                                    @foreach($managers as $manager)
                                        <option value="{{$manager->id}}" id="manageWork{{$manager->id}}"> {{$manager->name .' '. $manager->lastName}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group"  style="display:none" id="headDivEdit">
                                <label class="dashboardLabel form-label"> سرپرست </label>
                                <select class="form-select form-select-sm" name="head" id="head">
                                        <option id="headIdEdit" value=""> -- </option>
                                @foreach($heads as $head)
                                        <option value="{{$head->id}}" id="headWork{{$head->id}}"> {{$head->name .' '. $head->lastName}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div> <br>
                        <div class="row"> 
                            <div class="col-md-4 mb-2"> 
                                <div class="form-group" id="">
                                    <label class="dashboardLabel form-label"> دسترسی به  </label>
                                    <select class="form-select form-select-sm" name="" id="">
                                            <option value="" >--</option>
                                            <option value="" > کل مشتریان </option>
                                            <option value="" > کل آلارمها </option>
                                            <option value="" > کل نظر سنجی </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-label mb-0"> توضیحات</label>
                                    <textarea class="form-control" rows="2" name="discription" style="background-color:blanchedalmond" id="adminDiscription"></textarea>
                                </div>
                            </div>
                        </div> <br>
                        <div class='card mb-4 mt-1' style="background-color:#abd2ed; padding-top:1%; paddding:0;">
                            <div class="container">
                                    <ul class="header-list nav nav-tabs" data-tabs="tabs">
                                        <li><a class="active" data-toggle="tab" style="color:black;" href="#baseInfoEDIT"> اطلاعات پایه </a></li>
                                        <li><a data-toggle="tab" style="color:black;"  href="#definElementEDIT"> تعریف عناصر </a></li>
                                        <li><a data-toggle="tab" style="color:black;"  href="#kalasTabEDIT"> عملیات </a></li>
                                        <li><a data-toggle="tab" style="color:black;"  href="#peoplesEDIT"> گزارشات </a></li>
                                    </ul>
                                    <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0; margin-bottom:2%; padding:2%; border-radius:10px 10px 2px 2px; dir:ltr !important;">
                                        <div class="c-checkout tab-pane active" id="baseInfoEDIT" style="border-radius:10px 10px 2px 2px;">
                                            <div class="container">
                                            <div class="row">
                                                    <fieldset class="border rounded-3">
                                                        <legend  class="float-none w-auto forLegend "> 
                                                            <input type="checkbox" name="baseInfoED" class="baseInfoFirstED form-check-input d-inline-block" id="baseInfoED"/> اطلاعات پایه </legend>
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                            <legend  class="float-none w-auto fs-6">
                                                                <input type="checkbox" name="baseInfoProfileED" id="baseInfoProfileED" class="baseInfoED form-check-input d-inline-block"/> پروفایل </legend>
                                                                
                                                            <div class="form-check">
                                                                <input class="ProfileED form-check-input box-check" id="deleteProfileED" type="checkbox" name="deleteProfileED">
                                                                <label class="form-check-label ">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="ProfileED form-check-input box-check " id="editProfileED" type="checkbox" name="editProfileED">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="ProfileED form-check-input box-check" id="seeProfileED" type="checkbox" name="seeProfileED">
                                                                <label class="form-check-label">مشاهده</label>
                                                            </div>
                                                        </fieldset>
            
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" name="infoRdED" id="infoRdED" class="baseInfoED form-check-input d-inline-block"/> R & D  &nbsp;</legend>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" name="rdSentED" id="rdSentED" class="rdED form-check-input d-inline-block"/> وارد شده ها    &nbsp;</legend>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input box-check" id="deleteSentRdED" type="checkbox" name="deleteSentRdED">
                                                                            <label class="form-check-label">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="adminN form-check-input box-check" id="editSentRdED" type="checkbox" name="editSentRdED">
                                                                            <label class="form-check-label">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN form-check-input box-check" id="seeSentRdED" type="checkbox"  name="seeSentRdED">
                                                                            <label class="form-check-label">مشاهده</label>
                                                                        </div>
                                                                    </fieldset>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" name="rdNotSentED" id="rdNotSentED" class="rdED form-check-input d-inline-block"/> وارد نشده ها    &nbsp;</legend>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input box-check" id="deleteRdNotSentED" type="checkbox" name="deleteRdNotSentED">
                                                                            <label class="form-check-label">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="adminN form-check-input box-check" id="editRdNotSentED" type="checkbox" name="editRdNotSentED">
                                                                            <label class="form-check-label">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN form-check-input box-check" id="seeRdNotSentED" type="checkbox"  name="seeRdNotSentED">
                                                                            <label class="form-check-label">مشاهده</label>
                                                                        </div>
                                                                    </fieldset>
                                                        </fieldset>
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" id="addSaleLineED" class="baseInfoED form-check-input d-inline-block" name="specialSettingED" /> افزودن خط فروش </legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input" id="deleteSaleLineED" type="checkbox" name="deleteSaleLineED"> 
                                                                <label class="form-check-label box-check">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input" type="checkbox" id="editSaleLineED" name="editSaleLineED">
                                                                <label class="form-check-label box-check">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN web form-check-input" id="seeSaleLineED" type="checkbox" name="seeSaleLineED">
                                                                <label class="form-check-label box-check">مشاهده</label>
                                                            </div>
                                                        </fieldset>
            
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" id="baseInfoSettingED" class="baseInfoED form-check-input d-inline-block" name="baseInfoSettingED" /> تنظیمات </legend>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" id="InfoSettingAccessED" class="InfoSettingED form-check-input d-inline-block" name="InfoSettingAccessED" /> سطح دسترسی </legend>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" id="deleteSettingAccessED" type="checkbox" name="deleteSettingAccessED"> 
                                                                            <label class="form-check-label box-check">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="adminN form-check-input" type="checkbox" id="editSettingAccessED" name="editSettingAccessED">
                                                                            <label class="form-check-label box-check">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN web form-check-input" id="seeSettingAccessED" type="checkbox" name="seeSettingAccessED">
                                                                            <label class="form-check-label box-check">مشاهده</label>
                                                                        </div>
                                                                    </fieldset>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" id="InfoSettingTargetED" class="InfoSettingED form-check-input d-inline-block" name="InfoSettingTargetED" />  تارگت ها و امتیازها  </legend>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" id="deleteSettingTargetED" type="checkbox" name="deleteSettingTargetED"> 
                                                                            <label class="form-check-label box-check">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="adminN form-check-input" type="checkbox" id="editSettingTargetED" name="editSettingTargetED">
                                                                            <label class="form-check-label box-check">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN web form-check-input" id="seeSettingTargetED" type="checkbox" name="seeSettingTargetED">
                                                                            <label class="form-check-label box-check">مشاهده</label>
                                                                        </div>
                                                                    </fieldset>
                                                            </fieldset>
                                                        </fieldset>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>
            
                                        <div class="c-checkout tab-pane" id="definElementEDIT" style="border-radius:10px 10px 2px 2px;">
                                            <div class="container">
                                                <div class="row">
                                                        <fieldset class="border rounded-3">
                                                            <legend  class="float-none w-auto fs-6"><input type="checkbox" id="declareElementED" class="declareElementED form-check-input d-inline-block" name="declareElementED" /> تعریف عناصر  </legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input box-check" id="deletedeclareElementED" type="checkbox" name="deletedeclareElementED">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input box-check" type="checkbox" id="editdeclareElementED" name="editdeclareElementED">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seedeclareElementED" name="seedeclareElementED">
                                                                <label class="form-check-label">مشاهده</label>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            </div>
            
                                        <div class="c-checkout tab-pane" id="kalasTabEDIT" style="border-radius:10px 10px 2px 2px;">
                                            <div class="container">
                                                <div class="row">
                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto forLegend"><input type="checkbox" class="oppED form-check-input d-inline-block" name="oppED" id="oppED"/> عملیات </legend>
            
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartED form-check-input d-inline-block" id="oppTakhsisED" name="oppTakhsisED"/> تخصیص به کاربر </legend>
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppTakhsisED form-check-input d-inline-block" id="oppManagerED" name="oppManagerED"/> مدیران  </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deleteManagerOppED" type="checkbox" name="deleteManagerOppED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editManagerOppED" name="editManagerOppED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeManagerOppED" name="seeManagerOppED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                            </fieldset>
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppTakhsisED form-check-input d-inline-block" id="oppHeadED" name="oppHeadED"/> سرپرستان  </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deleteHeadOppED" type="checkbox" name="deleteHeadOppED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editHeadOppED" name="editHeadOppED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeHeadOppED" name="seeHeadOppED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                            </fieldset>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppTakhsisED form-check-input d-inline-block" id="oppBazaryabED" name="oppBazaryabED"/> بازاریابها  </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deleteBazaryabOppED" type="checkbox" name="deleteBazaryabOppED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editBazaryabOppED" name="editBazaryabOppED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeBazaryabOppED" name="seeBazaryabOppED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                            </fieldset>
                                                    </fieldset>
            
            
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartED form-check-input d-inline-block" id="oppDriverED" name="oppDriverED"/>  راننده ها  </legend>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppDriverED form-check-input d-inline-block" id="oppDriverServiceED" name="oppDriverServiceED"/> سرویس راننده ها  </legend>
                                                                <div class="form-check">
                                                                    <input class="form-check-input box-check" id="deleteoppDriverServiceED" type="checkbox" name="deleteoppDriverServiceED">
                                                                    <label class="form-check-label">حذف</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="adminN form-check-input box-check " type="checkbox" id="editoppDriverServiceED" name="editoppDriverServiceED">
                                                                    <label class="form-check-label">تغییر</label>
                                                                </div>
                                                                <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check " type="checkbox" id="seeoppDriverServiceED" name="seeoppDriverServiceED">
                                                                    <label class="form-check-label">مشاهده</label>
                                                                </div>
                                                            </fieldset>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppDriverED form-check-input d-inline-block" id="oppBargiriED" name="oppBargiriED"/>  بار گیری  </legend>
                                                                <div class="form-check">
                                                                    <input class="form-check-input box-check" id="deleteoppBargiriED" type="checkbox" name="deleteoppBargiriED">
                                                                    <label class="form-check-label">حذف</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="adminN form-check-input box-check " type="checkbox" id="editoppBargiriED" name="editoppBargiriED">
                                                                    <label class="form-check-label">تغییر</label>
                                                                </div>
                                                                <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check " type="checkbox" id="seeoppBargiriED" name="seeoppBargiriED">
                                                                    <label class="form-check-label">مشاهده</label>
                                                                </div>
                                                            </fieldset>
                                                    </fieldset>
            
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartED form-check-input d-inline-block" id="oppNazarSanjiED" name="oppNazarSanjiED"/> نظر سنجی </legend>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppNazarSanjiED form-check-input d-inline-block" id="todayoppNazarsanjiED" name="todayoppNazarsanjiED"/> نظرات امروز  </legend>
                                                                        <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deletetodayoppNazarsanjiED" type="checkbox" name="deletetodayoppNazarsanjiED">
                                                                            <label class="form-check-label">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="edittodayoppNazarsanjiED" name="edittodayoppNazarsanjiED">
                                                                            <label class="form-check-label">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seetodayoppNazarsanjiED" name="seetodayoppNazarsanjiED">
                                                                            <label class="form-check-label">مشاهده</label>
                                                                        </div>
                                                            </fieldset>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppNazarSanjiED form-check-input d-inline-block" id="pastoppNazarsanjiED" name="pastoppNazarsanjiED"/> نظرات گذشته   </legend>
                                                                        <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deletepastoppNazarsanjiED" type="checkbox" name="deletepastoppNazarsanjiED">
                                                                            <label class="form-check-label">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editpastoppNazarsanjiED" name="editpastoppNazarsanjiED">
                                                                            <label class="form-check-label">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seepastoppNazarsanjiED" name="seepastoppNazarsanjiED">
                                                                            <label class="form-check-label">مشاهده</label>
                                                                        </div>
                                                            </fieldset>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppNazarSanjiED form-check-input d-inline-block" id="DoneoppNazarsanjiED" name="DoneoppNazarsanjiED"/> نظرات انجام شده    </legend>
                                                                        <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deleteDoneoppNazarsanjiED" type="checkbox" name="deleteDoneoppNazarsanjiED">
                                                                            <label class="form-check-label">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editDoneoppNazarsanjiED" name="editDoneoppNazarsanjiED">
                                                                            <label class="form-check-label">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeDoneoppNazarsanjiED" name="seeDoneoppNazarsanjiED">
                                                                            <label class="form-check-label">مشاهده</label>
                                                                        </div>
                                                            </fieldset>
                                                    </fieldset>
            
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartED form-check-input d-inline-block" id="OppupDownBonusED" name="OppupDownBonusED" /> افزایش و کاهش امتیازات  </legend>
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="OppupDownBonusED form-check-input d-inline-block" id="AddOppupDownBonusED" name="AddOppupDownBonusED" />    اضافه شده  </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deleteAddOppupDownBonusED" type="checkbox" name="deleteAddOppupDownBonusED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editAddOppupDownBonusED" name="editAddOppupDownBonusED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox"  id="seeAddOppupDownBonusED" name="seeAddOppupDownBonusED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                            </fieldset>
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="OppupDownBonusED form-check-input d-inline-block" id="SubOppupDownBonusED" name="SubOppupDownBonusED" />  کاهش یافته  </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deleteSubOppupDownBonusED" type="checkbox" name="deleteSubOppupDownBonusED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editSubOppupDownBonusED" name="editSubOppupDownBonusED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox"  id="seeSubOppupDownBonusED" name="seeSubOppupDownBonusED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                            </fieldset>
                                                    </fieldset>
            
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartED form-check-input d-inline-block" id="oppRDED" name="oppRDED"/>  R & D </legend>
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppRDED form-check-input d-inline-block" id="AddedoppRDED" name="AddedoppRDED"/>  وارده شده  </legend>
                                                                <div class="form-check">
                                                                    <input class="form-check-input box-check" id="deleteAddedoppRDED" type="checkbox" name="deleteAddedoppRDED">
                                                                    <label class="form-check-label">حذف</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="adminN form-check-input box-check" type="checkbox" id="editAddedoppRDED" name="editAddedoppRDED">
                                                                    <label class="form-check-label">تغییر</label>
                                                                </div>
                                                                <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeAddedoppRDED" name="seeAddedoppRDED">
                                                                    <label class="form-check-label">مشاهده</label>
                                                                </div>
                                                            </fieldset>
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppRDED form-check-input d-inline-block" id="NotAddedoppRDED" name="NotAddedoppRDED"/>  وارده نشده  </legend>
                                                                <div class="form-check">
                                                                    <input class="form-check-input box-check" id="deleteNotAddedoppRDED" type="checkbox" name="deleteNotAddedoppRDED">
                                                                    <label class="form-check-label">حذف</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="adminN form-check-input box-check" type="checkbox" id="editNotAddedoppRDED" name="editNotAddedoppRDED">
                                                                    <label class="form-check-label">تغییر</label>
                                                                </div>
                                                                <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeNotAddedoppRDED" name="seeNotAddedoppRDED">
                                                                    <label class="form-check-label">مشاهده</label>
                                                                </div>
                                                            </fieldset>
                                                    </fieldset>
            
                                                    
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartED form-check-input d-inline-block" id="oppCalendarED" name="oppCalendarED"/> تقویم روزانه  </legend>
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppCalendarED form-check-input d-inline-block" id="oppjustCalendarED" name="oppjustCalendarED"/> تقویم روزانه </legend>
                                                                <div class="form-check">
                                                                <input class="form-check-input box-check" id="deleteoppjustCalendarED" type="checkbox" name="deleteoppjustCalendarED">
                                                                    <label class="form-check-label">حذف</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="adminN form-check-input box-check" type="checkbox" id="editoppjustCalendarED" name="editoppjustCalendarED">
                                                                    <label class="form-check-label">تغییر</label>
                                                                </div>
                                                                <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeoppjustCalendarED" name="seeoppjustCalendarED">
                                                                    <label class="form-check-label">مشاهده</label>
                                                                </div>
                                                            </fieldset>
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppCalendarED form-check-input d-inline-block" id="oppCustCalendarED" name="oppCustCalendarED"/> لیست مشتریان </legend>
                                                                <div class="form-check">
                                                                <input class="form-check-input box-check" id="deleteoppCustCalendarED" type="checkbox" name="deleteoppCustCalendarED">
                                                                    <label class="form-check-label">حذف</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="adminN form-check-input box-check" type="checkbox" id="editoppCustCalendarED" name="editoppCustCalendarED">
                                                                    <label class="form-check-label">تغییر</label>
                                                                </div>
                                                                <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeoppCustCalendarED" name="seeoppCustCalendarED">
                                                                    <label class="form-check-label">مشاهده</label>
                                                                </div>
                                                            </fieldset>
                                                    </fieldset>
            
                                                    
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartED form-check-input d-inline-block" id="alarmoppED" name="alarmoppED" /> آلارم  </legend>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="alarmoppED form-check-input d-inline-block" id="allalarmoppED" name="allalarmoppED" /> آلارمها   </legend>
                                                                    
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deleteallalarmoppED" type="checkbox" name="deleteallalarmoppED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editallalarmoppED" name="editallalarmoppED" >
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeallalarmoppED" name="seeallalarmoppED" >
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                                </fieldset>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="alarmoppED form-check-input d-inline-block" id="donealarmoppED" name="donealarmoppED" />  انجام شده </legend>
                                                                    
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deletedonealarmoppED" type="checkbox" name="deletedonealarmoppED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editdonealarmoppED" name="editdonealarmoppED" >
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seedonealarmoppED" name="seedonealarmoppED" >
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                                </fieldset>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="alarmoppED form-check-input d-inline-block" id="NoalarmoppED" name="NoalarmoppED" />  مشتریان فاقد آلارم   </legend>
                                                                    
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deleteNoalarmoppED" type="checkbox" name="deleteNoalarmoppED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editNoalarmoppED" name="editNoalarmoppED" >
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeNoalarmoppED" name="seeNoalarmoppED" >
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                                </fieldset>
                                                    </fieldset>
            
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartED form-check-input d-inline-block" id="massageOppED" name="massageOppED" /> پیام ها   </legend>
                                                        
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deletemassageOppED" type="checkbox" name="deletemassageOppED">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editmassageOppED" name="editmassageOppED" >
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seemassageOppED" name="seemassageOppED" >
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
            
                                                    <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="oppPartED form-check-input d-inline-block" id="justBargiriOppED" name="justBargiriOppED" /> بار گیری   </legend>
                                                        <div class="form-check">
                                                            <input class="form-check-input box-check" id="deletejustBargiriOppED" type="checkbox" name="deletejustBargiriOppED">
                                                            <label class="form-check-label">حذف</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editjustBargiriOppED" name="editjustBargiriOppED" >
                                                            <label class="form-check-label">تغییر</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seejustBargiriOppED" name="seejustBargiriOppED" >
                                                            <label class="form-check-label">مشاهده</label>
                                                        </div>
                                                    </fieldset>
                                                </fieldset>
                                                </div>
                                            </div>
                                        </div>
            
                                        <div class="c-checkout tab-pane" id="peoplesEDIT" style="border-radius:10px 10px 2px 2px;">
                                            <div class="container">
                                                <div class="row">
                                                <fieldset class="border rounded-3">
                                                <legend  class="float-none w-auto "><input type="checkbox" class="reportED form-check-input d-inline-block" id="reportED" name="reportED"/> گزارشات  </legend>
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                            <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="reportPartED form-check-input d-inline-block" id="amalKardreportED" name="amalKardreportED"/> عملکرد کاربران </legend>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalKardreportED form-check-input d-inline-block" id="managerreportED" name="managerreportED"/> مدیران  </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deletemanagerreportED" type="checkbox" name="deletemanagerreportED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editmanagerreportED" name="editmanagerreportED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seemanagerreportED" name="seemanagerreportED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                                </fieldset>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalKardreportED form-check-input d-inline-block" id="HeadreportED" name="HeadreportED"/>  سرپرستان   </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deleteHeadreportED" type="checkbox" name="deleteHeadreportED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editHeadreportED" name="editHeadreportED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeHeadreportED" name="seeHeadreportED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                                </fieldset>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalKardreportED form-check-input d-inline-block" id="poshtibanreportED" name="poshtibanreportED"/>  پشتیبانها   </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deleteposhtibanreportED" type="checkbox" name="deleteposhtibanreportED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editposhtibanreportED" name="editposhtibanreportED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeposhtibanreportED" name="seeposhtibanreportED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                                </fieldset>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalKardreportED form-check-input d-inline-block" id="bazaryabreportED" name="bazaryabreportED"/>  بازاریابها    </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deletebazaryabreportED" type="checkbox" name="deletebazaryabreportED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editbazaryabreportED" name="editbazaryabreportED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seebazaryabreportED" name="seebazaryabreportED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                                </fieldset>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalKardreportED form-check-input d-inline-block" id="reportDriverED" name="reportDriverED"/>  راننده ها     </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deletereportDriverED" type="checkbox" name="deletereportDriverED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editreportDriverED" name="editreportDriverED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seereportDriverED" name="seereportDriverED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                                </fieldset>
                                                        </fieldset>
            
            
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="reportPartED form-check-input d-inline-block" id="trazEmployeeReportED" name="trazEmployeeReportED"/> تراز کاربران </legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input box-check" id="deletetrazEmployeeReportED" type="checkbox" name="deletetrazEmployeeReportED">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input box-check" type="checkbox" id="edittrazEmployeeReportED" name="edittrazEmployeeReportED">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seetrazEmployeeReportED" name="seetrazEmployeeReportED">
                                                                <label class="form-check-label">مشاهده</label>
                                                            </div>
                                                        </fieldset>
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="reportPartED form-check-input d-inline-block" id="amalkardCustReportED" name="amalkardCustReportED"/>  عملکرد مشتریان </legend>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalkardCustReportED form-check-input d-inline-block" id="loginCustRepED" name="loginCustRepED"/>  گزارش ورود  </legend>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input box-check" id="deleteloginCustRepED" type="checkbox" name="deleteloginCustRepED">
                                                                            <label class="form-check-label">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editloginCustRepED" name="editloginCustRepED">
                                                                            <label class="form-check-label">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeloginCustRepED" name="seeloginCustRepED">
                                                                            <label class="form-check-label">مشاهده</label>
                                                                        </div>
                                                                    </fieldset>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalkardCustReportED form-check-input d-inline-block" id="inActiveCustRepED" name="inActiveCustRepED"/>   غیر فعال   </legend>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input box-check" id="deleteinActiveCustRepED" type="checkbox" name="deleteinActiveCustRepED">
                                                                            <label class="form-check-label">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editinActiveCustRepED" name="editinActiveCustRepED">
                                                                            <label class="form-check-label">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeinActiveCustRepED" name="seeinActiveCustRepED">
                                                                            <label class="form-check-label">مشاهده</label>
                                                                        </div>
                                                                    </fieldset>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalkardCustReportED form-check-input d-inline-block" id="noAdminCustRepED" name="noAdminCustRepED"/> فاقد کاربر </legend>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input box-check" id="deletenoAdminCustRepED" type="checkbox" name="deletenoAdminCustRepED">
                                                                            <label class="form-check-label">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editnoAdminCustRepED" name="editnoAdminCustRepED">
                                                                            <label class="form-check-label">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seenoAdminCustRepED" name="seenoAdminCustRepED">
                                                                            <label class="form-check-label">مشاهده</label>
                                                                        </div>
                                                                    </fieldset>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="amalkardCustReportED form-check-input d-inline-block" id="returnedCustRepED" name="returnedCustRepED"/> ارجاعی  </legend>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input box-check" id="deletereturnedCustRepED" type="checkbox" name="deletereturnedCustRepED">
                                                                            <label class="form-check-label">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editreturnedCustRepED" name="editreturnedCustRepED">
                                                                            <label class="form-check-label">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seereturnedCustRepED" name="seereturnedCustRepED">
                                                                            <label class="form-check-label">مشاهده</label>
                                                                        </div>
                                                                    </fieldset>
                                                        </fieldset>
                                                        
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="reportPartED form-check-input d-inline-block" id="goodsReportED" name="goodsReportED"/> عملکرد کالا </legend>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="goodsReportED form-check-input d-inline-block" id="salegoodsReportED" name="salegoodsReportED"/> گزارش فروش کالا  </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deletesalegoodsReportED" type="checkbox" name="deletesalegoodsReportED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editsalegoodsReportED" name="editsalegoodsReportED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seesalegoodsReportED" name="seesalegoodsReportED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                                </fieldset>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="goodsReportED form-check-input d-inline-block" id="returnedgoodsReportED" name="returnedgoodsReportED"/> کالاهای برگشتی  </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deletereturnedgoodsReportED" type="checkbox" name="deletereturnedgoodsReportED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editreturnedgoodsReportED" name="editreturnedgoodsReportED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seereturnedgoodsReportED" name="seereturnedgoodsReportED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                                </fieldset>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="goodsReportED form-check-input d-inline-block" id="NoExistgoodsReportED" name="NoExistgoodsReportED"/> کالاهای فقد موجودی  </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deleteNoExistgoodsReportED" type="checkbox" name="deleteNoExistgoodsReportED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editNoExistgoodsReportED" name="editNoExistgoodsReportED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seeNoExistgoodsReportED" name="seeNoExistgoodsReportED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                                </fieldset>
                                                            <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="goodsReportED form-check-input d-inline-block" id="nosalegoodsReportED" name="nosalegoodsReportED"/> کالاهای راکت </legend>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input box-check" id="deletenosalegoodsReportED" type="checkbox" name="deletenosalegoodsReportED">
                                                                        <label class="form-check-label">حذف</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="adminN form-check-input box-check" type="checkbox" id="editnosalegoodsReportED" name="editnosalegoodsReportED">
                                                                        <label class="form-check-label">تغییر</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="poshtibanN form-check-input box-check" type="checkbox" id="seenosalegoodsReportED" name="seenosalegoodsReportED">
                                                                        <label class="form-check-label">مشاهده</label>
                                                                    </div>
                                                                </fieldset>
                                                        </fieldset>
            
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="reportPartED form-check-input d-inline-block" id="returnedReportgoodsReportED" name="returnedReportgoodsReportED"/> گزارش برگشتی کالا </legend>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="returnedReportgoodsReportED form-check-input d-inline-block" id="returnedNTasReportgoodsReportED" name="returnedNTasReportgoodsReportED"/> تسویه نشده  </legend>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input box-check" id="deletereturnedNTasReportgoodsReportED" type="checkbox" name="deletereturnedNTasReportgoodsReportED">
                                                                            <label class="form-check-label">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="adminN form-check-input box-check" type="checkbox" id="editreturnedNTasReportgoodsReportED" name="editreturnedNTasReportgoodsReportED">
                                                                            <label class="form-check-label">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seereturnedNTasReportgoodsReportED" name="seereturnedNTasReportgoodsReportED">
                                                                            <label class="form-check-label">مشاهده</label>
                                                                        </div>
                                                                    </fieldset>
                                                                <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                                    <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="returnedReportgoodsReportED form-check-input d-inline-block" id="tasgoodsReprtED" name="tasgoodsReprtED"/> تسویه شده  </legend>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input box-check" id="deletetasgoodsReprtED" type="checkbox" name="deletetasgoodsReprtED">
                                                                            <label class="form-check-label">حذف</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="adminN form-check-input box-check" type="checkbox" id="edittasgoodsReprtED" name="edittasgoodsReprtED">
                                                                            <label class="form-check-label">تغییر</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="poshtibanN form-check-input box-check" type="checkbox" id="seetasgoodsReprtED" name="seetasgoodsReprtED">
                                                                            <label class="form-check-label">مشاهده</label>
                                                                        </div>
                                                                </fieldset>
                                                        </fieldset>
            
                                                        <fieldset class="border rounded-3" style="display: justify-content:flex-start; float: right;">
                                                        <legend  class="float-none w-auto fs-6"> <input type="checkbox" class="goodsbargiriReportED form-check-input d-inline-block" id="goodsbargiriReportED" name="goodsbargiriReportED"/> گزارش بارگیری </legend>
                                                            <div class="form-check">
                                                                <input class="form-check-input box-check" id="deletegoodsbargiriReportED" type="checkbox" name="deletegoodsbargiriReportED">
                                                                <label class="form-check-label">حذف</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="adminN form-check-input box-check" type="checkbox" id="editgoodsbargiriReportED" name="editgoodsbargiriReportED">
                                                                <label class="form-check-label">تغییر</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="poshtibanN form-check-input box-check" type="checkbox" id="seegoodsbargiriReportED" name="seegoodsbargiriReportED">
                                                                <label class="form-check-label">مشاهده</label>
                                                            </div>
                                                        </fieldset>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                </div> 
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" id="cancelEditProfile"> انصراف <i class="fa-solid fa-xmark"> </i> </button>
                                        <button type="submit" class="btn btn-sm btn-primary">ذخیره <i class="fa fa-save" aria-hidden="true"> </i> </button>
                                    </div>
                                   
                                </form>
                        </div>
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
        $(this).toggleClass(function(){
          return $(this).is('.caret::before, .caret-minus') ? 'caret::before caret-minus' : 'caret-minus';
     })
      
    this.parentElement.querySelector(".nested").classList.toggle("active");
    this.classList.toggle("caret-down caret-down-minus::before");
  });
}
</script>
@endsection

