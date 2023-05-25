
<?php
// اگر اطلاعات پایه آن بود 
        $baseInfoED = $request->post("baseInfoED");
        // اطلاعات پایه============
        $baseInfoProfileED;
        $infoRdED;
        $specialSettingED;
        $baseInfoSettingED;
        $InfoSettingAccessED;
        $InfoSettingTargetED;
        $rdSentED;
        $rdNotSentED;
        //==========================
        if($baseInfoED=="on"){
            $baseInfoED = 1;
                    // پروفایل با سه تا عنصر اش چک میشوند
                    $baseInfoProfileED = $request->post("baseInfoProfileED");

                    $deleteProfileED = $request->post("deleteProfileED");
                    $editProfileED = $request->post("editProfileED");
                    $seeProfileED = $request->post("seeProfileED");

                if($baseInfoProfileED=="on"){
                    if($deleteProfileED=="on"){
                        $baseInfoProfileED=2;
                    }elseif($editProfileED=="on" and $deleteProfileED!="on"){
                        $baseInfoProfileED=1;
                    }elseif($editProfileED !="on" and $seeProfileED =="on"){
                        $baseInfoProfileED=0;
                    }else{
                        $baseInfoProfileED=-1;
                    }
                }else{
                    $baseInfoProfileED=-1;
                }

            //  اگر آر و دی آن بود وارد شده و وارد نشده چک گردد
            $infoRdED = $request->post("infoRdED");

                if($infoRdED=="on"){
                    $infoRdED = 1;
                    // وارد شده ها چک میگردد
                    $rdSentED = $request->post("rdSentED");

                    $deleteSentRdED = $request->post("deleteSentRdED");
                    $editSentRdED = $request->post("editSentRdED");
                    $seeSentRdED = $request->post("seeSentRdED");

                            
                    if($rdSentED=="on"){
                        $rdSentED=1;
                        if($deleteSentRdED=="on"){
                            $rdSentED=2;
                        }elseif($editSentRdED=="on" and $deleteSentRdED!="on"){
                            $rdSentED=1;
                        }elseif($editSentRdED!="on" and $seeSentRdED=="on"){
                            $rdSentED=0;
                        }else{
                            $rdSentED=-1;
                        }
                    }else{
                        $rdSentED=-1;
                    }

                    // وراد نشده ها چک میگردد
                    $rdNotSentED = $request->post("rdNotSentED");

                    $deleteRdNotSentED = $request->post("deleteRdNotSentED");
                    $editRdNotSentED = $request->post("editRdNotSentED");
                    $seeRdNotSentED = $request->post("seeRdNotSentED");

                    if($rdNotSentED=="one"){
                        $rdNotSentED=1;
                        if($deleteRdNotSentED=="on"){
                            $rdNotSentED=2;
                        }elseif($editRdEDotSentED=="on" and $deleteRdNotSentED!="on"){
                            $rdNotSentED=1;
                        }elseif($editRdEDotSentED!="on" and $seeRdNotSentED=="on"){
                            $rdNotSentED=0;
                        }else{
                            $rdNotSentED=-1;
                        }
                    }else{
                        $rdNotSentED=-1;
                    }

                }else{
                    $infoRdED=-1;
                    $rdSentED=-1;
                    $rdNotSentED=-1;
                }
                
                // افزودن خط فروش چک میگردد
                $specialSettingED = $request->post("specialSettingED");
                $deleteSaleLineED = $request->post("deleteSaleLineED");
                $editSaleLineED = $request->post("editSaleLineED");
                $seeSaleLineED = $request->post("seeSaleLineED");

                if($specialSettingED=="on"){
                    $specialSettingED=1;
                    if($deleteSaleLineED=="on"){
                        $specialSettingOpED=2;
                    }elseif($editSaleLineED=="on" and $deleteSaleLineED!="on"){
                        $specialSettingOpED=1;
                    }elseif($editSaleLineED!="on" and  $seeSaleLineED=="on"){
                        $specialSettingOpED=0;
                    }else{
                        $specialSettingOpED=-1;
                    }
                }else{
                    $specialSettingOpED=-1;
                }
            
            // اگر تنظیمات آن بود 
            $baseInfoSettingED = $request->post("baseInfoSettingED");
            if($baseInfoSettingED=="on"){
                $baseInfoSettingED=1;
                // سطح دسترسی با عناصر اش چک میگردد
                $InfoSettingAccessED = $request->post("InfoSettingAccessED");

                $deleteSettingAccessED = $request->post("deleteSettingAccessED");
                $editSettingAccessED = $request->post("editSettingAccessED");
                $seeSettingAccessED = $request->post("seeSettingAccessED");
                if($InfoSettingAccessED=="on"){
                    $InfoSettingAccessED=1;
                    if($deleteSettingAccessED=="on"){
                    $InfoSettingAccessOpED=2;
                    }elseif($editSettingAccessED=="on" and $deleteSettingAccessED!="on"){
                    $InfoSettingAccessOpED=1;
                    }elseif($editSettingAccessED!="on" and $seeSettingAccessED=="on"){
                        $InfoSettingAccessOpED=0;
                    }else{
                        $InfoSettingAccessOpED=-1;
                    }
                }else{
                    $InfoSettingAccessOpED=-1;
                }

                // تارگیت ها و امتیازات چک میگردد
               $InfoSettingTargetED = $request->post("InfoSettingTargetED");

                $deleteSettingTargetED = $request->post("deleteSettingTargetED");
                $editSettingTargetED = $request->post("editSettingTargetED");
                $seeSettingTargetED = $request->post("seeSettingTargetED");
                if($InfoSettingTargetED=="on"){
                    $InfoSettingTargetED=1;
                    if($deleteSettingTargetED=="on"){
                        $InfoSettingTargetOpED=2;
                    }elseif($editSettingTargetED=="on" and $deleteSettingTargetED!="on"){
                        $InfoSettingTargetOpED=1;
                    }elseif($editSettingTargetED!="on" and $seeSettingTargetED=="on"){
                        $InfoSettingTargetOpED=0;
                    }else{
                        $InfoSettingTargetOpED=-1;
                    }
                }else{
                    $InfoSettingTargetOpED=-1;
                }


            }else {
                $InfoSettingAccessOpED=-1;
                $InfoSettingTargetOpED=-1;
                $baseInfoSettingED=-1;
            }

        }else{
            $InfoSettingAccessOpED=-1;
            $InfoSettingTargetOpED=-1;
            $baseInfoSettingED=-1;
            //
            $baseInfoProfileED = -1;
            $infoRdED=-1;
            $rdSentED=-1;
            $rdNotSentED=-1;
            //
            $specialSettingED = -1;
            $baseInfoED=-1;
        }


    //   چک کردن تعریف عناصر با سه عناصر اش
    //تعریف عناصر===============
    $declareElementOppED;
    // ==========================
        $declareElementED = $request->post("declareElementED");
        if($declareElementED=="on"){
            $declareElementOppED=1;
            $deletedeclareElementED = $request->post("deletedeclareElementED");
            $editdeclareElementED = $request->post("editdeclareElementED");
            $seedeclareElementED = $request->post("seedeclareElementED");

            if($deletedeclareElementED=="on"){
            $declareElementOppED=2;
            }elseif($editdeclareElementED=="on" and $deletedeclareElementED!="on"){
            $declareElementOppED=1;
            }elseif($editdeclareElementED!="on" and $seedeclareElementED=="on"){
                $declareElementOppED=0;
            }else{
                $declareElementOppED=-1;
            }
        }else{
            $declareElementOppED=-1;
        }

       // اگر عملیات آن بود 
        $oppED = $request->post("oppED");
        $oppManagerED;
        $oppHeadED;
        $oppBazaryabED;
        $oppTakhsisED;
        $oppDriverServiceED;
        $oppBargiriED;
        $oppDriverED;
        $todayoppNazarsanjiED;
        $pastoppNazarsanjiED;
        $DoneoppNazarsanjiED;
        $oppNazarSanjiED;
        $AddOppupDownBonusED;
        $SubOppupDownBonusED;
        $OppupDownBonusED;
        $AddedoppRDED;
        $NotAddedoppRDED;
        $oppRDED;
        $oppjustCalendarED;
        $oppCustCalendarED;
        $oppCalendarED;
        $allalarmoppED;
        $donealarmoppED;
        $NoalarmoppED;
        $alarmoppED;
        $massageOppED;
        $justBargiriOppED;

        if($oppED=="on"){
            $oppED = 1;
            //
            // و اگر تخصسص به کاربر آن بود
            $oppTakhsisED = $request->post("oppTakhsisED");
            if($oppTakhsisED=="on"){
                $oppTakhsisED=-1;
                // مدیران چک گردد
                $oppManagerED = $request->post("oppManagerED");
                $deleteManagerOppED = $request->post("deleteManagerOppED");
                $editManagerOppED = $request->post("editManagerOppED");
                $seeManagerOppED = $request->post("seeManagerOppED");
                if($oppManagerED=="on"){
                    $oppManagerED = 1;
                    if($deleteManagerOppED=="on"){
                    $oppManagerED=2;
                    }elseif($editManagerOppED=="on" and $deleteManagerOppED!="on"){
                    $oppManagerED=1;
                    }elseif($editManagerOppED!="on" and $seeManagerOppED=="on"){
                        $oppManagerED=0;
                    }else{
                        $oppManagerED=-1;
                    }
                }else{
                    $oppManagerED=-1;
                }
                // سرپرستان چک گردد
                $oppHeadED = $request->post("oppHeadED");
                $deleteHeadOppED = $request->post("deleteHeadOppED");
                $editHeadOppED = $request->post("editHeadOppED");
                $seeHeadOppED = $request->post("seeHeadOppED");

                if($oppHeadED == "on"){
                    $oppHeadED = 1;
                    if($deleteHeadOppED=="on"){
                    $oppHeadED=2;
                    }elseif($editHeadOppED=="on" and $deleteHeadOppED!="on"){
                    $oppHeadED=1;
                    }elseif($editHeadOppED!="on" and $seeHeadOppED=="on"){
                        $oppHeadED=0;
                    }else{
                        $oppHeadED=-1;
                    }
                }else{
                    $oppHeadED=-1; 
                }

                // بازاریابها چک گردد
                $oppBazaryabED = $request->post("oppBazaryabED");

                $deleteBazaryabOppED = $request->post("deleteBazaryabOppED");
                $editBazaryabOppED = $request->post("editBazaryabOppED");
                $seeBazaryabOppED = $request->post("seeBazaryabOppED");

                if($oppBazaryabED=="on"){
                    $oppBazaryabED = 1;
                    if($deleteBazaryabOppED=="on"){
                    $oppBazaryabED=2;
                    }elseif($editBazaryabOppED=="on" and $deleteBazaryabOppED!="on"){
                    $oppBazaryabED=1;
                    }elseif($editBazaryabOppED!="on" and $seeBazaryabOppED=="on"){
                        $oppBazaryabED=0;
                    }else{
                        $oppBazaryabED=-1;
                    }
                }else{
                    $oppBazaryabED=-1;
                }

            }else{
                $oppBazaryabED=-1;
                $oppHeadED=-1; 
                $oppManagerED=-1;
                $oppTakhsisED=-1;
            }

            //   راننده ها چگ میگردد
            $oppDriverED = $request->post("oppDriverED");
            if($oppDriverED=="on"){
                $oppDriverED=1;
               // سرویس راننده ها چک میگردد
                $oppDriverServiceED = $request->post("oppDriverServiceED");

                $deleteoppDriverServiceED = $request->post("deleteoppDriverServiceED");
                $editoppDriverServiceED = $request->post("editoppDriverServiceED");
                $seeoppDriverServiceED = $request->post("seeoppDriverServiceED");

                if($oppDriverServiceED=="on"){
                    $oppDriverServiceED = 1;
                    if($deleteoppDriverServiceED=="on"){
                        $oppDriverServiceED=2;
                    }elseif($editoppDriverServiceED=="on" and $deleteoppDriverServiceED!="on"){
                        $oppDriverServiceED=1;
                    }elseif($editoppDriverServiceED!="on" and $seeoppDriverServiceED=="on"){
                        $oppDriverServiceED=0;
                    }else{
                        $oppDriverServiceED=-1;
                    }
                }else{
                    $oppDriverServiceED=-1;
                }


                // بارگیری چک می گردد
                $oppBargiriED = $request->post("oppBargiriED");

                $deleteoppBargiriED = $request->post("deleteoppBargiriED");
                $editoppBargiriED = $request->post("editoppBargiriED");
                $seeoppBargiriED = $request->post("seeoppBargiriED");

                if($oppBargiriED=="on"){
                    $oppBargiriED = 1;
                    if($deleteoppBargiriED=="on"){
                    $oppBargiriED=2;
                    }elseif($editoppBargiriED=="on" and $deleteoppBargiriED!="on"){
                    $oppBargiriED=1;
                    }elseif($editoppBargiriED!="on" and $seeoppBargiriED=="on"){
                        $oppBargiriED=0;
                    }else{
                        $oppBargiriED=-1;

                    }
                }else{
                    $oppBargiriED=-1;
                }
            }else{
                $oppBargiriED=-1;
                $oppDriverServiceED=-1;
                $oppDriverED=1;
            }

           // اگر نظر سنجی آن بود 
            $oppNazarSanjiED = $request->post("oppNazarSanjiED");
            if($oppNazarSanjiED=="on"){
                $oppNazarSanjiED=1;
              // نظرات امروز چک گردد
                $todayoppNazarsanjiED = $request->post("todayoppNazarsanjiED");

                $deletetodayoppNazarsanjiED = $request->post("deletetodayoppNazarsanjiED");
                $edittodayoppNazarsanjiED = $request->post("edittodayoppNazarsanjiED");
                $seetodayoppNazarsanjiED = $request->post("seetodayoppNazarsanjiED");
                if($todayoppNazarsanjiED=="on"){
                    $todayoppNazarsanjiED = 1;
                    if($deletetodayoppNazarsanjiED=="on"){
                        $todayoppNazarsanjiED=2;
                    }elseif($edittodayoppNazarsanjiED=="on" and $deletetodayoppNazarsanjiED!="on"){
                        $todayoppNazarsanjiED=1;
                    }elseif($edittodayoppNazarsanjiED!="on" and $seetodayoppNazarsanjiED=="on"){
                        $todayoppNazarsanjiED=0;
                    }else{
                        $todayoppNazarsanjiED=-1;
                    }
                }else{
                    $todayoppNazarsanjiED=-1;
                }

                // نظرات گذشته چک گردد
                $pastoppNazarsanjiED = $request->post("pastoppNazarsanjiED");

                $deletepastoppNazarsanjiED = $request->post("deletepastoppNazarsanjiED");
                $editpastoppNazarsanjiED = $request->post("editpastoppNazarsanjiED");
                $seepastoppNazarsanjiED = $request->post("seepastoppNazarsanjiED");

                if($pastoppNazarsanjiED=="on"){
                    $pastoppNazarsanjiED = 1;
                    if($deletepastoppEDazarsanjiED=="on"){
                    $pastoppNazarsanjiED=2;
                    }elseif($editpastoppEDazarsanjiED=="on" and $deletepastoppNazarsanjiED!="on"){
                    $pastoppNazarsanjiED=1;
                    }elseif($editpastoppNazarsanjiED!="on" and $seepastoppNazarsanjiED=="on"){
                        $pastoppNazarsanjiED=0;
                    }else{
                        $pastoppNazarsanjiED=-1;
                    }
                }else{
                    $pastoppNazarsanjiED=-1;
                }
            
                // نظرات انجام شده چک میگردد
                $DoneoppNazarsanjiED = $request->post("DoneoppNazarsanjiED");

                $deleteDoneoppNazarsanjiED = $request->post("deleteDoneoppNazarsanjiED");
                $editDoneoppNazarsanjiED = $request->post("editDoneoppNazarsanjiED");
                $seeDoneoppNazarsanjiED = $request->post("seeDoneoppNazarsanjiED");
                if($DoneoppNazarsanjiED=="on"){
                    $DoneoppNazarsanjiED = 1;
                    if($deleteDoneoppNazarsanjiED=="on"){
                    $DoneoppNazarsanjiED=2;
                    }elseif($editDoneoppEDazarsanjiED=="on" and $deleteDoneoppNazarsanjiED!="on"){
                    $DoneoppNazarsanjiED=1;
                    }elseif($editDoneoppEDazarsanjiED!="on" and $seeDoneoppNazarsanjiED=="on"){
                        $DoneoppNazarsanjiED=0;
                    }else{
                        $DoneoppNazarsanjiED=-1;
                    }
                }else{
                    $DoneoppNazarsanjiED=-1;
                }
            }else{
                $DoneoppNazarsanjiED=-1;
                $pastoppNazarsanjiED=-1;
                $todayoppNazarsanjiED=-1;
                $oppNazarSanjiED=-1;
            }


            // اگر افزایش و کا هش آن بود
            $OppupDownBonusED = $request->post("OppupDownBonusED");
            if($OppupDownBonusED=="on"){
                $OppupDownBonusED=1;
                // امتیاز های اضافه شده چک گردد
                $AddOppupDownBonusED = $request->post("AddOppupDownBonusED");

                $deleteAddOppupDownBonusED = $request->post("deleteAddOppupDownBonusED");
                $editAddOppupDownBonusED = $request->post("editAddOppupDownBonusED");
                $seeAddOppupDownBonusED = $request->post("seeAddOppupDownBonusED");

                if($AddOppupDownBonusED=="on"){
                    $AddOppupDownBonusED = 1;
                    if($deleteAddOppupDownBonusED=="on"){
                    $AddOppupDownBonusED=2;
                    }elseif($editAddOppupDownBonusED=="on" and $deleteAddOppupDownBonusED!="on"){
                    $AddOppupDownBonusED=1;
                    }elseif($editAddOppupDownBonusED!="on" and $seeAddOppupDownBonusED=="on"){
                        $AddOppupDownBonusED=0;
                    }else{
                        $AddOppupDownBonusED=-1;
                    }
                }else{
                    $AddOppupDownBonusED=-1;
                }

               // امتیاز های کاهش یافته چک گردد
                $SubOppupDownBonusED = $request->post("SubOppupDownBonusED");

                $deleteSubOppupDownBonusED = $request->post("deleteSubOppupDownBonusED");
                $editSubOppupDownBonusED = $request->post("editSubOppupDownBonusED");
                $seeSubOppupDownBonusED = $request->post("seeSubOppupDownBonusED");

                if($SubOppupDownBonusED=="on"){
                     $SubOppupDownBonusED = 1;
                    if($deleteSubOppupDownBonusED=="on"){
                        $SubOppupDownBonusED=2;
                    }elseif($editSubOppupDownBonusED=="on" and $deleteSubOppupDownBonusED!="on"){
                        $SubOppupDownBonusED=1;
                    }elseif($editSubOppupDownBonusED!="on" and $seeSubOppupDownBonusED=="on"){
                        $SubOppupDownBonusED=0;
                    }else{
                        $SubOppupDownBonusED=-1;
                    }
                }else{
                    $SubOppupDownBonusED=-1;
                }
            }else{
                $SubOppupDownBonusED=-1;
                $AddOppupDownBonusED=-1;
                $OppupDownBonusED=-1;
            }

               // اگر آر و دی آن بود 
            $oppRDED = $request->post("oppRDED");
            if($oppRDED =="on"){
                $oppRDED=1;
                // وارد شده ها چک میگردد
                $AddedoppRDED = $request->post("AddedoppRDED");

                $deleteAddedoppRDED = $request->post("deleteAddedoppRDED");
                $editAddedoppRDED = $request->post("editAddedoppRDED");
                $seeAddedoppRDED = $request->post("seeAddedoppRDED");

                if($AddedoppRDED=="on"){
                    $AddedoppRDED = 1;
                    if($deleteAddedoppRDED=="on"){
                        $AddedoppRDED=2;
                    }elseif($editAddedoppRDED=="on" and $deleteAddedoppRDED!="on"){
                        $AddedoppRDED=1;
                    }elseif($editAddedoppRDED!="on" and $seeAddedoppRDED=="on"){
                        $AddedoppRDED=0;
                    }else{
                        $AddedoppRDED=-1;
                    }
                }else{
                    $AddedoppRDED=-1;
                }

                // وارد نشده ها چک میگردد
                $NotAddedoppRDED = $request->post("NotAddedoppRDED");

                $deleteNotAddedoppRDED = $request->post("deleteNotAddedoppRDED");
                $editNotAddedoppRDED = $request->post("editNotAddedoppRDED");
                $seeNotAddedoppRDED = $request->post("seeNotAddedoppRDED");
                if($NotAddedoppRDED=="on"){
                    $NotAddedoppRDED = 1;
                    if($deleteNotAddedoppRDED=="on"){
                    $NotAddedoppRDED=2;
                    }elseif($editNotAddedoppRDED=="on" and $deleteNotAddedoppRDED!="on"){
                    $NotAddedoppRDED=1;
                    }elseif($editNotAddedoppRDED!="on" and $seeNotAddedoppRDED=="on"){
                        $NotAddedoppRDED=0;
                    }else{
                        $NotAddedoppRDED=-1;
                    }
                }else{
                    $NotAddedoppRDED=-1;
                }
            }else{
                $AddedoppRDED = -1;
                $NotAddedoppRDED = -1;
                $oppRDED=-1;
            }


              // اگر تقویم روزانه آن بود 
            $oppCalendarED = $request->post("oppCalendarED");
            if($oppCalendarED=="on"){
                $oppCalendarED=1;
                // تقویم روزانه چک می گردد
                $oppjustCalendarED = $request->post("oppjustCalendarED");

                $deleteoppjustCalendarED = $request->post("deleteoppjustCalendarED");
                $editoppjustCalendarED = $request->post("editoppjustCalendarED");
                $seeoppjustCalendarED = $request->post("seeoppjustCalendarED");

                if($oppjustCalendarED=="on"){
                    $oppjustCalendarED = 1;
                    if($deleteoppjustCalendarED=="on"){
                    $oppjustCalendarED=2;
                    }elseif($editoppjustCalendarED=="on" and $deleteoppjustCalendarED!="on"){
                    $oppjustCalendarED=1;
                    }elseif($editoppjustCalendarED!="on" and $seeoppjustCalendarED=="on"){
                        $oppjustCalendarED=0;
                    }else{
                        $oppjustCalendarED=-1;
                    }
                }else{
                    $oppjustCalendarED=-1;
                    $oppCalendarED=1;
                }

                 //لیست مشتریان چک گردد
                $oppCustCalendarED = $request->post("oppCustCalendarED");

                $deleteoppCustCalendarED = $request->post("deleteoppCustCalendarED");
                $editoppCustCalendarED = $request->post("editoppCustCalendarED");
                $seeoppCustCalendarED = $request->post("seeoppCustCalendarED");

                if($oppCustCalendarED=="on"){
                    $oppCustCalendarED = 1;
                    if($deleteoppCustCalendarED=="on"){
                    $oppCustCalendarED=2;
                    }elseif($editoppCustCalendarED=="on" and $deleteoppCustCalendarED!="on"){
                    $oppCustCalendarED=1;
                    }elseif($editoppCustCalendarED!="on" and $seeoppCustCalendarED=="on"){
                        $oppCustCalendarED=0;
                    }else{
                        $oppCustCalendarED=-1;
                    }
                }else{
                    $oppCustCalendarED=-1;
                }

            }else {
                $oppjustCalendarED = -1;
                $oppCustCalendarED = -1;
                $oppCalendarED=-1;
            }


              // اگر آلارمها آن بود 
            $alarmoppED = $request->post("alarmoppED");
            if($alarmoppED=="on"){
                $alarmoppED = 1;
                // آلارمها چک گردد
                $allalarmoppED = $request->post("allalarmoppED");

                $deleteallalarmoppED = $request->post("deleteallalarmoppED");
                $editallalarmoppED = $request->post("editallalarmoppED");
                $seeallalarmoppED = $request->post("seeallalarmoppED");

                if($allalarmoppED=="on"){
                    $allalarmoppED = 1;
                    if($deleteallalarmoppED=="on"){
                        $allalarmoppED=2;
                    }elseif($editallalarmoppED=="on" and $deleteallalarmoppED!="on"){
                        $allalarmoppED=1;
                    }elseif($editallalarmoppED!="on" and $seeallalarmoppED=="on"){
                        $allalarmoppED=0;
                    }else{
                        $allalarmoppED=-1;
                    }
                }else{
                    $allalarmoppED=-1;
                }

                    // آلارمهای انجام شده چک میگردد
                $donealarmoppED = $request->post("donealarmoppED");

                $deletedonealarmoppED = $request->post("deletedonealarmoppED");
                $editdonealarmoppED = $request->post("editdonealarmoppED");
                $seedonealarmoppED = $request->post("seedonealarmoppED");

                if($donealarmoppED=="on"){
                    $donealarmoppED = 1;
                    if($deletedonealarmoppED=="on"){
                    $donealarmoppED=2;
                    }elseif($editdonealarmoppED=="on" and $deletedonealarmoppED!="on"){
                    $donealarmoppED=1;
                    }elseif($editdonealarmoppED!="on" and $seedonealarmoppED=="on"){
                        $donealarmoppED=0;
                    }else{
                        $donealarmoppED=-1;
                    }
                }else{
                    $donealarmoppED=-1;
                }

                // مشتریان فاقد آلارم چک میگردد
                $NoalarmoppED = $request->post("NoalarmoppED");

                $deleteNoalarmoppED = $request->post("deleteNoalarmoppED");
                $editNoalarmoppED = $request->post("editNoalarmoppED");
                $seeNoalarmoppED = $request->post("seeNoalarmoppED");

                if($NoalarmoppED=="on"){
                        $NoalarmoppED = 1;
                    if($deleteNoalarmoppED=="on"){
                    $NoalarmoppED=2;
                    }elseif($editNoalarmoppED=="on" and $deleteNoalarmoppED!="on"){
                    $NoalarmoppED=1;
                    }elseif($editNoalarmoppED!="on" and $seeNoalarmoppED=="on"){
                        $NoalarmoppED=0;
                    }else{
                        $NoalarmoppED=-1;
                    }
                }else{
                    $NoalarmoppED=-1;
                }

            }else {
                $allalarmoppED = -1;
                $donealarmoppED = -1;
                $NoalarmoppED = -1;
                $alarmoppED=-1;
            }

            // پیامها چک میگردد
            $massageTopOppED = $request->post("massageOppED");

            $deletemassageOppED = $request->post("deletemassageOppED");
            $editmassageOppED = $request->post("editmassageOppED");
            $seemassageOppED = $request->post("seemassageOppED");
            if($massageTopOppED=="on"){
                $massageTopOppED = 1;
                $massageOppED= 0;
                if($deletemassageOppED=="on"){
                    $massageOppED=2;
                }elseif($editmassageOppED=="on" and $deletemassageOppED!="on"){
                    $massageOppED=1;
                }elseif($editmassageOppED!="on" and $seemassageOppED=="on"){
                    $massageOppED=0;
                }else{
                    $massageOppED=-1;
                }
            }else{
                $massageOppED=-1;
                $massageTopOppED=-1;
            }

            // بارگیری چک میگردد
            $justBargiriTopOppED = $request->post("justBargiriOppED");
            if($justBargiriTopOppED=="on"){
                $justBargiriTopOppED = 1;
                $deletejustBargiriOppED = $request->post("deletejustBargiriOppED");
                $editjustBargiriOppED = $request->post("editjustBargiriOppED");
                $seejustBargiriOppED = $request->post("seejustBargiriOppED");

                $justBargiriOppED= 0;
                if($deletejustBargiriOppED=="on"){
                $justBargiriOppED=2;
                }elseif($editjustBargiriOppED=="on" and $deletejustBargiriOppED!="on"){
                $justBargiriOppED=1;
                }elseif($editjustBargiriOppED!="on" and $seejustBargiriOppED=="on"){
                    $justBargiriOppED=0;
                }else{
                    $justBargiriOppED=-1;
                }
            }else{
                $justBargiriOppED=-1;
                $justBargiriTopOppED=-1;
            }

        }else{
            $oppManagerED=-1;
            $oppHeadED=-1;
            $oppBazaryabED=-1;
            $oppTakhsisED=-1;
            $oppDriverServiceED=-1;
            $oppBargiriED=-1;
            $oppDriverED=-1;
            $todayoppNazarsanjiED=-1;
            $pastoppNazarsanjiED=-1;
            $DoneoppNazarsanjiED=-1;
            $oppNazarSanjiED=-1;
            $AddOppupDownBonusED=-1;
            $SubOppupDownBonusED=-1;
            $OppupDownBonusED=-1;
            $AddedoppRDED=-1;
            $NotAddedoppRDED=-1;
            $oppRDED=-1;
            $oppjustCalendarED=-1;
            $oppCustCalendarED=-1;
            $oppCalendarED=-1;
            $allalarmoppED=-1;
            $donealarmoppED=-1;
            $NoalarmoppED=-1;
            $alarmoppED=-1;
            $massageOppED=-1;
            $justBargiriOppED=-1;
            $oppED=-1;

        }

        
        // اگر گزارشات آن بود 
        $reportED = $request->post("reportED");
        $amalKardreportED;
        $managerreportED;
        $HeadreportED;
        $poshtibanreportED;
        $bazaryabreportED;
        $reportDriverED;
        $amalkardCustReportED;
        $loginCustRepED;
        $inActiveCustRepED;
        $noAdminCustRepED;
        $returnedCustRepED;
        $trazEmployeeReportED;
        $nosalegoodsReportED;
        $NoExistgoodsReportED;
        $returnedgoodsReportED;
        $salegoodsReportED;
        $goodsReportED;
        $returnedEDTasReportgoodsReportED;
        $tasgoodsReprtED;
        $returnedReportgoodsReportED;
        $goodsbargiriReportED;
        if($reportED =="on"){
            $reportED=1;
            // اگر عملکرد کاربران آن بود 
            $amalKardreportED = $request->post("amalKardreportED");
            if($amalKardreportED=="on"){
                $amalKardreportED=1;
                // مدیران با سه تا عناصر اش چک میگردد
                $managerreportED = $request->post("managerreportED");

                $deletemanagerreportED = $request->post("deletemanagerreportED");
                $editmanagerreportED = $request->post("editmanagerreportED");
                $seemanagerreportED = $request->post("seemanagerreportED");

                if($managerreportED=="on"){
                    if($deletemanagerreportED=="on"){
                        $managerreportED=2;
                    }elseif($editmanagerreportED=="on" and $deletemanagerreportED!="on"){
                        $managerreportED=1;
                    }elseif($editmanagerreportED!="on" and $seemanagerreportED=="on"){
                        $managerreportED=0;
                    }else{
                        $managerreportED=-1;
                    }
                }else{
                    $managerreportED=-1;
                }

                // سرپرستان با سه تا عناصر اش چک میگردد
                    $HeadreportED = $request->post("HeadreportED");

                $deleteHeadreportED = $request->post("deleteHeadreportED");
                $editHeadreportED = $request->post("editHeadreportED");
                $seeHeadreportED = $request->post("seeHeadreportED");

                if($HeadreportED=="on"){
                    if($deleteHeadreportED=="on"){
                        $HeadreportED=2;
                    }elseif($editHeadreportED=="on" and $deleteHeadreportED!="on"){
                        $HeadreportED=1;
                    }elseif($editHeadreportED!="on" and $seeHeadreportED=="on"){
                        $HeadreportED=0;
                    }else{
                        $HeadreportED=-1;
                    }
                }else{
                    $HeadreportED=-1;
                }

                // پشتیبانها با سه از عناصر اش چک میگردد
                $poshtibanreportED = $request->post("poshtibanreportED");

                $deleteposhtibanreportED = $request->post("deleteposhtibanreportED");
                $editposhtibanreportED = $request->post("editposhtibanreportED");
                $seeposhtibanreportED = $request->post("seeposhtibanreportED");

                if($poshtibanreportED=="on"){
                    if($deleteposhtibanreportED=="on"){
                        $poshtibanreportED=2;
                    }elseif($editposhtibanreportED=="on" and $deleteposhtibanreportED!="on"){
                        $poshtibanreportED=1;
                    }elseif($editposhtibanreportED!="on" and $seeposhtibanreportED=="on"){
                        $poshtibanreportED=0;
                    }else{
                        $poshtibanreportED=-1;
                    }
                }else{
                    $poshtibanreportED=-1;
                }

                // بازار یابها چک میگردد
                $bazaryabreportED = $request->post("bazaryabreportED");

                $deletebazaryabreportED = $request->post("deletebazaryabreportED");
                $editbazaryabreportED = $request->post("editbazaryabreportED");
                $seebazaryabreportED = $request->post("seebazaryabreportED");

                if($bazaryabreportED=="on"){
                    if($deletebazaryabreportED=="on"){
                    $bazaryabreportED=2;
                    }elseif($editbazaryabreportED=="on" and $deletebazaryabreportED!="on"){
                    $bazaryabreportED=1;
                    }elseif($editbazaryabreportED!="on" and $seebazaryabreportED=="on"){
                        $bazaryabreportED=0;
                    }else{
                        $bazaryabreportED=-1;
                    }
                }else{
                    $bazaryabreportED=-1;
                }

                // راننده ها چک میگردد
                    $reportDriverED = $request->post("reportDriverED");

                $deletereportDriverED = $request->post("deletereportDriverED");
                $editreportDriverED = $request->post("editreportDriverED");
                $seereportDriverED = $request->post("seereportDriverED");

                if($reportDriverED=="on"){
                    if($deletereportDriverED=="on"){
                        $reportDriverED=2;
                    }elseif($editreportDriverED=="on" and $deletereportDriverED!="on"){
                        $reportDriverED=1;
                    }elseif($editreportDriverED!="on" and $seereportDriverED=="on"){
                        $reportDriverED=0;
                    }else{
                        $reportDriverED=-1;
                    }
                }else{
                    $reportDriverED=-1;
                }

            }else{
                $amalKardreportED=-1;
                $managerreportED = -1;
                $HeadreportED = -1;
                $poshtibanreportED = -1;
                $bazaryabreportED = -1;
                $reportDriverED = -1;
            }
            //  تراز کاربران 
              $trazEmployeeReportED = $request->post("trazEmployeeReportED");

                $deletetrazEmployeeReportED = $request->post("deletetrazEmployeeReportED");
                $edittrazEmployeeReportED = $request->post("edittrazEmployeeReportED");
                $seetrazEmployeeReportED = $request->post("seetrazEmployeeReportED");

                if($trazEmployeeReportED=="on"){
                    if($deletetrazEmployeeReportED=="on"){
                        $trazEmployeeReportED=2;
                    }elseif($edittrazEmployeeReportED=="on" and $deletetrazEmployeeReportED!="on"){
                        $trazEmployeeReportED=1;
                    }elseif($edittrazEmployeeReportED!="on" and $seetrazEmployeeReportED=="on"){
                        $trazEmployeeReportED=0;
                    }else{
                        $trazEmployeeReportED=-1;
                    }
                }else{
                    $trazEmployeeReportED=-1;
                }

                // اگر عملکرد مشتریان آن بود
                 $amalkardCustReportED = $request->post("amalkardCustReportED");
                 if($amalkardCustReportED=="on"){
                    $amalkardCustReportED = 1;
                    // گزارش ورود چک گردد
                       $loginCustRepED = $request->post("loginCustRepED");

                        $deleteloginCustRepED = $request->post("deleteloginCustRepED");
                        $editloginCustRepED = $request->post("editloginCustRepED");
                        $seeloginCustRepED = $request->post("seeloginCustRepED");

                        if($loginCustRepED=="on"){
                            if($deleteloginCustRepED=="on"){
                            $loginCustRepED=2;
                            }elseif($editloginCustRepED=="on" and $deleteloginCustRepED!="on"){
                            $loginCustRepED=1;
                            }elseif($editloginCustRepED!="on" and $seeloginCustRepED=="on"){
                                $loginCustRepED=0;
                            }else{
                                $loginCustRepED=-1;
                            }
                        }else{
                            $loginCustRepED=-1;
                        }

                        // مشتریان غیر فعال چک گردد
                         $inActiveCustRepED = $request->post("inActiveCustRepED");

                        $deleteinActiveCustRepED = $request->post("deleteinActiveCustRepED");
                        $editinActiveCustRepED = $request->post("editinActiveCustRepED");
                        $seeinActiveCustRepED = $request->post("seeinActiveCustRepED");

                        if($inActiveCustRepED=="on"){
                            if($deleteinActiveCustRepED=="on"){
                                $inActiveCustRepED=2;
                            }elseif($editinActiveCustRepED=="on" and $deleteinActiveCustRepED!="on"){
                                $inActiveCustRepED=1;
                            }elseif($editinActiveCustRepED!="on" and $seeinActiveCustRepED=="on"){
                                $inActiveCustRepED=0;
                            }else{
                                    $inActiveCustRepED=-1;
                            }
                        }else{
                            $inActiveCustRepED=-1;
                        }


                        // فاقد کاربر چک گردد
                        $noAdminCustRepED = $request->post("noAdminCustRepED");

                        $deletenoAdminCustRepED = $request->post("deletenoAdminCustRepED");
                        $editnoAdminCustRepED = $request->post("editnoAdminCustRepED");
                        $seenoAdminCustRepED = $request->post("seenoAdminCustRepED");

                        if($noAdminCustRepED=="on"){
                            if($deletenoAdminCustRepED=="on"){
                                $noAdminCustRepED=2;
                            }elseif($editnoAdminCustRepED=="on" and $deletenoAdminCustRepED!="on"){
                                $noAdminCustRepED=1;
                            }elseif($editnoAdminCustRepED!="on" and $seenoAdminCustRepED=="on"){
                                $noAdminCustRepED=0;
                            }else{
                                $noAdminCustRepED=-1;
                            }
                        }else{
                            $noAdminCustRepED=-1;
                        }

                        // مشتریان ارجاعی چک گردد
                        $returnedCustRepED = $request->post("returnedCustRepED");
                        $deletereturnedCustRepED = $request->post("deletereturnedCustRepED");
                        $editreturnedCustRepED = $request->post("editreturnedCustRepED");
                        $seereturnedCustRepED = $request->post("seereturnedCustRepED");

                        if($returnedCustRepED=="on"){
                            if($deletereturnedCustRepED=="on"){
                                $returnedCustRepED=2;
                            }elseif($editreturnedCustRepED=="on" and $deletereturnedCustRepED!="on"){
                                $returnedCustRepED=1;
                            }elseif($editreturnedCustRepED!="on" and $seereturnedCustRepED=="on"){
                                $returnedCustRepED=0;
                            }else{
                                    $returnedCustRepED=-1;
                            }
                        }else{
                            $returnedCustRepED=-1;
                        }

                 }else {
                    $amalkardCustReportED=-1;
                    $loginCustRepED = -1;
                    $inActiveCustRepED = -1;
                    $noAdminCustRepED = -1;
                    $returnedCustRepED = -1;
                 }


                // اگر عملکرد کالا آن بود 
                 $goodsReportED = $request->post("goodsReportED");
                 if($goodsReportED=="on"){
                    $goodsReportED = 1;
                    //گزارش فروش کالا چک گردد
                    $salegoodsReportED = $request->post("salegoodsReportED");
                    $deletesalegoodsReportED = $request->post("deletesalegoodsReportED");
                    $editsalegoodsReportED = $request->post("editsalegoodsReportED");
                    $seesalegoodsReportED = $request->post("seesalegoodsReportED");

                    if($salegoodsReportED=="on"){
                        if($deletesalegoodsReportED=="on"){
                            $salegoodsReportED=2;
                        }elseif($editsalegoodsReportED=="on" and $deletesalegoodsReportED!="on"){
                            $salegoodsReportED=1;
                        }elseif($editsalegoodsReportED!="on" and $seesalegoodsReportED=="on"){
                            $salegoodsReportED=0;
                        }else{
                            $salegoodsReportED=-1;
                        }
                    }else{
                        $salegoodsReportED=-1;
                    }

                    // کالاهای برگشتی چک گردد
                    $returnedgoodsReportED = $request->post("returnedgoodsReportED");
                        $deletereturnedgoodsReportED = $request->post("deletereturnedgoodsReportED");
                        $editreturnedgoodsReportED = $request->post("editreturnedgoodsReportED");
                        $seereturnedgoodsReportED = $request->post("seereturnedgoodsReportED");

                        if($returnedgoodsReportED=="on"){
                            if($deletereturnedgoodsReportED=="on"){
                                $returnedgoodsReportED=2;
                            }elseif($editreturnedgoodsReportED=="on" and $deletereturnedgoodsReportED!="on"){
                                $returnedgoodsReportED=1;
                            }elseif($editreturnedgoodsReportED!="on" and $seereturnedgoodsReportED=="on"){
                                $returnedgoodsReportED=0;
                            }else{
                                $returnedgoodsReportED=-1;
                            }
                        }else{
                            $returnedgoodsReportED=-1;
                        }

                    // کالاهای فاقد موجودی چک گردد
                    $NoExistgoodsReportED = $request->post("NoExistgoodsReportED");

                        $deleteNoExistgoodsReportED = $request->post("deleteNoExistgoodsReportED");
                        $editNoExistgoodsReportED = $request->post("editNoExistgoodsReportED");
                        $seeNoExistgoodsReportED = $request->post("seeNoExistgoodsReportED");

                        if($NoExistgoodsReportED=="on"){
                            if($deleteNoExistgoodsReportED=="on"){
                                $NoExistgoodsReportED=2;
                            }elseif($editNoExistgoodsReportED=="on" and $deleteNoExistgoodsReportED!="on"){
                                $NoExistgoodsReportED=1;
                            }elseif($editNoExistgoodsReportED!="on" and $seeNoExistgoodsReportED=="on"){
                                $NoExistgoodsReportED=0;
                            }else{
                                $NoExistgoodsReportED=-1;
                            }
                        }else{
                            $NoExistgoodsReportED=-1;
                        }

                        // کالاهای راکت چک گردد

                        $nosalegoodsReportED = $request->post("nosalegoodsReportED");

                        $deletenosalegoodsReportED = $request->post("deletenosalegoodsReportED");
                        $editnosalegoodsReportED = $request->post("editnosalegoodsReportED");
                        $seenosalegoodsReportED = $request->post("seenosalegoodsReportED");

                        if($nosalegoodsReportED=="on"){
                            if($deletenosalegoodsReportED=="on"){
                                $nosalegoodsReportED=2;
                            }elseif($editnosalegoodsReportED=="on" and $deletenosalegoodsReportED!="on"){
                                $nosalegoodsReportED=1;
                            }elseif($editnosalegoodsReportED!="on" and $seenosalegoodsReportED=="on"){
                                $nosalegoodsReportED=0;
                            }else{
                                $nosalegoodsReportED=-1;
                            }
                        }else{
                            $nosalegoodsReportED=-1;
                        }
                 }else {
                    $nosalegoodsReportED=-1;
                    $NoExistgoodsReportED=-1;
                    $returnedgoodsReportED=-1;
                    $salegoodsReportED=-1;
                    $goodsReportED=-1;
                 }
          
            //    اگر گزارش برگشتی کالا آن بود 
            $returnedReportgoodsReportED = $request->post("returnedReportgoodsReportED");
                if($returnedReportgoodsReportED=="on"){
                    $returnedReportgoodsReportED = 1;
                    // تسویه نشده ها چک گردد
                    $returnedNTasReportgoodsReportED = $request->post("returnedNTasReportgoodsReportED");
                    $deletereturnedNTasReportgoodsReportED = $request->post("deletereturnedNTasReportgoodsReportED");
                    $editreturnedNTasReportgoodsReportED = $request->post("editreturnedNTasReportgoodsReportED");
                    $seereturnedNTasReportgoodsReportED = $request->post("seereturnedNTasReportgoodsReportED");

                    if($returnedNTasReportgoodsReportED=="on"){
                        if($deletereturnedNTasReportgoodsReportED=="on"){
                            $returnedNTasReportgoodsReportED=2;
                        }elseif($editreturnedNTasReportgoodsReportED=="on" and $deletereturnedNTasReportgoodsReportED!="on"){
                            $returnedNTasReportgoodsReportED=1;
                        }elseif($editreturnedNTasReportgoodsReportED!="on" and $seereturnedNTasReportgoodsReportED=="on"){
                            $returnedNTasReportgoodsReportED=0;
                        }else{
                            $returnedNTasReportgoodsReportED=-1;
                        }
                    }else{
                        $returnedEDTasReportgoodsReportED=-1;
                    }

                        // تسویه شده ها چک گردد
                    $tasgoodsReprtED = $request->post("tasgoodsReprtED");

                    $deletetasgoodsReprtED = $request->post("deletetasgoodsReprtED");
                    $edittasgoodsReprtED = $request->post("edittasgoodsReprtED");
                    $seetasgoodsReprtED = $request->post("seetasgoodsReprtED");

                    if($tasgoodsReprtED=="on"){
                        if($deletetasgoodsReprtED=="on"){
                            $tasgoodsReprtED=2;
                        }elseif($editreturnedEDTasReportgoodsReportED=="on" and $deletetasgoodsReprtED!="on"){
                            $tasgoodsReprtED=1;
                        }elseif($editreturnedEDTasReportgoodsReportED!="on" and $seereturnedEDTasReportgoodsReportED=="on"){
                            $tasgoodsReprtED=0;
                        }else{
                            $tasgoodsReprtED=-1;
                        }
                    }else{
                        $tasgoodsReprtED=-1;
                    }
                }else{
                    $returnedEDTasReportgoodsReportED=-1;
                    $tasgoodsReprtED=-1;
                    $returnedReportgoodsReportED=-1;
                }
  
                // گزارش بارگیری چک گردد

                $goodsbargiriReportED = $request->post("goodsbargiriReportED");
        
                $deletegoodsbargiriReportED = $request->post("deletegoodsbargiriReportED");
                $editgoodsbargiriReportED = $request->post("editgoodsbargiriReportED");
                $seegoodsbargiriReportED = $request->post("seegoodsbargiriReportED");

                if($goodsbargiriReportED=="on"){
                    if($deletegoodsbargiriReportED=="on"){
                        $goodsbargiriReportED=2;
                    }elseif($editgoodsbargiriReportED=="on" and $deletegoodsbargiriReportED!="on"){
                        $goodsbargiriReportED=1;
                    }elseif($editgoodsbargiriReportED!="on" and $seegoodsbargiriReportED=="on"){
                        $goodsbargiriReportED=0;
                    }else{
                        $goodsbargiriReportED=-1;
                    }
                }else{
                    $goodsbargiriReportED=-1;
                }

        }else {
            $reportED=-1;
            $amalKardreportED=-1;
            $managerreportED=-1;
            $HeadreportED=-1;
            $poshtibanreportED=-1;
            $bazaryabreportED=-1;
            $reportDriverED=-1;
            $amalkardCustReportED=-1;
            $loginCustRepED=-1;
            $inActiveCustRepED=-1;
            $noAdminCustRepED=-1;
            $returnedCustRepED=-1;
            $trazEmployeeReportED=-1;
            $nosalegoodsReportED=-1;
            $NoExistgoodsReportED=-1;
            $returnedgoodsReportED=-1;
            $salegoodsReportED=-1;
            $goodsReportED=-1;
            $returnedNTasReportgoodsReportED=-1;
            $tasgoodsReprtED=-1;
            $returnedReportgoodsReportED=-1;
            $goodsbargiriReportED=-1;
        }
        $bossId=0;
        $saleLineSn=0;

        if($manager){
            $bossId=$manager;  
        }

        if($head){
            $bossId=$head;  
        }
        
        if($saleLine){
            $saleLineSn=$saleLine;
        }
        
        if($picture){
            $fileName=$picture->getClientOriginalName();
            $maxId=0;
            $maxId=DB::table("CRM.dbo.crm_admin")->max('id');
            if($maxId>1){
                $maxId=$maxId+1;
            }else{
                $maxId=1;
            }
            $fileName=$maxId.".jpg";
            $picture->move("resources/assets/images/admins/",$fileName);
        }

          DB::table("CRM.dbo.crm_hasAccess3")->WHERE()->update(
            [ "adminId"=>$lastId
           , "declareElementOppN"=>$declareElementOppED
            
            ,"baseInfoN"=>$baseInfoED
                    
            ,"baseInfoProfileN"=>$baseInfoProfileED
             
            ,"infoRdN"=>$infoRdED
                      
            ,"specialSettingN"=>$specialSettingED
              
            ,"baseInfoSettingN"=>$baseInfoSettingED
             
            ,"InfoSettingAccessN"=>$InfoSettingAccessED
           
            ,"InfoSettingTargetN"=>$InfoSettingTargetED
           
            ,"rdSentN"=>$rdSentED
                      
            ,"rdNotSentN"=>$rdNotSentED
                   
            ,"oppManagerN"=>$oppManagerED
                  
            ,"oppHeadN"=>$oppHeadED
                     
            ,"oppBazaryabN"=>$oppBazaryabED
                 
            ,"oppTakhsisN"=>$oppTakhsisED
                  
            ,"oppDriverServiceN"=>$oppDriverServiceED
            
            ,"oppBargiriN"=>$oppBargiriED
                  
            ,"oppDriverN"=>$oppDriverED
                   
            ,"todayoppNazarsanjiN"=>$todayoppNazarsanjiED
          
            ,"pastoppNazarsanjiN"=>$pastoppNazarsanjiED
           
            ,"DoneoppNazarsanjiN"=>$DoneoppNazarsanjiED
           
            ,"oppNazarSanjiN"=>$oppNazarSanjiED
               
            ,"AddOppupDownBonusN"=>$AddOppupDownBonusED
           
            ,"SubOppupDownBonusN"=>$SubOppupDownBonusED
           
            ,"OppupDownBonusN"=>$OppupDownBonusED
              
            ,"AddedoppRDN"=>$AddedoppRDED
                  
            ,"NotAddedoppRDN"=>$NotAddedoppRDED
               
            ,"oppRDN"=>$oppRDED
                       
            ,"oppjustCalendarN"=>$oppjustCalendarED
             
            ,"oppCustCalendarN"=>$oppCustCalendarED
             
            ,"oppCalendarN"=>$oppCalendarED
                 
            ,"allalarmoppN"=>$allalarmoppED
                 
            ,"donealarmoppN"=>$donealarmoppED
                
            ,"NoalarmoppN"=>$NoalarmoppED
                  
            ,"alarmoppN"=>$alarmoppED
                    
            ,"massageOppN"=>$massageOppED
                  
            ,"justBargiriOppN"=>$justBargiriOppED
              
            ,"oppN"=>$oppED
                         
            ,"reportN"=>$reportED
                      
            ,"amalKardreportN"=>$amalKardreportED
              
            ,"managerreportN"=>$managerreportED
               
            ,"HeadreportN"=>$HeadreportED
                  
            ,"poshtibanreportN"=>$poshtibanreportED
             
            ,"bazaryabreportN"=>$bazaryabreportED
              
            ,"reportDriverN"=>$reportDriverED
                
            ,"amalkardCustReportN"=>$amalkardCustReportED
          
            ,"loginCustRepN"=>$loginCustRepED
                
            ,"inActiveCustRepN"=>$inActiveCustRepED
             
            ,"noAdminCustRepN"=>$noAdminCustRepED
              
            ,"returnedCustRepN"=>$returnedCustRepED
             
            ,"trazEmployeeReportN"=>$trazEmployeeReportED
          
            ,"nosalegoodsReportN"=>$nosalegoodsReportED
           
            ,"NoExistgoodsReportN"=>$NoExistgoodsReportED
          
            ,"returnedgoodsReportN"=>$returnedgoodsReportED
         
            ,"salegoodsReportN"=>$salegoodsReportED
             
            ,"goodsReportN"=>$goodsReportED
                 
            ,"returnedNTasReportgoodsReportN"=>$returnedNTasReportgoodsReportED
            ,"tasgoodsReprtN"=>$tasgoodsReprtED
               
            ,"returnedReportgoodsReportN"=>$returnedReportgoodsReportED
   
            ,"goodsbargiriReportNrtN"=>$goodsbargiriReportED
       
            ,"goodsbargiriReportN"=>$goodsbargiriReportED
          ]);