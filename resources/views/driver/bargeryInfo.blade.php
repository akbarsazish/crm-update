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
</style>

<div class="container-fluid containerDiv">
    <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                <fieldset class="border rounded mt-5 sidefieldSet">
                    <legend  class="float-none w-auto legendLabel mb-0">  اطلاعات بارگیری  </legend>
                       <div class="quick-access mt-2">
                            <div class="quick-acess-item"> <span> اسم مشتری : </span><span class="quick-access-label text-danger" id="quick_CustomerName"> </span> </div>
                            <div class="quick-acess-item"> <span>  تعداد فاکتور : </span> <span class="quick-access-label" id="quick_countFactor"> </span> </div>
                            <div class="quick-acess-item"> <span>  آخرین مبلغ خرید : </span> <span class="quick-access-label" id="quick_lastBuyMoney"> </span> </div>
                            <div class="quick-acess-item"> <span>  جمع مبلغ خرید : </span> <span class="quick-access-label" id="quick_BuyAllMoney"> </span> </div>
                            <div class="quick-acess-item"> <span>  وضعیت سبد : </span> <span class="quick-access-label" id="quick_basketState"> </span> </div>
                            <div class="quick-acess-item"> <span>  آخرین خرید : </span> <span class="quick-access-label" id="quick_lastFactDate"> </span></div>
                            <div class="quick-acess-item"> <span>  آخرین ورود : </span> <span class="quick-access-label" id="quick_lastLoginDate"> </span></div>
                            <div class="quick-acess-item"> <span>  آدرس  : </span> <span class="quick-access-label" id="quick_address"> </span></div>
                            <div class="quick-acess-item"> <span>  شماره تماس  : </span> <span class="quick-access-label" id="quick_Phone"> </span> </div>
                        </div>
                    
                </fieldset>
                </div>
            <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                <div class="row contentHeader"> </div>
                <div class="row mainContent">
                     <table class="select-highlight table table-bordered table-striped" id="">
                            <thead class="tableHeader">
                                <tr>
                                    <th>ردیف</th>
                                    <th>نام کاربر</th>
                                    <th>شماره تماس</th>
                                    <th>فعال</th>
                                </tr>
                            </thead>
                            <tbody class="tableBody" id="" style="height:222px !important;"> 
                                @foreach ($admins as $admin)
                                        <tr onclick="showBargiriFactors(this,{{$admin->driverId}}); selectTableRow(this);">
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{trim($admin->name)." ".trim($admin->lastName)}}</td>
                                            <td>{{trim($admin->phone)}}</td>
                                            <td>
                                                <input class="mainGroupId" type="radio" name="AdminId[]" value="{{$admin->id}}">
                                            </td>
                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                    
                        <table class="table table-bordered" id="tableGroupList">
                            <thead class="bg-primary text-warning tableHeader">
                                <tr>
                                    <th class="mobileDisplay">#</th>
                                    <th>نام مشتری</th>
                                    <th class="mobileDisplay"> آدرس </th>
                                    <th>تلفن </th>
                                    <th style="width:111px">فاکتور</th>
                                    <th> انتخاب</th>
                                </tr>
                            </thead>
                            <tbody class="tableBody" id="crmDriverBargeri">
                            </tbody>
                        </table>
                </div>
                <div class="row contentFooter"> </div>
            </div>
    </div>
</div>


<!-- modal for demonestrating factor deatails -->
        <div class="modal fade" id="bargiriFactor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close"></button>
                        <h5 class="modal-title" id="exampleModalLabel">فاکتور فروش <span  id="totalMoney"> </span> </h5>
                    </div>
                        <div class="modal-body">
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
                                <table id="strCusDataTable" class='table table-bordered table-striped table-sm' style="background-color:#dee2e6">
                                    <thead class="bg-primary tableHeader">
                                        <tr>
                                            <th class="driveFactor">#</th>
                                            <th>نام کالا </th>
                                            <th class="driveFactor">تعداد/مقدار</th>
                                            <th>واحد کالا</th>
                                            <th>فی (تومان)</th>
                                            <th >مبلغ (تومان)</th>
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
    </div>
</section>
@endsection
