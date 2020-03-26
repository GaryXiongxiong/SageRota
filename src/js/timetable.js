$(document).ready(function () {
    let auth = suAuthenticate();
    if(auth.level!==1){
        window.location.href="su_login.html";
    }


    loadNav(1,auth);

    loadContent();
    loadPages();
    loadGoto();

    $("#assign-shift-form").submit(function (e) {
        e.preventDefault();
        let data = $(this).serialize();
        let submitData = decodeURIComponent(data);
        $.ajax({
            method: "POST",
            dataType: "json",
            url: "api/add_shift.php",
            data: submitData,
            success: function (result) {
                if (result.result === "success") {
                    $("#assign-shift-popup").modal("hide");
                    $("#assign-shift-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-success alert-dismissible fade show' role='alert'>" +
                        "  <strong>Add Success!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                    loadContent();
                } else if (result.result === "fail") {
                    $("#assign-shift-popup").modal("hide");
                    $("#assign-shift-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                        "  <strong>Add Fail! Please check if input is valid.</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                }
            },
            error: function (e) {
                console.log(e);
                $("#assign-shift-popup").modal("hide");
                $("#assign-shift-form")[0].reset();
                $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                    "  <strong>Add Fail! Please check if input is valid.</strong>" +
                    "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                    "<span aria-hidden='true'>&times;</span> " +
                    "</button> " +
                    "</div>");
            }
        });
    });

    $("#delete-shift-form").submit(function (e) {
        e.preventDefault();
        let data = $(this).serialize();
        let submitData = decodeURIComponent(data);
        $.ajax({
            url: "api/delete_shift.php",
            method: "POST",
            dataType: "json",
            data: submitData,
            success: function (result) {
                if (result.result === "success") {
                    $("#delete-shift-popup").modal("hide");
                    $("#delete-shift-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-success alert-dismissible fade show' role='alert'>" +
                        "  <strong>Delete Success!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                    loadContent();
                } else {
                    $("#delete-shift-popup").modal("hide");
                    $("#delete-shift-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                        "  <strong>Delete Fail!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                }
            },
            error: function (e) {
                console.log(e);
                $("#delete-shift-popup").modal("hide");
                $("#delete-shift-form")[0].reset();
                $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                    "  <strong>Delete Fail!</strong>" +
                    "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                    "<span aria-hidden='true'>&times;</span> " +
                    "</button> " +
                    "</div>");
            }
        })
    });

    $("#edit-shift-form").submit(function (e) {
        e.preventDefault();
        let data = $(this).serialize();
        let submitData = decodeURIComponent(data);
        $.ajax({
            method: "POST",
            dataType: "json",
            url: "api/update_shift.php",
            data: submitData,
            success: function (result) {
                if (result.result === "success") {
                    $("#edit-shift-popup").modal("hide");
                    $("#edit-shift-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-success alert-dismissible fade show' role='alert'>" +
                        "  <strong>Update Success!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                    loadContent();
                } else if (result.result === "fail") {
                    $("#edit-shift-popup").modal("hide");
                    $("#edit-shift-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                        "  <strong>Update Fail! Please check if input is valid.</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                }
            },
            error: function (e) {
                console.log(e);
                $("#assign-shift-popup").modal("hide");
                $("#assign-shift-form")[0].reset();
                $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                    "  <strong>update Fail! Please check if input is valid.</strong>" +
                    "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                    "<span aria-hidden='true'>&times;</span> " +
                    "</button> " +
                    "</div>");
            }
        })
    })

});

function loadStartDate() {
    let startDate = getUrlParam("start_date");
    let date;
    let dayOfWeek;
    if (startDate == null) {
        date = new Date();
    } else {
        date = new Date(startDate);
    }
    dayOfWeek = date.getDay();
    if (dayOfWeek === 0) dayOfWeek = 7;
    date.setDate(date.getDate() - dayOfWeek + 1);
    return date;
}

function loadPages() {
    let startDate = loadStartDate();
    let previousDate = new Date(startDate);
    let nextDate = new Date(startDate);
    previousDate.setDate(startDate.getDate() - 63);
    nextDate.setDate(startDate.getDate() + 63);
    $("#previous-page").attr("href", "?start_date=" + previousDate.format("YYYY-MM-DD"));
    $("#next-page").attr("href", "?start_date=" + nextDate.format("YYYY-MM-DD"));
}

function loadGoto() {
    $(".datepicker").datepicker({
        defaultValue: getUrlParam("start_date"),
        weekStart: 1
    });
    $("#goto-btn").click(function () {
        window.location.href = '?start_date=' + $(".datepicker").datepicker('getDate');
    })
}

function loadContent() {
    let startDate = loadStartDate();
    $(".shift-info").remove();
    $.ajax({
        url: "api/timetable_list.php",
        method: "POST",
        dataType: "json",
        data: {start_date: startDate.format("YYYY-MM-DD")},
        beforeSend: function () {
            showLoading();
        },
        success: function (result) {
            let shifts = result.shift;
            let curDate = startDate;
            for (let i = 0; i < 9; i++) {
                while (shifts.length !== 0 && new Date(shifts[0].start_time).getDay() !== 1) shifts.shift();
                if (shifts.length !== 0 && curDate.format("YYYY-MM-DD") === shifts[0].start_time) {
                    appendShift(shifts.shift());
                } else {
                    appendEmptyShift(curDate);
                }
                curDate.setDate(curDate.getDate() + 7);
            }
            loadCurrentShiftStyle();
            bindAssignEvent();
            bindDeleteEvent();
            bindEditEvent();
            removeLoading();
        }
    });
}

function appendShift(shift) {
    let shiftContent;
    if (shift.location == null || shift.location === "") {
        shiftContent = '<div class="col-lg-4 col-md-6 shift-info" data-id="' + shift.id + '" data-date="' + shift.start_time + '">\n' +
            '                <div class="card">\n' +
            '                    <div class="card-body">\n' +
            '                        <h5>' + new Date(shift.start_time).format("DS MMM") + " - "
            + ' ' + new Date(shift.end_time).format("DS MMM") + '</h5>\n' +
            '                        <p>\n' +
            '                            <i class="fas fa-user"></i>\n' +
            '                            <strong>' + shift.staff_first_name + ' ' + shift.staff_last_name + '</strong>\n' +
            '                            <br>\n' +
            '                            <br>\n' +
            '                        </p>\n' +
            '                        <span class="btn btn-primary btn-shift-edit">Edit</span>\n' +
            '                        <span class="btn btn-secondary btn-shift-delete">Delete</span>\n' +
            '                    </div>\n' +
            '                </div>\n' +
            '            </div>';
    } else {
        shiftContent = '<div class="col-lg-4 col-md-6 shift-info" data-id="' + shift.id + '" data-date="' + shift.start_time + '">\n' +
            '                <div class="card">\n' +
            '                    <div class="card-body">\n' +
            '                        <h5>' + new Date(shift.start_time).format("DS MMM") + " - "
            + ' ' + new Date(shift.end_time).format("DS MMM") + '</h5>\n' +
            '                        <p>\n' +
            '                            <i class="fas fa-user"></i>\n' +
            '                            <strong>' + shift.staff_first_name + ' ' + shift.staff_last_name + '</strong>\n' +
            '                            <br>\n' +
            '                            <i class="fas fa-map-marked-alt"></i>\n' +
            '                            ' + shift.location + '\n' +
            '                            <br>\n' +
            '                        </p>\n' +
            '                        <span class="btn btn-primary btn-shift-edit">Edit</span>\n' +
            '                        <span class="btn btn-secondary btn-shift-delete">Delete</span>\n' +
            '                    </div>\n' +
            '                </div>\n' +
            '            </div>';
    }
    $("#timetable-list").append(shiftContent)
}

function appendEmptyShift(date) {
    let endDate = new Date(date);
    endDate.setDate(date.getDate() + 6);
    let shiftContent = '<div class="col-lg-4 col-md-6 shift-info invalid" data-id="" data-date="' + date.format("YYYY-MM-DD") + '">\n' +
        '                <div class="card">\n' +
        '                    <div class="card-body">\n' +
        '                        <h5>' + date.format("DS MMM") + ' - ' + endDate.format("DS MMM") + '</h5>\n' +
        '                        <p>\n' +
        '                            <i class="fas fa-user"></i>\n' +
        '                            <strong>Unassigned</strong>\n' +
        '                            <br>\n' +
        '                            <br>\n' +
        '                        </p>\n' +
        '                        <span class="btn btn-primary btn-shift-assign">Assign</span>\n' +
        '                    </div>\n' +
        '                </div>\n' +
        '            </div>';
    $("#timetable-list").append(shiftContent);
}

function bindAssignEvent() {
    $(".btn-shift-assign").click(function () {
        let startDate = $(this).parents(".shift-info").attr("data-date");
        let endDateDate = new Date(startDate);
        endDateDate.setDate(endDateDate.getDate() + 6);
        let endDate = new Date(endDateDate).format("YYYY-MM-DD");
        $("#assign-shift-popup").modal("show");
        $("#assign-shift-form #start_date").val(startDate);
        $("#assign-shift-form #end_date").val(endDate);
        $.ajax({
            url: "api/staff_list.php",
            data: {page: "all"},
            method: "POST",
            dataType: "json",
            success: function (result) {
                $("#assign-shift-form #staff_sid .staff-entry").remove();
                let staffs = result.staff;
                staffs.forEach(function (staff) {
                    $("#assign-shift-form #staff_sid").append(
                        '<option value="' + staff.sid + '" class="staff-entry">' + staff.first_name + ' ' + staff.last_name + '</option>'
                    )
                });
                $('#assign-shift-popup .selectpicker').selectpicker("refresh");
            },
            error: function (e) {
                console.log(e)
            }
        });

    });
}

function bindDeleteEvent() {
    $(".btn-shift-delete").click(function () {
        let id = $(this).parents(".shift-info").attr("data-id");
        let start_date = $(this).parents(".shift-info").attr("data-date");
        //bind delete popup
        $("#delete-shift-popup").modal("show");
        //bind confirm delete to send request
        $("#delete-shift-form #delete_id").val(id);
        $("#delete-shift-form #delete_start_date").val(start_date);
    });
}

function bindEditEvent() {
    $(".btn-shift-edit").click(function () {
        let id = $(this).parents(".shift-info").attr("data-id");
        let startDate = $(this).parents(".shift-info").attr("data-date");
        $("#edit-shift-popup").modal("show");
        $.ajax({
            url: "api/staff_list.php",
            data: {page: "all"},
            method: "POST",
            dataType: "json",
            success: function (result) {
                $("#edit-shift-form #edit_staff_sid .staff-entry").remove();
                let staffs = result.staff;
                staffs.forEach(function (staff) {
                    $("#edit-shift-form #edit_staff_sid").append(
                        '<option value="' + staff.sid + '" class="staff-entry">' + staff.first_name + ' ' + staff.last_name + '</option>'
                    )
                });

                $.ajax({
                    url: "api/shift_info.php",
                    method: "POST",
                    dataType: "json",
                    data: {id: id, start_date: startDate},
                    success: function (result) {
                        if (result.shift.length === 1) {
                            let shift = result.shift[0];
                            $("#edit-shift-form #edit_id").val(shift.id);
                            $("#edit-shift-form #edit_start_date").val(shift.start_time);
                            $("#edit-shift-form #edit_end_date").val(shift.end_time);
                            $("#edit-shift-form #edit_location").val(shift.location);
                            $("#edit-shift-form #edit_remark").val(shift.remark);
                            $("#edit-shift-form #edit_staff_sid").val(shift.staff_sid);
                            $("#edit-shift-form .selectpicker").selectpicker("refresh");
                        } else {
                            console.log("fail");
                        }
                    },
                    error: function (e) {
                        console.log(e);
                    }
                })

            },
            error: function (e) {
                console.log(e)
            }
        });
    })
}

function loadCurrentShiftStyle() {
    let date = new Date();
    let dayOfWeek = date.getDay();
    if (dayOfWeek === 0) dayOfWeek = 7;
    date.setDate(date.getDate() - dayOfWeek + 1);
    let dateStr = date.format("YYYY-MM-DD");
    $("[data-date=" + dateStr +"]").children(".card").addClass("current-shift");
}
