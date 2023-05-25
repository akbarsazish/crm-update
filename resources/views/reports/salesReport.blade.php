@extends('layout')
@section('content')

<style>
   
        .grid-container {
            display: grid;
            grid-template-columns: auto auto;
            gap: 2px;
            padding: 5px;
            height:55px;
            margin:2px;
            }

        .grid-container > div {
            text-align: center;
            font-size: 14px;
            font-weight:bold;
            text-align:right;
            padding:2px;
			background-color:#bad5ef;
			border-radius:6px;
            }
</style>

<div class="container-fluid containerDiv">
    <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                <fieldset class="border rounded sidefieldSet">
                    <legend  class="float-none w-auto legendLabel mb-0">  گزارشات فروش  </legend>
                    <form action="{{url('/getSalesReportInfo')}}" method="get" id='salesReportForm'>
                        <div class="form-group col-sm-12">
                            <label class="dashboardLabel form-label"> جستجو</label>
                            <input type="text" name="nameCode" size="20" class="form-control form-control-sm" id="searchAllCName">
                        </div>
                        <div class="form-group col-sm-12 mb-1">
                            <label for="" class="form-label"> شهر  </label>
                            <select class="form-select form-select-sm" name="CityName" id="searchByCitySalesRep">
                                <option value=""> همه</option>
                                @foreach($cities as $city)
                                <option value="{{$city->SnMNM}}">{{$city->NameRec}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12 mb-1">
                            <label for="" class="form-label"> منطقه  </label>
                            <select class="form-select form-select-sm" name="mantaghehName" id="searchByMantagheh">
                                <option value=""> همه</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12 mb-1">
                            <input type="text" name="firstDate" placeholder="از تاریخ" class="form-control form-control-sm" id="firstDateReturned" autocomplete="off">
                        </div>
                        <div class="form-group col-sm-12 mb-2">
                            <input type="text" name="secondDate" placeholder="تا تاریخ" class="form-control form-control-sm" id="secondDateReturned" autocomplete="off">
                        </div>
                    
                        <div class="form-group col-sm-12 mb-1">
                            <label for="" class="form-label">پشتیبان</label>
                            <select class="form-select form-select-sm" name="adminName" id="AllByAdmin">
                                <option value=""> همه</option>
                                <option value="00000000000000">بدون پشتیبان</option>
                                @foreach($poshtibans as $poshtiban)
                                <option value="{{$poshtiban->name}}">{{$poshtiban->name.' '.$poshtiban->lastName}}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-md btn-primary" Type="submit">بازخوانی</button>
                    </form>
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
                                    <th>مشتری</th>
                                    <th>تاریخ</th>
                                    <th>مبلغ خرید(تومان) </th>
                                    <th style="width:122px"> کد مشتری</th>
                                </tr>
                            </thead>
                            <tbody class="tableBody" id="salesReportList">
                            </tbody>
                        </table>
                        <div class="grid-container">
                            <div class="item1"> <b> مجموع فاکتور   :  </b> <span id="customersMoney">  </span> </div>
                            <div class="item2"> <b>  تعداد فاکتور  :  </b> <span id="customersCountFactor"> </span>    </div>
                        </div>
                       
                </div>
                <div class="row contentFooter">
                     <div class="col-lg-12 text-start">
                        <button type="button" class="btn btn-sm btn-primary footerButton"> امروز  : </button>
                        <button type="button" class="btn btn-sm btn-primary footerButton"> دیروز : </button>
                        <button type="button" class="btn btn-sm btn-primary footerButton"> صد تای آخر : 100</button>
                        <button type="button" class="btn btn-sm btn-primary footerButton"> همه : </button>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection