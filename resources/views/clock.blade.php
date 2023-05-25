<div class="affairs">
            <div class="row">
                <div class="myClock">
                        <div id="myclock"></div>
                        <div id="alarm1" class="alarm"><a href="javascript:void(0)" id="turnOffAlarm">خاموش</a></div>
                </div>
                <input type="text" id="altime" style="display: block!important" placeholder="ساعت:دقیقه"/>
                <br/>
                <a id="set" class="float-end">تعیین آلارم</a>
                <span style="padding:5px;">
                    <textarea class="form-control"  name="" placeholder="یاداشت....." id="alarmComment" cols="30" rows="4" style="background-color: #eeefff"></textarea>
                </span>
                <ol class="list-group list-group-numbered mt-3">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                         <div class="ms-2 me-auto">
                             <div class="fw-bold">  وظایف انجام شده </div>
                         </div>
                       <span class="badge bg-primary rounded-pill workSummary">{{$doneWorks}}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">وظایف انجام نشده </div>
                        </div>
                        <span class="badge bg-primary rounded-pill workSummary">@if($remainedWorks){{$remainedWorks}}@else 0 @endif</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                       <div class="ms-2 me-auto">
                          <div class="fw-bold"> پیام ها  </div>
                       </div>
                      <span class="badge bg-primary rounded-pill workSummary">{{$inbox}}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                           <div class="fw-bold"> زمان آلارم </div>
                        </div>
                       <span class="badge bg-primary rounded-pill workSummary" id="alarmShows">{{$alarmTime}}</span>
                     </li>
              </ol>
            </div>
        </div>