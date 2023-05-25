@extends('layout')
@section('content')

<style>
.flex-container {
  display: flex;
  font-size:14px;

}

.flex-left {
  width:49% !important;
   background-color: #b3d1ef;
   padding:5px;
   margin:3px;
   border-radius: 5px;
}
.flex-right {
  width:49% !important;
   padding:5px;
   background-color: #b3d1ef;
     margin:3px;
     border-radius:5px;
}
</style>
 <div class="container-fluid containerDiv">
      <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                  <fieldset class="border rounded mt-5 sidefieldSet">
                     
                  </fieldset>
                </div>
                <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                    <div class="row contentHeader">
                        
                    </div>
                    <div class="row mainContent">
                      <div class="col-lg-4 text-center shadow rounded">
                          <img style="width:111px; height:111px;" src="{{url('resources/assets/images/admins/'.Session::get('asn').'.jpg')}}" alt="avatar"
                                            class="rounded-circle mt-3">
                             <h5 class="mt-5 w-100 rounded-2 p-2" style="background-color:#b3d1ef">{{Session::get('username')}} </h5>
                                        
                               <div class="flex-container">
                                  <div class="flex-left">    وضعیت   </div>
                                  <div class="flex-right">   @if(Session::get('activeState')==1)  فعال   @else  غیر فعال   @endif </div>
                                </div>

                               <div class="flex-container">
                                  <div class="flex-left"> نام کاربر   </div>
                                  <div class="flex-right">   {{$admin->name.' '.$admin->lastName}} </div>
                                </div>
                               <div class="flex-container">
                                  <div class="flex-left">  نقش کاربر   </div>
                                  <div class="flex-right">   @if(Session::get('adminType')==1)   ادمین  @elseif(Session::get('adminType')==2)  پشتیبان  @endif  </div>
                                </div>
                               <div class="flex-container">
                                  <div class="flex-left">  شماره تماس   </div>
                                  <div class="flex-right">  {{$admin->phone}}  </div>
                                </div>
                               <div class="flex-container">
                                  <div class="flex-left">   آدرس   </div>
                                  <div class="flex-right">  {{$admin->address}} </div>
                                </div>
                              @if(hasPermission(Session::get("asn"),"baseInfoProfileN") > 1)
                                 <button class="w-50 btn btn-sm btn-primary" data-toggle="modal" data-target="#editProfile"> ویرایش <i class="fa fa-edit"> </i>  </button>
                              @endif
                              </div>
                              

                    </div>
                    <div class="row contentFooter"> </div>
                </div>
        </div>
    </div>







             <!-- modal of new Brand -->
             <div class="modal fade" id="editProfile" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header py-2" style="margin:0; border:none">
                            <button type="button" class="btn-close btn-danger" style="background-color:red;" data-dismiss="modal" aria-label="Close"></button>
                            <h5 class="modal-title" id="exampleModalLongTitle"> ویرایش پروفایل </h5>
                        </div>
                        <div class="modal-body">
                                <form action="{{url('/editOwnAdmin')}}" method="post"  enctype="multipart/form-data">
                                  @csrf

                                  <div class="row">
                                      <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label"> نام کاربری</label>
                                            <input type="text" required maxlength="20" minlength="5" class="form-control" value="{{trim($admin->username)}}" autocomplete="off" name="userName">
                                        </div>
                                      </div>
                                      <div class="col-lg-6">
                                          <div class="form-group">
                                              <label class="form-label"> شماره تماس </label>
                                              <input type="number" maxlength="12" minlength="10" required class="form-control" value="{{trim($admin->phone)}}" autocomplete="off" name="phone">
                                          </div>
                                      </div>
                                  </div>
                                  <div class="row">
                                      <div class="col-lg-6">
                                         <div class="form-group">
                                            <label class="form-label"> رمز</label>
                                            <input type="text" required class="form-control"  maxlength="20" minlength="4" value="{{trim($admin->password)}}" autocomplete="off" name="password" >
                                        </div>
                                      </div>
                                      <div class="col-lg-6">
                                          <div class="form-group">
                                              <label class="form-label"> عکس </label>
                                              <input type="file" class="form-control" required name="picture" placeholder="">
                                          </div>
                                      </div>
                                  </div>
                                    <div class="form-group">
                                        <label class="form-label"> ادرس  </label>
                                        <input type="text" required class="form-control" value="{{trim($admin->address)}}" autocomplete="off" name="address">
                                    </div>
                                    
                                    <div class="form-group tex-end mt-3">
                                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"> انصراف <i class="fa-solid fa-xmark"> </i> </button>
                                        <button type="submit" class="btn btn-sm btn-primary">ذخیره <i class="fa fa-save" aria-hidden="true"> </i> </button>
                                    </div>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
</section>
@endsection
