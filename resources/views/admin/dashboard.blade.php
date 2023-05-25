@extends('layout')
@section('content')
<main class="home-margin-top">
    <!-- Main page content-->
 <!-- <div class="container">
    <div class="card mb-2">
     <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="card h-100 دCard">
                    <a class=" stretched-link" href="{{url('/allCustomers')}}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="me-3">
                                    <div class="large fw-bold">لیست کل مشتریان</div>
                                    <div class="text-xxl fw-bold"></div>
                                </div>
                                <i class="fa fa-home fa-3x" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between small"></div>
                    </a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card  h-100 دCard">
                    <a class=" stretched-link" href="{{url('/karbarAction')}}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="me-3">
                                    <div class="fw-bold">عمل کرد کاربران</div>
                                    <div class="text-lg fw-bold"></div>
                                </div>
                                <i class="fa fa-user fa-3x" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between small">
                            <div class=""></div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 دCard">
                <a class=" stretched-link" href="{{url('/crmSetting')}}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <div class="-75 fw-bold">مدیریت اختصاصی سایت</div>
                                <div class="text-lg fw-bold"></div>
                            </div>
                            <i class="fa fa-cog fa-3x" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <div class=""></div>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 دCard">
                <a class=" stretched-link" href="{{url('/reports')}}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <div class="-75 fw-bold ">گزارشات</div>
                                <div class="text-lg fw-bold"></div>
                            </div>
                            <i class="fa-solid fa-chart-mixed fa-3x"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <div class=""></div>
                    </div>
                    </a>
                </div>
            </div>
        </div>
        <br>
        <br>
        <div class="row">
            <div class="col-md-6">
                <div class="card h-100 دCard">
                  <a class=" stretched-link" href="{{url('/referedCustomer')}}" target="_blank">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <div class="fw-bold"> ارجاعات </div>
                                <div class="text-lg fw-bold"></div>
                            </div>
                            <i class="fa fa-history fa-3x" aria-hidden="true"></i>
                            </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                       <div class=""></div>
                    </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 دCard">
                  <a class="stretched-link" href="{{url('/customerLocation')}}" target="_blank">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-3">
                                <div class="-75 fw-bold">موقعیت مشتری </div>
                                <div class="text-lg fw-bold"></div>
                            </div>
                            <i class="fas fa-map-marker-alt fa-3x" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                       <div class=""></div>
                    </div>
                    </a>
                </div>
            </div>
        </div>
            </div>
        </div>
     </div> -->
        <div class="container">
            <div class="c-checkout container-fluid" style="background-image: linear-gradient(to right, #ffffff,#3fa7ef,#3fa7ef); margin:0.2% 0; margin-bottom:0; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                <div class="col-lg-6 col-12" style="margin: 0; padding:0;">
                    <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                        <li><a class="active" data-toggle="tab" style="color:black;"  href="#custAddress"> عملکرد مشتریان </a></li>
                        <li><a data-toggle="tab" style="color:black;"  href="#kalaInfo"> عملکرد کالاها  </a></li>
                        <li><a data-toggle="tab" style="color:black;"  href="#moRagiInfo">  چارت ها  </a></li>
                        <li><a data-toggle="tab" style="color:black;"  href="#siteAdmin"> کاربران </a></li>
                    </ul>
                </div>
                    <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                        <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="col-sm-12">
                                <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                    <span class="card p-4">
                                        <div class="row">
                                            <div class="col-md-2 col-sm-6">
                                                <div class="counter">
                                                    <div class="counter-icon">
                                                        <i class="fa fa-group"></i>
                                                    </div>
                                                    <h3>کل مشتریان</h3>
                                                    <span class="counter-value">{{$allCustomers}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-6">
                                                <div class="counter blue">
                                                    <div class="counter-icon">
                                                        <i class="fa fa-toggle-on"></i>
                                                    </div>
                                                    <h3>مشتری های دارای پشتیبان  </h3>
                                                    <span class="counter-value">{{$allAddedCustomersCount}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-6">
                                                <div class="counter blue">
                                                    <div class="counter-icon">
                                                        <i class="fa fa-toggle-off"></i>
                                                    </div>
                                                    <h3>مشتری های غیر فعال  </h3>
                                                    <span class="counter-value">{{$allInActiveCustomers}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-6">
                                              <div class="counter green">
                                                  <div class="counter-icon">
                                                      <i class="fas fa-history"></i>
                                                  </div>
                                                  <h3>مشتریان  ارجاع شده</h3>
                                                  <span class="counter-value">{{$allReturnedCustomer}}</span>
                                              </div>
                                            </div>
                                        </div>
                                      </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row c-checkout rounded-3 tab-pane" id="kalaInfo" style="background-color:#f5f5f5; width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                            <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                <span class="card p-4">
                                    <div class="row">
                                        <div class="col-md-2 col-sm-6">
                                            <div class="counter orange">
                                                <div class="counter-icon">
                                                    <i class="fas fa-clipboard-list"></i>
                                                </div>
                                                <h3>کالا های موجود</h3>
                                                <span class="counter-value">{{$allGoods}}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-6">
                                            <div class="counter blue">
                                                <div class="counter-icon">
                                                    <i class="fas fa-dolly-flatbed"></i>
                                                </div>
                                                <h3> کالا های قابل پیش خرید </h3>
                                                <span class="counter-value">{{$prebuyableGoods}}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-6">
                                            <div class="counter">
                                                <div class="counter-icon">
                                                    <i class="fas fa-cart-arrow-down"></i>
                                                </div>
                                                <h3>کالاهای خریداری شده  </h3>
                                                <span class="counter-value">{{$allboughtGoods}}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-6">
                                            <div class="counter">
                                                <div class="counter-icon">
                                                    <i class="fa fa-shopping-basket"></i>
                                                </div>
                                                <h3>کالاهای خریداری نشده  </h3>
                                                <span class="counter-value">{{$allGoods-$allboughtGoods}}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-6">
                                            <div class="counter orange">
                                                <div class="counter-icon">
                                                    <i class="fas fa-trophy"></i>
                                                </div>
                                                <h3>برندها</h3>
                                                <span class="counter-value">{{$allBrands}}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-6">
                                            <div class="counter orange">
                                                <div class="counter-icon">
                                                    <i class="fas fa-trophy"></i>
                                                </div>
                                                <h3>کالاهای مشمول برندها</h3>
                                                <span class="counter-value">{{$allBrandGoods}}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-6">
                                            <div class="counter blue">
                                                <div class="counter-icon">
                                                    <i class="fas fa-gem"></i>
                                                </div>
                                                <h3>گروهای اصلی </h3>
                                                <span class="counter-value">{{$allmainGroup}}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-6">
                                            <div class="counter blue">
                                                <div class="counter-icon">
                                                    <i class="fa fa-object-group"></i>
                                                </div>
                                                <h3>گروهای فرعی</h3>
                                                <span class="counter-value">{{$allSubGroups}}</span>
                                            </div>
                                        </div>
                                    </div>
                                  </span>
                            </div>
                       </div>
                    <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="background-color:#f5f5f5; width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                        <div class="row " style="width:98%; padding:0 1% 2% 0%">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-12 card">
                                        <div id="pieChartdiv"></div>
                                    </div>
                                    <div class="col-lg-8 col-md-8 col-sm-12 card">
                                        <div id="chartdiv"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row c-checkout rounded-3 tab-pane" id="siteAdmin" style="background-color:#f5f5f5; width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                        <div class="row " style="width:98%; padding:0 1% 2% 0%">
                            <div class="container">
                                <div class="main-body">
                                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 gutters-sm">
                                      
                                      @foreach($admins as $admin)

                                        <div class="col mb-3" style="mim-height:333px; max-height:411px;">
                                            <div class="card" style="background-color: #f1efef;">
                                                <img src="/resources/assets/images/banner.png" alt="Cover" class="card-img-top" style="height:80px;">
                                                <div class="card-body text-center">
                                                    <img src="{{url('resources/assets/images/admins/'.$admin->adminId.'.jpg')}}" style="width:100px;height:100px;margin-top:-60px" alt="User" class="img-fluid img-thumbnail rounded-circle border-0 mb-3">
                                                    <h5 class="card-title">{{trim($admin->name).' '.trim($admin->lastName)}}</h5>
                                                    <p class="text-secondary mb-1">نقش : {{trim($admin->adminType)}}</p>
                                                    <p class="text-muted font-size-sm"> آدرس: {{trim($admin->address)}}</p>
                                                    <p class="text-muted font-size-sm"> شماره تماس: {{trim($admin->phone)}}</p>
                                                    <p class="text-muted font-size-sm"> تعداد مشتری: @if($admin->countCustomer){{$admin->countCustomer}} @else 0 @endif</p>
                                                </div>
                                            </div>
                                        </div>
                                      @endforeach
                                      
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
