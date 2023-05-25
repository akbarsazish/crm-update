@extends('layout')
@section('content')
<style> 
@media (max-width:920px){
	.salesExpertMobile{
		margin-top:22% !important;
	}
}

</style>
<div class="container salesExpertMobile" style="margin-top:8%;">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12 d-flex justify-content-end">
            <form action="{{url('/poshtibanActionInfo')}}" method="get">
                <input type="hidden" id="subPoshtibanId" name="subPoshtibanId">
                <button class="btn btn-sm btn-primary" disabled id="subListDashboardBtn"> رفتن به جزئیات <i class="fa fa-info-circle" aria-hidden="true"></i> </button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <div class="row">
					<div class="col-lg-12">
                        <table class="table table-bordered" id="tableGroupList" >
                            <thead class="tableHeader">
                                <tr>
                                    <th>ردیف</th>
                                    <th>نام کاربر</th>
                                    <th>نقش کاربری</th>
                                    <th>توضیحات</th>
                                    <th>فعال</th>
                                </tr>
                            </thead>
                            <tbody class="tableBody" id="adminGroupList">
                                @foreach ($poshtibans as $poshtiban)
                                <?php
                                $poshtibanType="تعریف نشده";
                                ?>
                                @if($poshtiban->poshtibanType==3)
                                <?php
                                $poshtibanType="تلفنی";
                                ?>
                                @elseif($poshtiban->poshtibanType==2)
                                <?php
                                $poshtibanType="هماهنگی";
                                ?>
                                @elseif($poshtiban->poshtibanType==1)
                                <?php
                                $poshtibanType="حضوری";
                                ?>
                                @endif
                                    <tr onclick="setSubPoshtibanStuff(this)">
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{trim($poshtiban->name)." ".trim($poshtiban->lastName)}}</td>
                                        <td>{{trim($poshtibanType)}}</td>
                                        <td>{{trim($poshtiban->discription)}}</td>
                                        <td>
                                            <input class="mainGroupId" type="radio" name="AdminId[]" value="{{$poshtiban->id}}">
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

