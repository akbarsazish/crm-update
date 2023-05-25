@extends('layout')
@section('content')
<style>
.targetCheck{
    width:22px;
    height:22px;
    border-radius:50%;
}
.targetLabel {
    margin-top:5px;
}
</style>
<div class="container" style="margin-top:80px;">
        
        <div class="row px-5">
            <fieldset class="rounded" style="min-height:390px;">
                <legend  class="float-none w-auto"> تارگیت ها  </legend>
                <input type="hidden" name="" id="selectTargetId">
                  <div class="row">
                          <div class="col-lg-3 col-md-3 col-sm-3">
                                <select class="form-select" aria-label="Default select example" id="selectTarget">
                                @foreach($targets as $target)
                                  <option value="{{$target->id}}">{{$target->BaseName}}</option>
                                @endforeach
                                </select>
                          </div>
                          <div class="col-lg-1 col-md-1 col-sm-1 mt-3">
                              <span data-toggle="modal" data-target="#addingTargetModal"><i class="fa fa-plus-circle fa-lg" style="color:#1684db; font-size:33px"></i></span>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2">
                               <button class='btn btn-primary text-warning' id="targetEditBtn" type="button" disabled  data-toggle="modal" style="margin-top:-3px;">ویرایش  تارگت <i class="fa fa-edit fa-lg"></i></button> 
                          </div>
                   </div>
                   <div class="row px-2 targetTable">
                              <table class="table table-bordered border-secondary">
                                <thead class="tableHeader">
                                  <tr class="targetTableTr">
                                    <th> ردیف </th>
                                    <th> اسم تارگت </th>
                                    <th>تارگیت 1</th>
                                    <th> امتیاز 1</th>
                                    <th>تارگیت 2</th>
                                    <th> امتیاز 2</th>
                                    <th>تارگیت 3</th>
                                    <th> امتیاز 3</th>
                                    <th> انتخاب  </th>
                                  </tr>
                                </thead>
                                <tbody class="tableBody" id="targetList">
                                @foreach($targets as $target)
                                  <tr class="targetTableTr" onclick="setTargetStuff(this)">
                                  <td>{{$loop->iteration}}</td>
                                    <td>{{$target->BaseName}}</td>
                                    <td> {{$target->firstTarget}}</td>
                                    <td> {{$target->firstTargetBonus}} </td>
                                    <td> {{$target->secondTarget}}</td>
                                    <td> {{$target->secondTargetBonus}} </td>
                                    <td> {{$target->thirdTarget}}</td>
                                    <td> {{$target->thirdTargetBonus}} </td>
                                    <td> <input class="form-check-input" name="targetId" type="radio" value="{{$target->id}}"></td>
                                  </tr>
                                 @endforeach
                                </tbody>
                              </table>
                        </div>
             </fieldset>
        </div>
</div>


<!-- Bazaryab Modal -->
<div class="modal fade dragableModal" id="addingTargetModal" data-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white">
          <button type="button" class="btn-close bg-danger" data-dismiss="modal" aria-label="Close" style="color:red"></button>
        <h5 class="modal-title" id="staticBackdropLabel">  افزودن تارگت  </h5>
      </div>
      <form action="{{url('/addTarget')}}" method="GET" id="addTarget">
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label"> اساس تارگت </label>
                <input type="text" class="form-control" placeholder="خرید اولیه" name="baseName" aria-describedby="emailHelp">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگت 1 </label>
                <input type="text" class="form-control" placeholder="تارگت 1" name="firstTarget" aria-describedby="emailHelp">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگیت 1  </label>
                <input type="text" class="form-control" placeholder="" name="firstTargetBonus" aria-describedby="emailHelp">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگت 2 </label>
                <input type="text" class="form-control" placeholder="تارگت 2" name="secondTarget" aria-describedby="emailHelp">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگت 2   </label>
                <input type="number" class="form-control" placeholder="20" name="secondTargetBonus" aria-describedby="emailHelp">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگیت 3   </label>
                <input type="number" class="form-control" placeholder="23" name="thirdTarget" aria-describedby="emailHelp">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگت 3   </label>
                <input type="number" class="form-control" placeholder="20" name="thirdTargetBonus" aria-describedby="emailHelp">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"> بستن <i class="fa fa-xmark"></i> </button>
          <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save "></i> </button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Bazaryab Modal -->
<div class="modal fade dragableModal" id="editingTargetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" style="color:red"></button>
        <h5 class="modal-title" id="staticBackdropLabel"> ویرایش تارگت </h5>
      </div>
      <form action="{{url('/editTarget')}}" method="GET" id="editTarget">
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label"> اساس تارگت </label>
                <input type="text" class="form-control" placeholder="خرید اولیه" name="baseName" id="baseName" aria-describedby="emailHelp">
                <input type="hidden" name="targetId" id="targetIdForEdit">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگت 1 </label>
                <input type="text" class="form-control" placeholder="تارگت 1" name="firstTarget" id="firstTarget">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگیت 1  </label>
                <input type="text" class="form-control" placeholder="" name="firstTargetBonus" id="firstTargetBonus">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگت 2 </label>
                <input type="text" class="form-control" placeholder="تارگت 2" name="secondTarget" id="secondTarget">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگت 2   </label>
                <input type="number" class="form-control" placeholder="20" name="secondTargetBonus" id="secondTargetBonus">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  تارگیت 3   </label>
                <input type="number" class="form-control" placeholder="23" name="thirdTarget" id="thirdTarget">
              </div>
            </div>
            <div class="col-lg-3">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">  امتیاز تارگت 3   </label>
                <input type="number" class="form-control" placeholder="20" name="thirdTargetBonus" id="thirdTargetBonus">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal"> بستن <i class="fa fa-xmark"></i> </button>
          <button type="submit" class="btn btn-primary">ذخیره <i class="fa fa-save "></i> </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection



