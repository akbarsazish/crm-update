

$(document).ready(
    function () {
        $(window).load(function () {
            $(".c-gallery__items img").click(function () {
                var src = $(this).attr("src");
                $(".c-gallery__img img").attr("src", src);
            });
            $("#modalBody").scrollTop($("#modalBody").prop("scrollHeight"));
        });
    } // document-ready
);
document.querySelector(".fa-bars")
    .parentElement.addEventListener("click", () => {
        // backdrop.classList.add('show');
    });

var baseUrl = "http://192.168.10.27:8080";
var myVar;
function setAdminStuffForAdmin(element, adminTypeId, driverId) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    let adminType = input.val().split("_")[1];
    let adminId = input.val().split("_")[0];
    $("#PoshtibanId").val(adminId);
    $("#subBazaryabId").val(adminId);
    $("#subListDashboardBtn").prop("disabled", false);
    $("#adminSn").val(adminId);
    if (adminTypeId == 1) {

        $("#adminInfo").css({ "display": "inline" });
        $("#poshtibanInfo").css({ "display": "none" });
        $("#bazaryabInfo").css({ "display": "none" });
        $("#subListDashboardBtnPoshtiban").prop("disabled", false);
    }

    if (adminTypeId == 2) {
        $("#poshtibanInfo").css({ "display": "inline" });
        $("#bazaryabInfo").css({ "display": "none" });
        $("#subListDashboardBtnPoshtiban").prop("disabled", false);
    }

    if (adminTypeId == 3) {
        $("#bazaryabInfo").css({ "display": "inline" });
        $("#poshtibanInfo").css({ "display": "none" });
        $("#subListDashboardBtn").prop("disabled", false);
    }

    if (adminTypeId == 4) {

        $("#PoshtibanId").val(adminId);
        $("#poshtibanInfo").css({ "display": "inline" });
        $("#bazaryabInfo").css({ "display": "none" });
        $("#subListDashboardBtnPoshtiban").prop("disabled", false);
    }
    if (adminTypeId == 6) {

        $("#PoshtibanId").val(adminId);
        $("#poshtibanInfo").css({ "display": "inline" });
        $("#bazaryabInfo").css({ "display": "none" });
    }
    if (adminTypeId == 7) {

        $("#PoshtibanId").val(adminId);
        $("#poshtibanInfo").css({ "display": "none" });
        $("#bazaryabInfo").css({ "display": "none" });
    }


    $.ajax({
        method: "get",
        url: baseUrl + "/getAdminTodayInfo",
        data: {
            _token: "{{ csrf_token() }}",
            asn: adminId,
        },
        async: true,
        success: function (arrayed_result) {
            $("#adminCustomers").empty();
            moment.locale("en");
            let info = arrayed_result[0];
            let customers = arrayed_result[1];
            let peopels = arrayed_result[2];
            $("#loginTimeToday").text("");
            $("#adminName").text("");
            $("#countCommentsToday").text(0);
            $("#countFactorsToday").text(0);
            $("#countCustomersToday").text(0);
            $("#loginTimeToday").text(
                moment(peopels[0].loginTime, "YYYY/M/D HH:mm:ss")
                    .locale("fa")
                    .format("HH:mm:ss YYYY/M/D")
            );

            $("#adminName").text(info.name + " " + info.lastName);
            $("#countCommentsToday").text(peopels[0].countComments);
            $("#countFactorsToday").text(peopels[0].countFctors);
            $("#countCustomersToday").text(peopels[0].countCustomers);
            $("#adminCustomers").empty();
            customers.forEach((element, index) => {
                let maxHour = 0;
                let countFactor = 0;
                if (element.maxHour != null) {
                    maxHour = moment(element.maxHour, "YYYY/M/D HH:mm:ss")
                        .locale("fa")
                        .format("HH:mm:ss YYYY/M/D");
                }
                if (element.countFactor != null) {
                    countFactor = element.countFactor;
                }
                $("#adminCustomers").append(`<tr onclick="selectTableRow(this)">
                                                <td>` + (index + 1) + `</td>
                                                <td>` + element.Name + `</td>
                                                <td>` + maxHour + `</td>
                                                <td>` + countFactor + `</td>
                                             </tr>`);
            });
        },
        error: function (data) { },
    });
}

$("#returnComment").on("click", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/viewReturnComment",
        data: {
            _token: "{{ csrf_token()}}",
            csn: $("#customerSn").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $("#returnView").text(arrayed_result);

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#returnViewComment").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#returnViewComment").modal("show");
        },
        error: function (data) { },
    });
});

function givFactor(element,factorSn) {
    swal({
        title: "هشدار!",
        text: "آیا مطمئین هستید؟",
        icon: "warning",
        buttons: true,
    }).then(function(willDo){
        if(willDo){
            $.get(baseUrl+'/giveFactor',{factorSn:factorSn},function(data,status){
                if(status=='success'){
                    let  givenClass = "selected";
                    if(data==1){
                        $(element).prop("checked",true);
                        let gevenState="";
                       
                        $(element).parents("tr").addClass(givenClass);
                        
                    }else{
                        $(element).prop("checked",false);
                        $(element).parents("tr").removeClass(givenClass);
                    }
                }
            })
        }

})

}

$("#changePositionForm").on("submit", function (event) {
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            var map;
            if (L.DomUtil.get("map2") !== undefined) {
                L.DomUtil.get("map2")._leaflet_id = null;
            }
            //  var map = L.map('map2').setView([43.64701, -79.39425], 10);
            map = L.map("map2", { center: [35.70163, 51.39211], zoom: 10 });
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: '<a href="https://osm.org/copyright">CRM</a>',
            }).addTo(map);

            var marker = {};
            data.forEach(function (item) {
                if (item.LatPers > 0 && item.LonPers > 0) {
                    var popup = new L.popup().setContent();
                    marker = L.marker([item.LonPers, item.LatPers], {
                        title: " تغییر موقعیت",
                        draggable: true,
                    })
                        .addTo(map)
                        .bindPopup(popup);

                    marker.on("dragend", () => {
                        let newposition = marker.getLatLng();
                        $("#newPosition").val(newposition);
                    });

                    let btn = document.createElement("a");
                    btn.innerText = "مشتری ";
                    // btn.setAttribute('href', "/Cardboard/cCode");
                    marker.bindPopup(btn, {
                        maxWidth: "200px",
                    });
                } else {
                    let defaultposition = [35.70163, 51.39211];

                    $("#newPosition").val(defaultposition);
                    marker = L.marker(defaultposition, {
                        title: "تعیین موقعیت",
                        draggable: true,
                    })
                        .addTo(map)
                        .bindPopup(popup);

                    marker.on("dragend", () => {
                        let newposition = marker.getLatLng();
                        $("#newPosition").val(newposition);
                    });
                }
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#changeCustomerLocation").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#changeCustomerLocation").modal("show");
            setTimeout(function () {
                window.dispatchEvent(new Event("resize"));
            }, 500);
        },
        error: function (params) {
            alert("good luck");
        },
    });
    event.preventDefault();
});

$("#openkarabarDashboard").on("click", () => {
    $("#waitToDashboard").css("display", "flex");
    let asn = $("#adminSn").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/adminDashboard",
        data: {
            _token: "{{ csrf_token() }}",
            asn: asn,
        },
        async: true,
        success: function (arrayed_result) {
            moment.locale("en");
            let admin = arrayed_result[0];
            let info = arrayed_result[1];
            let history = arrayed_result[2];
            let customers = arrayed_result[3];
            let subTreeInfo=arrayed_result[4];
            let allTreeInfo=arrayed_result[5];
            sumAllReturnedFactor=0;

            //set subTrees of amalkard karbaran modal
			
			            $("#lastMonthActionsPub").empty();
            subTreeInfo.customers.forEach((element, index) => {
                $("#lastMonthActionsPub").append(
                    `
            <tr onclick="selectTableRow(this)">
            <td>` +
                    index +
                    1 +
                    `</td>
<td style="width:133px;">` +
                    element.countCustomers +
                    `</td>
            <td>` +
                    element.countAllFactor +
                    `</td>
            <td>` +
                    parseInt(
                        parseInt(
                            element.sumAllFactor / 10 +
                            element.sumAllReturnedFactor / 10
                        )
                    ).toLocaleString("en-us") +
                    ` تومن` +
                    `</td>
            <td>` +
                    parseInt(
                        element.sumAllReturnedFactor / 10
                    ).toLocaleString("en-us") +
                    ` تومن` +
                    `</td>
            <td style="width:133px;">` +
                    parseInt(
                        parseInt(element.sumAllFactor / 10)
                    ).toLocaleString("en-us") +
                    ` تومن` +
                    `</td>
          </tr>
          `
                );
            });
			
			
            $("#assignCustomerDatePub").text((subTreeInfo.minDateCustomerCurrMonthPub || '00:00:00')); 
            $("#countCustomerPub").text(subTreeInfo.countAllCustomerCurrMonthPub);
            $("#countFactorsPub").text(subTreeInfo.countAllFactorCurrMonthPub + subTreeInfo.countAllReturnedFactorCurrMonthPub);
            $("#allMoneyFactorPub").text(parseInt(subTreeInfo.sumAllFactorCurrMonthPub/10 + subTreeInfo.sumAllReturnedFactorCurrMonthPub/10).toLocaleString("en-us"));
            $("#countReturnedFactorPub").text(subTreeInfo.countAllReturnedFactorCurrMonthPub);
            $("#allMoneyReturnedFactorPub").text(parseInt(subTreeInfo.sumAllReturnedFactorCurrMonthPub/10).toLocaleString("en-us"));
            $("#notlogedInPub").text(0);
            $("#lastMonthAllFactorMoneyPub").text(parseInt(subTreeInfo.lastMonthTotalMoney/10).toLocaleString("en-us"));
            $("#lastMonthAllFactorMoneyReturnedPub").text(parseInt(subTreeInfo.lastMonthReturnedTotalMoney/10).toLocaleString("en-us"));

            //all tree info 

			
			$("#lastMonthActionsAll").empty();
            allTreeInfo.customers.forEach((element, index) => {
                $("#lastMonthActionsAll").append(
                    `
            <tr onclick="selectTableRow(this)">
            <td>` +
                    index +
                    1 +
                    `</td>
<td style="width:133px;">` +
                    element.countCustomers +
                    `</td>
            <td>` +
                    element.countAllFactor +
                    `</td>
            <td>` +
                    parseInt(
                        parseInt(
                            element.sumAllFactor / 10 +
                            element.sumAllReturnedFactor / 10
                        )
                    ).toLocaleString("en-us") +
                    ` تومن` +
                    `</td>
            <td>` +
                    parseInt(
                        element.sumAllReturnedFactor / 10
                    ).toLocaleString("en-us") +
                    ` تومن` +
                    `</td>
            <td style="width:133px;">` +
                    parseInt(
                        parseInt(element.sumAllFactor / 10)
                    ).toLocaleString("en-us") +
                    ` تومن` +
                    `</td>
          </tr>
          `
                );
            });
			
			
            $("#assignCustomerDateAll").text((allTreeInfo.minDateCustomerCurrMonthPub || '00:00:00')); 
            $("#countCustomerAll").text(allTreeInfo.countAllCustomerCurrMonthPub);
            $("#countFactorsAll").text(allTreeInfo.countAllFactorCurrMonthPub+allTreeInfo.countAllReturnedFactorCurrMonthPub);
            $("#allMoneyFactorAll").text(parseInt(allTreeInfo.sumAllFactorCurrMonthPub/10 + allTreeInfo.sumAllReturnedFactorCurrMonthPub/10).toLocaleString("en-us"));
            $("#countReturnedFactorAll").text(allTreeInfo.countAllReturnedFactorCurrMonthPub);
            $("#allMoneyReturnedFactorAll").text(parseInt(allTreeInfo.sumAllReturnedFactorCurrMonthPub/10).toLocaleString("en-us"));
            $("#notlogedInAll").text(0);
            $("#lastMonthAllFactorMoneyAll").text(parseInt(allTreeInfo.lastMonthTotalMoney/10).toLocaleString("en-us"));
            $("#lastMonthAllFactorMoneyReturnedAll").text(parseInt(allTreeInfo.lastMonthReturnedTotalMoney/10).toLocaleString("en-us"));
            

            //اطلاعات شخصی 
            if (info[0]) {
                sumAllReturnedFactor = parseInt(
                    parseInt(info[0].totalReturnMoneyHds) / 10
                );
                $("#countCustomerBought").text(info[0].boughtPeopelsCount);
                $("#countFactors").text(
                    parseInt(info[0].countFactor) +
                    parseInt(info[0].countReturnFactor)
                );
                $("#allMoneyFactor").text(
                    parseInt(
                        parseInt(info[0].totalMoneyHds / 10) +
                        parseInt(sumAllReturnedFactor)
                    ).toLocaleString("en-us") + " تومن"
                );
                $("#lastMonthAllFactorMoney").text(
                    parseInt(info[0].lastMonthFactorAllMoney / 10).toLocaleString(
                        "en-us"
                    ) + " تومن"
                );
                if (info[0].lastMonthReturnedAllMoney) {
                    $("#lastMonthAllFactorMoneyReturned").text(
                        parseInt(
                            info[0].lastMonthReturnedAllMoney / 10
                        ).toLocaleString("en-us") + " تومن"
                    );
                } else {
                    $("#lastMonthAllFactorMoneyReturned").text("0 تومن");
                }
                $("#countReturnedFactor").text(info[0].countReturnFactor);
            }
            if (admin[0]) {
               // $("#assignCustomerDate").text( moment(admin[0].minDate, "YYYY/M/D").locale("fa").format("YYYY/M/D"));
                $("#countCustomer").text(admin[0].countPeopel);
                $("#comment").text(admin[0].discription);
                $("#adminNameModal").text(admin[0].name + " " + admin[0].lastName);
            } else {
                $("#assignCustomerDate").text("");
            }
            $("#allMoneyReturnedFactor").text(
                sumAllReturnedFactor.toLocaleString("en-us") + " تومن"
            );
            $("#notlogedIn").text(0);

            $("#factorTable").empty();
            history.forEach((element, index) => {
                $("#factorTable").append(
                    ` <tr onclick="selectTableRow(this)">
                            <td>` + (index + 1) + `</td>
                            <td>` +  element.countPeople + `</td>
                            <td>` +   element.countBuyPeople +`</td>
                            <td>` + element.countFactor + `</td>
                            <td>` + parseInt( element.lastMonthReturnedAllMoney / 10).toLocaleString("en-us") + ` تومن` + `</td>
                            <td>` +  parseInt(element.factorAllMoney / 10).toLocaleString( "en-us") +` تومن` +`</td>
                            <td>` + parseInt(element.lastMonthAllMoney / 10).toLocaleString("en-us" ) + ` تومن` + `</td>
                            <td>` +  (element.meanIncrease * 100).toLocaleString("en-us") + ` </td>
                            <td>` + element.noCommentCust + `</td>
                            <td>` + element.noDoneWork + `</td>
                            <td  onclick="showAdminComment(` +  admin[0].id +  `,'` +  element.timeStamp +  `')"><input name="factorId" style="display:none" type="radio" value="` +  admin[0].id +  `" /><i class="fa fa-eye" /> </td>
                  </tr>
            ` );
            });

            $("#lastMonthActions").empty();
            customers.forEach((element, index) => {
                $("#lastMonthActions").append(
                    `
            <tr onclick="selectTableRow(this)">
            <td>` +
                    index +
                    1 +
                    `</td>
<td style="width:133px;">` +
                    element.countCustomers +
                    `</td>
            <td>` +
                    element.countAllFactor +
                    `</td>
            <td>` +
                    parseInt(
                        parseInt(
                            element.sumAllFactor / 10 +
                            element.sumAllReturnedFactor / 10
                        )
                    ).toLocaleString("en-us") +
                    ` تومن` +
                    `</td>
            <td>` +
                    parseInt(
                        element.sumAllReturnedFactor / 10
                    ).toLocaleString("en-us") +
                    ` تومن` +
                    `</td>
            <td style="width:133px;">` +
                    parseInt(
                        parseInt(element.sumAllFactor / 10)
                    ).toLocaleString("en-us") +
                    ` تومن` +
                    `</td>
          </tr>
          `
                );
            });
            $("#waitToDashboard").hide();

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#karbarAction").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#karbarAction").modal("show");
        },
        error: function (data) { },
    });
});
function showAdminComment(id, timeStamp) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAdminHistoryComment",
        data: {
            _token: "{{ csrf_token() }}",
            timeStamp: timeStamp,
            id: id,
        },
        async: true,
        success: function (arrayed_result) {
            // alert(arrayed_result.comment);
            $("#discription").text(arrayed_result.comment);

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#readDiscription").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#readDiscription").modal("show");
        },
        error: function (data) { },
    });
}

function showFactorDetails(element) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    $("tr").removeClass("selected");
    $(element).parent("tr").toggleClass("selected");
    $.ajax({
        method: "get",
        url: baseUrl + "/getFactorDetail",
        data: {
            _token: "{{ csrf_token() }}",
            FactorSn: input.val(),
        },
        async: true,
        success: function (arrayed_result) {
            let factor = arrayed_result[0];
            if (arrayed_result[0]) {
                $("#factorDate").text(factor.FactDate);
            }
            $("#customerNameFactor").text(factor.Name);
            $("#customerComenter").text(factor.Name);
            $("#customerAddressFactor").text(factor.peopeladdress);
            $("#customerPhoneFactor").text(factor.sabit);
            $("#factorSnFactor").text(factor.FactNo);
            $("#Admin1").text(factor.Name + " " + factor.Name);
            $("#productList").empty();

            arrayed_result.forEach((element, index) => {
                $("#productList").append(
                    `<tr onclick="selectTableRow(this)">
            <td class="driveFactor">` +
                    (index + 1) +
                    `</td>
            <td>` +
                    element.GoodName +
                    ` </td>
            <td class="driveFactor">` +
                    element.Amount / 1 +
                    `</td>
            <td>` +
                    element.UName +
                    `</td>
            <td>` +
                    (element.Fi / 10).toLocaleString("en-us") +
                    `</td>
            <td style="width:111px;">` +
                    (
                        (element.Fi / 10) *
                        (element.Amount / 1)
                    ).toLocaleString("en-us") +
                    `</td>
            </tr>`
                );
            });

            $("#factorDate1").text(factor.FactDate);
            $("#customerNameFactor1").text(factor.Name);
            $("#customerComenter1").text(factor.Name);
            $("#customerAddressFactor1").text(factor.peopeladdress);
            $("#customerPhoneFactor1").text(factor.hamrah);
            $("#factorSnFactor1").text(factor.FactNo);
            $("#Admin1").text(factor.name + " " + factor.lastName);
            $("#productList1").empty();
            arrayed_result.forEach((element, index) => {
                $("#productList1").append(
                    `<tr onclick="selectTableRow(this)">
            <td class="driveFactor">` +
                    (index + 1) +
                    `</td>
            <td>` +
                    element.GoodName +
                    ` </td>
            <td class="driveFactor">` +
                    element.Amount / 1 +
                    `</td>
            <td>` +
                    element.UName +
                    `</td>
            <td>` +
                    (element.Fi / 10).toLocaleString("en-us") +
                    `</td>
            <td style="width:111px;">` +
                    (
                        (element.Fi / 10) *
                        (element.Amount / 1)
                    ).toLocaleString("en-us") +
                    `</td>
            </tr>`
                );
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#viewFactorDetail").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#viewFactorDetail").modal("show");
        },
        error: function (data) { },
    });
}

// searching the bargeri list
$("#bargerilist").on("keyup", () => {
    let searchTerm = $("#bargerilist").val();

    $.ajax({
        method: "get",
        url: baseUrl + "/crmDriverSearch",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm,
        },
        async: true,
        success: function (arrayed_result) {
            let searchingFactor = arrayed_result[0];
            $("#crmDriverBargeri").empty();
            searchingFactor.forEach((element, index) => {
                $("#crmDriverBargeri").append(
                    `
               <tr onclick="setBargiryStuff(this,`+element.PSN+`); selectTableRow(this)">
                    <td>` + (index + 1) + `</td>
                    <td>` + element.Name + ` </td>
                    <td class="address">` +  element.peopeladdress +`</td>
                    <td>` + element.PhoneStr + `</td>
                    <td style="text-align: center;">
                     <a style="text-decoration:none;" target="_blank" href="https://maps.google.com/?q=` +element.LonPers + "," +element.LatPers + `"><i class="fas fa-map-marker-alt fa-1xl" style="color:#116bc7; "></i></a> </td>
                    <td style="width:111px;" data-toggle="modal" data-target="#factorDeatials"><i class="fa fa-eye fa-1xl"> </i> </td>
                    <td class="choice"> <input class="customerList form-check-input" name="factorId" type="radio" value="` + element.SerialNoHDS + `"></td>
                </tr>
           `);
            });
        },
        error: function (data) { },
    });
});

function setAdminStuff(element, adminId, adminTypeId) {
    $(element).find("input:radio").prop("checked", true);
    let adminType = adminTypeId;
    let id = adminId;
    
    $("#asn").val(id);
    $("#emptyKarbarButton").val(id);
    $("#moveKarbarButton").val(id);
    $("#editAssingId").val(id);
    $("#editAssingBtn").prop("disabled", false);
    $("#adminTakerId").val(id);
    if ($("#emptyAdminBtn")) {
        $("#emptyAdminBtn").val(id);
    }
    $.ajax({
        method: "get",
        url: baseUrl + "/getAdminInfo",
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
        },
        success: function (respond) {
            $("#adminDiscription").text("");
            $("#adminDiscription").text(respond[3].discription);
        },
        error: function (error) { },
    });


    if ((adminType != 5)) {
        $("#customerContainer").css("display", "flex");
        $.ajax({
            method: "get",
            url: baseUrl + "/getCustomer",
            data: {
                _token: "{{ csrf_token() }}",
            },
            async: true,
            success: function (arrayed_result) {
                $("#allCustomer").empty();

                arrayed_result.forEach((element, index) => {
                    $("#allCustomer").append(
                        `
                <tr onclick="checkCheckBox(this,event); selectTableRow(this)">
                    <td style="">` +
                        (index + 1) +
                        `</td>
                    <td style="">` +
                        element.PCode +
                        `</td>
                    <td>` +
                        element.Name +
                        `</td>
                    <td style="">
                    <input class="form-check-input" name="customerIDs[]" type="checkbox" value="` +
                        element.PSN +
                        `" id="customerId">
                    </td>
                </tr>`
                    );
                });
            },
            error: function (data) { },
        });
        $.ajax({
            method: "get",
            url: baseUrl + "/getAdminEmptyState",
            data: {
                _token: "{{ csrf_token() }}",
                adminId: id,
            },
            async: true,
            success: function (data) {
            
                if (data.emptyState==0) {
                    $("#emptyKarbarButton").prop("disabled", false);
                    $("#moveKarbarButton").prop("disabled", false);
                    $("#deleteAdmin").prop("disabled", true);
                } else {
                    $("#emptyKarbarButton").prop("disabled", true);
                    $("#moveKarbarButton").prop("disabled", true);
                    $("#deleteAdmin").prop("disabled", false);
                }
                $("#addedCustomer").empty();

                data.customers.forEach((element, index) => {
                    $("#addedCustomer").append(
                        `
                    <tr onclick="checkCheckBox(this,event); selectTableRow(this)">
                        <td>` +  (index + 1) +  `</td>
                        <td>` +  element.Name + `</td>
                        <td>` +  element.PCode + `</td>
                        <td >` + element.Mantagheh + `</td>
                        <td> <input class="form-check-input" name="addedCustomerIDs" type="radio" value="` +  element.PSN + `" id="kalaId">  </td>
                 </tr>`
                    );
                });
            },
            error: function (data) { },
        });
    } else {
        $("#emptyKarbarButton").prop("disabled", true);
        $("#moveKarbarButton").prop("disabled", true);
        $("#deleteAdmin").prop("disabled", true);
        $("#customerContainer").hide();
    }
}
function setAdminListStuff(element, adminType, adminId, logedInId) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    let id = input.val();
    $("#asn").val(id);
    $("#AdminForAdd").val(id);
    if (adminType == 2) {
        $.ajax({
            method: "get",
            url: baseUrl + "/getAddedCustomer",
            data: {
                _token: "{{ csrf_token() }}",
                adminId: id,
            },
            async: true,
            success: function (arrayed_result) {
                if (arrayed_result.length > 0) {
                    $("#deleteSupporter").prop("disabled", true);
                    $("#setEditStuff").prop("disabled", true);
                    $("#deleteDriver").prop("disabled", true);
                    $("#deleteMarketer").prop("disabled", true);
                    $("#deleteAdmin").prop("disabled", true);
                    $("#editAdmin").prop("disabled", true);
                    $("#editDriver").prop("disabled", true);
                    $("#editSupporter").prop("disabled", false);
                    $("#editMarketer").prop("disabled", true);
                } else {
                    $("#deleteSupporter").prop("disabled", false);
                    $("#editSupporter").prop("disabled", false);
                    $("#deleteMarketer").prop("disabled", true);
                    $("#setEditStuff").prop("disabled", true);
                    $("#deleteDriver").prop("disabled", true);
                    $("#editAdmin").prop("disabled", true);
                    $("#editDriver").prop("disabled", true);
                    $("#editSupporter").prop("disabled", false);
                    $("#editMarketer").prop("disabled", true);
                }
            },
            error: function (data) { },
        });
    } else {
        if (adminType == 3) {
            $.ajax({
                method: "get",
                url: baseUrl + "/getAddedCustomer",
                data: {
                    _token: "{{ csrf_token() }}",
                    adminId: id,
                },
                async: true,
                success: function (arrayed_result) {
                    if (arrayed_result.length > 0) {
                        $("#deleteMarketer").prop("disabled", true);
                        $("#deleteSupporter").prop("disabled", true);
                        $("#setEditStuff").prop("disabled", true);
                        $("#deleteDriver").prop("disabled", true);
                        $("#deleteAdmin").prop("disabled", true);
                        $("#editAdmin").prop("disabled", true);
                        $("#editDriver").prop("disabled", true);
                        $("#editSupporter").prop("disabled", true);
                        $("#editMarketer").prop("disabled", false);
                    } else {
                        $("#deleteMarketer").prop("disabled", false);
                        $("#deleteSupporter").prop("disabled", true);
                        $("#setEditStuff").prop("disabled", true);
                        $("#deleteDriver").prop("disabled", true);
                        $("#deleteAdmin").prop("disabled", true);
                        $("#editAdmin").prop("disabled", true);
                        $("#editDriver").prop("disabled", true);
                        $("#editSupporter").prop("disabled", true);
                        $("#editMarketer").prop("disabled", false);
                    }
                },
                error: function (data) { },
            });
        } else {
            if (adminType == 1) {
                $("#deleteMarketer").prop("disabled", true);
                $("#deleteSupporter").prop("disabled", true);
                $("#setEditStuff").prop("disabled", true);
                $("#deleteDriver").prop("disabled", true);
                $("#deleteAdmin").prop("disabled", true);
                $("#editAdmin").prop("disabled", false);
                $("#editDriver").prop("disabled", true);
                $("#editSupporter").prop("disabled", true);
                $("#editMarketer").prop("disabled", true);
            } else {
                if (adminType == 5) {
                    if (logedInId == adminId) {
                        $("#deleteMarketer").prop("disabled", true);
                        $("#deleteAdmin").prop("disabled", true);
                        $("#editAdmin").prop("disabled", false);
                        $("#editDriver").prop("disabled", true);
                        $("#editSupporter").prop("disabled", true);
                        $("#editMarketer").prop("disabled", true);
                        $("#setEditStuff").prop("disabled", true);
                        $("#deleteDriver").prop("disabled", true);
                    } else {
                        $("#deleteMarketer").prop("disabled", true);
                        $("#deleteAdmin").prop("disabled", true);
                        $("#editAdmin").prop("disabled", true);
                        $("#editDriver").prop("disabled", true);
                        $("#editSupporter").prop("disabled", true);
                        $("#editMarketer").prop("disabled", true);
                        $("#setEditStuff").prop("disabled", true);
                        $("#deleteDriver").prop("disabled", true);
                    }
                } else {
                    $("#deleteMarketer").prop("disabled", true);
                    $("#deleteSupporter").prop("disabled", true);
                    $("#deleteAdmin").prop("disabled", true);
                    $("#deleteDriver").prop("disabled", false);
                    $("#editAdmin").prop("disabled", true);
                    $("#editDriver").prop("disabled", false);
                    $("#editSupporter").prop("disabled", true);
                    $("#editMarketer").prop("disabled", true);
                }
            }
        }
    }
}
$("#addMessageButton").on("click", () => {
    if (!$(".modal.in").length) {
        $(".modal-dialog").css({
            top: 0,
            left: 0,
        });
    }
    $("#userList").modal({
        backdrop: false,
        show: true,
    });

    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });

    $("#userList").modal("show");
});
function setMessageStuff(element) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    let adminType = input.val().split("_")[1];
    let id = input.val().split("_")[0];
    $.ajax({
        method: "get",
        url: baseUrl + "/getAdminInfo",
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
        },
        async: true,
        success: function (msg) {
            $("#sendTo").text(msg[3].name + " " + msg[3].lastName);
            $("#getterId").val(msg[3].id);
            moment.locale("en");
            let sended = msg[0];
            let myId = msg[2];
            let appositId = msg[1];
            $("#messageList").empty();
            sended.forEach((element, index) => {
                let showDate = "";
                if (element.diffDate > 0) {
                    showDate =
                        ` ` +
                        moment(element.messageDate, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("YYYY/M/D") +
                        ` `;
                }
                if (appositId == element.getterId) {
                    $("#messageList").append(
                        `` +
                        showDate +
                        `<div class="d-flex flex-row justify-content-start mb-1">
                <img src="resources/assets/images/admins/` +
                        myId +
                        `.jpg" alt="avatar 1" style="width: 50px; height: 50px; border-radius:100%";">
                <div class="p-2 ms-2" style="border-radius:10px; background-color: rgba(78, 192, 229, 0.2);">
                    <p class="small" style="font-size:0.9rem;"> <span style="color:gray; font-size:10px; padding-bottom:30px; font-style:italic; margin-left:10px;"> ` +
                        moment(element.messageDate, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("HH:mm:ss") +
                        ` </span> ` +
                        element.messageContent +
                        `</p>
                </div>
            </div> 
            `
                    );
                } else {
                    $("#messageList").append(
                        `` +
                        showDate +
                        `<div class="d-flex flex-row justify-content-end mb-2">
                    <div class="p-2 me-2 border" id="replayDiv'.$replay->id.'" style="border-radius: 15px; background-color: #fbfbfb;">
                    <p class="small" style="font-size:0.9rem;"> ` +
                        element.messageContent +
                        ` <span style="color:gray; font-size:10px; padding-bottom:30px; font-style:italic; margin-left:10px;"> ` +
                        moment(element.messageDate, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("HH:mm:ss") +
                        ` </span></p>
                    </div>
                    <img src="resources/assets/images/admins/` +
                        appositId +
                        `.jpg" alt="avatar 1" style="width: 50px; height: 50px; border-radius:100%;">
                </div>`
                    );
                }
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#addMessage").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#addMessage").modal("show");
            $("#userList").modal("hide");
        },
        error: function (err) { },
    });
}
$("#addMessageForm").submit(function (e) {
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (msg) {
            $("#sendTo").text(msg[0].name + " " + msg[0].lastName);
            $("#getterId").val(msg[0].id);
            moment.locale("en");
            let sended = msg[0];
            let myId = msg[2];
            let appositId = msg[1];
            $("#messageList").empty();
            sended.forEach((element, index) => {
                let showDate = "";
                if (element.diffDate > 0) {
                    showDate =
                        ` ` +
                        moment(element.messageDate, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("YYYY/M/D") +
                        ` `;
                }
                if (appositId == element.getterId) {
                    $("#messageList").append(
                        `` +
                        showDate +
                        `<div class="d-flex flex-row justify-content-start mb-1">
                <img src="resources/assets/images/admins/` +
                        myId +
                        `.jpg" alt="avatar 1" style="width: 50px; height: 50px; border-radius:100%";">
                <div class="p-2 ms-2" style="border-radius:10px; background-color: rgba(78, 192, 229, 0.2);">
                    <p class="small" style="font-size:0.9rem;"> <span style="color:gray; font-size:10px; padding-bottom:30px; font-style:italic; margin-left:10px;"> ` +
                        moment(element.messageDate, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("HH:mm:ss") +
                        ` </span> ` +
                        element.messageContent +
                        `</p>
                </div>
            </div> 
            `
                    );
                } else {
                    $("#messageList").append(
                        `` +
                        showDate +
                        `<div class="d-flex flex-row justify-content-end mb-2">
                    <div class="p-2 me-2 border" id="replayDiv'.$replay->id.'" style="border-radius: 15px; background-color: #fbfbfb;">
                    <p class="small" style="font-size:0.9rem;"> ` +
                        element.messageContent +
                        ` <span style="color:gray; font-size:10px; padding-bottom:30px; font-style:italic; margin-left:10px;"> ` +
                        moment(element.messageDate, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("HH:mm:ss") +
                        ` </span></p>
                    </div>
                    <img src="resources/assets/images/admins/` +
                        appositId +
                        `.jpg" alt="avatar 1" style="width: 50px; height: 50px; border-radius:100%;">
                </div>`
                    );
                }
            });
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#addMessage").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#addMessage").modal("show");
            $("#userList").modal("hide");
        },
        error: () => {
            alert("bad");
        },
    });
    e.preventDefault();
});
function setReadMessageStuff(element) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    $("#senderId").val(input.val());
    $("#getterIdD").val(input.val());
    sendId = $("#senderId").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/getDiscusstion",
        data: {
            _token: "{{ csrf_token() }}",
            sendId: sendId,
        },
        async: true,
        success: function (arrayed_result) {
            let sended = arrayed_result[0];
            let appositId = arrayed_result[1];
            let myId = arrayed_result[2];
            moment.locale("en");
            $("#sendedMessages").empty();
            $("#recivedMessages").empty();
            $("#messageDiscusstion").empty();
            let prevDate;
            sended.forEach((element, index) => {
                let showDate = "";
                if (element.diffDate > 0) {
                    showDate =
                        ` ` +
                        moment(element.messageDate, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("YYYY/M/D") +
                        ` `;
                }
                if (appositId == element.getterId) {
                    $("#messageDiscusstion").append(
                        `` +
                        showDate +
                        `<div class="d-flex flex-row justify-content-start mb-1">
                <img src="resources/assets/images/admins/` +
                        myId +
                        `.jpg" alt="avatar 1" style="width: 50px; height: 50px; border-radius:100%">
                <div class="p-2 ms-2" style="border-radius:10px; height:40px; background-color: rgba(78, 192, 229, 0.2);">
                    <p class="small" style="font-size:0.9rem;"> <span style="color:gray; font-size:10px; padding-bottom:30px; font-style:italic; margin-left:10px;"> ` +
                        moment(element.messageDate, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("HH:mm:ss") +
                        ` </span> ` +
                        element.messageContent +
                        `</p>
    
                </div>
            </div>`
                    );
                } else {
                    $("#messageDiscusstion").append(
                        `` +
                        showDate +
                        `<div class="d-flex flex-row justify-content-end mb-2">
                    <div class="p-2 me-2 border" id="replayDiv'.$replay->id.'" style="border-radius: 15px; height:40px; background-color: #fbfbfb;">
                    <p class="small" style="font-size:0.9rem;">  ` +
                        element.messageContent +
                        `<span style="color:gray; font-size:10px; padding-bottom:30px; font-style:italic; margin-left:10px;"> ` +
                        moment(element.messageDate, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("HH:mm:ss") +
                        `</span> </p>
                     
                    </div>
                    <img src="resources/assets/images/admins/` +
                        appositId +
                        `.jpg" alt="avatar 1" style="width: 50px; height: 50px; border-radius:100%">
                    
                </div>`
                    );
                }
                prevDate = element.messageDate;
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#readComments").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#readComments").modal("show");
        },
        error: function (data) { },
    });
}

$("#addDisscusstionForm").submit(function (e) {
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (arrayed_result) {
            $("#messageArea").val("");
            let sended = arrayed_result[0];
            let myId = arrayed_result[2];
            let appositId = arrayed_result[1];
            $("#sendedMessages").empty();
            $("#recivedMessages").empty();
            $("#messageDiscusstion").empty();
            sended.forEach((element, index) => {
                if (appositId == element.getterId) {
                    $("#messageDiscusstion").append(
                        `<div class="d-flex flex-row justify-content-start mb-1">
                <img src="resources/assets/images/admins/` +
                        myId +
                        `.jpg" alt="avatar 1" style="width: 45px; height: 100%;">
                <div class="p-2 ms-2" style="border-radius:10px; height:40px; background-color: rgba(78, 192, 229, 0.2);">
                    <p class="small" style="font-size:0.9rem;"> ` +
                        element.messageContent +
                        `</p>
                </div>
            </div>`
                    );
                } else {
                    $("#messageDiscusstion").append(
                        `<div class="d-flex flex-row justify-content-end mb-2">
                    <div class="p-2 me-2 border" id="replayDiv'.$replay->id.'" style="border-radius: 15px; height:40px; background-color: #fbfbfb;">
                    <p class="small" style="font-size:0.9rem;"> ` +
                        element.messageContent +
                        `</p>
                    </div>
                    <img src="resources/assets/images/admins/` +
                        appositId +
                        `.jpg" alt="avatar 1" style="width: 45px; height: 100%;">
                </div>`
                    );
                }
            });
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#readComments").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#readComments").modal("show");
        },
        error: () => {
            alert("bad");
        },
    });
    e.preventDefault();
});

$(".selectAllFromTop").on("change", (e) => {
    if ($(e.target).is(":checked")) {
        var table = $(e.target).closest("table");
        if (!$("td input:checkbox", table).is(":disabled")) {
            $("td input:checkbox", table).prop("checked", true);
        }
    } else {
        var table = $(e.target).closest("table");
        $("td input:checkbox", table).prop("checked", false);
    }
});

$("#takhsisEditRightSideForm").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (arrayed_result) {
            $("#allCustomer").empty();

            arrayed_result.forEach((element, index) => {
                $("#allCustomer").append(
                    `
            <tr onclick="checkCheckBox(this,event); selectTableRow(this)">
                <td style="">` +
                    (index + 1) +
                    `</td>
                <td style="">` +
                    element.NameRec +
                    `</td>
                <td>` +
                    element.Name +
                    `</td>
                <td style="">
                <input class="form-check-input" name="customerIDs[]" type="checkbox" value="` +
                    element.PSN +
                    `" id="customerId">
                </td>
            </tr>`
                );
            });
        },
        error: function (error) { },
    });
});

$("#addCustomerToAdmin").on("click", () => {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید مشتریان اضافه شوند؟",
        icon: "warning",
        buttons: true,
    }).then(function (willAdd) {
        if (willAdd) {
            $("#transferLoader").show();
            $("#selectAllTopRight").prop("checked", false);
            let adminId = $("#AdminForAdd").val();
            var customerID = [];
            $('input[name="customerIDs[]"]:checked').map(function () {
                customerID.push($(this).val());
            });
            $.ajax({
                method: "get",
                url: baseUrl + "/AddCustomerToAdmin",
                data: {
                    _token: "{{ csrf_token() }}",
                    adminId: adminId,
                    customerIDs: customerID,
                },
                async: true,
                success: function (arrayed_result) {
                    $("#transferLoader").hide();
                    $("#addedCustomer").empty();
                    arrayed_result.forEach((element, index) => {
                        $("#addedCustomer").append(
                            `
                <tr  onclick="checkCheckBox(this,event); selectTableRow(this)">
                    <td>` +
                            (index + 1) +
                            `</td>
                    <td>` +
                            element.PCode +
                            `</td>
                    <td>` +
                            element.Name +
                            `</td>
                    <td>
                    <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` +
                            element.PSN +
                            `">
                    </td>
                </tr>
            `
                        );
                    });
                },
                error: function (data) { },
            });
            $.ajax({
                method: "get",
                url: baseUrl + "/getCustomer",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                async: true,
                success: function (arrayed_result) {
                    $("#allCustomer").empty();
                    arrayed_result.forEach((element, index) => {
                        $("#allCustomer").append(
                            `
            <tr  onclick="checkCheckBox(this,event); selectTableRow(this)">
                <td>` +
                            (index + 1) +
                            `</td>
                <td>` +
                            element.PCode +
                            `</td>
                <td>` +
                            element.Name +
                            `</td>
                <td>
                <input class="form-check-input" name="customerIDs[]" type="checkbox" value="` +
                            element.PSN +
                            `" id="customerId">
                </td>
            </tr>
        `
                        );
                    });
                },
                error: function (data) { },
            });
        } else {
        }
    });
});


$("#addCustomerToAdminOp").on("click", () => {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید مشتریان اضافه شوند؟",
        icon: "warning",
        buttons: true,
    }).then(function (willAdd) {
        if (willAdd) {
            $("#transferLoader").show();
            $("#selectAllTopRight").prop("checked", false);
            let adminId = $("#takhsisToAdminBtn").val();
            var customerID = [];
            $('input[name="customerIDs[]"]:checked').map(function () {
                customerID.push($(this).val());
            });
            $.ajax({
                method: "get",
                url: baseUrl + "/AddCustomerToAdmin",
                data: {
                    _token: "{{ csrf_token() }}",
                    adminId: adminId,
                    customerIDs: customerID,
                },
                async: true,
                success: function (arrayed_result) {
                    $("#transferLoader").hide();
                    $("#addedCustomer").empty();
                    arrayed_result.forEach((element, index) => {
                        $("#addedCustomer").append(
                            `
                <tr  onclick="checkCheckBox(this,event); selectTableRow(this)">
                    <td>` +
                            (index + 1) +
                            `</td>
                    <td>` +
                            element.PCode +
                            `</td>
                    <td>` +
                            element.Name +
                            `</td>
                    <td>
                    <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` +
                            element.PSN +
                            `">
                    </td>
                </tr>
            `
                        );
                    });
                },
                error: function (data) { },
            });
            $.ajax({
                method: "get",
                url: baseUrl + "/getCustomer",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                async: true,
                success: function (arrayed_result) {
                    $("#allCustomer").empty();
                    arrayed_result.forEach((element, index) => {
                        $("#allCustomer").append(
                            `
            <tr  onclick="checkCheckBox(this,event); selectTableRow(this)">
                <td>` +
                            (index + 1) +
                            `</td>
                <td>` +
                            element.PCode +
                            `</td>
                <td>` +
                            element.Name +
                            `</td>
                <td>
                <input class="form-check-input" name="customerIDs[]" type="checkbox" value="` +
                            element.PSN +
                            `" id="customerId">
                </td>
            </tr>
        `
                        );
                    });
                },
                error: function (data) { },
            });
        } else {
        }
    });
});

$("#removeCustomerFromAdmin").on("click", () => {
    var customerIDs = [];
    adminId = $("#AdminForAdd").val();
    swal({
        title: "اخطار!",
        text: "آیا می خواهید مشتریان حذف شوند؟",
        icon: "warning",
        buttons: true,
    }).then(function (willDelete) {
        if (willDelete) {
            $("#selectAllTopLeft").prop("checked", false);
            $("#transferLoader").show();
            adminId = $("#AdminForAdd").val();
            $('input[name="addedCustomerIDs[]"]:checked').map(function () {
                customerIDs.push($(this).val());
            });
            $.ajax({
                method: "get",
                url: baseUrl + "/RemoveCustomerFromAdmin",
                data: {
                    _token: "{{ csrf_token() }}",
                    adminId: adminId,
                    customerIDs: customerIDs,
                },
                async: true,
                success: function (arrayed_result) {
                    if (arrayed_result != 1) {
                        $("#addedCustomer").empty();
                        arrayed_result.forEach((element, index) => {
                            $("#addedCustomer").append(
                                `
            <tr  onclick="checkCheckBox(this,event); selectTableRow(this)">
                <td>` +
                                (index + 1) +
                                `</td>
                <td>` +
                                element.PCode +
                                `</td>
                <td>` +
                                element.Name +
                                `</td>
                <td>
                <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` +
                                element.PSN +
                                `">
                </td>
            </tr>
        `
                            );
                        });
                    } else {
                        swal({
                            title: "اخطار!",
                            text: "تاریخچه ثبت نمی شود، می خواهید انجام شود؟",
                            icon: "warning",
                            buttons: true,
                        }).then(function (willDelete) {
                            if (willDelete) {
                                $("#selectAllTopLeft").prop("checked", false);
                                $("#transferLoader").show();
                                $.ajax({
                                    method: "get",
                                    url: baseUrl + "/RemoveCustomerAndEmpty",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        adminId: adminId,
                                        customerIDs: customerIDs,
                                    },
                                    async: true,
                                    success: function (arrayed_result) {
                                        $("#addedCustomer").empty();
                                        arrayed_result.forEach(
                                            (element, index) => {
                                                $("#addedCustomer").append(
                                                    `
                    <tr  onclick="checkCheckBox(this,event); selectTableRow(this)">
                        <td>` +
                                                    (index + 1) +
                                                    `</td>
                        <td>` +
                                                    element.PCode +
                                                    `</td>
                        <td>` +
                                                    element.Name +
                                                    `</td>
                        <td>
                        <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` +
                                                    element.PSN +
                                                    `">
                        </td>
                    </tr>
                `
                                                );
                                            }
                                        );
                                        $("#transferLoader").css(
                                            "display",
                                            "none"
                                        );
                                    },
                                    error: function (data) { },
                                });
                            }
                        });
                    }
                    $("#transferLoader").hide();
                },
                error: function (data) { },
            });
        }
    });
});

$("#removeCustomerFromAdminOp").on("click", () => {
    var customerIDs = [];
    adminId = $("#takhsisToAdminBtn").val();
    swal({
        title: "اخطار!",
        text: "آیا می خواهید مشتریان حذف شوند؟",
        icon: "warning",
        buttons: true,
    }).then(function (willDelete) {
        if (willDelete) {
            $("#selectAllTopLeft").prop("checked", false);
            $("#transferLoader").show();
            adminId = adminId;
            $('input[name="addedCustomerIDs[]"]:checked').map(function () {
                customerIDs.push($(this).val());
            });
            $.ajax({
                method: "get",
                url: baseUrl + "/RemoveCustomerFromAdmin",
                data: {
                    _token: "{{ csrf_token() }}",
                    adminId: adminId,
                    customerIDs: customerIDs,
                },
                async: true,
                success: function (arrayed_result) {
                    if (arrayed_result != 1) {
                        $("#addedCustomer").empty();
                        arrayed_result.forEach((element, index) => {
                            $("#addedCustomer").append(
                                `
                <tr  onclick="checkCheckBox(this,event); selectTableRow(this)">
                    <td>` +
                                (index + 1) +
                                `</td>
                    <td>` +
                                element.PCode +
                                `</td>
                    <td>` +
                                element.Name +
                                `</td>
                    <td>
                    <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` +
                                element.PSN +
                                `">
                    </td>
                </tr>
            `
                            );
                        });
                    } else {
                        swal({
                            title: "اخطار!",
                            text: "تاریخچه ثبت نمی شود، می خواهید انجام شود؟",
                            icon: "warning",
                            buttons: true,
                        }).then(function (willDelete) {
                            if (willDelete) {
                                $("#selectAllTopLeft").prop("checked", false);
                                $("#transferLoader").show();
                                $.ajax({
                                    method: "get",
                                    url: baseUrl + "/RemoveCustomerAndEmpty",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        adminId: adminId,
                                        customerIDs: customerIDs,
                                    },
                                    async: true,
                                    success: function (arrayed_result) {
                                        $("#addedCustomer").empty();
                                        arrayed_result.forEach(
                                            (element, index) => {
                                                $("#addedCustomer").append(
                                                    `
                        <tr  onclick="checkCheckBox(this,event); selectTableRow(this)">
                            <td>` +
                                                    (index + 1) +
                                                    `</td>
                            <td>` +
                                                    element.PCode +
                                                    `</td>
                            <td>` +
                                                    element.Name +
                                                    `</td>
                            <td>
                            <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` +
                                                    element.PSN +
                                                    `">
                            </td>
                        </tr>
                    `
                                                );
                                            }
                                        );
                                        $("#transferLoader").css(
                                            "display",
                                            "none"
                                        );
                                    },
                                    error: function (data) { },
                                });
                            }
                        });
                    }
                    $("#transferLoader").hide();
                },
                error: function (data) { },
            });
            //برای نمایش مشتریان بدون کاربر سمت راست
            $.ajax({
                method: "get",
                url: baseUrl + "/getCustomer",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                async: true,
                success: function (arrayed_result) {
                    $("#allCustomer").empty();

                    arrayed_result.forEach((element, index) => {
                        $("#allCustomer").append(
                            `
            <tr onclick="checkCheckBox(this,event); selectTableRow(this)">
                <td style="">` +
                            (index + 1) +
                            `</td>
                <td style="">` +
                            element.PCode +
                            `</td>
                <td>` +
                            element.Name +
                            `</td>
                <td style="">
                <input class="form-check-input" name="customerIDs[]" type="checkbox" value="` +
                            element.PSN +
                            `" id="customerId">
                </td>
            </tr>
        `
                        );
                    });
                },
                error: function (data) { },
            });
        }
    });
});

$("#searchAddedCity").on("change", function () {
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAssignRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#searchAddedCity").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $("#searchAddedMantagheh").empty();
            arrayed_result.forEach((element, index) => {
                $("#searchAddedMantagheh").append(
                    `
            <option value="` +
                    element.SnMNM +
                    `">` +
                    element.NameRec +
                    `</option>
        `
                );
            });
        },
        error: function (data) { },
    });
});
$("#searchByCity").on("change", () => {
    if($("#searchByCity").val()!=0){
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAssignRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#searchByCity").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $("#searchByMantagheh").empty();
            arrayed_result.forEach((element, index) => {
                $("#searchByMantagheh").append(
                    `
            <option value="` +
                    element.SnMNM +
                    `">` +
                    element.NameRec +
                    `</option>
        `
                );
            });
        },
        error: function (data) { },
    });
}else{
    $("#searchByMantagheh").empty();
        $("#searchByMantagheh").append(
            `
    <option value="0">همه</option>
`
        );
}
});
$("#searchByCitySalesRep").on("change", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAssignRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#searchByCitySalesRep").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $("#searchByMantagheh").empty();
            $("#searchByMantagheh").append(
                `<option value=""> همه</option>`);

            arrayed_result.forEach((element, index) => {
                $("#searchByMantagheh").append(
                    `<option value="` + element.NameRec +`">` + element.NameRec + `</option>`
                );
            });
        },
        error: function (data) { },
    });
});

$("#searchAlarmByCity").on("change", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAssignRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#searchAlarmByCity").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $("#searchAlarmByMantagheh").empty();
            arrayed_result.forEach((element, index) => {
                $("#searchAlarmByMantagheh").append(
                    `
            <option value="` +
                    element.SnMNM +
                    `">` +
                    element.NameRec +
                    `</option>
        `
                );
            });
        },
        error: function (data) { },
    });
});

function getAlarmHistory(history) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAlarmsHistory",
        data: {
            _token: "{{ csrf_token() }}",
            history: history
        },
        async: true,
        success: function (msg) {
            $("#alarmsbody").empty();
            msg.forEach((element, index) => {
                $("#alarmsbody").append(`<tr onClick="setAlarmCustomerStuff(this,` + element.id + `); selectTableRow(this)">
                    <td>`+ (index + 1) + `</td>
                    <td style="width="158px">`+ element.Name + `</td>
                    <td style="width:111px">`+ element.PhoneStr + `</td>
                    <td style="width:111px">` + moment(element.TimeStamp, "YYYY/M/D") .locale("fa").format("YYYY/M/D") + `</td>
                    <td style="width:99px">`+ element.countCycle + `</td>
                    <td style="width:77px">`+ element.NameRec + `</td>
                    <td style="width:66px">`+ element.assignedDays + `</td>
                    <td style="width:111px">`+ element.FactDate + `</td>
                    <td style="width:111px; color:red">`+ element.alarmDate + `</td>
                    <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+ element.PSN + `_` + element.adminSn + `_` + element.SerialNoHDS + `"></td>
                </tr>`);
            })
        }
        , error: function (error) {

        }
    });
}

function getDoneAlarmHistory(history){
    $.ajax({
        method: "get",
        url: baseUrl + "/getDoneAlarmsHistory",
        data: {
            _token: "{{ csrf_token() }}",
            history: history
        },
        async: true,
        success: function (msg) {
            $("#alarmsbody").empty();
            msg.forEach((element, index) => {
                $("#alarmsbody").append(`<tr onClick="setAlarmCustomerStuff(this,` + element.id + `); selectTableRow(this)">
                    <td>`+ (index + 1) + `</td>
                    <td>`+ element.Name + `</td>
                    <td>`+ element.PhoneStr + `</td>
                    <td style="width:111px">` + moment(element.TimeStamp, "YYYY/M/D").locale("fa") .format("YYYY/M/D") + `</td>
                    <td  style="width:99px">`+ element.countCycle + `</td>
                    <td style="width:77px">`+ element.NameRec + `</td>
                    <td style="width:66px">`+ element.assignedDays + `</td>
                    <td style="width:111px">`+ element.FactDate + `</td>
                    <td style="width:111px; color:red">`+ element.alarmDate + `</td>
                    <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+ element.PSN + `_` + element.adminSn + `_` + element.SerialNoHDS + `"></td>
                </tr>`);
            })
        }
        , error: function (error) {

        }
    }); 
}

$("#filterAlarmsForm").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (msg) {
            if (!$("#customerWithOutAlarm").is(":checked")) {
                $("#alarmedCustomers").show();
                $("#unAlarmedCustomers").hide();
                $("#alarmsbody").empty();
                msg.forEach((element, index) => {
                    $("#alarmsbody").append(`
                    <tr onClick="setAlarmCustomerStuff(this,` + element.id + `); selectTableRow(this)">
                        <td>`+ (index + 1) + `</td>
                        <td  style="width:166px; font-size:12px;">`+ element.Name + `</td>
                        <td  style="width:111px; font-size:12px;  word-wrap: break-word;">`+ element.PhoneStr + `</td>
                        <td class="forMobile-hide"  style="width:100px">` + moment(element.TimeStamp, "YYYY/M/D HH:mm:ss").locale("fa").format("YYYY/M/D") + `</td>
                            <td  class="forMobile-hide" style="width:66px; text-wrap:wrap">`+ element.countCycle + `</td>
                            <td class="forMobile-hide" style="width:77px">`+ element.NameRec + `</td>
                            <td class="forMobile-hide"  style="width:50px">`+ element.assignedDays + `</td>
                            <td class="forMobile-hide"  style="width:111px;">`+ (element.FactDate ||'') + `</td>
                            <td class="forMobile-hide"  style="width:111px; color:red">`+ element.alarmDate + `</td>
                            <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+ element.PSN + `_` + element.adminSn + `_` + element.SerialNoHDS + `"></td>
                        </tr>`);
                })
            } else {
                $("#customerWithOutAlarmBuyOrNot").show();
                $("#unAlarmedCustomers").show();
                $("#alarmedCustomers").hide();
                $("#alarmDates").hide();
                $("#alamButtonsHistoryDiv").hide();
                $("#unalarmsbody").empty();
                msg.forEach((element, index) => {
                    $("#unalarmsbody").append(`
                       <tr  onclick="setUnAlarmStuff(this,` + element.PSN + `,` + element.adminId + `); selectTableRow(this)">
                            <td >`+ (index + 1) + `</td>
                            <td style="width:55px">`+ element.PCode + `</td>
                            <td>`+ element.Name + `</td>
                            <td>`+ element.PhoneStr + `</td>
                            <td class="forMobile-hide" style="width:77px">`+ element.NameRec + `</td>
                            <td class="forMobile-hide" style="width:111px">`+ ( element.FactDate || '') + `</td>
                            <td class="forMobile-hide" style="width:166px">`+( element.adminName || '') + `</td>
                            <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+ element.PSN + `_` + element.adminId + `_` + element.SerialNoHDS + `"></td>
                        </tr>`);
                })
            }
        },
        error: function (error) {

        }
    });
})

$("#orderUnAlarms").on("change", function () {
    searchTerm = $("#searchAlarmName").val();
    snMantagheh = $("#searchAlarmByMantagheh").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/orderUnAlarms",
        data: {
            _token: "{{ csrf_token() }}",
            baseName: $("#orderUnAlarms").val(),
            searchTerm: searchTerm,
            snMantagheh: snMantagheh
        },
        async: true,
        success: function (msg) {
            $("#unalarmsbody").empty();
            msg.forEach((element, index) => {
                $("#unalarmsbody").append(` 
                    <tr  onclick="setUnAlarmStuff(this,` + element.PSN + `,` + element.adminId + `); selectTableRow(this)">
                        <td >`+ (index + 1) + `</td>
                        <td style="width="158px">`+ element.Name + `</td>
                        <td>`+ element.PCode + `</td>
                        <td>`+ element.PhoneStr + `</td>
                        <td style="width:77px">`+ element.NameRec + `</td>
                        <td style="width:111px;">`+ element.FactDate + `</td>
                        <td style="width:166px">`+ element.adminName + `</td>
                        <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+ element.PSN + `_` + element.adminId + `_` + element.SerialNoHDS + `"></td>
                    </tr>`);
            });
        }
        , error: function (error) {

        }
    });
});

function setUnAlarmStuff(element, customerSn, adminId) {
    $("tr").removeClass("selected");
    $(element).toggleClass("selected");
    $("#customerSn").val(customerSn);
    $("#adminSn").val(adminId);
    $(".enableBtn").prop("disabled", false);
}

function getUnAlarmHistory(history) {

    $.ajax({
        method: "get",
        url: baseUrl + "/getUnAlarmHistory",
        data: {
            _token: "{{ csrf_token() }}",
            history: history
        },
        async: true,
        success: function (msg) {
            $("#unalarmsbody").empty();
            msg.forEach((element, index) => {
                $("#unalarmsbody").append(`
                         <tr onclick="setUnAlarmStuff(this,` + element.PSN + `,` + element.adminId + `); selectTableRow(this)">
                            <td >`+ (index + 1) + `</td>
                            <td style="width="158px">`+ element.Name + `</td>
                            <td>`+ element.PCode + `</td>
                            <td>`+ element.PhoneStr + `</td>
                            <td style="width:77px">`+ element.NameRec + `</td>
                            <td style="width:111px;">`+ element.FactDate + `</td>
                            <td style="width:166px">`+ element.adminName + `</td>
                            <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+ element.PSN + `_` + element.adminId + `_` + element.SerialNoHDS + `"></td>
                        </tr>`);
            });
        }
        , error: function (error) {

        }
    });

}

$("#searchCity").on("change", function () {
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAssignRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#searchCity").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $("#activeOrInActive").prop("disabled", false);
            $("#searchMantagheh").empty();
            arrayed_result.forEach((element, index) => {
                $("#searchMantagheh").append(
                    `
            <option value="` +
                    element.SnMNM +
                    `">` +
                    element.NameRec +
                    `</option>
        `
                );
            });
        },
        error: function (data) { },
    });
});
$("#snNahiyehE").on("change", function () {
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAssignRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#snNahiyehE").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $("#snMantaghehE").empty();
            arrayed_result.forEach((element, index) => {
                $("#snMantaghehE").append(
                    `
            <option value="` +
                    element.SnMNM +
                    `">` +
                    element.NameRec +
                    `</option>
        `
                );
            });
        },
        error: function (data) { },
    });
});
$("#findMantaghehByCity").on("change", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/searchRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#findMantaghehByCity").val(),
            adminId:$("#employeeId").val()
        },
        async: true,
        success: function (arrayed_result) {
            $("#searchCustomerByMantagheh").empty();
            $("#searchCustomerByMantagheh").append(
                `<option value="0">همه</option>`
            );
            arrayed_result.forEach((element, index) => {
                $("#searchCustomerByMantagheh").append(
                    `<option value="` + element.SnMNM + `">` + element.NameRec + `</option> `
                );
            });
        },
        error: function (data) { },
    });
});

$("#searchCustomerByMantagheh").on("change", () => {
    let searchTerm1 = $("#searchCustomerByMantagheh").val();
    $("#mantaghehId").val(searchTerm1);
    $.ajax({
        method: "get",
        url: baseUrl + "/searchCustomerByMantagheh",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1,
            adminId:$("#employeeId").val()
        },
        async: true,
        success: function (msg) {
            $("#customerListBody1").empty();
            msg.forEach((element, index) => {
                let backgroundColor = "";
                if (element.countComment > 0) {
                    backgroundColor = "lightblue";
                }
                $("#customerListBody1").append( `
                    <tr onclick="selectAndHighlight(this); selectTableRow(this); getCustomerInformation(`+element.PSN+`)" style="background-color:` +backgroundColor +  `">
                        <td class="forMobileDisplay" style="width:55px">` +(index + 1) + `</td>
                        <td  class="forMobileDisplay" style="width:66px">` +element.PCode + `</td>
                        <td>` + element.Name +  `</td>
                        <td  class="forMobileDisplay" style="width:222px">` +  element.peopeladdress + `</td>
                        <td>` + element.PhoneStr +  `</td>
                        <td>` + element.NameRec + `</td>
                        <td  style="width:88px"> <input class="customerList form-check-input" name="customerId" type="radio" value="` + element.PSN + `_` + element.GroupCode + `"></td>
                    </tr>`
                );
            });
        },
        error: function (data) { },
    });
});

$("#searchMantagheh").on("change", function () {
    $.ajax({
        method: "get",
        url: baseUrl + "/searchCustomerByRegion",
        data: {
            _token: "{{ csrf_token() }}",
            rsn: $("#searchMantagheh").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $("#allCustomer").empty();
            arrayed_result.forEach((element, index) => {
                $("#allCustomer").append(
                    `
                <tr onclick="checkCheckBox(this,event);  selectTableRow(this);">
                    <td >` + (index + 1) + `</td>
                    <td>` + element.PCode + `</td>
                    <td>` + element.Name + `</td>
                    <td > <input class="form-check-input" name="customerIDs[]" type="checkbox" value=" ` + element.PSN + ` " id="customerId">
                    </td>
                </tr>
            `
                );
            });
        },
        error: function (data) { },
    });
});

$("#searchAddedMantagheh").on("change", function () {
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAddedCustomerByRegion",
        data: {
            _token: "{{ csrf_token() }}",
            rsn: $("#searchAddedMantagheh").val(),
            asn: $("#takhsisToAdminBtn").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $("#addedCustomer").empty();
            arrayed_result.forEach((element, index) => {
                $("#addedCustomer").append(
                    `
            <tr onclick="checkCheckBox(this,event); selectTableRow(this);">
                <td id="radif">` +
                    (index + 1) +
                    `</td>
                <td id="mCode">` +
                    element.PCode +
                    `</td>
                <td>` +
                    element.Name +
                    `</td>
                <td>
                    <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` +
                    element.PSN +
                    `" id="kalaId">
                </td>
            </tr>
        `
                );
            });
        },
        error: function (data) { },
    });
});

$("#searchAddedNameByMNM").on("keyup", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAddedCustomerByNameMNM",
        data: {
            _token: "{{ csrf_token() }}",
            rsn: $("#searchAddedMantagheh").val(),
            asn: $("#takhsisToAdminBtn").val(),
            name: $("#searchAddedNameByMNM").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $("#addedCustomer").empty();
            arrayed_result.forEach((element, index) => {
                $("#addedCustomer").append(
                    `
        <tr onclick="checkCheckBox(this,event);  selectTableRow(this);">
            <td id="radif">` +
                    (index + 1) +
                    `</td>
            <td id="mCode">` +
                    element.PCode +
                    `</td>
            <td>` +
                    element.Name +
                    `</td>
            <td>
                <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` +
                    element.PSN +
                    `" id="kalaId">
            </td>
        </tr>
        `
                );
            });
        },
        error: function (data) { },
    });
});

$("#addCommentForm").submit(function (e) {
    $("#addComment").modal("hide");
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            swal({
                title: "موفق!",
                text: "ثبت شد!",
                icon: "success",
                buttons: true,
            });
            $("#firstComment").val("");
            $("#secondComment").val("");
            $("#commentDate2").val("");
            moment.locale("en");
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerComments").empty();
            data[0].forEach((element, index) => {
                $("#customerComments").append(
                    `<tr class="tbodyTr" onclick="selectTableRow(this);">
                <td> ` + (index + 1) + ` </td>
                <td>` + moment(element.TimeStamp, "YYYY/M/D HH:mm:ss").locale("fa").format("YYYY/M/D") + `</td>
                <td onclick="viewComment(` + element.id + `)">` + element.newComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i> </td>
                <td onclick="viewNextComment(` + element.id + `)">` + element.nexComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i>  </td>
                <td style="width:111px;">` + moment(element.specifiedDate, "YYYY/M/D HH:mm:ss").locale("fa").format("YYYY/M/D") + `</td>
                </tr>`
                );
            });
            $("#customerListBody1").empty();
            data[1].forEach((element, index) => {
                let backgroundColor = "";
                if (element.maxTime) {
                    backgroundColor = "lightblue";
                }
                $("#customerListBody1").append(
                    `
            <tr onclick="selectAndHighlight(this); selectTableRow(this)" style="background-color:` + backgroundColor + `">
            <td>` + (index + 1) + `</td>
            <td style="66px;">` + element.PCode + `</td>
            <td>` + element.Name + `</td>
            <td>` + element.peopeladdress + `</td>
            <td>` + element.sabit + `</td>
            <td>` + element.hamrah + `</td>
            <td>` + element.NameRec + `</td>
            <td>2</td>
            <td style="width:100px;"> <input class="customerList form-check-input" name="customerId" type="radio" value="` + element.PSN + `_` + element.GroupCode + `"></td>
            </tr>`
                );
            });
            // $('.crmDataTable').dataTable();
        },
    });
    e.preventDefault();
});
$(".openAddCommentModal").on("click", () => {
    if (!$(".modal.in").length) {
        $(".modal-dialog").css({
            top: 0,
            left: 0,
        });
    }
    $("#addComment").modal({
        backdrop: false,
        show: true,
    });

    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });

    $("#addComment").modal("show");
});
$("#openDashboardForAlarm").on("click", () => {
    let csn = $("#customerSn").val();
    $("#customerSnLogin").val(csn);
    $.ajax({
        method: "get",
        url: baseUrl + "/customerDashboard",
        dataType: "json",
        contentType: "json",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
        },
        async: true,
        success: function (msg) {
            moment.locale("en");
            let exactCustomer = msg[0];
            
            let factors = msg[1];
            let goodDetails = msg[2];
            let basketOrders = msg[3];
            let comments = msg[4];
            let specialComments = msg[5];
            let specialComment = specialComments[0];
            let assesments = msg[6];
            let returnedFactors = msg[7];
            let loginInfo = msg[8];
            if (specialComment) {
            $("#customerProperty").val(specialComment.comment.trim());
            }
            $("#dashboardTitle").text(exactCustomer.Name);
            $("#customerCode").text(exactCustomer.PCode);
            $("#customerName").text(exactCustomer.Name);
            $("#customerAddress").text(exactCustomer.peopeladdress);
            $("#username").text(exactCustomer.userName);
            $("#password").text(exactCustomer.customerPss);
            $("#mobile1").text(exactCustomer.PhoneStr);
            $("#customerIdForComment").text(exactCustomer.PSN);
            $("#countFactor").text(exactCustomer.countFactor);
            $("#factorTable").empty();
            factors.forEach((element, index) => {
                $("#factorTable").append(
                    `<tr class="tbodyTr" onclick="selectTableRow(this)">
                        <td>` + (index + 1) +`</td>
                        <td>` + element.FactDate + `</td>
                        <td>نامعلوم</td>
                        <td>` + parseInt(element.TotalPriceHDS / 10).toLocaleString("en-us") +`</td>
                        <td onclick="showFactorDetails(this)"><input name="factorId" style="display:none"  type="radio" value="` + element.SerialNoHDS +`" /><i class="fa fa-eye" /></td>
                    </tr>
             ` );
            });

            $("#returnedFactorsBody").empty();
            returnedFactors.forEach((element, index) => {
                $("#returnedFactorsBody").append(
                    `<tr class="tbodyTr" onclick="selectTableRow(this)">
            <td>` +
                    (index + 1) +
                    `</td>
            <td>` +
                    element.FactDate +
                    `</td>
            <td>نامعلوم</td>
            <td style="width:111px">` +
                    parseInt(element.TotalPriceHDS / 10).toLocaleString(
                        "en-us"
                    ) +
                    `</td>
            </tr>`
                );
            });
            $("#goodDetail").empty();
            goodDetails.forEach((element, index) => {
                $("#goodDetail").append(
                    `
            <tr class="tbodyTr" onclick="selectTableRow(this)">
                <td>` +(index + 1) + ` </td>
                <td>` + moment(element.maxTime, "YYYY/M/D HH:mm:ss") .locale("fa") .format("YYYY/M/D") + `</td>
                <td>` + element.GoodName +
                    `</td>
                <td>  </td>
                <td>  </td>
                
            </tr>`
                );
            });

            $("#basketOrders").empty();
            basketOrders.forEach((element, index) => {
                $("#basketOrders").append(
                    `<tr onclick="selectTableRow(this)">
                <td>` +
                    (index + 1) +
                    `</td>
                <td>` +
                    moment(element.TimeStamp, "YYYY/M/D HH:mm:ss")
                        .locale("fa")
                        .format("YYYY/M/D") +
                    `</td>
                <td>` +
                    element.GoodName +
                    `</td>
                <td>` +
                    element.Amount +
                    `</td>
                <td>` +
                    element.Fi +
                    `</td>
                </tr>`
                );
            });

            $("#customerLoginInfoBody").empty();
            if (loginInfo) {
                loginInfo.forEach((element, index) => {
                    $("#customerLoginInfoBody").append(
                        `<tr onclick="selectTableRow(this)">
                <td>` + (index + 1) + `</td>
                <td>` + moment(element.visitDate, "YYYY/M/D HH:mm:ss").locale("fa").format("YYYY/M/D") + `</td>
                <td>` + element.platform + `</td>
                <td class="forMobile-hide" style="width:155px;">` + element.browser + `</td>
                </tr>`
                    );
                });
            }

            $("#customerComments").empty();
            comments.forEach((element, index) => {
                $("#customerComments").append(`<tr class="tbodyTr" onclick="selectTableRow(this)">
                <td> ` + (index + 1) + ` </td>
                <td>` + moment(element.TimeStamp, "YYYY/M/D HH:mm:ss").locale("fa").format("YYYY/M/D") + `</td>
                <td onclick="viewComment(` + element.id + `)"</td>` + element.newComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i> </td>
                <td onclick="viewNextComment(` + element.id + `)">` + element.nexComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i>  </td>
                <td style=""width:111px;">` + moment(element.specifiedDate, "YYYY/M/D").locale("fa").format("YYYY/M/D") + `</td>
                </tr>`
                );
            });
            $("#customerAssesments").empty();
            assesments.forEach((element, index) => {
                let driverBehavior = "";
                let shipmentProblem = "بله";
                if (element.shipmentProblem == 1) {
                    shipmentProblem = "خیر";
                }
                switch (parseInt(element.driverBehavior)) {
                    case 1:
                        driverBehavior = "عالی";
                        break;
                    case 2:
                        driverBehavior = "خوب";
                        break;
                    case 3:
                        driverBehavior = "متوسط";
                        break;
                    case 4:
                        driverBehavior = "بد";
                        break;
                    default:
                        break;
                }
                $("#customerAssesments").append(
                    ` <tr onclick="selectTableRow(this)">
                            <td>` + (index + 1) + `</td>
                            <td>` + moment(element.TimeStamp, "YYYY/M/D").locale("fa").format("YYYY/M/D") +`</td>
                            <td>` + element.comment +`</td>
                            <td>` + driverBehavior +`</td>
                            <td class="for-mobil">` +shipmentProblem +`</td>
                            <td style="width:70px;"> </td>
                    </tr>`
                );
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#customerDashboard").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#customerDashboard").modal("show");
        },
        error: function (data) { },
    });
});

function alarmHistory() {
    let factorId = $("#factorAlarm").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/getAlarmHistory",
        data: {
            _token: "{{ csrf_token() }}",
            fsn: factorId,
        },
        async: true,
        success: function (data) {
            $("#alarmHistoryBody").empty();
            data.forEach((element, index) => {
                $("#alarmHistoryBody").append(
                    `
            <tr onclick="selectTableRow(this)">
            <td>` +
                    (index + 1) +
                    `</td>
            <td>` +
                    element.TimeStamp +
                    `</td>
            <td>` +
                    element.comment +
                    `</td>
           <td style="width:210px;">`+element.adminName+`</td>
            </tr>`
                );
            });
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#alarmHistoryModal").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#alarmHistoryModal").modal("show");
        },
    });
}
$(".select-highlight tr").click(function () {
    $(this).children("td").children("input").prop("checked", true);
    $(".enableBtn").prop("disabled", false);
    if ($(".enableBtn").is(":disabled")) {
    } else {
        $(".enableBtn").css("color", "red !important");
    }
    $(".select-highlight tr").removeClass("selected");

    $(this).toggleClass("selected");
    $("#customerSn").val(
        $(this).children("td").children("input").val().split("_")[0]
    );
    $("#customerIdForComment").val($("#customerSn").val());
});

function selectAndHighlight(element) {
    $(element).children("td").children("input").prop("checked", true);
    $(".enableBtn").prop("disabled", false);
    if ($(".enableBtn").is(":disabled")) {
    } else {
        $(".enableBtn").css("color", "red !important");
    }
    $(".select-highlight tr").removeClass("selected");

    $(element).toggleClass("selected");
    $("#customerSn").val(
        $(element).children("td").children("input").val().split("_")[0]
    );
    $("#customerIdForComment").val($("#customerSn").val());
}

$("#openCustomerActionModal").on("click", () => {
    let csn = $("#customerSn").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/customerDashboardForAdmin",
        data: {
            _token: "{{csrf_token()}}",
            csn: csn,
        },
        async: true,
        success: function (msg) {
            moment.locale("en");
            let exactCustomer = msg[0];
            let factors = msg[1];
            let goodDetails = msg[2];
            let basketOrders = msg[3];
            let comments = msg[4];
            let specialComments = msg[5];
            let assesments = msg[6];
            let returendFactors = msg[7];
            let specialComment = specialComments[0];
            $("#customerProperty").text(specialComment.comment.trim());
            $("#customerCode").val(exactCustomer.PCode);
            $("#customerName").val(exactCustomer.Name);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#mobile1").val(exactCustomer.PhoneStr);
            $("#username").val(exactCustomer.userName);
            $("#password").val(exactCustomer.customerPss);
            let adminName =
                exactCustomer.adminName.trim() +
                " " +
                exactCustomer.lastName.trim();
            $("#admin").val(adminName);
            $("#customerIdForComment").val(exactCustomer.PSN);
            $("#countFactor").val(exactCustomer.countFactor);
            $("#factorTable").empty();
            factors.forEach((element, index) => {
                $("#factorTable").append(
                    `
            <tr onclick="selectTableRow(this)">
                <td>` + (index + 1) + `</td>
                <td>` +  element.FactDate + `</td>
                <td>ناuمعلوم</td>
                <td>` + parseInt(element.TotalPriceHDS / 10).toLocaleString( "en-us") + `</td>
                <td onclick="showFactorDetails(this)"><input name="factorId" style="display:none"  type="radio" value="` +element.SerialNoHDS + `" /><i class="fa fa-eye" /></td>
            </tr>
            `
                );
            });

            $("#returnedFactorTable").empty();
            returendFactors.forEach((element, index) => {
                $("#returnedFactorTable").append(
                    `
            <tr onclick="selectTableRow(this)">
                <td>` +
                    (index + 1) +
                    `</td>
                <td>` +
                    element.FactDate +
                    `</td>
                <td>نامعلوم</td>
                <td>` +
                    parseInt(element.TotalPriceHDS / 10).toLocaleString(
                        "en-us"
                    ) +
                    `</td>
            </tr>
            `
                );
            });
            $("#goodDetail").empty();
            goodDetails.forEach((element, index) => {
                $("#goodDetail").append(
                    `<tr onclick="selectTableRow(this)">
            <td>` +
                    (index + 1) +
                    ` </td>
            <td>` +
                    moment(element.maxTime, "YYYY/M/D")
                        .locale("fa")
                        .format("YYYY/M/D") +
                    `</td>
            <td>` +
                    element.GoodName +
                    `</td>
            <td> </td>
            </tr>`
                );
            });
            $("#basketOrders").empty();
            basketOrders.forEach((element, index) => {
                $("#basketOrders").append(
                    `<tr>
                <td>` +
                    (index + 1) +
                    `</td>
                <td>` +
                    moment(element.TimeStamp, "YYYY/M/D")
                        .locale("fa")
                        .format("YYYY/M/D") +
                    `</td>
                <td>` +
                    element.GoodName +
                    `</td>
                <td>` +
                    element.Amount +
                    `</td>
                <td>` +
                    element.Fi +
                    `</td>
                </tr>`
                );
            });
            $("#customerComments").empty();
            comments.forEach((element, index) => {
                $("#customerComments").append(
                    `<tr onclick="selectTableRow(this)">
                <td> ` + (index + 1) + ` </td>
                <td>` + moment(element.TimeStamp, "YYYY/M/D").locale("fa").format("YYYY/M/D") + `</td>
                <td  onclick="viewComment(` + element.id + `)"</td>` + element.newComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i> </td>
                <td  onclick="viewNextComment(` + element.id + `)">` + element.nexComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i>  </td>
                <td style="width:111px !important;">` + moment(element.specifiedDate, "YYYY/M/D").locale("fa").format("YYYY/M/D") + `</td>
                </tr>`
                );
            });
            $("#karbarActionAssesment").empty();
            assesments.forEach((element, index) => {
                let driverBehavior = "";
                let shipmentProblem = "بله";
                if (element.shipmentProblem == 1) {
                    shipmentProblem = "خیر";
                }
                switch (parseInt(element.driverBehavior)) {
                    case 1:
                        driverBehavior = "عالی";
                        break;
                    case 2:
                        driverBehavior = "خوب";
                        break;
                    case 3:
                        driverBehavior = "متوسط";
                        break;
                    case 4:
                        driverBehavior = "بد";
                        break;
                    default:
                        break;
                }
                $("#karbarActionAssesment").append(
                    `
            <tr onclick="selectTableRow(this)">
            <td>` +
                    (index + 1) +
                    `</td>
            <td>` +
                    moment(element.TimeStamp, "YYYY/M/D")
                        .locale("fa")
                        .format("YYYY/M/D") +
                    `</td>
            <td>` +
                    element.comment +
                    `</td>
            <td>` +
                    driverBehavior +
                    `</td>
            <td>` +
                    shipmentProblem +
                    `</td>
            <td> <i class="fa fa-eye"/> </td>
            <td><input type="radio" class="form-input"/></td>
        </tr>
            `
                );
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#reportCustomerModal").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#reportCustomerModal").modal("show");
        },
        error: function (data) { },
    });
});

function moveCustomerToAdmin() {
    let csn = $("#customerSn").val();
    let firstAdminID = $("#adminSn").val();
    let newAdminSn = $("#adminID").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/takhsisCustomer",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
            asn: newAdminSn,
            FirstAdminID: firstAdminID,
        },
        async: true,
        success: function (msg) {
            if ($("#changeAdminModal")) {
                $("#changeAdminModal").modal("hide");
            }
            if ($("#takhsisCustomerModal")) {
                $("#takhsisCustomerModal").modal("hide");
            }

            $("#submitFilterAlarmFormBtn").click();

        }
        , error: function (error) {

        }
    });
}

function takhsisCustomerAlarm() {
    let csn = $("#customerSn").val();

    $.ajax({
        method: "get",
        url: baseUrl + "/getCustomerAndAdminInfo",
        data: {
            _token: "{{csrf_token()}}",
            csn: csn,
            asn: 0
        },
        async: true,
        success: function (msg) {
            $("#customerToTakhsisBody").empty();
            msg[0].forEach((element, index) => {
                $("#customerToTakhsisBody").append(`
                <tr onclick="selectTableRow(this)">
                    <td>`+ (index + 1) + `</td>
                    <td> `+ element.Name + ` </td>
                    <td>`+ element.PhoneStr + `</td>
                    <td style="display:none"><input type="radio" value="`+ element.PSN + `" name="customerToMove"/></td>
                </tr>`);
            })
            $("#selectKarbarToTakhsis").empty();
            msg[1].forEach((element, index) => {
                adminType = "";
                discription = "توضیحی ندارد";
                if (element.discription != null) {
                    discription = element.discription;
                }
                switch (element.adminType) {
                    case 2:
                        adminType = "پشتیبان";
                        break;
                    case 1:
                        adminType = "ادمین";
                        break;
                    case 3:
                        adminType = "بازاریاب";
                        break;
                }
                $("#selectKarbarToTakhsis").append(`
                <tr onclick="selectKarbarToTakeCustomer(this,`+ element.id + `)">
                    <td>`+ (index + 1) + `</td>
                    <td> `+ element.name + ` ` + element.lastName + ` </td>
                    <td> `+ adminType + ` </td>
                    <td>`+ element.discription + `</td>
                    <td><input type="radio" value="`+ element.id + `" name="AdminToMove"/></td>
                </tr>`);
            })

            $("#takhsisCustomerModal").modal("show");
        },
        error: function (error) {
            alert("در تخصیص مشتری ارور وجود دارد.")
        }
    });


}

function changeAdminAlarm() {
    let csn = $("#customerSn").val();
    let asn = $("#adminSn").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/getCustomerAndAdminInfo",
        data: {
            _token: "{{csrf_token()}}",
            csn: csn,
            asn: asn
        },
        async: true,
        success: function (msg) {
            $("#customerToMoveBody").empty();
            msg[0].forEach((element, index) => {
                $("#customerToMoveBody").append(`
                <tr onclick="selectTableRow(this)">
                    <td>`+ (index + 1) + `</td>
                    <td> `+ element.Name + ` </td>
                    <td> `+ element.name + ` ` + element.lastName + ` </td>
                    <td>`+ element.PhoneStr + `</td>
                    <td style="display:none"><input type="radio" value="`+ element.PSN + `" name="customerToMove"/></td>
                </tr>`);
            })
            $("#selectKarbarToMove").empty();
            msg[1].forEach((element, index) => {
                adminType = "پشتیبان";
                discription = "توضیحی ندارد";
                if (element.discription != null) {
                    discription = element.discription;
                }
                switch (element.adminType) {
                    case 2:
                        adminType = "پشتیبان";
                        break;
                    case 1:
                        adminType = "ادمین";
                        break;
                    case 3:
                        adminType = "بازاریاب";
                        break;
                }
                $("#selectKarbarToMove").append(`
                <tr onclick="selectKarbarToTakeCustomer(this,`+ element.id + `)">
                    <td>`+ (index + 1) + `</td>
                    <td> `+ element.name + ` ` + element.lastName + ` </td>
                    <td> `+ adminType + ` </td>
                    <td>`+ element.discription + `</td>
                    <td><input type="radio" value="`+ element.id + `" name="AdminToMove"/></td>
                </tr>`);
            })

            $("#changeAdminModal").modal("show");
        },
        error: function (error) {
            alert("در تخصیص مشتری ارور وجود دارد.")
        }
    });

}

function selectKarbarToTakeCustomer(element, adminID) {
    $("tr").removeClass("selected");
    $(element).toggleClass("selected");
    $("#adminID").val(adminID);
}

function changeAlarm() {
    let csn = $("#customerSn").val();
    let asn = $("#adminSn").val();
    $("#adminIdForAlarm").val(asn);
    $("#customerIdForAlarm").val(csn);
    if (!$(".modal.in").length) {
        $(".modal-dialog").css({
            top: 0,
            left: 0,
        });
    }
    $("#changeAlarm").modal({
        backdrop: false,
        show: true,
    });

    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });
    $("#changeAlarm").modal("show");
}

$("#changeAlarmForm").on("submit", function (e) {
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#changeAlarm").modal("hide");

            $("#submitFilterAlarmFormBtn").click();
        },
    });
    e.preventDefault();
});

function assesmentStuff(element) {
    let input = $(element).find("input:radio").prop("checked", true);
    $("#customerSn").val(input.val().split("_")[0]);
    $("#factorSn").val(input.val().split("_")[1]);
    $("#customerIdForAssesment").val(input.val().split("_")[0]);
    $("#factorIdForAssesment").val(input.val().split("_")[1]);
    $("#openDashboard").prop("disabled", false);
    $("#openAssessmentModal1").prop("disabled", false);
    $("#customerSnLogin").val($("#customerSn").val());
    $("#fakeLogin").prop("disabled", false);

    $.ajax({
        method: "get",
        url: baseUrl + "/getFactorDetail",
        data: {
            _token: "{{ csrf_token() }}",
            FactorSn: $("#factorSn").val(),
        },
        async: true,
        success: function (msg) {
            $("#factorInfo").css({ display: "block" });
            let factor = msg[0];
            $("#factorDateP").text(factor.FactDate);
            $("#customerNameFactorP").text(factor.Name);
            $("#customerComenterP").text(factor.Name);
            $("#Admin1P").text(factor.lastName);
            $("#customerAddressFactorP").text(factor.peopeladdress);
            $("#customerPhoneFactorP").text(factor.sabit);
            $("#factorSnFactorP").text(factor.FactNo);
            $("#productListP").empty();
            msg.forEach((element, index) => {
                $("#productListP").append(
                   `<tr onclick="selectTableRow(this)">
                        <td class="driveFactor">` +(index + 1) +`</td>
                        <td>` + element.GoodName +` </td>
                        <td class="driveFactor">` + element.Amount / 1 + `</td>
                        <td>` + element.UName +`</td>
                        <td>` + (element.Fi / 10).toLocaleString("en-us") +`</td>
                        <td style="width:111px;">` + (element.goodPrice / 10).toLocaleString("en-us") + `</td>
                    </tr>`
                );
            });
        },
        error: function (data) { },
    });
}

function checkExistance(element) {
    userName = element.value;
    $.ajax({
        method: "get",
        url: baseUrl + "/checkUserNameExistance",
        data: {
            _token: "{{ csrf_token() }}",
            username: userName,
        },
        async: true,
        success: function (msg) {
            if (msg > 0) {
                $("#existAlert").text("قبلا موجود است");
                $("#submitNewAdminbtn").prop("disabled",true);
            }else{
                $("#existAlert").text("");
                $("#submitNewAdminbtn").prop("disabled",false);
            }
        },
        error: function (data) { },
    });
}
$("#emptyKarbarButton").on("click", () => {
    let asn = $("#emptyKarbarButton").val();

    $.ajax({
        method: "get",
        url: baseUrl + "/getAdminForEmpty",
        data: {
            _token: "{{ csrf_token() }}",
            asn: asn,
        },
        async: true,
        success: function (msg) {
            let admin = msg[0];
            let adminType = "";
            if (admin.adminType == 1) {
                adminType = "ادمین";
            } else {
                if (admin.adminType == 2) {
                    adminType = "پشتیبان";
                } else {
                    if (admin.adminType == 3) {
                        adminType = "بازاریاب";
                    } else {
                        if (admin.adminType == 4) {
                            adminType = "راننده";
                        }else{
                            if(admin.adminType == 7){
                                adminType = "سرپرست";
                            }else{
                                adminType = "مدیر";
                            }
                        }
                    }
                }
            }

            let discription = "";
            if (admin.discription != null) {
                discription = admin.discription;
            }
            if (admin.adminType != 5) {
                
                $("#emptyKarbar").empty();
                $("#emptyKarbar").append(
                    `<tr onclick="selectTableRow(this)">
                        <td> </td>
                        <td style="font-size:18px; font-weight:bold">` + admin.name + ` ` + admin.lastName + `</td>
                        <td style="font-size:18px; font-weight:bold">` + adminType + `</td>
                        <td>` + discription + `</td>
                        <td> </td>
                     </tr>`
                );

                if (!$(".modal.in").length) {
                    $(".modal-dialog").css({
                        top: 0,
                        left: 0,
                    });
                }
                $("#removeKarbar").modal({
                    backdrop: false,
                    show: true,
                });

                $(".modal-dialog").draggable({
                    handle: ".modal-header",
                });

                if (!$(".modal.in").length) {
                    $(".modal-dialog").css({
                        top: 0,
                        left: 0,
                    });
                }
                $("#removeKarbar").modal({
                    backdrop: false,
                    show: true,
                });

                $(".modal-dialog").draggable({
                    handle: ".modal-header",
                });
                $("#removeKarbar").modal("show");
            }else{
                alert("Good")
            }
        },
        error: function (data) { },
    });
});



$("#openDashboard").on("click", () => {
    let csn = $("#customerSn").val();
    $(".customerSnLogin").val($("#customerSn").val());
    $("#customerProperty").val("");
    $.ajax({
        method: "get",
        url: baseUrl + "/customerDashboard",
        dataType: "json",
        contentType: "json",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
        },
        async: true,
        success: function (msg) {
            moment.locale("en");
            let exactCustomer = msg[0];
            let factors = msg[1];
            let goodDetails = msg[2];
            let basketOrders = msg[3];
            let comments = msg[4];
            let specialComments = msg[5];
            let specialComment = specialComments[0];
            let assesments = msg[6];
            let returnedFactors = msg[7];
            let loginInfo = msg[8];
            if (specialComment) {
                $("#customerProperty").val(specialComment.comment.trim());
            }
            $("#dashboardTitle").text(exactCustomer.Name);
            $("#customerCode").text(exactCustomer.PCode);
            $("#customerName").text(exactCustomer.Name);
            $("#customerAddress").text(exactCustomer.peopeladdress);
            $("#username").text(exactCustomer.userName);
            $("#password").text(exactCustomer.customerPss);
            $("#mobile1").text(exactCustomer.PhoneStr);
            $("customerIdForComment").val(exactCustomer.PSN);
            $("#countFactor").text(exactCustomer.countFactor);
            $("#factorTable").empty();
            factors.forEach((element, index) => {
                $("#factorTable").append(
                    `<tr class="tbodyTr" onclick="selectTableRow(this)">
                        <td>` + (index + 1) + `</td>
                        <td>` + element.FactDate + `</td>
                        <td>نامعلوم</td>
                        <td>` +  parseInt(element.TotalPriceHDS / 10).toLocaleString(  "en-us") + `</td>
                        <td style="width:83px; !important;" onclick="showFactorDetails(this)"><input name="factorId" style="display:none"  type="radio" value="` +element.SerialNoHDS +`" /><i class="fa fa-eye" /></td>
                    </tr>`
                );
            });

            $("#returnedFactorsBody").empty();
            returnedFactors.forEach((element, index) => {
                $("#returnedFactorsBody").append(
                    `<tr class="tbodyTr" onclick="selectTableRow(this)">
                        <td>` + (index + 1) + `</td>
                        <td>` + element.FactDate + `</td>
                        <td> نامعلوم</td>
                        <td>` + parseInt(element.TotalPriceHDS / 10).toLocaleString("en-us") + `</td>
                        <td></td>
                </tr>`
                );
            });
            $("#goodDetail").empty();
            goodDetails.forEach((element, index) => {
                $("#goodDetail").append(`
                    <tr class="tbodyTr" onclick="selectTableRow(this)">
                        <td>` + (index + 1) + ` </td>
                        <td>` + moment(element.maxTime, "YYYY/M/D HH:mm:ss")
                                .locale("fa")
                                .format("YYYY/M/D") + `</td>
                        <td>` + element.GoodName + `</td>
                        <td>  </td>
                        <td>  </td>
                    </tr>`
                );
            });

            $("#basketOrders").empty();
            basketOrders.forEach((element, index) => {
                $("#basketOrders").append(
                    `<tr onclick="selectTableRow(this)">
                <td>` + (index + 1) + `</td>
                <td>` + moment(element.TimeStamp, "YYYY/M/D HH:mm:ss").locale("fa") .format("YYYY/M/D") + `</td>
                <td>` + element.GoodName + `</td>
                <td>` + element.Amount + `</td>
                <td>` + element.Fi + `</td>
                </tr>`
                );
            });

            $("#customerLoginInfoBody").empty();
            if (loginInfo) {
                loginInfo.forEach((element, index) => {
                    $("#customerLoginInfoBody").append(
                        `<tr onclick="selectTableRow(this)">
                        <td>` + (index + 1) + `</td>
                        <td>` + moment(element.visitDate, "YYYY/M/D HH:mm:ss").locale("fa").format("YYYY/M/D") + `</td>
                        <td>` + element.platform + `</td>
                        <td style="width:155px;">` + element.browser + `</td>
                </tr>`
                    );
                });
            }

            $("#customerComments").empty();
            comments.forEach((element, index) => {
                $("#customerComments").append(
                    `<tr class="tbodyTr" onclick="selectTableRow(this)">
                <td> ` + (index + 1) + ` </td>
                <td>` + moment(element.TimeStamp, "YYYY/M/D HH:mm:ss").locale("fa").format("YYYY/M/D") + `</td>
                <td onclick="viewComment(` + element.id + `)"</td>` + element.newComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i> </td>
                <td onclick="viewNextComment(` + element.id + `)">` + element.nexComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i>  </td>
                <td style="width:101px !important;">` + moment(element.specifiedDate, "YYYY/M/D").locale("fa").format("YYYY/M/D") + `</td>
                </tr>`
                );
            });
            $("#customerAssesments").empty();
            assesments.forEach((element, index) => {
                let driverBehavior = "";
                let shipmentProblem = "بله";
                if (element.shipmentProblem == 1) {
                    shipmentProblem = "خیر";
                }
                switch (parseInt(element.driverBehavior)) {
                    case 1:
                        driverBehavior = "عالی";
                        break;
                    case 2:
                        driverBehavior = "خوب";
                        break;
                    case 3:
                        driverBehavior = "متوسط";
                        break;
                    case 4:
                        driverBehavior = "بد";
                        break;
                    default:
                        break;
                }
                $("#customerAssesments").append(
                    `
            <tr onclick="selectTableRow(this)">
            <td>` + (index + 1) + `</td>
            <td>` + moment(element.TimeStamp, "YYYY/M/D").locale("fa").format("YYYY/M/D") + `</td>
            <td>` + element.comment + `</td>
            <td>` + driverBehavior + `</td>
            <td>` + shipmentProblem + `</td>
            <td style="width:100px"></td>
        </tr>`
                );
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#customerDashboard").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#customerDashboard").modal("show");
        },
        error: function (data) { },
    });
});

function openAssesmentStuff() {
    if ($("#assesToday").is(":checked")) {
    }
    $("#assesType").val("TODAY");
    if ($("#assesPast").is(":checked")) {
        $("#assesType").val("PAST");
    }
    if ($("#assesDone").is(":checked")) {
        $("#openAssesmentStuffBtn").prop("disabled", true);
    }

    if (
        !$("#assesToday").is(":checked") &&
        !$("#assesPast").is(":checked") &&
        !$("#assesDone").is(":checked")
    ) {
        $("#assesType").val("TODAY");
    }

    $("#assesmentDashboard").modal("show");
    $.ajax({
        method: "get",
        url: baseUrl + "/getFactorDetail",
        data: {
            _token: "{{ csrf_token() }}",
            FactorSn: $("#factorSn").val(),
        },
        async: true,
        success: function (msg) {
            let factor = msg[0];
            $("#factorDate").text(factor.FactDate);
            $("#customerNameFactor").text(factor.Name);
            $("#customerComenter").text(factor.Name);
            $("#Admin1").text(factor.lastName);
            $("#customerAddressFactor").text(factor.peopeladdress);
            $("#customerPhoneFactor").text(factor.sabit);
            $("#factorSnFactor").text(factor.FactNo);
            $("#assesmentDashboard").modal("show");
            $("#productList").empty();
            msg.forEach((element, index) => {
                $("#productList").append(
                    `  <tr onclick="selectTableRow(this)">
            <td class="driveFactor">` +
                    (index + 1) +
                    `</td>
            <td>` +
                    element.GoodName +
                    ` </td>
            <td class="driveFactor">` +
                    element.Amount / 1 +
                    `</td>
            <td>` +
                    element.UName +
                    `</td>
            <td>` +
                    (element.Fi / 10).toLocaleString("en-us") +
                    `</td>
            <td style="width:111px;">` +
                    (element.goodPrice / 10).toLocaleString("en-us") +
                    `</td>
            </tr>`
                );
            });
        },
        error: function (data) { },
    });
}

function showDoneCommentDetail(element) {
    let input = $(element).find("input:radio").prop("checked", true);
    $("#customerSn").val(input.val().split("_")[0]);
    $("#factorSn").val(input.val().split("_")[1]);
    $("#customerIdForAssesment").val(input.val().split("_")[0]);
    $("#factorIdForAssesment").val(input.val().split("_")[1]);
    $("#openAssesmentModal").prop("disabled", false);
    $("#openDashboard").prop("disabled", false);
    $.ajax({
        method: "get",
        url: baseUrl + "/getDonCommentInfo",
        data: {
            _token: "{{@csrf}}",
            factorSn: $("#factorSn").val(),
        },
        async: true,
        success: function (response) {
            $("#doneCommentDate").text(response[0].TimeStamp);
            $("#doneCommentComment").text(response[0].assessComment);
            $("#doneCommentAlarm").text(response[0].alarmDate);
        },
        error: function (error) {
            alert("bad");
        },
    });
}
function getDonComment(history) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getDoneAsses",
        async: true,
        data: {
            _token: "{{@csrf}}",
            history: history,
        },
        success: function (response) {
            $("#customerListBodyDone").empty();
            response.forEach((element, index) => {
                $("#customerListBodyDone").append(
                    `
                <tr  onclick="showDoneCommentDetail(this); selectTableRow(this)">
                    <td>` +
                    (index + 1) +
                    `</td>
                    <td>` +
                    element.Name +
                    `</td>
                    <td>` +
                    element.PhoneStr +
                    `</td>
                    <td>` +
                    moment(element.TimeStamp, "YYYY/M/D HH:mm:ss")
                        .locale("fa")
                        .format("HH:mm:ss YYYY/M/D") +
                    `</td>
                    <td>` +
                    element.AdminName +
                    ` ` +
                    element.lastName +
                    `</td>
                    <td> <input class="customerList form-check-input" name="factorId" type="radio" value="` +
                    element.PSN +
                    `_` +
                    element.SerialNoHDS +
                    `"></td>
                </tr>`
                );
            });
        },
        error: function () {
            alert("error occored");
        },
    });
}

$("#inactiveButton").on("click", () => {
    $("#inactiveId").val($("#customerSn").val());

    if (!$(".modal.in").length) {
        $(".modal-dialog").css({
            top: 0,
            left: 0,
        });
    }
    $("#inactiveCustomer").modal({
        backdrop: false,
        show: true,
    });

    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });
    $("#inactiveCustomer").modal("show");
});

$("#addAssesment").submit(function (e) {
    $("#assesmentDashboard").modal("hide");
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#customersAssesBody").empty();
            data.forEach((element, index) => {
                $("#customersAssesBody").append(
                    `
            <tr onclick="assesmentStuff(this)">
            <td class="no-sort" style="width:40px">` +
                    (index + 1) +
                    `</td>
            <td>` +
                    element.Name +
                    `</td>
            <td>` +
                    (element.TotalPriceHDS / 10).toLocaleString("en") +
                    `</td>
            <td>` +
                    element.FactDate +
                    `</td>
            <td style="width:70px">` +
                    element.FactNo +
                    `</td>
            <td style="width:40px"> <input class="customerList form-check-input" name="factorId" type="radio" value="` +
                    element.PSN +
                    `_` +
                    element.SerialNoHDS +
                    `"></td>
        </tr>
            `
                );
            });
        },
    });
    e.preventDefault();
});

$("#addAssesmentPast").submit(function (e) {
    $("#assesmentDashboard").modal("hide");
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#customersAssesBody").empty();
            data.forEach((element, index) => {
                $("#customersAssesBody").append(
                    `
            <tr onclick="assesmentStuff(this); selectTableRow(this)">
            <td class="no-sort" style="width:40px">` +
                    (index + 1) +
                    `</td>
            <td>` +
                    element.Name +
                    `</td>
            <td>` +
                    (element.TotalPriceHDS / 10).toLocaleString("en") +
                    `</td>
            <td>` +
                    element.FactDate +
                    `</td>
            <td style="width:40px"> <input class="customerList form-check-input" name="factorId" type="radio" value="` +
                    element.PSN +
                    `_` +
                    element.SerialNoHDS +
                    `"></td>
        </tr>
            `
                );
            });
        },
    });
    e.preventDefault();
});

$("#openCommentTimeTable").on("click", () => {
    $("#addComment").modal("show");
});


$("#addCommentTimeTable").submit(function (e) {
    $("#addComment").modal("hide");
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            swal({
                title: "موفق!",
                text: "ثبت شد!",
                icon: "success",
                buttons: true,
            });
            $.ajax({
                method: "get",
                url: baseUrl + "/getCustomerForTimeTable",
                data: {
                    _token: "{{ csrf_token() }}",
                    dayDate: $("#dayDate").val(),
                },
                async: true,
                success: function (msg) {
                    if (msg.length > 0) {
                        $("#customerListSection").css({ display: "block" });
                    } else {
                        $("#customerListSection").css({ display: "none" });
                    }

                    // $('.crmDataTable').dataTable().fnDestroy();
                    $("#customerListBody").empty();
                    msg.forEach((element, index) => {

                   
                        $("#customerListBody").append(`
                            <tr  onclick="timeTableCustomerStuff(this); selectTableRow(this)">
                                <td>` + (index + 1) + `</td>
                                <td style="width:66px">` + element.PCode + `</td>
                                <td>` + element.Name + `</td>
                                <td>` + element.peopeladdress + `</td>
                                <td>` + element.sabit + `</td>
                                <td>` + element.hamrah + `</td>
                                <td>` + element.NameRec + `</td>
                                <td style="width:100px"> <input name="timeTableCustomer" class="form-check-input" type="radio" value="` + element.PSN + `_` + element.commentId + `"></td>
                            </tr>`
                        );
                    });
                    // $('.crmDataTable').dataTable();
                },
                error: function (data) { },
            });
        },
    });
    e.preventDefault();
    let csn = $("#customerSn").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/customerDashboard",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
        },
        async: true,
        success: function (msg) {
            let exactCustomer = msg[0];
            let factors = msg[1];
            let goodDetails = msg[2];
            let basketOrders = msg[3];
            $("#dashboardTitle").text(exactCustomer.Name);
            $("#customerCode").val(exactCustomer.PCode);
            $("#customerName").val(exactCustomer.Name);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#mobile1").val(exactCustomer.PhoneStr);
            $("#customerIdForComment").val(exactCustomer.PSN);
            $("#countFactor").val(exactCustomer.countFactor);
            $("#factorTable").empty();
            factors.forEach((element, index) => {
                $("#factorTable").append(
                    `
            <tr>
                <td>` +
                    (index + 1) +
                    `</td>
                <td>` +
                    element.FactDate +
                    `</td>
                <td>نامعلوم</td>
                <td>` +
                    parseInt(element.TotalPriceHDS / 10).toLocaleString(
                        "en-us"
                    ) +
                    `</td>
                <td onclick="showFactorDetails(this)"><span><input name="factorId" style="display:none"  type="radio" value="` +
                    element.SerialNoHDS +
                    `" /><i class="fa fa-eye" /></span></td>
            </tr>
            `
                );
            });
            $("#goodDetail").empty();
            goodDetails.forEach((element, index) => {
                $("#goodDetail").append(
                    `
            <tr>
            <td> ` +
                    (index + 1) +
                    ` </td>
            <td>` +
                    element.TimeStamp +
                    `</td>
            <td>` +
                    element.GoodName +
                    `</td>
            <td>` +
                    element.Amount +
                    `</td>
            <td>` +
                    element.Fi +
                    `</td>

            </tr >`
                );
            });
            $("#basketOrders").empty();
            basketOrders.forEach((element, index) => {
                $("#basketOrders").append(
                    `<tr>
                <td>` +
                    (index + 1) +
                    `</td>
                <td>` +
                    element.TimeStamp +
                    `</td>
                <td>` +
                    element.GoodName +
                    `</td>
                <td>` +
                    element.Amount +
                    `</td>
                <td>` +
                    element.Fi +
                    `</td>
                </tr>`
                );
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#customerDashboard").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#customerDashboard").modal("show");
        },
        error: function (data) { },
    });
});

function setAdminStuffForMove(element) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    let adminId = input.val();
    $("#adminID").val(adminId);
}

function refreshDashboard() {
    $("#addComment").modal("hide");
    let csn = $("#customerSn").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/customerDashboard",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
        },
        async: true,
        success: function (msg) {
            let exactCustomer = msg[0];
            let factors = msg[1];
            let goodDetails = msg[2];
            let basketOrders = msg[3];
            $("#dashboardTitle").text(exactCustomer.Name);
            $("#customerCode").val(exactCustomer.PCode);
            $("#customerName").val(exactCustomer.Name);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#customerAddress").val(exactCustomer.peopeladdress);
            $("#mobile1").val(exactCustomer.PhoneStr);
            $("#customerIdForComment").val(exactCustomer.PSN);
            $("#countFactor").val(exactCustomer.countFactor);
            $("#factorTable").empty();
            factors.forEach((element, index) => {
                $("#factorTable").append(
                    `
            <tr>
                <td>` +
                    (index + 1) +
                    `</td>
                <td>` +
                    element.FactDate +
                    `</td>
                <td>نامعلوم</td>
                <td>` +
                    parseInt(element.TotalPriceHDS / 10).toLocaleString(
                        "en-us"
                    ) +
                    `</td>
                <td onclick="showFactorDetails(this)"><span><input name="factorId" style="display:none"  type="radio" value="` +
                    element.SerialNoHDS +
                    `" /><i class="fa fa-eye" /></span></td>
            </tr>
            `
                );
            });
            $("#goodDetail").empty();
            goodDetails.forEach((element, index) => {
                $("#goodDetail").append(
                    `
            <tr>
                <td>` + (index + 1) + ` </td>
                <td>` +  element.TimeStamp + `</td>
                <td>` + element.GoodName +  `</td>
                <td>` + element.Amount + `</td>
                <td>` +  element.Fi + `</td>
            </tr >`
                );
            });
            $("#basketOrders").empty();
            basketOrders.forEach((element, index) => {
                $("#basketOrders").append(
                    `<tr>
                <td>` +
                    (index + 1) +
                    `</td>
                <td>` +
                    element.TimeStamp +
                    `</td>
                <td>` +
                    element.GoodName +
                    `</td>
                <td>` +
                    element.Amount +
                    `</td>
                <td>` +
                    element.Fi +
                    `</td>
                </tr>`
                );
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#customerDashboard").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#customerDashboard").modal("show");
        },
        error: function (data) { },
    });
}

function viewComment(id) {
    let comment;
    $.ajax({
        method: "get",
        url: baseUrl + "/getFirstComment",
        data: {
            _token: "{{ csrf_token() }}",
            commentId: id,
        },
        async: true,
        success: function (msg) {
            comment = msg.newComment;
            $("#readCustomerComment1").text(comment);
            $("#viewComment").modal("show");
        },
        error: function (data) { },
    });
}

function viewNextComment(id) {
    let comment;
    $.ajax({
        method: "get",
        url: baseUrl + "/getFirstComment",
        data: {
            _token: "{{ csrf_token() }}",
            commentId: id,
        },
        async: true,
        success: function (msg) {
            comment = msg.nexComment;
            $("#readCustomerComment1").text(comment);

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#viewComment").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#viewComment").modal("show");
        },
        error: function (data) { },
    });
}

$("#viewComment").blur(function () {
    $("#viewComment").modal("hide");
    $("#readCustomerComment1").empty();
});

function showTimeTableTasks(element, adminId) {
    let input = $(element).find("input:radio");
    $("#dayDate").val(input.val());
    $("#openDashboard").prop("disabled", false);
    $("#returnCustomer").prop("disabled", false);
    $.ajax({
        method: "get",
        url: baseUrl + "/getCustomerForTimeTable",
        data: {
            _token: "{{ csrf_token() }}",
            dayDate: input.val(),
            asn: adminId
        },
        async: true,
        success: function (msg) {
            console.log(msg)
            $("#testText").text("مشتریان" + " " + msg[0].adminName+' '+new Date(input.val()).toLocaleDateString("fa-ir"))

            $("#customerListBody").empty();
            msg.forEach((element, index) => {
                $("#customerListBody").append(
                    `
            <tr  onclick="timeTableCustomerStuff(this); selectTableRow(this);  getCustomerInformationForModal(`+element.PSN+`)">
                <td>` + (index + 1) + `</td>
                <td style="width:66px">` + element.PCode + `</td>
                <td>` + element.Name + `</td>
                <td>` + element.peopeladdress + `</td>
                <td>` + element.PhoneStr + `</td>
                <td>` + element.NameRec + `</td>
                <td style="width:100px;"> <input name="timeTableCustomer" class="form-check-input" type="radio" value="` + element.PSN + `_` + element.commentId + `"></td>
            </tr>`
                );
            });
            // $('.crmDataTable').dataTable();
            $("#customreForCallModal").modal("show");
        },
        error: function (data) { },
    });
}

function timeTableCustomerStuff(element) {
    let input = $(element).find("input:radio").prop("checked", true);
    $(".customerSnLogin").val(input.val().split("_")[0]);
    $("#commentSn").val(input.val().split("_")[1]);
    $("#customerIdForComment").val(input.val().split("_")[0]);
    $(".enableBtn").prop("disabled", false);

}

function showAssesComment(id) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAssesComment",
        data: {
            _token: "{{ csrf_token() }}",
            assesId: id,
        },
        async: true,
        success: function (msg) {
            $("#assesComment").text(msg.comment);
            $("#readAssesComment").modal("show");
        },
        error: function (data) {

        },
    });
}

function returnedCustomerStuff(element) {
    let input = $(element).find("input:radio").prop("checked", true);
    let customerId=input.val().split("_")[0];
    $("#customerSn").val(customerId);
    $("#adminSn").val(input.val().split("_")[1]);
    $("#customerSnLogin").val(customerId);
    $(".enableBtn").prop("disabled", false);
    $(".enableBtn").val(customerId);

    var checked = []
    $("input[name='options[]']:checked").each(function ()
    {
        checked.push(parseInt($(this).val()));
    });

    $(".enableBtn").prop("disabled", false);
    $("#takhsisButton").val(checked);
}



$("#returnCustomer").on("click", () => {
    let csn = $("#customerSn").val();
    $("#returnCustomerId").val(csn);
    $("#returnComment").modal("show");
});
$("#cancelSetAlarm").on("click", () => {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
    }).then(function (value) {
        if (value === true) {
            $("#changeAlarm").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#changeAlarm").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#changeAlarm").modal("show");
        }
    });
});

$("#cancelEditCustomer").on("click", () => {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
    }).then(function (value) {
        if (value === true) {
            $("#editNewCustomer").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#editNewCustomer").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#editNewCustomer").modal("show");
        }
    });
});

$("#cancelinActive").on("click", () => {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
    }).then(function (value) {
        if (value === true) {
            $("#inactiveCustomer").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#inactiveCustomer").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#inactiveCustomer").modal("show");
        }
    });
});

$("#cancelTakhsis").on("click", () => {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید بدون ثبت تخصیص خارج شوید؟",
        icon: "warning",
        buttons: true,
    }).then(function (value) {
        if (value === true) {
            $("#takhsesKarbar").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#takhsesKarbar").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#takhsesKarbar").modal("show");
        }
    });
});

$("#cancelCommentButton").on("click", () => {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
    }).then(function (value) {
        if (value === true) {
            $("#addComment").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#addComment").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#addComment").modal("show");
        }
    });
});

$("#cancelReturn").on("click", () => {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
    }).then(function (value) {
        if (value === true) {
            $("#returnComment").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#returnComment").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#returnComment").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#returnComment").modal("show");
        }
    });
});

$("#cancelInActive").on("click", () => {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
    }).then(function (value) {
        if (value === true) {
            $("#inactiveCustomer").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#inactiveCustomer").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#inactiveCustomer").modal("show");
        }
    });
});

$("#cancelAssesment").on("click", () => {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
    }).then(function (value) {
        if (value === true) {
            $("#assesmentDashboard").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#assesmentDashboard").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#assesmentDashboard").modal("show");
        }
    });
});

$("#returnCustomerForm").submit(function (e) {
    $("#returnComment").modal("hide");
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            swal({
                title: "موفق!",
                text: "ثبت شد!",
                icon: "success",
                buttons: true,
            });
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            data.forEach((element, index) => {
                let backgroundColor = "";
                if (element.countComment > 0) {
                    backgroundColor = "lightblue";
                }
                $("#customerListBody1").append(
                    `
            <tr onclick="selectAndHighlight(this); selectTableRow(this)" style="background-color:` + backgroundColor + `">
                    <td>` + (index + 1) + `</td>
                    <td style="width:66px">` + element.PCode + `</td>
                    <td>` + element.Name + `</td>
                    <td>` + element.peopeladdress + `</td>
                    <td>` + element.sabit + `</td>
                    <td>` + element.hamrah + `</td>
                    <td>` + element.NameRec + `</td>
                    <td>2</td>
                    <td style="width:100px;"> <input class="customerList form-check-input" name="customerId" type="radio" value="` + element.PSN + `_` + element.GroupCode + `"></td>
            </tr>`
                );
            });
            // $('.crmDataTable').dataTable();
        },
    });
    e.preventDefault();
});

$("#openDashboardAlarm").on("click", () => {
    if (!$(".modal.in").length) {
        $(".modal-dialog").css({
            top: 0,
            left: 0,
        });
    }
    $("#karbarAlarm").modal({
        backdrop: false,
        show: true,
    });

    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });

    if (!$(".modal.in").length) {
        $(".modal-dialog").css({
            top: 0,
            left: 0,
        });
    }
    $("#karbarAlarm").modal({
        backdrop: false,
        show: true,
    });

    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });

    $("#karbarAlarm").modal("show");
});

function takhsisCustomer() {
    $("#takhsesKarbar").modal("hide");
    let csn = $("#customerSn").val();
    let FirstAdminID = $("#adminSn").val();
    let asn = $("input[name='AdminId']:checked").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/takhsisCustomer",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
            asn: asn,
            FirstAdminID: FirstAdminID,
        },
        async: true,
        success: function (msg) {
            if($("#allCustomerReportRadio").is(":checked")){
                getAllCustomerInfos();
            }
            if($("#customerLoginReportRadio").is(":checked")){
                $("#filterAllLoginsBtn").click();
            }
            if($("#customerInactiveRadio").is(":checked")){
                $("#filterInActivesBtn").click();
            }
            if($("#referentialCustomerRadio").is(":checked")){
                $("#filterReturnedsBtn").click();

            }
            if($("#evacuatedCustomerRadio").is(":checked")){
                $("#filterNoAdminsBtn").click();
            }
            if($("#newCustomerRadio").is(":checked")){
                $("#filterNewCustomerBtn").click();
            }
        
        },
        error: function (data) { },
    });
}
function openEditCustomerModalForm(csn) {
    let customerId = csn;
    $.ajax({
        method: "get",
        url: baseUrl + "/getCustomerInfo",
        data: {
            _token: "{{ csrf_token() }}",
            csn: customerId,
        },
        async: true,
        success: function (respond) {
            let exactCustomerInfo = respond[0];
            let phones = respond[1];
            let cities = respond[2];
            let mantagheh = respond[3];

            $("#customerID").val(exactCustomerInfo.PSN);
            $("#name").val(exactCustomerInfo.Name);
            $("#PCode").val(exactCustomerInfo.PCode);
            $("#mobilePhone").val(phones[0].hamrah);
            $("#sabitPhone").val(phones[0].sabit);
            $("#gender").empty();
            $("#gender").append(`
            <option value="2" >مرد</option>
            <option value="1" >زن</option>`);
            $("#snNahiyehE").empty();
            cities.forEach((element, index) => {
                let selectRec = "";
                if (element.SnMNM == exactCustomerInfo.SnNahiyeh) {
                    selectRec = "selected";
                }
                $("#snNahiyehE").append(
                    `<option value="` +
                    element.SnMNM +
                    `" ` +
                    selectRec +
                    `>` +
                    element.NameRec +
                    `</option>`
                );
            });

            $("#snMantaghehE").empty();
            mantagheh.forEach((element, index) => {
                let selectRec = "";
                if (element.SnMNM == exactCustomerInfo.SnMantagheh) {
                    selectRec = "selected";
                }
                $("#snMantaghehE").append(
                    `<option value="` +
                    element.SnMNM +
                    `" ` +
                    selectRec +
                    `>` +
                    element.NameRec +
                    `</option>`
                );
            });
            $("#peopeladdress").val(exactCustomerInfo.peopeladdress);
            $("#password").val(exactCustomerInfo.customerPss);

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#editNewCustomer").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#editNewCustomer").modal("show");
        },
        error: function (data) { },
    });
}
function takhsisNewCustomer() {
    $("#takhsesKarbar").modal("hide");
    let csn = $("#customerSn").val();
    let asn = $("input[name='AdminId']:checked").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/takhsisNewCustomer",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
            asn: asn,
        },
        async: true,
        success: function (msg) {
            swal("موفقانه اختصاص داده شد.", {
                icon: "success",
            });
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            msg.forEach((element, index) => {
                $("#customerListBody1").append(
                    `
            <tr onclick="selectTableRow(this)">
            <td style="width:40px">` +
                    index +
                    1 +
                    `</td>
            <td>` +
                    element.Name +
                    `</td>
            <td>` +
                    element.hamrah +
                    `</td>
            <td>` +
                    element.sabit +
                    `</td>
            <td>` +
                    element.NameRec +
                    `</td>
            <td>` +
                    moment(element.TimeStamp, "YYYY-M-D HH:mm:ss")
                        .locale("fa")
                        .format("HH:mm:ss YYYY/M/D") +
                    `</td>
            <td>` +
                    element.peopeladdress +
                    `</td>
            <td>` +
                    element.adminName +
                    ` ` +
                    element.adminLastName +
                    `</td>
            <td style="width:40px"> <input class="customerList form-check-input" name="customerId" type="radio" value="` +
                    element.PSN +
                    ` ` +
                    element.GroupCode +
                    `"></td>
        </tr>`
                );
            });
            // $('.crmDataTable').dataTable();
        },
        error: function (data) { },
    });
}
// function takhsisNewCustomer() {
//     $("#takhsesKarbar").modal("hide");
//     let csn = $("#customerSn").val();
//     let FirstAdminID = $("#adminSn").val();
//     let asn = $("input[name='AdminId']:checked").val();
//     $.ajax({
//         method: 'get',
//         url: baseUrl + "/takhsisCustomerFromEmpty",
//         data: {
//             _token: "{{ csrf_token() }}",
//             csn: csn,
//             asn: asn,
//             FirstAdminID: FirstAdminID
//         },
//         async: true,
//         success: function(msg) {
//             // $('.crmDataTable').dataTable().fnDestroy();
//             $("#returnedCustomerList").empty();
//             msg.forEach((element, index) => {
//                 $("#returnedCustomerList").append(`
//             <tr onclick="returnedCustomerStuff(this)">
//             <td>` + (index + 1) + `</td>
//             <td>` + element.Name + `</td>
//             <td>` + element.PCode + `</td>
//             <td>` + element.peopeladdress + `</td>
//             <td>` + element.PhoneStr + `</td>
//             <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="` + element.PSN + `_` + element.adminId + `"></td>
//         </tr>`);
//             });
//             // $('.crmDataTable').dataTable();

//         },
//         error: function(data) {}
//     });
// }

function activateCustomer() {
    let csn = $("#customerSn").val();
    let asn = $("input[name='AdminId']:checked").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/activateCustomer",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
            asn: asn,
        },
        async: true,
        success: function (msg) {
            $("#inactiveCustomerBody").empty();
            msg.forEach((element, index) => {
                $("#inactiveCustomerBody").append(
                    `
                <tr onclick="setInActiveCustomerStuff(this); selectTableRow(this); getCustomerInformation(`+element.PSN+`)">
                    <td>` + (index + 1) +`</td>
                    <td>` +element.PCode +`</td>
                    <td>` + element.Name +`</td>
                    <td>` +  element.peopeladdress +`</td>
                    <td>` + element.PhoneStr +`</td>
                    <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="` + element.PSN +`"></td>
                </tr>`
                );
            });
            $("#takhsesKarbar").modal("hide");
        },
        error: function (data) { },
    });
}

$("#takhsisButton").on("click", () => {
    $("#inactiveId").val($("#customerSn").val());
   let takhsisBtnVal = $("#takhsisButton").val();

    if (!$(".modal.in").length) {
        $(".modal-dialog").css({
            top: 0,
            left: 0,
        });
    }
    $("#takhsesKarbar").modal({
        backdrop: false,
        show: true,
    });

    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });

    $("#takhsesKarbar").modal("show");
});

function setInActiveCustomerStuff(element) {
    let input = $(element).find("input:radio").prop("checked", true);
    $("#customerSn").val($(input).val());
    $(".enableBtn").val($(input).val());
    $(".enableBtn").prop("disabled", false);
    $("#customerSnLogin").val($(input).val());
}

$("#inactiveCustomerForm").submit(function (e) {
    swal({
        title: "مطمئین هستید؟",
        text: "پس از غیر فعالسازی این مشتری به لیست غیر فعالها اضافه می شود. !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $("#inactiveCustomer").modal("hide");
            $.ajax({
                url: $(this).attr("action"),
                data: $(this).serialize(),
                success: function (msg) {
                    $("#changeAlarm").modal("hide");
                    $("#alarmsbody").empty();
                    $("#filterNoAdminsBtn").click();
                    swal("مشتری غیر فعال شد", {
                        icon: "success",
                    });
                },
            });
        }
    });
    e.preventDefault();
});

function removeStaff(adminId) {
    swal({
        title: "مطمئین هستید؟",
        text: "پس از تخلیه نمی توانید این مشتریان را برگردانید !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $("#transferLoader").show();
            $.ajax({
                method: "get",
                url: baseUrl + "/emptyAdmin",
                data: {
                    _token: "{{ csrf_token() }}",
                    asn: adminId,
                },
                async: true,
                success: function (msg) {
                    if (msg == 1) {
                        $("#transferLoader").hide();
                        swal("مشتریان تخلیه گردید", {
                            icon: "success",
                        });
                        $("#addedCustomer").empty();
                    } else {
                    }
                },
                error: function (data) { },
            });
        }
    });
}


function moveStaff() {
    swal({
        title: "مطمئین هستید؟",
        text: "پس از انتقال نمی توانید این مشتریان را برگردانید !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $("#moveKarbar").modal("hide");
            $("#transferLoader").show();
            $.ajax({
                method: "get",
                url: baseUrl + "/moveCustomerToAdmin",
                data: {
                    _token: "{{ csrf_token() }}",
                    holderID: $("#adminID").val(),
                    giverID: $("#adminTakerId").val(),
                },
                async: true,
                success: function (msg) {
                    if (msg == 1) {
                        $("#transferLoader").hide();
                        swal("مشتریان انتقال گردید", {
                            icon: "success",
                        });
                    } else {
                    }
                },
                error: function (data) { },
            });
        }
    });
}

$("#moveKarbarButton").on("click", () => {
    let asn = $("#moveKarbarButton").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/getAdminForMove",
        data: {
            _token: "{{ csrf_token() }}",
            asn: asn,
        },
        async: true,
        success: function (msg) {
            let adminArray = msg[0];
            let admin = adminArray[0];
            let otherAdmins = msg[1];
            let adminType = "";
            let discription = "توضیحی ندارد.";
            if (admin.discription != null) {
                discription = admin.discription;
            }
            
            if (admin.adminType == 1) {
                adminType = "ادمین";
            } else {
                if (admin.adminType == 2) {
                    adminType = "پشتیبان";
                } else {
                    if (admin.adminType == 3) {
                        adminType = "بازاریاب";
                    } else {
                        if (admin.adminType == 4) {
                            adminType = "راننده";
                        }
                    }
                }
            }
            if (
                admin.adminType != 1 &&
                admin.adminType != 4 &&
                admin.emptyState != 1
            ) {
                $("#adminToMove").empty();
                $("#adminToMove").append(
                    `<tr>
                  <td> 1 </td>
                <td style="font-size:18px; font-weight:bold">` +
                    admin.name +
                    ` ` +
                    admin.lastName +
                    `</td>
                <td style="font-size:18px; font-weight:bold">` +
                    adminType +
                    `</td>
                <td>` +
                    discription +
                    `</td>
                        <td>  </td>
                </tr>`
                );

                if (!$(".modal.in").length) {
                    $(".modal-dialog").css({
                        top: 0,
                        left: 0,
                    });
                }
                $("#moveKarbar").modal({
                    backdrop: false,
                    show: true,
                });

                $(".modal-dialog").draggable({
                    handle: ".modal-header",
                });

                $("#moveKarbar").modal("show");
            }
            $("#selectKarbarToMove").empty();
            otherAdmins.forEach((element, index) => {
                adminType = "پشتیبان";
                discription = "توضیحی ندارد";
                if (element.discription != null) {
                    discription = element.discription;
                }
                switch (element.adminType) {
                    case 2:
                        adminType = "پشتیبان";
                        break;
                    case 3:
                        adminType = "بازاریاب";
                        break;
                }
                $("#selectKarbarToMove").append(
                    `
            <tr onclick="setAdminStuffForMove(this); selectTableRow(this)">
            <td>` +
                    (index + 1) +
                    `</td>
            <td>` +
                    element.name +
                    " " +
                    element.lastName +
                    `</td>
            <td>` +
                    adminType +
                    `</td>
            <td>` +
                    discription +
                    `</td>
            <td>
                <input class="form-check-input" name="adminId" type="radio" value="` +
                    element.id +
                    `">
            </td>
        </tr>`
                );
            });
        },
        error: function (data) { },
    });
});

$("#cancelAddAddmin").on("click", () => {
    swal({
        title: "اخطار!",
        text: "می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $("#newAdmin").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#newAdmin").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#newAdmin").modal("show");
        }
    });
});

$("#cancelEditProfile").on("click", () => {
    swal({
        title: "اخطار!",
        text: "می خواهید بدون ویرایش خارج شوید؟",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $("#editProfile").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#editProfile").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#editProfile").modal("show");
        }
    });
});



$("#cancelRemoveKarbar").on("click", () => {
    swal({
        title: "اخطار!",
        text: "می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $("#removeKarbar").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#removeKarbar").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#removeKarbar").modal("show");
        }
    });
});

$("#cancelMoveKarbar").on("click", () => {
    swal({
        title: "اخطار!",
        text: "می خواهید بدون ذخیره خارج شوید؟",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $("#moveKarbar").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#moveKarbar").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#moveKarbar").modal("show");
        }
    });
});

function setKarbarEditStuff() {
    let asn = $("#editAdmin").val();

    let admyTypes;
    let sexes;
    $("#editAdminID").val(asn);
    $.ajax({
        method: "get",
        url: baseUrl + "/getAdminForMove",
        data: {
            _token: "{{ csrf_token() }}",
            asn: asn,
        },
        async: true,
        success: function (msg) {
            let adminArray = msg[0];
            let admin = adminArray[0];
            let otherAdmins = msg[1];
            let adminType = "";
            let discription = "توضیحی ندارد.";
            let bossAdmins = msg[2];
            $("#editProfileModalTitle").text("("+admin.name+' '+admin.lastName+")");
            $("#adminId").val(admin.id);
            if (
                admin.adminType == 5 ||
                admin.adminType == 4 ||
                admin.adminType == 1
            ) {
                $("#assignBossDiv").css({ display: "none" });
            } else {
                $("#assignBossDiv").css({ display: "block" });
            }

            if (admin.adminType == 2 || admin.adminType == 4) {
                $("#poshtibanDiv").css({ display: "block" });
            } else {
                $("#poshtibanDiv").css({ display: "none" });
            }

            $("#poshtibanTypeEdit").empty();
            if (admin.poshtibanType == 1) {
                $("#poshtibanTypeEdit").append(`
            <option value="4">راننده</option>
            <option value="2">پشتیبان حضوری</option>
            <option value="2">پشتیبان هماهنگی</option>
            <option value="2">پشتیبان تلفنی</option>
            <option value="3"> راننده </option>
            <option value="3">بازاریاب حضوری</option>
            <option value="3">بازاریاب هماهنگی</option>
            <option value="3">بازاریاب تلفنی</option>`);
            }

            if (admin.poshtibanType == 2) {
                $("#poshtibanTypeEdit").append(`
                <option value="4">راننده</option>
                <option value="2">پشتیبان حضوری</option>
                <option selected value="2">پشتیبان هماهنگی</option>
                <option value="2">پشتیبان تلفنی</option>
                <option value="3"> راننده </option>
                <option value="3">بازاریاب حضوری</option>
                <option value="3">بازاریاب هماهنگی</option>
                <option value="3">بازاریاب تلفنی</option>`);
            }

            if (admin.poshtibanType == 3) {
                $("#poshtibanTypeEdit").append(`
                <option value="4">راننده</option>
                <option value="2">پشتیبان حضوری</option>
                <option value="2">پشتیبان هماهنگی</option>
                <option value="2">پشتیبان تلفنی</option>
                <option value="3"> راننده </option>
                <option selected value="3">بازاریاب حضوری</option>
                <option value="3">بازاریاب هماهنگی</option>
                <option value="3">بازاریاب تلفنی</option>`);
            }
            if (admin.poshtibanType == 0) {
                $("#poshtibanTypeEdit").append(`
                <option selected value="0">--</option>
                <option value="4">راننده</option>
                <option value="2">پشتیبان حضوری</option>
                <option value="2">پشتیبان هماهنگی</option>
                <option value="2">پشتیبان تلفنی</option>
                <option value="3"> راننده </option>
                <option value="3">بازاریاب حضوری</option>
                <option value="3">بازاریاب هماهنگی</option>
                <option value="3">بازاریاب تلفنی</option>`);
            }
            if (admin.poshtibanType == 4) {
                $("#poshtibanTypeEdit").append(`
                <option selected value="4">راننده</option>
                <option value="2">پشتیبان حضوری</option>
                <option value="2">پشتیبان هماهنگی</option>
                <option value="2">پشتیبان تلفنی</option>
                <option  value="3">بازاریاب حضوری</option>
                <option value="3">بازاریاب هماهنگی</option>
                <option value="3">بازاریاب تلفنی</option>`);
            }

            if (admin.employeeType == 1) {
                $("#managerEdit").prop("selected", true);
                $("#saleLineWork" + admin.SaleLineId).prop("selected", true);
                $("#saleLineDivEdit").show();
                $("#headDivEdit").hide();
                $("#managerDivEdit").hide();
                $("#employeeJobDivEdit").hide();
            }

            if (admin.employeeType == 2) {
                $("#headEdit").prop("selected", true);
                $("#manageWork" + admin.bossId).prop("selected", true);
                $("#managerDivEdit").show();
                $("#saleLineDivEdit").hide();
                $("#headDivEdit").hide();
                $("#employeeJobDivEdit").hide();
            }

            if (admin.employeeType == 3) {
                $("#jobEdit" + admin.poshtibanType).prop("selected", true);
                $("#headWork" + admin.bossId).prop("selected", true);
                $("#employeeEdit").prop("selected", true);
                $("#headDivEdit").show();
                $("#employeeJobDivEdit").show();
                $("#saleLineDivEdit").hide();
                $("#managerDivEdit").hide();
            }

            $("#bosses").empty();
            let hasBoss = false;
            bossAdmins.forEach((element, index) => {
                if (admin.bossId != element.id) {
                    $("#bosses").append(
                        `<option value="` +
                        element.id +
                        `">` +
                        element.name +
                        ` ` +
                        element.lastName +
                        `</option>`
                    );
                } else {
                    if (admin.bossId == element.id) {
                        hasBoss = true;
                        $("#bosses").append(
                            `<option selected value="` +
                            element.id +
                            `">` +
                            element.name +
                            ` ` +
                            element.lastName +
                            `</option>`
                        );
                    }
                }
            });
            if (!hasBoss) {
                $("#bosses").append(
                    `<option selected value="0">هیچکیس</option>`
                );
            } else {
                $("#bosses").append(`<option value="0">هیچکیس</option>`);
            }
            if (admin.discription != null) {
                discription = admin.discription;
            }
            if (admin.adminType == 1) {
                admyTypes = [
                    `<option selected value="1">ادمین</option>`,
                    `<option value="2">پشتیبان</option>`,
                    `<option value="3">بازاریاب</option>`,
                    `<option value="4">راننده</option>`,
                ];
            } else {
                if (admin.adminType == 2) {
                    admyTypes = [
                        `<option  value="1">ادمین</option>`,
                        `<option selected value="2">پشتیبان</option>`,
                        `<option value="3">بازاریاب</option>`,
                        `<option value="4">راننده</option>`,
                    ];
                } else {
                    if (admin.adminType == 3) {
                        admyTypes = [
                            `<option  value="1">ادمین</option>`,
                            `<option  value="2">پشتیبان</option>`,
                            `<option  selected value="3">بازاریاب</option>`,
                            `<option value="4">راننده</option>`,
                        ];
                    } else {
                        if (admin.adminType == 4) {
                            admyTypes = [
                                `<option  value="1">ادمین</option>`,
                                `<option  value="2">پشتیبان</option>`,
                                `<option  value="3">بازاریاب</option>`,
                                `<option selected value="4">راننده</option>`,
                            ];
                        } else {
                            admyTypes = [
                                `<option  value="1">ادمین</option>`,
                                `<option  value="2">پشتیبان</option>`,
                                `<option  value="3">بازاریاب</option>`,
                                `<option value="4">راننده</option>`,
                                `<option selected value="5">سوپر ادمین</option>`,
                            ];
                        }
                    }
                }
            }

            if (admin.sex == 1) {
                sexes = [
                    `<option selected value="1">مرد</option>`,
                    `<option value="2">زن</option>`,
                ];
            } else {
                if (admin.sex == 2) {
                    sexes = [
                        `<option value="1">مرد</option>`,
                        `<option selected value="2">زن</option>`,
                    ];
                } else {
                    sexes = [
                        `<option value="1">مرد</option>`,
                        `<option value="2">زن</option>`,
                    ];
                }
            }
            $("#bosses").append();
            $("#adminName").val(admin.name.trim());
            $("#adminLastName").val(admin.lastName.trim());
            $("#adminUserName").val(admin.username.trim());
            $("#adminPassword").val(admin.password.trim());
            $("#adminPhone").val(parseInt(admin.phone.trim()));
            $("#adminDiscription").text(admin.discription.trim());
            $("#adminAddress").val(admin.address.trim());
            $("#adminSex").empty();
            sexes.forEach((element) => {
                $("#adminSex").append(element);
            });
            $("#editAdminType").empty();
            admyTypes.forEach((element) => {
                $("#editAdminType").append(element);
            });

            $("#editAdminID").val(admin.id);

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#editProfile").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            // قسمت base Info

            switch (parseInt(admin.rdSentN)) {
                case 1:
                    $("#editSentRdED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeSentRdED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeSentRdED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteSentRdED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.rdNotSentED)) {
                case 1:
                    $("#editRdNotSentED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeRdNotSentED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeRdNotSentED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteRdNotSentED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.baseInfoProfileN)) {
                case 1:
                    $("#editProfileED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeProfileED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeProfileED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteProfileED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.rdNotSentN)) {
                case 1:
                    $("#editRdNotSentED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeRdNotSentED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeRdNotSentED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteRdNotSentED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.specialSettingN)) {
                case 1:
                    $("#editSaleLineED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeSaleLineED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeSaleLineED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteSaleLineED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.InfoSettingAccessN)) {
                case 1:
                    $("#editSettingAccessED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeSettingAccessED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeSettingAccessED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteSettingAccessED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.InfoSettingTargetN)) {
                case 1:
                    $("#editSettingTargetED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeSettingTargetED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeSettingTargetED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteSettingTargetED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.InfoSettingTargetN)) {
                case 1:
                    $("#editSettingTargetED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeSettingTargetED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeSettingTargetED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteSettingTargetED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.declareElementOppN)) {
                case 1:
                    $("#editdeclareElementED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seedeclareElementED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seedeclareElementED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletedeclareElementED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.oppManagerN)) {
                case 1:
                    $("#editManagerOppED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeManagerOppED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeManagerOppED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteManagerOppED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.oppHeadN)) {
                case 1:
                    $("#editHeadOppED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeHeadOppED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeHeadOppED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteHeadOppED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.oppBazaryabN)) {
                case 1:
                    $("#editBazaryabOppED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeBazaryabOppED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeBazaryabOppED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteBazaryabOppED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.oppDriverServiceN)) {
                case 1:
                    $("#editoppDriverServiceED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeoppDriverServiceED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeoppDriverServiceED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteoppDriverServiceED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.oppBargiriN)) {
                case 1:
                    $("#editoppBargiriED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeoppBargiriED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeoppBargiriED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteoppBargiriED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.todayoppNazarsanjiN)) {
                case 1:
                    $("#edittodayoppNazarsanjiED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seetodayoppNazarsanjiED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seetodayoppNazarsanjiED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletetodayoppNazarsanjiED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.pastoppNazarsanjiN)) {
                case 1:
                    $("#editpastoppNazarsanjiED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seepastoppNazarsanjiED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seepastoppNazarsanjiED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletepastoppNazarsanjiED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.DoneoppNazarsanjiN)) {
                case 1:
                    $("#editDoneoppNazarsanjiED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeDoneoppNazarsanjiED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeDoneoppNazarsanjiED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteDoneoppNazarsanjiED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.AddOppupDownBonusN)) {
                case 1:
                    $("#editAddOppupDownBonusED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeAddOppupDownBonusED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeAddOppupDownBonusED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteAddOppupDownBonusED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.SubOppupDownBonusN)) {
                case 1:
                    $("#editSubOppupDownBonusED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeSubOppupDownBonusED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeSubOppupDownBonusED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteSubOppupDownBonusED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.AddedoppRDN)) {
                case 1:
                    $("#editAddedoppRDED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeAddedoppRDED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeAddedoppRDED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteAddedoppRDED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.oppjustCalendarN)) {
                case 1:
                    $("#editoppjustCalendarED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeoppjustCalendarED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeoppjustCalendarED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteoppjustCalendarED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.oppCustCalendarN)) {
                case 1:
                    $("#editoppCustCalendarED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeoppCustCalendarED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeoppCustCalendarED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteoppCustCalendarED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.allalarmoppN)) {
                case 1:
                    $("#editallalarmoppED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeallalarmoppED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeallalarmoppED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteallalarmoppED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.donealarmoppN)) {
                case 1:
                    $("#editdonealarmoppED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seedonealarmoppED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seedonealarmoppED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletedonealarmoppED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.NoalarmoppN)) {
                case 1:
                    $("#editNoalarmoppED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeNoalarmoppED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeNoalarmoppED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteNoalarmoppED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.massageOppN)) {
                case 1:
                    $("#editmassageOppED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seemassageOppED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seemassageOppED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletemassageOppED").prop("checked", true).change();
                    break;
            }
            switch (parseInt(admin.justBargiriOppN)) {
                case 1:
                    $("#editjustBargiriOppED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seejustBargiriOppED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seejustBargiriOppED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletejustBargiriOppED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.NotAddedoppRDN)) {
                case 1:
                    $("#editNotAddedoppRDED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeNotAddedoppRDED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeNotAddedoppRDED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteNotAddedoppRDED").prop("checked", true).change();
                    break;
            }
            //گزارشات

           
            switch (parseInt(admin.managerreportN)) {
                case 1:
                    $("#editmanagerreportED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seemanagerreportED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seemanagerreportED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletemanagerreportED").prop("checked", true).change();
                    break;
            }


            switch (parseInt(admin.HeadreportN)) {
                case 1:
                    $("#editHeadreportED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeHeadreportED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeHeadreportED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteHeadreportED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.poshtibanreportN)) {
                case 1:
                    $("#editposhtibanreportED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeposhtibanreportED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeposhtibanreportED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteposhtibanreportED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.bazaryabreportN)) {
                case 1:
                    $("#editbazaryabreportED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seebazaryabreportED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seebazaryabreportED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletebazaryabreportED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.reportDriverN)) {
                case 1:
                    $("#editreportDriverED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seereportDriverED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seereportDriverED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletereportDriverED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.trazEmployeeReportN)) {
                case 1:
                    $("#edittrazEmployeeReportED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seetrazEmployeeReportED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seetrazEmployeeReportED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletetrazEmployeeReportED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.loginCustRepN)) {
                case 1:
                    $("#editloginCustRepED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeloginCustRepED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeloginCustRepED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteloginCustRepED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.inActiveCustRepN)) {
                case 1:
                    $("#editinActiveCustRepED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeinActiveCustRepED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeinActiveCustRepED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteinActiveCustRepED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.noAdminCustRepN)) {
                case 1:
                    $("#editnoAdminCustRepED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seenoAdminCustRepED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seenoAdminCustRepED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletenoAdminCustRepED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.salegoodsReportN)) {
                case 1:
                    $("#editsalegoodsReportED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seesalegoodsReportED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seesalegoodsReportED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletesalegoodsReportED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.returnedgoodsReportN)) {
                case 1:
                    $("#editreturnedgoodsReportED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seereturnedgoodsReportED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seereturnedgoodsReportED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletereturnedgoodsReportED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.returnedNTasReportgoodsReportN)) {
                case 1:
                    $("#editreturnedNTasReportgoodsReportED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seereturnedNTasReportgoodsReportED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seereturnedNTasReportgoodsReportED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletereturnedNTasReportgoodsReportED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.tasgoodsReprtN)) {
                case 1:
                    $("#edittasgoodsReprtED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seetasgoodsReprtED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seetasgoodsReprtED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletetasgoodsReprtED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.goodsbargiriReportN)) {
                case 1:
                    $("#editgoodsbargiriReportED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seegoodsbargiriReportED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seegoodsbargiriReportED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletegoodsbargiriReportED").prop("checked", true).change();
                    break;
            }


            switch (parseInt(admin.nosalegoodsReportN)) {
                case 1:
                    $("#editnosalegoodsReportED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seenosalegoodsReportED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seenosalegoodsReportED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletenosalegoodsReportED").prop("checked", true).change();
                    break;
            }


            switch (parseInt(admin.NoExistgoodsReportN)) {
                case 1:
                    $("#editNoExistgoodsReportED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seeNoExistgoodsReportED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seeNoExistgoodsReportED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deleteNoExistgoodsReportED").prop("checked", true).change();
                    break;
            }

            switch (parseInt(admin.returnedCustRepN)) {
                case 1:
                    $("#editreturnedCustRepED").prop("checked", true).change();
                    break;
                case -1:
                    $("#seereturnedCustRepED").prop("checked", false).change();
                    break;
                case 0:
                    $("#seereturnedCustRepED").prop("checked", true).change();
                    break;
                case 2:
                    $("#deletereturnedCustRepED").prop("checked", true).change();
                    break;
            }
            $("#editProfile").modal("show");
        },
        error: function (data) { },
    });
}

$("#adminDiscription").on("blur", function (e) {
    adminId = $("#AdminForAdd").val();

    $.ajax({
        method: "get",
        url: baseUrl + "/EditAdminComment",
        data: {
            _token: "{{ csrf_token() }}",
            comment: $("#adminDiscription").val(),
            adminId: adminId,
        },
        async: true,
        success: function (arrayed_result) { },
        error: function (error) { },
    });
});

$("#searchCity").on("change", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAssignRegion",
        data: {
            _token: "{{ csrf_token() }}",
            cityId: $("#searchCity").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $("#searchMantagheh").empty();
            arrayed_result.forEach((element, index) => {
                $("#searchMantagheh").append(
                    `
            <option value="` +
                    element.SnMNM +
                    `">` +
                    element.NameRec +
                    `</option>
        `
                );
            });
        },
        error: function (data) { },
    });
});

$("#customerMap").on("click", () => {
    if (!$(".modal.in").length) {
        $(".modal-dialog").css({
            top: 0,
            left: 0,
        });
    }
    $("#driverLocation").modal({
        backdrop: false,
        show: true,
    });

    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });
    $("#driverLocation").modal("show");
    let fsn = $("#factorSn").val();
    $.ajax({
        method: "GET",
        url: baseUrl + "/searchMapByFactor",
        data: {
            _token: "{{ csrf_token() }}",
            fsn: fsn,
        },
        async: true,
    }).then(function (data) {
        var map = L.map("map2").setView([35.70163, 51.39211], 12);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '<a href="https://osm.org/copyright">CRM</a>',
        }).addTo(map);
        var marker = {};
        data.forEach(function (item) {
            if (item.LatPers > 0 && item.LonPers > 0) {
                var popup = new L.popup().setContent();
                marker = L.marker([item.LonPers, item.LatPers])
                    .addTo(map)
                    .bindPopup(popup);

                let btn = document.createElement("a");
                btn.setAttribute("data-lat", item.LatPers);
                btn.setAttribute("data-lng", item.LonPers);
                btn.setAttribute("class", "map-btn");
                btn.setAttribute("target", "_blank");
                btn.setAttribute(
                    "href",
                    "https://maps.google.com/?q=" +
                    item.LonPers +
                    "," +
                    item.LatPers
                );
                btn.textContent = "مشتری";
                marker.bindPopup(btn, {
                    maxWidth: "auto",
                });
            }
        });
    });
});

$(window).load(function () {
    var currentUrl = window.location.pathname;
    if (currentUrl == "/crmDriver") {
        document.querySelector(".affairs").style.display = "none";
        document.querySelector("#publicMenu").style.display = "none";
        $(".topMenu").css({ marginTop: "-44px" });
    }
});

$("#deleteAdmin").on("click", () => {
    if ($("#AdminForAdd").val() > 0) {
        swal({
            title: "مطمئین هستید؟",
            text: "کاربر با تمام جزءیاتش حذف خواهد شد.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    method: "get",
                    url: baseUrl + "/deleteAdmin",
                    data: {
                        _token: "{{ csrf_token() }}",
                        asn: $("#AdminForAdd").val(),
                    },
                    async: true,
                    success: function (msg) {
                        $("#adminGroupList").empty();
                        msg.forEach((element, index) => {
                            let discription = "";
                            if (element.discription != null) {
                                discription = element.discription;
                            }
                            $("#adminGroupList").append(
                                `
                            <tr onclick="setAdminStuff(this); selectTableRow(this)">
                            <td>` +
                                (index + 1) +
                                `</td>
                            <td>` +
                                element.name +
                                ` ` +
                                element.lastName +
                                `</td>
                            <td>` +
                                element.adminType +
                                `</td>
                            <td>` +
                                discription +
                                `</td>
                            <td>
                                <input class="mainGroupId" type="radio" name="AdminId[]" value="` +
                                element.id +
                                `_` +
                                element.adminTypeId +
                                `">
                            </td>
                            </tr>`
                            );
                        });
                        swal("کاربر حذف شد.", {
                            icon: "success",
                        });
                        $("#removeKarbar").modal("hide");
                    },
                    error: function (data) { },
                });
            }
        });
    }
});

function deleteAdminList(customerId) {
    if (customerId > 0) {
        swal({
            title: "مطمئین هستید؟",
            text: "کاربر با تمام جزءیاتش حذف خواهد شد.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    method: "get",
                    url: baseUrl + "/deleteAdmin",
                    data: {
                        _token: "{{ csrf_token() }}",
                        asn: customerId
                    },
                    async: true,
                    success: function (msg) {
                        swal("کاربر حذف شد.", {
                            icon: "success",
                        });
                    },
                    error: function (data) { },
                });
            }
        });
    }
}
$("#addedCustomerLeftSideForm").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (arrayed_result) {
            $("#addedCustomer").empty();
            arrayed_result.forEach((element, index) => {
                $("#addedCustomer").append(
                    `
                <tr onclick="checkCheckBox(this,event); selectTableRow(this)">
                    <td id="radif" style="width:55px;">` +
                    (index + 1) +
                    `</td>
                    <td id="mCode" style="width:115px;">` +
                    element.NameRec +
                    `</td>
                    <td >` +
                    element.Name +
                    `</td>
                    <td style="width:50px;">
                        <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` +
                    element.PSN +
                    `" id="kalaId">
                    </td>
                </tr>`
                );
            });
        },
        error: function (error) { },
    });
});

function saveCustomerCommentProperty(element) {
    let csn = $("#customerSn").val();

    let comment = element.value;

    $.ajax({
        method: "get",
        url: baseUrl + "/setCommentProperty",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
            comment: comment,
        },
        async: true,
        success: function (msg) {
            element.value = "";
            element.value = msg[0].comment;
        },
        error: function (data) {
            alert("done comment");
        },
    });
}

$("#addCustomerFirstDate").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});

$("#addCustomerSecondDate").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});

$("#addCustomerFristSabtDate").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});

$("#addCustomerSecondSabtDate").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});

$("#assesFirstDate").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});

$("#assesSecondDate").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});

$("#altime").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "0h:0m:0s YYYY/0M/0D",
    startDate: "today",
    endDate: "1440/5/5",
});
$("#firstDate").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});


$("#firstDateRakid").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});
$("#secondDateRakid").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});

$("#firstDateReturn").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});
$("#secondDateReturn").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});
$("#secondDate").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
    onSelect: () => {
        let secondDate = $("#secondDate").val();
        let firstDate = $("#firstDate").val();
        $.ajax({
            method: "get",
            url: baseUrl + "/searchPastAssesByDate",
            data: {
                _token: "{{ csrf_token() }}",
                secondDate: secondDate,
                firstDate: firstDate,
            },
            async: true,
            success: function (msg) {
                // $('.crmDataTable').dataTable().fnDestroy();
                $("#customerListBody1").empty();
                msg.forEach((element, index) => {
                    $("#customerListBody1").append(
                        `
                <tr onclick="assesmentStuff(this); selectTableRow(this)">
                <td>` +
                        (index + 1) +
                        `</td>
                <td>` +
                        element.Name +
                        `</td>
                <td class="scrollTd">` +
                        element.NetPriceHDS +
                        `</td>
                <td>` +
                        element.FactDate +
                        `</td>
                <td> <input class="customerList form-check-input" name="customerId" type="radio" value="` +
                        element.PSN +
                        `_` +
                        element.SerialNoHDS +
                        `"></td>
                </tr>`
                    );
                });
                // $('.crmDataTable').dataTable();
            },
            error: function (data) { },
        });
    },
});

$("#firstDateDoneComment").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});
$("#secondDateDoneComment").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});

$("#searchEmptyName").on("keyup", () => {
    let searchTerm = $("#searchEmptyName").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchEmptyByName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm,
        },
        async: true,
        success: function (msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#returnedCustomerList").empty();
            msg.forEach((element, index) => {
                $("#returnedCustomerList").append(
                   ` <tr onclick="returnedCustomerStuff(this); selectTableRow(this)">
                        <td>` + (index + 1) +  `</td>
                        <td>` +   element.Name +  `</td>
                        <td>` +  element.PCode + `</td>
                        <td>` +   element.peopeladdress + `</td>
                        <td>` +  element.PhoneStr + `</td>
                        <td>` +  moment(element.removedDate, "YYYY-M-D HH:mm:ss") .locale("fa").format("HH:mm:ss YYYY/M/D") + `</td>
                        <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="` + element.PSN + `"></td>
                    </tr>`
                );
            });
            // $('.crmDataTable').dataTable();
        },
        error: function (data) { },
    });
});

$("#searchEmptyPCode").on("keyup", () => {
    let searchTerm = $("#searchEmptyPCode").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchEmptyByPCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm,
        },
        async: true,
        success: function (msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#returnedCustomerList").empty();
            msg.forEach((element, index) => {
                $("#returnedCustomerList").append(
                    `
                <tr onclick="returnedCustomerStuff(this); selectTableRow(this)">
                <td>` +
                    (index + 1) +
                    `</td>
                <td>` +
                    element.Name +
                    `</td>
                <td>` +
                    element.PCode +
                    `</td>
                <td>` +
                    element.peopeladdress +
                    `</td>
                <td>` +
                    element.PhoneStr +
                    `</td>
                <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="` +
                    element.PSN +
                    `"></td>
            </tr>`
                );
            });
            // $('.crmDataTable').dataTable();
        },
        error: function (data) { },
    });
});

$(".orderReport").on("change", () => {
    let searchTerm = $("#searchAllName").val();
    snMantagheh = $("#searchByMantagheh").val();
    let baseName;

    if ($(".reportRadio:checked").val() == "returned") {
        baseName = $("#orderReportCustomers").val();
        $.ajax({
            method: "get",
            url: baseUrl + "/orderReturned",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
                SnMantagheh: snMantagheh,
                baseName: baseName
            },
            async: true,
            success: function (msg) {
                $("#returnedCustomerList").empty();
                msg.forEach((element, index) => {
                    $("#returnedCustomerList").append(`
                            <tr onclick="returnedCustomerStuff(this,`+ element.PSN + `); selectTableRow(this)">
                                <td>`+ (index + 1) + `</td>
                                <td style="width:188px; font-size:12px">`+ element.Name + `</td>
                                <td style="width:144px;">`+ element.PhoneStr + `</td>
                                <td style="width:133px;">`+ element.adminName + ` ` + element.adminLastName + `</td>
                                <td style="width:88px;">`+ moment(element.returnDate, "YYYY-M-D HH:mm:ss")
                            .locale("fa")
                            .format("HH:mm:ss YYYY/M/D") + `</td>
                                <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="`+ element.PSN + `_` + element.adminId + `"></td>
                            </tr>`);
                });
            },
            error: function (data) { },
        });
    }

});

$("#searchByAdmin").on("change", () => {
    let searchTerm = $("#searchByAdmin").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAllCustomerByAdmin",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm,
        },
        async: true,
        success: function (msg) {
            $("#allCustomerReportyBody").empty();
            msg.forEach((element, index) => {
                let checkOrNot = "";
                if (element.state == 1) {
                    checkOrNot = "";
                } else {
                    checkOrNot = "checked";
                }
                $("#allCustomerReportyBody").append(
                    `
                <tr  onclick="setAlarmCustomerStuff(this); selectTableRow(this)">
                    <td>` + (index + 1) +`</td>
                    <td style="width:333px">` + element.Name +`</td>
                    <td style="width:177px">` +  element.hamrah + ` ` + element.sabit +`</td>
                    <td>` + element.lastDate +`</td>
                    <td>` +  element.adminName + ` ` +  element.lastName + `</td>
                    <td style="width:66px"> <input class="customerList form-check-input" name="customerId" type="radio" value="` + element.PSN +`"></td>
                    <td><input type="checkbox" disabled ` + checkOrNot +` /></td>
            </tr>`
                );
            });
        },
        error: function (data) { },
    });
});

$("#searchAllPCode").on("keyup", () => {
    let searchTerm = $("#searchAllPCode").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAllCustomerByPCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm,
        },
        async: true,
        success: function (msg) {
            msg.forEach((element, index) => {
                $("#allCustomerReportyBody").append(
                    `
                <tr  onclick="setAlarmCustomerStuff(this); selectTableRow(this)">
                    <td>` + (index + 1) + `</td>
                    <td>` +  element.Name +  `</td>
                    <td>` + element.hamrah + ` ` + element.sabit + `</td>
                    <td>` + element.peopeladdress + `</td>
                    <td>` +  element.countFactor + `</td>
                    <td>` + element.lastDate + `</td>
                    <td>هنوز نیست</td>
                    <td style="width:60px">` +  element.adminName + ` ` +element.lastName + `</td>
                    <td> <input class="customerList form-check-input" name="customerId" type="radio" value="` + element.PSN + `"></td>
                </tr>`
                );
            });
        },
        error: function (data) { },
    });
});

$("#searchAllActiveOrNot").on("change", () => {
    let searchTerm = $("#searchAllActiveOrNot").val();
    if (searchTerm != 0) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchAllCustomerActiveOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
            },
            async: true,
            success: function (msg) {
                $("#allCustomerReportyBody").empty();
                msg.forEach((element, index) => {
                    let checkOrNot = "";
                    if (element.state == 1) {
                        checkOrNot = "";
                    } else {
                        checkOrNot = "checked";
                    }
                    $("#allCustomerReportyBody").append(
                        `
            <tr  onclick="setAlarmCustomerStuff(this); selectTableRow(this)">
            <td>` +
                        (index + 1) +
                        `</td>
            <td>` +
                        element.Name +
                        `</td>
            <td>` +
                        element.hamrah +
                        ` ` +
                        element.sabit +
                        `</td>
            <td>` +
                        element.peopeladdress +
                        `</td>
            <td>` +
                        element.countFactor +
                        `</td>
            <td>` +
                        element.lastDate +
                        `</td>
            <td>هنوز نیست</td>
            <td style="width:60px">` +
                        element.adminName +
                        ` ` +
                        element.lastName +
                        `</td>
            <td> <input class="customerList form-check-input" name="customerId" type="radio" value="` +
                        element.PSN +
                        `"></td>
            <td><input type="checkbox" disabled ` +
                        checkOrNot +
                        ` /></td>
        </tr>`
                    );
                });
            },
            error: function (data) { },
        });
    }
});

function getAllCustomerInfos() {
    let locationState = $("#AllLocationOrNot").val();
    let factorState = $("#AllFactorOrNot").val();
    let basketState = $("#AllBasketOrNot").val();
    let adminId = $("#AllByAdmin").val();
    let snMantagheh = $("#searchByMantagheh").val();
    let namePhoneCode=$("#searchAllName").val();
    let orderOption=$("#orderAllCustomers").val();
    let buyStatus=$("#buyStatus").val();
    let firstDate=$("#firstDateBuyOrNot").val();
    let secondDate=$("#secondDateBuyOrNot").val();

    $.ajax({
        method: "get",
        url: baseUrl + "/filterAllCustomer",
        data: {
            _token: "{{ csrf_token() }}",
            locationState: locationState,
            factorState: factorState,
            basketState: basketState,
            adminId: adminId,
            snMantagheh:snMantagheh,
            buyStatus:buyStatus,
            firstDate:firstDate,
            secondDate:secondDate,
            namePhoneCode:namePhoneCode,
            orderOption:orderOption
        },
        async: true,
        success: function (msg) {
            $("#allCustomerReportyBody").empty();
            msg.forEach((element, index) => {
                let checkOrNot = "";
                if (element.state == 1) {
                    checkOrNot = "";
                } else {
                    checkOrNot = "checked";
                }
                $("#allCustomerReportyBody").append(
                    `<tr  onclick="setAmalkardStuff(this,`+ element.PSN + `); selectTableRow(this); getCustomerInformation(`+element.PSN+`)">
                     <td>` +  (index + 1) + `</td>
                     <td style="width:55px">`+ element.PCode +`</td>
                     <td>` +element.Name + `</td>
                     <td >` + element.PhoneStr +`</td>
                     <td>` +  (element.lastDate || '' ) +`</td>
                     <td>` + ( element.adminName || '' ) +  ` ` + ( element.lastName || '' ) + `</td>
                     <td style="width:66px"> <input class="customerList form-check-input" name="customerId" type="radio" value="` + element.PSN + `"></td>
                     <td><input type="checkbox" disabled ` + checkOrNot +` /></td>
                 </tr>`
                );
            });
        },
        error: function (data) { },
    });
}


$("#searchKalaStock").on("change", () => {
    let searchTerm = $("#searchKalaStock").val();
    if (searchTerm > 0) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchKalaByStock",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
            },
            async: true,
            success: function (msg) {
                $("#kalaContainer").empty();
                msg.forEach((element, index) => {
                    $("#kalaContainer").append(
                        `
                <tr>
                <td>` +
                        (index + 1) +
                        `</td>
                <td  style="width:88px">` +
                        element.GoodCde +
                        `</td>
                <td  style="width:333px">` +
                        element.GoodName +
                        `</td>
                <td>` +
                        element.maxFactDate +
                        `</td>
                <td>` +
                        element.hideKala +
                        `</td>
                <td style="color:red;background-color:azure">` +
                        element.Amount +
                        `</td>
                <td>
                    <input class="kala form-check-input" name="kalaId[]" type="radio" value="` +
                        element.GoodSn +
                        `" id="flexCheckCheckedKala">
                </td>
            </tr>`
                    );
                });
            },
            error: function (data) { },
        });
    }
});
$("#searchKalaActiveOrNot").on("change", () => {
    let searchTerm = $("#searchKalaActiveOrNot").val();
    if (searchTerm > 0) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchKalaByActiveOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
            },
            async: true,
            success: function (msg) {
                $("#kalaContainer").empty();
                msg.forEach((element, index) => {
                    $("#kalaContainer").append(
                        `
                    <tr>
                    <td>` +
                        (index + 1) +
                        `</td>
                    <td style="width:88px">` +
                        element.GoodCde +
                        `</td>
                    <td style="width:333px">` +
                        element.GoodName +
                        `</td>
                    <td>` +
                        element.maxFactDate +
                        `</td>
                    <td>` +
                        element.hideKala +
                        `</td>
                    <td style="color:red;background-color:azure">` +
                        element.Amount +
                        `</td>
                    <td>
                        <input class="kala form-check-input" name="kalaId[]" type="radio" value="` +
                        element.GoodSn +
                        `" id="flexCheckCheckedKala">
                    </td>
                </tr>`
                    );
                });
            },
            error: function (data) { },
        });
    }
});
$("#searchKalaExistInStock").on("change", () => {
    let searchTerm = $("#searchKalaExistInStock").val();
    if (searchTerm > 0) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchKalaByZeroOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
            },
            async: true,
            success: function (msg) {
                $("#kalaContainer").empty();
                msg.forEach((element, index) => {
                    $("#kalaContainer").append(
                        `
                    <tr>
                    <td>` +
                        (index + 1) +
                        `</td>
                    <td style="width:88px">` +
                        element.GoodCde +
                        `</td>
                    <td style="width:333px">` +
                        element.GoodName +
                        `</td>
                    <td>` +
                        element.maxFactDate +
                        `</td>
                    <td>` +
                        element.hideKala +
                        `</td>
                    <td style="color:red;background-color:azure">` +
                        element.Amount +
                        `</td>
                    <td>
                        <input class="kala form-check-input" name="kalaId[]" type="radio" value="` +
                        element.GoodSn +
                        `" id="flexCheckCheckedKala">
                    </td>
                </tr>`
                    );
                });
            },
            error: function (data) { },
        });
    }
});
$("#searchMainGroupKala").on("change", () => {
    let searchTerm = $("#searchMainGroupKala").val();
    if (searchTerm > 0) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchSubGroupKala",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
            },
            async: true,
            success: function (msg) {
                $("#searchSubGroupKala").empty();
                $("#searchSubGroupKala").append(`
                <option value="0" selected>همه</option>
                `);
                msg.forEach((element, index) => {
                    $("#searchSubGroupKala").append(
                        `
                    <option value="` +
                        element.id +
                        `">` +
                        element.title +
                        `</option>
                    `
                    );
                });
            },
            error: function (data) {
                alert("not GOOD");
            },
        });
    } else {
        $("#searchSubGroupKala").empty();
        $("#searchSubGroupKala").append(`
    <option value="-1" selected>--</option>
    `);
    }
});
$("#searchSubGroupKala").on("change", () => {
    let searchTerm = $("#searchSubGroupKala").val();
    if (searchTerm > 0) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchBySubGroupKala",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
            },
            async: true,
            success: function (msg) {
                $("#kalaContainer").empty();
                msg.forEach((element, index) => {
                    $("#kalaContainer").append(
                        `
                    <tr>
                    <td>` +
                        (index + 1) +
                        `</td>
                    <td>` +
                        element.GoodCde +
                        `</td>
                    <td>` +
                        element.GoodName +
                        `</td>
                    <td>` +
                        element.title +
                        `</td>
                    <td>` +
                        element.maxFactDate +
                        `</td>
                    <td>` +
                        element.hideKala +
                        `</td>
                    <td style="color:red;background-color:azure">` +
                        element.Amount +
                        `</td>
                    <td>
                        <input class="kala form-check-input" name="kalaId[]" type="radio" value="` +
                        element.GoodSn +
                        `" id="flexCheckCheckedKala">
                    </td>
                </tr>`
                    );
                });
            },
            error: function (data) { },
        });
    }
});
$("#searchAdminNameCode").on("keyup", () => {
    let searchTerm = $("#searchAdminNameCode").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAdminByNameCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm,
        },
        async: true,
        success: function (msg) {
            $("#adminList").empty();
            msg.forEach((element, index) => {
                let adminType = "";

                if (element.adminType == 3) {
                    adminType = "بازاریاب";
                } else {
                    adminType = "پشتیبان";
                }

                $("#adminList").append(
                    `
                                    <tr onclick="setAdminStuffForAdmin(this); selectTableRow(this)">
                                    <td>` +
                    (index + 1) +
                    `</td>
                                    <td>` +
                    element.name +
                    ` ` +
                    element.lastName +
                    `</td>
                                    <td>` +
                    adminType +
                    `</td>
                                    <td></td>
                                    <td>
                                        <input class="mainGroupId" type="radio" name="AdminId[]" value="` +
                    element.id +
                    `_` +
                    element.adminTypeId +
                    `">
                                    </td>
                                    </tr>`
                );
            });
        },
        error: function (data) { },
    });
});

$("#searchAdminGroup").on("change", () => {
    let searchTerm = $("#searchAdminGroup").val();
    if (searchTerm > -1) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchAdminByType",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
            },
            async: true,
            success: function (msg) {
                $("#adminList").empty();
                msg.forEach((element, index) => {
                    $("#adminList").append(
                        `
                    <tr onclick="setAdminStuffForAdmin(this); selectTableRow(this)">
                    <td>` +
                        (index + 1) +
                        `</td>
                    <td>` +
                        element.name +
                        ` ` +
                        element.lastName +
                        `</td>
                    <td>` +
                        element.adminType +
                        `</td>
                    <td></td>
                    <td>
                        <input class="mainGroupId" type="radio" name="AdminId[]" value="` +
                        element.id +
                        `_` +
                        element.adminTypeId +
                        `">
                    </td>
                </tr>`
                    );
                });
            },
            error: function (data) {
                alert("not good");
            },
        });
    }
});

$("#searchAdminActiveOrNot").on("change", () => {
    let searchTerm = $("#searchAdminActiveOrNot").val();
    if (searchTerm > -1) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchAdminByActivation",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
            },
            async: true,
            success: function (msg) {
                $("#adminList").empty();
                msg.forEach((element, index) => {
                    $("#adminList").append(
                        `
                    <tr onclick="setAdminStuffForAdmin(this); selectTableRow(this)">
                    <td>` +
                        (index + 1) +
                        `</td>
                    <td>` +
                        element.name +
                        ` ` +
                        element.lastName +
                        `</td>
                    <td>` +
                        element.adminType +
                        `</td>
                    <td></td>
                    <td>
                        <input class="mainGroupId" type="radio" name="AdminId[]" value="` +
                        element.id +
                        `_` +
                        element.adminTypeId +
                        `">
                    </td>
                </tr>`
                    );
                });
            },
            error: function (data) {
                alert("not good");
            },
        });
    }
});

$("#searchAdminFactorOrNot").on("change", () => {
    let searchTerm = $("#searchAdminFactorOrNot").val();
    if (searchTerm > -1) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchAdminFactorOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
            },
            async: true,
            success: function (msg) {
                $("#adminList").empty();
                msg.forEach((element, index) => {
                    $("#adminList").append(
                        `
                    <tr onclick="setAdminStuffForAdmin(this); selectTableRow(this)">
                    <td>` +
                        (index + 1) +
                        `</td>
                    <td>` +
                        element.name +
                        ` ` +
                        element.lastName +
                        `</td>
                    <td>` +
                        element.adminType +
                        `</td>
                    <td></td>
                    <td>
                        <input class="mainGroupId" type="radio" name="AdminId[]" value="` +
                        element.id +
                        `_` +
                        element.adminTypeId +
                        `">
                    </td>
                </tr>`
                    );
                });
            },
            error: function (data) {
                alert("not good");
            },
        });
    }
});

$("#searchAdminLoginOrNot").on("change", () => {
    let searchTerm = $("#searchAdminLoginOrNot").val();
    if (searchTerm > -1) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchAdminLoginOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
            },
            async: true,
            success: function (msg) {
                $("#adminList").empty();
                msg.forEach((element, index) => {
                    $("#adminList").append(
                        `
                    <tr onclick="setAdminStuffForAdmin(this); selectTableRow(this)">
                    <td>` +
                        (index + 1) +
                        `</td>
                    <td>` +
                        element.name +
                        ` ` +
                        element.lastName +
                        `</td>
                    <td>` +
                        element.adminType +
                        `</td>
                    <td>` +
                        element.discription +
                        `</td>
                    <td>
                        <input class="mainGroupId" type="radio" name="AdminId[]" value="` +
                        element.id +
                        `_` +
                        element.adminTypeId +
                        `">
                    </td>
                </tr>`
                    );
                });
            },
            error: function (data) {
                alert("not good");
            },
        });
    }
});

$("#searchAdminCustomerLoginOrNot").on("change", () => {
    let searchTerm = $("#searchAdminCustomerLoginOrNot").val();
    if (searchTerm > -1) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchAdminCustomerLoginOrNot",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
            },
            async: true,
            success: function (msg) {
                $("#adminList").empty();
                msg.forEach((element, index) => {
                    $("#adminList").append(
                        `
                <tr onclick="setAdminStuffForAdmin(this); selectTableRow(this)">
                <td>` +
                        (index + 1) +
                        `</td>
                <td>` +
                        element.name +
                        ` ` +
                        element.lastName +
                        `</td>
                <td>` +
                        element.adminType +
                        `</td>
                <td>` +
                        element.discription +
                        `</td>
                <td>
                    <input class="mainGroupId" type="radio" name="AdminId[]" value="` +
                        element.id +
                        `_` +
                        element.adminTypeId +
                        `">
                </td>
            </tr>`
                    );
                });
            },
            error: function (data) {
                alert("not good");
            },
        });
    }
});

$("#searchInActiveByName").on("keyup", () => {
    let searchTerm = $("#searchInActiveByName").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchInActiveCustomerByName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm,
        },
        async: true,
        success: function (msg) {
            $("#inactiveCustomerBody").empty();
            msg.forEach((element, index) => {
                $("#inactiveCustomerBody").append(
                    `<tr onclick="setInActiveCustomerStuff(this,`+ element.PSN + `); selectTableRow(this)">
                        <td>` + (index + 1) + `</td>
                        <td>` + element.CustomerName + `</td>
                        <td>` + element.PhoneStr +`</td>
                        <td>` + moment(element.TimeStamp, "YYYY-M-D HH:mm:ss").locale("fa").format("HH:mm:ss YYYY/M/D") + `</td>
                        <td>` + element.name + ` ` + element.lastName + `</td>
                        <td>بدست نیامده</td>
                        <td>` +  element.comment + `</td>
                        <td><input class="customerList form-check-input" name="customerId" type="radio" value="` + element.PSN + `"></td>
                    </tr>`
                );
            });
        },
        error: function (data) {
            alert("not good");
        },
    });
});

$("#searchInActiveByCode").on("keyup", () => {
    let searchTerm = $("#searchInActiveByCode").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchInActiveCustomerByCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm,
        },
        async: true,
        success: function (msg) {
            $("#inactiveCustomerBody").empty();
            msg.forEach((element, index) => {
                $("#inactiveCustomerBody").append(
                    `
            <tr onclick="setInActiveCustomerStuff(this); selectTableRow(this)">
                    <td>` + (index + 1) +`</td>
                    <td>` + element.CustomerName + `</td>
                    <td style="width:99px">` +element.PhoneStr +`</td>
                    <td style="width:133px">` + moment(element.TimeStamp, "YYYY-M-D HH:mm:ss").locale("fa") .format("HH:mm:ss YYYY/M/D") +`</td>
                    <td style="width:133px">` + element.name + ` ` +element.lastName +`</td>
                    <td>` + element.comment +`</td>
                    <td><input class="customerList form-check-input" name="customerId" type="radio" value="` +element.PSN + `"></td>
                </tr>`
                );
            });
        },
        error: function (data) {
            alert("not good");
        },
    });
});

$("#searchInActiveByLocation").on("change", () => {
    let searchTerm = $("#searchInActiveByLocation").val();
    if (searchTerm > -1) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchInActiveCustomerByLocation",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm,
            },
            async: true,
            success: function (msg) {
                $("#inactiveCustomerBody").empty();
                msg.forEach((element, index) => {
                    $("#inactiveCustomerBody").append(
                        `
                <tr onclick="setInActiveCustomerStuff(this); selectTableRow(this)">
                <td>` +
                        (index + 1) +
                        `</td>
                <td>` +
                        element.CustomerName +
                        `</td>
                <td style="width:99px">` +
                        element.PhoneStr +
                        `</td>
                <td style="width:133px">` +
                        moment(element.TimeStamp, "YYYY-M-D HH:mm:ss")
                            .locale("fa")
                            .format("HH:mm:ss YYYY/M/D") +
                        `</td>
                <td style="width:133px">` +
                        element.name +
                        ` ` +
                        element.lastName +
                        `</td>
                <td>` +
                        element.comment +
                        `</td>
                <td><input class="customerList form-check-input" name="customerId" type="radio" value="` +
                        element.PSN +
                        `"></td>
            </tr>`
                    );
                });
            },
            error: function (data) {
                alert("not good");
            },
        });
    }
});

$("#filterLocationBtn").on("click", function () {
    let inactiverAdmin = $("#inactiverAdmin").val();
    let boughtState = $("#boughtState").val();
    let snMantagheh = $("#searchByMantagheh").val();
    let namePhoneCode=$("#searchAllName").val();
    let orderOption=$("#orderInActiveCustomers").val();
});

$("#filterInActivesBtn").on("click", function () {
    let inactiverAdmin = $("#inactiverAdmin").val();
    let boughtState = $("#boughtState").val();
    let snMantagheh = $("#searchByMantagheh").val();
    let namePhoneCode=$("#searchAllName").val();
    let orderOption=$("#orderInActiveCustomers").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/filterInactiveCustomers",
        data: {
            _token: "{{ csrf_token() }}",
            inactiverAdmin: inactiverAdmin,
            boughtState: boughtState,
            snMantagheh :snMantagheh,
            namePhoneCode:namePhoneCode,
            orderOption:orderOption
        },
        async: true,
        success: function (msg) {
            $("#inactiveCustomerBody").empty();
            msg.forEach((element, index) => {
                $("#inactiveCustomerBody").append(
                    `
                <tr onclick="setInActiveCustomerStuff(this); selectTableRow(this); getCustomerInformation(`+element.PSN+`)">
                    <td>` + (index + 1) +`</td>
                    <td>` + element.PCode + `</td>
                    <td>` +  element.CustomerName +`</td>
                    <td style="width:99px">` + element.PhoneStr + `</td>
                    <td style="width:133px">` +(moment(element.TimeStamp, "YYYY-M-D HH:mm:ss") .locale("fa").format("HH:mm:ss YYYY/M/D") ||'') +`</td>
                    <td style="width:133px">` + element.name +  ` ` + element.lastName +`</td>
                    <td>` + element.comment +`</td>
                    <td><input class="customerList form-check-input" name="customerId" type="radio" value="` + element.PSN + `"></td>
                </tr>`
                );
            });
        },
        error: function (error) {

        }
    });
});

function getgoodSaleRound(productId){
    $.get(baseUrl+'/getGoodSalesRound',{productId:productId},(data,status)=>{
        if(status=="success"){
            $("#salesRoundBody").empty();
            $("#kalaNameRound").text("");
            $("#kalaNameRound").text(data[0].GoodName);
            data.forEach((element,index)=>{
                $("#salesRoundBody").append(`<tr onclick="selectTableRow(this)">
                <td>`+(index+1)+`</td>
                <td>`+element.Name+`</td>
                <td>`+element.saleDate+`</td>
                <td>`+parseInt(element.Fi/10).toLocaleString("en")+` تومان</td>
                <td>`+parseInt(element.Amount)+` </td>
                <td>`+parseInt(element.Price/10).toLocaleString("en")+` تومان</td>
                 <td>`+element.GoodCde+`</td>
                </tr> `);
            });
        $("#goodSalesRound").modal("show");
        }
    });
}


$("#allKalaRadio").on("change",()=>{
    $(".allKalaTools").css("display","inline");
    $(".rakidKalaTools").css("display","none");
    $(".returnedKalaTools").css("display","none");
});

$("#rakidKalaReportRadio").on("change",()=>{
    $(".allKalaTools").css("display","none");
    $(".returnedKalaTools").css("display","none");
    $(".rakidKalaTools").css("display","inline");
    
});

$("#returnKalaReportRadio").on("change",()=>{
    $(".returnedKalaTools").css("display","inline");
    $(".allKalaTools").css("display","none");
    $(".rakidKalaTools").css("display","none");
});

$("#filterNoAdminsBtn").on("click", function () {
    let boughtState = $("#buyOrNot").val();
    let snMantagheh = $("#searchByMantagheh").val();
    let namePhoneCode=$("#searchAllName").val();
    let orderOption=$("#orderNoAdminCustomers").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/filterNoAdmins",
        data: {
            _token: "{{ csrf_token() }}",
            boughtState: boughtState,
            snMantagheh:snMantagheh,
            namePhoneCode:namePhoneCode,
            orderOption:orderOption
        },
        async: true,
        success: function (msg) {
            $("#evacuatedCustomers").empty();
            msg.forEach((element, index) => {
                $("#evacuatedCustomers").append(`
                    <tr onclick="returnedCustomerStuff(this,`+ element.PSN + `); selectTableRow(this); getCustomerInformation(`+element.PSN+`)">
                        <td>`+ (index + 1) + `</td>
                        <td style="width:66px;">`+ element.PCode + `</td>
                        <td>`+ element.Name + `</td>
                        <td style="width:333px;">`+ element.peopeladdress + `</td>
                        <td>`+ element.PhoneStr + `</td>
                        <td>`+ (element.LastDate||"") + `</td>
                        <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="`+ element.PSN + `"></td>
                    </tr>`);
            });
        }
        , error: function (error) { }
    });

});

$("#filterNewCustomerBtn").on("click", function () {
    let boughtState = $("#newCustomerBuyState").val();
    let snMantagheh = $("#searchByMantagheh").val();
    let namePhoneCode=$("#searchAllName").val();
    let orderOption=$("#orderAllCustomers").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/filterNewCustomers",
        data: {
            _token: "{{ csrf_token() }}",
            boughtState: boughtState,
            admin:$("#installer").val(),
            snMantagheh:snMantagheh,
            namePhoneCode:namePhoneCode,
            orderOption:orderOption
        },
        async: true,
        success: function (msg) {
            $("#customerBodyList").empty();
            msg.forEach((element, index) => {
                $("#customerBodyList").append(
                    `<tr onclick="returnedCustomerStuff(this); selectTableRow(this); getCustomerInformation(`+element.PSN+`)">
                        <td>`+(index+1)+`</td>
                        <td style="width:66px; font-size:12px">`+element.PCode+`</td>
                        <td style="width:188px; font-size:12px">`+element.Name+`</td>
                        <td style="width:166px;">`+element.adminName+`</td>
                        <td style="width:144px;">`+element.PhoneStr+`</td>
                        <td style="width:133px;">`+(element.sabtDate||'')+`</td>
                        <td style="width:133px;">`+(element.lastFactDate||'')+`</td>
                        <td style="width:66px;"> <input class="customerList form-check-input" name="options[]" type="checkbox" value="`+element.PSN+`"></td>
                        <td style="display:none;"> <input class="customerList form-check-input" name="customerId[]" type="radio" value="`+element.PSN+`"></td>
                    </tr>
                `);
            });
        }
        , error: function (error) { }
    });

});

$("#filterReturnedsBtn").on("click", function () {
    buyState = $("#buyState").val();
    returner = $("#returner").val();
    let snMantagheh = $("#searchByMantagheh").val();
    let namePhoneCode=$("#searchAllName").val();
    let orderOption=$("#orderReportCustomers").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/filterReturneds",
        data: {
            _token: "{{ csrf_token() }}",
            buyState: buyState,
            returner: returner,
            snMantagheh:snMantagheh,
            namePhoneCode:namePhoneCode,
            orderOption:orderOption
        },
        async: true,
        success: function (msg) {
            $("#returnedCustomerList").empty();
            msg.forEach((element, index) => {
                $("#returnedCustomerList").append(`
                <tr onclick="returnedCustomerStuff(this,`+ element.PSN + `); selectTableRow(this)">
                    <td>`+ (index + 1) + `</td>
                    <td style="width:188px; font-size:12px">`+ element.Name + `</td>
                    <td style="width:144px;">`+ element.PhoneStr + `</td>
                    <td style="width:133px;">`+ element.adminName + ` ` + element.adminLastName + `</td>
                    <td style="width:88px;">`+( moment(element.returnDate, "YYYY-M-D HH:mm:ss")
                        .locale("fa")
                        .format("HH:mm:ss YYYY/M/D") ||'') + `</td>
                    <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="`+ element.PSN + `_` + element.adminId + `"></td>
                </tr>`);
            });
        },
        error: function (error) {
        }
    });
});

$("#addProvincePhoneCode").on("submit", function (e) {
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#countryCodeModal").modal("hide");
            $("#PhoneCode").empty();
            data.forEach((element) => {
                $("#PhoneCode").append(
                    `<option value="` +
                    element.provinceCode +
                    `">` +
                    element.provinceCode +
                    `</option>`
                );
            });
        },
        error: function (err) {
            alert("کد اضافه نشد.");
        },
    });
    e.preventDefault();
});


function getLoginReport(history) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getHistroyLogins",
        data: {
            _token: "{{ csrf_token() }}",
            history: "" + history + ""
        },
        async: true,
        success: function (msg) {
            $("#listVisitorBody").empty();
            msg.forEach((element, index) => {
                $("#listVisitorBody").append(
                    `<tr onclick="setAmalkardStuff(this,` + element.PSN + `); selectTableRow(this)">
                        <td >` + (index + 1) +`</td>
                        <td style="width:244px">` +element.Name + `</td>
                         <td>` + element.adminName +`</td>
                       <td >` + moment(element.lastVisit, "YYYY-M-D HH:mm:ss").locale("fa").format("D/M/YYYY HH:mm:ss") +
                    `</td>

            <td >` +
                    element.platform +
                    `</td>
            <td >` +
                    element.browser +
                    `</td>
            <td style="width:77px">` +
                    element.countLogin +
                    `</td>
            <td >` +
                    element.countSameTime +
                    `</td>
                    <td  style="width:66px"> <input class="customerList form-check-input" name="customerId" type="radio" value="`+ element.PSN + `"></td>
            </tr>`
                );
            });
        },
        error: function (data) {
            alert("bad");
        },
    });
}
function getReferencialReport(history) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getReferencialReport",
        data: {
            _token: "{{ csrf_token() }}",
            history: history
        },
        async: true,
        success: function (msg) {
            $("#returnedCustomerList").empty();
            msg.forEach((element, index) => {
                $("#returnedCustomerList").append(`
                <tr onclick="returnedCustomerStuff(this,`+ element.PSN + `); selectTableRow(this) ">
                    <td>`+ (index + 1) + `</td>
                    <td style="width:188px; font-size:12px">`+ element.Name + `</td>
                    <td style="width:144px;">`+ element.PhoneStr + `</td>
                    <td style="width:133px;">`+ element.adminName + ` ` + element.adminLastName + `</td>
                    <td style="width:88px;">`+ moment(element.returnDate, "YYYY-M-D HH:mm:ss")
                        .locale("fa")
                        .format("HH:mm:ss YYYY/M/D") + `</td>
                    <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="`+ element.PSN + `_` + element.adminId + `"></td>
                </tr>`);
            });
        },
        error: function (error) {
        }
    });
}
function getInactiveReport(history) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getInactiveReport",
        data: {
            _token: "{{ csrf_token() }}",
            history: history
        },
        async: true,
        success: function (msg) {
            $("#inactiveCustomerBody").empty();
            msg.forEach((element, index) => {
                $("#inactiveCustomerBody").append(`
                            <tr onclick="setInActiveCustomerStuff(this,`+ element.PSN + `); selectTableRow(this)">
                                <td>`+ (index + 1) + `</td>
                                <td>`+ element.CustomerName + `</td>
                                <td  style="width:99px">`+ element.PhoneStr + `</td>
                                <td style="width:133px">`+ moment(element.TimeStamp, "YYYY-M-D HH:mm:ss")
                        .locale("fa")
                        .format("HH:mm:ss YYYY/M/D") + `</td>
                                <td style="width:133px">`+ element.name + ` ` + element.lastName + `</td>
                                <td  style="font-size:12px;">`+ element.comment + `</td>
                                <td><input class="customerList form-check-input" name="customerId" type="radio" value="`+ element.PSN + `"></td>
                            </tr>`);
            });
        },
        error: function (data) { },
    });
}



// $("#searchByReturner").on("change", () => {
//     let searchTerm = $("#searchByReturner").val();
//     if (searchTerm != 0) {
//         $.ajax({
//             method: "get",
//             url: baseUrl + "/searchByReturner",
//             data: {
//                 _token: "{{ csrf_token() }}",
//                 searchTerm: searchTerm,
//             },
//             async: true,
//             success: function (msg) {
//                 // $('.crmDataTable').dataTable().fnDestroy();
//                 moment.locale("en");
//                 $("#returnedCustomerList").empty();
//                 msg.forEach((element, index) => {
//                     $("#returnedCustomerList").append(
//                         `
//                 <tr onclick="returnedCustomerStuff(this); selectTableRow(this)">
//                     <td>` +(index + 1) + `</td>
//                     <td>` + element.Name +`</td>
//                     <td>` + element.PCode +`</td>
//                     <td class="scrollTd">` + element.peopeladdress + `</td>
//                     <td>` + element.hamrah +`</td>
//                     <td>` + element.adminName + ` ` + element.adminLastName + `</td>
//                     <td>` + moment(element.returnDate, "YYYY-M-D HH:mm:ss").locale("fa").format("HH:mm:ss YYYY/M/D") + `</td>
//                     <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="` + element.PSN + ` ` + element.adminId + `"></td>
//                 </tr> `
//                     );
//                 });
//                 // $('.crmDataTable').dataTable();
//             },
//             error: function (data) { },
//         });
//     }
// });


$("#commentDate").persianDatepicker({
    cellWidth: 40,
    cellHeight: 22,
    fontSize: 12,
    formatDate: "0h:0m:0s YYYY/0M/0D",
    startDate: "today",
    endDate: "1440/5/5",
});

$("#commentDate2").persianDatepicker({
    cellWidth: 40,
    cellHeight: 22,
    fontSize: 12,
    formatDate: "YYYY/0M/0D",
    startDate: "today",
    endDate: "1440/05/05",
});

$("#commentDate3").persianDatepicker({
    cellWidth: 40,
    cellHeight: 22,
    fontSize: 12,
    formatDate: "YYYY/0M/0D",
    startDate: "today"
});
$("#LoginDate2").persianDatepicker({
    cellWidth: 40,
    cellHeight: 22,
    fontSize: 12,
    formatDate: "YYYY/0M/0D"
});


$("#LoginFrom").on("keyup", function () {
    $.ajax({
        method: "get",
        url: baseUrl + "/searchVisotrsLoginFrom",
        data: {
            _token: "{{ csrf_token() }}",
            loginFrom: $("#LoginFrom").val(),
        },
        async: true,
        success: function (msg) {
            $("#listVisitorBody").empty();
            msg.forEach((element, index) => {
                $("#listVisitorBody").append(
                    `<tr  onclick="setAmalkardStuff(this,`+ element.PSN + `); selectTableRow(this)">
            <td >` +
                    (index + 1) +
                    `</td>
                    
            <td >` +
                    moment(element.lastVisit, "YYYY-M-D HH:mm:ss")
                        .locale("fa")
                        .format("D/M/YYYY HH:mm:ss") +
                    `</td>
            <td style="width:244px">` +
                    element.Name +
                    `</td>
            <td >` +
                    element.platform +
                    `</td>
            <td >` +
                    element.browser +
                    `</td>
            <td style="width:77px">` +
                    element.countLogin +
                    `</td>
            <td >` +
                    element.countSameTime +
                    `</td>
                    <td  style="width:66px"> <input class="customerList form-check-input" name="customerId" type="radio" value="`+ element.PSN + `"></td>
            </tr>`
                );
            });
        },
        error: function (data) {
            alert("bad");
        },
    });
});


$("#filterAllLoginsBtn").on("click", function () {
    let snMantagheh =$("#searchByMantagheh").val();
    let namePhoneCode=$("#searchAllName").val();
    let orderOption=$("#orderLoginCustomers").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/filterAllLogins",
        data: {
            _token: "{{ csrf_token() }}",
            platform: $("#visitorPlatform").val(),
            countLoginFrom: $("#LoginFrom").val(),
            countLoginTo: $("#LoginTo").val(),
            countSameTimeFrom: $("#countSameTime").val(),
            countSameTimeTo: $("#countSameTimeTo").val(),
            firstDate: $("#LoginDate1").val(),
            secondDate: $("#LoginDate2").val(),
            orderOption:orderOption,
            namePhoneCode:namePhoneCode,
            snMantagheh:snMantagheh
        },
        async: true,
        success: function (msg) {
            $("#listVisitorBody").empty();
            msg.forEach((element, index) => {
                $("#listVisitorBody").append(
                    `<tr  onclick="setAmalkardStuff(this,`+ element.PSN + `); selectTableRow(this)">
            <td>` +
                    (index + 1) +
                    `</td>
                    <td style="width:244px">` +
                    element.Name +
                    `</td>
                    <td>` +
                    element.adminName +
                    `</td>
            <td >` + moment(element.lastVisit, "YYYY-M-D HH:mm:ss")
                        .locale("fa")
                        .format("D/M/YYYY HH:mm:ss") +
                    `</td>
            <td >` +
                    element.platform +
                    `</td>
            <td >` +
                    element.browser +
                    `</td>
            <td style="width:77px">` +
                    element.countLogin +
                    `</td>
            <td >` +
                    element.countSameTime +
                    `</td>
                    <td  style="width:66px"> <input class="customerList form-check-input" name="customerId" type="radio" value="`+ element.PSN + `"></td>
            </tr>`
                );
            });
        },
        error: function (data) {
            alert("bad");
        },
    });
});


$("#commentDate1").persianDatepicker({
    cellWidth: 40,
    cellHeight: 22,
    fontSize: 12,
    formatDate: "YYYY/0M/0D",
    startDate: "today",
    endDate: "1440/5/5",
});

$("#searchAllCName").on("keyup", function () {
    let searchTerm1 = $("#searchAllCName").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAllCustomerByName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1,
        },
        async: true,
        success: function (msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            msg.forEach((element, index) => {
                $("#customerListBody1").append(
                    `
            <tr onclick="selectAndHighlight(this); selectTableRow(this);">
            <td>` +
                    (index + 1) +
                    `</td>
            <td>` +
                    element.PCode +
                    `</td>
            <td>` +
                    element.Name +
                    `</td>
            <td  class="scrollTd">` +
                    element.peopeladdress +
                    `</td>
            <td>` +
                    element.sabit +
                    `</td>
            <td>` +
                    element.hamrah +
                    `</td>
            <td>` +
                    element.NameRec +
                    `</td>
            <td>2</td>
            <td> <input class="customerList form-check-input" name="customerId" type="radio" value="` +
                    element.PSN +
                    `_` +
                    element.GroupCode +
                    `"></td>
            </tr>`
                );
            });
            // $('.crmDataTable').dataTable();
        },
        error: function (data) { },
    });
});

$("#searchAllCCode").on("keyup", function () {
    let searchTerm1 = $("#searchAllCCode").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchAllCustomerByCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1,
        },
        async: true,
        success: function (msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            msg.forEach((element, index) => {
                $("#customerListBody1").append(
                    `
            <tr onclick="selectAndHighlight(this); selectTableRow(this)">
            <td>` +
                    (index + 1) +
                    `</td>
            <td>` +
                    element.PCode +
                    `</td>
            <td>` +
                    element.Name +
                    `</td>
            <td  class="scrollTd">` +
                    element.peopeladdress +
                    `</td>
            <td>` +
                    element.sabit +
                    `</td>
            <td>` +
                    element.hamrah +
                    `</td>
            <td>` +
                    element.NameRec +
                    `</td>
            <td>2</td>
            <td> <input class="customerList form-check-input" name="customerId" type="radio" value="` +
                    element.PSN +
                    `_` +
                    element.GroupCode +
                    `"></td>
            </tr>`
                );
            });
            // $('.crmDataTable').dataTable();
        },
        error: function (data) { },
    });
});

$("#orderAllByCName").on("change", function () {
    let searchTerm1 = $("#orderAllByCName").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/orderAllCustomerByCName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1,
        },
        async: true,
        success: function (msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            msg.forEach((element, index) => {
                $("#customerListBody1").append(
                    `
            <tr onclick="selectAndHighlight(this); selectTableRow(this)">
            <td>` +
                    (index + 1) +
                    `</td>
            <td>` +
                    element.PCode +
                    `</td>
            <td>` +
                    element.Name +
                    `</td>
            <td  class="scrollTd">` +
                    element.peopeladdress +
                    `</td>
            <td>` +
                    element.sabit +
                    `</td>
            <td>` +
                    element.hamrah +
                    `</td>
            <td>` +
                    element.NameRec +
                    `</td>
            <td>2</td>
            <td> <input class="customerList form-check-input" name="customerId" type="radio" value="` +
                    element.PSN +
                    `_` +
                    element.GroupCode +
                    `"></td>
            </tr>`
                );
            });
            // $('.crmDataTable').dataTable();
        },
        error: function (data) { },
    });
});

$("#searchCustomerName").on("keyup", function () {
    let searchTerm1 = $("#searchCustomerName").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchCustomerByName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1,
            adminId:$("#employeeId").val()
        },
        async: true,
        success: function (msg) {
            // $('.crmDataTable').dataTable().fnDestroy();
            $("#customerListBody1").empty();
            msg.forEach((element, index) => {
                let backgroundColor = "";
                if (element.countComment > 0) {
                    backgroundColor = "lightblue";
                }
                $("#customerListBody1").append(
                    `
            <tr onclick="selectAndHighlight(this); selectTableRow(this)" table" style="background-color:` +
                    backgroundColor +
                    `">
            <td class="forMobileDisplay" style="width:55px">` +
                    (index + 1) +
                    `</td>
            <td  class="forMobileDisplay" style="width:66px">` +
                    element.PCode +
                    `</td>
            <td>` +
                    element.Name +
                    `</td>
            <td  class="forMobileDisplay" style="width:222px">` +
                    element.peopeladdress +
                    `</td>
            <td>` +
                    element.PhoneStr +
                    `</td>
            <td>` +
                    element.NameRec +
                    `</td>
            <td  style="width:88px"> <input class="customerList form-check-input" name="customerId" type="radio" value="` +
                    element.PSN +
                    `_` +
                    element.GroupCode +
                    `"></td>
            </tr>`
                );
            });
            // $('.crmDataTable').dataTable();
        },
        error: function (data) { },
    });
});
$("#searchReferedName").on("keyup", () => {
    let searchTerm1 = $("#searchReferedName").val();
    if (searchTerm1.length > 0) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchReferedCustomerName",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm1,
            },
            async: true,
            success: function (msg) {
                // $('.crmDataTable').dataTable().fnDestroy();
                $("#returnedCustomerList").empty();
                msg.forEach((element, index) => {
                    $("#returnedCustomerList").append(
                        `
            <tr  onclick="returnedCustomerStuff(this); selectTableRow(this)">
                <td>` +
                        (index + 1) +
                        `</td>
                <td>` +
                        element.Name +
                        `</td>
                <td>` +
                        element.PCode +
                        `</td>
                <td>` +
                        element.peopeladdress +
                        `</td>
                <td>` +
                        element.hamrah +
                        `</td>
                <td>` +
                        element.adminName +
                        ` ` +
                        element.adminLastName +
                        `</td>
                <td>` +
                        element.returnDate +
                        `</td>
                <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="` +
                        element.PSN +
                        `_` +
                        element.adminId +
                        `"></td>
            </tr>`
                    );
                });
                // $('.crmDataTable').dataTable();
            },
            error: function (data) {
                alert("bad");
            },
        });
    }
});

$("#searchPCode").on("keyup", () => {
    let searchTerm1 = $("#searchPCode").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchReferedPCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1,
        },
        async: true,
        success: function (msg) {
            $("#returnedCustomerList").empty();
            msg.forEach((element, index) => {
                $("#returnedCustomerList").append(
                    `
                <tr onclick="returnedCustomerStuff(this); selectTableRow(this)">
                    <td>` +
                    (index + 1) +
                    `</td>
                    <td>` +
                    element.Name +
                    `</td>
                    <td style="width:66px;">` +
                    element.PCode +
                    `</td>
                    <td class="scrollTd">` +
                    element.peopeladdress +
                    `</td>
                    <td>` +
                    element.PhoneStr +
                    `</td>
                    <td>` +
                    element.adminName +
                    ` ` +
                    element.adminLastName +
                    `</td>
                    <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="` +
                    element.PSN +
                    `_` +
                    element.adminId +
                    `"></td>
                </tr>`
                );
            });
        },
        error: function (data) { },
    });
});
$("#searchCustomerCode").on("keyup", function () {
    let searchTerm1 = $("#searchCustomerCode").val();
    if (searchTerm1.length > 0) {
        $.ajax({
            method: "get",
            url: baseUrl + "/searchCustomerByCode",
            data: {
                _token: "{{ csrf_token() }}",
                searchTerm: searchTerm1,
                adminId:$("#employeeId").val()
            },
            async: true,
            success: function (msg) {
                $("#customerListBody1").empty();
                msg.forEach((element, index) => {
                    let backgroundColor = "";
                    if (element.countComment > 0) {
                        backgroundColor = "lightblue";
                    }
                    $("#customerListBody1").append(
                        `
                <tr onclick="selectAndHighlight(this); selectTableRow(this)" style="background-color:` +
                        backgroundColor +
                        `">
                <td class="forMobileDisplay" style="width:55px">` +
                        (index + 1) +
                        `</td>
                <td  class="forMobileDisplay" style="width:66px">` +
                        element.PCode +
                        `</td>
                <td>` +
                        element.Name +
                        `</td>
                <td  class="forMobileDisplay" style="width:222px">` +
                        element.peopeladdress +
                        `</td>
                <td>` +
                        element.PhoneStr +
                        `</td>
                <td>` +
                        element.NameRec +
                        `</td>
                <td  style="width:88px"> <input class="customerList form-check-input" name="customerId" type="radio" value="` +
                        element.PSN +
                        `_` +
                        element.GroupCode +
                        `"></td>
                </tr>`
                    );
                });
            },
            error: function (data) { },
        });
    }
});




$("#orderByCodeOrName").on("change", () => {
    let searchTerm1 = $("#orderByCodeOrName").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/orderByNameCode",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm1,
            adminId:$("#employeeId").val()
        },
        async: true,
        success: function (msg) {
            $("#customerListBody1").empty();
            msg.forEach((element, index) => {
                let backgroundColor = "";
                if (element.countComment > 0) {
                    backgroundColor = "lightblue";
                }
                $("#customerListBody1").append( `
                <tr onclick="selectAndHighlight(this); selectTableRow(this); getCustomerInformation(`+element.PSN+`)" style="background-color:` + backgroundColor +`">
                        <td class="forMobileDisplay" style="width:55px">` + (index + 1) + `</td>
                        <td class="forMobileDisplay" style="width:66px">` +  element.PCode +`</td>
                        <td>` + element.Name +`</td>
                        <td class="forMobileDisplay" style="width:222px">` + element.peopeladdress + `</td>
                        <td>` + element.PhoneStr +`</td>
                        <td>` + element.NameRec + `</td>
                        <td style="width:88px"> <input class="customerList form-check-input" name="customerId" type="radio" value="` + element.PSN + `_` + element.GroupCode + `"></td>
                </tr>`
                );
            });
        },
        error: function (data) {
            alert("bad");
        },
    });
});

function setAlarmCustomerStuff(element) {

    $(element).children("input").prop("checked", true);
    $(".enableBtn").prop("disabled", false);
    if ($(".enableBtn").is(":disabled")) {
    } else {
        $(".enableBtn").css("color", "red !important");
    }
    $(".select-highlight tr").removeClass("selected");
    $(element).toggleClass("selected");
    $("#customerSn").val(
        $(element).children("td").children("input").val().split("_")[0]
    );
    $("#adminSn").val(
        $(element).children("td").children("input").val().split("_")[1]
    );
    $("#factorAlarm").val(
        $(element).children("td").children("input").val().split("_")[2]
    );

    $.ajax({
        method: "get",
        url: baseUrl + "/getAlarmInfo",
        data: {
            _token: "{{ csrf_token() }}",
            factorId: $("#factorAlarm").val()
        },
        async: true
        , success: function (msg) {
            $("#alarmLastComment").text("");
            $("#alarmLastComment").text(msg.comment);
        }
        , error: function (error) { }
    });
}



function calcAndClock() {
    var watch = document.querySelector(".affairs");
    var calc = document.querySelector("#myCalculator");
    if (watch.style.display === "none") {
        watch.style.display = "block";
    } else {
        watch.style.display = "none";
    }

    if (calc.style.display === "block") {
        calc.style.display = "none";
    } else {
        calc.style.display = "block";
    }
}

function clockAndClac() {
    var calculator = document.querySelector(".crmCalculator");
    var clock = document.querySelector("#myWatch");
    if (calculator.style.display === "none") {
        calculator.style.display = "block";
    } else {
        calculator.style.display = "none";
    }

    if (clock.style.display === "block") {
        clock.style.display = "none";
    } else {
        clock.style.display = "block";
    }
}

var cancelButton = $("#cancelComment");
cancelButton.on("click", function () {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید بدون ثبت کامنت خارج شوید؟",
        icon: "warning",
        buttons: true,
    }).then(function (value) {
        if (value === true) {
            $("#addComment").modal("hide");
        } else {
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#addComment").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#addComment").modal("show");
        }
    });
});
$("#firstDateBuyOrNot").persianDatepicker({
    cellWidth: 30,
    cellHeight: 12,
    fontSize: 12,
    formatDate: "YYYY/0M/0D",
});
$("#secondDateBuyOrNot").persianDatepicker({
    cellWidth: 30,
    cellHeight: 12,
    fontSize: 12,
    formatDate: "YYYY/0M/0D",
});

$("#firstDateReturned").persianDatepicker({
    cellWidth: 30,
    cellHeight: 12,
    fontSize: 12,
    formatDate: "YYYY/0M/0D",
});
$("#secondDateReturned").persianDatepicker({
    cellWidth: 30,
    cellHeight: 12,
    fontSize: 12,
    formatDate: "YYYY/0M/0D",
    onSelect: () => {
        let secondDate = $("#secondDateReturned").val();
        let firstDate = $("#firstDateReturned").val();

        $.ajax({
            method: "get",
            url: baseUrl + "/searchReturnedByDate",
            data: {
                _token: "{{ csrf_token() }}",
                secondDate: secondDate,
                firstDate: firstDate,
            },
            async: true,
            success: function (msg) {
                moment.locale("en");
                $("#returnedCustomerList").empty();
                msg.forEach((element, index) => {
                    $("#returnedCustomerList").append(
                        `
                    <tr onclick="returnedCustomerStuff(this); selectTableRow(this)">
                        <td>` +
                        (index + 1) +
                        `</td>
                        <td>` +
                        element.Name +
                        `</td>
                        <td>` +
                        element.PCode +
                        `</td>
                        <td>` +
                        element.peopeladdress +
                        `</td>
                        <td>` +
                        element.PhoneStr +
                        `</td>
                        <td>` +
                        element.name +
                        ` ` +
                        element.lastName +
                        `</td>
                        <td>` +
                        moment(element.returnDate, "YYYY-M-D HH:mm:ss")
                            .locale("fa")
                            .format("HH:mm:ss YYYY/M/D") +
                        `</td>
                        <td> <input class="customerList form-check-input" name="customerId[]" type="radio" value="` +
                        element.PSN +
                        `_` +
                        element.adminId +
                        `"></td>
                    </tr> `
                    );
                });
            },
            error: function (data) {
                alert("bad");
            },
        });
    },
});

function selectCustomerLocation(element) {
    $(element).find("input:radio").prop("checked", true);
    let targetRadio = $(element).find("input:radio:checked");
    let customerLoc = targetRadio.val();
    $("#customerLocInputHidden").val(customerLoc);
}

// the following function is used to filter customer with location or without location
$("#customerWithorWithoutLocation").on("change", () => {
    let searchLocation = $("#customerWithorWithoutLocation").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchCustomerLocation",
        data: {
            _token: "{{ csrf_token() }}",
            searchLocation: searchLocation,
        },
        async: true,
        success: function (msg) {
            $("#customerLocation").empty();
            msg.forEach((element, index) => {
                $("#customerLocation").append(
                    `
                         <tr id="forTh" onclick="selectCustomerLocation(this); selectTableRow(this)">
                                <td style="width:33px">` +
                    (index + 1) +
                    `</td>
                                <td style="width:250px;">` +
                    element.Name +
                    ` </td>
                                <td style="width:500px;">` +
                    element.peopeladdress +
                    `</td>
                                <td style="width: 90px;">` +
                    element.sabit +
                    `</td>
                                <td style="width: 90px;">` +
                    element.hamrah +
                    `</td>
                            
                                <td style="width:70px;"><span><input type="radio" name="changeLocation" id="selectCustomer" value="` +
                    element.PSN +
                    `"/></span></td>
                          </tr> `
                );
            });
        },
        error: function (data) { },
    });
});

// the following function searching customer by name
$("#searchingCustomerName").on("keyup", () => {
    let searchTerm = $("#searchingCustomerName").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/searchingCustomerName",
        data: {
            _token: "{{ csrf_token() }}",
            searchTerm: searchTerm,
        },
        async: true,
        success: function (msg) {
            $("#customerLocation").empty();
            msg.forEach((element, index) => {
                $("#customerLocation").append(
                    `
                      <tr id="forTh" onclick="selectCustomerLocation(this); selectTableRow(this)">
                            <td style="width:33px">` +
                    (index + 1) +
                    `</td>
                            <td style="width:250px;">` +
                    element.Name +
                    ` </td>
                            <td style="width:500px;">` +
                    element.peopeladdress +
                    `</td>
                            <td style="width: 90px;">` +
                    element.sabit +
                    `</td>
                            <td style="width: 90px;">` +
                    element.hamrah +
                    `</td>
                            <td style="width:70px;"><span><input type="radio" name="changeLocation" id="selectCustomer" value="` +
                    element.PSN +
                    `"/></span></td>
                            
                        </tr>
                     `
                );
            });
        },
        error: function (data) { },
    });
});


function setBargiryStuff(element,customerId) {
    $("#customerIdForLoc").val(customerId);
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    let factorId = input.val().split("_")[1];
    $("#factorId").val(factorId);
    $("#bargiriyBYSId").val(input.val().split("_")[0]);
    $("#totalMoney").text(parseInt(input.val().split("_")[2] / 10).toLocaleString("en-us")); 
    $("#diffPrice1").text(input.val().split("_")[2] / 10);
    $.ajax({
        method: "get",
        url: baseUrl + "/getFactorInfo",
        data: {
            _token: "{{ csrf_token() }}",
            fsn: factorId,
            bargiriyBYSId: $("#bargiriyBYSId").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $facorInfo = arrayed_result[0];
            $bargiryInfo = arrayed_result[1];
            $("#productList").empty();
            $("#customerPhoneFactor").text($facorInfo[0].PhoneStr);
            $("#factorSnFactor").text($facorInfo[0].FactNo);
            $("#customerAddressFactor").text($facorInfo[0].peopeladdress);
            $("#customerNameFactor").text($facorInfo[0].Name);
            $("#factorDate").text($facorInfo[0].FactDate);

            $("#cartPrice1").text($bargiryInfo.KartPrice);
            $("#naghdPrice1").text($bargiryInfo.NaghdPrice);
            $("#takhfifPrice1").text($bargiryInfo.TakhfifPriceBar);
            $("#varizPrice1").text($bargiryInfo.VarizPrice);
            $("#diffPrice1").text($bargiryInfo.DifPrice);
            $("#description1").text($bargiryInfo.DescRec);

            $facorInfo.forEach((element, index) => {
                $("#productList").append(
                    `
                <tr onclick="selectTableRow(this)">
                <td class="driveFactor" scope="col">` +
                    (index + 1) +
                    `</td>
                <td scope="col">` +
                    element.GoodName +
                    `</td>
                <td  scope="col">` +
                    element.Amount +
                    `</td>
                <td class="driveFactor" scope="col">` +
                    element.UName +
                    `</td>
                <td scope="col">` +
                    (element.Fi / 10).toLocaleString("en-us") +
                    `</td>
                <td style="width:90px;">` +
                    (element.Price / 10).toLocaleString("en-us") +
                    `</td>
                </tr>
        `
                );
            });
        },
        error: function (data) { },
    });
}

$("#openReciveMoneyModal").on("click", function () {
    $("#bargiryFactorId").val($("#bargiriyBYSId").val());
    if (!$(".modal.in").length) {
        $(".modal-dialog").css({
            top: 0,
            left: 0,
        });
    }
    $("#addingDocuments").modal({
        backdrop: false,
        show: true,
    });

    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });

    $("#addingDocuments").modal("show");
    $("#remainHisab").val(parseInt($("#totalMoney").text()));
});

$("#addProvincePhoneCode").on("submit", function (e) {
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#countryCodeModal").modal("hide");
            $("#PhoneCode").empty();
            data.forEach((element) => {
                $("#PhoneCode").append(
                    `<option value="` +
                    element.provinceCode +
                    `">` +
                    element.provinceCode +
                    `</option>`
                );
            });
        },
        error: function (err) {
            alert("کد اضافه نشد.");
        },
    });
    e.preventDefault();
});

$("#addCustomerStateForm").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            
        }
        ,
        error: function(error){

        }
    })
});

$("#addNewCustomer").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            moment.locale("en");
            $("#addingNewCutomer").modal("hide");
            swal({
                title: "موفق!",
                text: "ثبت شد!",
                icon: "success",
                buttons: true,
            });
            $("#customerListBody1").empty();
            data.forEach((element, index) => {
                $("#customerListBody1").append(
                    `<tr onclick="selectTableRow(this)">
                <td class="mobileDisplay" style="width:40px">` +
                    (index + 1) +
                    `</td>
                <td>` +
                    element.Name +
                    `</td>
                <td>` +
                    element.hamrah +
                    `</td>
                <td>` +
                    element.sabit +
                    `</td>
                <td class="mobileDisplay">` +
                    element.NameRec +
                    `</td>
                <td>` +
                    moment(element.TimeStamp, "YYYY/M/D HH:mm:ss")
                        .locale("fa")
                        .format("HH:mm:ss YYYY/M/D") +
                    `</td>
                <td>` +
                    element.peopeladdress +
                    `</td>
                <td class="mobileDisplay">` +
                    element.adminName +
                    ` ` +
                    element.adminLastName +
                    `</td>
                <td style="width:40px"> <input class="customerList form-check-input" name="customerId" type="radio" value="` +
                    element.PSN +
                    ` ` +
                    element.GroupCode +
                    `"></td>
                </tr>`
                );
            });
        },
        error: function (err) { },
    });
});

$("#editCustomerForm").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            moment.locale("en");
            $("#editNewCustomer").modal("hide");
            swal({
                title: "موفق!",
                text: "ثبت شد!",
                icon: "success",
                buttons: true,
            });
            $("#customerListBody1").empty();
            data.forEach((element, index) => {
                $("#customerListBody1").append(
                    `<tr onclick="selectTableRow(this)">
                <td class="mobileDisplay" style="width:40px">` +
                    (index + 1) +
                    `</td>
                <td>` +
                    element.Name +
                    `</td>
                <td>` +
                    element.hamrah +
                    `</td>
                <td>` +
                    element.sabit +
                    `</td>
                <td class="mobileDisplay">` +
                    element.NameRec +
                    `</td>
                <td>` +
                    moment(element.TimeStamp, "YYYY/M/D HH:mm:ss")
                        .locale("fa")
                        .format("HH:mm:ss YYYY/M/D") +
                    `</td>
                <td>` +
                    element.peopeladdress +
                    `</td>
                <td class="mobileDisplay">` +
                    element.adminName +
                    ` ` +
                    element.adminLastName +
                    `</td>
                <td style="width:40px"> <input class="customerList form-check-input" name="customerId" type="radio" value="` +
                    element.PSN +
                    ` ` +
                    element.GroupCode +
                    `"></td>
                </tr>`
                );
            });
        },
        error: function (err) { },
    });
});

$("#setReciveMonyDetails").on("submit", function (e) {
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#addingDocuments").modal("hide");
            swal({
                title: "موفق!",
                text: "ثبت شد!",
                icon: "success",
                buttons: true,
            });
            $("#cartPrice1").text(data.KartPrice);
            $("#naghdPrice1").text(data.NaghdPrice);
            $("#takhfifPrice1").text(data.TakhfifPriceBar);
            $("#varizPrice1").text(data.VarizPrice);
            $("#diffPrice1").text(data.DifPrice);
            $("#description1").text(data.DescRec);
        },
        error: function (err) {
            alert(err);
        },
    });
    e.preventDefault();
});


function showBargiriFactors(element, adminId) {
    $.ajax({
        method: "get",
        url: baseUrl + "/bargeryFactors",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
        },
        async: true,
        success: function (arrayed_result) {
            $("#crmDriverBargeri").empty();

            arrayed_result.forEach((element, index) => {
                $("#crmDriverBargeri").append(
                    `
                <tr onclick="setBargiryStuff(this,`+element.PSN+`); selectTableRow(this); getCustomerInformation(`+element.PSN+`)" @if($factor->isGeven==1) class="selected" @endif">
                    <td>` + (index + 1) + `</td>
                    <td>` + element.Name + `</td>
                    <td class="address">` + element.peopeladdress + `</td>
                    <td><a style="color:black; font-size:12px;" href="tel:+900300400"> ` + element.PhoneStr + ` </a> </td>
                    <td style="text-align: center; cursor:pointer; width:111px" data-toggle="modal" data-target="#bargiriFactor"><i class="fa fa-eye fa-1xl"> </i> </td>
                    <td class="choice"> <input class="customerList form-check-input" name="factorId" type="radio" value="  ` +
                    element.SnBargiryBYS + `_` + element.SerialNoHDS + `_` + element.TotalPriceHDS + `"></td>
              </tr>
                `
                );
            });
        },
    });
}



function salesExpertSelfInfo(adminId) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getSalesExpertSelfInfo",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
        },
        async: true,
        success: function (msg) {
            $("#new_install_today").text(msg[0]);
            $("#all-buys-today").text(msg[1]);
            $("#all-installs").text(msg[2]);
            $("#all_new_buys").text(msg[3]);
            $("#all_monthly_bonuses").text(msg[4]);
        },
        error: function (err) {
            alert("error in getting salesExpertSalesInfo");
        },
    });
}

$(document).ready( function () {
  
    let dataTableLanguage = {
         "language": {
        "emptyTable": "هیچ داده‌ای در جدول وجود ندارد",
        "info": "نمایش _START_ تا _END_ از _TOTAL_ ردیف",
        "infoEmpty": "نمایش 0 تا 0 از 0 ردیف",
        "infoFiltered": "(فیلتر شده از _MAX_ ردیف)",
        "infoThousands": ",",
        "lengthMenu": "نمایش _MENU_ ردیف",
        "processing": "در حال پردازش...",
        "search": "جستجو:",
        "zeroRecords": "رکوردی با این مشخصات پیدا نشد",
        "paginate": {
            "next": "بعدی",
            "previous": "قبلی",
            "first": "ابتدا",
            "last": "انتها"
        },
        "aria": {
            "sortAscending": ": فعال سازی نمایش به صورت صعودی",
            "sortDescending": ": فعال سازی نمایش به صورت نزولی"
        },
        "autoFill": {
            "cancel": "انصراف",
            "fill": "پر کردن همه سلول ها با ساختار سیستم",
            "fillHorizontal": "پر کردن سلول به صورت افقی",
            "fillVertical": "پرکردن سلول به صورت عمودی"
        },
        "buttons": {
            "collection": "مجموعه",
            "colvis": "قابلیت نمایش ستون",
            "colvisRestore": "بازنشانی قابلیت نمایش",
            "copy": "کپی",
            "copySuccess": {
                "1": "یک ردیف داخل حافظه کپی شد",
                "_": "%ds ردیف داخل حافظه کپی شد"
            },
            "copyTitle": "کپی در حافظه",
            "pageLength": {
                "-1": "نمایش همه ردیف‌ها",
                "_": "نمایش %d ردیف",
                "1": "نمایش 1 ردیف"
            },
            "print": "چاپ",
            "copyKeys": "برای کپی داده جدول در حافظه سیستم کلید های ctrl یا ⌘ + C را فشار دهید",
            "csv": "فایل CSV",
            "pdf": "فایل PDF",
            "renameState": "تغییر نام",
            "updateState": "به روز رسانی",
            "excel": "فایل اکسل",
            "createState": "ایجاد وضعیت جدول",
            "removeAllStates": "حذف همه وضعیت ها",
            "removeState": "حذف",
            "savedStates": "وضعیت های ذخیره شده",
            "stateRestore": "بازگشت به وضعیت %d"
        },
        "searchBuilder": {
            "add": "افزودن شرط",
            "button": {
                "0": "جستجو ساز",
                "_": "جستجوساز (%d)"
            },
            "clearAll": "خالی کردن همه",
            "condition": "شرط",
            "conditions": {
                "date": {
                    "after": "بعد از",
                    "before": "بعد از",
                    "between": "میان",
                    "empty": "خالی",
                    "not": "نباشد",
                    "notBetween": "میان نباشد",
                    "notEmpty": "خالی نباشد",
                    "equals": "برابر باشد با"
                },
                "number": {
                    "between": "میان",
                    "empty": "خالی",
                    "gt": "بزرگتر از",
                    "gte": "برابر یا بزرگتر از",
                    "lt": "کمتر از",
                    "lte": "برابر یا کمتر از",
                    "not": "نباشد",
                    "notBetween": "میان نباشد",
                    "notEmpty": "خالی نباشد",
                    "equals": "برابر باشد با"
                },
                "string": {
                    "contains": "حاوی",
                    "empty": "خالی",
                    "endsWith": "به پایان می رسد با",
                    "not": "نباشد",
                    "notEmpty": "خالی نباشد",
                    "startsWith": "شروع  شود با",
                    "notContains": "نباشد حاوی",
                    "notEndsWith": "پایان نیابد با",
                    "notStartsWith": "شروع نشود با",
                    "equals": "برابر باشد با"
                },
                "array": {
                    "empty": "خالی",
                    "contains": "حاوی",
                    "not": "نباشد",
                    "notEmpty": "خالی نباشد",
                    "without": "بدون",
                    "equals": "برابر باشد با"
                }
            },
            "data": "اطلاعات",
            "logicAnd": "و",
            "logicOr": "یا",
            "title": {
                "0": "جستجو ساز",
                "_": "جستجوساز (%d)"
            },
            "value": "مقدار",
            "deleteTitle": "حذف شرط فیلتر",
            "leftTitle": "شرط بیرونی",
            "rightTitle": "شرط فرورفتگی"
        },
        "select": {
            "cells": {
                "1": "1 سلول انتخاب شد",
                "_": "%d سلول انتخاب شد"
            },
            "columns": {
                "1": "یک ستون انتخاب شد",
                "_": "%d ستون انتخاب شد"
            },
            "rows": {
                "1": "1ردیف انتخاب شد",
                "_": "%d  انتخاب شد"
            }
        },
        "thousands": ",",
        "searchPanes": {
            "clearMessage": "همه را پاک کن",
            "collapse": {
                "0": "صفحه جستجو",
                "_": "صفحه جستجو (٪ d)"
            },
            "count": "{total}",
            "countFiltered": "{shown} ({total})",
            "emptyPanes": "صفحه جستجو وجود ندارد",
            "loadMessage": "در حال بارگیری صفحات جستجو ...",
            "title": "فیلترهای فعال - %d",
            "showMessage": "نمایش همه",
            "collapseMessage": "بستن همه"
        },
        "loadingRecords": "در حال بارگذاری...",
        "datetime": {
            "previous": "قبلی",
            "next": "بعدی",
            "hours": "ساعت",
            "minutes": "دقیقه",
            "seconds": "ثانیه",
            "amPm": [
                "صبح",
                "عصر"
            ],
            "months": {
                "0": "ژانویه",
                "1": "فوریه",
                "10": "نوامبر",
                "4": "می",
                "8": "سپتامبر",
                "11": "دسامبر",
                "3": "آوریل",
                "9": "اکتبر",
                "7": "اوت",
                "2": "مارس",
                "5": "ژوئن",
                "6": "ژوئیه"
            },
            "unknown": "-",
            "weekdays": [
                "یکشنبه",
                "دوشنبه",
                "سه‌شنبه",
                "چهارشنبه",
                "پنجشنبه",
                "جمعه",
                "شنبه"
            ]
        },
        "editor": {
            "close": "بستن",
            "create": {
                "button": "جدید",
                "title": "ثبت جدید",
                "submit": "ایجــاد"
            },
            "edit": {
                "button": "ویرایش",
                "title": "ویرایش",
                "submit": "به روز رسانی"
            },
            "remove": {
                "button": "حذف",
                "title": "حذف",
                "submit": "حذف",
                "confirm": {
                    "_": "آیا از حذف %d خط اطمینان دارید؟",
                    "1": "آیا از حذف یک خط اطمینان دارید؟"
                }
            },
            "multi": {
                "restore": "واگرد",
                "noMulti": "این ورودی را می توان به صورت جداگانه ویرایش کرد، اما نه بخشی از یک گروه",
                "title": "مقادیر متعدد",
                "info": "مقادیر متعدد"
            },
            "error": {
                "system": "خطایی رخ داده (اطلاعات بیشتر)"
            }
        },
        "decimal": ".",
        "stateRestore": {
            "creationModal": {
                "button": "ایجاد",
                "columns": {
                    "search": "جستجوی ستون",
                    "visible": "وضعیت نمایش ستون"
                },
                "name": "نام:",
                "order": "مرتب سازی",
                "paging": "صفحه بندی",
                "search": "جستجو",
                "select": "انتخاب",
                "title": "ایجاد وضعیت جدید",
                "toggleLabel": "شامل:",
                "scroller": "موقعیت جدول (اسکرول)",
                "searchBuilder": "صفحه جستجو"
            },
            "emptyError": "نام نمیتواند خالی باشد.",
            "removeConfirm": "آیا از حذف %s مطمئنید؟",
            "removeJoiner": "و",
            "renameButton": "تغییر نام",
            "renameLabel": "نام جدید برای $s :",
            "duplicateError": "وضعیتی با این نام از پیش ذخیره شده.",
            "emptyStates": "هیچ وضعیتی ذخیره نشده",
            "removeError": "حذف با خطا موماجه شد",
            "removeSubmit": "حذف وضعیت",
            "removeTitle": "حذف وضعیت جدول",
            "renameTitle": "تغییر نام وضعیت"
        }   
    }
    }

   $('.myDataTable').DataTable({
        scrollY:  "calc(100vh - 280px)",
        scrollX:     false,
        scrollCollapse: true,
        paging:         true,
        fixedColumns: true,
        pageLength: 50,
        "columnDefs": [
            {"className": "dt-center", "targets": "_all"}
          ],
          language: dataTableLanguage.language,
      });

  // R an D script
        $("#logedInorNot").on("change", () => {
                $("#logedIn").show();
                $("#notLogin").hide();
            });

            $("#logedInRadio").on("change", () => {
                $("#logedIn").show();
                $("#notLogin").hide();
            });

            $("#notLoginRadio").on("change", () => {
                $("#notLogin").show();
                
                $("#logedIn_wrapper").hide();
                $(".loginTable").css("display", "none");
            });

   });

function setAmalkardStuff(element, customerId) {

    $("tr").removeClass("selected");
    $(element).toggleClass("selected");
    $(".enableBtn").val(customerId);
    $("#customerSnLogin").val(customerId);
    $(".enableBtn").prop("disabled", false);
    $("#customerSn").val(customerId);
    $("#customerSnLogin").val(customerId);
}
function openDashboard(customerId) {
    let csn = customerId;
    $("#customerProperty").val("");
    $(".customerSnLogin").val(customerId);
    if ($("#customerSn")) {
        $("#customerSn").val(csn);
    }
    if ($("#customerIdForComment")) {
        $("#customerIdForComment").val(customerId);
    }
    $.ajax({
        method: "get",
        url: baseUrl + "/customerDashboard",
        dataType: "json",
        contentType: "json",
        data: {
            _token: "{{ csrf_token() }}",
            csn: csn,
        },
        async: true,
        success: function (msg) {
            moment.locale("en");
            let exactCustomer = msg[0];
            let factors = msg[1];
            let goodDetails = msg[2];
            let basketOrders = msg[3];
            let comments = msg[4];
            let specialComments = msg[5];
            let specialComment = specialComments[0];
            let assesments = msg[6];
            let returnedFactors = msg[7];
            let loginInfo = msg[8];
            if (specialComment) {
                $("#customerProperty").val(specialComment.comment.trim());
            }
            $("#dashboardTitle").text(exactCustomer.Name);
            $("#customerCode").text(exactCustomer.PCode);
            $("#customerName").text(exactCustomer.Name);
            $("#customerAddress").text(exactCustomer.peopeladdress);
            $("#username").text(exactCustomer.userName);
            $("#password").text(exactCustomer.customerPss);
            $("#mobile1").text(exactCustomer.PhoneStr);
            $("#customerIdForComment").text(exactCustomer.PSN);
            $("#countFactor").text(exactCustomer.countFactor);
            $("#factorTable").empty();
            factors.forEach((element, index) => {
                $("#factorTable").append(
                    `<tr class="tbodyTr" onclick="selectTableRow(this)">
                <td>` +
                    (index + 1) +
                    `</td>
                <td>` +
                    element.FactDate +
                    `</td>
                <td>نامعلوم</td>
                <td>` +
                    parseInt(element.TotalPriceHDS / 10).toLocaleString(
                        "en-us"
                    ) +
                    `</td>
                <td onclick="showFactorDetails(this)"><input name="factorId" style="display:none"  type="radio" value="` +
                    element.SerialNoHDS +
                    `" /><i class="fa fa-eye" /></td>
            </tr>`
                );
            });

            $("#returnedFactorsBody").empty();
            returnedFactors.forEach((element, index) => {
                $("#returnedFactorsBody").append(
                    `<tr class="tbodyTr" onclick="selectTableRow(this)">
            <td>` + (index + 1) + `</td>
            <td>` + element.FactDate + `</td>
            <td>نامع1 لوم</td>
            <td>` + parseInt(element.TotalPriceHDS / 10).toLocaleString("en-us") + `</td>
            <td></td>
            </tr>`
                );
            });
            $("#goodDetail").empty();
            goodDetails.forEach((element, index) => {
                $("#goodDetail").append(
                    `
            <tr class="tbodyTr" onclick="selectTableRow(this)">
                <td>` + (index + 1) + ` </td>
                <td>` + moment(element.maxTime, "YYYY/M/D HH:mm:ss")
                        .locale("fa")
                        .format("YYYY/M/D") + `</td>
                <td>` + element.GoodName + `</td>
                <td>  </td>
                <td>  </td>
                
            </tr>`
                );
            });

            $("#basketOrders").empty();
            basketOrders.forEach((element, index) => {
                $("#basketOrders").append(
                    `<tr onclick="selectTableRow(this)">
                <td>` + (index + 1) + `</td>
                <td>` + moment(element.TimeStamp, "YYYY/M/D HH:mm:ss")
                        .locale("fa")
                        .format("YYYY/M/D") + `</td>
                <td>` + element.GoodName + `</td>
                <td>` + element.Amount + `</td>
                <td>` + element.Fi + `</td>
                </tr>`
                );
            });

            $("#customerLoginInfoBody").empty();
            if (loginInfo) {
                loginInfo.forEach((element, index) => {
                    $("#customerLoginInfoBody").append(
                        `<tr onclick="selectTableRow(this)">
                        <td>` + (index + 1) + `</td>
                        <td>` + moment(element.visitDate, "YYYY/M/D HH:mm:ss").locale("fa").format("YYYY/M/D") + `</td>
                        <td>` + element.platform + `</td>
                        <td style="width:155px;">` + element.browser + `</td>
                </tr>`
                    );
                });
            }

            $("#customerComments").empty();
            comments.forEach((element, index) => {
                $("#customerComments").append(
                    `<tr class="tbodyTr" onclick="selectTableRow(this)">
                <td> ` + (index + 1) + ` </td>
                <td>` + moment(element.TimeStamp, "YYYY/M/D HH:mm:ss").locale("fa").format("YYYY/M/D") + `</td>
                <td onclick="viewComment(` + element.id + `)"</td>` + element.newComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i> </td>
                <td onclick="viewNextComment(` + element.id + `)">` + element.nexComment.substr(0, 10) + `... <i class="fas fa-comment-dots float-end"></i>  </td>
                <td style="width:101px !important;">` + moment(element.specifiedDate, "YYYY/M/D").locale("fa").format("YYYY/M/D") + `</td>
                </tr>`
                );
            });
            $("#customerAssesments").empty();
            assesments.forEach((element, index) => {
                let driverBehavior = "";
                let shipmentProblem = "بله";
                if (element.shipmentProblem == 1) {
                    shipmentProblem = "خیر";
                }
                switch (parseInt(element.driverBehavior)) {
                    case 1:
                        driverBehavior = "عالی";
                        break;
                    case 2:
                        driverBehavior = "خوب";
                        break;
                    case 3:
                        driverBehavior = "متوسط";
                        break;
                    case 4:
                        driverBehavior = "بد";
                        break;
                    default:
                        break;
                }
                $("#customerAssesments").append(
                    `<tr onclick="selectTableRow(this)">
                        <td>` + (index + 1) + `</td>
                        <td>` + moment(element.TimeStamp, "YYYY/M/D").locale("fa").format("YYYY/M/D") + `</td>
                        <td>` + element.comment + `</td>
                        <td>` + driverBehavior + `</td>
                        <td>` + shipmentProblem + `</td>
                        <td style="width:100px"> </td>
                    </tr>`
                );
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#customerDashboard").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#customerDashboard").modal("show");
        },
        error: function (data) { },
    });
}

function getTodaySelfInstalls(adminId, bounus, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getTodaySelfInstalls",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
        },
        async: true,
        success: function (arrayed_result) {
            let countAllTodayInstall = arrayed_result.length;
            if (!countAllTodayInstall) {
            }
            let allBonusToday =
                parseInt(countAllTodayInstall / limitAmount) * bounus;
            $("#all_today_bonus").text(allBonusToday);
            $("#new_customer_today_div").empty();
            arrayed_result.forEach((element, index) => {
                let groupName = "";
                if (element.GroupName) {
                    groupName = element.GroupName;
                } else {
                    groupName = "نا مشخص";
                }
                $("#new_customer_today_div").append(`

                 <tr onclick="selectTableRow(this)">
                     <td class="tdAsButton" style="width:122px !important; text-decoration:none; color:black; font-size:14px;">  <a href="tel:` +
                    element.PhoneStr.split("-")[0] + `"> ` + element.PhoneStr.split("-")[0] + ` </a> </td>
                     <td class="tdAsButton">` + element.Name + ` </td>
                     <td class="tdAsButton"> <a href="tel:` + element.PhoneStr.split("-")[0] + `"> ` + groupName + ` </a> </td>
                    <td class="tdAsButton" onclick="openDashboard(` + element.PSN + `)" style="width:144px;" >` + moment(element.addedDate, "YYYY/M/D h:mm:ss").locale("fa").format("YYYY/M/D h:mm:s") +
                    ` </td>
                   </tr>
                   `);
            });
        },
        error: {},
    });
}

function getAllNewInstallSelf(adminId, bonus, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAllNewInstallSelf",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
        },
        async: true,
        success: function (arrayed_result) {
            moment.locale("en");
            let countAllInstalls = arrayed_result.length;
            let allBonus = parseInt(countAllInstalls / limitAmount) * bonus;
            if (!allBonus) {
                allBonus = 0;
            }
            $("#allInstallBonus").text(allBonus);
            $("#all_new_install").empty();

            arrayed_result.forEach((element, index) => {
                let groupName = "";
                if (element.GroupName) {
                    groupName = element.GroupName;
                } else {
                    groupName = "نامشخص";
                }
                $("#all_new_install").append(`  
                       <tr onclick="selectTableRow(this)">
                            <td class="tdAsButton" style="width:88px !important; text-decoration:none; color:#000; font-size:9px;"> 
                             <a href="tel:` + element.PhoneStr.split("-")[0] + `" style="color:#000 !important"> ` + element.PhoneStr.split("-")[0] + ` </a> </td>
                            <td class="tdAsButton">` + element.Name + ` </td>
                            <td class="tdAsButton" onclick="openDashboard(
                    ` + element.PSN + `)" style="width:99px; font-size:11px; color:black;" > ` + moment(element.addedTime, "YYYY/M/D HH:mm:ss").locale("fa").format("YYYY/M/D HH:mm:ss") +
                    `</td>
                </tr>
                 ` );
            });
        },
        error: function () {
            alert("all installs data is not correct!");
        },
    });
}
function getAllBuyAghlamSelf(adminId, emptydate, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAllBuyAghlamSelf",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
            emptydate: `` + emptydate + ``,
        },
        async: true,
        success: function (arrayed_result) {
            $("#all_aghlam_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#all_aghlam_list").append(`
                       <tr onclick="selectTableRow(this)">
                            <td class="tdAsButton" style="width:55px;">` + (index + 1) + ` </td>
                            <td class="tdAsButton"> ` + element.GoodName + ` </td>
                            <td class="tdAsButton" onclick="openKalaDashboard(` + element.GoodSn + `)" style="width:111px;" > داشبورد خرید  </td>
                        </tr>
                   `);
            });
        },
        error: function (error) {
            alert("aghlam not found error");
        },
    });
}

function getAllBuyAghlamPoshtiban(adminId, emptyDate, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAllBuyAghlamByAdmin",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
            emptydate: `` + emptyDate + ``,
        },
        async: true,
        success: function (arrayed_result) {
            $("#all_aghlam_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#all_aghlam_list").append(`
                       <tr onclick="selectTableRow(this)">
                            <td class="tdAsButton" style="width:55px;">` + (index + 1) + ` </td>
                            <td class="tdAsButton"> ` + element.GoodName + ` </td>
                            <td class="tdAsButton" onclick="openKalaDashboard(` + element.GoodSn + `)" style="width:111px;" > داشبورد خرید  </td>
                        </tr>
            `  );
            });
        },
    });
}

function getTodayBuyAghlamSelf(adminId) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getTodayBuyAghlamSelf",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
        },
        async: true,
        success: function (arrayed_result) {
            $("#today_aghlam_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#today_aghlam_list").append(`
                        <tr onclick="selectTableRow(this)">
                            <td class="tdAsButton" style="width:55px;">` + (index + 1) + ` </td>
                            <td class="tdAsButton"> ` + element.GoodName + ` </td>
                            <td class="tdAsButton" onclick="openKalaDashboard(` + element.GoodSn + `)" style="width:111px;" > داشبورد خرید  </td>
                        </tr>
                   `);
            });
        },
        error: function (error) {
            alert("aghlam not found error");
        },
    });
}
function getAllBuyMoneySelf(adminId, bonus, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAllBuyMoneySelf",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
        },
        async: true,
        success: function (arrayed_result) {
            $("#all_mablagh_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#all_mablagh_list").append(`
                    <tr onclick="selectTableRow(this)">
                    <td class="tdAsButton" style="width:111px !important;"> ` + parseInt(element.SumOfMoney / 10).toLocaleString("us-en") + ` </td>
                    <td class="tdAsButton">` + element.Name + ` </td>
                        <td class="tdAsButton" onclick="openDashboard(` + element.PSN + `)" style="width:111px;" > داشبورد خرید  </td>
                    </tr>
            ` );
            });
        },
        error: function (error) { },
    });
}

function getAllBuyMoneyPoshtiban(adminId, emptyDate, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAllBuyMoneyPoshtiban",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
            adminDate: `` + emptyDate + ``,
        },
        async: true,
        success: function (arrayed_result) {
            $("#all_mablagh_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#all_mablagh_list").append(`
                     <tr onclick="selectTableRow(this)">
                        <td class="tdAsButton" style="width:122px !important;"> ` + parseInt(element.SumOfMoney / 10).toLocaleString("en-us") + ` </td>
                        <td class="tdAsButton">` + element.Name + ` </td>
                        <td class="tdAsButton" onclick="openDashboard(` + element.PSN + `)" style="width:111px;" > داشبورد خرید  </td>
                    </tr> 
            ` );
            });
        },
        error: function (error) { },
    });
}

function getAllNewBuyPoshtiban(adminId, emptyDate, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAllNewBuyPoshtiban",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
            emptyDate: `` + emptyDate + ``,
        },
        async: true,
        success: function (arrayed_result) {
            $("#all_new_buys_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#all_new_buys_list").append(`
                        <tr onclick="selectTableRow(this)">
                           <td class="tdAsButton" style="width:122px !important;"> ` + parseInt(element.SumOfMoney / 10).toLocaleString("en-us") + ` </td>
                           <td class="tdAsButton">` + element.Name + ` </td>
                           <td class="tdAsButton" onclick="openDashboard(` + element.PSN + `)" style="width:111px;" > داشبورد خرید  </td>
                        </tr> 
                  `);
            });
        },
        error: function (error) { },
    });
}
function getTodayPoshtibanBuy(adminId) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAllNewTodayBuyPoshtiban",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
        },
        async: true,
        success: function (arrayed_result) {
            $("#new_buy_today_div").empty();
            arrayed_result.forEach((element, index) => {
                $("#new_buy_today_div").append(`
                     <tr onclick="selectTableRow(this)">
                        <td class="tdAsButton" style="width:122px !important;"> ` + parseInt(element.totalMoney / 10).toLocaleString("en-us") + ` </td>
                        <td class="tdAsButton">` + element.Name + ` </td>
                        <td class="tdAsButton" onclick="openDashboard(` + element.PSN + `)" style="width:111px;" > داشبورد خرید  </td>
                    </tr> 
            `);
            });
        },
        error: function (error) { },
    });
}

function getTodayBuyMoneySelf(adminId, bonus, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getTodayBuyMoneySelf",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
        },
        async: true,
        success: function (arrayed_result) {
            $("#today_mablagh_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#today_mablagh_list").append(`
                    <tr onclick="selectTableRow(this)">
                        <td class="tdAsButton" style="width:122px !important;"> ` + element.SumOfMoney + ` </td>
                        <td class="tdAsButton">` + element.Name + ` </td>
                        <td class="tdAsButton" onclick="openDashboard(` + element.PSN + `)" style="width:111px;" > داشبورد خرید  </td>
                    </tr> 
               `);
            });
        },
        error: function (error) { },
    });
}

function getAllBuyMoneyTodayPoshtiban(adminId) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAllBuyMoneyTodayPoshtiban",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
        },
        async: true,
        success: function (arrayed_result) {
            $("#today_mablagh_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#today_mablagh_list").append(`
                     <tr onclick="selectTableRow(this)">
                        <td class="tdAsButton" style="width:122px !important;"> ` + parseInt(element.SumOfMoney / 10).toLocaleString("en-us") + ` </td>
                        <td class="tdAsButton">` + element.Name + ` </td>
                        <td class="tdAsButton" onclick="openDashboard(` + element.PSN + `)" style="width:111px;" > داشبورد   </td>
                    </tr> 
                  ` );
            });
        },
        error: function (error) {
            alert("server side error data");
        },
    });
}

function getTodaySelfBuyToday(adminId, emptyDate, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getTodaySelfBuyToday",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
        },
        async: true,
        success: function (arrayed_result) {
            $("#new_buy_today_div").empty();
            arrayed_result.forEach((element, index) => {
                $("#new_buy_today_div").append(`
                    <tr onclick="selectTableRow(this)">
                        <td class="tdAsButton" style="width:122px !important; color:black; font-size:12px;"> <a href="tel:` +
                    element.PhoneStr.split("-")[0] + `"> ` + element.PhoneStr.split("-")[0] + ` </a> </td>
                        <td class="tdAsButton">` + element.Name + ` </td>
                        <td class="tdAsButton" onclick="openDashboard(` + element.PSN + `)" style="width:111px;" > داشبورد   </td>
                    </tr> 
                `);
            });
        },
        error: function (error) {
            alert("error in self buy today data");
        },
    });
}

function getAllNewBuySelf(adminId, emptyDate, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAllNewBuySelf",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: adminId,
            emptyDate: emptyDate,
        },
        async: true,
        success: function (arrayed_result) {
            $("#all_new_buys_div").empty();
            arrayed_result.forEach((element, index) => {
                $("#all_new_buys_div").append(`
                      <tr onclick="selectTableRow(this)">
                        <td class="tdAsButton" style="width:122px !important; color:black; font-size:12px;"> <a href="tel:` + element.PhoneStr.split("-")[0] + `"> ` + element.PhoneStr.split("-")[0] + ` </a> </td>
                        <td class="tdAsButton">` + element.Name + ` </td>
                        <td class="tdAsButton" onclick="openDashboard(` + element.PSN + `)" style="width:111px;" > داشبورد   </td>
                    </tr> 
                    `);
            });
        },
        error: function () {
            alert("an error has occured on getting data of all new buys!");
        },
    });
}

$("#firstDateSefSaleExpert").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});

$("#secondDateSefSaleExpert").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
    onSelect: () => {
        const adminId = $("#adminId").val();
        const firstDate = $("#secondDateSefSaleExpert").val();
        const secondDate = $("#firstDateSefSaleExpert").val();
        $.ajax({
            method: "get",
            url: baseUrl + "/getSalesExpertSelfInfoByDates",
            data: {
                _token: "{{ csrf_token() }}",
                adminId: adminId,
                firstDate: $("#firstDateSefSaleExpert").val(),
                secondDate: $("#secondDateSefSaleExpert").val(),
            },
            async: true,
            success: function (msg) {
                $("#all-installs").text(msg[0]);
                $("#all_new_buys").text(msg[1]);
            },
            error: function (err) {
                alert("error in getting salesExpertSalesInfo");
            },
        });
    },
});

// appending the related employee to managers table

function getBossBazarYab(bossId, iteration) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getBossBazarYab",
        data: {
            _token: "{{ csrf_token() }}",
            bossId: bossId,
        },
        async: true,
        success: function (arrayed_result) {
            $("#fellowEmployee" + iteration).empty();
            arrayed_result.forEach((element, index) => {
                $("#fellowEmployee" + iteration).append(
                    `
                    <tr onclick="setSubBazaryabStuff(this);selectTableRow(this)">
                        <td style="width:88px">` +
                    (index + 1) +
                    `</td>
                        <td style="width:140px">` +
                    element.name +
                    ` ` +
                    element.lastName +
                    `</td>
                        <td style="width:60px">
                            <input type="radio" name="adminId" value="` +
                    element.id +
                    `">
                        </td>
                    </tr>
                `
                );
            });
        },

        error: function () {
            alert("an error has occured on getting data of all salesExpert!");
        },
    });
}

$("#addTarget").on("submit", function (e) {
    alert("success");
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#targetList").empty();
            data.forEach((element, index) => {
                $("#targetList").append(
                    `<tr  onclick="setTargetStuff(this); selectTableRow(this)">
                <td>` +
                    (index + 1) +
                    `</td><td>` +
                    element.BaseName +
                    `</td>
                <td>` +
                    element.firstTarget +
                    `</td><td>` +
                    element.firstTargetBonus +
                    `</td>
                <td>` +
                    element.secondTarget +
                    `</td><td>` +
                    element.secondTargetBonus +
                    `</td>
                <td>` +
                    element.thirdTarget +
                    `</td><td>` +
                    element.thirdTargetBonus +
                    `</td>
                <td><input class="form-check-input" name="targetId" type="radio" value="` +
                    element.id +
                    `"></td>
                </tr>`
                );
            });
        },
        error: function (error) { },
    });
    e.preventDefault();
});

$("#selectTarget").on("change", function () {
    const targetId = $(this).val();
    $.ajax({
        method: "get",
        url: baseUrl + "/getTargetInfo",
        data: {
            _token: "{{ csrf_token() }}",
            targetId: targetId,
        },
        async: true,
        success: function (data) {
            $("#targetList").empty();
            data.forEach((element, index) => {
                $("#targetList").append(
                    `<tr  onclick="setTargetStuff(this); selectTableRow(this)">
                <td>` +
                    (index + 1) +
                    `</td><td>` +
                    element.BaseName +
                    `</td>
                <td>` +
                    element.firstTarget +
                    `</td><td>` +
                    element.firstTargetBonus +
                    `</td>
                <td>` +
                    element.secondTarget +
                    `</td><td>` +
                    element.secondTargetBonus +
                    `</td>
                <td>` +
                    element.thirdTarget +
                    `</td><td>` +
                    element.thirdTargetBonus +
                    `</td>
                <td><input class="form-check-input" name="targetId" type="radio" value="` +
                    element.id +
                    `"></td>
                </tr>`
                );
            });
        },
        error: function () {
            alert("cant get data of target!!");
        },
    });
});

$("#editTarget").on("submit", function (e) {
    $("#editingTargetModal").modal("hide");
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#targetList").empty();
            data.forEach((element, index) => {
                $("#targetList").append(
                    `<tr  onclick="setTargetStuff(this); selectTableRow(this)">
                <td>` +
                    (index + 1) +
                    `</td><td>` +
                    element.BaseName +
                    `</td>
                <td>` +
                    parseInt(element.firstTarget).toLocaleString("en-US") +
                    `</td><td>` +
                    element.firstTargetBonus +
                    `</td>
                <td>` +
                    parseInt(element.secondTarget).toLocaleString("en-US") +
                    `</td><td>` +
                    element.secondTargetBonus +
                    `</td>
                <td>` +
                    parseInt(element.thirdTarget).toLocaleString("en-US") +
                    `</td><td>` +
                    element.thirdTargetBonus +
                    `</td>
                <td><input class="form-check-input" name="targetId" type="radio" value="` +
                    element.id +
                    `"></td>
                </tr>`
                );
            });
        },
        error: function (error) { },
    });
    e.preventDefault();
});

function setTargetStuff(element) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    const targetId = input.val();
    $("#selectTargetId").val(targetId);
    $("#deleteTargetBtn").prop("disabled", false);
}

$("#selectTarget").on("change", () => {
    $(".targetTable").show();
});

$(".targetTableTr").on("click", () => {
    $("#targetEditBtn").prop("disabled", false);
});

function setSpecialBonusStuff(element) {
    $(element).find("input:radio").prop("checked", true);
    const input = $(element).find("input:radio");
    const bonusId = input.val();
    $("#specialBonusIdForEdit").val(bonusId);
    $("#specialBonusBtn").prop("disabled", false);
    $("#deleteSpecialBonus").prop("disabled", false);
}

$("#targetEditBtn").on("click", function () {
    const targetId = $("#selectTargetId").val();
    $("#targetIdForEdit").val(targetId);
    $.ajax({
        method: "get",
        url: baseUrl + "/getTargetInfo",
        data: {
            _token: "{{ csrf_token() }}",
            targetId: targetId,
        },
        async: true,
        success: function (data) {
            msg = data[0];
            $("#baseName").val(msg.BaseName);
            $("#firstTarget").val(
                parseInt(msg.firstTarget).toLocaleString("en-US")
            );
            $("#firstTargetBonus").val(msg.firstTargetBonus);
            $("#secondTarget").val(
                parseInt(msg.secondTarget).toLocaleString("en-US")
            );
            $("#secondTargetBonus").val(msg.secondTargetBonus);
            $("#thirdTarget").val(
                parseInt(msg.thirdTarget).toLocaleString("en-US")
            );
            $("#thirdTargetBonus").val(msg.thirdTargetBonus);
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#editingTargetModal").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#editingTargetModal").modal("show");
        },
        error: function () {
            alert("cant get data of target!!");
        },
    });
});

$("#specialBonusBtn").on("click", function () {
    const specialBonusId = $("#specialBonusIdForEdit").val();

    $.ajax({
        method: "get",
        url: baseUrl + "/getSpecialBonusInfo",
        data: {
            _token: "{{ csrf_token() }}",
            bonusId: specialBonusId,
        },
        async: true,
        success: function (msg) {
            let data = msg[0];
            $("#specialBaseName").val(data.BaseName);
            $("#specialBonus").val(data.Bonus);
            $("#limitAmount").val(
                parseInt(data.limitAmount).toLocaleString("en-US")
            );
            $("#specialBaseId").val(data.id);
            if (data.id == 13) {
                $("#limitDiv").text("(تومان)");
            } else {
                $("#limitDiv").text("(تعداد)");
            }
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#editSpecialBonusModal").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#editSpecialBonusModal").modal("show");
        },
        error: function () { },
    });
});

$("#editBonusForm").on("submit", function (e) {
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#specialBonusList").empty();
            data.forEach((element, index) => {
                $("#specialBonusList").append(
                    `
                <tr  onclick="setSpecialBonusStuff(this); selectTableRow(this)">
                <td  style="width:100px;">` +
                    (index + 1) +
                    `</td>
                <td>` +
                    element.BaseName +
                    `</td>
                <td>` +
                    element.Bonus +
                    `</td>
                <td>` +
                    element.limitAmount +
                    `</td>
                <td> <input class="form-check-input" name="specialBonusId" type="radio" value="` +
                    element.id +
                    `"></td>
                </tr>
                `
                );
            });
        },
        error: function () {
            alert("update is not completed");
        },
    });
    e.preventDefault();
});

$("#deleteTargetBtn").on("click", function () {
    $.ajax({
        method: "get",
        url: baseUrl + "/deleteTarget",
        data: {
            _token: "{{ csrf_token() }}",
            baseId: $("#selectTargetId").val(),
        },
        async: true,
        success: function (data) {
            $("#targetList").empty();
            data.forEach((element, index) => {
                $("#targetList").append(
                    `<tr  onclick="setTargetStuff(this); selectTableRow(this)">
        <td>` +
                    (index + 1) +
                    `</td><td>` +
                    element.BaseName +
                    `</td>
        <td>` +
                    element.firstTarget +
                    `</td><td>` +
                    element.firstTargetBonus +
                    `</td>
        <td>` +
                    element.secondTarget +
                    `</td><td>` +
                    element.secondTargetBonus +
                    `</td>
        <td>` +
                    element.thirdTarget +
                    `</td><td>` +
                    element.thirdTargetBonus +
                    `</td>
        <td><input class="form-check-input" name="targetId" type="radio" value="` +
                    element.id +
                    `"></td>
        </tr>`
                );
            });
        },
    });
});

$("#deleteSpecialBonus").on("click", function () {
    $.ajax({
        method: "get",
        url: baseUrl + "/deleteSpecialBonus",
        data: {
            _token: "{{ csrf_token() }}",
            baseId: $("#specialBonusIdForEdit").val(),
        },
        async: true,
        success: function (data) {
            $("#specialBonusList").empty();
            data.forEach((element, index) => {
                $("#specialBonusList").append(
                    `
                    <tr  onclick="setSpecialBonusStuff(this); selectTableRow(this)">
                    <td  style="width:100px;">` +
                    (index + 1) +
                    `</td>
                    <td>` +
                    element.BaseName +
                    `</td>
                    <td>` +
                    element.Bonus +
                    `</td>
                    <td>` +
                    element.limitAmount +
                    `</td>
                    <td> <input class="form-check-input" name="specialBonusId" type="radio" value="` +
                    element.id +
                    `"></td>
                    </tr>
                    `
                );
            });
        },
        error: function (err) {
            alert("cant delete any special Bonus");
        },
    });
});

$("#addBonusForm").on("submit", function (e) {
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#specialBonusList").empty();
            data.forEach((element, index) => {
                $("#specialBonusList").append(
                    `
                <tr  onclick="setSpecialBonusStuff(this); selectTableRow(this)">
                <td  style="width:100px;">` +
                    (index + 1) +
                    `</td>
                <td>` +
                    element.BaseName +
                    `</td>
                <td>` +
                    element.Bonus +
                    `</td>
                <td>` +
                    element.limitAmount +
                    `</td>
                <td> <input class="form-check-input" name="specialBonusId" type="radio" value="` +
                    element.id +
                    `"></td>
                </tr>
                `
                );
            });
        },
        error: function (error) { },
    });
    e.preventDefault();
});

function setSubBazaryabStuff(element) {
    $(element).find("input:radio").prop("checked", true);
    const input = $(element).find("input:radio");
    const subBazaryabId = input.val();
    $("#subBazaryabId").val(subBazaryabId);
    alert(subBazaryabId)
    $("#PoshtibanId").val(subBazaryabId);
    $("#subListDashboardBtn").prop("disabled", false);
}

function setSubPoshtibanStuff(element) {
    $(element).find("input:radio").prop("checked", true);
    const input = $(element).find("input:radio");
    const subBazaryabId = input.val();
    $("#subPoshtibanId").val(subBazaryabId);
    $("#subListDashboardBtn").prop("disabled", false);
}

function openHistoryModal() {
    if (!$(".modal.in").length) {
        $(".modal-dialog").css({
            top: 0,
            left: 0,
        });
    }
    $("#selfHistoryModal").modal({
        backdrop: false,
        show: true,
    });

    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });
    $("#selfHistoryModal").modal("show");
}

$("#limitAmount").on("keyup", () => {
    if (!$("#limitAmount").val()) {
        $("#limitAmount").val(0);
    }

    $("#limitAmount").val(
        parseInt($("#limitAmount").val().replace(/\,/g, "")).toLocaleString(
            "en-US"
        )
    );
});
$("#firstTarget").on("keyup", () => {
    if (!$("#firstTarget").val()) {
        $("#firstTarget").val(0);
    }

    $("#firstTarget").val(
        parseInt($("#firstTarget").val().replace(/\,/g, "")).toLocaleString(
            "en-US"
        )
    );
});
$("#secondTarget").on("keyup", () => {
    if (!$("#secondTarget").val()) {
        $("#secondTarget").val(0);
    }

    $("#secondTarget").val(
        parseInt($("#secondTarget").val().replace(/\,/g, "")).toLocaleString(
            "en-US"
        )
    );
});
$("#thirdTarget").on("keyup", () => {
    if (!$("#thirdTarget").val()) {
        $("#thirdTarget").val(0);
    }

    $("#thirdTarget").val(
        parseInt($("#thirdTarget").val().replace(/\,/g, "")).toLocaleString(
            "en-US"
        )
    );
});

function setEditRTStuff(csn) {
    $("#editRTbtn").val(csn);
}

$("#editRTbtn").on("click", function () {
    let customerId = $("#editRTbtn").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/getRandTInfo",
        data: {
            _token: "{{ csrf_token() }}",
            csn: customerId,
        },
        async: true,
        success: function (respond) {
            let exactCustomerInfo = respond[0];
            let phones = respond[1];
            let cities = respond[2];
            let mantagheh = respond[3];

            $("#customerID").val(exactCustomerInfo.PSN);
            $("#name").val(exactCustomerInfo.Name);
            $("#PCode").val(exactCustomerInfo.PCode);
            $("#mobilePhone").val(phones[0].hamrah);
            $("#sabitPhone").val(phones[0].sabit);
            alert(exactCustomerInfo.Description);
            $("#discription").val(exactCustomerInfo.Description);
            $("#gender").empty();
            $("#gender").append(`
                <option value="2" >مرد</option>
                <option value="1" >زن</option>`);
            $("#snNahiyehE").empty();
            cities.forEach((element, index) => {
                let selectRec = "";
                if (element.SnMNM == exactCustomerInfo.SnNahiyeh) {
                    selectRec = "selected";
                }
                $("#snNahiyehE").append(
                    `<option value="` +
                    element.SnMNM +
                    `" ` +
                    selectRec +
                    `>` +
                    element.NameRec +
                    `</option>`
                );
            });

            $("#snMantaghehE").empty();
            mantagheh.forEach((element, index) => {
                let selectRec = "";
                if (element.SnMNM == exactCustomerInfo.SnMantagheh) {
                    selectRec = "selected";
                }
                $("#snMantaghehE").append(
                    `<option value="` +
                    element.SnMNM +
                    `" ` +
                    selectRec +
                    `>` +
                    element.NameRec +
                    `</option>`
                );
            });
            $("#peopeladdress").val(exactCustomerInfo.peopeladdress);
            $("#password").val(exactCustomerInfo.customerPss);

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#editNewCustomer").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });
            $("#editNewCustomer").modal("show");
        },
        error: function (data) { },
    });
});



$("#addSaleLineBtn").on("click", function () {
    $("#addSaleLineModal").modal("show");
});

$("#addSaleLineForm").on("submit", function (e) {
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#addSaleLineModal").modal("hide");
            $("#saleLines").empty();
            data.forEach((element, index) => {
                $("#saleLines").append(
                    `<tr onclick="setSaleLineStuff(this,` + element.SaleLineSn + `); selectTableRow(this)"> <td>` +
                    (index + 1) +
                    `</td><td>` +
                    element.LineName +
                    `</td> </tr>`
                );
            });
        },
        error: function (error) {
            alert("data server error");
        },
    });
    e.preventDefault();
});

$("#editSaleLineForm").on("submit", function (e) {
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#editSaleLineModal").modal("hide");
            $("#saleLines").empty();
            data.forEach((element, index) => {
                $("#saleLines").append(
                    `<tr onclick="setSaleLineStuff(this,` + element.SaleLineSn + `); selectTableRow(this)"><td>` +
                    (index + 1) +
                    `</td><td>` +
                    element.LineName +
                    `</td> </tr>`
                );
            });
        },
        error: function (error) {
            alert("data server error");
        },
    });
    e.preventDefault();
});

$("#editSaleLineBtn").on("click", function () {
    $.ajax({
        method: "get",
        url: baseUrl + "/getSaleLine",
        data: { _token: "{{@csrf}}", saleLineSn: $("#editSaleLineBtn").val() },
        async: true,
        success: function (data) {
            $("#lineNameId").val(data[0].LineName);
            $("#SaleLineId").val(data[0].SaleLineSn);
            $("#editSaleLineModal").modal("show");
        },
        error: function (error) { },
    });
});

$("#deleteSaleLineBtn").on("click", function () {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید حذف کنید؟",
        icon: "warning",
        buttons: true,
    }).then(function (willAdd) {
        if (willAdd) {
            $.ajax({
                method: "get",
                url: baseUrl + "/deleteSaleLine",
                data: {
                    _token: "{{@csrf}}",
                    saleLineSn: $("#deleteSaleLineBtn").val(),
                },
                async: true,
                success: function (data) {
                    $("#saleLines").empty();
                    data.forEach((element, index) => {
                        $("#saleLines").append(
                            `<tr onclick="setSaleLineStuff(this,` + element.SaleLineSn +`); selectTableRow(this)"><td>` +
                            (index + 1) +
                            `</td><td>` +
                            element.LineName +
                            `</td> </tr>`
                        );
                    });
                },
                error: function (error) { },
            });
        }
    });
});
function setSaleLineStuff(element, snSaleLine) {
    $("tr").removeClass("selected");
    $(element).toggleClass("selected");
    $("#deleteSaleLineBtn").val(snSaleLine);
    $("#editSaleLineBtn").val(snSaleLine);
}

// filtering bargeri list base date
$("#bargeriFirstDate").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});

$("#bargeriSecondDate").persianDatepicker({
    cellWidth: 32,
    cellHeight: 22,
    fontSize: 14,
    formatDate: "YYYY/0M/0D",
});

$("#searchBargiriSelfForm").on("submit", function (e) {
    e.preventDefault()
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (msg) {
            moment.locale("en");
            $("#crmDriverBargeri").empty();
            msg.forEach((element, index) => {
                let gevenState="";
                let givenClass="";
                if(element.isGeven==1){
                    gevenState="checked";
                    givenClass = "selected";
                }
                $("#crmDriverBargeri").append(
                    `<tr onclick="setBargiryStuff(this,`+element.PSN+`); selectTableRow(this)" `+givenClass+`>
                            <td>` + (index + 1) + `</td>
                            <td>` + element.Name + `</td>
                            <td class="address">` + element.peopeladdress + `</td>
                            <td><a style="color:black; font-size:12px;" href="tel:+900300400"> ` + element.PhoneStr + ` </a> </td>
                            <td style="text-align: center; width:50px"><a style="text-decoration:none;" target="_blank" href="https://maps.google.com/?q=` +
                    element.LonPers + "," + element.LatPers + `"><i class="fas fa-map-marker-alt fa-1xl" style="color:#116bc7; "></i></a></td>
                            <td style="text-align: center; cursor:pointer; width:50px" data-toggle="modal" data-target="#factorDeatials"><i class="fa fa-eye fa-1xl"> </i> </td>
                            <td style="width:50px"> 
                            <div class="checkbox-wrapper-44">
                                <label class="toggleButton">
                                    <input type="checkbox" `+gevenState+` onchange="givFactor(this,`+element.SerialNoHDS+`)">
                                    <div>
                                    <svg viewBox="0 0 44 44">
                                        <path d="M14,24 L21,31 L39.7428882,11.5937758 C35.2809627,6.53125861 30.0333333,4 24,4 C12.95,4 4,12.95 4,24 C4,35.05 12.95,44 24,44 C35.05,44 44,35.05 44,24 C44,19.3 42.5809627,15.1645919 39.7428882,11.5937758" transform="translate(-2.000000, -2.000000)"></path>
                                    </svg>
                                    </div>
                                </label>
                                </div>
                            <td class="choice"  style="display:none;"> <input class="customerList form-check-input" name="element." type="radio" value="` +
                    element.SnBargiryBYS + "_" + element.SerialNoHDS + "_" + element.TotalPriceHDS + `"></td>
                        </tr>`
                );
            });
        },
        error: function (data) {
            alert("جستجوی بارگیری مشکل دارد.");
        }
    })
}
);

$("#salesReportForm").on("submit", function (e) {
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#salesReportList").empty();
            let rows = data.map((element, index) =>{
                if(element.FactType==3){
                   return `<tr onclick="selectTableRow(this); getCustomerInformation(`+element.PSN+`) ">
                    <td>` + (index + 1) + `</td><td>` + element.Name + `</td><td>` + element.FactDate + `</td><td>` + parseInt((element.sumAllMoney || "")).toLocaleString("en-us") + `</td><td>` + parseInt((0)).toLocaleString("en-us") + `</td><td style="width: 116px">` + element.PCode + `</td></tr>`
                }else{
                   return `<tr onclick="selectTableRow(this); getCustomerInformation(`+element.PSN+`) ">
                    <td>` + (index + 1) + `</td><td>` + element.Name + `</td><td>` + element.FactDate + `</td><td>` + parseInt((0)).toLocaleString("en-us") + `</td> <td>` + parseInt((element.sumAllMoney || "")).toLocaleString("en-us") + `</td> <td style="width: 116px">` + element.PCode + `</td></tr>`
                }
        });

            let sumAllMoney = data.reduce((accumulator, curValue) => {
                return accumulator + parseInt((curValue.sumAllMoney || 0));
            }, 0);

            $("#salesReportList").append(rows)
            $("#customersMoney").text(sumAllMoney.toLocaleString("en-us"));
            $("#customersCountFactor").text(rows.length);
        },
        error: function (error) {
            alert("data server error");
        },
    });
    e.preventDefault();
});

$("#decreasingEmtyaz").on("submit", function (e) {
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#decreasingCredit").modal("hide");
        },
        error: function (error) {
            alert("data server error");
        },
    });
    e.preventDefault();
});

$("#showEmtiyazHistoryBtn").on("click", () => {
    adminId = $("#adminSn").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/showAdminEmtyazHistory",
        data: {
            _token: "{{ csrf_token() }}",
            adminID: adminId,
        },
        async: true,
        success: function (data) {
            $("#adminEmtyasHistoryBody").empty();
            data.forEach((element, index) => {
                $("#adminEmtyasHistoryBody").append(
                    `
                    <tr onclick="selectTableRow(this)">
                    <td>` + (index + 1) + `</td>
                    <td>` + element.name + ` ` + element.lastName + `</td>
                    <td> بازاریاب </td>
                    <td>` + element.positiveBonus + `</td>
                    <td> ` + element.negativeBonus + `</td>
                    <td>` + element.discription + `</td>
                    <td>
                    <button class="btn btn-primary btn-sm" onclick="editAdminsHistoryEmtyaz(` +
                    element.id +
                    `)"> <i class="fa fa-edit"></i>  </button>
                    </td>
                </tr>`
                );
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#adminEmtyazHistory").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#adminEmtyazHistory").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#adminEmtyazHistory").modal("show");
        },
        error: function (error) {
            alert("data server error");
        },
    });
});

function editAdminsHistoryEmtyaz(adminEmtyasHistoryId) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAdminHistory",
        data: {
            _token: "{{ csrf_token() }}",
            historyID: adminEmtyasHistoryId,
        },
        async: true,
        success: function (data) {
            $("#negativeEmtiyasEdit").val(data[0].negativeBonus);
            $("#historyIDEmtiyasEdit").val(data[0].id);
            $("#positiveEmtiyasEdit").val(data[0].positiveBonus);
            $("#discriptionEmtiyasEdit").val(data[0].discription);
            $("#editingEmtyaz").modal("show");
        },
        error: function () {
            alert("data server error editAdminHistory");
        },
    });
}
$("#addingEmtyaz").on("submit", function (e) {
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (respond) {
            $("#creditSetting").modal("hide");
            $("#historyListBody").empty();
            respond.forEach((element, index) => {
                bonus = 0;
                color = "";
                if (element.positiveBonus > 0) {
                    bonus = element.positiveBonus;
                } else {
                    color = "red";
                    bonus = element.negativeBonus;
                }
                $("#historyListBody").append(`
                    <tr onclick="setUpDownHistoryStuff(this,` + element.historyId + `); selectTableRow(this)">
                        <td>`+ (index + 1) + `</td>
                        <td> `+ element.TimeStamp + ` </td>
                        <td>`+ element.adminName + `</td>
                        <td style="color:`+ color + `">` + bonus + `</td>
                        <td style="width:144px">`+ element.superName + `</td>
                    </tr>
                `);
            });
        },
        error: function (error) {
            alert("data server error");
        },
    });
    e.preventDefault();
});

$("#editingEmtyazForm").on("submit", function (e) {
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#editingEmtyaz").modal("hide");
            $("#adminEmtyasHistoryBody").empty();
            data.forEach((element, index) => {
                $("#adminEmtyasHistoryBody").append(
                    `
                <tr onclick="selectTableRow(this)">
                <td>` + (index + 1) + `</td>
                <td>` + element.name + ` ` + element.lastName +
                    `</td>
                <td> بازاریاب </td>
                <td>` +
                    element.positiveBonus +
                    `</td>
                <td> ` +
                    element.negativeBonus +
                    `</td>
                <td>` +
                    element.discription +
                    `</td>
                <td>
                <button class="btn btn-primary btn-sm" onclick="editAdminsHistoryEmtyaz(` +
                    element.id +
                    `)"> <i class="fa fa-edit"></i>  </button>
                </td>
            </tr>`
                );
            });
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#adminEmtyazHistory").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#adminEmtyazHistory").modal("show");
        },
        error: function () {
            alert("data server error");
        },
    });
    e.preventDefault();
});

function showThisDayMyCustomer(thisDayDate, iteration, adminId) {
    var date = moment();

    var currentDate = date.format("YYYY-MM-DD");
    $.ajax({
        method: "get",
        url: baseUrl + "/getThisDayMyCustomer",
        data: {
            _token: "{{ csrf_token() }}",
            thisDayDate: thisDayDate,
            asn:adminId
        },
        async: true,
        success: function (data) {
            let isDisable = "disabled";
            $("#flush-collapse" + iteration).empty();
            data.forEach((element, index) => {
                if (element.addedDate === currentDate) {
                    isDisable = "";
                } else {
                    isDisable = "disabled";
                }

                if (index == 10) {
                    $("#flush-collapse" + iteration).append(
                        `<div class="bazaryabButton">
								<button class="btn btn-sm btn-primary bazarYabaction" id="loadMore"> بیشتر ...</button>
                            </div> `
                    );
                }
                if (index >= 10) {
                    $("#flush-collapse" + iteration).append(
                        ` <div class="accordion-body showLater" style="display:none;">
                             <div class="row bazarYabcard ">
                                 <div class="bazarYabGrid">
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-info enableBtn bazarYabaction" type="button" onclick="openDashboard(` +
                        element.PSN +
                        `)"> داشبورد <i class="fal fa-dashboard"></i></button> </div>
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-warning bazarYabaction" ` +
                        isDisable +
                        ` onclick="openEditCustomerModalForm(` +
                        element.PSN +
                        `)"> ویرایش <i class="fa fa-edit"></i> </button> </div>
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction" > ` +
                        element.Name +
                        `</button></div>
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction" syle="text-decoration:none; color:black;"> <a href="tel:09030276259"> ` +
                        element.PhoneStr.split("-")[0] +
                        ` </a> </button></div>
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction"> تاریخ   ` +
                                    moment(element.TimeStamp, "YYYY/M/D HH:mm:ss")
                                    .locale("fa")
                                    .format("YYYY/M/D HH:mm:ss")+
                        `</button></div>
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction" onclick="openAddCommentModal(` +
                        element.PSN +
                        `)"> کامنت </button></div>       
                            </div>
                                <div class="row">
                                    <div class="col-12 col-md-12 col-sm-12">
                                        <button class="btn btn-sm btn-primary me-4">` +
                        element.peopeladdress +
                        `</button>
                                    </div>
                                </div>
                        </div>
                    </div> `
                    );
                } else {
                    $("#flush-collapse" + iteration).append(
                        `
                        <div class="accordion-body">
                            <div class="row bazarYabcard">
                                <div class="bazarYabGrid">
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-info enableBtn bazarYabaction" type="button" onclick="openDashboard(` +
                        element.PSN +
                        `)"> داشبورد <i class="fal fa-dashboard"></i></button> </div>
                                        <div class="bazaryabButton"> <button class="btn btn-sm btn-warning bazarYabaction" ` +
                        isDisable +
                        ` onclick="openEditCustomerModalForm(` +
                        element.PSN +
                        `)"> ویرایش <i class="fa fa-edit"></i> </button> </div>
                                        <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction" > ` +
                        element.Name +
                        `</button></div>
                                        <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction" syle="text-decoration:none; color:black;"> <a href="tel:09030276259"> ` +
                        element.PhoneStr.split("-")[0] +
                        ` </a> </button></div>
                                        <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction"> تاریخ   ` +
                        moment(element.TimeStamp, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("YYYY/M/D HH:mm:ss") +
                        `</button></div>
                                        <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction" onclick="openAddCommentModal(` +
                        element.PSN +
                        `)"> کامنت </button></div>       
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-12 col-sm-12">
                                        <button class="btn btn-sm btn-primary me-4">` +
                        element.peopeladdress +
                        `</button>
                                    </div>
                                </div>
                            </div>
                        </div>`
                    );
                }
            });
        },
        error: function () {
            alert("show thisDay method has error");
        },
    });
}

$("#activeOrInActive").on("change", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/getActiveInactiveCustomers",
        data: {
            _token: "{{ csrf_token() }}",
            activeState: $("#activeOrInActive").val(),
            SnMantagheh: $("#searchMantagheh").val(),
        },
        async: true,
        success: function (arrayed_result) {
            $("#allCustomer").empty();
            arrayed_result.forEach((element, index) => {
                $("#allCustomer").append(
                    `
            <tr onclick="checkCheckBox(this,event); selectTableRow(this)">
                <td style="">` +
                    (index + 1) +
                    `</td>
                <td style="">` +
                    element.PCode +
                    `</td>
                <td>` +
                    element.Name +
                    `</td>
                <td style="">
                <input class="form-check-input" name="customerIDs[]" type="checkbox" value="` +
                    element.PSN +
                    `" id="customerId">
                </td>
            </tr>
        `
                );
            });
        },
        error: function () {
            alert("data server error editAdminHistory");
        },
    });
});

function setGeneralTargetStuff(element, userType) {
    let selectedElement = $(element).find("input:radio").prop("checked", true);

    if (userType == 1) {
        $("#generalTargetBtn1").val($(selectedElement).val());
        $("#generalTargetBtn1").prop("disabled", false);
        $("#generalTargetBtn2").prop("disabled", true);
        $("#generalTargetBtn3").prop("disabled", true);
    }
    if (userType == 2) {
        $("#generalTargetBtn2").val($(selectedElement).val());
        $("#generalTargetBtn2").prop("disabled", false);
        $("#generalTargetBtn1").prop("disabled", true);
        $("#generalTargetBtn3").prop("disabled", true);
    }
    if (userType == 3) {
        $("#generalTargetBtn3").val($(selectedElement).val());
        $("#generalTargetBtn3").prop("disabled", false);
        $("#generalTargetBtn2").prop("disabled", true);
        $("#generalTargetBtn1").prop("disabled", true);
    }
    if (userType == 4) {
        $("#generalTargetBtn4").val($(selectedElement).val());
        $("#generalTargetBtn4").prop("disabled", false);
        $("#generalTargetBtn3").prop("disabled", true);
        $("#generalTargetBtn2").prop("disabled", true);
        $("#generalTargetBtn1").prop("disabled", true);
    }
}

function editGeneralBase(element) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getGeneralBase",
        data: {
            _token: "{{csrf_token()}}",
            baseSn: $(element).val().split("_"),
        },
        async: true,
        success: function (arrayed_result) {
            $("#baseGName").val(arrayed_result[0].baseName);
            $("#firstGTarget").val(arrayed_result[0].firstTarget);
            $("#firstGTargetBonus").val(arrayed_result[0].firstTargetBonus);
            $("#secondGTarget").val(arrayed_result[0].secondTarget);
            $("#secondGTargetBonus").val(arrayed_result[0].secondTargetBonus);
            $("#thirdGTarget").val(arrayed_result[0].thirdTarget);
            $("#thirdGTargetBonus").val(arrayed_result[0].thirdTargetBonus);
            $("#baseId").val(arrayed_result[0].SnBase);
            $("#userTypeID").val(arrayed_result[0].userType);
            $("#editingGeneralTargetModal").modal("show");
        },
        eeror: function (error) {
            alert("data server error editGeralBases");
        },
    });
}

$("#editGTarget").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            if (data[0].userType == 1) {
                $("#gtargetList1").empty();
                data.forEach((element, index) => {
                    $("#gtargetList1").append(
                        `
                <tr class="targetTableTr" onclick="setGeneralTargetStuff(this,` +  data.userType + `); selectTableRow(this)">
                    <td>` +
                        (index + 1) +
                        `</td>
                    <td>` +
                        element.baseName +
                        `</td>
                    <td>` +
                        parseInt(element.firstTarget).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                    <td>` +
                        parseInt(element.firstTargetBonus).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                    <td>` +
                        parseInt(element.secondTarget).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                    <td>` +
                        parseInt(element.secondTargetBonus).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                    <td>` +
                        parseInt(element.thirdTarget).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                    <td>` +
                        parseInt(element.thirdTargetBonus).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                    <td><input class="form-check-input" name="targetId" type="radio" value="` +
                        element.SnBase +
                        `_` +
                        element.userType +
                        `"></td>
                </tr>`
                    );
                });
            }
            if (data[0].userType == 3) {
                $("#gtargetList3").empty();
                data.forEach((element, index) => {
                    $("#gtargetList3").append(
                        `
                                <tr class="targetTableTr" onclick="setGeneralTargetStuff(this,` + data.userType + `); selectTableRow(this)">
                                    <td>` +
                        (index + 1) +
                        `</td>
                                    <td>` +
                        element.baseName +
                        `</td>
                                    <td>` +
                        parseInt(element.firstTarget).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                                    <td>` +
                        parseInt(element.firstTargetBonus).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                                    <td>` +
                        parseInt(element.secondTarget).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                                    <td>` +
                        parseInt(element.secondTargetBonus).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                                    <td>` +
                        parseInt(element.thirdTarget).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                                    <td>` +
                        parseInt(element.thirdTargetBonus).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                                    <td> <input class="form-check-input" name="targetId" type="radio" value="` +
                        element.SnBase +
                        `_` +
                        element.userType +
                        `"></td>
                                </tr>`
                    );
                });
            }

            if (data[0].userType == 4) {
                $("#gtargetList4").empty();
                data.forEach((element, index) => {
                    $("#gtargetList4").append(
                        `
            <tr class="targetTableTr" onclick="setGeneralTargetStuff(this,` +
                        data.userType +
                        `); selectTableRow(this)">
            <td>` +
                        (index + 1) +
                        `</td>
            <td>` +
                        element.baseName +
                        `</td>
            <td>` +
                        parseInt(element.firstTarget).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
            <td>` +
                        parseInt(element.firstTargetBonus).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
            <td>` +
                        parseInt(element.secondTarget).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
            <td>` +
                        parseInt(element.secondTargetBonus).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
            <td>` +
                        parseInt(element.thirdTarget).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
            <td>` +
                        parseInt(element.thirdTargetBonus).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
            <td> <input class="form-check-input" name="targetId" type="radio" value="` +
                        element.SnBase +
                        `_` +
                        element.userType +
                        `"></td>
            </tr>`
                    );
                });
            }

            if (data[0].userType == 2) {
                $("#gtargetList2").empty();
                data.forEach((element, index) => {
                    $("#gtargetList2").append(
                        `
                    <tr class="targetTableTr" onclick="setGeneralTargetStuff(this,` +  data.userType +`); selectTableRow(this)">
                        <td>` +
                        (index + 1) +
                        `</td>
                        <td>` +
                        element.baseName +
                        `</td>
                        <td>` +
                        parseInt(element.firstTarget).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                        <td>` +
                        parseInt(element.firstTargetBonus).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                        <td>` +
                        parseInt(element.secondTarget).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                        <td>` +
                        parseInt(element.secondTargetBonus).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                        <td>` +
                        parseInt(element.thirdTarget).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                        <td>` +
                        parseInt(element.thirdTargetBonus).toLocaleString(
                            "en-us"
                        ) +
                        `</td>
                        <td> <input class="form-check-input" name="targetId" type="radio" value="` +
                        element.SnBase +
                        `_` +
                        element.userType +
                        `"></td>
                    </tr>`
                    );
                });
            }
        },
        error: function (error) {
            alert("data server error geditGrarget");
        },
    });
});

//نمایش مشتریان جدید برای ادمین
function showThisDayCustomerForAdmin(thisDayDate, iteration,adminId) {
    var date = moment();

    var currentDate = date.format("YYYY-M-0D");

    $.ajax({
        method: "get",
        url: baseUrl + "/getThisDayCustomerForAdmin",
        data: {
            _token: "{{ csrf_token() }}",
            thisDayDate: thisDayDate,
            asn:adminId
        },
        async: true,
        success: function (data) {
            let isDisable = "disabled";
            $("#flush-collapse" + iteration).empty();
            data.forEach((element, index) => {
                if (element.addedDate === currentDate) {
                    isDisable = "";
                } else {
                    isDisable = "disabled";
                }

                if (index == 10) {
                    $("#flush-collapse" + iteration).append(
                        `<div class="bazaryabButton">
								<button class="btn btn-sm btn-primary bazarYabaction" id="loadMore"> بیشتر ...</button>
                            </div> `
                    );
                }
                if (index >= 10) {
                    $("#flush-collapse" + iteration).append(
                        ` <div class="accordion-body showLater" style="display:none;">
                             <div class="row bazarYabcard ">
                                 <div class="bazarYabGrid">
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-info enableBtn bazarYabaction" type="button" onclick="openDashboard(` +
                        element.PSN +
                        `)"> داشبورد <i class="fal fa-dashboard"></i></button> </div>
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-warning bazarYabaction" ` +
                        isDisable +
                        ` onclick="openEditCustomerModalForm(` +
                        element.PSN +
                        `)"> ویرایش <i class="fa fa-edit"></i> </button> </div>
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction" > ` +
                        element.Name +
                        `</button></div>
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction" syle="text-decoration:none; color:black;"> <a href="tel:09030276259"> ` +
                        element.PhoneStr.split("-")[0] +
                        ` </a> </button></div>
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction"> تاریخ   ` +
                        moment(element.TimeStamp, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("YYYY/M/D") +
                        `</button></div>
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction" onclick="openAddCommentModal(` +
                        element.PSN +
                        `)"> کامنت </button></div>       
                            </div>
                                <div class="row">
                                    <div class="col-12 col-md-12 col-sm-12">
                                        <button class="btn btn-sm btn-primary me-4">` +
                        element.peopeladdress +
                        `</button>
                                    </div>
                                </div>
                        </div>
                    </div> `
                    );
                } else {
                    $("#flush-collapse" + iteration).append(
                        `
                        <div class="accordion-body">
                            <div class="row bazarYabcard">
                                <div class="bazarYabGrid">
                                    <div class="bazaryabButton"> <button class="btn btn-sm btn-info enableBtn bazarYabaction" type="button" onclick="openDashboard(` +
                        element.PSN +
                        `)"> داشبورد <i class="fal fa-dashboard"></i></button> </div>
                                        <div class="bazaryabButton"> <button class="btn btn-sm btn-warning bazarYabaction" ` +
                        isDisable +
                        ` onclick="openEditCustomerModalForm(` +
                        element.PSN +
                        `)"> ویرایش <i class="fa fa-edit"></i> </button> </div>
                                        <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction" > ` +
                        element.Name +
                        `</button></div>
                                        <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction" syle="text-decoration:none; color:black;"> <a href="tel:09030276259"> ` +
                        element.PhoneStr.split("-")[0] +
                        ` </a> </button></div>
                                        <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction"> تاریخ   ` +
                        moment(element.TimeStamp, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("YYYY/M/D") +
                        `</button></div>
                                        <div class="bazaryabButton"> <button class="btn btn-sm btn-primary bazarYabaction" onclick="openAddCommentModal(` +
                        element.PSN +
                        `)"> کامنت </button></div>       
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-12 col-sm-12">
                                        <button class="btn btn-sm btn-primary me-4">` +
                        element.peopeladdress +
                        `</button>
                                    </div>
                                </div>
                            </div>
                        </div>`
                    );
                }
            });
        },
    });
}
//تنظیمات امتیازات

$("#firstGTarget").on("keyup", () => {
    if (!$("#firstGTarget").val()) {
        $("#firstGTarget").val(0);
    }

    $("#firstGTarget").val(
        parseInt($("#firstGTarget").val().replace(/\,/g, "")).toLocaleString(
            "en-US"
        )
    );
});
$("#secondGTarget").on("keyup", () => {
    if (!$("#secondGTarget").val()) {
        $("#secondGTarget").val(0);
    }

    $("#secondGTarget").val(
        parseInt($("#secondGTarget").val().replace(/\,/g, "")).toLocaleString(
            "en-US"
        )
    );
});
$("#thirdGTarget").on("keyup", () => {
    if (!$("#thirdTarget").val()) {
        $("#thirdTarget").val(0);
    }

    $("#thirdGTarget").val(
        parseInt($("#thirdGTarget").val().replace(/\,/g, "")).toLocaleString(
            "en-US"
        )
    );
});

$("#firstGTargetBonus").on("keyup", () => {
    if (!$("#firstGTargetBonus").val()) {
        $("#firstGTargetBonus").val(0);
    }

    $("#firstGTargetBonus").val(
        parseInt(
            $("#firstGTargetBonus").val().replace(/\,/g, "")
        ).toLocaleString("en-US")
    );
});

$("#secondGTargetBonus").on("keyup", () => {
    if (!$("#secondGTargetBonus").val()) {
        $("#secondGTargetBonus").val(0);
    }

    $("#secondGTargetBonus").val(
        parseInt(
            $("#secondGTargetBonus").val().replace(/\,/g, "")
        ).toLocaleString("en-US")
    );
});

$("#thirdGTargetBonus").on("keyup", () => {
    if (!$("#thirdGTargetBonus").val()) {
        $("#thirdGTargetBonus").val(0);
    }

    $("#thirdGTargetBonus").val(
        parseInt(
            $("#thirdGTargetBonus").val().replace(/\,/g, "")
        ).toLocaleString("en-US")
    );
});

$("#generallimitAmount").on("keyup", () => {
    if (!$("#generallimitAmount").val()) {
        $("#generallimitAmount").val(0);
    }

    $("#generallimitAmount").val(
        parseInt(
            $("#generallimitAmount").val().replace(/\,/g, "")
        ).toLocaleString("en-US")
    );
});

$("#generalBonus").on("keyup", () => {
    if (!$("#generalBonus").val()) {
        $("#generalBonus").val(0);
    }

    $("#generalBonus").val(
        parseInt($("#generalBonus").val().replace(/\,/g, "")).toLocaleString(
            "en-US"
        )
    );
});

function setGeneralBonusStuff(element, userType) {
    let input = $(element).find("input:radio").prop("checked", true);
    $("#generalBonusBtn" + userType).val(input.val());
    $("#generalBonusBtn" + userType).prop("disabled", false);
}

function openGeneralSettingModal(element) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getGeneralBonus",
        data: {
            _token: "{{ csrf_token() }}",
            generalBonusID: $(element).val(),
        },
        async: true,
        success: function (data) {
            $("#generalBaseName").val(data.BaseName);
            $("#generalBaseId").val(data.id);
            $("#generallimitAmount").val(data.limitAmount);
            $("#generalBonus").val(data.Bonus);
            $("#generalUserType").val(data.userType);

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#editGeneralBonusModal").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#editGeneralBonusModal").modal("show");
        },
    });
}

$("#editGeneralBonusForm").on("submit", function (e) {
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            if (data[0].userType == 3) {
                $("#generalBonusList3").empty();
                data.forEach((element, index) => {
                    $("#generalBonusList3").append(
                        `
                        <tr onclick="setGeneralBonusStuff(this,` +  element.userType + `); selectTableRow(this)">
                        <td  style="width:100px;">` +
                        (index + 1) +
                        `</td>
                        <td>` +
                        element.BaseName +
                        `</td>
                        <td>` +
                        element.Bonus +
                        `</td>
                        <td>` +
                        parseInt(element.limitAmount).toLocaleString(
                            "en-US"
                        ) +
                        `</td>
                        <td> <input class="form-check-input" name="generalBonusId" type="radio" value="` +
                        element.id +
                        `"></td>
                        </tr>`
                    );
                });
            }

            if (data[0].userType == 1) {
                $("#generalBonusList1").empty();
                data.forEach((element, index) => {
                    $("#generalBonusList1").append(
                        `
                        <tr onclick="setGeneralBonusStuff(this,` + element.userType +  `); selectTableRow(this)">
                        <td  style="width:100px;">` +
                        (index + 1) +
                        `</td>
                        <td>` +
                        element.BaseName +
                        `</td>
                        <td>` +
                        element.Bonus +
                        `</td>
                        <td>` +
                        parseInt(element.limitAmount).toLocaleString(
                            "en-US"
                        ) +
                        `</td>
                        <td> <input class="form-check-input" name="generalBonusId" type="radio" value="` +
                        element.id +
                        `"></td>
                        </tr>`
                    );
                });
            }
            if (data[0].userType == 2) {
                $("#generalBonusList2").empty();
                data.forEach((element, index) => {
                    $("#generalBonusList2").append(
                        `
                        <tr onclick="setGeneralBonusStuff(this,` + element.userType +`); selectTableRow(this)">
                        <td  style="width:100px;">` +
                        (index + 1) +
                        `</td>
                        <td>` +
                        element.BaseName +
                        `</td>
                        <td>` +
                        element.Bonus +
                        `</td>
                        <td>` +
                        parseInt(element.limitAmount).toLocaleString(
                            "en-US"
                        ) +
                        `</td>
                        <td> <input class="form-check-input" name="generalBonusId" type="radio" value="` +
                        element.id +
                        `"></td>
                        </tr>`
                    );
                });
            }
            if (data[0].userType == 4) {
                $("#generalBonusList4").empty();
                data.forEach((element, index) => {
                    $("#generalBonusList4").append(
                        `
                        <tr onclick="setGeneralBonusStuff(this,` +
                        element.userType + `); selectTableRow(this)">
                        <td  style="width:100px;">` +
                        (index + 1) +
                        `</td>
                        <td>` +
                        element.BaseName +
                        `</td>
                        <td>` +
                        element.Bonus +
                        `</td>
                        <td>` +
                        parseInt(element.limitAmount).toLocaleString(
                            "en-US"
                        ) +
                        `</td>
                        <td> <input class="form-check-input" name="generalBonusId" type="radio" value="` +
                        element.id +
                        `"></td>
                        </tr>`
                    );
                });
            }
        },
    });
    e.preventDefault();
});

function getTodayBuyAghlamPoshtiban(adminID, lastDate, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getTodayBuyAghlamPoshtiban",
        data: {
            _token: "{{ csrf_token() }}",
            adminID: adminID,
        },
        async: true,
        success: function (arrayed_result) {
            $("#today_aghlam_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#today_aghlam_list").append(`
                            <tr onclick="selectTableRow(this)">
                                <td class="tdAsButton" style="width:55px;">` + (index + 1) + ` </td>
                                <td class="tdAsButton"> ` + element.GoodName + ` </td>
                                <td class="tdAsButton" onclick="openKalaDashboard(` + element.GoodSn + `)" style="width:111px;" > داشبورد خرید  </td>
                               
                             </tr>
                    `
                );
            });
        },
    });
}

function getTodayBuyAghlamDriver(adminId, emptyDate, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getDriverTodayAghlam",
        data: {
            _token: "{{@csrf}}",
            driverId: adminId,
            emptyDate: "" + emptyDate + "",
        },
        async: true,
        success: function (arrayed_result) {
            $("#today_aghlam_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#today_aghlam_list").append(`
                            <tr onclick="selectTableRow(this)">
                                <td class="tdAsButton" style="width:55px;">` + (index + 1) + ` </td>
                                <td class="tdAsButton"> ` + element.GoodName + ` </td>
                                <td class="tdAsButton" onclick="openKalaDashboard(` + element.GoodSn + `)" style="width:111px;" > داشبورد خرید  </td>
                             </tr>
            `
                );
            });
        },
        error: function (error) {
            alert("error in getting data");
        },
    });
}

function getAllBuyAghlamDriver(adminId, emptyDate, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getDriverAllAghlam",
        data: {
            _token: "{{@csrf}}",
            driverId: adminId,
            emptyDate: "" + emptyDate + "",
        },
        async: true,
        success: function (arrayed_result) {
            $("#all_aghlam_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#all_aghlam_list").append(`
                    <tr onclick="selectTableRow(this)">
                        <td class="tdAsButton" style="width:55px;">` + (index + 1) + ` </td>
                        <td class="tdAsButton"> ` + element.GoodName + ` </td>
                        <td class="tdAsButton" onclick="openKalaDashboard(` + element.GoodSn + `)" style="width:111px;" > داشبورد خرید  </td>
                    </tr>
                `
                );
            });
        },
        error: function (error) {
            alert("error in getting data");
        },
    });
}

function getAllFactorDriver(adminId, emptyDate, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getAllFactorDriver",
        data: {
            _token: "{{@csrf}}",
            driverId: adminId,
            emptyDate: "" + emptyDate + "",
        },
        async: true,
        success: function (arrayed_result) {
            $("#all_factor_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#all_factor_list").append(
                    `
                <div class="row mb-2"> 
                    <div class="col-4 col-sm-4 px-1">  <button class="btn btn-info btn-sm nasb-button"><a href="#"> ` +
                    (index + 1) +
                    `</a></button> </div>
                    <div class="col-4 col-sm-4 px-1">  <button class="btn btn-info btn-sm nasb-button"> <a href="">` +
                    element.Name +
                    `</a> </button> </div>
                    <div class="col-4 col-sm-4 px-1">  <button class="btn btn-info btn-sm nasb-button"> <a href="">` +
                    element.FactDate +
                    `</a> </button> </div>
                    <div class="col-4 col-sm-4 px-1">  <button class="btn btn-info btn-sm nasb-button" onclick="openKalaDashboard(` +
                    element.SnGood +
                    `)"><a href="#">  داشبورد کالا</a> </button> </div>
                </div>
                `
                );
            });
        },
        error: function (error) {
            alert("error in getting data");
        },
    });
}

function getTodayDriverFactors(adminId, emptyDate, limitAmount) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getTodayDriverFactors",
        data: {
            _token: "{{@csrf}}",
            driverId: adminId,
            emptyDate: "" + emptyDate + "",
        },
        async: true,
        success: function (arrayed_result) {
            $("#today_factor_list").empty();
            arrayed_result.forEach((element, index) => {
                $("#today_factor_list").append(
                    `
                <div class="row mb-2"> 
                    <div class="col-4 col-sm-4 px-1">  <button class="btn btn-info btn-sm nasb-button"><a href="#"> ` +
                    (index + 1) +
                    `</a></button> </div>
                    <div class="col-4 col-sm-4 px-1">  <button class="btn btn-info btn-sm nasb-button"> <a href="">` +
                    element.Name +
                    `</a> </button> </div>
                    <div class="col-4 col-sm-4 px-1">  <button class="btn btn-info btn-sm nasb-button"> <a href="">` +
                    element.FactDate +
                    `</a> </button> </div>
                    <div class="col-4 col-sm-4 px-1">  <button class="btn btn-info btn-sm nasb-button" onclick="openKalaDashboard(` +
                    element.SnGood +
                    `)"><a href="#">  داشبورد کالا</a> </button> </div>
                </div>
                `
                );
            });
        },
        error: function (error) {
            alert("error in getting data");
        },
    });
}

$("#driverServicesBtn").on("click", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/getInfoForDriverService",
        data: { _token: "{{@csrf}}" },
        async: true,
        success: function (data) {
            $("#selectDriver").empty();
            data.forEach((element, index) => {
                $("#selectDriver").append(
                    `
                    <option value="` +
                    element.driverId +
                    `">` +
                    element.name +
                    ` ` +
                    element.lastName +
                    `</option>`
                );
            });
        },
        error: function (error) {
            alert("error in getting data.");
        },
    });

    if (!$(".modal.in").length) {
        $(".modal-dialog").css({
            top: 0,
            left: 0,
        });
    }
    $("#driverServicesModal").modal({
        backdrop: false,
        show: true,
    });

    $(".modal-dialog").draggable({
        handle: ".modal-header",
    });

    $("#driverServicesModal").modal("show");
});

$("#addService").on("submit", function (e) {
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#driverServiceBodyList").empty();
            data.forEach((element, index) => {
                let serviceType = "";
                if (element.serviceType == 1) {
                    serviceType = "دور";
                }
                if (element.serviceType == 2) {
                    serviceType = "متوسط";
                }
                if (element.serviceType == 3) {
                    serviceType = "نزدیک";
                }
                $("#driverServiceBodyList").append(
                    `
                        <tr onclick="setDriverServiceStuff(this,` + element.ServiceSn + `); selectTableRow(this)">
                            <th>` +
                    (index + 1) +
                    `</th>
                            <td> ` +
                    element.name +
                    ` ` +
                    element.lastName +
                    `</td>
                            <td>` +
                    serviceType +
                    `</td>
                            <td>` +
                    element.discription +
                    `</td>
                            <td>` +
                    moment(element.TimeStamp, "YYYY/M/D HH:mm:ss")
                        .locale("fa")
                        .format("YYYY/M/D HH:mm:ss") +
                    `</td>
                            <td>  <input  type="radio" name="radioBtn" value="` +
                    element.ServiceSn +
                    `"> </td>
                        </tr>`
                );
            });
            $("#driverServicesModal").modal("hide");
        },
        error: function (error) {
            console.log(error);
        },
    });

    e.preventDefault();
});

function setDriverServiceStuff(element, serviceId) {
    $("tr").removeClass("selected");
    $(element).toggleClass("selected");
    $("#serviceSn").val(serviceId);
    $("#editDriverServicesBtn").prop("disabled", false);
}

$("#editDriverServicesBtn").on("click", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/getServiceInfo",
        async: true,
        data: { _token: "{{@csrf}}", serviceId: $("#serviceSn").val() },
        success: function (data) {
            if (data[0][0].serviceType == 3) {
                $("#weakService").prop("selected", true);
            }
            if (data[0][0].serviceType == 2) {
                $("#mediumService").prop("selected", true);
            }
            if (data[0][0].serviceType == 1) {
                $("#strongService").prop("selected", true);
            }
            $("#editDiscription").val(data[0][0].discription);

            $("#editDriverSn").empty();
            data[1].forEach((element) => {
                if (data[0][0].adminId == element.driverId) {
                    $("#editDriverSn").append(
                        `<option selected value="` +
                        element.driverId +
                        `">` +
                        element.name +
                        ` ` +
                        element.lastName +
                        `</option>`
                    );
                } else {
                    $("#editDriverSn").append(
                        `<option value="` +
                        element.driverId +
                        `">` +
                        element.name +
                        ` ` +
                        element.lastName +
                        `</option>`
                    );
                }
            });

            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 0,
                    left: 0,
                });
            }
            $("#editDriverServicModal").modal({
                backdrop: false,
                show: true,
            });

            $(".modal-dialog").draggable({
                handle: ".modal-header",
            });

            $("#editDriverServicModal").modal("show");
        },
        error: function (error) {
            alert("bad");
        },
    });
});



$("#editServiceForm").on("submit", function (e) {
    $.ajax({
        method: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#driverServiceBodyList").empty();
            data.forEach((element, index) => {
                let serviceType = "";
                if (element.serviceType == 1) {
                    serviceType = "دور";
                }
                if (element.serviceType == 2) {
                    serviceType = "متوسط";
                }
                if (element.serviceType == 3) {
                    serviceType = "نزدیک";
                }
                $("#driverServiceBodyList").append(
                    `
                        <tr onclick="setDriverServiceStuff(this,` +
                    element.ServiceSn +
                    `); selectTableRow(this)">
                            <th>` +
                    (index + 1) +
                    `</th>
                            <td> ` +
                    element.name +
                    ` ` +
                    element.lastName +
                    `</td>
                            <td>` +
                    serviceType +
                    `</td>
                            <td>` +
                    element.discription +
                    `</td>
                            <td>` +
                    moment(element.TimeStamp, "YYYY/M/D HH:mm:ss")
                        .locale("fa")
                        .format("YYYY/M/D HH:mm:ss") +
                    `</td>
                            <td>  <input  type="radio" name="radioBtn" value="` +
                    element.ServiceSn +
                    `"> </td>
                        </tr>`
                );
            });
            $("#editDriverServicModal").modal("hide");
        },
        error: function (error) {
            alert("error getting data");
        },
    });
    e.preventDefault();
});

$("#getServiceSearchForm").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#driverServiceBodyList").empty();
            data.forEach((element, index) => {
                let serviceType = "";
                if (element.serviceType == 1) {
                    serviceType = "دور";
                }
                if (element.serviceType == 2) {
                    serviceType = "متوسط";
                }
                if (element.serviceType == 3) {
                    serviceType = "نزدیک";
                }
                $("#driverServiceBodyList").append(
                    `
                        <tr onclick="setDriverServiceStuff(this,` +
                    element.ServiceSn +`); selectTableRow(this)">
                            <th>` +
                    (index + 1) +
                    `</th>
                            <td> ` +
                    element.name +
                    ` ` +
                    element.lastName +
                    `</td>
                            <td>` +
                    serviceType +
                    `</td>
                            <td>` +
                    element.discription +
                    `</td>
                            <td>` +
                    element.TimeStamp +
                    `</td>
                            <td>  <input  type="radio" name="radioBtn" value="` +
                    element.ServiceSn +
                    `"> </td>
                        </tr>`
                );
            });
        },
        error: function (error) { },
    });
});

$("#orderDriverServices").on("change", function () {
    $.ajax({
        method: "get",
        url: baseUrl + "/serviceOrder",
        async: true,
        data: {
            _token: "{{@csrf}}",
            selectedBase: $("#orderDriverServices").val(),
        },
        success: function (data) {
            $("#driverServiceBodyList").empty();
            data.forEach((element, index) => {
                let serviceType = "";
                if (element.serviceType == 1) {
                    serviceType = "دور";
                }
                if (element.serviceType == 2) {
                    serviceType = "متوسط";
                }
                if (element.serviceType == 3) {
                    serviceType = "نزدیک";
                }
                $("#driverServiceBodyList").append(
                    `<tr onclick="setDriverServiceStuff(this,` + element.ServiceSn + `); selectTableRow(this)"> <td>` +
                    (index + 1) +
                    `</td>
                            <td> ` +
                    element.name +
                    ` ` +
                    element.lastName +
                    `</td>
                            <td>` +
                    serviceType +
                    `</td>
                            <td>` +
                    element.discription +
                    `</td>
                            <td>` +
                    element.TimeStamp +
                    `</td>
                            <td>  <input  type="radio" name="radioBtn" value="` +
                    element.ServiceSn +
                    `"> </td>
                        </tr>`
                );
            });
        },
        error: function (error) { },
    });
});
function getServices(flag) {
    $.ajax({
        method: "get",
        url: baseUrl + "/getDriverServices",
        async: true,
        data: {
            _token: "{{@csrf}}",
            flag: flag,
        },
        success: function (data) {
            $("#driverServiceBodyList").empty();
            data.forEach((element, index) => {
                let serviceType = "";
                if (element.serviceType == 1) {
                    serviceType = "دور";
                }
                if (element.serviceType == 2) {
                    serviceType = "متوسط";
                }
                if (element.serviceType == 3) {
                    serviceType = "نزدیک";
                }
                $("#driverServiceBodyList").append(
                    `<tr onclick="setDriverServiceStuff(this,` +
                    element.ServiceSn +
                    `); selectTableRow(this)">
                            <td>` +
                    (index + 1) +
                    `</td>
                            <td> ` +
                    element.name +
                    ` ` +
                    element.lastName +
                    `</td>
                            <td>` +
                    serviceType +
                    `</td>
                            <td>` +
                    element.discription +
                    `</td>
                            <td>` +
                    element.TimeStamp +
                    `</td>
                            <td>  <input  type="radio" name="radioBtn" value="` +
                    element.ServiceSn +
                    `"> </td>
                        </tr>`
                );
            });
        },
        error: function (error) { },
    });
}

function setUpDownHistoryStuff(element, historyID) {
    $("tr").removeClass("selected");
    $(element).toggleClass("selected");
    $.ajax({
        method: "get",
        url: baseUrl + "/getUpDownBonusInfo",
        data: {
            _token: "{{@csrf}}",
            historyID: historyID,
        },
        async: true,
        success: function (respond) {
            $("#deleteCreditBtn").prop("disabled",false);
            $("#deleteCreditBtn").val(respond[0][0].id);
            $("#editCreditBtn").prop("disabled",false);
            $("#editCreditBtn").val(respond[0][0].id);
        },
        error: function (error) {
            console.log("get up down bonus info")
        },
    });
}

$("#deleteCreditBtn").on("click", function () {
    swal({
        title: "اخطار!",
        text: "آیا می خواهید حذف شود؟",
        icon: "warning",
        buttons: true,
    }).then(function (willAdd) {
        if (willAdd) {
            $.ajax({
                method: 'get',
                url: baseUrl + '/deleteUpDownBonus',
                data: {
                    _token: "{{@csrf}}",
                    historyId: $("#deleteCreditBtn").val()
                },
                async: true,
                success: function (respond) {
                    $("#historyListBody").empty();
                    respond.forEach((element, index) => {
                        bonus = 0;
                        color = "";
                        if (element.positiveBonus > 0) {
                            bonus = element.positiveBonus;
                        } else {
                            color = "red";
                            bonus = element.negativeBonus;
                        }
                        $("#historyListBody").append(`
                              <tr onclick="setUpDownHistoryStuff(this,` + element.historyId + `); selectTableRow(this)">
                                    <td>`+ (index + 1) + `</td>
                                    <td> `+ element.TimeStamp + ` </td>
                                    <td>`+ element.adminName + `</td>
                                    <td style="color:`+ color + `">` + bonus + `</td>
                                    <td style="width:144px">`+ element.superName + `</td>
                                </tr>`);
                    });
                },
                error: function (error) { }
            });
        }
    });
});

$("#editCreditBtn").on("click", function () {
    $.ajax({
        method: 'get',
        url: baseUrl + '/getUpDownBonusInfo',
        data: {
            _token: "{{@csrf}}",
            historyID: $("#editCreditBtn").val()
        },
        async: true,
        success: function (respond) {
            $("#adminBonusTaker").empty();
            respond[1].forEach((element, index) => {
                isSelected = "";
                if (respond[0][0].adminId == element.id) {
                    isSelected = "selected";
                }
                $("#adminBonusTaker").append(`<option ` + isSelected + ` value="` + element.id + `">` + element.name + ` ` + element.lastName + `</option>`);
            });
            if (respond[0][0].positiveBonus > 0) {
                $("#pBonus").prop("disabled", false);
                $("#nBonus").prop("disabled", true);
                $("#pBonus").val(respond[0][0].positiveBonus);
                $("#nBonus").val(0);
                $("#commentBonus").val("");
                $("#commentBonus").val(respond[0][0].discription);
            } else {
                $("#pBonus").prop("disabled", true);
                $("#nBonus").prop("disabled", false);
                $("#nBonus").val(respond[0][0].negativeBonus);
                $("#pBonus").val(0);
                $("#commentBonus").val("");
                $("#commentBonus").val(respond[0][0].discription);
            }
            $("#historyId").val(respond[0][0].id);
            $("#editingCredit").modal("show");
        },
        error: function (error) {
            alert(error);
        }
    });
});

$("#editEmtyaz").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (respond) {
            $("#historyListBody").empty();
            respond.forEach((element, index) => {
                bonus = 0;
                color = "";
                if (element.positiveBonus > 0) {
                    bonus = element.positiveBonus;
                } else {
                    color = "red";
                    bonus = element.negativeBonus;
                }
                $("#historyListBody").append(`<tr onclick="setUpDownHistoryStuff(this,` + element.historyId + `); selectTableRow(this)">
                            <td>`+ (index + 1) + `</td>
                            <td> `+ element.TimeStamp + ` </td>
                            <td>`+ element.adminName + `</td>
                            <td style="color:`+ color + `">` + bonus + `</td>
                            <td style="width:144px">`+ element.superName + `</td>
                        </tr>`);
            });
        }
        , error: function (error) { }
    });
});

function getUpDownHistory(flag) {
    $.ajax({
        method: 'get',
        url: baseUrl + '/getUpDownBonusHistory',
        data: {
            _token: "{{@csrf}}",
            flag: flag
        },
        async: true,
        success: function (data) {
            $("#historyListBody").empty();
            data.forEach((element, index) => {
                bonus = 0;
                color = "";
                if (element.positiveBonus > 0) {
                    bonus = element.positiveBonus;
                } else {
                    color = "red";
                    bonus = element.negativeBonus;
                }
                $("#historyListBody").append(`
                <tr onclick="setUpDownHistoryStuff(this,` + element.historyId + `); selectTableRow(this)">
                    <td>`+ (index + 1) + `</td>
                    <td> `+ element.TimeStamp + ` </td>
                    <td>`+ element.adminName + `</td>
                    <td style="color:`+ color + `">` + bonus + `</td>
                    <td style="width:144px">`+ element.superName + `</td>
                </tr>`);
            });
        },
        error: function (error) {

        }
    });
}


$("#getHistorySearchBtn").on("click", function (e) {
    bonusType = "";
    if ($("#positiveBonusRadio").is(":checked")) {
        bonusType = "positive";
    }
    if ($("#negativeBonusRadio").is(":checked")) {
        bonusType = "negative";
    }
    if ($("#allBonusRadio").is(":checked")) {
        bonusType = "all";
    }
    firstDate = $("#firstDateReturned").val();
    secondDate = $("#secondDateReturned").val();
    e.preventDefault();
    $.ajax({
        method: 'get',
        url: baseUrl + '/getHistorySearch',
        data: {
            _token: "{{@csrf}}",
            bonusType: bonusType,
            firstDate: firstDate,
            secondDate: secondDate,
            name:$("#searchUpDownHistoryName").val(),
            orderBase:$("#orderBonusHistory").val()
        },
        success: function (data) {
            $("#historyListBody").empty();
            data.forEach((element, index) => {
                bonus = 0;
                color = "";
                if (element.positiveBonus > 0) {
                    bonus = element.positiveBonus;
                } else {
                    color = "red";
                    bonus = element.negativeBonus;
                }
                $("#historyListBody").append(`
                <tr onclick="setUpDownHistoryStuff(this,` + element.historyId + `); selectTableRow(this)">
                    <td>`+ (index + 1) + `</td>
                    <td> `+ element.TimeStampH + ` </td>
                    <td>`+ element.adminName + `</td>
                    <td style="color:`+ color + `">` + bonus + `</td>
                    <td style="width:144px">`+ element.superName + `</td>
                </tr>`);
            });
        },
        error: function (error) {
            console.log(error)
        }
    });
});

$("#assesToday").on("change", () => {
    if ($("#assesToday").is(":checked")) {
        $("#assesSecondDate").prop("disabled", true);
        $("#assesFirstDate").prop("disabled", true);
        $("#assesDoneT").css({ display: "none" });
        $(".donComment").css({ display: "none" });
        $("#assesNotDone").css({ display: "block" });
    } else {
        $("#assesSecondDate").prop("disabled", false);
        $("#assesFirstDate").prop("disabled", false);
    }
});

$("#assesPast").on("change", () => {
    if ($("#assesPast").is(":checked")) {
        $("#assesSecondDate").prop("disabled", false);
        $("#assesFirstDate").prop("disabled", false);
        $("#assesDoneT").css({ display: "none" });
        $(".donComment").css({ display: "none" });
        $("#assesNotDone").css({ display: "block" });
    }
});

$("#assesDone").on("change", () => {
    if ($("#assesDone").is(":checked")) {
        $("#assesNotDone").css({ display: "none" });
        $(".donComment").css({ display: "inline" });
        $("#assesDoneT").css({ display: "block" });
    }
});

$("#getAssesBtn").on("click", function () {
    let assesDay = "today";
    if ($("#assesToday").is(":checked")) {
        assesDay = "today";
    }
    if ($("#assesPast").is(":checked")) {
        assesDay = "past";
    }
    if ($("#assesDone").is(":checked")) {
        assesDay = "done";
    }

    let assescustomerName = $("#assescustomerName").val();
    let fromDate = $("#assesFirstDate").val();
    let toDate = $("#assesSecondDate").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/getAsses",
        async: true,
        data: {
            _token: "{{@csrf}}",
            dayAsses: assesDay,
            assescustomerName: assescustomerName,
            formatDate: "" + fromDate + "",
            toDate: "" + toDate + "",
        },
        success: function (response) {
            if (assesDay != "done") {
                $("#customersAssesBody").empty();
                response.forEach((element, index) => {
                    $("#customersAssesBody").append(
                        `
                <tr onclick="assesmentStuff(this); selectTableRow(this)">
                    <td class="no-sort">` +
                        (index + 1) +
                        `</td>
                    <td>` +
                        element.Name +
                        `</td>
                    <td>` +
                        parseInt(element.TotalPriceHDS / 10).toLocaleString(
                            "en-us"
                        ) +
                        ` تومان</td>
                    <td>` +
                        element.FactDate +
                        `</td>
                    <td>` +
                        element.FactNo +
                        `</td>
                    <td> <input class="customerList form-check-input" name="factorId" type="radio" value="` +
                        element.PSN +
                        `_` +
                        element.SerialNoHDS +
                        `"></td>
                </tr>`
                    );
                });
            } else {
                $("#customerListBodyDone").empty();
                response.forEach((element, index) => {
                    $("#customerListBodyDone").append(
                        `
                        <tr  onclick="showDoneCommentDetail(this); selectTableRow(this); selectTableRow(this)">
                            <td>` +
                        (index + 1) +
                        `</td>
                            <td>` +
                        element.Name +
                        `</td>
                            <td>` +
                        element.PhoneStr +
                        `</td>
                            <td>` +
                        moment(element.TimeStamp, "YYYY/M/D HH:mm:ss")
                            .locale("fa")
                            .format("HH:mm:ss YYYY/M/D") +
                        `</td>
                            <td>` +
                        element.AdminName +
                        ` ` +
                        element.lastName +
                        `</td>
                            <td> <input class="customerList form-check-input" name="factorId" type="radio" value="` +
                        element.PSN +
                        `_` +
                        element.SerialNoHDS +
                        `"></td>
                        </tr>`
                    );
                });
            }
        },
        error: function (error) { },
    });
});

$("#settingAndTargetRadio").on("change", () => {
    $("#targetAndSettingContent").show();
    $("#generalSettings").hide();
});
$("#generalSettingsRadio").on("change", () => {
    $("#generalSettings").show();
    $("#targetAndSettingContent").hide();
});

$("#firstManger").on("change", () => {
    $("#relatedHeadOfficer").css("display", "flex");
});
$("#firstHeadOfficer").on("change", () => {
    $("#tableGroupList").show();
    $(".forSecondHeadOfficer").hide();
});

$("#secondHeadOfficer").on("change", () => {
    $(".forSecondHeadOfficer").show();
    $("#tableGroupList").hide();
});

$("#karbarnRadioBtn").on("change", () => {
    $("#karbaranActionContainer").show();
    $("#lowlevelEmployee").hide();
});
$("#bazarYabRadioBtn").on("change", () => {
    $("#lowlevelEmployee").show();
    $("#karbaranActionContainer").hide();
});

$("#allCustomerReportRadio").on("change", () => {
    $("#staffVisitor").hide();
    $("#newCustomerList").hide();
    $("#loginTosystemReport").hide();
    $("#allCustomerStaff").show();
    $("#customerActionTable").show();
    $(".inActiveBtn").hide();
    $(".customerDashboarBtn").show();
    $("#inActiveTools").hide();
    $("#inActiveCustomerTable").hide();
    $(".referencialTools").hide();
    $(".evcuatedCustomer").hide();
    $(".referencialReport").hide();
    $(".inactiveReport").hide();
    $("#newCustomerTools").hide();
    $("#orderAll").show();
    $("#orderLogins").hide();
    $("#orderNoAdmins").hide();
    $("#orderInActives").hide();
    $("#orderReturn").hide();
    $("#customerLocationDiv").hide();
    $("#locationTools").hide();
});

$("#customerLoginReportRadio").on("change", () => {
    $("#staffVisitor").css("display", "flex");
    $("#newCustomerList").hide();
    $("#loginTosystemReport").show();
    $(".loginReport").show();
    $("#allCustomerStaff").hide();
    $("#customerActionTable").hide();
    $(".inActiveBtn").hide();
    $(".customerDashboarBtn").hide();
    $("#inActiveTools").hide();
    $("#inActiveCustomerTable").hide();
    $(".referencialTools").hide();
    $(".evcuatedCustomer").hide();
    $(".referencialReport").hide();
    $(".inactiveReport").hide();
    $("#newCustomerTools").hide();
    $("#orderAll").hide();
    $("#orderLogins").show();
    $("#orderNoAdmins").hide();
    $("#orderInActives").hide();
    $("#orderReturn").hide();
    $("#customerLocationDiv").hide();
    $("#locationTools").hide();
});

$("#customerInactiveRadio").on("change", () => {
    $("#inActiveTools").show();
    $("#newCustomerList").hide();
    $(".inactiveReport").show();
    $("#allCustomerStaff").hide();
    $(".customerDashboarBtn").hide();
    $(".inActiveBtn").show();
    $("#inActiveCustomerTable").show();
    $("#customerActionTable").hide();
    $("#loginTosystemReport").hide();
    $("#staffVisitor").hide();
    $(".evcuatedCustomer").hide();
    $(".referencialTools").hide();
    $(".referencialReport").hide();
    $(".loginReport").hide();
    $("#newCustomerTools").hide();
    $("#orderAll").hide();
    $("#orderLogins").hide();
    $("#orderNoAdmins").hide();
    $("#orderInActives").show();
    $("#orderReturn").hide();
    $("#customerLocationDiv").hide();
    $("#locationTools").hide();
});
$("#evacuatedCustomerRadio").on("change", () => {
    $(".evcuatedCustomer").show();
    $("#newCustomerList").hide();
    $("#allCustomerStaff").hide();
    $(".customerDashboarBtn").hide();
    $(".inActiveBtn").hide();
    $("#inActiveCustomerTable").hide();
    $("#customerActionTable").hide();
    $("#inActiveTools").hide();
    $("#loginTosystemReport").hide();
    $("#staffVisitor").hide();
    $(".referencialTools").hide();
    $(".referencialReport").hide();
    $(".loginReport").hide();
    $(".inactiveReport").hide();
    $("#newCustomerTools").hide();
    $("#orderAll").hide();
    $("#orderLogins").hide();
    $("#orderNoAdmins").show();
    $("#orderInActives").hide();
    $("#orderReturn").hide();
    $("#customerLocationDiv").hide();
    $("#locationTools").hide();
});

$("#referentialCustomerRadio").on("change", () => {
    $(".referencialTools").show();
    $("#newCustomerList").hide();
    $(".referencialReport").show();
    $(".evcuatedCustomer").hide();
    $("#allCustomerStaff").hide();
    $(".customerDashboarBtn").hide();
    $(".inActiveBtn").hide();
    $("#inActiveCustomerTable").hide();
    $("#customerActionTable").hide();
    $("#inActiveTools").hide();
    $("#loginTosystemReport").hide();
    $(".loginReport").hide();
    $("#staffVisitor").hide();
    $(".inactiveReport").hide();
    $("#newCustomerTools").hide();
    $("#orderAll").hide();
    $("#orderLogins").hide();
    $("#orderNoAdmins").hide();
    $("#orderInActives").hide();
    $("#orderReturn").show();
    $("#customerLocationDiv").hide();
    $("#locationTools").hide();
});

$("#newCustomerRadio").on("change", () => {
    $(".referencialTools").hide();
    $("#newCustomerList").css("display", "table");
    $("#newCustomerTools").show();
    $(".referencialReport").hide();
    $(".evcuatedCustomer").hide();
    $("#allCustomerStaff").hide();
    $(".customerDashboarBtn").hide();
    $(".inActiveBtn").hide();
    $("#inActiveCustomerTable").hide();
    $("#customerActionTable").hide();
    $("#inActiveTools").hide();
    $("#loginTosystemReport").hide();
    $(".loginReport").hide();
    $("#staffVisitor").hide();
    $(".inactiveReport").hide();
    $("#orderAll").hide();
    $("#orderLogins").hide();
    $("#orderNoAdmins").hide();
    $("#orderInActives").hide();
    $("#orderReturn").hide();
    $("#customerLocationDiv").hide();
    $("#locationTools").hide();

});

$("#customerLocationRadio").on("change", () => {
    $(".referencialTools").hide();
    $("#customerLocationDiv").css("display", "flex");
    $("#locationTools").css("display", "block");
    $("#newCustomerTools").hide();
    $("#newCustomerList").hide();
    $(".referencialReport").hide();
    $(".evcuatedCustomer").hide();
    $("#allCustomerStaff").hide();
    $(".customerDashboarBtn").hide();
    $(".inActiveBtn").hide();
    $("#inActiveCustomerTable").hide();
    $("#customerActionTable").hide();
    $("#inActiveTools").hide();
    $("#loginTosystemReport").hide();
    $(".loginReport").hide();
    $("#staffVisitor").hide();
    $(".inactiveReport").hide();
    $("#orderAll").hide();
    $("#orderLogins").hide();
    $("#orderNoAdmins").hide();
    $("#orderInActives").hide();
    $("#orderReturn").hide();
});

$(".alarmRighRdios").on("change", () => {

    $("#customerWithOutAlarm").on("change",()=>{
        $("#customerWithOutAlarmBuyOrNot").show();
        $("#unAlarmedCustomers").show();
        $("#alarmedCustomers").hide();
        $("#alarmDates").hide();
        $("#alarmBuysDates").show();
        $("#alamButtonsHistoryDiv").hide();
        $("#noAlarmButtonsHistoryDiv").show();
        $(".alarmBtn").hide();
        $("#orderUnAlarms").show();
        $("#alarmDoneButtonsHistoryDiv").hide();
    })
    $("#customerWithAlarm").on("change",()=>{
        $("#customerWithOutAlarmBuyOrNot").hide();
        $("#unAlarmedCustomers").hide();
        $("#alarmedCustomers").show();
        $("#alarmDates").show();
        $("#alarmBuysDates").hide();
        $("#alamButtonsHistoryDiv").show();
        $("#noAlarmButtonsHistoryDiv").hide();
        $(".alarmBtn").show();
        $("#orderUnAlarms").hide();
        $("#alarmDoneButtonsHistoryDiv").hide();
    })
    $("#customerDoneAlarms").on("change",()=>{
       
        $("#customerWithOutAlarmBuyOrNot").hide();
        $("#unAlarmedCustomers").hide();
        $("#alarmedCustomers").show();
        $("#alarmDates").show();
        $("#alarmBuysDates").hide();
        $("#alamButtonsHistoryDiv").hide();
        $("#noAlarmButtonsHistoryDiv").hide();
        $(".alarmBtn").hide();
        $("#orderUnAlarms").hide();
        $("#alarmDoneButtonsHistoryDiv").show();
    });
})

$("#dirverServiceRadio").on("change", () => {
    $(".driverServicesTable").show();
    $(".bargeriTable").hide();
    $("#serviceDive").show();
    $("#bottomServiceBttons").show();
    $("#orderService").show();

});
$("#bargeriRadio").on("change", () => {
    $(".driverServicesTable").hide();
    $(".bargeriTable").show();
    $("#serviceDive").hide();
    $("#bottomServiceBttons").hide();
    $("#orderService").hide();
});

$("#employeeType").on("change", function () {
    if ($("#employeeType").val() == 1) {
        $("#saleLineDive").show();
        $("#headDiv").hide();
        $("#managerDiv").hide();
        $("#employeeJobDiv").hide();
        setAccessLevel($("#employeeType").val());
    }

    if ($("#employeeType").val() == 2) {
        $("#managerDiv").show();
        $("#saleLineDive").hide();
        $("#headDiv").hide();
        $("#employeeJobDiv").hide();
        setAccessLevel($("#employeeType").val());
    }

    if ($("#employeeType").val() == 3) {
        $("#headDiv").show();
        $("#employeeJobDiv").show();
        $("#saleLineDive").hide();
        $("#managerDiv").hide();
        setAccessLevel($("#employeeType").val());
    }
});

$("#employeeTypeEdit").on("change", function () {
    setAccessLevelEdit($("#employeeTypeEdit").val());
    if ($("#employeeTypeEdit").val() == 1) {
        $("#saleLineDivEdit").show();
        $("#headDivEdit").hide();
        $("#managerDivEdit").hide();
        $("#employeeJobDivEdit").hide();
        $("#managerIdEdit").prop("selected", true);
        $("#headIdEdit").prop("selected", true);
    }

    if ($("#employeeTypeEdit").val() == 2) {
        $("#managerDivEdit").show();
        $("#saleLineDivEdit").hide();
        $("#headDivEdit").hide();
        $("#employeeJobDivEdit").hide();
        $("#headIdEdit").prop("selected", true);
    }

    if ($("#employeeTypeEdit").val() == 3) {
        $("#headDivEdit").show();
        $("#employeeJobDivEdit").show();
        $("#saleLineDivEdit").hide();
        $("#managerDivEdit").hide();
        $("#managerIdEdit").prop("selected", true);
    }
});

$("#poshtibanTypeEdit").on("change", function () {
    setAccessLevelEmployeeEdit($("#poshtibanTypeEdit").val());
});

function setAccessLevelEdit(employeeType) {
    if (employeeType == 1) {
        //گزارشات
        $("#reportED").prop("checked", true);
        $("#amalKardreportED").prop("checked", true);
        $("#managerreportED").prop("checked", true);
        $("#deletemanagerreportED").prop("checked", true);
        $("#editmanagerreportED").prop("checked", true);
        $("#seemanagerreportED").prop("checked", true);


        $("#HeadreportED").prop("checked", true);
        $("#deleteHeadreportED").prop("checked", true);
        $("#editHeadreportED").prop("checked", true);
        $("#seeHeadreportED").prop("checked", true);


        $("#poshtibanreportED").prop("checked", true);
        $("#deleteposhtibanreportED").prop("checked", true);
        $("#editposhtibanreportED").prop("checked", true);
        $("#seeposhtibanreportED").prop("checked", true);


        $("#bazaryabreportED").prop("checked", true);
        $("#deletebazaryabreportED").prop("checked", true);
        $("#editbazaryabreportED").prop("checked", true);
        $("#seebazaryabreportED").prop("checked", true);


        $("#reportDriverED").prop("checked", true);
        $("#deletereportDriverED").prop("checked", true);
        $("#editreportDriverED").prop("checked", true);
        $("#seereportDriverED").prop("checked", true);


        $("#trazEmployeeReportED").prop("checked", true);
        $("#deletetrazEmployeeReportED").prop("checked", true);
        $("#edittrazEmployeeReportED").prop("checked", true);
        $("#seetrazEmployeeReportED").prop("checked", true);


        $("#amalkardCustReportED").prop("checked", true);

        $("#customerReportED").prop("checked", true);
        $("#deletecustomerReportED").prop("checked", true);
        $("#editcustomerReportED").prop("checked", true);
        $("#seecustomerReportED").prop("checked", true);

        $("#loginCustRepED").prop("checked", true);
        $("#deleteloginCustRepED").prop("checked", true);
        $("#editloginCustRepED").prop("checked", true);
        $("#seeloginCustRepED").prop("checked", true);

        $("#inActiveCustRepED").prop("checked", true);
        $("#deleteinActiveCustRepED").prop("checked", true);
        $("#editinActiveCustRepED").prop("checked", true);
        $("#seeinActiveCustRepED").prop("checked", true);

        $("#noAdminCustRepED").prop("checked", true);
        $("#deletenoAdminCustRepED").prop("checked", true);
        $("#editnoAdminCustRepED").prop("checked", true);
        $("#seenoAdminCustRepED").prop("checked", true);

        $("#returnedCustRepED").prop("checked", true);
        $("#deletereturnedCustRepED").prop("checked", true);
        $("#editreturnedCustRepED").prop("checked", true);
        $("#seereturnedCustRepED").prop("checked", true);

        $("#goodsReportED").prop("checked", true);
        $("#salegoodsReportED").prop("checked", true);
        $("#deletesalegoodsReportED").prop("checked", true);
        $("#editsalegoodsReportED").prop("checked", true);
        $("#seesalegoodsReportED").prop("checked", true);


        $("#returnedgoodsReportED").prop("checked", true);
        $("#deletereturnedgoodsReportED").prop("checked", true);
        $("#editreturnedgoodsReportED").prop("checked", true);
        $("#seereturnedgoodsReportED").prop("checked", true);


        $("#NoExistgoodsReportED").prop("checked", true);
        $("#deleteNoExistgoodsReportED").prop("checked", true);
        $("#editNoExistgoodsReportED").prop("checked", true);
        $("#seeNoExistgoodsReportED").prop("checked", true);


        $("#nosalegoodsReportED").prop("checked", true);
        $("#deletenosalegoodsReportED").prop("checked", true);
        $("#editnosalegoodsReportED").prop("checked", true);
        $("#seenosalegoodsReportED").prop("checked", true);


        $("#returnedReportgoodsReportED").prop("checked", true);
        $("#returnedNTasReportgoodsReportED").prop("checked", true);
        $("#deletereturnedNTasReportgoodsReportED").prop("checked", true);
        $("#editreturnedNTasReportgoodsReportED").prop("checked", true);
        $("#seereturnedNTasReportgoodsReportED").prop("checked", true);


        $("#tasgoodsReprtED").prop("checked", true);
        $("#deletetasgoodsReprtED").prop("checked", true);
        $("#edittasgoodsReprtED").prop("checked", true);
        $("#seetasgoodsReprtED").prop("checked", true);


        $("#goodsbargiriReportED").prop("checked", true);
        $("#deletegoodsbargiriReportED").prop("checked", true);
        $("#editgoodsbargiriReportED").prop("checked", true);
        $("#seegoodsbargiriReportED").prop("checked", true);
        //عملیات
        $("#oppED").prop("checked", true);
        $("#oppTakhsisED").prop("checked", true);
        $("#oppManagerED").prop("checked", true);
        $("#deleteManagerOppED").prop("checked", true);
        $("#editManagerOppED").prop("checked", true);
        $("#seeManagerOppED").prop("checked", true);


        $("#oppHeadED").prop("checked", true);
        $("#deleteHeadOppED").prop("checked", true);
        $("#editHeadOppED").prop("checked", true);
        $("#seeHeadOppED").prop("checked", true);


        $("#oppBazaryabED").prop("checked", true);
        $("#deleteBazaryabOppED").prop("checked", true);
        $("#editBazaryabOppED").prop("checked", true);
        $("#seeBazaryabOppED").prop("checked", true);


        $("#oppDriverED").prop("checked", true);
        $("#oppDriverServiceED").prop("checked", true);
        $("#deleteoppDriverServiceED").prop("checked", true);
        $("#editoppDriverServiceED").prop("checked", true);
        $("#seeoppDriverServiceED").prop("checked", true);


        $("#oppBargiriED").prop("checked", true);
        $("#deleteoppBargiriED").prop("checked", true);
        $("#editoppBargiriED").prop("checked", true);
        $("#seeoppBargiriED").prop("checked", true);

        $("#oppNazarSanjiED").prop("checked", true);
        $("#todayoppNazarsanjiED").prop("checked", true);
        $("#deletetodayoppNazarsanjiED").prop("checked", true);
        $("#edittodayoppNazarsanjiED").prop("checked", true);
        $("#seetodayoppNazarsanjiED").prop("checked", true);


        $("#pastoppNazarsanjiED").prop("checked", true);
        $("#deletepastoppNazarsanjiED").prop("checked", true);
        $("#editpastoppNazarsanjiED").prop("checked", true);
        $("#seepastoppNazarsanjiED").prop("checked", true);


        $("#DoneoppNazarsanjiED").prop("checked", true);
        $("#deleteDoneoppNazarsanjiED").prop("checked", true);
        $("#editDoneoppNazarsanjiED").prop("checked", true);
        $("#seeDoneoppNazarsanjiED").prop("checked", true);


        $("#OppupDownBonusED").prop("checked", true);
        $("#AddOppupDownBonusED").prop("checked", true);
        $("#deleteAddOppupDownBonusED").prop("checked", true);
        $("#editAddOppupDownBonusED").prop("checked", true);
        $("#seeAddOppupDownBonusED").prop("checked", true);


        $("#SubOppupDownBonusED").prop("checked", true);
        $("#deleteSubOppupDownBonusED").prop("checked", true);
        $("#editSubOppupDownBonusED").prop("checked", true);
        $("#seeSubOppupDownBonusED").prop("checked", true);


        $("#oppRDED").prop("checked", true);
        $("#AddedoppRDED").prop("checked", true);
        $("#deleteAddedoppRDED").prop("checked", true);
        $("#editAddedoppRDED").prop("checked", true);
        $("#seeAddedoppRDED").prop("checked", true);


        $("#NotAddedoppRDED").prop("checked", true);
        $("#deleteNotAddedoppRDED").prop("checked", true);
        $("#editNotAddedoppRDED").prop("checked", true);
        $("#seeNotAddedoppRDED").prop("checked", true);


        $("#oppCalendarED").prop("checked", true);
        $("#oppjustCalendarED").prop("checked", true);
        $("#deleteoppjustCalendarED").prop("checked", true);
        $("#editoppjustCalendarED").prop("checked", true);
        $("#seeoppjustCalendarED").prop("checked", true);


        $("#oppCustCalendarED").prop("checked", true);
        $("#deleteoppCustCalendarED").prop("checked", true);
        $("#editoppCustCalendarED").prop("checked", true);
        $("#seeoppCustCalendarED").prop("checked", true);


        $("#alarmoppED").prop("checked", true);
        $("#allalarmoppED").prop("checked", true);
        $("#deleteallalarmoppED").prop("checked", true);
        $("#editallalarmoppED").prop("checked", true);
        $("#seeallalarmoppED").prop("checked", true);


        $("#donealarmoppED").prop("checked", true);
        $("#deletedonealarmoppED").prop("checked", true);
        $("#editdonealarmoppED").prop("checked", true);
        $("#seedonealarmoppED").prop("checked", true);


        $("#NoalarmoppED").prop("checked", true);
        $("#deleteNoalarmoppED").prop("checked", true);
        $("#editNoalarmoppED").prop("checked", true);
        $("#seeNoalarmoppED").prop("checked", true);


        $("#massageOppED").prop("checked", true);
        $("#deletemassageOppED").prop("checked", true);
        $("#editmassageOppED").prop("checked", true);
        $("#seemassageOppED").prop("checked", true);


        $("#justBargiriOppED").prop("checked", true);
        $("#deletejustBargiriOppED").prop("checked", true);
        $("#editjustBargiriOppED").prop("checked", true);
        $("#seejustBargiriOppED").prop("checked", true);
        //تعریف عناصر
        $("#declareElementED").prop("checked", true);
        $("#editdeclareElementED").prop("checked", true);
        $("#deletedeclareElementED").prop("checked", true);
        $("#seedeclareElementED").prop("checked", true);
        //اطلاعات پایه
        $("#baseInfoED").prop("checked", true);
        $("#rdSentED").prop("checked", true);
        $("#infoRdED").prop("checked", true);
        $("#deleteSentRdED").prop("checked", true);
        $("#editSentRdED").prop("checked", true);
        $("#seeSentRdED").prop("checked", true);

        $("#rdNotSentED").prop("checked", true);
        $("#deleteRdNotSentED").prop("checked", true);
        $("#editRdNotSentED").prop("checked", true);
        $("#seeRdNotSentED").prop("checked", true);

        $("#deleteProfileED").prop("checked", true);
        $("#editProfileED").prop("checked", true);
        $("#seeProfileED").prop("checked", true);
        $("#baseInfoProfileED").prop("checked", true);

        $("#addSaleLineED").prop("checked", true);
        $("#deleteSaleLineED").prop("checked", true);
        $("#editSaleLineED").prop("checked", true);
        $("#seeSaleLineED").prop("checked", true);

        $("#baseInfoSettingED").prop("checked", true);
        $("#InfoSettingAccessED").prop("checked", true);
        $("#deleteSettingAccessED").prop("checked", true);
        $("#editSettingAccessED").prop("checked", true);
        $("#seeSettingAccessED").prop("checked", true);

        $("#InfoSettingTargetED").prop("checked", true);
        $("#deleteSettingTargetED").prop("checked", true);
        $("#editSettingTargetED").prop("checked", true);
        $("#seeSettingTargetED").prop("checked", true);
    } else {
        if (employeeType == 2) {

            $("#reportED").prop("checked", true);
            $("#amalKardreportED").prop("checked", true);

            $("#managerreportED").prop("checked", false);
            $("#deletemanagerreportED").prop("checked", false);
            $("#editmanagerreportED").prop("checked", false);
            $("#seemanagerreportED").prop("checked", false);


            $("#HeadreportED").prop("checked", false);
            $("#deleteHeadreportED").prop("checked", false);
            $("#editHeadreportED").prop("checked", false);
            $("#seeHeadreportED").prop("checked", false);


            $("#poshtibanreportED").prop("checked", true);
            $("#deleteposhtibanreportED").prop("checked", true);
            $("#editposhtibanreportED").prop("checked", true);
            $("#seeposhtibanreportED").prop("checked", true);


            $("#bazaryabreportED").prop("checked", true);
            $("#deletebazaryabreportED").prop("checked", true);
            $("#editbazaryabreportED").prop("checked", true);
            $("#seebazaryabreportED").prop("checked", true);


            $("#reportDriverED").prop("checked", true);
            $("#deletereportDriverED").prop("checked", true);
            $("#editreportDriverED").prop("checked", true);
            $("#seereportDriverED").prop("checked", true);


            $("#trazEmployeeReportED").prop("checked", true);
            $("#deletetrazEmployeeReportED").prop("checked", true);
            $("#edittrazEmployeeReportED").prop("checked", true);
            $("#seetrazEmployeeReportED").prop("checked", true);


            $("#amalkardCustReportED").prop("checked", true);

            $("#customerReportED").prop("checked", true);
            $("#deletecustomerReportED").prop("checked", true);
            $("#editcustomerReportED").prop("checked", true);
            $("#seecustomerReportED").prop("checked", true);

            $("#loginCustRepED").prop("checked", true);
            $("#deleteloginCustRepED").prop("checked", true);
            $("#editloginCustRepED").prop("checked", true);
            $("#seeloginCustRepED").prop("checked", true);

            $("#inActiveCustRepED").prop("checked", true);
            $("#deleteinActiveCustRepED").prop("checked", true);
            $("#editinActiveCustRepED").prop("checked", true);
            $("#seeinActiveCustRepED").prop("checked", true);

            $("#noAdminCustRepED").prop("checked", true);
            $("#deletenoAdminCustRepED").prop("checked", true);
            $("#editnoAdminCustRepED").prop("checked", true);
            $("#seenoAdminCustRepED").prop("checked", true);

            $("#returnedCustRepED").prop("checked", true);
            $("#deletereturnedCustRepED").prop("checked", true);
            $("#editreturnedCustRepED").prop("checked", true);
            $("#seereturnedCustRepED").prop("checked", true);

            $("#goodsReportED").prop("checked", true);
            $("#salegoodsReportED").prop("checked", true);
            $("#deletesalegoodsReportED").prop("checked", true);
            $("#editsalegoodsReportED").prop("checked", true);
            $("#seesalegoodsReportED").prop("checked", true);


            $("#returnedgoodsReportED").prop("checked", true);
            $("#deletereturnedgoodsReportED").prop("checked", true);
            $("#editreturnedgoodsReportED").prop("checked", true);
            $("#seereturnedgoodsReportED").prop("checked", true);


            $("#NoExistgoodsReportED").prop("checked", true);
            $("#deleteNoExistgoodsReportED").prop("checked", true);
            $("#editNoExistgoodsReportED").prop("checked", true);
            $("#seeNoExistgoodsReportED").prop("checked", true);


            $("#nosalegoodsReportED").prop("checked", true);
            $("#deletenosalegoodsReportED").prop("checked", true);
            $("#editnosalegoodsReportED").prop("checked", true);
            $("#seenosalegoodsReportED").prop("checked", true);


            $("#returnedReportgoodsReportED").prop("checked", true);
            $("#returnedNTasReportgoodsReportED").prop("checked", true);
            $("#deletereturnedNTasReportgoodsReportED").prop("checked", true);
            $("#editreturnedNTasReportgoodsReportED").prop("checked", true);
            $("#seereturnedNTasReportgoodsReportED").prop("checked", true);


            $("#tasgoodsReprtED").prop("checked", true);
            $("#deletetasgoodsReprtED").prop("checked", true);
            $("#edittasgoodsReprtED").prop("checked", true);
            $("#seetasgoodsReprtED").prop("checked", true);


            $("#goodsbargiriReportED").prop("checked", true);
            $("#deletegoodsbargiriReportED").prop("checked", true);
            $("#editgoodsbargiriReportED").prop("checked", true);
            $("#seegoodsbargiriReportED").prop("checked", true);
            //عملیات
            $("#oppED").prop("checked", true);
            $("#oppTakhsisED").prop("checked", true);
            $("#oppManagerED").prop("checked", false);
            $("#deleteManagerOppED").prop("checked", false);
            $("#editManagerOppED").prop("checked", false);
            $("#seeManagerOppED").prop("checked", false);


            $("#oppHeadED").prop("checked", false);
            $("#deleteHeadOppED").prop("checked", false);
            $("#editHeadOppED").prop("checked", false);
            $("#seeHeadOppED").prop("checked", false);


            $("#oppBazaryabED").prop("checked", true);
            $("#deleteBazaryabOppED").prop("checked", true);
            $("#editBazaryabOppED").prop("checked", true);
            $("#seeBazaryabOppED").prop("checked", true);


            $("#oppDriverED").prop("checked", true);
            $("#oppDriverServiceED").prop("checked", true);
            $("#deleteoppDriverServiceED").prop("checked", true);
            $("#editoppDriverServiceED").prop("checked", true);
            $("#seeoppDriverServiceED").prop("checked", true);


            $("#oppBargiriED").prop("checked", false);
            $("#deleteoppBargiriED").prop("checked", false);
            $("#editoppBargiriED").prop("checked", false);
            $("#seeoppBargiriED").prop("checked", false);

            $("#oppNazarSanjiED").prop("checked", true);
            $("#todayoppNazarsanjiED").prop("checked", true);
            $("#deletetodayoppNazarsanjiED").prop("checked", true);
            $("#edittodayoppNazarsanjiED").prop("checked", true);
            $("#seetodayoppNazarsanjiED").prop("checked", true);


            $("#pastoppNazarsanjiED").prop("checked", true);
            $("#deletepastoppNazarsanjiED").prop("checked", true);
            $("#editpastoppNazarsanjiED").prop("checked", true);
            $("#seepastoppNazarsanjiED").prop("checked", true);


            $("#DoneoppNazarsanjiED").prop("checked", true);
            $("#deleteDoneoppNazarsanjiED").prop("checked", true);
            $("#editDoneoppNazarsanjiED").prop("checked", true);
            $("#seeDoneoppNazarsanjiED").prop("checked", true);


            $("#OppupDownBonusED").prop("checked", false);
            $("#AddOppupDownBonusED").prop("checked", false);
            $("#deleteAddOppupDownBonusED").prop("checked", false);
            $("#editAddOppupDownBonusED").prop("checked", false);
            $("#seeAddOppupDownBonusED").prop("checked", false);


            $("#SubOppupDownBonusED").prop("checked", false);
            $("#deleteSubOppupDownBonusED").prop("checked", false);
            $("#editSubOppupDownBonusED").prop("checked", false);
            $("#seeSubOppupDownBonusED").prop("checked", false);


            $("#oppRDED").prop("checked", false);
            $("#AddedoppRDED").prop("checked", false);
            $("#deleteAddedoppRDED").prop("checked", false);
            $("#editAddedoppRDED").prop("checked", false);
            $("#seeAddedoppRDED").prop("checked", false);


            $("#NotAddedoppRDED").prop("checked", false);
            $("#deleteNotAddedoppRDED").prop("checked", false);
            $("#editNotAddedoppRDED").prop("checked", false);
            $("#seeNotAddedoppRDED").prop("checked", false);


            $("#oppCalendarED").prop("checked", true);
            $("#oppjustCalendarED").prop("checked", false);
            $("#deleteoppjustCalendarED").prop("checked", false);
            $("#editoppjustCalendarED").prop("checked", false);
            $("#seeoppjustCalendarED").prop("checked", false);


            $("#oppCustCalendarED").prop("checked", true);
            $("#deleteoppCustCalendarED").prop("checked", true);
            $("#editoppCustCalendarED").prop("checked", true);
            $("#seeoppCustCalendarED").prop("checked", true);


            $("#alarmoppED").prop("checked", true);
            $("#allalarmoppED").prop("checked", true);
            $("#deleteallalarmoppED").prop("checked", true);
            $("#editallalarmoppED").prop("checked", true);
            $("#seeallalarmoppED").prop("checked", true);


            $("#donealarmoppED").prop("checked", true);
            $("#deletedonealarmoppED").prop("checked", true);
            $("#editdonealarmoppED").prop("checked", true);
            $("#seedonealarmoppED").prop("checked", true);


            $("#NoalarmoppED").prop("checked", true);
            $("#deleteNoalarmoppED").prop("checked", true);
            $("#editNoalarmoppED").prop("checked", true);
            $("#seeNoalarmoppED").prop("checked", true);


            $("#massageOppED").prop("checked", true);
            $("#deletemassageOppED").prop("checked", true);
            $("#editmassageOppED").prop("checked", true);
            $("#seemassageOppED").prop("checked", true);


            $("#justBargiriOppED").prop("checked", false);
            $("#deletejustBargiriOppED").prop("checked", false);
            $("#editjustBargiriOppED").prop("checked", false);
            $("#seejustBargiriOppED").prop("checked", false);
            //تعریف عناصر
            $("#declareElementED").prop("checked", false);
            $("#editdeclareElementED").prop("checked", false);
            $("#deletedeclareElementED").prop("checked", false);
            $("#seedeclareElementED").prop("checked", false);
            //اطلاعات پایه
            $("#baseInfoED").prop("checked", true);
            $("#rdSentED").prop("checked", false);
            $("#infoRdED").prop("checked", false);
            $("#deleteSentRdED").prop("checked", false);
            $("#editSentRdED").prop("checked", false);
            $("#seeSentRdED").prop("checked", false);

            $("#rdNotSentED").prop("checked", false);
            $("#deleteRdNotSentED").prop("checked", false);
            $("#editRdNotSentED").prop("checked", false);
            $("#seeRdNotSentED").prop("checked", false);

            $("#deleteProfileED").prop("checked", true);
            $("#editProfileED").prop("checked", true);
            $("#seeProfileED").prop("checked", true);
            $("#baseInfoProfileED").prop("checked", true);

            $("#addSaleLineED").prop("checked", false);
            $("#deleteSaleLineED").prop("checked", false);
            $("#editSaleLineED").prop("checked", false);
            $("#seeSaleLineED").prop("checked", false);

            $("#baseInfoSettingED").prop("checked", false);
            $("#InfoSettingAccessED").prop("checked", false);
            $("#deleteSettingAccessED").prop("checked", false);
            $("#editSettingAccessED").prop("checked", false);
            $("#seeSettingAccessED").prop("checked", false);

            $("#InfoSettingTargetED").prop("checked", false);
            $("#deleteSettingTargetED").prop("checked", false);
            $("#editSettingTargetED").prop("checked", false);
            $("#seeSettingTargetED").prop("checked", false);
        } else {
            $("#reportED").prop("checked", false);
            $("#amalKardreportED").prop("checked", false);
            $("#managerreportED").prop("checked", false);
            $("#deletemanagerreportED").prop("checked", false);
            $("#editmanagerreportED").prop("checked", false);
            $("#seemanagerreportED").prop("checked", false);


            $("#HeadreportED").prop("checked", false);
            $("#deleteHeadreportED").prop("checked", false);
            $("#editHeadreportED").prop("checked", false);
            $("#seeHeadreportED").prop("checked", false);


            $("#poshtibanreportED").prop("checked", false);
            $("#deleteposhtibanreportED").prop("checked", false);
            $("#editposhtibanreportED").prop("checked", false);
            $("#seeposhtibanreportED").prop("checked", false);


            $("#bazaryabreportED").prop("checked", false);
            $("#deletebazaryabreportED").prop("checked", false);
            $("#editbazaryabreportED").prop("checked", false);
            $("#seebazaryabreportED").prop("checked", false);


            $("#reportDriverED").prop("checked", false);
            $("#deletereportDriverED").prop("checked", false);
            $("#editreportDriverED").prop("checked", false);
            $("#seereportDriverED").prop("checked", false);


            $("#trazEmployeeReportED").prop("checked", false);
            $("#deletetrazEmployeeReportED").prop("checked", false);
            $("#edittrazEmployeeReportED").prop("checked", false);
            $("#seetrazEmployeeReportED").prop("checked", false);


            $("#amalkardCustReportED").prop("checked", false);

            $("#customerReportED").prop("checked", false);
            $("#deletecustomerReportED").prop("checked", false);
            $("#editcustomerReportED").prop("checked", false);
            $("#seecustomerReportED").prop("checked", false);

            $("#loginCustRepED").prop("checked", false);
            $("#deleteloginCustRepED").prop("checked", false);
            $("#editloginCustRepED").prop("checked", false);
            $("#seeloginCustRepED").prop("checked", false);

            $("#inActiveCustRepED").prop("checked", false);
            $("#deleteinActiveCustRepED").prop("checked", false);
            $("#editinActiveCustRepED").prop("checked", false);
            $("#seeinActiveCustRepED").prop("checked", false);

            $("#noAdminCustRepED").prop("checked", false);
            $("#deletenoAdminCustRepED").prop("checked", false);
            $("#editnoAdminCustRepED").prop("checked", false);
            $("#seenoAdminCustRepED").prop("checked", false);

            $("#returnedCustRepED").prop("checked", false);
            $("#deletereturnedCustRepED").prop("checked", false);
            $("#editreturnedCustRepED").prop("checked", false);
            $("#seereturnedCustRepED").prop("checked", false);

            $("#goodsReportED").prop("checked", false);
            $("#salegoodsReportED").prop("checked", false);
            $("#deletesalegoodsReportED").prop("checked", false);
            $("#editsalegoodsReportED").prop("checked", false);
            $("#seesalegoodsReportED").prop("checked", false);


            $("#returnedgoodsReportED").prop("checked", false);
            $("#deletereturnedgoodsReportED").prop("checked", false);
            $("#editreturnedgoodsReportED").prop("checked", false);
            $("#seereturnedgoodsReportED").prop("checked", false);


            $("#NoExistgoodsReportED").prop("checked", false);
            $("#deleteNoExistgoodsReportED").prop("checked", false);
            $("#editNoExistgoodsReportED").prop("checked", false);
            $("#seeNoExistgoodsReportED").prop("checked", false);


            $("#nosalegoodsReportED").prop("checked", false);
            $("#deletenosalegoodsReportED").prop("checked", false);
            $("#editnosalegoodsReportED").prop("checked", false);
            $("#seenosalegoodsReportED").prop("checked", false);

            $("#returnedReportgoodsReportED").prop("checked", false);
            $("#returnedNTasReportgoodsReportED").prop("checked", false);
            $("#deletereturnedNTasReportgoodsReportED").prop("checked", false);
            $("#editreturnedNTasReportgoodsReportED").prop("checked", false);
            $("#seereturnedNTasReportgoodsReportED").prop("checked", false);


            $("#tasgoodsReprtED").prop("checked", false);
            $("#deletetasgoodsReprtED").prop("checked", false);
            $("#edittasgoodsReprtED").prop("checked", false);
            $("#seetasgoodsReprtED").prop("checked", false);


            $("#goodsbargiriReportED").prop("checked", false);
            $("#deletegoodsbargiriReportED").prop("checked", false);
            $("#editgoodsbargiriReportED").prop("checked", false);
            $("#seegoodsbargiriReportED").prop("checked", false);
            //عملیات
            $("#oppED").prop("checked", false);
            $("#oppTakhsisED").prop("checked", false);
            $("#oppManagerED").prop("checked", false);
            $("#deleteManagerOppED").prop("checked", false);
            $("#editManagerOppED").prop("checked", false);
            $("#seeManagerOppED").prop("checked", false);


            $("#oppHeadED").prop("checked", false);
            $("#deleteHeadOppED").prop("checked", false);
            $("#editHeadOppED").prop("checked", false);
            $("#seeHeadOppED").prop("checked", false);


            $("#oppBazaryabED").prop("checked", false);
            $("#deleteBazaryabOppED").prop("checked", false);
            $("#editBazaryabOppED").prop("checked", false);
            $("#seeBazaryabOppED").prop("checked", false);


            $("#oppDriverED").prop("checked", false);
            $("#oppDriverServiceED").prop("checked", false);
            $("#deleteoppDriverServiceED").prop("checked", false);
            $("#editoppDriverServiceED").prop("checked", false);
            $("#seeoppDriverServiceED").prop("checked", false);


            $("#oppBargiriED").prop("checked", false);
            $("#deleteoppBargiriED").prop("checked", false);
            $("#editoppBargiriED").prop("checked", false);
            $("#seeoppBargiriED").prop("checked", false);

            $("#oppNazarSanjiED").prop("checked", false);
            $("#todayoppNazarsanjiED").prop("checked", false);
            $("#deletetodayoppNazarsanjiED").prop("checked", false);
            $("#edittodayoppNazarsanjiED").prop("checked", false);
            $("#seetodayoppNazarsanjiED").prop("checked", false);


            $("#pastoppNazarsanjiED").prop("checked", false);
            $("#deletepastoppNazarsanjiED").prop("checked", false);
            $("#editpastoppNazarsanjiED").prop("checked", false);
            $("#seepastoppNazarsanjiED").prop("checked", false);


            $("#DoneoppNazarsanjiED").prop("checked", false);
            $("#deleteDoneoppNazarsanjiED").prop("checked", false);
            $("#editDoneoppNazarsanjiED").prop("checked", false);
            $("#seeDoneoppNazarsanjiED").prop("checked", false);


            $("#OppupDownBonusED").prop("checked", false);
            $("#AddOppupDownBonusED").prop("checked", false);
            $("#deleteAddOppupDownBonusED").prop("checked", false);
            $("#editAddOppupDownBonusED").prop("checked", false);
            $("#seeAddOppupDownBonusED").prop("checked", false);


            $("#SubOppupDownBonusED").prop("checked", false);
            $("#deleteSubOppupDownBonusED").prop("checked", false);
            $("#editSubOppupDownBonusED").prop("checked", false);
            $("#seeSubOppupDownBonusED").prop("checked", false);


            $("#oppRDED").prop("checked", false);
            $("#AddedoppRDED").prop("checked", false);
            $("#deleteAddedoppRDED").prop("checked", false);
            $("#editAddedoppRDED").prop("checked", false);
            $("#seeAddedoppRDED").prop("checked", false);


            $("#NotAddedoppRDED").prop("checked", false);
            $("#deleteNotAddedoppRDED").prop("checked", false);
            $("#editNotAddedoppRDED").prop("checked", false);
            $("#seeNotAddedoppRDED").prop("checked", false);


            $("#oppCalendarED").prop("checked", false);
            $("#oppjustCalendarED").prop("checked", false);
            $("#deleteoppjustCalendarED").prop("checked", false);
            $("#editoppjustCalendarED").prop("checked", false);
            $("#seeoppjustCalendarED").prop("checked", false);


            $("#oppCustCalendarED").prop("checked", false);
            $("#deleteoppCustCalendarED").prop("checked", false);
            $("#editoppCustCalendarED").prop("checked", false);
            $("#seeoppCustCalendarED").prop("checked", false);


            $("#alarmoppED").prop("checked", false);
            $("#allalarmoppED").prop("checked", false);
            $("#deleteallalarmoppED").prop("checked", false);
            $("#editallalarmoppED").prop("checked", false);
            $("#seeallalarmoppED").prop("checked", false);


            $("#donealarmoppED").prop("checked", false);
            $("#deletedonealarmoppED").prop("checked", false);
            $("#editdonealarmoppED").prop("checked", false);
            $("#seedonealarmoppED").prop("checked", false);


            $("#NoalarmoppED").prop("checked", false);
            $("#deleteNoalarmoppED").prop("checked", false);
            $("#editNoalarmoppED").prop("checked", false);
            $("#seeNoalarmoppED").prop("checked", false);


            $("#massageOppED").prop("checked", false);
            $("#deletemassageOppED").prop("checked", false);
            $("#editmassageOppED").prop("checked", false);
            $("#seemassageOppED").prop("checked", false);


            $("#justBargiriOppED").prop("checked", false);
            $("#deletejustBargiriOppED").prop("checked", false);
            $("#editjustBargiriOppED").prop("checked", false);
            $("#seejustBargiriOppED").prop("checked", false);
            //تعریف عناصر
            $("#declareElementED").prop("checked", false);
            $("#editdeclareElementED").prop("checked", false);
            $("#deletedeclareElementED").prop("checked", false);
            $("#seedeclareElementED").prop("checked", false);
            //اطلاعات پایه
            $("#baseInfoED").prop("checked", false);
            $("#rdSentED").prop("checked", false);
            $("#infoRdED").prop("checked", false);
            $("#deleteSentRdED").prop("checked", false);
            $("#editSentRdED").prop("checked", false);
            $("#seeSentRdED").prop("checked", false);

            $("#rdNotSentED").prop("checked", false);
            $("#deleteRdNotSentED").prop("checked", false);
            $("#editRdNotSentED").prop("checked", false);
            $("#seeRdNotSentED").prop("checked", false);

            $("#deleteProfileED").prop("checked", false);
            $("#editProfileED").prop("checked", false);
            $("#seeProfileED").prop("checked", false);
            $("#baseInfoProfileED").prop("checked", false);

            $("#addSaleLineED").prop("checked", false);
            $("#deleteSaleLineED").prop("checked", false);
            $("#editSaleLineED").prop("checked", false);
            $("#seeSaleLineED").prop("checked", false);

            $("#baseInfoSettingED").prop("checked", false);
            $("#InfoSettingAccessED").prop("checked", false);
            $("#deleteSettingAccessED").prop("checked", false);
            $("#editSettingAccessED").prop("checked", false);
            $("#seeSettingAccessED").prop("checked", false);

            $("#InfoSettingTargetED").prop("checked", false);
            $("#deleteSettingTargetED").prop("checked", false);
            $("#editSettingTargetED").prop("checked", false);
            $("#seeSettingTargetED").prop("checked", false);
        }
    }
}

function setAccessLevelEmployeeEdit(employeeType) {
    if (employeeType == 4) {
        //گزارشات
        $("#reportED").prop("checked", false);
        $("#amalKardreportED").prop("checked", false);

        $("#managerreportED").prop("checked", false);
        $("#deletemanagerreportED").prop("checked", false);
        $("#editmanagerreportED").prop("checked", false);
        $("#seemanagerreportED").prop("checked", false);


        $("#HeadreportED").prop("checked", false);
        $("#deleteHeadreportED").prop("checked", false);
        $("#editHeadreportED").prop("checked", false);
        $("#seeHeadreportED").prop("checked", false);


        $("#poshtibanreportED").prop("checked", false);
        $("#deleteposhtibanreportED").prop("checked", false);
        $("#editposhtibanreportED").prop("checked", false);
        $("#seeposhtibanreportED").prop("checked", false);


        $("#bazaryabreportED").prop("checked", false);
        $("#deletebazaryabreportED").prop("checked", false);
        $("#editbazaryabreportED").prop("checked", false);
        $("#seebazaryabreportED").prop("checked", false);


        $("#reportDriverED").prop("checked", false);
        $("#deletereportDriverED").prop("checked", false);
        $("#editreportDriverED").prop("checked", false);
        $("#seereportDriverED").prop("checked", false);


        $("#trazEmployeeReportED").prop("checked", false);
        $("#deletetrazEmployeeReportED").prop("checked", false);
        $("#edittrazEmployeeReportED").prop("checked", false);
        $("#seetrazEmployeeReportED").prop("checked", false);


        $("#amalkardCustReportED").prop("checked", false);

        $("#customerReportED").prop("checked", false);
        $("#deletecustomerReportED").prop("checked", false);
        $("#editcustomerReportED").prop("checked", false);
        $("#seecustomerReportED").prop("checked", false);

        $("#loginCustRepED").prop("checked", false);
        $("#deleteloginCustRepED").prop("checked", false);
        $("#editloginCustRepED").prop("checked", false);
        $("#seeloginCustRepED").prop("checked", false);

        $("#inActiveCustRepED").prop("checked", false);
        $("#deleteinActiveCustRepED").prop("checked", false);
        $("#editinActiveCustRepED").prop("checked", false);
        $("#seeinActiveCustRepED").prop("checked", false);

        $("#noAdminCustRepED").prop("checked", false);
        $("#deletenoAdminCustRepED").prop("checked", false);
        $("#editnoAdminCustRepED").prop("checked", false);
        $("#seenoAdminCustRepED").prop("checked", false);

        $("#returnedCustRepED").prop("checked", false);
        $("#deletereturnedCustRepED").prop("checked", false);
        $("#editreturnedCustRepED").prop("checked", false);
        $("#seereturnedCustRepED").prop("checked", false);

        $("#goodsReportED").prop("checked", false);
        $("#salegoodsReportED").prop("checked", false);
        $("#deletesalegoodsReportED").prop("checked", false);
        $("#editsalegoodsReportED").prop("checked", false);
        $("#seesalegoodsReportED").prop("checked", false);


        $("#returnedgoodsReportED").prop("checked", false);
        $("#deletereturnedgoodsReportED").prop("checked", false);
        $("#editreturnedgoodsReportED").prop("checked", false);
        $("#seereturnedgoodsReportED").prop("checked", false);


        $("#NoExistgoodsReportED").prop("checked", false);
        $("#deleteNoExistgoodsReportED").prop("checked", false);
        $("#editNoExistgoodsReportED").prop("checked", false);
        $("#seeNoExistgoodsReportED").prop("checked", false);


        $("#nosalegoodsReportED").prop("checked", false);
        $("#deletenosalegoodsReportED").prop("checked", false);
        $("#editnosalegoodsReportED").prop("checked", false);
        $("#seenosalegoodsReportED").prop("checked", false);


        $("#returnedReportgoodsReportED").prop("checked", false);
        $("#returnedNTasReportgoodsReportED").prop("checked", false);
        $("#deletereturnedNTasReportgoodsReportED").prop("checked", false);
        $("#editreturnedNTasReportgoodsReportED").prop("checked", false);
        $("#seereturnedNTasReportgoodsReportED").prop("checked", false);


        $("#tasgoodsReprtED").prop("checked", false);
        $("#deletetasgoodsReprtED").prop("checked", false);
        $("#edittasgoodsReprtED").prop("checked", false);
        $("#seetasgoodsReprtED").prop("checked", false);


        $("#goodsbargiriReportED").prop("checked", false);
        $("#deletegoodsbargiriReportED").prop("checked", false);
        $("#editgoodsbargiriReportED").prop("checked", false);
        $("#seegoodsbargiriReportED").prop("checked", false);
        //عملیات
        $("#oppED").prop("checked", true);
        $("#oppTakhsisED").prop("checked", false);
        $("#oppManagerED").prop("checked", false);
        $("#deleteManagerOppED").prop("checked", false);
        $("#editManagerOppED").prop("checked", false);
        $("#seeManagerOppED").prop("checked", false);


        $("#oppHeadED").prop("checked", false);
        $("#deleteHeadOppED").prop("checked", false);
        $("#editHeadOppED").prop("checked", false);
        $("#seeHeadOppED").prop("checked", false);


        $("#oppBazaryabED").prop("checked", false);
        $("#deleteBazaryabOppED").prop("checked", false);
        $("#editBazaryabOppED").prop("checked", false);
        $("#seeBazaryabOppED").prop("checked", false);


        $("#oppDriverED").prop("checked", false);
        $("#oppDriverServiceED").prop("checked", false);
        $("#deleteoppDriverServiceED").prop("checked", false);
        $("#editoppDriverServiceED").prop("checked", false);
        $("#seeoppDriverServiceED").prop("checked", false);


        $("#oppBargiriED").prop("checked", false);
        $("#deleteoppBargiriED").prop("checked", false);
        $("#editoppBargiriED").prop("checked", false);
        $("#seeoppBargiriED").prop("checked", false);

        $("#oppNazarSanjiED").prop("checked", false);
        $("#todayoppNazarsanjiED").prop("checked", false);
        $("#deletetodayoppNazarsanjiED").prop("checked", false);
        $("#edittodayoppNazarsanjiED").prop("checked", false);
        $("#seetodayoppNazarsanjiED").prop("checked", false);


        $("#pastoppNazarsanjiED").prop("checked", false);
        $("#deletepastoppNazarsanjiED").prop("checked", false);
        $("#editpastoppNazarsanjiED").prop("checked", false);
        $("#seepastoppNazarsanjiED").prop("checked", false);


        $("#DoneoppNazarsanjiED").prop("checked", false);
        $("#deleteDoneoppNazarsanjiED").prop("checked", false);
        $("#editDoneoppNazarsanjiED").prop("checked", false);
        $("#seeDoneoppNazarsanjiED").prop("checked", false);


        $("#OppupDownBonusED").prop("checked", false);
        $("#AddOppupDownBonusED").prop("checked", false);
        $("#deleteAddOppupDownBonusED").prop("checked", false);
        $("#editAddOppupDownBonusED").prop("checked", false);
        $("#seeAddOppupDownBonusED").prop("checked", false);


        $("#SubOppupDownBonusED").prop("checked", false);
        $("#deleteSubOppupDownBonusED").prop("checked", false);
        $("#editSubOppupDownBonusED").prop("checked", false);
        $("#seeSubOppupDownBonusED").prop("checked", false);


        $("#oppRDED").prop("checked", false);
        $("#AddedoppRDED").prop("checked", false);
        $("#deleteAddedoppRDED").prop("checked", false);
        $("#editAddedoppRDED").prop("checked", false);
        $("#seeAddedoppRDED").prop("checked", false);


        $("#NotAddedoppRDED").prop("checked", false);
        $("#deleteNotAddedoppRDED").prop("checked", false);
        $("#editNotAddedoppRDED").prop("checked", false);
        $("#seeNotAddedoppRDED").prop("checked", false);


        $("#oppCalendarED").prop("checked", false);
        $("#oppjustCalendarED").prop("checked", false);
        $("#deleteoppjustCalendarED").prop("checked", false);
        $("#editoppjustCalendarED").prop("checked", false);
        $("#seeoppjustCalendarED").prop("checked", false);


        $("#oppCustCalendarED").prop("checked", false);
        $("#deleteoppCustCalendarED").prop("checked", false);
        $("#editoppCustCalendarED").prop("checked", false);
        $("#seeoppCustCalendarED").prop("checked", false);


        $("#alarmoppED").prop("checked", false);
        $("#allalarmoppED").prop("checked", false);
        $("#deleteallalarmoppED").prop("checked", false);
        $("#editallalarmoppED").prop("checked", false);
        $("#seeallalarmoppED").prop("checked", false);


        $("#donealarmoppED").prop("checked", false);
        $("#deletedonealarmoppED").prop("checked", false);
        $("#editdonealarmoppED").prop("checked", false);
        $("#seedonealarmoppED").prop("checked", false);


        $("#NoalarmoppED").prop("checked", false);
        $("#deleteNoalarmoppED").prop("checked", false);
        $("#editNoalarmoppED").prop("checked", false);
        $("#seeNoalarmoppED").prop("checked", false);


        $("#massageOppED").prop("checked", true);
        $("#deletemassageOppED").prop("checked", true);
        $("#editmassageOppED").prop("checked", true);
        $("#seemassageOppED").prop("checked", true);


        $("#justBargiriOppED").prop("checked", true);
        $("#deletejustBargiriOppED").prop("checked", true);
        $("#editjustBargiriOppED").prop("checked", true);
        $("#seejustBargiriOppED").prop("checked", true);
        //تعریف عناصر
        $("#declareElementED").prop("checked", false);
        $("#editdeclareElementED").prop("checked", false);
        $("#deletedeclareElementED").prop("checked", false);
        $("#seedeclareElementED").prop("checked", false);
        //اطلاعات پایه
        $("#baseInfoED").prop("checked", true);
        $("#rdSentED").prop("checked", false);
        $("#infoRdED").prop("checked", false);
        $("#deleteSentRdED").prop("checked", false);
        $("#editSentRdED").prop("checked", false);
        $("#seeSentRdED").prop("checked", false);

        $("#rdNotSentED").prop("checked", false);
        $("#deleteRdNotSentED").prop("checked", false);
        $("#editRdNotSentED").prop("checked", false);
        $("#seeRdNotSentED").prop("checked", false);

        $("#deleteProfileED").prop("checked", true);
        $("#editProfileED").prop("checked", true);
        $("#seeProfileED").prop("checked", true);
        $("#baseInfoProfileED").prop("checked", true);

        $("#addSaleLineED").prop("checked", false);
        $("#deleteSaleLineED").prop("checked", false);
        $("#editSaleLineED").prop("checked", false);
        $("#seeSaleLineED").prop("checked", false);

        $("#baseInfoSettingED").prop("checked", false);
        $("#InfoSettingAccessED").prop("checked", false);
        $("#deleteSettingAccessED").prop("checked", false);
        $("#editSettingAccessED").prop("checked", false);
        $("#seeSettingAccessED").prop("checked", false);

        $("#InfoSettingTargetED").prop("checked", false);
        $("#deleteSettingTargetED").prop("checked", false);
        $("#editSettingTargetED").prop("checked", false);
        $("#seeSettingTargetED").prop("checked", false);
    } else {
        if (employeeType == 2) {

            $("#reportED").prop("checked", true);
            $("#amalKardreportED").prop("checked", false);

            $("#managerreportED").prop("checked", false);
            $("#deletemanagerreportED").prop("checked", false);
            $("#editmanagerreportED").prop("checked", false);
            $("#seemanagerreportED").prop("checked", false);


            $("#HeadreportED").prop("checked", false);
            $("#deleteHeadreportED").prop("checked", false);
            $("#editHeadreportED").prop("checked", false);
            $("#seeHeadreportED").prop("checked", false);


            $("#poshtibanreportED").prop("checked", false);
            $("#deleteposhtibanreportED").prop("checked", false);
            $("#editposhtibanreportED").prop("checked", false);
            $("#seeposhtibanreportED").prop("checked", false);


            $("#bazaryabreportED").prop("checked", false);
            $("#deletebazaryabreportED").prop("checked", false);
            $("#editbazaryabreportED").prop("checked", false);
            $("#seebazaryabreportED").prop("checked", false);


            $("#reportDriverED").prop("checked", false);
            $("#deletereportDriverED").prop("checked", false);
            $("#editreportDriverED").prop("checked", false);
            $("#seereportDriverED").prop("checked", false);


            $("#trazEmployeeReportED").prop("checked", false);
            $("#deletetrazEmployeeReportED").prop("checked", false);
            $("#edittrazEmployeeReportED").prop("checked", false);
            $("#seetrazEmployeeReportED").prop("checked", false);


            $("#amalkardCustReportED").prop("checked", true);

            $("#customerReportED").prop("checked", true);
            $("#deletecustomerReportED").prop("checked", true);
            $("#editcustomerReportED").prop("checked", true);
            $("#seecustomerReportED").prop("checked", true);

            $("#loginCustRepED").prop("checked", true);
            $("#deleteloginCustRepED").prop("checked", true);
            $("#editloginCustRepED").prop("checked", true);
            $("#seeloginCustRepED").prop("checked", true);

            $("#inActiveCustRepED").prop("checked", true);
            $("#deleteinActiveCustRepED").prop("checked", true);
            $("#editinActiveCustRepED").prop("checked", true);
            $("#seeinActiveCustRepED").prop("checked", true);

            $("#noAdminCustRepED").prop("checked", true);
            $("#deletenoAdminCustRepED").prop("checked", true);
            $("#editnoAdminCustRepED").prop("checked", true);
            $("#seenoAdminCustRepED").prop("checked", true);

            $("#returnedCustRepED").prop("checked", false);
            $("#deletereturnedCustRepED").prop("checked", false);
            $("#editreturnedCustRepED").prop("checked", false);
            $("#seereturnedCustRepED").prop("checked", false);

            $("#goodsReportED").prop("checked", true);
            $("#salegoodsReportED").prop("checked", true);
            $("#deletesalegoodsReportED").prop("checked", true);
            $("#editsalegoodsReportED").prop("checked", true);
            $("#seesalegoodsReportED").prop("checked", true);


            $("#returnedgoodsReportED").prop("checked", true);
            $("#deletereturnedgoodsReportED").prop("checked", true);
            $("#editreturnedgoodsReportED").prop("checked", true);
            $("#seereturnedgoodsReportED").prop("checked", true);


            $("#NoExistgoodsReportED").prop("checked", true);
            $("#deleteNoExistgoodsReportED").prop("checked", true);
            $("#editNoExistgoodsReportED").prop("checked", true);
            $("#seeNoExistgoodsReportED").prop("checked", true);


            $("#nosalegoodsReportED").prop("checked", true);
            $("#deletenosalegoodsReportED").prop("checked", true);
            $("#editnosalegoodsReportED").prop("checked", true);
            $("#seenosalegoodsReportED").prop("checked", true);


            $("#returnedReportgoodsReportED").prop("checked", true);
            $("#returnedNTasReportgoodsReportED").prop("checked", true);
            $("#deletereturnedNTasReportgoodsReportED").prop("checked", true);
            $("#editreturnedNTasReportgoodsReportED").prop("checked", true);
            $("#seereturnedNTasReportgoodsReportED").prop("checked", true);


            $("#tasgoodsReprtED").prop("checked", true);
            $("#deletetasgoodsReprtED").prop("checked", true);
            $("#edittasgoodsReprtED").prop("checked", true);
            $("#seetasgoodsReprtED").prop("checked", true);


            $("#goodsbargiriReportED").prop("checked", false);
            $("#deletegoodsbargiriReportED").prop("checked", false);
            $("#editgoodsbargiriReportED").prop("checked", false);
            $("#seegoodsbargiriReportED").prop("checked", false);
            //عملیات
            $("#oppED").prop("checked", true);
            $("#oppTakhsisED").prop("checked", false);
            $("#oppManagerED").prop("checked", false);
            $("#deleteManagerOppED").prop("checked", false);
            $("#editManagerOppED").prop("checked", false);
            $("#seeManagerOppED").prop("checked", false);


            $("#oppHeadED").prop("checked", false);
            $("#deleteHeadOppED").prop("checked", false);
            $("#editHeadOppED").prop("checked", false);
            $("#seeHeadOppED").prop("checked", false);


            $("#oppBazaryabED").prop("checked", false);
            $("#deleteBazaryabOppED").prop("checked", false);
            $("#editBazaryabOppED").prop("checked", false);
            $("#seeBazaryabOppED").prop("checked", false);


            $("#oppDriverED").prop("checked", false);
            $("#oppDriverServiceED").prop("checked", false);
            $("#deleteoppDriverServiceED").prop("checked", false);
            $("#editoppDriverServiceED").prop("checked", false);
            $("#seeoppDriverServiceED").prop("checked", false);


            $("#oppBargiriED").prop("checked", false);
            $("#deleteoppBargiriED").prop("checked", false);
            $("#editoppBargiriED").prop("checked", false);
            $("#seeoppBargiriED").prop("checked", false);

            $("#oppNazarSanjiED").prop("checked", true);
            $("#todayoppNazarsanjiED").prop("checked", true);
            $("#deletetodayoppNazarsanjiED").prop("checked", true);
            $("#edittodayoppNazarsanjiED").prop("checked", true);
            $("#seetodayoppNazarsanjiED").prop("checked", true);


            $("#pastoppNazarsanjiED").prop("checked", true);
            $("#deletepastoppNazarsanjiED").prop("checked", true);
            $("#editpastoppNazarsanjiED").prop("checked", true);
            $("#seepastoppNazarsanjiED").prop("checked", true);


            $("#DoneoppNazarsanjiED").prop("checked", true);
            $("#deleteDoneoppNazarsanjiED").prop("checked", true);
            $("#editDoneoppNazarsanjiED").prop("checked", true);
            $("#seeDoneoppNazarsanjiED").prop("checked", true);


            $("#OppupDownBonusED").prop("checked", false);
            $("#AddOppupDownBonusED").prop("checked", false);
            $("#deleteAddOppupDownBonusED").prop("checked", false);
            $("#editAddOppupDownBonusED").prop("checked", false);
            $("#seeAddOppupDownBonusED").prop("checked", false);


            $("#SubOppupDownBonusED").prop("checked", false);
            $("#deleteSubOppupDownBonusED").prop("checked", false);
            $("#editSubOppupDownBonusED").prop("checked", false);
            $("#seeSubOppupDownBonusED").prop("checked", false);


            $("#oppRDED").prop("checked", false);
            $("#AddedoppRDED").prop("checked", false);
            $("#deleteAddedoppRDED").prop("checked", false);
            $("#editAddedoppRDED").prop("checked", false);
            $("#seeAddedoppRDED").prop("checked", false);


            $("#NotAddedoppRDED").prop("checked", false);
            $("#deleteNotAddedoppRDED").prop("checked", false);
            $("#editNotAddedoppRDED").prop("checked", false);
            $("#seeNotAddedoppRDED").prop("checked", false);


            $("#oppCalendarED").prop("checked", true);
            $("#oppjustCalendarED").prop("checked", true);
            $("#deleteoppjustCalendarED").prop("checked", true);
            $("#editoppjustCalendarED").prop("checked", true);
            $("#seeoppjustCalendarED").prop("checked", true);


            $("#oppCustCalendarED").prop("checked", true);
            $("#deleteoppCustCalendarED").prop("checked", true);
            $("#editoppCustCalendarED").prop("checked", true);
            $("#seeoppCustCalendarED").prop("checked", true);


            $("#alarmoppED").prop("checked", true);
            $("#allalarmoppED").prop("checked", true);
            $("#deleteallalarmoppED").prop("checked", true);
            $("#editallalarmoppED").prop("checked", true);
            $("#seeallalarmoppED").prop("checked", true);


            $("#donealarmoppED").prop("checked", true);
            $("#deletedonealarmoppED").prop("checked", true);
            $("#editdonealarmoppED").prop("checked", true);
            $("#seedonealarmoppED").prop("checked", true);


            $("#NoalarmoppED").prop("checked", true);
            $("#deleteNoalarmoppED").prop("checked", true);
            $("#editNoalarmoppED").prop("checked", true);
            $("#seeNoalarmoppED").prop("checked", true);


            $("#massageOppED").prop("checked", true);
            $("#deletemassageOppED").prop("checked", true);
            $("#editmassageOppED").prop("checked", true);
            $("#seemassageOppED").prop("checked", true);


            $("#justBargiriOppED").prop("checked", false);
            $("#deletejustBargiriOppED").prop("checked", false);
            $("#editjustBargiriOppED").prop("checked", false);
            $("#seejustBargiriOppED").prop("checked", false);
            //تعریف عناصر
            $("#declareElementED").prop("checked", false);
            $("#editdeclareElementED").prop("checked", false);
            $("#deletedeclareElementED").prop("checked", false);
            $("#seedeclareElementED").prop("checked", false);
            //اطلاعات پایه
            $("#baseInfoED").prop("checked", true);
            $("#rdSentED").prop("checked", false);
            $("#infoRdED").prop("checked", false);
            $("#deleteSentRdED").prop("checked", false);
            $("#editSentRdED").prop("checked", false);
            $("#seeSentRdED").prop("checked", false);

            $("#rdNotSentED").prop("checked", false);
            $("#deleteRdNotSentED").prop("checked", false);
            $("#editRdNotSentED").prop("checked", false);
            $("#seeRdNotSentED").prop("checked", false);

            $("#deleteProfileED").prop("checked", true);
            $("#editProfileED").prop("checked", true);
            $("#seeProfileED").prop("checked", true);
            $("#baseInfoProfileED").prop("checked", true);

            $("#addSaleLineED").prop("checked", false);
            $("#deleteSaleLineED").prop("checked", false);
            $("#editSaleLineED").prop("checked", false);
            $("#seeSaleLineED").prop("checked", false);

            $("#baseInfoSettingED").prop("checked", false);
            $("#InfoSettingAccessED").prop("checked", false);
            $("#deleteSettingAccessED").prop("checked", false);
            $("#editSettingAccessED").prop("checked", false);
            $("#seeSettingAccessED").prop("checked", false);

            $("#InfoSettingTargetED").prop("checked", false);
            $("#deleteSettingTargetED").prop("checked", false);
            $("#editSettingTargetED").prop("checked", false);
            $("#seeSettingTargetED").prop("checked", false);
        } else {
            if (employeeType == 3) {
                $("#reportED").prop("checked", true);
                $("#amalKardreportED").prop("checked", false);

                $("#managerreportED").prop("checked", false);
                $("#deletemanagerreportED").prop("checked", false);
                $("#editmanagerreportED").prop("checked", false);
                $("#seemanagerreportED").prop("checked", false);


                $("#HeadreportED").prop("checked", false);
                $("#deleteHeadreportED").prop("checked", false);
                $("#editHeadreportED").prop("checked", false);
                $("#seeHeadreportED").prop("checked", false);


                $("#poshtibanreportED").prop("checked", false);
                $("#deleteposhtibanreportED").prop("checked", false);
                $("#editposhtibanreportED").prop("checked", false);
                $("#seeposhtibanreportED").prop("checked", false);


                $("#bazaryabreportED").prop("checked", false);
                $("#deletebazaryabreportED").prop("checked", false);
                $("#editbazaryabreportED").prop("checked", false);
                $("#seebazaryabreportED").prop("checked", false);


                $("#reportDriverED").prop("checked", false);
                $("#deletereportDriverED").prop("checked", false);
                $("#editreportDriverED").prop("checked", false);
                $("#seereportDriverED").prop("checked", false);


                $("#trazEmployeeReportED").prop("checked", false);
                $("#deletetrazEmployeeReportED").prop("checked", false);
                $("#edittrazEmployeeReportED").prop("checked", false);
                $("#seetrazEmployeeReportED").prop("checked", false);


                $("#amalkardCustReportED").prop("checked", true);

                $("#customerReportED").prop("checked", true);
                $("#deletecustomerReportED").prop("checked", true);
                $("#editcustomerReportED").prop("checked", true);
                $("#seecustomerReportED").prop("checked", true);

                $("#loginCustRepED").prop("checked", true);
                $("#deleteloginCustRepED").prop("checked", true);
                $("#editloginCustRepED").prop("checked", true);
                $("#seeloginCustRepED").prop("checked", true);

                $("#inActiveCustRepED").prop("checked", true);
                $("#deleteinActiveCustRepED").prop("checked", true);
                $("#editinActiveCustRepED").prop("checked", true);
                $("#seeinActiveCustRepED").prop("checked", true);

                $("#noAdminCustRepED").prop("checked", true);
                $("#deletenoAdminCustRepED").prop("checked", true);
                $("#editnoAdminCustRepED").prop("checked", true);
                $("#seenoAdminCustRepED").prop("checked", true);

                $("#returnedCustRepED").prop("checked", false);
                $("#deletereturnedCustRepED").prop("checked", false);
                $("#editreturnedCustRepED").prop("checked", false);
                $("#seereturnedCustRepED").prop("checked", false);

                $("#goodsReportED").prop("checked", true);
                $("#salegoodsReportED").prop("checked", true);
                $("#deletesalegoodsReportED").prop("checked", true);
                $("#editsalegoodsReportED").prop("checked", true);
                $("#seesalegoodsReportED").prop("checked", true);


                $("#returnedgoodsReportED").prop("checked", true);
                $("#deletereturnedgoodsReportED").prop("checked", true);
                $("#editreturnedgoodsReportED").prop("checked", true);
                $("#seereturnedgoodsReportED").prop("checked", true);


                $("#NoExistgoodsReportED").prop("checked", true);
                $("#deleteNoExistgoodsReportED").prop("checked", true);
                $("#editNoExistgoodsReportED").prop("checked", true);
                $("#seeNoExistgoodsReportED").prop("checked", true);


                $("#nosalegoodsReportED").prop("checked", true);
                $("#deletenosalegoodsReportED").prop("checked", true);
                $("#editnosalegoodsReportED").prop("checked", true);
                $("#seenosalegoodsReportED").prop("checked", true);


                $("#returnedReportgoodsReportED").prop("checked", true);
                $("#returnedNTasReportgoodsReportED").prop("checked", true);
                $("#deletereturnedNTasReportgoodsReportED").prop("checked", true);
                $("#editreturnedNTasReportgoodsReportED").prop("checked", true);
                $("#seereturnedNTasReportgoodsReportED").prop("checked", true);


                $("#tasgoodsReprtED").prop("checked", true);
                $("#deletetasgoodsReprtED").prop("checked", true);
                $("#edittasgoodsReprtED").prop("checked", true);
                $("#seetasgoodsReprtED").prop("checked", true);


                $("#goodsbargiriReportED").prop("checked", false);
                $("#deletegoodsbargiriReportED").prop("checked", false);
                $("#editgoodsbargiriReportED").prop("checked", false);
                $("#seegoodsbargiriReportED").prop("checked", false);
                //عملیات
                $("#oppED").prop("checked", true);
                $("#oppTakhsisED").prop("checked", false);
                $("#oppManagerED").prop("checked", false);
                $("#deleteManagerOppED").prop("checked", false);
                $("#editManagerOppED").prop("checked", false);
                $("#seeManagerOppED").prop("checked", false);


                $("#oppHeadED").prop("checked", false);
                $("#deleteHeadOppED").prop("checked", false);
                $("#editHeadOppED").prop("checked", false);
                $("#seeHeadOppED").prop("checked", false);


                $("#oppBazaryabED").prop("checked", false);
                $("#deleteBazaryabOppED").prop("checked", false);
                $("#editBazaryabOppED").prop("checked", false);
                $("#seeBazaryabOppED").prop("checked", false);


                $("#oppDriverED").prop("checked", false);
                $("#oppDriverServiceED").prop("checked", false);
                $("#deleteoppDriverServiceED").prop("checked", false);
                $("#editoppDriverServiceED").prop("checked", false);
                $("#seeoppDriverServiceED").prop("checked", false);


                $("#oppBargiriED").prop("checked", false);
                $("#deleteoppBargiriED").prop("checked", false);
                $("#editoppBargiriED").prop("checked", false);
                $("#seeoppBargiriED").prop("checked", false);

                $("#oppNazarSanjiED").prop("checked", true);
                $("#todayoppNazarsanjiED").prop("checked", true);
                $("#deletetodayoppNazarsanjiED").prop("checked", true);
                $("#edittodayoppNazarsanjiED").prop("checked", true);
                $("#seetodayoppNazarsanjiED").prop("checked", true);


                $("#pastoppNazarsanjiED").prop("checked", true);
                $("#deletepastoppNazarsanjiED").prop("checked", true);
                $("#editpastoppNazarsanjiED").prop("checked", true);
                $("#seepastoppNazarsanjiED").prop("checked", true);


                $("#DoneoppNazarsanjiED").prop("checked", true);
                $("#deleteDoneoppNazarsanjiED").prop("checked", true);
                $("#editDoneoppNazarsanjiED").prop("checked", true);
                $("#seeDoneoppNazarsanjiED").prop("checked", true);


                $("#OppupDownBonusED").prop("checked", false);
                $("#AddOppupDownBonusED").prop("checked", false);
                $("#deleteAddOppupDownBonusED").prop("checked", false);
                $("#editAddOppupDownBonusED").prop("checked", false);
                $("#seeAddOppupDownBonusED").prop("checked", false);


                $("#SubOppupDownBonusED").prop("checked", false);
                $("#deleteSubOppupDownBonusED").prop("checked", false);
                $("#editSubOppupDownBonusED").prop("checked", false);
                $("#seeSubOppupDownBonusED").prop("checked", false);


                $("#oppRDED").prop("checked", false);
                $("#AddedoppRDED").prop("checked", false);
                $("#deleteAddedoppRDED").prop("checked", false);
                $("#editAddedoppRDED").prop("checked", false);
                $("#seeAddedoppRDED").prop("checked", false);


                $("#NotAddedoppRDED").prop("checked", false);
                $("#deleteNotAddedoppRDED").prop("checked", false);
                $("#editNotAddedoppRDED").prop("checked", false);
                $("#seeNotAddedoppRDED").prop("checked", false);


                $("#oppCalendarED").prop("checked", true);
                $("#oppjustCalendarED").prop("checked", true);
                $("#deleteoppjustCalendarED").prop("checked", true);
                $("#editoppjustCalendarED").prop("checked", true);
                $("#seeoppjustCalendarED").prop("checked", true);


                $("#oppCustCalendarED").prop("checked", true);
                $("#deleteoppCustCalendarED").prop("checked", true);
                $("#editoppCustCalendarED").prop("checked", true);
                $("#seeoppCustCalendarED").prop("checked", true);


                $("#alarmoppED").prop("checked", true);
                $("#allalarmoppED").prop("checked", true);
                $("#deleteallalarmoppED").prop("checked", true);
                $("#editallalarmoppED").prop("checked", true);
                $("#seeallalarmoppED").prop("checked", true);


                $("#donealarmoppED").prop("checked", true);
                $("#deletedonealarmoppED").prop("checked", true);
                $("#editdonealarmoppED").prop("checked", true);
                $("#seedonealarmoppED").prop("checked", true);


                $("#NoalarmoppED").prop("checked", true);
                $("#deleteNoalarmoppED").prop("checked", true);
                $("#editNoalarmoppED").prop("checked", true);
                $("#seeNoalarmoppED").prop("checked", true);


                $("#massageOppED").prop("checked", true);
                $("#deletemassageOppED").prop("checked", true);
                $("#editmassageOppED").prop("checked", true);
                $("#seemassageOppED").prop("checked", true);


                $("#justBargiriOppED").prop("checked", false);
                $("#deletejustBargiriOppED").prop("checked", false);
                $("#editjustBargiriOppED").prop("checked", false);
                $("#seejustBargiriOppED").prop("checked", false);
                //تعریف عناصر
                $("#declareElementED").prop("checked", false);
                $("#editdeclareElementED").prop("checked", false);
                $("#deletedeclareElementED").prop("checked", false);
                $("#seedeclareElementED").prop("checked", false);
                //اطلاعات پایه
                $("#baseInfoED").prop("checked", true);
                $("#rdSentED").prop("checked", false);
                $("#infoRdED").prop("checked", false);
                $("#deleteSentRdED").prop("checked", false);
                $("#editSentRdED").prop("checked", false);
                $("#seeSentRdED").prop("checked", false);

                $("#rdNotSentED").prop("checked", false);
                $("#deleteRdNotSentED").prop("checked", false);
                $("#editRdNotSentED").prop("checked", false);
                $("#seeRdNotSentED").prop("checked", false);

                $("#deleteProfileED").prop("checked", true);
                $("#editProfileED").prop("checked", true);
                $("#seeProfileED").prop("checked", true);
                $("#baseInfoProfileED").prop("checked", true);

                $("#addSaleLineED").prop("checked", false);
                $("#deleteSaleLineED").prop("checked", false);
                $("#editSaleLineED").prop("checked", false);
                $("#seeSaleLineED").prop("checked", false);

                $("#baseInfoSettingED").prop("checked", false);
                $("#InfoSettingAccessED").prop("checked", false);
                $("#deleteSettingAccessED").prop("checked", false);
                $("#editSettingAccessED").prop("checked", false);
                $("#seeSettingAccessED").prop("checked", false);

                $("#InfoSettingTargetED").prop("checked", false);
                $("#deleteSettingTargetED").prop("checked", false);
                $("#editSettingTargetED").prop("checked", false);
                $("#seeSettingTargetED").prop("checked", false);
            }
        }
    }
}




function setManagerStuff(element, adminId) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    $("#editAdmin").val($(input).val());
    $("#editAdmin").prop("disabled", false);
    $(".caret").css({ color: "gray" });
    $(element).css({ color: "blue" });

    if ($("#takhsisToAdminBtn")) {
        $("#takhsisToAdminBtn").val(adminId);
        $("#takhsisToAdminBtn").prop("disabled", false);
    }

    if ($("#adminTasviyahBtn")) {
        $("#adminTasviyahBtn").val(adminId);
        $("#adminTasviyahBtn").prop("disabled", false);
    }

    $.ajax({
        method: "get",
        url: baseUrl + "/getEmployees",
        data: { _token: "{{@csrf}}", headId: adminId },
        async: true,
        success: function (response) {
            if (response.length > 0) {
                $("#deleteAdmin").prop("disabled",true);
            }else{
                $("#deleteAdmin").val(adminId);
                $("#deleteAdmin").prop("disabled",false);
            }

        },
        error: function () { },
    });

}

function setHeadStuff(element, headId) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    $("#editAdmin").val($(input).val());
    $("#editAdmin").prop("disabled", false);
    $(".caret").css({ color: "gray" });
    $(element).css({ color: "blue" });

    if ($("#adminTasviyahBtn")) {
        $("#adminTasviyahBtn").prop("disabled", false);
    }

    $.ajax({
        method: "get",
        url: baseUrl + "/getEmployees",
        data: { _token: "{{@csrf}}", headId: headId },
        async: true,
        success: function (response) {
            $("#customerListBody").empty();
            response.forEach((element, index) => {
                $("#customerListBody").append(`
                <tr onclick="checkCheckBox(this,event); selectTableRow(this)">
                <td>` + (index + 1) + `</td>
                <td>` + element.name + ` ` + element.lastName + `</td>
                <td>` + element.phone + `</td>
                <td>` + element.discription + `</td>
             
                <td>
                    <input class="mainGroupId" type="checkbox" name="customerIDs[]" value="` +
                    element.id +
                    `">
                    <input class="mainGroupId" type="radio" style="display:none" name="customerIDs" value="` +
                    element.id +
                    `">
                </td>`
                );
            });

            if (response.length > 0) {
                $("#deleteAdmin").prop("disabled",true);
            }else{
                $("#deleteAdmin").val(headId);
                $("#deleteAdmin").prop("disabled",false);
            }

        },
        error: function () { },
    });
}
function setHeadOpStuff(element, headId) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    $("#editAdmin").val($(input).val());
    $("#editAdmin").prop("disabled", false);
    $(".caret").css({ color: "gray" });
    $(element).css({ color: "blue" });
    if ($("#takhsisToAdminBtn")) {
        $("#takhsisToAdminBtn").val(headId);
    }
    $.ajax({
        method: "get",
        url: baseUrl + "/getEmployees",
        data: { _token: "{{@csrf}}", headId: headId },
        async: true,
        success: function (response) {
            $("#customerListBody").empty();
            response.forEach((element, index) => {
                $("#customerListBody").append(`
                <tr onclick="setKarbarOpStuff(this,` + element.id + `); selectTableRow(this)">
                <td>` + (index + 1) + `</td>
                <td>` + element.name + ` ` + element.lastName + `</td>
                <td>` + element.phone + `</td>
                <td>` + element.discription + `</td>
                <td>
                    <input class="mainGroupId" type="radio" name="customerIDs[]" value="` + element.id + `">
                    <input class="mainGroupId" type="radio" style="display:none" name="customerIDs" value="` +
                    element.id +
                    `">
                </td>`
                );
            });
        },
        error: function () { },
    });
}
function setKarbarOpStuff(element, adminId) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    $("#takhsisToAdminBtn").val($(input).val());
}
$("#takhsisToAdminBtn").on("click", () => {
    let id = $("#takhsisToAdminBtn").val();
    $.ajax({
        method: "get",
        url: baseUrl + "/getAdminInfo",
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
        },
        async: true,
        success: function (msg) {
            $("#takhsisAdminName").text(msg[3].name + " " + msg[3].lastName);
        },
        error: function (error) { },
    });

    $.ajax({
        method: "get",
        url: baseUrl + "/getCustomer",
        data: {
            _token: "{{ csrf_token() }}",
        },
        async: true,
        success: function (arrayed_result) {
            $("#allCustomer").empty();

            arrayed_result.forEach((element, index) => {
                $("#allCustomer").append(
                    `
                <tr onclick="checkCheckBox(this,event); selectTableRow(this)">
                    <td style="">` +
                    (index + 1) +
                    `</td>
                    <td style="">` +
                    element.PCode +
                    `</td>
                    <td>` +
                    element.Name +
                    `</td>
                    <td style="">
                    <input class="form-check-input" name="customerIDs[]" type="checkbox" value="` +
                    element.PSN +
                    `" id="customerId">
                    </td>
                </tr>
            `
                );
            });
        },
        error: function (data) { },
    });
    $.ajax({
        method: "get",
        url: baseUrl + "/getAddedCustomer",
        data: {
            _token: "{{ csrf_token() }}",
            adminId: id,
        },
        async: true,
        success: function (arrayed_result) {
            $("#addedCustomer").empty();
            arrayed_result.forEach((element, index) => {
                $("#addedCustomer").append(
                    `
                        <tr onclick="checkCheckBox(this,event); selectTableRow(this)">
                            <td id="radif" style="width:55px;">` + (index + 1) +`</td>
                            <td id="mCode" style="width:115px;">` + element.PCode +  `</td>
                            <td >` +  element.Name +  `</td>
                            <td style="width:50px;">  <input class="form-check-input" name="addedCustomerIDs[]" type="checkbox" value="` +  element.PSN +   `" id="kalaId">  </td>
                        </tr>
                    `
                );
            });
        },
        error: function (data) { },
    });

    $("#takhsisCustomerModal").modal("show");
});

$("#adminTasviyahBtn").on("click", () => {
    let id = $("#takhsisToAdminBtn").val();
    removeStaff(id);
});

$("#emptyAdminBtn").on("click", () => {
    let id = $("#emptyAdminBtn").val();
    removeStaff(id);
});

$("#moveEmployee").on("click", () => {
    $("#moveEmployeeModal").modal("show");
    $.ajax({
        method: "get",
        url: baseUrl + "/getHeads",
        async: true,
        data: { _token: "{{@csrf}}" },
        success: function (response) {
            $("#headList").empty();
            response.forEach((element, index) => {
                $("#headList").append(
                    `<tr onclick="selectTableRow(this); setHeadSelectStuff(this,` + element.id +`); ">
                          <td >` +  (index + 1) + `</td>
                          <td >` +  element.name + ` ` +  element.lastName + `</td>
                          <td >` + element.phone + `</td>
                          <td><input class="customerList form-check-input" name="adminId" type="radio" value="` + element.id +`"></td>
                   </tr>`
                );
            });
        },
        error: function () { },
    });
});

function setHeadSelectStuff(element, headId) {
    $("#moveEmployeeDoneBtn").val(headId);
}

$("#moveEmployeeDoneBtn").on("click", () => {
    var adminID = [];
    $('input[name="customerIDs[]"]:checked').map(function () {
        adminID.push($(this).val());
    });

    $.ajax({
        method: "get",
        url: baseUrl + "/addToHeadEmployee",
        data: {
            _token: "{{@csrf}}",
            adminID: adminID,
            headId: $("#moveEmployeeDoneBtn").val(),
        },
        async: true,
        success: function (response) {
            window.location.reload();
        },
        error: function (error) { },
    });
    alert(customerID.length);
});

function setEmployeeStuff(element) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    $("#editAdmin").val($(input).val());
    $("#editAdmin").prop("disabled", false);
    $(".caret").css({ color: "gray" });
    $(element).css({ color: "blue" });
}


function checkCheckBox(element, event) {
    if (event.target.type == "checkbox") {
        e.stopPropagation();
    } else {
        if ($(element).find("input:checkbox").prop("disabled") == false) {
            if ($(element).find("input:checkbox").prop("checked") == false) {
                $(element).find("input:checkbox").prop("checked", true);
            } else {
                $(element).find("input:checkbox").prop("checked", false);
                $(element).find("td.selected").removeClass("selected");
            }
        }
    }

    if ($("#adminTasviyahBtn")) {
        $("#adminTasviyahBtn").prop("disabled", false);
    }
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    $("#editAdmin").val($(input).val());
    $("#editAdmin").prop("disabled", false);
    $(".caret").css({ color: "gray" });
    $(element).css({ color: "blue" });

    $.ajax({
        method: "get",
        url: baseUrl + "/getAddedCustomers",
        data: {
            adminId: $(input).val(),
        },
        async: true,
        success: function (arrayed_result) {
            if (arrayed_result.length > 0) {
                $("#deleteAdmin").prop("disabled",true);
            }else{
                $("#deleteAdmin").val($(input).val());
                $("#deleteAdmin").prop("disabled",false);
            }
        }
    });




}
// تنظیمات
//صفحه تخصیص جدید

$("#takhsisAllRadio").on("change", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/getEmployies",
        data: {
            _token: "{{@csrf}}",
            employeeType: $("#takhsisAllRadio").val(),
        },
        async: true,
        success: function (respond) {
            $("#adminGroupList").empty();
            respond.forEach((element, index) => {
                let countCustomer = "0";
                let takhsisDate = "مشتری ندارد";
                if (element.countCustomer) {
                    countCustomer = element.countCustomer;
                }
                if (element.takhsisDate) {
                    takhsisDate = element.takhsisDate;
                }
                $("#adminGroupList").append(`
                    <tr  onclick="setAdminStuff(this,`+ element.id + `,` + element.adminType + `); selectTableRow(this);"> 
                        <td>` + (index + 1) + `</td>
                        <td>` + element.name + ` ` + element.lastName + `</td>
                        <td> ` + countCustomer + ` </td>
                        <td> ` + takhsisDate + ` </td>
                        <td> <input class="mainGroupId" type="radio" name="AdminId[]" value="` + element.id + ` ` + element.adminTypeId + `"> </td>
                    </tr>
                `);
            });
        },
        error: function (error) { },
    });
});





$("#takhsisManagerRadio").on("change", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/getEmployies",
        data: {
            _token: "{{@csrf}}",
            employeeType: $("#takhsisManagerRadio").val(),
        },
        async: true,
        success: function (respond) {
            $("#adminGroupList").empty();
            respond.forEach((element, index) => {
                let countCustomer = "0";
                let takhsisDate = "مشتری ندارد";
                if (element.countCustomer) {
                    countCustomer = element.countCustomer;
                }
                if (element.takhsisDate) {
                    takhsisDate = element.takhsisDate;
                }
                $("#adminGroupList").append(`
                    <tr  onclick="setAdminStuff(this,`+ element.id + `,` + element.adminType + `); selectTableRow(this);"> 
                       <td>` + (index + 1) + `</td>
                        <td>` + element.name + ` ` + element.lastName + `</td>
                        <td> ` + countCustomer + ` </td>
                        <td> ` + takhsisDate + ` </td>
                        <td> <input class="mainGroupId" type="radio" name="AdminId[]" value="` + element.id + ` ` + element.adminTypeId + `"> </td>
                    </tr>
                `);
            });
        },
        error: function (error) { },
    });
});

$("#takhsisHeadRadio").on("change", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/getEmployies",
        data: {
            _token: "{{@csrf}}",
            employeeType: $("#takhsisHeadRadio").val(),
        },
        async: true,
        success: function (respond) {
            $("#adminGroupList").empty();
            respond.forEach((element, index) => {
                let countCustomer = "0";
                let takhsisDate = "مشتری ندارد";
                if (element.countCustomer) {
                    countCustomer = element.countCustomer;
                }
                if (element.takhsisDate) {
                    takhsisDate = element.takhsisDate;
                }
                $("#adminGroupList").append(`
                    <tr   onclick="setAdminStuff(this,`+ element.id + `,` + element.adminType + `); selectTableRow(this);">
                        <td>` + (index + 1) + `</td>
                        <td>` + element.name + ` ` + element.lastName + `</td>
                        <td> ` + countCustomer + ` </td>
                        <td> ` + takhsisDate + ` </td>
                        <td> <input class="mainGroupId" type="radio" name="AdminId[]" value="` + element.id + ` ` + element.adminTypeId + `"> </td>
                    </tr>`
                );
            });
        },
        error: function (error) { },
    });
});

$("#takhsisEmployeeRadio").on("change", () => {
    $.ajax({
        method: "get",
        url: baseUrl + "/getEmployies",
        data: {
            _token: "{{@csrf}}",
            employeeType: $("#takhsisEmployeeRadio").val(),
        },
        async: true,
        success: function (respond) {
            $("#adminGroupList").empty();
            respond.forEach((element, index) => {
                let countCustomer = "0";
                let takhsisDate = "مشتری ندارد";
                if (element.countCustomer) {
                    countCustomer = element.countCustomer;
                }
                if (element.takhsisDate) {
                    takhsisDate = element.takhsisDate;
                }
                $("#adminGroupList").append(`
                    <tr   onclick="setAdminStuff(this,`+ element.id + `,` + element.adminType + `); selectTableRow(this);">
                        <td>` + (index + 1) + `</td>
                        <td>` + element.name + ` ` + element.lastName + `</td>
                        <td> ` + countCustomer + ` </td>
                        <td> ` + takhsisDate + ` </td>
                        <td> <input class="mainGroupId" type="radio" name="AdminId[]" value="` + element.id + ` ` + element.adminTypeId + `"> </td>
                    </tr>`
                );
            });
        },
        error: function (error) { },
    });
});
//

$(document).on("click", "#loadMore", () => {
    $(".showLater").show();
});

function openAddCommentModal(customerId) {
    $("#customerIdForComment").val(customerId);
    $("#viewComment").modal("show");
}





$("#getPersonalsForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        success: function (data) {
            $("#adminList").empty();
            data.forEach((element, index) => {
                let adminType = "";

                switch (element.adminType) {
                    case `1`:
                        adminType = "ادمین"
                        break;
                    case `2`:
                        adminType = "پشتیبان"
                        break;
                    case `3`:
                        adminType = "بازاریاب"
                        break;
                    case `4`:
                        adminType = "راننده"
                        break;
                    case `5`:
                        adminType = "مدیر سیستم"
                        break;
                    case `6`:
                        adminType = "مدیر"
                        break;
                    case `7`:
                        adminType = "سرپرست"
                        break;
                }
                $("#adminList").append(`
                <tr onclick="setAdminStuffForAdmin(this,`+ element.adminType + `,` + element.driverId + `); selectTableRow(this)">
                    <td>`+ (index + 1) + `</td>
                    <td>`+ element.name + ` ` + element.lastName + `</td>
                    <td>`+ adminType + `</td>
                    <td class="descriptionForMobile">`+ element.discription + `</td>
                    <td>
                        <input class="mainGroupId" type="radio" name="AdminId[]" value="`+ element.id + `_` + element.adminType + `">
                    </td>
                </tr>`);
            });
        },
        error: function (error) { }
    });
});



$("#searchManagerByLine").on("change", function () {
    $.ajax({
        method: "get",
        url: baseUrl + "/getManagerByLine",
        data: {
            _token: "{{@crsf}}",
            lineId: $("#searchManagerByLine").val()
        },
        async: true,
        success: function (data) {
            $("#searchManagerSelect").empty();
            $("#searchManagerSelect").append(`<option value="-1">مدیران</option>`)
            data.forEach((element) => {
                $("#searchManagerSelect").append(`<option value="` + element.id + `">` + element.name + ` ` + element.lastName + `</option>`)
            });

        },
        error: function (error) {
            console.log(error)
        }
    })
})






$("#searchManagerSelect").on("change", () => {

    $.ajax({
        method: "get",
        url: baseUrl + '/getOrgChart',
        data: {
            _token: "{{@csrf}}",
            managerId: $("#searchManagerSelect").val()
        },
        async: true,
        success: function (respons) {

            // Create root and chart for oganizational chart
            var root = am5.Root.new("chartdiv12");
            root._logo.dispose();

            root.setThemes([
                am5themes_Animated.new(root)
            ]);

            var data = respons;

            var container = root.container.children.push(
                am5.Container.new(root, {
                    width: am5.percent(100),
                    height: am5.percent(100),
                    layout: root.verticalLayout
                })
            );

            var series = container.children.push(
                am5hierarchy.Tree.new(root, {
                    singleBranchOnly: false,
                    downDepth: 1,
                    initialDepth: 5,
                    topDepth: 0,
                    valueField: "value",
                    categoryField: "name",
                    childDataField: "children",
                    idField: "idField",
                    linkWithField: "link"
                })
            );

            series.circles.template.setAll({
                radius: 38,
            });

            series.outerCircles.template.setAll({
                radius: 39
            });

            series.labels.template.setAll({
                fontSize: 30,
            });


            series.circles.template.events.on("click", function (ev) {
                var nextUrl = ev.target.dataItem.dataContext.idField;
                var url;
                $.ajax({
                    method: "get",
                    url: baseUrl + '/getEmployeeInfo',
                    data: {
                        _token: "{{@csrf}}",
                        adminId: nextUrl
                    },
                    async: true,
                    success: function (respond) {
                        if (respond.adminType == 2 || respond.adminType == 4) {
                            if (respond.adminType == 4) {
                                url = baseUrl + "/poshtibanActionInfo?subPoshtibanId=" +nextUrl;
                                window.open(url);
                            } else {
                                url = baseUrl + "/poshtibanActionInfo?subPoshtibanId=" + nextUrl;
                                window.open(url);
                            }
                        } else {
                            url = baseUrl + "/saleExpertActionInfo?subId=" + nextUrl;
                            window.open(url);
                        }
                    },
                    error: function (error) {
                    }
                });
            });
            series.data.setAll(data);
            series.set("selectedDataItem", series.dataItems[0]);
        },
        error: function (error) {

        }
    })
});




$(document).on("change", ".headsRadio", function () {
    
    $.ajax({
        method: "get",
        url: baseUrl + '/getAdminInfo',
        data: {
            _token: "{{@csrf}}",
            id: $(this).val()
        },
        async: true,
        success: function (data) {
            $("#bazaryabList").empty();
            data[4].forEach((element, index) => {
                $("#bazaryabList").append(`
                <div class="form-check bg-gray">
                    <input class="personalList form-check-input p-2 float-end" type="radio" name="settings" value="`+ element.id + `">
                    <label class="form-check-label me-4" for="assesPast">`+ element.name + ` ` + element.lastName + `</label>
                </div>
                `);
            })

        }
        ,
        error: function (error) {

        }
    });
});



// kala

$(document).on("change", ".personalList", function () {
    $.ajax({
        method: "get",
        url: baseUrl + '/getCustomers',
        data: {
            _token: "{{@csrf}}",
            adminId: $(this).val()
        },
        success: function (data) {
            $("#customerListBody").empty();
            data.forEach((element, index) => {
                visitDate = "";
                if (element.lastVisitDate == null) {
                    visitDate = "ورود ندارد"
                } else {
                    visitDate = moment(element.lastVisitDate, 'YYYY/M/D HH:mm:ss').locale('fa').format('YYYY/M/D');
                }
                $("#customerListBody").append(`
                <tr onclick="getCustomerInfo(this,`+ element.PSN + `); selectTableRow(this)">
                    <td>`+ (index + 1) + `</td>
                    <td>`+ element.Name + `</td>
                    <td>`+ element.FactDate + `</td>
                    <td>`+ visitDate + `</td>
                    <td>
                        <input class="mainGroupId" type="radio" name="AdminId[]" value="">
                    </td>
                </tr>`);
            })
        },
        error: function (error) {

        }
    })
});

function getCustomerInfo(element, customerId) {
    $("tr").removeClass("selected");
    $(element).toggleClass("selected");
    $.ajax({
        method: "get",
        url: baseUrl + "/getCustomerInfo",
        data: {
            _token: "{{ csrf_token() }}",
            csn: customerId,
        },
        async: true,
        success: function (data) {
            $("#customerSpecialComment").text("");
            $("#customerSpecialComment").text(data[0].comment);
        },
        error: function (error) { }
    });
}
function getKalaId(element) {
    $(element).find("input:radio").prop("checked", true);
    let input = $(element).find("input:radio");
    var kalaId = input.val();
    $("#kalaSettingsBtn").val(kalaId);
    $(".kalaBtn").val(kalaId);
    $("#kalaSettingsBtn").prop("disabled", false);
    $("#openViewTenSalesModal").prop("disabled", false);

}


// kala settings script 
$("#kalaSettingsBtn").on("click", () => {

    const kalaId = $("#kalaSettingsBtn").val();
    $("#kalaIdForAddStock").val(kalaId);
    $("#kalaIdSpecialRest").val(kalaId);
    $("#kalaIdEdit").val(kalaId);
    $("#kalaIdDescription").val(kalaId);
    $("#kalaIdSameKala").val(kalaId);
    $('#mainPicEdit').attr('src', baseUrl + '/resources/assets/images/kala/' + kalaId + '_1.jpg');
    $("#kalaIdChangePic").val(kalaId);


    $.ajax({
        method: "get",
        dataType: "json",
        url: baseUrl + "/kalaSettings",
        data: {
            _token: "{{ csrf_token() }}",
            kalaId: kalaId
        },
        async: true,
        success: function (data) {
            let kala = data[0];
            let maingroupList = data[1];
            let stocks = data[2];
            let sameKala = data[3];
            let addedStocks = data[4];
            let costInfo = data[5];
            let kalaPriceCycle = data[6];
            $("#original").text(kala.NameGRP);
            $("#editKalaTitle").text("ویرایش :  " + "  " + kala.GoodName);
            $("#subsidiary").text(kala.NameGRP);
            $("#mainPrice").text(kala.mainPrice);
            $("#overLinePrice").text(kala.overLinePrice);
            $("#costLimit").val(kala.costLimit);
            $("#costContent").val(kala.costError);
            $("#costAmount").val(kala.costAmount);
            $("#existanceAlarm").val(kala.alarmAmount);
            $("#descriptionKala").text(kala.descProduct);
            $("#minSaleValue").text(kala.minSale + " " + kala.secondUnit + " " + " تعیین شده است ");
            $("#maxSaleValue").text(kala.maxSale + " " + kala.secondUnit + " " + " تعیین شده است ");


            $("#maingroupTableBody").empty();
            maingroupList.forEach((element, index) => {
                $("#maingroupTableBody").append(`
                     <tr id="grouptableRow" onclick="selectTableRow(this)">
                        <td>`+ (index + 1) + `</td>
                        <td>`+ element.title + `</td>
                        <td><input type="checkBox" class="form-check-input" disabled `+ (element.exist === 'ok' ? 'checked' : 'unchecked') + ` ></td>
                        <td>
                            <input class="mainGroupId form-check-input" type="radio" value="`+ element.id + `_` + kala.GoodSn + `" name="IDs[]" id="flexCheckChecked">
                            <input class="mainGroupId" type="text" value="`+ kala.GoodSn + `" name="ProductId" id="GoodSn" style="display: none">
                        </td>
                    </tr>`
                );


                $("#costTypeInfo").empty();
                costInfo.forEach((element, index) => {
                    $("#costTypeInfo").append(`
                <option `+ (kala.inforsType == element.SnInfor ? "selected" : " ") + ` value="` + element.SnInfor + `">` + element.InforName + `</option> 
            `)
                });

                // while check takhsis Anbar Checkbox it will append to the bottome table 
                $("#allStockForList").empty();
                stocks.forEach((element, index) => {
                    $("#allStockForList").append(`
                    <tr onclick="selectTableRow(this)">
                        <td>`+ (index + 1) + `</td>
                        <td>`+ element.NameStock + `</td>
                        <td>
                            <input class="form-check-input" name="stock[]" type="checkbox" value="`+ element.SnStock + '_' + element.NameStock + `" id="stockId">
                        </td>
                    </tr>
                 `)
                });


                $(document).on('click', '#removeStocksFromWeb', (function () {
                    $('tr').find('input:checkbox:checked').attr("name", "removeStocksFromWeb[]");
                    $('tr').has('input:checkbox:checked').hide();
                }));


                $(document).on('click', '#addStockToWeb', (function () {
                    var kalaListID = [];
                    $('input[name="allStocks[]"]:checked').map(function () {
                        kalaListID.push($(this).val());
                    });

                    $('input[name="allStocks[]"]:checked').parents('tr').css('color', 'white');
                    $('input[name="allStocks[]"]:checked').parents('tr').children('td').css('background-color', 'red');
                    $('input[name="allStocks[]"]:checked').prop("disabled", true);
                    $('input[name="allStocks[]"]:checked').prop("checked", false);

                    for (let i = 0; i < kalaListID.length; i++) {
                        $('#addedStocks').prepend(`
                    <tr class="addedTrStocks" onclick="checkCheckBox(this,event); selectTableRow(this)">
                        <td>` + kalaListID[i].split('_')[0] + `</td>
                        <td>` + kalaListID[i].split('_')[1] + `</td>
                        <td>
                            <input class="form-check-input" name="addedStocksToWeb[]" type="checkbox" value="` + kalaListID[i].split('_')[0] + `_` + kalaListID[i].split('_')[1] + `" id="kalaIds" checked>
                        </td>
                    </tr>`);
                    }
                }));



                // the following code assign Anbar to the left table 
                $("#allstockOfList").empty();
                addedStocks.forEach((element, index) => {
                    $("#allstockOfList").append(`
                <tr onclick="checkCheckBox(this); selectTableRow(this)">
                    <td>`+ (index + 1) + `</td>
                    <td>`+ element.NameStock + `</td>
                    <td>
                    <input  class="addStockToList form-check-input" name="addedStockToList[]" type="checkbox" value="`+ element.SnStock + `">
                    </td>
                </tr>
              `)
                });

                //for setting minimam saling of kala
                $(document).on('click', '.setMinSale', (function () {
                    var amountUnit = $(this).val().split('_')[0];
                    var productId = $(this).val().split('_')[1];
                    $.ajax({
                        type: "get",
                        url: baseUrl + "/setMinimamSaleKala",
                        data: { _token: "{{ csrf_token() }}", kalaId: productId, amountUnit: amountUnit },
                        dataType: "json",
                        success: function (msg) {
                            $("#minSaleValue").text(msg + " " + kala.secondUnit + " " + " تعیین شده است ");
                        },
                        error: function (msg) {
                            console.log(msg);
                        }
                    });
                }));



                //for setting maximam saling of kala
                $(document).on('click', '.setMaxSale', (function () {
                    var amountUnit = $(this).val().split('_')[0];
                    var productId = $(this).val().split('_')[1];
                    $.ajax({
                        type: "get",
                        url: baseUrl + "/setMaximamSaleKala",
                        data: { _token: "{{ csrf_token() }}", kalaId: productId, amountUnit: amountUnit },
                        dataType: "json",
                        success: function (msg) {
                            $("#maxSaleValue").text(msg + " " + kala.secondUnit + " " + " تعیین شده است ");
                        },
                        error: function (msg) {
                            console.log(msg);
                        }
                    });
                }));





                $(document).on("click", "#submitSubGroup", () => {
                    var addableStuff = [];
                    let kalaId = $("#kalaIdEdit").val();
                    $('input[name="addables[]"]:checked').map(function () {
                        addableStuff.push($(this).val());
                    });
                    var removableStuff = [];
                    $('input[name="removables[]"]:not(:checked)').map(function () {
                        removableStuff.push($(this).val());
                    });
                    $.ajax({
                        type: "get",
                        url: baseUrl + "/addOrDeleteKalaFromSubGroup",
                        data: {
                            _token: "{{ csrf_token() }}",
                            addableStuff: addableStuff,
                            removableStuff: removableStuff,
                            kalaId: kalaId
                        },
                        dataType: "json",
                        success: function (msg) {
                            $('#submitSubGroup').prop("disabled", true);
                            $("#stockSubmit").hide();
                            $("#kalaRestictionbtn").hide();
                            $("#completDescriptionbtn").hide();
                            $("#addToListSubmit").hide();
                            $("#submitChangePic").hide();
                        },
                        error: function (msg) {
                            console.log(msg);
                        }
                    });
                });



                // following function show the kala restriction button
                $(".restriction").on("click", () => {
                    $("#kalaRestictionbtn").show();
                    $("#stockSubmit").hide();
                    $("#completDescriptionbtn").hide();
                    $("#addToListSubmit").hide();
                    $("#submitChangePic").hide();
                    $("#submitSubGroup").hide();
                });

                // following function show the kala restriction button
                $(".keyRestriction").on("keydown", () => {
                    $("#kalaRestictionbtn").show();
                    $("#stockSubmit").hide();
                    $("#completDescriptionbtn").hide();
                    $("#addToListSubmit").hide();
                    $("#submitChangePic").hide();
                    $("#submitSubGroup").hide();
                });


                // for added sameKala 
                $("#allKalaOfList").empty();
                sameKala.forEach((element, index) => {
                    $("#allKalaOfList").append(`
                      <tr class="addedTrList" onclick="selectTableRow(this)">
                            <td>`+ (index + 1) + `</td>
                            <td>`+ element.GoodName + `</td>
                            <td>
                            <input class="form-check-input" style="" name="" type="checkbox" value="`+ element.GoodSn + '_' + element.GoodName + `" id="kalaIds">
                            </td>
                      </tr>
                  `)
                });


                $("#priceCycle").empty();
                kalaPriceCycle.forEach((element, index) => {
                    $("#priceCycle").append(`
                     <tr class="tableRow" onclick="selectTableRow(this)">
                        <td>`+ (index + 1) + `</td>
                        <td>`+ element.name + ' ' + element.lastName + `</td>
                        <td>`+ element.application + `</td>
                        <td>`+ moment(element.changedate, 'YYYY/M/D HH:mm:ss').locale('fa').format('YYYY/M/D') + `</td>
                        <td>`+ element.firstPrice + `</td>
                        <td>`+ element.changedFirstPrice + `</td>
                        <td>`+ element.secondPrice + `</td>
                        <td>`+ element.changedSecondPrice + `</td>
                        <td>
                            <input class="mainGroupId  form-check-input" type="radio" value="`+ maingroupList.id + '_' + kala.GoodSn + `" name="IDs[]" id="flexCheckChecked">
                            <input class="mainGroupId" type="text" value="`+ kala.GoodSn + `" name="ProductId" id="GoodSn" style="display: none">
                        </td>
                    </tr>
                `)
                });


                $(".kalaEditbtn").on("click", () => {
                    $("#submitChangePic").show();
                    $("#stockSubmit").hide();
                    $('#completDescriptionbtn').css('display', 'none');
                    $("#kalaRestictionbtn").hide();
                    $("#addToListSubmit").hide();
                    $("#submitSubGroup").hide();
                });




                // chech or uncheck the kala restriction 
                if (kala.callOnSale == 1) {
                    $('#callOnSale').prop('checked', true);
                } else {
                    $('#callOnSale').prop('checked', false);
                }

                if (kala.zeroExistance == 1) {
                    $('#zeroExistance').prop('checked', true);
                } else {
                    $('#zeroExistance').prop('checked', false);
                }

                if (kala.showTakhfifPercent == 1) {
                    $('#showTakhfifPercent').prop('checked', true);
                } else {
                    $('#showTakhfifPercent').prop('checked', false);
                }

                if (kala.overLine == 1) {
                    $('#showFirstPrice').prop('checked', true);
                } else {
                    $('#showFirstPrice').prop('checked', false);
                }

                if (kala.hideKala == 1) {
                    $('#inactiveAll').prop('checked', true);
                } else {
                    $('#inactiveAll').prop('checked', false);
                }

                if (kala.freeExistance == 1) {
                    $('#freeExistance').prop('checked', true);
                } else {
                    $('#freeExistance').prop('checked', false);
                }

                if (kala.activePishKharid == 1) {
                    $('#activePreBuy').prop('checked', true);
                } else {
                    $('#activePreBuy').prop('checked', false);
                }


            });




            // while onclick on radio button adding subgroup to left table 
            $(".mainGroupId").on("click", () => {
                $.ajax({
                    type: 'get',
                    async: true,
                    dataType: 'text',
                    url: baseUrl + "/subGroupsEdit",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: $('.mainGroupId:checked').val().split('_')[0],
                        kalaId: $('.mainGroupId:checked').val().split('_')[1]
                    },
                    success: function (answer) {
                        data = $.parseJSON(answer);
                        $('#subGroup1').empty();
                        for (var i = 0; i <= data.length - 1; i++) {
                            $('#subGroup1').append(
                                `<tr id="subgroupTableRow" onClick="addOrDeleteKala(this); selectTableRow(this)">
                                <td>` + (i + 1) + `</td>
                                <td>` + data[i].title + `</td>
                                <td>
                                   <input class="subGroupId form-check-input" name="subGroupId[]" value="` + data[i].id + `_` + data[i].selfGroupId + `" type="checkBox" id="flexCheckChecked` + i + `">
                               </td>
                        </tr>`);
                            if (data[i].exist == 'ok') {
                                $('#flexCheckChecked' + i).prop('checked', true);
                            } else {
                                $('#flexCheckChecked' + i).prop('checked', false);
                            }
                        }
                    }
                });
            });

            $("#groupSubgoupCategory").on("submit", function (e) {
                var addableStuff = [];
                let kalaId = $("#kalaIdEdit").val();

                $('input[name="addables[]"]:checked').map(function () {
                    addableStuff.push($(this).val());
                });

                var removableStuff = [];
                $('input[name="removables[]"]:not(:checked)').map(function () {
                    removableStuff.push($(this).val());
                });
                $.ajax({
                    type: "get",
                    url: baseUrl + "/addOrDeleteKalaFromSubGroup",
                    data: {
                        _token: "{{ csrf_token() }}",
                        addables: addableStuff,
                        removables: removableStuff,
                        ProductId: kalaId
                    },
                    dataType: "json",
                    success: function (msg) {
                        $('#submitSubGroup').prop("disabled", true);
                    },
                    error: function (msg) {
                        console.log(msg);
                    }
                });
                e.preventDefault();
            });


            $("#stockTakhsis").change(() => {
                if ($("#stockTakhsis").is(":checked")) {
                    $("#allStock").css("display", "flex");
                    $("#addAndDeleteStock").css("display", "flex");
                    $("#stockSubmit").show();
                    $("#submitSubGroup").hide();
                    $("#kalaRestictionbtn").hide();
                    $("#completDescriptionbtn").hide();
                    $("#addToListSubmit").hide();
                    $("#submitChangePic").hide();
                } else {
                    $("#stockSubmit").hide();
                    $("#kalaRestictionbtn").hide();
                    $("#completDescriptionbtn").hide();
                    $("#addToListSubmit").hide();
                    $("#submitChangePic").hide();
                    $("#submitSubGroup").show();
                }
            });

            if (!($('.modal.in').length)) {
                $('.modal-dialog').css({
                    top: 0,
                    left: 0
                });
            }
            $('#kalaSettingModal').modal({
                backdrop: false,
                show: true
            });

            $('.modal-dialog').draggable({
                handle: ".modal-header"
            });

            $("#kalaSettingModal").modal("show");
        },


        error: function (data) {
            alert("Some thing went to wrong in editing kala modal");
        }
    });

});


function SetMinQty() {
    const code = $("#kalaIdEdit").val();
    $.ajax({
        type: "get",
        url: baseUrl + "/getUnitsForSettingMinSale",
        data: { _token: "{{ csrf_token() }}", Pcode: code },
        dataType: "json",
        success: function (msg) {
            $("#unitStuffContainer").html(msg);
            const modal = document.querySelector('.modalBackdrop');
            const modalContent = modal.querySelector('.modal');
            modal.classList.add('active');
            modal.addEventListener('click', () => {
                modal.classList.remove('active');
            });
        },
        error: function (msg) {
            alert('Not good');
            console.log(msg);
        }
    });
}

function SetMaxQty() {
    const code = $("#kalaIdEdit").val();
    $.ajax({
        type: "get",
        url: baseUrl + "/getUnitsForSettingMaxSale",
        data: { _token: "{{ csrf_token() }}", Pcode: code },
        dataType: "json",
        success: function (msg) {
            $("#unitStuffContainer").html(msg);
            const modal = document.querySelector('.modalBackdrop');
            const modalContent = modal.querySelector('.modal');
            modal.classList.add('active');
            modal.addEventListener('click', () => {
                modal.classList.remove('active');
            });

        },
        error: function (msg) {
            alert('Not good');
            console.log(msg);
        }
    });
}

function UpdateQty(code, event, SnOrderBYS) {
    $.ajax({
        type: "get",
        url: baseUrl + "/getUnitsForUpdate",
        data: {
            _token: "{{ csrf_token() }}",
            Pcode: code
        },
        dataType: "json",
        success: function (msg) {
            $("#unitStuffContainer").html(msg);
            $(".SnOrderBYS").val(SnOrderBYS);
            const modal = document.querySelector('.modalBackdrop');
            const modalContent = modal.querySelector('.modal');
            modal.classList.add('active');
            modal.addEventListener('click', () => {
                modal.classList.remove('active');
            });


        },
        error: function (msg) {
            console.log(msg);
        }
    });
}





function activeSubmitButton(element) {

    if (element.id == "callOnSale") {
        if (element.checked) {
            document.querySelector("#zeroExistance").checked = false;
            document.querySelector("#showTakhfifPercent").checked = false;
            document.querySelector("#showFirstPrice").checked = false;
            document.querySelector("#freeExistance").checked = false;
            document.querySelector("#activePreBuy").checked = false;
        } else { }
    }
    if (element.id == "inactiveAll") {
        if (element.checked) {
            document.querySelector("#zeroExistance").checked = false;
            document.querySelector("#showTakhfifPercent").checked = false;
            document.querySelector("#showFirstPrice").checked = false;
            document.querySelector("#freeExistance").checked = false;
            document.querySelector("#activePreBuy").checked = false;
            document.querySelector("#callOnSale").checked = false;
        } else { }
    }
    if (element.id == "zeroExistance") {
        if (element.checked) {
            document.querySelector("#callOnSale").checked = false;
            document.querySelector("#showTakhfifPercent").checked = false;
            document.querySelector("#showFirstPrice").checked = false;
            document.querySelector("#freeExistance").checked = false;
            document.querySelector("#activePreBuy").checked = false;
        } else { }
    }
    if (element.id == "showTakhfifPercent") {
        if (element.checked) {
            document.querySelector("#zeroExistance").checked = false;
            document.querySelector("#callOnSale").checked = false;
        } else { }
    }

    if (element.id == "showFirstPrice") {
        if (element.checked) {
            document.querySelector("#callOnSale").checked = false;
            document.querySelector("#zeroExistance").checked = false;
        } else { }
    }
    if (element.id == "freeExistance") {
        if (element.checked) {
            document.querySelector("#callOnSale").checked = false;
            document.querySelector("#zeroExistance").checked = false;
        } else { }
    }

    if (element.id == "activePreBuy") {
        if (element.checked) {
            document.querySelector("#callOnSale").checked = false;
            document.querySelector("#zeroExistance").checked = false;
        } else {
            //do nothing
        }
    }
    $("#restrictStuffId").prop("disabled", false);
}


//برای افزودن انبار به لیست دست چپ
$(document).on('click', '#addStockToList', (function () {
    var stockListID = [];
    $('input[name="stock[]"]:checked').map(function () {
        stockListID.push($(this).val());
    });

    $("#stockSubmit").prop("disabled", false);
    $('input[name="stock[]"]:checked').parents('tr').css('color', 'white');
    $('input[name="stock[]"]:checked').parents('tr').children('td').css('background-color', 'red');
    $('input[name="stock[]"]:checked').prop("disabled", true);
    $('input[name="stock[]"]:checked').prop("checked", false);
    for (let i = 0; i < stockListID.length; i++) {
        $('#allstockOfList').append(`
                    <tr onclick="selectTableRow(this)">
                        <td>` + (i + 1) + `</td>
                        <td>` + stockListID[i].split('_')[1] + `</td>
                        <td>
                             <input class="addStockToList form-check-input" name="addedStockToList[]" type="checkbox" value="` + stockListID[i].split('_')[0] + `" id="kalaIds" checked>
                        </td>
                    </tr>
                    `);
    }
}));


//حذف انبار
$(document).on('click', '#removeStockFromList', (function () {
    $('tr').find('input:checkbox:checked').attr("name", "removeStockFromList[]");
    $('tr').has('input:checkbox:checked').hide();
    $("#stockSubmit").prop("disabled", false);
    $('#completDescriptionbtn').css('display', 'none');
    $("#kalaRestictionbtn").hide();
    $("#addToListSubmit").hide();
    $("#submitChangePic").hide();
    $("#submitSubGroup").hide();
}
)
);

$("#sameKalaList").change(function () {
    if ($("#sameKalaList").is(':checked')) {
        $("#addKalaToList").css("display", "flex");
        $("#addAndDelete").css("display", "flex");
        $("#addToListSubmit").css("display", "flex");
        $("#addedList").css("display", "flex");
        let mainKalaId = $("#mainKalaId").val();
        $.ajax({
            method: 'get',
            url: baseUrl + "/getAllKalas",
            data: { _token: "{{ csrf_token() }}", mainKalaId: mainKalaId },
            dataType: "json",
            async: true,
            success: function (arrayed_result) {
                $('#allKalaForList').empty();
                for (var i = 0; i <= arrayed_result.length - 1; i++) {
                    $('#allKalaForList').append(`
        <tr  onclick="checkCheckBox(this,event); selectTableRow(this)">
            <td>` + (i + 1) + `</td>
            <td>` + arrayed_result[i].GoodName + `</td>
            <td>
            <input class="form-check-input" name="kalaListForList[]" type="checkbox" value="` +
                        arrayed_result[i].GoodSn + `_` + arrayed_result[i]
                            .GoodName + `" id="kalaId">
            </td>
        </tr>
        `);
                }
            },
            error: function (data) { }
        });
    } else {
        $("#addKalaToList").hide();
        $("#addAndDelete").hide();
        $("#addToListSubmit").hide();
    }
});
$('#mainPic').on('change', () => {
    $("#submitChangePic").prop('disabled', false);
}

);


//used for adding kala to List to the left side(kalaList)
$(document).on('click', '#addDataToList', (function () {

    var kalaListID = [];
    $('input[name="kalaListForList[]"]:checked').map(function () {
        kalaListID.push($(this).val());
    });
    $("#addToListSubmit").prop("disabled", false);
    $('input[name="kalaListForList[]"]:checked').parents('tr').css('color', 'white');
    $('input[name="kalaListForList[]"]:checked').parents('tr').children('td').css('background-color', 'red');
    $('input[name="kalaListForList[]"]:checked').prop("disabled", true);
    $('input[name="kalaListForList[]"]:checked').prop("checked", false);
    for (let i = 0; i < kalaListID.length; i++) {
        $('#allKalaOfList').append(`<tr class="addedTrList" onclick="selectTableRow(this)">
<td>` + (i + 1) + `</td>
<td>` + kalaListID[i].split('_')[1] + `</td>
<td>
<input class="addKalaToList form-check-input" name="addedKalaToList[]" type="checkbox" value="` + kalaListID[i].split('_')[0] + `_` + kalaListID[i].split('_')[1] + `" id="kalaIds" checked>
</td>
</tr>`);

    }
}));


// for submiting Samekala form 
$('#sameKalaForm').on('submit', function (e) {

    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        dataType: 'json',
        data: $(this).serialize(),
        success: function (data) {
            $("#stockSubmit").hide();
            $('#completDescriptionbtn').css('display', 'none');
            $("#kalaRestictionbtn").hide();
            $("#addToListSubmit").hide();
            $("#submitSubGroup").show();


            $("#addKalaToList").hide();
            $("#addAndDelete").hide();
            $("#sameKalaList").prop("checked", false);
        },

        error: function (xhr, err) {
            alert('same Kala is not submited');
        }

    });

    e.preventDefault();

});




//used for removing data from assame List
$(document).on('click', '#removeDataFromList', (function () {
    $('tr').find('input:checkbox:checked').attr("name", "removeKalaFromList[]");
    $('tr').has('input:checkbox:checked').hide();
}));




function addOrDeleteKala(element) {
    let input = $(element).find('input:checkbox');
    if (input.is(":checked")) {
        input.prop("checked", false);
        input.prop("name", 'removables[]');
        $("#submitSubGroup").prop("disabled", false);
    } else {
        input.prop("checked", true);
        input.prop("name", 'addables[]');
        $("#submitSubGroup").prop("disabled", false);
    }
}
$(document).on("click", "#submitSubGroup", () => {
    var addableStuff = [];
    let kalaId = $("#kalaIdEdit").val();
    $('input[name="addables[]"]:checked').map(function () {
        addableStuff.push($(this).val());
    });
    var removableStuff = [];
    $('input[name="removables[]"]:not(:checked)').map(function () {
        removableStuff.push($(this).val());
    });
    $.ajax({
        type: "get",
        url: baseUrl + "/addOrDeleteKalaFromSubGroup",
        data: {
            _token: "{{ csrf_token() }}",
            addableStuff: addableStuff,
            removableStuff: removableStuff,
            kalaId: kalaId
        },
        dataType: "json",
        success: function (msg) {
            $('#submitSubGroup').prop("disabled", true);
        },
        error: function (msg) {
            console.log(msg);
        }
    });
});

$(document).on("submit", "#addDescKala", () => {

    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        dataType: 'json',
        data: $(this).serialize(),
        success: function (data) { },
        error: function (xhr, err) {
            alert('Error');
        }
    });
    return false;
});



//سبمیت محدودیت ها روی کالا
$("#restrictFormStuff").on('submit', function (event) {

    event.preventDefault();
    if (!($("#inactiveAll").is(':checked'))) {

        let inputElements = document.getElementsByTagName('input');
        let len = inputElements.length;

        for (let i = 0; i < len; i++) {
            inputElements[i].disabled = false;
        }

        let buttonElements = document.getElementsByTagName('button');
        let buttonLen = buttonElements.length;
        for (let i = 0; i < buttonLen; i++) {
            buttonElements[i].disabled = false;
        }
        let selectElements = document.getElementsByTagName('select');
        let selectLen = selectElements.length;

        for (let i = 0; i < selectLen; i++) {
            selectElements[i].disabled = false;
        }
        let textAreaElements = document.getElementsByTagName('textArea');
        let textAreaLen = textAreaElements.length;

        for (let i = 0; i < textAreaLen; i++) {
            textAreaElements[i].disabled = false;
        }

    } else {
        document.querySelector("#zeroExistance").checked = false;
        document.querySelector("#showTakhfifPercent").checked = false;
        document.querySelector("#showFirstPrice").checked = false;
        document.querySelector("#freeExistance").checked = false;
        document.querySelector("#activePreBuy").checked = false;
    }

    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        dataType: 'json',
        data: $(this).serialize(),

        success: function (data) {
            if (data == 1) {

                let inputElements = document.getElementsByTagName('input');
                let len = inputElements.length;

                for (let i = 0; i < len; i++) {
                    inputElements[i].disabled = true;
                }
                let buttonElements = document.getElementsByTagName('button');
                let buttonLen = buttonElements.length;

                for (let i = 0; i < buttonLen; i++) {
                    buttonElements[i].disabled = true;
                }
                let selectElements = document.getElementsByTagName('select');
                let selectLen = selectElements.length;

                for (let i = 0; i < selectLen; i++) {
                    selectElements[i].disabled = true;
                }
                let textAreaElements = document.getElementsByTagName('textArea');
                let textAreaLen = textAreaElements.length;

                for (let i = 0; i < textAreaLen; i++) {
                    textAreaElements[i].disabled = true;
                }
                document.querySelector("#inactiveAll").disabled = false;
                $("#restrictStuffId").prop("disabled", true);
            }
        },
        error: function (xhr, err) {
            alert("kala restriction doesn't work");
        }
    });

    return false;
});

// for submiting description kala data 
$("#completDescription").submit(function (e) {
    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        dataType: 'json',
        data: $(this).serialize(),
        success: function (data) {
            $('#completDescriptionbtn').css('display', 'none');
            $("#kalaRestictionbtn").hide();
            $("#stockSubmit").hide();
            $("#addToListSubmit").hide();
            $("#submitChangePic").hide();
            $("#submitSubGroup").show();
        },
        error: function (xhr, err) {
            alert('description Kala is not submited');
        }

    });
    e.preventDefault();
});


$("#calendarRadioBtn").on("change", () => {
    $("#duplicatecustomerTable").css("display", "none")
    $("#timeTable").css("display", "table")
    $("#calendarStaff").css("display", "inline")
    $("#month").css("display", "inline")
    $("#year").css("display", "inline")
    $("#customerTable").css("display", "none")
    $("#customerTable_wrapper").css("display", "none")

    $("#customerStaff").css("display", "none")
    $("#newCustomerTable").css("display", "none")
    $(".contentHeader").addClass("calendarContentHeader")
    $(".contentHeader").removeClass("customerListContentHeader")
    $(".contentHeader").removeClass("newCustListContentHeader")
    $("#newCustomerTable").removeClass("newCustomerContent")
    $("#duplicatecustomerTable_wrapper").css("display", "none")

})
$("#customerListRadioBtn").on("change", () => {
    $("#duplicatecustomerTable_wrapper").css("display", "none")
    $("#customerTable_wrapper").css("display", "table")
    $("#customerStaff").css("display", "inline")
    $("#timeTable").css("display", "none")
    $("#calendarStaff").css("display", "none")
    $("#month").css("display", "none")
    $("#year").hide();
    $("#newCustomerTable").css("display", "none")
    $(".contentHeader").addClass("customerListContentHeader")
    $(".contentHeader").removeClass("calendarContentHeader")
    $(".contentHeader").removeClass("newCustListContentHeader")
    $("#newCustomerTable").removeClass("newCustomerContent")
})
$("#newCustomerRadioBtn").on("change", () => {
    $("#customerTable").css("display", "none");
    $("#customerTable_wrapper").css("display", "none");
    $("#customerStaff").css("display", "none")
    $("#timeTable").css("display", "none")
    $("#calendarStaff").css("display", "none")
    $("#month").css("display", "none")
    $("#year").hide();
    $("#duplicatecustomerTable_wrapper").css("dsiplay", "none");
    $("#newCustomerTable").css("display", "grid")
    $(".contentHeader").addClass("newCustListContentHeader")
    $("#newCustomerTable").addClass("newCustomerContent")
    $(".contentHeader").removeClass("customerListContentHeader")
    $(".contentHeader").removeClass("calendarContentHeader")
})

$("#duplicatCustomerRadioBtn").on("change", () => {
    $("#customerTable_wrapper").css("display", "none")
    $("#duplicatecustomerTable_wrapper").css("display", "block")
    $("#customerStaff").css("display", "none")
    $("#timeTable").css("display", "none")
    $("#calendarStaff").css("display", "none")
    $("#month").css("display", "none")
    $("#year").hide();
    $("#newCustomerTable").css("display", "none")
    $(".contentHeader").removeClass("newCustListContentHeader")
    $("#newCustomerTable").removeClass("newCustomerContent")
    $(".contentHeader").removeClass("customerListContentHeader")
    $(".contentHeader").removeClass("calendarContentHeader")
})

$("#openCurrentLocationModal").on("click", () => {
    var map_init = L.map('mapId').setView([35.70163, 51.39211], 12);
    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map_init);

    var lc = L.Control.geocoder().addTo(map_init);
    if (!navigator.geolocation) {
        console.log("لطفا مرورگر خویش را آپدیت نمایید!")
    } else {
        setInterval(() => {
            navigator.geolocation.getCurrentPosition(getPosition)
        }, 5000);
    };

    var marker, circle, lat, long, accuracy;

    function getPosition(position) {
        lat = position.coords.latitude
        long = position.coords.longitude
        accuracy = position.coords.accuracy

        if (marker) {
            map_init.removeLayer(marker)
        }

        if (circle) {
            map_init.removeLayer(circle)
        }

        marker = L.marker([lat, long]);
        circle = L.circle([lat, long], { radius: accuracy });
        var featureGroup = L.featureGroup([marker, circle]).addTo(map_init);
        map_init.fitBounds(featureGroup.getBounds());
        $("#currentLocationInput").val(lat + ',' + long);
        $("#saveLocationBtn").prop("disabled", false);
        //alert("Your coordinate is: Lat: " + lat + " Long: " + long + " Accuracy: " + accuracy);
    }


    $("#currentLocationModal").modal("show");

    setTimeout(() => {
        map_init.invalidateSize();
    }, 500);

});

$("#changeAddressOnMap").on("click", ()=>{
    
    var map_init = L.map('changeAdd').setView([35.70163, 51.39211], 12);


    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map_init);

    var lc = L.Control.geocoder().addTo(map_init);
    if (!navigator.geolocation) {
        console.log("لطفا مرورگر خویش را آپدیت نمایید!")
    } else {
        setInterval(() => {
            navigator.geolocation.getCurrentPosition(getPosition)
        }, 5000);
    };

    var marker, circle, lat, long, accuracy;

    function getPosition(position) {
        lat = position.coords.latitude
        long = position.coords.longitude
        accuracy = position.coords.accuracy

        if (marker) {
            map_init.removeLayer(marker)
        }

        if (circle) {
            map_init.removeLayer(circle)
        }

        marker = L.marker([lat, long]);
        circle = L.circle([lat, long], { radius: accuracy });
        var featureGroup = L.featureGroup([marker, circle]).addTo(map_init);
        map_init.fitBounds(featureGroup.getBounds());
        $("#currentLocationInput").val(long + ',' + lat);
        $("#registerLocByDriverBtn").prop("disabled", false);
        //alert("Your coordinate is: Lat: " + lat + " Long: " + long + " Accuracy: " + accuracy);
    }


    $("#currentLocationModal").modal("show");

    setTimeout(() => {
        map_init.invalidateSize();
    }, 500);
    $("#changeAddressModal").modal("show");
});

$("#registerLocByDriverBtn").on("click",function(){
$.get(baseUrl+'/updatePosition',{pers:$("#currentLocationInput").val(),psn:$("#customerIdForLoc").val()},function(data,status){
    if(status=='success'){
        $("#changeAddressModal").modal("hide");
    }
})
});

function saveLocation() {
    $("#customerLocation").val($("#currentLocationInput").val());
    $("#openCurrentLocationModal").prop("disabled", true);
    $("#currentLocationModal").modal("hide");
    
}

$("#addingNewCustomerBtn").on("click", () => {
    if (!($('.modal.in').length)) {
        $('.modal-dialog').css({
            top: 0,
            left: 0
        });
    }
    $('#addingNewCutomer').modal({
        backdrop: false,
        show: true
    });

    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });
    $("#addingNewCutomer").modal("show");

});



$("#poshtibanType").on("change", () => {
    setEmployeeAccessLevel($("#poshtibanType").val());
});

function setEmployeeAccessLevel(employeeType) {
    if (employeeType == 4) {
        //گزارشات
        $("#reportN").prop("checked", false);
        $("#amalKardreportN").prop("checked", false);

        $("#managerreportN").prop("checked", false);
        $("#deletemanagerreportN").prop("checked", false);
        $("#editmanagerreportN").prop("checked", false);
        $("#seemanagerreportN").prop("checked", false);


        $("#HeadreportN").prop("checked", false);
        $("#deleteHeadreportN").prop("checked", false);
        $("#editHeadreportN").prop("checked", false);
        $("#seeHeadreportN").prop("checked", false);


        $("#poshtibanreportN").prop("checked", false);
        $("#deleteposhtibanreportN").prop("checked", false);
        $("#editposhtibanreportN").prop("checked", false);
        $("#seeposhtibanreportN").prop("checked", false);


        $("#bazaryabreportN").prop("checked", false);
        $("#deletebazaryabreportN").prop("checked", false);
        $("#editbazaryabreportN").prop("checked", false);
        $("#seebazaryabreportN").prop("checked", false);


        $("#reportDriverN").prop("checked", false);
        $("#deletereportDriverN").prop("checked", false);
        $("#editreportDriverN").prop("checked", false);
        $("#seereportDriverN").prop("checked", false);


        $("#trazEmployeeReportN").prop("checked", false);
        $("#deletetrazEmployeeReportN").prop("checked", false);
        $("#edittrazEmployeeReportN").prop("checked", false);
        $("#seetrazEmployeeReportN").prop("checked", false);


        $("#amalkardCustReportN").prop("checked", false);

        $("#customerReportN").prop("checked", false);
        $("#deletecustomerReportN").prop("checked", false);
        $("#editcustomerReportN").prop("checked", false);
        $("#seecustomerReportN").prop("checked", false);

        $("#loginCustRepN").prop("checked", false);
        $("#deleteloginCustRepN").prop("checked", false);
        $("#editloginCustRepN").prop("checked", false);
        $("#seeloginCustRepN").prop("checked", false);

        $("#inActiveCustRepN").prop("checked", false);
        $("#deleteinActiveCustRepN").prop("checked", false);
        $("#editinActiveCustRepN").prop("checked", false);
        $("#seeinActiveCustRepN").prop("checked", false);

        $("#noAdminCustRepN").prop("checked", false);
        $("#deletenoAdminCustRepN").prop("checked", false);
        $("#editnoAdminCustRepN").prop("checked", false);
        $("#seenoAdminCustRepN").prop("checked", false);

        $("#returnedCustRepN").prop("checked", false);
        $("#deletereturnedCustRepN").prop("checked", false);
        $("#editreturnedCustRepN").prop("checked", false);
        $("#seereturnedCustRepN").prop("checked", false);

        $("#goodsReportN").prop("checked", false);
        $("#salegoodsReportN").prop("checked", false);
        $("#deletesalegoodsReportN").prop("checked", false);
        $("#editsalegoodsReportN").prop("checked", false);
        $("#seesalegoodsReportN").prop("checked", false);


        $("#returnedgoodsReportN").prop("checked", false);
        $("#deletereturnedgoodsReportN").prop("checked", false);
        $("#editreturnedgoodsReportN").prop("checked", false);
        $("#seereturnedgoodsReportN").prop("checked", false);


        $("#NoExistgoodsReportN").prop("checked", false);
        $("#deleteNoExistgoodsReportN").prop("checked", false);
        $("#editNoExistgoodsReportN").prop("checked", false);
        $("#seeNoExistgoodsReportN").prop("checked", false);


        $("#nosalegoodsReportN").prop("checked", false);
        $("#deletenosalegoodsReportN").prop("checked", false);
        $("#editnosalegoodsReportN").prop("checked", false);
        $("#seenosalegoodsReportN").prop("checked", false);


        $("#returnedReportgoodsReportN").prop("checked", false);
        $("#returnedNTasReportgoodsReportN").prop("checked", false);
        $("#deletereturnedNTasReportgoodsReportN").prop("checked", false);
        $("#editreturnedNTasReportgoodsReportN").prop("checked", false);
        $("#seereturnedNTasReportgoodsReportN").prop("checked", false);


        $("#tasgoodsReprtN").prop("checked", false);
        $("#deletetasgoodsReprtN").prop("checked", false);
        $("#edittasgoodsReprtN").prop("checked", false);
        $("#seetasgoodsReprtN").prop("checked", false);


        $("#goodsbargiriReportN").prop("checked", false);
        $("#deletegoodsbargiriReportN").prop("checked", false);
        $("#editgoodsbargiriReportN").prop("checked", false);
        $("#seegoodsbargiriReportN").prop("checked", false);
        //عملیات
        $("#oppN").prop("checked", false);
        $("#oppTakhsisN").prop("checked", false);
        $("#oppManagerN").prop("checked", false);
        $("#deleteManagerOppN").prop("checked", false);
        $("#editManagerOppN").prop("checked", false);
        $("#seeManagerOppN").prop("checked", false);


        $("#oppHeadN").prop("checked", false);
        $("#deleteHeadOppN").prop("checked", false);
        $("#editHeadOppN").prop("checked", false);
        $("#seeHeadOppN").prop("checked", false);


        $("#oppBazaryabN").prop("checked", false);
        $("#deleteBazaryabOppN").prop("checked", false);
        $("#editBazaryabOppN").prop("checked", false);
        $("#seeBazaryabOppN").prop("checked", false);


        $("#oppDriverN").prop("checked", false);
        $("#oppDriverServiceN").prop("checked", false);
        $("#deleteoppDriverServiceN").prop("checked", false);
        $("#editoppDriverServiceN").prop("checked", false);
        $("#seeoppDriverServiceN").prop("checked", false);


        $("#oppBargiriN").prop("checked", false);
        $("#deleteoppBargiriN").prop("checked", false);
        $("#editoppBargiriN").prop("checked", false);
        $("#seeoppBargiriN").prop("checked", false);

        $("#oppNazarSanjiN").prop("checked", false);
        $("#todayoppNazarsanjiN").prop("checked", false);
        $("#deletetodayoppNazarsanjiN").prop("checked", false);
        $("#edittodayoppNazarsanjiN").prop("checked", false);
        $("#seetodayoppNazarsanjiN").prop("checked", false);


        $("#pastoppNazarsanjiN").prop("checked", false);
        $("#deletepastoppNazarsanjiN").prop("checked", false);
        $("#editpastoppNazarsanjiN").prop("checked", false);
        $("#seepastoppNazarsanjiN").prop("checked", false);


        $("#DoneoppNazarsanjiN").prop("checked", false);
        $("#deleteDoneoppNazarsanjiN").prop("checked", false);
        $("#editDoneoppNazarsanjiN").prop("checked", false);
        $("#seeDoneoppNazarsanjiN").prop("checked", false);


        $("#OppupDownBonusN").prop("checked", false);
        $("#AddOppupDownBonusN").prop("checked", false);
        $("#deleteAddOppupDownBonusN").prop("checked", false);
        $("#editAddOppupDownBonusN").prop("checked", false);
        $("#seeAddOppupDownBonusN").prop("checked", false);


        $("#SubOppupDownBonusN").prop("checked", false);
        $("#deleteSubOppupDownBonusN").prop("checked", false);
        $("#editSubOppupDownBonusN").prop("checked", false);
        $("#seeSubOppupDownBonusN").prop("checked", false);


        $("#oppRDN").prop("checked", false);
        $("#AddedoppRDN").prop("checked", false);
        $("#deleteAddedoppRDN").prop("checked", false);
        $("#editAddedoppRDN").prop("checked", false);
        $("#seeAddedoppRDN").prop("checked", false);


        $("#NotAddedoppRDN").prop("checked", false);
        $("#deleteNotAddedoppRDN").prop("checked", false);
        $("#editNotAddedoppRDN").prop("checked", false);
        $("#seeNotAddedoppRDN").prop("checked", false);


        $("#oppCalendarN").prop("checked", false);
        $("#oppjustCalendarN").prop("checked", false);
        $("#deleteoppjustCalendarN").prop("checked", false);
        $("#editoppjustCalendarN").prop("checked", false);
        $("#seeoppjustCalendarN").prop("checked", false);


        $("#oppCustCalendarN").prop("checked", false);
        $("#deleteoppCustCalendarN").prop("checked", false);
        $("#editoppCustCalendarN").prop("checked", false);
        $("#seeoppCustCalendarN").prop("checked", false);


        $("#alarmoppN").prop("checked", false);
        $("#allalarmoppN").prop("checked", false);
        $("#deleteallalarmoppN").prop("checked", false);
        $("#editallalarmoppN").prop("checked", false);
        $("#seeallalarmoppN").prop("checked", false);


        $("#donealarmoppN").prop("checked", false);
        $("#deletedonealarmoppN").prop("checked", false);
        $("#editdonealarmoppN").prop("checked", false);
        $("#seedonealarmoppN").prop("checked", false);


        $("#NoalarmoppN").prop("checked", false);
        $("#deleteNoalarmoppN").prop("checked", false);
        $("#editNoalarmoppN").prop("checked", false);
        $("#seeNoalarmoppN").prop("checked", false);


        $("#massageOppN").prop("checked", true);
        $("#deletemassageOppN").prop("checked", true);
        $("#editmassageOppN").prop("checked", true);
        $("#seemassageOppN").prop("checked", true);


        $("#justBargiriOppN").prop("checked", true);
        $("#deletejustBargiriOppN").prop("checked", true);
        $("#editjustBargiriOppN").prop("checked", true);
        $("#seejustBargiriOppN").prop("checked", true);
        //تعریف عناصر
        $("#declareElementN").prop("checked", false);
        $("#editdeclareElementN").prop("checked", false);
        $("#deletedeclareElementN").prop("checked", false);
        $("#seedeclareElementN").prop("checked", false);
        //اطلاعات پایه
        $("#baseInfoN").prop("checked", true);
        $("#rdSentN").prop("checked", false);
        $("#infoRdN").prop("checked", false);
        $("#deleteSentRdN").prop("checked", false);
        $("#editSentRdN").prop("checked", false);
        $("#seeSentRdN").prop("checked", false);

        $("#rdNotSentN").prop("checked", false);
        $("#deleteRdNotSentN").prop("checked", false);
        $("#editRdNotSentN").prop("checked", false);
        $("#seeRdNotSentN").prop("checked", false);

        $("#deleteProfileN").prop("checked", true);
        $("#editProfileN").prop("checked", true);
        $("#seeProfileN").prop("checked", true);
        $("#baseInfoProfileN").prop("checked", true);

        $("#addSaleLineN").prop("checked", false);
        $("#deleteSaleLineN").prop("checked", false);
        $("#editSaleLineN").prop("checked", false);
        $("#seeSaleLineN").prop("checked", false);

        $("#baseInfoSettingN").prop("checked", false);
        $("#InfoSettingAccessN").prop("checked", false);
        $("#deleteSettingAccessN").prop("checked", false);
        $("#editSettingAccessN").prop("checked", false);
        $("#seeSettingAccessN").prop("checked", false);

        $("#InfoSettingTargetN").prop("checked", false);
        $("#deleteSettingTargetN").prop("checked", false);
        $("#editSettingTargetN").prop("checked", false);
        $("#seeSettingTargetN").prop("checked", false);
    } else {
        if (employeeType == 2) {

            $("#reportN").prop("checked", true);
            $("#amalKardreportN").prop("checked", true);

            $("#managerreportN").prop("checked", false);
            $("#deletemanagerreportN").prop("checked", false);
            $("#editmanagerreportN").prop("checked", false);
            $("#seemanagerreportN").prop("checked", false);


            $("#HeadreportN").prop("checked", false);
            $("#deleteHeadreportN").prop("checked", false);
            $("#editHeadreportN").prop("checked", false);
            $("#seeHeadreportN").prop("checked", false);


            $("#poshtibanreportN").prop("checked", false);
            $("#deleteposhtibanreportN").prop("checked", false);
            $("#editposhtibanreportN").prop("checked", false);
            $("#seeposhtibanreportN").prop("checked", false);


            $("#bazaryabreportN").prop("checked", false);
            $("#deletebazaryabreportN").prop("checked", false);
            $("#editbazaryabreportN").prop("checked", false);
            $("#seebazaryabreportN").prop("checked", false);


            $("#reportDriverN").prop("checked", false);
            $("#deletereportDriverN").prop("checked", false);
            $("#editreportDriverN").prop("checked", false);
            $("#seereportDriverN").prop("checked", false);


            $("#trazEmployeeReportN").prop("checked", false);
            $("#deletetrazEmployeeReportN").prop("checked", false);
            $("#edittrazEmployeeReportN").prop("checked", false);
            $("#seetrazEmployeeReportN").prop("checked", false);


            $("#amalkardCustReportN").prop("checked", true);

            $("#customerReportN").prop("checked", true);
            $("#deletecustomerReportN").prop("checked", true);
            $("#editcustomerReportN").prop("checked", true);
            $("#seecustomerReportN").prop("checked", true);

            $("#loginCustRepN").prop("checked", true);
            $("#deleteloginCustRepN").prop("checked", true);
            $("#editloginCustRepN").prop("checked", true);
            $("#seeloginCustRepN").prop("checked", true);

            $("#inActiveCustRepN").prop("checked", true);
            $("#deleteinActiveCustRepN").prop("checked", true);
            $("#editinActiveCustRepN").prop("checked", true);
            $("#seeinActiveCustRepN").prop("checked", true);

            $("#noAdminCustRepN").prop("checked", true);
            $("#deletenoAdminCustRepN").prop("checked", true);
            $("#editnoAdminCustRepN").prop("checked", true);
            $("#seenoAdminCustRepN").prop("checked", true);

            $("#returnedCustRepN").prop("checked", false);
            $("#deletereturnedCustRepN").prop("checked", false);
            $("#editreturnedCustRepN").prop("checked", false);
            $("#seereturnedCustRepN").prop("checked", false);

            $("#goodsReportN").prop("checked", true);
            $("#salegoodsReportN").prop("checked", true);
            $("#deletesalegoodsReportN").prop("checked", true);
            $("#editsalegoodsReportN").prop("checked", true);
            $("#seesalegoodsReportN").prop("checked", true);


            $("#returnedgoodsReportN").prop("checked", true);
            $("#deletereturnedgoodsReportN").prop("checked", true);
            $("#editreturnedgoodsReportN").prop("checked", true);
            $("#seereturnedgoodsReportN").prop("checked", true);


            $("#NoExistgoodsReportN").prop("checked", true);
            $("#deleteNoExistgoodsReportN").prop("checked", true);
            $("#editNoExistgoodsReportN").prop("checked", true);
            $("#seeNoExistgoodsReportN").prop("checked", true);


            $("#nosalegoodsReportN").prop("checked", true);
            $("#deletenosalegoodsReportN").prop("checked", true);
            $("#editnosalegoodsReportN").prop("checked", true);
            $("#seenosalegoodsReportN").prop("checked", true);


            $("#returnedReportgoodsReportN").prop("checked", true);
            $("#returnedNTasReportgoodsReportN").prop("checked", true);
            $("#deletereturnedNTasReportgoodsReportN").prop("checked", true);
            $("#editreturnedNTasReportgoodsReportN").prop("checked", true);
            $("#seereturnedNTasReportgoodsReportN").prop("checked", true);


            $("#tasgoodsReprtN").prop("checked", true);
            $("#deletetasgoodsReprtN").prop("checked", true);
            $("#edittasgoodsReprtN").prop("checked", true);
            $("#seetasgoodsReprtN").prop("checked", true);


            $("#goodsbargiriReportN").prop("checked", false);
            $("#deletegoodsbargiriReportN").prop("checked", false);
            $("#editgoodsbargiriReportN").prop("checked", false);
            $("#seegoodsbargiriReportN").prop("checked", false);
            //عملیات
            $("#oppN").prop("checked", false);
            $("#oppTakhsisN").prop("checked", false);
            $("#oppManagerN").prop("checked", false);
            $("#deleteManagerOppN").prop("checked", false);
            $("#editManagerOppN").prop("checked", false);
            $("#seeManagerOppN").prop("checked", false);


            $("#oppHeadN").prop("checked", false);
            $("#deleteHeadOppN").prop("checked", false);
            $("#editHeadOppN").prop("checked", false);
            $("#seeHeadOppN").prop("checked", false);


            $("#oppBazaryabN").prop("checked", false);
            $("#deleteBazaryabOppN").prop("checked", false);
            $("#editBazaryabOppN").prop("checked", false);
            $("#seeBazaryabOppN").prop("checked", false);


            $("#oppDriverN").prop("checked", false);
            $("#oppDriverServiceN").prop("checked", false);
            $("#deleteoppDriverServiceN").prop("checked", false);
            $("#editoppDriverServiceN").prop("checked", false);
            $("#seeoppDriverServiceN").prop("checked", false);


            $("#oppBargiriN").prop("checked", false);
            $("#deleteoppBargiriN").prop("checked", false);
            $("#editoppBargiriN").prop("checked", false);
            $("#seeoppBargiriN").prop("checked", false);

            $("#oppNazarSanjiN").prop("checked", true);
            $("#todayoppNazarsanjiN").prop("checked", true);
            $("#deletetodayoppNazarsanjiN").prop("checked", true);
            $("#edittodayoppNazarsanjiN").prop("checked", true);
            $("#seetodayoppNazarsanjiN").prop("checked", true);


            $("#pastoppNazarsanjiN").prop("checked", true);
            $("#deletepastoppNazarsanjiN").prop("checked", true);
            $("#editpastoppNazarsanjiN").prop("checked", true);
            $("#seepastoppNazarsanjiN").prop("checked", true);


            $("#DoneoppNazarsanjiN").prop("checked", true);
            $("#deleteDoneoppNazarsanjiN").prop("checked", true);
            $("#editDoneoppNazarsanjiN").prop("checked", true);
            $("#seeDoneoppNazarsanjiN").prop("checked", true);


            $("#OppupDownBonusN").prop("checked", false);
            $("#AddOppupDownBonusN").prop("checked", false);
            $("#deleteAddOppupDownBonusN").prop("checked", false);
            $("#editAddOppupDownBonusN").prop("checked", false);
            $("#seeAddOppupDownBonusN").prop("checked", false);


            $("#SubOppupDownBonusN").prop("checked", false);
            $("#deleteSubOppupDownBonusN").prop("checked", false);
            $("#editSubOppupDownBonusN").prop("checked", false);
            $("#seeSubOppupDownBonusN").prop("checked", false);


            $("#oppRDN").prop("checked", false);
            $("#AddedoppRDN").prop("checked", false);
            $("#deleteAddedoppRDN").prop("checked", false);
            $("#editAddedoppRDN").prop("checked", false);
            $("#seeAddedoppRDN").prop("checked", false);


            $("#NotAddedoppRDN").prop("checked", false);
            $("#deleteNotAddedoppRDN").prop("checked", false);
            $("#editNotAddedoppRDN").prop("checked", false);
            $("#seeNotAddedoppRDN").prop("checked", false);


            $("#oppCalendarN").prop("checked", true);
            $("#oppjustCalendarN").prop("checked", true);
            $("#deleteoppjustCalendarN").prop("checked", true);
            $("#editoppjustCalendarN").prop("checked", true);
            $("#seeoppjustCalendarN").prop("checked", true);


            $("#oppCustCalendarN").prop("checked", true);
            $("#deleteoppCustCalendarN").prop("checked", true);
            $("#editoppCustCalendarN").prop("checked", true);
            $("#seeoppCustCalendarN").prop("checked", true);


            $("#alarmoppN").prop("checked", true);
            $("#allalarmoppN").prop("checked", true);
            $("#deleteallalarmoppN").prop("checked", true);
            $("#editallalarmoppN").prop("checked", true);
            $("#seeallalarmoppN").prop("checked", true);


            $("#donealarmoppN").prop("checked", true);
            $("#deletedonealarmoppN").prop("checked", true);
            $("#editdonealarmoppN").prop("checked", true);
            $("#seedonealarmoppN").prop("checked", true);


            $("#NoalarmoppN").prop("checked", true);
            $("#deleteNoalarmoppN").prop("checked", true);
            $("#editNoalarmoppN").prop("checked", true);
            $("#seeNoalarmoppN").prop("checked", true);


            $("#massageOppN").prop("checked", true);
            $("#deletemassageOppN").prop("checked", true);
            $("#editmassageOppN").prop("checked", true);
            $("#seemassageOppN").prop("checked", true);


            $("#justBargiriOppN").prop("checked", false);
            $("#deletejustBargiriOppN").prop("checked", false);
            $("#editjustBargiriOppN").prop("checked", false);
            $("#seejustBargiriOppN").prop("checked", false);
            //تعریف عناصر
            $("#declareElementN").prop("checked", false);
            $("#editdeclareElementN").prop("checked", false);
            $("#deletedeclareElementN").prop("checked", false);
            $("#seedeclareElementN").prop("checked", false);
            //اطلاعات پایه
            $("#baseInfoN").prop("checked", true);
            $("#rdSentN").prop("checked", false);
            $("#infoRdN").prop("checked", false);
            $("#deleteSentRdN").prop("checked", false);
            $("#editSentRdN").prop("checked", false);
            $("#seeSentRdN").prop("checked", false);

            $("#rdNotSentN").prop("checked", false);
            $("#deleteRdNotSentN").prop("checked", false);
            $("#editRdNotSentN").prop("checked", false);
            $("#seeRdNotSentN").prop("checked", false);

            $("#deleteProfileN").prop("checked", true);
            $("#editProfileN").prop("checked", true);
            $("#seeProfileN").prop("checked", true);
            $("#baseInfoProfileN").prop("checked", true);

            $("#addSaleLineN").prop("checked", false);
            $("#deleteSaleLineN").prop("checked", false);
            $("#editSaleLineN").prop("checked", false);
            $("#seeSaleLineN").prop("checked", false);

            $("#baseInfoSettingN").prop("checked", false);
            $("#InfoSettingAccessN").prop("checked", false);
            $("#deleteSettingAccessN").prop("checked", false);
            $("#editSettingAccessN").prop("checked", false);
            $("#seeSettingAccessN").prop("checked", false);

            $("#InfoSettingTargetN").prop("checked", false);
            $("#deleteSettingTargetN").prop("checked", false);
            $("#editSettingTargetN").prop("checked", false);
            $("#seeSettingTargetN").prop("checked", false);
        } else {
            if (employeeType == 3) {
                $("#reportN").prop("checked", true);
                $("#amalKardreportN").prop("checked", true);

                $("#managerreportN").prop("checked", false);
                $("#deletemanagerreportN").prop("checked", false);
                $("#editmanagerreportN").prop("checked", false);
                $("#seemanagerreportN").prop("checked", false);


                $("#HeadreportN").prop("checked", false);
                $("#deleteHeadreportN").prop("checked", false);
                $("#editHeadreportN").prop("checked", false);
                $("#seeHeadreportN").prop("checked", false);


                $("#poshtibanreportN").prop("checked", false);
                $("#deleteposhtibanreportN").prop("checked", false);
                $("#editposhtibanreportN").prop("checked", false);
                $("#seeposhtibanreportN").prop("checked", false);


                $("#bazaryabreportN").prop("checked", false);
                $("#deletebazaryabreportN").prop("checked", false);
                $("#editbazaryabreportN").prop("checked", false);
                $("#seebazaryabreportN").prop("checked", false);


                $("#reportDriverN").prop("checked", false);
                $("#deletereportDriverN").prop("checked", false);
                $("#editreportDriverN").prop("checked", false);
                $("#seereportDriverN").prop("checked", false);


                $("#trazEmployeeReportN").prop("checked", false);
                $("#deletetrazEmployeeReportN").prop("checked", false);
                $("#edittrazEmployeeReportN").prop("checked", false);
                $("#seetrazEmployeeReportN").prop("checked", false);


                $("#amalkardCustReportN").prop("checked", true);

                $("#customerReportN").prop("checked", true);
                $("#deletecustomerReportN").prop("checked", true);
                $("#editcustomerReportN").prop("checked", true);
                $("#seecustomerReportN").prop("checked", true);

                $("#loginCustRepN").prop("checked", true);
                $("#deleteloginCustRepN").prop("checked", true);
                $("#editloginCustRepN").prop("checked", true);
                $("#seeloginCustRepN").prop("checked", true);

                $("#inActiveCustRepN").prop("checked", true);
                $("#deleteinActiveCustRepN").prop("checked", true);
                $("#editinActiveCustRepN").prop("checked", true);
                $("#seeinActiveCustRepN").prop("checked", true);

                $("#noAdminCustRepN").prop("checked", true);
                $("#deletenoAdminCustRepN").prop("checked", true);
                $("#editnoAdminCustRepN").prop("checked", true);
                $("#seenoAdminCustRepN").prop("checked", true);

                $("#returnedCustRepN").prop("checked", false);
                $("#deletereturnedCustRepN").prop("checked", false);
                $("#editreturnedCustRepN").prop("checked", false);
                $("#seereturnedCustRepN").prop("checked", false);

                $("#goodsReportN").prop("checked", true);
                $("#salegoodsReportN").prop("checked", true);
                $("#deletesalegoodsReportN").prop("checked", true);
                $("#editsalegoodsReportN").prop("checked", true);
                $("#seesalegoodsReportN").prop("checked", true);


                $("#returnedgoodsReportN").prop("checked", true);
                $("#deletereturnedgoodsReportN").prop("checked", true);
                $("#editreturnedgoodsReportN").prop("checked", true);
                $("#seereturnedgoodsReportN").prop("checked", true);


                $("#NoExistgoodsReportN").prop("checked", true);
                $("#deleteNoExistgoodsReportN").prop("checked", true);
                $("#editNoExistgoodsReportN").prop("checked", true);
                $("#seeNoExistgoodsReportN").prop("checked", true);


                $("#nosalegoodsReportN").prop("checked", true);
                $("#deletenosalegoodsReportN").prop("checked", true);
                $("#editnosalegoodsReportN").prop("checked", true);
                $("#seenosalegoodsReportN").prop("checked", true);


                $("#returnedReportgoodsReportN").prop("checked", true);
                $("#returnedNTasReportgoodsReportN").prop("checked", true);
                $("#deletereturnedNTasReportgoodsReportN").prop("checked", true);
                $("#editreturnedNTasReportgoodsReportN").prop("checked", true);
                $("#seereturnedNTasReportgoodsReportN").prop("checked", true);


                $("#tasgoodsReprtN").prop("checked", true);
                $("#deletetasgoodsReprtN").prop("checked", true);
                $("#edittasgoodsReprtN").prop("checked", true);
                $("#seetasgoodsReprtN").prop("checked", true);


                $("#goodsbargiriReportN").prop("checked", false);
                $("#deletegoodsbargiriReportN").prop("checked", false);
                $("#editgoodsbargiriReportN").prop("checked", false);
                $("#seegoodsbargiriReportN").prop("checked", false);
                //عملیات
                $("#oppN").prop("checked", false);
                $("#oppTakhsisN").prop("checked", false);
                $("#oppManagerN").prop("checked", false);
                $("#deleteManagerOppN").prop("checked", false);
                $("#editManagerOppN").prop("checked", false);
                $("#seeManagerOppN").prop("checked", false);


                $("#oppHeadN").prop("checked", false);
                $("#deleteHeadOppN").prop("checked", false);
                $("#editHeadOppN").prop("checked", false);
                $("#seeHeadOppN").prop("checked", false);


                $("#oppBazaryabN").prop("checked", false);
                $("#deleteBazaryabOppN").prop("checked", false);
                $("#editBazaryabOppN").prop("checked", false);
                $("#seeBazaryabOppN").prop("checked", false);


                $("#oppDriverN").prop("checked", false);
                $("#oppDriverServiceN").prop("checked", false);
                $("#deleteoppDriverServiceN").prop("checked", false);
                $("#editoppDriverServiceN").prop("checked", false);
                $("#seeoppDriverServiceN").prop("checked", false);


                $("#oppBargiriN").prop("checked", false);
                $("#deleteoppBargiriN").prop("checked", false);
                $("#editoppBargiriN").prop("checked", false);
                $("#seeoppBargiriN").prop("checked", false);

                $("#oppNazarSanjiN").prop("checked", true);
                $("#todayoppNazarsanjiN").prop("checked", true);
                $("#deletetodayoppNazarsanjiN").prop("checked", true);
                $("#edittodayoppNazarsanjiN").prop("checked", true);
                $("#seetodayoppNazarsanjiN").prop("checked", true);


                $("#pastoppNazarsanjiN").prop("checked", true);
                $("#deletepastoppNazarsanjiN").prop("checked", true);
                $("#editpastoppNazarsanjiN").prop("checked", true);
                $("#seepastoppNazarsanjiN").prop("checked", true);


                $("#DoneoppNazarsanjiN").prop("checked", true);
                $("#deleteDoneoppNazarsanjiN").prop("checked", true);
                $("#editDoneoppNazarsanjiN").prop("checked", true);
                $("#seeDoneoppNazarsanjiN").prop("checked", true);


                $("#OppupDownBonusN").prop("checked", false);
                $("#AddOppupDownBonusN").prop("checked", false);
                $("#deleteAddOppupDownBonusN").prop("checked", false);
                $("#editAddOppupDownBonusN").prop("checked", false);
                $("#seeAddOppupDownBonusN").prop("checked", false);


                $("#SubOppupDownBonusN").prop("checked", false);
                $("#deleteSubOppupDownBonusN").prop("checked", false);
                $("#editSubOppupDownBonusN").prop("checked", false);
                $("#seeSubOppupDownBonusN").prop("checked", false);


                $("#oppRDN").prop("checked", false);
                $("#AddedoppRDN").prop("checked", false);
                $("#deleteAddedoppRDN").prop("checked", false);
                $("#editAddedoppRDN").prop("checked", false);
                $("#seeAddedoppRDN").prop("checked", false);


                $("#NotAddedoppRDN").prop("checked", false);
                $("#deleteNotAddedoppRDN").prop("checked", false);
                $("#editNotAddedoppRDN").prop("checked", false);
                $("#seeNotAddedoppRDN").prop("checked", false);


                $("#oppCalendarN").prop("checked", true);
                $("#oppjustCalendarN").prop("checked", true);
                $("#deleteoppjustCalendarN").prop("checked", true);
                $("#editoppjustCalendarN").prop("checked", true);
                $("#seeoppjustCalendarN").prop("checked", true);


                $("#oppCustCalendarN").prop("checked", true);
                $("#deleteoppCustCalendarN").prop("checked", true);
                $("#editoppCustCalendarN").prop("checked", true);
                $("#seeoppCustCalendarN").prop("checked", true);


                $("#alarmoppN").prop("checked", true);
                $("#allalarmoppN").prop("checked", true);
                $("#deleteallalarmoppN").prop("checked", true);
                $("#editallalarmoppN").prop("checked", true);
                $("#seeallalarmoppN").prop("checked", true);


                $("#donealarmoppN").prop("checked", true);
                $("#deletedonealarmoppN").prop("checked", true);
                $("#editdonealarmoppN").prop("checked", true);
                $("#seedonealarmoppN").prop("checked", true);


                $("#NoalarmoppN").prop("checked", true);
                $("#deleteNoalarmoppN").prop("checked", true);
                $("#editNoalarmoppN").prop("checked", true);
                $("#seeNoalarmoppN").prop("checked", true);


                $("#massageOppN").prop("checked", true);
                $("#deletemassageOppN").prop("checked", true);
                $("#editmassageOppN").prop("checked", true);
                $("#seemassageOppN").prop("checked", true);


                $("#justBargiriOppN").prop("checked", false);
                $("#deletejustBargiriOppN").prop("checked", false);
                $("#editjustBargiriOppN").prop("checked", false);
                $("#seejustBargiriOppN").prop("checked", false);
                //تعریف عناصر
                $("#declareElementN").prop("checked", false);
                $("#editdeclareElementN").prop("checked", false);
                $("#deletedeclareElementN").prop("checked", false);
                $("#seedeclareElementN").prop("checked", false);
                //اطلاعات پایه
                $("#baseInfoN").prop("checked", true);
                $("#rdSentN").prop("checked", false);
                $("#infoRdN").prop("checked", false);
                $("#deleteSentRdN").prop("checked", false);
                $("#editSentRdN").prop("checked", false);
                $("#seeSentRdN").prop("checked", false);

                $("#rdNotSentN").prop("checked", false);
                $("#deleteRdNotSentN").prop("checked", false);
                $("#editRdNotSentN").prop("checked", false);
                $("#seeRdNotSentN").prop("checked", false);

                $("#deleteProfileN").prop("checked", true);
                $("#editProfileN").prop("checked", true);
                $("#seeProfileN").prop("checked", true);
                $("#baseInfoProfileN").prop("checked", true);

                $("#addSaleLineN").prop("checked", false);
                $("#deleteSaleLineN").prop("checked", false);
                $("#editSaleLineN").prop("checked", false);
                $("#seeSaleLineN").prop("checked", false);

                $("#baseInfoSettingN").prop("checked", false);
                $("#InfoSettingAccessN").prop("checked", false);
                $("#deleteSettingAccessN").prop("checked", false);
                $("#editSettingAccessN").prop("checked", false);
                $("#seeSettingAccessN").prop("checked", false);

                $("#InfoSettingTargetN").prop("checked", false);
                $("#deleteSettingTargetN").prop("checked", false);
                $("#editSettingTargetN").prop("checked", false);
                $("#seeSettingTargetN").prop("checked", false);
            }
        }
    }
}

function setAccessLevel(employeeType) {

    if (employeeType == 1) {
        //گزارشات
        $("#reportN").prop("checked", true);
        $("#amalKardreportN").prop("checked", true);
        $("#managerreportN").prop("checked", true);
        $("#deletemanagerreportN").prop("checked", true);
        $("#editmanagerreportN").prop("checked", true);
        $("#seemanagerreportN").prop("checked", true);


        $("#HeadreportN").prop("checked", true);
        $("#deleteHeadreportN").prop("checked", true);
        $("#editHeadreportN").prop("checked", true);
        $("#seeHeadreportN").prop("checked", true);


        $("#poshtibanreportN").prop("checked", true);
        $("#deleteposhtibanreportN").prop("checked", true);
        $("#editposhtibanreportN").prop("checked", true);
        $("#seeposhtibanreportN").prop("checked", true);


        $("#bazaryabreportN").prop("checked", true);
        $("#deletebazaryabreportN").prop("checked", true);
        $("#editbazaryabreportN").prop("checked", true);
        $("#seebazaryabreportN").prop("checked", true);


        $("#reportDriverN").prop("checked", true);
        $("#deletereportDriverN").prop("checked", true);
        $("#editreportDriverN").prop("checked", true);
        $("#seereportDriverN").prop("checked", true);


        $("#trazEmployeeReportN").prop("checked", true);
        $("#deletetrazEmployeeReportN").prop("checked", true);
        $("#edittrazEmployeeReportN").prop("checked", true);
        $("#seetrazEmployeeReportN").prop("checked", true);


        $("#amalkardCustReportN").prop("checked", true);

        $("#customerReportN").prop("checked", true);
        $("#deletecustomerReportN").prop("checked", true);
        $("#editcustomerReportN").prop("checked", true);
        $("#seecustomerReportN").prop("checked", true);

        $("#loginCustRepN").prop("checked", true);
        $("#deleteloginCustRepN").prop("checked", true);
        $("#editloginCustRepN").prop("checked", true);
        $("#seeloginCustRepN").prop("checked", true);

        $("#inActiveCustRepN").prop("checked", true);
        $("#deleteinActiveCustRepN").prop("checked", true);
        $("#editinActiveCustRepN").prop("checked", true);
        $("#seeinActiveCustRepN").prop("checked", true);

        $("#noAdminCustRepN").prop("checked", true);
        $("#deletenoAdminCustRepN").prop("checked", true);
        $("#editnoAdminCustRepN").prop("checked", true);
        $("#seenoAdminCustRepN").prop("checked", true);

        $("#returnedCustRepN").prop("checked", true);
        $("#deletereturnedCustRepN").prop("checked", true);
        $("#editreturnedCustRepN").prop("checked", true);
        $("#seereturnedCustRepN").prop("checked", true);

        $("#goodsReportN").prop("checked", true);
        $("#salegoodsReportN").prop("checked", true);
        $("#deletesalegoodsReportN").prop("checked", true);
        $("#editsalegoodsReportN").prop("checked", true);
        $("#seesalegoodsReportN").prop("checked", true);


        $("#returnedgoodsReportN").prop("checked", true);
        $("#deletereturnedgoodsReportN").prop("checked", true);
        $("#editturnedgoodsReportN").prop("checked", true);
        $("#seereturnedgoodsReportN").prop("checked", true);


        $("#NoExistgoodsReportN").prop("checked", true);
        $("#deleteNoExistgoodsReportN").prop("checked", true);
        $("#editNoExistgoodsReportN").prop("checked", true);
        $("#seeNoExistgoodsReportN").prop("checked", true);


        $("#nosalegoodsReportN").prop("checked", true);
        $("#deletenosalegoodsReportN").prop("checked", true);
        $("#editnosalegoodsReportN").prop("checked", true);
        $("#seenosalegoodsReportN").prop("checked", true);


        $("#returnedReportgoodsReportN").prop("checked", true);
        $("#returnedNTasReportgoodsReportN").prop("checked", true);
        $("#deletereturnedNTasReportgoodsReportN").prop("checked", true);
        $("#editreturnedgoodsReportN").prop("checked", true);
        $("#seereturnedNTasReportgoodsReportN").prop("checked", true);


        $("#tasgoodsReprtN").prop("checked", true);
        $("#deletetasgoodsReprtN").prop("checked", true);
        $("#edittasgoodsReprtN").prop("checked", true);
        $("#seetasgoodsReprtN").prop("checked", true);


        $("#goodsbargiriReportN").prop("checked", true);
        $("#deletegoodsbargiriReportN").prop("checked", true);
        $("#editgoodsbargiriReportN").prop("checked", true);
        $("#seegoodsbargiriReportN").prop("checked", true);
        //عملیات
        $("#oppN").prop("checked", true);
        $("#oppTakhsisN").prop("checked", true);
        $("#oppManagerN").prop("checked", true);
        $("#deleteManagerOppN").prop("checked", true);
        $("#editManagerOppN").prop("checked", true);
        $("#seeManagerOppN").prop("checked", true);


        $("#oppHeadN").prop("checked", true);
        $("#deleteHeadOppN").prop("checked", true);
        $("#editHeadOppN").prop("checked", true);
        $("#seeHeadOppN").prop("checked", true);


        $("#oppBazaryabN").prop("checked", true);
        $("#deleteBazaryabOppN").prop("checked", true);
        $("#editBazaryabOppN").prop("checked", true);
        $("#seeBazaryabOppN").prop("checked", true);


        $("#oppDriverN").prop("checked", true);
        $("#oppDriverServiceN").prop("checked", true);
        $("#deleteoppDriverServiceN").prop("checked", true);
        $("#editoppDriverServiceN").prop("checked", true);
        $("#seeoppDriverServiceN").prop("checked", true);


        $("#oppBargiriN").prop("checked", true);
        $("#deleteoppBargiriN").prop("checked", true);
        $("#editoppBargiriN").prop("checked", true);
        $("#seeoppBargiriN").prop("checked", true);

        $("#oppNazarSanjiN").prop("checked", true);
        $("#todayoppNazarsanjiN").prop("checked", true);
        $("#deletetodayoppNazarsanjiN").prop("checked", true);
        $("#edittodayoppNazarsanjiN").prop("checked", true);
        $("#seetodayoppNazarsanjiN").prop("checked", true);


        $("#pastoppNazarsanjiN").prop("checked", true);
        $("#deletepastoppNazarsanjiN").prop("checked", true);
        $("#editpastoppNazarsanjiN").prop("checked", true);
        $("#seepastoppNazarsanjiN").prop("checked", true);


        $("#DoneoppNazarsanjiN").prop("checked", true);
        $("#deleteDoneoppNazarsanjiN").prop("checked", true);
        $("#editDoneoppNazarsanjiN").prop("checked", true);
        $("#seeDoneoppNazarsanjiN").prop("checked", true);


        $("#OppupDownBonusN").prop("checked", true);
        $("#AddOppupDownBonusN").prop("checked", true);
        $("#deleteAddOppupDownBonusN").prop("checked", true);
        $("#editAddOppupDownBonusN").prop("checked", true);
        $("#seeAddOppupDownBonusN").prop("checked", true);


        $("#SubOppupDownBonusN").prop("checked", true);
        $("#deleteSubOppupDownBonusN").prop("checked", true);
        $("#editSubOppupDownBonusN").prop("checked", true);
        $("#seeSubOppupDownBonusN").prop("checked", true);


        $("#oppRDN").prop("checked", true);
        $("#AddedoppRDN").prop("checked", true);
        $("#deleteAddedoppRDN").prop("checked", true);
        $("#editAddedoppRDN").prop("checked", true);
        $("#seeAddedoppRDN").prop("checked", true);


        $("#NotAddedoppRDN").prop("checked", true);
        $("#deleteNotAddedoppRDN").prop("checked", true);
        $("#editNotAddedoppRDN").prop("checked", true);
        $("#seeNotAddedoppRDN").prop("checked", true);


        $("#oppCalendarN").prop("checked", true);
        $("#oppjustCalendarN").prop("checked", true);
        $("#deleteoppjustCalendarN").prop("checked", true);
        $("#editoppjustCalendarN").prop("checked", true);
        $("#seeoppjustCalendarN").prop("checked", true);


        $("#oppCustCalendarN").prop("checked", true);
        $("#deleteoppCustCalendarN").prop("checked", true);
        $("#editoppCustCalendarN").prop("checked", true);
        $("#seeoppCustCalendarN").prop("checked", true);


        $("#alarmoppN").prop("checked", true);
        $("#allalarmoppN").prop("checked", true);
        $("#deleteallalarmoppN").prop("checked", true);
        $("#editallalarmoppN").prop("checked", true);
        $("#seeallalarmoppN").prop("checked", true);


        $("#donealarmoppN").prop("checked", true);
        $("#deletedonealarmoppN").prop("checked", true);
        $("#editdonealarmoppN").prop("checked", true);
        $("#seedonealarmoppN").prop("checked", true);


        $("#NoalarmoppN").prop("checked", true);
        $("#deleteNoalarmoppN").prop("checked", true);
        $("#editNoalarmoppN").prop("checked", true);
        $("#seeNoalarmoppN").prop("checked", true);


        $("#massageOppN").prop("checked", true);
        $("#deletemassageOppN").prop("checked", true);
        $("#editmassageOppN").prop("checked", true);
        $("#seemassageOppN").prop("checked", true);


        $("#justBargiriOppN").prop("checked", true);
        $("#deletejustBargiriOppN").prop("checked", true);
        $("#editjustBargiriOppN").prop("checked", true);
        $("#seejustBargiriOppN").prop("checked", true);
        //تعریف عناصر
        $("#declareElementN").prop("checked", true);
        $("#editdeclareElementN").prop("checked", true);
        $("#deletedeclareElementN").prop("checked", true);
        $("#seedeclareElementN").prop("checked", true);
        //اطلاعات پایه
        $("#baseInfoN").prop("checked", true);
        $("#rdSentN").prop("checked", true);
        $("#infoRdN").prop("checked", true);
        $("#deleteSentRdN").prop("checked", true);
        $("#editSentRdN").prop("checked", true);
        $("#seeSentRdN").prop("checked", true);

        $("#rdNotSentN").prop("checked", true);
        $("#deleteRdNotSentN").prop("checked", true);
        $("#editRdNotSentN").prop("checked", true);
        $("#seeRdNotSentN").prop("checked", true);

        $("#deleteProfileN").prop("checked", true);
        $("#editProfileN").prop("checked", true);
        $("#seeProfileN").prop("checked", true);
        $("#baseInfoProfileN").prop("checked", true);

        $("#addSaleLineN").prop("checked", true);
        $("#deleteSaleLineN").prop("checked", true);
        $("#editSaleLineN").prop("checked", true);
        $("#seeSaleLineN").prop("checked", true);

        $("#baseInfoSettingN").prop("checked", true);
        $("#InfoSettingAccessN").prop("checked", true);
        $("#deleteSettingAccessN").prop("checked", true);
        $("#editSettingAccessN").prop("checked", true);
        $("#seeSettingAccessN").prop("checked", true);

        $("#InfoSettingTargetN").prop("checked", true);
        $("#deleteSettingTargetN").prop("checked", true);
        $("#editSettingTargetN").prop("checked", true);
        $("#seeSettingTargetN").prop("checked", true);
    } else {
        if (employeeType == 2) {

            $("#reportN").prop("checked", true);
            $("#amalKardreportN").prop("checked", true);

            $("#managerreportN").prop("checked", false);
            $("#deletemanagerreportN").prop("checked", false);
            $("#editmanagerreportN").prop("checked", false);
            $("#seemanagerreportN").prop("checked", false);


            $("#HeadreportN").prop("checked", false);
            $("#deleteHeadreportN").prop("checked", false);
            $("#editHeadreportN").prop("checked", false);
            $("#seeHeadreportN").prop("checked", false);


            $("#poshtibanreportN").prop("checked", true);
            $("#deleteposhtibanreportN").prop("checked", true);
            $("#editposhtibanreportN").prop("checked", true);
            $("#seeposhtibanreportN").prop("checked", true);


            $("#bazaryabreportN").prop("checked", true);
            $("#deletebazaryabreportN").prop("checked", true);
            $("#editbazaryabreportN").prop("checked", true);
            $("#seebazaryabreportN").prop("checked", true);


            $("#reportDriverN").prop("checked", true);
            $("#deletereportDriverN").prop("checked", true);
            $("#editreportDriverN").prop("checked", true);
            $("#seereportDriverN").prop("checked", true);


            $("#trazEmployeeReportN").prop("checked", true);
            $("#deletetrazEmployeeReportN").prop("checked", true);
            $("#edittrazEmployeeReportN").prop("checked", true);
            $("#seetrazEmployeeReportN").prop("checked", true);


            $("#amalkardCustReportN").prop("checked", true);

            $("#customerReportN").prop("checked", true);
            $("#deletecustomerReportN").prop("checked", true);
            $("#editcustomerReportN").prop("checked", true);
            $("#seecustomerReportN").prop("checked", true);

            $("#loginCustRepN").prop("checked", true);
            $("#deleteloginCustRepN").prop("checked", true);
            $("#editloginCustRepN").prop("checked", true);
            $("#seeloginCustRepN").prop("checked", true);

            $("#inActiveCustRepN").prop("checked", true);
            $("#deleteinActiveCustRepN").prop("checked", true);
            $("#editinActiveCustRepN").prop("checked", true);
            $("#seeinActiveCustRepN").prop("checked", true);

            $("#noAdminCustRepN").prop("checked", true);
            $("#deletenoAdminCustRepN").prop("checked", true);
            $("#editnoAdminCustRepN").prop("checked", true);
            $("#seenoAdminCustRepN").prop("checked", true);

            $("#returnedCustRepN").prop("checked", true);
            $("#deletereturnedCustRepN").prop("checked", true);
            $("#editreturnedCustRepN").prop("checked", true);
            $("#seereturnedCustRepN").prop("checked", true);

            $("#goodsReportN").prop("checked", true);
            $("#salegoodsReportN").prop("checked", true);
            $("#deletesalegoodsReportN").prop("checked", true);
            $("#editsalegoodsReportN").prop("checked", true);
            $("#seesalegoodsReportN").prop("checked", true);


            $("#returnedgoodsReportN").prop("checked", true);
            $("#deletereturnedgoodsReportN").prop("checked", true);
            $("#editreturnedgoodsReportN").prop("checked", true);
            $("#seereturnedgoodsReportN").prop("checked", true);


            $("#NoExistgoodsReportN").prop("checked", true);
            $("#deleteNoExistgoodsReportN").prop("checked", true);
            $("#editNoExistgoodsReportN").prop("checked", true);
            $("#seeNoExistgoodsReportN").prop("checked", true);


            $("#nosalegoodsReportN").prop("checked", true);
            $("#deletenosalegoodsReportN").prop("checked", true);
            $("#editnosalegoodsReportN").prop("checked", true);
            $("#seenosalegoodsReportN").prop("checked", true);


            $("#returnedReportgoodsReportN").prop("checked", true);
            $("#returnedNTasReportgoodsReportN").prop("checked", true);
            $("#deletereturnedNTasReportgoodsReportN").prop("checked", true);
            $("#editreturnedNTasReportgoodsReportN").prop("checked", true);
            $("#seereturnedNTasReportgoodsReportN").prop("checked", true);


            $("#tasgoodsReprtN").prop("checked", true);
            $("#deletetasgoodsReprtN").prop("checked", true);
            $("#edittasgoodsReprtN").prop("checked", true);
            $("#seetasgoodsReprtN").prop("checked", true);


            $("#goodsbargiriReportN").prop("checked", true);
            $("#deletegoodsbargiriReportN").prop("checked", true);
            $("#editgoodsbargiriReportN").prop("checked", true);
            $("#seegoodsbargiriReportN").prop("checked", true);
            //عملیات
            $("#oppN").prop("checked", true);
            $("#oppTakhsisN").prop("checked", true);
            $("#oppManagerN").prop("checked", false);
            $("#deleteManagerOppN").prop("checked", false);
            $("#editManagerOppN").prop("checked", false);
            $("#seeManagerOppN").prop("checked", false);


            $("#oppHeadN").prop("checked", false);
            $("#deleteHeadOppN").prop("checked", false);
            $("#editHeadOppN").prop("checked", false);
            $("#seeHeadOppN").prop("checked", false);


            $("#oppBazaryabN").prop("checked", true);
            $("#deleteBazaryabOppN").prop("checked", true);
            $("#editBazaryabOppN").prop("checked", true);
            $("#seeBazaryabOppN").prop("checked", true);


            $("#oppDriverN").prop("checked", true);
            $("#oppDriverServiceN").prop("checked", true);
            $("#deleteoppDriverServiceN").prop("checked", true);
            $("#editoppDriverServiceN").prop("checked", true);
            $("#seeoppDriverServiceN").prop("checked", true);


            $("#oppBargiriN").prop("checked", false);
            $("#deleteoppBargiriN").prop("checked", false);
            $("#editoppBargiriN").prop("checked", false);
            $("#seeoppBargiriN").prop("checked", false);

            $("#oppNazarSanjiN").prop("checked", true);
            $("#todayoppNazarsanjiN").prop("checked", true);
            $("#deletetodayoppNazarsanjiN").prop("checked", true);
            $("#edittodayoppNazarsanjiN").prop("checked", true);
            $("#seetodayoppNazarsanjiN").prop("checked", true);


            $("#pastoppNazarsanjiN").prop("checked", true);
            $("#deletepastoppNazarsanjiN").prop("checked", true);
            $("#editpastoppNazarsanjiN").prop("checked", true);
            $("#seepastoppNazarsanjiN").prop("checked", true);


            $("#DoneoppNazarsanjiN").prop("checked", true);
            $("#deleteDoneoppNazarsanjiN").prop("checked", true);
            $("#editDoneoppNazarsanjiN").prop("checked", true);
            $("#seeDoneoppNazarsanjiN").prop("checked", true);


            $("#OppupDownBonusN").prop("checked", false);
            $("#AddOppupDownBonusN").prop("checked", false);
            $("#deleteAddOppupDownBonusN").prop("checked", false);
            $("#editAddOppupDownBonusN").prop("checked", false);
            $("#seeAddOppupDownBonusN").prop("checked", false);


            $("#SubOppupDownBonusN").prop("checked", false);
            $("#deleteSubOppupDownBonusN").prop("checked", false);
            $("#editSubOppupDownBonusN").prop("checked", false);
            $("#seeSubOppupDownBonusN").prop("checked", false);


            $("#oppRDN").prop("checked", false);
            $("#AddedoppRDN").prop("checked", false);
            $("#deleteAddedoppRDN").prop("checked", false);
            $("#editAddedoppRDN").prop("checked", false);
            $("#seeAddedoppRDN").prop("checked", false);


            $("#NotAddedoppRDN").prop("checked", false);
            $("#deleteNotAddedoppRDN").prop("checked", false);
            $("#editNotAddedoppRDN").prop("checked", false);
            $("#seeNotAddedoppRDN").prop("checked", false);


            $("#oppCalendarN").prop("checked", true);
            $("#oppjustCalendarN").prop("checked", false);
            $("#deleteoppjustCalendarN").prop("checked", false);
            $("#editoppjustCalendarN").prop("checked", false);
            $("#seeoppjustCalendarN").prop("checked", false);


            $("#oppCustCalendarN").prop("checked", true);
            $("#deleteoppCustCalendarN").prop("checked", true);
            $("#editoppCustCalendarN").prop("checked", true);
            $("#seeoppCustCalendarN").prop("checked", true);


            $("#alarmoppN").prop("checked", true);
            $("#allalarmoppN").prop("checked", true);
            $("#deleteallalarmoppN").prop("checked", true);
            $("#editallalarmoppN").prop("checked", true);
            $("#seeallalarmoppN").prop("checked", true);


            $("#donealarmoppN").prop("checked", true);
            $("#deletedonealarmoppN").prop("checked", true);
            $("#editdonealarmoppN").prop("checked", true);
            $("#seedonealarmoppN").prop("checked", true);


            $("#NoalarmoppN").prop("checked", true);
            $("#deleteNoalarmoppN").prop("checked", true);
            $("#editNoalarmoppN").prop("checked", true);
            $("#seeNoalarmoppN").prop("checked", true);


            $("#massageOppN").prop("checked", true);
            $("#deletemassageOppN").prop("checked", true);
            $("#editmassageOppN").prop("checked", true);
            $("#seemassageOppN").prop("checked", true);


            $("#justBargiriOppN").prop("checked", false);
            $("#deletejustBargiriOppN").prop("checked", false);
            $("#editjustBargiriOppN").prop("checked", false);
            $("#seejustBargiriOppN").prop("checked", false);
            //تعریف عناصر
            $("#declareElementN").prop("checked", false);
            $("#editdeclareElementN").prop("checked", false);
            $("#deletedeclareElementN").prop("checked", false);
            $("#seedeclareElementN").prop("checked", false);
            //اطلاعات پایه
            $("#baseInfoN").prop("checked", true);
            $("#rdSentN").prop("checked", false);
            $("#infoRdN").prop("checked", false);
            $("#deleteSentRdN").prop("checked", false);
            $("#editSentRdN").prop("checked", false);
            $("#seeSentRdN").prop("checked", false);

            $("#rdNotSentN").prop("checked", false);
            $("#deleteRdNotSentN").prop("checked", false);
            $("#editRdNotSentN").prop("checked", false);
            $("#seeRdNotSentN").prop("checked", false);

            $("#deleteProfileN").prop("checked", true);
            $("#editProfileN").prop("checked", true);
            $("#seeProfileN").prop("checked", true);
            $("#baseInfoProfileN").prop("checked", true);

            $("#addSaleLineN").prop("checked", false);
            $("#deleteSaleLineN").prop("checked", false);
            $("#editSaleLineN").prop("checked", false);
            $("#seeSaleLineN").prop("checked", false);

            $("#baseInfoSettingN").prop("checked", false);
            $("#InfoSettingAccessN").prop("checked", false);
            $("#deleteSettingAccessN").prop("checked", false);
            $("#editSettingAccessN").prop("checked", false);
            $("#seeSettingAccessN").prop("checked", false);

            $("#InfoSettingTargetN").prop("checked", false);
            $("#deleteSettingTargetN").prop("checked", false);
            $("#editSettingTargetN").prop("checked", false);
            $("#seeSettingTargetN").prop("checked", false);
        } else {
            $("#reportN").prop("checked", false);
            $("#amalKardreportN").prop("checked", false);
            $("#managerreportN").prop("checked", false);
            $("#deletemanagerreportN").prop("checked", false);
            $("#editmanagerreportN").prop("checked", false);
            $("#seemanagerreportN").prop("checked", false);


            $("#HeadreportN").prop("checked", false);
            $("#deleteHeadreportN").prop("checked", false);
            $("#editHeadreportN").prop("checked", false);
            $("#seeHeadreportN").prop("checked", false);


            $("#poshtibanreportN").prop("checked", false);
            $("#deleteposhtibanreportN").prop("checked", false);
            $("#editposhtibanreportN").prop("checked", false);
            $("#seeposhtibanreportN").prop("checked", false);


            $("#bazaryabreportN").prop("checked", false);
            $("#deletebazaryabreportN").prop("checked", false);
            $("#editbazaryabreportN").prop("checked", false);
            $("#seebazaryabreportN").prop("checked", false);


            $("#reportDriverN").prop("checked", false);
            $("#deletereportDriverN").prop("checked", false);
            $("#editreportDriverN").prop("checked", false);
            $("#seereportDriverN").prop("checked", false);


            $("#trazEmployeeReportN").prop("checked", false);
            $("#deletetrazEmployeeReportN").prop("checked", false);
            $("#edittrazEmployeeReportN").prop("checked", false);
            $("#seetrazEmployeeReportN").prop("checked", false);


            $("#amalkardCustReportN").prop("checked", false);

            $("#customerReportN").prop("checked", false);
            $("#deletecustomerReportN").prop("checked", false);
            $("#editcustomerReportN").prop("checked", false);
            $("#seecustomerReportN").prop("checked", false);

            $("#loginCustRepN").prop("checked", false);
            $("#deleteloginCustRepN").prop("checked", false);
            $("#editloginCustRepN").prop("checked", false);
            $("#seeloginCustRepN").prop("checked", false);

            $("#inActiveCustRepN").prop("checked", false);
            $("#deleteinActiveCustRepN").prop("checked", false);
            $("#editinActiveCustRepN").prop("checked", false);
            $("#seeinActiveCustRepN").prop("checked", false);

            $("#noAdminCustRepN").prop("checked", false);
            $("#deletenoAdminCustRepN").prop("checked", false);
            $("#editnoAdminCustRepN").prop("checked", false);
            $("#seenoAdminCustRepN").prop("checked", false);

            $("#returnedCustRepN").prop("checked", false);
            $("#deletereturnedCustRepN").prop("checked", false);
            $("#editreturnedCustRepN").prop("checked", false);
            $("#seereturnedCustRepN").prop("checked", false);

            $("#goodsReportN").prop("checked", false);
            $("#salegoodsReportN").prop("checked", false);
            $("#deletesalegoodsReportN").prop("checked", false);
            $("#editsalegoodsReportN").prop("checked", false);
            $("#seesalegoodsReportN").prop("checked", false);


            $("#returnedgoodsReportN").prop("checked", false);
            $("#deletereturnedgoodsReportN").prop("checked", false);
            $("#editreturnedgoodsReportN").prop("checked", false);
            $("#seereturnedgoodsReportN").prop("checked", false);


            $("#NoExistgoodsReportN").prop("checked", false);
            $("#deleteNoExistgoodsReportN").prop("checked", false);
            $("#editNoExistgoodsReportN").prop("checked", false);
            $("#seeNoExistgoodsReportN").prop("checked", false);


            $("#nosalegoodsReportN").prop("checked", false);
            $("#deletenosalegoodsReportN").prop("checked", false);
            $("#editnosalegoodsReportN").prop("checked", false);
            $("#seenosalegoodsReportN").prop("checked", false);

            $("#returnedReportgoodsReportN").prop("checked", false);
            $("#returnedNTasReportgoodsReportN").prop("checked", false);
            $("#deletereturnedNTasReportgoodsReportN").prop("checked", false);
            $("#editreturnedNTasReportgoodsReportN").prop("checked", false);
            $("#seereturnedNTasReportgoodsReportN").prop("checked", false);


            $("#tasgoodsReprtN").prop("checked", false);
            $("#deletetasgoodsReprtN").prop("checked", false);
            $("#edittasgoodsReprtN").prop("checked", false);
            $("#seetasgoodsReprtN").prop("checked", false);


            $("#goodsbargiriReportN").prop("checked", false);
            $("#deletegoodsbargiriReportN").prop("checked", false);
            $("#editgoodsbargiriReportN").prop("checked", false);
            $("#seegoodsbargiriReportN").prop("checked", false);
            //عملیات
            $("#oppN").prop("checked", false);
            $("#oppTakhsisN").prop("checked", false);
            $("#oppManagerN").prop("checked", false);
            $("#deleteManagerOppN").prop("checked", false);
            $("#editManagerOppN").prop("checked", false);
            $("#seeManagerOppN").prop("checked", false);


            $("#oppHeadN").prop("checked", false);
            $("#deleteHeadOppN").prop("checked", false);
            $("#editHeadOppN").prop("checked", false);
            $("#seeHeadOppN").prop("checked", false);


            $("#oppBazaryabN").prop("checked", false);
            $("#deleteBazaryabOppN").prop("checked", false);
            $("#editBazaryabOppN").prop("checked", false);
            $("#seeBazaryabOppN").prop("checked", false);


            $("#oppDriverN").prop("checked", false);
            $("#oppDriverServiceN").prop("checked", false);
            $("#deleteoppDriverServiceN").prop("checked", false);
            $("#editoppDriverServiceN").prop("checked", false);
            $("#seeoppDriverServiceN").prop("checked", false);


            $("#oppBargiriN").prop("checked", false);
            $("#deleteoppBargiriN").prop("checked", false);
            $("#editoppBargiriN").prop("checked", false);
            $("#seeoppBargiriN").prop("checked", false);

            $("#oppNazarSanjiN").prop("checked", false);
            $("#todayoppNazarsanjiN").prop("checked", false);
            $("#deletetodayoppNazarsanjiN").prop("checked", false);
            $("#edittodayoppNazarsanjiN").prop("checked", false);
            $("#seetodayoppNazarsanjiN").prop("checked", false);


            $("#pastoppNazarsanjiN").prop("checked", false);
            $("#deletepastoppNazarsanjiN").prop("checked", false);
            $("#editpastoppNazarsanjiN").prop("checked", false);
            $("#seepastoppNazarsanjiN").prop("checked", false);


            $("#DoneoppNazarsanjiN").prop("checked", false);
            $("#deleteDoneoppNazarsanjiN").prop("checked", false);
            $("#editDoneoppNazarsanjiN").prop("checked", false);
            $("#seeDoneoppNazarsanjiN").prop("checked", false);


            $("#OppupDownBonusN").prop("checked", false);
            $("#AddOppupDownBonusN").prop("checked", false);
            $("#deleteAddOppupDownBonusN").prop("checked", false);
            $("#editAddOppupDownBonusN").prop("checked", false);
            $("#seeAddOppupDownBonusN").prop("checked", false);


            $("#SubOppupDownBonusN").prop("checked", false);
            $("#deleteSubOppupDownBonusN").prop("checked", false);
            $("#editSubOppupDownBonusN").prop("checked", false);
            $("#seeSubOppupDownBonusN").prop("checked", false);


            $("#oppRDN").prop("checked", false);
            $("#AddedoppRDN").prop("checked", false);
            $("#deleteAddedoppRDN").prop("checked", false);
            $("#editAddedoppRDN").prop("checked", false);
            $("#seeAddedoppRDN").prop("checked", false);


            $("#NotAddedoppRDN").prop("checked", false);
            $("#deleteNotAddedoppRDN").prop("checked", false);
            $("#editNotAddedoppRDN").prop("checked", false);
            $("#seeNotAddedoppRDN").prop("checked", false);


            $("#oppCalendarN").prop("checked", false);
            $("#oppjustCalendarN").prop("checked", false);
            $("#deleteoppjustCalendarN").prop("checked", false);
            $("#editoppjustCalendarN").prop("checked", false);
            $("#seeoppjustCalendarN").prop("checked", false);


            $("#oppCustCalendarN").prop("checked", false);
            $("#deleteoppCustCalendarN").prop("checked", false);
            $("#editoppCustCalendarN").prop("checked", false);
            $("#seeoppCustCalendarN").prop("checked", false);


            $("#alarmoppN").prop("checked", false);
            $("#allalarmoppN").prop("checked", false);
            $("#deleteallalarmoppN").prop("checked", false);
            $("#editallalarmoppN").prop("checked", false);
            $("#seeallalarmoppN").prop("checked", false);


            $("#donealarmoppN").prop("checked", false);
            $("#deletedonealarmoppN").prop("checked", false);
            $("#editdonealarmoppN").prop("checked", false);
            $("#seedonealarmoppN").prop("checked", false);


            $("#NoalarmoppN").prop("checked", false);
            $("#deleteNoalarmoppN").prop("checked", false);
            $("#editNoalarmoppN").prop("checked", false);
            $("#seeNoalarmoppN").prop("checked", false);


            $("#massageOppN").prop("checked", false);
            $("#deletemassageOppN").prop("checked", false);
            $("#editmassageOppN").prop("checked", false);
            $("#seemassageOppN").prop("checked", false);


            $("#justBargiriOppN").prop("checked", false);
            $("#deletejustBargiriOppN").prop("checked", false);
            $("#editjustBargiriOppN").prop("checked", false);
            $("#seejustBargiriOppN").prop("checked", false);
            //تعریف عناصر
            $("#declareElementN").prop("checked", false);
            $("#editdeclareElementN").prop("checked", false);
            $("#deletedeclareElementN").prop("checked", false);
            $("#seedeclareElementN").prop("checked", false);
            //اطلاعات پایه
            $("#baseInfoN").prop("checked", false);
            $("#rdSentN").prop("checked", false);
            $("#infoRdN").prop("checked", false);
            $("#deleteSentRdN").prop("checked", false);
            $("#editSentRdN").prop("checked", false);
            $("#seeSentRdN").prop("checked", false);

            $("#rdNotSentN").prop("checked", false);
            $("#deleteRdNotSentN").prop("checked", false);
            $("#editRdNotSentN").prop("checked", false);
            $("#seeRdNotSentN").prop("checked", false);

            $("#deleteProfileN").prop("checked", false);
            $("#editProfileN").prop("checked", false);
            $("#seeProfileN").prop("checked", false);
            $("#baseInfoProfileN").prop("checked", false);

            $("#addSaleLineN").prop("checked", false);
            $("#deleteSaleLineN").prop("checked", false);
            $("#editSaleLineN").prop("checked", false);
            $("#seeSaleLineN").prop("checked", false);

            $("#baseInfoSettingN").prop("checked", false);
            $("#InfoSettingAccessN").prop("checked", false);
            $("#deleteSettingAccessN").prop("checked", false);
            $("#editSettingAccessN").prop("checked", false);
            $("#seeSettingAccessN").prop("checked", false);

            $("#InfoSettingTargetN").prop("checked", false);
            $("#deleteSettingTargetN").prop("checked", false);
            $("#editSettingTargetN").prop("checked", false);
            $("#seeSettingTargetN").prop("checked", false);
        }
    }
}


$("#rdSentN").on("change", function () {
    if ($("#rdSentN").is(":checked")) {
        $("#baseInfoN").prop("checked", true);
        $("#infoRdN").prop("checked", true);
        $("#seeSentRdN").prop("checked", true);
    } else {
        if (!$(".rdN").is(":checked")) {
            $("#infoRdN").prop("checked", false);
            $("#infoRdN").trigger("change");
        }
        $("#deleteSentRdN").prop("checked", false);
        $("#editSentRdN").prop("checked", false);
        $("#seeSentRdN").prop("checked", false);
    }
});

$("#rdNotSentN").on("change", function () {
    if ($("#rdNotSentN").is(":checked")) {
        $("#baseInfoN").prop("checked", true);
        $("#infoRdN").prop("checked", true);
        $("#rdNotSentN").prop("checked", true);
        $("#seeRdNotSentN").prop("checked", true);
    } else {
        if (!$(".rdN").is(":checked")) {
            $("#infoRdN").prop("checked", false);
            $("#infoRdN").trigger("change");
        }
        $("#rdNotSentN").prop("checked", false);
        $("#deleteRdNotSentN").prop("checked", false);
        $("#editRdNotSentN").prop("checked", false);
        $("#seeRdNotSentN").prop("checked", false);
    }
})


$("#addSaleLineN").on("change", function () {
    if ($("#addSaleLineN").is(":checked")) {
        $("#baseInfoN").prop("checked", true);
        $("#seeSaleLineN").prop("checked", true);
    } else {
        if (!$(".baseInfoN").is(":checked")) {
            $("#baseInfoN").prop("checked", false);
        }
        $("#deleteSaleLineN").prop("checked", false);
        $("#editSaleLineN").prop("checked", false);
        $("#seeSaleLineN").prop("checked", false);
    }
})


$("#baseInfoSettingN").on("change", function () {
    if ($("#baseInfoSettingN").is(":checked")) {
        $("#baseInfoN").prop("checked", true);
        $("#InfoSettingAccessN").prop("checked", true);
        $("#seeSettingAccessN").prop("checked", true);

        $("#InfoSettingTargetN").prop("checked", true);
        $("#seeSettingTargetN").prop("checked", true);

    } else {
        if (!$(".baseInfoN").is(":checked")) {
            $("#baseInfoN").prop("checked", false);
        }
        $("#InfoSettingAccessN").prop("checked", false);
        $("#deleteSettingAccessN").prop("checked", false);
        $("#editSettingAccessN").prop("checked", false);
        $("#seeSettingAccessN").prop("checked", false);

        $("#InfoSettingTargetN").prop("checked", false);
        $("#deleteSettingTargetN").prop("checked", false);
        $("#editSettingTargetN").prop("checked", false);
        $("#seeSettingTargetN").prop("checked", false);

    }
})

$("#baseInfoProfileN").on("change", function () {
    if ($("#baseInfoProfileN").is(":checked")) {
        $("#baseInfoN").prop("checked", true);
        $("#seeProfileN").prop("checked", true);
    } else {
        if (!$(".baseInfoN").is(":checked")) {
            $("#baseInfoN").prop("checked", false);
        }
        $("#deleteProfileN").prop("checked", false);
        $("#editProfileN").prop("checked", false);
        $("#seeProfileN").prop("checked", false);
    }
})

$("#infoRdN").on("change", function () {
    if ($("#infoRdN").is(":checked")) {

        $("#baseInfoN").prop("checked", true);
        $("#rdSentN").prop("checked", true);
        $("#seeSentRdN").prop("checked", true);

        $("#rdNotSentN").prop("checked", true);
        $("#seeRdNotSentN").prop("checked", true);

    } else {
        if (!$(".baseInfoN").is(":checked")) {

            $("#baseInfoN").prop("checked", false);
        }
        $("#rdSentN").prop("checked", false);
        $("#deleteSentRdN").prop("checked", false);
        $("#editSentRdN").prop("checked", false);
        $("#seeSentRdN").prop("checked", false);

        $("#rdNotSentN").prop("checked", false);
        $("#deleteRdNotSentN").prop("checked", false);
        $("#editRdNotSentN").prop("checked", false);
        $("#seeRdNotSentN").prop("checked", false);
    }
});

$("#seeProfileN").on("change", function () {
    if (!$("#seeProfileN").is(":checked")) {
        $(".ProfileN").prop("checked", false);
        $("#baseInfoProfileN").prop("checked", false);
        $("#baseInfoProfileN").trigger("change");
    } else {
        $("#baseInfoProfileN").prop("checked", true);
        $("#baseInfoProfileN").trigger("change");
    }
})

$("#editProfileN").on("change", function () {
    if (!$("#editProfileN").is(":checked")) {
        $("#deleteProfileN").prop("checked", false);
    } else {
        $("#seeProfileN").prop("checked", false);
        $("#baseInfoProfileN").prop("checked", true);
        $("#baseInfoProfileN").trigger("change");
    }
})

$("#deleteProfileN").on("change", function () {
    if (!$("#deleteProfileN").is(":checked")) {
    } else {
        $(".ProfileN").prop("checked", true);
        $("#baseInfoProfileN").prop("checked", true);
        $("#baseInfoProfileN").trigger("change");
    }
})

//
$("#seeSentRdN").on("change", function () {
    if (!$("#seeSentRdN").is(":checked")) {
        $("#rdSentN").prop("checked", false);
        $("#rdSentN").trigger("change");
    } else {
        $("#rdSentN").prop("checked", true);
        $("#rdSentN").trigger("change");
    }
})

$("#editSentRdN").on("change", function () {
    if (!$("#editSentRdN").is(":checked")) {
        $("#deleteSentRdN").prop("checked", false);
    } else {
        $("#rdSentN").prop("checked", true);
        $("#rdSentN").trigger("change");
    }
});

$("#deleteSentRdN").on("change", function () {
    if (!$("#deleteSentRdN").is(":checked")) {
    } else {
        $("#rdSentN").prop("checked", true);
        $("#editSentRdN").prop("checked", true);
        $("#rdSentN").trigger("change");
    }
})
//
$("#seeRdNotSentN").on("change", function () {
    if (!$("#seeRdNotSentN").is(":checked")) {
        $("#rdNotSentN").prop("checked", false);
        $("#rdNotSentN").trigger("change");
    } else {
        $("#rdNotSentN").prop("checked", true);
        $("#rdNotSentN").trigger("change");
    }
})


$("#editRdNotSentN").on("change", function () {
    if (!$("#editRdNotSentN").is(":checked")) {
        $("#deleteRdNotSentN").prop("checked", false);
    } else {
        $("#seeSentRdN").prop("checked", false);
        $("#rdNotSentN").prop("checked", true);
        $("#rdNotSentN").trigger("change");
    }
});


$("#deleteRdNotSentN").on("change", function () {
    if (!$("#deleteRdNotSentN").is(":checked")) {
    } else {
        $("#rdNotSentN").prop("checked", true);
        $("#editRdNotSentN").prop("checked", true);
        $("#rdNotSentN").trigger("change");
    }
})

//
$("#seeSaleLineN").on("change", function () {
    if (!$("#seeSaleLineN").is(":checked")) {
        $("#addSaleLineN").prop("checked", false);
        $("#addSaleLineN").trigger("change");
    } else {
        $("#addSaleLineN").prop("checked", true);
        $("#addSaleLineN").trigger("change");
    }
})


$("#editSaleLineN").on("change", function () {
    if (!$("#editSaleLineN").is(":checked")) {
        $("#deleteSaleLineN").prop("checked", false);
    } else {
        $("#addSaleLineN").prop("checked", true);
        $("#addSaleLineN").trigger("change");
    }
});


$("#deleteSaleLineN").on("change", function () {
    if (!$("#deleteSaleLineN").is(":checked")) {
    } else {
        $("#addSaleLineN").prop("checked", true);
        $("#editSaleLineN").prop("checked", true);
        $("#addSaleLineN").trigger("change");
    }
})

//
$("#seeSettingAccessN").on("change", function () {
    if (!$("#seeSettingAccessN").is(":checked")) {
        $("#InfoSettingAccessN").prop("checked", false);
        $("#InfoSettingAccessN").trigger("change");
    } else {
        $("#InfoSettingAccessN").prop("checked", true);
        $("#InfoSettingAccessN").trigger("change");
    }
})


$("#editSettingAccessN").on("change", function () {
    if (!$("#editSettingAccessN").is(":checked")) {
        $("#deleteSettingAccessN").prop("checked", false);
    } else {
        $("#InfoSettingAccessN").prop("checked", true);
        $("#InfoSettingAccessN").trigger("change");
    }
});


$("#deleteSettingAccessN").on("change", function () {
    if (!$("#deleteSettingAccessN").is(":checked")) {
    } else {
        $("#InfoSettingAccessN").prop("checked", true);
        $("#editSettingAccessN").prop("checked", true);
        $("#InfoSettingAccessN").trigger("change");
    }
})
//
$("#seeSettingTargetN").on("change", function () {
    if (!$("#seeSettingTargetN").is(":checked")) {
        $("#InfoSettingTargetN").prop("checked", false);
        $("#InfoSettingTargetN").trigger("change");
    } else {
        $("#InfoSettingTargetN").prop("checked", true);
        $("#InfoSettingTargetN").trigger("change");
    }
})


$("#editSettingTargetN").on("change", function () {
    if (!$("#editSettingTargetN").is(":checked")) {
        $("#deleteSettingTargetN").prop("checked", false);
    } else {
        $("#InfoSettingTargetN").prop("checked", true);
        $("#InfoSettingTargetN").trigger("change");
    }
});


$("#deleteSettingTargetN").on("change", function () {
    if (!$("#deleteSettingTargetN").is(":checked")) {
    } else {
        $("#InfoSettingTargetN").prop("checked", true);
        $("#editSettingTargetN").prop("checked", true);
        $("#InfoSettingTargetN").trigger("change");
    }
})
//
$("#seedeclareElementN").on("change", function () {
    if (!$("#seedeclareElementN").is(":checked")) {
        $("#declareElementN").prop("checked", false);
        $("#declareElementN").trigger("change");
    } else {
        $("#declareElementN").prop("checked", true);
        $("#declareElementN").trigger("change");
    }
});

$("#declareElementN").on("change", function () {
    if (!$("#declareElementN").is(":checked")) {
        $("#editdeclareElementN").prop("checked", false);
        $("#deletedeclareElementN").prop("checked", false);
        $("#seedeclareElementN").prop("checked", false);
    } else {
        $("#seedeclareElementN").prop("checked", true);
    }
});



$("#editdeclareElementN").on("change", function () {
    if (!$("#editdeclareElementN").is(":checked")) {
        $("#deletedeclareElementN").prop("checked", false);
    } else {
        $("#declareElementN").prop("checked", true);
        $("#declareElementN").trigger("change");
    }
});


$("#deletedeclareElementN").on("change", function () {
    if (!$("#deletedeclareElementN").is(":checked")) {
    } else {
        $("#declareElementN").prop("checked", true);
        $("#editdeclareElementN").prop("checked", true);
        $("#declareElementN").trigger("change");
    }
});

$("#InfoSettingAccessN").on("change", function () {
    if ($("#InfoSettingAccessN").is(":checked")) {
        $("#baseInfoSettingN").prop("checked", true);
        $("#baseInfoN").prop("checked", true);
        $("#InfoSettingAccessN").prop("checked", true);
        $("#seeSettingAccessN").prop("checked", true);
    } else {
        if (!$(".InfoSettingN").is(":checked")) {
            $("#baseInfoSettingN").prop("checked", false);
            $("#baseInfoSettingN").trigger("change");
        }
        $("#deleteSettingAccessN").prop("checked", false);
        $("#editSettingAccessN").prop("checked", false);
        $("#seeSettingAccessN").prop("checked", false);
    }
});


$("#InfoSettingTargetN").on("change", function () {
    if ($("#InfoSettingTargetN").is(":checked")) {
        $("#baseInfoSettingN").prop("checked", true);
        $("#baseInfoN").prop("checked", true);
        $("#seeSettingTargetN").prop("checked", true);
    } else {
        if (!$(".InfoSetting").is(":checked")) {
            $("#baseInfoSettingN").prop("checked", false);
            $("#baseInfoSettingN").trigger("change");
        }
        $("#deleteSettingTargetN").prop("checked", false);
        $("#editSettingTargetN").prop("checked", false);
        $("#seeSettingTargetN").prop("checked", false);
    }
});



$("#baseInfoN").on("change", function () {
    if ($("#baseInfoN").is(":checked")) {
        $("#seeProfileN").prop("checked", true);
        $("#baseInfoProfileN").prop("checked", true);

        $("#rdSentN").prop("checked", true);
        $("#infoRdN").prop("checked", true);
        $("#seeSentRdN").prop("checked", true);

        $("#rdNotSentN").prop("checked", true);
        $("#baseInfoSettingN").prop("checked", true);
        $("#seeRdNotSentN").prop("checked", true);

        $("#addSaleLineN").prop("checked", true);
        $("#seeSaleLineN").prop("checked", true);

        $("declareElementN").prop("checked", true);
        $("#InfoSettingAccessN").prop("checked", true);
        $("#seeSettingAccessN").prop("checked", true);

        $("#InfoSettingTargetN").prop("checked", true);
        $("#seeSettingTargetN").prop("checked", true);
    } else {
        $("#rdSentN").prop("checked", false);
        $("#infoRdN").prop("checked", false);
        $("#deleteSentRdN").prop("checked", false);
        $("#editSentRdN").prop("checked", false);
        $("#seeSentRdN").prop("checked", false);

        $("#rdNotSentN").prop("checked", false);
        $("#deleteRdNotSentN").prop("checked", false);
        $("#editRdNotSentN").prop("checked", false);
        $("#seeRdNotSentN").prop("checked", false);

        $("#deleteProfileN").prop("checked", false);
        $("#editProfileN").prop("checked", false);
        $("#seeProfileN").prop("checked", false);
        $("#baseInfoProfileN").prop("checked", false);

        $("#addSaleLineN").prop("checked", false);
        $("#deleteSaleLineN").prop("checked", false);
        $("#editSaleLineN").prop("checked", false);
        $("#seeSaleLineN").prop("checked", false);

        $("#baseInfoSettingN").prop("checked", false);
        $("#InfoSettingAccessN").prop("checked", false);
        $("#deleteSettingAccessN").prop("checked", false);
        $("#editSettingAccessN").prop("checked", false);
        $("#seeSettingAccessN").prop("checked", false);

        $("#InfoSettingTargetN").prop("checked", false);
        $("#deleteSettingTargetN").prop("checked", false);
        $("#editSettingTargetN").prop("checked", false);
        $("#seeSettingTargetN").prop("checked", false);
    }
});

$("#salegoodsReportN").on("change", function () {
    if ($("#salegoodsReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#goodsReportN").prop("checked", true);
        $("#seesalegoodsReportN").prop("checked", true);
    } else {
        if (!$(".goodsReportN").is(":checked")) {
            // $("#reportN").prop("checked",false);
            $("#goodsReportN").prop("checked", false);
            $("#goodsReportN").trigger("change");
        }
        $("#seesalegoodsReportN").prop("checked", false);
        $("#editsalegoodsReportN").prop("checked", false);
        $("#deletesalegoodsReportN").prop("checked", false);
    }
});


$("#returnedgoodsReportN").on("change", function () {
    if ($("#returnedgoodsReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#goodsReportN").prop("checked", true);
        $("#seereturnedgoodsReportN").prop("checked", true);
    } else {
        if (!$(".goodsReportN").is(":checked")) {
            // $("#reportN").prop("checked",false);
            $("#goodsReportN").prop("checked", false);
            $("#goodsReportN").trigger("change");
        }
        $("#seereturnedgoodsReportN").prop("checked", false);
        $("#editreturnedgoodsReportN").prop("checked", false);
        $("#deletereturnedgoodsReportN").prop("checked", false);
    }
});
$("#NoExistgoodsReportN").on("change", function () {
    if ($("#NoExistgoodsReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#goodsReportN").prop("checked", true);
        $("#seeNoExistgoodsReportN").prop("checked", true);
    } else {
        if (!$(".goodsReportN").is(":checked")) {
            // $("#reportN").prop("checked",false);
            $("#goodsReportN").prop("checked", false);
            $("#goodsReportN").trigger("change");
        }
        $("#seeNoExistgoodsReportN").prop("checked", false);
        $("#editNoExistgoodsReportN").prop("checked", false);
        $("#deleteNoExistgoodsReportN").prop("checked", false);
    }
});
$("#nosalegoodsReportN").on("change", function () {
    if ($("#nosalegoodsReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#goodsReportN").prop("checked", true);
        $("#seenosalegoodsReportN").prop("checked", true);
    } else {
        if (!$(".goodsReportN").is(":checked")) {
            // $("#reportN").prop("checked",false);
            $("#goodsReportN").prop("checked", false);
            $("#goodsReportN").trigger("change");
        }
        $("#seenosalegoodsReportN").prop("checked", false);
        $("#editnosalegoodsReportN").prop("checked", false);
        $("#deletenosalegoodsReportN").prop("checked", false);
    }
});
$("#oppTakhsisN").on("change", function () {
    if ($("#oppTakhsisN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppManagerN").prop("checked", true);
        $("#oppHeadN").prop("checked", true);
        $("#oppBazaryabN").prop("checked", true);
        $("#seeManagerOppN").prop("checked", true);
        $("#seeBazaryabOppN").prop("checked", true);
        $("#seeHeadOppN").prop("checked", true);
        $("#seeBazaryabOppN").prop("checked", true);
    } else {
        if (!$(".oppPartN").is(":checked")) {
            $("#oppN").prop("checked", false);
        }

        $("#oppManagerN").prop("checked", false);
        $("#oppHeadN").prop("checked", false);
        $("#oppBazaryabN").prop("checked", false);
        $("#seeManagerOppN").prop("checked", false);
        $("#seeBazaryabOppN").prop("checked", false);
        $("#seeHeadOppN").prop("checked", false);

        $("#editManagerOppN").prop("checked", false);
        $("#editBazaryabOppN").prop("checked", false);
        $("#editHeadOppN").prop("checked", false);

        $("#deleteManagerOppN").prop("checked", false);
        $("#deleteBazaryabOppN").prop("checked", false);
        $("#deleteHeadOppN").prop("checked", false);
    }
});

$("#oppDriverN").on("change", function () {
    if ($("#oppDriverN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppDriverServiceN").prop("checked", true);
        $("#oppBargiriN").prop("checked", true);

        $("#seeoppDriverServiceN").prop("checked", true);
        $("#seeoppBargiriN").prop("checked", true);
    } else {
        if (!$(".oppPartN").is(":checked")) {
            $("#oppN").prop("checked", false);
        }

        $("#oppDriverServiceN").prop("checked", false);
        $("#oppBargiriN").prop("checked", false);

        $("#seeoppDriverServiceN").prop("checked", false);
        $("#seeoppBargiriN").prop("checked", false);

        $("#editoppDriverServiceN").prop("checked", false);
        $("#editoppBargiriN").prop("checked", false);

        $("#deleteoppDriverServiceN").prop("checked", false);
        $("#deleteoppBargiriN").prop("checked", false);
    }
});


$("#oppNazarSanjiN").on("change", function () {
    if ($("#oppNazarSanjiN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#todayoppNazarsanjiN").prop("checked", true);
        $("#pastoppNazarsanjiN").prop("checked", true);
        $("#DoneoppNazarsanjiN").prop("checked", true);

        $("#seetodayoppNazarsanjiN").prop("checked", true);
        $("#seepastoppNazarsanjiN").prop("checked", true);
        $("#seeDoneoppNazarsanjiN").prop("checked", true);
    } else {
        if (!$(".oppPartN").is(":checked")) {
            $("#oppN").prop("checked", false);
        }

        $("#todayoppNazarsanjiN").prop("checked", false);
        $("#pastoppNazarsanjiN").prop("checked", false);
        $("#DoneoppNazarsanjiN").prop("checked", false);

        $("#seetodayoppNazarsanjiN").prop("checked", false);
        $("#seepastoppNazarsanjiN").prop("checked", false);
        $("#seeDoneoppNazarsanjiN").prop("checked", false);

        $("#edittodayoppNazarsanjiN").prop("checked", false);
        $("#editpastoppNazarsanjiN").prop("checked", false);
        $("#editDoneoppNazarsanjiN").prop("checked", false);

        $("#deletetodayoppNazarsanjiN").prop("checked", false);
        $("#deletepastoppNazarsanjiN").prop("checked", false);
        $("#deleteDoneoppNazarsanjiN").prop("checked", false);
    }
});


$("#OppupDownBonusN").on("change", function () {
    if ($("#OppupDownBonusN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#AddOppupDownBonusN").prop("checked", true);
        $("#SubOppupDownBonusN").prop("checked", true);


        $("#seeAddOppupDownBonusN").prop("checked", true);
        $("#seeSubOppupDownBonusN").prop("checked", true);

    } else {
        if (!$(".oppPartN").is(":checked")) {
            $("#oppN").prop("checked", false);
        }

        $("#AddOppupDownBonusN").prop("checked", false);
        $("#SubOppupDownBonusN").prop("checked", false);

        $("#seeAddOppupDownBonusN").prop("checked", false);
        $("#seeSubOppupDownBonusN").prop("checked", false);

        $("#editAddOppupDownBonusN").prop("checked", false);
        $("#editSubOppupDownBonusN").prop("checked", false);

        $("#deleteAddOppupDownBonusN").prop("checked", false);
        $("#deleteSubOppupDownBonusN").prop("checked", false);
    }
});

$("#oppRDN").on("change", function () {
    if ($("#oppRDN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#AddedoppRDN").prop("checked", true);
        $("#NotAddedoppRDN").prop("checked", true);

        $("#seeAddedoppRDN").prop("checked", true);
        $("#seeNotAddedoppRDN").prop("checked", true);
    } else {
        if (!$(".oppPartN").is(":checked")) {
            $("#oppN").prop("checked", false);
        }

        $("#AddedoppRDN").prop("checked", false);
        $("#NotAddedoppRDN").prop("checked", false);

        $("#seeAddedoppRDN").prop("checked", false);
        $("#seeNotAddedoppRDN").prop("checked", false);

        $("#editAddedoppRDN").prop("checked", false);
        $("#editNotAddedoppRDN").prop("checked", false);

        $("#deleteAddedoppRDN").prop("checked", false);
        $("#deleteNotAddedoppRDN").prop("checked", false);
    }
});

$("#oppCalendarN").on("change", function () {
    if ($("#oppCalendarN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppjustCalendarN").prop("checked", true);
        $("#oppCustCalendarN").prop("checked", true);

        $("#seeoppjustCalendarN").prop("checked", true);
        $("#seeoppCustCalendarN").prop("checked", true);

    } else {
        if (!$(".oppPartN").is(":checked")) {
            $("#oppN").prop("checked", false);
        }

        $("#oppjustCalendarN").prop("checked", false);
        $("#oppCustCalendarN").prop("checked", false);

        $("#seeoppjustCalendarN").prop("checked", false);
        $("#seeoppCustCalendarN").prop("checked", false);

        $("#editoppjustCalendarN").prop("checked", false);
        $("#editoppCustCalendarN").prop("checked", false);

        $("#deleteoppjustCalendarN").prop("checked", false);
        $("#deleteoppCustCalendarN").prop("checked", false);

    }
});


$("#alarmoppN").on("change", function () {
    if ($("#alarmoppN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#allalarmoppN").prop("checked", true);
        $("#donealarmoppN").prop("checked", true);
        $("#NoalarmoppN").prop("checked", true);

        $("#seeallalarmoppN").prop("checked", true);
        $("#seedonealarmoppN").prop("checked", true);
        $("#seeNoalarmoppN").prop("checked", true);
    } else {
        if (!$(".oppPartN").is(":checked")) {
            $("#oppN").prop("checked", false);
        }

        $("#allalarmoppN").prop("checked", false);
        $("#donealarmoppN").prop("checked", false);
        $("#NoalarmoppN").prop("checked", false);

        $("#seeallalarmoppN").prop("checked", false);
        $("#seedonealarmoppN").prop("checked", false);
        $("#seeNoalarmoppN").prop("checked", false);

        $("#editallalarmoppN").prop("checked", false);
        $("#editdonealarmoppN").prop("checked", false);
        $("#editNoalarmoppN").prop("checked", false);


        $("#deleteallalarmoppN").prop("checked", false);
        $("#deletedonealarmoppN").prop("checked", false);
        $("#deleteNoalarmoppN").prop("checked", false);
    }
});

$("#massageOppN").on("change", function () {
    if ($("#massageOppN").is(":checked")) {
        $("#seemassageOppN").prop("checked", true);
        $("#oppN").prop("checked", true);
    } else {
        if (!$(".oppPartN").is(":checked")) {
            $("#oppN").prop("checked", false);
        }
        $("#seemassageOppN").prop("checked", false);
        $("#editmassageOppN").prop("checked", false);
        $("#deletemassageOppN").prop("checked", false);
    }
});

$("#justBargiriOppN").on("change", function () {
    if ($("#justBargiriOppN").is(":checked")) {
        $("#seejustBargiriOppN").prop("checked", true);
        $("#oppN").prop("checked", true);
    } else {
        if (!$(".oppPartN").is(":checked")) {
            $("#oppN").prop("checked", false);
        }
        $("#seejustBargiriOppN").prop("checked", false);
        $("#editjustBargiriOppN").prop("checked", false);
        $("#deletejustBargiriOppN").prop("checked", false);
    }
});


$("#oppManagerN").on("change", function () {
    if ($("#oppManagerN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppTakhsisN").prop("checked", true);
        $("#seeManagerOppN").prop("checked", true);
    } else {
        if (!$(".oppTakhsisN").is(":checked")) {
            // $("#oppN").prop("checked",false);
            $("#oppTakhsisN").prop("checked", false);
            $("#oppTakhsisN").trigger("change");
        }
        $("#seeManagerOppN").prop("checked", false);
        $("#editManagerOppN").prop("checked", false);
        $("#deleteManagerOppN").prop("checked", false);
    }
});

$("#oppHeadN").on("change", function () {
    if ($("#oppHeadN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppTakhsisN").prop("checked", true);
        $("#seeHeadOppN").prop("checked", true);
    } else {
        if (!$(".oppTakhsisN").is(":checked")) {
            // $("#oppN").prop("checked",false);
            $("#oppTakhsisN").prop("checked", false);
            $("#oppTakhsisN").trigger("change");
        }
        $("#seeHeadOppN").prop("checked", false);
        $("#editHeadOppN").prop("checked", false);
        $("#deleteHeadOppN").prop("checked", false);
    }
});

$("#oppBazaryabN").on("change", function () {
    if ($("#oppBazaryabN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppTakhsisN").prop("checked", true);
        $("#seeBazaryabOppN").prop("checked", true);
    } else {
        if (!$(".oppTakhsisN").is(":checked")) {
            // $("#oppN").prop("checked",false);
            $("#oppTakhsisN").prop("checked", false);
            $("#oppTakhsisN").trigger("change");
        }
        $("#seeBazaryabOppN").prop("checked", false);
        $("#editBazaryabOppN").prop("checked", false);
        $("#deleteBazaryabOppN").prop("checked", false);
    }
});

$("#oppDriverServiceN").on("change", function () {
    if ($("#oppDriverServiceN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppDriverN").prop("checked", true);
        $("#seeoppDriverServiceN").prop("checked", true);
    } else {
        if (!$(".oppDriverN").is(":checked")) {
            //$("#oppN").prop("checked",false);
            $("#oppDriverN").prop("checked", false);
            $("#oppDriverN").trigger("change");
        }
        $("#seeoppDriverServiceN").prop("checked", false);
        $("#editoppDriverServiceN").prop("checked", false);
        $("#deleteoppDriverServiceN").prop("checked", false);
    }
});

$("#oppBargiriN").on("change", function () {
    if ($("#oppBargiriN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppDriverN").prop("checked", true);
        $("#seeoppBargiriN").prop("checked", true);
    } else {
        if (!$(".oppDriverN").is(":checked")) {
            //$("#oppN").prop("checked",false);
            $("#oppDriverN").prop("checked", false);
            $("#oppDriverN").trigger("change");
        }
        $("#seeoppBargiriN").prop("checked", false);
        $("#editoppBargiriN").prop("checked", false);
        $("#deleteoppBargiriN").prop("checked", false);
    }
});

$("#todayoppNazarsanjiN").on("change", function () {
    if ($("#todayoppNazarsanjiN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppNazarSanjiN").prop("checked", true);
        $("#seetodayoppNazarsanjiN").prop("checked", true);
    } else {
        if (!$(".oppNazarSanjiN").is(":checked")) {
            //$("#oppN").prop("checked",false);
            $("#oppNazarSanjiN").prop("checked", false);
            $("#oppNazarSanjiN").trigger("change");
        }
        $("#seetodayoppNazarsanjiN").prop("checked", false);
        $("#edittodayoppNazarsanjiN").prop("checked", false);
        $("#deletetodayoppNazarsanjiN").prop("checked", false);
    }
});

$("#pastoppNazarsanjiN").on("change", function () {
    if ($("#pastoppNazarsanjiN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppNazarSanjiN").prop("checked", true);
        $("#seepastoppNazarsanjiN").prop("checked", true);
    } else {
        if (!$(".oppNazarSanjiN").is(":checked")) {
            //$("#oppN").prop("checked",false);
            $("#oppNazarSanjiN").prop("checked", false);
            $("#oppNazarSanjiN").trigger("change");
        }
        $("#seepastoppNazarsanjiN").prop("checked", false);
        $("#editpastoppNazarsanjiN").prop("checked", false);
        $("#deletepastoppNazarsanjiN").prop("checked", false);
    }
});

$("#DoneoppNazarsanjiN").on("change", function () {
    if ($("#DoneoppNazarsanjiN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppNazarSanjiN").prop("checked", true);
        $("#seeDoneoppNazarsanjiN").prop("checked", true);
    } else {
        if (!$(".oppNazarSanjiN").is(":checked")) {
            // $("#oppN").prop("checked",false);
            $("#oppNazarSanjiN").prop("checked", false);
            $("#oppNazarSanjiN").trigger("change");
        }
        $("#seeDoneoppNazarsanjiN").prop("checked", false);
        $("#editDoneoppNazarsanjiN").prop("checked", false);
        $("#deleteDoneoppNazarsanjiN").prop("checked", false);
    }
});

$("#AddOppupDownBonusN").on("change", function () {
    if ($("#AddOppupDownBonusN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#OppupDownBonusN").prop("checked", true);
        $("#seeAddOppupDownBonusN").prop("checked", true);
    } else {
        if (!$(".OppupDownBonusN").is(":checked")) {
            //$("#oppN").prop("checked",false);
            $("#OppupDownBonusN").prop("checked", false);
            $("#OppupDownBonusN").trigger("change");
        }
        $("#seeAddOppupDownBonusN").prop("checked", false);
        $("#editAddOppupDownBonusN").prop("checked", false);
        $("#deleteAddOppupDownBonusN").prop("checked", false);
    }
});

$("#SubOppupDownBonusN").on("change", function () {
    if ($("#SubOppupDownBonusN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#OppupDownBonusN").prop("checked", true);
        $("#seeSubOppupDownBonusN").prop("checked", true);
    } else {
        if (!$(".OppupDownBonusN").is(":checked")) {
            // $("#oppN").prop("checked",false);
            $("#OppupDownBonusN").prop("checked", false);
            $("#OppupDownBonusN").trigger("change");
        }
        $("#seeSubOppupDownBonusN").prop("checked", false);
        $("#editSubOppupDownBonusN").prop("checked", false);
        $("#deleteSubOppupDownBonusN").prop("checked", false);
    }
});


$("#AddedoppRDN").on("change", function () {
    if ($("#AddedoppRDN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppRDN").prop("checked", true);
        $("#seeAddedoppRDN").prop("checked", true);
    } else {
        if (!$(".oppRDN").is(":checked")) {
            // $("#oppN").prop("checked",false);
            $("#oppRDN").prop("checked", false);
            $("#oppRDN").trigger("change")
        }
        $("#seeAddedoppRDN").prop("checked", false);
        $("#editAddedoppRDN").prop("checked", false);
        $("#deleteAddedoppRDN").prop("checked", false);
    }
});

$("#NotAddedoppRDN").on("change", function () {
    if ($("#NotAddedoppRDN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppRDN").prop("checked", true);
        $("#seeNotAddedoppRDN").prop("checked", true);
    } else {
        if (!$(".oppRDN").is(":checked")) {
            //$("#oppN").prop("checked",false);
            $("#oppRDN").prop("checked", false);
            $("#oppRDN").trigger("change")
        }
        $("#seeNotAddedoppRDN").prop("checked", false);
        $("#editNotAddedoppRDN").prop("checked", false);
        $("#deleteNotAddedoppRDN").prop("checked", false);
    }
});

$("#oppjustCalendarN").on("change", function () {
    if ($("#oppjustCalendarN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppCalendarN").prop("checked", true);
        $("#seeoppjustCalendarN").prop("checked", true);
    } else {
        if (!$(".oppCalendarN").is(":checked")) {
            // $("#oppN").prop("checked",false);
            $("#oppCalendarN").prop("checked", false);
            $("#oppCalendarN").trigger("change")
        }
        $("#seeoppjustCalendarN").prop("checked", false);
        $("#editoppjustCalendarN").prop("checked", false);
        $("#deleteoppjustCalendarN").prop("checked", false);
    }
});

$("#oppCustCalendarN").on("change", function () {
    if ($("#oppCustCalendarN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#oppCalendarN").prop("checked", true);
        $("#seeoppCustCalendarN").prop("checked", true);
    } else {
        if (!$(".oppCalendarN").is(":checked")) {
            //$("#oppN").prop("checked",false);
            $("#oppCalendarN").prop("checked", false);
            $("#oppCalendarN").trigger("change")
        }
        $("#seeoppCustCalendarN").prop("checked", false);
        $("#editoppCustCalendarN").prop("checked", false);
        $("#deleteoppCustCalendarN").prop("checked", false);
    }
});

$("#allalarmoppN").on("change", function () {
    if ($("#allalarmoppN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#alarmoppN").prop("checked", true);
        $("#seeallalarmoppN").prop("checked", true);
    } else {
        if (!$(".alarmoppN").is(":checked")) {
            //$("#oppN").prop("checked",false);
            $("#alarmoppN").prop("checked", false);
            $("#alarmoppN").trigger("change");
        }
        $("#seeallalarmoppN").prop("checked", false);
        $("#editallalarmoppN").prop("checked", false);
        $("#deleteallalarmoppN").prop("checked", false);
    }
});

$("#donealarmoppN").on("change", function () {
    if ($("#donealarmoppN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#alarmoppN").prop("checked", true);
        $("#seedonealarmoppN").prop("checked", true);
    } else {
        if (!$(".alarmoppN").is(":checked")) {
            $("#oppN").prop("checked", false);
            $("#alarmoppN").prop("checked", false);
        }
        $("#seedonealarmoppN").prop("checked", false);
        $("#editdonealarmoppN").prop("checked", false);
        $("#deletedonealarmoppN").prop("checked", false);
    }
});

$("#NoalarmoppN").on("change", function () {
    if ($("#NoalarmoppN").is(":checked")) {
        $("#oppN").prop("checked", true);
        $("#alarmoppN").prop("checked", true);
        $("#seeNoalarmoppN").prop("checked", true);
    } else {
        if (!$(".alarmoppN").is(":checked")) {
            // $("#oppN").prop("checked",false);
            $("#alarmoppN").prop("checked", false);
            $("#alarmoppN").trigger("change");
        }
        $("#seeNoalarmoppN").prop("checked", false);
        $("#editNoalarmoppN").prop("checked", false);
        $("#deleteNoalarmoppN").prop("checked", false);
    }
});

//
$("#seeManagerOppN").on("change", function () {
    if (!$("#seeManagerOppN").is(":checked")) {
        $("#oppManagerN").prop("checked", false);
        $("#oppManagerN").trigger("change");
        $("#editManagerOppN").prop("checked", false);
        $("#deleteManagerOppN").prop("checked", false);
    } else {
        $("#oppManagerN").prop("checked", true);
        $("#oppManagerN").trigger("change");

    }
})


$("#editManagerOppN").on("change", function () {
    if (!$("#editManagerOppN").is(":checked")) {
        $("#deleteManagerOppN").prop("checked", false);
    } else {
        $("#oppManagerN").prop("checked", true);

        $("#oppManagerN").trigger("change");
    }
});


$("#deleteManagerOppN").on("change", function () {
    if (!$("#deleteManagerOppN").is(":checked")) {
    } else {
        $("#oppManagerN").prop("checked", true);
        $("#editManagerOppN").prop("checked", true);
        $("#oppManagerN").trigger("change");
    }
});

//
$("#seeHeadOppN").on("change", function () {
    if (!$("#seeHeadOppN").is(":checked")) {
        $("#oppHeadN").prop("checked", false);
        $("#oppHeadN").trigger("change");
        $("#editHeadOppN").prop("checked", false);
        $("#deleteHeadOppN").prop("checked", false);
    } else {
        $("#oppHeadN").prop("checked", true);
        $("#oppHeadN").trigger("change");

    }
})


$("#editHeadOppN").on("change", function () {
    if (!$("#editHeadOppN").is(":checked")) {
        $("#deleteHeadOppN").prop("checked", false);
    } else {
        $("#oppHeadN").prop("checked", true);

        $("#oppHeadN").trigger("change");
    }
});


$("#deleteHeadOppN").on("change", function () {
    if (!$("#deleteHeadOppN").is(":checked")) {
    } else {
        $("#oppHeadN").prop("checked", true);
        $("#editHeadOppN").prop("checked", true);
        $("#oppHeadN").trigger("change");
    }
});
//
$("#seeBazaryabOppN").on("change", function () {
    if (!$("#seeBazaryabOppN").is(":checked")) {
        $("#oppBazaryabN").prop("checked", false);
        $("#oppBazaryabN").trigger("change");
        $("#deleteBazaryabOppN").prop("checked", false);
        $("#editBazaryabOppN").prop("checked", false);
    } else {
        $("#oppBazaryabN").prop("checked", true);
        $("#oppBazaryabN").trigger("change");

    }
})


$("#editBazaryabOppN").on("change", function () {
    if (!$("#editBazaryabOppN").is(":checked")) {
        $("#deleteBazaryabOppN").prop("checked", false);
    } else {
        $("#oppBazaryabN").prop("checked", true);

        $("#oppBazaryabN").trigger("change");
    }
});


$("#deleteBazaryabOppN").on("change", function () {
    if (!$("#deleteBazaryabOppN").is(":checked")) {
    } else {
        $("#oppBazaryabN").prop("checked", true);
        $("#editBazaryabOppN").prop("checked", true);
        $("#oppBazaryabN").trigger("change");
    }
});

//
$("#seeoppDriverServiceN").on("change", function () {
    if (!$("#seeoppDriverServiceN").is(":checked")) {
        $("#oppDriverServiceN").prop("checked", false);
        $("#oppDriverServiceN").trigger("change");
        $("#deleteoppDriverServiceN").prop("checked", false);
        $("#editoppDriverServiceN").prop("checked", false);
    } else {
        $("#oppDriverServiceN").prop("checked", true);
        $("#oppDriverServiceN").trigger("change");

    }
})


$("#editoppDriverServiceN").on("change", function () {
    if (!$("#editoppDriverServiceN").is(":checked")) {
        $("#deleteoppDriverServiceN").prop("checked", false);
    } else {
        $("#oppDriverServiceN").prop("checked", true);

        $("#oppDriverServiceN").trigger("change");
    }
});


$("#deleteoppDriverServiceN").on("change", function () {
    if (!$("#deleteoppDriverServiceN").is(":checked")) {
    } else {
        $("#oppDriverServiceN").prop("checked", true);
        $("#editoppDriverServiceN").prop("checked", true);
        $("#oppDriverServiceN").trigger("change");
    }
});

//
$("#seeoppBargiriN").on("change", function () {
    if (!$("#seeoppBargiriN").is(":checked")) {
        $("#oppBargiriN").prop("checked", false);
        $("#oppBargiriN").trigger("change");
        $("#deleteoppBargiriN").prop("checked", false);
        $("#editoppBargiriN").prop("checked", false);
    } else {
        $("#oppBargiriN").prop("checked", true);
        $("#oppBargiriN").trigger("change");

    }
})


$("#editoppBargiriN").on("change", function () {
    if (!$("#editoppBargiriN").is(":checked")) {
        $("#deleteoppBargiriN").prop("checked", false);
    } else {
        $("#oppBargiriN").prop("checked", true);

        $("#oppBargiriN").trigger("change");
    }
});


$("#deleteoppBargiriN").on("change", function () {
    if (!$("#deleteoppBargiriN").is(":checked")) {
    } else {
        $("#oppBargiriN").prop("checked", true);
        $("#editoppBargiriN").prop("checked", true);
        $("#oppBargiriN").trigger("change");
    }
});

//
$("#seetodayoppNazarsanjiN").on("change", function () {
    if (!$("#seetodayoppNazarsanjiN").is(":checked")) {
        $("#todayoppNazarsanjiN").prop("checked", false);
        $("#todayoppNazarsanjiN").trigger("change");
        $("#deletetodayoppNazarsanjiN").prop("checked", false);
        $("#edittodayoppNazarsanjiN").prop("checked", false);
    } else {
        $("#todayoppNazarsanjiN").prop("checked", true);
        $("#todayoppNazarsanjiN").trigger("change");

    }
})


$("#edittodayoppNazarsanjiN").on("change", function () {
    if (!$("#edittodayoppNazarsanjiN").is(":checked")) {
        $("#deletetodayoppNazarsanjiN").prop("checked", false);
    } else {
        $("#todayoppNazarsanjiN").prop("checked", true);

        $("#todayoppNazarsanjiN").trigger("change");
    }
});


$("#deletetodayoppNazarsanjiN").on("change", function () {
    if (!$("#deletetodayoppNazarsanjiN").is(":checked")) {
    } else {
        $("#todayoppNazarsanjiN").prop("checked", true);
        $("#edittodayoppNazarsanjiN").prop("checked", true);
        $("#todayoppNazarsanjiN").trigger("change");
    }
});
//
$("#seepastoppNazarsanjiN").on("change", function () {
    if (!$("#seepastoppNazarsanjiN").is(":checked")) {
        $("#pastoppNazarsanjiN").prop("checked", false);
        $("#pastoppNazarsanjiN").trigger("change");
        $("#deletepastoppNazarsanjiN").prop("checked", false);
        $("#editpastoppNazarsanjiN").prop("checked", false);
    } else {
        $("#pastoppNazarsanjiN").prop("checked", true);
        $("#pastoppNazarsanjiN").trigger("change");

    }
})


$("#editpastoppNazarsanjiN").on("change", function () {
    if (!$("#editpastoppNazarsanjiN").is(":checked")) {
        $("#deletepastoppNazarsanjiN").prop("checked", false);
    } else {
        $("#pastoppNazarsanjiN").prop("checked", true);

        $("#pastoppNazarsanjiN").trigger("change");
    }
});


$("#deletepastoppNazarsanjiN").on("change", function () {
    if (!$("#deletepastoppNazarsanjiN").is(":checked")) {
        $("#pastoppNazarsanjiN").trigger("change");
    } else {
        $("#pastoppNazarsanjiN").prop("checked", true);
        $("#pastoppNazarsanjiN").trigger("change");
        $("#editpastoppNazarsanjiN").prop("checked", true);

    }
});
//
$("#seeDoneoppNazarsanjiN").on("change", function () {
    if (!$("#seeDoneoppNazarsanjiN").is(":checked")) {
        $("#DoneoppNazarsanjiN").prop("checked", false);
        $("#DoneoppNazarsanjiN").trigger("change");
        $("#deleteDoneoppNazarsanjiN").prop("checked", false);
        $("#editDoneoppNazarsanjiN").prop("checked", false);
    } else {
        $("#DoneoppNazarsanjiN").prop("checked", true);
        $("#DoneoppNazarsanjiN").trigger("change");
    }
})


$("#editDoneoppNazarsanjiN").on("change", function () {
    if (!$("#editDoneoppNazarsanjiN").is(":checked")) {
        $("#deleteDoneoppNazarsanjiN").prop("checked", false);
    } else {
        $("#DoneoppNazarsanjiN").prop("checked", true);

        $("#DoneoppNazarsanjiN").trigger("change");
    }
});


$("#deleteDoneoppNazarsanjiN").on("change", function () {
    if (!$("#deleteDoneoppNazarsanjiN").is(":checked")) {
    } else {
        $("#DoneoppNazarsanjiN").prop("checked", true);
        $("#editDoneoppNazarsanjiN").prop("checked", true);
        $("#DoneoppNazarsanjiN").trigger("change");
    }
});

//
$("#seeAddOppupDownBonusN").on("change", function () {
    if (!$("#seeAddOppupDownBonusN").is(":checked")) {
        $("#AddOppupDownBonusN").prop("checked", false);
        $("#AddOppupDownBonusN").trigger("change");
        $("#deleteAddOppupDownBonusN").prop("checked", false);
        $("#editAddOppupDownBonusN").prop("checked", false);
    } else {
        $("#AddOppupDownBonusN").prop("checked", true);
        $("#AddOppupDownBonusN").trigger("change");
    }
})


$("#editAddOppupDownBonusN").on("change", function () {
    if (!$("#editAddOppupDownBonusN").is(":checked")) {
        $("#deleteAddOppupDownBonusN").prop("checked", false);
    } else {
        $("#AddOppupDownBonusN").prop("checked", true);

        $("#AddOppupDownBonusN").trigger("change");
    }
});


$("#deleteAddOppupDownBonusN").on("change", function () {
    if (!$("#deleteAddOppupDownBonusN").is(":checked")) {
    } else {
        $("#AddOppupDownBonusN").prop("checked", true);
        $("#editAddOppupDownBonusN").prop("checked", true);
        $("#AddOppupDownBonusN").trigger("change");
    }
});

//
$("#seeSubOppupDownBonusN").on("change", function () {
    if (!$("#seeSubOppupDownBonusN").is(":checked")) {
        $("#SubOppupDownBonusN").prop("checked", false);
        $("#SubOppupDownBonusN").trigger("change");
        $("#deleteSubOppupDownBonusN").prop("checked", false);
        $("#editSubOppupDownBonusN").prop("checked", false);
    } else {
        $("#SubOppupDownBonusN").prop("checked", true);
        $("#SubOppupDownBonusN").trigger("change");
    }
})


$("#editSubOppupDownBonusN").on("change", function () {
    if (!$("#editSubOppupDownBonusN").is(":checked")) {
        $("#deleteSubOppupDownBonusN").prop("checked", false);
    } else {
        $("#SubOppupDownBonusN").prop("checked", true);

        $("#SubOppupDownBonusN").trigger("change");
    }
});


$("#deleteSubOppupDownBonusN").on("change", function () {
    if (!$("#deleteSubOppupDownBonusN").is(":checked")) {
    } else {
        $("#SubOppupDownBonusN").prop("checked", true);
        $("#editSubOppupDownBonusN").prop("checked", true);
        $("#SubOppupDownBonusN").trigger("change");
    }
});

//
$("#seeAddedoppRDN").on("change", function () {
    if (!$("#seeAddedoppRDN").is(":checked")) {
        $("#AddedoppRDN").prop("checked", false);
        $("#AddedoppRDN").trigger("change");
        $("#deleteAddedoppRDN").prop("checked", false);
        $("#editAddedoppRDN").prop("checked", false);
    } else {
        $("#AddedoppRDN").prop("checked", true);
        $("#AddedoppRDN").trigger("change");
    }
})


$("#editAddedoppRDN").on("change", function () {
    if (!$("#editAddedoppRDN").is(":checked")) {
        $("#deleteAddedoppRDN").prop("checked", false);
    } else {
        $("#AddedoppRDN").prop("checked", true);

        $("#AddedoppRDN").trigger("change");
    }
});


$("#deleteAddedoppRDN").on("change", function () {
    if (!$("#deleteAddedoppRDN").is(":checked")) {
    } else {
        $("#AddedoppRDN").prop("checked", true);
        $("#editAddedoppRDN").prop("checked", true);
        $("#AddedoppRDN").trigger("change");
    }
});

//
$("#seeNotAddedoppRDN").on("change", function () {
    if (!$("#seeNotAddedoppRDN").is(":checked")) {
        $("#NotAddedoppRDN").prop("checked", false);
        $("#NotAddedoppRDN").trigger("change");
        $("#deleteNotAddedoppRDN").prop("checked", false);
        $("#editNotAddedoppRDN").prop("checked", false);
    } else {
        $("#NotAddedoppRDN").prop("checked", true);
        $("#NotAddedoppRDN").trigger("change");
    }
})


$("#editNotAddedoppRDN").on("change", function () {
    if (!$("#editNotAddedoppRDN").is(":checked")) {
        $("#deleteNotAddedoppRDN").prop("checked", false);
    } else {
        $("#NotAddedoppRDN").prop("checked", true);
        $("#NotAddedoppRDN").trigger("change");
    }
});


$("#deleteNotAddedoppRDN").on("change", function () {
    if (!$("#deleteNotAddedoppRDN").is(":checked")) {
    } else {
        $("#NotAddedoppRDN").prop("checked", true);
        $("#editNotAddedoppRDN").prop("checked", true);
        $("#NotAddedoppRDN").trigger("change");
    }
});

//
$("#seeoppjustCalendarN").on("change", function () {
    if (!$("#seeoppjustCalendarN").is(":checked")) {
        $("#oppjustCalendarN").prop("checked", false);
        $("#oppjustCalendarN").trigger("change");
        $("#deleteoppjustCalendarN").prop("checked", false);
        $("#editoppjustCalendarN").prop("checked", false);
    } else {
        $("#oppjustCalendarN").prop("checked", true);
        $("#oppjustCalendarN").trigger("change");
    }
})


$("#editoppjustCalendarN").on("change", function () {
    if (!$("#editoppjustCalendarN").is(":checked")) {
        $("#deleteoppjustCalendarN").prop("checked", false);
    } else {
        $("#oppjustCalendarN").prop("checked", true);
        $("#oppjustCalendarN").trigger("change");
    }
});


$("#deleteoppjustCalendarN").on("change", function () {
    if (!$("#deleteoppjustCalendarN").is(":checked")) {
    } else {
        $("#oppjustCalendarN").prop("checked", true);
        $("#editoppjustCalendarN").prop("checked", true);
        $("#oppjustCalendarN").trigger("change");
    }
});


//
$("#seeoppCustCalendarN").on("change", function () {
    if (!$("#seeoppCustCalendarN").is(":checked")) {
        $("#oppCustCalendarN").prop("checked", false);
        $("#oppCustCalendarN").trigger("change");
        $("#editoppCustCalendarN").prop("checked", false);
        $("#deleteoppCustCalendarN").prop("checked", false);
    } else {
        $("#oppCustCalendarN").prop("checked", true);
        $("#oppCustCalendarN").trigger("change");
    }
})

$("#editoppCustCalendarN").on("change", function () {
    if (!$("#editoppCustCalendarN").is(":checked")) {
        $("#deleteoppCustCalendarN").prop("checked", false);
    } else {
        $("#oppCustCalendarN").prop("checked", true);
        $("#oppCustCalendarN").trigger("change");
    }
});

$("#deleteoppCustCalendarN").on("change", function () {
    if (!$("#deleteoppCustCalendarN").is(":checked")) {
    } else {
        $("#oppCustCalendarN").prop("checked", true);
        $("#editoppCustCalendarN").prop("checked", true);
        $("#oppCustCalendarN").trigger("change");
    }
});


//
$("#seeallalarmoppN").on("change", function () {
    if (!$("#seeallalarmoppN").is(":checked")) {
        $("#allalarmoppN").prop("checked", false);
        $("#allalarmoppN").trigger("change");
        $("#deleteallalarmoppN").prop("checked", false);
        $("#editallalarmoppN").prop("checked", false);
    } else {
        $("#allalarmoppN").prop("checked", true);
        $("#allalarmoppN").trigger("change");
    }
})

$("#editallalarmoppN").on("change", function () {
    if (!$("#editallalarmoppN").is(":checked")) {
        $("#deleteallalarmoppN").prop("checked", false);
    } else {
        $("#allalarmoppN").prop("checked", true);
        $("#allalarmoppN").trigger("change");
    }
});

$("#deleteallalarmoppN").on("change", function () {
    if (!$("#deleteallalarmoppN").is(":checked")) {
    } else {
        $("#allalarmoppN").prop("checked", true);
        $("#editallalarmoppN").prop("checked", true);
        $("#allalarmoppN").trigger("change");
    }
});


//
$("#seedonealarmoppN").on("change", function () {
    if (!$("#seedonealarmoppN").is(":checked")) {
        $("#donealarmoppN").prop("checked", false);
        $("#donealarmoppN").trigger("change");
        $("#deletedonealarmoppN").prop("checked", false);
        $("#editdonealarmoppN").prop("checked", false);
    } else {
        $("#donealarmoppN").prop("checked", true);
        $("#donealarmoppN").trigger("change");
    }
});

$("#editdonealarmoppN").on("change", function () {
    if (!$("#editdonealarmoppN").is(":checked")) {
        $("#deletedonealarmoppN").prop("checked", false);
    } else {
        $("#donealarmoppN").prop("checked", true);
        $("#donealarmoppN").trigger("change");
    }
});

$("#deletedonealarmoppN").on("change", function () {
    if (!$("#deletedonealarmoppN").is(":checked")) {
    } else {
        $("#donealarmoppN").prop("checked", true);
        $("#editdonealarmoppN").prop("checked", true);
        $("#donealarmoppN").trigger("change");
    }
});


//
$("#seeNoalarmoppN").on("change", function () {
    if (!$("#seeNoalarmoppN").is(":checked")) {
        $("#NoalarmoppN").prop("checked", false);
        $("#NoalarmoppN").trigger("change");
        $("#deleteNoalarmoppN").prop("checked", false);
        $("#editNoalarmoppN").prop("checked", false);
    } else {
        $("#NoalarmoppN").prop("checked", true);
        $("#NoalarmoppN").trigger("change");
    }
});

$("#editNoalarmoppN").on("change", function () {
    if (!$("#editNoalarmoppN").is(":checked")) {
        $("#deleteNoalarmoppN").prop("checked", false);
    } else {
        $("#NoalarmoppN").prop("checked", true);
        $("#NoalarmoppN").trigger("change");
    }
});

$("#deleteNoalarmoppN").on("change", function () {
    if (!$("#deleteNoalarmoppN").is(":checked")) {
    } else {
        $("#NoalarmoppN").prop("checked", true);
        $("#editNoalarmoppN").prop("checked", true);
        $("#NoalarmoppN").trigger("change");
    }
});

//
$("#seemassageOppN").on("change", function () {
    if (!$("#seemassageOppN").is(":checked")) {
        $("#massageOppN").prop("checked", false);
        $("#massageOppN").trigger("change");
        $("#deletemassageOppN").prop("checked", false);
        $("#editmassageOppN").prop("checked", false);
    } else {
        $("#massageOppN").prop("checked", true);
        $("#massageOppN").trigger("change");
    }
});

$("#editmassageOppN").on("change", function () {
    if (!$("#editmassageOppN").is(":checked")) {
        $("#deletemassageOppN").prop("checked", false);
    } else {
        $("#massageOppN").prop("checked", true);
        $("#massageOppN").trigger("change");
    }
});

$("#deletemassageOppN").on("change", function () {
    if (!$("#deletemassageOppN").is(":checked")) {
    } else {
        $("#massageOppN").prop("checked", true);
        $("#editmassageOppN").prop("checked", true);
        $("#massageOppN").trigger("change");
    }
});

//
$("#seejustBargiriOppN").on("change", function () {
    if (!$("#seejustBargiriOppN").is(":checked")) {
        $("#justBargiriOppN").prop("checked", false);
        $("#justBargiriOppN").trigger("change");
        $("#deletejustBargiriOppN").prop("checked", false);
        $("#editjustBargiriOppN").prop("checked", false);
    } else {
        $("#justBargiriOppN").prop("checked", true);
        $("#justBargiriOppN").trigger("change");
    }
});

$("#editjustBargiriOppN").on("change", function () {
    if (!$("#editjustBargiriOppN").is(":checked")) {
        $("#deletejustBargiriOppN").prop("checked", false);
    } else {
        $("#justBargiriOppN").prop("checked", true);
        $("#justBargiriOppN").trigger("change");
    }
});

$("#deletejustBargiriOppN").on("change", function () {
    if (!$("#deletejustBargiriOppN").is(":checked")) {
    } else {
        $("#justBargiriOppN").prop("checked", true);
        $("#editjustBargiriOppN").prop("checked", true);
        $("#justBargiriOppN").trigger("change");
    }
});
$("#oppN").on("change", function () {
    if ($("#oppN").is(":checked")) {
        $("#oppTakhsisN").prop("checked", true);
        $("#oppManagerN").prop("checked", true);
        $("#seeManagerOppN").prop("checked", true);


        $("#oppHeadN").prop("checked", true);
        $("#seeHeadOppN").prop("checked", true);


        $("#oppBazaryabN").prop("checked", true);
        $("#seeBazaryabOppN").prop("checked", true);


        $("#oppDriverN").prop("checked", true);
        $("#oppDriverServiceN").prop("checked", true);
        $("#seeoppDriverServiceN").prop("checked", true);


        $("#oppBargiriN").prop("checked", true);
        $("#seeoppBargiriN").prop("checked", true);

        $("#oppNazarSanjiN").prop("checked", true);
        $("#todayoppNazarsanjiN").prop("checked", true);
        $("#seetodayoppNazarsanjiN").prop("checked", true);


        $("#pastoppNazarsanjiN").prop("checked", true);
        $("#seepastoppNazarsanjiN").prop("checked", true);


        $("#DoneoppNazarsanjiN").prop("checked", true);
        $("#seeDoneoppNazarsanjiN").prop("checked", true);


        $("#OppupDownBonusN").prop("checked", true);
        $("#AddOppupDownBonusN").prop("checked", true);
        $("#seeAddOppupDownBonusN").prop("checked", true);


        $("#SubOppupDownBonusN").prop("checked", true);
        $("#seeSubOppupDownBonusN").prop("checked", true);


        $("#oppRDN").prop("checked", true);
        $("#AddedoppRDN").prop("checked", true);
        $("#seeAddedoppRDN").prop("checked", true);


        $("#NotAddedoppRDN").prop("checked", true);
        $("#seeNotAddedoppRDN").prop("checked", true);


        $("#oppCalendarN").prop("checked", true);
        $("#oppjustCalendarN").prop("checked", true);
        $("#seeoppjustCalendarN").prop("checked", true);


        $("#oppCustCalendarN").prop("checked", true);
        $("#seeoppCustCalendarN").prop("checked", true);


        $("#alarmoppN").prop("checked", true);
        $("#allalarmoppN").prop("checked", true);
        $("#seeallalarmoppN").prop("checked", true);


        $("#donealarmoppN").prop("checked", true);
        $("#seedonealarmoppN").prop("checked", true);


        $("#NoalarmoppN").prop("checked", true);
        $("#seeNoalarmoppN").prop("checked", true);


        $("#massageOppN").prop("checked", true);
        $("#seemassageOppN").prop("checked", true);


        $("#justBargiriOppN").prop("checked", true);
        $("#seejustBargiriOppN").prop("checked", true);
    } else {
        $("#oppTakhsisN").prop("checked", false);
        $("#oppManagerN").prop("checked", false);
        $("#deleteManagerOppN").prop("checked", false);
        $("#editManagerOppN").prop("checked", false);
        $("#seeManagerOppN").prop("checked", false);


        $("#oppHeadN").prop("checked", false);
        $("#deleteHeadOppN").prop("checked", false);
        $("#editHeadOppN").prop("checked", false);
        $("#seeHeadOppN").prop("checked", false);


        $("#oppBazaryabN").prop("checked", false);
        $("#deleteBazaryabOppN").prop("checked", false);
        $("#editBazaryabOppN").prop("checked", false);
        $("#seeBazaryabOppN").prop("checked", false);


        $("#oppDriverN").prop("checked", false);
        $("#oppDriverServiceN").prop("checked", false);
        $("#deleteoppDriverServiceN").prop("checked", false);
        $("#editoppDriverServiceN").prop("checked", false);
        $("#seeoppDriverServiceN").prop("checked", false);


        $("#oppBargiriN").prop("checked", false);
        $("#deleteoppBargiriN").prop("checked", false);
        $("#editoppBargiriN").prop("checked", false);
        $("#seeoppBargiriN").prop("checked", false);

        $("#oppNazarSanjiN").prop("checked", false);
        $("#todayoppNazarsanjiN").prop("checked", false);
        $("#deletetodayoppNazarsanjiN").prop("checked", false);
        $("#edittodayoppNazarsanjiN").prop("checked", false);
        $("#seetodayoppNazarsanjiN").prop("checked", false);


        $("#pastoppNazarsanjiN").prop("checked", false);
        $("#deletepastoppNazarsanjiN").prop("checked", false);
        $("#editpastoppNazarsanjiN").prop("checked", false);
        $("#seepastoppNazarsanjiN").prop("checked", false);


        $("#DoneoppNazarsanjiN").prop("checked", false);
        $("#deleteDoneoppNazarsanjiN").prop("checked", false);
        $("#editDoneoppNazarsanjiN").prop("checked", false);
        $("#seeDoneoppNazarsanjiN").prop("checked", false);


        $("#OppupDownBonusN").prop("checked", false);
        $("#AddOppupDownBonusN").prop("checked", false);
        $("#deleteAddOppupDownBonusN").prop("checked", false);
        $("#editAddOppupDownBonusN").prop("checked", false);
        $("#seeAddOppupDownBonusN").prop("checked", false);


        $("#SubOppupDownBonusN").prop("checked", false);
        $("#deleteSubOppupDownBonusN").prop("checked", false);
        $("#editSubOppupDownBonusN").prop("checked", false);
        $("#seeSubOppupDownBonusN").prop("checked", false);


        $("#oppRDN").prop("checked", false);
        $("#AddedoppRDN").prop("checked", false);
        $("#deleteAddedoppRDN").prop("checked", false);
        $("#editAddedoppRDN").prop("checked", false);
        $("#seeAddedoppRDN").prop("checked", false);


        $("#NotAddedoppRDN").prop("checked", false);
        $("#deleteNotAddedoppRDN").prop("checked", false);
        $("#editNotAddedoppRDN").prop("checked", false);
        $("#seeNotAddedoppRDN").prop("checked", false);


        $("#oppCalendarN").prop("checked", false);
        $("#oppjustCalendarN").prop("checked", false);
        $("#deleteoppjustCalendarN").prop("checked", false);
        $("#editoppjustCalendarN").prop("checked", false);
        $("#seeoppjustCalendarN").prop("checked", false);


        $("#oppCustCalendarN").prop("checked", false);
        $("#deleteoppCustCalendarN").prop("checked", false);
        $("#editoppCustCalendarN").prop("checked", false);
        $("#seeoppCustCalendarN").prop("checked", false);


        $("#alarmoppN").prop("checked", false);
        $("#allalarmoppN").prop("checked", false);
        $("#deleteallalarmoppN").prop("checked", false);
        $("#editallalarmoppN").prop("checked", false);
        $("#seeallalarmoppN").prop("checked", false);


        $("#donealarmoppN").prop("checked", false);
        $("#deletedonealarmoppN").prop("checked", false);
        $("#editdonealarmoppN").prop("checked", false);
        $("#seedonealarmoppN").prop("checked", false);


        $("#NoalarmoppN").prop("checked", false);
        $("#deleteNoalarmoppN").prop("checked", false);
        $("#editNoalarmoppN").prop("checked", false);
        $("#seeNoalarmoppN").prop("checked", false);


        $("#massageOppN").prop("checked", false);
        $("#deletemassageOppN").prop("checked", false);
        $("#editmassageOppN").prop("checked", false);
        $("#seemassageOppN").prop("checked", false);


        $("#justBargiriOppN").prop("checked", false);
        $("#deletejustBargiriOppN").prop("checked", false);
        $("#editjustBargiriOppN").prop("checked", false);
        $("#seejustBargiriOppN").prop("checked", false);
    }
});


$("#amalKardreportN").on("change", function () {
    if ($("#amalKardreportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#managerreportN").prop("checked", true);
        $("#HeadreportN").prop("checked", true);
        $("#poshtibanreportN").prop("checked", true);
        $("#bazaryabreportN").prop("checked", true);
        $("#reportDriverN").prop("checked", true);

        $("#seemanagerreportN").prop("checked", true);
        $("#seeHeadreportN").prop("checked", true);
        $("#seebazaryabreportN").prop("checked", true);
        $("#seeposhtibanreportN").prop("checked", true);
        $("#seereportDriverN").prop("checked", true);



    } else {
        if (!$(".reportPartN").is(":checked")) {
            $("#reportN").prop("checked", false);
        }
        $("#managerreportN").prop("checked", false);
        $("#HeadreportN").prop("checked", false);
        $("#poshtibanreportN").prop("checked", false);
        $("#bazaryabreportN").prop("checked", false);
        $("#reportDriverN").prop("checked", false);

        $("#seemanagerreportN").prop("checked", false);
        $("#seeHeadreportN").prop("checked", false);
        $("#seebazaryabreportN").prop("checked", false);
        $("#seeposhtibanreportN").prop("checked", false);
        $("#seereportDriverN").prop("checked", false);

        $("#editmanagerreportN").prop("checked", false);
        $("#editHeadreportN").prop("checked", false);
        $("#editbazaryabreportN").prop("checked", false);
        $("#editposhtibanreportN").prop("checked", false);
        $("#editreportDriverN").prop("checked", false);

        $("#deletemanagerreportN").prop("checked", false);
        $("#deleteHeadreportN").prop("checked", false);
        $("#deletebazaryabreportN").prop("checked", false);
        $("#deleteposhtibanreportN").prop("checked", false);
        $("#deletereportDriverN").prop("checked", false);
    }
});

$("#trazEmployeeReportN").on("change", function () {
    if ($("#trazEmployeeReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#seetrazEmployeeReportN").prop("checked", true);
    } else {
        if (!$(".reportPartN").is(":checked")) {
            $("#reportN").prop("checked", false);
        }
        $("#seetrazEmployeeReportN").prop("checked", false);
        $("#edittrazEmployeeReportN").prop("checked", false);
        $("#deletetrazEmployeeReportN").prop("checked", false);
    }
});

$("#customerReportN").on("change", function () {
    if ($("#customerReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#seecustomerReportN").prop("checked", true);
    } else {
        if (!$(".reportPartN").is(":checked")) {
            $("#reportN").prop("checked", false);
        }
        $("#seecustomerReportN").prop("checked", false);
    }
});
$("#amalkardCustReportN").on("change", function () {
    if ($("#amalkardCustReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#loginCustRepN").prop("checked", true);
        $("#inActiveCustRepN").prop("checked", true);
        $("#noAdminCustRepN").prop("checked", true);
        $("#returnedCustRepN").prop("checked", true);

        $("#seeloginCustRepN").prop("checked", true);
        $("#seeinActiveCustRepN").prop("checked", true);
        $("#seenoAdminCustRepN").prop("checked", true);
        $("#seereturnedCustRepN").prop("checked", true);
    } else {
        if (!$(".reportPartN").is(":checked")) {
            $("#reportN").prop("checked", false);
        }
        $("#salegoodsReportN").prop("checked", false);
        $("#loginCustRepN").prop("checked", false);
        $("#inActiveCustRepN").prop("checked", false);
        $("#noAdminCustRepN").prop("checked", false);
        $("#returnedCustRepN").prop("checked", false);

        $("#seeloginCustRepN").prop("checked", false);
        $("#seeinActiveCustRepN").prop("checked", false);
        $("#seenoAdminCustRepN").prop("checked", false);
        $("#seereturnedCustRepN").prop("checked", false);

        $("#editloginCustRepN").prop("checked", false);
        $("#editinActiveCustRepN").prop("checked", false);
        $("#editnoAdminCustRepN").prop("checked", false);
        $("#editreturnedCustRepN").prop("checked", false);


        $("#deleteloginCustRepN").prop("checked", false);
        $("#deleteinActiveCustRepN").prop("checked", false);
        $("#deletenoAdminCustRepN").prop("checked", false);
        $("#deletereturnedCustRepN").prop("checked", false);
    }
});

$("#loginCustRepN").on("change", function () {
    if ($("#loginCustRepN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#amalkardCustReportN").prop("checked", true);
        $("#seeloginCustRepN").prop("checked", true);
    } else {
        if (!$(".amalkardCustReportN").is(":checked")) {
            //$("#reportN").prop("checked",false);
            $("#amalkardCustReportN").prop("checked", false);
            $("#amalkardCustReportN").trigger("change");
        }
        $("#seeloginCustRepN").prop("checked", false);
    }
});

$("#inActiveCustRepN").on("change", function () {
    if ($("#inActiveCustRepN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#amalkardCustReportN").prop("checked", true);
        $("#seeinActiveCustRepN").prop("checked", true);
    } else {
        if (!$(".amalkardCustReportN").is(":checked")) {
            //$("#reportN").prop("checked",false);
            $("#amalkardCustReportN").prop("checked", false);
            $("#amalkardCustReportN").trigger("change");
        }
        $("#seeinActiveCustRepN").prop("checked", false);
    }
});
$("#noAdminCustRepN").on("change", function () {
    if ($("#noAdminCustRepN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#amalkardCustReportN").prop("checked", true);
        $("#seenoAdminCustRepN").prop("checked", true);
    } else {
        if (!$(".amalkardCustReportN").is(":checked")) {
            //$("#reportN").prop("checked",false);
            $("#amalkardCustReportN").prop("checked", false);
            $("#amalkardCustReportN").trigger("change");
        }
        $("#seenoAdminCustRepN").prop("checked", false);
    }
});
$("#returnedCustRepN").on("change", function () {
    if ($("#returnedCustRepN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#amalkardCustReportN").prop("checked", true);
        $("#seereturnedCustRepN").prop("checked", true);
    } else {
        if (!$(".amalkardCustReportN").is(":checked")) {
            //$("#reportN").prop("checked",false);
            $("#amalkardCustReportN").prop("checked", false);
            $("#amalkardCustReportN").trigger("change");
        }
        $("#seereturnedCustRepN").prop("checked", false);
    }
});
$("#loginCustRepN").on("change", function () {
    if ($("#loginCustRepN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#amalkardCustReportN").prop("checked", true);
        $("#seeloginCustRepN").prop("checked", true);
    } else {
        if (!$(".amalkardCustReportN").is(":checked")) {
            //$("#reportN").prop("checked",false);
            $("#amalkardCustReportN").prop("checked", false);
            $("#amalkardCustReportN").trigger("change");
        }
        $("#seeloginCustRepN").prop("checked", false);
    }
});


$("#goodsReportN").on("change", function () {
    if ($("#goodsReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#salegoodsReportN").prop("checked", true);
        $("#returnedgoodsReportN").prop("checked", true);
        $("#NoExistgoodsReportN").prop("checked", true);
        $("#nosalegoodsReportN").prop("checked", true);

        $("#seesalegoodsReportN").prop("checked", true);
        $("#seereturnedgoodsReportN").prop("checked", true);
        $("#seeNoExistgoodsReportN").prop("checked", true);
        $("#seenosalegoodsReportN").prop("checked", true);
    } else {
        if (!$(".reportPartN").is(":checked")) {
            $("#reportN").prop("checked", false);
        }
        $("#salegoodsReportN").prop("checked", false);
        $("#returnedgoodsReportN").prop("checked", false);
        $("#NoExistgoodsReportN").prop("checked", false);
        $("#nosalegoodsReportN").prop("checked", false);

        $("#seesalegoodsReportN").prop("checked", false);
        $("#seereturnedgoodsReportN").prop("checked", false);
        $("#seeNoExistgoodsReportN").prop("checked", false);
        $("#seenosalegoodsReportN").prop("checked", false);

        $("#editsalegoodsReportN").prop("checked", false);
        $("#editreturnedgoodsReportN").prop("checked", false);
        $("#editNoExistgoodsReportN").prop("checked", false);
        $("#editnosalegoodsReportN").prop("checked", false);

        $("#deletesalegoodsReportN").prop("checked", false);
        $("#deletereturnedgoodsReportN").prop("checked", false);
        $("#deleteNoExistgoodsReportN").prop("checked", false);
        $("#deletenosalegoodsReportN").prop("checked", false);
    }
});

$("#returnedReportgoodsReportN").on("change", function () {
    if ($("#returnedReportgoodsReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#returnedNTasReportgoodsReportN").prop("checked", true);
        $("#tasgoodsReprtN").prop("checked", true);

        $("#seereturnedNTasReportgoodsReportN").prop("checked", true);
        $("#seetasgoodsReprtN").prop("checked", true);
    } else {
        if (!$(".reportPartN").is(":checked")) {
            $("#reportN").prop("checked", false);
        }
        $("#returnedNTasReportgoodsReportN").prop("checked", false);
        $("#tasgoodsReprtN").prop("checked", false);

        $("#seereturnedNTasReportgoodsReportN").prop("checked", false);
        $("#seetasgoodsReprtN").prop("checked", false);

        $("#editreturnedNTasReportgoodsReportN").prop("checked", false);
        $("#edittasgoodsReprtN").prop("checked", false);

        $("#deletereturnedNTasReportgoodsReportN").prop("checked", false);
        $("#deletetasgoodsReprtN").prop("checked", false);
    }
});


$("#goodsbargiriReportN").on("change", function () {
    if ($("#goodsbargiriReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#seegoodsbargiriReportN").prop("checked", true);
    } else {
        if (!$(".reportPartN").is(":checked")) {
            $("#reportN").prop("checked", false);
        }
        $("#seegoodsbargiriReportN").prop("checked", false);
        $("#editgoodsbargiriReportN").prop("checked", false);
        $("#deletegoodsbargiriReportN").prop("checked", false);
    }
});

$("#managerreportN").on("change", function () {
    if ($("#managerreportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#amalKardreportN").prop("checked", true);
        $("#seemanagerreportN").prop("checked", true);
    } else {
        if (!$(".amalKardreportN").is(":checked")) {
            // $("#reportN").prop("checked",false);
            $("#amalKardreportN").prop("checked", false);
            $("#amalKardreportN").trigger("change");
        }
        $("#seemanagerreportN").prop("checked", false);
        $("#editmanagerreportN").prop("checked", false);
        $("#deletemanagerreportN").prop("checked", false);
    }
});

$("#HeadreportN").on("change", function () {
    if ($("#HeadreportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#amalKardreportN").prop("checked", true);
        $("#seeHeadreportN").prop("checked", true);
    } else {
        if (!$(".amalKardreportN").is(":checked")) {
            // $("#reportN").prop("checked",false);
            $("#amalKardreportN").prop("checked", false);
            $("#amalKardreportN").trigger("change");
        }
        $("#seeHeadreportN").prop("checked", false);
        $("#editHeadreportN").prop("checked", false);
        $("#deleteHeadreportN").prop("checked", false);
    }
});

$("#poshtibanreportN").on("change", function () {
    if ($("#poshtibanreportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#amalKardreportN").prop("checked", true);
        $("#seeposhtibanreportN").prop("checked", true);
    } else {
        if (!$(".amalKardreportN").is(":checked")) {
            // $("#reportN").prop("checked",false);
            $("#amalKardreportN").prop("checked", false);
            $("#amalKardreportN").trigger("change");
        }
        $("#seeposhtibanreportN").prop("checked", false);
        $("#editposhtibanreportN").prop("checked", false);
        $("#deleteposhtibanreportN").prop("checked", false);
    }
});


$("#bazaryabreportN").on("change", function () {
    if ($("#bazaryabreportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#amalKardreportN").prop("checked", true);
        $("#seebazaryabreportN").prop("checked", true);
    } else {
        if (!$(".amalKardreportN").is(":checked")) {
            // $("#reportN").prop("checked",false);
            $("#amalKardreportN").prop("checked", false);
            $("#amalKardreportN").trigger("change");
        }
        $("#seebazaryabreportN").prop("checked", false);
        $("#editbazaryabreportN").prop("checked", false);
        $("#deletebazaryabreportN").prop("checked", false);
    }
});

$("#reportDriverN").on("change", function () {
    if ($("#reportDriverN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#amalKardreportN").prop("checked", true);
        $("#seereportDriverN").prop("checked", true);
    } else {
        if (!$(".amalKardreportN").is(":checked")) {
            // $("#reportN").prop("checked",false);
            $("#amalKardreportN").prop("checked", false);
            $("#amalKardreportN").trigger("change");
        }
        $("#seereportDriverN").prop("checked", false);
        $("#editreportDriverN").prop("checked", false);
        $("#deletereportDriverN").prop("checked", false);
    }
});


$("#goodsReport").on("change", function () {
    if ($("#goodsReport").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#salegoodsReportN").prop("checked", true);
        $("#returnedgoodsReportN").prop("checked", true);
        $("#NoExistgoodsReportN").prop("checked", true);
        $("#nosalegoodsReportN").prop("checked", true);

        $("#seesalegoodsReportN").prop("checked", true);
        $("#seereturnedgoodsReportN").prop("checked", true);
        $("#seeNoExistgoodsReportN").prop("checked", true);
        $("#seenosalegoodsReportN").prop("checked", true);
    } else {
        if (!$(".reportPartN").is(":checked")) {
            $("#reportN").prop("checked", false);
        }
        $("#salegoodsReportN").prop("checked", false);
        $("#returnedgoodsReportN").prop("checked", false);
        $("#NoExistgoodsReportN").prop("checked", false);
        $("#nosalegoodsReportN").prop("checked", false);

        $("#seesalegoodsReportN").prop("checked", false);
        $("#seereturnedgoodsReportN").prop("checked", false);
        $("#seeNoExistgoodsReportN").prop("checked", false);
        $("#seenosalegoodsReportN").prop("checked", false);

        $("#editsalegoodsReportN").prop("checked", false);
        $("#editreturnedgoodsReportN").prop("checked", false);
        $("#editNoExistgoodsReportN").prop("checked", false);
        $("#editnosalegoodsReportN").prop("checked", false);

        $("#deletesalegoodsReportN").prop("checked", false);
        $("#deletereturnedgoodsReportN").prop("checked", false);
        $("#deleteNoExistgoodsReportN").prop("checked", false);
        $("#deletenosalegoodsReportN").prop("checked", false);
    }
});

$("#returnedReportgoodsReportN").on("change", function () {
    if ($("#returnedReportgoodsReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#returnedNTasReportgoodsReportN").prop("checked", true);
        $("#tasgoodsReprtN").prop("checked", true);

        $("#seereturnedNTasReportgoodsReportN").prop("checked", true);
        $("#seetasgoodsReprtN").prop("checked", true);
    } else {
        if (!$(".reportPartN").is(":checked")) {
            $("#reportN").prop("checked", false);
        }
        $("#returnedNTasReportgoodsReportN").prop("checked", false);
        $("#tasgoodsReprtN").prop("checked", false);

        $("#seereturnedNTasReportgoodsReportN").prop("checked", false);
        $("#seetasgoodsReprtN").prop("checked", false);

        $("#editreturnedNTasReportgoodsReportN").prop("checked", false);
        $("#edittasgoodsReprtN").prop("checked", false);

        $("#deletereturnedNTasReportgoodsReportN").prop("checked", false);
        $("#deletetasgoodsReprtN").prop("checked", false);
    }
});


$("#goodsbargiriReportN").on("change", function () {
    if ($("#goodsbargiriReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#seegoodsbargiriReportN").prop("checked", true);
    } else {
        if (!$(".reportPartN").is(":checked")) {
            $("#reportN").prop("checked", false);
        }
        $("#seegoodsbargiriReportN").prop("checked", false);
        $("#editgoodsbargiriReportN").prop("checked", false);
        $("#deletegoodsbargiriReportN").prop("checked", false);
    }
});




//
$("#seemanagerreportN").on("change", function () {
    if (!$("#seemanagerreportN").is(":checked")) {
        $("#managerreportN").prop("checked", false);
        $("#managerreportN").trigger("change");
        $("#deletemanagerreportN").prop("checked", false);
        $("#editmanagerreportN").prop("checked", false);
    } else {
        $("#managerreportN").prop("checked", true);
        $("#managerreportN").trigger("change");
    }
});

$("#editmanagerreportN").on("change", function () {
    if (!$("#editmanagerreportN").is(":checked")) {
        $("#deletejustBargiriOppN").prop("checked", false);
    } else {
        $("#managerreportN").prop("checked", true);
        $("#managerreportN").trigger("change");
    }
});

$("#deletemanagerreportN").on("change", function () {
    if (!$("#deletemanagerreportN").is(":checked")) {
    } else {
        $("#managerreportN").prop("checked", true);
        $("#editmanagerreportN").prop("checked", true);
        $("#managerreportN").trigger("change");
    }
});

//
$("#seeHeadreportN").on("change", function () {
    if (!$("#seeHeadreportN").is(":checked")) {
        $("#HeadreportN").prop("checked", false);
        $("#HeadreportN").trigger("change");
        $("#deleteHeadreportN").prop("checked", false);
        $("#editHeadreportN").prop("checked", false);
    } else {
        $("#HeadreportN").prop("checked", true);
        $("#HeadreportN").trigger("change");
    }
});

$("#editHeadreportN").on("change", function () {
    if (!$("#editHeadreportN").is(":checked")) {
        $("#deleteHeadreportN").prop("checked", false);
    } else {
        $("#HeadreportN").prop("checked", true);
        $("#HeadreportN").trigger("change");
    }
});

$("#deleteHeadreportN").on("change", function () {
    if (!$("#deleteHeadreportN").is(":checked")) {
    } else {
        $("#HeadreportN").prop("checked", true);
        $("#editHeadreportN").prop("checked", true);
        $("#HeadreportN").trigger("change");
    }
});

//
$("#seeposhtibanreportN").on("change", function () {
    if (!$("#seeposhtibanreportN").is(":checked")) {
        $("#poshtibanreportN").prop("checked", false);
        $("#poshtibanreportN").trigger("change");
        $("#deleteposhtibanreportN").prop("checked", false);
        $("#editposhtibanreportN").prop("checked", false);
    } else {
        $("#poshtibanreportN").prop("checked", true);
        $("#poshtibanreportN").trigger("change");
    }
});

$("#editposhtibanreportN").on("change", function () {
    if (!$("#editposhtibanreportN").is(":checked")) {
        $("#deleteposhtibanreportN").prop("checked", false);
    } else {
        $("#poshtibanreportN").prop("checked", true);
        $("#poshtibanreportN").trigger("change");
    }
});

$("#deleteposhtibanreportN").on("change", function () {
    if (!$("#deleteposhtibanreportN").is(":checked")) {
    } else {
        $("#poshtibanreportN").prop("checked", true);
        $("#editposhtibanreportN").prop("checked", true);
        $("#poshtibanreportN").trigger("change");
    }
});

//
$("#seereportDriverN").on("change", function () {
    if (!$("#seereportDriverN").is(":checked")) {
        $("#reportDriverN").prop("checked", false);
        $("#reportDriverN").trigger("change");
        $("#deletereportDriverN").prop("checked", false);
        $("#editreportDriverN").prop("checked", false);
    } else {
        $("#reportDriverN").prop("checked", true);
        $("#reportDriverN").trigger("change");
    }
});

$("#editreportDriverN").on("change", function () {
    if (!$("#editreportDriverN").is(":checked")) {
        $("#deletereportDriverN").prop("checked", false);
    } else {
        $("#reportDriverN").prop("checked", true);
        $("#reportDriverN").trigger("change");
    }
});

$("#deletereportDriverN").on("change", function () {
    if (!$("#deletereportDriverN").is(":checked")) {
    } else {
        $("#reportDriverN").prop("checked", true);
        $("#editreportDriverN").prop("checked", true);
        $("#reportDriverN").trigger("change");
    }
});


//
$("#seebazaryabreportN").on("change", function () {
    if (!$("#seebazaryabreportN").is(":checked")) {
        $("#bazaryabreportN").prop("checked", false);
        $("#bazaryabreportN").trigger("change");
        $("#deletereportDriverN").prop("checked", false);
        $("#editbazaryabreportN").prop("checked", false);
    } else {
        $("#bazaryabreportN").prop("checked", true);
        $("#bazaryabreportN").trigger("change");
    }
});

$("#editbazaryabreportN").on("change", function () {
    if (!$("#editbazaryabreportN").is(":checked")) {
        $("#deletereportDriverN").prop("checked", false);
    } else {
        $("#bazaryabreportN").prop("checked", true);
        $("#bazaryabreportN").trigger("change");
    }
});

$("#deletebazaryabreportN").on("change", function () {
    if (!$("#deletebazaryabreportN").is(":checked")) {
    } else {
        $("#bazaryabreportN").prop("checked", true);
        $("#editbazaryabreportN").prop("checked", true);
        $("#bazaryabreportN").trigger("change");
    }
});


//
$("#seetrazEmployeeReportN").on("change", function () {
    if (!$("#seetrazEmployeeReportN").is(":checked")) {
        $("#trazEmployeeReportN").prop("checked", false);
        $("#trazEmployeeReportN").trigger("change");
        $("#deletetrazEmployeeReportN").prop("checked", false);
        $("#edittrazEmployeeReportN").prop("checked", false);
    } else {
        $("#trazEmployeeReportN").prop("checked", true);
        $("#trazEmployeeReportN").trigger("change");
    }
});

$("#edittrazEmployeeReportN").on("change", function () {
    if (!$("#edittrazEmployeeReportN").is(":checked")) {
        $("#deletetrazEmployeeReportN").prop("checked", false);
    } else {
        $("#trazEmployeeReportN").prop("checked", true);
        $("#trazEmployeeReportN").trigger("change");
    }
});

$("#deletetrazEmployeeReportN").on("change", function () {
    if (!$("#deletetrazEmployeeReportN").is(":checked")) {
    } else {
        $("#trazEmployeeReportN").prop("checked", true);
        $("#edittrazEmployeeReportN").prop("checked", true);
        $("#trazEmployeeReportN").trigger("change");
    }
});

//
$("#seesalegoodsReportN").on("change", function () {
    if (!$("#seesalegoodsReportN").is(":checked")) {
        $("#salegoodsReportN").prop("checked", false);
        $("#salegoodsReportN").trigger("change");
        $("#deletesalegoodsReportN").prop("checked", false);
        $("#editsalegoodsReportN").prop("checked", false);
    } else {
        $("#salegoodsReportN").prop("checked", true);
        $("#salegoodsReportN").trigger("change");
    }
});

$("#editsalegoodsReportN").on("change", function () {
    if (!$("#editsalegoodsReportN").is(":checked")) {
        $("#deletesalegoodsReportN").prop("checked", false);
    } else {
        $("#salegoodsReportN").prop("checked", true);
        $("#salegoodsReportN").trigger("change");
    }
});

$("#deletesalegoodsReportN").on("change", function () {
    if (!$("#deletesalegoodsReportN").is(":checked")) {
    } else {
        $("#salegoodsReportN").prop("checked", true);
        $("#editsalegoodsReportN").prop("checked", true);
        $("#salegoodsReportN").trigger("change");
    }
});


//
$("#seereturnedgoodsReportN").on("change", function () {
    if (!$("#seereturnedgoodsReportN").is(":checked")) {
        $("#returnedgoodsReportN").prop("checked", false);
        $("#returnedgoodsReportN").trigger("change");
        $("#deletereturnedgoodsReportN").prop("checked", false);
        $("#editreturnedgoodsReportN").prop("checked", false);
    } else {
        $("#returnedgoodsReportN").prop("checked", true);
        $("#returnedgoodsReportN").trigger("change");
    }
});

$("#editreturnedgoodsReportN").on("change", function () {
    if (!$("#editreturnedgoodsReportN").is(":checked")) {
        $("#deletereturnedgoodsReportN").prop("checked", false);
    } else {
        $("#returnedgoodsReportN").prop("checked", true);
        $("#returnedgoodsReportN").trigger("change");
    }
});

$("#deletereturnedgoodsReportN").on("change", function () {
    if (!$("#deletereturnedgoodsReportN").is(":checked")) {
    } else {
        $("#returnedgoodsReportN").prop("checked", true);
        $("#editreturnedgoodsReportN").prop("checked", true);
        $("#returnedgoodsReportN").trigger("change");
    }
});


//
$("#seeNoExistgoodsReportN").on("change", function () {
    if (!$("#seeNoExistgoodsReportN").is(":checked")) {
        $("#NoExistgoodsReportN").prop("checked", false);
        $("#NoExistgoodsReportN").trigger("change");
        $("#deleteNoExistgoodsReportN").prop("checked", false);
        $("#editNoExistgoodsReportN").prop("checked", false);
    } else {
        $("#NoExistgoodsReportN").prop("checked", true);
        $("#NoExistgoodsReportN").trigger("change");
    }
});

$("#editNoExistgoodsReportN").on("change", function () {
    if (!$("#editNoExistgoodsReportN").is(":checked")) {
        $("#deleteNoExistgoodsReportN").prop("checked", false);
    } else {
        $("#NoExistgoodsReportN").prop("checked", true);
        $("#NoExistgoodsReportN").trigger("change");
    }
});

$("#deleteNoExistgoodsReportN").on("change", function () {
    if (!$("#deleteNoExistgoodsReportN").is(":checked")) {
    } else {
        $("#NoExistgoodsReportN").prop("checked", true);
        $("#editNoExistgoodsReportN").prop("checked", true);
        $("#NoExistgoodsReportN").trigger("change");
    }
});

//
$("#seenosalegoodsReportN").on("change", function () {
    if (!$("#seenosalegoodsReportN").is(":checked")) {
        $("#nosalegoodsReportN").prop("checked", false);
        $("#nosalegoodsReportN").trigger("change");
        $("#deletenosalegoodsReportN").prop("checked", false);
        $("#editnosalegoodsReportN").prop("checked", false);
    } else {
        $("#nosalegoodsReportN").prop("checked", true);
        $("#nosalegoodsReportN").trigger("change");
    }
});

$("#editnosalegoodsReportN").on("change", function () {
    if (!$("#editnosalegoodsReportN").is(":checked")) {
        $("#deletenosalegoodsReportN").prop("checked", false);
    } else {
        $("#nosalegoodsReportN").prop("checked", true);
        $("#nosalegoodsReportN").trigger("change");
    }
});

$("#deletenosalegoodsReportN").on("change", function () {
    if (!$("#deletenosalegoodsReportN").is(":checked")) {
    } else {
        $("#nosalegoodsReportN").prop("checked", true);
        $("#editnosalegoodsReportN").prop("checked", true);
        $("#nosalegoodsReportN").trigger("change");
    }
});

//
$("#seereturnedNTasReportgoodsReportN").on("change", function () {
    if (!$("#seereturnedNTasReportgoodsReportN").is(":checked")) {
        $("#returnedNTasReportgoodsReportN").prop("checked", false);
        $("#returnedNTasReportgoodsReportN").trigger("change");
        $("#deletereturnedNTasReportgoodsReportN").prop("checked", false);
        $("#editreturnedNTasReportgoodsReportN").prop("checked", false);
    } else {
        $("#returnedNTasReportgoodsReportN").prop("checked", true);
        $("#returnedNTasReportgoodsReportN").trigger("change");
    }
});

$("#editreturnedNTasReportgoodsReportN").on("change", function () {
    if (!$("#editreturnedNTasReportgoodsReportN").is(":checked")) {
        $("#deletereturnedNTasReportgoodsReportN").prop("checked", false);
    } else {
        $("#returnedNTasReportgoodsReportN").prop("checked", true);
        $("#returnedNTasReportgoodsReportN").trigger("change");
    }
});

$("#deletereturnedNTasReportgoodsReportN").on("change", function () {
    if (!$("#deletereturnedNTasReportgoodsReportN").is(":checked")) {
    } else {
        $("#returnedNTasReportgoodsReportN").prop("checked", true);
        $("#editreturnedNTasReportgoodsReportN").prop("checked", true);
        $("#returnedNTasReportgoodsReportN").trigger("change");
    }
});

$("#returnedNTasReportgoodsReportN").on("change", function () {
    if ($("#returnedNTasReportgoodsReportN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#returnedReportgoodsReportN").prop("checked", true);
        $("#seereturnedNTasReportgoodsReportN").prop("checked", true);
    } else {
        if (!$(".returnedReportgoodsReportN").is(":checked")) {
            // $("#reportN").prop("checked",false);
            $("#returnedReportgoodsReportN").prop("checked", false);
            $("#returnedReportgoodsReportN").trigger("change");
        }
        $("#seereturnedNTasReportgoodsReportN").prop("checked", false);
        $("#editreturnedNTasReportgoodsReportN").prop("checked", false);
        $("#deletereturnedNTasReportgoodsReportN").prop("checked", false);
    }
});

$("#tasgoodsReprtN").on("change", function () {
    if ($("#tasgoodsReprtN").is(":checked")) {
        $("#reportN").prop("checked", true);
        $("#returnedReportgoodsReportN").prop("checked", true);
        $("#seetasgoodsReprtN").prop("checked", true);
    } else {
        if (!$(".returnedReportgoodsReportN").is(":checked")) {
            $("#returnedReportgoodsReportN").prop("checked", false);
            $("#returnedReportgoodsReportN").trigger("change");
        }
        $("#seetasgoodsReprtN").prop("checked", false);
        $("#editasgoodsReprtN").prop("checked", false);
        $("#deleteasgoodsReprtN").prop("checked", false);
    }
});

//
$("#seetasgoodsReprtN").on("change", function () {
    if (!$("#seetasgoodsReprtN").is(":checked")) {
        $("#tasgoodsReprtN").prop("checked", false);
        $("#tasgoodsReprtN").trigger("change");
        $("#deletetasgoodsReprtN").prop("checked", false);
        $("#edittasgoodsReprtN").prop("checked", false);
    } else {
        $("#tasgoodsReprtN").prop("checked", true);
        $("#tasgoodsReprtN").trigger("change");
    }
});

$("#edittasgoodsReprtN").on("change", function () {
    if (!$("#edittasgoodsReprtN").is(":checked")) {
        $("#deletetasgoodsReprtN").prop("checked", false);
    } else {
        $("#tasgoodsReprtN").prop("checked", true);
        $("#tasgoodsReprtN").trigger("change");
    }
});

$("#deletetasgoodsReprtN").on("change", function () {
    if (!$("#deletetasgoodsReprtN").is(":checked")) {
    } else {
        $("#tasgoodsReprtN").prop("checked", true);
        $("#edittasgoodsReprtN").prop("checked", true);
        $("#tasgoodsReprtN").trigger("change");
    }
});

//
$("#seegoodsbargiriReportN").on("change", function () {
    if (!$("#seegoodsbargiriReportN").is(":checked")) {
        $("#goodsbargiriReportN").prop("checked", false);
        $("#goodsbargiriReportN").trigger("change");
        $("#deletegoodsbargiriReportN").prop("checked", false);
        $("#editgoodsbargiriReportN").prop("checked", false);
    } else {
        $("#goodsbargiriReportN").prop("checked", true);
        $("#goodsbargiriReportN").trigger("change");
    }
});

$("#editgoodsbargiriReportN").on("change", function () {
    if (!$("#editgoodsbargiriReportN").is(":checked")) {
        $("#deletegoodsbargiriReportN").prop("checked", false);
    } else {
        $("#goodsbargiriReportN").prop("checked", true);
        $("#goodsbargiriReportN").trigger("change");
    }
});

$("#deletegoodsbargiriReportN").on("change", function () {
    if (!$("#deletegoodsbargiriReportN").is(":checked")) {
    } else {
        $("#goodsbargiriReportN").prop("checked", true);
        $("#editgoodsbargiriReportN").prop("checked", true);
        $("#goodsbargiriReportN").trigger("change");
    }
});

//
$("#seeloginCustRepN").on("change", function () {
    if (!$("#seeloginCustRepN").is(":checked")) {
        $("#loginCustRepN").prop("checked", false);
        $("#loginCustRepN").trigger("change");
        $("#deleteloginCustRepN").prop("checked", false);
        $("#editloginCustRepN").prop("checked", false);
    } else {
        $("#loginCustRepN").prop("checked", true);
        $("#loginCustRepN").trigger("change");
    }
});

$("#editloginCustRepN").on("change", function () {
    if (!$("#editloginCustRepN").is(":checked")) {
        $("#deleteloginCustRepN").prop("checked", false);
    } else {
        $("#loginCustRepN").prop("checked", true);
        $("#loginCustRepN").trigger("change");
    }
});

$("#deleteloginCustRepN").on("change", function () {
    if (!$("#deleteloginCustRepN").is(":checked")) {
    } else {
        $("#loginCustRepN").prop("checked", true);
        $("#editloginCustRepN").prop("checked", true);
        $("#loginCustRepN").trigger("change");
    }
});

//
$("#seeinActiveCustRepN").on("change", function () {
    if (!$("#seeinActiveCustRepN").is(":checked")) {
        $("#inActiveCustRepN").prop("checked", false);
        $("#inActiveCustRepN").trigger("change");
        $("#deleteinActiveCustRepN").prop("checked", false);
        $("#editinActiveCustRepN").prop("checked", false);
    } else {
        $("#inActiveCustRepN").prop("checked", true);
        $("#inActiveCustRepN").trigger("change");
    }
});

$("#editinActiveCustRepN").on("change", function () {
    if (!$("#editinActiveCustRepN").is(":checked")) {
        $("#deleteinActiveCustRepN").prop("checked", false);
    } else {
        $("#inActiveCustRepN").prop("checked", true);
        $("#inActiveCustRepN").trigger("change");
    }
});

$("#deleteinActiveCustRepN").on("change", function () {
    if (!$("#deleteinActiveCustRepN").is(":checked")) {
    } else {
        $("#inActiveCustRepN").prop("checked", true);
        $("#editinActiveCustRepN").prop("checked", true);
        $("#inActiveCustRepN").trigger("change");
    }
});

//
$("#seenoAdminCustRepN").on("change", function () {
    if (!$("#seenoAdminCustRepN").is(":checked")) {
        $("#noAdminCustRepN").prop("checked", false);
        $("#noAdminCustRepN").trigger("change");
        $("#deletenoAdminCustRepN").prop("checked", false);
        $("#editnoAdminCustRepN").prop("checked", false);
    } else {
        $("#noAdminCustRepN").prop("checked", true);
        $("#noAdminCustRepN").trigger("change");
    }
});

$("#editnoAdminCustRepN").on("change", function () {
    if (!$("#editnoAdminCustRepN").is(":checked")) {
        $("#deletenoAdminCustRepN").prop("checked", false);
    } else {
        $("#noAdminCustRepN").prop("checked", true);
        $("#noAdminCustRepN").trigger("change");
    }
});

$("#deletenoAdminCustRepN").on("change", function () {
    if (!$("#deletenoAdminCustRepN").is(":checked")) {
    } else {
        $("#noAdminCustRepN").prop("checked", true);
        $("#editnoAdminCustRepN").prop("checked", true);
        $("#noAdminCustRepN").trigger("change");
    }
});

//
$("#seereturnedCustRepN").on("change", function () {
    if (!$("#seereturnedCustRepN").is(":checked")) {
        $("#returnedCustRepN").prop("checked", false);
        $("#returnedCustRepN").trigger("change");
        $("#deletereturnedCustRepN").prop("checked", false);
        $("#editreturnedCustRepN").prop("checked", false);
    } else {
        $("#returnedCustRepN").prop("checked", true);
        $("#returnedCustRepN").trigger("change");
    }
});

$("#editreturnedCustRepN").on("change", function () {
    if (!$("#editreturnedCustRepN").is(":checked")) {
        $("#deletereturnedCustRepN").prop("checked", false);
    } else {
        $("#returnedCustRepN").prop("checked", true);
        $("#returnedCustRepN").trigger("change");
    }
});

$("#deletereturnedCustRepN").on("change", function () {
    if (!$("#deletereturnedCustRepN").is(":checked")) {
    } else {
        $("#returnedCustRepN").prop("checked", true);
        $("#editreturnedCustRepN").prop("checked", true);
        $("#returnedCustRepN").trigger("change");
    }
});
$(".reportN").on("change", function () {
    if ($(".reportN").is(":checked")) {

        $("#amalKardreportN").prop("checked", true);
        $("#managerreportN").prop("checked", true);
        $("#seemanagerreportN").prop("checked", true);


        $("#HeadreportN").prop("checked", true);
        $("#seeHeadreportN").prop("checked", true);


        $("#poshtibanreportN").prop("checked", true);
        $("#seeposhtibanreportN").prop("checked", true);


        $("#bazaryabreportN").prop("checked", true);
        $("#seebazaryabreportN").prop("checked", true);


        $("#reportDriverN").prop("checked", true);
        $("#seereportDriverN").prop("checked", true);


        $("#trazEmployeeReportN").prop("checked", true);
        $("#seetrazEmployeeReportN").prop("checked", true);

        $("#amalkardCustReportN").prop("checked", true);

        $("#customerReportN").prop("checked", true);
        $("#seecustomerReportN").prop("checked", true);


        $("#goodsReportN").prop("checked", true);
        $("#salegoodsReportN").prop("checked", true);
        $("#seesalegoodsReportN").prop("checked", true);


        $("#returnedgoodsReportN").prop("checked", true);
        $("#seereturnedgoodsReportN").prop("checked", true);


        $("#NoExistgoodsReportN").prop("checked", true);
        $("#seeNoExistgoodsReportN").prop("checked", true);


        $("#nosalegoodsReportN").prop("checked", true);
        $("#seenosalegoodsReportN").prop("checked", true);


        $("#returnedReportgoodsReportN").prop("checked", true);
        $("#returnedNTasReportgoodsReportN").prop("checked", true);
        $("#seereturnedNTasReportgoodsReportN").prop("checked", true);


        $("#tasgoodsReprtN").prop("checked", true);
        $("#seetasgoodsReprtN").prop("checked", true);


        $("#goodsbargiriReportN").prop("checked", true);
        $("#seegoodsbargiriReportN").prop("checked", true);

        $("#loginCustRepN").prop("checked", true);
        $("#seeloginCustRepN").prop("checked", true);

        $("#inActiveCustRepN").prop("checked", true);
        $("#seeinActiveCustRepN").prop("checked", true);

        $("#noAdminCustRepN").prop("checked", true);
        $("#seenoAdminCustRepN").prop("checked", true);

        $("#returnedCustRepN").prop("checked", true);
        $("#seereturnedCustRepN").prop("checked", true);

    } else {
        $("#amalKardreportN").prop("checked", false);
        $("#managerreportN").prop("checked", false);
        $("#deletemanagerreportN").prop("checked", false);
        $("#editmanagerreportN").prop("checked", false);
        $("#seemanagerreportN").prop("checked", false);


        $("#HeadreportN").prop("checked", false);
        $("#deleteHeadreportN").prop("checked", false);
        $("#editHeadreportN").prop("checked", false);
        $("#seeHeadreportN").prop("checked", false);


        $("#poshtibanreportN").prop("checked", false);
        $("#deleteposhtibanreportN").prop("checked", false);
        $("#editposhtibanreportN").prop("checked", false);
        $("#seeposhtibanreportN").prop("checked", false);


        $("#bazaryabreportN").prop("checked", false);
        $("#deletebazaryabreportN").prop("checked", false);
        $("#editbazaryabreportN").prop("checked", false);
        $("#seebazaryabreportN").prop("checked", false);


        $("#reportDriverN").prop("checked", false);
        $("#deletereportDriverN").prop("checked", false);
        $("#editreportDriverN").prop("checked", false);
        $("#seereportDriverN").prop("checked", false);


        $("#trazEmployeeReportN").prop("checked", false);
        $("#deletetrazEmployeeReportN").prop("checked", false);
        $("#edittrazEmployeeReportN").prop("checked", false);
        $("#seetrazEmployeeReportN").prop("checked", false);


        $("#amalkardCustReportN").prop("checked", false);

        $("#customerReportN").prop("checked", false);
        $("#deletecustomerReportN").prop("checked", false);
        $("#editcustomerReportN").prop("checked", false);
        $("#seecustomerReportN").prop("checked", false);

        $("#loginCustRepN").prop("checked", false);
        $("#deleteloginCustRepN").prop("checked", false);
        $("#editloginCustRepN").prop("checked", false);
        $("#seeloginCustRepN").prop("checked", false);

        $("#inActiveCustRepN").prop("checked", false);
        $("#deleteinActiveCustRepN").prop("checked", false);
        $("#editinActiveCustRepN").prop("checked", false);
        $("#seeinActiveCustRepN").prop("checked", false);

        $("#noAdminCustRepN").prop("checked", false);
        $("#deletenoAdminCustRepN").prop("checked", false);
        $("#editnoAdminCustRepN").prop("checked", false);
        $("#seenoAdminCustRepN").prop("checked", false);

        $("#returnedCustRepN").prop("checked", false);
        $("#deletereturnedCustRepN").prop("checked", false);
        $("#editreturnedCustRepN").prop("checked", false);
        $("#seereturnedCustRepN").prop("checked", false);

        $("#goodsReportN").prop("checked", false);
        $("#salegoodsReportN").prop("checked", false);
        $("#deletesalegoodsReportN").prop("checked", false);
        $("#editsalegoodsReportN").prop("checked", false);
        $("#seesalegoodsReportN").prop("checked", false);


        $("#returnedgoodsReportN").prop("checked", false);
        $("#deletereturnedgoodsReportN").prop("checked", false);
        $("#editturnedgoodsReportN").prop("checked", false);
        $("#seereturnedgoodsReportN").prop("checked", false);


        $("#NoExistgoodsReportN").prop("checked", false);
        $("#deleteNoExistgoodsReportN").prop("checked", false);
        $("#editNoExistgoodsReportN").prop("checked", false);
        $("#seeNoExistgoodsReportN").prop("checked", false);


        $("#nosalegoodsReportN").prop("checked", false);
        $("#deletenosalegoodsReportN").prop("checked", false);
        $("#editnosalegoodsReportN").prop("checked", false);
        $("#seenosalegoodsReportN").prop("checked", false);


        $("#returnedReportgoodsReportN").prop("checked", false);
        $("#returnedNTasReportgoodsReportN").prop("checked", false);
        $("#deletereturnedNTasReportgoodsReportN").prop("checked", false);
        $("#editreturnedgoodsReportN").prop("checked", false);
        $("#seereturnedNTasReportgoodsReportN").prop("checked", false);


        $("#tasgoodsReprtN").prop("checked", false);
        $("#deletetasgoodsReprtN").prop("checked", false);
        $("#edittasgoodsReprtN").prop("checked", false);
        $("#seetasgoodsReprtN").prop("checked", false);


        $("#goodsbargiriReportN").prop("checked", false);
        $("#deletegoodsbargiriReportN").prop("checked", false);
        $("#editgoodsbargiriReportN").prop("checked", false);
        $("#seegoodsbargiriReportN").prop("checked", false);
    }
})
// ==================================for edit of access level===============================================

$("#rdSentED").on("change", function () {
    if ($("#rdSentED").is(":checked")) {
        $("#baseInfoED").prop("checked", true);
        $("#infoRdED").prop("checked", true);
        $("#seeSentRdED").prop("checked", true);
    } else {
        if (!$(".rdED").is(":checked")) {
            $("#infoRdED").prop("checked", false);
            $("#infoRdED").trigger("change");
        }
        $("#deleteSentRdED").prop("checked", false);
        $("#editSentRdED").prop("checked", false);
        $("#seeSentRdED").prop("checked", false);
    }
});

$("#rdNotSentED").on("change", function () {
    if ($("#rdNotSentED").is(":checked")) {
        $("#baseInfoED").prop("checked", true);
        $("#infoRdED").prop("checked", true);
        $("#rdNotSentED").prop("checked", true);
        $("#seeRdNotSentED").prop("checked", true);
    } else {
        if (!$(".rdED").is(":checked")) {
            $("#infoRdED").prop("checked", false);
            $("#infoRdED").trigger("change");
        }
        $("#rdNotSentED").prop("checked", false);
        $("#deleteRdNotSentED").prop("checked", false);
        $("#editRdNotSentED").prop("checked", false);
        $("#seeRdNotSentED").prop("checked", false);
    }
})


$("#addSaleLineED").on("change", function () {
    if ($("#addSaleLineED").is(":checked")) {
        $("#baseInfoED").prop("checked", true);
        $("#seeSaleLineED").prop("checked", true);
    } else {
        if (!$(".baseInfoED").is(":checked")) {
            $("#baseInfoED").prop("checked", false);
        }
        $("#deleteSaleLineED").prop("checked", false);
        $("#editSaleLineED").prop("checked", false);
        $("#seeSaleLineED").prop("checked", false);
    }
})


$("#baseInfoSettingED").on("change", function () {
    if ($("#baseInfoSettingED").is(":checked")) {
        $("#baseInfoED").prop("checked", true);
        $("#InfoSettingAccessED").prop("checked", true);
        $("#seeSettingAccessED").prop("checked", true);

        $("#InfoSettingTargetED").prop("checked", true);
        $("#seeSettingTargetED").prop("checked", true);

    } else {
        if (!$(".baseInfoED").is(":checked")) {
            $("#baseInfoED").prop("checked", false);
        }
        $("#InfoSettingAccessED").prop("checked", false);
        $("#deleteSettingAccessED").prop("checked", false);
        $("#editSettingAccessED").prop("checked", false);
        $("#seeSettingAccessED").prop("checked", false);

        $("#InfoSettingTargetED").prop("checked", false);
        $("#deleteSettingTargetED").prop("checked", false);
        $("#editSettingTargetED").prop("checked", false);
        $("#seeSettingTargetED").prop("checked", false);

    }
})

$("#baseInfoProfileED").on("change", function () {
    if ($("#baseInfoProfileED").is(":checked")) {
        $("#baseInfoED").prop("checked", true);
        $("#seeProfileED").prop("checked", true);
    } else {
        if (!$(".baseInfoED").is(":checked")) {
            $("#baseInfoED").prop("checked", false);
        }
        $("#deleteProfileED").prop("checked", false);
        $("#editProfileED").prop("checked", false);
        $("#seeProfileED").prop("checked", false);
    }
})

$("#infoRdED").on("change", function () {
    if ($("#infoRdED").is(":checked")) {

        $("#baseInfoED").prop("checked", true);
        $("#rdSentED").prop("checked", true);
        $("#seeSentRdED").prop("checked", true);

        $("#rdNotSentED").prop("checked", true);
        $("#seeRdNotSentED").prop("checked", true);

    } else {
        if (!$(".baseInfoED").is(":checked")) {

            $("#baseInfoED").prop("checked", false);
        }
        $("#rdSentED").prop("checked", false);
        $("#deleteSentRdED").prop("checked", false);
        $("#editSentRdED").prop("checked", false);
        $("#seeSentRdED").prop("checked", false);

        $("#rdNotSentED").prop("checked", false);
        $("#deleteRdNotSentED").prop("checked", false);
        $("#editRdNotSentED").prop("checked", false);
        $("#seeRdNotSentED").prop("checked", false);
    }
});

$("#seeProfileED").on("change", function () {
    if (!$("#seeProfileED").is(":checked")) {
        $(".ProfileED").prop("checked", false);
        $("#baseInfoProfileED").prop("checked", false);
        $("#baseInfoProfileED").trigger("change");
    } else {
        $("#baseInfoProfileED").prop("checked", true);
        $("#baseInfoProfileED").trigger("change");
    }
})

$("#editProfileED").on("change", function () {
    if (!$("#editProfileED").is(":checked")) {
        $("#deleteProfileED").prop("checked", false);
    } else {
        $("#seeProfileED").prop("checked", false);
        $("#baseInfoProfileED").prop("checked", true);
        $("#baseInfoProfileED").trigger("change");
    }
})

$("#deleteProfileED").on("change", function () {
    if (!$("#deleteProfileED").is(":checked")) {
    } else {
        $(".ProfileED").prop("checked", true);
        $("#baseInfoProfileED").prop("checked", true);
        $("#baseInfoProfileED").trigger("change");
    }
})

//
$("#seeSentRdED").on("change", function () {
    if (!$("#seeSentRdED").is(":checked")) {
        $("#rdSentED").prop("checked", false);
        $("#rdSentED").trigger("change");
    } else {
        $("#rdSentED").prop("checked", true);
        $("#rdSentED").trigger("change");
    }
})

$("#editSentRdED").on("change", function () {
    if (!$("#editSentRdED").is(":checked")) {
        $("#deleteSentRdED").prop("checked", false);
    } else {
        $("#seeSentRdED").prop("checked", true);
        $("#rdSentED").prop("checked", true);
        $("#rdSentED").trigger("change");
    }
});

$("#deleteSentRdED").on("change", function () {
    if (!$("#deleteSentRdED").is(":checked")) {
    } else {
        $("#rdSentED").prop("checked", true);
        $("#editSentRdED").prop("checked", true);
        $("#rdSentED").trigger("change");
    }
})
//
$("#seeRdNotSentED").on("change", function () {
    if (!$("#seeRdNotSentED").is(":checked")) {
        $("#rdNotSentED").prop("checked", false);
        $("#rdNotSentED").trigger("change");
    } else {
        $("#rdNotSentED").prop("checked", true);
        $("#rdNotSentED").trigger("change");
    }
})


$("#editRdNotSentED").on("change", function () {
    if (!$("#editRdNotSentED").is(":checked")) {
        $("#deleteRdNotSentED").prop("checked", false);
    } else {
        $("#seeRdNotSentED").prop("checked", true);
        $("#rdNotSentED").prop("checked", true);
        $("#rdNotSentED").trigger("change");
    }
});


$("#deleteRdNotSentED").on("change", function () {
    if (!$("#deleteRdNotSentED").is(":checked")) {
    } else {
        $("#rdNotSentED").prop("checked", true);
        $("#editRdNotSentED").prop("checked", true);
        $("#rdNotSentED").trigger("change");
    }
})

//
$("#seeSaleLineED").on("change", function () {
    if (!$("#seeSaleLineED").is(":checked")) {
        $("#addSaleLineED").prop("checked", false);
        $("#addSaleLineED").trigger("change");
    } else {
        $("#addSaleLineED").prop("checked", true);
        $("#addSaleLineED").trigger("change");
    }
})


$("#editSaleLineED").on("change", function () {
    if (!$("#editSaleLineED").is(":checked")) {
        $("#deleteSaleLineED").prop("checked", false);
    } else {
        $("#addSaleLineED").prop("checked", true);
        $("#addSaleLineED").trigger("change");
    }
});


$("#deleteSaleLineED").on("change", function () {
    if (!$("#deleteSaleLineED").is(":checked")) {
    } else {
        $("#addSaleLineED").prop("checked", true);
        $("#editSaleLineED").prop("checked", true);
        $("#addSaleLineED").trigger("change");
    }
})

//
$("#seeSettingAccessED").on("change", function () {
    if (!$("#seeSettingAccessED").is(":checked")) {
        $("#InfoSettingAccessED").prop("checked", false);
        $("#InfoSettingAccessED").trigger("change");
    } else {
        $("#InfoSettingAccessED").prop("checked", true);
        $("#InfoSettingAccessED").trigger("change");
    }
})


$("#editSettingAccessED").on("change", function () {
    if (!$("#editSettingAccessED").is(":checked")) {
        $("#deleteSettingAccessED").prop("checked", false);
    } else {
        $("#InfoSettingAccessED").prop("checked", true);
        $("#InfoSettingAccessED").trigger("change");
    }
});


$("#deleteSettingAccessED").on("change", function () {
    if (!$("#deleteSettingAccessED").is(":checked")) {
    } else {
        $("#InfoSettingAccessED").prop("checked", true);
        $("#editSettingAccessED").prop("checked", true);
        $("#InfoSettingAccessED").trigger("change");
    }
})
//
$("#seeSettingTargetED").on("change", function () {
    if (!$("#seeSettingTargetED").is(":checked")) {
        $("#InfoSettingTargetED").prop("checked", false);
        $("#InfoSettingTargetED").trigger("change");
    } else {
        $("#InfoSettingTargetED").prop("checked", true);
        $("#InfoSettingTargetED").trigger("change");
    }
})


$("#editSettingTargetED").on("change", function () {
    if (!$("#editSettingTargetED").is(":checked")) {
        $("#deleteSettingTargetED").prop("checked", false);
    } else {
        $("#InfoSettingTargetED").prop("checked", true);
        $("#InfoSettingTargetED").trigger("change");
    }
});


$("#deleteSettingTargetED").on("change", function () {
    if (!$("#deleteSettingTargetED").is(":checked")) {
    } else {
        $("#InfoSettingTargetED").prop("checked", true);
        $("#editSettingTargetED").prop("checked", true);
        $("#InfoSettingTargetED").trigger("change");
    }
})
//
$("#seedeclareElementED").on("change", function () {
    if (!$("#seedeclareElementED").is(":checked")) {
        $("#declareElementED").prop("checked", false);
        $("#declareElementED").trigger("change");
    } else {
        $("#declareElementED").prop("checked", true);
        $("#declareElementED").trigger("change");
    }
});

$("#declareElementED").on("change", function () {
    if (!$("#declareElementED").is(":checked")) {
        $("#editdeclareElementED").prop("checked", false);
        $("#deletedeclareElementED").prop("checked", false);
        $("#seedeclareElementED").prop("checked", false);
    } else {
        $("#seedeclareElementED").prop("checked", true);
    }
});



$("#editdeclareElementED").on("change", function () {
    if (!$("#editdeclareElementED").is(":checked")) {
        $("#deletedeclareElementED").prop("checked", false);
    } else {
        $("#declareElementED").prop("checked", true);
        $("#declareElementED").trigger("change");
    }
});


$("#deletedeclareElementED").on("change", function () {
    if (!$("#deletedeclareElementED").is(":checked")) {
    } else {
        $("#declareElementED").prop("checked", true);
        $("#editdeclareElementED").prop("checked", true);
        $("#declareElementED").trigger("change");
    }
});

$("#InfoSettingAccessED").on("change", function () {
    if ($("#InfoSettingAccessED").is(":checked")) {
        $("#baseInfoSettingED").prop("checked", true);
        $("#baseInfoED").prop("checked", true);
        $("#InfoSettingAccessED").prop("checked", true);
        $("#seeSettingAccessED").prop("checked", true);
    } else {
        if (!$(".InfoSettingED").is(":checked")) {
            $("#baseInfoSettingED").prop("checked", false);
            $("#baseInfoSettingED").trigger("change");
        }
        $("#deleteSettingAccessED").prop("checked", false);
        $("#editSettingAccessED").prop("checked", false);
        $("#seeSettingAccessED").prop("checked", false);
    }
});


$("#InfoSettingTargetED").on("change", function () {
    if ($("#InfoSettingTargetED").is(":checked")) {
        $("#baseInfoSettingED").prop("checked", true);
        $("#baseInfoED").prop("checked", true);
        $("#seeSettingTargetED").prop("checked", true);
    } else {
        if (!$(".InfoSettingED").is(":checked")) {
            $("#baseInfoSettingED").prop("checked", false);
            $("#baseInfoSettingED").trigger("change");
        }
        $("#deleteSettingTargetED").prop("checked", false);
        $("#editSettingTargetED").prop("checked", false);
        $("#seeSettingTargetED").prop("checked", false);
    }
});



$("#baseInfoED").on("change", function () {
    if ($("#baseInfoED").is(":checked")) {
        $("#seeProfileED").prop("checked", true);
        $("#baseInfoProfileED").prop("checked", true);

        $("#rdSentED").prop("checked", true);
        $("#infoRdED").prop("checked", true);
        $("#seeSentRdED").prop("checked", true);

        $("#rdNotSentED").prop("checked", true);
        $("#baseInfoSettingED").prop("checked", true);
        $("#seeRdNotSentED").prop("checked", true);

        $("#addSaleLineED").prop("checked", true);
        $("#seeSaleLineED").prop("checked", true);

        $("declareElementED").prop("checked", true);
        $("#InfoSettingAccessED").prop("checked", true);
        $("#seeSettingAccessED").prop("checked", true);

        $("#InfoSettingTargetED").prop("checked", true);
        $("#seeSettingTargetED").prop("checked", true);
    } else {
        $("#rdSentED").prop("checked", false);
        $("#infoRdED").prop("checked", false);
        $("#deleteSentRdED").prop("checked", false);
        $("#editSentRdED").prop("checked", false);
        $("#seeSentRdED").prop("checked", false);

        $("#rdNotSentED").prop("checked", false);
        $("#deleteRdNotSentED").prop("checked", false);
        $("#editRdNotSentED").prop("checked", false);
        $("#seeRdNotSentED").prop("checked", false);

        $("#deleteProfileED").prop("checked", false);
        $("#editProfileED").prop("checked", false);
        $("#seeProfileED").prop("checked", false);
        $("#baseInfoProfileED").prop("checked", false);

        $("#addSaleLineED").prop("checked", false);
        $("#deleteSaleLineED").prop("checked", false);
        $("#editSaleLineED").prop("checked", false);
        $("#seeSaleLineED").prop("checked", false);

        $("#baseInfoSettingED").prop("checked", false);
        $("#InfoSettingAccessED").prop("checked", false);
        $("#deleteSettingAccessED").prop("checked", false);
        $("#editSettingAccessED").prop("checked", false);
        $("#seeSettingAccessED").prop("checked", false);

        $("#InfoSettingTargetED").prop("checked", false);
        $("#deleteSettingTargetED").prop("checked", false);
        $("#editSettingTargetED").prop("checked", false);
        $("#seeSettingTargetED").prop("checked", false);
    }
});

$("#salegoodsReportED").on("change", function () {
    if ($("#salegoodsReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#goodsReportED").prop("checked", true);
        $("#seesalegoodsReportED").prop("checked", true);
    } else {
        if (!$(".goodsReportED").is(":checked")) {
            // $("#reportED").prop("checked",false);
            $("#goodsReportED").prop("checked", false);
            $("#goodsReportED").trigger("change");
        }
        $("#seesalegoodsReportED").prop("checked", false);
        $("#editsalegoodsReportED").prop("checked", false);
        $("#deletesalegoodsReportED").prop("checked", false);
    }
});


$("#returnedgoodsReportED").on("change", function () {
    if ($("#returnedgoodsReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#goodsReportED").prop("checked", true);
        $("#seereturnedgoodsReportED").prop("checked", true);
    } else {
        if (!$(".goodsReportED").is(":checked")) {
            // $("#reportED").prop("checked",false);
            $("#goodsReportED").prop("checked", false);
            $("#goodsReportED").trigger("change");
        }
        $("#seereturnedgoodsReportED").prop("checked", false);
        $("#editreturnedgoodsReportED").prop("checked", false);
        $("#deletereturnedgoodsReportED").prop("checked", false);
    }
});
$("#NoExistgoodsReportED").on("change", function () {
    if ($("#NoExistgoodsReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#goodsReportED").prop("checked", true);
        $("#seeNoExistgoodsReportED").prop("checked", true);
    } else {
        if (!$(".goodsReportED").is(":checked")) {
            // $("#reportED").prop("checked",false);
            $("#goodsReportED").prop("checked", false);
            $("#goodsReportED").trigger("change");
        }
        $("#seeNoExistgoodsReportED").prop("checked", false);
        $("#editNoExistgoodsReportED").prop("checked", false);
        $("#deleteNoExistgoodsReportED").prop("checked", false);
    }
});
$("#nosalegoodsReportED").on("change", function () {
    if ($("#nosalegoodsReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#goodsReportED").prop("checked", true);
        $("#seenosalegoodsReportED").prop("checked", true);
    } else {
        if (!$(".goodsReportED").is(":checked")) {
            // $("#reportED").prop("checked",false);
            $("#goodsReportED").prop("checked", false);
            $("#goodsReportED").trigger("change");
        }
        $("#seenosalegoodsReportED").prop("checked", false);
        $("#editnosalegoodsReportED").prop("checked", false);
        $("#deletenosalegoodsReportED").prop("checked", false);
    }
});
$("#oppTakhsisED").on("change", function () {
    if ($("#oppTakhsisED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppManagerED").prop("checked", true);
        $("#oppHeadED").prop("checked", true);
        $("#oppBazaryabED").prop("checked", true);
        $("#seeManagerOppED").prop("checked", true);
        $("#seeBazaryabOppED").prop("checked", true);
        $("#seeHeadOppED").prop("checked", true);
        $("#seeBazaryabOppED").prop("checked", true);
    } else {
        if (!$(".oppPartED").is(":checked")) {
            $("#oppED").prop("checked", false);
        }

        $("#oppManagerED").prop("checked", false);
        $("#oppHeadED").prop("checked", false);
        $("#oppBazaryabED").prop("checked", false);
        $("#seeManagerOppED").prop("checked", false);
        $("#seeBazaryabOppED").prop("checked", false);
        $("#seeHeadOppED").prop("checked", false);

        $("#editManagerOppED").prop("checked", false);
        $("#editBazaryabOppED").prop("checked", false);
        $("#editHeadOppED").prop("checked", false);

        $("#deleteManagerOppED").prop("checked", false);
        $("#deleteBazaryabOppED").prop("checked", false);
        $("#deleteHeadOppED").prop("checked", false);
    }
});

$("#oppDriverED").on("change", function () {
    if ($("#oppDriverED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppDriverServiceED").prop("checked", true);
        $("#oppBargiriED").prop("checked", true);

        $("#seeoppDriverServiceED").prop("checked", true);
        $("#seeoppBargiriED").prop("checked", true);
    } else {
        if (!$(".oppPartED").is(":checked")) {
            $("#oppED").prop("checked", false);
        }

        $("#oppDriverServiceED").prop("checked", false);
        $("#oppBargiriED").prop("checked", false);

        $("#seeoppDriverServiceED").prop("checked", false);
        $("#seeoppBargiriED").prop("checked", false);

        $("#editoppDriverServiceED").prop("checked", false);
        $("#editoppBargiriED").prop("checked", false);

        $("#deleteoppDriverServiceED").prop("checked", false);
        $("#deleteoppBargiriED").prop("checked", false);
    }
});


$("#oppNazarSanjiED").on("change", function () {
    if ($("#oppNazarSanjiED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#todayoppNazarsanjiED").prop("checked", true);
        $("#pastoppNazarsanjiED").prop("checked", true);
        $("#DoneoppNazarsanjiED").prop("checked", true);

        $("#seetodayoppNazarsanjiED").prop("checked", true);
        $("#seepastoppNazarsanjiED").prop("checked", true);
        $("#seeDoneoppNazarsanjiED").prop("checked", true);
    } else {
        if (!$(".oppPartED").is(":checked")) {
            $("#oppED").prop("checked", false);
        }

        $("#todayoppNazarsanjiED").prop("checked", false);
        $("#pastoppNazarsanjiED").prop("checked", false);
        $("#DoneoppNazarsanjiED").prop("checked", false);

        $("#seetodayoppNazarsanjiED").prop("checked", false);
        $("#seepastoppNazarsanjiED").prop("checked", false);
        $("#seeDoneoppNazarsanjiED").prop("checked", false);

        $("#edittodayoppNazarsanjiED").prop("checked", false);
        $("#editpastoppNazarsanjiED").prop("checked", false);
        $("#editDoneoppNazarsanjiED").prop("checked", false);

        $("#deletetodayoppNazarsanjiED").prop("checked", false);
        $("#deletepastoppNazarsanjiED").prop("checked", false);
        $("#deleteDoneoppNazarsanjiED").prop("checked", false);
    }
});


$("#OppupDownBonusED").on("change", function () {
    if ($("#OppupDownBonusED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#AddOppupDownBonusED").prop("checked", true);
        $("#SubOppupDownBonusED").prop("checked", true);


        $("#seeAddOppupDownBonusED").prop("checked", true);
        $("#seeSubOppupDownBonusED").prop("checked", true);

    } else {
        if (!$(".oppPartED").is(":checked")) {
            $("#oppED").prop("checked", false);
        }

        $("#AddOppupDownBonusED").prop("checked", false);
        $("#SubOppupDownBonusED").prop("checked", false);

        $("#seeAddOppupDownBonusED").prop("checked", false);
        $("#seeSubOppupDownBonusED").prop("checked", false);

        $("#editAddOppupDownBonusED").prop("checked", false);
        $("#editSubOppupDownBonusED").prop("checked", false);

        $("#deleteAddOppupDownBonusED").prop("checked", false);
        $("#deleteSubOppupDownBonusED").prop("checked", false);
    }
});

$("#oppRDED").on("change", function () {
    if ($("#oppRDED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#AddedoppRDED").prop("checked", true);
        $("#NotAddedoppRDED").prop("checked", true);

        $("#seeAddedoppRDED").prop("checked", true);
        $("#seeNotAddedoppRDED").prop("checked", true);
    } else {
        if (!$(".oppPartED").is(":checked")) {
            $("#oppED").prop("checked", false);
        }

        $("#AddedoppRDED").prop("checked", false);
        $("#NotAddedoppRDED").prop("checked", false);

        $("#seeAddedoppRDED").prop("checked", false);
        $("#seeNotAddedoppRDED").prop("checked", false);

        $("#editAddedoppRDED").prop("checked", false);
        $("#editNotAddedoppRDED").prop("checked", false);

        $("#deleteAddedoppRDED").prop("checked", false);
        $("#deleteNotAddedoppRDED").prop("checked", false);
    }
});

$("#oppCalendarED").on("change", function () {
    if ($("#oppCalendarED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppjustCalendarED").prop("checked", true);
        $("#oppCustCalendarED").prop("checked", true);

        $("#seeoppjustCalendarED").prop("checked", true);
        $("#seeoppCustCalendarED").prop("checked", true);

    } else {
        if (!$(".oppPartED").is(":checked")) {
            $("#oppED").prop("checked", false);
        }

        $("#oppjustCalendarED").prop("checked", false);
        $("#oppCustCalendarED").prop("checked", false);

        $("#seeoppjustCalendarED").prop("checked", false);
        $("#seeoppCustCalendarED").prop("checked", false);

        $("#editoppjustCalendarED").prop("checked", false);
        $("#editoppCustCalendarED").prop("checked", false);

        $("#deleteoppjustCalendarED").prop("checked", false);
        $("#deleteoppCustCalendarED").prop("checked", false);

    }
});


$("#alarmoppED").on("change", function () {
    if ($("#alarmoppED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#allalarmoppED").prop("checked", true);
        $("#donealarmoppED").prop("checked", true);
        $("#NoalarmoppED").prop("checked", true);

        $("#seeallalarmoppED").prop("checked", true);
        $("#seedonealarmoppED").prop("checked", true);
        $("#seeNoalarmoppED").prop("checked", true);
    } else {
        if (!$(".oppPartED").is(":checked")) {
            $("#oppED").prop("checked", false);
        }

        $("#allalarmoppED").prop("checked", false);
        $("#donealarmoppED").prop("checked", false);
        $("#NoalarmoppED").prop("checked", false);

        $("#seeallalarmoppED").prop("checked", false);
        $("#seedonealarmoppED").prop("checked", false);
        $("#seeNoalarmoppED").prop("checked", false);

        $("#editallalarmoppED").prop("checked", false);
        $("#editdonealarmoppED").prop("checked", false);
        $("#editNoalarmoppED").prop("checked", false);


        $("#deleteallalarmoppED").prop("checked", false);
        $("#deletedonealarmoppED").prop("checked", false);
        $("#deleteNoalarmoppED").prop("checked", false);
    }
});

$("#massageOppED").on("change", function () {
    if ($("#massageOppED").is(":checked")) {
        $("#seemassageOppED").prop("checked", true);
        $("#oppED").prop("checked", true);
    } else {
        if (!$(".oppPartED").is(":checked")) {
            $("#oppED").prop("checked", false);
        }
        $("#seemassageOppED").prop("checked", false);
        $("#editmassageOppED").prop("checked", false);
        $("#deletemassageOppED").prop("checked", false);
    }
});

$("#justBargiriOppED").on("change", function () {
    if ($("#justBargiriOppED").is(":checked")) {
        $("#seejustBargiriOppED").prop("checked", true);
        $("#oppED").prop("checked", true);
    } else {
        if (!$(".oppPartED").is(":checked")) {
            $("#oppED").prop("checked", false);
        }
        $("#seejustBargiriOppED").prop("checked", false);
        $("#editjustBargiriOppED").prop("checked", false);
        $("#deletejustBargiriOppED").prop("checked", false);
    }
});


$("#oppManagerED").on("change", function () {
    if ($("#oppManagerED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppTakhsisED").prop("checked", true);
        $("#seeManagerOppED").prop("checked", true);
    } else {
        if (!$(".oppTakhsisED").is(":checked")) {
            // $("#oppED").prop("checked",false);
            $("#oppTakhsisED").prop("checked", false);
            $("#oppTakhsisED").trigger("change");
        }
        $("#seeManagerOppED").prop("checked", false);
        $("#editManagerOppED").prop("checked", false);
        $("#deleteManagerOppED").prop("checked", false);
    }
});

$("#oppHeadED").on("change", function () {
    if ($("#oppHeadED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppTakhsisED").prop("checked", true);
        $("#seeHeadOppED").prop("checked", true);
    } else {
        if (!$(".oppTakhsisED").is(":checked")) {
            // $("#oppED").prop("checked",false);
            $("#oppTakhsisED").prop("checked", false);
            $("#oppTakhsisED").trigger("change");
        }
        $("#seeHeadOppED").prop("checked", false);
        $("#editHeadOppED").prop("checked", false);
        $("#deleteHeadOppED").prop("checked", false);
    }
});

$("#oppBazaryabED").on("change", function () {
    if ($("#oppBazaryabED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppTakhsisED").prop("checked", true);
        $("#seeBazaryabOppED").prop("checked", true);
    } else {
        if (!$(".oppTakhsisED").is(":checked")) {
            // $("#oppED").prop("checked",false);
            $("#oppTakhsisED").prop("checked", false);
            $("#oppTakhsisED").trigger("change");
        }
        $("#seeBazaryabOppED").prop("checked", false);
        $("#editBazaryabOppED").prop("checked", false);
        $("#deleteBazaryabOppED").prop("checked", false);
    }
});

$("#oppDriverServiceED").on("change", function () {
    if ($("#oppDriverServiceED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppDriverED").prop("checked", true);
        $("#seeoppDriverServiceED").prop("checked", true);
    } else {
        if (!$(".oppDriverED").is(":checked")) {
            //$("#oppED").prop("checked",false);
            $("#oppDriverED").prop("checked", false);
            $("#oppDriverED").trigger("change");
        }
        $("#seeoppDriverServiceED").prop("checked", false);
        $("#editoppDriverServiceED").prop("checked", false);
        $("#deleteoppDriverServiceED").prop("checked", false);
    }
});

$("#oppBargiriED").on("change", function () {
    if ($("#oppBargiriED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppDriverED").prop("checked", true);
        $("#seeoppBargiriED").prop("checked", true);
    } else {
        if (!$(".oppDriverED").is(":checked")) {
            //$("#oppED").prop("checked",false);
            $("#oppDriverED").prop("checked", false);
            $("#oppDriverED").trigger("change");
        }
        $("#seeoppBargiriED").prop("checked", false);
        $("#editoppBargiriED").prop("checked", false);
        $("#deleteoppBargiriED").prop("checked", false);
    }
});

$("#todayoppNazarsanjiED").on("change", function () {
    if ($("#todayoppNazarsanjiED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppNazarSanjiED").prop("checked", true);
        $("#seetodayoppNazarsanjiED").prop("checked", true);
    } else {
        if (!$(".oppNazarSanjiED").is(":checked")) {
            //$("#oppED").prop("checked",false);
            $("#oppNazarSanjiED").prop("checked", false);
            $("#oppNazarSanjiED").trigger("change");
        }
        $("#seetodayoppNazarsanjiED").prop("checked", false);
        $("#edittodayoppNazarsanjiED").prop("checked", false);
        $("#deletetodayoppNazarsanjiED").prop("checked", false);
    }
});

$("#pastoppNazarsanjiED").on("change", function () {
    if ($("#pastoppNazarsanjiED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppNazarSanjiED").prop("checked", true);
        $("#seepastoppNazarsanjiED").prop("checked", true);
    } else {
        if (!$(".oppNazarSanjiED").is(":checked")) {
            //$("#oppED").prop("checked",false);
            $("#oppNazarSanjiED").prop("checked", false);
            $("#oppNazarSanjiED").trigger("change");
        }
        $("#seepastoppNazarsanjiED").prop("checked", false);
        $("#editpastoppNazarsanjiED").prop("checked", false);
        $("#deletepastoppNazarsanjiED").prop("checked", false);
    }
});

$("#DoneoppNazarsanjiED").on("change", function () {
    if ($("#DoneoppNazarsanjiED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppNazarSanjiED").prop("checked", true);
        $("#seeDoneoppNazarsanjiED").prop("checked", true);
    } else {
        if (!$(".oppNazarSanjiED").is(":checked")) {
            // $("#oppED").prop("checked",false);
            $("#oppNazarSanjiED").prop("checked", false);
            $("#oppNazarSanjiED").trigger("change");
        }
        $("#seeDoneoppNazarsanjiED").prop("checked", false);
        $("#editDoneoppNazarsanjiED").prop("checked", false);
        $("#deleteDoneoppNazarsanjiED").prop("checked", false);
    }
});

$("#AddOppupDownBonusED").on("change", function () {
    if ($("#AddOppupDownBonusED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#OppupDownBonusED").prop("checked", true);
        $("#seeAddOppupDownBonusED").prop("checked", true);
    } else {
        if (!$(".OppupDownBonusED").is(":checked")) {
            //$("#oppED").prop("checked",false);
            $("#OppupDownBonusED").prop("checked", false);
            $("#OppupDownBonusED").trigger("change");
        }
        $("#seeAddOppupDownBonusED").prop("checked", false);
        $("#editAddOppupDownBonusED").prop("checked", false);
        $("#deleteAddOppupDownBonusED").prop("checked", false);
    }
});

$("#SubOppupDownBonusED").on("change", function () {
    if ($("#SubOppupDownBonusED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#OppupDownBonusED").prop("checked", true);
        $("#seeSubOppupDownBonusED").prop("checked", true);
    } else {
        if (!$(".OppupDownBonusED").is(":checked")) {
            // $("#oppED").prop("checked",false);
            $("#OppupDownBonusED").prop("checked", false);
            $("#OppupDownBonusED").trigger("change");
        }
        $("#seeSubOppupDownBonusED").prop("checked", false);
        $("#editSubOppupDownBonusED").prop("checked", false);
        $("#deleteSubOppupDownBonusED").prop("checked", false);
    }
});


$("#AddedoppRDED").on("change", function () {
    if ($("#AddedoppRDED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppRDED").prop("checked", true);
        $("#seeAddedoppRDED").prop("checked", true);
    } else {
        if (!$(".oppRDED").is(":checked")) {
            // $("#oppED").prop("checked",false);
            $("#oppRDED").prop("checked", false);
            $("#oppRDED").trigger("change")
        }
        $("#seeAddedoppRDED").prop("checked", false);
        $("#editAddedoppRDED").prop("checked", false);
        $("#deleteAddedoppRDED").prop("checked", false);
    }
});

$("#NotAddedoppRDED").on("change", function () {
    if ($("#NotAddedoppRDED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppRDED").prop("checked", true);
        $("#seeNotAddedoppRDED").prop("checked", true);
    } else {
        if (!$(".oppRDED").is(":checked")) {
            //$("#oppED").prop("checked",false);
            $("#oppRDED").prop("checked", false);
            $("#oppRDED").trigger("change")
        }
        $("#seeNotAddedoppRDED").prop("checked", false);
        $("#editNotAddedoppRDED").prop("checked", false);
        $("#deleteNotAddedoppRDED").prop("checked", false);
    }
});

$("#oppjustCalendarED").on("change", function () {
    if ($("#oppjustCalendarED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppCalendarED").prop("checked", true);
        $("#seeoppjustCalendarED").prop("checked", true);
    } else {
        if (!$(".oppCalendarED").is(":checked")) {
            // $("#oppED").prop("checked",false);
            $("#oppCalendarED").prop("checked", false);
            $("#oppCalendarED").trigger("change")
        }
        $("#seeoppjustCalendarED").prop("checked", false);
        $("#editoppjustCalendarED").prop("checked", false);
        $("#deleteoppjustCalendarED").prop("checked", false);
    }
});

$("#oppCustCalendarED").on("change", function () {
    if ($("#oppCustCalendarED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#oppCalendarED").prop("checked", true);
        $("#seeoppCustCalendarED").prop("checked", true);
    } else {
        if (!$(".oppCalendarED").is(":checked")) {
            //$("#oppED").prop("checked",false);
            $("#oppCalendarED").prop("checked", false);
            $("#oppCalendarED").trigger("change")
        }
        $("#seeoppCustCalendarED").prop("checked", false);
        $("#editoppCustCalendarED").prop("checked", false);
        $("#deleteoppCustCalendarED").prop("checked", false);
    }
});

$("#allalarmoppED").on("change", function () {
    if ($("#allalarmoppED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#alarmoppED").prop("checked", true);
        $("#seeallalarmoppED").prop("checked", true);
    } else {
        if (!$(".alarmoppED").is(":checked")) {
            //$("#oppED").prop("checked",false);
            $("#alarmoppED").prop("checked", false);
            $("#alarmoppED").trigger("change");
        }
        $("#seeallalarmoppED").prop("checked", false);
        $("#editallalarmoppED").prop("checked", false);
        $("#deleteallalarmoppED").prop("checked", false);
    }
});

$("#donealarmoppED").on("change", function () {
    if ($("#donealarmoppED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#alarmoppED").prop("checked", true);
        $("#seedonealarmoppED").prop("checked", true);
    } else {
        if (!$(".alarmoppED").is(":checked")) {
            $("#oppED").prop("checked", false);
            $("#alarmoppED").prop("checked", false);
        }
        $("#seedonealarmoppED").prop("checked", false);
        $("#editdonealarmoppED").prop("checked", false);
        $("#deletedonealarmoppED").prop("checked", false);
    }
});

$("#NoalarmoppED").on("change", function () {
    if ($("#NoalarmoppED").is(":checked")) {
        $("#oppED").prop("checked", true);
        $("#alarmoppED").prop("checked", true);
        $("#seeNoalarmoppED").prop("checked", true);
    } else {
        if (!$(".alarmoppED").is(":checked")) {
            // $("#oppED").prop("checked",false);
            $("#alarmoppED").prop("checked", false);
            $("#alarmoppED").trigger("change");
        }
        $("#seeNoalarmoppED").prop("checked", false);
        $("#editNoalarmoppED").prop("checked", false);
        $("#deleteNoalarmoppED").prop("checked", false);
    }
});

//
$("#seeManagerOppED").on("change", function () {
    if (!$("#seeManagerOppED").is(":checked")) {
        $("#oppManagerED").prop("checked", false);
        $("#oppManagerED").trigger("change");
        $("#editManagerOppED").prop("checked", false);
        $("#deleteManagerOppED").prop("checked", false);
    } else {
        $("#oppManagerED").prop("checked", true);
        $("#oppManagerED").trigger("change");

    }
})


$("#editManagerOppED").on("change", function () {
    if (!$("#editManagerOppED").is(":checked")) {
        $("#deleteManagerOppED").prop("checked", false);
    } else {
        $("#oppManagerED").prop("checked", true);

        $("#oppManagerED").trigger("change");
    }
});


$("#deleteManagerOppED").on("change", function () {
    if (!$("#deleteManagerOppED").is(":checked")) {
    } else {
        $("#oppManagerED").prop("checked", true);
        $("#editManagerOppED").prop("checked", true);
        $("#oppManagerED").trigger("change");
    }
});

//
$("#seeHeadOppED").on("change", function () {
    if (!$("#seeHeadOppED").is(":checked")) {
        $("#oppHeadED").prop("checked", false);
        $("#oppHeadED").trigger("change");
        $("#editHeadOppED").prop("checked", false);
        $("#deleteHeadOppED").prop("checked", false);
    } else {
        $("#oppHeadED").prop("checked", true);
        $("#oppHeadED").trigger("change");

    }
})


$("#editHeadOppED").on("change", function () {
    if (!$("#editHeadOppED").is(":checked")) {
        $("#deleteHeadOppED").prop("checked", false);
    } else {
        $("#oppHeadED").prop("checked", true);

        $("#oppHeadED").trigger("change");
    }
});


$("#deleteHeadOppED").on("change", function () {
    if (!$("#deleteHeadOppED").is(":checked")) {
    } else {
        $("#oppHeadED").prop("checked", true);
        $("#editHeadOppED").prop("checked", true);
        $("#oppHeadED").trigger("change");
    }
});
//
$("#seeBazaryabOppED").on("change", function () {
    if (!$("#seeBazaryabOppED").is(":checked")) {
        $("#oppBazaryabED").prop("checked", false);
        $("#oppBazaryabED").trigger("change");
        $("#deleteBazaryabOppED").prop("checked", false);
        $("#editBazaryabOppED").prop("checked", false);
    } else {
        $("#oppBazaryabED").prop("checked", true);
        $("#oppBazaryabED").trigger("change");

    }
})


$("#editBazaryabOppED").on("change", function () {
    if (!$("#editBazaryabOppED").is(":checked")) {
        $("#deleteBazaryabOppED").prop("checked", false);
    } else {
        $("#oppBazaryabED").prop("checked", true);

        $("#oppBazaryabED").trigger("change");
    }
});


$("#deleteBazaryabOppED").on("change", function () {
    if (!$("#deleteBazaryabOppED").is(":checked")) {
    } else {
        $("#oppBazaryabED").prop("checked", true);
        $("#editBazaryabOppED").prop("checked", true);
        $("#oppBazaryabED").trigger("change");
    }
});

//
$("#seeoppDriverServiceED").on("change", function () {
    if (!$("#seeoppDriverServiceED").is(":checked")) {
        $("#oppDriverServiceED").prop("checked", false);
        $("#oppDriverServiceED").trigger("change");
        $("#deleteoppDriverServiceED").prop("checked", false);
        $("#editoppDriverServiceED").prop("checked", false);
    } else {
        $("#oppDriverServiceED").prop("checked", true);
        $("#oppDriverServiceED").trigger("change");

    }
})


$("#editoppDriverServiceED").on("change", function () {
    if (!$("#editoppDriverServiceED").is(":checked")) {
        $("#deleteoppDriverServiceED").prop("checked", false);
    } else {
        $("#oppDriverServiceED").prop("checked", true);

        $("#oppDriverServiceED").trigger("change");
    }
});


$("#deleteoppDriverServiceED").on("change", function () {
    if (!$("#deleteoppDriverServiceED").is(":checked")) {
    } else {
        $("#oppDriverServiceED").prop("checked", true);
        $("#editoppDriverServiceED").prop("checked", true);
        $("#oppDriverServiceED").trigger("change");
    }
});

//
$("#seeoppBargiriED").on("change", function () {
    if (!$("#seeoppBargiriED").is(":checked")) {
        $("#oppBargiriED").prop("checked", false);
        $("#oppBargiriED").trigger("change");
        $("#deleteoppBargiriED").prop("checked", false);
        $("#editoppBargiriED").prop("checked", false);
    } else {
        $("#oppBargiriED").prop("checked", true);
        $("#oppBargiriED").trigger("change");

    }
})


$("#editoppBargiriED").on("change", function () {
    if (!$("#editoppBargiriED").is(":checked")) {
        $("#deleteoppBargiriED").prop("checked", false);
    } else {
        $("#oppBargiriED").prop("checked", true);

        $("#oppBargiriED").trigger("change");
    }
});


$("#deleteoppBargiriED").on("change", function () {
    if (!$("#deleteoppBargiriED").is(":checked")) {
    } else {
        $("#oppBargiriED").prop("checked", true);
        $("#editoppBargiriED").prop("checked", true);
        $("#oppBargiriED").trigger("change");
    }
});

//
$("#seetodayoppNazarsanjiED").on("change", function () {
    if (!$("#seetodayoppNazarsanjiED").is(":checked")) {
        $("#todayoppNazarsanjiED").prop("checked", false);
        $("#todayoppNazarsanjiED").trigger("change");
        $("#deletetodayoppNazarsanjiED").prop("checked", false);
        $("#edittodayoppNazarsanjiED").prop("checked", false);
    } else {
        $("#todayoppNazarsanjiED").prop("checked", true);
        $("#todayoppNazarsanjiED").trigger("change");

    }
})


$("#edittodayoppNazarsanjiED").on("change", function () {
    if (!$("#edittodayoppNazarsanjiED").is(":checked")) {
        $("#deletetodayoppNazarsanjiED").prop("checked", false);
    } else {
        $("#todayoppNazarsanjiED").prop("checked", true);

        $("#todayoppNazarsanjiED").trigger("change");
    }
});


$("#deletetodayoppNazarsanjiED").on("change", function () {
    if (!$("#deletetodayoppNazarsanjiED").is(":checked")) {
    } else {
        $("#todayoppNazarsanjiED").prop("checked", true);
        $("#edittodayoppNazarsanjiED").prop("checked", true);
        $("#todayoppNazarsanjiED").trigger("change");
    }
});
//
$("#seepastoppNazarsanjiED").on("change", function () {
    if (!$("#seepastoppNazarsanjiED").is(":checked")) {
        $("#pastoppNazarsanjiED").prop("checked", false);
        $("#pastoppNazarsanjiED").trigger("change");
        $("#deletepastoppNazarsanjiED").prop("checked", false);
        $("#editpastoppNazarsanjiED").prop("checked", false);
    } else {
        $("#pastoppNazarsanjiED").prop("checked", true);
        $("#pastoppNazarsanjiED").trigger("change");

    }
})


$("#editpastoppNazarsanjiED").on("change", function () {
    if (!$("#editpastoppNazarsanjiED").is(":checked")) {
        $("#deletepastoppNazarsanjiED").prop("checked", false);
    } else {
        $("#pastoppNazarsanjiED").prop("checked", true);

        $("#pastoppNazarsanjiED").trigger("change");
    }
});


$("#deletepastoppNazarsanjiED").on("change", function () {
    if (!$("#deletepastoppNazarsanjiED").is(":checked")) {
        $("#pastoppNazarsanjiED").trigger("change");
    } else {
        $("#pastoppNazarsanjiED").prop("checked", true);
        $("#pastoppNazarsanjiED").trigger("change");
        $("#editpastoppNazarsanjiED").prop("checked", true);

    }
});
//
$("#seeDoneoppNazarsanjiED").on("change", function () {
    if (!$("#seeDoneoppNazarsanjiED").is(":checked")) {
        $("#DoneoppNazarsanjiED").prop("checked", false);
        $("#DoneoppNazarsanjiED").trigger("change");
        $("#deleteDoneoppNazarsanjiED").prop("checked", false);
        $("#editDoneoppNazarsanjiED").prop("checked", false);
    } else {
        $("#DoneoppNazarsanjiED").prop("checked", true);
        $("#DoneoppNazarsanjiED").trigger("change");
    }
})


$("#editDoneoppNazarsanjiED").on("change", function () {
    if (!$("#editDoneoppNazarsanjiED").is(":checked")) {
        $("#deleteDoneoppNazarsanjiED").prop("checked", false);
    } else {
        $("#DoneoppNazarsanjiED").prop("checked", true);

        $("#DoneoppNazarsanjiED").trigger("change");
    }
});


$("#deleteDoneoppNazarsanjiED").on("change", function () {
    if (!$("#deleteDoneoppNazarsanjiED").is(":checked")) {
    } else {
        $("#DoneoppNazarsanjiED").prop("checked", true);
        $("#editDoneoppNazarsanjiED").prop("checked", true);
        $("#DoneoppNazarsanjiED").trigger("change");
    }
});

//
$("#seeAddOppupDownBonusED").on("change", function () {
    if (!$("#seeAddOppupDownBonusED").is(":checked")) {
        $("#AddOppupDownBonusED").prop("checked", false);
        $("#AddOppupDownBonusED").trigger("change");
        $("#deleteAddOppupDownBonusED").prop("checked", false);
        $("#editAddOppupDownBonusED").prop("checked", false);
    } else {
        $("#AddOppupDownBonusED").prop("checked", true);
        $("#AddOppupDownBonusED").trigger("change");
    }
})


$("#editAddOppupDownBonusED").on("change", function () {
    if (!$("#editAddOppupDownBonusED").is(":checked")) {
        $("#deleteAddOppupDownBonusED").prop("checked", false);
    } else {
        $("#AddOppupDownBonusED").prop("checked", true);

        $("#AddOppupDownBonusED").trigger("change");
    }
});


$("#deleteAddOppupDownBonusED").on("change", function () {
    if (!$("#deleteAddOppupDownBonusED").is(":checked")) {
    } else {
        $("#AddOppupDownBonusED").prop("checked", true);
        $("#editAddOppupDownBonusED").prop("checked", true);
        $("#AddOppupDownBonusED").trigger("change");
    }
});

//
$("#seeSubOppupDownBonusED").on("change", function () {
    if (!$("#seeSubOppupDownBonusED").is(":checked")) {
        $("#SubOppupDownBonusED").prop("checked", false);
        $("#SubOppupDownBonusED").trigger("change");
        $("#deleteSubOppupDownBonusED").prop("checked", false);
        $("#editSubOppupDownBonusED").prop("checked", false);
    } else {
        $("#SubOppupDownBonusED").prop("checked", true);
        $("#SubOppupDownBonusED").trigger("change");
    }
})


$("#editSubOppupDownBonusED").on("change", function () {
    if (!$("#editSubOppupDownBonusED").is(":checked")) {
        $("#deleteSubOppupDownBonusED").prop("checked", false);
    } else {
        $("#SubOppupDownBonusED").prop("checked", true);

        $("#SubOppupDownBonusED").trigger("change");
    }
});


$("#deleteSubOppupDownBonusED").on("change", function () {
    if (!$("#deleteSubOppupDownBonusED").is(":checked")) {
    } else {
        $("#SubOppupDownBonusED").prop("checked", true);
        $("#editSubOppupDownBonusED").prop("checked", true);
        $("#SubOppupDownBonusED").trigger("change");
    }
});

//
$("#seeAddedoppRDED").on("change", function () {
    if (!$("#seeAddedoppRDED").is(":checked")) {
        $("#AddedoppRDED").prop("checked", false);
        $("#AddedoppRDED").trigger("change");
        $("#deleteAddedoppRDED").prop("checked", false);
        $("#editAddedoppRDED").prop("checked", false);
    } else {
        $("#AddedoppRDED").prop("checked", true);
        $("#AddedoppRDED").trigger("change");
    }
})


$("#editAddedoppRDED").on("change", function () {
    if (!$("#editAddedoppRDED").is(":checked")) {
        $("#deleteAddedoppRDED").prop("checked", false);
    } else {
        $("#AddedoppRDED").prop("checked", true);

        $("#AddedoppRDED").trigger("change");
    }
});


$("#deleteAddedoppRDED").on("change", function () {
    if (!$("#deleteAddedoppRDED").is(":checked")) {
    } else {
        $("#AddedoppRDED").prop("checked", true);
        $("#editAddedoppRDED").prop("checked", true);
        $("#AddedoppRDED").trigger("change");
    }
});

//
$("#seeNotAddedoppRDED").on("change", function () {
    if (!$("#seeNotAddedoppRDED").is(":checked")) {
        $("#NotAddedoppRDED").prop("checked", false);
        $("#NotAddedoppRDED").trigger("change");
        $("#deleteNotAddedoppRDED").prop("checked", false);
        $("#editNotAddedoppRDED").prop("checked", false);
    } else {
        $("#NotAddedoppRDED").prop("checked", true);
        $("#NotAddedoppRDED").trigger("change");
    }
})


$("#editNotAddedoppRDED").on("change", function () {
    if (!$("#editNotAddedoppRDED").is(":checked")) {
        $("#deleteNotAddedoppRDED").prop("checked", false);
    } else {
        $("#NotAddedoppRDED").prop("checked", true);
        $("#NotAddedoppRDED").trigger("change");
    }
});


$("#deleteNotAddedoppRDED").on("change", function () {
    if (!$("#deleteNotAddedoppRDED").is(":checked")) {
    } else {
        $("#NotAddedoppRDED").prop("checked", true);
        $("#editNotAddedoppRDED").prop("checked", true);
        $("#NotAddedoppRDED").trigger("change");
    }
});

//
$("#seeoppjustCalendarED").on("change", function () {
    if (!$("#seeoppjustCalendarED").is(":checked")) {
        $("#oppjustCalendarED").prop("checked", false);
        $("#oppjustCalendarED").trigger("change");
        $("#deleteoppjustCalendarED").prop("checked", false);
        $("#editoppjustCalendarED").prop("checked", false);
    } else {
        $("#oppjustCalendarED").prop("checked", true);
        $("#oppjustCalendarED").trigger("change");
    }
})


$("#editoppjustCalendarED").on("change", function () {
    if (!$("#editoppjustCalendarED").is(":checked")) {
        $("#deleteoppjustCalendarED").prop("checked", false);
    } else {
        $("#oppjustCalendarED").prop("checked", true);
        $("#oppjustCalendarED").trigger("change");
    }
});


$("#deleteoppjustCalendarED").on("change", function () {
    if (!$("#deleteoppjustCalendarED").is(":checked")) {
    } else {
        $("#oppjustCalendarED").prop("checked", true);
        $("#editoppjustCalendarED").prop("checked", true);
        $("#oppjustCalendarED").trigger("change");
    }
});


//
$("#seeoppCustCalendarED").on("change", function () {
    if (!$("#seeoppCustCalendarED").is(":checked")) {
        $("#oppCustCalendarED").prop("checked", false);
        $("#oppCustCalendarED").trigger("change");
        $("#editoppCustCalendarED").prop("checked", false);
        $("#deleteoppCustCalendarED").prop("checked", false);
    } else {
        $("#oppCustCalendarED").prop("checked", true);
        $("#oppCustCalendarED").trigger("change");
    }
})

$("#editoppCustCalendarED").on("change", function () {
    if (!$("#editoppCustCalendarED").is(":checked")) {
        $("#deleteoppCustCalendarED").prop("checked", false);
    } else {
        $("#oppCustCalendarED").prop("checked", true);
        $("#oppCustCalendarED").trigger("change");
    }
});

$("#deleteoppCustCalendarED").on("change", function () {
    if (!$("#deleteoppCustCalendarED").is(":checked")) {
    } else {
        $("#oppCustCalendarED").prop("checked", true);
        $("#editoppCustCalendarED").prop("checked", true);
        $("#oppCustCalendarED").trigger("change");
    }
});


//
$("#seeallalarmoppED").on("change", function () {
    if (!$("#seeallalarmoppED").is(":checked")) {
        $("#allalarmoppED").prop("checked", false);
        $("#allalarmoppED").trigger("change");
        $("#deleteallalarmoppED").prop("checked", false);
        $("#editallalarmoppED").prop("checked", false);
    } else {
        $("#allalarmoppED").prop("checked", true);
        $("#allalarmoppED").trigger("change");
    }
})

$("#editallalarmoppED").on("change", function () {
    if (!$("#editallalarmoppED").is(":checked")) {
        $("#deleteallalarmoppED").prop("checked", false);
    } else {
        $("#allalarmoppED").prop("checked", true);
        $("#allalarmoppED").trigger("change");
    }
});

$("#deleteallalarmoppED").on("change", function () {
    if (!$("#deleteallalarmoppED").is(":checked")) {
    } else {
        $("#allalarmoppED").prop("checked", true);
        $("#editallalarmoppED").prop("checked", true);
        $("#allalarmoppED").trigger("change");
    }
});


//
$("#seedonealarmoppED").on("change", function () {
    if (!$("#seedonealarmoppED").is(":checked")) {
        $("#donealarmoppED").prop("checked", false);
        $("#donealarmoppED").trigger("change");
        $("#deletedonealarmoppED").prop("checked", false);
        $("#editdonealarmoppED").prop("checked", false);
    } else {
        $("#donealarmoppED").prop("checked", true);
        $("#donealarmoppED").trigger("change");
    }
});

$("#editdonealarmoppED").on("change", function () {
    if (!$("#editdonealarmoppED").is(":checked")) {
        $("#deletedonealarmoppED").prop("checked", false);
    } else {
        $("#donealarmoppED").prop("checked", true);
        $("#donealarmoppED").trigger("change");
    }
});

$("#deletedonealarmoppED").on("change", function () {
    if (!$("#deletedonealarmoppED").is(":checked")) {
    } else {
        $("#donealarmoppED").prop("checked", true);
        $("#editdonealarmoppED").prop("checked", true);
        $("#donealarmoppED").trigger("change");
    }
});


//
$("#seeNoalarmoppED").on("change", function () {
    if (!$("#seeNoalarmoppED").is(":checked")) {
        $("#NoalarmoppED").prop("checked", false);
        $("#NoalarmoppED").trigger("change");
        $("#deleteNoalarmoppED").prop("checked", false);
        $("#editNoalarmoppED").prop("checked", false);
    } else {
        $("#NoalarmoppED").prop("checked", true);
        $("#NoalarmoppED").trigger("change");
    }
});

$("#editNoalarmoppED").on("change", function () {
    if (!$("#editNoalarmoppED").is(":checked")) {
        $("#deleteNoalarmoppED").prop("checked", false);
    } else {
        $("#NoalarmoppED").prop("checked", true);
        $("#NoalarmoppED").trigger("change");
    }
});

$("#deleteNoalarmoppED").on("change", function () {
    if (!$("#deleteNoalarmoppED").is(":checked")) {
    } else {
        $("#NoalarmoppED").prop("checked", true);
        $("#editNoalarmoppED").prop("checked", true);
        $("#NoalarmoppED").trigger("change");
    }
});

//
$("#seemassageOppED").on("change", function () {
    if (!$("#seemassageOppED").is(":checked")) {
        $("#massageOppED").prop("checked", false);
        $("#massageOppED").trigger("change");
        $("#deletemassageOppED").prop("checked", false);
        $("#editmassageOppED").prop("checked", false);
    } else {
        $("#massageOppED").prop("checked", true);
        $("#massageOppED").trigger("change");
    }
});

$("#editmassageOppED").on("change", function () {
    if (!$("#editmassageOppED").is(":checked")) {
        $("#deletemassageOppED").prop("checked", false);
    } else {
        $("#massageOppED").prop("checked", true);
        $("#massageOppED").trigger("change");
    }
});

$("#deletemassageOppED").on("change", function () {
    if (!$("#deletemassageOppED").is(":checked")) {
    } else {
        $("#massageOppED").prop("checked", true);
        $("#editmassageOppED").prop("checked", true);
        $("#massageOppED").trigger("change");
    }
});

//
$("#seejustBargiriOppED").on("change", function () {
    if (!$("#seejustBargiriOppED").is(":checked")) {
        $("#justBargiriOppED").prop("checked", false);
        $("#justBargiriOppED").trigger("change");
        $("#deletejustBargiriOppED").prop("checked", false);
        $("#editjustBargiriOppED").prop("checked", false);
    } else {
        $("#justBargiriOppED").prop("checked", true);
        $("#justBargiriOppED").trigger("change");
    }
});

$("#editjustBargiriOppED").on("change", function () {
    if (!$("#editjustBargiriOppED").is(":checked")) {
        $("#deletejustBargiriOppED").prop("checked", false);
    } else {
        $("#justBargiriOppED").prop("checked", true);
        $("#justBargiriOppED").trigger("change");
    }
});

$("#deletejustBargiriOppED").on("change", function () {
    if (!$("#deletejustBargiriOppED").is(":checked")) {
    } else {
        $("#justBargiriOppED").prop("checked", true);
        $("#editjustBargiriOppED").prop("checked", true);
        $("#justBargiriOppED").trigger("change");
    }
});
$("#oppED").on("change", function () {
    if ($("#oppED").is(":checked")) {
        $("#oppTakhsisED").prop("checked", true);
        $("#oppManagerED").prop("checked", true);
        $("#seeManagerOppED").prop("checked", true);


        $("#oppHeadED").prop("checked", true);
        $("#seeHeadOppED").prop("checked", true);


        $("#oppBazaryabED").prop("checked", true);
        $("#seeBazaryabOppED").prop("checked", true);


        $("#oppDriverED").prop("checked", true);
        $("#oppDriverServiceED").prop("checked", true);
        $("#seeoppDriverServiceED").prop("checked", true);


        $("#oppBargiriED").prop("checked", true);
        $("#seeoppBargiriED").prop("checked", true);

        $("#oppNazarSanjiED").prop("checked", true);
        $("#todayoppNazarsanjiED").prop("checked", true);
        $("#seetodayoppNazarsanjiED").prop("checked", true);


        $("#pastoppNazarsanjiED").prop("checked", true);
        $("#seepastoppNazarsanjiED").prop("checked", true);


        $("#DoneoppNazarsanjiED").prop("checked", true);
        $("#seeDoneoppNazarsanjiED").prop("checked", true);


        $("#OppupDownBonusED").prop("checked", true);
        $("#AddOppupDownBonusED").prop("checked", true);
        $("#seeAddOppupDownBonusED").prop("checked", true);


        $("#SubOppupDownBonusED").prop("checked", true);
        $("#seeSubOppupDownBonusED").prop("checked", true);


        $("#oppRDED").prop("checked", true);
        $("#AddedoppRDED").prop("checked", true);
        $("#seeAddedoppRDED").prop("checked", true);


        $("#NotAddedoppRDED").prop("checked", true);
        $("#seeNotAddedoppRDED").prop("checked", true);


        $("#oppCalendarED").prop("checked", true);
        $("#oppjustCalendarED").prop("checked", true);
        $("#seeoppjustCalendarED").prop("checked", true);


        $("#oppCustCalendarED").prop("checked", true);
        $("#seeoppCustCalendarED").prop("checked", true);


        $("#alarmoppED").prop("checked", true);
        $("#allalarmoppED").prop("checked", true);
        $("#seeallalarmoppED").prop("checked", true);


        $("#donealarmoppED").prop("checked", true);
        $("#seedonealarmoppED").prop("checked", true);


        $("#NoalarmoppED").prop("checked", true);
        $("#seeNoalarmoppED").prop("checked", true);


        $("#massageOppED").prop("checked", true);
        $("#seemassageOppED").prop("checked", true);


        $("#justBargiriOppED").prop("checked", true);
        $("#seejustBargiriOppED").prop("checked", true);
    } else {
        $("#oppTakhsisED").prop("checked", false);
        $("#oppManagerED").prop("checked", false);
        $("#deleteManagerOppED").prop("checked", false);
        $("#editManagerOppED").prop("checked", false);
        $("#seeManagerOppED").prop("checked", false);


        $("#oppHeadED").prop("checked", false);
        $("#deleteHeadOppED").prop("checked", false);
        $("#editHeadOppED").prop("checked", false);
        $("#seeHeadOppED").prop("checked", false);


        $("#oppBazaryabED").prop("checked", false);
        $("#deleteBazaryabOppED").prop("checked", false);
        $("#editBazaryabOppED").prop("checked", false);
        $("#seeBazaryabOppED").prop("checked", false);


        $("#oppDriverED").prop("checked", false);
        $("#oppDriverServiceED").prop("checked", false);
        $("#deleteoppDriverServiceED").prop("checked", false);
        $("#editoppDriverServiceED").prop("checked", false);
        $("#seeoppDriverServiceED").prop("checked", false);


        $("#oppBargiriED").prop("checked", false);
        $("#deleteoppBargiriED").prop("checked", false);
        $("#editoppBargiriED").prop("checked", false);
        $("#seeoppBargiriED").prop("checked", false);

        $("#oppNazarSanjiED").prop("checked", false);
        $("#todayoppNazarsanjiED").prop("checked", false);
        $("#deletetodayoppNazarsanjiED").prop("checked", false);
        $("#edittodayoppNazarsanjiED").prop("checked", false);
        $("#seetodayoppNazarsanjiED").prop("checked", false);


        $("#pastoppNazarsanjiED").prop("checked", false);
        $("#deletepastoppNazarsanjiED").prop("checked", false);
        $("#editpastoppNazarsanjiED").prop("checked", false);
        $("#seepastoppNazarsanjiED").prop("checked", false);


        $("#DoneoppNazarsanjiED").prop("checked", false);
        $("#deleteDoneoppNazarsanjiED").prop("checked", false);
        $("#editDoneoppNazarsanjiED").prop("checked", false);
        $("#seeDoneoppNazarsanjiED").prop("checked", false);


        $("#OppupDownBonusED").prop("checked", false);
        $("#AddOppupDownBonusED").prop("checked", false);
        $("#deleteAddOppupDownBonusED").prop("checked", false);
        $("#editAddOppupDownBonusED").prop("checked", false);
        $("#seeAddOppupDownBonusED").prop("checked", false);


        $("#SubOppupDownBonusED").prop("checked", false);
        $("#deleteSubOppupDownBonusED").prop("checked", false);
        $("#editSubOppupDownBonusED").prop("checked", false);
        $("#seeSubOppupDownBonusED").prop("checked", false);


        $("#oppRDED").prop("checked", false);
        $("#AddedoppRDED").prop("checked", false);
        $("#deleteAddedoppRDED").prop("checked", false);
        $("#editAddedoppRDED").prop("checked", false);
        $("#seeAddedoppRDED").prop("checked", false);


        $("#NotAddedoppRDED").prop("checked", false);
        $("#deleteNotAddedoppRDED").prop("checked", false);
        $("#editNotAddedoppRDED").prop("checked", false);
        $("#seeNotAddedoppRDED").prop("checked", false);


        $("#oppCalendarED").prop("checked", false);
        $("#oppjustCalendarED").prop("checked", false);
        $("#deleteoppjustCalendarED").prop("checked", false);
        $("#editoppjustCalendarED").prop("checked", false);
        $("#seeoppjustCalendarED").prop("checked", false);


        $("#oppCustCalendarED").prop("checked", false);
        $("#deleteoppCustCalendarED").prop("checked", false);
        $("#editoppCustCalendarED").prop("checked", false);
        $("#seeoppCustCalendarED").prop("checked", false);


        $("#alarmoppED").prop("checked", false);
        $("#allalarmoppED").prop("checked", false);
        $("#deleteallalarmoppED").prop("checked", false);
        $("#editallalarmoppED").prop("checked", false);
        $("#seeallalarmoppED").prop("checked", false);


        $("#donealarmoppED").prop("checked", false);
        $("#deletedonealarmoppED").prop("checked", false);
        $("#editdonealarmoppED").prop("checked", false);
        $("#seedonealarmoppED").prop("checked", false);


        $("#NoalarmoppED").prop("checked", false);
        $("#deleteNoalarmoppED").prop("checked", false);
        $("#editNoalarmoppED").prop("checked", false);
        $("#seeNoalarmoppED").prop("checked", false);


        $("#massageOppED").prop("checked", false);
        $("#deletemassageOppED").prop("checked", false);
        $("#editmassageOppED").prop("checked", false);
        $("#seemassageOppED").prop("checked", false);


        $("#justBargiriOppED").prop("checked", false);
        $("#deletejustBargiriOppED").prop("checked", false);
        $("#editjustBargiriOppED").prop("checked", false);
        $("#seejustBargiriOppED").prop("checked", false);
    }
});


$("#amalKardreportED").on("change", function () {
    if ($("#amalKardreportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#managerreportED").prop("checked", true);
        $("#HeadreportED").prop("checked", true);
        $("#poshtibanreportED").prop("checked", true);
        $("#bazaryabreportED").prop("checked", true);
        $("#reportDriverED").prop("checked", true);

        $("#seemanagerreportED").prop("checked", true);
        $("#seeHeadreportED").prop("checked", true);
        $("#seebazaryabreportED").prop("checked", true);
        $("#seeposhtibanreportED").prop("checked", true);
        $("#seereportDriverED").prop("checked", true);



    } else {
        if (!$(".reportPartED").is(":checked")) {
            $("#reportED").prop("checked", false);
        }
        $("#managerreportED").prop("checked", false);
        $("#HeadreportED").prop("checked", false);
        $("#poshtibanreportED").prop("checked", false);
        $("#bazaryabreportED").prop("checked", false);
        $("#reportDriverED").prop("checked", false);

        $("#seemanagerreportED").prop("checked", false);
        $("#seeHeadreportED").prop("checked", false);
        $("#seebazaryabreportED").prop("checked", false);
        $("#seeposhtibanreportED").prop("checked", false);
        $("#seereportDriverED").prop("checked", false);

        $("#editmanagerreportED").prop("checked", false);
        $("#editHeadreportED").prop("checked", false);
        $("#editbazaryabreportED").prop("checked", false);
        $("#editposhtibanreportED").prop("checked", false);
        $("#editreportDriverED").prop("checked", false);

        $("#deletemanagerreportED").prop("checked", false);
        $("#deleteHeadreportED").prop("checked", false);
        $("#deletebazaryabreportED").prop("checked", false);
        $("#deleteposhtibanreportED").prop("checked", false);
        $("#deletereportDriverED").prop("checked", false);
    }
});

$("#trazEmployeeReportED").on("change", function () {
    if ($("#trazEmployeeReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#seetrazEmployeeReportED").prop("checked", true);
    } else {
        if (!$(".reportPartED").is(":checked")) {
            $("#reportED").prop("checked", false);
        }
        $("#seetrazEmployeeReportED").prop("checked", false);
        $("#edittrazEmployeeReportED").prop("checked", false);
        $("#deletetrazEmployeeReportED").prop("checked", false);
    }
});

$("#customerReportED").on("change", function () {
    if ($("#customerReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#seecustomerReportED").prop("checked", true);
    } else {
        if (!$(".reportPartED").is(":checked")) {
            $("#reportED").prop("checked", false);
        }
        $("#seecustomerReportED").prop("checked", false);
    }
});
$("#amalkardCustReportED").on("change", function () {
    if ($("#amalkardCustReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#loginCustRepED").prop("checked", true);
        $("#inActiveCustRepED").prop("checked", true);
        $("#noAdminCustRepED").prop("checked", true);
        $("#returnedCustRepED").prop("checked", true);

        $("#seeloginCustRepED").prop("checked", true);
        $("#seeinActiveCustRepED").prop("checked", true);
        $("#seenoAdminCustRepED").prop("checked", true);
        $("#seereturnedCustRepED").prop("checked", true);
    } else {
        if (!$(".reportPartED").is(":checked")) {
            $("#reportED").prop("checked", false);
        }
        $("#salegoodsReportED").prop("checked", false);
        $("#loginCustRepED").prop("checked", false);
        $("#inActiveCustRepED").prop("checked", false);
        $("#noAdminCustRepED").prop("checked", false);
        $("#returnedCustRepED").prop("checked", false);

        $("#seeloginCustRepED").prop("checked", false);
        $("#seeinActiveCustRepED").prop("checked", false);
        $("#seenoAdminCustRepED").prop("checked", false);
        $("#seereturnedCustRepED").prop("checked", false);

        $("#editloginCustRepED").prop("checked", false);
        $("#editinActiveCustRepED").prop("checked", false);
        $("#editnoAdminCustRepED").prop("checked", false);
        $("#editreturnedCustRepED").prop("checked", false);


        $("#deleteloginCustRepED").prop("checked", false);
        $("#deleteinActiveCustRepED").prop("checked", false);
        $("#deletenoAdminCustRepED").prop("checked", false);
        $("#deletereturnedCustRepED").prop("checked", false);
    }
});

$("#loginCustRepED").on("change", function () {
    if ($("#loginCustRepED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#amalkardCustReportED").prop("checked", true);
        $("#seeloginCustRepED").prop("checked", true);
    } else {
        if (!$(".amalkardCustReportED").is(":checked")) {
            //$("#reportED").prop("checked",false);
            $("#amalkardCustReportED").prop("checked", false);
            $("#amalkardCustReportED").trigger("change");
        }
        $("#seeloginCustRepED").prop("checked", false);
    }
});

$("#inActiveCustRepED").on("change", function () {
    if ($("#inActiveCustRepED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#amalkardCustReportED").prop("checked", true);
        $("#seeinActiveCustRepED").prop("checked", true);
    } else {
        if (!$(".amalkardCustReportED").is(":checked")) {
            //$("#reportED").prop("checked",false);
            $("#amalkardCustReportED").prop("checked", false);
            $("#amalkardCustReportED").trigger("change");
        }
        $("#seeinActiveCustRepED").prop("checked", false);
    }
});
$("#noAdminCustRepED").on("change", function () {
    if ($("#noAdminCustRepED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#amalkardCustReportED").prop("checked", true);
        $("#seenoAdminCustRepED").prop("checked", true);
    } else {
        if (!$(".amalkardCustReportED").is(":checked")) {
            //$("#reportED").prop("checked",false);
            $("#amalkardCustReportED").prop("checked", false);
            $("#amalkardCustReportED").trigger("change");
        }
        $("#seenoAdminCustRepED").prop("checked", false);
    }
});
$("#returnedCustRepED").on("change", function () {
    if ($("#returnedCustRepED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#amalkardCustReportED").prop("checked", true);
        $("#seereturnedCustRepED").prop("checked", true);
    } else {
        if (!$(".amalkardCustReportED").is(":checked")) {
            //$("#reportED").prop("checked",false);
            $("#amalkardCustReportED").prop("checked", false);
            $("#amalkardCustReportED").trigger("change");
        }
        $("#seereturnedCustRepED").prop("checked", false);
    }
});
$("#loginCustRepED").on("change", function () {
    if ($("#loginCustRepED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#amalkardCustReportED").prop("checked", true);
        $("#seeloginCustRepED").prop("checked", true);
    } else {
        if (!$(".amalkardCustReportED").is(":checked")) {
            //$("#reportED").prop("checked",false);
            $("#amalkardCustReportED").prop("checked", false);
            $("#amalkardCustReportED").trigger("change");
        }
        $("#seeloginCustRepED").prop("checked", false);
    }
});


$("#goodsReportED").on("change", function () {
    if ($("#goodsReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#salegoodsReportED").prop("checked", true);
        $("#returnedgoodsReportED").prop("checked", true);
        $("#NoExistgoodsReportED").prop("checked", true);
        $("#nosalegoodsReportED").prop("checked", true);

        $("#seesalegoodsReportED").prop("checked", true);
        $("#seereturnedgoodsReportED").prop("checked", true);
        $("#seeNoExistgoodsReportED").prop("checked", true);
        $("#seenosalegoodsReportED").prop("checked", true);
    } else {
        if (!$(".reportPartED").is(":checked")) {
            $("#reportED").prop("checked", false);
        }
        $("#salegoodsReportED").prop("checked", false);
        $("#returnedgoodsReportED").prop("checked", false);
        $("#NoExistgoodsReportED").prop("checked", false);
        $("#nosalegoodsReportED").prop("checked", false);

        $("#seesalegoodsReportED").prop("checked", false);
        $("#seereturnedgoodsReportED").prop("checked", false);
        $("#seeNoExistgoodsReportED").prop("checked", false);
        $("#seenosalegoodsReportED").prop("checked", false);

        $("#editsalegoodsReportED").prop("checked", false);
        $("#editreturnedgoodsReportED").prop("checked", false);
        $("#editNoExistgoodsReportED").prop("checked", false);
        $("#editnosalegoodsReportED").prop("checked", false);

        $("#deletesalegoodsReportED").prop("checked", false);
        $("#deletereturnedgoodsReportED").prop("checked", false);
        $("#deleteNoExistgoodsReportED").prop("checked", false);
        $("#deletenosalegoodsReportED").prop("checked", false);
    }
});

$("#returnedReportgoodsReportED").on("change", function () {
    if ($("#returnedReportgoodsReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#returnedNTasReportgoodsReportED").prop("checked", true);
        $("#tasgoodsReprtED").prop("checked", true);

        $("#seereturnedNTasReportgoodsReportED").prop("checked", true);
        $("#seetasgoodsReprtED").prop("checked", true);
    } else {
        if (!$(".reportPartED").is(":checked")) {
            $("#reportED").prop("checked", false);
        }
        $("#returnedNTasReportgoodsReportED").prop("checked", false);
        $("#tasgoodsReprtED").prop("checked", false);

        $("#seereturnedNTasReportgoodsReportED").prop("checked", false);
        $("#seetasgoodsReprtED").prop("checked", false);

        $("#editreturnedNTasReportgoodsReportED").prop("checked", false);
        $("#edittasgoodsReprtED").prop("checked", false);

        $("#deletereturnedNTasReportgoodsReportED").prop("checked", false);
        $("#deletetasgoodsReprtED").prop("checked", false);
    }
});


$("#goodsbargiriReportED").on("change", function () {
    if ($("#goodsbargiriReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#seegoodsbargiriReportED").prop("checked", true);
    } else {
        if (!$(".reportPartED").is(":checked")) {
            $("#reportED").prop("checked", false);
        }
        $("#seegoodsbargiriReportED").prop("checked", false);
        $("#editgoodsbargiriReportED").prop("checked", false);
        $("#deletegoodsbargiriReportED").prop("checked", false);
    }
});

$("#managerreportED").on("change", function () {
    if ($("#managerreportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#amalKardreportED").prop("checked", true);
        $("#seemanagerreportED").prop("checked", true);
    } else {
        if (!$(".amalKardreportED").is(":checked")) {
            // $("#reportED").prop("checked",false);
            $("#amalKardreportED").prop("checked", false);
            $("#amalKardreportED").trigger("change");
        }
        $("#seemanagerreportED").prop("checked", false);
        $("#editmanagerreportED").prop("checked", false);
        $("#deletemanagerreportED").prop("checked", false);
    }
});

$("#HeadreportED").on("change", function () {
    if ($("#HeadreportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#amalKardreportED").prop("checked", true);
        $("#seeHeadreportED").prop("checked", true);
    } else {
        if (!$(".amalKardreportED").is(":checked")) {
            // $("#reportED").prop("checked",false);
            $("#amalKardreportED").prop("checked", false);
            $("#amalKardreportED").trigger("change");
        }
        $("#seeHeadreportED").prop("checked", false);
        $("#editHeadreportED").prop("checked", false);
        $("#deleteHeadreportED").prop("checked", false);
    }
});

$("#poshtibanreportED").on("change", function () {
    if ($("#poshtibanreportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#amalKardreportED").prop("checked", true);
        $("#seeposhtibanreportED").prop("checked", true);
    } else {
        if (!$(".amalKardreportED").is(":checked")) {
            // $("#reportED").prop("checked",false);
            $("#amalKardreportED").prop("checked", false);
            $("#amalKardreportED").trigger("change");
        }
        $("#seeposhtibanreportED").prop("checked", false);
        $("#editposhtibanreportED").prop("checked", false);
        $("#deleteposhtibanreportED").prop("checked", false);
    }
});


$("#bazaryabreportED").on("change", function () {
    if ($("#bazaryabreportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#amalKardreportED").prop("checked", true);
        $("#seebazaryabreportED").prop("checked", true);
    } else {
        if (!$(".amalKardreportED").is(":checked")) {
            // $("#reportED").prop("checked",false);
            $("#amalKardreportED").prop("checked", false);
            $("#amalKardreportED").trigger("change");
        }
        $("#seebazaryabreportED").prop("checked", false);
        $("#editbazaryabreportED").prop("checked", false);
        $("#deletebazaryabreportED").prop("checked", false);
    }
});

$("#reportDriverED").on("change", function () {
    if ($("#reportDriverED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#amalKardreportED").prop("checked", true);
        $("#seereportDriverED").prop("checked", true);
    } else {
        if (!$(".amalKardreportED").is(":checked")) {
            // $("#reportED").prop("checked",false);
            $("#amalKardreportED").prop("checked", false);
            $("#amalKardreportED").trigger("change");
        }
        $("#seereportDriverED").prop("checked", false);
        $("#editreportDriverED").prop("checked", false);
        $("#deletereportDriverED").prop("checked", false);
    }
});


$("#goodsReport").on("change", function () {
    if ($("#goodsReport").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#salegoodsReportED").prop("checked", true);
        $("#returnedgoodsReportED").prop("checked", true);
        $("#NoExistgoodsReportED").prop("checked", true);
        $("#nosalegoodsReportED").prop("checked", true);

        $("#seesalegoodsReportED").prop("checked", true);
        $("#seereturnedgoodsReportED").prop("checked", true);
        $("#seeNoExistgoodsReportED").prop("checked", true);
        $("#seenosalegoodsReportED").prop("checked", true);
    } else {
        if (!$(".reportPartED").is(":checked")) {
            $("#reportED").prop("checked", false);
        }
        $("#salegoodsReportED").prop("checked", false);
        $("#returnedgoodsReportED").prop("checked", false);
        $("#NoExistgoodsReportED").prop("checked", false);
        $("#nosalegoodsReportED").prop("checked", false);

        $("#seesalegoodsReportED").prop("checked", false);
        $("#seereturnedgoodsReportED").prop("checked", false);
        $("#seeNoExistgoodsReportED").prop("checked", false);
        $("#seenosalegoodsReportED").prop("checked", false);

        $("#editsalegoodsReportED").prop("checked", false);
        $("#editreturnedgoodsReportED").prop("checked", false);
        $("#editNoExistgoodsReportED").prop("checked", false);
        $("#editnosalegoodsReportED").prop("checked", false);

        $("#deletesalegoodsReportED").prop("checked", false);
        $("#deletereturnedgoodsReportED").prop("checked", false);
        $("#deleteNoExistgoodsReportED").prop("checked", false);
        $("#deletenosalegoodsReportED").prop("checked", false);
    }
});

$("#returnedReportgoodsReportED").on("change", function () {
    if ($("#returnedReportgoodsReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#returnedNTasReportgoodsReportED").prop("checked", true);
        $("#tasgoodsReprtED").prop("checked", true);

        $("#seereturnedNTasReportgoodsReportED").prop("checked", true);
        $("#seetasgoodsReprtED").prop("checked", true);
    } else {
        if (!$(".reportPartED").is(":checked")) {
            $("#reportED").prop("checked", false);
        }
        $("#returnedNTasReportgoodsReportED").prop("checked", false);
        $("#tasgoodsReprtED").prop("checked", false);

        $("#seereturnedNTasReportgoodsReportED").prop("checked", false);
        $("#seetasgoodsReprtED").prop("checked", false);

        $("#editreturnedNTasReportgoodsReportED").prop("checked", false);
        $("#edittasgoodsReprtED").prop("checked", false);

        $("#deletereturnedNTasReportgoodsReportED").prop("checked", false);
        $("#deletetasgoodsReprtED").prop("checked", false);
    }
});


$("#goodsbargiriReportED").on("change", function () {
    if ($("#goodsbargiriReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#seegoodsbargiriReportED").prop("checked", true);
    } else {
        if (!$(".reportPartED").is(":checked")) {
            $("#reportED").prop("checked", false);
        }
        $("#seegoodsbargiriReportED").prop("checked", false);
        $("#editgoodsbargiriReportED").prop("checked", false);
        $("#deletegoodsbargiriReportED").prop("checked", false);
    }
});




//
$("#seemanagerreportED").on("change", function () {
    if (!$("#seemanagerreportED").is(":checked")) {
        $("#managerreportED").prop("checked", false);
        $("#managerreportED").trigger("change");
        $("#deletemanagerreportED").prop("checked", false);
        $("#editmanagerreportED").prop("checked", false);
    } else {
        $("#managerreportED").prop("checked", true);
        $("#managerreportED").trigger("change");
    }
});

$("#editmanagerreportED").on("change", function () {
    if (!$("#editmanagerreportED").is(":checked")) {
        $("#deletejustBargiriOppED").prop("checked", false);
    } else {
        $("#managerreportED").prop("checked", true);
        $("#managerreportED").trigger("change");
    }
});

$("#deletemanagerreportED").on("change", function () {
    if (!$("#deletemanagerreportED").is(":checked")) {
    } else {
        $("#managerreportED").prop("checked", true);
        $("#editmanagerreportED").prop("checked", true);
        $("#managerreportED").trigger("change");
    }
});

//
$("#seeHeadreportED").on("change", function () {
    if (!$("#seeHeadreportED").is(":checked")) {
        $("#HeadreportED").prop("checked", false);
        $("#HeadreportED").trigger("change");
        $("#deleteHeadreportED").prop("checked", false);
        $("#editHeadreportED").prop("checked", false);
    } else {
        $("#HeadreportED").prop("checked", true);
        $("#HeadreportED").trigger("change");
    }
});

$("#editHeadreportED").on("change", function () {
    if (!$("#editHeadreportED").is(":checked")) {
        $("#deleteHeadreportED").prop("checked", false);
    } else {
        $("#HeadreportED").prop("checked", true);
        $("#HeadreportED").trigger("change");
    }
});

$("#deleteHeadreportED").on("change", function () {
    if (!$("#deleteHeadreportED").is(":checked")) {
    } else {
        $("#HeadreportED").prop("checked", true);
        $("#editHeadreportED").prop("checked", true);
        $("#HeadreportED").trigger("change");
    }
});

//
$("#seeposhtibanreportED").on("change", function () {
    if (!$("#seeposhtibanreportED").is(":checked")) {
        $("#poshtibanreportED").prop("checked", false);
        $("#poshtibanreportED").trigger("change");
        $("#deleteposhtibanreportED").prop("checked", false);
        $("#editposhtibanreportED").prop("checked", false);
    } else {
        $("#poshtibanreportED").prop("checked", true);
        $("#poshtibanreportED").trigger("change");
    }
});

$("#editposhtibanreportED").on("change", function () {
    if (!$("#editposhtibanreportED").is(":checked")) {
        $("#deleteposhtibanreportED").prop("checked", false);
    } else {
        $("#poshtibanreportED").prop("checked", true);
        $("#poshtibanreportED").trigger("change");
    }
});

$("#deleteposhtibanreportED").on("change", function () {
    if (!$("#deleteposhtibanreportED").is(":checked")) {
    } else {
        $("#poshtibanreportED").prop("checked", true);
        $("#editposhtibanreportED").prop("checked", true);
        $("#poshtibanreportED").trigger("change");
    }
});

//
$("#seereportDriverED").on("change", function () {
    if (!$("#seereportDriverED").is(":checked")) {
        $("#reportDriverED").prop("checked", false);
        $("#reportDriverED").trigger("change");
        $("#deletereportDriverED").prop("checked", false);
        $("#editreportDriverED").prop("checked", false);
    } else {
        $("#reportDriverED").prop("checked", true);
        $("#reportDriverED").trigger("change");
    }
});

$("#editreportDriverED").on("change", function () {
    if (!$("#editreportDriverED").is(":checked")) {
        $("#deletereportDriverED").prop("checked", false);
    } else {
        $("#reportDriverED").prop("checked", true);
        $("#reportDriverED").trigger("change");
    }
});

$("#deletereportDriverED").on("change", function () {
    if (!$("#deletereportDriverED").is(":checked")) {
    } else {
        $("#reportDriverED").prop("checked", true);
        $("#editreportDriverED").prop("checked", true);
        $("#reportDriverED").trigger("change");
    }
});


//
$("#seebazaryabreportED").on("change", function () {
    if (!$("#seebazaryabreportED").is(":checked")) {
        $("#bazaryabreportED").prop("checked", false);
        $("#bazaryabreportED").trigger("change");
        $("#deletereportDriverED").prop("checked", false);
        $("#editbazaryabreportED").prop("checked", false);
    } else {
        $("#bazaryabreportED").prop("checked", true);
        $("#bazaryabreportED").trigger("change");
    }
});

$("#editbazaryabreportED").on("change", function () {
    if (!$("#editbazaryabreportED").is(":checked")) {
        $("#deletereportDriverED").prop("checked", false);
    } else {
        $("#bazaryabreportED").prop("checked", true);
        $("#bazaryabreportED").trigger("change");
    }
});

$("#deletebazaryabreportED").on("change", function () {
    if (!$("#deletebazaryabreportED").is(":checked")) {
    } else {
        $("#bazaryabreportED").prop("checked", true);
        $("#editbazaryabreportED").prop("checked", true);
        $("#bazaryabreportED").trigger("change");
    }
});


//
$("#seetrazEmployeeReportED").on("change", function () {
    if (!$("#seetrazEmployeeReportED").is(":checked")) {
        $("#trazEmployeeReportED").prop("checked", false);
        $("#trazEmployeeReportED").trigger("change");
        $("#deletetrazEmployeeReportED").prop("checked", false);
        $("#edittrazEmployeeReportED").prop("checked", false);
    } else {
        $("#trazEmployeeReportED").prop("checked", true);
        $("#trazEmployeeReportED").trigger("change");
    }
});

$("#edittrazEmployeeReportED").on("change", function () {
    if (!$("#edittrazEmployeeReportED").is(":checked")) {
        $("#deletetrazEmployeeReportED").prop("checked", false);
    } else {
        $("#trazEmployeeReportED").prop("checked", true);
        $("#trazEmployeeReportED").trigger("change");
    }
});

$("#deletetrazEmployeeReportED").on("change", function () {
    if (!$("#deletetrazEmployeeReportED").is(":checked")) {
    } else {
        $("#trazEmployeeReportED").prop("checked", true);
        $("#edittrazEmployeeReportED").prop("checked", true);
        $("#trazEmployeeReportED").trigger("change");
    }
});

//
$("#seesalegoodsReportED").on("change", function () {
    if (!$("#seesalegoodsReportED").is(":checked")) {
        $("#salegoodsReportED").prop("checked", false);
        $("#salegoodsReportED").trigger("change");
        $("#deletesalegoodsReportED").prop("checked", false);
        $("#editsalegoodsReportED").prop("checked", false);
    } else {
        $("#salegoodsReportED").prop("checked", true);
        $("#salegoodsReportED").trigger("change");
    }
});

$("#editsalegoodsReportED").on("change", function () {
    if (!$("#editsalegoodsReportED").is(":checked")) {
        $("#deletesalegoodsReportED").prop("checked", false);
    } else {
        $("#salegoodsReportED").prop("checked", true);
        $("#salegoodsReportED").trigger("change");
    }
});

$("#deletesalegoodsReportED").on("change", function () {
    if (!$("#deletesalegoodsReportED").is(":checked")) {
    } else {
        $("#salegoodsReportED").prop("checked", true);
        $("#editsalegoodsReportED").prop("checked", true);
        $("#salegoodsReportED").trigger("change");
    }
});


//
$("#seereturnedgoodsReportED").on("change", function () {
    if (!$("#seereturnedgoodsReportED").is(":checked")) {
        $("#returnedgoodsReportED").prop("checked", false);
        $("#returnedgoodsReportED").trigger("change");
        $("#deletereturnedgoodsReportED").prop("checked", false);
        $("#editreturnedgoodsReportED").prop("checked", false);
    } else {
        $("#returnedgoodsReportED").prop("checked", true);
        $("#returnedgoodsReportED").trigger("change");
    }
});

$("#editreturnedgoodsReportED").on("change", function () {
    if (!$("#editreturnedgoodsReportED").is(":checked")) {
        $("#deletereturnedgoodsReportED").prop("checked", false);
    } else {
        $("#returnedgoodsReportED").prop("checked", true);
        $("#returnedgoodsReportED").trigger("change");
    }
});

$("#deletereturnedgoodsReportED").on("change", function () {
    if (!$("#deletereturnedgoodsReportED").is(":checked")) {
    } else {
        $("#returnedgoodsReportED").prop("checked", true);
        $("#editreturnedgoodsReportED").prop("checked", true);
        $("#returnedgoodsReportED").trigger("change");
    }
});


//
$("#seeNoExistgoodsReportED").on("change", function () {
    if (!$("#seeNoExistgoodsReportED").is(":checked")) {
        $("#NoExistgoodsReportED").prop("checked", false);
        $("#NoExistgoodsReportED").trigger("change");
        $("#deleteNoExistgoodsReportED").prop("checked", false);
        $("#editNoExistgoodsReportED").prop("checked", false);
    } else {
        $("#NoExistgoodsReportED").prop("checked", true);
        $("#NoExistgoodsReportED").trigger("change");
    }
});

$("#editNoExistgoodsReportED").on("change", function () {
    if (!$("#editNoExistgoodsReportED").is(":checked")) {
        $("#deleteNoExistgoodsReportED").prop("checked", false);
    } else {
        $("#NoExistgoodsReportED").prop("checked", true);
        $("#NoExistgoodsReportED").trigger("change");
    }
});

$("#deleteNoExistgoodsReportED").on("change", function () {
    if (!$("#deleteNoExistgoodsReportED").is(":checked")) {
    } else {
        $("#NoExistgoodsReportED").prop("checked", true);
        $("#editNoExistgoodsReportED").prop("checked", true);
        $("#NoExistgoodsReportED").trigger("change");
    }
});

//
$("#seenosalegoodsReportED").on("change", function () {
    if (!$("#seenosalegoodsReportED").is(":checked")) {
        $("#nosalegoodsReportED").prop("checked", false);
        $("#nosalegoodsReportED").trigger("change");
        $("#deletenosalegoodsReportED").prop("checked", false);
        $("#editnosalegoodsReportED").prop("checked", false);
    } else {
        $("#nosalegoodsReportED").prop("checked", true);
        $("#nosalegoodsReportED").trigger("change");
    }
});

$("#editnosalegoodsReportED").on("change", function () {
    if (!$("#editnosalegoodsReportED").is(":checked")) {
        $("#deletenosalegoodsReportED").prop("checked", false);
    } else {
        $("#nosalegoodsReportED").prop("checked", true);
        $("#nosalegoodsReportED").trigger("change");
    }
});

$("#deletenosalegoodsReportED").on("change", function () {
    if (!$("#deletenosalegoodsReportED").is(":checked")) {
    } else {
        $("#nosalegoodsReportED").prop("checked", true);
        $("#editnosalegoodsReportED").prop("checked", true);
        $("#nosalegoodsReportED").trigger("change");
    }
});

//
$("#seereturnedNTasReportgoodsReportED").on("change", function () {
    if (!$("#seereturnedNTasReportgoodsReportED").is(":checked")) {
        $("#returnedNTasReportgoodsReportED").prop("checked", false);
        $("#returnedNTasReportgoodsReportED").trigger("change");
        $("#deletereturnedNTasReportgoodsReportED").prop("checked", false);
        $("#editreturnedNTasReportgoodsReportED").prop("checked", false);
    } else {
        $("#returnedNTasReportgoodsReportED").prop("checked", true);
        $("#returnedNTasReportgoodsReportED").trigger("change");
    }
});

$("#editreturnedNTasReportgoodsReportED").on("change", function () {
    if (!$("#editreturnedNTasReportgoodsReportED").is(":checked")) {
        $("#deletereturnedNTasReportgoodsReportED").prop("checked", false);
    } else {
        $("#returnedNTasReportgoodsReportED").prop("checked", true);
        $("#returnedNTasReportgoodsReportED").trigger("change");
    }
});

$("#deletereturnedNTasReportgoodsReportED").on("change", function () {
    if (!$("#deletereturnedNTasReportgoodsReportED").is(":checked")) {
    } else {
        $("#returnedNTasReportgoodsReportED").prop("checked", true);
        $("#editreturnedNTasReportgoodsReportED").prop("checked", true);
        $("#returnedNTasReportgoodsReportED").trigger("change");
    }
});

$("#returnedNTasReportgoodsReportED").on("change", function () {
    if ($("#returnedNTasReportgoodsReportED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#returnedReportgoodsReportED").prop("checked", true);
        $("#seereturnedNTasReportgoodsReportED").prop("checked", true);
    } else {
        if (!$(".returnedReportgoodsReportED").is(":checked")) {
            // $("#reportED").prop("checked",false);
            $("#returnedReportgoodsReportED").prop("checked", false);
            $("#returnedReportgoodsReportED").trigger("change");
        }
        $("#seereturnedNTasReportgoodsReportED").prop("checked", false);
        $("#editreturnedNTasReportgoodsReportED").prop("checked", false);
        $("#deletereturnedNTasReportgoodsReportED").prop("checked", false);
    }
});

$("#tasgoodsReprtED").on("change", function () {
    if ($("#tasgoodsReprtED").is(":checked")) {
        $("#reportED").prop("checked", true);
        $("#returnedReportgoodsReportED").prop("checked", true);
        $("#seetasgoodsReprtED").prop("checked", true);
    } else {
        if (!$(".returnedReportgoodsReportED").is(":checked")) {
            $("#returnedReportgoodsReportED").prop("checked", false);
            $("#returnedReportgoodsReportED").trigger("change");
        }
        $("#seetasgoodsReprtED").prop("checked", false);
        $("#editasgoodsReprtED").prop("checked", false);
        $("#deleteasgoodsReprtED").prop("checked", false);
    }
});

//
$("#seetasgoodsReprtED").on("change", function () {
    if (!$("#seetasgoodsReprtED").is(":checked")) {
        $("#tasgoodsReprtED").prop("checked", false);
        $("#tasgoodsReprtED").trigger("change");
        $("#deletetasgoodsReprtED").prop("checked", false);
        $("#edittasgoodsReprtED").prop("checked", false);
    } else {
        $("#tasgoodsReprtED").prop("checked", true);
        $("#tasgoodsReprtED").trigger("change");
    }
});

$("#edittasgoodsReprtED").on("change", function () {
    if (!$("#edittasgoodsReprtED").is(":checked")) {
        $("#deletetasgoodsReprtED").prop("checked", false);
    } else {
        $("#tasgoodsReprtED").prop("checked", true);
        $("#tasgoodsReprtED").trigger("change");
    }
});

$("#deletetasgoodsReprtED").on("change", function () {
    if (!$("#deletetasgoodsReprtED").is(":checked")) {
    } else {
        $("#tasgoodsReprtED").prop("checked", true);
        $("#edittasgoodsReprtED").prop("checked", true);
        $("#tasgoodsReprtED").trigger("change");
    }
});

//
$("#seegoodsbargiriReportED").on("change", function () {
    if (!$("#seegoodsbargiriReportED").is(":checked")) {
        $("#goodsbargiriReportED").prop("checked", false);
        $("#goodsbargiriReportED").trigger("change");
        $("#deletegoodsbargiriReportED").prop("checked", false);
        $("#editgoodsbargiriReportED").prop("checked", false);
    } else {
        $("#goodsbargiriReportED").prop("checked", true);
        $("#goodsbargiriReportED").trigger("change");
    }
});

$("#editgoodsbargiriReportED").on("change", function () {
    if (!$("#editgoodsbargiriReportED").is(":checked")) {
        $("#deletegoodsbargiriReportED").prop("checked", false);
    } else {
        $("#goodsbargiriReportED").prop("checked", true);
        $("#goodsbargiriReportED").trigger("change");
    }
});

$("#deletegoodsbargiriReportED").on("change", function () {
    if (!$("#deletegoodsbargiriReportED").is(":checked")) {
    } else {
        $("#goodsbargiriReportED").prop("checked", true);
        $("#editgoodsbargiriReportED").prop("checked", true);
        $("#goodsbargiriReportED").trigger("change");
    }
});

//
$("#seeloginCustRepED").on("change", function () {
    if (!$("#seeloginCustRepED").is(":checked")) {
        $("#loginCustRepED").prop("checked", false);
        $("#loginCustRepED").trigger("change");
        $("#deleteloginCustRepED").prop("checked", false);
        $("#editloginCustRepED").prop("checked", false);
    } else {
        $("#loginCustRepED").prop("checked", true);
        $("#loginCustRepED").trigger("change");
    }
});

$("#editloginCustRepED").on("change", function () {
    if (!$("#editloginCustRepED").is(":checked")) {
        $("#deleteloginCustRepED").prop("checked", false);
    } else {
        $("#loginCustRepED").prop("checked", true);
        $("#loginCustRepED").trigger("change");
    }
});

$("#deleteloginCustRepED").on("change", function () {
    if (!$("#deleteloginCustRepED").is(":checked")) {
    } else {
        $("#loginCustRepED").prop("checked", true);
        $("#editloginCustRepED").prop("checked", true);
        $("#loginCustRepED").trigger("change");
    }
});

//
$("#seeinActiveCustRepED").on("change", function () {
    if (!$("#seeinActiveCustRepED").is(":checked")) {
        $("#inActiveCustRepED").prop("checked", false);
        $("#inActiveCustRepED").trigger("change");
        $("#deleteinActiveCustRepED").prop("checked", false);
        $("#editinActiveCustRepED").prop("checked", false);
    } else {
        $("#inActiveCustRepED").prop("checked", true);
        $("#inActiveCustRepED").trigger("change");
    }
});

$("#editinActiveCustRepED").on("change", function () {
    if (!$("#editinActiveCustRepED").is(":checked")) {
        $("#deleteinActiveCustRepED").prop("checked", false);
    } else {
        $("#inActiveCustRepED").prop("checked", true);
        $("#inActiveCustRepED").trigger("change");
    }
});

$("#deleteinActiveCustRepED").on("change", function () {
    if (!$("#deleteinActiveCustRepED").is(":checked")) {
    } else {
        $("#inActiveCustRepED").prop("checked", true);
        $("#editinActiveCustRepED").prop("checked", true);
        $("#inActiveCustRepED").trigger("change");
    }
});

//
$("#seenoAdminCustRepED").on("change", function () {
    if (!$("#seenoAdminCustRepED").is(":checked")) {
        $("#noAdminCustRepED").prop("checked", false);
        $("#noAdminCustRepED").trigger("change");
        $("#deletenoAdminCustRepED").prop("checked", false);
        $("#editnoAdminCustRepED").prop("checked", false);
    } else {
        $("#noAdminCustRepED").prop("checked", true);
        $("#noAdminCustRepED").trigger("change");
    }
});

$("#editnoAdminCustRepED").on("change", function () {
    if (!$("#editnoAdminCustRepED").is(":checked")) {
        $("#deletenoAdminCustRepED").prop("checked", false);
    } else {
        $("#noAdminCustRepED").prop("checked", true);
        $("#noAdminCustRepED").trigger("change");
    }
});

$("#deletenoAdminCustRepED").on("change", function () {
    if (!$("#deletenoAdminCustRepED").is(":checked")) {
    } else {
        $("#noAdminCustRepED").prop("checked", true);
        $("#editnoAdminCustRepED").prop("checked", true);
        $("#noAdminCustRepED").trigger("change");
    }
});

//
$("#seereturnedCustRepED").on("change", function () {
    if (!$("#seereturnedCustRepED").is(":checked")) {
        $("#returnedCustRepED").prop("checked", false);
        $("#returnedCustRepED").trigger("change");
        $("#deletereturnedCustRepED").prop("checked", false);
        $("#editreturnedCustRepED").prop("checked", false);
    } else {
        $("#returnedCustRepED").prop("checked", true);
        $("#returnedCustRepED").trigger("change");
    }
});

$("#editreturnedCustRepED").on("change", function () {
    if (!$("#editreturnedCustRepED").is(":checked")) {
        $("#deletereturnedCustRepED").prop("checked", false);
    } else {
        $("#returnedCustRepED").prop("checked", true);
        $("#returnedCustRepED").trigger("change");
    }
});

$("#deletereturnedCustRepED").on("change", function () {
    if (!$("#deletereturnedCustRepED").is(":checked")) {
    } else {
        $("#returnedCustRepED").prop("checked", true);
        $("#editreturnedCustRepED").prop("checked", true);
        $("#returnedCustRepED").trigger("change");
    }
});
$(".reportED").on("change", function () {
    if ($(".reportED").is(":checked")) {

        $("#amalKardreportED").prop("checked", true);
        $("#managerreportED").prop("checked", true);
        $("#seemanagerreportED").prop("checked", true);


        $("#HeadreportED").prop("checked", true);
        $("#seeHeadreportED").prop("checked", true);


        $("#poshtibanreportED").prop("checked", true);
        $("#seeposhtibanreportED").prop("checked", true);


        $("#bazaryabreportED").prop("checked", true);
        $("#seebazaryabreportED").prop("checked", true);


        $("#reportDriverED").prop("checked", true);
        $("#seereportDriverED").prop("checked", true);


        $("#trazEmployeeReportED").prop("checked", true);
        $("#seetrazEmployeeReportED").prop("checked", true);


        $("#customerReportED").prop("checked", true);
        $("#seecustomerReportED").prop("checked", true);


        $("#goodsReportED").prop("checked", true);
        $("#salegoodsReportED").prop("checked", true);
        $("#seesalegoodsReportED").prop("checked", true);


        $("#returnedgoodsReportED").prop("checked", true);
        $("#seereturnedgoodsReportED").prop("checked", true);


        $("#NoExistgoodsReportED").prop("checked", true);
        $("#seeNoExistgoodsReportED").prop("checked", true);


        $("#nosalegoodsReportED").prop("checked", true);
        $("#seenosalegoodsReportED").prop("checked", true);


        $("#returnedReportgoodsReportED").prop("checked", true);
        $("#returnedNTasReportgoodsReportED").prop("checked", true);
        $("#seereturnedNTasReportgoodsReportED").prop("checked", true);


        $("#tasgoodsReprtED").prop("checked", true);
        $("#seetasgoodsReprtED").prop("checked", true);


        $("#goodsbargiriReportED").prop("checked", true);
        $("#seegoodsbargiriReportED").prop("checked", true);

        $("#amalkardCustReportED").prop("checked", true);

        $("#customerReportED").prop("checked", true);
        $("#seecustomerReportED").prop("checked", true);

        $("#loginCustRepED").prop("checked", true);
        $("#seeloginCustRepED").prop("checked", true);

        $("#inActiveCustRepED").prop("checked", true);
        $("#seeinActiveCustRepED").prop("checked", true);

        $("#noAdminCustRepED").prop("checked", true);
        $("#seenoAdminCustRepED").prop("checked", true);

        $("#returnedCustRepED").prop("checked", true);
        $("#seereturnedCustRepED").prop("checked", true);


    } else {
        $("#amalKardreportED").prop("checked", false);
        $("#managerreportED").prop("checked", false);
        $("#deletemanagerreportED").prop("checked", false);
        $("#editmanagerreportED").prop("checked", false);
        $("#seemanagerreportED").prop("checked", false);


        $("#HeadreportED").prop("checked", false);
        $("#deleteHeadreportED").prop("checked", false);
        $("#editHeadreportED").prop("checked", false);
        $("#seeHeadreportED").prop("checked", false);


        $("#poshtibanreportED").prop("checked", false);
        $("#deleteposhtibanreportED").prop("checked", false);
        $("#editposhtibanreportED").prop("checked", false);
        $("#seeposhtibanreportED").prop("checked", false);


        $("#bazaryabreportED").prop("checked", false);
        $("#deletebazaryabreportED").prop("checked", false);
        $("#editbazaryabreportED").prop("checked", false);
        $("#seebazaryabreportED").prop("checked", false);


        $("#reportDriverED").prop("checked", false);
        $("#deletereportDriverED").prop("checked", false);
        $("#editreportDriverED").prop("checked", false);
        $("#seereportDriverED").prop("checked", false);


        $("#trazEmployeeReportED").prop("checked", false);
        $("#deletetrazEmployeeReportED").prop("checked", false);
        $("#edittrazEmployeeReportED").prop("checked", false);
        $("#seetrazEmployeeReportED").prop("checked", false);


        $("#customerReportED").prop("checked", false);
        $("#deletecustomerReportED").prop("checked", false);
        $("#editcustomerReportED").prop("checked", false);
        $("#seecustomerReportED").prop("checked", false);


        $("#amalkardCustReportED").prop("checked", false);

        $("#customerReportED").prop("checked", false);
        $("#deletecustomerReportED").prop("checked", false);
        $("#editcustomerReportED").prop("checked", false);
        $("#seecustomerReportED").prop("checked", false);

        $("#loginCustRepED").prop("checked", false);
        $("#deleteloginCustRepED").prop("checked", false);
        $("#editloginCustRepED").prop("checked", false);
        $("#seeloginCustRepED").prop("checked", false);

        $("#inActiveCustRepED").prop("checked", false);
        $("#deleteinActiveCustRepED").prop("checked", false);
        $("#editinActiveCustRepED").prop("checked", false);
        $("#seeinActiveCustRepED").prop("checked", false);

        $("#noAdminCustRepED").prop("checked", false);
        $("#deletenoAdminCustRepED").prop("checked", false);
        $("#editnoAdminCustRepED").prop("checked", false);
        $("#seenoAdminCustRepED").prop("checked", false);

        $("#returnedCustRepED").prop("checked", false);
        $("#deletereturnedCustRepED").prop("checked", false);
        $("#editreturnedCustRepED").prop("checked", false);
        $("#seereturnedCustRepED").prop("checked", false);


        $("#goodsReportED").prop("checked", false);
        $("#salegoodsReportED").prop("checked", false);
        $("#deletesalegoodsReportED").prop("checked", false);
        $("#editsalegoodsReportED").prop("checked", false);
        $("#seesalegoodsReportED").prop("checked", false);


        $("#returnedgoodsReportED").prop("checked", false);
        $("#deletereturnedgoodsReportED").prop("checked", false);
        $("#editturnedgoodsReportED").prop("checked", false);
        $("#seereturnedgoodsReportED").prop("checked", false);


        $("#NoExistgoodsReportED").prop("checked", false);
        $("#deleteNoExistgoodsReportED").prop("checked", false);
        $("#editNoExistgoodsReportED").prop("checked", false);
        $("#seeNoExistgoodsReportED").prop("checked", false);


        $("#nosalegoodsReportED").prop("checked", false);
        $("#deletenosalegoodsReportED").prop("checked", false);
        $("#editnosalegoodsReportED").prop("checked", false);
        $("#seenosalegoodsReportED").prop("checked", false);


        $("#returnedReportgoodsReportED").prop("checked", false);
        $("#returnedNTasReportgoodsReportED").prop("checked", false);
        $("#deletereturnedNTasReportgoodsReportED").prop("checked", false);
        $("#editreturnedgoodsReportED").prop("checked", false);
        $("#seereturnedNTasReportgoodsReportED").prop("checked", false);


        $("#tasgoodsReprtED").prop("checked", false);
        $("#deletetasgoodsReprtED").prop("checked", false);
        $("#edittasgoodsReprtED").prop("checked", false);
        $("#seetasgoodsReprtED").prop("checked", false);


        $("#goodsbargiriReportED").prop("checked", false);
        $("#deletegoodsbargiriReportED").prop("checked", false);
        $("#editgoodsbargiriReportED").prop("checked", false);
        $("#seegoodsbargiriReportED").prop("checked", false);
    }
});

$("#openViewTenSalesModal").on("click",()=>{
    const kalaId=$("#kalaSettingsBtn").val();
        $.ajax({
        method: 'get',
        url:baseUrl+"/getTenLastSales",
        async: true,
        data: {
            _token: "{{ csrf_token() }}",
            kalaId: kalaId
        },
        success: function(arrayed_result) {
            $('#lastTenSaleBody').empty();
            arrayed_result.forEach((element,index)=>{
                $('#lastTenSaleBody').append(`<tr onclick="selectTableRow(this)">
                                    <td>`+(index+1)+`</td>
                                   
                                    <td>`+element.Name+`</td>
                                    <td>`+element.FactDate+`</td>
                                    <td>`+parseInt(element.Fi/10).toLocaleString("en")+` تومان</td>
                                    <td>`+parseInt(element.Amount)+` </td>
                                    <td>`+parseInt(element.Price/10).toLocaleString("en")+` تومان</td>
 									<td>`+element.PCode+`</td>
                                    </tr>`);
            });
			
			 if (!($('.modal.in').length)) {
                $('.modal-dialog').css({
                    left: 50,
                    top: 0
                });
              }
              $('#viewTenSales').modal({
                backdrop: false,
                show: true
              });
              $('.modal-dialog').draggable({
                  handle: ".modal-header"
                });
            $("#viewTenSales").modal("show");
        },
        error: function(data) {}
        });
});

function filterAllKala(){
    $.get(baseUrl+"/filterAllKala",{
            kalaNameCode:$("#searchKalaNameCode").val(),
            mainGroup:$("#superGroup").val().split("_")[1],
            subGroup:$("#subGroup").val(),
            searchKalaStock:$("#searchKalaStock").val(),
            searchKalaActiveOrNot:$("#searchKalaActiveOrNot").val(),
            searchKalaExistInStock:$("#searchKalaExistInStock").val(),
            assesFirstDate:$("#assesFirstDate").val(),
            assesSecondDate:$("#assesSecondDate").val()
    },function(data,status) {
        if(status=="success"){
            $("#lastDateSaleOrOther").text("");
            $("#lastDateSaleOrOther").text("آخرین تاریخ خرید");
        $("#allKalaContainer").empty();
        data.forEach((element,index)=>{
            hidStyle="";
            if(element.hideKala==1){
                hidStyle="Style='background-color:red'";
            }
            $("#allKalaContainer").append(`<tr onclick="getKalaId(this); selectTableRow(this)">
            <td >`+(index+1)+`</td>
            <td class="forMobile-hide">`+element.GoodCde+`</td>
            <td >`+element.GoodName+`</td>
            <td>`+element.lastDate+`</td>
            <td class="forMobile-hide" `+hidStyle+`>`+element.hideKala+`</td>
            <td style="color:red;background-color:azure;">`+element.Amount+`</td>
            <td >
                <input class="kala form-check-input" name="kalaId[]" type="radio" value="`+element.GoodSn+`" id="flexCheckCheckedKala">
            </td>
        </tr>`);
        })
    }else{
        alert("data has not come");
    }
    });
}

function filterRakidKala(){
    $.get(baseUrl+"/getRakidKala",{
            kalaNameCode:$("#searchKalaNameCode").val(),
            mainGroup:$("#superGroup").val().split("_")[1],
            subGroup:$("#subGroup").val(),
            searchKalaStock:$("#searchKalaStock").val(),
            searchKalaActiveOrNot:$("#searchKalaActiveOrNot").val(),
            searchKalaExistInStock:$("#searchKalaExistInStock").val(),
            rakidFirstDate:$("#firstDateRakid").val(),
            rakidSecondDate:$("#secondDateRakid").val()
    },function(data,status) {
        if(status=="success"){
            $("#lastDateSaleOrOther").text("");
            $("#lastDateSaleOrOther").text("آخرین تاریخ خرید");
        $("#allKalaContainer").empty();
        data.forEach((element,index)=>{
            hidStyle="";
            if(element.hideKala==1){
                hidStyle="Style='background-color:red'";
            }
            $("#allKalaContainer").append(`<tr onclick="getKalaId(this); selectTableRow(this)">
            <td >`+(index+1)+`</td>
            <td style="width:88px">`+element.GoodCde+`</td>
            <td style="width:333px">`+element.GoodName+`</td>
            <td>`+element.lastDate+`</td>
            <td `+hidStyle+`>`+element.hideKala+`</td>
            <td style="color:red;background-color:azure;">`+element.Amount+`</td>
            <td >
                <input class="kala form-check-input" name="kalaId[]" type="radio" value="`+element.GoodSn+`" id="flexCheckCheckedKala">
            </td>
        </tr>`);
        })
    }else{
        alert("data has not come");
    }
    });
}


function filterReturnedKala(){
    $.get(baseUrl+"/getReturnedKala",{
            kalaNameCode:$("#searchKalaNameCode").val(),
            mainGroup:$("#superGroup").val().split("_")[1],
            subGroup:$("#subGroup").val(),
            searchKalaStock:$("#searchKalaStock").val(),
            searchKalaActiveOrNot:$("#searchKalaActiveOrNot").val(),
            searchKalaExistInStock:$("#searchKalaExistInStock").val(),
            returnFirstDate:$("#firstDateReturn").val(),
            returnSecondDate:$("#secondDateReturn").val()
    },function(data,status) {
        if(status=="success"){
            $("#lastDateSaleOrOther").text("");
            $("#lastDateSaleOrOther").text("آخرین تاریخ برگشت");
        $("#allKalaContainer").empty();
        data.forEach((element,index)=>{
            hidStyle="";
            if(element.hideKala==1){
                hidStyle="Style='background-color:red'";
            }
            $("#allKalaContainer").append(`<tr onclick="getKalaId(this); selectTableRow(this)">
                                                <td >`+(index+1)+`</td>
                                                <td style="width:88px">`+element.GoodCde+`</td>
                                                <td style="width:333px">`+element.GoodName+`</td>
                                                <td>`+element.lastDate+`</td>
                                                <td `+hidStyle+`>`+element.hideKala+`</td>
                                                <td style="color:red;background-color:azure;">`+element.Amount+`</td>
                                                <td >
                                                    <input class="kala form-check-input" name="kalaId[]" type="radio" value="`+element.GoodSn+`" id="flexCheckCheckedKala">
                                                </td>
                                            </tr>`);
                                            })  
    }else{
        alert("data has not come");
    }
    });
}

function getReturnedFactors(){
    $.get(baseUrl+"/getReturnedFactors",
    {
        goodName:$("#goodName").val(),
        firstDate:$("#assesFirstDate").val(),
        secondDate:$("#assesSecondDate").val(),
        firstTime:$("#assesFirstTime").val(),
        secondTime:$("#assesSecondTime").val(),
        firstFactNo:$("#firstFactNo").val(),
        secondFactNo:$("#secondFactNo").val(),
        customreName:$("#customerNameId").val(),
        setterName:$("#setterName").val(),
        stockSn:$("#stockSnId").val(),
        FactNo:$("#justFactNo").val()
    },function(data,status){
        
        if(status=="success"){
            $("#returnedBodyFactorList").empty();
            data.forEach((element,index)=>{
                $("#returnedBodyFactorList").append(`
                <tr onclick="getReturnedFactorDetail(this,`+element.SerialNoHDS+`); selectTableRow(this);>
                <td>`+(index+1)+`</td>
                <td>`+element.PCode+`</td>
                <td>`+element.Name+`</td>
                <td class="forMobile-hide">`+element.FactNo+`</td>
                <td>`+element.FactDate+`</td>
                <td class="forMobile-hide">`+element.FactDesc+`</td>
                <td>`+element.NameStock+`</td>
                <td class="forMobile-hide">`+parseInt(element.TotalPriceHDS/10).toLocaleString("en-us")+`</td>
                <td class="forMobile-hide">`+element.NameUser+`</td>
                <td>`+element.DateBargiri+`</td>
                <td class="forMobile-hide">`+element.TimeBargiri+`</td>
                <td class="forMobile-hide">`+element.FactTime+`</td>
            </tr>`);
            })
        }else{
            alert("can get data from returned Factors")
        }
    })
}

function getReturnDateHistory(history) {
    $.get(baseUrl+"/getReturnedFactorsHistory",
    {
        HISTORY:``+history+``
    },function(data,status){
        if(status=="success"){
            $("#returnedBodyFactorList").empty();
            data.forEach((element,index)=>{
                $("#returnedBodyFactorList").append(`<tr onclick="getReturnedFactorDetail(this,`+element.SerialNoHDS+`); selectTableRow(this)">
                <td>`+(index+1)+`</td>
                <td>`+element.FactNo+`</td>
                <td>`+element.FactDate+`</td>
                <td>`+element.FactDesc+`</td>
                <td>`+element.PCode+`</td>
                <td>`+element.Name+`</td>
                <td>`+element.NameStock+`</td>
                <td>`+parseInt(element.TotalPriceHDS/10).toLocaleString("en-us")+`</td>
                <td>`+element.NameUser+`</td>
                <td>`+element.DateBargiri+`</td>
                <td>`+element.TimeBargiri+`</td>
                <td>`+element.FactTime+`</td>
            </tr>`);
            })
        }else{
            alert("can get data from returned Factors")
        }
    })
}

if($("#stockSnId")){
$.get(baseUrl+'/getStocks',{},function(data,status) {
   if(status=="success"){
    $("#stockSnId").empty();
    $("#stockSnId").append(`<option value="">همه</option>`);
    data.forEach((element,index)=>{
        $("#stockSnId").append(`<option value="`+element.NameStock+`">`+element.NameStock+`</option>`);
    });
   }
});
}

if($("#setterName")){
    $.get(baseUrl+'/getFactorSetter',{},function(data,status) {
       if(status=="success"){
        $("#setterName").empty();
        $("#setterName").append(`<option value="">همه</option>`);
        data.forEach((element,index)=>{
            $("#setterName").append(`<option value="`+element.NameUser+`">`+element.NameUser+`</option>`);
        });
       }
    });
}


function getReturnedFactorDetail(element,factorId){
    $("tr").removeClass("selected");
    $(element).addClass("selected");

    $.get(baseUrl+"/getFactorDetail",{FactorSn:factorId}, function(data, status){
        if(status=="success"){
            $("#factorInfo").css({ display: "block" });
            let factor = data[0];
            $("#factorDateP").text(factor.FactDate);
            $("#customerNameFactorP").text(factor.Name);
            $("#customerComenterP").text(factor.Name);
            $("#Admin1P").text(factor.lastName);
            $("#customerAddressFactorP").text(factor.peopeladdress);
            $("#customerPhoneFactorP").text(factor.sabit);
            $("#factorSnFactorP").text(factor.FactNo);
            $("#productListP").empty();
            data.forEach((element, index) => {
                $("#productListP").append(
                    ` <tr onclick="selectTableRow(this)">
            <td class="driveFactor">` +
                    (index + 1) +
                    `</td>
            <td>` +
                    element.GoodName +
                    ` </td>
            <td class="driveFactor">` +
                    element.Amount / 1 +
                    `</td>
            <td>` +
                    element.UName +
                    `</td>
            <td>` +
                    (element.Fi / 10).toLocaleString("en-us") +
                    `</td>
            <td style="width:111px;">` +
                    (element.goodPrice / 10).toLocaleString("en-us") +
                    `</td>
            </tr>`
                );
            });
        }
      });
}

$.get(baseUrl+"/getProductMainGroups", function(data, status){
    $("#superGroup").empty();
    $("#superGroup").append(`<option value="_">همه</option>`);
    data.forEach((element)=>{
        $("#superGroup").append(`<option value="`+element.id+`_`+element.title+`">`+element.title+`</option>`);
    });
  });

if($("#superGroup")){
    $("#superGroup").on("change",function(){
    $.get(baseUrl+"/getSubGroups",{ mainGroupId:$("#superGroup").val().split("_")[0] }, function(data, status){
        $("#subGroup").empty();
        $("#subGroup").append(`<option value="">همه</option>`);
        data.forEach((element)=>{
            $("#subGroup").append(`<option value="`+element.title+`">`+element.title+`</option>`);
        });
      });
});
}

$("#addCustomerForm").on("submit",function(e){
    e.preventDefault();
    $.ajax({
        method: $(this).attr('method'),
        url: $(this).attr('action'),
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function (data) {
            if(data.mobileExist === "YES"){
                alert("شماره همراه قبلا در سیستم ثبت است.")
            }
            if(data.phoneExist === "YES"){
                alert("شماره ثابت قبلا در سیستم ثبت است.")
            }
            if(data==1){
            alert("مشتری موفقانه ذخیره شد")
            window.location.reload();
            }
        },
        error: function (error) {

        }
    });



    $("#addNewAdminForm").on("submit",function(e){
        e.preventDefault();
        $.ajax({
            method: $(this).attr('method'),
            url: $(this).attr('action'),
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (data) {
                if(data === "exist"){
                    alert("کاربری با این نام کاربری قبلا ثبت سیستم است.")
                }else{
                    alert(" موفقانه ذخیره شد")
                    window.location.reload();
                }
               
            },
            error: function (error) {
    
            }
        });
    });
    
    // phone = $("#mobileNumberInput").val()

    // let existance="NO"

    // if(phone.length===11){

    //     $.get(baseUrl+"/checkPhoneExistance",{phone:phone},function(response,status){

    //         existance=response;

    //         if(existance=="YES"){

    //             $("#saveCustomerButton").prop("disabled",true);

    //             $("#mobileNumberInput").css("border-color","red");

    //             // alert("قبلا کاربری با این شماره تماس ثبت سیستم شده است.");

    //         }
            
    //     })

    // }else{

    //     if(phone.length>0){

    //         $("#mobileNumberInput").css("border-color","red");

    //         alert("ارقام وارد شده شماره تماس صحیح نمی باشد. باید 11 رقم باشد.");

    //     }

    // }

})


// function checkPhoneExistance(phone,element){

//     let existance="NO"

//     if(phone.length===11){

//         $.get(baseUrl+"/checkPhoneExistance",{phone:phone},function(response,status){

//             existance=response;

//             if(existance=="YES"){

//                 $("#saveCustomerButton").prop("disabled",true);

//                 $(element).css("border-color","red");

//             //    alert("قبلا کاربری با این شماره تماس ثبت سیستم شده است.");

//             }else{
//                 $(element).css("border-color","green");

//                 $("#saveCustomerButton").prop("disabled",false);

//             }
            
//         })
//     }else{

//         if(phone.length>0){

//             $(element).css("border-color","red");

//             alert("ارقام وارد شده شماره تماس صحیح نمی باشد. باید 11 رقم باشد.");
            
//         }

//     }

// }

// // Get all the tr elements in the table
// const tableRows = document.querySelectorAll('table tr');

// // Add a click event listener to each tr element
// tableRows.forEach(row => {
//   row.addEventListener('click', () => {
//     // Remove the class from the previously selected row
//     const previouslySelectedRow = document.querySelector('.selected');
//     if (previouslySelectedRow) {
//       previouslySelectedRow.classList.remove('selected');
//     }

//     // Add the class to the clicked row
//     row.classList.add('selected');
//   });
// });


// function tableTrHighlight(){
//     const tableRows = document.querySelectorAll('table tr');
// }


function selectTableRow(row) {

    const previouslySelectedRow = document.querySelector('.selected');
    if (previouslySelectedRow) {
      previouslySelectedRow.classList.remove('selected');
    }
    row.classList.add('selected');
  }

  var tableRows = document.getElementsByTagName('tr');

    // Add a click event listener to each table row
    for (var i = 0; i < tableRows.length; i++) {
    tableRows[i].addEventListener('click', function() {
        // Find the radio button within the clicked table row
        var radioBtn = this.querySelector('td input[type="radio"]');
        
        // Check the radio button
        radioBtn.checked = true;
    });
    }


$("#addingScopeInfoBtn").on("click", ()=>{
    if (!($('.modal.in').length)) {
        $('.modal-dialog').css({
            left: 50,
            top: 0
        });
      }
      $('#addingScopeInfoModal').modal({
        backdrop: false,
        show: true
      });
      $('.modal-dialog').draggable({
          handle: ".modal-header"
        });
    $("#addingScopeInfoModal").modal("show");
})





// Create root and chart
var root = am5.Root.new("chartdiv");
root.setThemes([am5themes_Animated.new(root)]);

var chart = root.container.children.push(
    am5xy.XYChart.new(root, {
        wheelY: "zoomX",
    })
);

// Define data
var data = [
    {
        date: new Date(2021, 0, 1).getTime(),
        value: 100,
    },
    {
        date: new Date(2021, 0, 2).getTime(),
        value: 320,
    },
    {
        date: new Date(2021, 0, 3).getTime(),
        value: 216,
    },
    {
        date: new Date(2021, 0, 4).getTime(),
        value: 150,
    },
    {
        date: new Date(2021, 0, 5).getTime(),
        value: 156,
    },
    {
        date: new Date(2021, 0, 6).getTime(),
        value: 199,
    },
    {
        date: new Date(2021, 0, 7).getTime(),
        value: 114,
    },
    {
        date: new Date(2021, 0, 8).getTime(),
        value: 269,
    },
    {
        date: new Date(2021, 0, 9).getTime(),
        value: 190,
    },
    {
        date: new Date(2021, 0, 10).getTime(),
        value: 380,
    },
    {
        date: new Date(2021, 0, 11).getTime(),
        value: 250,
    },
    {
        date: new Date(2021, 0, 12).getTime(),
        value: 110,
    },
    {
        date: new Date(2021, 0, 13).getTime(),
        value: 185,
    },
    {
        date: new Date(2021, 0, 14).getTime(),
        value: 105,
    },
];

// Create Y-axis
var yAxis = chart.yAxes.push(
    am5xy.ValueAxis.new(root, {
        extraTooltipPrecision: 1,
        renderer: am5xy.AxisRendererY.new(root, {
            minGridDistance: 30,
        }),
    })
);

// Create X-Axis
let xAxis = chart.xAxes.push(
    am5xy.DateAxis.new(root, {
        baseInterval: { timeUnit: "day", count: 1 },
        renderer: am5xy.AxisRendererX.new(root, {
            minGridDistance: 20,
            cellStartLocation: 0.2,
            cellEndLocation: 0.8,
        }),
    })
);

// Create series
function createSeries(name, field) {
    var series = chart.series.push(
        am5xy.ColumnSeries.new(root, {
            name: name,
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: field,
            valueXField: "date",
            tooltip: am5.Tooltip.new(root, {}),
            clustered: true,
        })
    );

    series
        .get("tooltip")
        .label.set("text", "[bold]{name}[/]\n{valueX.formatDate()}: {valueY}");
    series.data.setAll(data);

    return series;
}

var series1 = createSeries("Series #1", "value");

// Create axis ranges
function createRange(series, value, endValue, color) {
    var range = series.createAxisRange(
        yAxis.makeDataItem({
            value: value,
            endValue: endValue,
        })
    );

    range.columns.template.setAll({
        fill: color,
        stroke: color,
    });

    range.axisDataItem.get("axisFill").setAll({
        fill: color,
        fillOpacity: 0.05,
        visible: true,
    });
}

createRange(series1, 125, 275, am5.color(0xff621f));

// Add cursor
chart.set(
    "cursor",
    am5xy.XYCursor.new(root, {
        behavior: "zoomX",
        xAxis: xAxis,
    })
);

xAxis.set(
    "tooltip",
    am5.Tooltip.new(root, {
        themeTags: ["axis"],
    })
);

yAxis.set(
    "tooltip",
    am5.Tooltip.new(root, {
        themeTags: ["axis"],
    })
);


function getCustomerInformation(PSN) {

    $.get(baseUrl+"/customerInformation",{customerId:PSN}, function(data, status){ 
        console.log(data)
        if(status=="success"){
            $("#quick_CustomerName").text(data[0].Name);
            $("#quick_countFactor").text(data[0].countFactor);
            $("#quick_BuyAllMoney").text(parseInt(data[0].AllMoneyBuy/10).toLocaleString("en-us")+' تومان');
            $("#quick_lastBuyMoney").text(parseInt(data[0].lastFactorAllMoney/10).toLocaleString("en-us")+' تومان');
            if(data[0].basketState>0){
                $("#quick_basketState").text("پر");
            }else{
                $("#quick_basketState").text("خالی");        
            }
            $("#quick_lastFactDate").text((data[0].LastDateFactor || ""));
            $("#quick_lastLoginDate").text((data[0].LastDateLogin || ""));
            $("#quick_address").text(data[0].peopeladdress);
            $("#quick_Phone").text(data[0].PhoneStr);
        }

    });
}


function getCustomerInformationForModal(PSN) {

    $.get(baseUrl+"/customerInformation",{customerId:PSN}, function(data, status){ 
        if(status=="success"){
            $("#quick_CustomerNameM").text(data[0].Name);
            $("#quick_countFactorM").text(data[0].countFactor);
            $("#quick_BuyAllMoneyM").text(parseInt(data[0].AllMoneyBuy/10).toLocaleString("en-us")+' تومان');
            $("#quick_lastBuyMoneyM").text(parseInt(data[0].lastFactorAllMoney/10).toLocaleString("en-us")+' تومان');
            if(data[0].basketState>0){
                $("#quick_basketStateM").text("پر");
            }else{
                $("#quick_basketStateM").text("خالی");        
            }
            $("#quick_lastFactDateM").text((data[0].LastDateFactor || ""));
            $("#quick_lastLoginDateM").text((data[0].LastDateLogin || ""));
            $("#quick_addressM").text(data[0].peopeladdress);
            $("#quick_PhoneM").text(data[0].PhoneStr);
        }

    });
}

