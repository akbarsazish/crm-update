@extends('layout')
@section('content')
<style>
    #notLogin {
        display:none;
    }

</style>
    <div class="container-fluid containerDiv">
      <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 sideBar rAdndDsidebar">
                   <fieldset class="border rounded mt-5 sidefieldSet">
                        <legend  class="float-none w-auto legendLabel mb-0"> تنظیمات </legend>
                         @if(hasPermission(Session::get("asn"),"infoRdN") > -1)
                        <div class="form-check">
                            <input class="form-check-input p-2 float-end" type="radio" name="settings" id="logedInorNot" checked> 
                            <label class="form-check-label me-4" for="assesPast"> همه  </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input p-2 float-end" type="radio" name="settings" id="logedInRadio">
                            <label class="form-check-label me-4" for="assesPast"> واردشده  </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input p-2 float-end" type="radio" name="settings" id="notLoginRadio">
                            <label class="form-check-label me-4" for="assesPast">  وارد نشده </label>
                        </div>
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
                         

                          <button class='btn btn-sm btn-primary text-warning w-50 sideBarBottomBtn' type="button" id='openDashboard'>  اخراج  <i class="fal fa-sign-out"></i></button>
                        <div class="form-group col-sm-2 mt-2 px-1">
                     @endif
                    </fieldset>
                  </div>
                <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                    <div class="row contentHeader RandTcontentHeader pt-2">
                        <div class="form-group col-sm-2">
                            <select class="form-select form-select-sm" id="searchGroup">
                                <option value="0"> موقعیت </option>
                                <option value="0">موقعیت دار </option>
                                <option value="0"> بدون موقعیت </option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <select class="form-select form-select-sm" id="orderInactiveCustomers">
                                <option value="-1">مرتب سازی</option>
                                <option value="2"> کد </option>
                                <option value="3">اسم</option>
                                <option value="1">همراه </option>
                                <option value="1"> تاریخ  </option>
                                <option value="1"> کاربر </option>
                            </select>
                        </div>
                        <div class="col-sm-6 text-start">
                            @if(hasPermission(Session::get("asn"),"infoRdN") > 1)
                                <button class='btn btn-primary btn-sm text-warning forMobileDisplay' disabled id="takhsisButton"> انتقال به دفتر حساب <i class="fal fa-exchange"> </i> </button>
                                <button class='btn btn-primary btn-sm text-warning enableBtn' type="button" disabled id="editRTbtn"> ویرایش <i class="fa fa-plus-square"></i></button>            
                                <button class='btn btn-primary btn-sm text-warning' type="button" id="addingNewCustomerBtn"> مشتری جدید  <i class="fa fa-plus-square"></i></button>            
                            @endif
                         </div>
                    </div>
                    <div class="row mainContent">
                         <div class="col-lg-12 p-0">
                                <table class='table table-bordered table-striped myDataTable loginTable' id="logedIn">
                                    <thead class="tableHeader">
                                        <tr>
                                            <th class="forMobileDisplay">ردیف</th>
                                            <th class="forMobileResize">اسم</th>
                                            <th class="forMobileDisplay">شماره تماس</th>
                                            <th class="forMobileDisplay">منطقه </th>
                                            <th>تاریخ ثبت</th>
                                            <th>  تاریخ ورود </th>
                                            <th class="forMobileDisplay">  تعداد ورود</th>
                                            <th>انتخاب</th>
                                        </tr>
                                    </thead>
                                    <tbody class="select-highlight tableBody" id="customerListBody1">
                                        @foreach($customers as $customer)
                                            <tr onclick="setEditRTStuff({{$customer->PSN}}); getCustomerInformation({{$customer->PSN}});">
                                                <td class="forMobileDisplay">{{$loop->iteration}}</td>
                                                <td  class="forMobileResize">{{$customer->Name}}</td>
                                                <td class="forMobileDisplay">{{$customer->PhoneStr}}</td>
                                                <td class="forMobileDisplay">{{$customer->NameRec}}</td>
                                                <td>{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($customer->TimeStamp))->format("Y/m/d")}}</td>
                                                <td> </td>
                                                <td class="forMobileDisplay"> </td>
                                                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="{{$customer->PSN.'_'.$customer->GroupCode}}"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <table class='table table-bordered table-striped myDataTable' id="notLogin">
                                    <thead class="tableHeader">
                                        <tr>
                                            <th class="forMobileDisplay">ردیف</th>
                                            <th class="forMobileResize" >اسم</th>
                                            <th class="forMobileDisplay">شماره تماس</th>
                                            <th class="forMobileDisplay">منطقه </th>
                                            <th>تاریخ ثبت</th>
                                            <th class="forMobileDisplay"> ادرس</th>
                                            <th>انتخاب</th>
                                        </tr>
                                    </thead>
                                    <tbody class="select-highlight tableBody" id="customerListBody1">
                                        @foreach($customers as $customer)
                                            <tr onclick="setEditRTStuff({{$customer->PSN}})">
                                                <td class="forMobileDisplay">{{$loop->iteration}}</td>
                                                <td class="forMobileResize">{{$customer->Name}}</td>
                                                <td class="forMobileDisplay">{{$customer->PhoneStr}}</td>
                                                <td class="forMobileDisplay" >{{$customer->NameRec}}</td>
                                                <td>{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($customer->TimeStamp))->format("Y/m/d")}}</td>
                                                <td class="forMobileDisplay">{{$customer->peopeladdress}}</td>
                                                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="{{$customer->PSN.'_'.$customer->GroupCode}}"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                         </div>

                    </div>
                    <div class="row contentFooter content-footer-mobile-version">
                         <div class="col-lg-12 text-start">
                            <button type="button" class="btn btn-sm btn-primary footerButton"> امروز  </button>
                            <button type="button" class="btn btn-sm btn-primary footerButton"> دیروز </button>
                            <button type="button" class="btn btn-sm btn-primary footerButton"> صد تای آخر 100</button>
                            <button type="button" class="btn btn-sm btn-primary footerButton"> همه </button>
                        </div>
                   </div>
                </div>
        </div>
    </div>


    <!-- modal of adding new customer -->
    <div class="modal fade dragableModal" id="addingNewCutomer" tabindex="-1"  data-bs-backdrop="static" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <button type="button" class="btn-close btn-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLongTitle"> افزودن مشتری </h5>
                </div>
                <div class="modal-body">
                    <form action="{{url('/addRandT')}}" method="POST"  enctype="multipart/form-data">
                    @csrf    
                    <div class="row">
                            <div class="col-md-3 col-sm-4 ">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label">نام و نام خانوادگی</label>
                                    <input type="text" required class="form-control" autocomplete="off" name="name">
                                </div>
                            </div>
						    <div class="col-md-3 col-sm-4 col-xs-5">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label">نام رستوران</label>
                                    <input type="text" required class="form-control" autocomplete="off" name="restaurantName">
                                </div>
                            </div>
						
                            <div class="col-md-2 col-sm-4 col-xs-7">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شماره همراه  </label>
                                    <input type="tel" required class="form-control" autocomplete="off" name="mobilePhone" maxlength="11">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 col-10">
                                   <div class="form-group ">
                                        <label class="dashboardLabel dashboardLabel form-label"> شماره ثابت </label>
                                             <div class="input-group  input-group-sm">
                                                 <input type="tel" style="height:40px !important;" required class="form-control p-0 " autocomplete="off" aria-label="Small" aria-describedby="inputGroup-sizing-sm" name="sabitPhone" min="0"  maxlength = "8">
                                            <div class="input-group-append">
                                                <select class="form-select" name="PhoneCode" id="PhoneCode">
                                                    @foreach($phoeCodes as $code)
                                                    <option value="{{$code->provinceCode}}">{{$code->provinceCode}}</option>
                                                    @endforeach
                                                </select>
                                            </div> &nbsp;
                                       <!--   <span id="addProvinceCode" data-toggle="modal" data-target="#countryCodeModal" style="margin-top:5px; color:blue; font-size:22px;"> <i class="fa fa-plus-circle fa-lg"></i> </sapn> -->
                                       </div>
                                 </div>
                            </div>
                            <div class="col-md-1 col-sm-1 col-1" style="margin-top:33px;"> 
                                <span id="addProvinceCode" data-toggle="modal" data-target="#countryCodeModal" style="margin-top:55px; color:blue; font-size:22px;"> <i class="fa fa-plus-circle fa-lg"></i> </sapn>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> ادرس کامل  </label>
                                    <input type="text" required class="form-control" autocomplete="off" name="peopeladdress">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> جنسیت</label>
                                    <select class="form-select" name="gender">
                                        <option value="2">مرد</option>
                                        <option value="1" >زن</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شهر</label>
                                    <select class="form-select" id="searchCity" name="snNahiyeh">
                                        @foreach($cities as $city)
                                        <option value="{{$city->SnMNM}}" >{{$city->NameRec}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
							   <div class="col-md-2">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> منطقه </label>
                                    <select class="form-select" id="searchMantagheh" name="snMantagheh">
                                        @foreach ($mantagheh as $mantaghe) {
                                        <option value="{{$mantaghe->SnMNM}}">{{$mantaghe->NameRec}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
							<div class="col-md-2">
                                 <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> نوع مشتری </label>
                                    <select class="form-select" name="secondGroupCode">
										<option value="7" >رستوران</option>
										<option value="8" >کترينگ</option>
										<option value="9" >فست فود</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                            @if(Session::get('adminType')==1 or Session::get('adminType')==5)
                            <div class="col-md-4">
                                <div class="form-group">
                                <label class="dashboardLabel dashboardLabel form-label">پشتیبان</label>
                                    <select class="form-select" name="adminId" id="">
                                        @foreach($admins as $admin)
                                        <option value="{{$admin->id}}">{{$admin->name.' '.$admin->lastName}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @else
                            <input type="hidden" name="adminId" value="{{Session::get('asn')}}">
                            @endif
                            <div class="col-md-5">
                               <div class="form-group">
                               <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label">توضیح</label>
                                    <textarea name="discription"   required class="form-control" rows="2"></textarea>
                                </div>
                                </div>
                            </div>  
							<div class="col-md-3 text-start">           
                                 <input type="text" id="customerLocation" name="location">  
                               <button type="button" class="btn btn-sm btn-success mt-3" id="openCurrentLocationModal" >دریافت لوکیشن خودکار</button>
                           </div>
						</div>
                   
                        <div class="modal-footer mt-2">
                            <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"> انصراف <i class="fa-solid fa-xmark"> </i> </button>
                            <button type="submit" id="submitRT"  class="btn btn-sm btn-primary">ذخیره <i class="fa fa-save" aria-hidden="true"> </i> </button>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
</div>
		

    <!-- modal of editting new customer -->
    <div class="modal fade dragableModal" id="editNewCustomer" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header py-2" style="margin:0; border:none">
                    <h5 class="modal-title" id="exampleModalLongTitle"> ویرایش مشتری</h5>
                </div>
                <div class="modal-body">
                    <form action="{{url('/editRT')}}" method="POST"  enctype="multipart/form-data">
                    @csrf   
                    <input type="hidden" name="customerId" id="customerID" value="3004345"> 
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label">نام و نام خانوادگی</label>
                                    <input type="text" required class="form-control" autocomplete="off" name="name" id="name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label">کد</label>
                                    <input type="text" required class="form-control" autocomplete="off" name="PCode" id="PCode">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شماره همراه  </label>
                                    <input type="number" required class="form-control" autocomplete="off" name="mobilePhone" id="mobilePhone">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شماره ثابت </label>
                                    <input type="number" required class="form-control" autocomplete="off" name="sabitPhone" id="sabitPhone">
                                </div>
                            </div>
                       
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> جنسیت</label>
                                    <select class="form-select" name="gender" id="gender">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شهر</label>
                                    <select class="form-select" name="snNahiyeh" id="snNahiyehE">
                                        @foreach($cities as $city)
                                        <option value="{{$city->SnMNM}}" >{{$city->NameRec}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> منطقه </label>
                                    <select class="form-select" name="snMantagheh" id="snMantaghehE">
                                        @foreach ($mantagheh as $mantaghe) {
                                        <option value="{{$mantaghe->SnMNM}}">{{$mantaghe->NameRec}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> ادرس کامل  </label>
                                    <input type="text" required class="form-control" autocomplete="off" name="peopeladdress" id="peopeladdress">
                                </div>
                            </div>
                       
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> نوع مشتری </label>
                                    <select class="form-select" name="secondGroupCode">
										<option value="7" >رستوران</option>
										<option value="8" >کترينگ</option>
										<option value="9" >فست فود</option>
                                    </select>
                                </div>
                            </div>
                            </div>
							<div class="col-md-12">           
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label">توضیح</label>
                                    <textarea name="discription"  required class="form-control" id="discription"  rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group text-start">
                            <button type="button" class="btn btn-sm btn-danger" id="cancelEditCustomer"> انصراف <i class="fa-solid fa-xmark"> </i> </button>
                            <button type="submit" class="btn btn-sm btn-primary">ذخیره <i class="fa fa-save" aria-hidden="true"> </i> </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
   </div>


  {{-- dashbor modal --}}

 <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

        <div class="modal" id="currentLocationModal" tabindex="-1" data-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable  modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="background-color:red"></button>
                        <h5 class="modal-title" id="exampleModalLabel"> تعیین موقعیت </h5>
                    </div>
                        <div class="modal-body">
                             <div id="mapId" style="width: 100%; height: 60vh"></div> 
                        </div>
                    <div class="modal-footer">
                           <button type="button" class="btn btn-primary" disabled id="saveLocationBtn" onclick="saveLocation()">ذخیره <i class="fa fa-save"></i> </button>
						<input type="text" id="currentLocationInput">
						   <button type="button" class="btn btn-danger" data-bs-dismiss="modal">بستن <i class="fa fa-x-mark"></i> </button>
                    </div>
                </div>
            </div>
        </div>

  
<script>
 
function generatePassword() {
    var length = 4,
        charset = "0123456789",
        retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    document.querySelector("#passwordValue").value = retVal;
}

 // for changing map
 $("#openCurrentLocationModal").on("click", ()=>{
	 var map_init = L.map('mapId').setView([35.70163, 51.39211], 12);
	
	 
        var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map_init);

       var lc = L.Control.geocoder().addTo(map_init);
        if (!navigator.geolocation) {
            console.log("لطفا مرورگر خویش را آپدیت نمایید!")
        } else {
            setInterval(() => {
                navigator.geolocation.getCurrentPosition(getPosition)
            }, 5000);
        };

        var marker, circle, lat, long, accuracy;

        function getPosition(position) {
            // console.log(position)
            lat = position.coords.latitude
            long = position.coords.longitude
            accuracy = position.coords.accuracy

            if (marker) {
                map_init.removeLayer(marker)
            }

            if (circle) {
                map_init.removeLayer(circle)
            }

            marker = L.marker([lat, long]);
            circle = L.circle([lat, long], { radius: accuracy });
            var featureGroup = L.featureGroup([marker, circle]).addTo(map_init);
            map_init.fitBounds(featureGroup.getBounds());
			$("#currentLocationInput").val(lat+','+long);
			$("#saveLocationBtn").prop("disabled",false);
            // $("#submitRT").prop("disabled",false);
            //alert("Your coordinate is: Lat: " + lat + " Long: " + long + " Accuracy: " + accuracy);
        }
	  
	
       $("#currentLocationModal").modal("show");
            
        setTimeout(() => {
            map_init.invalidateSize();
        }, 500);

});
	
	function saveLocation(){
		$("#customerLocation").val($("#currentLocationInput").val());
		$("#openCurrentLocationModal").prop("disabled",true);
		$("#currentLocationModa").modal("hide");
	}
	
$("#addingNewCustomerBtn").on("click", ()=>{	
		$("#addingNewCutomer").modal("show");

});
</script>
@endsection
