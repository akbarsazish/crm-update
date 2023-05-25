@extends('layout')
@section('content')

<style>
    label { font-size:14px; font-weight: bold; }
#getPassword {
    cursor: pointer;
}

@media only screen and (max-width: 920px) {
    .mobileDisplay{ display:none;}
    .bazarYabTable, tr{ font-size:10px !important;}
    .selectTr{width:20px !important; padding:0px;}
    
}
.bazarYabaction {
    min-width: 140px;
}
</style>
    <div class="container" style="margin-top:6%;">
            <div class="row">
                <h3 class="page-title">  مشتری جدید </h3>
            </div>
            <div class="row">
                     <div class="col-sm-2">
                        <div class="form-group ">
                            <input type="text" name="" size="20" placeholder="جستجو" class="form-control publicTop" id="allKalaFirst">
                        </div>
                     </div>
                     <div class="col-sm-2">
                        <div class="form-group">
                            <select class="form-select publicTop" id="searchGroup">
                                <option value="0"> موقعیت </option>
                                <option value="0">موقعیت دار </option>
                                <option value="0"> بدون موقعیت </option>
                            </select>
                        </div>
                     </div>
                     <div class="col-sm-8" style="display:flex; justify-content:flex-end">
                        @csrf
                        <!-- <button class='enableBtn btn btn-primary btn-sm text-warning mx-1' type="button" disabled id='openDashboard'> داشبورد <i class="fal fa-dashboard"></i></button> -->
                        @if(Session::get('adminType')==1 or Session::get('adminType')==5)
                        <button class='enableBtn btn btn-primary btn-sm btn-md text-warning buttonHover'   style="width:170px;"  disabled id="takhsisButton">بررسی مشتری<i class="fal fa-tasks fa-lg"> </i> </button>
                        @endif
                        @if(Session::get('adminType')==1 or Session::get('adminType')==3)
                        <button class='enableBtn btn btn-primary btn-sm text-warning mx-1' type="button" disabled onclick="openEditCustomerModalForm()">ویرایش مشتری<i class="fa fa-plus-square fa-lg"></i></button>            
                        @endif
                        <button class='enableBtn btn btn-primary btn-sm text-warning mx-1' type="button" id="addingNewCustomerBtn">افزودن مشتری جدید  <i class="fa fa-plus-square fa-lg"></i></button>            
                    </div>
            </div>  
@if(Session::get('adminType')==5)
            <div class="row">
                <div class="col-lg-12 p-2">
                     <h1>لیست مشتریان جدید برای ادمین </h1>
                     <table class='table table-bordered table-striped homeTables'>
                        <thead class="tableHeader">
                        <tr>
                            <th class="mobileDisplay">ردیف</th>
                            <th style="width:122px;">اسم</th>
                            <th style="width:111px;">شماره تماس</th>
                            <th class="mobileDisplay" style="width:88px">منطقه </th>
                            <th style="width:88px">تاریخ ثبت</th>
                            <th> ادرس</th>
                            <th class="mobileDisplay">ثبت کننده  </th>
                            <th>انتخاب</th>
                        </tr>
                        </thead>
                        <tbody class="select-highlight tableBody" id="customerListBody1">
                            @foreach($customers as $customer)
                                <tr>
                                    <td class="mobileDisplay" style="width:40px">{{$loop->iteration}}</td>
                                    <td style="width:122px;">{{$customer->Name}}</td>
                                    <td style="width:111px;">{{$customer->PhoneStr}}</td>
                                    <td class="mobileDisplay" style="width:88px">{{$customer->NameRec}}</td>
                                    <td style="width:88px">{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($customer->TimeStamp))->format("Y/m/d")}}</td>
                                    <td>{{$customer->peopeladdress}}</td>
                                    <td class="mobileDisplay">{{$customer->adminName.' '.$customer->adminLastName}}</td>
                                    <td> <input class="customerList form-check-input" name="customerId" type="radio" value="{{$customer->PSN.'_'.$customer->GroupCode}}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
		@else
            <div class="row">
            <div class="monthlyAction">
                @foreach($eachdays as $eachday)
                    <div class="eachMonth">
                        <div class="accordion accordion-flush" id="firstMonth">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingOne">
                                    <button class="accordion-button collapsed" @if(Session::get('adminType')==1 or Session::get('adminType')==5) onclick="showThisDayCustomerForAdmin({{"'".$eachday->addedDate."'"}},{{$loop->iteration}})" @else onclick="showThisDayMyCustomer({{"'".$eachday->addedDate."'"}},{{$loop->iteration}})" @endif type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{$loop->iteration}}" aria-expanded="false" aria-controls="flush-collapse{{$loop->iteration}}">
                                        {{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($eachday->addedDate))->format('Y/m/d')}} <span class="mx-5" style="border-radius:50%;background-color:black;padding:10px;">{{$eachday->countPeopels}}</span>
                                    </button>
                                </h2>
                                <div id="flush-collapse{{$loop->iteration}}" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
<!-- customers show here -->        <button class="btn btn-primary" id="loadMoreData" style="display:none"> بیشتر ...</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
              </div>
      </div>
		@endif

    <div class="modal fade dragableModal" id="takhsesKarbar" tabindex="-1" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" style="background-color:red;"></button>
                    <h5 class="modal-title"> تخصیص </h5>
                </div>
                <div class="modal-body" id="readCustomerComment">
                    <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">
                        @if(isset($customer))
                          <h3> تخصیص ({{$customer->Name}}) به کاربر دیگر</h3>
                        <table class="table table-bordered table-hover table-sm" id="tableGroupList">
                            <thead class="tableHeader">
                                <tr>
                                    <th>ردیف</th>
                                    <th>نام کاربر</th>
                                    <th>نقش کاربری</th>
                                    <th>فعال</th>
                                </tr>
                            </thead>
                            <tbody class="tableBody" id="mainGroupList">
                                @foreach ($admins as $admin)
                                    <tr onclick="setAdminStuff(this)">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$admin->name.' '.$admin->lastName}}</td>
                                        <td>
                                        @switch($admin->adminType)
                                            @case(2) پشتیبان
                                            @break
                                            @case(3) بازاریاب
                                            @break
                                        @endswitch
                                        </td>
                                        <td>
                                            <input class="mainGroupId" type="radio" name="AdminId" value="{{$admin->id}}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" id="cancelTakhsis">انصراف <i class="fa fa-xmark"></i></button>
                    <button type="button" onclick="takhsisNewCustomer()" class="btn btn-primary btn-sm">ذخیره <i class="fa fa-save"></i></button>
                </div>
            </div>
            </div>
        </div>
    </div>


    <!-- modal of adding new customer -->
    <div class="modal fade dragableModal" id="addingNewCutomer" tabindex="-1"  data-bs-backdrop="static" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="margin:0; border:none">
                    <button type="button" class="btn-close btn-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLongTitle"> افزودن مشتری </h5>
                </div>
                <div class="modal-body">
                    <form action="{{url('/addCustomer')}}" method="POST"  enctype="multipart/form-data">
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
                            <div class="col-md-2">
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
                            <div class="col-md-4">
                               <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> عکس </label>
								    <input type="file" class="form-control" name="picture" accept="image/*" capture="user">
                                </div>
                            </div>  
							<div class="col-md-3">           
                                 <input type="text" id="customerLocation" name="location">  
                               <button type="button" class="btn btn-success mt-3" id="openCurrentLocationModal" >دریافت لوکیشن خودکار</button>
                           </div>
						</div>
                   
                        <div class="modal-footer mt-2">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"> انصراف <i class="fa-solid fa-xmark"> </i> </button>
                            <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save" aria-hidden="true"> </i> </button>
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
                <div class="modal-header" style="margin:0; border:none">
                    <h5 class="modal-title" id="exampleModalLongTitle"> ویرایش مشتری</h5>
                </div>
                <div class="modal-body">
                    <form action="{{url('/editCustomer')}}" method="POST"  enctype="multipart/form-data">
                    @csrf   
                    <input type="hidden" name="customerId" id="customerID" value="3004345"> 
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label">نام و نام خانوادگی</label>
                                    <input type="text" required class="form-control" autocomplete="off" name="name" id="name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label">کد</label>
                                    <input type="text" required class="form-control" autocomplete="off" name="PCode" id="PCode">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شماره همراه  </label>
                                    <input type="number" required class="form-control" autocomplete="off" name="mobilePhone" id="mobilePhone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> شماره ثابت </label>
                                    <input type="number" required class="form-control" autocomplete="off" name="sabitPhone" id="sabitPhone">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> جنسیت</label>
                                    <select class="form-select" name="gender" id="gender">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> منطقه </label>
                                    <select class="form-select" name="snMantagheh" id="snMantaghehE">
                                        @foreach ($mantagheh as $mantaghe) {
                                        <option value="{{$mantaghe->SnMNM}}">{{$mantaghe->NameRec}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> ادرس کامل  </label>
                                    <input type="text" required class="form-control" autocomplete="off" name="peopeladdress" id="peopeladdress">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> نوع مشتری </label>
                                    <select class="form-select" name="groupCode" id="groupCode">
                                            <option value="314" >جدید</option>
                                    </select>
                                    <input type="hidden" name="adminId" id="adminId">
                                </div>
                            </div>
							<div class="col-md-6">           
                                <div class="form-group">
                                    <label class="dashboardLabel dashboardLabel form-label"> عکس </label>
                                    <input type="file" class="form-control" name="picture"  name="image" accept="image/*" capture="user">
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="form-group" style="margin-top:4%">
                            <button type="button" class="btn btn-danger" id="cancelEditCustomer"> انصراف <i class="fa-solid fa-xmark"> </i> </button>
                            <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save" aria-hidden="true"> </i> </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
   </div>


  {{-- dashbor modal --}}
  <div class="modal fade dragableModal" id="customerDashboard" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close btn-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="exampleModalLabel"> داشبورد </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <span class="fw-bold fs-4"  id="dashboardTitle" style="display:none;"></span>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <Button class="btn btn-sm buttonHover crmButtonColor float-end  mx-2" id="openAddCommentModal" type="button" value="" name="" > کامنت <i class="fas fa-comment fa-lg"> </i> </Button>
                            <form action="https://starfoods.ir/crmLogin" target="_blank"  method="get">
                                    <input type="text" id="customerSn" style="display: none" name="psn" value="" />
                                    <input type="text"  style="display: none" name="otherName" value="{{trim(Session::get('username'))}}" />
                                    <Button class="btn btn-sm buttonHover crmButtonColor float-end" type="submit"> ورود جعلی  <i class="fas fa-sign-in fa-lg"> </i> </Button>
                            </form>
                        </div>
                    </div><hr>
                    <div class="row">
                       <div class="col-lg-8 col-md-8 col-sm-8">
                           <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-2">
                                    <div class="form-outline" style="padding-bottom:1%">
                                        <label class="dashboardLabel form-label">کد</label>
                                        <input type="text" class="form-control form-control-sm noChange" id="customerCode" value="">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-outline " style="padding-bottom:1%">
                                        <label class="dashboardLabel form-label">نام و نام خانوادگی</label>
                                        <input type="text" class="form-control form-control-sm noChange" id="customerName"  value="علی حسینی" >
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> تعداد فاکتور </label>
                                        <input type="text" class="form-control form-control-sm noChange" id="countFactor">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label">  تلفن ثابت </label>
                                        <input class="form-control noChange" id="tell" type="text" name="" >
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label">  تلفن همراه 1 </label>
                                        <input class="form-control noChange" type="text" id="mobile1" >
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label">  تلفن همراه 2 </label>
                                        <input class="form-control noChange" type="text" id="mobile2" >
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-12 col-md-12">
                                    <div class="form-group">
                                        <label class="dashboardLabel form-label"> آدرس </label>
                                        <input type="text" class="form-control form-control-sm noChange" id="customerAddress" value="آدرس">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="mb-3" style="width:350px;">
                                    <label for="exampleFormControlTextarea1" class="form-label fw-bold">یاداشت  </label>
                                    <textarea class="form-control" id="customerProperty" onblur="saveCustomerCommentProperty(this)" rows="6"></textarea>
                                </div>
                            </div>
                       </div>
                    </div>

                    <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                        <div class="col-sm-12" style="margin: 0; padding:0;">
                            <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                                <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری شده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#userLoginInfo1"> کالاهای سبد خرید</a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#customerLoginInfo">ورود به سیستم</a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#returnedFactors1"> فاکتور های برگشت داده </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments"> کامنت ها </a></li>
                                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments"> نظرسنجی ها</a></li>
                            </ul>
                        </div>
                        <div class="c-checkout tab-content"   style="background-color:#f5f5f5; margin:0;padding:0.3%; border-radius:10px 10px 2px 2px;">
                            <div class="row c-checkout rounded-3 tab-pane active" id="custAddress"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="col-sm-12">
                                    <table class="homeTables factor crmDataTable tableSection4 table table-bordered table-striped table-sm">
                                        <thead  style="position: sticky;top: 0;">
                                        <tr>
                                            <th> ردیف</th>
                                            <th>تاریخ</th>
                                            <th> نام راننده</th>
                                            <th>مبلغ </th>
                                            <th>مشاهد جزئیات </th>
                                        </tr>
                                        </thead>
                                        <tbody  id="factorTable">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="homeTables crmDataTable buyiedKala tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="position: sticky;top: 0;">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام کالا</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="goodDetail">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="userLoginInfo1" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="homeTables crmDataTable basketKala tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="position: sticky;top: 0;">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام کالا</th>
                                                <th>تعداد </th>
                                                <th>فی</th>
                                            </tr>
                                            </thead>
                                            <tbody id="basketOrders">
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="customerLoginInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="homeTables crmDataTable returnedFactor tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="position: sticky;top: 0;">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th>نوع پلتفورم</th>
                                                <th>مرورگر</th>
                                            </tr>
                                            </thead>
                                            <tbody id="customerLoginInfoBody">
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row c-checkout rounded-3 tab-pane" id="returnedFactors1"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="row c-checkout rounded-3 tab-pane" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="homeTables crmDataTable comments tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="position: sticky;top: 0;">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> نام راننده</th>
                                                <th>مبلغ </th>
                                            </tr>
                                            </thead>
                                            <tbody id="returnedFactorsBody">
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="c-checkout tab-pane" id="comments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="homeTables crmDataTable nazarSanji tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="position: sticky;top: 0;">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> کامنت</th>
                                                <th> کامنت بعدی</th>
                                                <th> تاریخ بعدی </th>
                                            </tr>
                                            </thead>
                                            <tbody id="customerComments"  >

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="c-checkout tab-pane" id="assesments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="homeTables crmDataTable myCustomer tableSection4 table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="position: sticky;top: 0;">
                                            <tr>
                                                <th> ردیف</th>
                                                <th>تاریخ</th>
                                                <th> کامنت</th>
                                                <th> برخورد راننده</th>
                                                <th> مشکل در بارگیری</th>
                                                <th> کالاهای برگشتی</th>
                                            </tr>
                                            </thead>
                                            <tbody id="customerAssesments"  >
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
        <!-- Modal for factor detail-->
        <div class="modal fade" id="viewFactorDetail" tabindex="0" data-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog  modal-dialog   modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close"></button>
                        <h5 class="modal-title" id="exampleModalLabel">جزئیات فاکتور</h5>
                    </div>
                    <div class="modal-body" id="readCustomerComment">
                        <div class="container">
                            <div class="row" style=" border:1px solid #dee2e6; padding:10px">
                                    <h4 style="padding:10px; border-bottom: 1px solid #dee2e6; text-align:center;">فاکتور فروش </h4>
                                    <div class="col-sm-6">
                                        <table class="crmDataTable table table-borderless" style="background-color:#dee2e6">
                                            <tbody>
                                            <tr>
                                                <td>تاریخ فاکتور:</td>
                                                <td id="factorDate"></td>
                                            </tr>
                                            <tr>
                                                <td>مشتری:</td>
                                                <td id="customerNameFactor"></td>
                                            </tr>
                                            <tr>
                                                <td>آدرس:</td>
                                                <td id="customerAddressFactor"> </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-sm-6">
                                        <table class="table table-borderless" style="background-color:#dee2e6">
                                            <tbody>
                                                <tr>
                                                    <td>تلفن :</td>
                                                    <td id="customerPhoneFactor"></td>
                                                </tr>
                                            <tr>
                                                <td>کاربر :</td>
                                                <td id="Admin"></td>
                                            </tr>
                                            <tr>
                                                <td>شماره فاکتور :</td>
                                                <td id="factorSnFactor"></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <table id="strCusDataTable"  class='crmDataTable dashbordTables table table-bordered table-striped table-sm' style="background-color:#dee2e6">
                                        <thead  style="position: sticky;top: 0;">
                                        <tr>
                                            <th scope="col">ردیف</th>
                                            <th scope="col">نام کالا </th>
                                            <th scope="col">تعداد/مقدار</th>
                                            <th scope="col">واحد کالا</th>
                                            <th scope="col">فی (تومان)</th>
                                            <th scope="col">مبلغ (تومان)</th>
                                        </tr>
                                        </thead>
                                        <tbody id="productList">

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

        <!-- Modal for reading comments-->
        <div class="modal fade" id="viewComment" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close"></button>
                        <h5 class="modal-title" id="exampleModalLabel">کامنت ها</h5>
                    </div>
                    <div class="modal-body" >
                        <h3 id="readCustomerComment1"></h3>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">بستن</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal for returning customer-->
        <div class="modal fade" id="returnComment"  data-backdrop="static"  aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> ارجاع</h5>
                    </div>
                    <form action="{{url('/returnCustomer')}}" id="returnCustomerForm" method="get">
                        <div class="modal-body">
                            <input type="text" name="returnCustomerId" style="display:none" id="returnCustomerId">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="form-label" for="tahvilBar">دلیل ارجاع</label>
                                    <textarea class="form-control" required style="position:relative" name="returnComment" rows="3" ></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" id="cancelReturn" style="background-color:red;">انصراف<i class="fal fa-cancel"> </i></button>
                            <button type="submit" class="btn btn-sm btn-primary">ذخیره <i class="fal fa-save"> </i> </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--- modal for adding comments -->
        <div class="modal" id="addComment" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel">
            <div class="modal-dialog modal-dialog-scrollable ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> افزودن کامنت </h5>
                    </div>
                <div class="modal-body">
                    <form action="{{url('/addComment')}}" id="addCommentForm" method="get">
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar">نوع تماس</label>
                                <select class="form-select" name="callType">
                                    <option value="1">موبایل</option>
                                    <option value="2">تلفن ثابت</option>
                                    <option value="3">واتساپ</option>
                                    <option value="4">حضوری</option>
                                </select>
                                <input type="text" style="display:none" name="customerIdForComment" id="customerIdForComment">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar" >کامنت </label>
                                <textarea class="form-control" style="position:relative" required name="firstComment" id="firstComment" rows="3" ></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 fw-bold">
                                <label for="tahvilBar" >زمان تماس بعدی </label>
                                    <input class="form-control" autocomplete="off" required name="nextDate" id="commentDate2">
                                    <input class="form-control" autocomplete="off" style="display:none" value="0" required name="mantagheh" id="mantaghehId">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="tahvilBar">کامنت بعدی</label>
                                <textarea class="form-control" name="secondComment" required id="secondComment" rows="5" ></textarea>
                                <input class="form-control" type="text" style="display: none;" name="place" value="customers"/>
                            </div>
                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-danger" id="cancelComment">انصراف<i class="fa fa-xmark"></i></button>
                                <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save"></i></button>
                        </div>
                    </form>
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
  if (!($('.modal.in').length)) {
                $('.modal-dialog').css({
                  top: 0,
                  left: 0
                });
              }
              $('#addingNewCutomer').modal({
                backdrop: false,
                show: true
              });
              
              $('.modal-dialog').draggable({
                  handle: ".modal-header"
                });	
		$("#addingNewCutomer").modal("show");

});
</script>
@endsection
