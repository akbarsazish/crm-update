
@extends('layout')
@section('content')

 <div class="container-fluid containerDiv">
        <div class="spinner-border text-danger" role="status" id="transferLoader" style="display:none;">
            <span class="sr-only">Loading...</span>
        </div>
      <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                   <fieldset class="border rounded sidefieldSet">
                        <legend  class="float-none w-auto legendLabel mb-0">  تخصیص به   {{Session::get('adminName')}} </legend>
                       <form action="{{url('/getTakhsisEditRightSide')}}" id="takhsisEditRightSideForm" method="get">
                            <div class="form-group mb-1 col-sm-12">
                                <div class="input-group input-group-sm mt-1">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">شهر</span>
                                    <input type="text" style="display:none" id="asn"/>
                                    <select  class="form-select form-select-sm" name="searchCity" id="searchCity">
                                        <option value="">همه</option>
                                        @foreach($cities as $city)
                                            <option value="{{$city->SnMNM}}">{{trim($city->NameRec)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-1 col-sm-12">
                            <div class="input-group input-group-sm mt-1">
                                <span class="input-group-text" id="inputGroup-sizing-sm">منطقه</span>
                                <select class="form-select form-select-sm" name="searchMantagheh" id="searchMantagheh">
                                    <option value="">همه</option>
                                </select>
                            </div>
                            </div>
                            <div class="form-group mb-2 col-sm-12">
                                <div class="input-group input-group-sm mt-1">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">نوعیت مشتری</span>
                                    <select class="form-select form-select-sm" name="buyNotBuyOrNew" id="">
                                        <option value="n">همه</option>
                                        <option value="1">خرید کرده </option>
                                        <option value="0"> خرید نکرده </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 mb-1">
                                <div class="input-group input-group-sm mt-1">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">اسم مشتری</span>
                                    <input type="text" class="form-control form-control-sm" name="name" id="">
                                </div>
                            </div>
                            <div class="form-group col-sm-12 mb-1">
                                <div class="input-group input-group-sm mt-1">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">از تاریخ خرید</span>
                                    <input type="text"  class="form-control form-control-sm" name="firstDateBuy" id="assesFirstDate">
                                </div>
                            </div>
                            <div class="form-group col-sm-12 mb-2">
                                <div class="input-group input-group-sm mt-1">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">تا تاریخ خرید</span>
                                    <input type="text"  class="form-control form-control-sm" name="secondDateBuy" id="assesSecondDate">
                                </div>
                            </div>
                            <div class="form-group col-sm-12 mb-1">
                                <div class="input-group input-group-sm mt-1">
                                    <span class="input-group-text" id="inputGroup-sizing-sm"> از تاریخ ثبت </span>
                                    <input type="text" class="form-control form-control-sm" name="firstDateSabt" id="firstDateDoneComment">
                                </div>
                            </div>
                            <div class="form-group col-sm-12 mb-2">
                                <div class="input-group input-group-sm mt-1">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">تا تاریخ ثبت</span>
                                    <input type="text"  class="form-control form-control-sm" name="secondDateSabt" id="secondDateDoneComment">
                                </div>
                            </div>
                            <button class='btn btn-primary btn-sm text-warning' type="submit" id='getAssesBtn'> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                        </form>
                        <div class="quick-access">
                            <div class="quick-acess-item"> <span> اسم مشتری : </span><span class="quick-access-label text-danger" id="quick_CustomerName"> </span> </div>
                            <div class="quick-acess-item"> <span>  تعداد فاکتور : </span> <span class="quick-access-label" id="quick_countFactor"> </span> </div>
                            <div class="quick-acess-item"> <span>  آخرین مبلغ خرید : </span> <span class="quick-access-label" id="quick_lastBuyMoney"> </span> </div>
                            <div class="quick-acess-item"> <span>  وضعیت سبد : </span> <span class="quick-access-label" id="quick_basketState"> </span> </div>
                            <div class="quick-acess-item"> <span>  جمع مبلغ خرید : </span> <span class="quick-access-label" id="quick_BuyAllMoney"> </span> </div>
                            <div class="quick-acess-item"> <span>  آخرین خرید : </span> <span class="quick-access-label" id="quick_lastFactDate"> </span></div>
                            <div class="quick-acess-item"> <span>  آخرین ورود : </span> <span class="quick-access-label" id="quick_lastLoginDate"> </span></div>
                            <div class="quick-acess-item"> <span>  آدرس  : </span> <span class="quick-access-label" id="quick_address"> </span></div>
                            <div class="quick-acess-item"> <span>  شماره تماس  : </span> <span class="quick-access-label" id="quick_Phone"> </span> </div>
                        </div>
                    </fieldset>
                  </div>
                <div class="col-sm-8 col-md-8 col-sm-12 contentDiv p-0 m-0">
                    <div class="row contentHeader">
                        <div class="col-lg-9 mt-3">
                            <div class="form-group mt-2 col-sm-2"></div>
                        </div>
                           <div class="col-lg-3 text-start"> </div>
                    </div>
                    <div class="row mainContent ">
                          
                          <div class="table-container" id="customerContainer">
                                <div class="table-item pt-0 mt-0">
                                      <input type="hidden" id="AdminForAdd" style="display: none" value="{{$adminId}}">
                                        <table class="table table-bordered table-striped table-hover" id="allCustomers" style="border-left:1px solid #cbcbcb;">
                                            <thead class="tableHeader">
                                                <tr>
                                                    <th>ردیف</th>
                                                    <th> منطقه  </th>
                                                    <th> نام  </th>
                                                    <th> <input type="checkbox" class="selectAllFromTop form-check-input" id="selectAllTopRight"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="allCustomer" class="tableBody" style="height:633px !important;">
                                            @foreach($customers as $customer)
                                               <tr onclick="checkCheckBox(this,event); getCustomerInformation({{$customer->PSN}}); selectTableRow(this);">
                                                    <td style="">{{$loop->iteration}}</td>
                                                    <td style="">{{$customer->NameRec}}</td>
                                                    <td>{{$customer->Name}}</td>
                                                    <td> <input type="checkbox" name="customerIDs[]" value="{{$customer->PSN}}" class="form-check-input"></td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                    </table>
                                </div>
                                <div class="table-item" style="margin-top:122px;">
                                   <a id="addCustomerToAdmin"> <i class="fa-regular fa-circle-chevron-left fa-2x"></i></a>
                                    <br />
                                     <a id="removeCustomerFromAdmin"> <i class="fa-regular fa-circle-chevron-right fa-2x"></i></a>
                                </div>

                                <div class="table-item pt-0 mt-0">
                                        <table class="table table-bordered table-striped table-hover"  id="addedCustomers" style="100%">
                                        <thead class="tableHeader">
                                                <tr>
                                                <th>ردیف</th>
                                                <th> منطقه  </th>
                                                <th> نام </th>
                                                <th> <input type="checkbox" class="selectAllFromTop form-check-input" id="selectAllTopLeft"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="addedCustomer" class="tableBody" style="border-right:1px solid #cbcbcb; height:633px !important;">
                                        @foreach($addedCustomers as $customer)
                                        <tr onclick="checkCheckBox(this,event); selectTableRow(this);">
                                                    <td style="">{{$loop->iteration}}</td>
                                                    <td style="">{{$customer->NameRec}}</td>
                                                    <td>{{$customer->Name}}</td>
                                                    <td> <input type="checkbox" name="addedCustomerIDs[]" value="{{$customer->PSN}}" class="form-check-input"></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>   
                        </div>
                     </div>
                    <div class="row contentFooter" id="footerInfo">
                     
                   </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                 <fieldset class="border rounded sidefieldSet">
                        <legend  class="float-none w-auto legendLabel mb-0"> جستجو  </legend>
                        <form action="{{url('/getAddedCustomers')}}" id="addedCustomerLeftSideForm" method="get">
                            <input type="hidden" name="adminId" value="{{$adminId}}" id="">
                            <div class="form-group mb-1 col-sm-12">
                            <select  class="form-select form-select-sm" id="searchAddedCity">
                            <option value="0" hidden>شهر</option>
                            <option value="1" >همه</option>
                                @foreach($cities as $city)
                                <option value="{{$city->SnMNM}}">{{trim($city->NameRec)}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-1 col-sm-12">
                            <select class="form-select form-select-sm" name="searchMantagheh" id="searchAddedMantagheh">
                                <option value="" hidden>منطقه</option>
                                <option value="0">همه</option>
                            </select>
                        </div>
                        <div class="form-group mb-2 col-sm-12">
                            <select class="form-select form-select-sm" name="buyNotBuyOrNew" id="">
                                <option value="n">نوعیت مشتری</option>
                                <option value="1">خرید کرده </option>
                                <option value="0"> خرید نکرده </option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12 mb-1">
                            <input type="text" placeholder="  از تاریخ خرید" class="form-control form-control-sm" name="firstDateBuy" id="addCustomerFirstDate">
                        </div>
                        <div class="form-group col-sm-12 mb-2">
                            <input type="text" placeholder="  تا تاریخ خرید" class="form-control form-control-sm" name="secondDateBuy" id="addCustomerSecondDate">
                        </div>
                        <div class="form-group col-sm-12 mb-1">
                            <input type="text" placeholder="  از تاریخ ثبت" class="form-control form-control-sm" name="firstDateSabt" id="addCustomerFristSabtDate">
                        </div>
                        <div class="form-group col-sm-12 mb-2">
                            <input type="text" placeholder="  تا تاریخ ثبت" class="form-control form-control-sm" name="secondDateSabt" id="addCustomerSecondSabtDate">
                        </div>
                        <button class='btn btn-primary btn-sm text-warning' type="submit" id='getAssesBtn'> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                    </form>
                          <div class="form-group mb-1 col-sm-12">
                            <label class="form-label"> توضیحات</label>
                            <textarea class="form-control" cols="10" rows="4" name="discription" style="background-color:blanchedalmond" id="adminDiscription">{{$admins->discription}}</textarea>
                          </div>
                    </fieldset>
               </div>
        </div>
    </div>

    

             <!-- modal for removing user profile -->
             <div class="modal fade dragableModal" id="moveKarbar" role="dialog"   data-backdrop="static" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable  modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="margin:0; border:none">
                            <button type="button" class="btn-close btn-danger" style="background-color:red;" data-dismiss="modal" aria-label="Close"></button>
                            <h5 class="modal-title" id="exampleModalLongTitle"> انتقال مشتریان از کاربر به کاربر  </h5>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered">
                                <thead class="text-warning tableHeader">
                                    <tr>
                                        <th>نام کاربر </th>
                                        <th>نقش کاربر </th>
                                        <th>توضیحات</th>
                                  </tr>
                                </thead>
                                <tbody id="adminToMove" class="tableBody">

                                </tbody>
                            </table>
                                <input type="hidden" id="adminID" >
                                <input type="text" id="adminTakerId">
                            <table class="table table-bordered">
                                <thead class="text-warning tableHeader">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>نام کاربر </th>
                                        <th>نقش کاربر </th>
                                        <th>توضیحات</th>
                                        <th>انتخاب </th>
                                  </tr>
                                </thead>
                                <tbody id="selectKarbarToMove" class="tableBody">

                                </tbody>
                            </table>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelMoveKarbar"> انصراف <i class="fa-solid fa-xmark"> </i> </button>
                            <button type="button" onclick="moveStaff()"  class="bt btn-danger btn-lg"> انتقال <i class="fa fa-sync"></i> </button>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
