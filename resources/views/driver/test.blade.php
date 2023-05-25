@extends('layout')
@section('content')
    <div class="container-fluid containerDiv">
        <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                <fieldset class="border rounded mt-5 sidefieldSet">
                    <legend  class="float-none w-auto legendLabel mb-0"> تعیین محدوه پخش </legend>
                     <div class="col-lg-12 sideBarBottomBtn">
                         
                            <button type="button" class="btn w-50 btn-sm  btn-primary" id="addSaleLineBtn"> افزودن <i class="fa fa-plus"></i> </button>
                           
                    </div>
                </fieldset>
            </div>
            <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                <div class="row contentHeader"> </div>
                <div class="row mainContent">
                    
                </div>
                <div class="row contentFooter"></div>
            </div>
        </div>
    </div>
    @endsection