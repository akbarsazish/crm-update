@extends('layout')
@section('content')
<style>
    .fa-dashboard:hover{
        color:rgb(251, 162, 54)
    }
 
    #chartdiv {
    width: 100%;
    height: 500px;
    text-align: center;
    }

    #ohclChart {
      width: 100%;
      height: 500px;
      max-width: 100%;
      text-align: right;
    }
	
#waitToDashboard {
	margin:0 auto;
	padding:20px;
	}
.amalKardGrid {
  display: grid;
  grid-template-columns: auto auto auto;
  padding: 5px;
}
.amalkard-item {
  padding: 8px;
  font-size: 14px;
  text-align: center;
  border-radius:6px;
 background-color:#b6d5f3;
 margin:5px;
text-align:right;
}
	
</style>
<main>

<div class="container" style="margin-top:5%;">
        <h3 class="page-title"> عملکرد کاربران </h3>
		
    <div class="spinner-border text-primary" role="status" id="waitToDashboard" style="display:none;">
          <span class="visually-hidden">Loading...</span>
    </div>
    <div class="card mb-4" style="margin: 0; padding:0;">
        <div class="card-body">
            <div class="row">
                 <div class="col-lg-9">
                    <div class="row">
                      <div class="form-group col-lg-2">
                          <input type="text" name="" placeholder="نام" class="form-control publicTop" id="searchAdminNameCode"/>
                      </div>
                      <div class="form-group col-lg-2">
                          <select class="form-select publicTop" id="searchAdminGroup">
                              <option value="-1" hidden>گروه بندی</option>
                              <option value="0">همه</option>
                              @foreach ($adminTypes as $element)

                                  <option value="{{$element->id}}">{{$element->adminType}}</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="form-group col-lg-2">
                          <select class="form-select publicTop" id="searchAdminActiveOrNot">
                              <option value="-1" hidden>فعال</option>
                              <option value="0">همه</option>
                              <option value="1">فعال</option>
                              <option value="2"> غیر فعال</option>
                          </select>
                      </div>
                    <div class="form-group col-lg-2">
                      <select class="form-select publicTop" id="searchAdminFactorOrNot">
                        <option value="-1" hidden>فاکتور</option>
                        <option value="0">همه</option>
                        <option value="1">دارد</option>
                        <option value="2">ندارد</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-2">
                      <select class="form-select publicTop" id="searchAdminLoginOrNot">
                        <option value="-1" hidden>ورود مشتری</option>
                        <option value="0">همه</option>
                        <option value="1">بله</option>
                        <option value="2">خیر</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-2">
                      <select class="form-select publicTop" id="searchAdminCustomerLoginOrNot">
                        <option value="-1" hidden>ورود ادمین</option>
                        <option value="0">همه</option>
                        <option value="1">بله</option>
                        <option value="2">خیر</option>
                      </select>
                    </div>
                  </div>
                </div>
              <div class="col-lg-3 text-start">
                      <input type="text" name="" id="adminSn" style="display: none">
                      <button class='enableBtn btn-sm btn btn-primary mx-1 text-warning' id="openkarabarDashboard"
                        type="button">عملکرد <i class="fas fa-balance-scale fa-lg"></i></button>
                      <button class='enableBtn btn-sm btn btn-primary mx-1 text-warning' id="chart" type="button" data-toggle="modal" data-bs-target="#karbarChart">نمودار عملکرد <i class="fas fa-bar-chart fa-lg"></i></button>
              </div>
            </div>
        <div class="row">
        <div class="col-sm-12">
                    <div class="well" style="margin-top:2%;">
                        <div class="c-checkout container p-1 pb-4 rounded-3">
                            <div class="col-sm-12 " style="padding:0; padding-left:25px;  margin-top: 0;">
                                <table class="table table-bordered table-hover table-striped" id="tableGroupList" style="width:100%;">
                                    <thead class="tableHeader">
                                        <tr>
                                            <th>ردیف</th>
                                            <th>نام کاربر</th>
                                            <th>نقش کاربری</th>
                                            <th class="descriptionForMobile">توضیحات</th>
                                            <th>انتخاب</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tableBody" id="adminList">
                                        @foreach ($admins as $admin)
                                            <tr onclick="setAdminStuffForAdmin(this)">
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$admin->name." ".$admin->lastName}}</td>
                                                <td>{{$admin->adminType}}</td>
                                                <td class="descriptionForMobile"></td>
                                                <td>
                                                    <input class="mainGroupId" type="radio" name="AdminId[]" value="{{$admin->id.'_'.$admin->adminTypeId}}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table> 
                                <br>
                                <h3 class="page-title"> عملکرد روزانه  <span id="adminName" style="display: none;"></span> </h3>
                                <br>
                                <div class="row">
									
                                    <span class='row c-checkout container p-1 p-2  rounded-3' style="margin: 0; border:1px solid rgb(223, 211, 211);">
                                        <div class="col-lg-3 col-md-3 col-sm-6">
                                            <p>ساعت ورود: <span id="loginTimeToday"></span></p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6">
                                            <p>تعداد کامنت های امروز: <span id="countCommentsToday"></span></p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6">
                                            <p>تعداد فاکتور های امروز: <span id="countFactorsToday"></span></p>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6">
                                            <p>تعداد مشتریان: <span id="countCustomersToday"></span></p>
                                        </div>
                                    </span>
                                </div> <br>

                                <table class="table table-bordered table-hover table-sm" id="tableGroupList">
                                    <thead class="tableHeader">
                                        <tr>
                                            <th>ردیف</th>
                                            <th>نام مشتری</th>
                                            <th>ساعت کامنت </th>
                                            <th>تعداد فاکتور</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tableBody" id="adminCustomers">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- modal for karabarn action  --}}
<div class="modal fade dragableModal" id="karbarAction" data-bs-keyboard="false"  data-bs-backdrop="static"  aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close" style="display: inline; background-color:red;"></button>
                <h5 class="modal-title" style="text-align: center;">عملکرد <span id="adminNameModal"></span></h5>
            </div>
            <div class="modal-body">
                    <div class="row rounded-5 shadow bg-light">
                            <div class="col-lg-9 col-md-9 col-sm-12">
								<div class="amalKardGrid">
								  <div class="amalkard-item"> تاریخ تخصیص مشتری : <span id="assignCustomerDate" > </span>  </div>
								  <div class="amalkard-item"> تعداد مشتری های خرید کرده : <span id="countCustomer"> </span>  </div>  
								  <div class="amalkard-item"> کل فاکتور فروش : <span id="countFactors"> </span>  </div>
								  <div class="amalkard-item"> جمع کل فروش : <span id="allMoneyFactor"> </span>  </div>
								  <div class="amalkard-item"> فاکتور های برگشتی : <span id="countReturnedFactor"> </span>  </div>  
								  <div class="amalkard-item"> مبلغ فاکتور های برگشتی : <span id="allMoneyReturnedFactor" > </span>  </div>
								  <div class="amalkard-item"> روزهای که وارد CRM نشده : <span id="notlogedIn" > </span>  </div>
									<div class="amalkard-item"> فاکتور های ماه قبل این کارتابل : <span id="lastMonthAllFactorMoney" > </span>  </div>
									<div class="amalkard-item" style="font-weight: bold; color:red;">   فاکتور های برگشتی ماه قبل این کارتابل :  <span id="lastMonthAllFactorMoneyReturned" > </span></div>
								</div>
								    <h6 class="text-primary">عملکرد مشتریان تخصیصی در ماه قبل </h6>
								   <table class="table table-bordered table-striped" >
									   <thead style="background: linear-gradient(#b6d5f3, #b6d5f3, #b6d5f3) !important; color:#000 ! important;">
										 <tr>
										   <th> ردیف </th>
										   <th style="width:133px">تعداد مشتری</th>
										   <th>فاکتورها</th>
										   <th>مبلغ فاکتورها </th>
										   <th>برگشتی </th>
										   <th style="width:133px">جمع کل مبلغ </th>
										 </tr>
									   </thead>
									   <tbody id="lastMonthActions">
									   </tbody>
									 </table>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12">
                                <label class="dashboardLabel form-label">یاداشت  </label>
                                   <textarea style="background-color:blanchedalmond" class="form-control" id="comment" rows="3" style="background-color:#b6d5f3" ></textarea>
                                   <label class="dashboardLabel form-label"> توضیحات  </label>
                                   <textarea style="background-color:blanchedalmond" class="form-control" id="comment" rows="4" style="background-color:#b6d5f3" disabled></textarea>
                            </div>
                        </div> <hr>

                <div class="c-checkout container" style="background-color:#c5c5c5; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                    <div class="col-sm-3">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs">
                            <li><a class="active" data-toggle="tab" style="font-size:16px; font-weight:bold; color:#000;"  href="#custAddress"> تاریخچه عملکرد </a></li>
                        </ul>
                    </div>
                    <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                            <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped table-sm" style="text-align:center;">
                                        <thead class="tableHeader">
                                        <tr>
                                            <th> ردیف</th>
                                            <th> تعداد مشتری  </th>
                                            <th> خرید کردها  </th>
                                            <th>  تعداد فاکتور فروش </th>
                                            <th>  مبلغ برگشتی  </th>
                                            <th>  خالص کل فاکتور فروش  </th>
                                            <th>خالص خرید ماه قبلی مشتریان </th>
                                            <th>میانگین رشد  </th>
                                            <th>م بدون کامنت </th>
                                            <th>ک انجام نشده </th>
                                            <th>کامنت </th>
                                        </tr>
                                        </thead>
                                        <tbody id="factorTable" class="tableBody">
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

<div class="modal fade" id="readDiscription" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-scrollable modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <div class="modal-body" style="background-color: #d2e9ff;">
            <h3 id="discription"></h3>
        </div>
    </div>
</div>

{{-- modal for karabarn action  --}}
<div class="modal fade" id="karbarChart" data-bs-keyboard="false"  data-bs-backdrop="static"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" >
        <div class="modal-content"  style="background-color:#d4d4d4;">
            <div class="modal-header" style="border-bottom:1px solid rgb(7, 42, 214);">
                <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close" style="display: inline; background-color:red;"></button>
                <h5 class="modal-title" style="text-align: center;">نمودار عملکرد {{$admin->name." ".$admin->lastName}}</h5>
            </div>

            <div class="modal-body"  style="background-color:#d4d4d4;;">
                <div class="c-checkout container-fluid" style="background-image: linear-gradient(to right, #ffffff,#3fa7ef,#3fa7ef); margin:0.2% 0; margin-bottom:0; padding:0.5% !important; border-radius:10px 10px 2px 2px;">
                    <div class="col-sm-6" style="margin: 0; padding:0;">
                        <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                            <li><a class="active" data-toggle="tab" style="color:black;"  href="#siteAdmin"> عملکرد ماهای قبل </a></li>
                            <li><a data-toggle="tab" style="color:black;"  href="#moRagiInfo">  عملکرد کاربران   </a></li>
                            <li><a data-toggle="tab" style="color:black;"  href="#kalaInfo"> عملکرد </a></li>
                        </ul>
                    </div>
                        <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="background-color:#f5f5f5; width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                             <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-12 fs-6">
                                                <div class="row mt-1">
                                                    <span style="width:30px; height:30px; background-color:#67b7dc; margin-right:11px;"></span> &nbsp;  عملکرد {{$admin->name." ".$admin->lastName}}
                                                </div> <br>
                                                <div class="row">
                                                    <span style="width:30px; height:30px; background-color:#6794dc;  margin-right:11px;"></span>  &nbsp; عملکرد کاربران دیگر
                                                </div>
                                            </div>
                                            <div class="col-lg-8 col-md-8 col-sm-12 card">
                                                <div id="chartdiv"></div>
                                            </div>
                                        </div>
                                    </div>
                             </div>
                        </div>
                          <div class="row c-checkout rounded-3 tab-pane active" id="siteAdmin" style="background-color:#f5f5f5; width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                             <div class="row " style="width:98%; padding:0 1% 2% 0%">
                                 <div class="col-lg-3 col-md-3 col-sm-3"></div>
                                   <div class="col-lg-9 col-md-9 col-sm-9">
                                     <div id="ohclChart"></div>
                                  </div>
                             </div>
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
<!-- 
<link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet" />
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.12.1/sorting/persian.js"></script> -->


<!-- Chart code
<script>
    am5.ready(function() {

    // Create root element
    var root = am5.Root.new("chartdiv");
    root._logo.dispose();

    // Set themes
    root.setThemes([
      am5themes_Animated.new(root)
    ]);

    // Create chart
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
      panX: true,
      panY: false,
      wheelX: "panX",
      wheelY: "zoomX",
      layout: root.verticalLayout
    }));

    // Add scrollbar
    chart.set("scrollbarX", am5.Scrollbar.new(root, {
      orientation: "horizontal"
    }));

    var data = [{
      "country": "کاربر فعلی",
      "year2004": 3.5,
      "year2005": 4.2
    }, {
      "country": "دیگر کاربران",
      "year2004": 1.7,
      "year2005": 3.1
    }, {
      "country": "کابرفعلی",
      "year2004": 2.8,
      "year2005": 2.9
    }, {
      "country": "کاربران دیگر ",
      "year2004": 2.6,
      "year2005": 2.3
    }, {
      "country": "کاربرفعلی ",
      "year2004": 1.4,
      "year2005": 2.1
    }, {
      "country": "کاربران دیگر",
      "year2004": 2.6,
      "year2005": 4.9
    }];

    // Create axes
    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
      categoryField: "country",
      renderer: am5xy.AxisRendererX.new(root, {}),
      tooltip: am5.Tooltip.new(root, {
        themeTags: ["axis"],
        animationDuration: 200
      })
    }));

    xAxis.data.setAll(data);

    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
      min: 0,
      renderer: am5xy.AxisRendererY.new(root, {})
    }));

    // Add series

    var series0 = chart.series.push(am5xy.ColumnSeries.new(root, {
      name: "Income",
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: "year2004",
      categoryXField: "country",
      clustered: false,
      tooltip: am5.Tooltip.new(root, {
        labelText: "2004: {valueY}"
      })
    }));

    series0.columns.template.setAll({
      width: am5.percent(80),
      tooltipY: 0
    });


    series0.data.setAll(data);


    var series1 = chart.series.push(am5xy.ColumnSeries.new(root, {
      name: "Income",
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: "year2005",
      categoryXField: "country",
      clustered: false,
      tooltip: am5.Tooltip.new(root, {
        labelText: "2005: {valueY}"
      })
    }));

    series1.columns.template.setAll({
      width: am5.percent(50),
      tooltipY: 0,

    });

    series1.data.setAll(data);

    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));


    // Make stuff animate on load
    chart.appear(1000, 100);
    series0.appear();
    series1.appear();

    }); // end am5.ready()

    </script>






{{-- script of OHCL Chart --}}

<script>
    am5.ready(function() {
    // Create root element
    var root = am5.Root.new("ohclChart");
    root._logo.dispose();

    // Set themes
    root.setThemes([
      am5themes_Animated.new(root)
    ]);

    function generateChartData() {
      var chartData = [];
      var firstDate = new Date();
      firstDate.setDate(firstDate.getDate() - 1000);
      firstDate.setHours(0, 0, 0, 0);
      var value = 1200;
      for (var i = 0; i < 5000; i++) {
        var newDate = new Date(firstDate);
        newDate.setDate(newDate.getDate() + i);

        value += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
        var open = value + Math.round(Math.random() * 16 - 8);
        var low = Math.min(value, open) - Math.round(Math.random() * 5);
        var high = Math.max(value, open) + Math.round(Math.random() * 5);

        chartData.push({
          date: newDate.getTime(),
          value: value,
          open: open,
          low: low,
          high: high,
        });
      }
      return chartData;
    }

    var data = generateChartData();

    // Create chart
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
      focusable: true,
      panX: true,
      panY: true,
      wheelX: "panX",
      wheelY: "zoomX"
    }));


    // Create axes
    var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
      maxDeviation:0.5,
      groupData: true,
      baseInterval: { timeUnit: "day", count: 1 },
      renderer: am5xy.AxisRendererX.new(root, {pan:"zoom"}),
      tooltip: am5.Tooltip.new(root, {})
    }));

    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
      maxDeviation:1,
      renderer: am5xy.AxisRendererY.new(root, {pan:"zoom"})
    }));


    var color = root.interfaceColors.get("background");

    // Add series
    var series = chart.series.push(am5xy.OHLCSeries.new(root, {
      fill: color,
      calculateAggregates: true,
      stroke: color,
      name: "CRM",
      xAxis: xAxis,
      yAxis: yAxis,
      valueYField: "value",
      openValueYField: "open",
      lowValueYField: "low",
      highValueYField: "high",
      valueXField: "date",
      lowValueYGrouped: "low",
      highValueYGrouped: "high",
      openValueYGrouped: "open",
      valueYGrouped: "close",
      legendValueText: "open: {openValueY} low: {lowValueY} high: {highValueY} close: {valueY}",
      legendRangeValueText: "{valueYClose}",
      tooltip: am5.Tooltip.new(root, {
      pointerOrientation: "horizontal",
    labelText: "open: {openValueY}\nlow: {lowValueY}\nhigh: {highValueY}\nclose: {valueY}"
      })
    }));


    // Add cursor
    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
      xAxis: xAxis
    }));
    cursor.lineY.set("visible", false);

    // Stack axes vertically
    chart.leftAxesContainer.set("layout", root.verticalLayout);

    // Add scrollbar
    var scrollbar = am5xy.XYChartScrollbar.new(root, {
      orientation: "horizontal",
      height: 50
    });
    chart.set("scrollbarX", scrollbar);

    var sbxAxis = scrollbar.chart.xAxes.push(am5xy.DateAxis.new(root, {
      groupData: true,
      groupIntervals: [{ timeUnit: "week", count: 1 }],
      baseInterval: { timeUnit: "day", count: 1 },
      renderer: am5xy.AxisRendererX.new(root, {
        opposite: false,
        strokeOpacity: 0
      })
    }));

    var sbyAxis = scrollbar.chart.yAxes.push(am5xy.ValueAxis.new(root, {
      renderer: am5xy.AxisRendererY.new(root, {})
    }));

    var sbseries = scrollbar.chart.series.push(am5xy.LineSeries.new(root, {
      xAxis: sbxAxis,
      yAxis: sbyAxis,
      valueYField: "value",
      valueXField: "date"
    }));

    // Add legend
    var legend = yAxis.axisHeader.children.push(
      am5.Legend.new(root, {})
    );

    legend.data.push(series);

    legend.markers.template.setAll({
      width: 10
    });

    legend.markerRectangles.template.setAll({
      cornerRadiusTR: 0,
      cornerRadiusBR: 0,
      cornerRadiusTL: 0,
      cornerRadiusBL: 0
    });

    series.data.setAll(data);
    sbseries.data.setAll(data);

    // Make stuff animate on load
    series.appear(1000);
    chart.appear(1000, 100);

    }); // end am5.ready()
    </script> -->
@endsection