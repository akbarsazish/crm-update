@extends('layout')
@section('content')
<style>
   
.grid-container {
        display: grid;
        grid-template-columns: auto auto auto auto;
        gap: 3px;
        padding: 5px;
        }

.grid-container > div {
        text-align: center;
        font-size: 14px;
        font-weight:bold;
        text-align:right;
        padding:5px;
        background-color:#bad5ef;
        border-radius:6px;
        }

.bargeriTable {
  display:none;
}
</style>

<div class="container-fluid containerDiv">
      <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                    <fieldset class="border rounded mt-5 sidefieldSet">
                        <legend  class="float-none w-auto legendLabel mb-0"> سرویس راننده ها  </legend>
                         @if(hasPermission(Session::get("asn"),"oppDriverServiceN") > 0)
                          <div class="form-check">
                              <input class="form-check-input p-2 float-end" checked type="radio" name="settings" id="dirverServiceRadio">
                              <label class="form-check-label me-4" for="assesPast"> سرویس راننده ها </label>
                          </div>
                          @endif
                          @if(hasPermission(Session::get("asn"),"oppBargiriN") > 0)
                          <div class="form-check mb-3">
                              <input class="form-check-input p-2 float-end" type="radio" name="settings" id="bargeriRadio">
                              <label class="form-check-label me-4" for="assesPast">  بارگیری  </label>
                          </div>
                        @endif
                        <div id="serviceDive"> 
                          <form action="{{url('/searchDriverServices')}}" id="getServiceSearchForm" method="get">
                            <div class="form-group col-sm-12 mb-1">
                              <input type="text" name="firstDateService" placeholder="از تاریخ" class="form-control form-control-sm" id="firstDateReturned">
                            </div>
                            <div class="form-group col-sm-12 mb-2">
                              <input type="text" name="secondDateService" placeholder="تا تاریخ" class="form-control form-control-sm" id="secondDateReturned">
                            </div>
                            <div class="col-sm-12 mt-2">
                              <select class="form-select form-select-sm" name="driverSn" id="searchDriverSelect">
                                <option value="-1"> راننده ها </option>
                                @forelse($drivers as $driver)
                                <option value="{{$driver->driverId}}" id="driver{{$driver->driverId}}">{{$driver->name.' '.$driver->lastName}}</option>
                                @empty
                                <div><span>داده وجود ندارد</span></div>
                                @endforelse
                              </select>
                            </div>
                            <button class='btn btn-primary btn-sm text-warning' type="submit" id='getServiceSearchBtn'> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                          </form>
                        </div> 
                    </fieldset>
                  </div>
                <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                    <div class="row contentHeader driverServiceHeader">
                          <div class="col-sm-2 mt-2" id="orderService">
                              <select class="form-select form-select-sm" id="orderDriverServices">
                                  <option value="-1"> مرتب سازی </option>
                                  <option value="name">   اسم  </option>
                                  <option value="serviceType">  مسیر  </option>
                                  <option value="TimeStamp">  تاریخ  </option>
                              </select>
                          </div>
                          <div class="col-sm-10 text-start">
                             @if(hasPermission(Session::get("asn"),"oppDriverServiceN") > 1)
                                <button class="btn btn-primary btn-sm driverServicesTable" id="driverServicesBtn"> افزودن سرویس <i class="fa fa-plus"></i> </button>
                                <button class="btn btn-primary btn-sm driverServicesTable" id="editDriverServicesBtn" disabled> ویرایش سرویس <i class="fa fa-edit"></i> </button>
                              @endif
                            </div>
                    </div>
                    <div class="row mainContent">
                        <table class="table table-bordered table-striped myDataTable" id="driverServicesTable">
                              <thead class="tableHeader">
                                  <tr>
                                    <th>  دریف  </th>
                                    <th> نام راننده</th>
                                    <th> نوع مسیر </th>
                                    <th> توضیحات </th>
                                    <th> تاریخ </th>
                                    <th> انتخاب </th>
                                  </tr>
                              </thead>
                              <tbody class="tableBody" id="driverServiceBodyList">
                                @foreach($services as $service)
                                  <tr onclick="setDriverServiceStuff(this,{{$service->ServiceSn}}); selectTableRow(this);">
                                      <th>{{$loop->iteration}}</th>
                                      <td> {{$service->name.' '.$service->lastName}}</td>
                                      <td>@if($service->serviceType==2) متوسط @endif @if($service->serviceType==1) دور @endif @if($service->serviceType==3) نزدیک @endif </td>
                                      <td>{{$service->discription}} </td>
                                      <td>{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($service->TimeStamp))->format('Y/m/d H:i:s')}}</td>
                                      <td>  <input  type="radio" name="radioBtn" value="{{$service->ServiceSn}}"> </td>
                                  </tr>
                                @endforeach
                                </tbody>
                          </table>

                    <!-- bargeri tables -->
                        <table class="select-highlight table table-bordered table-striped bargeriTable" id="">
                                <thead class="tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>نام کاربر</th>
                                        <th>نقش کاربری</th>
                                        <th>شماره تماس</th>
                                        <th> جزئیات </th>
                                        <th>فعال</th>
                                    </tr>
                                </thead>
                                <tbody class="tableBody" id="" >
                                    @foreach ($admins as $admin)
                                        <tr onclick="showBargiriFactors(this,{{$admin->driverId}}); selectTableRow(this);">
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{trim($admin->name)." ".trim($admin->lastName)}}</td>
                                            <td>{{trim($admin->adminType)}}</td>
                                            <td>{{trim($admin->phone)}}</td>
                                            <td> <a href="{{url('crmDriver?asn='.$admin->driverId.'')}}"> <i class="fa fa-eye fa-lg" style="color:#000;"></i> </a> </td>
                                            <td>
                                                <input class="mainGroupId" type="radio" name="AdminId[]" value="{{$admin->id}}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                             </table>

                            <table class="table table-bordered bargeriTable" id="tableGroupList">
                                <thead class="bg-primary text-warning tableHeader">
                                    <tr>
                                        <th>#</th>
                                        <th>نام مشتری</th>
                                        <th> آدرس </th>
                                        <th>تلفن </th>
                                        <th style="width:111px">فاکتور</th>
                                        <th> انتخاب</th>
                                    </tr>
                                </thead>
                                <tbody class="tableBody" id="crmDriverBargeri">
                                </tbody>
                            </table>
                    </div>
                    <div class="row contentFooter"> 
                        <div class="col-lg-12 text-start" id="bottomServiceBttons">
                            <button type="button" class="btn btn-sm btn-primary footerButton" onclick="getServices('TODAY')"> امروز  : </button>
                            <button type="button" class="btn btn-sm btn-primary footerButton" onclick="getServices('YESTERDAY')"> دیروز : </button>
                            <button type="button" class="btn btn-sm btn-primary footerButton" onclick="getServices('LASTHUNDRED')"> صد تای آخر : 100</button>
                            <button type="button" class="btn btn-sm btn-primary footerButton" onclick="getServices('ALLSERVICES')"> همه : </button>
                        </div>
                    </div>
                </div>
        </div>
    </div>








<!-- Modal for adding services -->
<div class="modal fade" id="driverServicesModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="driverServicesModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{url('/addService')}}" id="addService" method="get">
          <div class="modal-header bg-primary text-white py-2">
              <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
              <h6 class="modal-title" id="driverServicesModalLabel"> افزودن سرویس راننده ها  </h6>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-6">
                <select class="form-select form-select-sm" id="selectDriver" name="selectDriver">
                  <option selected> انتخاب راننده </option>
                  <option value="2"> کیانی  </option>
                  <option value="2"> اسنب  </option>
                  <option value="3"> محمد رضا حداد </option>
                </select>
              </div>
              <div class="col-lg-6">
                <select class="form-select form-select-sm" id="selectService" name="selectService">
                  <option value="3"> نزدیک </option>
                  <option value="2"> متوسط </option>
                  <option value="1"> دور </option>
                </select>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="mb-3 mt-2">
                <label for="exampleFormControlTextarea1" class="form-label" > توضیحات </label>
                <textarea class="form-control" name="discription" rows="3"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"> بستن <i class="fa fa-xmark"></i> </button>
            <button type="submit" class="btn btn-primary btn-sm"> ذخیره <i class="fa fa-save"></i> </button>
          </div>
        </form>
      </div>
    </div>
  </div>


<!-- Modal for editing services-->
<div class="modal fade" id="editDriverServicModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editDriverServicModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{url('/editDriverService')}}" id="editServiceForm" method="get">
      <div class="modal-header bg-primary text-white py-2">
          <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
          <h6 class="modal-title" id="editDriverServicModalLabel"> ویرایش سرویس راننده ها  </h6>
      </div>
      <div class="modal-body">
            <div class="row">
              <input type="text" name="serviceSn" id="serviceSn">
                <div class="col-lg-6">
                   <select class="form-select form-select-sm" name="editDriverSn" id="editDriverSn">
                    </select>
                </div>
                <div class="col-lg-6">
                   <select class="form-select form-select-sm" name="editServiceType" id="editServiceSn">
                        <option id="weakService" value="3">نزدیک</option>
                        <option id="mediumService" value="2">متوسط</option>
                        <option id="strongService" value="1">دور</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-12">
               <div class="mb-3 mt-2">
                   <label for="editDiscription" class="form-label"> توضیحات </label>
                    <textarea class="form-control" id="editDiscription" name="editDiscription" rows="3"></textarea>
                </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"> بستن <i class="fa fa-xmark"></i> </button>
        <button type="submit" class="btn btn-primary btn-sm"> ذخیره <i class="fa fa-save"></i> </button>
      </div>
      </form>
    </div>
  </div>
</div>



<!-- modal for demonestrating factor deatails -->
            <div class="modal fade" id="bargiriFactor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header py-2">
                        <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close"></button>
                        <h5 class="modal-title" id="exampleModalLabel">فاکتور فروش <span  id="totalMoney"> </span> </h5>
                    </div>
                        <div class="modal-body py-1">
                                       <div class="grid-container">
                                            <div class="item1"> <b>مشتری  :  </b> <span id="customerNameFactor">  </span> </div>
                                            <div class="item2"> <b> آدرس  :  </b> <span id="customerAddressFactor"> </span>    </div>
                                            <div class="item3"> <b>تلفن :  </b> <span id="customerPhoneFactor"> </span>   </div>
                                             <div class="item4"><span> مبلغ کارت:</span>    <span id="cartPrice1"> </span></div>
                                             <div class="item5"><span> واریز:  </span>   <span id="varizPrice1"> </span></div>
                                             <div class="item6"><span> مبلغ نقد :</span>  <span id="naghdPrice1">  </span></div>
                                             <div class="item7 text-danger"> <span> تخفیف :  </span>   <span id="takhfifPrice1"> </span></div> 
                                             <div class="item8"><span> باقی :  </span>   <span  id="diffPrice1"> </span> </div>
                                             <div class="item9"><span> توضیح:  </span>   <span  id="description1">  </span></div>
                                        </div>
                            <div class="row">
                                <table id="strCusDataTable" class='table table-bordered table-striped table-sm'>
                                    <thead class="bg-primary tableHeader">
                                        <tr onclick="selectTableRow(this);">
                                            <th class="driveFactor">#</th>
                                            <th>نام کالا </th>
                                            <th class="driveFactor">تعداد/مقدار</th>
                                            <th>واحد کالا</th>
                                            <th>فی (تومان)</th>
                                            <th style="width:121px;">مبلغ (تومان)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productList" class="tableBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">بستن  <i class="fa fa-xmark"> </i> </button>
                        <input type="hidden" id="bargiriyBYSId"/>
                    </div>
                </div>
                </div>
            </div>


@endsection