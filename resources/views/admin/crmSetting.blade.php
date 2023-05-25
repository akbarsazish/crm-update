@extends('layout')
@section('content')
<style>

    input[type="checkbox"]{
        padding:8px;
        margin-top:10px;
    }
</style>

<div class="container" style="margin-top:6%;">
    <h3 class="page-title">تنظیمات سیستم </h3>
    <div class="c-checkout container" style="    background: linear-gradient(#85baef, #116bc7, #0b2d62); padding:0.5% !important; border-radius:10px 10px 2px 2px;">
        <div class="col-sm-8" style="margin: 0; padding:0;">
            <ul class="header-list nav nav-tabs" data-tabs="tabs" style="margin: 0; padding:0;">
                <li><a class="active" data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#custAddress"> فاصله تا مقصد برای ثبت کامنت </a></li>
                <li><a data-toggle="tab" style="color:black; font-size:14px; font-weight:bold;"  href="#moRagiInfo">  سطح دسترسی کاربران</a></li>
            </ul>
        </div>
        <div class="c-checkout tab-content" style="background-color:#f5f5f5; margin:0;  padding:0.3%; border-radius:10px 10px 2px 2px;">
                <div class="row c-checkout rounded-3 tab-pane active" id="custAddress" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                    <div class="col-sm-12">
                        <div class="form-check form-check-inline">
                            <label class="dashboardLabel form-check-label" for="inlineCheckbox1"> 10 متر</label> &nbsp;
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">

                          </div>
                          <div class="form-check form-check-inline">
                            <label class="dashboardLabel form-check-label" for="inlineCheckbox2">50 متر </label> &nbsp;
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">

                          </div>
                          <div class="form-check form-check-inline">
                            <label class="dashboardLabel form-check-label" for="inlineCheckbox3">100 متر </label> &nbsp;
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3">
                          </div>
                          <div class="form-check form-check-inline">
                            <label class="dashboardLabel form-check-label" for="inlineCheckbox3">150 متر </label> &nbsp;
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3">
                          </div>
                          <div class="form-check form-check-inline">
                            <label class="dashboardLabel form-check-label" for="inlineCheckbox3">200 متر </label> &nbsp;
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3">
                          </div>
                          <div class="form-check form-check-inline">
                            <label class="dashboardLabel form-check-label" for="inlineCheckbox3">250 متر </label> &nbsp;
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3">
                          </div>
                          <div class="form-check form-check-inline">
                            <label class="dashboardLabel form-check-label" for="inlineCheckbox3">300 متر </label> &nbsp;
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3">
                          </div>
                          <div class="form-check form-check-inline">
                            <label class="dashboardLabel form-check-label" for="inlineCheckbox3">350 متر </label> &nbsp;
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3">
                          </div>
                          <div class="form-check form-check-inline">
                            <label class="dashboardLabel form-check-label" for="inlineCheckbox3">400 متر </label> &nbsp;
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3">
                          </div>
                          <div class="form-check form-check-inline">
                            <label class="dashboardLabel form-check-label" for="inlineCheckbox3">450 متر </label> &nbsp;
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3">
                          </div>
                          <div class="form-check form-check-inline">
                            <label class="dashboardLabel form-check-label" for="inlineCheckbox3">500 متر </label> &nbsp;
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3">
                          </div>
                          <div class="form-check form-check-inline">
                            <div class="form-group">
                                <label for="distance"> فاصله دلخواه </label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            <div class="row c-checkout rounded-3 tab-pane" id="moRagiInfo" style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                <div class="row c-checkout rounded-3 tab-pane active"  style="width:99%; margin:0 auto; padding:1% 0% 0% 0%">
                    <div class="col-sm-12">
                    <!-- <label class="dashboardLabel form-label">  سطح دسترسی کاربران </label> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
