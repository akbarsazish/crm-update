@extends('layout')
@section('content')
   <div class="container-fluid containerDiv">
      <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 sideBar">
                   <fieldset class="border rounded mt-5 sidefieldSet">
                        <legend  class="float-none w-auto legendLabel mb-0"> پیام ها </legend>
                        <!-- <div class="form-check">
                            <input class="form-check-input p-2 float-end" type="radio" name="settings" id="elseSettingsRadio">
                            <label class="form-check-label me-4" for="assesPast">  سطح دسترسی  </label>
                        </div>
                       -->
                        <div class="col-sm-12">
                            @if(hasPermission(Session::get("asn"),"massageOppN") > 1)
                               <button class="btn btn-primary mb-2" id="addMessageButton"> فرستادن پیام <i class="fa fa-plus"> </i></button>
                            @endif
                            <input type="text" style="display: none;" name="" id="senderId">
                        </div>
                        
                    </fieldset>
                  </div>
                <div class="col-sm-10 col-md-10 col-sm-12 contentDiv">
                    <div class="row contentHeader">  </div>
                    <div class="row mainContent"> 
                            <div class="col-sm-12 px-0">
                             @if(hasPermission(Session::get("asn"),"massageOppN") > -1)
                                <table class="myDataTable table table-bordered table-striped table-sm">
                                    <thead class="tableHeader">
                                        <tr >
                                            <th> ردیف</th>
                                            <th>تاریخ </th>
                                            <th>فرستنده </th>
                                            <th> گرینده </th>
                                            <th>پیام </th>
                                            <th>مشاهده </th>
                                        </tr>
                                    </thead>
                                    <tbody class="tableBody" id="factorTable">
                                        @foreach($messages as $msg)
                                        <tr onclick="setReadMessageStuff(this); selectTableRow(this);">
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($msg->messageDate))->format("Y/m/d H:i:s")}} </td>
                                            <td>{{$msg->name.' '.$msg->lastName}} </td>
                                            <td></td>
                                            <td> <input type="radio" name="" style="display: none" value="{{$msg->senderId}}" > {{$msg->messageContent}} </td>
                                            <td> <i class="fa fa-eye fa-xl"> </i></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                              
                                 <div class="grid-today rounded-2 mx-2">
                                        <div class="today-item"> <span style="color:red; font-weight:bold;">  پیام :  </span> <span id="">  </span>  </div>
                                        <div class="today-item"> <span style="color:red; font-weight:bold;">  پاسخ پیام :</span> <span id="" ></span>  </div>
                                </div>
                                  @endif
                            </div>
                     </div>
                    <div class="row contentFooter"> </div>
                </div>
          </div>
     </div>


<div class="modal fade" id="readComments" data-bs-keyboard="false" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable  modal-xl">
        <div class="modal-content">
            <div class="modal-header py-2">
                <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close" style="display: inline; background-color:red;"></button>
                <h6> پیام های من</h6>
            </div>
            <div class="modal-body">
                <div class="row">
                        <div class="row d-flex justify-content-center">
                          <div class="col-md-12 col-lg-12 col-xl-12">
                            <div class="card" id="chat1" style="border-radius: 15px;">
                                <div class="form-outline messageDiv">
                                    <form action="{{url('/addDiscussion')}}" id="addDisscusstionForm" method="get">
                                        <input type="text" style="display: none;" name="getterId" id="getterIdD">
                                    <textarea style="background-color:blanchedalmond" required class="form-control" name="messageArea" id="messageArea"  placeholder="متن پیام خود را بنویسید" rows="4"></textarea>
                                    @if(hasPermission(Session::get("asn"),"massageOppN") > 1)
                                    <button type="submit" class="btn btn-primary btn-md" id="btnSaveMsg">ارسال پیام</button>
                                    @endif
                                    </form>
                                </div>
                                   <div class="card-body messageBody" id="messageDiscusstion" style="overflow-y: scroll; scroll; height:300px;">
                                    <span id="sendedMessages">
                                        <div class="d-flex flex-row justify-content-start mb-1">
                                            <img src="/resources/assets/images/boy.png" alt="avatar 1" style="width:50px; height:50px; border-radius:100%;">
                                            <div class="flex" style="border-radius:10px; background-color: rgba(78, 192, 229, 0.2); min-height:66px">
                                                <p class="small" style="font-size:0.9rem;"> سلام وقت بخیر </p>
                                            </div>
                                        </div>
                                    </span>
                                    <span id="recivedMessages">
                                        <div class="d-flex flex-row justify-content-end mb-2">
                                            <div class="p-2 me-2 border" id="replayDiv'.$replay->id.'" style="border-radius: 15px; background-color: #fbfbfb;">
                                               <p class="small" style="font-size:0.9rem;"> ع سلام وقت بخیر! </p>
                                            </div>
                                            <img src="/resources/assets/images/girl.png" alt="avatar 1" style="width:50px; height:50px; border-radius:100%;">
                                        </div>
                                    </span>
                                  </div>
                             </div>
                           </div>
                         </div>
                       </div>
                    </div>
                 </div>
              </div>
         </div>


         <div class="modal fade" id="addMessage" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-scrollable  modal-xl">
                <div class="modal-content">
                    <div class="modal-header py-2">
                        <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close" style="display: inline; background-color:red;"></button>
                        <h5> پیام های من   </h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                                <div class="row d-flex justify-content-center">
                                  <div class="col-md-12 col-lg-12 col-xl-12">
                                      <span id="sendTo" class="fs-6">  کاربر <i class="fa fa-plus"></i></span>
                                    <div class="card" id="chat1" style="border-radius: 15px;">
                                        <div class="form-outline messageDiv">
                                            <form action="{{url('/addMessage')}}" id="addMessageForm" method="get">
                                                <input type="text" style="display: none;" name="getterId" id="getterId">
                                                <textarea style="background-color:blanchedalmond" required class="form-control" name="messageContent" id="messageContent" placeholder="متن پیام خود را بنویسید" rows="4"></textarea>
                                                <button type="submit" class="btn btn-primary btn-md" id="btnSaveMsg">ارسال پیام</button>
                                            </form>
                                        </div>
                                           <div class="card-body messageBody" id="messageList" style="overflow-y: scroll; height:300px; background-color:azure">
                                                <div class="d-flex flex-row justify-content-start mb-1">
                                                    <img src="/resources/assets/images/boy.png" alt="avatar 1" style="width:50px; height:50px; border-radius:100%;">
                                                    <div class="p-2 ms-2" style="border-radius:10px; background-color: rgba(78, 192, 229, 0.2); min-height:55px">
                                                        <p class="small" style="font-size:0.9rem;"> سلام وقت بخیر </p>
                                                    </div>
                                                </div>
                                
                                                <div class="d-flex flex-row justify-content-end mb-2">
                                                    <div class="p-2 me-2 border" id="replayDiv'.$replay->id.'" style="border-radius: 15px; background-color: #fbfbfb;">
                                                        <p class="small" style="font-size:0.9rem;"> ع سلام وقت بخیر! </p>
                                                    </div>
                                                <img src="/resources/assets/images/girl.png" alt="avatar 1" style="width:50px; height:50px; border-radius:100%;">
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


             <!-- modal for listing other users -->
             <div class="modal fade" id="userList" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable " role="document">
                    <div class="modal-content">
                        <div class="modal-header py-2" style="margin:0; border:none">
                            <button type="button" class="btn-close btn-danger" style="background-color:red;" data-bs-dismiss="modal" aria-label="Close"></button>
                            <h5 class="modal-title" id="exampleModalLongTitle">انتخاب کاربر </h5>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr onclick="selectTableRow(this);">
                                        <th>ردیف</th>
                                        <th>نام کاربر </th>
                                        <th>انتخاب </th>
                                  </tr>
                                </thead>
                                <tbody>
                                    @foreach ($admins as $admin)

                                    <tr onclick="setMessageStuff(this); selectTableRow(this);">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$admin->name." ".$admin->lastName}}</td>
                                        <td>
                                            <input class="mainGroupId" type="radio" name="AdminId[]" value="{{$admin->id.'_'.$admin->adminTypeId}}">
                                        </td>
                                    </tr>
                                   @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
         </div>

@endsection
