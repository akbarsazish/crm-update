@extends('layout')
@section('content')

<style>
  #chartdiv {
  width: 100%;
  height: 400px;
}

 </style>
        <div class="container" style="margin-top:5%">
            <div class="c-checkout container-fluid" style="background-image: linear-gradient(to right, #ffffff,#3fa7ef,#3fa7ef); margin:0.2% 0; margin-bottom:0; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                <div class="col-sm-6" style="margin: 0; padding:0;">
                    <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                        <li><a class="active" data-toggle="tab" style="color:black;"  href="#karbarLogin">  گزارش ورود به سیستم (اشخاص)  </a></li>
                        <li><a data-toggle="tab" style="color:black;"  href="#custAddress"> گزارش ورود به سیستم (نموداری) </a></li>
                    </ul>
                </div>
                  <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                    <div class="row c-checkout rounded-3 tab-pane active" id="karbarLogin" style="background-color:#f5f5f5; width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                         <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                <div class="card-body">
                                <div class="row">
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                  <label for="visitorSearchName">جستجوی اسم</label>
                                                   <input type="text" class="form-control" id="visitorSearchName" placeholder="جستجو">
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                  <label for="visitorPlatform">پلتفورم</label>
                                                   <select type="text" class="form-control" id="visitorPlatform">
                                                        <option value='0'>همه</option>
                                                        <option value='Android'>اندروید</option>
                                                        <option value='iOS'>ios</option>
                                                        <option value='Windows'>windows</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                  <label for="LoginDate1">از تاریخ</label>
                                                   <input type="text" placeholder="تاریخ" id="LoginDate1" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                  <label for="LoginDate2">الی تاریخ</label>
                                                   <input type="text" placeholder="تاریخ" id="LoginDate2" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-1">
                                                <div class="form-group">
                                                  <label for="LoginDate2">تعدا ورود از:</label>
                                                   <input type="number" placeholder="تعداد" id="LoginFrom" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-1">
                                                <div class="form-group">
                                                  <label for="LoginDate2">تعداد ورود تا:</label>
                                                   <input type="number" placeholder="تعداد" id="LoginTo" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                  <label for="countSameTime">تعداد همزمان هر مشتری از:</label>
                                                   <input type="number" placeholder="تعداد" id="countSameTime" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                    <div class="row">
                                    <div class="well" style="margin-top:1%;">
                                      <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">
                                        <table class='table table-bordered table-striped table-sm'>
                                          <thead class="tableHeader">
                                              <tr>
                                                <th > ردیف</th>
                                                <th >اولین ورود</th>
                                                <th >آخرین ورود</th>
                                                <th style="width:244px"> نام مشتری</th>
                                                <th >سیستم </th>
                                                <th >مرورگر</th>
                                                <th style="width:88px">تعداد ورود </th>
                                                <th > ورود همزمان</th>
                                            </tr>
                                            </thead>
                                            <tbody id="listVisitorBody" class="tableBody">
                                              @foreach($visitors as $visitor)
                                                <tr>
                                                    <td >{{$loop->iteration}}</td>
                                                    <td >{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($visitor->firstVisit))->format("Y/m/d H:i:s")}}</td>
                                                    <td >{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($visitor->lastVisit))->format("Y/m/d H:i:s")}}</td>
                                                    <td style="width:244px">{{$visitor->Name}}</td>
                                                    <td >{{$visitor->platform}}</td>
                                                    <td >{{$visitor->browser}}</td>
                                                    <td style="width:88px">{{$visitor->countLogin}}</td>
                                                    <td >{{$visitor->countSameTime}}</td>
                                                </tr>
                                              @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                    </div>
                                </div>
                         </div>
                    </div>
                    <div class="row c-checkout rounded-3 tab-pane" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="col-sm-12">
                                <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                <span class="card p-4">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                   <input type="date" class="form-control">
                                                </div>
                                            </div>
                                        </div> <br>
                                        <div class="col-lg-12 col-md-12 col-sm-12 card">
                                             <div id="chartdiv"></div>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                 </div>
             </div>
          </div>

  


@endsection
