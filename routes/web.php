<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Product;
use App\Http\Middleware\CheckAdmin;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\SalseExper;
use App\Http\Controllers\Poshtiban;
use App\Http\Controllers\SaleLine;
//poshtiban routes
Route::get('/customers',[Customer::class,'index'])->middleware('CheckPoshtiban');
Route::post('/changeDate',[Admin::class,'changeDate'])->middleware('CheckCommon');
Route::get("/calendar",[Admin::class,"myCalendar"])->middleware('CheckCommon');
Route::get("/getCustomerForTimeTable",[Customer::class,"getCustomerForTimeTable"])->middleware('CheckCommon');
Route::get('/myCustomers',[Customer::class,'myCustomers'])->middleware('CheckPoshtiban');
Route::get('/getCustomerInfo',[Customer::class,'getCustomerInfo'])->middleware('CheckCommon');
Route::post('/editCustomer',[Customer::class,'editCustomer'])->middleware('CheckCommon');
Route::get('/getRandTInfo',[Customer::class,'getRandTInfo'])->middleware('CheckCommon');
Route::post('/editRT',[Customer::class,'editRT'])->middleware('CheckCommon');

//common routes for all
Route::get("/addComment",[Customer::class,"addComment"])->middleware('CheckCommon');
Route::get("/message",[Admin::class,"message"]);
Route::get("/customerDashboard",[Customer::class,"customerDashboard"])->middleware('CheckCommon');
Route::get("/getFactorDetail",[Customer::class,"getFactorDetail"])->middleware('CheckCommon');
Route::get("/addAssessment",[Customer::class,"addAssessment"])->middleware('CheckCommon');
Route::get("/addAssessmentPast",[Customer::class,"addAssessmentPast"])->middleware('CheckCommon');
Route::get("/setCommentProperty",[Customer::class,"setCommentProperty"])->middleware('CheckCommon');
Route::get("/logoutUser",[Admin::class,"logoutUser"])->middleware('CheckCommon');
Route::get("/userProfile",[Admin::class,"userProfile"])->middleware('CheckCommon');
Route::post("/editOwnAdmin",[Admin::class,"editOwnAdmin"])->middleware('CheckCommon');

Route::get("/getAdminInfo",[Admin::class,"getAdminInfo"])->middleware('CheckCommon');

Route::get("/addMessage",[Admin::class,"addMessage"])->middleware('CheckCommon');
Route::get("/getDiscusstion",[Admin::class,"getDiscusstion"])->middleware('CheckCommon');
Route::get("/addDiscussion",[Admin::class,"addDiscussion"])->middleware('CheckCommon');
Route::get("/searchCustomerByName",[Customer::class,"searchCustomerByName"])->middleware('CheckCommon');
Route::get("/searchCustomerByMantagheh",[Customer::class,"searchCustomerByMantagheh"])->middleware('CheckCommon');
Route::get("/searchAllCustomerByMantagheh",[Customer::class,"searchAllCustomerByMantagheh"])->middleware('CheckCommon');
Route::get("/searchCustomerByCode",[Customer::class,"searchCustomerByCode"])->middleware('CheckCommon');
Route::get("/addAlarmClock",[Admin::class,"addAlarmClock"])->middleware('CheckCommon');
Route::get("/getAlarmTime",[Admin::class,"getAlarmTime"])->middleware('CheckCommon');
Route::get("/offAlarmClock",[Admin::class,"offAlarmClock"])->middleware('CheckCommon');
Route::get("/orderByNameCode",[Customer::class,"orderByNameCode"])->middleware('CheckCommon');
Route::get("/searchPastAssesByDate",[Customer::class,"searchPastAssesByDate"])->middleware('CheckCommon');
Route::get("/searchDoneAssesByDate",[Customer::class,"searchDoneAssesByDate"])->middleware('CheckCommon');
Route::get("/searchCustomerByRegion",[Customer::class,"searchCustomerByRegion"])->middleware('CheckCommon');
Route::get("/searchRegion",[Customer::class,"searchRegion"])->middleware('CheckCommon');
Route::get("/searchAssignRegion",[Customer::class,"searchAssignRegion"])->middleware('CheckCommon');

Route::get("/reports",[Admin::class,"report"])->middleware('CheckCommon');

//[drivers]
Route::get("/crmDriver",[DriverController::class,"crmDriver"])->middleware('CheckCommon');
// for searching 
Route::get("/crmDriverSearch",[DriverController::class,"crmDriverSearch"])->middleware('CheckCommon');
Route::get("/searchMapByFactor",[Customer::class,"searchMapByFactor"])->middleware('CheckCommon');
Route::get("/getFactorInfo",[DriverController::class,"getFactorInfo"])->middleware('CheckCommon');
Route::get("/filterNewCustomers",[Customer::class,"filterNewCustomers"])->middleware('CheckCommon');

//Admins routes

Route::get('/getAdminHistoryComment',[Admin::class,'getAdminHistoryComment'])->middleware('checkUser');
Route::get('/',[Admin::class,'crmTerminal'])->middleware('CheckCommon');
Route::get('/home', [Admin::class,'dashboard'])->middleware('CheckCommon');
Route::post('/addCustomer',[Customer::class,'addCustomer'])->middleware('CheckCommon');
Route::get("/getCustomer",[Customer::class,"getCustomer"])->middleware('checkUser');
Route::post("/assesCustomer",[Customer::class,"assesCustomer"])->middleware('CheckCommon');
Route::get('/newCustomer',[Customer::class,'newCustomer'])->middleware('checkUser');
Route::get("/assignCustomer",[Admin::class,'index'])->middleware('checkUser');
Route::get("/editAssignCustomer",[Admin::class,'editAssignCustomer'])->middleware('CheckCommon');
Route::post("/addAdmin",[Admin::class,'AddAdmin'])->middleware('checkUser');
Route::post("/addAdminFromList",[Admin::class,'addAdminFromList'])->middleware('checkUser');
Route::get("/AddCustomerToAdmin",[Admin::class,"AddCustomerToAdmin"])->middleware('checkUser');
Route::get("/RemoveCustomerFromAdmin",[Admin::class,"RemoveCustomerFromAdmin"])->middleware('checkUser');

Route::get("/karbarAction",[Admin::class,"karbarAction"])->middleware('checkUser');

Route::get("/amalKardKarbarn",[Admin::class,"amalKardKarbarn"])->middleware('CheckCommon');

Route::get("/getProducts",[Product::class,"getProducts"])->middleware('CheckCommon');
Route::get("/getProductMainGroups",[Product::class,"getProductMainGroups"])->middleware('CheckCommon');
Route::get("/getSubGroups",[Product::class,"getSubGroups"])->middleware('CheckCommon');
Route::get("/getTenLastSales",[Product::class,"getTenLastSales"])->middleware('CheckCommon');
Route::get("/filterAllKala",[Product::class,"filterAllKala"])->middleware('CheckCommon');
Route::get("/getGoodSalesRound",[Product::class,"getGoodSalseRounds"])->middleware('CheckCommon');
Route::get("/getReturnedKala",[Product::class,"getReturnedKala"])->middleware('CheckCommon');
Route::get("/getRakidKala",[Product::class,"getRakidKala"])->middleware('CheckCommon');
Route::get("/sendBackReport",[Admin::class,"sendBackReport"])->middleware('CheckCommon');
Route::get("/getReturnedFactors",[Product::class,"getReturnedFactors"])->middleware('CheckCommon');
Route::get("/getStocks",[Product::class,"getStocks"])->middleware('CheckCommon');
Route::get("/getFactorSetter",[Product::class,"getFactorSetter"])->middleware('CheckCommon');
Route::get("/getReturnedFactorsHistory",[Product::class,"getReturnedFactorsHistory"])->middleware('CheckCommon');
Route::get("/commentToday",[Customer::class,"todayComment"])->middleware('CheckCommon');
Route::get("/commentPast",[Customer::class,"pastComment"])->middleware('CheckCommon');
Route::get("/commentDone",[Customer::class,"doneComment"])->middleware('CheckCommon');
Route::get("/alarm",[Admin::class,"alarm"])->middleware('CheckCommon');
Route::get("/customerDashboardForAlarm",[Admin::class,"customerDashboardForAlarm"])->middleware('CheckCommon');
Route::get("/inactiveCustomerAlarm",[Customer::class,"inactiveCustomerAlarm"])->middleware('CheckCommon');
Route::get("/activateCustomer",[Admin::class,"activateCustomer"])->middleware('CheckCommon');
Route::get("/changeAlarm",[Admin::class,"changeAlarm"])->middleware('CheckCommon');
Route::get("/getAlarmHistory",[Admin::class,"getAlarmHistory"])->middleware('CheckCommon');
Route::get("/searchCustomerAalarmName",[Customer::class,"searchCustomerAalarmName"])->middleware('CheckCommon');
Route::get("/searchCustomerAalarmCode",[Customer::class,"searchCustomerAalarmCode"])->middleware('CheckCommon');
Route::get("/searchCustomerAalarmLocation",[Customer::class,"searchCustomerAalarmLocation"])->middleware('CheckCommon');
Route::get("/searchCustomerAalarmActive",[Customer::class,"searchCustomerAalarmActive"])->middleware('CheckCommon');
Route::get("/searchCustomerAalarmOrder",[Customer::class,"searchCustomerAalarmOrder"])->middleware('CheckCommon');
Route::get("/searchNewCustomerByName",[Customer::class,"searchNewCustomerByName"])->middleware('CheckCommon');
Route::get("/deleteAdmin",[Admin::class,"deleteAdmin"])->middleware('CheckCommon');

Route::get("/referedCustomer",[Admin::class,"referedCustomer"])->middleware('checkUser');
Route::get("/visitorReport",[Admin::class,"visitorReport"])->middleware('checkUser');
Route::get("/searchVisotrsByDate",[Admin::class,"searchVisotrsByDate"])->middleware('checkUser');
Route::get("/searchVisotrsLoginFrom",[Admin::class,"searchVisotrsLoginFrom"])->middleware('checkUser');
Route::get("/filterAllLogins",[Customer::class,"filterAllLogins"])->middleware('CheckCommon');
Route::get("/filterInactiveCustomers",[Customer::class,"filterInactiveCustomers"])->middleware('CheckCommon');
Route::get("/searchSameTimeCountLogin",[Admin::class,"searchSameTimeCountLogin"])->middleware('checkUser');
Route::get("/searchVisotrsLoginTo",[Admin::class,"searchVisotrsLoginTo"])->middleware('checkUser');
Route::get("/inactivCustomer",[Admin::class,"inactivCustomer"])->middleware('checkUser');
Route::get("/customerDashboardForAdmin",[Admin::class,"customerDashboardForAdmin"])->middleware('checkUser');
Route::get("/getFirstComment",[Customer::class,"getFirstComment"])->middleware('CheckCommon');
Route::get("/returnCustomer",[Customer::class,"returnCustomer"])->middleware('CheckCommon');
Route::get("/tempRoute",[Admin::class,"tempRoute"])->middleware('checkUser');
Route::get("/kalaAction",[Admin::class,"kalaAction"])->middleware('CheckCommon');
Route::get('/subGroupsEdit',[Admin::class,'subGroupsEdit'])->middleware('checkUser');
Route::get('/getUnitsForSettingMinSale',[Admin::class,'getUnitsForSettingMinSale'])->middleware('checkUser');
Route::get('/setMinimamSaleKala',[Admin::class,'setMinimamSaleKala'])->middleware('checkUser');
Route::get('/getUnitsForSettingMaxSale',[Admin::class,'getUnitsForSettingMaxSale'])->middleware('checkUser');
Route::get('/setMaximamSaleKala',[Admin::class,'setMaximamSaleKala'])->middleware('checkUser');
Route::get('/restrictSale',[Admin::class,'restrictSale'])->middleware('checkUser');
Route::post('/addpicture',[Admin::class,'changeKalaPic'])->middleware('checkUser');
Route::post('/addStockToList',[Admin::class,'addStockToList'])->middleware('checkUser');
Route::get('/getAllKalas',[Admin::class,'getAllKalas'])->middleware('checkUser');
Route::get('/addKalaToList',[Admin::class,'addKalaToList'])->middleware('checkUser');
Route::get('/addOrDeleteKalaFromSubGroup',[Admin::class,'addOrDeleteKalaFromSubGroup'])->middleware('checkUser');
Route::post('/addDescKala',[Admin::class,'setDescribeKala'])->middleware('checkUser');



Route::get("/kalaSettings",[Admin::class,"kalaSettings"])->middleware('checkUser');
Route::get("/adminDashboard",[Admin::class,"adminDashboard"])->middleware('checkUser');
Route::get("/getAdminTodayInfo",[Admin::class,"getAdminTodayInfo"])->middleware('checkUser');
Route::get("/takhsisCustomer",[Admin::class,"takhsisCustomer"])->middleware('CheckCommon');
Route::get("/takhsisNewCustomer",[Admin::class,"takhsisNewCustomer"])->middleware('checkUser');
Route::get("/takhsisCustomerFromEmpty",[Admin::class,"takhsisCustomerFromEmpty"])->middleware('checkUser');
Route::post("/loginUser",[Admin::class,"loginUser"]);
Route::get("/crmSetting",[Admin::class,"crmSetting"])->middleware('checkUser');
Route::get("/checkUserNameExistance",[Admin::class,"checkUserNameExistance"])->middleware('checkUser');
Route::get("/searchMap",[Customer::class,"searchMap"])->middleware('checkUser');
Route::get("/getAdminForEmpty",[Admin::class,"getAdminForEmpty"])->middleware('CheckCommon');
Route::get("/emptyAdmin",[Admin::class,"emptyAdmin"])->middleware('checkUser');
Route::get("/moveCustomerToAdmin",[Admin::class,"moveCustomerToAdmin"])->middleware('checkUser');
Route::get("/getAdminForMove",[Admin::class,"getAdminForMove"])->middleware('CheckCommon');
Route::post("/editAdmintStuff",[Admin::class,"editAdmintStuff"])->middleware('checkUser');
Route::post("/editAdmintListStuff",[Admin::class,"editAdmintListStuff"])->middleware('checkUser');
Route::get("/inactiveCustomer",[Customer::class,"inactiveCustomer"])->middleware('checkUser');
Route::get("/gotEmpty",[Admin::class,"gotEmpty"])->middleware('checkUser');
Route::get("/searchReferedCustomerName",[Admin::class,"searchReferedCustomerName"])->middleware('checkUser');
Route::get("/searchReferedPCode",[Customer::class,"searchReferedPCode"])->middleware('checkUser');
Route::get("/searchReturnedByDate",[Customer::class,"searchReturnedByDate"])->middleware('checkUser');
Route::get("/searchByReturner",[Customer::class,"searchByReturner"])->middleware('checkUser');
Route::get("/searchEmptyByName",[Customer::class,"searchEmptyByName"])->middleware('checkUser');
Route::get("/searchEmptyByPCode",[Customer::class,"searchEmptyByPCode"])->middleware('checkUser');
Route::get("/searchEmptyByDate",[Customer::class,"searchEmptyByDate"])->middleware('checkUser');
Route::get("/searchAllCustomerByName",[Customer::class,"searchAllCustomerByName"])->middleware('CheckCommon');
Route::get("/searchAllCustomerByPCode",[Customer::class,"searchAllCustomerByPCode"])->middleware('CheckCommon');
Route::get("/searchAllCustomerByAdmin",[Customer::class,"searchAllCustomerByAdmin"])->middleware('checkUser');
Route::get("/searchAllCustomerActiveOrNot",[Customer::class,"searchAllCustomerActiveOrNot"])->middleware('checkUser');
Route::get("/filterAllCustomer",[Customer::class,"filterAllCustomer"])->middleware('CheckCommon');
Route::get("/searchAllCustomerFactorOrNot",[Customer::class,"searchAllCustomerFactorOrNot"])->middleware('checkUser');
Route::get("/searchAllCustomerBasketOrNot",[Customer::class,"searchAllCustomerBasketOrNot"])->middleware('checkUser');
Route::get("/searchAllCustomerLoginOrNot",[Customer::class,"searchAllCustomerLoginOrNot"])->middleware('checkUser');

Route::get("/searchKalaNameCode",[Customer::class,"searchKalaNameCode"])->middleware('checkUser');

Route::get("/filterMap",[Customer::class,"filterMap"])->middleware('CheckCommon');

Route::get("/searchKalaByStock",[Customer::class,"searchKalaByStock"])->middleware('checkUser');
Route::get("/searchKalaByActiveOrNot",[Customer::class,"searchKalaByActiveOrNot"])->middleware('checkUser');
Route::get("/searchKalaByZeroOrNot",[Customer::class,"searchKalaByZeroOrNot"])->middleware('checkUser');
Route::get("/searchSubGroupKala",[Customer::class,"searchSubGroupKala"])->middleware('checkUser');
Route::get("/searchBySubGroupKala",[Customer::class,"searchBySubGroupKala"])->middleware('checkUser');
Route::get("/searchAdminByNameCode",[Admin::class,"searchAdminByNameCode"])->middleware('checkUser');
Route::get("/searchAdminByType",[Admin::class,"searchAdminByType"])->middleware('checkUser');
Route::get("/searchAdminByActivation",[Admin::class,"searchAdminByActivation"])->middleware('checkUser');
Route::get("/searchAdminFactorOrNot",[Admin::class,"searchAdminFactorOrNot"])->middleware('checkUser');
Route::get("/searchAdminLoginOrNot",[Admin::class,"searchAdminLoginOrNot"])->middleware('checkUser');
Route::get("/searchAdminCustomerLoginOrNot",[Admin::class,"searchAdminCustomerLoginOrNot"])->middleware('checkUser');
Route::get("/searchInActiveCustomerByName",[Customer::class,"searchInActiveCustomerByName"])->middleware('checkUser');
Route::get("/searchInActiveCustomerByCode",[Customer::class,"searchInActiveCustomerByCode"])->middleware('checkUser');
Route::get("/searchInActiveCustomerByLocation",[Customer::class,"searchInActiveCustomerByLocation"])->middleware('checkUser');
Route::get("/viewReturnComment",[Customer::class,"viewReturnComment"])->middleware('checkUser');
Route::get("/allCustomers",[Admin::class,"allCustomers"])->middleware('checkUser');
Route::get("/searchAllCustomerByCode",[Admin::class,"searchAllCustomerByCode"])->middleware('checkUser');
Route::get("/orderAllCustomerByCName",[Admin::class,"orderAllCustomerByCName"])->middleware('checkUser');
Route::get("/searchAddedCustomerByRegion",[Customer::class,"searchAddedCustomerByRegion"])->middleware('checkUser');
Route::get("/searchAddedCustomerByNameMNM",[Customer::class,"searchAddedCustomerByNameMNM"])->middleware('checkUser');
Route::get("/listKarbaran",[Admin::class,"listKarbaran"])->middleware('CheckCommon');
Route::get("/testRoute",[Admin::class,"testRoute"])->middleware('checkUser');
Route::get("/getAssesComment",[Admin::class,"getAssesComment"])->middleware('checkUser');
Route::get("/getCustomerLoginInfo",[Admin::class,"getCustomerLoginInfo"])->middleware('checkUser');

Route::get("/searchCustomerLocation",[Customer::class,"searchCustomerLocation"])->middleware('checkUser');
Route::get("/searchingCustomerName",[Customer::class,"searchingCustomerName"])->middleware('checkUser');

//no need to checking
Route::get('/login',[Admin::class,'login']);
Route::get("/changePosition",[Customer::class,"changePosition"])->middleware('checkUser');
Route::get("/updatePosition",[Customer::class,"updatePosition"])->middleware('CheckCommon');
Route::get("/setReciveMoneyDetail",[DriverController::class,"setReciveMoneyDetail"])->middleware('CheckCommon');
Route::get('/addProvinceCode',[Admin::class,'addProvinceCode'])->middleware('CheckCommon');

// search bargeri based on date
Route::get("/searchBargeriByDate",[DriverController::class,"searchBargeriByDate"])->middleware('CheckCommon');
Route::get('/downloadApk',[Admin::class,'downloadApk']);

Route::get("/bargeryInfo",[DriverController::class,"bargeryInfo"])->middleware('CheckCommon');
Route::get("/bargeryFactors",[DriverController::class,"bargeryFactors"])->middleware('CheckCommon');
Route::get("/giveFactor",[DriverController::class,"giveFactor"])->middleware('CheckCommon');

Route::get("/salesExpertAction",[SalseExper::class,"salesExpertAction"])->middleware('CheckCommon');
Route::get("/bonusSetting",[SalseExper::class,"bonusSetting"])->middleware('CheckCommon');
Route::get("/defineTarget",[SalseExper::class,"defineTarget"])->middleware('CheckCommon');
Route::get("/getSalesExpertSelfInfo",[SalseExper::class,"getSalesExpertSelfInfo"])->middleware('CheckCommon');
Route::get("/getTodaySelfInstalls",[SalseExper::class,"getTodaySelfInstalls"])->middleware('CheckCommon');
Route::get("/getTodaySelfBuyToday",[SalseExper::class,"getTodaySelfBuyToday"])->middleware('CheckCommon');
Route::get("/getAllNewInstallSelf",[SalseExper::class,"getAllNewInstallSelf"])->middleware('CheckCommon');
Route::get("/getAllNewBuySelf",[SalseExper::class,"getAllNewBuySelf"])->middleware('CheckCommon');
Route::get("/getSalesExpertSelfInfoByDates",[SalseExper::class,"getSalesExpertSelfInfoByDates"])->middleware('CheckCommon');
Route::get("/addTarget",[SalseExper::class,"addTarget"])->middleware('CheckCommon');
Route::get("/editTarget",[SalseExper::class,"editTarget"])->middleware('CheckCommon');
Route::get("/getTargetInfo",[SalseExper::class,"getTargetInfo"])->middleware('CheckCommon');
Route::get("/addSpecialBonus",[SalseExper::class,"addSpecialBonus"])->middleware('CheckCommon');
Route::get("/editSpecialBonus",[SalseExper::class,"editSpecialBonus"])->middleware('CheckCommon');
Route::get("/getSpecialBonusInfo",[SalseExper::class,"getSpecialBonusInfo"])->middleware('CheckCommon');
Route::get("/deleteSpecialBonus",[SalseExper::class,"deleteSpecialBonus"])->middleware('CheckCommon');
Route::get("/deleteTarget",[SalseExper::class,"deleteTarget"])->middleware('CheckCommon');
Route::get("/subTrees",[SalseExper::class,"subTrees"])->middleware('CheckCommon');

Route::get("/saleExpertActionInfo",[SalseExper::class,"saleExpertActionInfo"])->middleware('CheckCommon');

Route::get("/getAllBuyAghlamSelf",[SalseExper::class,"getAllBuyAghlamSelf"])->middleware('CheckCommon');
Route::get("/getTodayBuyAghlamSelf",[SalseExper::class,"getTodayBuyAghlamSelf"])->middleware('CheckCommon');
Route::get("/getAllBuyMoneySelf",[SalseExper::class,"getAllBuyMoneySelf"])->middleware('CheckCommon');
Route::get("/getTodayBuyMoneySelf",[SalseExper::class,"getTodayBuyMoneySelf"])->middleware('CheckCommon');
Route::get("/getThisDayMyCustomer",[Customer::class,"getThisDayMyCustomer"])->middleware('CheckCommon');
Route::get("/getThisDayCustomerForAdmin",[Customer::class,"getThisDayCustomerForAdmin"])->middleware('CheckCommon');

Route::get("/addUpDownBonus",[SalseExper::class,"addUpDownBonus"])->middleware('CheckCommon');
Route::get("/showAdminEmtyazHistory",[Admin::class,"showAdminEmtyazHistory"])->middleware('CheckCommon');
Route::get("/getAdminHistory",[Admin::class,"getAdminHistory"])->middleware('CheckCommon');
Route::get("/editEmtiyazHistory",[Admin::class,"editEmtiyazHistory"])->middleware('CheckCommon');
Route::get("/getActiveInactiveCustomers",[Customer::class,"getActiveInactiveCustomers"])->middleware('CheckCommon');
Route::get("/getGeneralBase",[SalseExper::class,"getGeneralBase"])->middleware('checkUser');
Route::post("/editGeneralTarget",[SalseExper::class,"editGeneralTarget"])->middleware('checkUser');

Route::get("/listPoshtibans",[Poshtiban::class,"getPostibanList"])->middleware('CheckCommon');
Route::get("/poshtibanActionInfo",[Poshtiban::class,"poshtibanActionInfo"])->middleware('CheckCommon');

Route::get("/subTrees",[SalseExper::class,"subTrees"])->middleware('CheckCommon');
Route::get("/getBossBazarYab",[SalseExper::class,"getBossBazarYab"])->middleware('CheckCommon');
Route::get("/getGeneralBonus",[SalseExper::class,"getGeneralBonus"])->middleware('CheckCommon');
Route::get("/editGeneralBonus",[SalseExper::class,"editGeneralBonus"])->middleware('CheckCommon');
Route::get("/getTodayBuyAghlamPoshtiban",[Poshtiban::class,"getTodayBuyAghlamPoshtiban"])->middleware('CheckCommon');
Route::get("/getAllBuyAghlamByAdmin",[Poshtiban::class,"getAllBuyAghlamPoshtiban"])->middleware('CheckCommon');
Route::get("/getAllBuyMoneyPoshtiban",[Poshtiban::class,"getAllBuyMoneyPoshtiban"])->middleware('CheckCommon');
Route::get("/getAllBuyMoneyTodayPoshtiban",[Poshtiban::class,"getAllBuyMoneyTodayPoshtiban"])->middleware('CheckCommon');
Route::get("/getAllNewBuyPoshtiban",[Poshtiban::class,"getAllNewBuyPoshtiban"])->middleware('CheckCommon');
Route::get("/getAllNewTodayBuyPoshtiban",[Poshtiban::class,"getAllNewTodayBuyPoshtiban"])->middleware('CheckCommon');
Route::get("/getPoshtibanActionInformation/{adminId}",[Poshtiban::class,"getPoshtibanActionInformation"])->middleware('checkUser');
Route::get("/getDriverTodayAghlam",[Poshtiban::class,"getDriverTodayAghlam"])->middleware('CheckCommon');
Route::get("/getDriverAllAghlam",[Poshtiban::class,"getDriverAllAghlam"])->middleware('CheckCommon');
Route::get("/getAllFactorDriver",[Poshtiban::class,"getAllFactorDriver"])->middleware('CheckCommon');
Route::get("/getTodayDriverFactors",[Poshtiban::class,"getTodayDriverFactors"])->middleware('CheckCommon');

Route::get("/driverService",[DriverController::class,"driverService"])->middleware('CheckCommon');

Route::get("/addService",[DriverController::class,"addService"])->middleware('CheckCommon');
Route::get("/getInfoForDriverService",[DriverController::class,"getInfoForDriverService"])->middleware('CheckCommon');
Route::get("/getServiceInfo",[DriverController::class,"getServiceInfo"])->middleware('CheckCommon');
Route::get("/editDriverService",[DriverController::class,"editDriverService"])->middleware('CheckCommon');
Route::get('/randt',[Customer::class,'randt'])->middleware('CheckCommon');
Route::post('/addRandT',[Customer::class,'addRandT'])->middleware('CheckCommon');


//بعد از تغیر ساختار
Route::get('/getAsses',[Customer::class,'getAsses'])->middleware('CheckCommon');
Route::get('/getDonCommentInfo',[Customer::class,'getDonCommentInfo'])->middleware('CheckCommon');
Route::get('/getDoneAsses',[Customer::class,'getDoneAsses'])->middleware('CheckCommon');
Route::get('/saleLine',[SaleLine::class,'index'])->middleware('CheckCommon');
Route::get('/addSaleLine',[SaleLine::class,'addSaleLine'])->middleware('CheckCommon');
Route::get('/getSaleLine',[SaleLine::class,'getSaleLine'])->middleware('CheckCommon');
Route::get('/editSaleLine',[SaleLine::class,'editSaleLine'])->middleware('CheckCommon');
Route::get('/deleteSaleLine',[SaleLine::class,'deleteSaleLine'])->middleware('CheckCommon');
Route::get('/getEmployees',[SaleLine::class,'getEmployees'])->middleware('CheckCommon');
Route::get('/getHeads',[Admin::class,'getHeads'])->middleware('CheckCommon');
Route::get('/addToHeadEmployee',[Admin::class,'addToHeadEmployee'])->middleware('CheckCommon');
Route::get('/bonusIncreaseDecrease',[SalseExper::class,'bonusIncreaseDecrease'])->middleware('CheckCommon');
Route::get('/karbaranOperations',[Admin::class,'karbaranOperations'])->middleware('CheckCommon');
//صفحه تخصیص جدید
Route::get('/getEmployies',[Admin::class,'getEmployies'])->middleware('CheckCommon');
Route::get('/getTakhsisEditRightSide',[Customer::class,'getTakhsisEditRightSide'])->middleware('CheckCommon');
Route::get('/getAddedCustomers',[Customer::class,'getAddedCustomers'])->middleware('CheckCommon');
Route::get('/getAdminEmptyState',[Admin::class,'getAdminEmptyState'])->middleware('CheckCommon');
Route::get('/EditAdminComment',[Admin::class,'EditAdminComment'])->middleware('CheckCommon');
Route::get('/searchDriverServices',[DriverController::class,'searchDriverServices'])->middleware('CheckCommon');
Route::get('/serviceOrder',[DriverController::class,'serviceOrder'])->middleware('CheckCommon');
Route::get('/getDriverServices',[DriverController::class,'getDriverServices'])->middleware('CheckCommon');
Route::get('/getUpDownBonusInfo',[SalseExper::class,'getUpDownBonusInfo'])->middleware('CheckCommon');
Route::get('/getUpDownBonusHistory',[SalseExper::class,'getUpDownBonusHistory'])->middleware('CheckCommon');
Route::get('/getHistorySearch',[SalseExper::class,'getHistorySearch'])->middleware('CheckCommon');
Route::get('/editUpDownBonus',[SalseExper::class,'editUpDownBonus'])->middleware('CheckCommon');
Route::get('/deleteUpDownBonus',[SalseExper::class,'deleteUpDownBonus'])->middleware('CheckCommon');
Route::get('/getCustomerAndAdminInfo',[Admin::class,'getCustomerAndAdminInfo'])->middleware('CheckCommon');
Route::get('/getAlarms',[Admin::class,'getAlarms'])->middleware('CheckCommon');
Route::get('/getAlarmInfo',[Admin::class,'getAlarmInfo'])->middleware('CheckCommon');
Route::get('/searchAlarms',[Admin::class,'searchAlarms'])->middleware('CheckCommon');
Route::get('/searchAlarmByMantagheh',[Admin::class,'searchAlarmByMantagheh'])->middleware('CheckCommon');
Route::get('/orderAlarms',[Admin::class,'orderAlarms'])->middleware('CheckCommon');
Route::get('/orderDoneAlarms',[Admin::class,'orderDoneAlarms'])->middleware('CheckCommon');
Route::get('/getAlarmsHistory',[Admin::class,'getAlarmsHistory'])->middleware('CheckCommon');
Route::get("/commentToday",[Customer::class,"todayComment"])->middleware('CheckCommon');
Route::get('/getDoneAlarmsHistory',[Admin::class,'getDoneAlarmsHistory'])->middleware('CheckCommon');
Route::get('/filteralarms',[Admin::class,'filteralarms'])->middleware('CheckCommon');
Route::get('/searchDoneAlarm',[Admin::class,'searchDoneAlarm'])->middleware('CheckCommon');
Route::get('/searchUnAlarmByMantagheh',[Admin::class,'searchUnAlarmByMantagheh'])->middleware('CheckCommon');
Route::get('/searchUnAlarmedCustomer',[Admin::class,'searchUnAlarmedCustomer'])->middleware('CheckCommon');
Route::get('/orderUnAlarms',[Admin::class,'orderUnAlarms'])->middleware('CheckCommon');
Route::get('/getUnAlarmHistory',[Admin::class,'getUnAlarmHistory'])->middleware('CheckCommon');
Route::get('/getPersonals',[Admin::class,'getPersonals'])->middleware('CheckCommon');


Route::get('/getManagerByLine',[Admin::class,'getManagerByLine'])->middleware('CheckCommon');

Route::get('/getOrgChart',[Admin::class,'getOrgChart'])->middleware('CheckCommon');

Route::get('/getManagerByLine',[Admin::class,'getManagerByLine'])->middleware('CheckCommon');
Route::get('/getCustomers',[Admin::class,'getCustomers'])->middleware('CheckCommon');
Route::get('/orderAllCustomerByName',[Customer::class,'orderAllCustomerByName'])->middleware('CheckCommon');
Route::get('/searchLoginsByName',[Customer::class,'searchLoginsByName'])->middleware('CheckCommon');
Route::get('/searchInActivesByName',[Customer::class,'searchInActivesByName'])->middleware('CheckCommon');
Route::get('/searchReturnedByName',[Customer::class,'searchReturnedByName'])->middleware('CheckCommon');
Route::get('/withoutAdmins',[Customer::class,'withoutAdmins'])->middleware('CheckCommon');
Route::get('/orderInActiveCustomers',[Customer::class,'orderInActiveCustomers'])->middleware('CheckCommon');
Route::get('/orderReturned',[Customer::class,'orderReturned'])->middleware('CheckCommon');
Route::get('/orderwithoutAdmins',[Customer::class,'orderwithoutAdmins'])->middleware('CheckCommon');
Route::get('/orderLogins',[Customer::class,'orderLogins'])->middleware('CheckCommon');
Route::get('/filterNoAdmins',[Customer::class,'filterNoAdmins'])->middleware('CheckCommon');
Route::get('/filterReturneds',[Customer::class,'filterReturneds'])->middleware('CheckCommon');
Route::get('/getHistroyLogins',[Customer::class,'getHistroyLogins'])->middleware('CheckCommon');
Route::get('/getReferencialReport',[Customer::class,'getReferencialReport'])->middleware('CheckCommon');
Route::get('/getInactiveReport',[Customer::class,'getInactiveReport'])->middleware('CheckCommon');

Route::get('/getEmployeeInfo',[Admin::class,'getEmployeeInfo'])->middleware('CheckCommon');

Route::get('/salesReport',[Product::class,'salesReport'])->middleware('CheckCommon');
Route::get('/getSalesReportInfo',[Product::class,'getSalesReportInfo'])->middleware('CheckCommon');
Route::get("/checkPhoneExistance",[Customer::class,'checkPhoneExistance'])->middleware('CheckCommon');


Route::get('/distributionScope',[DriverController::class,'distributionScope'])->middleware('CheckCommon');

Route::get('/customerInformation',[Customer::class,'customerInformation'])->middleware('CheckCommon');
Route::get('/addCustomerState',[Customer::class,'addCustomerState'])->middleware('CheckCommon');