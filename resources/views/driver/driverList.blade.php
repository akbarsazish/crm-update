@extends('layout')
@section('content')

<style>
    label{
        font-size: 14px;
        font-weight: bold;
    }
    .list-group-item{
        font-size:14px;
    }
        .grid-container {
            display: grid;
            grid-template-columns: auto auto;
            gap: 5px;
            padding: 5px;
            background-color:#f1efef;
            }

        .grid-container > div {
            text-align: center;
            font-size: 14px;
            font-weight:bold;
            text-align:right;
            padding:3px;
            }

   @media only screen and (max-width: 992px) {
    .driverTable .address,.choice {
        display:none;
    }
   }
   @media only screen and (max-width: 678px) and (min-width:278px) {
    .driveFactor{
        display: none !important;
    }
  }

  tbody, td, tfoot, th, thead, tr {
    font-size:12px;
  }

  .checkbox-wrapper-44 input[type="checkbox"] {
    display: none;
    visibility: hidden;
  }

  .checkbox-wrapper-44 *,
  .checkbox-wrapper-44 *::before,
  .checkbox-wrapper-44 *::after {
    box-sizing: border-box;
  }

  .checkbox-wrapper-44 .toggleButton {
    cursor: pointer;
    display: block;
    transform-origin: 50% 50%;
    transform-style: preserve-3d;
    transition: transform 0.14s ease;
  }
  .checkbox-wrapper-44 .toggleButton:active {
    transform: rotateX(30deg);
  }
  .checkbox-wrapper-44 .toggleButton input + div {
    border: 3px solid rgba(0, 0, 0, 0.2);
    border-radius: 50%;
    position: relative;
    width: 40px;
    height: 40px;
  }
  .checkbox-wrapper-44 .toggleButton input + div svg {
    fill: none;
    stroke-width: 3.6;
    stroke: #000;
    stroke-linecap: round;
    stroke-linejoin: round;
    width: 40px;
    height: 40px;
    display: block;
    position: absolute;
    left: -3px;
    top: -3px;
    right: -3px;
    bottom: -3px;
    z-index: 1;
    stroke-dashoffset: 124.6;
    stroke-dasharray: 0 162.6 133 29.6;
    transition: all 0.4s ease 0s;
  }
  .checkbox-wrapper-44 .toggleButton input + div:before,
  .checkbox-wrapper-44 .toggleButton input + div:after {
    content: "";
    width: 3px;
    height: 16px;
    background: #000;
    position: absolute;
    left: 50%;
    top: 50%;
    border-radius: 5px;
  }
  .checkbox-wrapper-44 .toggleButton input + div:before {
    opacity: 0;
    transform: scale(0.3) translate(-50%, -50%) rotate(45deg);
    -webkit-animation: bounceInBefore-44 0.3s linear forwards 0.3s;
            animation: bounceInBefore-44 0.3s linear forwards 0.3s;
  }
  .checkbox-wrapper-44 .toggleButton input + div:after {
    opacity: 0;
    transform: scale(0.3) translate(-50%, -50%) rotate(-45deg);
    -webkit-animation: bounceInAfter-44 0.3s linear forwards 0.3s;
            animation: bounceInAfter-44 0.3s linear forwards 0.3s;
  }
  .checkbox-wrapper-44 .toggleButton input:checked + div svg {
    stroke-dashoffset: 162.6;
    stroke-dasharray: 0 162.6 28 134.6;
    transition: all 0.4s ease 0.2s;
  }
  .checkbox-wrapper-44 .toggleButton input:checked + div:before {
    opacity: 0;
    transform: scale(0.3) translate(-50%, -50%) rotate(45deg);
    -webkit-animation: bounceInBeforeDont-44 0.3s linear forwards 0s;
            animation: bounceInBeforeDont-44 0.3s linear forwards 0s;
  }
  .checkbox-wrapper-44 .toggleButton input:checked + div:after {
    opacity: 0;
    transform: scale(0.3) translate(-50%, -50%) rotate(-45deg);
    -webkit-animation: bounceInAfterDont-44 0.3s linear forwards 0s;
            animation: bounceInAfterDont-44 0.3s linear forwards 0s;
  }

  @-webkit-keyframes bounceInBefore-44 {
    0% {
      opacity: 0;
      transform: scale(0.3) translate(-50%, -50%) rotate(45deg);
    }
    50% {
      opacity: 0.9;
      transform: scale(1.1) translate(-50%, -50%) rotate(45deg);
    }
    80% {
      opacity: 1;
      transform: scale(0.89) translate(-50%, -50%) rotate(45deg);
    }
    100% {
      opacity: 1;
      transform: scale(1) translate(-50%, -50%) rotate(45deg);
    }
  }

  @keyframes bounceInBefore-44 {
    0% {
      opacity: 0;
      transform: scale(0.3) translate(-50%, -50%) rotate(45deg);
    }
    50% {
      opacity: 0.9;
      transform: scale(1.1) translate(-50%, -50%) rotate(45deg);
    }
    80% {
      opacity: 1;
      transform: scale(0.89) translate(-50%, -50%) rotate(45deg);
    }
    100% {
      opacity: 1;
      transform: scale(1) translate(-50%, -50%) rotate(45deg);
    }
  }
  @-webkit-keyframes bounceInAfter-44 {
    0% {
      opacity: 0;
      transform: scale(0.3) translate(-50%, -50%) rotate(-45deg);
    }
    50% {
      opacity: 0.9;
      transform: scale(1.1) translate(-50%, -50%) rotate(-45deg);
    }
    80% {
      opacity: 1;
      transform: scale(0.89) translate(-50%, -50%) rotate(-45deg);
    }
    100% {
      opacity: 1;
      transform: scale(1) translate(-50%, -50%) rotate(-45deg);
    }
  }
  @keyframes bounceInAfter-44 {
    0% {
      opacity: 0;
      transform: scale(0.3) translate(-50%, -50%) rotate(-45deg);
    }
    50% {
      opacity: 0.9;
      transform: scale(1.1) translate(-50%, -50%) rotate(-45deg);
    }
    80% {
      opacity: 1;
      transform: scale(0.89) translate(-50%, -50%) rotate(-45deg);
    }
    100% {
      opacity: 1;
      transform: scale(1) translate(-50%, -50%) rotate(-45deg);
    }
  }
  @-webkit-keyframes bounceInBeforeDont-44 {
    0% {
      opacity: 1;
      transform: scale(1) translate(-50%, -50%) rotate(45deg);
    }
    100% {
      opacity: 0;
      transform: scale(0.3) translate(-50%, -50%) rotate(45deg);
    }
  }
  @keyframes bounceInBeforeDont-44 {
    0% {
      opacity: 1;
      transform: scale(1) translate(-50%, -50%) rotate(45deg);
    }
    100% {
      opacity: 0;
      transform: scale(0.3) translate(-50%, -50%) rotate(45deg);
    }
  }
  @-webkit-keyframes bounceInAfterDont-44 {
    0% {
      opacity: 1;
      transform: scale(1) translate(-50%, -50%) rotate(-45deg);
    }
    100% {
      opacity: 0;
      transform: scale(0.3) translate(-50%, -50%) rotate(-45deg);
    }
  }
  @keyframes bounceInAfterDont-44 {
    0% {
      opacity: 1;
      transform: scale(1) translate(-50%, -50%) rotate(-45deg);
    }
    100% {
      opacity: 0;
      transform: scale(0.3) translate(-50%, -50%) rotate(-45deg);
    }
  }
</style>


 <div class="container-fluid containerDiv">
      <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                   <fieldset class="border rounded mt-5 sidefieldSet">
                        <legend  class="float-none w-auto legendLabel mb-0"> {{Session::get('username')}} </legend>
                        @if(hasPermission(Session::get("asn"),"justBargiriOppN") > 0)
                        <form action="{{url('/searchBargeriByDate')}}" method="get" id="searchBargiriSelfForm">
                                <span class="fromDate">
                                        <div class="form-group fromDateTodate">
                                            <input type="text" name="firstDate" class="form-control form-control-sm" id="bargeriFirstDate" placeholder=" از تاریخ " />
                                        </div>
                                        <div class="form-group fromDateTodate">
                                            <input type="text" name="secondDate" class="form-control form-control-sm" id="bargeriSecondDate" placeholder=" تا تاریخ " />
                                        </div>
                                 </span>
                                <input type="hidden" id="adminId" name="adminId" value="{{$adminId}}">
                                <span class="fromDate">
                                    <div class="form-group mt-2">
                                        <input class="form-control form-control-sm" name="customerName" type="text" placeholder="اسم مشتری">
                                    </div>
                                    <div class="form-group">
                                        <button class='btn btn-primary btn-sm text-warning' type="submit" id='getBargiriSearchBtn'> بازخوانی <i class="fal fa-dashboard fa-lg"></i></button>
                                    </div>
                                </span>
                        </form>
                        @endif
                    </fieldset>
                </div>
                <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                    <div class="row contentHeader"> 

                    </div>
                    <div class="row mainContent">
                        <div class="col-lg-12 px-0">
                            <table class="table table-bordered myDataTable driverTable" id="tableGroupList">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>نام مشتری</th>
                                        <th class="address"> آدرس </th>
                                        <th>تلفن </th>
                                        <th> <i class="fas fa-map-marker-alt fa-1xl" style="color:#fff"></i>  </th>
                                        <th>فاکتور</th>
                                        <th> تحویل </th>
                                    </tr>
                                </thead>

                                <tbody class="c-checkout tableBody" id="crmDriverBargeri">
                                    @foreach ($factors as $factor)
                                        <tr onclick="setBargiryStuff(this,{{$factor->PSN}})" id="givfactors" @if($factor->isGeven==1) class="selected" @endif>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ Str::limit($factor->Name, 22) }}</td>
                                            <td class="address">{{$factor->peopeladdress}}</td>
                                            <td><a style="color:black; font-size:12px;" href="tel:+900300400"> {{$factor->PhoneStr}} </a> </td> 
                                            <td style="text-align: center;"><a style="text-decoration:none;" target="_blank" href="geo:{{$factor->LonPers.','.$factor->LatPers}}"><i class="fas fa-map-marker-alt fa-1xl" style="color:#116bc7;"></i></a></td>
                                            <td style="text-align: center; cursor:pointer;" data-toggle="modal" data-target="#factorDeatials"><i class="fa fa-eye fa-1xl"> </i> </td>
                                            <td> 
                                            <div class="checkbox-wrapper-44">
                                                <label class="toggleButton">
                                                    <input type="checkbox" @if($factor->isGeven==1) checked class="selected" @endif onchange="givFactor(this,{{$factor->SerialNoHDS}})">
                                                    <div>
                                                    <svg viewBox="0 0 44 44">
                                                        <path d="M14,24 L21,31 L39.7428882,11.5937758 C35.2809627,6.53125861 30.0333333,4 24,4 C12.95,4 4,12.95 4,24 C4,35.05 12.95,44 24,44 C35.05,44 44,35.05 44,24 C44,19.3 42.5809627,15.1645919 39.7428882,11.5937758" transform="translate(-2.000000, -2.000000)"></path>
                                                    </svg>
                                                    </div>
                                                </label>
                                                </div>
                                                 
                                            </td>
                                            <td  class="choice" style="display:none;"> <input class="customerList form-check-input" name="factorId" type="radio" value="{{$factor->SnBargiryBYS.'_'.$factor->SerialNoHDS.'_'.$factor->TotalPriceHDS}}"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row contentFooter"> </div>
                </div>
         </div>
    </div>


<!-- Modal -->
<div class="modal fade" id="driverLocation" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color:red"></button>
            <h5 class="modal-title" id="exampleModalLabel"> موقعیت راننده </h5>
        </div>
            <div class="modal-body">
                 <div class="container-fluid m-0 p-0">
                     <div id="map2" class="z-depth-1-half map-container-4" style="height: 500px"></div>
                 </div>
            </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">بستن </button>
        </div>
    </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="factorDeatials" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close" style="background-color:red"></button>
            <h5 class="modal-title" id="exampleModalLabel" >فاکتور فروش <span  id="totalMoney"> </span> </h5>
        </div>
            <div class="modal-body">
                <div class="row"> 
                    <div class="col-lg-6">
                        <ul class="list-group px-0 card">
                            <li class="list-group-item">  <b>مشتری  :  </b><span id="customerNameFactor">  </span> </li>
                            <li class="list-group-item"> <b> آدرس  :  </b> <span id="customerAddressFactor"> </span>    </li>
                            <li class="list-group-item">  <b>تلفن :  </b> <span id="customerPhoneFactor"> </span> &nbsp; &nbsp; <b>تاریخ  :</b>  <span id="factorDate"> </span> </li>
                          </ul>
                      </div>
                      <div class="col-lg-6"> 
                        <div class="grid-container card">
                                <div class="item1"><span> مبلغ کارت:</span>    <span id="cartPrice1"> </span></div>
                                <div class="item1"><span> واریز:  </span>   <span id="varizPrice1"> </span></div>
                                <div class="item2"><span> مبلغ نقد :</span>  <span id="naghdPrice1">  </span></div>
                                <div class="item3 text-danger"> <span> تخفیف :  </span>   <span id="takhfifPrice1"> </span></div>  
                                <div class="item5"><span> باقی :  </span>   <span  id="diffPrice1"> </span> </div>
                                <div class="item6"><span> توضیح:  </span>   <span  id="description1">  </span></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table id="strCusDataTable"  class='css-serial display table table-bordered table-striped table-sm' style="background-color:#dee2e6">
                        <thead class="bg-primary">
                            <tr onclick="selectTableRow(this);">
                                <th class="driveFactor">#</th>
                                <th>نام کالا </th>
                                <th >تعداد/مقدار</th>
                                <th class="driveFactor">واحد کالا</th>
                                <th>فی (تومان)</th>
                                <th>مبلغ (تومان)</th>
                            </tr>
                        </thead>
                        <tbody id="productList">
                        </tbody>
                    </table>
                </div>
            </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" id="openReciveMoneyModal">  دریافت  <i class="fa fa-plus"> </i> </button>
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" id="changeAddressOnMap"> دریافت لوکیشن <i class="fa fa-edit"> </i> </button>
            <input type="hidden" id="bargiriyBYSId"/>
        </div>
    </div>
    </div>
</div>


<!-- modal for adding documents  -->
<div class="modal fade" id="addingDocuments" data-bs-backdrop='static' tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-header py-2" style="background: linear-gradient(#4e6aa9, #4e6aa9, #4e6aa9);">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color:red"></button>
            <h5 class="modal-title" id="exampleModalLabel"> افزودن دریافت </h5>
        </div>
            <div class="modal-body">
                 <form method="GET" action="{{url('/setReciveMoneyDetail')}}" id="setReciveMonyDetails">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="mb-1">
                                <label  class="form-label">  وجه نقدی (تومان)</label>
                                <input  type="text" class="receivedInput form-control form-control-sm" id="naghdHisab" name="naghdPrice" >
                                <input type="hidden" id="bargiryFactorId" name="bargiriId"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="mb-1">
                                <label  class="form-label"> کارتخوان  (تومان) </label>
                                <input type="text" class="receivedInput form-control form-control-sm received" id="cartHisab" name="cardPrice">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="mb-1">
                                <label  class="form-label"> واریز به حساب  </label>
                                <input type="text" disabled class="receivedInput form-control form-control-sm received" id="varizHisab" name="varizPrice" >
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="mb-1">
                                <label  class="form-label"> تخفیف (تومان)</label>
                                <input type="text" class="receivedInput form-control form-control-sm received" id="discountHisab" name="takhfifPrice">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="mb-1">
                                <label  class="form-label"> باقی (تومان)</label>
                                <input type="text" class="receivedInput form-control form-control-sm" disabled id="remainHisab" name="diffPrice" >
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="mb-1">
                                <label  class="form-label">توضیح</label>
                                <textarea name="description"  class="form-control form-control-sm"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">بستن <i class="fa fa-xmark"> </i> </button>
                <button type="submit" class="btn btn-sm btn-primary" disabled id="receivedBtn">ذخیره <i class="fa fa-save"> </i> </button>
            </div>
        </form>
    </div>
    </div>
</div>

<!-- modal for changing addesss  -->
<div class="modal fade" id="changeAddressModal" data-bs-backdrop='static' tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-header" style="background: linear-gradient(#4e6aa9, #4e6aa9, #4e6aa9);">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color:red"></button>
            <h5 class="modal-title" id="exampleModalLabel">تغییر آدرس</h5>
        </div>
            <div class="modal-body">
                 <div id="changeAdd"  style="width:100%; height:320px;"></div>
            </div>
            <div class="modal-footer">
                <input type="text" id="currentLocationInput" class="form-control d-none">
                <input type="text" id="customerIdForLoc" class="form-control d-none">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">بستن <i class="fa fa-xmark"> </i> </button>
                <button type="button" disabled id="registerLocByDriverBtn" class="btn btn-primary">ثبت موقعیت <i class="fa fa-save"> </i> </button>
            </div>
      </div>
    </div>
</div>

<script>

$('#varizHisab').on("keyup", ()=>{
    $('#receivedBtn').prop("disabled", false);

    if(!$("#varizHisab").val()){
        $("#varizHisab").val(0);
    }
    $('#varizHisab').val(parseInt($('#varizHisab').val().replace(/\,/g,'')).toLocaleString("en-US"));
});


$('#naghdHisab').on("keyup", ()=>{
    $('#receivedBtn').prop("disabled", false);

    if(!$("#naghdHisab").val()){
        $("#naghdHisab").val(0);
    }
    $('#naghdHisab').val(parseInt($('#naghdHisab').val().replace(/\,/g,'')).toLocaleString("en-US"));
});


$('#cartHisab').on("keyup", ()=>{
    $('#receivedBtn').prop("disabled", false);

    if(!$("#cartHisab").val()){
        $("#cartHisab").val(0);
    }
    $('#cartHisab').val(parseInt($('#cartHisab').val().replace(/\,/g,'')).toLocaleString("en-US"));
});


$('#remainHisab').on("keyup", ()=>{
    $('#receivedBtn').prop("disabled", false);

    if(!$("#remainHisab").val()){
        $("#remainHisab").val(0);
    }
    $('#remainHisab').val(parseInt($('#remainHisab').val().replace(/\,/g,'')).toLocaleString("en-US"));
});

$('#discountHisab').on("keyup", ()=>{
    $('#receivedBtn').prop("disabled", false);

    if(!$("#discountHisab").val()){
        $("#discountHisab").val(0);
    }
    $('#discountHisab').val(parseInt($('#discountHisab').val().replace(/\,/g,'')).toLocaleString("en-US"));
});


$(".receivedInput").on("keyup",function(){
        let variz=0;
        let cart=0;
        let discount=0;
        let naghd=0;
        let allPrice=parseInt($("#totalMoney").text());
        let totalPardakht=0;
        variz=parseInt($('#varizHisab').val().replace(/\,/g,''));
        cart=parseInt($('#cartHisab').val().replace(/\,/g,''));
        discount=parseInt($('#discountHisab').val().replace(/\,/g,''));
        naghd=parseInt($('#naghdHisab').val().replace(/\,/g,''));
        
        if(!variz){
            variz=0;
        }

        if(!cart){
            cart=0;
        }

        if(!discount){
            discount=0;
        }

        if(!naghd){
            naghd=0;
        }
        

        totalPardakht=cart+discount+naghd;

        if(totalPardakht > allPrice){
            $("#remainHisab").css({"background":"red","color":"white"});
        }

        if(totalPardakht == allPrice){
            $("#remainHisab").css({"background":"green","color":"white"});
         
        }
        if(totalPardakht < allPrice){
            $("#remainHisab").css({"background":"white","color":"black"});
        }
        $("#remainHisab").val(parseInt((allPrice)-(totalPardakht)).toLocaleString("en-US")
        );

 });








// $(document).ready(function() {
//     $("#changeAddressOnMap").on("click", ()=>{
//         var changeaddress = new Mapp({
//           element: '#changeAdd',
//           presets: { latlng: {
//                   lat: 31, lng: 52,
//               },
//                 zoom:5,
//           },
//           apiKey: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjMxNDMxNWIwNDI4YWQzNjQ4NzM2NDQ3OTJhNzRmYWY0MWM1M2VlNzhjYmMxNjQwNDYzNTNhMjU4MmNhNzU2MmNjMDkwMWU5ZWUwZWFkNjc4In0.eyJhdWQiOiIxOTk5NyIsImp0aSI6IjMxNDMxNWIwNDI4YWQzNjQ4NzM2NDQ3OTJhNzRmYWY0MWM1M2VlNzhjYmMxNjQwNDYzNTNhMjU4MmNhNzU2MmNjMDkwMWU5ZWUwZWFkNjc4IiwiaWF0IjoxNjY4NDMwNDg3LCJuYmYiOjE2Njg0MzA0ODcsImV4cCI6MTY3MDkzNjA4Nywic3ViIjoiIiwic2NvcGVzIjpbImJhc2ljIl19.bBvecfskKy0m6abjEKbRt_JZeu2hVyCjd7N8vxfv439efdtxJ6-c4UglKkLyjeOgnIhn_JPKuSYD5tQbEm5bI-TCD1sSpFosnz-eKeufsKY7AOtWTYXhQSZ2n6nRKQU6ltRyZQurWlP0lyeNZBYgVbgJFs1V1WVRErD3A8Kr5bztZESFdNI86KbQs6_I3BwwOA9GkXc-RyXU8dwxKj9uG4c7_w1E23e2jQOie4QfuFEdvqxRFoV5YFwUr_49HvdN7DoMC26Pj6QIPtv6h7Luwlmvn8vG8iiawreYtv0-EJUxxwVulkZMaU8YBa5_VXg5gvGWzTYtKcf3iBtIfivGBw',

//       });

//       changeaddress.addLayers();
//       const southWest = L.latLng(35.564629176277855, 51.265826416015625),
//         northEast = L.latLng(35.81335872633348, 51.73187255859375),
//         bounds = L.latLngBounds(southWest, northEast);

//        //* Restrict to current bound
//       // app.map.setMaxBounds(app.map.getBounds());

//       // Restrict to other bounds
//        changeaddress.map.setMaxBounds(bounds);

//       changeaddress.map.on('click', function(e) {
//       // آدرس یابی و نمایش نتیجه در یک باکس مشخص
//        changeaddress.showReverseGeocode({
//             state: {
//                 latlng: {
//                 lat: e.latlng.lat,
//                 lng: e.latlng.lng
//                 },
//                 zoom: 16
//               }
//             });

//             changeaddress.addMarker({
//                 name: 'advanced-marker',
//                 latlng: {
//                     lat: e.latlng.lat,
//                     lng: e.latlng.lng
//                 },
//                 icon:changeaddress.icons.blue,
//                 popup: false

//                 });
//             });
   
//              changeaddress.addFullscreen();
//         $("#changeAddressModal").modal("show");
//     });
// });


</script>

@endsection



