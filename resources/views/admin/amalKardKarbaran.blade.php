@extends('layout')
@section('content')

<style>
.grid-amalKard {
    display: grid;
    grid-template-columns: auto auto auto auto auto;
    margin-bottom:5px;
}
.amalKarItem {
    background-color:#b3d1ef;
    padding: 3px;
    font-size: 14px;
    text-align: center;
    border-radius:5px;
    margin:2px;
}

.today{
   color:red;
}

.amalKardContent {
    border: 1px solid #b3d1ef;
    border-radius:8px; 
    padding:5px;
    margin-bottom:15px;
}

#chartdiv12 {
  width: 100%;
  height:100%;
  text-align:center;
  direction:ltr;
}

</style>

    <div class="container-fluid containerDiv">
      <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 sideBar amalkardKarbaranSidebar">
                   <fieldset class="border rounded">
                        <legend  class="float-none w-auto legendLabel mb-0">  عملکرد کاربران </legend>
                            <div class="row mt-2">
                              @if(hasPermission(Session::get("asn"),"trazEmployeeReportN") > 0)
                                <div class="form-group col-sm-12">
                                    <select class="form-select form-select-sm" id="searchManagerByLine">
                                        <option value="-1" hidden>  خطوط  </option>
                                        @foreach($saleLine as $line)
                                          <option value="{{$line->SaleLineSn}}"> {{$line->LineName}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            
                                <div class="form-group col-sm-12 mt-1">
                                    <select class="form-select form-select-sm" id="searchManagerSelect">
                                         <option value="-1" hidden>  مدیران   </option>
                                          @foreach($admins as $admin)
                                            <option value="{{$admin->id}}">{{$admin->name.' '.$admin->lastName}}</option>
                                          @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                            @if(hasPermission(Session::get("asn"),"trazEmployeeReportN") > 1)
                               <button class='btn btn-sm btn-primary text-warning w-75' type="button" id='openDashboard'> تراز نامه  <i class="fal fa-dashboard"></i></button>
                            @endif
                          </fieldset>
                  </div>
                <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                    <div class="row contentHeader"></div>
                    <div class="row mainContent"> 

                        <div class="col-lg-12">
                              <div id="chartdiv12"></div>
                        </div>

                    </div>
                    <div class="row contentFooter"> </div>
                </div>
        </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="amalKardModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="amalKardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header py-2 ">
          <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
          <h5 class="modal-title" id="amalKardModalLabel"> عملکرد  </h5>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-lg-10">
                <div class="grid-amalKard">
                    <div class="amalKarItem today"> نصب امروز </div>
                    <div class="amalKarItem today"> اقلام امروز </div>
                    <div class="amalKarItem today"> فاکتور امروز </div>  
                    <div class="amalKarItem today"> خرید اولیه امروز</div>
                    <div class="amalKarItem"> نصب ها   </div>
                    <div class="amalKarItem"> اقلامها </div>  
                    <div class="amalKarItem"> فاکتورها </div>
                    <div class="amalKarItem"> خریدهای اولیه </div>
                    <div class="amalKarItem"> کل امتیاز (آذر) </div>  
                    <div class="amalKarItem"> تاریخچه عملکرد </div>  
                </div>
            </div>
                <div class="col-lg-2 p-0 m-0"> 
                <button class="btn btn-sm btn-primary" type="button">  امتیاز <i class="fa fa-rocket"></i></button>
                <button class="btn btn-sm btn-primary" type="button">  تسویه <i class="fas fa-balance-scale"></i> </button>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"> بستن <i class="fa fa-xmark"></i> </button>
        <button type="button" class="btn btn-sm btn-primary">دخیره <i class="fa fa-save"></i> </button>
      </div>
    </div>
  </div>
</div>



@endsection


