@extends('layout')
@section('content')
<style>
  .settingTableBody{
    display:block;
    height:133px;
    overflow-y:scroll;
  }


  .tableHeader {
    position: sticky !important;
    top: 0 !important;
    background-color: #198754 !important;
}

.tableBody {
    height: 444px !important;
    overflow-y: scroll !important;
    display: block !important;
}

.tableHeader .tableBbody,
tr {
    display: table !important;
    table-layout: fixed !important;
    width: 100% !important;
    text-align: center;
}

.tableHeader .tableBbody,
tr>th:first-child {
    width: 55px !important;
}

.tableHeader .tableBbody,
tr>td:first-child {
    width: 65px !important;
}

.tableHeader .tableBbody,
tr>th:last-child {
    width: 70px;
}

.tableHeader .tableBbody,
tr>td:last-child {
    width: 60px;
}

</style>
    <div class="container-fluid containerDiv">
      <div class="row">
          <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
              <fieldset class="border rounded mt-5 sidefieldSet">
                  <legend  class="float-none w-auto legendLabel mb-0"> تنظیمات </legend>
                     
                     
                      @if(hasPermission(Session::get("asn"),"infoSettingTargetN") > 0)
                          
                      <div class="form-check">
                          <input class="form-check-input p-2 float-end" type="radio" name="settings" id="settingAndTargetRadio" checked>
                          <label class="form-check-label me-4" for="assesPast"> تارگت ها و امتیازات </label>
                      </div>
                      <form action="{{url('/getAsses')}}" method="get">
                              <button class='btn btn-primary btn-sm text-warning settigRefreshBtn' type="button" id='getAssesBtn'> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                          </form>
                     @endif
                     @if(hasPermission(Session::get("asn"),"InfoSettingAccessN") > 0)
                      <div class="form-check">
                          <input class="form-check-input p-2 float-end" type="radio" name="settings" id="generalSettingsRadio">
                          <label class="form-check-label me-4" for="assesPast"> تنظیمات عمومی سیستم </label>
                      </div>
                      @endif
                    
                    </fieldset>
                  </div>
                    <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                        <div class="row mainContent">
                          <div class="c-checkout container-fluid" id="targetAndSettingContent" style="background-image: linear-gradient(to right, #ffffff,#3fa7ef,#3fa7ef); margin:0.1% 0; margin-bottom:0; padding:0.5% !important; border-radius:6px 6px 2px 2px;">
                            <div class="col-sm-12" style="margin: 0; padding:0;">
                                <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0; text-align:center;">
                                    <li><a class="active" data-toggle="tab" style="color:black;"  href="#targetSetting">بازاریابها</a></li>
                                    <li><a data-toggle="tab" style="color:black;"  href="#tellPTargetSetting">پشتیبانهای تلفنی</a></li>
                                    <li><a data-toggle="tab" style="color:black;"  href="#presentPTargetSetting">پشتیبانهای حضوری</a></li>
                                    <li><a data-toggle="tab" style="color:black;"  href="#syncPTargetSetting">پشتیبانهای هماهنگی</a></li>
                                    <li><a data-toggle="tab" style="color:black;"  href="#driverTargetSetting">رانندها</a></li>
                                </ul>
                            </div>
                            <!-- style=" height:200px !important; overflow-y:scroll !important; display:block !important;" -->
              <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                  <div class="row c-checkout rounded-3 tab-pane active" id="targetSetting">
                      <div class="col-sm-12 px-2">
                        <div class="row px-3">
                          <fieldset class="rounded">
                              <legend  class="float-none w-auto"> تارگت ها  </legend>
                              <input type="hidden" name="" id="selectTargetId">
                                <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                          <select class="form-select form-select-sm" aria-label="Default select example" id="selectTarget">
                                            @foreach($targets as $target)
                                              <option value="{{$target->id}}">{{$target->BaseName}}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                        <div class="col-lg-9 col-md-9 col-sm-9 text-start">
                                           @if(hasPermission(Session::get("asn"),"infoSettingTargetN") > 0)
                                            <button class='btn btn-primary btn-sm text-warning' id="targetEditBtn" type="button" disabled  data-toggle="modal" style="margin-top:-3px;">ویرایش تارگت<i class="fa fa-edit fa-lg"></i></button> 
                                            @endif
                                          </div>
                                </div>
                               <div class="row px-1">
                                    <table class="table table table-bordered">
                                      <thead>
                                            <tr>
                                                <td> ردیف </td>
                                                <td> اسم تارگت </td>
                                                <td>تارگیت 1</td>
                                                <td> امتیاز 1</td>
                                                <td>تارگیت 2</td>
                                                <td> امتیاز 2</td>
                                                <td>تارگیت 3</td>
                                                <td> امتیاز 3</td>
                                                <td class="for-mobil"> انتخاب  </td>
                                            </tr>
                                      </thead>
                                      <tbody id="targetList" class="settingTableBody">
                                          @foreach($targets as $target)
                                              <tr class="targetTableTr" onclick="setTargetStuff(this); selectTableRow(this);">
                                                  <td>{{$loop->iteration}}</td>
                                                  <td>{{$target->BaseName}}</td>
                                                  <td> {{number_format($target->firstTarget)}}</td>
                                                  <td> {{$target->firstTargetBonus}} </td>
                                                  <td> {{number_format($target->secondTarget)}}</td>
                                                  <td> {{$target->secondTargetBonus}} </td>
                                                  <td> {{number_format($target->thirdTarget)}}</td>
                                                  <td> {{$target->thirdTargetBonus}} </td>
                                                  <td class="for-mobil"><input class="form-check-input" name="targetId" type="radio" value="{{$target->id}}"></td>
                                              </tr>
                                             @endforeach
                                      </tbody>
                                    </table>
                                  </div>
                              </fieldset>
                             </div>
                            <div class="row px-3">
                            <input type="hidden" id="specialBonusIdForEdit">
                            <fieldset class="rounded px-1">
                              <legend  class="float-none w-auto"> امتیازات  </legend>
                                <div class="row px-0">
                                  <div class="col-lg-3 col-md-3 col-sm-3 mt-3">
                                    <!-- <span data-toggle="modal" data-target="#addSpecialBonusModal" ><i class="fa fa-plus-circle fa-lg" style="color:#1684db; font-size:33px"></i></span> -->
                                  </div>
                                  <div class="col-lg-9 col-md-9 col-sm-9 text-start">
                                      @if(hasPermission(Session::get("asn"),"infoSettingTargetN") > 0)
                                          <button class='btn btn-primary btn-sm text-warning' id="specialBonusBtn" type="button" disabled  data-toggle="modal" style="margin-top:-3px;">ویرایش  امتیاز <i class="fa fa-edit fa-lg"></i></button> 
                                      @endif
                                          <!-- <button class='btn btn-danger text-warning' style="margin-top:-3px;" disabled id="deleteSpecialBonus"> حذف <i class="fa fa-trash fa-lg"></i></button>  -->
                                  </div>
                                </div>
                                <table class="table table-bordered  px-0">
                                  <thead>
                                        <tr>
                                        <th>ردیف </th>
                                        <th>اساس</th>
                                        <th>امتیاز</th>
                                        <th>حد</th>
                                        <th >انتخاب</th>
                                        </tr>
                                  </thead>
                                  <tbody id="specialBonusList" class="settingTableBody">
                                    @foreach($specialBonuses as $Bonus)
                                        <tr onclick="setSpecialBonusStuff(this); selectTableRow(this);">
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$Bonus->BaseName}}</td>
                                            <td>{{$Bonus->Bonus}}</td>
                                            <td>{{number_format($Bonus->limitAmount,0,"",",")}}</td>
                                            <td> <input class="form-check-input" name="specialBonusId" type="radio" value="{{$Bonus->id}}"></td>
                                        </tr>
                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                            </fieldset>
                          </div>
                        </div>

                  <div class="row c-checkout rounded-3 tab-pane" id="tellPTargetSetting">
                    <div class="col-sm-12">
                        <div class="row px-2">
                            <fieldset class="rounded">
                                <legend  class="float-none w-auto">تارگت ها</legend>
                                <input type="hidden" name="" id="selectTargetId">
                                <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                          <select class="form-select form-select-sm" aria-label="Default select example" id="selectTarget">
                                            @foreach($targets as $target)
                                              <option value="{{$target->id}}">{{$target->BaseName}}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-sm-1 mt-3">
                                          <!-- <span data-toggle="modal" data-target="#addingTargetModal"><i class="fa fa-plus-circle fa-lg" style="color:#1684db; font-size:33px"></i></span> -->
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8 text-start">
                                           @if(hasPermission(Session::get("asn"),"infoSettingTargetN") > 0)
                                               <button class='btn btn-primary btn-sm  text-warning' id="generalTargetBtn3" type="button" disabled onclick="editGeneralBase(this)" style="margin-top:-3px;">ویرایش تارگت<i class="fa fa-edit fa-lg"></i></button> 
                                           @endif
                                          <!-- <button class='btn btn-danger text-warning' disabled style="margin-top:-3px;" id="deleteTargetBtn"> حذف <i class="fa fa-trash fa-lg"></i></button>  -->
                                        </div>
                                 </div>
                                 <div class="row px-1">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr class="targetTableTr">
                                                  <th> ردیف </th>
                                                  <th> اسم تارگت </th>
                                                  <th>تارگیت 1</th>
                                                  <th> امتیاز 1</th>
                                                  <th>تارگیت 2</th>
                                                  <th> امتیاز 2</th>
                                                  <th>تارگیت 3</th>
                                                  <th> امتیاز 3</th>
                                                  <th class="for-mobil"> انتخاب  </th>
                                                </tr>
                                                </thead>
                                                <tbody id="gtargetList3" class="settingTableBody">
                                                @foreach($generalTargets as $target)
                                                @if($target->userType==3)
                                                <tr class="targetTableTr" onclick="setGeneralTargetStuff(this,{{$target->userType}}); selectTableRow(this);">
                                                <td>{{$loop->iteration}}</td>
                                                    <td>{{$target->baseName}}</td>
                                                    <td> {{number_format($target->firstTarget)}}</td>
                                                    <td> {{$target->firstTargetBonus}} </td>
                                                    <td> {{number_format($target->secondTarget)}}</td>
                                                    <td> {{$target->secondTargetBonus}} </td>
                                                    <td> {{number_format($target->thirdTarget)}}</td>
                                                    <td> {{$target->thirdTargetBonus}} </td>
                                                    <td class="for-mobil"> <input class="form-check-input" name="targetId" type="radio" value="{{$target->SnBase.'_'.$target->userType}}"></td>
                                                </tr>
                                                @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                </fieldset>
                            </div>

                            <div class="row px-2">
                              <input type="hidden" id="generalBonusIdForEdit">
                            <fieldset class="rounded px-1">
                              <legend  class="float-none w-auto"> امتیازات</legend>
                                <div class="row">
                                  <div class="col-lg-3 col-md-3 col-sm-3 mt-3">
                                    <!-- <span data-toggle="modal" data-target="#addgeneralBonusModal" ><i class="fa fa-plus-circle fa-lg" style="color:#1684db; font-size:33px"></i></span> -->
                                  </div>
                                 <div class="col-lg-9 col-md-9 col-sm-9 text-start">
                                      @if(hasPermission(Session::get("asn"),"infoSettingTargetN") > 0)
                                          <button class='btn btn-primary btn-sm text-warning' id="generalBonusBtn3" type="button" disabled    onclick="openGeneralSettingModal(this)" style="margin-top:-3px;">ویرایش  امتیاز <i class="fa fa-edit fa-lg"></i></button> 
                                      @endif
                                          <!-- <button class='btn btn-danger text-warning' style="margin-top:-3px;" disabled id="deletegeneralBonus"> حذف <i class="fa fa-trash fa-lg"></i></button>  -->
                                  </div>
                                </div>
                                <table class="table table-bordered">
                                  <thead>
                                      <tr>
                                          <th>ردیف </th>
                                          <th>اساس</th>
                                          <th>امتیاز</th>
                                          <th>حد</th>
                                          <th>انتخاب</th>
                                      </tr>
                                  </thead>
                                  <tbody id="generalBonusList3" class="settingTableBody">
                                    @foreach($generalBonuses as $Bonus)
                                    @if($Bonus->userType==3)
                                      <tr onclick="setGeneralBonusStuff(this,{{$Bonus->userType}}); selectTableRow(this);">
                                            <td >{{$loop->iteration}}</td>
                                            <td>{{$Bonus->BaseName}}</td>
                                            <td>{{$Bonus->Bonus}}</td>
                                            <td>{{number_format($Bonus->limitAmount,0,"",",")}}</td>
                                            <td> <input class="form-check-input" name="generalBonusId" type="radio" value="{{$Bonus->id}}"></td>
                                      </tr>
                                    @endif
                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                            </fieldset>
                           </div>
                        </div>

                         
                  <div class="row c-checkout rounded-3 tab-pane" id="presentPTargetSetting">
                    <div class="col-sm-12">
                    <div class="row px-2">
                            <fieldset class="rounded">
                                <legend  class="float-none w-auto">تارگت ها</legend>
                                <input type="hidden" name="" id="selectTargetId">
                                <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                          <select class="form-select form-select-sm" aria-label="Default select example" id="selectTarget">
                                            @foreach($targets as $target)
                                              <option value="{{$target->id}}">{{$target->BaseName}}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-sm-1 mt-3">
                                          <!-- <span data-toggle="modal" data-target="#addingTargetModal"><i class="fa fa-plus-circle fa-lg" style="color:#1684db; font-size:33px"></i></span> -->
                                        </div>
                                       <div class="col-lg-8 col-md-8 col-sm-8 text-start">
                                         @if(hasPermission(Session::get("asn"),"infoSettingTargetN") > 0)
                                            <button class='btn btn-primary btn-sm text-warning' id="generalTargetBtn1" type="button" disabled onclick="editGeneralBase(this)"   style="margin-top:-3px;">ویرایش تارگت<i class="fa fa-edit fa-lg"></i></button> 
                                         @endif
                                          <!-- <button class='btn btn-danger text-warning' disabled style="margin-top:-3px;" id="deleteTargetBtn"> حذف <i class="fa fa-trash fa-lg"></i></button>  -->
                                        </div>
                                </div>
                                  <div class="row">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr class="targetTableTr">
                                                      <th> ردیف </th>
                                                      <th> اسم تارگت </th>
                                                      <th>تارگیت 1</th>
                                                      <th> امتیاز 1</th>
                                                      <th>تارگیت 2</th>
                                                      <th> امتیاز 2</th>
                                                      <th>تارگیت 3</th>
                                                      <th> امتیاز 3</th>
                                                      <th class="for-mobil"> انتخاب  </th>
                                                </tr>
                                                </thead>
                                                <tbody id="gtargetList1" class="settingTableBody">
                                                @foreach($generalTargets as $target)
                                                @if($target->userType==1)
                                                <tr class="targetTableTr" onclick="setGeneralTargetStuff(this,{{$target->userType}}); selectTableRow(this);">
                                                        <td>{{$loop->iteration}}</td>
                                                        <td>{{$target->baseName}}</td>
                                                        <td> {{number_format($target->firstTarget)}}</td>
                                                        <td> {{$target->firstTargetBonus}} </td>
                                                        <td> {{number_format($target->secondTarget)}}</td>
                                                        <td> {{$target->secondTargetBonus}} </td>
                                                        <td> {{number_format($target->thirdTarget)}}</td>
                                                        <td> {{$target->thirdTargetBonus}} </td>
                                                        <td class="for-mobil"> <input class="form-check-input" name="targetId" type="radio" value="{{$target->SnBase.'_'.$target->userType}}"></td>
                                                </tr>
                                                  @endif

                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                </fieldset>
                            </div>
                            <div class="row px-2">
                              <input type="hidden" id="generalBonusIdForEdit">
                            <fieldset class="rounded px-1">
                              <legend  class="float-none w-auto"> امتیازات</legend>
                                <div class="row">
                                  <div class="col-lg-3 col-md-3 col-sm-3 mt-3">
                                    <!-- <span data-toggle="modal" data-target="#addgeneralBonusModal" ><i class="fa fa-plus-circle fa-lg" style="color:#1684db; font-size:33px"></i></span> -->
                                  </div>
                                  <div class="col-lg-9 col-md-9 col-sm-9 text-start">
                                      @if(hasPermission(Session::get("asn"),"infoSettingTargetN") > 0)
                                          <button class='btn btn-primary btn-sm text-warning' id="generalBonusBtn1" type="button" disabled  onclick="openGeneralSettingModal(this)" style="margin-top:-3px;">ویرایش  امتیاز <i class="fa fa-edit fa-lg"></i></button> 
                                      @endif
                                          <!-- <button class='btn btn-danger text-warning' style="margin-top:-3px;" disabled id="deletegeneralBonus"> حذف <i class="fa fa-trash fa-lg"></i></button>  -->
                                  </div>
                                </div>
                                <table class="table table-bordered">
                                  <thead>
                                      <tr>
                                          <th>ردیف </th>
                                          <th>اساس</th>
                                          <th>امتیاز</th>
                                          <th>حد</th>
                                          <th class="for-mobil">انتخاب</th>
                                      </tr>
                                  </thead>
                                  <tbody id="generalBonusList1" class="settingTableBody">
                                    @foreach($generalBonuses as $Bonus)
                                    @if($Bonus->userType==1)
                                        <tr onclick="setGeneralBonusStuff(this,{{$Bonus->userType}}); selectTableRow(this);">
                                          <td >{{$loop->iteration}}</td>
                                          <td>{{$Bonus->BaseName}}</td>
                                          <td>{{$Bonus->Bonus}}</td>
                                          <td>{{number_format($Bonus->limitAmount,0,"",",")}}</td>
                                          <td class="for-mobil"> <input class="form-check-input" name="generalBonusId" type="radio" value="{{$Bonus->id}}"></td>
                                        </tr>
                                    @endif
                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                            </fieldset>
                          </div>
                        </div>


                    <div class="row c-checkout rounded-3 tab-pane" id="syncPTargetSetting" >
                      <div class="col-sm-12">
                        <div class="row px-2">
                          <fieldset class="rounded">
                            <legend  class="float-none w-auto">تارگت‌ها</legend>
                            <input type="hidden" name="" id="selectTargetId">
                              <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-3">
                                  <select class="form-select form-select-sm" aria-label="Default select example" id="selectTarget">
                                    @foreach($targets as $target)
                                      <option value="{{$target->id}}">{{$target->BaseName}}</option>
                                    @endforeach
                                  </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 mt-3">
                                  <!-- <span data-toggle="modal" data-target="#addingTargetModal"><i class="fa fa-plus-circle fa-lg" style="color:#1684db; font-size:33px"></i></span> -->
                                </div>
                               <div class="col-lg-6 col-md-6 col-sm-6 text-start">
                                 @if(hasPermission(Session::get("asn"),"infoSettingTargetN") > 0)
                                  <button class='btn btn-primary btn-sm  text-warning' type="button" disabled onclick="editGeneralBase(this)"  id="generalTargetBtn2" style="margin-top:-3px;">ویرایش تارگت<i class="fa fa-edit fa-lg"></i></button> 
                                 @endif
                                  <!-- <button class='btn btn-danger text-warning' disabled style="margin-top:-3px;" id="deleteTargetBtn"> حذف <i class="fa fa-trash fa-lg"></i></button>  -->
                                </div>
                              </div>
                              <div class="row px-1">
                                <table class="table table-bordered">
                                  <thead>
                                        <tr class="targetTableTr">
                                              <th> ردیف </th>
                                              <th> اسم تارگت </th>
                                              <th>تارگیت 1</th>
                                              <th> امتیاز 1</th>
                                              <th>تارگیت 2</th>
                                              <th> امتیاز 2</th>
                                              <th>تارگیت 3</th>
                                              <th> امتیاز 3</th>
                                              <th class="for-mobil"> انتخاب  </th>
                                        </tr>
                                  </thead>
                                  <tbody id="gtargetList2" class="settingTableBody">
                                    @foreach($generalTargets as $target)
                                      @if($target->userType==2)
                                          <tr class="targetTableTr" onclick="setGeneralTargetStuff(this,{{$target->userType}}); selectTableRow(this);">
                                                <td> {{$loop->iteration}}</td>
                                                <td> {{$target->baseName}}</td>
                                                <td> {{number_format($target->firstTarget)}}</td>
                                                <td> {{$target->firstTargetBonus}} </td>
                                                <td> {{number_format($target->secondTarget)}}</td>
                                                <td> {{$target->secondTargetBonus}} </td>
                                                <td> {{number_format($target->thirdTarget)}}</td>
                                                <td> {{$target->thirdTargetBonus}} </td>
                                                <td class="for-mobil"> <input class="form-check-input" name="targetId" type="radio" value="{{$target->SnBase.'_'.$target->userType}}"></td>
                                            </tr>
                                      @endif

                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                            </fieldset>
                          </div>
                          <div class="row px-1">
                              <input type="hidden" id="specialBonusIdForEdit">
                            <fieldset class="rounded">
                              <legend  class="float-none w-auto"> امتیازات</legend>
                                <div class="row">
                                  <div class="col-lg-3 col-md-3 col-sm-3 mt-3">
                                    <!-- <span data-toggle="modal" data-target="#addSpecialBonusModal" ><i class="fa fa-plus-circle fa-lg" style="color:#1684db; font-size:33px"></i></span> -->
                                  </div>
                                  <div class="col-lg-9 col-md-9 col-sm-9 text-start">
                                      @if(hasPermission(Session::get("asn"),"infoSettingTargetN") > 0)
                                          <button class='btn btn-primary btn-sm  text-warning' id="generalBonusBtn2" type="button" disabled    onclick="openGeneralSettingModal(this)" style="margin-top:-3px;">ویرایش  امتیاز <i class="fa fa-edit fa-lg"></i></button> 
                                      @endif
                                          <!-- <button class='btn btn-danger text-warning' style="margin-top:-3px;" disabled id="deleteSpecialBonus"> حذف <i class="fa fa-trash fa-lg"></i></button>  -->
                                  </div>
                                </div>
                                <table class="table table-bordered">
                                  <thead>
                                      <tr>
                                          <th >ردیف </th>
                                          <th>اساس</th>
                                          <th>امتیاز</th>
                                          <th>حد</th>
                                          <th >انتخاب</th>
                                      </tr>
                                  </thead>
                                  <tbody id="generalBonusList2" class="settingTableBody">
                                    @foreach($generalBonuses as $Bonus)
                                    @if($Bonus->userType==2)
                                        <tr onclick="setGeneralBonusStuff(this,{{$Bonus->userType}}); selectTableRow(this);">
                                              <td  >{{$loop->iteration}}</td>
                                              <td>{{$Bonus->BaseName}}</td>
                                              <td>{{$Bonus->Bonus}}</td>
                                              <td>{{number_format($Bonus->limitAmount,0,"",",")}}</td>
                                              <td> <input class="form-check-input" name="generalBonusId" type="radio" value="{{$Bonus->id}}"></td>
                                        </tr>
                                    @endif
                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                            </fieldset>
                        </div>
                      </div>

                      <div class="row c-checkout rounded-3 tab-pane" id="driverTargetSetting">
                      <div class="col-sm-12">
                        <div class="row px-2">
                          <fieldset class="rounded">
                            <legend  class="float-none w-auto"> تارگت‌ها</legend>
                            <input type="hidden" name="" id="selectTargetId">
                              <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-3">
                                  <select class="form-select form-select-sm" aria-label="Default select example" id="selectTarget">
                                    @foreach($generalTargets as $target)
                                    @if($target->userType==4)
                                      <tr class="targetTableTr" onclick="setGeneralTargetStuff(this,{{$target->userType}})">
                                      <option value="{{$target->SnBase}}">{{$target->baseName}}</option>
                                      @endif
                                    @endforeach
                                  </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 mt-3">
                                  <!-- <span data-toggle="modal" data-target="#addingTargetModal"><i class="fa fa-plus-circle fa-lg" style="color:#1684db; font-size:33px"></i></span> -->
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 text-start">
                                   @if(hasPermission(Session::get("asn"),"infoSettingTargetN") > 0)
                                    <button class='btn btn-primary btn-sm  text-warning' type="button" disabled onclick="editGeneralBase(this)"  id="generalTargetBtn4" style="margin-top:-3px;">ویرایش تارگت<i class="fa fa-edit fa-lg"></i></button> 
                                   @endif
                                    <!-- <button class='btn btn-danger text-warning' disabled style="margin-top:-3px;" id="deleteTargetBtn"> حذف <i class="fa fa-trash fa-lg"></i></button>  -->
                                </div>
                              </div>
                              <div class="row px-2">
                                <table class="table table-bordered">
                                  <thead>
                                        <tr class="targetTableTr">
                                              <th> ردیف </th>
                                              <th> اسم تارگت </th>
                                              <th>تارگیت 1</th>
                                              <th> امتیاز 1</th>
                                              <th>تارگیت 2</th>
                                              <th> امتیاز 2</th>
                                              <th>تارگیت 3</th>
                                              <th> امتیاز 3</th>
                                              <th class="for-mobil"> انتخاب  </th>
                                        </tr>
                                  </thead>
                                  <tbody id="gtargetList4" class="settingTableBody">
                                    @foreach($generalTargets as $target)
                                      @if($target->userType==4)
                                           <tr class="targetTableTr" onclick="setGeneralTargetStuff(this,{{$target->userType}}); selectTableRow(this);">
                                              <td>{{$loop->iteration}}</td>
                                              <td>{{$target->baseName}}</td>
                                              <td> {{number_format($target->firstTarget)}}</td>
                                              <td> {{$target->firstTargetBonus}} </td>
                                              <td> {{number_format($target->secondTarget)}}</td>
                                              <td> {{$target->secondTargetBonus}} </td>
                                              <td> {{number_format($target->thirdTarget)}}</td>
                                              <td> {{$target->thirdTargetBonus}} </td>
                                              <td class="for-mobil"> <input class="form-check-input" name="targetId" type="radio" value="{{$target->SnBase.'_'.$target->userType}}"></td>
                                          </tr>
                                      @endif

                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                            </fieldset>
                          </div>
                          <div class="row">
                              <input type="hidden" id="specialBonusIdForEdit">
                            <fieldset class="rounded">
                              <legend  class="float-none w-auto"> امتیازات</legend>
                                <div class="row">
                                  
                                  <div class="col-lg-1 col-md-1 col-sm-1 mt-3">
                                    <!-- <span data-toggle="modal" data-target="#addSpecialBonusModal" ><i class="fa fa-plus-circle fa-lg" style="color:#1684db; font-size:33px"></i></span> -->
                                  </div>
                                  <div class="col-lg-11 col-md-11 col-sm-11 text-start">
                                      @if(hasPermission(Session::get("asn"),"infoSettingTargetN") > 0)
                                          <button class='btn btn-primary btn-sm  text-warning' id="generalBonusBtn4" type="button" disabled    onclick="openGeneralSettingModal(this)" style="margin-top:-3px;">ویرایش  امتیاز <i class="fa fa-edit fa-lg"></i></button> 
                                      @endif
                                          <!-- <button class='btn btn-danger text-warning' style="margin-top:-3px;" disabled id="deleteSpecialBonus"> حذف <i class="fa fa-trash fa-lg"></i></button>  -->
                                  </div>
                                </div>
                                <table class="table table-bordered ">
                                  <thead>
                                        <tr>
                                            <th>ردیف </th>
                                            <th>اساس</th>
                                            <th>امتیاز</th>
                                            <th>حد</th>
                                            <th>انتخاب</th>
                                        </tr>
                                  </thead>
                                  <tbody id="generalBonusList4" class="settingTableBody">
                                    @foreach($generalBonuses as $Bonus)
                                    @if($Bonus->userType==4)
                                        <tr onclick="setGeneralBonusStuff(this,{{$Bonus->userType}}); selectTableRow(this);">
                                              <td >{{$loop->iteration}}</td>
                                              <td>{{$Bonus->BaseName}}</td>
                                              <td>{{$Bonus->Bonus}}</td>
                                              <td>{{number_format($Bonus->limitAmount,0,"",",")}}</td>
                                              <td> <input class="form-check-input" name="generalBonusId" type="radio" value="{{$Bonus->id}}"></td>
                                        </tr>
                                    @endif
                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                            </fieldset>
                          </div>
                        </div>
                    </div>
                  </div>

            <!-- else setting  -->
             <div class="c-checkout container elseSettings" id="generalSettings" style="background-image: linear-gradient(to right, #ffffff,#3fa7ef,#3fa7ef); margin:0.2% 0; margin-bottom:0; padding:0.5% !important; border-radius:10px 10px 2px 2px; display:none;">
                    <div class="col-sm-3" style="margin: 0; padding:0;">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                            <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#customerStateItem"> آیتم های وضعیت مشتری </a></li>
                        </ul>
                    </div>
                    <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px; height:68vh">
                    
                    <div >
                      <button data-toggle="modal" data-target="#addCustomerStateModal" class="btn btn-sm btn-primary" >افزودن آیتم جدید </button>
                      <div class="row">
                        <table class="table table-bordered table-striped myDataTable">
                          <thead>
                            <tr>
                              <th> # </th>
                              <th> اسم </th>
                              <th> اولویت </th>
                              <th> رنگ </th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td> 1 </td>
                              <td> نصب </td>
                              <td>  1 </td>
                              <td> قرمز </td>
                            </tr>
                            <tr>
                              <td> 1 </td>
                              <td> پیگیری 1 </td>
                              <td>  2 </td>
                              <td> سبز </td>
                            </tr>
                            <tr>
                              <td> 1 </td>
                              <td> پیگیری 2 </td>
                              <td> 3 </td>
                              <td> زرد </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                   </div>
                   </div>
             
            <div class="row contentFooter">
  
            </div>
            </div>
        </div>
    </div>
</div>
    

<!-- Bazaryab Modal -->
<div class="modal fade" id="addingTargetModal" data-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white py-2">
          <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close" style="color:red"></button>
        <h5 class="modal-title" id="staticBackdropLabel">  افزودن تارگت  </h5>
      </div>
      <form action="{{url('/addTarget')}}" method="GET" id="addTarget">
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label"> اساس تارگت </label>
                <input type="text" class="form-control" placeholder="خرید اولیه"  name="baseName" aria-describedby="emailHelp">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگت 1 </label>
                <input type="text" class="form-control" placeholder="تارگت 1" name="firstTarget" aria-describedby="emailHelp">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگیت 1  </label>
                <input type="text" class="form-control" placeholder="" name="firstTargetBonus" aria-describedby="emailHelp">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگت 2 </label>
                <input type="text" class="form-control" placeholder="تارگت 2" name="secondTarget" aria-describedby="emailHelp">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگت 2   </label>
                <input type="number" class="form-control" placeholder="20" name="secondTargetBonus" aria-describedby="emailHelp">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگیت 3   </label>
                <input type="number" class="form-control" placeholder="23" name="thirdTarget" aria-describedby="emailHelp">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگت 3   </label>
                <input type="number" class="form-control" placeholder="20" name="thirdTargetBonus" aria-describedby="emailHelp">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"> بستن <i class="fa fa-xmark"></i> </button>
          <button type="submit" class="btn btn-sm btn-primary">ذخیره <i class="fa fa-save "></i> </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal" tabindex="-1" id="addSpecialBonusModal" data-backdrop='static'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-white py-2">
        <h5 class="modal-title">افزودن اساس جدید</h5>
        <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{url('/addSpecialBonus')}}" method="get" id="addBonusForm">
        <div class="mb-3">
            <label  class="form-label">اساس</label>
            <input type="text" class="form-control" name="baseName"  placeholder="نصب">
        </div>
        <div class="mb-3">
            <label  class="form-label">حد</label>
            <input type="text" class="form-control" name="limitAmount" id="limitAmountAdd"  placeholder="نصب">
        </div>
        <div class="mb-3">
            <label class="form-label">امتیاز</label>
            <input type="text" class="form-control" name="bonus"  placeholder="نصب">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">بستن</button>
        <button type="submit" class="btn btn-sm btn-primary">ذخیره</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal" tabindex="-1" id="addCustomerStateModal" data-backdrop='static'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-white py-2">
        <h5 class="modal-title">افزودن آیتم وضعیت مشتری</h5>
        <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <div class="row c-checkout rounded-3 tab-pane active" id="customerStateItem">
                          <form action="{{url('/addCustomerState')}}" method="get" id="addCustomerStateForm">
                            <div class="form-group">
                              <label for="" class="form-label">آیتم</label>
                              <input type="text" name="name" class="form-control">
                            </div>
                            <div class="form-group">
                              <label for="" class="form-label"> اولویت </label>
                              <input type="text" name="priority" class="form-control">
                            </div>
                            <div class="form-group">
                              <label for="" class="form-label"> رنگ </label>
                              <input type="color" name="color" value="this.value" class="form-control">
                            </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">بستن</button>
        <button type="submit" class="btn btn-sm btn-primary">ذخیره</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal" tabindex="-1" id="editSpecialBonusModal" data-bs-backdrop='static'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white py-2">
                <button type="button" class="btn-close bg-dnager" data-bs-dismiss="modal" aria-label="Close"></button>
				<h5 class="modal-title">ویرایش اساس</h5>
            </div>
            <form action="{{url('/editSpecialBonus')}}" method="get" id="editBonusForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label  class="form-label">اساس</label>
                        <input type="text" class="form-control" name="baseName" disabled id="specialBaseName"  placeholder="نصب">
                        <input type="hidden" name="baseId" id="specialBaseId">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">حد<span id="limitDiv">(تومن -تعداد)</span></label>
                        <input type="text" class="form-control" name="limitAmount" id="limitAmount"  placeholder="10">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">امتیاز</label>
                        <input type="text" class="form-control" name="bonus" id="specialBonus"  placeholder="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">بستن <i class="fa fa-xmark"> </i></button>
					<button type="submit" class="btn btn-sm btn-primary">ذخیره <i class="fa fa-save"> </i> </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- تنظیم  امتیازات عمومی-->


<div class="modal" tabindex="-1" id="editGeneralBonusModal" data-bs-backdrop='static'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-white py-2">
        <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
        <h5 class="modal-title">ویرایش اساس</h5>
      </div>
      <form action="{{url('/editGeneralBonus')}}" method="get" id="editGeneralBonusForm">
        <div class="modal-body">
        <input type="hidden" class="form-control" name="userType" id="generalUserType"  placeholder="نصب">
          <div class="mb-3">
            <label  class="form-label">اساس</label>
            <input type="text" class="form-control" name="baseName" disabled id="generalBaseName"  placeholder="نصب">
            <input type="hidden" name="baseId" id="generalBaseId">
          </div>

          <div class="mb-3">
            <label class="form-label">حد<span id="limitDiv">(تومن -تعداد)</span></label>
            <input type="text" class="form-control" name="limitAmount" id="generallimitAmount"  placeholder="10">
          </div>

          <div class="mb-3">
            <label class="form-label">امتیاز</label>
            <input type="text" class="form-control" name="bonus" id="generalBonus"  placeholder="1">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">بستن <i class="fa fa-xmark"> </i></button>
          <button type="submit" class="btn btn-sm btn-primary">ذخیره <i class="fa fa-save"> </i> </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ختم تنظیم امتیازات عمومی -->
<!-- تارگت های بخصوص-->
<div class="modal fade" id="editingTargetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white py-2">
          <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" style="color:red"></button>
        <h5 class="modal-title" id="staticBackdropLabel"> ویرایش تارگت </h5>
      </div>
      <form action="{{url('/editTarget')}}" method="GET" id="editTarget">
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label"> اساس تارگت </label>
                <input type="text" class="form-control" disabled placeholder="خرید اولیه"  name="baseName" id="baseName" aria-describedby="emailHelp">
                <input type="hidden" name="targetId" id="targetIdForEdit">
              </div>
            </div>
            <div class="col-lg-3"> 
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگت 1 </label>
                <input type="text" class="form-control" placeholder="تارگت 1" name="firstTarget" id="firstTarget">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگیت 1  </label>
                <input type="text" class="form-control" placeholder="" name="firstTargetBonus" id="firstTargetBonus">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگت 2 </label>
                <input type="text" class="form-control" placeholder="تارگت 2" name="secondTarget" id="secondTarget">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگت 2   </label>
                <input type="text" class="form-control" placeholder="20" name="secondTargetBonus" id="secondTargetBonus">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگیت 3   </label>
                <input type="text" class="form-control" placeholder="23" name="thirdTarget" id="thirdTarget">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگت 3   </label>
                <input type="text" class="form-control" placeholder="20" name="thirdTargetBonus" id="thirdTargetBonus">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"> بستن <i class="fa fa-xmark"></i> </button>
          <button type="submit" class="btn btn-sm btn-primary">ذخیره <i class="fa fa-save "></i> </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- تارگت های عمومی-->
<div class="modal fade" id="editingGeneralTargetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white py-2">
          <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" style="color:red"></button>
        <h5 class="modal-title" id="staticBackdropLabel"> ویرایش تارگت </h5>
      </div>
      <form action="{{url('/editGeneralTarget')}}" method="post" id="editGTarget">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <input type="hidden" id="baseId" name="baseId"> 
                <label for="exampleInputEmail1" class="form-label"> اساس تارگت </label>
                <input type="text" class="form-control" disabled placeholder="خرید اولیه"  name="baseGName" id="baseGName" aria-describedby="emailHelp">
                <input type="hidden" name="userTypeID" id="userTypeID">
              </div>
            </div> 
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگت 1 </label>
                <input type="text" class="form-control" placeholder="تارگت 1" name="firstTarget" id="firstGTarget">
              </div>
            </div> 
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگیت 1  </label>
                <input type="text" class="form-control" placeholder="" name="firstTargetBonus" id="firstGTargetBonus">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگت 2 </label>
                <input type="text" class="form-control" placeholder="تارگت 2" name="secondTarget" id="secondGTarget">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگت 2   </label>
                <input type="text" class="form-control" placeholder="20" name="secondTargetBonus" id="secondGTargetBonus">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگیت 3   </label>
                <input type="text" class="form-control" placeholder="23" name="thirdTarget" id="thirdGTarget">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگت 3   </label>
                <input type="text" class="form-control" placeholder="20" name="thirdTargetBonus" id="thirdGTargetBonus">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"> بستن <i class="fa fa-xmark"></i> </button>
          <button type="submit" class="btn btn-sm btn-primary">ذخیره <i class="fa fa-save "></i> </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection



