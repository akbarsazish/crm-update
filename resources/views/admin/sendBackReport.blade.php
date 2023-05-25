@extends('layout')
@section('content')
<style>
    th, td {
        font-size:10px !important;
    }
</style>
    <div class="container-fluid containerDiv">
      <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                   <fieldset class="border rounded mt-5 sidefieldSet">
                        <legend  class="float-none w-auto legendLabel mb-0"> تنظیمات </legend>
                        @if(hasPermission(Session::get("asn"),"returnedReportgoodsReportN") > -1)
                        <!-- <div class="form-check">
                            <input class="form-check-input p-2 float-end" type="radio" name="settings" id="elseSettingsRadio">
                            <label class="form-check-label me-4" for="assesPast"> تسویه شده  </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input p-2 float-end" type="radio" name="settings" id="settingAndTargetRadio">
                            <label class="form-check-label me-4" for="assesPast"> تسویه نشده  </label>
                        </div> -->
                        <div class="input-group input-group-sm mt-2">
                            <span class="input-group-text" id=""> از تاریخ  </span>
                            <input type="text" class="form-control" id="assesFirstDate">
                        </div>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" id=""> تا تاریخ  </span>
                            <input type="text" class="form-control" id="assesSecondDate">
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="input-group input-group-sm mt-2">
                                  <span class="input-group-text text-danger" id=""> ساعت   </span>
                                  <input type="time" class="form-control" id="assesFirstTime">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group input-group-sm mt-2">
                                    <span class="input-group-text text-danger" id=""> الی </span>
                                    <input type="time" class="form-control" id="assesSecondTime">
                                </div>
                           </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="input-group input-group-sm mt-2">
                                  <span class="input-group-text text-danger" id=""> فاکتور   </span>
                                  <input type="number" min=0 placeholder="0" class="form-control" id="firstFactNo">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group input-group-sm mt-2">
                                    <span class="input-group-text text-danger" id=""> الی </span>
                                    <input type="number" min=0 placeholder="0" class="form-control" id="secondFactNo">
                                </div>
                           </div>
                        </div>
                         <div class="input-group input-group-sm mt-2">
                            <span class="input-group-text sm" id=""> خریدار </span>
                            <input type="text" class="form-control" value="" id="customerNameId">
                        </div>
                        <div class="input-group input-group-sm mt-2">
                            <span class="input-group-text sm" id=""> اسم کالا</span>
                            <input type="text" class="form-control" value="" id="goodName">
                        </div>
                        <div class="input-group input-group-sm mt-2">
                            <span class="input-group-text" id=""> تنظیم کننده</span>
                            <select class="form-select form-select-sm mt" id="setterName">
                            </select>
                        </div>
                        <div class="input-group input-group-sm mt-2">
                            <span class="input-group-text sm" id=""> انبار </span>
                            <select class="form-select form-select-sm mt" id="stockSnId">
                                <option value="1"> افشار </option>
                                <option value="2"> بندر  </option>
                                <option value="2"> سعید اباد  </option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12 mb-2">
                            <button class='btn btn-primary btn-sm text-warning' type="button" onclick="getReturnedFactors()" > بازخوانی <i class="fal fa-refresh fa-lg"></i></button>
                        </div>
                        @endif
                    </fieldset>
                  </div>
                <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                    <div class="row contentHeader"> 
                    </div>
                    <div class="row mainContent">
                         <table class="table table-bordered table-striped table-sm mb-0" id="allKala">
                            <thead class="tableHeader">
                                <tr>
                                    <th >ردیف</th>
                                    <th>کد مشتری</th>
                                    <th>نام مشتری</th>
                                    <th class="forMobile-hide">شماره</th>
                                    <th>تاریخ</th>
                                    <th class="forMobile-hide">توضیحات</th>
                                    <th>مبلغ فاکتور</th>
                                    <th class="forMobile-hide">انبار</th>
                                    <th class="forMobile-hide">تنظیم کننده</th>
                                    <th>تاریخ بارگیری</th>
                                    <th class="forMobile-hide">ساعت بارگیری</th>
                                    <th class="forMobile-hide">ساعت ثبت</th>
                                </tr>
                            </thead>
                            <tbody id='returnedBodyFactorList' class="select-highlightKala tableBody" style="height:300px !important;">
                            
                            </tbody>
                        </table>
                            
                        <div class="col-lg-12 py-0 my-0" id="factorInfo">
                            <h6 style=" text-align:center;">فاکتور فروش </h6>
                            <div class="grid-container mx-1">
                                <div class="item1"> <b style="color:red; bold"> تاریخ فاکتور   :  </b> <span id="factorDateP">  </span> </div>
                                <div class="item2"> <b style="color:red; bold"> مشتری  :  </b> <span  id="customerNameFactorP"> </span>    </div>
                                <div class="item3"> <b style="color:red; bold"> آدرس  :  </b> <span id="customerAddressFactorP"> </span>   </div>
                                <div class="item4"> <b style="color:red; bold"> تلفن : </b>    <span id="customerPhoneFactorP"> </span></div>
                                <div class="item5"> <b style="color:red; bold"> کاربر :  </b>   <span id="Admin1P"> </span></div>
                                <div class="item6"> <b style="color:red; bold">  شماره فاکتور : </b>  <span id="factorSnFactorP">  </span></div>
                            </div>     
                    </div>
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
                        <tbody id="productListP" class="tableBody" style="height:200px !important;">

                        </tbody>
                    </table>
                    
                    <div class="row contentFooter py-0 my-0">
                        <div class="col-lg-12 text-start">
                            <button type="button" class="btn btn-sm btn-primary footerButton" onclick="getReturnDateHistory('TODAY')">امروز</button>
                            <button type="button" class="btn btn-sm btn-primary footerButton" onclick="getReturnDateHistory('YESTERDAY')">دیروز</button>
                            <button type="button" class="btn btn-sm btn-primary footerButton" onclick="getReturnDateHistory('LASTHUNDRED')">صدتای آخر</button>
                            <button type="button" class="btn btn-sm btn-primary footerButton" onclick="getReturnDateHistory('ALL')">همه</button>
                        </div>
                    </div>
                </div>
        </div>
    </div>

@endsection





