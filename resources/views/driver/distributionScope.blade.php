@extends('layout')
@section('content')
    <div class="container-fluid containerDiv">
        <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                <fieldset class="border rounded mt-5 sidefieldSet">
                    <legend  class="float-none w-auto legendLabel mb-0"> اطلاعات موقعیت مکانی </legend>
                     <div class="col-lg-12">
                        <div class="form-group col-sm-12">
                            <label class="dashboardLabel form-label"> جستجو</label>
                            <input type="text" name="nameCode" size="20" class="form-control form-control-sm" id="searchAllCName">
                        </div>
                        <div class="form-group col-sm-12 mt-1">
                            <label for="" class="form-label mb-0"> استانها   </label>
                            <select class="form-select form-select-sm" name="mantaghehName" id="AllByAdmin">
                                <option value=""> همه</option>
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12 mt-1">
                            <label for="" class="form-label mb-0"> شهرستان    </label>
                            <select class="form-select form-select-sm" name="mantaghehName" id="AllByAdmin">
                                <option value=""> همه</option>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                <div class="row contentHeader"> 
                    <div class="leftPart text-start">
                        <button type="button" class="btn btn-sm  btn-primary" id="addingScopeInfoBtn"> جدید <i class="fa fa-plus"></i> </button>
                        <button type="button" class="btn btn-sm  btn-primary" id="defineNewDistributionScope"> ویرایش  <i class="fa fa-edit"></i> </button>
                        <button type="button" class="btn btn-sm  btn-danger" id="defineNewDistributionScope"> حذف <i class="fa fa-trash"></i> </button>
                        <button type="button" class="btn btn-sm  btn-primary" id="defineNewDistributionScope"> بازگشت <i class="fa fa-history"></i> </button>
                    </div>
                </div>
                <div class="row mainContent">
                      <table class="select-highlight table table-bordered table-striped" id="">
                            <thead class="tableHeader">
                                <tr>
                                    <th>ردیف</th>
                                    <th> نام   </th>
                                    <th> استان   </th>
                                    <th style=" width:122px"> شهرستان </th>
                                </tr>
                            </thead>
                            <tbody class="tableBody" id=""> 
                                <tr>
                                    <td>1</td>
                                    <td> منطقه2 مسیر 1 </td>
                                    <td> تهران </td>
                                
                                    <td style="width: 116px"> تهران </td>
                                </tr>
                            </tbody>
                        </table>
                </div>
                <div class="row contentFooter"></div>
            </div>
        </div>
    </div>


  <!-- modal for adding information location -->
      <div class="modal fade dragableModal" id="addingScopeInfoModal"  tabindex="-1"   data-bs-backdrop="static" >
            <div class="modal-dialog modal-dialog-scrollable  modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header py-2 text-white">
                           <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                         <h5 class="modal-title" id="exampleModalLabel"> اطلاعات محدوده  </h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <label for="exampleInputEmail1" class="form-label"> نام دلخواه  </label>
                                <input type="text" class="form-control" name="" aria-describedby="emailHelp">
                            </div>
                            <div class="col-lg-6">
                                   <label for="exampleFormControlTextarea1" class="form-label"> مساحت  </label>
                                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group col-sm-12 mt-1">
                                   <label for="" class="form-label mb-0"> استانها   </label>
                                    <select class="form-select form-select-sm" name="mantaghehName" id="AllByAdmin">
                                        <option value=""> همه</option>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 ">
                                <div class="row">
                                <div class="form-group col-lg-9 mt-1">
                                    <label for="" class="form-label mb-0"> شهرستان    </label>
                                    <select class="form-select form-select-sm" name="mantaghehName" id="AllByAdmin">
                                        <option value=""> همه</option>
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-3 mt-1">
                                <label for="" class="form-label mb-0">    </label>
                                     <i class="fa fa-circle-plus fa-lg" style="font-size:33px; color:blue; margin-top:33px; cursor:pointer"></i>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2"> 
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-primary"> تعیین موقعیت مکانی  <i class="fa fa-map-marker "></i> </button>
                            </div>
                         </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"> بستن <i class="fa fa-xmark"></i> </button>
                        <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save "></i> </button>
                    </div>
                </div>
            </div>
        </div>
    @endsection