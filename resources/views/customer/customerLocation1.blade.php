@extends('layout')
@section('content')
    <div class="container" style="margin-top:4%;">
                <h3 class="page-title">موقعیت مشتری</h3>
                <div class="row">
                    <div class="col-sm-12">
                       <div  id="map"></div>
                    </div>
                </div>
               <div class="row">
                   <div class="col-lg-2 col-md-2 col-sm-3">
                        <div class="form-group">
                            <label class="dashboardLabel form-label">  وضعیت  </label>
                            <select class="form-select form-select-sm" id="customerWithorWithoutLocation">
                                <option value="" hidden>همه </option>
                                <option value="0">مشتری فاقد لوکیشن</option>
                                <option value="1">مشتری با لوکیشن</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-4">
                            <div class="form-group">
                              <label class="dashboardLabel form-label">   </label>
                             <input type="text" class="form-control form-control-sm" id="searchingCustomerName" placeholder="جستجو " style="margin-top:18px;">
                         </div>
                    </div>
                <div class="col-lg-7 col-md-7 col-sm-5" style="margin-top:28px; display:flex; justify-content:flex-end;">
               <button class="btn btn-primary btn-sm enableBtn buttonHover text-warning"  id="changePosition">تغییر لوکیشن <i class="fa fa-edit fa-lg"></i></button>
					
                    </div>
                </div>
                <div class="row">
                    <table class='table table-bordered table-striped' id="tableGroupList">
                    <thead class="tableHeader">
                            <tr>
                                <th>ردیف</th>
                                <th style="width:250px;">نام مشتری</th>
                                <th style="width:500px;">ادرس</th>
                                <th style="width: 90px;">تلفن </th>
                                <th style="width: 90px;">همراه  </th>
                                <!-- <th style="width:100px">موقعیت  </th> -->
                                <th> انتخاب</th>
                            </tr>
                        </thead>
                        <tbody class="tableBody" id="customerLocation">
                            @foreach ($allCustomers as $customer)
                            <tr id="forTh" onclick="selectCustomerLocation(this);">
                                <td >{{$loop->iteration}}</td>
                                <td style="width:250px;">{{$customer->Name}} </td>
                                <td style="width:500px;">{{$customer->peopeladdress}}</td>
                                <td style="width: 90px;">{{$customer->sabit}}</td>
                                <td style="width: 90px;">{{$customer->hamrah}}</td>
                                <!-- <td style="width:100px">دارد/ندارد</td> -->
                                <td style="width:70px;"><span><input type="radio" name="changeLocation" id="selectCustomer" value="{{$customer->PSN}}"/></span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        <div class="modal fade" id="customerDashboard" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable  modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close btn-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h5 class="modal-title" id="exampleModalLabel"> داشبورد </h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 coll-sm-12">
                                <span class="fw-bold fs-4"  id="dashboardTitle"></span>
                            </div>
                            <div class="col-lg-6 col-md-6 coll-sm-12">
                                <form action="https://starfod.ir/crmLogin" target="_blank"  method="get">
                                    <input type="text" id="customerSn" style="display: none" name="psn" value="" />
                                    <input type="text"  style="display:none" name="otherName" value="{{trim(Session::get('username'))}}" />
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
                                            <label class="dashboardLabel form-label"> شماره های تماس </label>
                                            <input class="form-control noChange" type="text" id="mobile1" >
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label">نام کاربری</label>
                                            <input class="form-control noChange" type="text" id="username" >
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-group">
                                            <label class="dashboardLabel form-label">رمز کاربری</label>
                                            <input class="form-control noChange" type="text" id="password" >
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
                                        <textarea class="form-control" id="customerProperty"  onblur="saveCustomerCommentProperty(this)" rows="6" ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                            <div class="col-sm-8" style="margin: 0; padding:0;">
                                <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                                    <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاکتور های ارسال شده </a></li>
                                    <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  کالاهای خریداری کرده </a></li>
                                    <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#userLoginInfo1"> کالاهای سبد خرید</a></li>
                                    <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#picture1"> فاکتور های برگشت داده </a></li>
                                    <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#comments"> کامنت ها </a></li>
                                    <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#assesments"> نظرسنجی ها</a></li>
                                </ul>
                            </div>
                            <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                            <thead  style="display:block;">
                                            <tr>
                                                <th style="width:44px;"> ردیف</th>
                                                <th style="width:111px;">تاریخ</th>
                                                <th style="width:170px;"> نام راننده</th>
                                                <th style="width:300px;">مبلغ </th>
                                            </tr>
                                            </thead>
                                            <tbody  id="factorTable"  style="height:150px; overflow-y:scroll; display:block;width:100%">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="col-sm-12">
                                            <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                                <thead style="display:block;">
                                                <tr>
                                                    <th style="width:44px;"> ردیف</th>
                                                    <th style="width:111px;">تاریخ</th>
                                                    <th style="width:170px;"> نام کالا</th>
                                                </tr>
                                                </thead>
                                                <tbody id="goodDetail" style="height:150px; overflow-y:scroll; display:block;">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row c-checkout rounded-3 tab-pane" id="userLoginInfo1" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                    <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="col-sm-12">
                                            <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                                <thead>
                                                <tr>
                                                    <th style="width:44px;"> ردیف</th>
                                                    <th style="width:111px;">تاریخ</th>
                                                    <th style="width:170px;"> نام کالا</th>
                                                    <th style="width:70px;">تعداد </th>
                                                    <th style="width:100px;">فی</th>
                                                </tr>
                                                </thead>
                                                <tbody id="basketOrders" style="height:150px; overflow-y:scroll; display:block;">
                                                <tr>
                                                    <td style="width:44px;"> 1 </td>
                                                    <td style="width:111px;"></td>
                                                    <td style="width:170px;"></td>
                                                    <td style="width:70px;"></td>
                                                    <td style="width:100px;"></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="c-checkout tab-pane" id="picture1" style="margin:0; border-radius:10px 10px 2px 2px;">
                                    <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="col-sm-12">
                                            <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                                <thead>
                                                <tr>
                                                    <th style="width:44px;"> ردیف</th>
                                                    <th style="width:111px;">تاریخ</th>
                                                    <th style="width:170px;"> نام کالا</th>
                                                    <th style="width:70px;">تعداد </th>
                                                    <th style="width:100px;">فی</th>
                                                </tr>
                                                </thead>
                                                <tbody style="height:150px; overflow-y:scroll;display:block;width:100%">
                                                <tr>
                                                    <td style="width:44px;"> 1 </td>
                                                    <td style="width:111px;"></td>
                                                    <td style="width:170px;"></td>
                                                    <td style="width:70px;"></td>
                                                    <td style="width:100px;">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="c-checkout tab-pane" id="comments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                    <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="col-sm-12">
                                            <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                                <thead>
                                                <tr>
                                                    <th style="width:44px;"> ردیف</th>
                                                    <th style="width:80px;">تاریخ</th>
                                                    <th style="width:300px;"> کامنت</th>
                                                    <th style="width:300px;"> کامنت بعدی</th>
                                                    <th style="width:80px;"> تاریخ بعدی </th>
                                                    <th style="width:40px;"> انتخاب </th>
                                                </tr>
                                                </thead>
                                                <tbody id="customerComments"   style="height:150px; overflow-y:scroll;display:block;width:100%">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="c-checkout tab-pane" id="assesments" style="margin:0; border-radius:10px 10px 2px 2px;">
                                    <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                        <div class="col-sm-12">
                                            <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                                <thead>
                                                <tr>
                                                    <th style="width:44px;"> ردیف</th>
                                                    <th style="width:80px;">تاریخ</th>
                                                    <th style="width:300px;"> کامنت</th>
                                                    <th style="width:300px;"> برخورد راننده</th>
                                                    <th style="width:80px;"> مشکل در بارگیری</th>
                                                    <th style="width:80px;"> کالاهای برگشتی</th>
                                                    <th style="width:40px;"> انتخاب </th>
                                                </tr>
                                                </thead>
                                                <tbody id="customerAssesments"   style="height:150px; overflow-y:scroll;display:block;width:100%">

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


        <!-- Modal -->
        <div class="modal fade" id="changeCustomerLocation" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable  modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color:red"></button>
                        <h5 class="modal-title" id="exampleModalLabel"> موقعیت راننده </h5>
                    </div>
                    <div class="modal-body">
                            <div class="row" style="width:100%; height:480px;">
                                <div class="container">
                                        <div class="row" style="width:100%;height:480px;" id="map2"></div>
                                </div>
                        </div>
                        <form action="{{url('/updatePosition')}}" id="changePositionForm">
                        <div>
                            <input type="text" class="form-control" value="" name='pers' id="newPosition">
                            <input type="hidden" class="form-control" name='psn' value="" id="customerLocInputHidden">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" form="changePositionForm" class="btn btn-danger" data-bs-dismiss="modal">بستن </button>
                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal"> ثبت موقعیت <i class="fa fa-save"></i> </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


<script>
        $.ajax({
            method:"GET",
            url:"https://star4.ir/searchMap",

        }).then(function(data){
            var map = L.map('map').setView([35.70163, 51.39211], 12);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    }).addTo(map);

            var marker ={};
                data.forEach(function(item){

                    if(item.LatPers>0 && item.LonPers>0){
                        var popup =new  L.popup().setContent();
                        marker = L.marker([item.LonPers,item.LatPers]).addTo(map);
                        let btn = document.createElement('a');
                        btn.innerText = '' +item.Name;
                        btn.setAttribute('onclick', "openDashboard("+item.PSN+")");

                        marker.bindPopup(btn, {
                          Width: '300px'
                        });
                    }
                });
        });



function openDashboard(psn) {
    let csn = psn;
    $.ajax({
        method: 'get',
        url: "https://star4.ir/customerDashboard",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn
        },
        async: true,
        success: function(msg) {
            moment.locale('en');
            let exactCustomer = msg[0];
            let factors = msg[1];
            let goodDetails = msg[2];
            let basketOrders = msg[3];
            let comments = msg[4];
            let specialComments = msg[5];
            let specialComment = specialComments[0];
            let assesments=msg[6];
            let returnedFactors=msg[7];
            $("#customerProperty").val(specialComment.comment);
            $("#dashboardTitle").text(exactCustomer.Name);
            $("#customerCode").val(exactCustomer.PCode);
            $("#customerName").val(exactCustomer.Name);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#mobile1").val(exactCustomer.PhoneStr);
            $("#customerIdForComment").val(exactCustomer.PSN);
            $("#countFactor").val(exactCustomer.countFactor);
            $("#factorTable").empty();
            factors.forEach((element, index) => {
                $("#factorTable").append(`<tr class="tbodyTr">
                    <td>` + (index + 1) + `</td>
                    <td>` + element.FactDate+ `</td>
                    <td>نامعلوم</td>
                    <td>` + parseInt(element.TotalPriceHDS/10).toLocaleString("en-us") + `</td>
                </tr>`);
            });
         $("#returnedFactorsBody").empty();
            returnedFactors.forEach((element, index) => {
                $("#returnedFactorsBody").append(`<tr class="tbodyTr">
                <td style="width:44px;">` + (index + 1) + `</td>
                <td style="width:111px;">` + element.FactDate+ `</td>
                <td style="width:170px;">نامعلوم</td>
                <td style="width:70px;">` + parseInt(element.TotalPriceHDS/10).toLocaleString("en-us") + `</td>
                </tr>`);
            });
          $('#goodDetail').empty();
            goodDetails.forEach((element, index) => {
                $('#goodDetail').append(`
                <tr class="tbodyTr">
                    <td>` + (index + 1) + ` </td>
                    <td>` + moment(element.maxTime, 'YYYY/M/D HH:mm:ss').locale('fa').format('YYYY/M/D') + `</td>
                    <td>` + element.GoodName + `</td>
                    <td>  </td>
                    <td>  </td>
                    
                </tr>`);
            });

          $("#basketOrders").empty();
            basketOrders.forEach((element, index) => {
                $("#basketOrders").append(`<tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + moment(element.TimeStamp, 'YYYY/M/D HH:mm:ss').locale('fa').format('YYYY/M/D') + `</td>
                    <td>` + element.GoodName + `</td>
                    <td>` + element.Amount + `</td>
                    <td>` + element.Fi + `</td>
                    </tr>`);
            });
            $("#customerComments").empty();
            comments.forEach((element, index) => {
                $("#customerComments").append(`<tr class="tbodyTr">
                    <td> ` + (index + 1) + ` </td>
                    <td>` +moment(element.TimeStamp, 'YYYY/M/D HH:mm:ss').locale('fa').format('YYYY/M/D')+ `</td>
                    <td onclick="viewComment(` + element.id + `)"</td>` + element.newComment + ` <i class="fas fa-comment-dots float-end"></i> </td>
                    <td>` + element.nexComment + ` <i class="fas fa-comment-dots float-end"></i>  </td>
                    <td>` + moment(element.specifiedDate, 'YYYY/M/D HH:mm:ss').locale('fa').format('YYYY/M/D') + `</td>
                    <td><input type="radio"/> </td>
                    </tr>`);
            });
            $("#customerAssesments").empty();
            assesments.forEach((element,index)=>{
                let driverBehavior="";
                let shipmentProblem="بله";
                if(element.shipmentProblem==1){
                    shipmentProblem="خیر"
                }
                switch (parseInt(element.driverBehavior)) {
                    case 1:
                        driverBehavior="عالی"
                        break;
                    case 2:
                        driverBehavior="خوب"
                        break;
                    case 3:
                        driverBehavior="متوسط"
                        break;
                    case 4:
                        driverBehavior="بد"
                        break;
                    default:
                        break;
                }
                $("#customerAssesments").append(`
                <tr>
                <td>`+(index+1)+`</td>
                <td>`+moment(element.TimeStamp, 'YYYY/M/D').locale('fa').format('YYYY/M/D')+`</td>
                <td>`+element.comment+`</td>
                <td>`+driverBehavior+`</td>
                <td>`+shipmentProblem+`</td>
                <td></td>
                <td></td>
                <td><input type="radio" class="form-input"/></td>
            </tr>`);
            });
			
            $("#customerDashboard").modal("show");
        },
        error: function(data) {}
    });
}


// the fowllowing functin is used to edit customer location based on address

$("#changePosition").on("click", (e) => {
    let custIdLoc = $("#customerLocInputHidden").val();
   
    $.ajax({
        method: 'get',
        url: "https://star4.ir/changePosition",
        data: {
            _token: "{{ csrf_token() }}",
            customerId: custIdLoc
        },
        async: true,
        success: function(data) {
            var map;
            if (L.DomUtil.get('map2') !== undefined) { 
                    L.DomUtil.get('map2')._leaflet_id = null; 
                }
                //  var map = L.map('map2').setView([43.64701, -79.39425], 10);
                 map = L.map('map2', {center:[35.70163, 51.39211], zoom:10});
                          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '<a href="https://osm.org/copyright">CRM</a>'
                          }).addTo(map);


             var marker ={};
                data.forEach(function(item){
                if(item.LatPers>0 && item.LonPers>0){
                    var popup =new  L.popup().setContent();
                    marker = L.marker([item.LonPers,item.LatPers],{
                        title: " تغییر موقعیت", 
                        draggable: true,
                    }
                    ).addTo(map).bindPopup(popup);

                    marker.on('dragend', () => {
                         let newposition = marker.getLatLng();
                         $("#newPosition").val(newposition.lng+','+newposition.lat);
                    });

                    let btn = document.createElement('a');
                    btn.innerText = 'مشتری ';
                    // btn.setAttribute('href', "/Cardboard/cCode");
                    marker.bindPopup(btn, {
                        maxWidth: '200px'
                    });
                }else {
                        let defaultposition = [35.70163, 51.39211];
                        
                        $("#newPosition").val(defaultposition);
                        marker = L.marker(defaultposition,{
                          title: "تعیین موقعیت", draggable: true,
                        
                     }
                    ).addTo(map).bindPopup(popup);

                    marker.on('dragend', () => {
                         let newposition = marker.getLatLng();
                         $("#newPosition").val(newposition.lng+','+newposition.lat);
                    });

                    let btn = document.createElement('a');
                    btn.innerText = 'مشتری ';
                    // btn.setAttribute('href', "/Cardboard/cCode");
                    marker.bindPopup(btn, {
                        maxWidth: '200px'
                    });
                }
        
                });
			
			  if (!($('.modal.in').length)) {
                $('.modal-dialog').css({
                  top: 0,
                  left: 0
                });
              }
              $('#changeCustomerLocation').modal({
                backdrop: false,
                show: true
              });
              
              $('.modal-dialog').draggable({
                  handle: ".modal-header"
                });
               
                $("#changeCustomerLocation").modal("show");
                setTimeout(function () {
                    window.dispatchEvent(new Event("resize"));
                    }, 500);
            }
        });
    });



    </script>

@endsection
